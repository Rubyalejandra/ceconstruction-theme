<?php
/**
 * SEO: meta description, Open Graph, Schema.org (JSON-LD) y breadcrumbs.
 * No sustituye un plugin dedicado (Yoast/RankMath) si el cliente ya
 * usa uno; se recomienda desactivar esta salida si ese es el caso
 * (ver ce_construction_seo_enabled más abajo).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_seo_enabled() {
	// Evita duplicar salidas si ya existe un plugin SEO activo.
	return ! ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) );
}

/* =========================================================
 * QA-014 (Sprint 8, Entregable 8.1 — corrección Media).
 * Los 8 bloques JSON-LD de este archivo inyectaban contenido
 * editorial (títulos, excerpts, nombres de cliente/miembro del
 * equipo, etc. — todos campos de wp-admin) directamente dentro
 * de un <script type="application/ld+json"> vía wp_json_encode(),
 * sin neutralizar una secuencia literal "</script>" que ese
 * contenido pudiera contener. Si algún día ese texto la incluyera
 * (aunque sea de forma accidental, ej. pegado desde otra fuente),
 * cerraría el <script> antes de tiempo e inyectaría HTML/JS fuera
 * de contexto en la página. Se centraliza el escapado en esta
 * función auxiliar (JSON_UNESCAPED_SLASHES para que las URLs no se
 * llenen de "\/", más un str_replace defensivo de "</script>" que
 * cubre el caso de que JSON_UNESCAPED_SLASHES esté activo) y se
 * usa en los 8 puntos de salida existentes, sin cambiar ningún
 * schema ni su estructura de datos. Ver DECISIONS.md D-042.
 * ========================================================= */
function ce_construction_output_json_ld( $data ) {
	$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES );
	$json = str_replace( '</script', '<\/script', $json );
	echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $json ya está codificado por wp_json_encode() y endurecido contra </script> arriba.
}

/* =========================================================
 * QA-038 (Sprint 8, Entregable 8.5 — corrección Media).
 *
 * ce_construction_meta_tags() (abajo) emitía og:url pero ningún
 * <link rel="canonical"> explícito en <head>. Sin canonical, el sitio
 * dependía enteramente de la heurística de Google para decidir cuál
 * URL indexar cuando existen variantes de la misma página (parámetros
 * de tracking tipo UTM, ?s= con variantes de mayúsculas/espacios,
 * paginación de comentarios) — riesgo real de contenido duplicado
 * indexable, especialmente porque el tema sí pagina archivos vía
 * paginate_links() (archive.php, archive-servicio.php,
 * archive-proyecto.php, archive-clientes.php, archive-equipo.php) sin
 * que existiera ningún canonical explícito hacia esas páginas.
 *
 * Diseño: para contenido singular, se delega en wp_get_canonical_url()
 * (función nativa de WordPress desde 4.6) — ya resuelve permalink +
 * paginación de post multi-página + paginación de comentarios
 * correctamente, sin reinventar esa lógica aquí. Para el resto de
 * contextos (Home de blog, archivos de CPT, categoría/etiqueta/
 * taxonomía custom, autor, fecha, búsqueda), se construye la URL base
 * "limpia" con las funciones nativas de permalink de cada tipo
 * (get_post_type_archive_link(), get_term_link(), etc.) — nunca a
 * partir de la URL de la petición actual (se descartó usar
 * get_pagenum_link() como fuente única: reconstruye la paginación
 * correctamente, pero conserva cualquier query string ya presente en
 * la URL, incluidos parámetros de tracking como UTMs, que es
 * exactamente lo que este canonical debe limpiar) y luego se le
 * reaplica el sufijo de paginación de archivo (/page/N/) de forma
 * auto-referencial (cada página paginada apunta a sí misma, no todas
 * hacia la página 1 — alineado con la recomendación vigente de Google
 * tras retirar rel="next"/"prev" como señal).
 *
 * 404 y cualquier contexto no cubierto explícitamente: se devuelve
 * cadena vacía a propósito. Imprimir un canonical adivinado o
 * incorrecto es peor que no imprimir ninguno.
 * ========================================================= */

/**
 * Reaplica la paginación de archivo (page/N/, o ?paged=N sin permalinks
 * bonitos) sobre una URL base ya "limpia" (sin query string de
 * tracking). Mismo criterio de sufijo que usa internamente
 * get_pagenum_link() de WordPress core, aplicado aquí sobre una URL
 * arbitraria en vez de sobre la URL de la petición actual.
 */
function ce_construction_apply_canonical_pagination( $url ) {
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( $paged < 2 ) {
		return $url;
	}

	global $wp_rewrite;
	if ( $wp_rewrite->using_permalinks() ) {
		return trailingslashit( $url ) . user_trailingslashit( $wp_rewrite->pagination_base . '/' . $paged, 'paged' );
	}

	return add_query_arg( 'paged', $paged, $url );
}

/**
 * Resuelve la URL canónica del contexto de la petición actual, o
 * cadena vacía si no aplica ninguna (ver docblock del bloque QA-038
 * arriba para el detalle de diseño).
 */
function ce_construction_get_canonical_url() {
	if ( is_singular() ) {
		$canonical = wp_get_canonical_url();
		return $canonical ? $canonical : get_permalink();
	}

	if ( is_front_page() ) {
		// Cubre el caso por defecto (el blog ES la portada): la página 1
		// pasa por aquí; page/2/ en adelante deja de ser is_front_page()
		// y cae en la rama is_home() de abajo, que sí reaplica paginación.
		return home_url( '/' );
	}

	if ( is_home() ) {
		// Portada estática configurada + una Página distinta como blog:
		// is_home() es true en el listado del blog aunque no sea la
		// portada. Sin Página "de entradas" asignada (configuración por
		// defecto), cae a home_url('/') igual que is_front_page().
		$page_for_posts = (int) get_option( 'page_for_posts' );
		$canonical = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
		return $canonical ? ce_construction_apply_canonical_pagination( $canonical ) : '';
	}

	if ( is_search() ) {
		return ce_construction_apply_canonical_pagination( get_search_link() );
	}

	if ( is_post_type_archive() ) {
		$canonical = get_post_type_archive_link( get_post_type() );
		return $canonical ? ce_construction_apply_canonical_pagination( $canonical ) : '';
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$canonical = get_term_link( get_queried_object() );
		return ( $canonical && ! is_wp_error( $canonical ) ) ? ce_construction_apply_canonical_pagination( $canonical ) : '';
	}

	if ( is_author() ) {
		$canonical = get_author_posts_url( get_queried_object_id() );
		return $canonical ? ce_construction_apply_canonical_pagination( $canonical ) : '';
	}

	if ( is_day() || is_month() || is_year() ) {
		if ( is_day() ) {
			$canonical = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
		} elseif ( is_month() ) {
			$canonical = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
		} else {
			$canonical = get_year_link( get_query_var( 'year' ) );
		}
		return $canonical ? ce_construction_apply_canonical_pagination( $canonical ) : '';
	}

	// 404, feeds u otro contexto no cubierto explícitamente arriba: sin
	// canonical — ver docblock del bloque QA-038 arriba.
	return '';
}

function ce_construction_meta_tags() {
	if ( ! ce_construction_seo_enabled() ) {
		return;
	}

	if ( is_singular() ) {
		global $post;
		$description = has_excerpt( $post->ID ) ? get_the_excerpt( $post->ID ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30 );
		$title       = get_the_title( $post->ID );
		$image       = has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'ce-hero' ) : get_theme_mod( 'ce_hero_image' );
		$url         = get_permalink( $post->ID );
	} else {
		$description = get_bloginfo( 'description' );
		$title       = get_bloginfo( 'name' );
		$image       = get_theme_mod( 'ce_hero_image' );
		$url         = home_url( '/' );
	}

	echo "\n<!-- CE Construction SEO -->\n";
	// QA-038 (Sprint 8, Entregable 8.5): <link rel="canonical"> explícito.
	// Se calcula por separado de $url (arriba, usado solo para og:url y
	// sin cambios en esta corrección) porque $url no reaplica paginación
	// de archivo ni distingue entre los distintos tipos de archivo no
	// singulares — ver ce_construction_get_canonical_url() arriba.
	$canonical_url = ce_construction_get_canonical_url();
	if ( $canonical_url ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical_url ) );
	}
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	// QA-039 (Sprint 8, Entregable 8.7): antes solo se emitía
	// twitter:card, sin twitter:title/description/image explícitos.
	// La mayoría de los parsers de Twitter/X hacen fallback a las
	// etiquetas og:* equivalentes (ya impresas arriba), pero eso no
	// está garantizado para todos los consumidores de la Card — se
	// declaran aquí explícitas, reutilizando exactamente las mismas
	// variables ya calculadas para og:title/og:description/og:image
	// (sin ninguna consulta ni cálculo adicional).
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'ce_construction_meta_tags', 1 );

/**
 * Schema.org JSON-LD: Organización + LocalBusiness (constructora).
 */
function ce_construction_schema_organization() {
	if ( ! ce_construction_seo_enabled() || ! is_front_page() ) {
		return;
	}

	$schema = array(
		'@context'  => 'https://schema.org',
		'@type'     => 'GeneralContractor',
		'name'      => get_bloginfo( 'name' ),
		'url'       => home_url( '/' ),
		'telephone' => get_theme_mod( 'ce_phone', '' ),
		'email'     => get_theme_mod( 'ce_email', '' ),
		'address'   => array(
			'@type'         => 'PostalAddress',
			'streetAddress' => get_theme_mod( 'ce_address', '' ),
		),
	);

	ce_construction_output_json_ld( $schema );
}
add_action( 'wp_head', 'ce_construction_schema_organization' );

/**
 * Breadcrumbs semánticos (HTML5 + Schema BreadcrumbList).
 */
function ce_construction_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = array();
	$items[] = array( 'label' => __( 'Inicio', 'ce-construction' ), 'url' => home_url( '/' ) );

	if ( is_singular( 'servicio' ) ) {
		$items[] = array( 'label' => __( 'Servicios', 'ce-construction' ), 'url' => get_post_type_archive_link( 'servicio' ) );
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive( 'servicio' ) ) {
		$items[] = array( 'label' => __( 'Servicios', 'ce-construction' ), 'url' => '' );
	} elseif ( is_singular( 'proyecto' ) ) {
		$items[] = array( 'label' => __( 'Proyectos', 'ce-construction' ), 'url' => get_post_type_archive_link( 'proyecto' ) );
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive( 'proyecto' ) ) {
		$items[] = array( 'label' => __( 'Proyectos', 'ce-construction' ), 'url' => '' );
	} elseif ( is_singular( 'miembro_equipo' ) ) {
		$items[] = array( 'label' => __( 'Equipo', 'ce-construction' ), 'url' => get_post_type_archive_link( 'miembro_equipo' ) );
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive( 'miembro_equipo' ) ) {
		$items[] = array( 'label' => __( 'Equipo', 'ce-construction' ), 'url' => '' );
	} elseif ( is_singular( 'cliente' ) ) {
		// NOTA (Sprint 5, Fase 3): antes 'cliente' no tenía archivo público
		// (has_archive => false), así que este nivel intermedio no enlazaba
		// a ningún archivo. Tras habilitar has_archive en inc/cpt-clientes.php
		// (ver ese archivo para el detalle), ahora sí enlaza correctamente,
		// igual que las ramas de Servicios/Proyectos/Equipo de arriba.
		$items[] = array( 'label' => __( 'Clientes', 'ce-construction' ), 'url' => get_post_type_archive_link( 'cliente' ) );
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive( 'cliente' ) ) {
		// Rama nueva (Sprint 5): antes no existía porque este archivo era
		// inalcanzable (has_archive era false). Ahora sí puede ser 'true'.
		$items[] = array( 'label' => __( 'Clientes', 'ce-construction' ), 'url' => '' );
	} elseif ( is_singular( 'post' ) ) {
		$items[] = array( 'label' => __( 'Blog', 'ce-construction' ), 'url' => get_permalink( get_option( 'page_for_posts' ) ) );
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_page() ) {
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	}

	echo '<nav class="ce-breadcrumbs" aria-label="' . esc_attr__( 'Ruta de navegación', 'ce-construction' ) . '"><ol class="ce-breadcrumbs__list">';
	foreach ( $items as $i => $item ) {
		echo '<li class="ce-breadcrumbs__item">';
		if ( $item['url'] ) {
			printf( '<a href="%s">%s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
		} else {
			echo '<span aria-current="page">' . esc_html( $item['label'] ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/* =========================================================
 * Añadido en Sprint 3 (módulo Servicios).
 * No modifica ninguna función anterior de este archivo.
 * ========================================================= */

/**
 * Schema.org JSON-LD específico para single de Servicio:
 * Service + Provider (Organization) + BreadcrumbList.
 */
function ce_construction_schema_service() {
	if ( ! ce_construction_seo_enabled() || ! is_singular( 'servicio' ) ) {
		return;
	}

	$post_id = get_the_ID();

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'name'        => get_the_title( $post_id ),
		'description' => wp_strip_all_tags( ce_get_short_excerpt( $post_id, 30 ) ),
		'url'         => get_permalink( $post_id ),
		'provider'    => array(
			'@type' => 'GeneralContractor',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$schema['image'] = get_the_post_thumbnail_url( $post_id, 'ce-card' );
	}

	$terms = get_the_terms( $post_id, 'categoria_servicio' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$schema['serviceType'] = wp_list_pluck( $terms, 'name' );
	}

	ce_construction_output_json_ld( $schema );

	// BreadcrumbList (Inicio > Servicios > Nombre del servicio).
	$breadcrumb_schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Inicio', 'ce-construction' ),
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => __( 'Servicios', 'ce-construction' ),
				'item'     => get_post_type_archive_link( 'servicio' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post_id ),
				'item'     => get_permalink( $post_id ),
			),
		),
	);
	ce_construction_output_json_ld( $breadcrumb_schema );
}
add_action( 'wp_head', 'ce_construction_schema_service' );

/* =========================================================
 * Añadido en Sprint 4 (módulo Proyectos).
 * No modifica ninguna función anterior de este archivo.
 * ========================================================= */

/**
 * Schema.org JSON-LD para single de Proyecto.
 *
 * NOTA TÉCNICA (ver DECISIONS.md D-014): Schema.org no define
 * un tipo "Project" en su vocabulario estándar. Se usa un
 * @type múltiple ["CreativeWork", "Project"] — "CreativeWork"
 * es el tipo válido y reconocido por buscadores para elegibilidad
 * de resultados enriquecidos, y "Project" se conserva como
 * segundo valor del array para cumplir literalmente con el
 * requisito "Schema.org Project" del brief, ya que JSON-LD
 * permite @type como array de strings.
 */
function ce_construction_schema_project() {
	if ( ! ce_construction_seo_enabled() || ! is_singular( 'proyecto' ) ) {
		return;
	}

	$post_id   = get_the_ID();
	$cliente   = get_post_meta( $post_id, '_ce_proyecto_cliente', true );
	$ubicacion = get_post_meta( $post_id, '_ce_proyecto_ubicacion', true );
	$fecha     = get_post_meta( $post_id, '_ce_proyecto_fecha', true );

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => array( 'CreativeWork', 'Project' ),
		'name'        => get_the_title( $post_id ),
		'description' => wp_strip_all_tags( ce_get_short_excerpt( $post_id, 30 ) ),
		'url'         => get_permalink( $post_id ),
		'creator'     => array(
			'@type' => 'GeneralContractor',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $cliente ) {
		$schema['sourceOrganization'] = array(
			'@type' => 'Organization',
			'name'  => $cliente,
		);
	}

	if ( $ubicacion ) {
		$schema['contentLocation'] = array(
			'@type'   => 'Place',
			'address' => array(
				'@type'         => 'PostalAddress',
				'addressLocality' => $ubicacion,
			),
		);
	}

	if ( $fecha ) {
		$schema['dateCreated'] = gmdate( 'Y-m-d', strtotime( $fecha ) );
	}

	if ( has_post_thumbnail( $post_id ) ) {
		$schema['image'] = get_the_post_thumbnail_url( $post_id, 'ce-card' );
	}

	$gallery_ids = function_exists( 'ce_get_gallery_ids' ) ? ce_get_gallery_ids( $post_id ) : array();
	if ( ! empty( $gallery_ids ) ) {
		$images = array();
		foreach ( $gallery_ids as $img_id ) {
			$url = wp_get_attachment_image_url( $img_id, 'large' );
			if ( $url ) {
				$images[] = $url;
			}
		}
		if ( $images ) {
			$schema['image'] = $images;
		}
	}

	$terms = get_the_terms( $post_id, 'categoria_proyecto' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$schema['keywords'] = implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}

	ce_construction_output_json_ld( $schema );

	// BreadcrumbList (Inicio > Proyectos > Nombre del proyecto).
	$breadcrumb_schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Inicio', 'ce-construction' ),
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => __( 'Proyectos', 'ce-construction' ),
				'item'     => get_post_type_archive_link( 'proyecto' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post_id ),
				'item'     => get_permalink( $post_id ),
			),
		),
	);
	ce_construction_output_json_ld( $breadcrumb_schema );
}
add_action( 'wp_head', 'ce_construction_schema_project' );

/* =========================================================
 * Añadido en Sprint 5 (módulo Equipo y Clientes).
 * No modifica ninguna función anterior de este archivo
 * (las ramas de breadcrumbs para estos CPTs se insertaron
 * dentro de ce_construction_breadcrumbs(), ver más arriba).
 * ========================================================= */

/**
 * QA-040 (Sprint 8, Entregable 8.7): Servicio y Proyecto (arriba) ya
 * emiten su propio BreadcrumbList JSON-LD, cada uno con el array
 * armado inline. Persona, Cliente y BlogPosting (las 3 funciones de
 * schema de abajo) no lo hacían, pese a tener el mismo breadcrumb
 * HTML de 3 niveles (Inicio > Archivo > Título) ya resuelto por
 * ce_construction_breadcrumbs() más arriba en este archivo —
 * inconsistencia de cobertura entre tipos de contenido
 * estructuralmente iguales, sin que faltara ningún dato para
 * completarla. Se introduce aquí un helper compartido para las 3
 * funciones nuevas (evita triplicar el mismo array de 3 niveles) sin
 * tocar las 2 ya existentes arriba, que siguen con su propio bloque
 * inline tal cual estaban.
 */
function ce_construction_breadcrumb_schema( $archive_label, $archive_url, $item_label, $item_url ) {
	ce_construction_output_json_ld( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Inicio', 'ce-construction' ),
				'item'     => home_url( '/' ),
			),
			array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $archive_label,
				'item'     => $archive_url,
			),
			array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => $item_label,
				'item'     => $item_url,
			),
		),
	) );
}

/**
 * Schema.org JSON-LD para single de Miembro del Equipo: `Person`,
 * vinculado a la organización mediante `worksFor`.
 */
function ce_construction_schema_person() {
	if ( ! ce_construction_seo_enabled() || ! is_singular( 'miembro_equipo' ) ) {
		return;
	}

	$post_id  = get_the_ID();
	$cargo    = get_post_meta( $post_id, '_ce_equipo_cargo', true );
	$linkedin = get_post_meta( $post_id, '_ce_equipo_linkedin', true );

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Person',
		'name'     => get_the_title( $post_id ),
		'url'      => get_permalink( $post_id ),
		'worksFor' => array(
			'@type' => 'GeneralContractor',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( $cargo ) {
		$schema['jobTitle'] = $cargo;
	}
	if ( has_post_thumbnail( $post_id ) ) {
		$schema['image'] = get_the_post_thumbnail_url( $post_id, 'ce-card' );
	}
	if ( $linkedin ) {
		$schema['sameAs'] = array( $linkedin );
	}

	ce_construction_output_json_ld( $schema );

	// QA-040: BreadcrumbList (Inicio > Equipo > Nombre) — mismo trail
	// que ce_construction_breadcrumbs() ya resuelve en HTML para este
	// tipo de contenido.
	ce_construction_breadcrumb_schema(
		__( 'Equipo', 'ce-construction' ),
		get_post_type_archive_link( 'miembro_equipo' ),
		get_the_title( $post_id ),
		get_permalink( $post_id )
	);
}
add_action( 'wp_head', 'ce_construction_schema_person' );

/**
 * Schema.org JSON-LD para single de Cliente: `Organization` simple.
 * El CPT `cliente` no soporta `editor` (solo título + imagen de logo,
 * ver inc/cpt-clientes.php), por lo que este schema es intencionalmente
 * mínimo.
 */
function ce_construction_schema_client_organization() {
	if ( ! ce_construction_seo_enabled() || ! is_singular( 'cliente' ) ) {
		return;
	}

	$post_id = get_the_ID();
	$sitio   = get_post_meta( $post_id, '_ce_cliente_sitio', true );

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_the_title( $post_id ),
	);

	if ( $sitio ) {
		$schema['url'] = $sitio;
	}
	if ( has_post_thumbnail( $post_id ) ) {
		$schema['logo'] = get_the_post_thumbnail_url( $post_id, 'ce-card' );
	}

	ce_construction_output_json_ld( $schema );

	// QA-040: BreadcrumbList (Inicio > Clientes > Nombre).
	ce_construction_breadcrumb_schema(
		__( 'Clientes', 'ce-construction' ),
		get_post_type_archive_link( 'cliente' ),
		get_the_title( $post_id ),
		get_permalink( $post_id )
	);
}
add_action( 'wp_head', 'ce_construction_schema_client_organization' );

/* =========================================================
 * Añadido en Entregable 6B.2 (single.php + comments.php).
 * No modifica ninguna función anterior de este archivo.
 * ========================================================= */

/**
 * Schema.org JSON-LD para entradas de blog (post_type 'post'): BlogPosting.
 * Mantiene la misma consistencia de calidad SEO que el resto de tipos de
 * contenido del tema (Service, Project, Person, Organization-cliente).
 */
function ce_construction_schema_blog_post() {
	if ( ! ce_construction_seo_enabled() || ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_the_ID();

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title( $post_id ),
		'description'      => wp_strip_all_tags( ce_get_short_excerpt( $post_id, 30 ) ),
		'url'              => get_permalink( $post_id ),
		'datePublished'    => get_the_date( 'c', $post_id ),
		'dateModified'     => get_the_modified_date( 'c', $post_id ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink( $post_id ),
		),
	);

	if ( has_post_thumbnail( $post_id ) ) {
		$schema['image'] = get_the_post_thumbnail_url( $post_id, 'ce-hero' );
	}

	ce_construction_output_json_ld( $schema );

	// QA-040: BreadcrumbList (Inicio > Blog > Título) — misma resolución
	// de la Página "de entradas" que ya usa ce_construction_breadcrumbs()
	// para el equivalente en HTML de este mismo tipo de contenido.
	ce_construction_breadcrumb_schema(
		__( 'Blog', 'ce-construction' ),
		get_permalink( get_option( 'page_for_posts' ) ),
		get_the_title( $post_id ),
		get_permalink( $post_id )
	);
}
add_action( 'wp_head', 'ce_construction_schema_blog_post' );

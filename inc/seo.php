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
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
	printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";

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
	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema ) . '</script>' . "\n";
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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";

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
	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ce_construction_schema_project' );

/* =========================================================
 * Añadido en Sprint 5 (módulo Equipo y Clientes).
 * No modifica ninguna función anterior de este archivo
 * (las ramas de breadcrumbs para estos CPTs se insertaron
 * dentro de ce_construction_breadcrumbs(), ver más arriba).
 * ========================================================= */

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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
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

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'ce_construction_schema_blog_post' );

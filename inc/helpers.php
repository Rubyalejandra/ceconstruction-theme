<?php
/**
 * Funciones auxiliares reutilizables en toda la capa de plantillas.
 * Este archivo estaba pendiente en el inventario del proyecto y se
 * incorpora ahora porque header.php / footer.php / template-parts
 * dependen directamente de él (ver TREE.md / PROJECT_STATUS.md).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Devuelve un array [ red => url ] solo con las redes que el
 * administrador configuró en el Customizer (evita imprimir
 * iconos vacíos).
 */
function ce_get_social_links() {
	$networks = array(
		'facebook'  => 'fa-brands fa-facebook-f',
		'instagram' => 'fa-brands fa-instagram',
		'linkedin'  => 'fa-brands fa-linkedin-in',
		'youtube'   => 'fa-brands fa-youtube',
		'tiktok'    => 'fa-brands fa-tiktok',
	);

	$links = array();
	foreach ( $networks as $network => $icon_class ) {
		$url = get_theme_mod( 'ce_social_' . $network, '' );
		if ( ! empty( $url ) ) {
			$links[ $network ] = array(
				'url'   => $url,
				'icon'  => $icon_class,
				'label' => ucfirst( $network ),
			);
		}
	}
	return $links;
}

/**
 * Imprime el bloque de iconos sociales (usado en header y footer).
 */
function ce_render_social_icons( $context = 'footer' ) {
	$links = ce_get_social_links();
	if ( empty( $links ) ) {
		return;
	}
	$class = 'header' === $context ? 'ce-header__social' : 'ce-footer__social';
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $links as $network => $data ) {
		printf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><i class="%3$s" aria-hidden="true"></i></a>',
			esc_url( $data['url'] ),
			esc_attr( $data['label'] ),
			esc_attr( $data['icon'] )
		);
	}
	echo '</div>';
}

/**
 * Número de WhatsApp saneado (solo dígitos) listo para wa.me.
 */
function ce_get_whatsapp_number() {
	$raw = get_theme_mod( 'ce_whatsapp_number', '' );
	return preg_replace( '/\D/', '', $raw );
}

/**
 * IDs de galería de un proyecto como array de enteros.
 * Depende del campo `_ce_proyecto_galeria` guardado en inc/meta-boxes.php.
 */
function ce_get_gallery_ids( $post_id ) {
	$raw = get_post_meta( $post_id, '_ce_proyecto_galeria', true );
	if ( empty( $raw ) ) {
		return array();
	}
	return array_filter( array_map( 'absint', explode( ',', $raw ) ) );
}

/**
 * Imprime un icono Font Awesome de forma segura (solo permite
 * la clase, nunca HTML arbitrario) para el CPT Servicios.
 */
function ce_render_service_icon( $post_id, $default_icon = 'fa-solid fa-trowel' ) {
	$icon = get_post_meta( $post_id, '_ce_icono_fa', true );
	$icon = $icon ? $icon : $default_icon;
	// Solo se permiten letras, números, guiones y espacios (clases FA válidas).
	$icon = preg_replace( '/[^a-zA-Z0-9\-\s]/', '', $icon );
	echo '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i>';
}

/**
 * Teléfono formateado para uso en href="tel:".
 */
function ce_get_phone_href() {
	$phone = get_theme_mod( 'ce_phone', '' );
	return preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Wrapper de excerpt seguro con longitud custom (evita duplicar
 * wp_trim_words en varios template-parts).
 */
function ce_get_short_excerpt( $post_id, $words = 20 ) {
	$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_post_field( 'post_content', $post_id );
	return wp_trim_words( wp_strip_all_tags( $excerpt ), $words );
}

/**
 * Devuelve true si existe al menos un post publicado del CPT dado.
 * Útil para ocultar secciones completas del home si el admin
 * aún no ha cargado contenido (mejor UX que mostrar una sección vacía).
 */
function ce_cpt_has_posts( $post_type ) {
	static $cache = array();
	if ( isset( $cache[ $post_type ] ) ) {
		return $cache[ $post_type ];
	}
	$query = new WP_Query( array(
		'post_type'      => $post_type,
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'no_found_rows'  => true,
	) );
	$has_posts = $query->have_posts();
	wp_reset_postdata();
	$cache[ $post_type ] = $has_posts;
	return $has_posts;
}

/* =========================================================
 * Añadido en Sprint 3 (módulo Servicios) — funciones de
 * relación entre CPTs. Ver DECISIONS.md D-010.
 *
 * No existe un campo relacional directo entre `servicio` y
 * `proyecto` en el modelo de datos actual, así que la relación
 * se infiere por coincidencia de nombre entre las taxonomías
 * `categoria_servicio` y `categoria_proyecto`. Es una solución
 * heurística documentada, no un rediseño del modelo de datos.
 * ========================================================= */

/**
 * Servicios relacionados: mismos términos de `categoria_servicio`,
 * excluyendo el servicio actual. Si el servicio no tiene categoría
 * o no hay más servicios en ella, hace fallback a "los más recientes".
 */
function ce_get_related_services( $service_id, $limit = 3 ) {
	$terms = get_the_terms( $service_id, 'categoria_servicio' );
	$args  = array(
		'post_type'      => 'servicio',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'post__not_in'   => array( $service_id ),
		'no_found_rows'  => true,
	);

	if ( $terms && ! is_wp_error( $terms ) ) {
		$args['tax_query'] = array( array(
			'taxonomy' => 'categoria_servicio',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $terms, 'term_id' ),
		) );
	}

	$query = new WP_Query( $args );

	// Fallback: si no hay suficientes relacionados por categoría, completar con recientes.
	if ( $query->post_count < $limit ) {
		$exclude = array_merge( array( $service_id ), wp_list_pluck( $query->posts, 'ID' ) );
		$fallback = new WP_Query( array(
			'post_type'      => 'servicio',
			'posts_per_page' => $limit - $query->post_count,
			'post_status'    => 'publish',
			'post__not_in'   => $exclude,
			'no_found_rows'  => true,
		) );
		$query->posts     = array_merge( $query->posts, $fallback->posts );
		$query->post_count = count( $query->posts );
	}

	return $query;
}

/**
 * Proyectos relacionados con un servicio: se relacionan por
 * coincidencia de nombre entre el término de `categoria_servicio`
 * del servicio y los términos de `categoria_proyecto` del proyecto.
 * Fallback a los proyectos más recientes si no hay coincidencia.
 */
function ce_get_related_projects( $service_id, $limit = 3 ) {
	$service_terms = get_the_terms( $service_id, 'categoria_servicio' );
	$query = null;

	if ( $service_terms && ! is_wp_error( $service_terms ) ) {
		$term_names = wp_list_pluck( $service_terms, 'name' );
		$project_terms = get_terms( array(
			'taxonomy'   => 'categoria_proyecto',
			'name'       => $term_names,
			'hide_empty' => false,
		) );

		if ( ! empty( $project_terms ) && ! is_wp_error( $project_terms ) ) {
			$query = new WP_Query( array(
				'post_type'      => 'proyecto',
				'posts_per_page' => $limit,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'tax_query'      => array( array(
					'taxonomy' => 'categoria_proyecto',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $project_terms, 'term_id' ),
				) ),
			) );
		}
	}

	if ( ! $query || $query->post_count < $limit ) {
		$exclude = $query ? wp_list_pluck( $query->posts, 'ID' ) : array();
		$fallback = new WP_Query( array(
			'post_type'      => 'proyecto',
			'posts_per_page' => $query ? $limit - $query->post_count : $limit,
			'post_status'    => 'publish',
			'post__not_in'   => $exclude,
			'no_found_rows'  => true,
		) );
		if ( $query ) {
			$query->posts      = array_merge( $query->posts, $fallback->posts );
			$query->post_count = count( $query->posts );
		} else {
			$query = $fallback;
		}
	}

	return $query;
}

/* =========================================================
 * Añadido en Sprint 4 (módulo Proyectos) — función inversa
 * de relación (Proyecto → Servicio), complementaria a
 * ce_get_related_projects() ya existente. Misma heurística
 * documentada en DECISIONS.md D-010, extendida en D-014.
 * ========================================================= */

/**
 * Servicios relacionados con un proyecto: se relacionan por
 * coincidencia de nombre entre los términos de `categoria_proyecto`
 * del proyecto y los términos de `categoria_servicio` de los
 * servicios. Fallback a los servicios más recientes si no hay
 * coincidencia o no hay suficientes resultados.
 */
function ce_get_related_services_for_project( $project_id, $limit = 3 ) {
	$project_terms = get_the_terms( $project_id, 'categoria_proyecto' );
	$query = null;

	if ( $project_terms && ! is_wp_error( $project_terms ) ) {
		$term_names = wp_list_pluck( $project_terms, 'name' );
		$service_terms = get_terms( array(
			'taxonomy'   => 'categoria_servicio',
			'name'       => $term_names,
			'hide_empty' => false,
		) );

		if ( ! empty( $service_terms ) && ! is_wp_error( $service_terms ) ) {
			$query = new WP_Query( array(
				'post_type'      => 'servicio',
				'posts_per_page' => $limit,
				'post_status'    => 'publish',
				'no_found_rows'  => true,
				'tax_query'      => array( array(
					'taxonomy' => 'categoria_servicio',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $service_terms, 'term_id' ),
				) ),
			) );
		}
	}

	if ( ! $query || $query->post_count < $limit ) {
		$exclude = $query ? wp_list_pluck( $query->posts, 'ID' ) : array();
		$fallback = new WP_Query( array(
			'post_type'      => 'servicio',
			'posts_per_page' => $query ? $limit - $query->post_count : $limit,
			'post_status'    => 'publish',
			'post__not_in'   => $exclude,
			'no_found_rows'  => true,
		) );
		if ( $query ) {
			$query->posts      = array_merge( $query->posts, $fallback->posts );
			$query->post_count = count( $query->posts );
		} else {
			$query = $fallback;
		}
	}

	return $query;
}

/* =========================================================
 * Añadido en Sprint UX-3, Entregable UX-3.1 (fase "Optimización
 * UX / Conversión"). No modifica ninguna función anterior de este
 * archivo. Ver DECISIONS.md D-049.
 * ========================================================= */

/**
 * URL/ancla de destino centralizada para todos los CTA "Solicitar
 * Cotización" del tema, según el modo configurado en el Customizer
 * (sección "CE: Formulario de Cotización", theme_mod
 * `ce_quote_form_mode`, registrada en inc/customizer.php).
 *
 * Antes de este Entregable, 6 archivos distintos tenían la ancla
 * "#ce-quote-form" hardcodeada; cambiar el comportamiento exigía
 * editarlos uno por uno. Ahora todos consultan esta única función.
 *
 * Modos soportados (semántica actualizada en Sprint UX-3, Entregable
 * UX-3.2 — ver DECISIONS.md D-053, que reemplaza en este punto el
 * diseño original de D-049):
 *   - 'integrated' (por defecto): los 7 CTA abren el popup de
 *     cotización (`#ce-quote-modal`, footer.php) — igual que en modo
 *     'modal'. La diferencia de este modo respecto a 'modal' ya NO
 *     está en el destino de los CTA, sino en que TAMBIÉN se muestra
 *     la instancia integrada del formulario allí donde ya existía
 *     (sección "Formulario de Cotización" del Home Builder, y la
 *     invocación incondicional de single-servicio.php/single-proyecto.php)
 *     — ver template-parts/quote-form.php.
 *   - 'modal': los 7 CTA abren el popup. La instancia integrada NO
 *     se imprime en ningún punto (guarda ya existente en
 *     template-parts/quote-form.php, sin cambios) — solo existe el
 *     popup.
 *   - 'disabled': devuelve cadena vacía. Cada punto de llamada debe
 *     omitir el botón/enlace por completo cuando el valor es vacío
 *     (mismo patrón ya usado en todo el tema para theme_mods
 *     opcionales — ver p. ej. ce_get_whatsapp_number()).
 *
 * @return string URL/ancla de destino, o cadena vacía si el modo es 'disabled'.
 */
function ce_get_quote_cta_url() {
	$mode = get_theme_mod( 'ce_quote_form_mode', 'integrated' );

	if ( 'disabled' === $mode ) {
		return '';
	}

	// 'integrated' y 'modal' comparten destino desde UX-3.2: el popup
	// de cotización queda disponible en TODAS las páginas (footer.php
	// lo imprime siempre que el modo no sea 'disabled', ver D-053),
	// así que los 7 puntos de CTA del tema pueden abrirlo sin
	// depender de si la página actual tiene o no una instancia
	// integrada del formulario en su propio flujo (p. ej.
	// archive-servicio.php nunca la tuvo).
	return '#ce-quote-modal';
}

/* =========================================================
 * Corrección dentro de Sprint UX-3, Entregable UX-3.2 (fase
 * "Optimización UX / Conversión", paralela al Sprint 8 pausado).
 * No modifica ninguna función anterior de este archivo.
 * ========================================================= */

/**
 * Marca, para la petición HTTP actual, que la instancia INTEGRADA
 * del formulario de cotización (`id="ce-quote-form"`, presentación
 * completa con `<section>`) ya se imprimió en la página en curso.
 *
 * Par con ce_construction_quote_form_rendered_inline(). Existe para
 * separar dos conceptos que `ce_quote_form_mode` conflacionaba:
 * (a) si la instancia integrada se imprime — lo sigue decidiendo,
 * de forma independiente, cada punto de invocación de
 * `template-parts/quote-form.php` (la sección `quote_form` del Home
 * Builder, o la llamada incondicional de `single-servicio.php`/
 * `single-proyecto.php`) — y (b) si el modal (`footer.php`) también
 * debe imprimir su propia copia del `<form id="ce-quote-form">`.
 * Sin esta señal, `footer.php` (que se ejecuta siempre al final de
 * cada plantilla, después de que el contenido principal ya decidió
 * si mostraba o no la instancia integrada) no tendría forma de saber
 * si ya existe un `id="ce-quote-form"` en el documento — ver
 * DECISIONS.md D-052.
 *
 * Implementación mínima con variable estática de función (mismo
 * patrón de caché ya usado por ce_cpt_has_posts() en este archivo):
 * no es un sistema general de estado, es una única bandera de solo
 * lectura/escritura para esta necesidad puntual. La variable
 * estática real vive en ce_construction_quote_form_rendered_inline_get()
 * — esta función es un wrapper delgado que delega ahí, para
 * garantizar que "marcar" y "leer" comparten exactamente el mismo
 * valor (dos `static` declaradas en cuerpos de función distintos NO
 * comparten memoria entre sí en PHP).
 */
function ce_construction_mark_quote_form_rendered_inline() {
	return ce_construction_quote_form_rendered_inline_get( true );
}

/**
 * Devuelve true si, en la petición HTTP actual, ya se imprimió la
 * instancia integrada del formulario de cotización — false en
 * cualquier otro caso, incluida cualquier página donde
 * `template-parts/quote-form.php` nunca llegó a invocarse en su
 * variante integrada (p. ej. `archive-servicio.php`,
 * `archive-proyecto.php`, o el Home con la sección "Formulario de
 * Cotización" desactivada en el Home Builder). Wrapper delgado de
 * solo lectura sobre ce_construction_quote_form_rendered_inline_get().
 */
function ce_construction_quote_form_rendered_inline() {
	return ce_construction_quote_form_rendered_inline_get();
}

/**
 * Almacén real (única variable `static` de todo este mecanismo) de
 * la bandera "¿ya se imprimió la instancia integrada del formulario
 * de cotización en esta petición?". Las 2 funciones públicas de
 * arriba delegan aquí para garantizar que leen/escriben exactamente
 * el mismo valor.
 *
 * @param bool|null $set Si es `true`, marca la bandera como activa.
 *                       Si es `null` (uso normal de lectura), no
 *                       modifica nada, solo consulta el valor actual.
 * @return bool Valor almacenado tras la operación.
 */
function ce_construction_quote_form_rendered_inline_get( $set = null ) {
	static $rendered = false;
	if ( true === $set ) {
		$rendered = true;
	}
	return $rendered;
}

/* =========================================================
 * SPRINT UX-4, ENTREGABLE UX-4.2 — Hero configurable: modo slider.
 * Ver DECISIONS.md D-055.
 * ========================================================= */

/**
 * IDs de las imágenes del slider del Hero como array de enteros.
 * Mismo criterio de parseo/saneamiento que `ce_get_gallery_ids()`
 * (arriba, este mismo archivo) — cadena de IDs separados por comas
 * → `array_map('absint')` → `array_filter()` (descarta ceros/valores
 * inválidos) — adaptado de post meta (`_ce_proyecto_galeria`) a
 * theme_mod (`ce_hero_slides`), por eso recibe la cadena ya leída
 * en vez de un `$post_id`: la usan tanto
 * `ce_construction_sanitize_hero_slides()` (`inc/customizer.php`,
 * saneando el valor entrante del control antes de guardar) como
 * `CE_Customize_Hero_Slides_Control::render_content()` (pintando
 * el preview de miniaturas ya guardadas) y `template-parts/hero.php`
 * (leyendo el theme_mod ya guardado para renderizar el slider en
 * frontend) — una sola fuente de verdad para las 3 lecturas.
 *
 * @param string $raw Cadena de IDs separados por comas (theme_mod
 *                     `ce_hero_slides`, o el valor entrante sin
 *                     guardar todavía en el caso del sanitize_callback).
 * @return int[] IDs de adjunto válidos (> 0), en el orden recibido.
 */
function ce_get_hero_slide_ids( $raw ) {
	if ( empty( $raw ) || ! is_string( $raw ) ) {
		return array();
	}
	return array_filter( array_map( 'absint', explode( ',', $raw ) ) );
}

/* =========================================================
 * SPRINT UX-7, ENTREGABLE UX-7.1 — Unificación del Hero
 * (Home + interior). Ver DECISIONS.md D-063.
 * ========================================================= */

/**
 * Resuelve el estado de fondo de video/slider del Hero a partir de
 * `ce_hero_type` y los theme_mods globales (`ce_hero_video`,
 * `ce_hero_slides`) ya usados por el Hero de Home desde UX-4.1/UX-4.2
 * (D-054/D-055) — única fuente de verdad, para que
 * `template-parts/hero.php` (Home) y `template-parts/page-hero.php`
 * (interior, desde UX-7.1) resuelvan el mismo `ce_hero_type` con
 * exactamente la misma lógica, sin reimplementarla en un segundo
 * archivo. `template-parts/hero.php` es la única fuente del tipo de
 * fondo 'image' (usa siempre `ce_hero_image`); esta función solo
 * cubre los modos 'video' y 'slider', porque el modo 'image' se
 * resuelve de forma distinta en cada contexto (Home usa siempre
 * `ce_hero_image`; el Hero interno usa por defecto la imagen
 * destacada de cada Página/entrada — ver `template-parts/page-hero.php`).
 *
 * Mismo criterio de fallback silencioso ya documentado en D-054/D-055:
 * si el tipo es 'video' pero no hay ningún video subido todavía (o
 * 'slider' sin ninguna imagen seleccionada), ambos flags devuelven
 * `false` y el llamador cae a su fondo de imagen normal.
 *
 * @param string $hero_type Valor ya leído de `get_theme_mod( 'ce_hero_type', 'image' )`.
 * @return array {
 *     @type bool   $is_video    Si debe renderizarse el fondo de video.
 *     @type string $video_url   URL del adjunto de video (vacío si no aplica).
 *     @type string $video_mime  MIME type del adjunto de video (vacío si no aplica).
 *     @type bool   $is_slider   Si debe renderizarse el fondo de slider.
 *     @type array  $slide_urls  URLs de las imágenes del slider (tamaño 'ce-hero'), vacío si no aplica.
 * }
 */
function ce_construction_get_hero_media_state( $hero_type ) {
	$hero_video_id   = get_theme_mod( 'ce_hero_video' );
	$hero_video_url  = $hero_video_id ? wp_get_attachment_url( $hero_video_id ) : '';
	$hero_video_mime = $hero_video_id ? get_post_mime_type( $hero_video_id ) : '';
	$is_video        = ( 'video' === $hero_type && $hero_video_url );

	$hero_slide_ids  = ce_get_hero_slide_ids( get_theme_mod( 'ce_hero_slides', '' ) );
	$hero_slide_urls = array();
	foreach ( $hero_slide_ids as $slide_id ) {
		$slide_url = wp_get_attachment_image_url( $slide_id, 'ce-hero' );
		if ( $slide_url ) {
			$hero_slide_urls[] = $slide_url;
		}
	}
	$is_slider = ( 'slider' === $hero_type && ! empty( $hero_slide_urls ) );

	return array(
		'is_video'   => (bool) $is_video,
		'video_url'  => $is_video ? $hero_video_url : '',
		'video_mime' => $is_video ? $hero_video_mime : '',
		'is_slider'  => (bool) $is_slider,
		'slide_urls' => $is_slider ? $hero_slide_urls : array(),
	);
}

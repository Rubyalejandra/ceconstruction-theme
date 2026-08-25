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

/* =========================================================
 * FASE "Optimización UX / Conversión", SPRINT UX-7, ENTREGABLE
 * UX-7.3 — "Aprovechamiento de espacios vacíos en sidebars"
 * (Servicios/Proyectos, template-parts/sidebar-servicios.php y
 * template-parts/sidebar-proyectos.php).
 *
 * Único helper nuevo de este Entregable: ambos sidebars pueden
 * mostrar, en su nuevo slot configurable desde el Customizer
 * ('ce_sidebar_servicios_slot' / 'ce_sidebar_proyectos_slot', ver
 * inc/customizer.php), un testimonio individual — se centraliza
 * aquí la consulta en vez de duplicarla en los 2 archivos.
 * ========================================================= */

/**
 * Un único testimonio publicado, elegido al azar en cada carga.
 * Devuelve null si el CPT `testimonio` no tiene contenido (el
 * llamador debe ocultar el slot en ese caso, mismo criterio que
 * ce_cpt_has_posts() ya aplica en el resto del tema).
 *
 * `orderby => rand` es intencional y exclusivo de este helper: es
 * una única fila (`posts_per_page => 1`) sobre un CPT pequeño
 * (testimonios de clientes), así que el costo de no cachear la
 * query es insignificante frente al beneficio de variar el
 * testimonio mostrado en el sidebar entre visitas — a diferencia
 * de template-parts/testimonials.php (sección del Home), que sí
 * necesita orden estable porque muestra TODOS los testimonios en
 * un slider, no uno solo.
 */
function ce_get_random_testimonio() {
	$query = new WP_Query( array(
		'post_type'      => 'testimonio',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'rand',
		'no_found_rows'  => true,
	) );

	return $query->have_posts() ? $query : null;
}

/**
 * Sprint UX-7, Entregable UX-7.8 (D-077): resuelve el video opcional
 * de un testimonio a partir de sus metadatos (`_ce_testimonio_video_id`
 * / `_ce_testimonio_video_url`, ver inc/meta-boxes.php), validando el
 * recurso antes de devolverlo.
 *
 * Prioridad: video local (Biblioteca de Medios) sobre URL externa, si
 * ambos estuvieran guardados — evita ambigüedad sin necesitar un
 * tercer campo de "tipo de fuente". Video local inválido en el
 * momento de leerlo (adjunto borrado después de guardarse, o ya no
 * es un mime `video/*`) se trata como "sin video local" y la función
 * continúa evaluando la URL externa guardada, si existe; si tampoco
 * hay URL externa válida, devuelve null (testimonio sin video, mismo
 * comportamiento que si el campo nunca se hubiera rellenado).
 *
 * URL externa: se resuelve exclusivamente vía `wp_oembed_get()`
 * (soporte nativo de WordPress). Si el proveedor no es compatible con
 * oEmbed, `wp_oembed_get()` devuelve `false` y esta función también
 * devuelve null — conforme al alcance de UX-7.8, sin integración
 * externa propia ni iframe fabricado a mano.
 *
 * El poster/miniatura NO se resuelve aquí a partir de la imagen
 * destacada del testimonio (eso es responsabilidad de
 * `content-testimonio-card.php`, que ya tiene esa imagen disponible
 * vía `has_post_thumbnail()`/`the_post_thumbnail()` dentro de su
 * propio loop, ver punto 7 del alcance de UX-7.8). Esta función solo
 * añade, para 'video-embed', el `thumbnail_url` que el propio
 * proveedor devuelve como parte de su respuesta oEmbed (mismo
 * mecanismo nativo ya usado por `wp_oembed_get()`, vía
 * `_wp_oembed_get_object()->get_data()` — función pública de
 * WordPress core, no una integración externa nueva) — el llamador la
 * usa solo como fallback si no hay imagen destacada. Para
 * 'video-local' no existe en WordPress core ningún mecanismo fiable
 * de generación automática de miniatura de video sin dependencias
 * externas (ffmpeg u otro servicio); por eso 'poster' viene vacío en
 * ese caso, y el llamador debe usar una alternativa visual (punto 7).
 *
 * @param int $post_id ID del testimonio.
 * @return array|null {
 *     @type string $type   'video-local' o 'video-embed'.
 *     @type string $src    URL directa del archivo (solo 'video-local').
 *     @type string $mime   Mime type del adjunto (solo 'video-local').
 *     @type string $html   Marcado ya generado por `wp_oembed_get()` (solo 'video-embed').
 *     @type string $poster URL de miniatura del proveedor si la ofreció, o cadena vacía.
 * } o null si el testimonio no tiene video (o el guardado ya no es válido).
 */
function ce_get_testimonio_video( $post_id ) {
	$video_id = (int) get_post_meta( $post_id, '_ce_testimonio_video_id', true );

	if ( $video_id ) {
		$mime = get_post_mime_type( $video_id );
		$src  = wp_get_attachment_url( $video_id );
		if ( $src && $mime && 0 === strpos( $mime, 'video/' ) ) {
			return array(
				'type'   => 'video-local',
				'src'    => $src,
				'mime'   => $mime,
				'poster' => '',
			);
		}
		// Adjunto guardado pero ya no válido (borrado / ya no es video):
		// se trata como "sin video local" y se continúa evaluando si
		// además hay una URL externa guardada, en vez de descartar el
		// testimonio de inmediato.
	}

	$video_url = get_post_meta( $post_id, '_ce_testimonio_video_url', true );
	if ( $video_url ) {
		$embed_html = wp_oembed_get( $video_url );
		if ( $embed_html ) {
			$poster = '';
			if ( function_exists( '_wp_oembed_get_object' ) ) {
				$oembed_data = _wp_oembed_get_object()->get_data( $video_url );
				if ( $oembed_data && ! empty( $oembed_data->thumbnail_url ) ) {
					$poster = esc_url_raw( $oembed_data->thumbnail_url );
				}
			}
			return array(
				'type'   => 'video-embed',
				'html'   => $embed_html,
				'poster' => $poster,
			);
		}
	}

	return null;
}

/**
 * Sprint UX-7, Entregable UX-7.10 (D-079): resuelve el contenido y la
 * configuración del Popup de Oferta a partir de sus theme_mods (ver
 * inc/customizer.php, sección `ce_section_offer_popup`).
 *
 * Centraliza aquí la condición de "no mostrar nada" (desactivado, o
 * mal configurado — sin título o sin URL de botón resoluble) para que
 * `template-parts/offer-popup.php` no tenga que repetir esa lógica ni
 * `footer.php` decidir por su cuenta si incluir el template-part.
 *
 * Cuando `ce_offer_popup_action` es 'quote_form', reutiliza
 * `ce_get_quote_cta_url()` (mismo helper ya usado por
 * cta.php/financing.php) en vez de leer `ce_quote_form_mode`
 * directamente — así, si el Formulario de Cotización está
 * globalmente desactivado (`ce_quote_form_mode = 'disabled'`), ese
 * helper devuelve '' y este popup se desactiva solo (no tiene sentido
 * un popup cuyo único destino configurado ya no existe en el sitio).
 *
 * 🆕 Ajuste puntual (mismo Entregable UX-7.10, ver DECISIONS.md D-080):
 * `icon` (clase Font Awesome, mismo mecanismo que `ce_cta_icon` de
 * UX-7.4) y `badge_text` (insignia corta opcional encima del título,
 * mismo patrón visual que `.ce-hero-quote-card__badge` de UX-7.2/D-065)
 * se agregaron para dar al popup un tratamiento visual más propio de
 * una oferta — ninguno de los dos es obligatorio para que el popup se
 * muestre: `icon` siempre tiene un valor por defecto, y `badge_text`
 * simplemente no imprime la insignia si se deja vacío.
 *
 * @return array|null Null si el popup no debe imprimirse.
 */
function ce_get_offer_popup_data() {
	if ( ! get_theme_mod( 'ce_offer_popup_enabled', false ) ) {
		return null;
	}

	$title = get_theme_mod( 'ce_offer_popup_title', '' );
	$text  = get_theme_mod( 'ce_offer_popup_text', '' );
	if ( ! $title ) {
		// Sin título no hay oferta que mostrar; se trata como "no
		// configurado todavía" en vez de imprimir un popup vacío.
		return null;
	}

	$action = get_theme_mod( 'ce_offer_popup_action', 'quote_form' );
	if ( 'url' === $action ) {
		$btn_url = get_theme_mod( 'ce_offer_popup_url', '' );
	} else {
		$btn_url = ce_get_quote_cta_url();
	}
	if ( ! $btn_url ) {
		return null;
	}

	return array(
		'title'            => $title,
		'text'             => $text,
		'badge_text'       => get_theme_mod( 'ce_offer_popup_badge_text', '' ),
		'icon'             => get_theme_mod( 'ce_offer_popup_icon', 'fa-solid fa-tags' ),
		'btn_text'         => get_theme_mod( 'ce_offer_popup_btn_text', __( 'Quiero mi cotización', 'ce-construction' ) ),
		'btn_url'          => $btn_url,
		'delay_seconds'    => (int) get_theme_mod( 'ce_offer_popup_delay', 6 ),
		'dismiss_minutes'  => (int) get_theme_mod( 'ce_offer_popup_dismiss_minutes', 1440 ),
		'convert_minutes'  => (int) get_theme_mod( 'ce_offer_popup_convert_minutes', 10080 ),
	);
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
 *     @type array  $slides      Slides del slider, vacío si no aplica. Cada elemento:
 *                                { url: string, position: string (valor CSS background-position) }.
 * }
 */
function ce_construction_get_hero_media_state( $hero_type ) {
	$hero_video_id   = get_theme_mod( 'ce_hero_video' );
	$hero_video_url  = $hero_video_id ? wp_get_attachment_url( $hero_video_id ) : '';
	$hero_video_mime = $hero_video_id ? get_post_mime_type( $hero_video_id ) : '';
	$is_video        = ( 'video' === $hero_type && $hero_video_url );

	// 🆕 Sprint UX-11 (D-083): cada slide expone ahora también su
	// posición de fondo configurable por imagen (ver
	// inc/hero-image-position.php) — antes solo se exponía la URL,
	// siempre con background-position:center implícito (CSS, sección
	// 10/27). Sin esta información, el llamador no podría aplicar la
	// posición elegida por el administrador para esa imagen concreta.
	$hero_slide_ids = ce_get_hero_slide_ids( get_theme_mod( 'ce_hero_slides', '' ) );
	$hero_slides    = array();
	foreach ( $hero_slide_ids as $slide_id ) {
		$slide_url = wp_get_attachment_image_url( $slide_id, 'ce-hero' );
		if ( $slide_url ) {
			$hero_slides[] = array(
				'url'      => $slide_url,
				'position' => ce_construction_get_hero_background_position( $slide_id ),
			);
		}
	}
	$is_slider = ( 'slider' === $hero_type && ! empty( $hero_slides ) );

	return array(
		'is_video'   => (bool) $is_video,
		'video_url'  => $is_video ? $hero_video_url : '',
		'video_mime' => $is_video ? $hero_video_mime : '',
		'is_slider'  => (bool) $is_slider,
		'slides'     => $is_slider ? $hero_slides : array(),
	);
}

/* =========================================================
 * SPRINT UX-11 — Hero: panel del formulario, altura, Hero interno
 * propio, posicionamiento de imagen y overlay configurable. Ver
 * DECISIONS.md D-083, D-084, D-086.
 * ========================================================= */

/**
 * Sprint UX-11, Entregable único (punto 3 del plan aprobado — ver
 * DECISIONS.md D-084, que documenta la modificación explícita de
 * D-063). Resuelve la imagen de fondo del Hero INTERNO
 * (`template-parts/page-hero.php`), que desde este Entregable ya NO
 * comparte el modo video/slider global del Home (revierte esa parte
 * puntual de D-063; la función compartida
 * `ce_construction_get_hero_media_state()` de arriba sigue existiendo
 * y sigue sirviendo exclusivamente al Hero de Home).
 *
 * Orden de resolución:
 *   1. Imagen destacada del post/página actual (`$image_id`).
 *   2. Fallback: la imagen de fondo configurada para el Hero de Home
 *      (`ce_hero_image`) — solo si el post no tiene imagen destacada
 *      propia. Nunca video ni slider, en ningún caso.
 *   3. Si tampoco existe esa imagen: cadena vacía — `page-hero.php`
 *      ya tiene un fondo de color sólido de respaldo
 *      (`background-color: var(--ce-color-primary)`, sección 20 de
 *      main.css), comportamiento histórico sin cambios.
 *
 * @param int $image_id ID de la imagen destacada del post/página actual (0 si no tiene).
 * @return array { @type string $url URL de la imagen (tamaño 'ce-hero'), vacío si ninguna disponible.
 *                 @type int    $attachment_id ID del adjunto realmente usado (0 si ninguno). }
 */
function ce_construction_get_page_hero_image_url( $image_id ) {
	$image_id = absint( $image_id );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, 'ce-hero' );
		if ( $url ) {
			return array( 'url' => $url, 'attachment_id' => $image_id );
		}
	}

	$fallback_id = absint( get_theme_mod( 'ce_hero_image' ) );
	if ( $fallback_id ) {
		$url = wp_get_attachment_image_url( $fallback_id, 'ce-hero' );
		if ( $url ) {
			return array( 'url' => $url, 'attachment_id' => $fallback_id );
		}
	}

	return array(
		'url'           => '',
		'attachment_id' => 0,
	);
}

/**
 * Sprint UX-11, punto 5 del plan aprobado (overlay/gradiente
 * configurable — ver DECISIONS.md D-086). Convierte un color
 * hexadecimal ('#rrggbb' o '#rgb') a un array [r, g, b]. No existe
 * un helper nativo de WordPress para esto (`sanitize_hex_color()`
 * valida el formato pero no lo descompone) — función pura, sin
 * efectos secundarios, reutilizable por cualquier necesidad futura
 * de manipular color en el theme.
 *
 * @param string $hex Color hexadecimal, con o sin '#'.
 * @return int[] [r, g, b] (0-255 cada uno). Si el valor es inválido,
 *               devuelve el equivalente de --ce-color-primary-dark
 *               (#081A2B) — mismo color que usaba el gradiente fijo
 *               del overlay antes de este Entregable.
 */
function ce_construction_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return array( 8, 26, 43 ); // --ce-color-primary-dark, color de respaldo.
	}
	return array(
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	);
}

/**
 * Sprint UX-11, punto 5 del plan aprobado (ver DECISIONS.md D-086).
 * Construye el valor CSS del gradiente de overlay a partir de los 3
 * nuevos theme_mods (`ce_hero_overlay_color`, `ce_hero_overlay_direction`,
 * `ce_hero_overlay_extent`), reutilizando exactamente las mismas
 * proporciones de opacidad (0.92 / 0.78 / 0.45) que el gradiente fijo
 * que reemplaza — con los valores por defecto de los 3 controles
 * nuevos, el resultado es *pixel-idéntico* al gradiente fijo anterior
 * (mismo color #081A2B, misma dirección 120deg, mismo punto medio al
 * 55%). El 4° control ya existente (`ce_hero_overlay_opacity`) no se
 * toca aquí — sigue aplicándose como multiplicador de opacidad sobre
 * el elemento `.ce-hero__overlay`/`.ce-page-hero__overlay` completo
 * (CSS, sección 10), independiente de este gradiente.
 *
 * "Extensión" (punto explícito del plan aprobado: 40/50/70/100):
 * controla en qué % del Hero se alcanza la opacidad mínima (0.45).
 * Más allá de ese punto, el color se mantiene constante hasta el
 * 100% (4° stop idéntico al 3°) — así se logra la "zona oscura donde
 * está el texto + transición progresiva hacia una zona donde se
 * conserva más visible la imagen" pedida en el plan, sin volver a
 * oscurecer después del punto de extensión.
 *
 * @return string Valor listo para usar como `linear-gradient(...)` en CSS.
 */
function ce_construction_get_hero_overlay_gradient_css() {
	$color_hex     = get_theme_mod( 'ce_hero_overlay_color', '#081A2B' );
	$direction_key = get_theme_mod( 'ce_hero_overlay_direction', 'diagonal' );
	$extent        = (int) get_theme_mod( 'ce_hero_overlay_extent', 100 );

	$directions = array(
		'diagonal'  => '120deg',
		'to-bottom' => '180deg',
		'to-top'    => '0deg',
		'to-right'  => '90deg',
		'to-left'   => '270deg',
	);
	$degrees = isset( $directions[ $direction_key ] ) ? $directions[ $direction_key ] : $directions['diagonal'];

	list( $r, $g, $b ) = ce_construction_hex_to_rgb( $color_hex );
	$mid_stop = (int) round( $extent * 0.55 );

	return sprintf(
		'linear-gradient(%1$s, rgba(%2$d,%3$d,%4$d,0.92) 0%%, rgba(%2$d,%3$d,%4$d,0.78) %5$d%%, rgba(%2$d,%3$d,%4$d,0.45) %6$d%%, rgba(%2$d,%3$d,%4$d,0.45) 100%%)',
		$degrees,
		$r,
		$g,
		$b,
		$mid_stop,
		$extent
	);
	// Nota: se usan exclusivamente marcadores posicionales (%1$s,
	// %2$d...) porque PHP no permite mezclar marcadores posicionales y
	// no posicionales en el mismo sprintf() de forma fiable — así
	// $r/$g/$b se reutilizan en los 4 stops sin repetirlos en la
	// lista de argumentos.
}

/**
 * Sprint UX-7, Entregable UX-7.4 ("CTA: icono y color de botón
 * configurables").
 *
 * Oscurece un color hexadecimal un porcentaje fijo, para derivar
 * automáticamente el estado ':hover' del color de botón que el
 * administrador elige en el Customizer (`ce_cta_btn_color` /
 * `ce_cta2_btn_color`, ver inc/customizer.php) — sin pedirle un
 * segundo color solo para el hover. Mismo criterio de "un color
 * base + una variante oscura derivada" que el tema ya aplica de
 * forma fija a `--ce-color-primary`/`--ce-color-primary-dark` y
 * `--ce-color-secondary`/`--ce-color-secondary-dark`, aquí
 * calculado en tiempo real porque el color de entrada es dinámico.
 *
 * @param string $hex     Color hexadecimal ('#rrggbb' o '#rgb'; el '#' es opcional).
 * @param int    $percent Porcentaje a oscurecer (0-100). Por defecto 15.
 * @return string Color hexadecimal '#rrggbb' oscurecido. Si `$hex` no es un
 *                hexadecimal válido, devuelve '#000000' como resguardo — no
 *                debería alcanzarse en la práctica porque el valor ya pasó
 *                por `sanitize_hex_color()` antes de guardarse en el
 *                theme_mod (ver inc/customizer.php).
 */
function ce_construction_hex_darken( $hex, $percent = 15 ) {
	$hex = ltrim( (string) $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#000000';
	}

	$percent = max( 0, min( 100, (int) $percent ) );
	$rgb     = array_map( 'hexdec', str_split( $hex, 2 ) );

	foreach ( $rgb as &$channel ) {
		$channel = (int) max( 0, round( $channel * ( 1 - ( $percent / 100 ) ) ) );
	}
	unset( $channel );

	return '#' . implode(
		'',
		array_map(
			function ( $channel ) {
				return str_pad( dechex( $channel ), 2, '0', STR_PAD_LEFT );
			},
			$rgb
		)
	);
}

/**
 * Sprint UX-7, Entregable UX-7.5 ("Logo independiente Header/Footer").
 *
 * Imprime el logo del footer, con 3 niveles de fallback, en este
 * orden:
 *   1. `ce_footer_logo` (theme_mod nuevo de este Entregable) — si el
 *      administrador subió una variante de logo específica para el
 *      footer, se usa esa, envuelta en el MISMO markup
 *      (`a.custom-logo-link > img.custom-logo`) que ya produce
 *      `the_custom_logo()` nativo — así el footer conserva el
 *      enlace a portada y ninguna regla CSS nueva es necesaria
 *      (`.ce-footer__brand img`, main.css, ya aplica).
 *   2. `has_custom_logo()` / `the_custom_logo()` — el logo nativo del
 *      sitio ("Identidad del sitio"), igual que antes de este
 *      Entregable si no se configura `ce_footer_logo`.
 *   3. Nombre del sitio en texto (`bloginfo( 'name' )`) — igual que
 *      antes de este Entregable si no hay ningún logo configurado.
 *
 * Centraliza en un único helper la lógica que antes vivía inline en
 * footer.php, siguiendo el mismo criterio ya aplicado en el resto
 * del tema (p. ej. `ce_render_social_icons()`) de no duplicar
 * markup condicional directamente en los templates. `header.php` NO
 * usa este helper — sigue llamando a `has_custom_logo()`/
 * `the_custom_logo()` directamente, sin cambios (alcance de UX-7.5,
 * ver DECISIONS.md D-069).
 */
function ce_render_footer_logo() {
	$footer_logo_id = get_theme_mod( 'ce_footer_logo' );

	if ( $footer_logo_id ) {
		$image = wp_get_attachment_image(
			$footer_logo_id,
			'full',
			false,
			array( 'class' => 'custom-logo' )
		);

		if ( $image ) {
			printf(
				'<a href="%1$s" class="custom-logo-link" rel="home">%2$s</a>',
				esc_url( home_url( '/' ) ),
				$image // Ya escapado por wp_get_attachment_image().
			);
			return;
		}
		// $footer_logo_id apunta a un adjunto inexistente/borrado:
		// wp_get_attachment_image() devuelve '' — cae a los
		// fallbacks de abajo en vez de imprimir un <a> vacío.
	}

	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<h3 class="ce-text-white"><?php bloginfo( 'name' ); ?></h3>
	<?php
}

/* =========================================================
 * Sprint UX-7, Entregable UX-7.6 ("Estadísticas configurables
 * desde el Customizer"). Ver docs/DECISIONS.md D-070.
 *
 * template-parts/stats.php tenía sus 4 estadísticas (número,
 * sufijo, etiqueta, icono) escritas directamente en un array de
 * PHP, con cantidad fija. Este bloque reemplaza esa fuente por un
 * theme_mod editable (control repeater custom del Customizer, ver
 * inc/customizer.php "CE: Estadísticas"), conservando el filtro
 * `ce_stats_items` ya existente como mecanismo de fallback/
 * extensión para desarrolladores — sin eliminarlo, tal como pide
 * explícitamente el plan (UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4).
 * ========================================================= */

/**
 * Valores por defecto — idénticos a los 4 que
 * template-parts/stats.php tenía hardcodeados antes de este
 * Entregable. Se usan como `default` del setting
 * `ce_stats_custom_items` (para que el panel del Customizer
 * aparezca ya poblado y editable, en vez de vacío) y como
 * comportamiento efectivo mientras el administrador no lo toque:
 * cero cambio visual por defecto.
 *
 * @return array<int,array{count:int,suffix:string,label:string,icon:string}>
 */
function ce_construction_default_stats_items() {
	return array(
		array(
			'count'  => 350,
			'suffix' => '+',
			'label'  => __( 'Proyectos realizados', 'ce-construction' ),
			'icon'   => 'fa-solid fa-building',
		),
		array(
			'count'  => 280,
			'suffix' => '+',
			'label'  => __( 'Clientes satisfechos', 'ce-construction' ),
			'icon'   => 'fa-solid fa-face-smile',
		),
		array(
			'count'  => 12,
			'suffix' => '+',
			'label'  => __( 'Años de experiencia', 'ce-construction' ),
			'icon'   => 'fa-solid fa-award',
		),
		array(
			'count'  => 60,
			'suffix' => '+',
			'label'  => __( 'Empleados', 'ce-construction' ),
			'icon'   => 'fa-solid fa-helmet-safety',
		),
	);
}

/**
 * Versión JSON de ce_construction_default_stats_items(), usada
 * como `default` del setting `ce_stats_custom_items`
 * (inc/customizer.php) y como respaldo de
 * ce_construction_get_stats_items() si el theme_mod no se ha
 * guardado nunca.
 */
function ce_construction_default_stats_items_json() {
	return wp_json_encode( ce_construction_default_stats_items() );
}

/**
 * Decodifica y normaliza el JSON guardado en `ce_stats_custom_items`.
 * Única fuente de saneamiento para este dato: la usan tanto el
 * `sanitize_callback` del setting (`ce_construction_sanitize_stats_items()`,
 * inc/customizer.php) como el `render_content()` del control repeater
 * (`CE_Customize_Stats_Items_Control`) como `ce_construction_get_stats_items()`
 * (frontend) — evita que el panel de administración y el resultado
 * renderizado puedan divergir por sanear cada uno a su manera.
 *
 * Tolerante a datos corruptos/vacíos: cualquier item sin `label` (el
 * único campo obligatorio) se descarta en silencio. El icono se sanea
 * con la misma whitelist de caracteres ya usada por
 * `ce_render_service_icon()` (solo letras/números/guiones/espacios,
 * válido para clases Font Awesome) — a diferencia del selector
 * curado de UX-7.4/D-068 (un único icono, alta visibilidad), aquí se
 * mantiene texto libre saneado, el mismo criterio que ya usa el
 * campo "Clase de icono Font Awesome" del metabox de Servicio
 * (inc/meta-boxes.php), consistente con que este es un repeater de
 * cantidad variable, no un único selector.
 *
 * Límite defensivo de 12 estadísticas (no exigido por el plan, que
 * solo pide "cantidad variable, no fija en 4"): protege el layout de
 * un número no realista de items sin bloquear el caso de uso real
 * del benchmark competitivo que originó este Entregable (D-058).
 *
 * @param string $json Valor guardado (JSON) o cadena vacía/corrupta.
 * @return array<int,array{count:int,suffix:string,label:string,icon:string}>
 */
function ce_construction_decode_stats_items( $json ) {
	$items = json_decode( (string) $json, true );
	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		if ( '' === $label ) {
			continue; // La etiqueta es el único campo obligatorio: sin ella, la fila se descarta.
		}

		$icon = isset( $item['icon'] ) ? preg_replace( '/[^a-zA-Z0-9\-\s]/', '', $item['icon'] ) : '';
		$icon = trim( (string) $icon );
		if ( '' === $icon ) {
			$icon = 'fa-solid fa-chart-line';
		}

		$normalized[] = array(
			'count'  => isset( $item['count'] ) ? absint( $item['count'] ) : 0,
			'suffix' => isset( $item['suffix'] ) ? sanitize_text_field( mb_substr( (string) $item['suffix'], 0, 6 ) ) : '',
			'label'  => mb_substr( $label, 0, 60 ),
			'icon'   => $icon,
		);

		if ( count( $normalized ) >= 12 ) {
			break;
		}
	}

	return $normalized;
}

/**
 * Devuelve las estadísticas efectivas a renderizar en
 * template-parts/stats.php: decodifica el theme_mod
 * `ce_stats_custom_items` (con el JSON por defecto de arriba si el
 * administrador nunca lo tocó) y aplica el filtro `ce_stats_items`
 * ya existente antes de este Entregable — se conserva sin
 * eliminarlo, tal como exige el plan, para no romper ninguna
 * extensión de desarrollador que ya lo estuviera usando.
 *
 * @return array
 */
function ce_construction_get_stats_items() {
	$raw   = get_theme_mod( 'ce_stats_custom_items', ce_construction_default_stats_items_json() );
	$items = ce_construction_decode_stats_items( $raw );
	return apply_filters( 'ce_stats_items', $items );
}

/* =========================================================
 * SPRINT UX-7, ENTREGABLE UX-7.7 — Franja de insignias de
 * confianza / licencias. Ver DECISIONS.md D-071.
 *
 * A diferencia de ce_construction_default_stats_items() (UX-7.6),
 * esta funcionalidad es contenido nuevo, sin ningún valor previo
 * hardcodeado que preservar — no existe un "default" no vacío:
 * el `default` del setting `ce_trust_badges_items` es una cadena
 * vacía, que decodifica a un array vacío. Mismo criterio de
 * auto-ocultado ya usado por template-parts/stats.php (UX-7.6)
 * cuando el administrador quita todas las filas.
 * ========================================================= */

/**
 * Decodifica y normaliza el JSON guardado en `ce_trust_badges_items`.
 * Única fuente de saneamiento para este dato — la usan tanto el
 * `sanitize_callback` del setting (`ce_construction_sanitize_trust_badges()`,
 * inc/customizer.php) como el `render_content()` del control repeater
 * (`CE_Customize_Trust_Badges_Control`) y `ce_construction_get_trust_badges()`
 * (frontend) — mismo principio arquitectónico ya aplicado en
 * ce_construction_decode_stats_items() (UX-7.6) y
 * ce_construction_decode_home_sections_order() (UX-1.2).
 *
 * Cada insignia admite: una imagen (adjunto de Medios, opcional),
 * una etiqueta de texto (obligatoria — se usa como `alt` de la
 * imagen si hay imagen, o como único contenido visible si no la
 * hay), un número de licencia opcional, y un enlace de verificación
 * opcional. Tolerante a datos corruptos/vacíos: cualquier fila sin
 * `label` (el único campo obligatorio, mismo criterio que
 * ce_construction_decode_stats_items()) se descarta en silencio.
 *
 * Límite defensivo de 12 insignias (mismo criterio y mismo número
 * que ce_construction_decode_stats_items(), UX-7.6) — el plan pide
 * "sin límite fijo" en el sentido de "no fijo en un número bajo
 * arbitrario como 3 ó 4", no en el sentido de "ilimitado de verdad";
 * protege el layout de una cantidad no realista de insignias.
 *
 * @param string $json Valor guardado (JSON) o cadena vacía/corrupta.
 * @return array<int,array{image_id:int,label:string,license:string,url:string}>
 */
function ce_construction_decode_trust_badges( $json ) {
	$items = json_decode( (string) $json, true );
	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();
	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
		if ( '' === $label ) {
			continue; // La etiqueta es el único campo obligatorio: sin ella, la fila se descarta.
		}

		$normalized[] = array(
			'image_id' => isset( $item['image_id'] ) ? absint( $item['image_id'] ) : 0,
			'label'    => mb_substr( $label, 0, 60 ),
			'license'  => isset( $item['license'] ) ? sanitize_text_field( mb_substr( (string) $item['license'], 0, 40 ) ) : '',
			'url'      => isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '',
		);

		if ( count( $normalized ) >= 12 ) {
			break;
		}
	}

	return $normalized;
}

/**
 * Devuelve las insignias de confianza efectivas a renderizar en
 * template-parts/trust-badges.php: decodifica el theme_mod
 * `ce_trust_badges_items` (cadena vacía por defecto — sin
 * regresión posible, ver nota de arriba) y aplica un filtro nuevo
 * `ce_trust_badges_items` (mismo principio de extensión para
 * desarrolladores ya aplicado por `ce_stats_items` en UX-7.6, sin
 * precedente previo que preservar aquí porque esta sección no
 * existía antes de este Entregable).
 *
 * @return array
 */
function ce_construction_get_trust_badges() {
	$raw   = get_theme_mod( 'ce_trust_badges_items', '' );
	$items = ce_construction_decode_trust_badges( $raw );
	return apply_filters( 'ce_trust_badges_items', $items );
}

/**
 * Título accesible de una insignia de confianza (usado como
 * `title=` en el modo compacto de template-parts/trust-badges.php,
 * donde el número de licencia no se muestra visualmente por
 * espacio — sigue siendo consultable sin ocupar espacio visual).
 * Incluye el número de licencia cuando existe.
 *
 * Definida aquí (inc/helpers.php) y no dentro del propio
 * template-part: template-parts/trust-badges.php puede invocarse
 * DOS VECES en la misma carga de página (la sección completa vía
 * el Home Builder + el modo compacto vía template-parts/hero.php,
 * si ambas están activas a la vez) — declarar una función dentro
 * de un archivo que `get_template_part()` puede incluir más de una
 * vez provocaría un fatal por redeclaración. Mismo principio ya
 * aplicado en todo el proyecto: la lógica reutilizable vive en
 * inc/helpers.php, nunca dentro de un template-part.
 *
 * @param array $badge Una insignia ya normalizada por
 *                      ce_construction_decode_trust_badges().
 * @return string
 */
function ce_construction_trust_badge_title( $badge ) {
	if ( ! empty( $badge['license'] ) ) {
		return sprintf(
			/* translators: 1: nombre de la insignia, 2: número de licencia */
			__( '%1$s — Lic. %2$s', 'ce-construction' ),
			$badge['label'],
			$badge['license']
		);
	}
	return $badge['label'];
}

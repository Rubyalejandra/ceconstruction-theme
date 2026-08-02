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

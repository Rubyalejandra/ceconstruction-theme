<?php
/**
 * Sprint UX-11 (fase "Optimización UX / Conversión") — punto 4 del
 * plan aprobado: posicionamiento de las imágenes de Hero.
 *
 * VERIFICACIÓN PREVIA (instrucción explícita del usuario antes de
 * implementar): WordPress **no** ofrece de forma nativa un selector
 * de "punto focal" (coordenadas X/Y) para adjuntos/imágenes
 * destacadas. El editor de imagen nativo (`wp-admin/includes/image.php`,
 * pantalla "Editar imagen") permite recortar, rotar y escalar, pero
 * no guarda ningún meta de punto focal reutilizable por el frontend.
 * Esa capacidad SÍ existe en algunos plugins (p. ej. plugins de
 * optimización de imágenes), pero el proyecto no usa plugins
 * adicionales (restricción explícita del plan aprobado).
 *
 * ALTERNATIVA elegida (nativa, sin plugin, bajo mantenimiento): un
 * campo adicional en la pantalla clásica de edición de adjuntos de
 * WordPress (`wp-admin/post.php?action=edit` sobre un adjunto), vía
 * los filtros nativos `attachment_fields_to_edit`/
 * `attachment_fields_to_save` (API pública de WordPress, sin
 * dependencias externas, en uso desde WP 2.5). El administrador
 * elige una de 9 posiciones predefinidas (esquinas/lados/centro,
 * mismos valores que la propiedad CSS `background-position`) en vez
 * de un punto focal libre por coordenadas — deliberadamente más
 * simple, sin JavaScript propio, sin nueva pantalla de admin.
 *
 * LIMITACIÓN CONOCIDA (se documenta explícitamente, no se oculta):
 * estos filtros solo se renderizan en la pantalla CLÁSICA de edición
 * de adjuntos (la que se abre al hacer clic en el NOMBRE de una
 * imagen dentro de la lista de Medios en modo "Lista"), no dentro
 * del modal de medios moderno (JS/Backbone) que abre, por ejemplo,
 * el selector de imagen del Customizer. Es una ruta de administración
 * adicional, no menos nativa ni menos soportada, pero sí menos
 * descubrible — se documenta el flujo exacto en la descripción del
 * campo mismo, visible para el administrador la primera vez que
 * edita cualquier imagen.
 *
 * Por defecto (sin que el administrador toque nada), el valor es
 * 'center center' — idéntico a `background-position: center` ya
 * usado en todo el theme — cero cambio de comportamiento hasta que
 * se edite explícitamente una imagen.
 *
 * Ver DECISIONS.md D-085.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whitelist cerrada de posiciones — mismos 9 valores que admite
 * nativamente la propiedad CSS `background-position` con palabras
 * clave (sin coordenadas libres, a propósito: mantiene el control
 * simple y sin posibilidad de valores inválidos).
 */
function ce_construction_hero_position_choices() {
	return array(
		'center center' => __( 'Centro (por defecto)', 'ce-construction' ),
		'center top'    => __( 'Arriba', 'ce-construction' ),
		'center bottom' => __( 'Abajo', 'ce-construction' ),
		'left center'   => __( 'Izquierda', 'ce-construction' ),
		'right center'  => __( 'Derecha', 'ce-construction' ),
		'left top'      => __( 'Arriba izquierda', 'ce-construction' ),
		'right top'     => __( 'Arriba derecha', 'ce-construction' ),
		'left bottom'   => __( 'Abajo izquierda', 'ce-construction' ),
		'right bottom'  => __( 'Abajo derecha', 'ce-construction' ),
	);
}

/**
 * Añade el campo "Posición en Heroes" a la pantalla clásica de
 * edición de un adjunto, solo para imágenes (nunca para PDF/video/
 * otros tipos ya subidos al Media Library del tema).
 */
function ce_construction_hero_position_field( $form_fields, $post ) {
	if ( 0 !== strpos( (string) $post->post_mime_type, 'image/' ) ) {
		return $form_fields;
	}

	$current = get_post_meta( $post->ID, '_ce_hero_position', true );
	if ( ! $current ) {
		$current = 'center center';
	}

	$options_html = '';
	foreach ( ce_construction_hero_position_choices() as $value => $label ) {
		$options_html .= sprintf(
			'<option value="%1$s"%2$s>%3$s</option>',
			esc_attr( $value ),
			selected( $current, $value, false ),
			esc_html( $label )
		);
	}

	$form_fields['ce_hero_position'] = array(
		'label' => __( 'Posición en Heroes (CE Construction)', 'ce-construction' ),
		'input' => 'html',
		'html'  => sprintf(
			'<select name="attachments[%1$d][ce_hero_position]" id="attachments-%1$d-ce_hero_position">%2$s</select>',
			(int) $post->ID,
			$options_html
		),
		'helps' => __( 'Solo aplica si esta imagen se usa como fondo de un Hero (portada o interior). Útil cuando la parte importante de la foto no está centrada.', 'ce-construction' ),
	);

	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'ce_construction_hero_position_field', 10, 2 );

/**
 * Guarda el valor elegido, validado contra la whitelist. Sin nonce
 * propio: `attachment_fields_to_save` ya se ejecuta exclusivamente
 * dentro del flujo de guardado nativo de un adjunto (`post.php`),
 * que WordPress protege con su propio nonce de edición de post — el
 * mismo criterio que WordPress aplica a sus propios campos nativos
 * de este mismo filtro (título/pie de foto/texto alternativo).
 */
function ce_construction_hero_position_save( $post, $attachment ) {
	if ( ! isset( $attachment['ce_hero_position'] ) ) {
		return $post;
	}

	$value   = sanitize_text_field( wp_unslash( $attachment['ce_hero_position'] ) );
	$allowed = array_keys( ce_construction_hero_position_choices() );

	if ( 'center center' === $value || ! in_array( $value, $allowed, true ) ) {
		delete_post_meta( $post['ID'], '_ce_hero_position' );
	} else {
		update_post_meta( $post['ID'], '_ce_hero_position', $value );
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'ce_construction_hero_position_save', 10, 2 );

/**
 * Lectura en frontend: valor sanitizado listo para imprimir como
 * `background-position` inline, con el mismo criterio de fallback
 * silencioso que el resto del sistema de Hero (nunca un valor
 * inválido llega al HTML).
 *
 * @param int $attachment_id ID del adjunto de imagen.
 * @return string Valor CSS válido para `background-position` (p. ej. 'center top').
 */
function ce_construction_get_hero_background_position( $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	if ( ! $attachment_id ) {
		return 'center center';
	}

	$value   = get_post_meta( $attachment_id, '_ce_hero_position', true );
	$allowed = array_keys( ce_construction_hero_position_choices() );

	return in_array( $value, $allowed, true ) ? $value : 'center center';
}

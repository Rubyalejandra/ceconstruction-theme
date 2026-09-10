<?php
/**
 * Metaboxes (campos personalizados) para los Custom Post Types.
 * Todos los guardados usan Nonces + sanitización + verificación
 * de permisos, siguiendo las buenas prácticas de seguridad de WP.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registro de metaboxes por CPT.
 */
function ce_construction_add_meta_boxes() {

	add_meta_box( 'ce_servicio_fields', __( 'Detalles del Servicio', 'ce-construction' ), 'ce_render_servicio_fields', 'servicio', 'normal', 'high' );

	add_meta_box( 'ce_proyecto_fields', __( 'Detalles del Proyecto', 'ce-construction' ), 'ce_render_proyecto_fields', 'proyecto', 'normal', 'high' );
	add_meta_box( 'ce_proyecto_gallery', __( 'Galería del Proyecto', 'ce-construction' ), 'ce_render_proyecto_gallery', 'proyecto', 'normal', 'default' );

	add_meta_box( 'ce_testimonio_fields', __( 'Detalles del Testimonio', 'ce-construction' ), 'ce_render_testimonio_fields', 'testimonio', 'normal', 'high' );

	// Sprint UX-7, Entregable UX-7.8 (D-077): video opcional del
	// testimonio. Metabox independiente (no se añade al existente
	// `ce_testimonio_fields`) para mantener ese formulario ya aprobado
	// sin tocar su render/guardado; este campo es completamente
	// opcional y su ausencia no afecta a `ce_testimonio_fields`.
	add_meta_box( 'ce_testimonio_video', __( 'Video del Testimonio (opcional)', 'ce-construction' ), 'ce_render_testimonio_video_field', 'testimonio', 'normal', 'default' );

	add_meta_box( 'ce_equipo_fields', __( 'Detalles del Miembro', 'ce-construction' ), 'ce_render_equipo_fields', 'miembro_equipo', 'normal', 'high' );

	add_meta_box( 'ce_cliente_fields', __( 'Detalles del Cliente', 'ce-construction' ), 'ce_render_cliente_fields', 'cliente', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'ce_construction_add_meta_boxes' );

/* =========================================================
 * RENDER: SERVICIO
 * ========================================================= */
function ce_render_servicio_fields( $post ) {
	wp_nonce_field( 'ce_save_servicio_meta', 'ce_servicio_nonce' );
	$icono = get_post_meta( $post->ID, '_ce_icono_fa', true );
	$enlace_ext = get_post_meta( $post->ID, '_ce_enlace_externo', true );
	?>
	<p>
		<label for="ce_icono_fa"><strong><?php esc_html_e( 'Clase de icono Font Awesome (ej: fa-solid fa-trowel)', 'ce-construction' ); ?></strong></label><br>
		<input type="text" id="ce_icono_fa" name="ce_icono_fa" class="widefat" value="<?php echo esc_attr( $icono ); ?>">
	</p>
	<p>
		<label for="ce_enlace_externo"><strong><?php esc_html_e( 'Enlace (opcional, si no usa el permalink del servicio)', 'ce-construction' ); ?></strong></label><br>
		<input type="url" id="ce_enlace_externo" name="ce_enlace_externo" class="widefat" value="<?php echo esc_attr( $enlace_ext ); ?>">
	</p>
	<?php
}

/* =========================================================
 * RENDER: PROYECTO
 * ========================================================= */
function ce_render_proyecto_fields( $post ) {
	wp_nonce_field( 'ce_save_proyecto_meta', 'ce_proyecto_nonce' );
	$cliente  = get_post_meta( $post->ID, '_ce_proyecto_cliente', true );
	$ubicacion = get_post_meta( $post->ID, '_ce_proyecto_ubicacion', true );
	$fecha    = get_post_meta( $post->ID, '_ce_proyecto_fecha', true );
	// Sprint UX-8, Entregable UX-8.2 (D-109): "proyecto destacado" para
	// la curación de la Galería del Home — ver
	// ce_construction_get_home_gallery_images() en inc/helpers.php.
	// Campo independiente de `_ce_proyecto_media` (galería mixta,
	// D-108): un proyecto puede estar destacado sin tener ninguna
	// imagen todavía (en ese caso simplemente no aporta imagen al
	// mosaico, ver el fallback aprobado de esa función).
	$destacado = (bool) get_post_meta( $post->ID, '_ce_proyecto_destacado', true );
	?>
	<p>
		<label><strong><?php esc_html_e( 'Cliente', 'ce-construction' ); ?></strong></label><br>
		<input type="text" name="ce_proyecto_cliente" class="widefat" value="<?php echo esc_attr( $cliente ); ?>">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Ubicación', 'ce-construction' ); ?></strong></label><br>
		<input type="text" name="ce_proyecto_ubicacion" class="widefat" value="<?php echo esc_attr( $ubicacion ); ?>">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Fecha de entrega', 'ce-construction' ); ?></strong></label><br>
		<input type="date" name="ce_proyecto_fecha" value="<?php echo esc_attr( $fecha ); ?>">
	</p>
	<p class="description"><?php esc_html_e( 'El "Estado" se gestiona con la taxonomía Estado de Proyecto en la barra lateral.', 'ce-construction' ); ?></p>
	<p>
		<label>
			<input type="checkbox" name="ce_proyecto_destacado" value="1" <?php checked( $destacado ); ?>>
			<strong><?php esc_html_e( 'Proyecto destacado en la Galería del Home', 'ce-construction' ); ?></strong>
		</label><br>
		<span class="description"><?php esc_html_e( 'Marca los proyectos que quieres curar para la sección "Galería" del Home. Si ninguno está marcado, esa sección se oculta. Si faltan imágenes para completar el mosaico, se rellena automáticamente con proyectos no destacados.', 'ce-construction' ); ?></span>
	</p>
	<?php
}

/**
 * QA-016 (Sprint 8, Entregable 8.5 — corrección Media): el <script>
 * inline que antes vivía aquí (selector de imágenes de galería vía
 * wp.media, sin wp_enqueue_script() ni dependencia formal de jQuery
 * declarada) se movió a assets/js/admin-proyecto-gallery.js, encolado
 * en inc/enqueue.php con array( 'jquery', 'media-editor' ) como
 * dependencia explícita.
 *
 * Sprint UX-8, Entregable UX-8.1 ("Galería mixta del Proyecto: imagen
 * y/o video, reordenable"): el selector de solo-imágenes anterior se
 * reemplaza por un repeater de ítems mixtos (imagen / video de la
 * Biblioteca / video por URL externa), reordenable con botones ↑/↓
 * (mismo criterio ya usado por Estadísticas/Insignias de Confianza,
 * sin jQuery UI Sortable). Fuente de verdad: meta `_ce_proyecto_media`
 * (JSON), leída/normalizada por `ce_construction_get_proyecto_media_raw()`
 * (inc/helpers.php), que además migra en modo solo-lectura desde el
 * formato antiguo `_ce_proyecto_galeria` si un proyecto no tiene
 * `_ce_proyecto_media` guardado todavía — ver el docblock de esa
 * función para el detalle completo. `_ce_proyecto_galeria` no se
 * elimina: `ce_construction_save_meta_boxes()` la mantiene
 * sincronizada automáticamente (solo con los ítems de tipo 'image')
 * en cada guardado, para que `ce_get_gallery_ids()` y sus 2
 * consumidores ya existentes (template-parts/gallery.php del Home,
 * inc/seo.php para el Schema.org de Proyecto) sigan funcionando sin
 * ningún cambio — ninguno de los dos entra en el alcance de este
 * Entregable (eso es UX-8.2, todavía sin aprobar).
 *
 * JS real (añadir imagen/video-local/video-url, quitar, reordenar,
 * serializar a JSON en el hidden input): assets/js/admin-proyecto-gallery.js.
 *
 * 🆕 Sprint UX-8, Entregable UX-8.2 (D-109, ampliado en D-110): cada
 * ítem `type:image` incorpora además un botón ★/☆ de "imagen(es)
 * favorita(s)" (flag `favorite` en `_ce_proyecto_media`, saneado en
 * ce_construction_decode_proyecto_media_json() — sin límite por
 * proyecto desde D-110, cualquier cantidad de imágenes puede estar
 * marcada a la vez). Los ítems de video no tienen este botón: la
 * favorita solo tiene sentido para el mosaico de imágenes de la
 * Galería del Home (ver ce_construction_get_home_gallery_images(),
 * inc/helpers.php), que nunca muestra un video como miniatura.
 */
function ce_render_proyecto_gallery( $post ) {
	$items = ce_construction_get_proyecto_media_raw( $post->ID );
	?>
	<input type="hidden" id="ce_proyecto_media" name="ce_proyecto_media" value="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">

	<ul id="ce-proyecto-media-list" class="ce-proyecto-media-list">
		<?php foreach ( $items as $item ) : ?>
			<?php if ( 'image' === $item['type'] ) : ?>
				<?php
				$thumb_url = wp_get_attachment_image_url( $item['id'], 'thumbnail' );
				// Sprint UX-8, Entregable UX-8.2 (D-109): "imagen favorita".
				$is_favorite = ! empty( $item['favorite'] );
				?>
				<li class="ce-proyecto-media-item<?php echo $is_favorite ? ' is-favorite' : ''; ?>" data-type="image" data-id="<?php echo esc_attr( $item['id'] ); ?>">
					<span class="ce-proyecto-media-item__preview"><?php if ( $thumb_url ) : ?><img src="<?php echo esc_url( $thumb_url ); ?>" alt=""><?php endif; ?></span>
					<span class="ce-proyecto-media-item__label"><?php esc_html_e( 'Imagen', 'ce-construction' ); ?></span>
					<span class="ce-proyecto-media-item__actions">
						<button type="button" class="button ce-proyecto-media-item__favorite" aria-pressed="<?php echo $is_favorite ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $is_favorite ? __( 'Quitar de favoritas', 'ce-construction' ) : __( 'Marcar como favorita', 'ce-construction' ) ); ?>"><?php echo $is_favorite ? '&#9733;' : '&#9734;'; /* ★ / ☆ */ ?></button>
						<button type="button" class="button ce-proyecto-media-item__up" aria-label="<?php esc_attr_e( 'Mover antes', 'ce-construction' ); ?>">&uarr;</button>
						<button type="button" class="button ce-proyecto-media-item__down" aria-label="<?php esc_attr_e( 'Mover después', 'ce-construction' ); ?>">&darr;</button>
						<button type="button" class="button ce-proyecto-media-item__remove" aria-label="<?php esc_attr_e( 'Quitar', 'ce-construction' ); ?>">&times;</button>
					</span>
				</li>
			<?php elseif ( 'video-local' === $item['type'] ) : ?>
				<li class="ce-proyecto-media-item" data-type="video-local" data-id="<?php echo esc_attr( $item['id'] ); ?>">
					<span class="ce-proyecto-media-item__preview ce-proyecto-media-item__preview--icon"><span class="dashicons dashicons-video-alt3"></span></span>
					<span class="ce-proyecto-media-item__label"><?php echo esc_html( basename( (string) get_attached_file( $item['id'] ) ) ); ?></span>
					<span class="ce-proyecto-media-item__actions">
						<button type="button" class="button ce-proyecto-media-item__up" aria-label="<?php esc_attr_e( 'Mover antes', 'ce-construction' ); ?>">&uarr;</button>
						<button type="button" class="button ce-proyecto-media-item__down" aria-label="<?php esc_attr_e( 'Mover después', 'ce-construction' ); ?>">&darr;</button>
						<button type="button" class="button ce-proyecto-media-item__remove" aria-label="<?php esc_attr_e( 'Quitar', 'ce-construction' ); ?>">&times;</button>
					</span>
				</li>
			<?php else : // 'video-embed' ?>
				<li class="ce-proyecto-media-item" data-type="video-embed" data-url="<?php echo esc_url( $item['url'] ); ?>">
					<span class="ce-proyecto-media-item__preview ce-proyecto-media-item__preview--icon"><span class="dashicons dashicons-video-alt3"></span></span>
					<span class="ce-proyecto-media-item__label"><?php echo esc_html( $item['url'] ); ?></span>
					<span class="ce-proyecto-media-item__actions">
						<button type="button" class="button ce-proyecto-media-item__up" aria-label="<?php esc_attr_e( 'Mover antes', 'ce-construction' ); ?>">&uarr;</button>
						<button type="button" class="button ce-proyecto-media-item__down" aria-label="<?php esc_attr_e( 'Mover después', 'ce-construction' ); ?>">&darr;</button>
						<button type="button" class="button ce-proyecto-media-item__remove" aria-label="<?php esc_attr_e( 'Quitar', 'ce-construction' ); ?>">&times;</button>
					</span>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>

	<div class="ce-proyecto-media-actions">
		<button type="button" class="button" id="ce-proyecto-media-add-images"><?php esc_html_e( 'Añadir imágenes', 'ce-construction' ); ?></button>
		<button type="button" class="button" id="ce-proyecto-media-add-video-local"><?php esc_html_e( 'Añadir video de la Biblioteca', 'ce-construction' ); ?></button>
		<span class="ce-proyecto-media-url-adder">
			<input type="url" id="ce-proyecto-media-video-url-input" placeholder="https://www.youtube.com/watch?v=...">
			<button type="button" class="button" id="ce-proyecto-media-add-video-url"><?php esc_html_e( 'Añadir video por URL', 'ce-construction' ); ?></button>
		</span>
	</div>
	<p class="description"><?php esc_html_e( 'Combina imágenes y videos en el orden en que quieres que aparezcan. El video por URL debe ser de un proveedor compatible con oEmbed de WordPress (ej. YouTube, Vimeo).', 'ce-construction' ); ?></p>
	<?php
}

/* =========================================================
 * RENDER: TESTIMONIO
 * ========================================================= */
function ce_render_testimonio_fields( $post ) {
	wp_nonce_field( 'ce_save_testimonio_meta', 'ce_testimonio_nonce' );
	$nombre = get_post_meta( $post->ID, '_ce_testimonio_nombre', true );
	$cargo  = get_post_meta( $post->ID, '_ce_testimonio_cargo', true );
	$rating = get_post_meta( $post->ID, '_ce_testimonio_rating', true );
	?>
	<p>
		<label><strong><?php esc_html_e( 'Nombre del cliente', 'ce-construction' ); ?></strong></label><br>
		<input type="text" name="ce_testimonio_nombre" class="widefat" value="<?php echo esc_attr( $nombre ); ?>">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Cargo / Empresa', 'ce-construction' ); ?></strong></label><br>
		<input type="text" name="ce_testimonio_cargo" class="widefat" value="<?php echo esc_attr( $cargo ); ?>">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Calificación (1-5)', 'ce-construction' ); ?></strong></label><br>
		<input type="number" min="1" max="5" name="ce_testimonio_rating" value="<?php echo esc_attr( $rating ? $rating : 5 ); ?>">
	</p>
	<?php
}

/* =========================================================
 * RENDER: VIDEO DEL TESTIMONIO (Sprint UX-7, Entregable UX-7.8, D-077)
 * Campo opcional e independiente de `ce_testimonio_fields`. Admite
 * UNA de dos fuentes (local O externa, no ambas a la vez desde la UI
 * — si el admin rellena las dos, `ce_get_testimonio_video()` en
 * inc/helpers.php prioriza la local, ver su docblock). Guardado con
 * su propio nonce (`ce_save_testimonio_video`), independiente del
 * nonce de `ce_testimonio_fields`, para no acoplar ambos metaboxes.
 * ========================================================= */
function ce_render_testimonio_video_field( $post ) {
	wp_nonce_field( 'ce_save_testimonio_video', 'ce_testimonio_video_nonce' );

	$video_id  = (int) get_post_meta( $post->ID, '_ce_testimonio_video_id', true );
	$video_url = get_post_meta( $post->ID, '_ce_testimonio_video_url', true );

	// Si el adjunto guardado ya no existe o ya no es un video (borrado,
	// reemplazado), no se muestra su nombre de archivo como si siguiera
	// vigente — mismo criterio de validación que ce_get_testimonio_video().
	$video_valid = $video_id
		&& get_post( $video_id )
		&& 0 === strpos( (string) get_post_mime_type( $video_id ), 'video/' );
	?>
	<p class="description">
		<?php esc_html_e( 'Video opcional para este testimonio. Se muestra únicamente en la página completa de Testimonios — no aparece en el Home, en el slider ni en los sidebars. Usa una de las dos opciones siguientes, no ambas.', 'ce-construction' ); ?>
	</p>

	<p>
		<label><strong><?php esc_html_e( 'Opción A — Video local (Biblioteca de Medios)', 'ce-construction' ); ?></strong></label><br>
		<input type="hidden" id="ce_testimonio_video_id" name="ce_testimonio_video_id" value="<?php echo esc_attr( $video_valid ? $video_id : '' ); ?>">
		<span id="ce-testimonio-video-preview">
			<?php if ( $video_valid ) : ?>
				<?php echo esc_html( basename( get_attached_file( $video_id ) ) ); ?>
			<?php endif; ?>
		</span><br>
		<button type="button" class="button" id="ce-testimonio-video-select-btn"><?php esc_html_e( 'Seleccionar video de la Biblioteca de Medios', 'ce-construction' ); ?></button>
		<button type="button" class="button" id="ce-testimonio-video-remove-btn" <?php echo $video_valid ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Quitar video local', 'ce-construction' ); ?></button>
	</p>

	<p>
		<label for="ce_testimonio_video_url"><strong><?php esc_html_e( 'Opción B — URL de video externo compatible', 'ce-construction' ); ?></strong></label><br>
		<input type="url" id="ce_testimonio_video_url" name="ce_testimonio_video_url" class="widefat" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://www.youtube.com/watch?v=...">
		<br>
		<span class="description"><?php esc_html_e( 'Debe ser un proveedor que WordPress reconozca de forma nativa mediante oEmbed (por ejemplo YouTube o Vimeo). Si WordPress no puede resolver la URL, el video no se mostrará en la página de Testimonios.', 'ce-construction' ); ?></span>
	</p>
	<script>
	jQuery(document).ready(function($){
		$('#ce-testimonio-video-select-btn').on('click', function(e){
			e.preventDefault();
			var frame = wp.media({
				title: <?php echo wp_json_encode( __( 'Seleccionar video del testimonio', 'ce-construction' ) ); ?>,
				library: { type: 'video' },
				multiple: false
			});
			frame.on('select', function(){
				var attachment = frame.state().get('selection').first().toJSON();
				$('#ce_testimonio_video_id').val(attachment.id);
				$('#ce-testimonio-video-preview').text(attachment.filename || attachment.title || '');
				$('#ce-testimonio-video-remove-btn').show();
			});
			frame.open();
		});
		$('#ce-testimonio-video-remove-btn').on('click', function(e){
			e.preventDefault();
			$('#ce_testimonio_video_id').val('');
			$('#ce-testimonio-video-preview').text('');
			$(this).hide();
		});
	});
	</script>
	<?php
}

/* =========================================================
 * RENDER: EQUIPO
 * ========================================================= */
function ce_render_equipo_fields( $post ) {
	wp_nonce_field( 'ce_save_equipo_meta', 'ce_equipo_nonce' );
	$cargo   = get_post_meta( $post->ID, '_ce_equipo_cargo', true );
	$linkedin = get_post_meta( $post->ID, '_ce_equipo_linkedin', true );
	?>
	<p>
		<label><strong><?php esc_html_e( 'Cargo', 'ce-construction' ); ?></strong></label><br>
		<input type="text" name="ce_equipo_cargo" class="widefat" value="<?php echo esc_attr( $cargo ); ?>">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'LinkedIn (opcional)', 'ce-construction' ); ?></strong></label><br>
		<input type="url" name="ce_equipo_linkedin" class="widefat" value="<?php echo esc_attr( $linkedin ); ?>">
	</p>
	<?php
}

/* =========================================================
 * RENDER: CLIENTE
 * ========================================================= */
function ce_render_cliente_fields( $post ) {
	wp_nonce_field( 'ce_save_cliente_meta', 'ce_cliente_nonce' );
	$sitio = get_post_meta( $post->ID, '_ce_cliente_sitio', true );
	?>
	<p>
		<label><strong><?php esc_html_e( 'Sitio web del cliente (opcional)', 'ce-construction' ); ?></strong></label><br>
		<input type="url" name="ce_cliente_sitio" class="widefat" value="<?php echo esc_attr( $sitio ); ?>">
	</p>
	<p class="description"><?php esc_html_e( 'La imagen destacada se usa como logo del cliente.', 'ce-construction' ); ?></p>
	<?php
}

/**
 * Guardado seguro de todos los metaboxes.
 * Verifica nonce, autosave, permisos y sanitiza cada campo.
 */
function ce_construction_save_meta_boxes( $post_id ) {

	// Evita guardar en autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// QA-007 (Sprint 5, Fase 1 — corrección alta): WordPress también
	// dispara `save_post` para el post de tipo `revision` que se crea
	// internamente al actualizar un post con revisiones habilitadas.
	// Sin esta guardia, los metadatos podían escribirse también sobre
	// el ID de la revisión (filas de meta redundantes/incorrectas en BD).
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// SERVICIO.
	if ( isset( $_POST['ce_servicio_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_servicio_nonce'] ) ), 'ce_save_servicio_meta' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['ce_icono_fa'] ) ) {
				update_post_meta( $post_id, '_ce_icono_fa', sanitize_text_field( wp_unslash( $_POST['ce_icono_fa'] ) ) );
			}
			if ( isset( $_POST['ce_enlace_externo'] ) ) {
				update_post_meta( $post_id, '_ce_enlace_externo', esc_url_raw( wp_unslash( $_POST['ce_enlace_externo'] ) ) );
			}
		}
	}

	// PROYECTO.
	if ( isset( $_POST['ce_proyecto_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_proyecto_nonce'] ) ), 'ce_save_proyecto_meta' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['ce_proyecto_cliente'] ) ) {
				update_post_meta( $post_id, '_ce_proyecto_cliente', sanitize_text_field( wp_unslash( $_POST['ce_proyecto_cliente'] ) ) );
			}
			if ( isset( $_POST['ce_proyecto_ubicacion'] ) ) {
				update_post_meta( $post_id, '_ce_proyecto_ubicacion', sanitize_text_field( wp_unslash( $_POST['ce_proyecto_ubicacion'] ) ) );
			}
			if ( isset( $_POST['ce_proyecto_fecha'] ) ) {
				update_post_meta( $post_id, '_ce_proyecto_fecha', sanitize_text_field( wp_unslash( $_POST['ce_proyecto_fecha'] ) ) );
			}
			// Sprint UX-8, Entregable UX-8.2 (D-109): checkbox "Proyecto
			// destacado". A diferencia de los campos de texto de arriba
			// (gateados con `isset()` porque un <input> siempre se envía),
			// un checkbox NO marcado no llega en absoluto en $_POST — por
			// eso este bloque no puede seguir el mismo patrón: debe borrar
			// explícitamente el meta cuando la key está ausente, o
			// desmarcar el checkbox nunca tendría efecto tras el primer
			// guardado.
			if ( isset( $_POST['ce_proyecto_destacado'] ) ) {
				update_post_meta( $post_id, '_ce_proyecto_destacado', '1' );
			} else {
				delete_post_meta( $post_id, '_ce_proyecto_destacado' );
			}
			// Sprint UX-8, Entregable UX-8.1: `_ce_proyecto_media`
			// (galería mixta) reemplaza a `_ce_proyecto_galeria` como
			// campo enviado por el formulario — el nombre del campo
			// oculto cambió en ce_render_proyecto_gallery() (ver
			// arriba), por lo que `$_POST['ce_proyecto_galeria']` ya
			// no llega desde el nuevo admin UI. Se sanea con la misma
			// función usada en lectura (ce_construction_decode_proyecto_media_json(),
			// inc/helpers.php) — única fuente de saneamiento para
			// este dato, evitando que el guardado y la lectura puedan
			// divergir.
			if ( isset( $_POST['ce_proyecto_media'] ) ) {
				$media_items = ce_construction_decode_proyecto_media_json( wp_unslash( $_POST['ce_proyecto_media'] ) );
				update_post_meta( $post_id, '_ce_proyecto_media', wp_json_encode( $media_items ) );

				// `_ce_proyecto_galeria` se deriva y sincroniza
				// automáticamente (solo los ítems de tipo 'image', en
				// orden) — NUNCA se elimina ni se deja de actualizar:
				// `ce_get_gallery_ids()` la sigue usando tal cual, sin
				// saber que la fuente real ahora es la galería mixta.
				// Consumidores ya existentes que dependen de ella
				// (template-parts/gallery.php del Home, inc/seo.php
				// para el Schema.org de Proyecto) siguen funcionando
				// sin ningún cambio — ambos quedan fuera del alcance
				// de este Entregable (ver UX-8.2, pendiente).
				$legacy_ids = array();
				foreach ( $media_items as $media_item ) {
					if ( 'image' === $media_item['type'] ) {
						$legacy_ids[] = $media_item['id'];
					}
				}
				update_post_meta( $post_id, '_ce_proyecto_galeria', implode( ',', $legacy_ids ) );
			}
		}
	}

	// TESTIMONIO.
	if ( isset( $_POST['ce_testimonio_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_testimonio_nonce'] ) ), 'ce_save_testimonio_meta' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['ce_testimonio_nombre'] ) ) {
				update_post_meta( $post_id, '_ce_testimonio_nombre', sanitize_text_field( wp_unslash( $_POST['ce_testimonio_nombre'] ) ) );
			}
			if ( isset( $_POST['ce_testimonio_cargo'] ) ) {
				update_post_meta( $post_id, '_ce_testimonio_cargo', sanitize_text_field( wp_unslash( $_POST['ce_testimonio_cargo'] ) ) );
			}
			if ( isset( $_POST['ce_testimonio_rating'] ) ) {
				$rating = absint( $_POST['ce_testimonio_rating'] );
				$rating = max( 1, min( 5, $rating ) );
				update_post_meta( $post_id, '_ce_testimonio_rating', $rating );
			}
		}
	}

	// TESTIMONIO — VIDEO (Sprint UX-7, Entregable UX-7.8, D-077).
	// Nonce propio e independiente del de TESTIMONIO de arriba.
	if ( isset( $_POST['ce_testimonio_video_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_testimonio_video_nonce'] ) ), 'ce_save_testimonio_video' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {

			// Video local: se valida que el adjunto exista y que su
			// mime type sea realmente `video/*` antes de guardarlo —
			// evita que el campo quede apuntando a un ID arbitrario
			// (ej. una imagen, o un ID inexistente) enviado desde el
			// formulario. Un ID inválido se guarda como "sin video
			// local" (se borra el meta), nunca como el valor recibido.
			$video_id = isset( $_POST['ce_testimonio_video_id'] ) ? absint( $_POST['ce_testimonio_video_id'] ) : 0;
			if ( $video_id && 0 !== strpos( (string) get_post_mime_type( $video_id ), 'video/' ) ) {
				$video_id = 0;
			}
			if ( $video_id ) {
				update_post_meta( $post_id, '_ce_testimonio_video_id', $video_id );
			} else {
				delete_post_meta( $post_id, '_ce_testimonio_video_id' );
			}

			// URL externa: esc_url_raw() sanea el formato de la URL en
			// el guardado; la validación de si WordPress puede
			// realmente resolverla vía oEmbed ocurre en lectura
			// (ce_get_testimonio_video(), inc/helpers.php) porque
			// wp_oembed_get() hace una petición externa y no debe
			// ejecutarse en cada guardado del post.
			if ( isset( $_POST['ce_testimonio_video_url'] ) ) {
				$video_url = esc_url_raw( wp_unslash( $_POST['ce_testimonio_video_url'] ) );
				if ( $video_url ) {
					update_post_meta( $post_id, '_ce_testimonio_video_url', $video_url );
				} else {
					delete_post_meta( $post_id, '_ce_testimonio_video_url' );
				}
			}
		}
	}

	// EQUIPO.
	if ( isset( $_POST['ce_equipo_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_equipo_nonce'] ) ), 'ce_save_equipo_meta' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['ce_equipo_cargo'] ) ) {
				update_post_meta( $post_id, '_ce_equipo_cargo', sanitize_text_field( wp_unslash( $_POST['ce_equipo_cargo'] ) ) );
			}
			if ( isset( $_POST['ce_equipo_linkedin'] ) ) {
				update_post_meta( $post_id, '_ce_equipo_linkedin', esc_url_raw( wp_unslash( $_POST['ce_equipo_linkedin'] ) ) );
			}
		}
	}

	// CLIENTE.
	if ( isset( $_POST['ce_cliente_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_cliente_nonce'] ) ), 'ce_save_cliente_meta' ) ) {
		if ( current_user_can( 'edit_post', $post_id ) ) {
			if ( isset( $_POST['ce_cliente_sitio'] ) ) {
				update_post_meta( $post_id, '_ce_cliente_sitio', esc_url_raw( wp_unslash( $_POST['ce_cliente_sitio'] ) ) );
			}
		}
	}
}
add_action( 'save_post', 'ce_construction_save_meta_boxes' );

/**
 * Carga wp.media (media uploader) en las pantallas de edición de
 * proyecto (selector de galería) y, desde UX-7.8 (D-077), también en
 * las de testimonio (selector de video local).
 */
function ce_construction_admin_enqueue( $hook ) {
	global $post_type;
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && in_array( $post_type, array( 'proyecto', 'testimonio' ), true ) ) {
		// 'testimonio' añadido en Sprint UX-7, Entregable UX-7.8 (D-077):
		// el selector de video local del nuevo metabox también necesita
		// wp.media. No afecta la condición ya existente para 'proyecto'.
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'ce_construction_admin_enqueue' );

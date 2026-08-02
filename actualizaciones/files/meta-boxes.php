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
	<?php
}

function ce_render_proyecto_gallery( $post ) {
	$gallery_ids = get_post_meta( $post->ID, '_ce_proyecto_galeria', true );
	?>
	<input type="hidden" id="ce_proyecto_galeria" name="ce_proyecto_galeria" value="<?php echo esc_attr( $gallery_ids ); ?>">
	<div id="ce-gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
		<?php
		if ( $gallery_ids ) {
			foreach ( explode( ',', $gallery_ids ) as $img_id ) {
				echo wp_get_attachment_image( absint( $img_id ), 'thumbnail' );
			}
		}
		?>
	</div>
	<button type="button" class="button" id="ce-gallery-upload-btn"><?php esc_html_e( 'Seleccionar imágenes de galería', 'ce-construction' ); ?></button>
	<script>
	jQuery(document).ready(function($){
		$('#ce-gallery-upload-btn').on('click', function(e){
			e.preventDefault();
			var frame = wp.media({ title: 'Seleccionar imágenes', multiple: true });
			frame.on('select', function(){
				var ids = frame.state().get('selection').map(function(a){ return a.id; });
				$('#ce_proyecto_galeria').val(ids.join(','));
				var preview = $('#ce-gallery-preview');
				preview.empty();
				frame.state().get('selection').each(function(a){
					preview.append('<img src="'+a.attributes.sizes.thumbnail.url+'" style="width:80px;height:80px;object-fit:cover;">');
				});
			});
			frame.open();
		});
	});
	</script>
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
			if ( isset( $_POST['ce_proyecto_galeria'] ) ) {
				$ids = array_filter( array_map( 'absint', explode( ',', wp_unslash( $_POST['ce_proyecto_galeria'] ) ) ) );
				update_post_meta( $post_id, '_ce_proyecto_galeria', implode( ',', $ids ) );
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
 * Carga wp.media (media uploader) solo en las pantallas de edición
 * de proyecto, para el selector de galería.
 */
function ce_construction_admin_enqueue( $hook ) {
	global $post_type;
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'proyecto' === $post_type ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'ce_construction_admin_enqueue' );

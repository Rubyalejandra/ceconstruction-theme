<?php
/**
 * Formulario de Cotización Gratuita.
 * - Validación de datos en servidor (nunca confiar solo en el cliente).
 * - Nonce obligatorio.
 * - Sanitización de todos los campos.
 * - Envío de correo vía wp_mail.
 * - Soporte de adjunto (con validación de tipo y tamaño).
 * - Registro opcional en un CPT interno "cotizacion" para
 *   que el formulario sea administrable desde el panel.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CPT interno para que las cotizaciones recibidas sean
 * administrables desde wp-admin (requisito: "Debe ser administrable").
 */
function ce_construction_register_cpt_cotizacion() {
	register_post_type( 'cotizacion', array(
		'labels' => array(
			'name'          => __( 'Cotizaciones', 'ce-construction' ),
			'singular_name' => __( 'Cotización', 'ce-construction' ),
			'all_items'     => __( 'Cotizaciones Recibidas', 'ce-construction' ),
			'menu_name'     => __( 'Cotizaciones', 'ce-construction' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-email-alt',
		'capability_type'    => 'post',
		'supports'           => array( 'title' ),
		'capabilities'       => array(
			'create_posts' => 'do_not_allow', // Solo se crean vía formulario, no manualmente.
		),
		'map_meta_cap'       => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_cotizacion' );

/**
 * Columnas personalizadas en el listado admin de cotizaciones.
 */
function ce_construction_cotizacion_columns( $columns ) {
	$columns = array(
		'cb'       => $columns['cb'],
		'title'    => __( 'Nombre', 'ce-construction' ),
		'ce_email' => __( 'Correo', 'ce-construction' ),
		'ce_phone' => __( 'Teléfono', 'ce-construction' ),
		'ce_service' => __( 'Servicio', 'ce-construction' ),
		'date'     => $columns['date'],
	);
	return $columns;
}
add_filter( 'manage_cotizacion_posts_columns', 'ce_construction_cotizacion_columns' );

function ce_construction_cotizacion_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'ce_email':
			echo esc_html( get_post_meta( $post_id, '_ce_email', true ) );
			break;
		case 'ce_phone':
			echo esc_html( get_post_meta( $post_id, '_ce_phone', true ) );
			break;
		case 'ce_service':
			echo esc_html( get_post_meta( $post_id, '_ce_service', true ) );
			break;
	}
}
add_action( 'manage_cotizacion_posts_custom_column', 'ce_construction_cotizacion_column_content', 10, 2 );

/**
 * Handler AJAX del formulario (usuarios logueados y no logueados).
 */
function ce_construction_handle_quote_form() {

	// 1. Verificación de Nonce (seguridad obligatoria).
	if ( ! isset( $_POST['ce_quote_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_quote_nonce'] ) ), 'ce_quote_form_action' ) ) {
		wp_send_json_error( array( 'message' => __( 'Sesión no válida. Recarga la página e inténtalo de nuevo.', 'ce-construction' ) ), 403 );
	}

	// 2. Honeypot anti-spam (campo oculto que un humano nunca llena).
	if ( ! empty( $_POST['ce_website'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Solicitud rechazada.', 'ce-construction' ) ), 400 );
	}

	// 2 bis. QA-004 (Sprint 5, Fase 1 — corrección alta): rate-limiting
	// por IP. Antes el honeypot era la única defensa anti-abuso; un
	// script que simplemente omitiera ese campo podía enviar solicitudes
	// ilimitadas. Se permite un máximo de 3 envíos cada 10 minutos por IP,
	// usando un transient (sin dependencias externas ni tablas nuevas).
	$client_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$rate_limit_key = 'ce_quote_rl_' . md5( $client_ip );
	$attempts       = (int) get_transient( $rate_limit_key );

	if ( $attempts >= 3 ) {
		wp_send_json_error( array( 'message' => __( 'Has enviado demasiadas solicitudes. Intenta de nuevo en unos minutos.', 'ce-construction' ) ), 429 );
	}
	set_transient( $rate_limit_key, $attempts + 1, 10 * MINUTE_IN_SECONDS );

	// 3. Sanitización de campos.
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	// 4. Validación en servidor.
	$errors = array();
	if ( empty( $name ) || mb_strlen( $name ) < 2 ) {
		$errors['name'] = __( 'Ingresa un nombre válido.', 'ce-construction' );
	}
	if ( empty( $email ) || ! is_email( $email ) ) {
		$errors['email'] = __( 'Ingresa un correo electrónico válido.', 'ce-construction' );
	}
	if ( empty( $phone ) || ! preg_match( '/^[0-9+\-\s()]{7,20}$/', $phone ) ) {
		$errors['phone'] = __( 'Ingresa un teléfono válido.', 'ce-construction' );
	}
	if ( empty( $service ) ) {
		$errors['service'] = __( 'Selecciona el servicio requerido.', 'ce-construction' );
	}
	if ( empty( $message ) || mb_strlen( $message ) < 10 ) {
		$errors['message'] = __( 'Cuéntanos un poco más sobre tu proyecto (mínimo 10 caracteres).', 'ce-construction' );
	}

	if ( ! empty( $errors ) ) {
		wp_send_json_error( array(
			'message' => __( 'Revisa los campos marcados.', 'ce-construction' ),
			'fields'  => $errors,
		), 422 );
	}

	// 5. Manejo seguro del archivo adjunto (opcional).
	$attachment_path = '';
	$attachment_name = '';
	if ( ! empty( $_FILES['attachment'] ) && UPLOAD_ERR_NO_FILE !== $_FILES['attachment']['error'] ) {

		if ( UPLOAD_ERR_OK !== $_FILES['attachment']['error'] ) {
			wp_send_json_error( array( 'message' => __( 'Error al subir el archivo.', 'ce-construction' ) ), 400 );
		}

		// QA-001 (Sprint 5, Fase 1 — corrección crítica): la validación
		// anterior comparaba $_FILES['attachment']['type'] (el MIME que
		// el NAVEGADOR del visitante envía, falsificable con herramientas
		// como curl) y solo exigía que wp_check_filetype() reconociera
		// alguna extensión de la lista GLOBAL de WordPress (docenas de
		// extensiones, no solo las 4 permitidas aquí). Ahora se usa
		// wp_check_filetype_and_ext(), que además inspecciona el
		// contenido real del archivo para imágenes, y se valida la
		// extensión resultante contra un whitelist explícito y cerrado.
		$allowed_extensions = array( 'pdf', 'jpg', 'jpeg', 'png', 'webp' );
		$max_size           = 5 * 1024 * 1024; // 5MB.

		require_once ABSPATH . 'wp-admin/includes/file.php';

		$file_type_and_ext = wp_check_filetype_and_ext(
			$_FILES['attachment']['tmp_name'],
			$_FILES['attachment']['name']
		);
		$real_ext = $file_type_and_ext['ext'] ? strtolower( $file_type_and_ext['ext'] ) : '';

		if ( empty( $real_ext ) || ! in_array( $real_ext, $allowed_extensions, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Formato de archivo no permitido. Usa PDF, JPG, PNG o WEBP.', 'ce-construction' ) ), 400 );
		}
		if ( $_FILES['attachment']['size'] > $max_size ) {
			wp_send_json_error( array( 'message' => __( 'El archivo supera el tamaño máximo de 5MB.', 'ce-construction' ) ), 400 );
		}

		$upload_overrides = array( 'test_form' => false );
		$uploaded_file    = wp_handle_upload( $_FILES['attachment'], $upload_overrides );

		if ( isset( $uploaded_file['file'] ) && empty( $uploaded_file['error'] ) ) {
			$attachment_path = $uploaded_file['file'];
			$attachment_name = basename( $uploaded_file['file'] );
			$attachment_type = $uploaded_file['type'];
		} else {
			wp_send_json_error( array( 'message' => __( 'No se pudo procesar el archivo adjunto.', 'ce-construction' ) ), 400 );
		}
	}

	// 6. Registrar la cotización como CPT (administrable desde el panel).
	$post_id = wp_insert_post( array(
		'post_type'   => 'cotizacion',
		'post_title'  => sprintf( '%s - %s', $name, gmdate( 'Y-m-d H:i' ) ),
		'post_status' => 'private',
	) );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_ce_email', $email );
		update_post_meta( $post_id, '_ce_phone', $phone );
		update_post_meta( $post_id, '_ce_company', $company );
		update_post_meta( $post_id, '_ce_service', $service );
		update_post_meta( $post_id, '_ce_message', $message );
		if ( $attachment_path ) {
			update_post_meta( $post_id, '_ce_attachment_path', $attachment_path );

			// QA-002 (Sprint 5, Fase 1 — corrección alta): antes el
			// archivo quedaba huérfano en wp-content/uploads/ (movido
			// por wp_handle_upload() pero nunca registrado como
			// attachment). Ahora se registra con wp_insert_attachment(),
			// vinculado a la cotización vía post_parent, para que
			// aparezca en la Media Library y se limpie automáticamente
			// si se borra la cotización o el propio attachment desde
			// el admin (ciclo de vida estándar de WordPress).
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$attachment_data = array(
				'guid'           => isset( $uploaded_file['url'] ) ? $uploaded_file['url'] : '',
				'post_mime_type' => isset( $attachment_type ) ? $attachment_type : '',
				'post_title'     => isset( $attachment_name ) ? $attachment_name : '',
				'post_status'    => 'inherit',
				'post_parent'    => $post_id,
			);
			$attachment_id = wp_insert_attachment( $attachment_data, $attachment_path, $post_id );

			if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
				$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment_path );
				wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
				update_post_meta( $post_id, '_ce_attachment_id', $attachment_id );
			}
		}
	}

	// 7. Envío de correo electrónico.
	$to      = get_theme_mod( 'ce_email', get_option( 'admin_email' ) );
	$subject = sprintf( __( 'Nueva solicitud de cotización de %s', 'ce-construction' ), $name );

	$body  = "Se ha recibido una nueva solicitud de cotización:\n\n";
	$body .= "Nombre: {$name}\n";
	$body .= "Correo: {$email}\n";
	$body .= "Teléfono: {$phone}\n";
	$body .= 'Empresa: ' . ( $company ? $company : '—' ) . "\n";
	$body .= "Servicio requerido: {$service}\n\n";
	$body .= "Mensaje:\n{$message}\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email );
	$attachments = $attachment_path ? array( $attachment_path ) : array();

	$sent = wp_mail( $to, $subject, $body, $headers, $attachments );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'Tu solicitud fue registrada, pero hubo un problema enviando la notificación por correo. Te contactaremos igualmente.', 'ce-construction' ) ), 200 );
	}

	wp_send_json_success( array( 'message' => __( '¡Gracias! Tu solicitud de cotización fue enviada. Te contactaremos muy pronto.', 'ce-construction' ) ) );
}
add_action( 'wp_ajax_ce_submit_quote', 'ce_construction_handle_quote_form' );
add_action( 'wp_ajax_nopriv_ce_submit_quote', 'ce_construction_handle_quote_form' );

/* =========================================================
 * QA-003 (Sprint 5, Fase 1 — corrección alta).
 * Antes no existía ningún mecanismo de retención/expiración
 * para los datos personales almacenados por el formulario de
 * cotización (nombre, correo, teléfono, mensaje, adjunto).
 * Se añade un cron diario que purga cotizaciones más antiguas
 * que un umbral configurable (por defecto 365 días), incluyendo
 * su archivo adjunto asociado (ver QA-002, ahora un attachment
 * real que se borra correctamente junto con el post).
 *
 * El plazo es intencionalmente configurable vía filtro, ya que
 * la política de retención definitiva es una decisión de negocio
 * del cliente (ver DECISIONS.md, decisión de esta corrección),
 * no algo que el código deba fijar de forma rígida.
 * ========================================================= */

/**
 * Número de días que se conserva una cotización antes de purgarse.
 * Filtrable: add_filter( 'ce_construction_quote_retention_days', fn() => 180 );
 */
function ce_construction_quote_retention_days() {
	return (int) apply_filters( 'ce_construction_quote_retention_days', 365 );
}

/**
 * Callback del cron: borra cotizaciones más antiguas que el umbral,
 * junto con su attachment vinculado (wp_delete_post con force delete
 * también elimina los attachments cuyo post_parent sea ese post,
 * gracias al hook nativo de WordPress que limpia adjuntos huérfanos
 * de un post al eliminarlo definitivamente).
 */
function ce_construction_purge_old_quotes() {
	$days = ce_construction_quote_retention_days();
	if ( $days <= 0 ) {
		return; // 0 o negativo = retención desactivada (conservar indefinidamente).
	}

	$old_quotes = get_posts( array(
		'post_type'      => 'cotizacion',
		'posts_per_page' => 50, // Procesar en lotes para no saturar una sola ejecución de cron.
		'post_status'    => 'any',
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'date_query'     => array(
			array(
				'before' => $days . ' days ago',
			),
		),
	) );

	foreach ( $old_quotes as $quote_id ) {
		wp_delete_post( $quote_id, true ); // true = borrado permanente (sin pasar por papelera).
	}
}
add_action( 'ce_construction_quote_cleanup_event', 'ce_construction_purge_old_quotes' );

/**
 * Programa el cron diario al activar el tema.
 */
function ce_construction_schedule_quote_cleanup() {
	if ( ! wp_next_scheduled( 'ce_construction_quote_cleanup_event' ) ) {
		wp_schedule_event( time(), 'daily', 'ce_construction_quote_cleanup_event' );
	}
}
add_action( 'after_switch_theme', 'ce_construction_schedule_quote_cleanup' );

/**
 * Cancela el cron al cambiar de tema, para no dejar tareas programadas
 * huérfanas si el cliente desactiva CE Construction en el futuro.
 */
function ce_construction_unschedule_quote_cleanup() {
	$timestamp = wp_next_scheduled( 'ce_construction_quote_cleanup_event' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'ce_construction_quote_cleanup_event' );
	}
}
add_action( 'switch_theme', 'ce_construction_unschedule_quote_cleanup' );
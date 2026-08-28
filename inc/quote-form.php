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
 * - Rate-limiting atómico por IP (QA-032) e idempotencia server-side
 *   con checkpoints (QA-034) vía inc/form-guards.php.
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
		'cb'            => $columns['cb'],
		'title'         => __( 'Nombre', 'ce-construction' ),
		'ce_email'      => __( 'Correo', 'ce-construction' ),
		'ce_phone'      => __( 'Teléfono', 'ce-construction' ),
		'ce_service'    => __( 'Servicio', 'ce-construction' ),
		'ce_attachment' => __( 'Adjunto', 'ce-construction' ), // QA-031 (Sprint 8, Entregable 8.3): único punto de acceso legítimo al adjunto, vía endpoint autenticado.
		'date'          => $columns['date'],
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
		case 'ce_attachment':
			// QA-031 (Sprint 8, Entregable 8.3): ya no se enlaza la URL
			// directa del adjunto (bloqueada a nivel de servidor, ver
			// inc/quote-attachments.php) — este es el único acceso
			// legítimo, autenticado y con nonce propio por cotización.
			$attachment_id = (int) get_post_meta( $post_id, '_ce_attachment_id', true );
			if ( $attachment_id && current_user_can( 'edit_post', $post_id ) && function_exists( 'ce_construction_get_quote_attachment_download_url' ) ) {
				printf(
					'<a href="%1$s"><i class="dashicons dashicons-paperclip" aria-hidden="true"></i> %2$s</a>',
					esc_url( ce_construction_get_quote_attachment_download_url( $post_id, $attachment_id ) ),
					esc_html__( 'Descargar', 'ce-construction' )
				);
			} else {
				echo '—';
			}
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

	/* =====================================================
	 * QA-034 (Sprint 8, Entregable 8.4): idempotencia server-side.
	 *
	 * La key viaja en un campo oculto (`ce_idempotency_key`) generado
	 * por el servidor en cada render del formulario (ver
	 * inc/form-guards.php, ce_construction_generate_idempotency_key(),
	 * y la precondición documentada sobre caché de página completa en
	 * DECISIONS.md). Si el campo no llega (p. ej. una plantilla del
	 * formulario todavía no actualizada), se continúa SIN protección de
	 * idempotencia en vez de bloquear el envío — comportamiento
	 * retrocompatible, nunca un fallo duro por este motivo.
	 * ===================================================== */
	$idempotency_hash    = null;
	$idempotency_claim   = null;
	$raw_idempotency_key = isset( $_POST['ce_idempotency_key'] ) ? sanitize_text_field( wp_unslash( $_POST['ce_idempotency_key'] ) ) : '';

	if ( $raw_idempotency_key && function_exists( 'ce_construction_claim_idempotency_key' ) ) {
		$idempotency_claim = ce_construction_claim_idempotency_key( $raw_idempotency_key );

		if ( ! $idempotency_claim['claimed'] ) {
			$row = $idempotency_claim['row'];

			if ( $row && 'done' === $row->status ) {
				// Replay de una petición ya completada: se devuelve
				// exactamente la misma respuesta guardada, sin repetir
				// ningún efecto secundario (sin crear post, sin
				// reenviar correo).
				$stored = json_decode( $row->response, true );
				if ( is_array( $stored ) && isset( $stored['success'] ) ) {
					if ( $stored['success'] ) {
						wp_send_json_success( isset( $stored['data'] ) ? $stored['data'] : array() );
					} else {
						wp_send_json_error( isset( $stored['data'] ) ? $stored['data'] : array(), isset( $stored['status_code'] ) ? $stored['status_code'] : 400 );
					}
				}
				// Si por algún motivo la respuesta guardada no se pudo
				// decodificar, se cae al mensaje genérico de abajo en
				// vez de reprocesar (nunca se repite un efecto
				// secundario por una respuesta ilegible).
				wp_send_json_success( array( 'message' => __( '¡Gracias! Tu solicitud de cotización fue enviada. Te contactaremos muy pronto.', 'ce-construction' ) ) );
			}

			if ( $row && 'processing' === $row->status && ! empty( $row->post_id ) ) {
				// El post ya fue creado por un intento anterior de esta
				// misma key; nunca se crea una segunda cotización. Se
				// reintenta únicamente el envío de correo si todavía no
				// se había confirmado, usando los datos ya guardados en
				// el post (no el $_POST de este replay), y se cierra el
				// checkpoint como 'done'.
				$resume_post_id = (int) $row->post_id;
				$already_sent   = (bool) get_post_meta( $resume_post_id, '_ce_email_sent', true );

				if ( ! $already_sent ) {
					$resume_name    = get_post_meta( $resume_post_id, '_ce_name', true );
					$resume_email   = get_post_meta( $resume_post_id, '_ce_email', true );
					$resume_phone   = get_post_meta( $resume_post_id, '_ce_phone', true );
					$resume_company = get_post_meta( $resume_post_id, '_ce_company', true );
					$resume_service = get_post_meta( $resume_post_id, '_ce_service', true );
					$resume_message = get_post_meta( $resume_post_id, '_ce_message', true );

					ce_construction_send_quote_email(
						$resume_post_id,
						$resume_name,
						$resume_email,
						$resume_phone,
						$resume_company,
						$resume_service,
						$resume_message
					);
				}

				ce_construction_idempotency_mark_done( $row->guard_key, array(
					'success' => true,
					'data'    => array( 'message' => __( '¡Gracias! Tu solicitud de cotización fue enviada. Te contactaremos muy pronto.', 'ce-construction' ) ),
				) );

				wp_send_json_success( array( 'message' => __( '¡Gracias! Tu solicitud de cotización fue enviada. Te contactaremos muy pronto.', 'ce-construction' ) ) );
			}

			// 'processing' con post_id todavía NULL y por debajo del
			// umbral de "atascada" (ver ce_construction_idempotency_stuck_threshold()):
			// se interpreta como una petición concurrente genuina
			// procesándose ahora mismo (dos pestañas, doble clic con
			// latencia de red). Se responde de forma neutra, SIN
			// reprocesar ni tocar el rate-limit.
			wp_send_json_error( array(
				'message' => __( 'Tu solicitud ya se está procesando. Por favor espera un momento antes de intentar de nuevo.', 'ce-construction' ),
			), 409 );
		}

		// $idempotency_claim['claimed'] === true: o bien es una key
		// nueva, o bien una fila abandonada que es seguro reprocesar
		// desde cero (ver 'resume' en ce_construction_claim_idempotency_key()).
		$idempotency_hash = $idempotency_claim['hash'];
	}

	/* =====================================================
	 * QA-032 (Sprint 8, Entregable 8.4): rate-limiting atómico por IP.
	 * Sustituye el mecanismo anterior (get_transient()+set_transient(),
	 * dos operaciones separadas y no atómicas — ver DECISIONS.md) por
	 * una única sentencia SQL atómica sobre la tabla dedicada
	 * `{prefix}ce_form_guards` (ver inc/form-guards.php). La IP nunca se
	 * guarda en texto plano: se usa un HMAC con wp_salt('auth').
	 * ===================================================== */
	$client_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

	if ( ! ce_construction_claim_rate_limit( $client_ip, 3, 10 * MINUTE_IN_SECONDS ) ) {
		if ( $idempotency_hash ) {
			ce_construction_idempotency_release( $idempotency_hash );
		}
		wp_send_json_error( array( 'message' => __( 'Has enviado demasiadas solicitudes. Intenta de nuevo en unos minutos.', 'ce-construction' ) ), 429 );
	}

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
		// La solicitud nunca llegó a crear nada: se libera la
		// idempotency key para que el usuario pueda corregir el campo y
		// reenviar sin quedar bloqueado por su propio intento fallido.
		if ( $idempotency_hash ) {
			ce_construction_idempotency_release( $idempotency_hash );
		}
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
			if ( $idempotency_hash ) {
				ce_construction_idempotency_release( $idempotency_hash );
			}
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
			if ( $idempotency_hash ) {
				ce_construction_idempotency_release( $idempotency_hash );
			}
			wp_send_json_error( array( 'message' => __( 'Formato de archivo no permitido. Usa PDF, JPG, PNG o WEBP.', 'ce-construction' ) ), 400 );
		}
		if ( $_FILES['attachment']['size'] > $max_size ) {
			if ( $idempotency_hash ) {
				ce_construction_idempotency_release( $idempotency_hash );
			}
			wp_send_json_error( array( 'message' => __( 'El archivo supera el tamaño máximo de 5MB.', 'ce-construction' ) ), 400 );
		}

		// QA-031 (Sprint 8, Entregable 8.3): el adjunto ya no se sube al
		// árbol normal de uploads/ (servible directamente por el servidor
		// web) — se redirige a una subcarpeta dedicada y bloqueada a nivel
		// de servidor (ver inc/quote-attachments.php). El filtro se añade
		// y se quita en esta misma función, para no afectar ninguna otra
		// subida del sitio (Media Library, galería de Proyectos, etc.).
		ce_construction_ensure_quote_uploads_protected();
		add_filter( 'upload_dir', 'ce_construction_quote_upload_dir' );

		$upload_overrides = array( 'test_form' => false );
		$uploaded_file    = wp_handle_upload( $_FILES['attachment'], $upload_overrides );

		remove_filter( 'upload_dir', 'ce_construction_quote_upload_dir' );

		if ( isset( $uploaded_file['file'] ) && empty( $uploaded_file['error'] ) ) {

			// QA-031: renombra el archivo físico a un nombre aleatorio
			// impredecible — defensa adicional aunque la carpeta ya esté
			// bloqueada (ver comentario completo en
			// ce_construction_randomize_quote_attachment_filename()).
			$renamed         = ce_construction_randomize_quote_attachment_filename( $uploaded_file );
			$uploaded_file   = $renamed['uploaded_file'];
			$original_name   = $renamed['original_name'];

			$attachment_path = $uploaded_file['file'];
			$attachment_name = basename( $uploaded_file['file'] );
			$attachment_type = $uploaded_file['type'];
		} else {
			if ( $idempotency_hash ) {
				ce_construction_idempotency_release( $idempotency_hash );
			}
			wp_send_json_error( array( 'message' => __( 'No se pudo procesar el archivo adjunto.', 'ce-construction' ) ), 400 );
		}
	}

	// 6. Registrar la cotización como CPT (administrable desde el panel).
	$post_id = wp_insert_post( array(
		'post_type'   => 'cotizacion',
		'post_title'  => sprintf( '%s - %s', $name, gmdate( 'Y-m-d H:i' ) ),
		'post_status' => 'private',
	) );

	/* =====================================================
	 * QA-033 (Sprint 8, Entregable 8.4): archivo huérfano si
	 * wp_insert_post() falla DESPUÉS de que el adjunto ya se movió
	 * físicamente a uploads/ (paso 5, arriba). Antes, este caso no se
	 * cubría: el bloque de abajo simplemente no se ejecutaba y la
	 * ejecución seguía hacia el envío de correo como si nada hubiera
	 * pasado, dejando el archivo en disco para siempre y respondiendo
	 * éxito sin que existiera ninguna cotización real registrada.
	 *
	 * Ahora, si falla la creación del post, se hace rollback del
	 * archivo ya subido (wp_delete_file(), wrapper de WordPress sobre
	 * unlink() que respeta los filtros de terceros), se registra el
	 * fallo en el log de errores del servidor con el prefijo
	 * "[CE Construction]" (sin ningún dato personal/sensible: ni
	 * nombre, ni correo, ni teléfono, ni mensaje — solo el motivo del
	 * fallo y, si lo hay, el mensaje del WP_Error, que es información
	 * técnica genérica, no del cliente), se libera la idempotency key,
	 * y se responde error real al usuario en vez de un falso éxito.
	 * ===================================================== */
	if ( ! $post_id || is_wp_error( $post_id ) ) {
		if ( $attachment_path && file_exists( $attachment_path ) ) {
			wp_delete_file( $attachment_path );
		}

		$error_detail = is_wp_error( $post_id ) ? $post_id->get_error_message() : 'wp_insert_post() devolvió 0';
		error_log( '[CE Construction] No se pudo registrar la cotización (wp_insert_post falló): ' . $error_detail ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- registro deliberado para diagnóstico de servidor, sin datos personales.

		if ( $idempotency_hash ) {
			ce_construction_idempotency_release( $idempotency_hash );
		}

		wp_send_json_error( array( 'message' => __( 'No se pudo registrar tu solicitud. Por favor inténtalo de nuevo en unos minutos.', 'ce-construction' ) ), 500 );
	}

	// QA-034: primer checkpoint — el post ya existe. A partir de aquí,
	// cualquier replay de esta misma idempotency key nunca debe crear
	// una segunda cotización, pase lo que pase con el correo.
	if ( $idempotency_hash ) {
		ce_construction_idempotency_mark_post_created( $idempotency_hash, $post_id );
	}

	// Nombre guardado como meta propio (además de ir en post_title) para
	// poder recomponer el correo de forma fiable en el flujo de "resume"
	// de QA-034, sin depender del parseo de post_title ni del $_POST de
	// una petición repetida.
	update_post_meta( $post_id, '_ce_name', $name );
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
			// QA-031: se usa el nombre ORIGINAL (no el aleatorio en disco)
			// como post_title, únicamente para que el registro sea legible
			// en la Media Library — no afecta la ruta física del archivo.
			'post_title'     => ! empty( $original_name ) ? $original_name : ( isset( $attachment_name ) ? $attachment_name : '' ),
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
		);
		$attachment_id = wp_insert_attachment( $attachment_data, $attachment_path, $post_id );

		if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
			$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment_path );
			wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
			update_post_meta( $post_id, '_ce_attachment_id', $attachment_id );

			// QA-031: nombre original del archivo (antes de renombrarlo
			// a un nombre aleatorio) — se usa solo para el nombre de
			// descarga mostrado al administrador vía el endpoint
			// autenticado, nunca para localizar el archivo en disco.
			if ( ! empty( $original_name ) ) {
				update_post_meta( $attachment_id, '_ce_attachment_original_name', $original_name );
			}
		} else {
			// El post ya existe (no es el caso QA-033), pero el
			// registro del attachment en sí falló: se deja constancia
			// en el log, sin datos personales, para que el administrador
			// pueda revisar manualmente ese archivo en
			// _ce_attachment_path si hiciera falta.
			error_log( '[CE Construction] wp_insert_attachment() falló para la cotización #' . $post_id . ' (el post y el archivo en disco sí existen).' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	// 7. Envío de correo electrónico.
	$sent = ce_construction_send_quote_email( $post_id, $name, $email, $phone, $company, $service, $message );

	if ( ! $sent ) {
		$response = array(
			'success' => false,
			'data'    => array( 'message' => __( 'Tu solicitud fue registrada, pero hubo un problema enviando la notificación por correo. Te contactaremos igualmente.', 'ce-construction' ) ),
			'status_code' => 200,
		);
		if ( $idempotency_hash ) {
			ce_construction_idempotency_mark_done( $idempotency_hash, $response );
		}
		wp_send_json_error( $response['data'], 200 );
	}

	$response = array(
		'success' => true,
		'data'    => array( 'message' => __( '¡Gracias! Tu solicitud de cotización fue enviada. Te contactaremos muy pronto.', 'ce-construction' ) ),
	);
	if ( $idempotency_hash ) {
		ce_construction_idempotency_mark_done( $idempotency_hash, $response );
	}
	wp_send_json_success( $response['data'] );
}
add_action( 'wp_ajax_ce_submit_quote', 'ce_construction_handle_quote_form' );
add_action( 'wp_ajax_nopriv_ce_submit_quote', 'ce_construction_handle_quote_form' );

/**
 * Envía el correo de notificación de una cotización y marca
 * `_ce_email_sent` en el post si tiene éxito. Extraído a su propia
 * función (QA-034, Sprint 8, Entregable 8.4) porque ahora se invoca
 * desde dos puntos: el flujo normal (paso 7 de
 * ce_construction_handle_quote_form()) y el flujo de "resume" de una
 * idempotency key cuyo post ya existía pero cuyo correo no se había
 * confirmado.
 */
function ce_construction_send_quote_email( $post_id, $name, $email, $phone, $company, $service, $message ) {
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

	$attachment_path = get_post_meta( $post_id, '_ce_attachment_path', true );
	$attachments     = $attachment_path ? array( $attachment_path ) : array();

	$sent = wp_mail( $to, $subject, $body, $headers, $attachments );

	if ( $sent ) {
		update_post_meta( $post_id, '_ce_email_sent', 1 );
	}

	return $sent;
}

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
 *
 * Este mismo evento cron es reutilizado desde Sprint 8, Entregable
 * 8.4 (ver inc/form-guards.php, ce_construction_purge_expired_guards())
 * para purgar también las filas vencidas de la tabla de guardas de
 * rate-limit/idempotencia (QA-032/QA-034), en vez de programar un
 * segundo cron independiente para lo mismo.
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

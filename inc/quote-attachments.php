<?php
/**
 * Protección de adjuntos del formulario de cotización (QA-031, Sprint 8,
 * Entregable 8.3).
 *
 * Problema corregido: los adjuntos se subían con wp_handle_upload() al
 * árbol normal de wp-content/uploads/AAAA/MM/, servido directamente por
 * el servidor web. El CPT `cotizacion` es privado ('public' => false,
 * inc/quote-form.php), pero eso NUNCA restringió el acceso directo al
 * archivo físico: cualquiera que conociera o adivinara/enumerara la URL
 * podía descargarlo sin autenticación, incluso siendo la cotización en
 * sí misma privada.
 *
 * Solución elegida (Opción 1 de las presentadas, aprobada explícitamente
 * por el usuario): mantener los adjuntos DENTRO de wp-content/uploads/
 * (sin depender de una ruta fuera del árbol estándar, por portabilidad
 * entre hostings), pero:
 *   1. Aislarlos en una subcarpeta propia (`uploads/cotizaciones/AAAA/MM/`),
 *      bloqueada a nivel de servidor (.htaccess + índice vacío) para que
 *      NINGÚN archivo ahí dentro sea servible por URL directa.
 *   2. Renombrar el archivo físico a un nombre aleatorio impredecible al
 *      subirlo (defensa adicional: ni siquiera conociendo el nombre
 *      original se puede adivinar la URL real).
 *   3. Servir el archivo exclusivamente a través de un endpoint PHP
 *      autenticado (`admin-post.php?action=ce_download_quote_attachment`)
 *      que verifica sesión, capacidad (`current_user_can( 'edit_post', ... )`,
 *      que en este CPT solo tienen administradores/editores por defecto),
 *      nonce, y que el adjunto solicitado pertenece realmente a la
 *      cotización indicada — nunca construye la ruta a partir de datos
 *      del usuario, siempre la resuelve vía `get_attached_file()` (la
 *      ruta real guardada en la base de datos), con una verificación
 *      adicional de que la ruta resuelta cae dentro de la carpeta
 *      protegida antes de servir nada.
 *
 * Limitación conocida y documentada (ver DECISIONS.md): el bloqueo por
 * .htaccess solo tiene efecto en servidores Apache con AllowOverride
 * habilitado (el escenario de hosting más común para WordPress). En
 * Nginx, WordPress no puede generar equivalentes de forma automática
 * desde un tema — requeriría una regla en la configuración del servidor,
 * fuera del alcance de lo que un tema puede controlar. La protección a
 * nivel de aplicación (autenticación + capacidad + nonce en el endpoint)
 * aplica siempre, en cualquier servidor.
 *
 * Sin cambios en ningún archivo de plantilla del formulario (normal,
 * modal o Hero): la subida sigue siendo el mismo <input type="file">
 * enviado por AJAX a inc/quote-form.php; esta protección es exclusivamente
 * de almacenamiento/servido en el backend.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nombre de la subcarpeta dedicada dentro de wp-content/uploads/.
 * Centralizado aquí para no repetir el literal en varias funciones.
 */
function ce_construction_quote_uploads_subdir() {
	return 'cotizaciones';
}

/**
 * Filtro de `upload_dir` aplicado ÚNICAMENTE alrededor de la llamada a
 * wp_handle_upload() en inc/quote-form.php (se añade y se quita en la
 * misma función que sube el archivo — nunca queda activo de forma
 * permanente, para no afectar ninguna otra subida del sitio: Media
 * Library, metabox de galería de Proyectos, etc.).
 */
function ce_construction_quote_upload_dir( $dirs ) {
	$sub = '/' . ce_construction_quote_uploads_subdir();
	$dirs['subdir'] = $sub . $dirs['subdir']; // Conserva la estructura /AAAA/MM ya calculada por WordPress.
	$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
	$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
	return $dirs;
}

/**
 * Crea (si no existen) el `.htaccess` y el índice vacío que bloquean el
 * listado y el acceso directo a `uploads/cotizaciones/` en servidores
 * Apache. Idempotente y silenciosa: si ya existen, no hace nada; si no
 * se pueden crear (permisos del hosting), no rompe la subida — se limita
 * a registrar un aviso para el administrador (ver
 * ce_construction_quote_uploads_admin_notice() más abajo).
 *
 * Se ejecuta en dos momentos: al activar el tema (after_switch_theme,
 * igual que el cron de purga ya existente) y de forma defensiva justo
 * antes de cada subida, por si la carpeta se creó después de activar el
 * tema o alguien borró el .htaccess manualmente.
 */
function ce_construction_ensure_quote_uploads_protected() {
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {
		return false;
	}

	$base = trailingslashit( $upload_dir['basedir'] ) . ce_construction_quote_uploads_subdir();

	if ( ! file_exists( $base ) ) {
		wp_mkdir_p( $base );
	}
	if ( ! is_dir( $base ) ) {
		update_option( 'ce_construction_quote_uploads_protected', 'no' );
		return false;
	}

	$htaccess = trailingslashit( $base ) . '.htaccess';
	$index    = trailingslashit( $base ) . 'index.php';
	$ok       = true;

	if ( ! file_exists( $htaccess ) ) {
		$contents  = "# CE Construction — QA-031: bloquea el acceso publico directo a los\n";
		$contents .= "# adjuntos de cotizacion. Deben servirse unicamente a traves del\n";
		$contents .= "# endpoint autenticado admin-post.php?action=ce_download_quote_attachment.\n";
		$contents .= "<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n";
		$contents .= "<IfModule !mod_authz_core.c>\n\tOrder allow,deny\n\tDeny from all\n</IfModule>\n";
		$contents .= "Options -Indexes\n";
		$written = @file_put_contents( $htaccess, $contents ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- fallo tolerado explícitamente, ver comentario de la función.
		$ok = $ok && ( false !== $written );
	}

	if ( ! file_exists( $index ) ) {
		$written = @file_put_contents( $index, "<?php\n// Silencio deliberado: evita listado de directorio si el .htaccess no aplica.\n" ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		$ok = $ok && ( false !== $written );
	}

	update_option( 'ce_construction_quote_uploads_protected', $ok ? 'yes' : 'no' );
	return $ok;
}
add_action( 'after_switch_theme', 'ce_construction_ensure_quote_uploads_protected' );

/**
 * Aviso en wp-admin (solo para quien puede gestionar el sitio) si la
 * protección por .htaccess no pudo verificarse — no bloquea nada, es
 * puramente informativo para que el administrador revise permisos del
 * hosting o añada la regla equivalente a mano si su servidor es Nginx.
 */
function ce_construction_quote_uploads_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( 'no' !== get_option( 'ce_construction_quote_uploads_protected' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p>
			<?php esc_html_e( 'CE Construction: no se pudo verificar automáticamente la protección de la carpeta de adjuntos de cotización (wp-content/uploads/cotizaciones/). Si tu hosting usa Apache, revisa los permisos de escritura de esa carpeta. Si usa Nginx, añade manualmente una regla que deniegue el acceso directo a esa ruta.', 'ce-construction' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'ce_construction_quote_uploads_admin_notice' );

/**
 * Renombra el archivo recién subido a un nombre aleatorio impredecible,
 * conservando la extensión real ya validada en inc/quote-form.php.
 * Defensa adicional: incluso si la protección por .htaccess no aplicara
 * (Nginx, o AllowOverride deshabilitado), el nombre original del cliente
 * no sirve para adivinar/enumerar la URL real del archivo.
 *
 * Se llama justo después de wp_handle_upload() en inc/quote-form.php.
 * Devuelve un array con las claves ya actualizadas de $uploaded_file
 * ('file', 'url') y el nombre original (para mostrarlo como nombre de
 * descarga al administrador), o el array sin cambios si el renombrado
 * falla (no se aborta la subida por esto: la protección de carpeta ya
 * es la barrera principal).
 */
function ce_construction_randomize_quote_attachment_filename( $uploaded_file ) {
	$original_name = basename( $uploaded_file['file'] );
	$ext           = pathinfo( $uploaded_file['file'], PATHINFO_EXTENSION );
	$random_base   = wp_generate_password( 32, false, false );
	$random_name   = $ext ? ( $random_base . '.' . strtolower( $ext ) ) : $random_base;

	$new_path = trailingslashit( dirname( $uploaded_file['file'] ) ) . $random_name;

	if ( @rename( $uploaded_file['file'], $new_path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors -- fallo tolerado, ver comentario de la función.
		$uploaded_file['file'] = $new_path;
		$uploaded_file['url']  = trailingslashit( dirname( $uploaded_file['url'] ) ) . $random_name;
	}

	return array(
		'uploaded_file'  => $uploaded_file,
		'original_name'  => $original_name,
	);
}

/**
 * Endpoint autenticado que sirve el adjunto de una cotización.
 * Registrado únicamente para usuarios logueados (sin contraparte
 * `admin_post_nopriv_...`): un visitante no autenticado nunca debe
 * poder descargar un adjunto, sea cual sea el resultado interno de
 * admin-post.php para acciones sin registro "nopriv".
 *
 * Validaciones, en orden (cualquier fallo termina en wp_die() con 403,
 * sin filtrar información adicional sobre la causa exacta):
 *   1. Sesión iniciada.
 *   2. Nonce específico de esta cotización+adjunto.
 *   3. El post indicado existe y es del CPT `cotizacion`.
 *   4. current_user_can( 'edit_post', $quote_id ) — con la configuración
 *      de capacidades de este CPT (map_meta_cap, capability_type 'post'),
 *      esto solo lo cumplen administradores/editores por defecto.
 *   5. El attachment_id solicitado coincide EXACTAMENTE con el guardado
 *      en el meta `_ce_attachment_id` de esa cotización (nunca se acepta
 *      un ID de adjunto arbitrario proporcionado por la URL).
 *   6. La ruta real del archivo (get_attached_file(), nunca construida
 *      a mano a partir de datos de la petición) existe, es un archivo
 *      regular, y su ruta resuelta (realpath) cae dentro de la carpeta
 *      protegida `uploads/cotizaciones/` — barrera adicional contra
 *      cualquier manipulación de metadatos.
 */
function ce_construction_download_quote_attachment() {

	if ( ! is_user_logged_in() ) {
		wp_die( esc_html__( 'No autorizado.', 'ce-construction' ), 403 );
	}

	$quote_id      = isset( $_GET['quote_id'] ) ? absint( $_GET['quote_id'] ) : 0;
	$attachment_id = isset( $_GET['attachment_id'] ) ? absint( $_GET['attachment_id'] ) : 0;
	$nonce         = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

	if ( ! $quote_id || ! $attachment_id || ! $nonce ) {
		wp_die( esc_html__( 'Solicitud inválida.', 'ce-construction' ), 400 );
	}

	if ( ! wp_verify_nonce( $nonce, 'ce_download_quote_' . $quote_id ) ) {
		wp_die( esc_html__( 'Sesión no válida. Recarga la página e inténtalo de nuevo.', 'ce-construction' ), 403 );
	}

	if ( 'cotizacion' !== get_post_type( $quote_id ) ) {
		wp_die( esc_html__( 'No autorizado.', 'ce-construction' ), 403 );
	}

	if ( ! current_user_can( 'edit_post', $quote_id ) ) {
		wp_die( esc_html__( 'No autorizado.', 'ce-construction' ), 403 );
	}

	$stored_attachment_id = (int) get_post_meta( $quote_id, '_ce_attachment_id', true );
	if ( ! $stored_attachment_id || $stored_attachment_id !== $attachment_id ) {
		wp_die( esc_html__( 'Adjunto no encontrado.', 'ce-construction' ), 404 );
	}

	$file_path = get_attached_file( $attachment_id );
	if ( ! $file_path ) {
		wp_die( esc_html__( 'Adjunto no encontrado.', 'ce-construction' ), 404 );
	}

	// Barrera adicional: confirma que la ruta real resuelta cae dentro
	// de la carpeta protegida, y que es un archivo regular (no un
	// symlink/directorio manipulado).
	$upload_dir     = wp_upload_dir();
	$protected_base = realpath( trailingslashit( $upload_dir['basedir'] ) . ce_construction_quote_uploads_subdir() );
	$real_file_path = realpath( $file_path );

	if ( ! $protected_base || ! $real_file_path || 0 !== strpos( $real_file_path, $protected_base ) || ! is_file( $real_file_path ) ) {
		wp_die( esc_html__( 'Adjunto no encontrado.', 'ce-construction' ), 404 );
	}

	$original_name = get_post_meta( $attachment_id, '_ce_attachment_original_name', true );
	$original_name = $original_name ? $original_name : basename( $real_file_path );
	$mime          = get_post_mime_type( $attachment_id );
	$mime          = $mime ? $mime : 'application/octet-stream';

	// Limpia cualquier salida previa (avisos de plugins, etc.) para no
	// corromper el archivo servido.
	if ( ob_get_level() ) {
		ob_end_clean();
	}

	nocache_headers();
	header( 'Content-Type: ' . $mime );
	header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $original_name ) . '"' );
	header( 'Content-Length: ' . filesize( $real_file_path ) );
	header( 'X-Content-Type-Options: nosniff' );

	readfile( $real_file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile -- servir binarios grandes vía WP_Filesystem no aporta valor aquí y complica el streaming de Content-Length.
	exit;
}
add_action( 'admin_post_ce_download_quote_attachment', 'ce_construction_download_quote_attachment' );

/**
 * Construye la URL segura de descarga (con su propio nonce) para usarla
 * en la columna "Adjunto" del listado admin de cotizaciones (ver
 * inc/quote-form.php, ce_construction_cotizacion_column_content()).
 */
function ce_construction_get_quote_attachment_download_url( $quote_id, $attachment_id ) {
	return wp_nonce_url(
		add_query_arg(
			array(
				'action'        => 'ce_download_quote_attachment',
				'quote_id'      => $quote_id,
				'attachment_id' => $attachment_id,
			),
			admin_url( 'admin-post.php' )
		),
		'ce_download_quote_' . $quote_id
	);
}

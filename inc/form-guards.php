<?php
/**
 * Guardas de formulario: rate-limiting atómico e idempotencia server-side
 * para el formulario de cotización.
 *
 * Corrige, todos sobre el mismo flujo y la misma tabla nueva:
 *   - QA-032 (Sprint 8, Entregable 8.4): el rate-limiting anterior usaba
 *     get_transient()+set_transient() como dos operaciones separadas y no
 *     atómicas — dos peticiones concurrentes desde la misma IP podían leer
 *     el mismo contador antes de que cualquiera de las dos lo actualizara,
 *     permitiendo superar el límite de 3 envíos/10 min en ráfagas.
 *   - QA-034 (Sprint 8, Entregable 8.4): no existía ninguna protección de
 *     idempotencia server-side. Un doble clic, una doble pestaña, o un
 *     reintento automático del navegador podían crear cotizaciones y
 *     correos duplicados.
 *
 * Ambos hallazgos comparten la misma tabla dedicada `{prefix}ce_form_guards`
 * porque ambos son, en esencia, el mismo problema (reclamar una operación
 * de forma atómica bajo concurrencia), solo con distinta clave y ventana.
 *
 * IMPORTANTE — motivo de tabla propia en vez de transients/object cache:
 * un transient respaldado únicamente por wp_options (sin object cache
 * persistente tipo Redis/Memcached, el caso más común en hosting
 * compartido/gestionado) NO ofrece ninguna operación atómica de
 * incremento condicional. Una tabla propia con una UNIQUE KEY sí permite
 * usar `INSERT ... ON DUPLICATE KEY UPDATE` / `INSERT IGNORE`, que MySQL
 * garantiza atómicos a nivel de fila, sin importar cuántos workers PHP-FPM
 * o servidores atiendan peticiones concurrentes.
 *
 * Migración de esquema (decisión explícita, ver DECISIONS.md):
 *   - Enganchada a `admin_init` (no a `after_setup_theme`, que se ejecuta
 *     en cada request de frontend): así la verificación de versión de
 *     esquema no tiene ningún coste en el sitio público.
 *   - También enganchada a `after_switch_theme` como red de seguridad
 *     para la activación inicial del tema.
 *   - Idempotente por diseño: dbDelta() y la comparación de
 *     CE_THEME_DB_VERSION contra la opción guardada cubren tanto
 *     instalación nueva como actualización de una instalación donde el
 *     tema ya está activo (subir código por Git/FTP y recargar wp-admin),
 *     y no producen ningún efecto adverso si se ejecutan repetidamente.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nombre completo de la tabla de guardas, con el prefijo real de la
 * instalación (nunca se asume "wp_").
 */
function ce_construction_form_guards_table() {
	global $wpdb;
	return $wpdb->prefix . 'ce_form_guards';
}

/**
 * Ventana de "abandono" para una idempotency key reclamada pero cuyo
 * post_id sigue NULL: por debajo de este umbral se interpreta como una
 * petición concurrente genuina procesándose ahora mismo (se responde sin
 * reprocesar); por encima, se asume que el proceso anterior murió antes
 * de crear el post y es seguro reprocesar reutilizando la misma fila.
 */
function ce_construction_idempotency_stuck_threshold() {
	return 10; // segundos.
}

/**
 * Crea o actualiza el esquema de `{prefix}ce_form_guards` de forma
 * idempotente, comparando CE_THEME_DB_VERSION (definida en functions.php)
 * contra la versión ya registrada en wp_options.
 */
function ce_construction_maybe_upgrade_db() {
	if ( ! defined( 'CE_THEME_DB_VERSION' ) ) {
		return;
	}

	$installed_version = get_option( 'ce_construction_db_version', '' );
	if ( $installed_version === CE_THEME_DB_VERSION ) {
		return;
	}

	global $wpdb;
	$table           = ce_construction_form_guards_table();
	$charset_collate = $wpdb->get_charset_collate();

	// guard_type: 'rate_limit' (QA-032) o 'idempotency' (QA-034).
	// guard_key: hash SHA-256 — HMAC de la IP para 'rate_limit' (ver
	//            ce_construction_hash_ip()), hash simple de la
	//            idempotency key cruda para 'idempotency'. Nunca se
	//            guarda la IP ni la key en texto plano.
	// attempts: solo usado por 'rate_limit'.
	// post_id / status / response: solo usados por 'idempotency'
	//            (modelo de checkpoints, ver ce_construction_handle_quote_form()).
	$sql = "CREATE TABLE {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		guard_type VARCHAR(20) NOT NULL,
		guard_key CHAR(64) NOT NULL,
		attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
		post_id BIGINT UNSIGNED NULL DEFAULT NULL,
		status VARCHAR(20) NOT NULL DEFAULT 'processing',
		response LONGTEXT NULL,
		expires_at DATETIME NOT NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		UNIQUE KEY guard_unique (guard_type, guard_key),
		KEY expires_at (expires_at)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	update_option( 'ce_construction_db_version', CE_THEME_DB_VERSION, true );
}
add_action( 'admin_init', 'ce_construction_maybe_upgrade_db' );
add_action( 'after_switch_theme', 'ce_construction_maybe_upgrade_db' );

/* =========================================================
 * QA-032 — Rate-limiting atómico por IP.
 * ========================================================= */

/**
 * HMAC de la IP del cliente con el salt de autenticación de la propia
 * instalación (wp_salt('auth')) — nunca se guarda la IP en texto plano
 * ni con un hash reversible/enumerable (md5 simple, como tenía la
 * versión anterior).
 */
function ce_construction_hash_ip( $ip ) {
	return hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );
}

/**
 * Reclama/incrementa el contador de rate-limit para una IP de forma
 * atómica (una sola sentencia SQL, sin ventana de lectura-luego-escritura).
 *
 * Devuelve true si la petición está dentro del límite permitido, false
 * si debe rechazarse por exceso de solicitudes.
 */
function ce_construction_claim_rate_limit( $ip, $max_attempts = 3, $window_seconds = 600 ) {
	global $wpdb;

	$table   = ce_construction_form_guards_table();
	$key     = ce_construction_hash_ip( $ip );
	$now     = gmdate( 'Y-m-d H:i:s' );
	$expires = gmdate( 'Y-m-d H:i:s', time() + $window_seconds );

	// INSERT ... ON DUPLICATE KEY UPDATE es una única operación atómica a
	// nivel de fila en MySQL/MariaDB: si la ventana anterior ya expiró
	// (expires_at < UTC_TIMESTAMP()), reinicia el contador a 1 y extiende
	// la ventana; si sigue vigente, solo incrementa. No hay lectura
	// separada antes de escribir, por lo que dos peticiones concurrentes
	// de la misma IP no pueden leer el mismo valor y perder un incremento.
	$wpdb->query( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $table es un identificador interno (prefijo + literal fijo), no dato de usuario; el resto de valores va parametrizado.
		$wpdb->prepare(
			"INSERT INTO {$table} (guard_type, guard_key, attempts, expires_at, created_at)
			 VALUES ('rate_limit', %s, 1, %s, %s)
			 ON DUPLICATE KEY UPDATE
			 	attempts = IF( expires_at < UTC_TIMESTAMP(), 1, attempts + 1 ),
			 	expires_at = IF( expires_at < UTC_TIMESTAMP(), %s, expires_at )",
			$key,
			$expires,
			$now,
			$expires
		)
	);

	$attempts = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT attempts FROM {$table} WHERE guard_type = 'rate_limit' AND guard_key = %s", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$key
		)
	);

	return $attempts <= $max_attempts;
}

/* =========================================================
 * QA-034 — Idempotencia server-side atómica.
 *
 * Precondición documentada (ver DECISIONS.md): la key se genera en PHP
 * en el momento del render del formulario y viaja en un campo oculto del
 * HTML — igual que el nonce actual del formulario, esto asume que la
 * página que contiene el formulario no está detrás de un caché de
 * página completa (full-page cache) que serviría la misma key a
 * visitantes distintos. El tema no usa ningún plugin de ese tipo hoy;
 * si se activara en el futuro, requeriría una nueva decisión de
 * arquitectura, fuera de este Entregable.
 * ========================================================= */

/**
 * Genera una idempotency key nueva y aleatoria (no reversible), para
 * imprimir en un campo oculto en cada render del formulario.
 * Nunca se guarda esta cadena cruda en la base de datos — solo su hash
 * (ver ce_construction_idempotency_hash()).
 */
function ce_construction_generate_idempotency_key() {
	try {
		return bin2hex( random_bytes( 32 ) );
	} catch ( Exception $e ) {
		// Entorno sin CSPRNG disponible (extremadamente raro en PHP 7+):
		// fallback a la utilidad de WordPress, igual de impredecible.
		return wp_generate_password( 64, false, false );
	}
}

function ce_construction_idempotency_hash( $raw_key ) {
	return hash( 'sha256', $raw_key );
}

/**
 * Intenta reclamar atómicamente una idempotency key.
 *
 * Devuelve un array:
 *   'claimed' => true  → esta petición ganó la reclamación, debe procesar
 *                        la solicitud desde cero (o reprocesar si estaba
 *                        abandonada, ver 'resume' más abajo).
 *   'claimed' => false → la key ya existía; 'row' trae el estado guardado
 *                        para que el llamador decida cómo responder sin
 *                        repetir efectos secundarios.
 *   'resume'  => true  → caso especial de 'claimed' true: la fila ya
 *                        existía pero se considera abandonada (status
 *                        'processing', post_id NULL, más antigua que el
 *                        umbral de "atascada") — se reutiliza la misma
 *                        fila en vez de insertar una nueva.
 */
function ce_construction_claim_idempotency_key( $raw_key, $window_seconds = 600 ) {
	global $wpdb;

	$table   = ce_construction_form_guards_table();
	$hash    = ce_construction_idempotency_hash( $raw_key );
	$now     = gmdate( 'Y-m-d H:i:s' );
	$expires = gmdate( 'Y-m-d H:i:s', time() + $window_seconds );

	// INSERT IGNORE: si la UNIQUE KEY ya existe, la sentencia no falla,
	// simplemente no inserta nada — así distinguimos "gané la
	// reclamación" (rows_affected 1) de "ya existía" (rows_affected 0)
	// sin necesitar una lectura previa (que reintroduciría la misma
	// condición de carrera que se quiere evitar).
	$wpdb->query( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->prepare(
			"INSERT IGNORE INTO {$table} (guard_type, guard_key, status, expires_at, created_at)
			 VALUES ('idempotency', %s, 'processing', %s, %s)",
			$hash,
			$expires,
			$now
		)
	);

	if ( 1 === (int) $wpdb->rows_affected ) {
		return array(
			'claimed' => true,
			'resume'  => false,
			'hash'    => $hash,
			'row'     => null,
		);
	}

	// La key ya existía: se consulta su estado actual para decidir cómo
	// responder sin repetir ningún efecto secundario.
	$row = $wpdb->get_row( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE guard_type = 'idempotency' AND guard_key = %s",
			$hash
		)
	);

	if ( ! $row ) {
		// Condición de carrera extremadamente improbable (la fila se
		// borró entre el INSERT IGNORE y este SELECT, p. ej. por el cron
		// de purga). Se trata como si hubiéramos ganado la reclamación,
		// reintentando el INSERT una sola vez.
		return ce_construction_claim_idempotency_key( $raw_key, $window_seconds );
	}

	if ( 'processing' === $row->status && empty( $row->post_id ) ) {
		$age_seconds = time() - strtotime( $row->created_at . ' UTC' );
		if ( $age_seconds >= ce_construction_idempotency_stuck_threshold() ) {
			// Abandonada: el intento anterior murió antes de llegar a
			// crear el post (nada se llegó a persistir). Es seguro
			// reprocesar desde cero reutilizando esta misma fila.
			return array(
				'claimed' => true,
				'resume'  => true,
				'hash'    => $hash,
				'row'     => $row,
			);
		}
	}

	return array(
		'claimed' => false,
		'resume'  => false,
		'hash'    => $hash,
		'row'     => $row,
	);
}

/**
 * Primer checkpoint: se llama en cuanto wp_insert_post() crea la
 * cotización con éxito, ANTES de intentar el envío de correo. Deja
 * constancia del post_id en la fila de guarda para que una petición
 * repetida (reintento del navegador, doble pestaña) pueda detectar que
 * el post ya existe y nunca cree una segunda cotización.
 */
function ce_construction_idempotency_mark_post_created( $hash, $post_id ) {
	global $wpdb;
	$table = ce_construction_form_guards_table();
	$wpdb->update(
		$table,
		array( 'post_id' => $post_id ),
		array(
			'guard_type' => 'idempotency',
			'guard_key'  => $hash,
		),
		array( '%d' ),
		array( '%s', '%s' )
	);
}

/**
 * Segundo checkpoint: se llama al completar el flujo (con o sin éxito de
 * wp_mail()), guardando la respuesta JSON exacta que debe devolverse a
 * cualquier replay futuro de esta misma key.
 */
function ce_construction_idempotency_mark_done( $hash, $response ) {
	global $wpdb;
	$table = ce_construction_form_guards_table();
	$wpdb->update(
		$table,
		array(
			'status'   => 'done',
			'response' => wp_json_encode( $response ),
		),
		array(
			'guard_type' => 'idempotency',
			'guard_key'  => $hash,
		),
		array( '%s', '%s' ),
		array( '%s', '%s' )
	);
}

/**
 * Libera una key reclamada sin haber llegado siquiera a crear el post
 * (p. ej. la petición falló en validación de campos o de archivo, antes
 * del paso 6 del handler). Sin esto, una key legítima "gastada" en un
 * intento fallido por validación no podría reutilizarse hasta expirar,
 * y el navegador SÍ debe poder reintentar tras corregir un campo.
 */
function ce_construction_idempotency_release( $hash ) {
	global $wpdb;
	$table = ce_construction_form_guards_table();
	$wpdb->delete(
		$table,
		array(
			'guard_type' => 'idempotency',
			'guard_key'  => $hash,
		),
		array( '%s', '%s' )
	);
}

/**
 * Limpieza periódica de filas vencidas de ambos tipos de guarda.
 * Reutiliza el cron diario ya existente de QA-003
 * (`ce_construction_quote_cleanup_event`, ver inc/quote-form.php) en vez
 * de programar un cron nuevo.
 */
function ce_construction_purge_expired_guards() {
	global $wpdb;
	$table = ce_construction_form_guards_table();
	$wpdb->query( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $table es un identificador interno, sin dato de usuario involucrado.
		"DELETE FROM {$table} WHERE expires_at < UTC_TIMESTAMP()"
	);
}
add_action( 'ce_construction_quote_cleanup_event', 'ce_construction_purge_expired_guards' );

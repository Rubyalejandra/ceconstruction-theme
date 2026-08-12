<?php
/**
 * CE Construction Theme - Bootstrap
 *
 * Este archivo NO contiene lógica de negocio directamente.
 * Cada funcionalidad vive en su propio archivo dentro de /inc,
 * siguiendo el principio de responsabilidad única pedido
 * en los requisitos del proyecto.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Seguridad: evita acceso directo al archivo.
}

// QA-008 (Sprint 5, Fase 1) fijó esta constante a mano por primera vez,
// con la instrucción de "actualizarla en cada versión que modifique
// CSS/JS". Ese enfoque manual volvió a fallar exactamente como cabía
// esperar: la constante quedó congelada en '0.4.1' desde el Sprint 5
// pese a que el proyecto avanzó hasta v0.8.0 (ver CHANGELOG.md) — ver
// QA-030 (Sprint 8, Entregable 8.2 — corrección alta).
//
// Solución: se elimina el valor hardcodeado. Ahora se deriva de
// wp_get_theme()->get('Version'), que lee directamente la cabecera
// "Version:" de style.css — la misma cabecera que WordPress ya usa
// de forma nativa para mostrar la versión del tema en Apariencia →
// Temas. Esto unifica en una sola fuente lo que antes eran DOS
// valores independientes que podían desincronizarse entre sí (esta
// constante y la cabecera de style.css, ver QA-022 histórico).
//
// Importante: esta constante ya NO es el mecanismo de cache-busting
// de los assets del tema. Ese cache-busting ahora usa filemtime() de
// cada archivo real en disco vía ce_construction_asset_version() en
// inc/enqueue.php, que se actualiza automáticamente en cada cambio
// sin depender de que nadie recuerde subir ningún número — ver ese
// archivo para el detalle. CE_THEME_VERSION se conserva únicamente
// como valor informativo/de compatibilidad general.
// Ver DECISIONS.md D-044.
define( 'CE_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'CE_THEME_DIR', get_template_directory() );
define( 'CE_THEME_URI', get_template_directory_uri() );

/**
 * Carga segura de módulos del tema.
 */
function ce_construction_require_modules() {
	$modules = array(
		'inc/setup.php',            // Theme support, menus, sidebars.
		'inc/enqueue.php',          // CSS / JS.
		'inc/customizer.php',       // Theme Customizer.
		'inc/cpt-servicios.php',    // CPT Servicios.
		'inc/cpt-proyectos.php',    // CPT Proyectos.
		'inc/cpt-testimonios.php',  // CPT Testimonios.
		'inc/cpt-equipo.php',       // CPT Equipo.
		'inc/cpt-clientes.php',     // CPT Clientes.
		'inc/cpt-faq.php',          // CPT Preguntas Frecuentes.
		'inc/meta-boxes.php',       // Campos personalizados (metaboxes) para los CPTs.
		'inc/quote-form.php',       // Formulario de cotización (AJAX + email + nonce).
		'inc/seo.php',              // Meta tags, Open Graph, Schema, breadcrumbs.
		'inc/helpers.php',          // Funciones auxiliares reutilizables.
		'inc/widgets.php',          // Widgets / sidebars del footer.
	);

	foreach ( $modules as $module ) {
		$path = CE_THEME_DIR . '/' . $module;
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
ce_construction_require_modules();

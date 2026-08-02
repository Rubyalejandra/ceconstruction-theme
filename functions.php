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

define( 'CE_THEME_VERSION', '1.0.0' );
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

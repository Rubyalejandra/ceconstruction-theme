<?php
/**
 * Carga de estilos y scripts del tema.
 * Performance: se usan defer/async donde aplica y se registra
 * solo lo necesario por contexto.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_enqueue_assets() {

	// Tipografía (Poppins + Inter).
	wp_enqueue_style(
		'ce-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);

	// Font Awesome (iconografía pedida en el brief).
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	// style.css raíz (requerido por WordPress).
	wp_enqueue_style( 'ce-construction-style', get_stylesheet_uri(), array(), CE_THEME_VERSION );

	// Hoja de estilos principal del tema (design system real).
	wp_enqueue_style(
		'ce-construction-main',
		CE_THEME_URI . '/assets/css/main.css',
		array( 'ce-construction-style' ),
		CE_THEME_VERSION
	);

	// JS principal del tema (ES6 modular), cargado en el footer.
	wp_enqueue_script(
		'ce-construction-main',
		CE_THEME_URI . '/assets/js/main.js',
		array(),
		CE_THEME_VERSION,
		true
	);
	wp_script_add_data( 'ce-construction-main', 'defer', true );

	// Variables PHP -> JS (ajax url, nonce del formulario, textos).
	wp_localize_script( 'ce-construction-main', 'ceConstructionData', array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'quoteNonce'   => wp_create_nonce( 'ce_quote_form_action' ),
		'whatsapp'     => get_theme_mod( 'ce_whatsapp_number', '' ),
		'i18n'         => array(
			'sending' => __( 'Enviando...', 'ce-construction' ),
			'error'   => __( 'Ocurrió un error. Intenta nuevamente.', 'ce-construction' ),
		),
	) );

	// Soporte para comentarios anidados en single de blog.
	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ce_construction_enqueue_assets' );

/**
 * Añade defer a nuestro script principal (mejora Core Web Vitals).
 */
function ce_construction_add_defer_attribute( $tag, $handle ) {
	if ( 'ce-construction-main' === $handle && strpos( $tag, 'defer' ) === false ) {
		$tag = str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'ce_construction_add_defer_attribute', 10, 2 );

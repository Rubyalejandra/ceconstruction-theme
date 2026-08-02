<?php
/**
 * Theme setup: soporte de features, menús y sidebars.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_setup() {
	// Traducciones.
	load_theme_textdomain( 'ce-construction', CE_THEME_DIR . '/languages' );

	// Soporte de características nativas de WordPress.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );

	// Tamaños de imagen personalizados.
	add_image_size( 'ce-hero', 1920, 1080, true );
	add_image_size( 'ce-card', 640, 480, true );
	add_image_size( 'ce-thumb', 320, 240, true );

	// Menús de navegación.
	register_nav_menus( array(
		'primary' => __( 'Menú Principal', 'ce-construction' ),
		'footer'  => __( 'Menú de Footer', 'ce-construction' ),
	) );
}
add_action( 'after_setup_theme', 'ce_construction_setup' );

/**
 * Sidebars / áreas de widgets (footer).
 */
function ce_construction_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer - Columna 1', 'ce-construction' ),
		'id'            => 'footer-1',
		'before_widget' => '<div class="ce-footer__widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="ce-footer__widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer - Columna 2', 'ce-construction' ),
		'id'            => 'footer-2',
		'before_widget' => '<div class="ce-footer__widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="ce-footer__widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'ce_construction_widgets_init' );

/**
 * Extiende el resumen de posts (blog) de forma segura.
 */
function ce_construction_excerpt_length( $length ) {
	return 22;
}
add_filter( 'excerpt_length', 'ce_construction_excerpt_length' );

function ce_construction_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'ce_construction_excerpt_more' );

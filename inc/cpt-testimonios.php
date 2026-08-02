<?php
/**
 * Custom Post Type: Testimonios.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_testimonio() {
	register_post_type( 'testimonio', array(
		'labels' => array(
			'name'          => __( 'Testimonios', 'ce-construction' ),
			'singular_name' => __( 'Testimonio', 'ce-construction' ),
			'add_new_item'  => __( 'Añadir Nuevo Testimonio', 'ce-construction' ),
			'edit_item'     => __( 'Editar Testimonio', 'ce-construction' ),
			'all_items'     => __( 'Todos los Testimonios', 'ce-construction' ),
			'menu_name'     => __( 'Testimonios', 'ce-construction' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'testimonios' ),
		'menu_icon'    => 'dashicons-format-quote',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_testimonio' );

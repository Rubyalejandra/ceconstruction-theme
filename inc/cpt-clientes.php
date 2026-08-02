<?php
/**
 * Custom Post Type: Clientes.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_cliente() {
	register_post_type( 'cliente', array(
		'labels' => array(
			'name'          => __( 'Clientes', 'ce-construction' ),
			'singular_name' => __( 'Cliente', 'ce-construction' ),
			'add_new_item'  => __( 'Añadir Cliente', 'ce-construction' ),
			'edit_item'     => __( 'Editar Cliente', 'ce-construction' ),
			'all_items'     => __( 'Todos los Clientes', 'ce-construction' ),
			'menu_name'     => __( 'Clientes', 'ce-construction' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'clientes' ),
		'menu_icon'    => 'dashicons-businessman',
		'supports'     => array( 'title', 'thumbnail' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_cliente' );

<?php
/**
 * Custom Post Type: Servicios.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_servicio() {
	$labels = array(
		'name'               => __( 'Servicios', 'ce-construction' ),
		'singular_name'      => __( 'Servicio', 'ce-construction' ),
		'add_new_item'       => __( 'Añadir Nuevo Servicio', 'ce-construction' ),
		'edit_item'          => __( 'Editar Servicio', 'ce-construction' ),
		'all_items'          => __( 'Todos los Servicios', 'ce-construction' ),
		'menu_name'          => __( 'Servicios', 'ce-construction' ),
	);

	register_post_type( 'servicio', array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'servicios' ),
		'menu_icon'     => 'dashicons-hammer',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'categoria_servicio', 'servicio', array(
		'labels'       => array(
			'name'          => __( 'Categorías de Servicio', 'ce-construction' ),
			'singular_name' => __( 'Categoría de Servicio', 'ce-construction' ),
		),
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'categoria-servicio' ),
	) );
}
add_action( 'init', 'ce_construction_register_cpt_servicio' );

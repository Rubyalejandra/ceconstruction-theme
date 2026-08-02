<?php
/**
 * Custom Post Type: Equipo.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_equipo() {
	register_post_type( 'miembro_equipo', array(
		'labels' => array(
			'name'          => __( 'Equipo', 'ce-construction' ),
			'singular_name' => __( 'Miembro del Equipo', 'ce-construction' ),
			'add_new_item'  => __( 'Añadir Miembro', 'ce-construction' ),
			'edit_item'     => __( 'Editar Miembro', 'ce-construction' ),
			'all_items'     => __( 'Todo el Equipo', 'ce-construction' ),
			'menu_name'     => __( 'Equipo', 'ce-construction' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'equipo' ),
		'menu_icon'    => 'dashicons-groups',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_equipo' );

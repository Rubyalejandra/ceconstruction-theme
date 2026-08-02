<?php
/**
 * Custom Post Type: Proyectos.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_proyecto() {
	$labels = array(
		'name'          => __( 'Proyectos', 'ce-construction' ),
		'singular_name' => __( 'Proyecto', 'ce-construction' ),
		'add_new_item'  => __( 'Añadir Nuevo Proyecto', 'ce-construction' ),
		'edit_item'     => __( 'Editar Proyecto', 'ce-construction' ),
		'all_items'     => __( 'Todos los Proyectos', 'ce-construction' ),
		'menu_name'     => __( 'Proyectos', 'ce-construction' ),
	);

	register_post_type( 'proyecto', array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'proyectos' ),
		'menu_icon'     => 'dashicons-building',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'categoria_proyecto', 'proyecto', array(
		'labels'       => array(
			'name'          => __( 'Categorías de Proyecto', 'ce-construction' ),
			'singular_name' => __( 'Categoría de Proyecto', 'ce-construction' ),
		),
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'categoria-proyecto' ),
	) );

	register_taxonomy( 'estado_proyecto', 'proyecto', array(
		'labels'       => array(
			'name'          => __( 'Estados de Proyecto', 'ce-construction' ),
			'singular_name' => __( 'Estado de Proyecto', 'ce-construction' ),
		),
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'estado-proyecto' ),
	) );
}
add_action( 'init', 'ce_construction_register_cpt_proyecto' );

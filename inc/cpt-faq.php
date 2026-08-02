<?php
/**
 * Custom Post Type: Preguntas Frecuentes (FAQ).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_register_cpt_faq() {
	register_post_type( 'ce_faq', array(
		'labels' => array(
			'name'          => __( 'Preguntas Frecuentes', 'ce-construction' ),
			'singular_name' => __( 'Pregunta Frecuente', 'ce-construction' ),
			'add_new_item'  => __( 'Añadir Pregunta', 'ce-construction' ),
			'edit_item'     => __( 'Editar Pregunta', 'ce-construction' ),
			'all_items'     => __( 'Todas las Preguntas', 'ce-construction' ),
			'menu_name'     => __( 'FAQ', 'ce-construction' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'faq' ),
		'menu_icon'    => 'dashicons-editor-help',
		'supports'     => array( 'title', 'editor' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_faq' );

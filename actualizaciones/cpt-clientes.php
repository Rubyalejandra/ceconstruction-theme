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
		// NOTA (Sprint 5, Fase 3): 'has_archive' se cambió de false a true.
		// Motivo: el Sprint 5 requiere `archive-clientes.php` como entregable
		// funcional; con has_archive=false no existe ninguna URL amigable
		// (/clientes/) que WordPress enrute a esa plantilla — la única forma
		// de alcanzarla habría sido la URL fea ?post_type=cliente, inaceptable
		// para una "plantilla profesional lista para producción" (objetivo
		// explícito del proyecto). Impacto: ahora existe una página de
		// archivo pública en /clientes/, coherente con el resto de CPTs
		// del tema (Servicios y Proyectos ya la tenían). No afecta ningún
		// dato ya guardado ni cambia el comportamiento de 'cliente' como
		// single (que ya funcionaba). Ver DECISIONS.md para el detalle.
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'clientes' ),
		'menu_icon'    => 'dashicons-businessman',
		'supports'     => array( 'title', 'thumbnail' ),
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'ce_construction_register_cpt_cliente' );
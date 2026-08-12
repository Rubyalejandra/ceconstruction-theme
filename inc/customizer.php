<?php
/**
 * Theme Customizer: logo, colores, tipografía, redes sociales,
 * datos de contacto, WhatsApp, horario, hero, CTA, footer, botones.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ce_construction_customize_register( $wp_customize ) {

	/* -----------------------------------------------------------
	 * 1. IDENTIDAD / COLORES
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_brand', array(
		'title'    => __( 'CE: Identidad y Colores', 'ce-construction' ),
		'priority' => 30,
	) );

	$color_fields = array(
		'ce_color_primary'   => array( '#0F2A43', __( 'Color Primario', 'ce-construction' ) ),
		'ce_color_secondary' => array( '#D98E29', __( 'Color Secundario', 'ce-construction' ) ),
		'ce_color_accent'    => array( '#1E6F5C', __( 'Color de Acento', 'ce-construction' ) ),
	);
	/*
	 * QA-011 (Sprint 8, Entregable 8.1 — corrección Media): estos 3
	 * ajustes declaraban 'transport' => 'postMessage' sin que exista
	 * ningún script en 'customize_preview_init' que escuche esos
	 * cambios y actualice el DOM del preview en vivo (ver
	 * assets/js/main.js — ningún módulo se engancha a wp.customize).
	 * Sin ese script, 'postMessage' no hace nada distinto de
	 * 'refresh': WordPress simplemente no tiene forma de aplicar el
	 * cambio sin recargar, así que el resultado real ya era un
	 * refresh silencioso. Se quita la clave para que el código
	 * refleje el comportamiento real (default 'refresh') en vez de
	 * prometer una vista previa instantánea que nunca ocurría.
	 * Ver DECISIONS.md D-042.
	 */
	foreach ( $color_fields as $id => $data ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $data[0],
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $data[1],
			'section' => 'ce_section_brand',
		) ) );
	}

	/* -----------------------------------------------------------
	 * 2. TIPOGRAFÍA
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_typography', array(
		'title'    => __( 'CE: Tipografía', 'ce-construction' ),
		'priority' => 31,
	) );

	$wp_customize->add_setting( 'ce_font_heading', array(
		'default'           => 'Poppins',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'ce_font_heading', array(
		'label'    => __( 'Fuente de Títulos', 'ce-construction' ),
		'section'  => 'ce_section_typography',
		'type'     => 'select',
		'choices'  => array(
			'Poppins'    => 'Poppins',
			'Montserrat' => 'Montserrat',
			'Raleway'    => 'Raleway',
		),
	) );

	$wp_customize->add_setting( 'ce_font_body', array(
		'default'           => 'Inter',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'ce_font_body', array(
		'label'   => __( 'Fuente de Texto', 'ce-construction' ),
		'section' => 'ce_section_typography',
		'type'    => 'select',
		'choices' => array(
			'Inter'    => 'Inter',
			'Roboto'   => 'Roboto',
			'Open Sans'=> 'Open Sans',
		),
	) );

	/* -----------------------------------------------------------
	 * 3. CONTACTO / WHATSAPP / HORARIO
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_contact', array(
		'title'    => __( 'CE: Contacto y Horario', 'ce-construction' ),
		'priority' => 32,
	) );

	$contact_fields = array(
		'ce_phone'           => array( 'text', __( 'Teléfono principal', 'ce-construction' ) ),
		'ce_whatsapp_number' => array( 'text', __( 'Número de WhatsApp (formato internacional, ej: 573001234567)', 'ce-construction' ) ),
		'ce_email'           => array( 'text', __( 'Correo de contacto', 'ce-construction' ) ),
		'ce_address'         => array( 'text', __( 'Dirección', 'ce-construction' ) ),
		'ce_schedule'        => array( 'textarea', __( 'Horario de atención', 'ce-construction' ) ),
		'ce_maps_embed_url'  => array( 'text', __( 'URL de Google Maps (embed src)', 'ce-construction' ) ),
	);
	foreach ( $contact_fields as $id => $data ) {
		$wp_customize->add_setting( $id, array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $data[1],
			'section' => 'ce_section_contact',
			'type'    => $data[0],
		) );
	}

	/* -----------------------------------------------------------
	 * 4. REDES SOCIALES
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_social', array(
		'title'    => __( 'CE: Redes Sociales', 'ce-construction' ),
		'priority' => 33,
	) );

	$social_fields = array( 'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok' );
	foreach ( $social_fields as $network ) {
		$id = 'ce_social_' . $network;
		$wp_customize->add_setting( $id, array(
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => ucfirst( $network ) . ' URL',
			'section' => 'ce_section_social',
			'type'    => 'url',
		) );
	}

	/* -----------------------------------------------------------
	 * 5. HERO
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_hero', array(
		'title'    => __( 'CE: Sección Hero', 'ce-construction' ),
		'priority' => 34,
	) );

	$wp_customize->add_setting( 'ce_hero_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'ce_hero_image', array(
		'label'    => __( 'Imagen de fondo del Hero', 'ce-construction' ),
		'section'  => 'ce_section_hero',
		'mime_type'=> 'image',
	) ) );

	$hero_text_fields = array(
		'ce_hero_title'    => __( 'Título principal', 'ce-construction' ),
		'ce_hero_subtitle' => __( 'Subtítulo', 'ce-construction' ),
		'ce_hero_btn1_text'=> __( 'Texto botón 1 (Cotización)', 'ce-construction' ),
		'ce_hero_btn1_url' => __( 'URL botón 1', 'ce-construction' ),
		'ce_hero_btn2_text'=> __( 'Texto botón 2 (Ver Proyectos)', 'ce-construction' ),
		'ce_hero_btn2_url' => __( 'URL botón 2', 'ce-construction' ),
	);
	foreach ( $hero_text_fields as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'ce_section_hero',
			'type'    => 'text',
		) );
	}

	/* -----------------------------------------------------------
	 * 6. CTA
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_cta', array(
		'title'    => __( 'CE: Sección CTA', 'ce-construction' ),
		'priority' => 35,
	) );
	$cta_fields = array(
		'ce_cta_title'    => __( 'Título CTA', 'ce-construction' ),
		'ce_cta_text'     => __( 'Texto CTA', 'ce-construction' ),
		'ce_cta_btn_text' => __( 'Texto del botón CTA', 'ce-construction' ),
		'ce_cta_btn_url'  => __( 'URL del botón CTA', 'ce-construction' ),
	);
	foreach ( $cta_fields as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'ce_section_cta',
			'type'    => 'text',
		) );
	}

	/* -----------------------------------------------------------
	 * 7. FOOTER
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_footer', array(
		'title'    => __( 'CE: Footer', 'ce-construction' ),
		'priority' => 36,
	) );
	$wp_customize->add_setting( 'ce_footer_about', array( 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'ce_footer_about', array(
		'label'   => __( 'Texto "Sobre nosotros" en footer', 'ce-construction' ),
		'section' => 'ce_section_footer',
		'type'    => 'textarea',
	) );
	$wp_customize->add_setting( 'ce_footer_copyright', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'ce_footer_copyright', array(
		'label'   => __( 'Texto de Copyright', 'ce-construction' ),
		'section' => 'ce_section_footer',
		'type'    => 'text',
	) );
}
add_action( 'customize_register', 'ce_construction_customize_register' );

/**
 * Inyecta las variables CSS del Customizer (colores/tipografía)
 * de forma dinámica, sobreescribiendo los defaults de main.css.
 */
function ce_construction_customizer_css() {
	$primary   = get_theme_mod( 'ce_color_primary', '#0F2A43' );
	$secondary = get_theme_mod( 'ce_color_secondary', '#D98E29' );
	$accent    = get_theme_mod( 'ce_color_accent', '#1E6F5C' );
	$heading_font = get_theme_mod( 'ce_font_heading', 'Poppins' );
	$body_font    = get_theme_mod( 'ce_font_body', 'Inter' );
	?>
	<style id="ce-customizer-vars">
		:root{
			--ce-color-primary: <?php echo esc_html( $primary ); ?>;
			--ce-color-secondary: <?php echo esc_html( $secondary ); ?>;
			--ce-color-accent: <?php echo esc_html( $accent ); ?>;
			--ce-font-heading: '<?php echo esc_html( $heading_font ); ?>', sans-serif;
			--ce-font-body: '<?php echo esc_html( $body_font ); ?>', sans-serif;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'ce_construction_customizer_css' );

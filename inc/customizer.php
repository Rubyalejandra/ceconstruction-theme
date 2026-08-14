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

/* =========================================================
 * SPRINT UX-1, ENTREGABLE UX-1.2 — Home Builder: panel de
 * administración (activar/desactivar y reordenar secciones).
 *
 * Contexto: el Entregable UX-1.1 (inc/home-builder.php) ya dejó
 * expuesto el filtro `ce_home_active_order` precisamente para que
 * este Entregable pudiera enganchar ahí la configuración guardada
 * por el administrador, sin tener que volver a tocar
 * inc/home-builder.php ni front-page.php. Este bloque cumple
 * exactamente eso: ninguno de esos dos archivos se modifica en
 * este Entregable.
 *
 * Persistencia: un único theme_mod (`ce_home_sections_order`) con
 * un array JSON `[ { "key": "hero", "enabled": true }, ... ]`, en
 * el mismo orden en que el administrador las dejó. Se eligió un
 * único theme_mod combinando orden + activo/inactivo (en vez de
 * un control por sección) para poder reordenar con drag&drop de
 * forma nativa en el Customizer — ver DECISIONS.md D-046.
 *
 * Todas las funciones de este bloque reutilizan exclusivamente
 * ce_construction_home_sections() y ce_construction_default_home_order()
 * (ya definidas en inc/home-builder.php, cargado antes de que
 * cualquier hook del Customizer se dispare) como única fuente de
 * verdad de qué secciones existen.
 * ========================================================= */

/**
 * Decodifica y normaliza el valor guardado del orden de secciones.
 *
 * Tolerante a datos corruptos/incompletos: cualquier clave no
 * reconocida en el registro se descarta; cualquier sección
 * registrada que falte en el JSON (p. ej. añadida después por un
 * filtro `ce_home_sections`) se añade al final, desactivada.
 *
 * @param string     $json     Valor guardado (JSON) o cadena vacía.
 * @param array|null $sections Registro ya resuelto (opcional, evita recalcular).
 * @return array<int,array{key:string,enabled:bool}>
 */
function ce_construction_decode_home_sections_order( $json, $sections = null ) {
	if ( null === $sections ) {
		$sections = ce_construction_home_sections();
	}

	$decoded = json_decode( (string) $json, true );
	$order   = array();
	$seen    = array();

	if ( is_array( $decoded ) ) {
		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['key'] ) || ! isset( $sections[ $item['key'] ] ) || isset( $seen[ $item['key'] ] ) ) {
				continue;
			}
			$seen[ $item['key'] ] = true;
			$order[]               = array(
				'key'     => $item['key'],
				'enabled' => ! empty( $item['enabled'] ),
			);
		}
	}

	// Secciones registradas que faltan en el JSON guardado (nuevas,
	// o primera vez que se abre el panel): se añaden al final,
	// desactivadas por defecto, para no alterar en silencio el Home
	// ya publicado por el administrador.
	foreach ( $sections as $key => $data ) {
		if ( ! isset( $seen[ $key ] ) ) {
			$order[] = array(
				'key'     => $key,
				'enabled' => false,
			);
		}
	}

	return $order;
}

/**
 * `sanitize_callback` del theme_mod `ce_home_sections_order`.
 * Reutiliza el decodificador anterior (misma tolerancia a datos
 * corruptos) y vuelve a codificar a JSON limpio.
 */
function ce_construction_sanitize_home_sections_order( $json ) {
	$order = ce_construction_decode_home_sections_order( $json );
	$clean = array();
	foreach ( $order as $item ) {
		$clean[] = array(
			'key'     => sanitize_key( $item['key'] ),
			'enabled' => (bool) $item['enabled'],
		);
	}
	return wp_json_encode( $clean );
}

/**
 * Valor por defecto del theme_mod `ce_home_sections_order` para
 * instalaciones sin configuración guardada todavía.
 *
 * Reproduce ce_construction_default_home_order() (las 10 secciones
 * ya activas antes del Home Builder) como "enabled: true", y añade
 * el resto de secciones registradas (Team/Clients/FAQ, sin
 * template-part todavía) como "enabled: false" — consistente con
 * el criterio ya documentado en inc/home-builder.php de no
 * activarlas hasta que su implementación exista (Sprint UX-2).
 */
function ce_construction_default_home_sections_order_json() {
	$sections      = ce_construction_home_sections();
	$default_order = ce_construction_default_home_order();
	$clean         = array();

	foreach ( $default_order as $key ) {
		if ( isset( $sections[ $key ] ) ) {
			$clean[] = array(
				'key'     => $key,
				'enabled' => true,
			);
		}
	}
	foreach ( $sections as $key => $data ) {
		if ( ! in_array( $key, $default_order, true ) ) {
			$clean[] = array(
				'key'     => $key,
				'enabled' => false,
			);
		}
	}

	return wp_json_encode( $clean );
}

/**
 * Enganche real al Home: filtra `ce_home_active_order` (ya
 * expuesto por ce_construction_get_active_home_order() desde
 * UX-1.1) para devolver, si existe, el orden guardado por el
 * administrador. Si no hay theme_mod guardado todavía (instalación
 * nueva, o panel nunca abierto), no se aplica ningún cambio: se
 * respeta el $default_order que ya traía el filtro (idéntico al
 * comportamiento de UX-1.1, cero regresión).
 */
function ce_construction_filter_home_active_order( $default_order ) {
	$raw = get_theme_mod( 'ce_home_sections_order', '' );
	if ( '' === $raw ) {
		return $default_order;
	}

	$order  = ce_construction_decode_home_sections_order( $raw );
	$active = array();
	foreach ( $order as $item ) {
		if ( ! empty( $item['enabled'] ) ) {
			$active[] = $item['key'];
		}
	}
	return $active;
}
add_filter( 'ce_home_active_order', 'ce_construction_filter_home_active_order' );

/**
 * Control custom del Customizer: lista de secciones con checkbox
 * (activar/desactivar) y asa de arrastre (reordenar). El reordenado
 * real (jQuery UI Sortable, ya incluido en WordPress core) y la
 * serialización a JSON del hidden input viven en
 * assets/js/admin-home-builder.js (encolado desde inc/enqueue.php,
 * único archivo válido de encolado de assets del proyecto — ver
 * TREE.md — de modo que inc/customizer.php define el control pero
 * no encola nada directamente, igual que ya ocurre con el resto
 * de assets del tema).
 *
 * Guardado en `class_exists()` porque WP_Customize_Control solo
 * está disponible cuando WordPress ya cargó el core del Customizer
 * (no en todas las requests de frontend).
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

	class CE_Customize_Home_Sections_Control extends WP_Customize_Control {

		public $type = 'ce_home_sections';

		public function render_content() {
			$sections = ce_construction_home_sections();
			$order    = ce_construction_decode_home_sections_order( $this->value(), $sections );
			?>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>

			<ul class="ce-home-builder-list">
				<?php foreach ( $order as $item ) :
					$key = $item['key'];
					if ( ! isset( $sections[ $key ] ) ) {
						continue;
					}
					$template_file = CE_THEME_DIR . '/' . $sections[ $key ]['template'] . '.php';
					$available     = file_exists( $template_file );
					?>
					<li class="ce-home-builder-item<?php echo $available ? '' : ' ce-home-builder-item--unavailable'; ?>" data-key="<?php echo esc_attr( $key ); ?>">
						<span class="ce-home-builder-item__handle" aria-hidden="true">⠿</span>
						<label>
							<input
								type="checkbox"
								class="ce-home-builder-item__enabled"
								<?php checked( ! empty( $item['enabled'] ) && $available ); ?>
								<?php disabled( ! $available ); ?>
							>
							<?php echo esc_html( $sections[ $key ]['label'] ); ?>
							<?php if ( ! $available ) : ?>
								<em class="ce-home-builder-item__note"><?php esc_html_e( '(próximamente)', 'ce-construction' ); ?></em>
							<?php endif; ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>

			<input type="hidden" class="ce-home-builder-value" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>">
			<?php
		}
	}
}

function ce_construction_customize_register( $wp_customize ) {

	/* -----------------------------------------------------------
	 * 0. HOME BUILDER (Sprint UX-1, Entregable UX-1.2)
	 * Activar/desactivar y reordenar las secciones del Home
	 * registradas en inc/home-builder.php. Ver bloque de funciones
	 * añadido al inicio de este archivo (decodificación, sanitize,
	 * default, filtro `ce_home_active_order`, control custom).
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_home_builder', array(
		'title'       => __( 'CE: Home Builder', 'ce-construction' ),
		'description' => __( 'Activa o desactiva cada sección del Home con la casilla, y arrástrala desde el ícono ⠿ para cambiar su orden. Publica para aplicar los cambios.', 'ce-construction' ),
		'priority'    => 29,
	) );

	$wp_customize->add_setting( 'ce_home_sections_order', array(
		'default'           => ce_construction_default_home_sections_order_json(),
		'sanitize_callback' => 'ce_construction_sanitize_home_sections_order',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( new CE_Customize_Home_Sections_Control( $wp_customize, 'ce_home_sections_order', array(
		'label'   => __( 'Secciones del Home', 'ce-construction' ),
		'section' => 'ce_section_home_builder',
	) ) );

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

	/* -----------------------------------------------------------
	 * 8. FORMULARIO DE COTIZACIÓN (Sprint UX-3, Entregable UX-3.1)
	 * Define el modo de todos los CTA "Solicitar Cotización" del
	 * tema (6 puntos, centralizados vía ce_get_quote_cta_url() en
	 * inc/helpers.php). Ver DECISIONS.md D-049.
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_quote_form', array(
		'title'       => __( 'CE: Formulario de Cotización', 'ce-construction' ),
		'description' => __( 'Define cómo se comportan todos los botones "Solicitar Cotización" del tema. El cambio se aplica automáticamente a los 6 puntos del sitio que enlazan a la cotización, sin tocar código.', 'ce-construction' ),
		'priority'    => 37,
	) );

	$wp_customize->add_setting( 'ce_quote_form_mode', array(
		'default'           => 'integrated',
		'sanitize_callback' => 'ce_construction_sanitize_quote_form_mode',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'ce_quote_form_mode', array(
		'label'   => __( 'Modo del formulario de cotización', 'ce-construction' ),
		'section' => 'ce_section_quote_form',
		'type'    => 'radio',
		'choices' => array(
			'integrated' => __( 'Integrado en el Home (sección normal)', 'ce-construction' ),
			'modal'      => __( 'Popup / Modal (próximamente — Entregable UX-3.2)', 'ce-construction' ),
			'disabled'   => __( 'Desactivado (oculta todos los botones de cotización del sitio)', 'ce-construction' ),
		),
	) );
}
add_action( 'customize_register', 'ce_construction_customize_register' );

/* =========================================================
 * SPRINT UX-3, ENTREGABLE UX-3.1 (continuación) — sanitize_callback
 * del theme_mod `ce_quote_form_mode`. Definida fuera de la función
 * de registro (igual que el resto de sanitize_callbacks nombrados
 * del proyecto), para poder referenciarla por nombre desde
 * add_setting() sin depender del orden de ejecución dentro de la
 * propia función.
 * ========================================================= */
function ce_construction_sanitize_quote_form_mode( $value ) {
	$allowed = array( 'integrated', 'modal', 'disabled' );
	return in_array( $value, $allowed, true ) ? $value : 'integrated';
}

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

/* =========================================================
 * SPRINT UX-1, ENTREGABLE UX-1.2 (continuación).
 * Estilos inline mínimos del control custom "Home Builder",
 * impresos únicamente dentro del admin del Customizer (nunca en
 * el frontend). Mismo patrón que ce_construction_customizer_css()
 * de arriba (imprimir un <style> propio en vez de crear un archivo
 * .css dedicado para unas pocas reglas de un único control admin).
 * ========================================================= */
function ce_construction_home_builder_control_styles() {
	?>
	<style id="ce-home-builder-control-styles">
		.ce-home-builder-list {
			margin: 8px 0 4px;
			padding: 0;
			list-style: none;
		}
		.ce-home-builder-item {
			display: flex;
			align-items: center;
			gap: 8px;
			padding: 6px 8px;
			margin-bottom: 4px;
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 3px;
		}
		.ce-home-builder-item--unavailable {
			opacity: 0.55;
		}
		.ce-home-builder-item__handle {
			cursor: grab;
			color: #787c82;
		}
		.ce-home-builder-item__note {
			font-style: italic;
			color: #787c82;
			margin-left: 4px;
		}
		.ce-home-builder-list .ui-sortable-helper {
			box-shadow: 0 2px 6px rgba( 0, 0, 0, .15 );
		}
	</style>
	<?php
}
add_action( 'customize_controls_print_styles', 'ce_construction_home_builder_control_styles' );

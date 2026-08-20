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

	/* ---------------------------------------------------------
	 * Sprint UX-4, Entregable UX-4.1: tipo de fondo del Hero
	 * (imagen/video) + overlay configurable. Aditivo dentro de la
	 * misma sección 'ce_section_hero' ya existente — el modo
	 * "slider" (UX-4.2) queda fuera de este Entregable, ver
	 * DECISIONS.md D-054.
	 * --------------------------------------------------------- */
	$wp_customize->add_setting( 'ce_hero_type', array(
		'default'           => 'image',
		'sanitize_callback' => 'ce_construction_sanitize_hero_type',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_hero_type', array(
		'label'       => __( 'Tipo de fondo del Hero', 'ce-construction' ),
		'description' => __( 'En modo Video, si no se sube ningún video (o el navegador no puede reproducirlo), el Hero usa automáticamente la imagen de fondo configurada abajo como respaldo. En modo Slider, si no se selecciona ninguna imagen, ocurre lo mismo. Sprint UX-7: en modo Video o Slider, este ajuste también se aplica al Hero interno de Páginas/Servicios/Proyectos/etc.; en modo Imagen (por defecto), el Hero interno sigue usando la imagen destacada propia de cada Página/entrada.', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'type'        => 'select',
		'choices'     => array(
			'image'  => __( 'Imagen (por defecto)', 'ce-construction' ),
			'video'  => __( 'Video', 'ce-construction' ),
			'slider' => __( 'Slider (varias imágenes)', 'ce-construction' ),
		),
		'priority'    => 5,
	) );

	$wp_customize->add_setting( 'ce_hero_video', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'ce_hero_video', array(
		'label'       => __( 'Video de fondo del Hero (solo si el tipo es "Video")', 'ce-construction' ),
		'description' => __( 'Se reproduce en bucle, silenciado y sin controles. Usa un archivo liviano (recomendado: menos de 10MB, MP4 H.264).', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'mime_type'   => 'video',
		'priority'    => 6,
	) ) );

	/* ---------------------------------------------------------
	 * Sprint UX-4, Entregable UX-4.2: imágenes del slider del Hero
	 * (solo aplica si el tipo de fondo es "Slider"). Ver
	 * DECISIONS.md D-055.
	 * --------------------------------------------------------- */
	$wp_customize->add_setting( 'ce_hero_slides', array(
		'default'           => '',
		'sanitize_callback' => 'ce_construction_sanitize_hero_slides',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new CE_Customize_Hero_Slides_Control( $wp_customize, 'ce_hero_slides', array(
		'label'       => __( 'Imágenes del slider del Hero (solo si el tipo es "Slider")', 'ce-construction' ),
		'description' => __( 'Añade una o varias imágenes; se reproducen en bucle automático, igual que el slider de Testimonios. Usa las flechas para reordenar y la "×" para quitar una imagen.', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'priority'    => 7,
	) ) );

	$wp_customize->add_setting( 'ce_hero_overlay_opacity', array(
		'default'           => '1',
		'sanitize_callback' => 'ce_construction_sanitize_hero_overlay_opacity',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_hero_overlay_opacity', array(
		'label'       => __( 'Intensidad del overlay oscuro', 'ce-construction' ),
		'description' => __( 'De 0 (sin overlay, fondo a la vista) a 1 (overlay al 100%, como hasta ahora). Por defecto: 1 — mismo aspecto que antes de este Entregable. Sprint UX-7: este ajuste ahora también controla el overlay del Hero interno de Páginas/Servicios/Proyectos/etc. (antes fijo, sin control).', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 0,
			'max'  => 1,
			'step' => 0.05,
		),
		'priority'    => 8,
	) );

	/* ---------------------------------------------------------
	 * 🆕 Sprint UX-7, Entregable UX-7.2: layout de columnas del
	 * Hero de Home + slot opcional de Quote Form embebido. Ver
	 * DECISIONS.md D-064. Aditivo dentro de la misma sección
	 * 'ce_section_hero' ya existente. Solo aplica al Hero de Home
	 * (template-parts/hero.php) — el Hero interno
	 * (template-parts/page-hero.php, unificado en UX-7.1) sigue de
	 * una sola columna, sin este control (decisión explícita del
	 * usuario para este Entregable).
	 * --------------------------------------------------------- */
	$wp_customize->add_setting( 'ce_hero_layout', array(
		'default'           => '1',
		'sanitize_callback' => 'ce_construction_sanitize_hero_layout',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_hero_layout', array(
		'label'       => __( 'Layout de columnas del Hero (solo Home)', 'ce-construction' ),
		'description' => __( 'Proporción de ancho entre el contenido (título/subtítulo/botones) y el Formulario de Cotización, cuando este último está activado abajo. Sin el formulario activado, el contenido siempre ocupa el ancho completo, sin importar esta opción.', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'type'        => 'select',
		'choices'     => array(
			'1' => __( 'Una columna (ancho completo)', 'ce-construction' ),
			'2' => __( 'Dos columnas — contenido 7 / formulario 5', 'ce-construction' ),
			'3' => __( 'Dos columnas — contenido 8 / formulario 4', 'ce-construction' ),
		),
		'priority'    => 9,
	) );

	$wp_customize->add_setting( 'ce_hero_show_quote_form', array(
		'default'           => false,
		'sanitize_callback' => 'ce_construction_sanitize_checkbox',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_hero_show_quote_form', array(
		'label'       => __( 'Mostrar el Formulario de Cotización dentro del Hero', 'ce-construction' ),
		'description' => __( 'Combinable con cualquier layout de arriba. Con layout "Una columna", el formulario aparece debajo del contenido (apilado); con "Dos columnas", aparece al lado. Reutiliza el mismo formulario/handler de siempre (mismo nonce, misma validación) — respeta el modo configurado en "CE: Formulario de Cotización" (si es "Desactivado" o "Solo Popup", este formulario embebido tampoco se muestra).', 'ce-construction' ),
		'section'     => 'ce_section_hero',
		'type'        => 'checkbox',
		'priority'    => 10,
	) );

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
	 * 6 ter. CTA — icono y color de botón configurables
	 * (Sprint UX-7, Entregable UX-7.4. Ver DECISIONS.md D-068.)
	 *
	 * `ce_cta_icon`/`ce_cta_btn_color` comparten el mismo prefijo
	 * 'ce_cta_' que los 4 campos de texto de arriba — mismo criterio
	 * ya aplicado por decisión explícita del usuario en UX-7.3
	 * (D-067): la variante 'sidebar' de template-parts/cta.php
	 * reutiliza estos campos, no se crea un tercer juego de
	 * theme_mods. El color, por eso, también sobrescribe el botón
	 * del slot de CTA en los sidebars (ver
	 * ce_construction_customizer_css() más abajo); el icono NO —
	 * la variante 'sidebar' no imprime icono en su botón (diseño
	 * compacto de UX-7.3 sin cambios, ver template-parts/cta.php).
	 * --------------------------------------------------------- */
	$ce_cta_icon_choices = array(
		'fa-solid fa-paper-plane'   => __( 'Avión de papel (envío) — por defecto', 'ce-construction' ),
		'fa-solid fa-arrow-right'   => __( 'Flecha derecha', 'ce-construction' ),
		'fa-solid fa-phone'         => __( 'Teléfono', 'ce-construction' ),
		'fa-solid fa-envelope'      => __( 'Sobre / Email', 'ce-construction' ),
		'fa-solid fa-calendar'      => __( 'Calendario', 'ce-construction' ),
		'fa-solid fa-circle-check'  => __( 'Check en círculo', 'ce-construction' ),
		'fa-solid fa-bolt'          => __( 'Rayo', 'ce-construction' ),
		'fa-solid fa-handshake'     => __( 'Apretón de manos', 'ce-construction' ),
		'fa-solid fa-headset'       => __( 'Auriculares (soporte)', 'ce-construction' ),
		'fa-solid fa-shield-halved' => __( 'Escudo (confianza)', 'ce-construction' ),
	);

	$wp_customize->add_setting( 'ce_cta_icon', array(
		'default'           => 'fa-solid fa-paper-plane',
		'sanitize_callback' => 'ce_construction_sanitize_cta_icon',
	) );
	$wp_customize->add_control( 'ce_cta_icon', array(
		'label'       => __( 'Icono del botón CTA', 'ce-construction' ),
		'description' => __( 'Solo afecta al botón de la sección CTA de ancho completo (Home/páginas/archivos). El slot de CTA en los sidebars de Servicios/Proyectos (UX-7.3) no muestra icono en su botón — diseño compacto sin cambios.', 'ce-construction' ),
		'section'     => 'ce_section_cta',
		'type'        => 'select',
		'choices'     => $ce_cta_icon_choices,
	) );

	$wp_customize->add_setting( 'ce_cta_btn_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ce_cta_btn_color', array(
		'label'       => __( 'Color del botón CTA (incluye el slot de CTA en sidebars)', 'ce-construction' ),
		'description' => __( 'Sobrescribe el color de fondo del botón "Solicitar Cotización" en la sección CTA y, por compartir el mismo campo (ver DECISIONS.md D-067), también en el slot de CTA de los sidebars de Servicios/Proyectos si está activado. El color al pasar el cursor se calcula automáticamente (más oscuro). Vacío = usa el color secundario de marca definido en "CE: Colores" (comportamiento actual, sin cambio).', 'ce-construction' ),
		'section'     => 'ce_section_cta',
	) ) );

	/* -----------------------------------------------------------
	 * 6 bis. CTA SECUNDARIO (Sprint UX-5, Entregable UX-5.1)
	 * Contenido independiente para la sección 'cta_secondary' del
	 * Home Builder — reutiliza el mismo template-parts/cta.php que
	 * la sección CTA primaria de arriba (ver inc/home-builder.php),
	 * mismo patrón exacto de 4 campos de texto. Permite construir,
	 * solo con el Home Builder, recorridos con más de un punto de
	 * CTA (Estrategias A/B del brief) sin tocar código. Ver
	 * DECISIONS.md D-056.
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_cta_secondary', array(
		'title'       => __( 'CE: CTA Secundario', 'ce-construction' ),
		'description' => __( 'Contenido de la sección "CTA Secundario" del Home Builder — el mismo componente visual que "CE: Sección CTA", con su propio texto y botón independientes. Actívala y posiciónala desde el panel "CE: Home Builder".', 'ce-construction' ),
		'priority'    => 35,
	) );
	$cta2_fields = array(
		'ce_cta2_title'    => __( 'Título CTA Secundario', 'ce-construction' ),
		'ce_cta2_text'     => __( 'Texto CTA Secundario', 'ce-construction' ),
		'ce_cta2_btn_text' => __( 'Texto del botón CTA Secundario', 'ce-construction' ),
		'ce_cta2_btn_url'  => __( 'URL del botón CTA Secundario', 'ce-construction' ),
	);
	foreach ( $cta2_fields as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'ce_section_cta_secondary',
			'type'    => 'text',
		) );
	}

	/* -----------------------------------------------------------
	 * 6 quater. CTA Secundario — icono y color de botón
	 * (Sprint UX-7, Entregable UX-7.4. Ver DECISIONS.md D-068.)
	 * Mismo mecanismo que 'ce_cta_icon'/'ce_cta_btn_color' de
	 * arriba, con su propio prefijo 'ce_cta2_' independiente — el
	 * CTA Secundario ya tenía su propio juego de theme_mods desde
	 * UX-5.1 (D-056), y este Entregable respeta ese mismo criterio.
	 * No hay sidebar asociado a este prefijo (la variante 'sidebar'
	 * de cta.php solo reutiliza 'ce_cta_', no 'ce_cta2_' — ver
	 * DECISIONS.md D-067).
	 * --------------------------------------------------------- */
	$wp_customize->add_setting( 'ce_cta2_icon', array(
		'default'           => 'fa-solid fa-paper-plane',
		'sanitize_callback' => 'ce_construction_sanitize_cta_icon',
	) );
	$wp_customize->add_control( 'ce_cta2_icon', array(
		'label'       => __( 'Icono del botón CTA Secundario', 'ce-construction' ),
		'section'     => 'ce_section_cta_secondary',
		'type'        => 'select',
		'choices'     => $ce_cta_icon_choices,
	) );

	$wp_customize->add_setting( 'ce_cta2_btn_color', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'ce_cta2_btn_color', array(
		'label'       => __( 'Color del botón CTA Secundario', 'ce-construction' ),
		'description' => __( 'Igual que el color del CTA principal, pero independiente. El color al pasar el cursor se calcula automáticamente (más oscuro). Vacío = usa el color secundario de marca definido en "CE: Colores" (comportamiento actual, sin cambio).', 'ce-construction' ),
		'section'     => 'ce_section_cta_secondary',
	) ) );

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
		'description' => __( 'Define el comportamiento del formulario de cotización. Desde el Entregable UX-3.2, los botones "Solicitar Cotización" del sitio abren siempre el popup (salvo en modo Desactivado); la diferencia entre modos está en si además se muestra el formulario integrado en el Home/Servicios/Proyectos.', 'ce-construction' ),
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
			// 🆕 Sprint UX-3, Entregable UX-3.2 (D-053): etiquetas
			// actualizadas — ambos modos abren el popup desde
			// cualquier CTA del sitio; la diferencia ya no está en
			// el destino del CTA, sino en si también se muestra el
			// formulario integrado allí donde ya existía.
			'integrated' => __( 'Integrado + Popup (se muestra el formulario en el Home/Servicios/Proyectos, y los botones también abren el popup)', 'ce-construction' ),
			'modal'      => __( 'Solo Popup (los botones abren el popup; no se muestra el formulario integrado en ninguna página)', 'ce-construction' ),
			'disabled'   => __( 'Desactivado (oculta todos los botones de cotización y el popup)', 'ce-construction' ),
		),
	) );

	/* -----------------------------------------------------------
	 * 🆕 Sprint UX-7, Entregable UX-7.3 ("Aprovechamiento de
	 * espacios vacíos en sidebars"): slot opcional al final de
	 * template-parts/sidebar-servicios.php y
	 * sidebar-proyectos.php, independiente por sidebar. Ver
	 * DECISIONS.md D-067.
	 * --------------------------------------------------------- */
	$wp_customize->add_section( 'ce_section_sidebars', array(
		'title'       => __( 'CE: Sidebars (Servicios/Proyectos)', 'ce-construction' ),
		'description' => __( 'Slot opcional al final de cada sidebar, debajo de la card de contacto fija. Desactivado por defecto — no cambia nada hasta que elijas una opción distinta de "Ninguno" en cada control.', 'ce-construction' ),
		'priority'    => 38,
	) );

	$ce_sidebar_slot_choices = array(
		'none'       => __( 'Ninguno (comportamiento actual, sin slot adicional)', 'ce-construction' ),
		'cta'        => __( 'CTA (reutiliza el CTA principal del Home — mismos textos/botón configurados en "CE: Sección CTA")', 'ce-construction' ),
		'testimonio' => __( 'Testimonio (uno al azar entre los ya publicados, con la misma card del Home)', 'ce-construction' ),
	);

	$wp_customize->add_setting( 'ce_sidebar_servicios_slot', array(
		'default'           => 'none',
		'sanitize_callback' => 'ce_construction_sanitize_sidebar_slot',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_sidebar_servicios_slot', array(
		'label'    => __( 'Slot del sidebar de Servicios', 'ce-construction' ),
		'section'  => 'ce_section_sidebars',
		'type'     => 'select',
		'choices'  => $ce_sidebar_slot_choices,
		'priority' => 10,
	) );

	$wp_customize->add_setting( 'ce_sidebar_proyectos_slot', array(
		'default'           => 'none',
		'sanitize_callback' => 'ce_construction_sanitize_sidebar_slot',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'ce_sidebar_proyectos_slot', array(
		'label'    => __( 'Slot del sidebar de Proyectos', 'ce-construction' ),
		'section'  => 'ce_section_sidebars',
		'type'     => 'select',
		'choices'  => $ce_sidebar_slot_choices,
		'priority' => 20,
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
 * Sprint UX-7, Entregable UX-7.3 (continuación) — sanitize_callback
 * compartido por 'ce_sidebar_servicios_slot' y
 * 'ce_sidebar_proyectos_slot' (mismas 3 opciones para ambos).
 */
function ce_construction_sanitize_sidebar_slot( $value ) {
	$allowed = array( 'none', 'cta', 'testimonio' );
	return in_array( $value, $allowed, true ) ? $value : 'none';
}

/**
 * Sprint UX-7, Entregable UX-7.4 — sanitize_callback compartido por
 * 'ce_cta_icon' y 'ce_cta2_icon'. Whitelist estricta (lista curada,
 * no input libre — decisión de alcance ya fijada en
 * UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4 "por consistencia visual"):
 * cualquier valor no reconocido cae al icono histórico del tema
 * (fa-paper-plane), sin regresión visual. Ver DECISIONS.md D-068.
 */
function ce_construction_sanitize_cta_icon( $value ) {
	$allowed = array(
		'fa-solid fa-paper-plane',
		'fa-solid fa-arrow-right',
		'fa-solid fa-phone',
		'fa-solid fa-envelope',
		'fa-solid fa-calendar',
		'fa-solid fa-circle-check',
		'fa-solid fa-bolt',
		'fa-solid fa-handshake',
		'fa-solid fa-headset',
		'fa-solid fa-shield-halved',
	);
	return in_array( $value, $allowed, true ) ? $value : 'fa-solid fa-paper-plane';
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

	/**
	 * 🆕 Sprint UX-7, Entregable UX-7.4 — color de botón del CTA,
	 * configurable e independiente por variante (primaria/
	 * secundaria). A diferencia de las variables de arriba (globales,
	 * siempre impresas), estos 2 overrides son deliberadamente
	 * CONDICIONALES y van en reglas con selector propio, NO en
	 * `:root` — no se toca `--ce-color-secondary` (usado por
	 * `.ce-btn--primary` en TODO el tema: header, hero, cards, etc.),
	 * así que ningún botón fuera del CTA cambia. `ce_cta_btn_color`
	 * también sobrescribe el botón del slot de CTA en los sidebars
	 * (`.ce-sidebar__contact-card`), porque esa variante reutiliza el
	 * mismo theme_mod por decisión ya tomada en UX-7.3 (D-067). El
	 * estado ':hover' se deriva automáticamente con
	 * `ce_construction_hex_darken()` (inc/helpers.php) — el
	 * administrador solo elige un color, no dos. Ver DECISIONS.md
	 * D-068.
	 */
	$cta_btn_color  = get_theme_mod( 'ce_cta_btn_color', '' );
	$cta2_btn_color = get_theme_mod( 'ce_cta2_btn_color', '' );
	?>
	<style id="ce-customizer-vars">
		:root{
			--ce-color-primary: <?php echo esc_html( $primary ); ?>;
			--ce-color-secondary: <?php echo esc_html( $secondary ); ?>;
			--ce-color-accent: <?php echo esc_html( $accent ); ?>;
			--ce-font-heading: '<?php echo esc_html( $heading_font ); ?>', sans-serif;
			--ce-font-body: '<?php echo esc_html( $body_font ); ?>', sans-serif;
		}
		<?php if ( $cta_btn_color ) : ?>
		#ce-cta .ce-btn--primary,
		.ce-sidebar__contact-card .ce-btn--primary {
			background: <?php echo esc_html( $cta_btn_color ); ?>;
		}
		#ce-cta .ce-btn--primary:hover,
		.ce-sidebar__contact-card .ce-btn--primary:hover {
			background: <?php echo esc_html( ce_construction_hex_darken( $cta_btn_color, 15 ) ); ?>;
		}
		<?php endif; ?>
		<?php if ( $cta2_btn_color ) : ?>
		#ce-cta-secondary .ce-btn--primary {
			background: <?php echo esc_html( $cta2_btn_color ); ?>;
		}
		#ce-cta-secondary .ce-btn--primary:hover {
			background: <?php echo esc_html( ce_construction_hex_darken( $cta2_btn_color, 15 ) ); ?>;
		}
		<?php endif; ?>
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

/* =========================================================
 * SPRINT UX-4, ENTREGABLE UX-4.1 — Hero configurable
 * (imagen/video + overlay). Ver DECISIONS.md D-054.
 * ========================================================= */

/**
 * `sanitize_callback` de `ce_hero_type`. Whitelist estricta —
 * cualquier valor no reconocido cae a 'image' (comportamiento
 * histórico del tema, sin regresión).
 *
 * 🆕 Sprint UX-4, Entregable UX-4.2: se añade 'slider' al whitelist
 * (antes solo 'image'/'video', ver DECISIONS.md D-054). Ampliación
 * de una única línea (el array `$allowed`) — la lógica de la
 * función no cambia.
 */
function ce_construction_sanitize_hero_type( $value ) {
	$allowed = array( 'image', 'video', 'slider' );
	return in_array( $value, $allowed, true ) ? $value : 'image';
}

/**
 * `sanitize_callback` de `ce_hero_overlay_opacity`. Acepta
 * cualquier valor numérico y lo acota estrictamente al rango
 * [0, 1] — protege el frontend de un valor fuera de rango llegado
 * por edición manual de la URL de la petición del Customizer
 * (los `input_attrs` min/max del control son solo una ayuda de UI
 * en el navegador, no una validación real de servidor).
 */
function ce_construction_sanitize_hero_overlay_opacity( $value ) {
	$value = (float) $value;
	if ( $value < 0 ) {
		$value = 0;
	} elseif ( $value > 1 ) {
		$value = 1;
	}
	return (string) $value;
}

/* =========================================================
 * SPRINT UX-7, ENTREGABLE UX-7.2 — Layout de columnas del Hero de
 * Home + Quote Form embebido. Ver DECISIONS.md D-064.
 * ========================================================= */

/**
 * `sanitize_callback` de `ce_hero_layout`. Whitelist estricta —
 * cualquier valor no reconocido cae a '1' (una columna, ancho
 * completo: comportamiento histórico del Hero, sin regresión),
 * mismo criterio ya usado por `ce_construction_sanitize_hero_type()`.
 */
function ce_construction_sanitize_hero_layout( $value ) {
	$allowed = array( '1', '2', '3' );
	return in_array( $value, $allowed, true ) ? $value : '1';
}

/**
 * `sanitize_callback` genérico para controles `type => 'checkbox'`
 * del Customizer. WordPress no incluye uno nativo (a diferencia de
 * `sanitize_hex_color`/`absint`/etc., ya usados en este archivo) —
 * un checkbox sin marcar no envía el campo en el POST, así que
 * cualquier valor recibido (normalmente '1') debe tratarse como
 * `true`; su ausencia ya la resuelve `get_theme_mod()` devolviendo
 * el `default` (`false`) declarado en `add_setting()`. Reutilizable
 * por cualquier futuro control de tipo checkbox del tema, no solo
 * `ce_hero_show_quote_form`.
 */
function ce_construction_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/* =========================================================
 * SPRINT UX-4, ENTREGABLE UX-4.2 — Hero configurable: modo slider.
 * Ver DECISIONS.md D-055.
 * ========================================================= */

/**
 * `sanitize_callback` de `ce_hero_slides`. Reutiliza
 * `ce_get_hero_slide_ids()` (`inc/helpers.php`) para parsear y
 * filtrar la cadena entrante a solo IDs de adjunto válidos (> 0),
 * y la vuelve a serializar como cadena separada por comas — mismo
 * criterio de saneamiento que ya usa `ce_construction_save_meta_boxes()`
 * para `_ce_proyecto_galeria` (`inc/meta-boxes.php`), aplicado aquí
 * a un theme_mod en vez de a post meta.
 */
function ce_construction_sanitize_hero_slides( $value ) {
	$ids = ce_get_hero_slide_ids( is_string( $value ) ? $value : '' );
	return implode( ',', $ids );
}

/**
 * Control custom del Customizer: selector múltiple de imágenes para
 * el slider del Hero, con miniaturas, botón "Añadir imágenes" (
 * `wp.media` en modo `multiple: true` — mismo mecanismo ya usado en
 * `inc/meta-boxes.php::ce_render_proyecto_gallery()` para la
 * galería de Proyecto, adaptado aquí de metabox a control de
 * Customizer) y reordenamiento mediante botones "mover antes/después"
 * por miniatura (sin jQuery UI Sortable — ver DECISIONS.md D-055
 * para la justificación de esta elección frente al patrón de
 * arrastre de `CE_Customize_Home_Sections_Control`).
 *
 * El JS real (añadir/quitar/reordenar + serialización del hidden
 * input) vive en assets/js/admin-hero-slides.js (encolado desde
 * inc/enqueue.php, mismo criterio que assets/js/admin-home-builder.js
 * — inc/customizer.php define el control, no encola nada
 * directamente).
 *
 * Guardado en `class_exists()` por el mismo motivo que
 * `CE_Customize_Home_Sections_Control`: `WP_Customize_Control` solo
 * está disponible cuando WordPress ya cargó el core del Customizer.
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

	class CE_Customize_Hero_Slides_Control extends WP_Customize_Control {

		public $type = 'ce_hero_slides';

		public function render_content() {
			$ids = ce_get_hero_slide_ids( $this->value() );
			?>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>

			<ul class="ce-hero-slides-list">
				<?php
				foreach ( $ids as $id ) :
					$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
					if ( ! $thumb ) {
						continue; // Adjunto borrado desde entonces: se omite del preview, no del guardado (lo purga el propio guardado en el siguiente 'Publicar').
					}
					?>
					<li class="ce-hero-slides-item" data-id="<?php echo esc_attr( $id ); ?>">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="">
						<button type="button" class="ce-hero-slides-item__up" aria-label="<?php esc_attr_e( 'Mover antes', 'ce-construction' ); ?>">&uarr;</button>
						<button type="button" class="ce-hero-slides-item__down" aria-label="<?php esc_attr_e( 'Mover después', 'ce-construction' ); ?>">&darr;</button>
						<button type="button" class="ce-hero-slides-item__remove" aria-label="<?php esc_attr_e( 'Quitar', 'ce-construction' ); ?>">&times;</button>
					</li>
				<?php endforeach; ?>
			</ul>

			<button type="button" class="button ce-hero-slides-add"><?php esc_html_e( 'Añadir imágenes', 'ce-construction' ); ?></button>

			<input type="hidden" class="ce-hero-slides-value" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>">
			<?php
		}
	}
}

/**
 * Estilos del control custom `ce_hero_slides` dentro del admin del
 * Customizer. Mismo criterio que
 * ce_construction_home_builder_control_styles() (arriba): función
 * separada, mismo hook (`customize_controls_print_styles`), `<style>`
 * con id propio — no se añade nada a la función ya existente.
 */
function ce_construction_hero_slides_control_styles() {
	?>
	<style id="ce-hero-slides-control-styles">
		.ce-hero-slides-list {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			margin: 8px 0 10px;
			padding: 0;
			list-style: none;
		}
		.ce-hero-slides-item {
			position: relative;
			width: 72px;
			border: 1px solid #dcdcde;
			border-radius: 3px;
			overflow: hidden;
			background: #fff;
		}
		.ce-hero-slides-item img {
			display: block;
			width: 100%;
			height: 54px;
			object-fit: cover;
		}
		.ce-hero-slides-item__up,
		.ce-hero-slides-item__down,
		.ce-hero-slides-item__remove {
			position: absolute;
			top: 2px;
			width: 18px;
			height: 18px;
			line-height: 16px;
			padding: 0;
			font-size: 11px;
			text-align: center;
			background: rgba(255,255,255,.92);
			border: 1px solid #dcdcde;
			border-radius: 2px;
			cursor: pointer;
		}
		.ce-hero-slides-item__up { left: 2px; }
		.ce-hero-slides-item__down { left: 22px; }
		.ce-hero-slides-item__remove { right: 2px; color: #b32d2e; }
	</style>
	<?php
}
add_action( 'customize_controls_print_styles', 'ce_construction_hero_slides_control_styles' );

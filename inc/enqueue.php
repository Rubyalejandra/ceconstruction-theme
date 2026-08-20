<?php
/**
 * Carga de estilos y scripts del tema.
 * Performance: se usan defer/async donde aplica y se registra
 * solo lo necesario por contexto.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * QA-030 (Sprint 8, Entregable 8.2 — corrección alta).
 *
 * Causa raíz: los 3 wp_enqueue_style()/wp_enqueue_script() de
 * los assets propios del tema (style.css, assets/css/main.css,
 * assets/js/main.js) usaban CE_THEME_VERSION como parámetro
 * $ver — una constante que dependía de que un desarrollador la
 * actualizara "a mano" en cada despliegue que tocara CSS/JS.
 * Quedó congelada en '0.4.1' desde el Sprint 5 pese a que el
 * proyecto avanzó hasta v0.8.0 (ver CHANGELOG.md): el cache de
 * navegador/CDN no tenía ninguna señal de que main.css/main.js
 * habían cambiado entre despliegues, por lo que visitantes
 * recurrentes podían seguir recibiendo CSS/JS desactualizado
 * indefinidamente.
 *
 * Esta función resuelve la causa raíz, no solo el síntoma:
 * usa filemtime() del archivo real en disco como versión, de
 * forma que la URL de cache-busting cambia automáticamente
 * CADA VEZ que el archivo cambia, sin que nadie tenga que
 * recordar actualizar ninguna constante nunca más. Es el
 * mecanismo que la documentación de WordPress recomienda para
 * temas/plugins bajo desarrollo activo (ver Recomendación R-1
 * de docs/QA_REPORT.md).
 *
 * Fallback defensivo: si el archivo no existiera en disco (no
 * debería ocurrir en producción, pero evita un $ver vacío/falso
 * que rompería el atributo del <link>/<script>), se usa
 * CE_THEME_VERSION como respaldo.
 *
 * Ver DECISIONS.md D-044.
 * ========================================================= */
function ce_construction_asset_version( $relative_path ) {
	$file = CE_THEME_DIR . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}
	return CE_THEME_VERSION;
}

function ce_construction_enqueue_assets() {

	// Tipografía (Poppins + Inter).
	wp_enqueue_style(
		'ce-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);

	// Font Awesome (iconografía pedida en el brief).
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);

	// style.css raíz (requerido por WordPress).
	// QA-030: versión por filemtime() real, no por CE_THEME_VERSION.
	wp_enqueue_style( 'ce-construction-style', get_stylesheet_uri(), array(), ce_construction_asset_version( 'style.css' ) );

	// Hoja de estilos principal del tema (design system real).
	// QA-030: versión por filemtime() real, no por CE_THEME_VERSION.
	wp_enqueue_style(
		'ce-construction-main',
		CE_THEME_URI . '/assets/css/main.css',
		array( 'ce-construction-style' ),
		ce_construction_asset_version( 'assets/css/main.css' )
	);

	// JS principal del tema (ES6 modular), cargado en el footer.
	// QA-030: versión por filemtime() real, no por CE_THEME_VERSION.
	wp_enqueue_script(
		'ce-construction-main',
		CE_THEME_URI . '/assets/js/main.js',
		array(),
		ce_construction_asset_version( 'assets/js/main.js' ),
		true
	);
	wp_script_add_data( 'ce-construction-main', 'defer', true );

	// Variables PHP -> JS (ajax url, nonce del formulario, textos).
	wp_localize_script( 'ce-construction-main', 'ceConstructionData', array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'quoteNonce'   => wp_create_nonce( 'ce_quote_form_action' ),
		'whatsapp'     => get_theme_mod( 'ce_whatsapp_number', '' ),
		'i18n'         => array(
			'sending' => __( 'Enviando...', 'ce-construction' ),
			'error'   => __( 'Ocurrió un error. Intenta nuevamente.', 'ce-construction' ),
		),
	) );

	// Soporte para comentarios anidados en single de blog.
	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ce_construction_enqueue_assets' );

/* =========================================================
 * QA-010 (Sprint 8, Entregable 8.1 — corrección Media).
 * Antes existía, además de wp_script_add_data('defer', true)
 * (arriba, soporte nativo de WordPress desde la versión 6.3),
 * un filtro manual `ce_construction_add_defer_attribute()` sobre
 * 'script_loader_tag' que hacía exactamente lo mismo vía
 * str_replace() sobre el <script> ya impreso. Con el mínimo de
 * WordPress del proyecto (>= 6.0, ver style.css) ya cubierto por
 * versiones reales en producción >= 6.3, y el proyecto apuntando
 * a compatibilidad con WordPress 7.x, el soporte nativo es
 * suficiente y el filtro manual es código muerto/redundante que
 * solo añadía una segunda vía (potencialmente conflictiva si un
 * plugin de terceros también engancha 'script_loader_tag' para
 * el mismo handle) de lograr el mismo resultado. Se elimina el
 * filtro; wp_script_add_data() sigue intacto y es quien realmente
 * añade `defer` al <script src="main.js">.
 * Ver DECISIONS.md D-042.
 * ========================================================= */

/* =========================================================
 * SPRINT UX-1, ENTREGABLE UX-1.2 — Home Builder: script del
 * control custom del Customizer (activar/desactivar + reordenar
 * secciones del Home, ver inc/customizer.php).
 *
 * Se encola aquí y no en inc/customizer.php porque este archivo
 * es "el único archivo válido de encolado de assets" del proyecto
 * (ver docs/TREE.md) — inc/customizer.php define el control, este
 * archivo se limita a encolar su JS, igual que ya hace con los 3
 * assets propios del tema para el frontend.
 *
 * Encolado exclusivamente en `customize_controls_enqueue_scripts`
 * (admin del Customizer): nunca se carga en el frontend público.
 * Dependencias: 'jquery-ui-sortable' ya viene incluido en el core
 * de WordPress (mismo mecanismo que usa la pantalla nativa de
 * Widgets) — no se añade ninguna librería nueva al proyecto.
 * Versión vía ce_construction_asset_version() (arriba, QA-030):
 * el nuevo asset admin queda cubierto por el mismo mecanismo de
 * cache-busting automático que ya protege a los assets del tema.
 *
 * Ver DECISIONS.md D-046.
 * ========================================================= */
function ce_construction_enqueue_home_builder_control_script() {
	wp_enqueue_script(
		'ce-admin-home-builder',
		CE_THEME_URI . '/assets/js/admin-home-builder.js',
		array( 'jquery', 'jquery-ui-sortable', 'customize-controls' ),
		ce_construction_asset_version( 'assets/js/admin-home-builder.js' ),
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'ce_construction_enqueue_home_builder_control_script' );

/* =========================================================
 * SPRINT UX-4, ENTREGABLE UX-4.2 — Hero configurable (modo slider):
 * script del control custom `CE_Customize_Hero_Slides_Control`
 * (ver inc/customizer.php). Mismo criterio de ubicación/hook que
 * ce_construction_enqueue_home_builder_control_script() (arriba):
 * este archivo es "el único archivo válido de encolado de assets"
 * del proyecto (ver docs/TREE.md).
 *
 * Dependencias: 'media-editor' (núcleo de WordPress) garantiza que
 * `wp.media` esté disponible independientemente de si algún otro
 * control de Media (ce_hero_image/ce_hero_video, ya registrados en
 * esta misma sección) lo hubiera cargado ya de forma incidental —
 * no se añade ninguna librería nueva al proyecto. No se usa
 * 'jquery-ui-sortable': el reordenamiento de este control es por
 * botones "mover antes/después", no por arrastre — ver
 * DECISIONS.md D-055.
 *
 * Localización: los textos de la ventana de `wp.media` (título,
 * botón) se pasan vía wp_localize_script(), mismo patrón ya usado
 * en inc/enqueue.php para `ceConstructionData` del frontend —
 * ningún string se hardcodea en el JS, todo pasa por __()/_x() en
 * PHP para que el catálogo de traducción del tema lo cubra.
 *
 * Ver DECISIONS.md D-055.
 * ========================================================= */
function ce_construction_enqueue_hero_slides_control_script() {
	wp_enqueue_script(
		'ce-admin-hero-slides',
		CE_THEME_URI . '/assets/js/admin-hero-slides.js',
		array( 'jquery', 'media-editor', 'customize-controls' ),
		ce_construction_asset_version( 'assets/js/admin-hero-slides.js' ),
		true
	);

	wp_localize_script( 'ce-admin-hero-slides', 'ceHeroSlidesData', array(
		'mediaTitle'  => __( 'Selecciona imágenes para el slider del Hero', 'ce-construction' ),
		'mediaButton' => __( 'Añadir al slider', 'ce-construction' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'ce_construction_enqueue_hero_slides_control_script' );

/* =========================================================
 * SPRINT UX-7, ENTREGABLE UX-7.6 — Estadísticas configurables:
 * script del control custom `CE_Customize_Stats_Items_Control`
 * (ver inc/customizer.php). Mismo criterio de ubicación/hook que
 * ce_construction_enqueue_hero_slides_control_script() (arriba):
 * este archivo es "el único archivo válido de encolado de assets"
 * del proyecto (ver docs/TREE.md) — inc/customizer.php define el
 * control, este archivo se limita a encolar su JS.
 *
 * Sin dependencia de 'media-editor': el control no usa `wp.media`
 * (son campos de texto, no imágenes). Sin 'jquery-ui-sortable': el
 * reordenamiento es por botones "mover antes/después", mismo
 * criterio ya usado por admin-hero-slides.js (ver DECISIONS.md
 * D-055, aplicado aquí también — ver D-070).
 *
 * Localización: los textos de las etiquetas de cada fila nueva
 * (creada por JS al pulsar "Añadir estadística") se pasan vía
 * wp_localize_script(), mismo patrón ya usado para
 * `ceHeroSlidesData` arriba — ningún string se hardcodea en el JS.
 *
 * Ver DECISIONS.md D-070.
 * ========================================================= */
function ce_construction_enqueue_stats_items_control_script() {
	wp_enqueue_script(
		'ce-admin-stats-items',
		CE_THEME_URI . '/assets/js/admin-stats-items.js',
		array( 'jquery', 'customize-controls' ),
		ce_construction_asset_version( 'assets/js/admin-stats-items.js' ),
		true
	);

	wp_localize_script( 'ce-admin-stats-items', 'ceStatsItemsData', array(
		'labelCount'  => __( 'Número', 'ce-construction' ),
		'labelSuffix' => __( 'Sufijo', 'ce-construction' ),
		'labelLabel'  => __( 'Etiqueta', 'ce-construction' ),
		'labelIcon'   => __( 'Icono (Font Awesome, ej. fa-solid fa-building)', 'ce-construction' ),
		'labelRemove' => __( 'Quitar', 'ce-construction' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'ce_construction_enqueue_stats_items_control_script' );

/* =========================================================
 * SPRINT UX-7, ENTREGABLE UX-7.7 — Insignias de confianza: script
 * del control custom `CE_Customize_Trust_Badges_Control` (ver
 * inc/customizer.php). Mismo criterio de ubicación/hook que los 3
 * bloques anteriores de esta sección.
 *
 * Dependencia 'media-editor' (a diferencia de admin-stats-items.js,
 * pero igual que admin-hero-slides.js): cada fila admite una
 * imagen opcional vía `wp.media` — mismo mecanismo, sin añadir
 * ninguna librería nueva al proyecto. Sin 'jquery-ui-sortable': el
 * reordenamiento es por botones "mover antes/después" (D-055/D-070/
 * D-071).
 *
 * Localización: los textos de las etiquetas de cada fila nueva
 * (creada por JS al pulsar "Añadir insignia") se pasan vía
 * wp_localize_script(), mismo patrón ya usado para
 * `ceStatsItemsData`/`ceHeroSlidesData` arriba.
 *
 * Ver DECISIONS.md D-071.
 * ========================================================= */
function ce_construction_enqueue_trust_badges_control_script() {
	wp_enqueue_script(
		'ce-admin-trust-badges',
		CE_THEME_URI . '/assets/js/admin-trust-badges.js',
		array( 'jquery', 'media-editor', 'customize-controls' ),
		ce_construction_asset_version( 'assets/js/admin-trust-badges.js' ),
		true
	);

	wp_localize_script( 'ce-admin-trust-badges', 'ceTrustBadgesData', array(
		'mediaTitle'   => __( 'Selecciona una imagen para la insignia', 'ce-construction' ),
		'mediaButton'  => __( 'Usar esta imagen', 'ce-construction' ),
		'labelLabel'   => __( 'Etiqueta', 'ce-construction' ),
		'labelLicense' => __( 'Número de licencia (opcional)', 'ce-construction' ),
		'labelUrl'     => __( 'Enlace de verificación (opcional)', 'ce-construction' ),
		'labelSelect'  => __( 'Seleccionar imagen', 'ce-construction' ),
		'labelReplace' => __( 'Cambiar imagen', 'ce-construction' ),
		'labelRemoveImage' => __( 'Quitar imagen', 'ce-construction' ),
		'labelRemove'  => __( 'Quitar', 'ce-construction' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'ce_construction_enqueue_trust_badges_control_script' );

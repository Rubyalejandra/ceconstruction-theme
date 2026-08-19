<?php
/**
 * Front Page — CE Construction.
 *
 * Ensambla las secciones del Home. Cada sección vive en su propio
 * archivo dentro de /template-parts (responsabilidad única) —
 * eso no cambia con este Entregable.
 *
 * Lo que SÍ cambia (Sprint UX-1, Entregable UX-1.1 — Home Builder,
 * ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md y DECISIONS.md D-045):
 * el orden de secciones deja de estar codificado como una lista fija
 * de `get_template_part()` y pasa a leerse del registro central de
 * `inc/home-builder.php` (`ce_construction_home_sections()` +
 * `ce_construction_get_active_home_order()`).
 *
 * En este Entregable el resultado visual es idéntico al anterior:
 * el orden por defecto reproduce exactamente la secuencia que este
 * archivo tenía codificada antes (hero → about → services → projects
 * → stats → why_us → testimonials → gallery → cta → quote_form).
 * El panel de administración para reordenar/activar secciones desde
 * WordPress llega en el Entregable UX-1.2 (todavía no implementado).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ce_home_sections = ce_construction_home_sections();

foreach ( ce_construction_get_active_home_order() as $ce_home_section_key ) {
	// Guarda de seguridad: ignora claves de orden que no existan en el
	// registro (p. ej. configuración desincronizada tras desactivar un
	// filtro de terceros que registraba una sección custom).
	if ( ! isset( $ce_home_sections[ $ce_home_section_key ] ) ) {
		continue;
	}
	// 🆕 Sprint UX-5, Entregable UX-5.1: 'cta_secondary' reutiliza el
	// mismo template que 'cta' (ver inc/home-builder.php) — se le pasa
	// $args['variant']='secondary' para que template-parts/cta.php
	// sepa qué conjunto independiente de theme_mods leer (prefijo
	// 'ce_cta2_'). Ninguna otra clave necesita $args; se resuelve null
	// para preservar exactamente el comportamiento anterior en todas
	// las demás. Ver DECISIONS.md D-056.
	// 🆕 Sprint UX-6, Entregable UX-6.2: este cálculo se extrajo a
	// ce_construction_get_home_section_args() (inc/home-builder.php)
	// para que el shortcode [ce_section] (inc/section-shortcode.php)
	// pueda resolver exactamente el mismo $args sin duplicar este
	// condicional en un segundo archivo. Sin cambio de comportamiento
	// aquí. Ver DECISIONS.md D-060.
	$ce_home_section_args = ce_construction_get_home_section_args( $ce_home_section_key );
	get_template_part( $ce_home_sections[ $ce_home_section_key ]['template'], null, $ce_home_section_args );
}

get_footer();

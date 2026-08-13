<?php
/**
 * Home Builder — registro central de secciones del Home.
 *
 * FASE: Optimización UX / Conversión — Sprint UX-1, Entregable UX-1.1.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse —
 * ver docs/CURRENT_UX_SPRINT.md y docs/DECISIONS.md D-045.)
 *
 * Este archivo es la base arquitectónica del Home Builder: define,
 * en un único punto, qué secciones existen y a qué template-part
 * corresponde cada una. `front-page.php` deja de tener una lista
 * fija de `get_template_part()` y pasa a iterar este registro.
 *
 * Alcance explícito de UX-1.1 (ver DECISIONS.md D-045):
 *   - Registro de las 13 secciones previstas en el brief de UX/Conversión.
 *   - Orden por defecto que reproduce EXACTAMENTE el orden que
 *     `front-page.php` ya tenía antes de este Entregable (sin
 *     regresión visual: las secciones nuevas — Team, Clients, FAQ —
 *     se registran para uso futuro, pero NO se incluyen en el
 *     orden activo por defecto, porque sus template-parts todavía
 *     no existen; se incorporan en el Sprint UX-2).
 *   - `apply_filters( 'ce_home_active_order', ... )` deja el punto de
 *     extensión ya preparado para que el Entregable UX-1.2 (panel de
 *     administración en el Customizer) pueda enganchar la persistencia
 *     real sin tener que modificar de nuevo esta función.
 *
 * Explícitamente FUERA de alcance en UX-1.1 (se implementan en
 * Entregables posteriores, ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md):
 *   - Panel de administración (Customizer) para activar/desactivar
 *     y reordenar secciones (UX-1.2).
 *   - Template-parts de Team/Clients/FAQ (UX-2.1/UX-2.2).
 *   - CTA centralizado y modos de cotización (UX-3).
 *   - Hero configurable (UX-4).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registro central de secciones disponibles para el Home.
 *
 * Cada sección declara únicamente:
 *   - 'label'    → nombre legible (uso futuro: panel de administración, UX-1.2).
 *   - 'template' → ruta del template-part, tal como la espera get_template_part().
 *
 * Deliberadamente NO incluye lógica de activo/inactivo ni de orden:
 * esa responsabilidad vive en ce_construction_get_active_home_order(),
 * para mantener separada la "definición de qué existe" de la
 * "configuración de qué se muestra y en qué posición" (mismo principio
 * de separación ya aplicado en el proyecto entre registro de CPTs y
 * consumo en frontend — ver ARCHITECTURE.md sección 7).
 *
 * Filtrable (`ce_home_sections`) para que integraciones futuras puedan
 * añadir secciones sin modificar este archivo — ver DECISIONS.md D-045.
 *
 * @return array<string,array{label:string,template:string}>
 */
function ce_construction_home_sections() {
	return apply_filters( 'ce_home_sections', array(
		'hero'         => array(
			'label'    => __( 'Hero', 'ce-construction' ),
			'template' => 'template-parts/hero',
		),
		'about'        => array(
			'label'    => __( 'Quiénes Somos', 'ce-construction' ),
			'template' => 'template-parts/about',
		),
		'services'     => array(
			'label'    => __( 'Servicios', 'ce-construction' ),
			'template' => 'template-parts/services',
		),
		'projects'     => array(
			'label'    => __( 'Proyectos', 'ce-construction' ),
			'template' => 'template-parts/projects',
		),
		'stats'        => array(
			'label'    => __( 'Estadísticas', 'ce-construction' ),
			'template' => 'template-parts/stats',
		),
		'why_us'       => array(
			'label'    => __( 'Por Qué Elegirnos', 'ce-construction' ),
			'template' => 'template-parts/why-us',
		),
		'testimonials' => array(
			'label'    => __( 'Testimonios', 'ce-construction' ),
			'template' => 'template-parts/testimonials',
		),
		'gallery'      => array(
			'label'    => __( 'Galería', 'ce-construction' ),
			'template' => 'template-parts/gallery',
		),
		// Registradas para el catálogo completo del brief de UX/Conversión.
		// Sus template-parts se crean en el Sprint UX-2 (Entregable UX-2.1/UX-2.2).
		// Hasta entonces, get_template_part() simplemente no imprime nada si
		// se invocan (comportamiento nativo de WordPress), y no forman parte
		// del orden activo por defecto (ver ce_construction_default_home_order()).
		'team'         => array(
			'label'    => __( 'Equipo', 'ce-construction' ),
			'template' => 'template-parts/team',
		),
		'clients'      => array(
			'label'    => __( 'Clientes', 'ce-construction' ),
			'template' => 'template-parts/clients',
		),
		'faq'          => array(
			'label'    => __( 'Preguntas Frecuentes', 'ce-construction' ),
			'template' => 'template-parts/faq',
		),
		'cta'          => array(
			'label'    => __( 'CTA', 'ce-construction' ),
			'template' => 'template-parts/cta',
		),
		'quote_form'   => array(
			'label'    => __( 'Formulario de Cotización', 'ce-construction' ),
			'template' => 'template-parts/quote-form',
		),
	) );
}

/**
 * Orden activo por defecto del Home cuando no existe configuración
 * guardada por el administrador (instalación nueva, o antes de que
 * UX-1.2 introduzca su propio theme_mod).
 *
 * Reproduce EXACTAMENTE el orden que front-page.php tenía codificado
 * antes de este Entregable — es una condición de aceptación explícita
 * de UX-1.1 (cero regresión visual). Team/Clients/FAQ quedan fuera de
 * este array a propósito: sus template-parts no existen todavía.
 *
 * @return string[] Claves de ce_construction_home_sections(), en orden.
 */
function ce_construction_default_home_order() {
	return array(
		'hero',
		'about',
		'services',
		'projects',
		'stats',
		'why_us',
		'testimonials',
		'gallery',
		'cta',
		'quote_form',
	);
}

/**
 * Orden activo real del Home.
 *
 * En UX-1.1 no existe todavía persistencia de configuración (esa
 * pieza es el alcance de UX-1.2, ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md
 * §5, Entregable UX-1.2), así que esta función devuelve el orden por
 * defecto. El filtro `ce_home_active_order` queda expuesto desde ya
 * para que UX-1.2 pueda leer el theme_mod correspondiente y devolver
 * el orden guardado por el administrador sin tener que modificar de
 * nuevo esta función ni front-page.php.
 *
 * Claves que no existan en ce_construction_home_sections() se
 * ignoran de forma segura por quien consuma este array (ver
 * front-page.php), para tolerar configuraciones desincronizadas
 * (p. ej. tras desactivar un plugin/filtro que registraba una
 * sección custom).
 *
 * @return string[]
 */
function ce_construction_get_active_home_order() {
	return apply_filters( 'ce_home_active_order', ce_construction_default_home_order() );
}

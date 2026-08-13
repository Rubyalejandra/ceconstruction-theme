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
		// 🆕 Sprint UX-2, Entregable UX-2.1: template-parts/team.php y
		// template-parts/clients.php YA EXISTEN (dejaron de apuntar a
		// rutas inexistentes) — reutilizan content-equipo.php/
		// content-cliente.php como partial de card, mismo patrón que
		// template-parts/projects.php.
		// 🆕 Sprint UX-2, Entregable UX-2.2: template-parts/faq.php
		// TAMBIÉN EXISTE YA — reutiliza el partial compartido
		// template-parts/content-faq-accordion.php (extraído en este
		// mismo Entregable de single-servicio.php, ver DECISIONS.md
		// D-048), mismo `.ce-accordion` en ambos contextos.
		// Con esto, las 13 secciones del catálogo del brief tienen
		// template-part real: el Sprint UX-2 queda completado. Ninguna
		// de las tres (team/clients/faq) forma parte del orden activo
		// por defecto (ver ce_construction_default_home_order()):
		// quedan disponibles mas no auto-activadas, a la espera de que
		// el administrador las active explícitamente desde el panel
		// "CE: Home Builder" del Customizer (Entregable UX-1.2) — ver
		// DECISIONS.md D-047 y D-048.
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
 * este array a propósito, y siguen fuera tras el Sprint UX-2 completo
 * (Entregables UX-2.1 y UX-2.2): las 3 secciones ya tienen
 * template-part real (`template-parts/team.php`, `clients.php`,
 * `faq.php`), pero activarlas en el Home es una decisión del
 * administrador, no algo que deba ocurrir en silencio con solo crear
 * el archivo — se activan explícitamente desde el panel "CE: Home
 * Builder" del Customizer (ver DECISIONS.md D-047 y D-048).
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

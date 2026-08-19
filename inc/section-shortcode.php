<?php
/**
 * Shortcode [ce_section] — reutilización de secciones del Home Builder
 * fuera del Home (Páginas, entradas, y cualquier contexto que procese
 * shortcodes vía `the_content()`).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-6, Entregable UX-6.2.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse — ver
 * docs/CURRENT_UX_SPRINT.md y docs/DECISIONS.md.)
 *
 * Alcance explícito de UX-6.2 (ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md
 * §8.4 y docs/DECISIONS.md D-060):
 *   - Un único shortcode, `[ce_section key="..."]`, que resuelve la
 *     clave contra el registro central `ce_construction_home_sections()`
 *     (inc/home-builder.php) y delega en `get_template_part()` — el
 *     MISMO mecanismo que ya usa `front-page.php` para construir el
 *     Home, sin reimplementarlo.
 *   - El shortcode NO depende de que la sección esté activa en el
 *     Home Builder (`ce_construction_get_active_home_order()`): son
 *     dos consumos independientes del mismo registro. Una sección
 *     puede estar desactivada en el Home y aun así renderizarse aquí.
 *   - `$args` se resuelve con `ce_construction_get_home_section_args()`
 *     (inc/home-builder.php, extraída en este mismo Entregable — ver
 *     D-060), la misma función que ahora usa también `front-page.php`,
 *     para que una clave como `cta_secondary` (D-056) se renderice con
 *     el conjunto de contenido correcto también vía shortcode, sin
 *     duplicar ese condicional en un segundo archivo.
 *
 * Explícitamente FUERA de alcance de UX-6.2:
 *   - Bloque Gutenberg dinámico (evaluado y descartado por ahora en el
 *     plan, §8.4 — reevaluable a futuro si se necesita preview visual
 *     en el editor).
 *   - Cualquier atributo del shortcode más allá de `key` (p. ej. un
 *     override de `$args` desde el propio shortcode) — no estaba en
 *     el criterio de aceptación de este Entregable; el `$args` sigue
 *     siendo responsabilidad exclusiva del registro central, igual
 *     que en `front-page.php`.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderiza `[ce_section key="..."]`.
 *
 * Guardas de seguridad (mismo criterio de tolerancia ya usado por
 * front-page.php ante claves desincronizadas, ver inc/home-builder.php):
 *   - Sin atributo `key`, o `key` vacío: no imprime nada.
 *   - `key` que no exista en ce_construction_home_sections(): no
 *     imprime nada (no se muestra ningún error en frontend).
 *   - Reentrancia: si una plantilla invocada por el shortcode volviera
 *     a contener el mismo (u otro) `[ce_section]` — p. ej. por un
 *     filtro de terceros mal configurado — se limita la profundidad
 *     de anidamiento para evitar un bucle infinito/fatal por memoria.
 *
 * @param array $atts Atributos del shortcode.
 * @return string Markup ya resuelto de la sección, o cadena vacía.
 */
function ce_construction_section_shortcode( $atts ) {
	static $ce_section_depth = 0;

	$atts = shortcode_atts(
		array(
			'key' => '',
		),
		$atts,
		'ce_section'
	);

	$key = sanitize_key( $atts['key'] );

	if ( '' === $key ) {
		return '';
	}

	$sections = ce_construction_home_sections();

	if ( ! isset( $sections[ $key ]['template'] ) ) {
		return '';
	}

	// Límite de anidamiento defensivo: 3 niveles son más que suficientes
	// para cualquier uso legítimo (una sección no debería contenerse a
	// sí misma), y evita un fatal por agotamiento de memoria si algún
	// filtro de terceros llegara a crear una referencia circular.
	if ( $ce_section_depth >= 3 ) {
		return '';
	}

	$args = ce_construction_get_home_section_args( $key );

	$ce_section_depth++;
	ob_start();
	get_template_part( $sections[ $key ]['template'], null, $args );
	$output = ob_get_clean();
	$ce_section_depth--;

	return $output;
}
add_shortcode( 'ce_section', 'ce_construction_section_shortcode' );

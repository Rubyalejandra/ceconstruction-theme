<?php
/**
 * Template part: ítem individual de accordion para una Pregunta
 * Frecuente (CPT `ce_faq`).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-2, Entregable UX-2.2.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse —
 * ver docs/CURRENT_UX_SPRINT.md.)
 *
 * Extraído en este Entregable del bloque "FAQ relacionadas" que ya
 * existía en single-servicio.php desde el Sprint 3, para reutilizar
 * exactamente el mismo markup en template-parts/faq.php (sección de
 * Home). Sigue el mismo patrón de partial-de-item-dentro-de-loop que
 * content-servicio.php/content-proyecto.php/content-equipo.php/
 * content-cliente.php (ver ARCHITECTURE.md sección 3): DEBE
 * invocarse DENTRO de un loop estándar (`have_posts()`/`the_post()`)
 * y DENTRO de un contenedor `.ce-accordion` ya abierto por quien lo
 * invoca — el wrapper de la lista vive en el archivo que hace el
 * loop (single-servicio.php o template-parts/faq.php), este partial
 * solo imprime el ítem individual.
 *
 * Cambio deliberado respecto al bloque original: el id del panel
 * (`aria-controls`/`id`) usa `get_the_ID()` en vez de un índice
 * secuencial (`$faq_index`) pasado por el llamador. Esto hace que el
 * identificador sea único de forma intrínseca (los IDs de post de
 * WordPress ya son únicos globalmente) sin depender de que cada
 * contexto lleve su propio contador — y evita cualquier colisión de
 * `id`/`aria-controls` si, en un futuro Sprint, ambos contextos
 * (Home y single-servicio.php) llegaran a coexistir en el mismo
 * documento (p. ej. dentro de un modal). `ModuleAccordion`
 * (assets/js/main.js) ya es genérico — usa `.closest('.ce-accordion')`
 * para acotar el comportamiento "un solo item abierto" a su propio
 * contenedor — y no requirió ningún cambio para soportar múltiples
 * instancias de `.ce-accordion` en la misma página.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$panel_id = 'ce-faq-panel-' . get_the_ID();
?>
<div class="ce-accordion__item">
	<button type="button" class="ce-accordion__question" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
		<span><?php the_title(); ?></span>
		<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
	</button>
	<div class="ce-accordion__answer" id="<?php echo esc_attr( $panel_id ); ?>" role="region">
		<div class="ce-accordion__answer-inner"><?php the_content(); ?></div>
	</div>
</div>

<?php
/**
 * Template part: Preguntas Frecuentes (Home).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-2, Entregable UX-2.2.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse —
 * ver docs/CURRENT_UX_SPRINT.md.)
 *
 * Sección de Home para el CPT `ce_faq`, registrada como 'faq' en
 * inc/home-builder.php desde el Entregable UX-1.1. Mismo patrón de
 * auto-ocultamiento que el resto de secciones de Home basadas en
 * CPT (`team.php`, `clients.php`, `projects.php`): `ce_cpt_has_posts()`
 * como guarda de entrada + `WP_Query` acotado.
 *
 * Reutiliza el mismo `.ce-accordion` ya usado en single-servicio.php
 * a través del partial compartido
 * template-parts/content-faq-accordion.php (extraído en este mismo
 * Entregable — ver DECISIONS.md D-048), para no duplicar el markup
 * del ítem de accordion entre Home y single-servicio.php. El
 * contenedor `.ce-accordion` se envuelve además en la utilidad ya
 * existente `.ce-max-w-content` (main.css, sin cambios) para una
 * columna de lectura más cómoda en el Home — mismo criterio que
 * `about.php`/`no-results.php`, sin CSS nuevo.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'ce_faq' ) ) {
	return;
}

$faq_home_query = new WP_Query( array(
	'post_type'      => 'ce_faq',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
) );
?>
<section class="ce-section" id="ce-faq">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Dudas Frecuentes', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Preguntas Frecuentes', 'ce-construction' ); ?></h2>
			<p class="ce-section-lead" style="margin-inline:auto;">
				<?php esc_html_e( 'Respuestas rápidas a las preguntas que más nos hacen nuestros clientes antes de iniciar un proyecto.', 'ce-construction' ); ?>
			</p>
		</div>

		<div class="ce-accordion ce-max-w-content ce-animate-on-scroll">
			<?php
			while ( $faq_home_query->have_posts() ) :
				$faq_home_query->the_post();
				get_template_part( 'template-parts/content-faq-accordion' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

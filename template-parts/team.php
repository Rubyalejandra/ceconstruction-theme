<?php
/**
 * Template part: Equipo (Home).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-2, Entregable UX-2.1.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse —
 * ver docs/CURRENT_UX_SPRINT.md.)
 *
 * Sección de Home para el CPT `miembro_equipo`, registrada como
 * 'team' en inc/home-builder.php desde el Entregable UX-1.1. Sigue
 * el mismo patrón exacto de template-parts/projects.php:
 *   - `WP_Query` acotado (posts_per_page fijo, post_status publish).
 *   - Auto-ocultamiento vía ce_cpt_has_posts() si el CPT está vacío.
 *   - Reutiliza template-parts/content-equipo.php como partial de
 *     card dentro del loop (cero markup nuevo de tarjeta: es el
 *     mismo partial que ya usa archive-equipo.php), envuelto en el
 *     mismo `.ce-grid.ce-grid--4` que ese archivo ya usa — sin
 *     inventar una clase de grid nueva.
 *   - Enlace final al archivo completo del CPT, igual que
 *     projects.php ("Ver todos los proyectos" → aquí "Ver todo el
 *     equipo"), sin condicional de `found_posts` (mismo patrón).
 *
 * Cero lógica de negocio nueva: reutiliza el CPT, el helper
 * ce_cpt_has_posts() y el partial de card ya existentes desde el
 * Sprint 5 (ver ARCHITECTURE.md, módulo Equipo y Clientes).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'miembro_equipo' ) ) {
	return;
}

$equipo_home_query = new WP_Query( array(
	'post_type'      => 'miembro_equipo',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
) );
?>
<section class="ce-section" id="ce-team">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Nuestra Gente', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Conoce a Nuestro Equipo', 'ce-construction' ); ?></h2>
			<p class="ce-section-lead" style="margin-inline:auto;">
				<?php esc_html_e( 'Profesionales certificados y comprometidos con la calidad y seguridad de cada proyecto que ejecutamos.', 'ce-construction' ); ?>
			</p>
		</div>

		<div class="ce-grid ce-grid--4">
			<?php
			while ( $equipo_home_query->have_posts() ) :
				$equipo_home_query->the_post();
				get_template_part( 'template-parts/content-equipo' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<div class="ce-text-center ce-mt-5">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'miembro_equipo' ) ); ?>" class="ce-btn ce-btn--dark">
				<?php esc_html_e( 'Ver todo el equipo', 'ce-construction' ); ?>
			</a>
		</div>
	</div>
</section>

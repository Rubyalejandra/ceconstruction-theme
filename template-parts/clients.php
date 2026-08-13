<?php
/**
 * Template part: Clientes (Home).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-2, Entregable UX-2.1.
 * (Fase paralela al Sprint 8, que permanece pausado sin cerrarse —
 * ver docs/CURRENT_UX_SPRINT.md.)
 *
 * Sección de Home para el CPT `cliente`, registrada como 'clients'
 * en inc/home-builder.php desde el Entregable UX-1.1. Mismo patrón
 * exacto de template-parts/projects.php:
 *   - `WP_Query` acotado (posts_per_page fijo, post_status publish).
 *   - Auto-ocultamiento vía ce_cpt_has_posts() si el CPT está vacío.
 *   - Reutiliza template-parts/content-cliente.php como partial de
 *     card dentro del loop (cero markup nuevo de tarjeta: es el
 *     mismo partial que ya usa archive-clientes.php), envuelto en
 *     el mismo `.ce-clients-grid` que ese archivo ya usa (NO el
 *     `.ce-grid.ce-grid--N` genérico: content-cliente.php ya
 *     imprime `.ce-clients-grid__item`, clase acoplada 1:1 al
 *     contenedor `.ce-clients-grid` definido en main.css sección 22
 *     — usar cualquier otro wrapper rompería el grid responsive ya
 *     estilado de ese componente).
 *   - Enlace final al archivo completo del CPT, igual que
 *     projects.php, sin condicional de `found_posts`.
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

if ( ! ce_cpt_has_posts( 'cliente' ) ) {
	return;
}

$clientes_home_query = new WP_Query( array(
	'post_type'      => 'cliente',
	'posts_per_page' => 10,
	'post_status'    => 'publish',
) );
?>
<section class="ce-section" id="ce-clients">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Confianza', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Empresas Que Confían en Nosotros', 'ce-construction' ); ?></h2>
			<p class="ce-section-lead" style="margin-inline:auto;">
				<?php esc_html_e( 'Un vistazo a algunas de las empresas y organizaciones que hemos acompañado en sus proyectos de construcción.', 'ce-construction' ); ?>
			</p>
		</div>

		<div class="ce-clients-grid">
			<?php
			while ( $clientes_home_query->have_posts() ) :
				$clientes_home_query->the_post();
				get_template_part( 'template-parts/content-cliente' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<div class="ce-text-center ce-mt-5">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'cliente' ) ); ?>" class="ce-btn ce-btn--dark">
				<?php esc_html_e( 'Ver todos los clientes', 'ce-construction' ); ?>
			</a>
		</div>
	</div>
</section>

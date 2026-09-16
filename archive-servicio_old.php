<?php
/**
 * Archive: Servicios.
 * Breadcrumbs se renderizan globalmente desde header.php
 * (ce_construction_breadcrumbs(), ver inc/seo.php).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow'  => __( 'CE Construction', 'ce-construction' ),
	'title'    => post_type_archive_title( '', false ),
	'subtitle' => __( 'Soluciones integrales de construcción adaptadas a las necesidades de cada proyecto, desde el diseño hasta la entrega.', 'ce-construction' ),
) );
?>

<section class="ce-section">
	<div class="ce-container">
		<div class="ce-layout-with-sidebar">

			<div>
				<?php if ( have_posts() ) : ?>
					<div class="ce-grid ce-grid--2">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content-servicio' );
						endwhile;
						?>
					</div>

					<nav class="ce-mt-6" aria-label="<?php esc_attr_e( 'Paginación de servicios', 'ce-construction' ); ?>">
						<?php
						echo paginate_links( array(
							'prev_text' => '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . esc_html__( 'Anterior', 'ce-construction' ),
							'next_text' => esc_html__( 'Siguiente', 'ce-construction' ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',
							'type'      => 'list',
						) );
						?>
					</nav>
				<?php else : ?>
					<div class="ce-card">
						<div class="ce-card__body ce-text-center">
							<p><?php esc_html_e( 'Aún no hay servicios publicados. Vuelve pronto.', 'ce-construction' ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php get_template_part( 'template-parts/sidebar-servicios' ); ?>

		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();

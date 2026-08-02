<?php
/**
 * Template part: Servicios (usa el CPT `servicio`).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'servicio' ) ) {
	return;
}

$servicios_query = new WP_Query( array(
	'post_type'      => 'servicio',
	'posts_per_page' => 6,
	'post_status'    => 'publish',
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>
<section class="ce-section ce-section--alt" id="ce-services">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Lo que hacemos', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Nuestros Servicios', 'ce-construction' ); ?></h2>
			<p class="ce-section-lead" style="margin-inline:auto;">
				<?php esc_html_e( 'Soluciones integrales de construcción adaptadas a las necesidades de cada proyecto, desde el diseño hasta la entrega.', 'ce-construction' ); ?>
			</p>
		</div>

		<div class="ce-grid ce-grid--3">
			<?php
			while ( $servicios_query->have_posts() ) :
				$servicios_query->the_post();
				$enlace = get_post_meta( get_the_ID(), '_ce_enlace_externo', true );
				$enlace = $enlace ? $enlace : get_permalink();
				?>
				<article class="ce-card ce-animate-on-scroll">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="ce-card__media">
							<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
						</div>
					<?php endif; ?>
					<div class="ce-card__body">
						<div class="ce-card__icon">
							<?php ce_render_service_icon( get_the_ID() ); ?>
						</div>
						<h3 class="ce-card__title"><?php the_title(); ?></h3>
						<p class="ce-card__text"><?php echo esc_html( ce_get_short_excerpt( get_the_ID(), 18 ) ); ?></p>
						<a href="<?php echo esc_url( $enlace ); ?>" class="ce-card__link">
							<?php esc_html_e( 'Conocer más', 'ce-construction' ); ?>
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<?php if ( $servicios_query->found_posts > 6 ) : ?>
			<div class="ce-text-center ce-mt-5">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'servicio' ) ); ?>" class="ce-btn ce-btn--dark">
					<?php esc_html_e( 'Ver todos los servicios', 'ce-construction' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

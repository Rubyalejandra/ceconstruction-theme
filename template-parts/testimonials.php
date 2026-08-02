<?php
/**
 * Template part: Testimonios (usa el CPT `testimonio`).
 * El slider (autoplay, dots, flechas, swipe) es manejado por
 * ModuleTestimonialSlider en assets/js/main.js.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'testimonio' ) ) {
	return;
}

$testimonios_query = new WP_Query( array(
	'post_type'      => 'testimonio',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
) );
?>
<section class="ce-section ce-testimonials" id="ce-testimonials">
	<div class="ce-container ce-relative">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Testimonios', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Lo Que Dicen Nuestros Clientes', 'ce-construction' ); ?></h2>
		</div>

		<div class="ce-relative">
			<div class="ce-testimonial-slider" data-autoplay="6000">
				<div class="ce-testimonial-track">
					<?php
					while ( $testimonios_query->have_posts() ) :
						$testimonios_query->the_post();
						$nombre = get_post_meta( get_the_ID(), '_ce_testimonio_nombre', true );
						$nombre = $nombre ? $nombre : get_the_title();
						$cargo  = get_post_meta( get_the_ID(), '_ce_testimonio_cargo', true );
						$rating = (int) get_post_meta( get_the_ID(), '_ce_testimonio_rating', true );
						$rating = $rating ? $rating : 5;
						?>
						<div class="ce-testimonial-slide">
							<div class="ce-testimonial-card">
								<div class="ce-testimonial-card__rating">
									<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
										<i class="fa-solid fa-star" aria-hidden="true" style="<?php echo $i > $rating ? 'opacity:.25;' : ''; ?>"></i>
									<?php endfor; ?>
								</div>
								<p class="ce-testimonial-card__quote">&ldquo;<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>&rdquo;</p>
								<div class="ce-testimonial-card__author">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy', 'alt' => $nombre ) ); ?>
									<?php endif; ?>
									<div>
										<div class="ce-testimonial-card__name"><?php echo esc_html( $nombre ); ?></div>
										<?php if ( $cargo ) : ?>
											<div class="ce-testimonial-card__role"><?php echo esc_html( $cargo ); ?></div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<button class="ce-slider-arrow ce-slider-arrow--prev" aria-label="<?php esc_attr_e( 'Testimonio anterior', 'ce-construction' ); ?>">
					<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
				</button>
				<button class="ce-slider-arrow ce-slider-arrow--next" aria-label="<?php esc_attr_e( 'Testimonio siguiente', 'ce-construction' ); ?>">
					<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
				</button>
			</div>
			<div class="ce-slider-nav"></div>
		</div>
	</div>
</section>

<?php
/**
 * Template part: Testimonios (usa el CPT `testimonio`).
 * El slider (autoplay, dots, flechas, swipe) es manejado por
 * ModuleTestimonialSlider en assets/js/main.js.
 *
 * 🆕 Sprint UX-10, Entregable UX-10.2 (ver DECISIONS.md D-073): CTA
 * "Ver todos los testimonios" al cierre de este teaser, enlazando a
 * la Página de Testimonios de UX-10.1
 * ([ce_section key="testimonials_full"]). El destino es un theme_mod
 * de texto (`ce_testimonials_page_url`, nueva sección "CE:
 * Testimonios" en el Customizer) porque esa Página la crea el propio
 * administrador (no existe una URL predecible/auto-generada, a
 * diferencia de `get_post_type_archive_link()` que ya usan
 * `projects.php`/`services.php` para sus CPTs con archivo nativo).
 * Sin configurar: el CTA no se imprime (mismo criterio de
 * auto-ocultado ya usado por `trust-badges.php`, D-071, Decisión 1)
 * — cero cambio visual hasta que el administrador cree esa Página y
 * pegue su URL.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'testimonio' ) ) {
	return;
}

$ce_testimonials_page_url = get_theme_mod( 'ce_testimonials_page_url', '' );

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
					// 🆕 Sprint UX-7, Entregable UX-7.3: la card individual
					// (antes inline aquí) se extrajo a
					// template-parts/content-testimonio-card.php para
					// reutilizarla también en el slot opcional de los
					// sidebars de Servicios/Proyectos, sin duplicar este
					// markup — mismo patrón que content-faq-accordion.php
					// (D-048). Este wrapper .ce-testimonial-slide y la
					// query $testimonios_query de arriba no cambiaron.
					while ( $testimonios_query->have_posts() ) :
						$testimonios_query->the_post();
						?>
						<div class="ce-testimonial-slide">
							<?php get_template_part( 'template-parts/content-testimonio-card' ); ?>
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

		<?php if ( $ce_testimonials_page_url ) : ?>
			<div class="ce-text-center ce-mt-5">
				<a href="<?php echo esc_url( $ce_testimonials_page_url ); ?>" class="ce-btn ce-btn--dark">
					<?php esc_html_e( 'Ver todos los testimonios', 'ce-construction' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * Single: Servicio.
 * Breadcrumbs (HTML) se renderizan globalmente desde header.php.
 * Schema.org (Service + BreadcrumbList) se emite desde
 * inc/seo.php -> ce_construction_schema_service().
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => __( 'Servicios', 'ce-construction' ),
		'title'    => get_the_title(),
		'subtitle' => ce_get_short_excerpt( $post_id, 24 ),
		'image_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
	) );
	?>

	<section class="ce-section">
		<div class="ce-container">
			<div class="ce-layout-with-sidebar">

				<div>
					<!-- Contenido principal del servicio -->
					<article <?php post_class( 'ce-animate-on-scroll is-in-view' ); ?>>
						<div class="ce-service-icon-badge">
							<?php ce_render_service_icon( $post_id ); ?>
						</div>

						<?php if ( has_post_thumbnail() ) : ?>
							<div class="ce-mb-4">
								<?php the_post_thumbnail( 'ce-hero', array(
									'loading' => 'lazy',
									'alt'     => get_the_title(),
									'style'   => 'border-radius:var(--ce-radius-lg); box-shadow:var(--ce-shadow-md); width:100%; object-fit:cover; aspect-ratio:16/9;',
								) ); ?>
							</div>
						<?php endif; ?>

						<div class="ce-service-content">
							<?php the_content(); ?>
						</div>
					</article>

					<!-- Navegación entre servicios -->
					<?php
					$prev_service = get_previous_post( false );
					$next_service = get_next_post( false );
					if ( $prev_service || $next_service ) :
						?>
						<nav class="ce-service-nav" aria-label="<?php esc_attr_e( 'Navegación entre servicios', 'ce-construction' ); ?>">
							<?php if ( $prev_service ) : ?>
								<a href="<?php echo esc_url( get_permalink( $prev_service ) ); ?>" class="ce-service-nav__item ce-service-nav__item--prev">
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></span>
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Servicio anterior', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $prev_service ) ); ?></span>
									</span>
								</a>
							<?php else : ?>
								<span></span>
							<?php endif; ?>

							<?php if ( $next_service ) : ?>
								<a href="<?php echo esc_url( get_permalink( $next_service ) ); ?>" class="ce-service-nav__item ce-service-nav__item--next">
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Siguiente servicio', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $next_service ) ); ?></span>
									</span>
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>

					<!-- Servicios relacionados -->
					<?php
					$related_services = ce_get_related_services( $post_id, 3 );
					if ( $related_services->have_posts() ) :
						?>
						<div class="ce-mt-6">
							<h2 class="ce-mb-4"><?php esc_html_e( 'Servicios Relacionados', 'ce-construction' ); ?></h2>
							<div class="ce-grid ce-grid--3">
								<?php
								while ( $related_services->have_posts() ) :
									$related_services->the_post();
									get_template_part( 'template-parts/content-servicio' );
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Proyectos relacionados -->
					<?php
					if ( post_type_exists( 'proyecto' ) && ce_cpt_has_posts( 'proyecto' ) ) :
						$related_projects = ce_get_related_projects( $post_id, 3 );
						if ( $related_projects->have_posts() ) :
							?>
							<div class="ce-mt-6">
								<h2 class="ce-mb-4"><?php esc_html_e( 'Proyectos Relacionados', 'ce-construction' ); ?></h2>
								<div class="ce-grid ce-grid--3">
									<?php
									while ( $related_projects->have_posts() ) :
										$related_projects->the_post();
										$ubicacion = get_post_meta( get_the_ID(), '_ce_proyecto_ubicacion', true );
										?>
										<article class="ce-card ce-animate-on-scroll">
											<?php if ( has_post_thumbnail() ) : ?>
												<div class="ce-card__media">
													<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
												</div>
											<?php endif; ?>
											<div class="ce-card__body">
												<h3 class="ce-card__title"><?php the_title(); ?></h3>
												<?php if ( $ubicacion ) : ?>
													<p class="ce-card__text"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html( $ubicacion ); ?></p>
												<?php endif; ?>
												<a href="<?php the_permalink(); ?>" class="ce-card__link">
													<?php esc_html_e( 'Ver proyecto', 'ce-construction' ); ?>
													<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
												</a>
											</div>
										</article>
										<?php
									endwhile;
									wp_reset_postdata();
									?>
								</div>
							</div>
							<?php
						endif;
					endif;
					?>

					<!-- FAQ relacionadas -->
					<?php if ( post_type_exists( 'ce_faq' ) && ce_cpt_has_posts( 'ce_faq' ) ) : ?>
						<?php
						$faqs = new WP_Query( array(
							'post_type'      => 'ce_faq',
							'posts_per_page' => 5,
							'post_status'    => 'publish',
							'no_found_rows'  => true,
						) );
						if ( $faqs->have_posts() ) :
							?>
							<div class="ce-mt-6">
								<h2 class="ce-mb-4"><?php esc_html_e( 'Preguntas Frecuentes', 'ce-construction' ); ?></h2>
								<div class="ce-accordion">
									<?php
									$faq_index = 0;
									while ( $faqs->have_posts() ) :
										$faqs->the_post();
										$faq_index++;
										$panel_id = 'ce-faq-panel-' . $faq_index;
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
										<?php
									endwhile;
									wp_reset_postdata();
									?>
								</div>
							</div>
							<?php
						endif;
						?>
					<?php endif; ?>

				</div>

				<?php get_template_part( 'template-parts/sidebar-servicios', null, array( 'exclude' => $post_id ) ); ?>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_template_part( 'template-parts/cta' );
get_template_part( 'template-parts/quote-form' );

get_footer();

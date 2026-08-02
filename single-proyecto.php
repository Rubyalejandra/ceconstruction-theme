<?php
/**
 * Single: Proyecto.
 * Breadcrumbs (HTML) se renderizan globalmente desde header.php.
 * Schema.org (CreativeWork/Project + BreadcrumbList) se emite desde
 * inc/seo.php -> ce_construction_schema_project().
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id   = get_the_ID();
	$cliente   = get_post_meta( $post_id, '_ce_proyecto_cliente', true );
	$ubicacion = get_post_meta( $post_id, '_ce_proyecto_ubicacion', true );
	$fecha     = get_post_meta( $post_id, '_ce_proyecto_fecha', true );
	$estados   = get_the_terms( $post_id, 'estado_proyecto' );
	$estado    = ( $estados && ! is_wp_error( $estados ) ) ? $estados[0]->name : '';
	$gallery_ids = ce_get_gallery_ids( $post_id );

	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => __( 'Proyectos', 'ce-construction' ),
		'title'    => get_the_title(),
		'subtitle' => ce_get_short_excerpt( $post_id, 24 ),
		'image_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
	) );
	?>

	<section class="ce-section">
		<div class="ce-container">
			<div class="ce-layout-with-sidebar">

				<div>
					<!-- Ficha de metadatos: cliente, ubicación, fecha, estado -->
					<?php if ( $cliente || $ubicacion || $fecha || $estado ) : ?>
						<div class="ce-project-meta-grid ce-animate-on-scroll is-in-view">
							<?php if ( $cliente ) : ?>
								<div class="ce-project-meta-item">
									<span class="ce-project-meta-item__icon"><i class="fa-solid fa-user-tie" aria-hidden="true"></i></span>
									<span>
										<span class="ce-project-meta-item__label"><?php esc_html_e( 'Cliente', 'ce-construction' ); ?></span>
										<span class="ce-project-meta-item__value"><?php echo esc_html( $cliente ); ?></span>
									</span>
								</div>
							<?php endif; ?>
							<?php if ( $ubicacion ) : ?>
								<div class="ce-project-meta-item">
									<span class="ce-project-meta-item__icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
									<span>
										<span class="ce-project-meta-item__label"><?php esc_html_e( 'Ubicación', 'ce-construction' ); ?></span>
										<span class="ce-project-meta-item__value"><?php echo esc_html( $ubicacion ); ?></span>
									</span>
								</div>
							<?php endif; ?>
							<?php if ( $fecha ) : ?>
								<div class="ce-project-meta-item">
									<span class="ce-project-meta-item__icon"><i class="fa-regular fa-calendar" aria-hidden="true"></i></span>
									<span>
										<span class="ce-project-meta-item__label"><?php esc_html_e( 'Fecha de entrega', 'ce-construction' ); ?></span>
										<span class="ce-project-meta-item__value"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fecha ) ) ); ?></span>
									</span>
								</div>
							<?php endif; ?>
							<?php if ( $estado ) : ?>
								<div class="ce-project-meta-item">
									<span class="ce-project-meta-item__icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></span>
									<span>
										<span class="ce-project-meta-item__label"><?php esc_html_e( 'Estado', 'ce-construction' ); ?></span>
										<span class="ce-project-meta-item__value"><?php echo esc_html( $estado ); ?></span>
									</span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<!-- Contenido principal del proyecto -->
					<article <?php post_class( 'ce-animate-on-scroll is-in-view' ); ?>>
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

					<!-- Galería con Lightbox -->
					<?php if ( ! empty( $gallery_ids ) ) : ?>
						<div class="ce-mt-6">
							<h2 class="ce-mb-4"><?php esc_html_e( 'Galería del Proyecto', 'ce-construction' ); ?></h2>
							<div class="ce-gallery-grid">
								<?php foreach ( $gallery_ids as $img_id ) :
									$thumb = wp_get_attachment_image_url( $img_id, 'ce-card' );
									$full  = wp_get_attachment_image_url( $img_id, 'full' );
									$alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
									if ( ! $thumb ) {
										continue;
									}
									?>
									<div class="ce-gallery-item ce-animate-on-scroll" data-full="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?>">
										<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
										<div class="ce-gallery-item__icon"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i></div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Navegación entre proyectos -->
					<?php
					$prev_project = get_previous_post( false );
					$next_project = get_next_post( false );
					if ( $prev_project || $next_project ) :
						?>
						<nav class="ce-service-nav" aria-label="<?php esc_attr_e( 'Navegación entre proyectos', 'ce-construction' ); ?>">
							<?php if ( $prev_project ) : ?>
								<a href="<?php echo esc_url( get_permalink( $prev_project ) ); ?>" class="ce-service-nav__item ce-service-nav__item--prev">
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></span>
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Proyecto anterior', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $prev_project ) ); ?></span>
									</span>
								</a>
							<?php else : ?>
								<span></span>
							<?php endif; ?>

							<?php if ( $next_project ) : ?>
								<a href="<?php echo esc_url( get_permalink( $next_project ) ); ?>" class="ce-service-nav__item ce-service-nav__item--next">
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Siguiente proyecto', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $next_project ) ); ?></span>
									</span>
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>

					<!-- Servicios relacionados -->
					<?php
					if ( post_type_exists( 'servicio' ) && ce_cpt_has_posts( 'servicio' ) ) :
						$related_services = ce_get_related_services_for_project( $post_id, 3 );
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
							<?php
						endif;
					endif;
					?>

				</div>

				<?php get_template_part( 'template-parts/sidebar-proyectos', null, array( 'exclude' => $post_id ) ); ?>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_template_part( 'template-parts/cta' );
get_template_part( 'template-parts/quote-form' );

get_footer();

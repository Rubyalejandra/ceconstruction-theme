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
	// Sprint UX-8, Entregable UX-8.1 (D-108): la galería del proyecto
	// se resuelve ahora desde $media_items (mosaico mixto, imagen y/o
	// video) — ver ce_construction_get_proyecto_media_items()
	// (inc/helpers.php), que incluye la migración automática de solo
	// lectura desde el formato antiguo (`_ce_proyecto_galeria`) para
	// proyectos que todavía no se han vuelto a guardar con el nuevo
	// selector. `ce_get_gallery_ids()` (usada antes directamente en
	// este archivo) sigue existiendo y sigue sincronizada en cada
	// guardado (ver inc/meta-boxes.php) para su otro consumidor,
	// inc/seo.php (Schema.org de Proyecto) — sin cambios ahí.
	$media_items = ce_construction_get_proyecto_media_items( $post_id );

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

					<!-- Galería con Lightbox (Sprint UX-8, Entregable UX-8.1, D-108: mosaico mixto imagen/video) -->
					<?php if ( ! empty( $media_items ) ) : ?>
						<div class="ce-mt-6">
							<h2 class="ce-mb-4"><?php esc_html_e( 'Galería del Proyecto', 'ce-construction' ); ?></h2>
							<div class="ce-gallery-grid">
								<?php foreach ( $media_items as $ce_pg_index => $ce_pg_item ) : ?>
									<?php if ( 'image' === $ce_pg_item['type'] ) : ?>
										<div class="ce-gallery-item ce-animate-on-scroll" data-full="<?php echo esc_url( $ce_pg_item['full'] ); ?>" data-caption="<?php echo esc_attr( $ce_pg_item['alt'] ); ?>">
											<img src="<?php echo esc_url( $ce_pg_item['thumb'] ); ?>" alt="<?php echo esc_attr( $ce_pg_item['alt'] ); ?>" loading="lazy">
											<div class="ce-gallery-item__icon"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i></div>
										</div>
									<?php else : // 'video-local' o 'video-embed' — mismo ModuleLightbox ya extendido para video desde UX-7.8 (D-077), sin cambios de JS. ?>
										<button
											type="button"
											class="ce-gallery-item ce-gallery-item--video ce-animate-on-scroll"
											aria-label="<?php echo esc_attr( sprintf( /* translators: %s: título del proyecto */ __( 'Reproducir video del proyecto: %s', 'ce-construction' ), get_the_title( $post_id ) ) ); ?>"
											data-lightbox-video="1"
											data-lightbox-type="<?php echo esc_attr( $ce_pg_item['type'] ); ?>"
											data-caption="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"
											<?php if ( 'video-local' === $ce_pg_item['type'] ) : ?>
												data-video-src="<?php echo esc_url( $ce_pg_item['src'] ); ?>"
											<?php else : ?>
												data-embed-target="ce-proyecto-media-embed-<?php echo esc_attr( $post_id . '-' . $ce_pg_index ); ?>"
											<?php endif; ?>
										>
											<?php if ( ! empty( $ce_pg_item['poster'] ) ) : ?>
												<?php // Miniatura ofrecida por el propio proveedor de oEmbed (típico en YouTube/Vimeo) — ver ce_construction_get_proyecto_media_items() en inc/helpers.php. Sin ella (caso habitual de video local), se mantiene el fondo degradado + ícono de Play ya existente. ?>
												<img class="ce-gallery-item--video__poster" src="<?php echo esc_url( $ce_pg_item['poster'] ); ?>" alt="" loading="lazy">
											<?php endif; ?>
											<div class="ce-gallery-item__icon"><i class="fa-solid fa-play" aria-hidden="true"></i></div>
										</button>
										<?php if ( 'video-embed' === $ce_pg_item['type'] ) : ?>
											<?php // El <template> nunca se renderiza visualmente; ModuleLightbox copia su innerHTML al abrir — mismo patrón ya usado por content-testimonio-card.php (UX-7.8). ?>
											<template id="ce-proyecto-media-embed-<?php echo esc_attr( $post_id . '-' . $ce_pg_index ); ?>">
												<?php
												// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Marcado ya generado y filtrado por wp_oembed_get() (WordPress core), mismo criterio ya aplicado en content-testimonio-card.php.
												echo $ce_pg_item['html'];
												?>
											</template>
										<?php endif; ?>
									<?php endif; ?>
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

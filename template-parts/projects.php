<?php
/**
 * Template part: Proyectos (usa el CPT `proyecto`).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'proyecto' ) ) {
	return;
}

$proyectos_query = new WP_Query( array(
	'post_type'      => 'proyecto',
	'posts_per_page' => 6,
	'post_status'    => 'publish',
) );
?>
<section class="ce-section" id="ce-projects">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Nuestro trabajo', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Proyectos Destacados', 'ce-construction' ); ?></h2>
			<p class="ce-section-lead" style="margin-inline:auto;">
				<?php esc_html_e( 'Una muestra de los proyectos que hemos ejecutado con éxito para nuestros clientes.', 'ce-construction' ); ?>
			</p>
		</div>

		<div class="ce-grid ce-grid--3">
			<?php
			while ( $proyectos_query->have_posts() ) :
				$proyectos_query->the_post();
				$cliente   = get_post_meta( get_the_ID(), '_ce_proyecto_cliente', true );
				$ubicacion = get_post_meta( get_the_ID(), '_ce_proyecto_ubicacion', true );
				$fecha     = get_post_meta( get_the_ID(), '_ce_proyecto_fecha', true );
				$estados   = get_the_terms( get_the_ID(), 'estado_proyecto' );
				$estado    = ( $estados && ! is_wp_error( $estados ) ) ? $estados[0]->name : '';
				?>
				<article class="ce-card ce-animate-on-scroll">
					<div class="ce-card__media">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
						<?php endif; ?>
						<?php if ( $estado ) : ?>
							<span class="ce-card__badge"><?php echo esc_html( $estado ); ?></span>
						<?php endif; ?>
					</div>
					<div class="ce-card__body">
						<h3 class="ce-card__title"><?php the_title(); ?></h3>
						<p class="ce-card__text"><?php echo esc_html( ce_get_short_excerpt( get_the_ID(), 16 ) ); ?></p>
						<div class="ce-card__meta">
							<?php if ( $ubicacion ) : ?>
								<span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?php echo esc_html( $ubicacion ); ?></span>
							<?php endif; ?>
							<?php if ( $cliente ) : ?>
								<span><i class="fa-solid fa-user-tie" aria-hidden="true"></i> <?php echo esc_html( $cliente ); ?></span>
							<?php endif; ?>
							<?php if ( $fecha ) : ?>
								<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fecha ) ) ); ?></span>
							<?php endif; ?>
						</div>
						<a href="<?php the_permalink(); ?>" class="ce-card__link ce-mt-3">
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

		<div class="ce-text-center ce-mt-5">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'proyecto' ) ); ?>" class="ce-btn ce-btn--dark">
				<?php esc_html_e( 'Ver todos los proyectos', 'ce-construction' ); ?>
			</a>
		</div>
	</div>
</section>

<?php
/**
 * Single: Miembro del Equipo.
 * Breadcrumbs (HTML) se renderizan globalmente desde header.php.
 * Schema.org (Person) se emite desde inc/seo.php -> ce_construction_schema_person().
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id  = get_the_ID();
	$cargo    = get_post_meta( $post_id, '_ce_equipo_cargo', true );
	$linkedin = get_post_meta( $post_id, '_ce_equipo_linkedin', true );
	?>

	<section class="ce-section">
		<div class="ce-container">
			<div class="ce-max-w-content">

				<div class="ce-team-bio__header ce-animate-on-scroll is-in-view">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="ce-team-bio__photo">
							<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
						</div>
					<?php endif; ?>
					<h1><?php the_title(); ?></h1>
					<?php if ( $cargo ) : ?>
						<p class="ce-team-card__role"><?php echo esc_html( $cargo ); ?></p>
					<?php endif; ?>
					<?php if ( $linkedin ) : ?>
						<a href="<?php echo esc_url( $linkedin ); ?>" class="ce-btn ce-btn--outline ce-btn--sm" style="color:var(--ce-color-primary); border-color: var(--ce-color-neutral-300);" target="_blank" rel="noopener noreferrer">
							<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
							<?php esc_html_e( 'Ver LinkedIn', 'ce-construction' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( get_the_content() ) : ?>
					<article class="ce-service-content ce-animate-on-scroll is-in-view">
						<?php the_content(); ?>
					</article>
				<?php endif; ?>

				<div class="ce-mt-6 ce-text-center">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'miembro_equipo' ) ); ?>" class="ce-card__link">
						<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
						<?php esc_html_e( 'Volver al equipo', 'ce-construction' ); ?>
					</a>
				</div>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
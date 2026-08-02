<?php
/**
 * Single: Cliente.
 * Breadcrumbs (HTML) se renderizan globalmente desde header.php.
 * Schema.org (Organization) se emite desde
 * inc/seo.php -> ce_construction_schema_client_organization().
 *
 * Nota: el CPT `cliente` solo soporta 'title' y 'thumbnail' (ver
 * inc/cpt-clientes.php, ya implementado) — no hay editor de
 * contenido, por lo que esta plantilla no depende de the_content().
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
	$sitio   = get_post_meta( $post_id, '_ce_cliente_sitio', true );
	?>

	<section class="ce-section">
		<div class="ce-container">
			<div class="ce-client-single ce-animate-on-scroll is-in-view">

				<?php if ( has_post_thumbnail() ) : ?>
					<div class="ce-client-single__logo">
						<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
					</div>
				<?php endif; ?>

				<h1><?php the_title(); ?></h1>

				<?php if ( $sitio ) : ?>
					<p class="ce-mt-3">
						<a href="<?php echo esc_url( $sitio ); ?>" class="ce-btn ce-btn--dark" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Visitar sitio web', 'ce-construction' ); ?>
							<i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
						</a>
					</p>
				<?php endif; ?>

				<div class="ce-mt-6">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'cliente' ) ); ?>" class="ce-card__link">
						<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
						<?php esc_html_e( 'Volver a clientes', 'ce-construction' ); ?>
					</a>
				</div>

			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();

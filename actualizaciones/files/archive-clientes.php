<?php
/**
 * Archive: Clientes.
 * Breadcrumbs se renderizan globalmente desde header.php.
 * Requiere 'has_archive' => true en inc/cpt-clientes.php (ver nota
 * añadida ahí mismo en Sprint 5) para ser alcanzable vía URL amigable.
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
	'subtitle' => __( 'Empresas y organizaciones que han confiado en nosotros para ejecutar sus proyectos de construcción.', 'ce-construction' ),
) );
?>

<section class="ce-section">
	<div class="ce-container">
		<?php if ( have_posts() ) : ?>
			<div class="ce-clients-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content-cliente' );
				endwhile;
				?>
			</div>

			<nav class="ce-mt-6" aria-label="<?php esc_attr_e( 'Paginación de clientes', 'ce-construction' ); ?>">
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
					<p><?php esc_html_e( 'Aún no hay clientes publicados. Vuelve pronto.', 'ce-construction' ); ?></p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();

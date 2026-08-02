<?php
/**
 * Template part: Sidebar opcional para archive/single de Proyectos.
 * Uso: get_template_part( 'template-parts/sidebar-proyectos', null,
 *          array( 'exclude' => get_the_ID() ) );
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$exclude = isset( $args['exclude'] ) ? absint( $args['exclude'] ) : 0;

$otros_proyectos = get_posts( array(
	'post_type'      => 'proyecto',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'exclude'        => $exclude ? array( $exclude ) : array(),
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
?>
<aside class="ce-sidebar" aria-label="<?php esc_attr_e( 'Barra lateral de proyectos', 'ce-construction' ); ?>">

	<?php if ( $otros_proyectos ) : ?>
		<div class="ce-card ce-mb-4">
			<div class="ce-card__body">
				<h4 class="ce-mb-3"><?php esc_html_e( 'Otros Proyectos', 'ce-construction' ); ?></h4>
				<ul class="ce-sidebar__list">
					<?php foreach ( $otros_proyectos as $proyecto ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $proyecto ) ); ?>" class="ce-sidebar__link <?php echo ( get_the_ID() === $proyecto->ID ) ? 'is-current' : ''; ?>">
								<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
								<?php echo esc_html( get_the_title( $proyecto ) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<div class="ce-card ce-sidebar__contact-card">
		<div class="ce-card__body ce-text-center">
			<div class="ce-card__icon" style="margin-inline:auto;">
				<i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i>
			</div>
			<h4><?php esc_html_e( '¿Tienes un proyecto en mente?', 'ce-construction' ); ?></h4>
			<p class="ce-card__text"><?php esc_html_e( 'Cuéntanos los detalles y te enviamos una cotización sin costo.', 'ce-construction' ); ?></p>
			<a href="#ce-quote-form" class="ce-btn ce-btn--primary ce-btn--block ce-mb-2">
				<?php esc_html_e( 'Cotizar ahora', 'ce-construction' ); ?>
			</a>
			<?php if ( get_theme_mod( 'ce_phone' ) ) : ?>
				<a href="tel:<?php echo esc_attr( ce_get_phone_href() ); ?>" class="ce-btn ce-btn--outline ce-btn--block" style="color:var(--ce-color-primary); border-color: var(--ce-color-neutral-300);">
					<i class="fa-solid fa-phone" aria-hidden="true"></i>
					<?php echo esc_html( get_theme_mod( 'ce_phone' ) ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

</aside>

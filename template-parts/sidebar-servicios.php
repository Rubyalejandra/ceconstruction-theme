<?php
/**
 * Template part: Sidebar opcional para archive/single de Servicios.
 * Uso: get_template_part( 'template-parts/sidebar-servicios', null,
 *          array( 'exclude' => get_the_ID() ) );
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$exclude = isset( $args['exclude'] ) ? absint( $args['exclude'] ) : 0;

$otros_servicios = get_posts( array(
	'post_type'      => 'servicio',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'exclude'        => $exclude ? array( $exclude ) : array(),
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>
<aside class="ce-sidebar" aria-label="<?php esc_attr_e( 'Barra lateral de servicios', 'ce-construction' ); ?>">

	<?php if ( $otros_servicios ) : ?>
		<div class="ce-card ce-mb-4">
			<div class="ce-card__body">
				<h4 class="ce-mb-3"><?php esc_html_e( 'Todos los Servicios', 'ce-construction' ); ?></h4>
				<ul class="ce-sidebar__list">
					<?php foreach ( $otros_servicios as $servicio ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $servicio ) ); ?>" class="ce-sidebar__link <?php echo ( get_the_ID() === $servicio->ID ) ? 'is-current' : ''; ?>">
								<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
								<?php echo esc_html( get_the_title( $servicio ) ); ?>
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
				<i class="fa-solid fa-headset" aria-hidden="true"></i>
			</div>
			<h4><?php esc_html_e( '¿Necesitas asesoría?', 'ce-construction' ); ?></h4>
			<p class="ce-card__text"><?php esc_html_e( 'Escríbenos y te ayudamos a definir el alcance de tu proyecto.', 'ce-construction' ); ?></p>
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

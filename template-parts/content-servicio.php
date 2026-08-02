<?php
/**
 * Template part: contenido de una tarjeta de Servicio dentro de un loop.
 * Se usa en archive-servicio.php y en la sección de "Servicios
 * relacionados" de single-servicio.php.
 *
 * Debe invocarse DENTRO de un loop estándar (`have_posts()`/`the_post()`).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$enlace = get_post_meta( get_the_ID(), '_ce_enlace_externo', true );
$enlace = $enlace ? $enlace : get_permalink();
?>
<article class="ce-card ce-animate-on-scroll">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="ce-card__media">
			<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
		</div>
	<?php endif; ?>
	<div class="ce-card__body">
		<div class="ce-card__icon">
			<?php ce_render_service_icon( get_the_ID() ); ?>
		</div>
		<h3 class="ce-card__title"><?php the_title(); ?></h3>
		<p class="ce-card__text"><?php echo esc_html( ce_get_short_excerpt( get_the_ID(), 18 ) ); ?></p>
		<a href="<?php echo esc_url( $enlace ); ?>" class="ce-card__link">
			<?php esc_html_e( 'Conocer más', 'ce-construction' ); ?>
			<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
		</a>
	</div>
</article>

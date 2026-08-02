<?php
/**
 * Template part: contenido de una tarjeta de Proyecto dentro de un loop.
 * Se usa en archive-proyecto.php. Debe invocarse DENTRO de un loop
 * estándar (`have_posts()`/`the_post()`).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

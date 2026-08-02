<?php
/**
 * Template part: Hero interno reutilizable para páginas de
 * contenido (archivos/singles), distinto del Hero de portada.
 * Se invoca con argumentos vía get_template_part() (WP 5.5+):
 *
 *   get_template_part( 'template-parts/page-hero', null, array(
 *       'eyebrow'  => __( 'Servicios', 'ce-construction' ),
 *       'title'    => get_the_title(),
 *       'subtitle' => '...',
 *       'image_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
 *   ) );
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = isset( $args['eyebrow'] ) ? $args['eyebrow'] : '';
$title    = isset( $args['title'] ) ? $args['title'] : get_the_title();
$subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : '';
$image_id = isset( $args['image_id'] ) ? absint( $args['image_id'] ) : 0;
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'ce-hero' ) : '';
?>
<section class="ce-page-hero" <?php echo $image_url ? 'style="background-image:url(\'' . esc_url( $image_url ) . '\')"' : ''; ?>>
	<div class="ce-page-hero__overlay"></div>
	<div class="ce-container">
		<div class="ce-page-hero__content ce-text-white ce-animate-on-scroll is-in-view">
			<?php if ( $eyebrow ) : ?>
				<span class="ce-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="ce-page-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

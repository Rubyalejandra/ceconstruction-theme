<?php
/**
 * Template part: Galería con Lightbox.
 * Reúne imágenes de las galerías de proyecto (campo
 * `_ce_proyecto_galeria`, ver inc/meta-boxes.php) para mostrar
 * un mosaico general de trabajos realizados.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gallery_images = array();

if ( ce_cpt_has_posts( 'proyecto' ) ) {
	$proyectos = new WP_Query( array(
		'post_type'      => 'proyecto',
		'posts_per_page' => 8,
		'post_status'    => 'publish',
	) );

	while ( $proyectos->have_posts() ) {
		$proyectos->the_post();
		$ids = ce_get_gallery_ids( get_the_ID() );
		if ( empty( $ids ) && has_post_thumbnail() ) {
			$ids = array( get_post_thumbnail_id() );
		}
		foreach ( $ids as $img_id ) {
			$gallery_images[] = $img_id;
			if ( count( $gallery_images ) >= 8 ) {
				break;
			}
		}
		if ( count( $gallery_images ) >= 8 ) {
			break;
		}
	}
	wp_reset_postdata();
}

if ( empty( $gallery_images ) ) {
	return;
}
?>
<section class="ce-section" id="ce-gallery">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Galería', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Nuestro Trabajo en Imágenes', 'ce-construction' ); ?></h2>
		</div>

		<div class="ce-gallery-grid">
			<?php foreach ( $gallery_images as $img_id ) :
				$thumb = wp_get_attachment_image_url( $img_id, 'ce-card' );
				$full  = wp_get_attachment_image_url( $img_id, 'full' );
				$alt   = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
				if ( ! $thumb ) {
					continue;
				}
				?>
				<div class="ce-gallery-item ce-animate-on-scroll" data-full="<?php echo esc_url( $full ); ?>" data-caption="<?php echo esc_attr( $alt ); ?>">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
					<div class="ce-gallery-item__icon"><i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

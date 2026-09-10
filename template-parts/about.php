<?php
/**
 * Template part: Quiénes Somos.
 * Usa una página estática (si existe, slug "quienes-somos") para
 * contenido editable desde wp-admin; si no existe, usa defaults.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_page = get_page_by_path( 'quienes-somos' );
$about_image_id = $about_page ? get_post_thumbnail_id( $about_page->ID ) : 0;
$about_image = $about_image_id ? wp_get_attachment_image_url( $about_image_id, 'large' ) : '';
$about_content = $about_page ? apply_filters( 'the_content', $about_page->post_content ) : '';
?>
<section class="ce-section" id="ce-about">
	<div class="ce-container">
		<div class="ce-grid ce-grid--2 ce-items-center">

			<div class="ce-animate-on-scroll">
				<?php if ( $about_image ) : ?>
					<img src="<?php echo esc_url( $about_image ); ?>" alt="<?php echo esc_attr( get_the_title( $about_page ) ); ?>" loading="lazy" style="border-radius: var(--ce-radius-lg); box-shadow: var(--ce-shadow-md);">
				<?php else : ?>
					<img src="<?php echo esc_url( CE_THEME_URI . '/assets/img/about-placeholder.jpg' ); ?>" alt="<?php esc_attr_e( 'Equipo de CE Construction en obra', 'ce-construction' ); ?>" loading="lazy" style="border-radius: var(--ce-radius-lg); box-shadow: var(--ce-shadow-md); aspect-ratio: 4/3; object-fit: cover; background: var(--ce-color-neutral-100);">
				<?php endif; ?>
			</div>

			<div class="ce-animate-on-scroll">
				<span class="ce-eyebrow"><?php esc_html_e( 'Quiénes Somos', 'ce-construction' ); ?></span>
				<h2 class="ce-section-title"><?php echo $about_page ? esc_html( get_the_title( $about_page ) ) : esc_html__( 'Una empresa de remodelación con experiencia, valores y visión de futuro', 'ce-construction' ); ?></h2>

				<?php if ( $about_content ) : ?>
					<div class="ce-mb-4"><?php echo wp_kses_post( $about_content ); ?></div>
				<?php else : ?>
					<p class="ce-mb-4">
						<?php esc_html_e( 'Transformamos espacios con calidad, diseño y atención al detalle, creando ambientes que reflejan las ideas y necesidades de nuestros clientes.', 'ce-construction' ); ?>
					</p>
				<?php endif; ?>

				<div class="ce-grid ce-grid--2 ce-gap-4">
					<div>
						<h4><i class="fa-solid fa-bullseye" style="color:var(--ce-color-secondary);"></i> <?php esc_html_e( 'Misión', 'ce-construction' ); ?></h4>
						<p><?php esc_html_e( 'Transformar hogares mediante remodelaciones de alta calidad, ofreciendo soluciones personalizadas, acabados impecables y una experiencia basada en la confianza, el compromiso y la atención a cada detalle.', 'ce-construction' ); ?></p>
					</div>
					<div>
						<h4><i class="fa-solid fa-eye" style="color:var(--ce-color-secondary);"></i> <?php esc_html_e( 'Visión', 'ce-construction' ); ?></h4>
						<p><?php esc_html_e( 'Ser una empresa reconocida por la excelencia en remodelaciones residenciales, convirtiéndonos en la primera opción para quienes buscan calidad, profesionalismo y resultados duraderos.', 'ce-construction' ); ?></p>
					</div>
					<div>
						<h4><i class="fa-solid fa-handshake" style="color:var(--ce-color-secondary);"></i> <?php esc_html_e( 'Valores', 'ce-construction' ); ?></h4>
						<p><?php esc_html_e( 'Excelencia, Integridad, Perfección en los detalles, Compromiso, Responsabilidad, Pasión por transformar espacios', 'ce-construction' ); ?></p>
					</div>
					<div>
						<h4><i class="fa-solid fa-award" style="color:var(--ce-color-secondary);"></i> <?php esc_html_e( 'Experiencia', 'ce-construction' ); ?></h4>
						<p><?php esc_html_e( 'No entregamos un proyecto hasta sentirnos orgullosos del resultado.
Más de una década transformando espacios. Cada remodelación refleja nuestro compromiso con la calidad, la precisión y la satisfacción de nuestros clientes.', 'ce-construction' ); ?></p>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>

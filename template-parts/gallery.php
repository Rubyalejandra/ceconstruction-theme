<?php
/**
 * Template part: Galería del Home (curada) con Lightbox.
 *
 * Sprint UX-8, Entregable UX-8.2 ("Curación de la Galería del Home":
 * proyecto destacado + imagen favorita, ver docs/DECISIONS.md D-109).
 * Reemplaza la selección automática anterior (los primeros proyectos
 * publicados, en orden cronológico, con todas sus imágenes hasta
 * completar 8) por una curación explícita del administrador:
 *   1. Proyecto destacado — checkbox `_ce_proyecto_destacado`
 *      (ce_render_proyecto_fields(), inc/meta-boxes.php).
 *   2. Imagen favorita — flag por ítem `type:image` dentro de
 *      `_ce_proyecto_media` (ce_render_proyecto_gallery(),
 *      inc/meta-boxes.php; D-108).
 * Resolución completa (fallbacks aprobados explícitamente por el
 * usuario antes de este Entregable): ver
 * ce_construction_get_home_gallery_images() en inc/helpers.php,
 * única fuente de la lista de imágenes a mostrar. Este archivo no
 * vuelve a tocar `_ce_proyecto_destacado`/`_ce_proyecto_media`
 * directamente.
 *
 * Auto-ocultado (aprobado): sin ningún proyecto destacado, esa
 * función devuelve un array vacío y esta sección se oculta por
 * completo — mismo criterio ya usado por stats.php/trust-badges.php.
 * NO cae de vuelta al comportamiento "todos los proyectos" que tenía
 * antes de este Entregable.
 *
 * Responsive (aprobado en este mismo Entregable): en desktop/tablet
 * el mosaico se muestra como grid (`.ce-home-gallery__track`, CSS
 * puro, sin JS). En móvil (`max-width: 767.98px`, mismo breakpoint
 * que QA-018/D-039) el mismo marcado se convierte en un carrusel de
 * una imagen por vista, reutilizando `createSliderController()`
 * (ModuleHomeGallerySlider, assets/js/main.js) — la misma fábrica ya
 * usada por ModuleTestimonialSlider/ModuleHeroSlider (D-055), con el
 * mismo botón de pausa/reanudación accesible por teclado/touch que
 * QA-035 (D-102) ya añadió a esa fábrica (WCAG 2.2.2), en vez de un
 * mecanismo nuevo.
 *
 * Los triggers de imagen conservan la clase `.ce-gallery-item` sin
 * ningún cambio (mismo hover/zoom, mismo `data-full`/`data-caption`
 * que usa la galería mixta de single-proyecto.php, D-108) —
 * `ModuleLightbox` los detecta automáticamente sin tocar su selector.
 * Solo el contenedor (`.ce-home-gallery`/`.ce-home-gallery__track`,
 * nuevo y exclusivo del Home) es distinto de `.ce-gallery-grid`
 * (single-proyecto.php), para que este cambio de layout nunca pueda
 * afectar a la galería del Proyecto individual — completamente fuera
 * del alcance de este Entregable.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gallery_images = ce_construction_get_home_gallery_images();

if ( empty( $gallery_images ) ) {
	// Sin ningún proyecto destacado (o destacados sin ninguna imagen
	// resoluble): sección oculta por completo — decisión explícita del
	// usuario para el alcance de UX-8.2 (D-109), sin fallback silencioso
	// al comportamiento anterior a este Entregable.
	return;
}
?>
<section class="ce-section" id="ce-gallery">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Galería', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Nuestro Trabajo en Imágenes', 'ce-construction' ); ?></h2>
		</div>

		<div class="ce-home-gallery-wrap">
			<div class="ce-home-gallery" data-autoplay="6000">
				<div class="ce-home-gallery__track">
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

				<button class="ce-slider-arrow ce-slider-arrow--prev" aria-label="<?php esc_attr_e( 'Imagen anterior', 'ce-construction' ); ?>">
					<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
				</button>
				<button class="ce-slider-arrow ce-slider-arrow--next" aria-label="<?php esc_attr_e( 'Imagen siguiente', 'ce-construction' ); ?>">
					<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
				</button>
			</div>
			<div class="ce-slider-nav"></div>
		</div>
	</div>
</section>

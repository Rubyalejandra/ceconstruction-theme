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
 * 🆕 Sprint UX-7, Entregable UX-7.1 (fase "Optimización UX /
 * Conversión") — Unificación del Hero (Home + interior). Ver
 * DECISIONS.md D-063.
 *
 * Este Hero interno ahora comparte con `template-parts/hero.php`
 * (Home) el mismo `ce_hero_type` (theme_mod global del Customizer) y
 * la misma resolución de video/slider
 * (`ce_construction_get_hero_media_state()`, inc/helpers.php) — sin
 * reimplementar esa lógica en este archivo:
 *   - `ce_hero_type = 'image'` (valor por defecto, comportamiento
 *     histórico sin cambios): sigue usando exclusivamente
 *     `$args['image_id']` (p. ej. la imagen destacada de cada
 *     Página/entrada) — el modo imagen es, a propósito, el único que
 *     NO se comparte con el Home: cada contexto interior mantiene su
 *     propia imagen por página, mientras que Home usa siempre
 *     `ce_hero_image`.
 *   - `ce_hero_type = 'video'` o `'slider'`: usa el mismo video/slides
 *     globales que el Hero de Home (`ce_hero_video`/`ce_hero_slides`)
 *     — mismas capas (`.ce-hero__video`/`.ce-hero-slider`, ya
 *     definidas en `assets/css/main.css` secciones 26/27, sin
 *     depender de `.ce-hero` como padre) y mismo
 *     `ModuleHeroSlider` de `assets/js/main.js` (auto-detecta
 *     `.ce-hero-slider` en el DOM, sin cambios de JS necesarios).
 *     `$args['image_id']` se ignora en estos dos modos, igual que
 *     `ce_hero_image` deja de usarse en el Home cuando el tipo no es
 *     'image'.
 *
 * Overlay: `.ce-page-hero__overlay` ahora usa la misma fuente de
 * gradiente que `.ce-hero__overlay` (`--ce-hero-overlay-gradient`,
 * `assets/css/main.css` sección 10) y el mismo `ce_hero_overlay_opacity`
 * del Customizer — un único control de opacidad para ambos Heros, en
 * vez de dos gradientes definidos por separado.
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

/* -----------------------------------------------------------
 * Sprint UX-7, Entregable UX-7.1: mismo `ce_hero_type` global que
 * el Hero de Home, misma función de resolución compartida. Ver
 * docblock de arriba y DECISIONS.md D-063.
 * --------------------------------------------------------- */
$hero_type  = get_theme_mod( 'ce_hero_type', 'image' );
$hero_media = ce_construction_get_hero_media_state( $hero_type );

$hero_is_video   = $hero_media['is_video'];
$hero_video_url  = $hero_media['video_url'];
$hero_video_mime = $hero_media['video_mime'];
$hero_is_slider  = $hero_media['is_slider'];
$hero_slide_urls = $hero_media['slide_urls'];

$hero_overlay_opacity = get_theme_mod( 'ce_hero_overlay_opacity', '1' );
?>
<section class="ce-page-hero" <?php echo ( ! $hero_is_video && ! $hero_is_slider && $image_url ) ? 'style="background-image:url(\'' . esc_url( $image_url ) . '\')"' : ''; ?>>
	<?php if ( $hero_is_video ) : ?>
		<video class="ce-hero__video" autoplay muted loop playsinline aria-hidden="true">
			<source src="<?php echo esc_url( $hero_video_url ); ?>" <?php echo $hero_video_mime ? 'type="' . esc_attr( $hero_video_mime ) . '"' : ''; ?>>
		</video>
	<?php elseif ( $hero_is_slider ) : ?>
		<div class="ce-hero-slider" aria-hidden="true">
			<div class="ce-hero-slider__track">
				<?php foreach ( $hero_slide_urls as $slide_url ) : ?>
					<div class="ce-hero-slider__slide" style="background-image:url('<?php echo esc_url( $slide_url ); ?>')"></div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="ce-page-hero__overlay" style="--ce-hero-overlay-opacity: <?php echo esc_attr( $hero_overlay_opacity ); ?>;"></div>
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

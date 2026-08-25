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
 * 🆕 Sprint UX-11 (fase "Optimización UX / Conversión") — punto 3
 * del plan aprobado por el usuario. MODIFICA EXPLÍCITAMENTE una
 * parte de la unificación introducida en el Sprint UX-7, Entregable
 * UX-7.1 (D-063): aquella unificación hacía que este Hero interno
 * compartiera el modo video/slider GLOBAL del Home (`ce_hero_type`)
 * — si el Home usaba slider, todas las páginas internas mostraban
 * ese mismo slider en vez de su propia imagen destacada. El usuario
 * reportó esto como un problema (el Hero del Home "se repite" en
 * páginas internas) y aprobó explícitamente revertir esa parte
 * puntual de D-063 — ver DECISIONS.md D-084, que documenta esta
 * modificación de forma explícita, no como una reversión silenciosa.
 *
 * Comportamiento vigente desde este Entregable:
 *   - Este Hero interno usa SIEMPRE una imagen de fondo — nunca
 *     video ni slider, sin importar el valor de `ce_hero_type`
 *     (ese ajuste queda ahora exclusivo del Hero de Home).
 *   - Fuente de la imagen: `ce_construction_get_page_hero_image_url()`
 *     (`inc/helpers.php`) — primero la imagen destacada del
 *     post/página actual (`$args['image_id']`); si no existe, cae a
 *     la imagen de fondo configurada para el Hero de Home
 *     (`ce_hero_image`) como último recurso; si tampoco existe,
 *     sin imagen (fondo de color sólido ya existente,
 *     `assets/css/main.css` sección 20).
 *   - Posición de la imagen configurable por imagen individual
 *     (`ce_construction_get_hero_background_position()`,
 *     `inc/hero-image-position.php`) — punto 4 del plan aprobado.
 *   - Overlay: sigue compartiendo con el Hero de Home la misma
 *     variable `--ce-hero-overlay-gradient` y el mismo
 *     `ce_hero_overlay_opacity` — eso SÍ se conserva de D-063, no
 *     formaba parte del problema reportado. Desde este Entregable el
 *     gradiente en sí es configurable (color/dirección/extensión),
 *     ver DECISIONS.md D-086.
 *
 * La función compartida `ce_construction_get_hero_media_state()`
 * (creada en D-063) NO se elimina — sigue siendo la única fuente de
 * verdad del modo video/slider del Hero de Home
 * (`template-parts/hero.php`), simplemente este archivo deja de
 * invocarla.
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

$hero_image        = ce_construction_get_page_hero_image_url( $image_id );
$image_url         = $hero_image['url'];
$image_position    = $image_url ? ce_construction_get_hero_background_position( $hero_image['attachment_id'] ) : 'center center';

$hero_overlay_opacity  = get_theme_mod( 'ce_hero_overlay_opacity', '1' );
$hero_overlay_gradient = ce_construction_get_hero_overlay_gradient_css();

$page_hero_style_parts = array( '--ce-hero-overlay-gradient: ' . $hero_overlay_gradient . ';' );
if ( $image_url ) {
	$page_hero_style_parts[] = "background-image:url('" . esc_url( $image_url ) . "');";
	$page_hero_style_parts[] = 'background-position: ' . esc_attr( $image_position ) . ';';
}
?>
<section class="ce-page-hero" style="<?php echo esc_attr( implode( ' ', $page_hero_style_parts ) ); ?>">
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

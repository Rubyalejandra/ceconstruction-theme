<?php
/**
 * Template part: card individual de un Testimonio (CPT `testimonio`).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-7, Entregable
 * UX-7.3 ("Aprovechamiento de espacios vacíos en sidebars").
 *
 * Extraído en este Entregable del bloque que ya existía, inline,
 * dentro de template-parts/testimonials.php (sección de Home), para
 * reutilizar exactamente el mismo markup en el nuevo slot opcional
 * de template-parts/sidebar-servicios.php y
 * template-parts/sidebar-proyectos.php (testimonio individual, sin
 * slider) sin duplicarlo — mismo patrón que
 * template-parts/content-faq-accordion.php (ver DECISIONS.md D-048).
 * DEBE invocarse DENTRO de un loop estándar (`have_posts()`/
 * `the_post()`) ya iniciado por quien lo llama; este partial solo
 * imprime la card del testimonio actual.
 *
 * template-parts/testimonials.php no cambia su HTML de salida: solo
 * delega en este partial el bloque `.ce-testimonial-card`, dentro de
 * su `.ce-testimonial-slide` de siempre.
 *
 * @param bool $args['compact'] Opcional. true en el slot de sidebar
 *   (columna angosta, ver .ce-layout-with-sidebar en main.css):
 *   añade el modificador `.ce-testimonial-card--compact` (cita más
 *   corta/pequeña, sin max-width de 720px pensado para el ancho
 *   completo del Home). Default false (comportamiento idéntico al
 *   bloque original de testimonials.php).
 *
 * @param bool $args['video_enabled'] Opcional. Sprint UX-7, Entregable
 *   UX-7.8 (D-077). Default false. Cuando es true Y el testimonio
 *   actual tiene un video válido (`ce_get_testimonio_video()`, ver
 *   inc/helpers.php), la card añade la miniatura/poster + botón Play
 *   que abre el video en `ModuleLightbox`. En false (default, todos
 *   los consumidores existentes sin cambios: teaser del Home, slider,
 *   sidebars) la card se comporta exactamente igual que antes de
 *   UX-7.8, sin importar si el testimonio tiene video guardado — la
 *   existencia del meta nunca activa por sí sola esta capacidad,
 *   solo lo hace este flag explícito. Actualmente solo
 *   `template-parts/testimonials-full.php` (página completa de
 *   Testimonios, UX-10.1) lo pasa en true.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_testimonio_compact       = ! empty( $args['compact'] );
$ce_testimonio_video_enabled = ! empty( $args['video_enabled'] );

$nombre = get_post_meta( get_the_ID(), '_ce_testimonio_nombre', true );
$nombre = $nombre ? $nombre : get_the_title();
$cargo  = get_post_meta( get_the_ID(), '_ce_testimonio_cargo', true );
$rating = (int) get_post_meta( get_the_ID(), '_ce_testimonio_rating', true );
$rating = $rating ? $rating : 5;

// El video solo se resuelve (y solo puede aparecer Play/poster) si el
// contexto de llamada lo solicita explícitamente — ver docblock de
// $args['video_enabled'] arriba. `ce_get_testimonio_video()` ya
// valida el recurso (adjunto realmente de tipo video / URL resoluble
// vía oEmbed) y devuelve null si no hay nada válido que mostrar.
$ce_testimonio_video = $ce_testimonio_video_enabled ? ce_get_testimonio_video( get_the_ID() ) : null;

// Prioridad de poster (punto 7 del alcance de UX-7.8): imagen
// destacada del testimonio primero; si no existe, la miniatura que el
// propio proveedor oEmbed haya ofrecido (solo aplica a video-embed,
// ver ce_get_testimonio_video()). Si ninguna está disponible, el
// botón Play se muestra igualmente sobre un fondo neutro (ver CSS
// `.ce-testimonial-video-trigger--no-poster`) — la ausencia de
// miniatura nunca bloquea la reproducción.
$ce_testimonio_video_poster = '';
if ( $ce_testimonio_video ) {
	if ( has_post_thumbnail() ) {
		$ce_testimonio_video_poster = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
	} elseif ( ! empty( $ce_testimonio_video['poster'] ) ) {
		$ce_testimonio_video_poster = $ce_testimonio_video['poster'];
	}
}
?>
<div class="ce-testimonial-card<?php echo $ce_testimonio_compact ? ' ce-testimonial-card--compact' : ''; ?>">
	<div class="ce-testimonial-card__rating">
		<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
			<i class="fa-solid fa-star" aria-hidden="true" style="<?php echo $i > $rating ? 'opacity:.25;' : ''; ?>"></i>
		<?php endfor; ?>
	</div>

	<?php if ( $ce_testimonio_video ) : ?>
		<div class="ce-testimonial-card__video">
			<button
				type="button"
				class="ce-testimonial-video-trigger<?php echo $ce_testimonio_video_poster ? '' : ' ce-testimonial-video-trigger--no-poster'; ?>"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nombre del autor del testimonio */ __( 'Reproducir video del testimonio de %s', 'ce-construction' ), $nombre ) ); ?>"
				data-lightbox-video="1"
				data-lightbox-type="<?php echo esc_attr( $ce_testimonio_video['type'] ); ?>"
				data-caption="<?php echo esc_attr( $nombre ); ?>"
				<?php if ( 'video-local' === $ce_testimonio_video['type'] ) : ?>
					data-video-src="<?php echo esc_url( $ce_testimonio_video['src'] ); ?>"
				<?php else : ?>
					data-embed-target="ce-testimonio-embed-<?php echo esc_attr( get_the_ID() ); ?>"
				<?php endif; ?>
			>
				<?php if ( $ce_testimonio_video_poster ) : ?>
					<img class="ce-testimonial-video-trigger__poster" src="<?php echo esc_url( $ce_testimonio_video_poster ); ?>" alt="" loading="lazy">
				<?php endif; ?>
				<span class="ce-testimonial-video-trigger__icon" aria-hidden="true"><i class="fa-solid fa-play"></i></span>
			</button>
			<?php if ( 'video-embed' === $ce_testimonio_video['type'] ) : ?>
				<?php // El <template> nunca se renderiza visualmente; ModuleLightbox copia su innerHTML al abrir. No requiere el nonce/hidden-input pattern porque no es un campo de formulario. ?>
				<template id="ce-testimonio-embed-<?php echo esc_attr( get_the_ID() ); ?>">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Marcado ya generado y filtrado por wp_oembed_get() (WordPress core); es el mismo criterio que WordPress ya aplica al insertar un oEmbed dentro de the_content() vía WP_Embed::autoembed() — no es HTML arbitrario introducido por el administrador. esc_html() aquí destruiría el propio embed.
					echo $ce_testimonio_video['html'];
					?>
				</template>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<p class="ce-testimonial-card__quote">&ldquo;<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>&rdquo;</p>
	<div class="ce-testimonial-card__author">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'thumbnail', array( 'loading' => 'lazy', 'alt' => $nombre ) ); ?>
		<?php endif; ?>
		<div>
			<div class="ce-testimonial-card__name"><?php echo esc_html( $nombre ); ?></div>
			<?php if ( $cargo ) : ?>
				<div class="ce-testimonial-card__role"><?php echo esc_html( $cargo ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>

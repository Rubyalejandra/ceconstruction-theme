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
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_testimonio_compact = ! empty( $args['compact'] );

$nombre = get_post_meta( get_the_ID(), '_ce_testimonio_nombre', true );
$nombre = $nombre ? $nombre : get_the_title();
$cargo  = get_post_meta( get_the_ID(), '_ce_testimonio_cargo', true );
$rating = (int) get_post_meta( get_the_ID(), '_ce_testimonio_rating', true );
$rating = $rating ? $rating : 5;
?>
<div class="ce-testimonial-card<?php echo $ce_testimonio_compact ? ' ce-testimonial-card--compact' : ''; ?>">
	<div class="ce-testimonial-card__rating">
		<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
			<i class="fa-solid fa-star" aria-hidden="true" style="<?php echo $i > $rating ? 'opacity:.25;' : ''; ?>"></i>
		<?php endfor; ?>
	</div>
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

<?php
/**
 * Template part: tarjeta genérica de contenido, usada por index.php
 * en los loops de búsqueda y de archivos genéricos (categorías,
 * autor, fecha, o CPTs sin archive-{cpt}.php propio como Testimonios
 * o FAQ). Debe invocarse DENTRO de un loop estándar.
 *
 * Reutiliza ce_get_short_excerpt() (ya existente en inc/helpers.php)
 * y las mismas clases .ce-card que el resto del tema, para mantener
 * coherencia visual sin depender de metadatos específicos de un CPT.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="ce-card ce-animate-on-scroll">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="ce-card__media">
			<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
		</div>
	<?php endif; ?>
	<div class="ce-card__body">
		<h3 class="ce-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<p class="ce-card__text">
			<?php echo esc_html( function_exists( 'ce_get_short_excerpt' ) ? ce_get_short_excerpt( get_the_ID(), 18 ) : wp_trim_words( get_the_excerpt(), 18 ) ); ?>
		</p>
		<a href="<?php the_permalink(); ?>" class="ce-card__link">
			<?php esc_html_e( 'Leer más', 'ce-construction' ); ?>
			<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
		</a>
	</div>
</article>

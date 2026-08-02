<?php
/**
 * Template part: contenido de una tarjeta de Miembro del Equipo
 * dentro de un loop. Se usa en archive-equipo.php.
 * Debe invocarse DENTRO de un loop estándar (`have_posts()`/`the_post()`).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cargo    = get_post_meta( get_the_ID(), '_ce_equipo_cargo', true );
$linkedin = get_post_meta( get_the_ID(), '_ce_equipo_linkedin', true );
?>
<article class="ce-card ce-team-card ce-animate-on-scroll">
	<div class="ce-team-card__media">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'ce-card', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
		<?php else : ?>
			<div class="ce-team-card__placeholder" aria-hidden="true"><i class="fa-solid fa-user"></i></div>
		<?php endif; ?>
	</div>
	<div class="ce-card__body ce-text-center">
		<h3 class="ce-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<?php if ( $cargo ) : ?>
			<p class="ce-team-card__role"><?php echo esc_html( $cargo ); ?></p>
		<?php endif; ?>
		<?php if ( $linkedin ) : ?>
			<a href="<?php echo esc_url( $linkedin ); ?>" class="ce-team-card__social" target="_blank" rel="noopener noreferrer" aria-label="<?php
				/* translators: %s: nombre del miembro del equipo */
				echo esc_attr( sprintf( __( 'Perfil de LinkedIn de %s', 'ce-construction' ), get_the_title() ) );
			?>">
				<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
	</div>
</article>

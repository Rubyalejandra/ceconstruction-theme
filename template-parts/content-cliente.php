<?php
/**
 * Template part: contenido de un item de logo de Cliente dentro
 * de un loop. Se usa en archive-clientes.php, DENTRO del contenedor
 * `.ce-clients-grid` (ver assets/css/main.css sección 22).
 * Debe invocarse DENTRO de un loop estándar (`have_posts()`/`the_post()`).
 *
 * Nota: el CPT `cliente` solo soporta 'title' y 'thumbnail' (ver
 * inc/cpt-clientes.php, ya implementado) — no tiene editor de
 * contenido, por lo que este item se diseña alrededor del logo,
 * sin depender de the_content().
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ce-clients-grid__item ce-animate-on-scroll">
	<a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'ce-thumb', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
		<?php else : ?>
			<span><?php the_title(); ?></span>
		<?php endif; ?>
	</a>
</div>

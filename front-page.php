<?php
/**
 * Front Page — CE Construction.
 * Ensambla las secciones del home en el orden definido en el
 * brief del proyecto. Cada sección vive en su propio archivo
 * dentro de /template-parts (responsabilidad única).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php get_template_part( 'template-parts/hero' ); ?>
<?php get_template_part( 'template-parts/about' ); ?>
<?php get_template_part( 'template-parts/services' ); ?>
<?php get_template_part( 'template-parts/projects' ); ?>
<?php get_template_part( 'template-parts/stats' ); ?>
<?php get_template_part( 'template-parts/why-us' ); ?>
<?php get_template_part( 'template-parts/testimonials' ); ?>
<?php get_template_part( 'template-parts/gallery' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>
<?php get_template_part( 'template-parts/quote-form' ); ?>

<?php
get_footer();

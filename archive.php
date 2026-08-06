<?php
/**
 * archive.php — Archivo genérico.
 *
 * Desde que este archivo existe, WordPress le da prioridad automática
 * sobre index.php para cualquier archivo sin plantilla más específica
 * (categoría, etiqueta, autor, fecha, o un CPT sin su propio
 * archive-{cpt}.php) — comportamiento nativo de la Template Hierarchy.
 * index.php sigue existiendo sin cambios como fallback final para
 * is_search()/is_404()/is_singular().
 *
 * A la fecha de este Entregable, los únicos CPTs de contenido sin
 * archive-{cpt}.php propio son `testimonio` y `ce_faq` (los otros 4:
 * Servicios, Proyectos, Equipo, Clientes, ya tienen su archivo
 * dedicado desde Sprints 3-5). Este archivo también cubre los
 * archivos nativos de WordPress (categoría, etiqueta, autor, fecha)
 * para entradas de blog.
 *
 * Reutiliza exclusivamente template-parts/page-hero.php y
 * template-parts/content-fallback.php (ya usados por index.php),
 * sin duplicar markup.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$archive_eyebrow = __( 'CE Construction', 'ce-construction' );

if ( is_post_type_archive() ) {
	$post_type_obj    = get_post_type_object( get_post_type() );
	$archive_subtitle = $post_type_obj && ! empty( $post_type_obj->description )
		? $post_type_obj->description
		: '';
} elseif ( is_category() || is_tag() ) {
	$archive_subtitle = wp_strip_all_tags( term_description() );
} elseif ( is_author() ) {
	/* translators: %s: nombre del autor */
	$archive_subtitle = sprintf( __( 'Entradas publicadas por %s.', 'ce-construction' ), get_the_author() );
} elseif ( is_date() ) {
	$archive_subtitle = __( 'Entradas del blog publicadas en esta fecha.', 'ce-construction' );
} else {
	$archive_subtitle = '';
}

get_template_part( 'template-parts/page-hero', null, array(
	'eyebrow'  => $archive_eyebrow,
	'title'    => wp_strip_all_tags( get_the_archive_title() ),
	'subtitle' => $archive_subtitle,
) );
?>

<section class="ce-section">
	<div class="ce-container">

		<?php if ( have_posts() ) : ?>

			<div class="ce-grid ce-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content-fallback' );
				endwhile;
				?>
			</div>

			<nav class="ce-mt-6" aria-label="<?php esc_attr_e( 'Paginación', 'ce-construction' ); ?>">
				<?php
				echo paginate_links( array(
					'prev_text' => '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . esc_html__( 'Anterior', 'ce-construction' ),
					'next_text' => esc_html__( 'Siguiente', 'ce-construction' ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',
					'type'      => 'list',
				) );
				?>
			</nav>

		<?php else : ?>

			<?php get_template_part( 'template-parts/no-results' ); ?>

		<?php endif; ?>

	</div>
</section>

<?php
get_footer();

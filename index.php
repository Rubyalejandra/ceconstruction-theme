<?php
/**
 * index.php — Plantilla de respaldo obligatoria de WordPress.
 *
 * Este es el último eslabón de la Template Hierarchy: WordPress la usa
 * para CUALQUIER contexto que no tenga una plantilla más específica.
 * Hoy en día (antes de que existan single.php, page.php, archive.php,
 * search.php o 404.php) esto significa que index.php sirve, en la
 * práctica, como:
 *   - Vista de una entrada de blog individual (is_singular('post'))
 *   - Vista de una página (is_singular('page'))
 *   - Archivos genéricos: categoría, etiqueta, autor, fecha, y los CPT
 *     sin archive-{cpt}.php propio (ej. Testimonios, FAQ)
 *   - Resultados de búsqueda (is_search())
 *   - Página 404 (is_404())
 *
 * Cuando en un sprint futuro se creen single.php/page.php/archive.php/
 * search.php/404.php, WordPress les dará prioridad automáticamente sobre
 * este archivo para sus contextos específicos (así funciona la Template
 * Hierarchy nativa) — este archivo seguirá existiendo como el fallback
 * final, tal como WordPress lo exige.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="ce-section">
	<div class="ce-container">

		<?php if ( is_search() ) : ?>

			<div class="ce-mb-6 ce-animate-on-scroll is-in-view">
				<span class="ce-eyebrow"><?php esc_html_e( 'Búsqueda', 'ce-construction' ); ?></span>
				<h1>
					<?php
					printf(
						/* translators: %s: término de búsqueda */
						esc_html__( 'Resultados de búsqueda para: %s', 'ce-construction' ),
						'<span style="color:var(--ce-color-secondary-text);">' . esc_html( get_search_query() ) . '</span>'
					);
					?>
				</h1>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="ce-grid ce-grid--3">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content-fallback' );
					endwhile;
					?>
				</div>

				<nav class="ce-mt-6" aria-label="<?php esc_attr_e( 'Paginación de resultados', 'ce-construction' ); ?>">
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

		<?php elseif ( is_404() ) : ?>

			<div class="ce-text-center ce-max-w-content ce-animate-on-scroll is-in-view">
				<span class="ce-eyebrow"><?php esc_html_e( 'Error 404', 'ce-construction' ); ?></span>
				<h1><?php esc_html_e( 'Página no encontrada', 'ce-construction' ); ?></h1>
				<p class="ce-mb-5"><?php esc_html_e( 'La página que buscas no existe, fue movida o la URL contiene un error.', 'ce-construction' ); ?></p>
				<?php get_template_part( 'template-parts/no-results' ); ?>
			</div>

		<?php elseif ( is_archive() ) : ?>

			<div class="ce-mb-6 ce-text-center ce-max-w-content ce-animate-on-scroll is-in-view" style="margin-inline:auto;">
				<span class="ce-eyebrow"><?php esc_html_e( 'CE Construction', 'ce-construction' ); ?></span>
				<h1><?php the_archive_title(); ?></h1>
				<?php if ( get_the_archive_description() ) : ?>
					<div class="ce-section-lead" style="margin-inline:auto;"><?php the_archive_description(); ?></div>
				<?php endif; ?>
			</div>

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

		<?php elseif ( is_singular() && have_posts() ) : ?>

			<?php
			while ( have_posts() ) :
				the_post();
				$post_id = get_the_ID();
				?>
				<article <?php post_class( 'ce-max-w-content ce-animate-on-scroll is-in-view' ); ?> style="margin-inline:auto;">

					<header class="ce-mb-4">
						<?php if ( is_singular( 'post' ) ) : ?>
							<span class="ce-eyebrow"><?php esc_html_e( 'Blog', 'ce-construction' ); ?></span>
						<?php endif; ?>
						<h1><?php the_title(); ?></h1>
						<?php if ( is_singular( 'post' ) ) : ?>
							<p class="ce-card__meta" style="border:none; padding:0; margin-top:var(--ce-space-2);">
								<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
								<span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php the_author(); ?></span>
							</p>
						<?php endif; ?>
					</header>

					<?php if ( has_post_thumbnail( $post_id ) ) : ?>
						<div class="ce-mb-5">
							<?php the_post_thumbnail( 'ce-hero', array(
								'loading' => 'lazy',
								'alt'     => get_the_title( $post_id ),
								'style'   => 'border-radius:var(--ce-radius-lg); box-shadow:var(--ce-shadow-md); width:100%; object-fit:cover; aspect-ratio:16/9;',
							) ); ?>
						</div>
					<?php endif; ?>

					<div class="ce-service-content">
						<?php the_content(); ?>
					</div>

				</article>

				<?php
				// Los comentarios se muestran con la plantilla de comentarios
				// que WordPress resuelva (comments.php aún no existe en este
				// tema; hasta que se cree en un sprint de Blog dedicado,
				// WordPress usa su plantilla de comentarios de compatibilidad
				// nativa, funcional pero sin el estilo del sistema de diseño).
				if ( is_singular( 'post' ) && ( comments_open( $post_id ) || get_comments_number( $post_id ) ) ) :
					echo '<div class="ce-max-w-content ce-mt-6" style="margin-inline:auto;">';
					comments_template();
					echo '</div>';
				endif;
				?>
			<?php endwhile; ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/no-results' ); ?>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();

<?php
/**
 * single.php — Entrada individual de blog (post_type 'post').
 *
 * Desde que este archivo existe, WordPress le da prioridad automática
 * sobre index.php para cualquier entrada de blog (comportamiento nativo
 * de la Template Hierarchy) — index.php sigue existiendo como fallback
 * final para el resto de contextos, sin necesidad de modificarlo.
 *
 * Junto con comments.php (mismo Entregable 6B.2), reemplaza el fallback
 * de compatibilidad nativo de WordPress que usaban index.php y page.php
 * para los comentarios, dándoles por fin el estilo del sistema de diseño.
 *
 * Schema.org (BlogPosting) se emite desde
 * inc/seo.php -> ce_construction_schema_blog_post().
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$categories = get_the_category( $post_id );
	?>

	<?php
	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => __( 'Blog', 'ce-construction' ),
		'title'    => get_the_title(),
		'subtitle' => has_excerpt() ? get_the_excerpt() : '',
		'image_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
	) );
	?>

	<section class="ce-section">
		<div class="ce-container">
			<div class="ce-max-w-content">

				<div class="ce-card__meta ce-mb-5" style="border:none; padding:0;">
					<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( get_the_date() ); ?></span>
					<span><i class="fa-regular fa-user" aria-hidden="true"></i> <?php the_author(); ?></span>
					<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
						<span>
							<i class="fa-solid fa-folder" aria-hidden="true"></i>
							<?php
							$cat_links = array();
							foreach ( $categories as $category ) {
								$cat_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
							}
							echo wp_kses_post( implode( ', ', $cat_links ) );
							?>
						</span>
					<?php endif; ?>
				</div>

			</div>

			<?php if ( post_password_required( $post_id ) ) : ?>

				<div class="ce-max-w-content">
					<div class="ce-card ce-animate-on-scroll is-in-view">
						<div class="ce-card__body">
							<?php echo get_the_password_form( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_password_form() ya escapa/genera su propio HTML seguro. ?>
						</div>
					</div>
				</div>

			<?php else : ?>

				<?php
				// 🆕 Sprint UX-6, Entregable UX-6.3: <article> ya NO va dentro
				// de .ce-max-w-content (ver DECISIONS.md D-061). Es hijo
				// directo de .ce-container para que .ce-service-content
				// pueda usar todo su ancho disponible: la clase adicional
				// ce-content-breakout (assets/css/main.css) convierte
				// .ce-service-content en un grid de 3 columnas — el
				// contenido normal de the_content() (párrafos, encabezados,
				// listas...) sigue centrado a 640px (misma lectura de
				// siempre), pero cualquier `[ce_section]` embebido
				// (inc/section-shortcode.php) ocupa el ancho completo del
				// .ce-container, igual que en el Home. Sin este cambio,
				// [ce_section] quedaba encogido a 640px por estar anidado
				// dentro de .ce-max-w-content (bug detectado probando
				// UX-6.2). .ce-max-w-content NO se modificó: sigue igual
				// en los otros ~15 usos del proyecto.
				?>
				<article <?php post_class( 'ce-animate-on-scroll is-in-view' ); ?>>
					<div class="ce-service-content ce-content-breakout">
						<?php
						the_content();

						wp_link_pages( array(
							'before'      => '<div class="ce-mt-4 ce-flex ce-gap-2 ce-flex-wrap">' . esc_html__( 'Páginas:', 'ce-construction' ),
							'after'       => '</div>',
							'link_before' => '<span class="ce-btn ce-btn--sm ce-btn--dark">',
							'link_after'  => '</span>',
						) );
						?>
					</div>
				</article>

				<div class="ce-max-w-content">

					<?php
					$tags = get_the_tags( $post_id );
					if ( $tags && ! is_wp_error( $tags ) ) :
						?>
						<div class="ce-mt-5 ce-flex ce-flex-wrap ce-gap-2">
							<?php foreach ( $tags as $tag ) : ?>
								<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="ce-badge">
									<i class="fa-solid fa-tag" aria-hidden="true"></i> <?php echo esc_html( $tag->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Navegación entre entradas de blog -->
					<?php
					$prev_post = get_previous_post( false );
					$next_post = get_next_post( false );
					if ( $prev_post || $next_post ) :
						?>
						<nav class="ce-service-nav" aria-label="<?php esc_attr_e( 'Navegación entre entradas', 'ce-construction' ); ?>">
							<?php if ( $prev_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" class="ce-service-nav__item ce-service-nav__item--prev">
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></span>
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Entrada anterior', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
									</span>
								</a>
							<?php else : ?>
								<span></span>
							<?php endif; ?>

							<?php if ( $next_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="ce-service-nav__item ce-service-nav__item--next">
									<span>
										<span class="ce-service-nav__label"><?php esc_html_e( 'Siguiente entrada', 'ce-construction' ); ?></span>
										<span class="ce-service-nav__title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
									</span>
									<span class="ce-service-nav__icon"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>

					<?php
					// comments.php (mismo Entregable 6B.2) ya existe: los
					// comentarios de blog ya tienen el estilo completo del
					// sistema de diseño, no el fallback de compatibilidad
					// nativo que usaban index.php/page.php hasta ahora.
					if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) :
						echo '<div class="ce-mt-6">';
						comments_template();
						echo '</div>';
					endif;
					?>

				</div>

			<?php endif; ?>

		</div>
	</section>

	<?php
endwhile;

get_footer();

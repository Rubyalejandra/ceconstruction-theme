<?php
/**
 * page.php — Plantilla para Páginas de WordPress (post_type 'page').
 *
 * Desde que este archivo existe, WordPress le da prioridad automática
 * sobre index.php para cualquier Página (comportamiento nativo de la
 * Template Hierarchy) — index.php sigue existiendo sin cambios como
 * fallback final para el resto de contextos (búsqueda, 404, archivos
 * genéricos), sin necesidad de modificarlo.
 *
 * Mismo patrón ya establecido por single.php (Sprint 6B, Entregable
 * UX-6.1): template-parts/page-hero + the_content(), reutilizando el
 * sistema de diseño existente sin introducir markup nuevo.
 *
 * Alcance explícito de este Entregable (UX-6.1): Página con hero
 * interno + contenido del editor. Sin mecanismo de reutilización de
 * secciones del Home Builder (shortcode) — eso es UX-6.2, no incluido
 * aquí.
 *
 * Reutiliza comments.php (ya genérico para post/page desde el
 * Entregable 6B.2 — ver cabecera de ese archivo) para el caso en que
 * el administrador habilite comentarios en una Página puntual.
 *
 * Schema.org: la rama is_page() de
 * inc/seo.php -> ce_construction_breadcrumbs() ya cubre Páginas (se
 * imprime globalmente desde header.php, sin cambios en este
 * Entregable).
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
	?>

	<?php
	get_template_part( 'template-parts/page-hero', null, array(
		'eyebrow'  => get_bloginfo( 'name' ),
		'title'    => get_the_title(),
		'subtitle' => has_excerpt() ? get_the_excerpt() : '',
		'image_id' => has_post_thumbnail() ? get_post_thumbnail_id() : 0,
	) );
	?>

	<section class="ce-section">
		<div class="ce-container">

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
				// de .ce-max-w-content (ver DECISIONS.md D-061; mismo cambio
				// aplicado en single.php). Es hijo directo de .ce-container
				// para que .ce-service-content pueda usar todo su ancho
				// disponible: la clase adicional ce-content-breakout
				// (assets/css/main.css) convierte .ce-service-content en un
				// grid de 3 columnas — el contenido normal de the_content()
				// sigue centrado a 640px (misma lectura de siempre), pero
				// cualquier `[ce_section]` embebido (inc/section-shortcode.php)
				// ocupa el ancho completo del .ce-container, igual que en el
				// Home. .ce-max-w-content NO se modificó: sigue igual en los
				// otros ~15 usos del proyecto.
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

				<?php
				// Los comentarios en Páginas están desactivados por
				// defecto en WordPress, pero el administrador puede
				// habilitarlos por Página desde el editor — cuando lo
				// hace, ya obtienen el estilo completo del sistema de
				// diseño vía comments.php (genérico post/page desde el
				// Entregable 6B.2), no el fallback de compatibilidad
				// nativo que usaba index.php hasta ahora.
				if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) :
					?>
					<div class="ce-max-w-content">
						<div class="ce-mt-6">
							<?php comments_template(); ?>
						</div>
					</div>
					<?php
				endif;
				?>

			<?php endif; ?>

		</div>
	</section>

	<?php
endwhile;

get_footer();

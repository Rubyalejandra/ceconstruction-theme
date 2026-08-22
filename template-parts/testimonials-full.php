<?php
/**
 * Template part: Testimonios — página completa (grid + paginación).
 *
 * FASE: Optimización UX / Conversión — Sprint UX-10, Entregable UX-10.1
 * ("Página de Testimonios: CPT propio + Google Reviews", Opción
 * Híbrida C). Ver docs/DECISIONS.md D-072 (secuencia frente a UX-7.8,
 * Opción 2 confirmada por el usuario: UX-10 ahora, en paralelo, con
 * el Sprint UX-7 pausado en UX-7.7 aprobado) y D-073 (decisiones de
 * este Entregable).
 *
 * Alcance explícito de UX-10.1 (sin Google todavía — eso es UX-10.3):
 * grid completo + paginación, usando EXCLUSIVAMENTE el CPT propio
 * `testimonio`. Sin campo de fuente ('local'/'google') todavía: ese
 * selector es UX-10.4, y llegará una vez exista UX-10.3.
 *
 * Decisión de diseño confirmada explícitamente por el usuario para
 * este Entregable (resolviendo la pregunta abierta de
 * docs/UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4): Página normal de
 * WordPress (`page.php`, ya existente desde UX-6.1) +
 * `[ce_section key="testimonials_full"]` (mecanismo ya existente
 * desde UX-6.2, `inc/section-shortcode.php`) — NO
 * `archive-testimonio.php` nativo. Por eso `inc/cpt-testimonios.php`
 * NO se modifica en este Entregable (`has_archive` permanece `false`,
 * sin cambios): el "hero interno" pedido en el criterio de aceptación
 * ya lo resuelve `page.php` (reutiliza `template-parts/page-hero.php`
 * con el título/extracto/imagen destacada de la Página que el
 * administrador cree, p. ej. "Testimonios") — este archivo no
 * duplica ese hero, solo añade su propio encabezado de sección
 * (eyebrow + título), igual que el resto de secciones registradas en
 * `ce_construction_home_sections()` que también se usan por fuera del
 * Home (`team.php`, `clients.php`, `faq.php`, `trust-badges.php`), de
 * modo que esta plantilla sea igual de válida si en el futuro se
 * reutiliza en un contexto sin `page-hero` (p. ej. otra Página, o el
 * propio Home si el administrador la activa ahí).
 *
 * Sprint UX-7, Entregable UX-7.8 (D-077): esta es la ÚNICA plantilla
 * que pasa `$args['video_enabled'] = true` a `content-testimonio-card.php`
 * (ver docblock de ese archivo). Es la razón por la que UX-7.8 pudo
 * retomarse aquí sin reabrir ni modificar el resto de esta plantilla
 * — la extensión de video vive enteramente dentro del partial de la
 * card, activada solo desde este único punto de llamada.
 *
 * Reutiliza sin cambios: `ce_cpt_has_posts()`, `content-testimonio-card.php`
 * (`$args['compact'] = true`, misma card que ya usan el teaser del
 * Home, los sidebars de Servicios/Proyectos, y el selector aleatorio
 * de sidebar), `.ce-grid.ce-grid--3` (mismo patrón que
 * `archive-clientes.php`/`archive-servicio.php`) y
 * `template-parts/no-results.php` (estado sin contenido). Cero CSS
 * nuevo: `.ce-testimonial-card--compact` (UX-7.3) ya elimina el
 * `max-width:720px`/centrado pensado para el slider de ancho
 * completo, por lo que encaja igual de bien dentro de una celda de
 * grid.
 *
 * Paginación: a diferencia de `archive-clientes.php` (que pagina
 * sobre la consulta PRINCIPAL de un archivo nativo de WordPress),
 * este grid vive dentro del contenido de una Página normal —
 * `$wp_query` principal es el de esa Página (siempre 1 resultado),
 * no el de este WP_Query secundario. Se usa el mismo criterio
 * documentado para paginar loops secundarios embebidos en contenido
 * (`get_query_var('paged')`, con fallback a `get_query_var('page')`
 * — esta última es la que WordPress expone para la paginación nativa
 * de `<!--nextpage-->` dentro de `the_content()`, reutilizada aquí
 * como número de página del grid ya que la Página de Testimonios no
 * la necesita para su propio contenido) y `paginate_links()` con
 * `base`/`format` explícitos (en vez de los defaults, pensados para
 * la consulta principal) — ver DECISIONS.md D-073 para el detalle
 * completo y las alternativas descartadas.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ce_cpt_has_posts( 'testimonio' ) ) {
	return;
}

$ce_testimonials_full_paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
$ce_testimonials_full_paged = $ce_testimonials_full_paged ? max( 1, absint( $ce_testimonials_full_paged ) ) : 1;

$testimonios_full_query = new WP_Query( array(
	'post_type'      => 'testimonio',
	'posts_per_page' => 9,
	'post_status'    => 'publish',
	'paged'          => $ce_testimonials_full_paged,
) );
?>
<section class="ce-section" id="ce-testimonials-full">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Testimonios', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Todos Nuestros Testimonios', 'ce-construction' ); ?></h2>
		</div>

		<?php if ( $testimonios_full_query->have_posts() ) : ?>

			<div class="ce-grid ce-grid--3 ce-mt-6">
				<?php
				while ( $testimonios_full_query->have_posts() ) :
					$testimonios_full_query->the_post();
					// 'video_enabled' => true (UX-7.8, D-077): único
					// consumidor de content-testimonio-card.php que
					// activa la capacidad de video — ver docblock de
					// ese archivo y el bloque de arriba.
					get_template_part( 'template-parts/content-testimonio-card', null, array(
						'compact'       => true,
						'video_enabled' => true,
					) );
				endwhile;
				?>
			</div>

			<?php if ( $testimonios_full_query->max_num_pages > 1 ) : ?>
				<nav class="ce-mt-6" aria-label="<?php esc_attr_e( 'Paginación de testimonios', 'ce-construction' ); ?>">
					<?php
					echo paginate_links( array(
						'base'      => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ),
						'format'    => '?paged=%#%',
						'current'   => $ce_testimonials_full_paged,
						'total'     => $testimonios_full_query->max_num_pages,
						'prev_text' => '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . esc_html__( 'Anterior', 'ce-construction' ),
						'next_text' => esc_html__( 'Siguiente', 'ce-construction' ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',
						'type'      => 'list',
					) );
					?>
				</nav>
			<?php endif; ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/no-results' ); ?>

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
</section>

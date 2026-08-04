<?php
/**
 * 404.php — Página de error "no encontrado".
 *
 * Desde que este archivo existe, WordPress le da prioridad automática
 * sobre index.php para cualquier URL que no resuelva en contenido real
 * (comportamiento nativo de la Template Hierarchy) — index.php sigue
 * existiendo como fallback final para el resto de contextos, sin
 * necesidad de modificarlo. Es, en esencia, la rama is_404() de
 * index.php extraída a su propio archivo dedicado, con una experiencia
 * visual más completa (numeral 404, enlaces rápidos de exploración)
 * en vez del mensaje mínimo que index.php usaba como fallback genérico.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Refuerzo defensivo: WordPress ya envía la cabecera HTTP 404 correcta
// al resolver is_404() en el query principal, pero se declara aquí de
// forma explícita (patrón estándar en temas de WordPress) para
// garantizar que ningún filtro/plugin de terceros la sobreescriba a 200.
status_header( 404 );
nocache_headers();

get_header();
?>

<section class="ce-section">
	<div class="ce-container">

		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll is-in-view" style="margin-inline:auto;">
			<div style="font-family:var(--ce-font-heading); font-weight:800; font-size:clamp(4rem, 12vw, 8rem); line-height:1; color:var(--ce-color-secondary-text); opacity:0.9;">
				404
			</div>
			<span class="ce-eyebrow"><?php esc_html_e( 'Error 404', 'ce-construction' ); ?></span>
			<h1><?php esc_html_e( 'Página no encontrada', 'ce-construction' ); ?></h1>
			<p class="ce-mb-6"><?php esc_html_e( 'La página que buscas no existe, fue movida o la URL contiene un error. Intenta buscar lo que necesitas o explora las siguientes secciones.', 'ce-construction' ); ?></p>

			<?php get_template_part( 'template-parts/no-results' ); ?>
		</div>

		<?php
		$quick_links = array();

		if ( post_type_exists( 'servicio' ) && function_exists( 'ce_cpt_has_posts' ) && ce_cpt_has_posts( 'servicio' ) ) {
			$quick_links[] = array(
				'icon'  => 'fa-solid fa-trowel',
				'title' => __( 'Servicios', 'ce-construction' ),
				'text'  => __( 'Conoce todo lo que hacemos por tu proyecto.', 'ce-construction' ),
				'url'   => get_post_type_archive_link( 'servicio' ),
			);
		}
		if ( post_type_exists( 'proyecto' ) && function_exists( 'ce_cpt_has_posts' ) && ce_cpt_has_posts( 'proyecto' ) ) {
			$quick_links[] = array(
				'icon'  => 'fa-solid fa-building',
				'title' => __( 'Proyectos', 'ce-construction' ),
				'text'  => __( 'Descubre nuestro trabajo ya entregado.', 'ce-construction' ),
				'url'   => get_post_type_archive_link( 'proyecto' ),
			);
		}
		if ( post_type_exists( 'miembro_equipo' ) && function_exists( 'ce_cpt_has_posts' ) && ce_cpt_has_posts( 'miembro_equipo' ) ) {
			$quick_links[] = array(
				'icon'  => 'fa-solid fa-people-group',
				'title' => __( 'Equipo', 'ce-construction' ),
				'text'  => __( 'Conoce a los profesionales detrás de cada obra.', 'ce-construction' ),
				'url'   => get_post_type_archive_link( 'miembro_equipo' ),
			);
		}
		$quick_links[] = array(
			'icon'  => 'fa-solid fa-house',
			'title' => __( 'Inicio', 'ce-construction' ),
			'text'  => __( 'Vuelve a la página principal del sitio.', 'ce-construction' ),
			'url'   => home_url( '/' ),
		);

		if ( $quick_links ) :
			?>
			<div class="ce-mt-6">
				<h2 class="ce-text-center ce-mb-5"><?php esc_html_e( 'Quizás te interese', 'ce-construction' ); ?></h2>
				<div class="ce-grid ce-grid--3">
					<?php foreach ( $quick_links as $link ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="ce-card ce-animate-on-scroll" style="text-decoration:none;">
							<div class="ce-card__body">
								<div class="ce-card__icon">
									<i class="<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></i>
								</div>
								<h3 class="ce-card__title"><?php echo esc_html( $link['title'] ); ?></h3>
								<p class="ce-card__text"><?php echo esc_html( $link['text'] ); ?></p>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		endif;
		?>

	</div>
</section>

<?php
get_footer();

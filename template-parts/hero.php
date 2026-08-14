<?php
/**
 * Template part: Hero.
 * Controlado 100% desde el Customizer (inc/customizer.php).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_image_id = get_theme_mod( 'ce_hero_image' );
$hero_image_url = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'ce-hero' ) : '';

$title    = get_theme_mod( 'ce_hero_title', __( 'Construimos con precisión, entregamos con confianza', 'ce-construction' ) );
$subtitle = get_theme_mod( 'ce_hero_subtitle', __( 'Más de una década ejecutando proyectos residenciales, comerciales e industriales con los más altos estándares de calidad y seguridad.', 'ce-construction' ) );
$btn1_text = get_theme_mod( 'ce_hero_btn1_text', __( 'Cotización Gratuita', 'ce-construction' ) );
$btn1_url  = get_theme_mod( 'ce_hero_btn1_url', '' );
if ( '' === $btn1_url ) {
	// CORRECCIÓN (revisión post-entrega de UX-3.1, ver DECISIONS.md D-050):
	// mismo caso que $btn_url en template-parts/cta.php — ce_hero_btn1_url
	// tampoco tiene 'default' propio en su add_setting() (ver el foreach
	// de $hero_text_fields más arriba en este mismo archivo), así que
	// get_theme_mod( 'ce_hero_btn1_url', ce_get_quote_cta_url() ) dejaba
	// de aplicar el destino centralizado en cuanto el theme_mod quedaba
	// guardado como '' tras cualquier publicación del Customizer.
	$btn1_url = ce_get_quote_cta_url();
}
$btn2_text = get_theme_mod( 'ce_hero_btn2_text', __( 'Ver Proyectos', 'ce-construction' ) );
$btn2_url  = get_theme_mod( 'ce_hero_btn2_url', post_type_exists( 'proyecto' ) ? get_post_type_archive_link( 'proyecto' ) : '#proyectos' );
?>
<section class="ce-hero" id="ce-hero" <?php echo $hero_image_url ? 'style="background-image:url(\'' . esc_url( $hero_image_url ) . '\')"' : ''; ?>>
	<div class="ce-hero__overlay"></div>
	<div class="ce-container">
		<div class="ce-hero__content ce-text-white">
			<span class="ce-eyebrow"><?php esc_html_e( 'CE Construction', 'ce-construction' ); ?></span>
			<h1><?php echo esc_html( $title ); ?></h1>
			<p class="ce-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<div class="ce-hero__actions">
				<?php if ( $btn1_url ) : ?>
					<a href="<?php echo esc_url( $btn1_url ); ?>" class="ce-btn ce-btn--primary">
						<i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i>
						<?php echo esc_html( $btn1_text ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( $btn2_url ); ?>" class="ce-btn ce-btn--outline">
					<i class="fa-solid fa-building" aria-hidden="true"></i>
					<?php echo esc_html( $btn2_text ); ?>
				</a>
			</div>
		</div>
	</div>
	<a href="#ce-about" class="ce-hero__scroll" aria-label="<?php esc_attr_e( 'Desplázate hacia abajo', 'ce-construction' ); ?>">
		<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
	</a>
</section>

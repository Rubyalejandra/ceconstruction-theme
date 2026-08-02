<?php
/**
 * Template part: CTA (Call To Action).
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = get_theme_mod( 'ce_cta_title', __( '¿Listo para construir tu próximo proyecto?', 'ce-construction' ) );
$text     = get_theme_mod( 'ce_cta_text', __( 'Solicita una cotización gratuita y un asesor se pondrá en contacto contigo en menos de 24 horas.', 'ce-construction' ) );
$btn_text = get_theme_mod( 'ce_cta_btn_text', __( 'Solicitar Cotización', 'ce-construction' ) );
$btn_url  = get_theme_mod( 'ce_cta_btn_url', '#ce-quote-form' );
?>
<section class="ce-section">
	<div class="ce-container">
		<div class="ce-cta ce-animate-on-scroll">
			<div class="ce-cta__content">
				<h2 class="ce-text-white"><?php echo esc_html( $title ); ?></h2>
				<p class="ce-text-white" style="opacity:.85;"><?php echo esc_html( $text ); ?></p>
				<div class="ce-cta__actions">
					<a href="<?php echo esc_url( $btn_url ); ?>" class="ce-btn ce-btn--primary">
						<i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
						<?php echo esc_html( $btn_text ); ?>
					</a>
					<?php if ( ce_get_whatsapp_number() ) : ?>
						<a href="https://wa.me/<?php echo esc_attr( ce_get_whatsapp_number() ); ?>" target="_blank" rel="noopener noreferrer" class="ce-btn ce-btn--outline">
							<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
							<?php esc_html_e( 'Escríbenos', 'ce-construction' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>

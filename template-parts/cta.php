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
$btn_url  = get_theme_mod( 'ce_cta_btn_url', '' );
if ( '' === $btn_url ) {
	// CORRECCIÓN (revisión post-entrega de UX-3.1, ver DECISIONS.md D-050):
	// get_theme_mod()'s segundo argumento solo actúa como default ANTES
	// de que el theme_mod se haya guardado alguna vez. `ce_cta_btn_url`
	// no tiene 'default' propio en su add_setting() (ver el foreach de
	// $cta_fields más arriba en este mismo archivo), así que en cuanto
	// el administrador publica cualquier cambio desde el Customizer,
	// WordPress persiste este campo como '' — y a partir de ahí
	// get_theme_mod( 'ce_cta_btn_url', ce_get_quote_cta_url() ) devolvía
	// siempre '' (el valor ya guardado), ignorando el destino
	// centralizado. Se resuelve tratando explícitamente la cadena vacía
	// como "sin personalizar", que es la intención real: un campo de
	// texto vacío en el Customizer significa "usa el comportamiento por
	// defecto", no "oculta el botón a propósito" (para eso existe el
	// modo 'disabled' de ce_quote_form_mode).
	$btn_url = ce_get_quote_cta_url();
}
?>
<section class="ce-section">
	<div class="ce-container">
		<div class="ce-cta ce-animate-on-scroll">
			<div class="ce-cta__content">
				<h2 class="ce-text-white"><?php echo esc_html( $title ); ?></h2>
				<p class="ce-text-white" style="opacity:.85;"><?php echo esc_html( $text ); ?></p>
				<div class="ce-cta__actions">
					<?php if ( $btn_url ) : ?>
						<a href="<?php echo esc_url( $btn_url ); ?>" class="ce-btn ce-btn--primary">
							<i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
							<?php echo esc_html( $btn_text ); ?>
						</a>
					<?php endif; ?>
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

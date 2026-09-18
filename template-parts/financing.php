<?php
/**
 * Template part: Financiamiento / Opciones de Pago.
 *
 * Sprint UX-7, Entregable UX-7.9 (fase "Optimización UX / Conversión").
 * Ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4/§8.8 (benchmark
 * DayBrook Homes / Re-Bath: franja de financiamiento con CTA propio,
 * ej. "0% interés 12 meses", "Pre-aprobación sin afectar tu crédito")
 * y docs/DECISIONS.md D-078.
 *
 * Alcance explícito de este Entregable (mismo patrón EXACTO ya usado
 * por template-parts/cta.php en su variante primaria, UX-5.1, sin
 * inventar un patrón nuevo): 4 campos de texto vía Customizer
 * (título, texto, texto de botón, URL de botón), reutilizando tal
 * cual los mismos componentes visuales (.ce-cta / .ce-cta__content /
 * .ce-cta__actions / .ce-btn) y el mismo fallback de URL de botón
 * (ce_get_quote_cta_url()) ya usado por cta.php. A diferencia de
 * 'cta_secondary' (D-056), esta sección NO reutiliza cta.php como
 * archivo — es un template-part propio y nuevo, tal como especifica
 * el plan ("nueva sección del Home Builder (template-parts/financing.php)"),
 * porque su propósito (financiamiento) es distinto del de un CTA
 * genérico, aunque comparta el mismo patrón de campos y de markup.
 *
 * Se registra en inc/home-builder.php para quedar disponible también
 * vía [ce_section key="financing"] (UX-6.2), igual que UX-7.7/UX-7.8.
 * No forma parte del orden activo por defecto del Home (mismo criterio
 * que team/clients/faq/trust_badges/google_reviews): el administrador
 * la activa y posiciona explícitamente desde "CE: Home Builder".
 *
 * Sin icono ni color de botón configurables (a diferencia de 'cta'/
 * 'cta_secondary' tras UX-7.4): el plan de este Entregable cita
 * explícitamente el patrón de UX-5.1 (4 campos de texto), no las
 * extensiones posteriores de UX-7.4 — el icono queda fijo en el
 * markup, igual que 'cta'/'cta_secondary' antes de que existiera
 * UX-7.4.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_theme_mod( 'ce_financing_title', __( 'Opciones de financiamiento a tu medida', 'ce-construction' ) );
$text  = get_theme_mod( 'ce_financing_text', __( 'Habla con nosotros sobre planes de pago flexibles y pre-aprobación sin afectar tu historial crediticio.', 'ce-construction' ) );
$btn_text = get_theme_mod( 'ce_financing_btn_text', __( 'Conocer opciones de pago', 'ce-construction' ) );
$btn_url  = get_theme_mod( 'ce_financing_btn_url', '' );
if ( '' === $btn_url ) {
	// Mismo fix de D-050/D-056: get_theme_mod()'s segundo argumento
	// solo actúa como default ANTES de que el theme_mod se haya
	// guardado alguna vez. En cuanto el administrador publica
	// cualquier cambio desde el Customizer, WordPress persiste el
	// campo como '' — se trata explícitamente esa cadena vacía como
	// "sin personalizar", igual que ya hace template-parts/cta.php.
	$btn_url = ce_get_quote_cta_url();
}
?>
<section class="ce-section" id="ce-financing">
	<div class="ce-container">
		<div class="ce-cta ce-animate-on-scroll">
			<div class="ce-cta__content">
				<h2 class="ce-text-white"><?php echo esc_html( $title ); ?></h2>
				<p class="ce-text-white" style="opacity:.85;"><?php echo esc_html( $text ); ?></p>
				<div class="ce-cta__actions">
					<?php if ( $btn_url ) : ?>
						<a href="<?php echo esc_url( $btn_url ); ?>" class="ce-btn ce-btn--primary">
							<i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
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

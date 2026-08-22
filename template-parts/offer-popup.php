<?php
/**
 * Template part: Popup de Oferta.
 *
 * Sprint UX-7, Entregable UX-7.10 (fase "Optimización UX / Conversión")
 * — ÚLTIMO Entregable del Sprint UX-7. Ver docs/DECISIONS.md D-079 y
 * docs/UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4.
 *
 * Componente INDEPENDIENTE del Formulario de Cotización (UX-3): no lo
 * modifica, no lo fusiona, no lo duplica. Su botón puede:
 *   a) abrir el modal existente de Cotización (`#ce-quote-modal`,
 *      impreso por footer.php) — vía el mismo `href` que ya devuelve
 *      `ce_get_quote_cta_url()` para cta.php/financing.php, capturado
 *      por el listener genérico ya existente `ModuleSmoothScroll`
 *      (assets/js/main.js) que abre cualquier ancla `.ce-modal-overlay`;
 *      o
 *   b) ir a una URL propia (interna o externa).
 *
 * Este archivo solo IMPRIME el markup + su configuración (como
 * `data-*` en el propio overlay, sin tocar wp_localize_script) si
 * `ce_get_offer_popup_data()` (inc/helpers.php) devuelve datos — esa
 * función concentra toda la condición de "activado y configurado".
 * Toda la lógica de temporización/cookies/apertura vive en
 * `ModuleOfferPopup` (assets/js/main.js).
 *
 * Se imprime UNA VEZ en footer.php (mismo punto que el modal de
 * Cotización), fuera del Home Builder — no es una sección de página,
 * es un overlay global. Reutiliza las clases `.ce-modal-overlay`/
 * `.ce-modal`/`.ce-modal__close` ya existentes (mismo CSS, sin
 * duplicar estilos de modal) — ModuleOfferPopup abre/cierra este
 * overlay reutilizando `ModuleModals.open()`/`close()` tal cual, sin
 * reimplementar esa mecánica.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_offer = ce_get_offer_popup_data();
if ( ! $ce_offer ) {
	return;
}
?>
<div
	class="ce-modal-overlay ce-offer-popup"
	id="ce-offer-popup"
	data-delay="<?php echo esc_attr( $ce_offer['delay_seconds'] ); ?>"
	data-dismiss-minutes="<?php echo esc_attr( $ce_offer['dismiss_minutes'] ); ?>"
	data-convert-minutes="<?php echo esc_attr( $ce_offer['convert_minutes'] ); ?>"
>
	<div class="ce-modal ce-modal--offer" role="dialog" aria-modal="true" aria-labelledby="ce-offer-popup-title">
		<button type="button" class="ce-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'ce-construction' ); ?>">
			<i class="fa-solid fa-xmark" aria-hidden="true"></i>
		</button>
		<?php if ( $ce_offer['icon'] ) : ?>
			<span class="ce-offer-popup__icon" aria-hidden="true">
				<i class="<?php echo esc_attr( $ce_offer['icon'] ); ?>"></i>
			</span>
		<?php endif; ?>
		<?php if ( $ce_offer['badge_text'] ) : ?>
			<span class="ce-offer-popup__badge"><?php echo esc_html( $ce_offer['badge_text'] ); ?></span>
		<?php endif; ?>
		<h3 id="ce-offer-popup-title"><?php echo esc_html( $ce_offer['title'] ); ?></h3>
		<?php if ( $ce_offer['text'] ) : ?>
			<p class="ce-modal__text"><?php echo esc_html( $ce_offer['text'] ); ?></p>
		<?php endif; ?>
		<a href="<?php echo esc_url( $ce_offer['btn_url'] ); ?>" id="ce-offer-popup-cta" class="ce-btn ce-btn--primary ce-btn--block ce-offer-popup__cta">
			<?php echo esc_html( $ce_offer['btn_text'] ); ?>
		</a>
	</div>
</div>

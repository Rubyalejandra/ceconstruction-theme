<?php
/**
 * Template part: CTA (Call To Action).
 *
 * Sprint UX-5, Entregable UX-5.1 (fase "Optimización UX / Conversión"):
 * admite una segunda variante de contenido, vía `$args['variant'] =
 * 'secondary'`, para poder registrarse DOS VECES en el Home Builder
 * (claves 'cta' y 'cta_secondary', ver inc/home-builder.php) sin
 * duplicar este archivo — cada variante lee su propio conjunto de
 * theme_mods (prefijo 'ce_cta_' para la primaria, 'ce_cta2_' para la
 * secundaria), configurables por separado en el Customizer. Permite
 * construir recorridos de conversión con más de un punto de CTA
 * (Estrategias A/B del brief) solo con el Home Builder. Ver
 * DECISIONS.md D-056.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_cta_is_secondary = isset( $args['variant'] ) && 'secondary' === $args['variant'];
$ce_cta_prefix        = $ce_cta_is_secondary ? 'ce_cta2_' : 'ce_cta_';

$title    = get_theme_mod( $ce_cta_prefix . 'title', $ce_cta_is_secondary
	? __( '¿Prefieres que te contactemos nosotros?', 'ce-construction' )
	: __( '¿Listo para construir tu próximo proyecto?', 'ce-construction' )
);
$text     = get_theme_mod( $ce_cta_prefix . 'text', $ce_cta_is_secondary
	? __( 'Déjanos tus datos y un asesor se comunicará contigo para resolver tus dudas antes de que decidas.', 'ce-construction' )
	: __( 'Solicita una cotización gratuita y un asesor se pondrá en contacto contigo en menos de 24 horas.', 'ce-construction' )
);
$btn_text = get_theme_mod( $ce_cta_prefix . 'btn_text', __( 'Solicitar Cotización', 'ce-construction' ) );
$btn_url  = get_theme_mod( $ce_cta_prefix . 'btn_url', '' );
if ( '' === $btn_url ) {
	// Mismo fix de D-050 (UX-3.1), ahora parametrizado por prefijo
	// para cubrir igual a las 2 variantes: get_theme_mod()'s segundo
	// argumento solo actúa como default ANTES de que el theme_mod se
	// haya guardado alguna vez. Ni 'ce_cta_btn_url' ni 'ce_cta2_btn_url'
	// tienen 'default' propio en su add_setting() (inc/customizer.php),
	// así que en cuanto el administrador publica cualquier cambio
	// desde el Customizer, WordPress persiste el campo como '' — y a
	// partir de ahí el segundo argumento de get_theme_mod() deja de
	// aplicarse. Se resuelve tratando explícitamente la cadena vacía
	// como "sin personalizar".
	$btn_url = ce_get_quote_cta_url();
}
?>
<section class="ce-section" id="<?php echo esc_attr( $ce_cta_is_secondary ? 'ce-cta-secondary' : 'ce-cta' ); ?>">
	<div class="ce-container">
		<div class="ce-cta ce-animate-on-scroll<?php echo $ce_cta_is_secondary ? ' ce-cta--secondary' : ''; ?>">
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

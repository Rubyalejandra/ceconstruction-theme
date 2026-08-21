<?php
/**
 * Template part: Google Reviews (Trustindex) — sección independiente.
 *
 * FASE: Optimización UX / Conversión — Sprint UX-10, Entregable UX-10.3.
 * Ver docs/DECISIONS.md D-075 (corrige la premisa funcional de D-072/
 * D-073: "mismo componente visual para ambas fuentes" nunca fue un
 * requisito independiente) y D-076 (implementación de este Entregable).
 *
 * Alcance explícito: esta sección embebe tal cual el código de embed/
 * shortcode que el propio Trustindex genera para la cuenta del
 * cliente (script/div/iframe, según el tipo de widget elegido en su
 * dashboard) — el tema NO se conecta a ninguna API de Trustindex ni
 * de Google, NO reformatea las reseñas, y NO las hace pasar por
 * `content-testimonio-card.php` ni por el CPT `testimonio`. Trustindex
 * sigue siendo, en todo momento, el proveedor y presentador visual
 * completo de sus propias reseñas.
 *
 * Fuente de datos: theme_mod `ce_google_reviews_embed` (textarea,
 * sección "CE: Google Reviews (Trustindex)" del Customizer),
 * saneado con `ce_construction_sanitize_google_reviews_embed()`
 * (inc/customizer.php) — una lista blanca explícita de etiquetas/
 * atributos (script/div/iframe/span/a) que preserva el `<script>`
 * que Trustindex suele requerir sin abrir la puerta a HTML/JS
 * arbitrario fuera de esa lista.
 *
 * Sin ningún embed configurado, la sección se oculta por completo
 * (mismo criterio de auto-ocultado ya usado por
 * template-parts/trust-badges.php, D-071, y template-parts/stats.php,
 * D-070) — a diferencia de "testimonials"/"testimonials_full" (CPT
 * propio, sin cambios en este Entregable), esta clave es
 * deliberadamente independiente: no hay ninguna rama condicional
 * compartida entre ambas fuentes, ni aquí ni en
 * content-testimonio-card.php.
 *
 * Registrada en el Home Builder (inc/home-builder.php, clave
 * 'google_reviews') con el mismo mecanismo que el resto de secciones,
 * por lo que queda automáticamente disponible también vía
 * `[ce_section key="google_reviews"]` (UX-6.2) — incluida, si el
 * administrador lo decide, dentro de la propia Página de Testimonios
 * de UX-10.1, sin que eso implique ningún acoplamiento de código
 * entre ambas secciones. NO forma parte del orden activo por defecto
 * del Home (mismo criterio que team/clients/faq/trust_badges): el
 * administrador la activa y la posiciona explícitamente desde el
 * panel "CE: Home Builder" una vez tenga su embed configurado.
 *
 * Fondo alterno (`ce-section--alt`), mismo criterio visual ya usado
 * por trust-badges.php para separar secciones de "prueba social/
 * confianza" de las que las rodean sin depender del orden en que el
 * administrador las coloque.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_google_reviews_embed = get_theme_mod( 'ce_google_reviews_embed', '' );

if ( empty( trim( $ce_google_reviews_embed ) ) ) {
	// Sin embed configurado (estado por defecto de instalación, o el
	// administrador vació el campo desde el Customizer): se oculta
	// por completo, igual que trust-badges.php cuando no hay insignias.
	// Nota: se comprueba la cadena en crudo, no su versión sin
	// etiquetas — un embed válido de Trustindex suele ser solo
	// `<script src="...">`, sin ningún texto entre etiquetas, por lo
	// que despojarlo de tags para comprobarlo daría un falso vacío.
	return;
}
?>
<section class="ce-section ce-section--alt" id="ce-google-reviews">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Reseñas de Google', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( 'Lo Que Dicen en Google', 'ce-construction' ); ?></h2>
		</div>

		<div class="ce-google-reviews-embed ce-mt-6">
			<?php
			// El valor ya fue saneado en el momento de guardarse
			// (ce_construction_sanitize_google_reviews_embed(), lista
			// blanca de etiquetas/atributos) — se imprime tal cual,
			// sin escaping adicional, porque escapar de nuevo aquí
			// destruiría el propio embed (mismo criterio que el resto
			// del tema aplica a HTML ya saneado en origen, ej.
			// wp_get_attachment_image() en trust-badges.php).
			echo $ce_google_reviews_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valor ya saneado por wp_kses() en ce_construction_sanitize_google_reviews_embed(), ver inc/customizer.php.
			?>
		</div>
	</div>
</section>

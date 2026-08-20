<?php
/**
 * Template part: Hero.
 * Controlado 100% desde el Customizer (inc/customizer.php).
 *
 * Sprint UX-4, Entregable UX-4.1 (fase "Optimización UX / Conversión"):
 * admite fondo de imagen (comportamiento histórico, por defecto) o
 * video (`ce_hero_type`), con overlay de opacidad configurable
 * (`ce_hero_overlay_opacity`). Ver DECISIONS.md D-054.
 *
 * 🆕 Sprint UX-4, Entregable UX-4.2: `ce_hero_type` admite un tercer
 * valor, `slider` — varias imágenes (`ce_hero_slides`, Customizer)
 * en bucle automático de fondo, mismo mecanismo de capas que el
 * modo `video` (posicionado detrás de `.ce-hero__overlay`) y mismo
 * criterio de fallback silencioso a imagen si no hay ninguna imagen
 * seleccionada. Reutiliza en frontend el módulo `ModuleHeroSlider`
 * de `assets/js/main.js` (fábrica compartida con
 * `ModuleTestimonialSlider`, ver DECISIONS.md D-055). El bloque de
 * cálculo de los botones del Hero (incluido el fix de D-050) no se
 * toca en este Entregable — se preserva línea por línea.
 *
 * 🆕 Sprint UX-7, Entregable UX-7.1 (fase "Optimización UX /
 * Conversión"): la resolución de video/slider se extrajo a
 * `ce_construction_get_hero_media_state()` (inc/helpers.php), única
 * fuente de verdad compartida con `template-parts/page-hero.php`
 * (Hero interno, ahora también capaz de video/slider). Sin cambio de
 * comportamiento en este archivo. Ver DECISIONS.md D-063.
 *
 * 🆕 Sprint UX-7, Entregable UX-7.2 (fase "Optimización UX /
 * Conversión"): layout de columnas (`ce_hero_layout`, solo Home) +
 * slot opcional de Quote Form embebido (`ce_hero_show_quote_form`),
 * reutilizando `template-parts/quote-form.php` con
 * `$args['context'] = 'hero'` (mismo handler/nonce/validación de
 * siempre, sin formulario ni endpoint nuevo). Ver DECISIONS.md D-064.
 *
 * Comportamiento (decisión explícita del usuario para este
 * Entregable, ver D-064):
 *   - `ce_hero_show_quote_form` desactivado (por defecto): el
 *     contenido ocupa siempre el ancho completo — comportamiento
 *     histórico sin ningún cambio de markup, sin importar el valor
 *     de `ce_hero_layout`.
 *   - `ce_hero_show_quote_form` activado + `ce_hero_layout = '1'`:
 *     contenido y formulario se apilan, ambos a ancho completo (una
 *     sola columna con 2 bloques, no lado a lado).
 *   - `ce_hero_show_quote_form` activado + `ce_hero_layout = '2'`
 *     o `'3'`: contenido y formulario en 2 columnas lado a lado,
 *     proporción 7/5 u 8/4 respectivamente (desde tablet grande;
 *     apilados en móvil, ver assets/css/main.css sección 28).
 * Este layout NO aplica al Hero interno (`page-hero.php`) — decisión
 * explícita del usuario, ese Hero permanece de una sola columna.
 *
 * 🆕 Ajuste puntual dentro de UX-7.2 (aún sin aprobar, amparado por
 * la excepción de `docs/UX_CONVERSION_ANALISIS_Y_PLAN.md` §8.2/§8.4
 * — ver DECISIONS.md D-065, que referencia D-064): con el formulario
 * activo, `.ce-hero` gana el modificador `.ce-hero--has-form` (altura
 * automática en vez de `min-height: 88vh` forzado) y se omite el
 * indicador `.ce-hero__scroll` (se posiciona mal con contenido más
 * largo). Sin cambios de comportamiento cuando el formulario está
 * desactivado.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_image_id = get_theme_mod( 'ce_hero_image' );
$hero_image_url = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'ce-hero' ) : '';

/* -----------------------------------------------------------
 * Sprint UX-4, Entregable UX-4.1: tipo de fondo del Hero
 * (imagen/video) + overlay configurable. Ver DECISIONS.md D-054.
 * 🆕 Entregable UX-4.2: 'ce_hero_type' admite además 'slider'
 * (whitelist ampliada en ce_construction_sanitize_hero_type(),
 * inc/customizer.php — ver DECISIONS.md D-055).
 * --------------------------------------------------------- */
$hero_type = get_theme_mod( 'ce_hero_type', 'image' );

/* -----------------------------------------------------------
 * 🆕 Sprint UX-7, Entregable UX-7.1: la resolución de video/slider
 * (antes inline en este archivo) se extrajo a
 * `ce_construction_get_hero_media_state()` (inc/helpers.php) — única
 * fuente de verdad, reutilizada también por
 * `template-parts/page-hero.php` (Hero interno, unificado en este
 * mismo Entregable). Mismo criterio de fallback silencioso que antes,
 * sin cambio de comportamiento aquí. Ver DECISIONS.md D-063.
 * --------------------------------------------------------- */
$hero_media = ce_construction_get_hero_media_state( $hero_type );

$hero_is_video   = $hero_media['is_video'];
$hero_video_url  = $hero_media['video_url'];
$hero_video_mime = $hero_media['video_mime'];
$hero_is_slider  = $hero_media['is_slider'];
$hero_slide_urls = $hero_media['slide_urls'];

$hero_overlay_opacity = get_theme_mod( 'ce_hero_overlay_opacity', '1' );

/* -----------------------------------------------------------
 * 🆕 Sprint UX-7, Entregable UX-7.2: layout de columnas + slot de
 * Quote Form embebido. Ver docblock de arriba y DECISIONS.md D-064.
 * --------------------------------------------------------- */
$hero_layout = get_theme_mod( 'ce_hero_layout', '1' );
$hero_show_quote_form = (bool) get_theme_mod( 'ce_hero_show_quote_form', false );
// El layout de columnas solo tiene efecto visual cuando el
// formulario está activado (si no, no hay un segundo bloque con el
// que compartir la fila) — con el formulario desactivado, el
// contenido siempre es de una sola columna, ancho completo, igual
// que antes de este Entregable, sin importar $hero_layout.
$hero_use_columns = $hero_show_quote_form && in_array( $hero_layout, array( '2', '3' ), true );

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
<?php
/* -----------------------------------------------------------
 * 🆕 Ajuste puntual dentro de UX-7.2 (D-065, referencia D-064):
 * con el formulario embebido activo, `.ce-hero` deja de forzar
 * `min-height: 88vh` (sección 10) — el modificador
 * `.ce-hero--has-form` (sección 28 de main.css) pasa a altura
 * automática con padding vertical, especialmente relevante en
 * móvil, donde el formulario ya aporta suficiente contenido.
 * --------------------------------------------------------- */
$hero_section_class = 'ce-hero' . ( $hero_show_quote_form ? ' ce-hero--has-form' : '' );
?>
<section class="<?php echo esc_attr( $hero_section_class ); ?>" id="ce-hero" <?php echo ( ! $hero_is_video && ! $hero_is_slider && $hero_image_url ) ? 'style="background-image:url(\'' . esc_url( $hero_image_url ) . '\')"' : ''; ?>>
	<?php if ( $hero_is_video ) : ?>
		<video class="ce-hero__video" autoplay muted loop playsinline aria-hidden="true">
			<source src="<?php echo esc_url( $hero_video_url ); ?>" <?php echo $hero_video_mime ? 'type="' . esc_attr( $hero_video_mime ) . '"' : ''; ?>>
		</video>
	<?php elseif ( $hero_is_slider ) : ?>
		<?php /*
		🆕 Sprint UX-4, Entregable UX-4.2: mismo posicionamiento de
		capa que .ce-hero__video (sección 27 de main.css, aditiva) —
		detrás de .ce-hero__overlay, delante del color sólido de
		respaldo de .ce-hero. Auto-inicializado por ModuleHeroSlider
		(assets/js/main.js): sin dots ni flechas (fondo decorativo),
		ver DECISIONS.md D-055. aria-hidden porque es puramente
		decorativo — el contenido real (título/CTA) está en
		.ce-hero__content, no en las slides.
		*/ ?>
		<div class="ce-hero-slider" aria-hidden="true">
			<div class="ce-hero-slider__track">
				<?php foreach ( $hero_slide_urls as $slide_url ) : ?>
					<div class="ce-hero-slider__slide" style="background-image:url('<?php echo esc_url( $slide_url ); ?>')"></div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="ce-hero__overlay" style="--ce-hero-overlay-opacity: <?php echo esc_attr( $hero_overlay_opacity ); ?>;"></div>
	<div class="ce-container">
		<?php if ( $hero_show_quote_form ) : ?>
			<?php
			// 🆕 UX-7.2 (D-064): con el formulario activado, el contenido
			// y el formulario comparten un grid — apilado (una columna)
			// si ce_hero_layout='1', lado a lado (7/5 u 8/4) si es '2'/'3'.
			// Sin el formulario activado (rama else, abajo) el markup NO
			// se toca: mismo .ce-hero__content suelto de siempre.
			$hero_layout_class = $hero_use_columns ? 'ce-hero__layout--' . $hero_layout : 'ce-hero__layout--stacked';
			?>
			<div class="ce-hero__layout <?php echo esc_attr( $hero_layout_class ); ?>">
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
				<div class="ce-hero__quote-form-col">
					<?php get_template_part( 'template-parts/quote-form', null, array( 'context' => 'hero' ) ); ?>
				</div>
			</div>
		<?php else : ?>
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
		<?php endif; ?>
	</div>
	<?php
	/* -----------------------------------------------------------
	 * 🆕 Ajuste puntual dentro de UX-7.2 (D-065): el indicador de
	 * scroll se posiciona mal (se superpone al formulario o queda
	 * fuera de lugar) cuando el contenido del Hero es más largo por
	 * el formulario embebido — se omite por completo en ese caso,
	 * en vez de reposicionarlo con CSS adicional.
	 * --------------------------------------------------------- */
	if ( ! $hero_show_quote_form ) :
		?>
		<a href="#ce-about" class="ce-hero__scroll" aria-label="<?php esc_attr_e( 'Desplázate hacia abajo', 'ce-construction' ); ?>">
			<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
		</a>
		<?php
	endif;
	?>
</section>

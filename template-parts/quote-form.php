<?php
/**
 * Template part: Formulario de Cotización Gratuita.
 * El handler de envío (validación, sanitización, nonce, email,
 * adjuntos) vive en inc/quote-form.php (acción AJAX `ce_submit_quote`).
 * El envío y la validación en cliente los maneja ModuleQuoteForm
 * en assets/js/main.js.
 *
 * Sprint UX-3, Entregable UX-3.2 (fase "Optimización UX / Conversión",
 * paralela al Sprint 8 pausado — ver docs/CURRENT_UX_SPRINT.md).
 * Arquitectura vigente (D-053, que reemplaza el diseño intermedio de
 * D-051/D-052 documentado en DECISIONS.md — historial completo ahí,
 * no repetido aquí):
 *
 * Este archivo admite 3 contextos de invocación, vía `$args['context']`:
 *   - Normal (sin `$args`, o `$args['context']` distinto de 'modal'/
 *     'hero'): sección completa (`<section id="ce-quote-form-section">`)
 *     con eyebrow/título/lista + tarjeta del formulario. La invocan la
 *     sección `quote_form` del Home Builder (`front-page.php` vía
 *     `inc/home-builder.php`) y, de forma incondicional, `single-servicio.php`/
 *     `single-proyecto.php`.
 *   - Modal (`$args['context'] = 'modal'`, usada exclusivamente por
 *     `footer.php`): imprime ÚNICAMENTE el `<form>`, sin el
 *     `<section>`/encabezado/tarjeta de la presentación integrada.
 *   - 🆕 Hero (`$args['context'] = 'hero'`, Sprint UX-7, Entregable
 *     UX-7.2 — ver DECISIONS.md D-064), usada exclusivamente por
 *     `template-parts/hero.php` cuando `ce_hero_show_quote_form` está
 *     activado: imprime el `<form>` envuelto en una tarjeta compacta
 *     (`.ce-hero-quote-card`), sin el `<section>`/eyebrow/lista de la
 *     presentación integrada — mismo criterio que el modal (bare
 *     form + wrapper propio), pero con su propio wrapper visual
 *     pensado para convivir con el fondo oscuro del Hero.
 *     🆕 Ajuste puntual dentro de UX-7.2 (D-065): la tarjeta gana un
 *     badge corto ("Respuesta en 24h") como único elemento de
 *     tratamiento de "oferta" en este ciclo — sin bullets de
 *     confianza (eso se absorbe en UX-7.7, sin aprobar todavía).
 *
 * Modos de `ce_quote_form_mode` (theme_mod, `inc/customizer.php`) y
 * su efecto en ESTE archivo:
 *   - `'integrated'`: la invocación normal imprime la sección
 *     completa allí donde se llame. `footer.php` imprime el modal en
 *     TODAS las páginas (ver footer.php) — si en la página actual ya
 *     se imprimió la instancia normal, el `<form>` del modal recibe
 *     un id distinto (`ce-quote-form-modal`, calculado más abajo)
 *     para no duplicar `id="ce-quote-form"` en el DOM.
 *   - `'modal'`: la invocación normal NO imprime nada (se corta antes
 *     de cualquier HTML) — solo el modal tiene el formulario, siempre
 *     con `id="ce-quote-form"` (nunca coexiste con una instancia
 *     normal en la misma página).
 *   - `'disabled'`: ni la invocación normal ni el modal (`footer.php`
 *     ni siquiera llega a invocar este archivo en modo 'disabled')
 *     imprimen nada — sin formulario de cotización en ningún punto
 *     del sitio.
 *   - 🆕 La invocación en contexto 'hero' se comporta, frente a este
 *     mismo guard, exactamente igual que la invocación normal (no
 *     modal): en modo 'modal'/'disabled' tampoco imprime nada — el
 *     Hero es una instancia INTEGRADA más (visible directamente en
 *     la página, no un popup), así que respeta la misma regla que ya
 *     aplican `single-servicio.php`/`single-proyecto.php`. Sin
 *     cambios en el guard ya existente (la condición `! $ce_quote_is_modal`
 *     ya cubre 'hero' sin necesidad de una rama nueva).
 *
 * IDs internos de los campos (`ce_name`, `ce_email`, etc.): se
 * sufijan con `-modal` únicamente cuando el `<form>` del modal usa el
 * id alternativo, para evitar también la duplicación de esos IDs
 * internos en el DOM. Los atributos `name=` de cada campo NUNCA se
 * tocan — `inc/quote-form.php` (handler AJAX) y `FormData()` en
 * `assets/js/main.js` identifican cada campo por `name`, no por `id`.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_quote_context = isset( $args['context'] ) ? $args['context'] : '';
$ce_quote_is_modal = ( 'modal' === $ce_quote_context );
// 🆕 Sprint UX-7, Entregable UX-7.2 (D-064): tercer contexto, usado
// exclusivamente por template-parts/hero.php.
$ce_quote_is_hero = ( 'hero' === $ce_quote_context );

// Invocación "normal" (no modal) mientras el modo global es 'modal' O
// 'disabled': no imprimir la sección integrada.
//   - 'modal' (sin cambios respecto a D-051): solo el popup debe
//     tener el formulario.
//   - 'disabled' (🆕 corrección UX-3.2, D-053): sin esta rama,
//     single-servicio.php/single-proyecto.php — que invocan este
//     archivo de forma INCONDICIONAL, fuera del Home Builder —
//     seguirían mostrando un formulario de cotización totalmente
//     funcional pese a que el administrador desactivó la cotización
//     por completo. La sección "Formulario de Cotización" del Home
//     Builder tiene su propio checkbox de activo/inactivo (UX-1.2),
//     pero esos dos archivos no pasan por ese registro, así que
//     necesitaban esta guarda explícita para respetar 'disabled' de
//     verdad en todos los puntos donde este template-part se invoca.
$ce_quote_mode = get_theme_mod( 'ce_quote_form_mode', 'integrated' );
if ( ! $ce_quote_is_modal && ( 'modal' === $ce_quote_mode || 'disabled' === $ce_quote_mode ) ) {
	return;
}

// 🆕 Corrección UX-3.2 (D-052): si llegamos aquí en la rama NO modal
// (es decir, la instancia integrada SÍ se va a imprimir — modo
// 'integrated' o 'disabled'), avisamos a footer.php para que no
// duplique el <form id="ce-quote-form"> dentro del modal en esta
// misma página. No afecta a la rama modal (ce_quote_is_modal=true):
// esa invocación no imprime una segunda instancia "integrada", así
// que no marca nada.
// 🆕 UX-7.2 (D-064): la instancia 'hero' NO marca esta bandera. Usa
// su propio id fijo, sin colisión posible con "ce-quote-form" (ver
// resolución de $ce_quote_form_id más abajo), así que no necesita
// (ni debe) reservar ese id para el modal — el modal solo necesita
// saber si la instancia INTEGRADA (la que sí usa "ce-quote-form")
// ya se imprimió.
if ( ! $ce_quote_is_modal && ! $ce_quote_is_hero ) {
	ce_construction_mark_quote_form_rendered_inline();
}

// 🆕 UX-3.2 (D-053): id real del <form>. Por defecto "ce-quote-form"
// (único id posible para la instancia NO modal — nunca se invoca más
// de una vez por request). Para la instancia del modal, solo si YA
// se imprimió antes una instancia integrada en esta misma página
// (bandera ce_construction_quote_form_rendered_inline(), fijada más
// arriba por la rama no-modal si corresponde) se usa un id distinto
// ("ce-quote-form-modal") para evitar 2 elementos con el mismo id en
// el DOM — ver ModuleQuoteForm en assets/js/main.js, que ahora
// localiza todas las instancias por clase (`.ce-quote-form-instance`)
// en vez de por un id fijo, precisamente para poder inicializar
// ambas de forma independiente cuando coexisten.
// 🆕 UX-7.2 (D-064): la instancia 'hero' usa un id FIJO y distinto
// ("ce-quote-form-hero") — a lo sumo un Hero por página, así que no
// necesita ningún cálculo de colisión dinámico como el modal. Al no
// competir nunca por el id base "ce-quote-form", tampoco afecta la
// decisión de abajo (que sigue mirando únicamente si la instancia
// INTEGRADA, no la de Hero, ya se imprimió).
if ( $ce_quote_is_hero ) {
	$ce_quote_form_id = 'ce-quote-form-hero';
} else {
	$ce_quote_form_id = 'ce-quote-form';
	if ( $ce_quote_is_modal && ce_construction_quote_form_rendered_inline() ) {
		$ce_quote_form_id = 'ce-quote-form-modal';
	}
}
// Mismo criterio para los `id`/`for` de los campos internos (nombre
// = 'ce_name', 'ce_email'...): solo se sufijan cuando hay colisión
// real (la instancia del modal, cuando coexiste con la integrada, o
// la instancia de Hero, que siempre tiene su propio id). Los
// atributos `name=` NO se tocan en ningún caso — inc/quote-form.php
// (handler AJAX) y FormData()/this.form.elements[name] en
// assets/js/main.js identifican cada campo por `name`, nunca por
// `id`; cambiar el `id` es puramente para no duplicar IDs en el DOM
// (validez HTML/accesibilidad de las etiquetas <label for="...">).
$ce_quote_id_suffix = '';
if ( 'ce-quote-form-modal' === $ce_quote_form_id ) {
	$ce_quote_id_suffix = '-modal';
} elseif ( 'ce-quote-form-hero' === $ce_quote_form_id ) {
	$ce_quote_id_suffix = '-hero';
}

$servicios = get_posts( array(
	'post_type'      => 'servicio',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>
<?php if ( $ce_quote_is_hero ) : ?>
<!-- 🆕 UX-7.2 (D-064): tarjeta compacta para el slot del Hero — sin
     <section>/eyebrow/lista de la presentación integrada, mismo
     criterio de "bare form + wrapper propio" que el modal.
     🆕 Ajuste puntual dentro de UX-7.2 (D-065): franja superior de
     acento (`--ce-color-secondary`, vía CSS en `.ce-hero-quote-card`,
     sección 28 de main.css) + badge corto de "oferta". Sin bullets de
     confianza aquí — ese requisito se absorbe en UX-7.7 (sin aprobar,
     ver D-065 y la nota añadida a UX-7.7 en este ciclo). -->
<div class="ce-hero-quote-card ce-card">
	<div class="ce-card__body">
		<span class="ce-hero-quote-card__badge">
			<i class="fa-solid fa-bolt" aria-hidden="true"></i>
			<?php esc_html_e( 'Respuesta en 24h', 'ce-construction' ); ?>
		</span>
<?php elseif ( ! $ce_quote_is_modal ) : ?>
<section class="ce-section ce-section--alt" id="ce-quote-form-section">
	<div class="ce-container">
		<div class="ce-grid ce-grid--2 ce-items-center">

			<div class="ce-animate-on-scroll">
				<span class="ce-eyebrow"><?php esc_html_e( 'Cotización Gratuita', 'ce-construction' ); ?></span>
				<h2 class="ce-section-title"><?php esc_html_e( 'Cuéntanos sobre tu proyecto', 'ce-construction' ); ?></h2>
				<p class="ce-section-lead">
					<?php esc_html_e( 'Completa el formulario con los detalles de tu proyecto y nuestro equipo te contactará con una propuesta a medida, sin costo ni compromiso.', 'ce-construction' ); ?>
				</p>
				<ul class="ce-mb-4">
					<li class="ce-mb-2"><i class="fa-solid fa-circle-check" style="color:var(--ce-color-success);"></i> <?php esc_html_e( 'Respuesta en menos de 24 horas', 'ce-construction' ); ?></li>
					<li class="ce-mb-2"><i class="fa-solid fa-circle-check" style="color:var(--ce-color-success);"></i> <?php esc_html_e( 'Sin costo ni compromiso', 'ce-construction' ); ?></li>
					<li class="ce-mb-2"><i class="fa-solid fa-circle-check" style="color:var(--ce-color-success);"></i> <?php esc_html_e( 'Asesoría de expertos certificados', 'ce-construction' ); ?></li>
				</ul>
			</div>

			<div class="ce-card ce-animate-on-scroll">
				<div class="ce-card__body">
<?php endif; ?>

					<form id="<?php echo esc_attr( $ce_quote_form_id ); ?>" class="ce-form ce-quote-form-instance" novalidate enctype="multipart/form-data">

						<?php wp_nonce_field( 'ce_quote_form_action', 'ce_quote_nonce_field' ); ?>
						<!-- Honeypot anti-spam: campo oculto que un humano nunca completa -->
						<div class="ce-honeypot" aria-hidden="true">
							<label for="ce_website<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'No completar este campo', 'ce-construction' ); ?></label>
							<input type="text" id="ce_website<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="ce_website" tabindex="-1" autocomplete="off">
						</div>

						<div class="ce-form__row ce-form__row--2">
							<div class="ce-field">
								<label for="ce_name<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Nombre', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="text" id="ce_name<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="name" required minlength="2">
								<span class="ce-field__error"></span>
							</div>
							<div class="ce-field">
								<label for="ce_email<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Correo', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="email" id="ce_email<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="email" required>
								<span class="ce-field__error"></span>
							</div>
						</div>

						<div class="ce-form__row ce-form__row--2">
							<div class="ce-field">
								<label for="ce_phone<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Teléfono', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="tel" id="ce_phone<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="phone" required>
								<span class="ce-field__error"></span>
							</div>
							<div class="ce-field">
								<label for="ce_company<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Empresa', 'ce-construction' ); ?></label>
								<input type="text" id="ce_company<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="company">
								<span class="ce-field__error"></span>
							</div>
						</div>

						<div class="ce-field">
							<label for="ce_service<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Servicio requerido', 'ce-construction' ); ?> <span class="required">*</span></label>
							<select id="ce_service<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="service" required>
								<option value=""><?php esc_html_e( 'Selecciona un servicio', 'ce-construction' ); ?></option>
								<?php if ( $servicios ) : ?>
									<?php foreach ( $servicios as $servicio ) : ?>
										<option value="<?php echo esc_attr( $servicio->post_title ); ?>"><?php echo esc_html( $servicio->post_title ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
								<option value="<?php esc_attr_e( 'Otro', 'ce-construction' ); ?>"><?php esc_html_e( 'Otro', 'ce-construction' ); ?></option>
							</select>
							<span class="ce-field__error"></span>
						</div>

						<?php if ( $ce_quote_is_hero ) : ?>
						<!-- 🆕 Ajuste puntual dentro de UX-11 (D-091): wrapper
						     colapsable EXCLUSIVO de la instancia 'hero' —
						     envuelve el resto de campos (Mensaje, Adjuntar
						     archivo, botón de envío) para la expansión
						     progresiva al enfocar cualquier campo visible
						     arriba. Progressive enhancement obligatorio: este
						     `<div>` no lleva ningún estilo/atributo que oculte
						     nada por sí mismo — sin CSS ni JS se muestra
						     exactamente igual que cualquier otro `<div>` del
						     formulario (todos los campos visibles). El
						     colapso real lo aplica ÚNICAMENTE
						     ModuleHeroFormProgressive (assets/js/main.js),
						     solo si el script carga y se ejecuta con éxito.
						     Las instancias normal y modal NUNCA imprimen este
						     `<div>` (rama condicionada a `$ce_quote_is_hero`),
						     así que no hay ningún cambio de markup para ellas. -->
						<div class="ce-hero-quote-form__extra">
						<?php endif; ?>

						<div class="ce-field">
							<label for="ce_message<?php echo esc_attr( $ce_quote_id_suffix ); ?>"><?php esc_html_e( 'Mensaje', 'ce-construction' ); ?> <span class="required">*</span></label>
							<textarea id="ce_message<?php echo esc_attr( $ce_quote_id_suffix ); ?>" name="message" rows="4" required minlength="10" placeholder="<?php esc_attr_e( 'Cuéntanos brevemente sobre tu proyecto: alcance, ubicación, tiempos estimados...', 'ce-construction' ); ?>"></textarea>
							<span class="ce-field__error"></span>
						</div>

						<div class="ce-field">
							<label><?php esc_html_e( 'Adjuntar archivo (opcional, máx. 5MB — PDF, JPG, PNG, WEBP)', 'ce-construction' ); ?></label>
							<div class="ce-field--file">
								<input type="file" name="attachment" id="ce_attachment<?php echo esc_attr( $ce_quote_id_suffix ); ?>" accept=".pdf,.jpg,.jpeg,.png,.webp">
								<i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
								<span class="ce-field--file__label"><?php esc_html_e( 'Haz clic o arrastra un archivo aquí', 'ce-construction' ); ?></span>
							</div>
							<span class="ce-field__error"></span>
						</div>

						<button type="submit" class="ce-btn ce-btn--primary ce-btn--block">
							<span class="ce-btn__label"><?php esc_html_e( 'Enviar Solicitud', 'ce-construction' ); ?></span>
							<span class="ce-spinner" aria-hidden="true"></span>
						</button>

						<?php if ( $ce_quote_is_hero ) : ?>
						</div><!-- /.ce-hero-quote-form__extra -->
						<?php endif; ?>

						<div class="ce-form-status" role="status" aria-live="polite"></div>

					</form>

<?php if ( $ce_quote_is_hero ) : ?>
	<?php
	// 🆕 Sprint UX-7, Entregable UX-7.7 (D-071): resolución de la
	// nota añadida durante el ajuste puntual de UX-7.2 (D-065) —
	// "una vez construido, el componente de insignias/bullets
	// también debe quedar disponible para la tarjeta del Quote Form
	// del Hero". Reutiliza el mismo template-part de las insignias
	// de confianza (template-parts/trust-badges.php), en su modo
	// compacto (`$args['compact']`), sin duplicar markup ni crear un
	// segundo sistema de insignias. SOLO se imprime en el contexto
	// 'hero' — el modal y la instancia integrada normal no lo llevan,
	// conforme al alcance de este Entregable. Se oculta por completo,
	// sin dejar ningún espacio vacío, si el administrador no
	// configuró ninguna insignia en "CE: Insignias de Confianza"
	// (mismo guard que ya trae ese template-part) — el badge
	// "Respuesta en 24h" de arriba no depende de esto y sigue
	// mostrándose igual.
	get_template_part( 'template-parts/trust-badges', null, array( 'compact' => true ) );
	?>
	</div>
</div>
<?php elseif ( ! $ce_quote_is_modal ) : ?>
				</div>
			</div>

		</div>
	</div>
</section>
<?php endif; ?>

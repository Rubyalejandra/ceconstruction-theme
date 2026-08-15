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
 * Este archivo admite 2 contextos de invocación, vía `$args['context']`:
 *   - Normal (sin `$args`, o `$args['context']` distinto de 'modal'):
 *     sección completa (`<section id="ce-quote-form-section">`) con
 *     eyebrow/título/lista + tarjeta del formulario. La invocan la
 *     sección `quote_form` del Home Builder (`front-page.php` vía
 *     `inc/home-builder.php`) y, de forma incondicional, `single-servicio.php`/
 *     `single-proyecto.php`.
 *   - Modal (`$args['context'] = 'modal'`, usada exclusivamente por
 *     `footer.php`): imprime ÚNICAMENTE el `<form>`, sin el
 *     `<section>`/encabezado/tarjeta de la presentación integrada.
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
if ( ! $ce_quote_is_modal ) {
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
$ce_quote_form_id = 'ce-quote-form';
if ( $ce_quote_is_modal && ce_construction_quote_form_rendered_inline() ) {
	$ce_quote_form_id = 'ce-quote-form-modal';
}
// Mismo criterio para los `id`/`for` de los campos internos (nombre
// = 'ce_name', 'ce_email'...): solo se sufijan cuando hay colisión
// real (la instancia del modal, cuando coexiste con la integrada).
// Los atributos `name=` NO se tocan en ningún caso — inc/quote-form.php
// (handler AJAX) y FormData()/this.form.elements[name] en
// assets/js/main.js identifican cada campo por `name`, nunca por
// `id`; cambiar el `id` es puramente para no duplicar IDs en el DOM
// (validez HTML/accesibilidad de las etiquetas <label for="...">).
$ce_quote_id_suffix = ( 'ce-quote-form-modal' === $ce_quote_form_id ) ? '-modal' : '';

$servicios = get_posts( array(
	'post_type'      => 'servicio',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>
<?php if ( ! $ce_quote_is_modal ) : ?>
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

						<div class="ce-form-status" role="status" aria-live="polite"></div>

					</form>

<?php if ( ! $ce_quote_is_modal ) : ?>
				</div>
			</div>

		</div>
	</div>
</section>
<?php endif; ?>

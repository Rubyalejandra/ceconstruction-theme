<?php
/**
 * Template part: Formulario de Cotización Gratuita.
 * El handler de envío (validación, sanitización, nonce, email,
 * adjuntos) vive en inc/quote-form.php (acción AJAX `ce_submit_quote`).
 * El envío y la validación en cliente los maneja ModuleQuoteForm
 * en assets/js/main.js.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$servicios = get_posts( array(
	'post_type'      => 'servicio',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'title',
	'order'          => 'ASC',
) );
?>
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

					<form id="ce-quote-form" class="ce-form" novalidate enctype="multipart/form-data">

						<?php wp_nonce_field( 'ce_quote_form_action', 'ce_quote_nonce_field' ); ?>
						<!-- Honeypot anti-spam: campo oculto que un humano nunca completa -->
						<div class="ce-honeypot" aria-hidden="true">
							<label for="ce_website"><?php esc_html_e( 'No completar este campo', 'ce-construction' ); ?></label>
							<input type="text" id="ce_website" name="ce_website" tabindex="-1" autocomplete="off">
						</div>

						<div class="ce-form__row ce-form__row--2">
							<div class="ce-field">
								<label for="ce_name"><?php esc_html_e( 'Nombre', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="text" id="ce_name" name="name" required minlength="2">
								<span class="ce-field__error"></span>
							</div>
							<div class="ce-field">
								<label for="ce_email"><?php esc_html_e( 'Correo', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="email" id="ce_email" name="email" required>
								<span class="ce-field__error"></span>
							</div>
						</div>

						<div class="ce-form__row ce-form__row--2">
							<div class="ce-field">
								<label for="ce_phone"><?php esc_html_e( 'Teléfono', 'ce-construction' ); ?> <span class="required">*</span></label>
								<input type="tel" id="ce_phone" name="phone" required>
								<span class="ce-field__error"></span>
							</div>
							<div class="ce-field">
								<label for="ce_company"><?php esc_html_e( 'Empresa', 'ce-construction' ); ?></label>
								<input type="text" id="ce_company" name="company">
								<span class="ce-field__error"></span>
							</div>
						</div>

						<div class="ce-field">
							<label for="ce_service"><?php esc_html_e( 'Servicio requerido', 'ce-construction' ); ?> <span class="required">*</span></label>
							<select id="ce_service" name="service" required>
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
							<label for="ce_message"><?php esc_html_e( 'Mensaje', 'ce-construction' ); ?> <span class="required">*</span></label>
							<textarea id="ce_message" name="message" rows="4" required minlength="10" placeholder="<?php esc_attr_e( 'Cuéntanos brevemente sobre tu proyecto: alcance, ubicación, tiempos estimados...', 'ce-construction' ); ?>"></textarea>
							<span class="ce-field__error"></span>
						</div>

						<div class="ce-field">
							<label><?php esc_html_e( 'Adjuntar archivo (opcional, máx. 5MB — PDF, JPG, PNG, WEBP)', 'ce-construction' ); ?></label>
							<div class="ce-field--file">
								<input type="file" name="attachment" id="ce_attachment" accept=".pdf,.jpg,.jpeg,.png,.webp">
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

				</div>
			</div>

		</div>
	</div>
</section>

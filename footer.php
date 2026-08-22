	</main><!-- #ce-main-content -->

	<footer class="ce-footer">
		<div class="ce-footer__top">
			<div class="ce-container">
				<div class="ce-footer__grid">

					<div class="ce-footer__brand">
						<?php
						/**
						 * Sprint UX-7, Entregable UX-7.5: antes de este
						 * Entregable este bloque llamaba directamente a
						 * has_custom_logo()/the_custom_logo() (idéntico a
						 * header.php). Ahora delega en
						 * ce_render_footer_logo() (inc/helpers.php), que
						 * añade un primer nivel de fallback opcional
						 * (`ce_footer_logo`) antes de esos mismos 2
						 * fallbacks nativos — comportamiento idéntico al
						 * anterior si ese theme_mod no está configurado.
						 * Ver DECISIONS.md D-069.
						 */
						ce_render_footer_logo();
						?>
						<p class="ce-footer__about">
							<?php
							echo wp_kses_post(
								get_theme_mod(
									'ce_footer_about',
									__( 'Empresa constructora especializada en proyectos residenciales, comerciales e industriales, comprometida con la calidad, la seguridad y el cumplimiento de cada obra.', 'ce-construction' )
								)
							);
							?>
						</p>
						<?php ce_render_social_icons( 'footer' ); ?>
					</div>

					<div class="ce-footer__links">
						<h4><?php esc_html_e( 'Enlaces', 'ce-construction' ); ?></h4>
						<?php
						if ( has_nav_menu( 'footer' ) ) {
							wp_nav_menu( array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'ce-footer__list',
								'fallback_cb'    => false,
							) );
						} else {
							?>
							<ul class="ce-footer__list">
								<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Inicio', 'ce-construction' ); ?></a></li>
								<?php if ( post_type_exists( 'servicio' ) ) : ?>
									<li><a href="<?php echo esc_url( get_post_type_archive_link( 'servicio' ) ); ?>"><?php esc_html_e( 'Servicios', 'ce-construction' ); ?></a></li>
								<?php endif; ?>
								<?php if ( post_type_exists( 'proyecto' ) ) : ?>
									<li><a href="<?php echo esc_url( get_post_type_archive_link( 'proyecto' ) ); ?>"><?php esc_html_e( 'Proyectos', 'ce-construction' ); ?></a></li>
								<?php endif; ?>
								<?php if ( ce_get_quote_cta_url() ) : ?>
									<li><a href="<?php echo esc_url( ce_get_quote_cta_url() ); ?>"><?php esc_html_e( 'Cotización', 'ce-construction' ); ?></a></li>
								<?php endif; ?>
							</ul>
							<?php
						}
						?>

						<?php
						// QA-006 (Sprint 5, Fase 1 — corrección alta): "Footer - Columna 1"
						// estaba registrado en inc/setup.php pero nunca se renderizaba en
						// ningún archivo del tema — un admin podía agregarle widgets desde
						// Apariencia → Widgets y nunca verlos reflejados en el sitio. Se
						// renderiza aquí, en la misma columna de "Enlaces", únicamente si
						// tiene widgets asignados (is_active_sidebar), para no alterar el
						// layout visual aprobado cuando la columna esté vacía.
						if ( is_active_sidebar( 'footer-1' ) ) :
							?>
							<div class="ce-mt-4">
								<?php dynamic_sidebar( 'footer-1' ); ?>
							</div>
							<?php
						endif;
						?>
					</div>

					<div class="ce-footer__contact">
						<h4><?php esc_html_e( 'Contacto', 'ce-construction' ); ?></h4>
						<?php if ( get_theme_mod( 'ce_address' ) ) : ?>
							<div class="ce-footer__contact-item">
								<i class="fa-solid fa-location-dot" aria-hidden="true"></i>
								<span><?php echo esc_html( get_theme_mod( 'ce_address' ) ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( get_theme_mod( 'ce_phone' ) ) : ?>
							<div class="ce-footer__contact-item">
								<i class="fa-solid fa-phone" aria-hidden="true"></i>
								<a href="tel:<?php echo esc_attr( ce_get_phone_href() ); ?>"><?php echo esc_html( get_theme_mod( 'ce_phone' ) ); ?></a>
							</div>
						<?php endif; ?>
						<?php if ( get_theme_mod( 'ce_email' ) ) : ?>
							<div class="ce-footer__contact-item">
								<i class="fa-solid fa-envelope" aria-hidden="true"></i>
								<a href="mailto:<?php echo esc_attr( get_theme_mod( 'ce_email' ) ); ?>"><?php echo esc_html( get_theme_mod( 'ce_email' ) ); ?></a>
							</div>
						<?php endif; ?>
						<?php if ( get_theme_mod( 'ce_schedule' ) ) : ?>
							<div class="ce-footer__contact-item">
								<i class="fa-regular fa-clock" aria-hidden="true"></i>
								<span><?php echo nl2br( esc_html( get_theme_mod( 'ce_schedule' ) ) ); ?></span>
							</div>
						<?php endif; ?>
					</div>

					<div class="ce-footer__map">
						<h4><?php esc_html_e( 'Ubicación', 'ce-construction' ); ?></h4>
						<?php if ( get_theme_mod( 'ce_maps_embed_url' ) ) : ?>
							<iframe
								src="<?php echo esc_url( get_theme_mod( 'ce_maps_embed_url' ) ); ?>"
								loading="lazy"
								referrerpolicy="no-referrer-when-downgrade"
								title="<?php esc_attr_e( 'Mapa de ubicación', 'ce-construction' ); ?>">
							</iframe>
						<?php else : ?>
							<p><?php esc_html_e( 'Configura la URL del mapa desde el Personalizador.', 'ce-construction' ); ?></p>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>

		<div class="ce-footer__bottom">
			<div class="ce-container ce-flex ce-justify-between ce-flex-wrap ce-gap-3">
				<span>
					<?php
					echo esc_html(
						get_theme_mod(
							'ce_footer_copyright',
							sprintf(
								/* translators: %1$d: año actual, %2$s: nombre del sitio */
								__( '&copy; %1$d %2$s. Todos los derechos reservados.', 'ce-construction' ),
								gmdate( 'Y' ),
								get_bloginfo( 'name' )
							)
						)
					);
					?>
				</span>
				<nav aria-label="<?php esc_attr_e( 'Enlaces legales', 'ce-construction' ); ?>">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</nav>
			</div>
		</div>
	</footer>

	<!-- Botones flotantes: WhatsApp + Volver arriba -->
	<div class="ce-float-stack">
		<?php if ( ce_get_whatsapp_number() ) : ?>
			<a href="#" class="ce-float-btn ce-float-btn--whatsapp" aria-label="<?php esc_attr_e( 'Escríbenos por WhatsApp', 'ce-construction' ); ?>" data-message="<?php esc_attr_e( 'Hola, quisiera más información sobre sus servicios de construcción.', 'ce-construction' ); ?>">
				<i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
		<button class="ce-float-btn ce-float-btn--top" aria-label="<?php esc_attr_e( 'Volver arriba', 'ce-construction' ); ?>">
			<i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
		</button>
	</div>

	<!-- Modal: éxito -->
	<div class="ce-modal-overlay" id="ce-modal-success">
		<div class="ce-modal" role="dialog" aria-modal="true" aria-labelledby="ce-modal-success-title">
			<button class="ce-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'ce-construction' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			<div class="ce-modal__icon ce-modal__icon--success"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
			<h3 id="ce-modal-success-title" class="ce-modal__title"><?php esc_html_e( '¡Solicitud enviada!', 'ce-construction' ); ?></h3>
			<p class="ce-modal__text"><?php esc_html_e( 'Gracias por contactarnos. Nuestro equipo revisará tu solicitud y te responderá muy pronto.', 'ce-construction' ); ?></p>
			<button class="ce-btn ce-btn--primary ce-modal__close-action ce-modal__close"><?php esc_html_e( 'Entendido', 'ce-construction' ); ?></button>
		</div>
	</div>

	<!-- Modal: error -->
	<div class="ce-modal-overlay" id="ce-modal-error">
		<div class="ce-modal" role="dialog" aria-modal="true" aria-labelledby="ce-modal-error-title">
			<button class="ce-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'ce-construction' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
			<div class="ce-modal__icon ce-modal__icon--error"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i></div>
			<h3 id="ce-modal-error-title" class="ce-modal__title"><?php esc_html_e( 'Algo salió mal', 'ce-construction' ); ?></h3>
			<p class="ce-modal__text"><?php esc_html_e( 'No pudimos procesar tu solicitud. Por favor verifica los datos o intenta nuevamente en unos minutos.', 'ce-construction' ); ?></p>
			<button class="ce-btn ce-btn--dark ce-modal__close-action ce-modal__close"><?php esc_html_e( 'Cerrar', 'ce-construction' ); ?></button>
		</div>
	</div>

	<?php
	// 🆕 Sprint UX-3, Entregable UX-3.2: modal de cotización.
	// Semántica actualizada (D-053, reemplaza el diseño original de
	// D-051): se imprime siempre que ce_quote_form_mode NO sea
	// 'disabled' — en 'integrated' Y en 'modal' por igual, porque
	// desde este Entregable los 7 puntos de CTA del tema abren el
	// popup en ambos modos (ver inc/helpers.php -> ce_get_quote_cta_url()).
	// Solo en 'disabled' este bloque no genera ningún HTML. El
	// contenido reutiliza template-parts/quote-form.php con
	// $args['context'] = 'modal', que decide internamente si el
	// <form> necesita un id distinto de "ce-quote-form" para evitar
	// colisión con una instancia integrada ya impresa antes en esta
	// misma página (Home Builder / single-servicio.php /
	// single-proyecto.php) — ver el docblock de ese archivo y
	// DECISIONS.md D-053.
	if ( 'disabled' !== get_theme_mod( 'ce_quote_form_mode', 'integrated' ) ) :
		?>
		<!-- Modal: formulario de cotización -->
		<div class="ce-modal-overlay" id="ce-quote-modal">
			<div class="ce-modal ce-modal--form" role="dialog" aria-modal="true" aria-labelledby="ce-quote-modal-title">
				<button class="ce-modal__close" aria-label="<?php esc_attr_e( 'Cerrar', 'ce-construction' ); ?>"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
				<h3 id="ce-quote-modal-title" class="ce-modal__title"><?php esc_html_e( 'Solicita tu Cotización Gratuita', 'ce-construction' ); ?></h3>
				<?php get_template_part( 'template-parts/quote-form', null, array( 'context' => 'modal' ) ); ?>
			</div>
		</div>
		<?php
	endif;
	?>

	<?php
	// 🆕 Sprint UX-7, Entregable UX-7.10 (D-079): Popup de Oferta —
	// componente independiente del modal de arriba (no lo modifica).
	// template-parts/offer-popup.php decide internamente (vía
	// ce_get_offer_popup_data(), inc/helpers.php) si hay algo que
	// imprimir; con el popup desactivado o sin configurar, esta
	// llamada no genera ningún HTML.
	get_template_part( 'template-parts/offer-popup' );
	?>

<?php wp_footer(); ?>
</body>
</html>

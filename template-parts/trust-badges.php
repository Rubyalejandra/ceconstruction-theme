<?php
/**
 * Template part: Franja de Insignias de Confianza / Licencias.
 *
 * Sprint UX-7, Entregable UX-7.7 (fase "Optimización UX / Conversión").
 * Ver docs/DECISIONS.md D-071.
 *
 * Contexto: hallazgo del benchmark competitivo (DayBrook Homes /
 * Re-Bath, ver docs/UX_CONVERSION_ANALISIS_Y_PLAN.md §8.4/§8.8) — el
 * tema no tenía ningún componente para mostrar insignias de
 * credibilidad (licencias estatales, seguros, afiliaciones,
 * certificaciones, ratings de plataformas externas). Requisito
 * explícito del usuario: el cliente real de este proyecto cuenta con
 * una licencia de contratista válida, y necesita poder mostrarla —
 * no es un elemento decorativo.
 *
 * Fuente de datos: `ce_construction_get_trust_badges()`
 * (inc/helpers.php), que decodifica el theme_mod
 * `ce_trust_badges_items` (control repeater del Customizer, sección
 * "CE: Insignias de Confianza") — cantidad variable, cada insignia
 * admite imagen (opcional), etiqueta (obligatoria), número de
 * licencia (opcional) y enlace de verificación (opcional). Sin
 * ninguna insignia configurada, la sección se oculta por completo
 * (mismo criterio de auto-ocultado ya usado por
 * template-parts/stats.php desde UX-7.6) — a diferencia de
 * Estadísticas, aquí no hay ningún valor previo que preservar: el
 * `default` del theme_mod es una cadena vacía.
 *
 * Registrada en el Home Builder (inc/home-builder.php, clave
 * 'trust_badges') con el mismo mecanismo que el resto de secciones,
 * por lo que queda automáticamente disponible también vía
 * `[ce_section key="trust_badges"]` (UX-6.2) en cuanto exista
 * contenido — sin arquitectura paralela. NO forma parte del orden
 * activo por defecto del Home (mismo criterio que team/clients/faq).
 *
 * Segundo modo de invocación — `$args['compact'] = true`: imprime
 * únicamente la franja de insignias (sin `<section>`/eyebrow/título),
 * en un tamaño reducido pensado para caber dentro de
 * `.ce-hero-quote-card` (ver template-parts/quote-form.php, contexto
 * 'hero') — resolución explícita, en este Entregable, de la nota
 * añadida durante el ajuste puntual de UX-7.2 (D-065): "una vez
 * construido, el componente de insignias/bullets también debe quedar
 * disponible para la tarjeta del Quote Form del Hero". Mismo patrón
 * ya usado por template-parts/content-testimonio-card.php
 * (`$args['compact']`, UX-7.3/D-067) para distinguir una variante
 * reducida del mismo componente sin duplicar el archivo. Este
 * archivo puede invocarse dos veces en la misma carga de página (la
 * sección completa vía el Home Builder + el modo compacto vía
 * template-parts/hero.php) — por eso ningún helper se declara aquí
 * dentro (ver ce_construction_trust_badge_title() en inc/helpers.php).
 *
 * Sin tags dinámicos por interpolación (`<a>`/`<div>`/`<span>` según
 * haya o no `url`): cada rama imprime su propio markup completo,
 * mismo criterio ya usado en el resto del proyecto (ej.
 * template-parts/cta.php, con `if ( $ce_cta_is_sidebar )`) en vez de
 * construir el nombre de la etiqueta HTML por concatenación.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ce_trust_badges_compact = ! empty( $args['compact'] );
$badges                  = ce_construction_get_trust_badges();

if ( empty( $badges ) ) {
	// Sin insignias configuradas (estado por defecto de instalación,
	// o el administrador las quitó todas desde el Customizer): se
	// oculta por completo, tanto en el modo sección completa como en
	// el modo compacto — mismo criterio que template-parts/stats.php.
	return;
}
?>
<?php if ( $ce_trust_badges_compact ) : ?>

	<div class="ce-trust-badges-list ce-trust-badges-list--compact">
		<?php foreach ( $badges as $badge ) : ?>
			<?php
			$ce_badge_media = '';
			if ( $badge['image_id'] ) {
				$ce_badge_media = wp_get_attachment_image( $badge['image_id'], 'thumbnail', false, array( 'alt' => $badge['label'], 'loading' => 'lazy' ) );
			}
			?>
			<?php if ( $badge['url'] ) : ?>
				<a href="<?php echo esc_url( $badge['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ce-trust-badge ce-trust-badge--compact" title="<?php echo esc_attr( ce_construction_trust_badge_title( $badge ) ); ?>">
					<?php if ( $ce_badge_media ) : ?>
						<?php echo $ce_badge_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() ya genera HTML seguro. ?>
					<?php else : ?>
						<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
						<span class="ce-trust-badge__label"><?php echo esc_html( $badge['label'] ); ?></span>
					<?php endif; ?>
				</a>
			<?php else : ?>
				<span class="ce-trust-badge ce-trust-badge--compact" title="<?php echo esc_attr( ce_construction_trust_badge_title( $badge ) ); ?>">
					<?php if ( $ce_badge_media ) : ?>
						<?php echo $ce_badge_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() ya genera HTML seguro. ?>
					<?php else : ?>
						<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
						<span class="ce-trust-badge__label"><?php echo esc_html( $badge['label'] ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>

<?php else : ?>

	<section class="ce-section ce-section--alt" id="ce-trust-badges">
		<div class="ce-container">
			<div class="ce-text-center ce-max-w-content ce-animate-on-scroll" style="margin-inline:auto;">
				<span class="ce-eyebrow"><?php esc_html_e( 'Confianza y Respaldo', 'ce-construction' ); ?></span>
				<h2 class="ce-section-title"><?php esc_html_e( 'Licencias, Certificaciones y Afiliaciones', 'ce-construction' ); ?></h2>
			</div>

			<div class="ce-trust-badges-list">
				<?php foreach ( $badges as $badge ) : ?>
					<?php
					$ce_badge_media = '';
					if ( $badge['image_id'] ) {
						$ce_badge_media = wp_get_attachment_image( $badge['image_id'], 'thumbnail', false, array( 'alt' => $badge['label'], 'loading' => 'lazy' ) );
					}
					?>
					<?php if ( $badge['url'] ) : ?>
						<a href="<?php echo esc_url( $badge['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ce-trust-badge ce-animate-on-scroll">
							<div class="ce-trust-badge__media">
								<?php if ( $ce_badge_media ) : ?>
									<?php echo $ce_badge_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() ya genera HTML seguro. ?>
								<?php else : ?>
									<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
								<?php endif; ?>
							</div>
							<span class="ce-trust-badge__label"><?php echo esc_html( $badge['label'] ); ?></span>
							<?php if ( $badge['license'] ) : ?>
								<span class="ce-badge ce-trust-badge__license">
									<?php
									printf(
										/* translators: %s: número de licencia */
										esc_html__( 'Lic. %s', 'ce-construction' ),
										esc_html( $badge['license'] )
									);
									?>
								</span>
							<?php endif; ?>
						</a>
					<?php else : ?>
						<div class="ce-trust-badge ce-animate-on-scroll">
							<div class="ce-trust-badge__media">
								<?php if ( $ce_badge_media ) : ?>
									<?php echo $ce_badge_media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() ya genera HTML seguro. ?>
								<?php else : ?>
									<i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
								<?php endif; ?>
							</div>
							<span class="ce-trust-badge__label"><?php echo esc_html( $badge['label'] ); ?></span>
							<?php if ( $badge['license'] ) : ?>
								<span class="ce-badge ce-trust-badge__license">
									<?php
									printf(
										/* translators: %s: número de licencia */
										esc_html__( 'Lic. %s', 'ce-construction' ),
										esc_html( $badge['license'] )
									);
									?>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

<?php endif; ?>

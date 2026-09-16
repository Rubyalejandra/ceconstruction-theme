<?php
/**
 * Template part: ¿Por qué elegirnos?
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reasons = apply_filters( 'ce_why_us_items', array(
	array(
		'icon'  => 'fa-solid fa-shield-halved',
		'title' => __( 'Seguridad Garantizada', 'ce-construction' ),
		'text'  => __( 'Cumplimos rigurosos protocolos de seguridad industrial en cada etapa de la obra.', 'ce-construction' ),
	),
	array(
		'icon'  => 'fa-solid fa-clock',
		'title' => __( 'Cumplimiento de Plazos', 'ce-construction' ),
		'text'  => __( 'Planificación detallada para entregar cada proyecto en el tiempo acordado.', 'ce-construction' ),
	),
	array(
		'icon'  => 'fa-solid fa-coins',
		'title' => __( 'Presupuestos Transparentes', 'ce-construction' ),
		'text'  => __( 'Cotizaciones claras, sin costos ocultos, ajustadas a tu presupuesto real.', 'ce-construction' ),
	),
	array(
		'icon'  => 'fa-solid fa-people-group',
		'title' => __( 'Equipo Certificado', 'ce-construction' ),
		'text'  => __( 'Profesionales calificados y en constante capacitación técnica.', 'ce-construction' ),
	),
	array(
		'icon'  => 'fa-solid fa-trowel-bricks',
		'title' => __( 'Materiales de Calidad', 'ce-construction' ),
		'text'  => __( 'Trabajamos únicamente con proveedores certificados y materiales duraderos.', 'ce-construction' ),
	),
	array(
		'icon'  => 'fa-solid fa-headset',
		'title' => __( 'Soporte Postventa', 'ce-construction' ),
		'text'  => __( 'Acompañamiento y garantía después de la entrega de cada proyecto.', 'ce-construction' ),
	),
) );
?>
<section class="ce-section ce-section--alt" id="ce-why-us">
	<div class="ce-container">
		<div class="ce-text-center ce-max-w-content ce-animate-on-scroll">
			<span class="ce-eyebrow"><?php esc_html_e( 'Ventajas', 'ce-construction' ); ?></span>
			<h2 class="ce-section-title"><?php esc_html_e( '¿Por Qué Elegirnos?', 'ce-construction' ); ?></h2>
		</div>

		<div class="ce-grid ce-why-grid ce-grid--3">
			<?php foreach ( $reasons as $reason ) : ?>
				<div class="ce-why-item ce-card ce-animate-on-scroll">
					<div class="ce-card__body">
						<div class="ce-card__icon">
							<i class="<?php echo esc_attr( $reason['icon'] ); ?>" aria-hidden="true"></i>
						</div>
						<h3 class="ce-card__title"><?php echo esc_html( $reason['title'] ); ?></h3>
						<p class="ce-card__text"><?php echo esc_html( $reason['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

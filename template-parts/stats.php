<?php
/**
 * Template part: Estadísticas / contadores animados.
 *
 * Sprint UX-7, Entregable UX-7.6 ("Estadísticas configurables desde
 * el Customizer", ver docs/DECISIONS.md D-070): los valores ya no
 * están hardcodeados en este archivo — se leen desde el theme_mod
 * `ce_stats_custom_items` (control repeater del Customizer, sección
 * "CE: Estadísticas") vía ce_construction_get_stats_items()
 * (inc/helpers.php), que conserva el filtro `ce_stats_items` ya
 * existente como mecanismo de fallback/extensión para
 * desarrolladores, sin eliminarlo.
 *
 * Sin configurar nada en el Customizer, el comportamiento es
 * idéntico al que este archivo tenía antes de este Entregable (las
 * mismas 4 estadísticas, mismo orden) — ver
 * ce_construction_default_stats_items() en inc/helpers.php.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = ce_construction_get_stats_items();

if ( empty( $stats ) ) {
	// El administrador quitó todas las estadísticas desde el
	// Customizer (o un filtro `ce_stats_items` de desarrollador
	// devolvió un array vacío): se oculta la sección completa,
	// mismo criterio de auto-ocultado que ya usan otras secciones
	// del Home basadas en contenido (ej. ce_cpt_has_posts()).
	return;
}
?>
<section class="ce-section ce-stats">
	<div class="ce-container">
		<div class="ce-stats-grid">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="ce-stat ce-animate-on-scroll">
					<div class="ce-stat__number" data-count="<?php echo esc_attr( $stat['count'] ); ?>" data-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>" data-duration="1800">
						<span class="ce-stat__number-value">0</span>
					</div>
					<div class="ce-stat__label">
						<i class="<?php echo esc_attr( $stat['icon'] ); ?>" aria-hidden="true"></i>
						<?php echo esc_html( $stat['label'] ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

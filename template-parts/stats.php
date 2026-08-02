<?php
/**
 * Template part: Estadísticas / contadores animados.
 * Los valores son editables vía filtro `ce_stats_items` si se
 * desea desacoplarlos a futuro del código; por ahora usan
 * theme_mods sencillos para que sean administrables.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = apply_filters( 'ce_stats_items', array(
	array(
		'count'  => 350,
		'suffix' => '+',
		'label'  => __( 'Proyectos realizados', 'ce-construction' ),
		'icon'   => 'fa-solid fa-building',
	),
	array(
		'count'  => 280,
		'suffix' => '+',
		'label'  => __( 'Clientes satisfechos', 'ce-construction' ),
		'icon'   => 'fa-solid fa-face-smile',
	),
	array(
		'count'  => 12,
		'suffix' => '+',
		'label'  => __( 'Años de experiencia', 'ce-construction' ),
		'icon'   => 'fa-solid fa-award',
	),
	array(
		'count'  => 60,
		'suffix' => '+',
		'label'  => __( 'Empleados', 'ce-construction' ),
		'icon'   => 'fa-solid fa-helmet-safety',
	),
) );
?>
<section class="ce-section ce-stats">
	<div class="ce-container">
		<div class="ce-grid ce-grid--4">
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

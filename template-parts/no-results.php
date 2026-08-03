<?php
/**
 * Template part: estado "sin resultados", reutilizado por index.php
 * en 3 escenarios: búsqueda sin resultados, archivo sin contenido,
 * y página 404. Incluye un formulario de búsqueda propio (en vez de
 * get_search_form(), cuyo markup por defecto de WordPress no usa las
 * clases del sistema de diseño del tema) y un enlace de vuelta al inicio.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ce-card ce-max-w-content" style="margin-inline:auto;">
	<div class="ce-card__body ce-text-center">
		<div class="ce-card__icon" style="margin-inline:auto;">
			<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
		</div>
		<h3 class="ce-mb-3"><?php esc_html_e( 'No se encontró contenido', 'ce-construction' ); ?></h3>
		<p class="ce-card__text ce-mb-4"><?php esc_html_e( 'Intenta con otros términos de búsqueda, o vuelve al inicio.', 'ce-construction' ); ?></p>

		<form role="search" method="get" class="ce-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="ce-field">
				<label for="ce-fallback-search" class="ce-sr-only"><?php esc_html_e( 'Buscar', 'ce-construction' ); ?></label>
				<input type="search" id="ce-fallback-search" name="s" placeholder="<?php esc_attr_e( '¿Qué estás buscando?', 'ce-construction' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			</div>
			<button type="submit" class="ce-btn ce-btn--primary ce-btn--block">
				<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
				<?php esc_html_e( 'Buscar', 'ce-construction' ); ?>
			</button>
		</form>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ce-card__link ce-mt-4">
			<i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
			<?php esc_html_e( 'Volver al inicio', 'ce-construction' ); ?>
		</a>
	</div>
</div>

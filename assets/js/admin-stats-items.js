/**
 * CE CONSTRUCTION — ADMIN: ESTADÍSTICAS (Customizer control)
 *
 * Sprint UX-7, Entregable UX-7.6 (fase "Optimización UX / Conversión").
 * Ver docs/DECISIONS.md D-070.
 *
 * Se ejecuta EXCLUSIVAMENTE dentro del admin del Customizer
 * (encolado en customize_controls_enqueue_scripts, ver
 * inc/enqueue.php) — nunca en el frontend público del tema.
 *
 * Responsabilidad única: inicializar el control custom
 * `CE_Customize_Stats_Items_Control` (ver inc/customizer.php) —
 * añadir una fila en blanco, quitar una fila, reordenar con botones
 * "mover antes/después" (mismo criterio que admin-hero-slides.js,
 * sin jQuery UI Sortable — ver DECISIONS.md D-055/D-070), y
 * serializar el resultado como JSON en el <input type="hidden">
 * vinculado al setting `ce_stats_custom_items`.
 *
 * A diferencia de admin-hero-slides.js (IDs de adjuntos de imagen),
 * aquí cada fila son 4 campos de texto editados directamente en el
 * panel — no hay `wp.media` involucrado.
 *
 * Mismo patrón de inicialización que admin-hero-slides.js/
 * admin-home-builder.js: wp.customize.control.bind('add', ...) +
 * deferred.embedded.done().
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	/**
	 * Reconstruye el array de estadísticas a partir del orden y
	 * los valores reales del DOM (tras añadir/quitar/reordenar/
	 * editar), y lo escribe como JSON en el hidden input del
	 * setting, notificando a la API del Customizer con 'change'.
	 */
	function serialize( $list, $input ) {
		var items = [];

		$list.find( '.ce-stats-items-item' ).each( function () {
			var $item = $( this );
			items.push( {
				count:  parseInt( $item.find( '.ce-stats-items-item__count' ).val(), 10 ) || 0,
				suffix: $item.find( '.ce-stats-items-item__suffix' ).val() || '',
				label:  $item.find( '.ce-stats-items-item__label' ).val() || '',
				icon:   $item.find( '.ce-stats-items-item__icon' ).val() || '',
			} );
		} );

		$input.val( JSON.stringify( items ) ).trigger( 'change' );
	}

	/**
	 * Construye el <li> de una fila nueva, en blanco (icono con un
	 * valor por defecto razonable, igual que ce_construction_decode_stats_items()
	 * en PHP hace con filas guardadas sin icono). Los textos de las
	 * etiquetas vienen localizados vía wp_localize_script()
	 * (ceStatsItemsData, inc/enqueue.php) — ningún string se
	 * hardcodea aquí, mismo criterio que admin-hero-slides.js.
	 */
	function buildItem() {
		var $item = $( '<li class="ce-stats-items-item"></li>' );
		var $row = $( '<div class="ce-stats-items-item__row"></div>' );

		$row.append(
			$( '<label></label>' )
				.text( ceStatsItemsData.labelCount )
				.append( $( '<input type="number" min="0" step="1" class="ce-stats-items-item__count" value="0">' ) )
		);
		$row.append(
			$( '<label></label>' )
				.text( ceStatsItemsData.labelSuffix )
				.append( $( '<input type="text" maxlength="6" class="ce-stats-items-item__suffix" value="+">' ) )
		);
		$item.append( $row );

		$item.append(
			$( '<label></label>' )
				.text( ceStatsItemsData.labelLabel )
				.append( $( '<input type="text" maxlength="60" class="ce-stats-items-item__label" value="">' ) )
		);
		$item.append(
			$( '<label></label>' )
				.text( ceStatsItemsData.labelIcon )
				.append( $( '<input type="text" class="ce-stats-items-item__icon" value="fa-solid fa-chart-line">' ) )
		);

		var $actions = $( '<div class="ce-stats-items-item__actions"></div>' );
		$actions.append( $( '<button type="button" class="button ce-stats-items-item__up">&uarr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-stats-items-item__down">&darr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-stats-items-item__remove"></button>' ).text( ceStatsItemsData.labelRemove ) );
		$item.append( $actions );

		return $item;
	}

	/**
	 * Inicializa un control ya insertado en el DOM del Customizer.
	 */
	function initControl( container ) {
		var $list = container.find( '.ce-stats-items-list' );
		var $input = container.find( '.ce-stats-items-value' );
		var $addBtn = container.find( '.ce-stats-items-add' );

		if ( ! $list.length || ! $input.length ) {
			return;
		}

		$addBtn.on( 'click', function ( e ) {
			e.preventDefault();
			$list.append( buildItem() );
			serialize( $list, $input );
		} );

		// Cualquier edición de un campo de texto/número re-serializa
		// de inmediato — el hidden input (y el preview del Customizer,
		// con transport 'refresh') siempre refleja el valor real.
		$list.on( 'input change', 'input', function () {
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-stats-items-item__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.ce-stats-items-item' ).remove();
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-stats-items-item__up', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-stats-items-item' );
			var $prev = $item.prev( '.ce-stats-items-item' );
			if ( $prev.length ) {
				$item.insertBefore( $prev );
				serialize( $list, $input );
			}
		} );

		$list.on( 'click', '.ce-stats-items-item__down', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-stats-items-item' );
			var $next = $item.next( '.ce-stats-items-item' );
			if ( $next.length ) {
				$item.insertAfter( $next );
				serialize( $list, $input );
			}
		} );
	}

	// La API del Customizer añade los controles de forma asíncrona;
	// 'add' se dispara por cada control cuando se registra, y
	// deferred.embedded.done() espera a que su HTML ya esté
	// insertado en el DOM antes de intentar inicializarlo.
	wp.customize.control.bind( 'add', function ( control ) {
		if ( 'ce_stats_items' !== control.params.type ) {
			return;
		}
		control.deferred.embedded.done( function () {
			initControl( control.container );
		} );
	} );
} )( jQuery );

/**
 * CE CONSTRUCTION — ADMIN: HOME BUILDER (Customizer control)
 *
 * Sprint UX-1, Entregable UX-1.2 (fase "Optimización UX / Conversión").
 *
 * Se ejecuta EXCLUSIVAMENTE dentro del admin del Customizer
 * (encolado en customize_controls_enqueue_scripts, ver
 * inc/enqueue.php) — nunca en el frontend público del tema.
 *
 * Responsabilidad única: inicializar el drag&drop (jQuery UI
 * Sortable, ya incluido en WordPress core — sin librerías nuevas)
 * y las casillas de activar/desactivar del control custom
 * `CE_Customize_Home_Sections_Control` (ver inc/customizer.php),
 * serializando el estado resultante como JSON en el <input type
 * ="hidden"> vinculado al setting `ce_home_sections_order` para
 * que la API del Customizer lo detecte como un cambio.
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	/**
	 * Reconstruye el JSON [{key, enabled}, ...] a partir del orden
	 * real del DOM (tras un drag&drop) y del estado de cada casilla,
	 * y lo escribe en el hidden input del setting, notificando a la
	 * API del Customizer con 'change' para que quede marcado como
	 * "sin publicar" hasta que el usuario pulse Publicar.
	 */
	function serialize( $list, $input ) {
		var order = [];

		$list.find( '.ce-home-builder-item' ).each( function () {
			var $item = $( this );
			order.push( {
				key: $item.data( 'key' ),
				enabled: $item.find( '.ce-home-builder-item__enabled' ).is( ':checked' ),
			} );
		} );

		$input.val( JSON.stringify( order ) ).trigger( 'change' );
	}

	/**
	 * Inicializa un control ya insertado en el DOM del Customizer.
	 */
	function initControl( container ) {
		var $list = container.find( '.ce-home-builder-list' );
		var $input = container.find( '.ce-home-builder-value' );

		if ( ! $list.length || ! $input.length ) {
			return;
		}

		$list.sortable( {
			handle: '.ce-home-builder-item__handle',
			axis: 'y',
			update: function () {
				serialize( $list, $input );
			},
		} );

		$list.on( 'change', '.ce-home-builder-item__enabled', function () {
			serialize( $list, $input );
		} );
	}

	// La API del Customizer añade los controles de forma asíncrona;
	// 'add' se dispara por cada control cuando se registra, y
	// deferred.embedded.done() espera a que su HTML ya esté
	// insertado en el DOM antes de intentar inicializarlo.
	wp.customize.control.bind( 'add', function ( control ) {
		if ( 'ce_home_sections' !== control.params.type ) {
			return;
		}
		control.deferred.embedded.done( function () {
			initControl( control.container );
		} );
	} );
} )( jQuery );

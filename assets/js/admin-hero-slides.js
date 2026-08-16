/**
 * CE CONSTRUCTION — ADMIN: HERO SLIDES (Customizer control)
 *
 * Sprint UX-4, Entregable UX-4.2 (fase "Optimización UX / Conversión").
 *
 * Se ejecuta EXCLUSIVAMENTE dentro del admin del Customizer
 * (encolado en customize_controls_enqueue_scripts, ver
 * inc/enqueue.php) — nunca en el frontend público del tema.
 *
 * Responsabilidad única: inicializar el control custom
 * `CE_Customize_Hero_Slides_Control` (ver inc/customizer.php) —
 * añadir imágenes vía `wp.media` (mismo mecanismo ya usado en
 * inc/meta-boxes.php::ce_render_proyecto_gallery() para la galería
 * de Proyecto, adaptado aquí de metabox a control de Customizer),
 * quitar una imagen, y reordenar con botones "mover antes/después"
 * (sin jQuery UI Sortable — ver DECISIONS.md D-055 para el porqué
 * de esta elección frente al arrastre de admin-home-builder.js),
 * serializando el resultado como IDs separados por comas en el
 * <input type="hidden"> vinculado al setting `ce_hero_slides`.
 *
 * Mismo patrón de inicialización que admin-home-builder.js:
 * wp.customize.control.bind('add', ...) + deferred.embedded.done().
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	/**
	 * Reconstruye la lista de IDs a partir del orden real del DOM
	 * (tras añadir/quitar/reordenar) y la escribe en el hidden input
	 * del setting, notificando a la API del Customizer con 'change'.
	 */
	function serialize( $list, $input ) {
		var ids = [];

		$list.find( '.ce-hero-slides-item' ).each( function () {
			ids.push( $( this ).data( 'id' ) );
		} );

		$input.val( ids.join( ',' ) ).trigger( 'change' );
	}

	/**
	 * Construye el <li> de una miniatura ya seleccionada.
	 */
	function buildItem( id, thumbUrl ) {
		var $item = $( '<li class="ce-hero-slides-item"></li>' ).attr( 'data-id', id );
		$item.append( $( '<img>' ).attr( 'src', thumbUrl ).attr( 'alt', '' ) );
		$item.append( $( '<button type="button" class="ce-hero-slides-item__up">&uarr;</button>' ) );
		$item.append( $( '<button type="button" class="ce-hero-slides-item__down">&darr;</button>' ) );
		$item.append( $( '<button type="button" class="ce-hero-slides-item__remove">&times;</button>' ) );
		return $item;
	}

	/**
	 * Inicializa un control ya insertado en el DOM del Customizer.
	 */
	function initControl( container ) {
		var $list = container.find( '.ce-hero-slides-list' );
		var $input = container.find( '.ce-hero-slides-value' );
		var $addBtn = container.find( '.ce-hero-slides-add' );

		if ( ! $list.length || ! $input.length ) {
			return;
		}

		$addBtn.on( 'click', function ( e ) {
			e.preventDefault();

			var frame = wp.media( {
				title: ceHeroSlidesData.mediaTitle,
				button: { text: ceHeroSlidesData.mediaButton },
				multiple: true,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				frame.state().get( 'selection' ).each( function ( attachment ) {
					var data = attachment.toJSON();
					var thumbUrl = ( data.sizes && data.sizes.thumbnail ) ? data.sizes.thumbnail.url : data.url;

					// Evita duplicados si la imagen ya está en la lista.
					if ( $list.find( '[data-id="' + data.id + '"]' ).length ) {
						return;
					}

					$list.append( buildItem( data.id, thumbUrl ) );
				} );
				serialize( $list, $input );
			} );

			frame.open();
		} );

		$list.on( 'click', '.ce-hero-slides-item__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.ce-hero-slides-item' ).remove();
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-hero-slides-item__up', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-hero-slides-item' );
			var $prev = $item.prev( '.ce-hero-slides-item' );
			if ( $prev.length ) {
				$item.insertBefore( $prev );
				serialize( $list, $input );
			}
		} );

		$list.on( 'click', '.ce-hero-slides-item__down', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-hero-slides-item' );
			var $next = $item.next( '.ce-hero-slides-item' );
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
		if ( 'ce_hero_slides' !== control.params.type ) {
			return;
		}
		control.deferred.embedded.done( function () {
			initControl( control.container );
		} );
	} );
} )( jQuery );

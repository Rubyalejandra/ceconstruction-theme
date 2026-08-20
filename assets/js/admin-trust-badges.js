/**
 * CE CONSTRUCTION — ADMIN: INSIGNIAS DE CONFIANZA (Customizer control)
 *
 * Sprint UX-7, Entregable UX-7.7 (fase "Optimización UX / Conversión").
 * Ver docs/DECISIONS.md D-071.
 *
 * Se ejecuta EXCLUSIVAMENTE dentro del admin del Customizer
 * (encolado en customize_controls_enqueue_scripts, ver
 * inc/enqueue.php) — nunca en el frontend público del tema.
 *
 * Responsabilidad única: inicializar el control custom
 * `CE_Customize_Trust_Badges_Control` (ver inc/customizer.php) —
 * añadir una fila en blanco, quitar una fila, reordenar con botones
 * "mover antes/después" (mismo criterio que admin-hero-slides.js/
 * admin-stats-items.js, sin jQuery UI Sortable), seleccionar/quitar
 * la imagen opcional de una fila vía `wp.media` (mismo mecanismo que
 * admin-hero-slides.js, adaptado de "una imagen por fila" a "una
 * imagen opcional entre varios campos por fila"), y serializar el
 * resultado como JSON en el <input type="hidden"> vinculado al
 * setting `ce_trust_badges_items`.
 *
 * Mismo patrón de inicialización que admin-hero-slides.js/
 * admin-stats-items.js: wp.customize.control.bind('add', ...) +
 * deferred.embedded.done().
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	/**
	 * Reconstruye el array de insignias a partir del orden y los
	 * valores reales del DOM (tras añadir/quitar/reordenar/editar/
	 * cambiar imagen), y lo escribe como JSON en el hidden input del
	 * setting, notificando a la API del Customizer con 'change'.
	 */
	function serialize( $list, $input ) {
		var items = [];

		$list.find( '.ce-trust-badges-items-item' ).each( function () {
			var $item = $( this );
			items.push( {
				image_id: parseInt( $item.data( 'image-id' ), 10 ) || 0,
				label:    $item.find( '.ce-trust-badges-items-item__label' ).val() || '',
				license:  $item.find( '.ce-trust-badges-items-item__license' ).val() || '',
				url:      $item.find( '.ce-trust-badges-items-item__url' ).val() || '',
			} );
		} );

		$input.val( JSON.stringify( items ) ).trigger( 'change' );
	}

	/**
	 * Actualiza la miniatura de vista previa y el botón "Quitar
	 * imagen" de una fila tras seleccionar/quitar una imagen.
	 */
	function updateImagePreview( $item, imageId, thumbUrl ) {
		var $preview = $item.find( '.ce-trust-badges-items-item__preview' );
		var $removeBtn = $item.find( '.ce-trust-badges-items-item__remove-image' );

		$item.attr( 'data-image-id', imageId || 0 );
		$preview.empty();

		if ( imageId && thumbUrl ) {
			$preview.append( $( '<img>' ).attr( 'src', thumbUrl ).attr( 'alt', '' ) );
			$removeBtn.show();
		} else {
			$removeBtn.hide();
		}
	}

	/**
	 * Construye el <li> de una fila nueva, en blanco (sin imagen).
	 * Los textos de las etiquetas vienen localizados vía
	 * wp_localize_script() (ceTrustBadgesData, inc/enqueue.php) —
	 * ningún string se hardcodea aquí, mismo criterio que
	 * admin-hero-slides.js/admin-stats-items.js.
	 */
	function buildItem() {
		var $item = $( '<li class="ce-trust-badges-items-item" data-image-id="0"></li>' );

		var $media = $( '<div class="ce-trust-badges-items-item__media"></div>' );
		$media.append( $( '<div class="ce-trust-badges-items-item__preview"></div>' ) );
		$media.append( $( '<button type="button" class="button ce-trust-badges-items-item__select-image"></button>' ).text( ceTrustBadgesData.labelSelect ) );
		$media.append( $( '<button type="button" class="button ce-trust-badges-items-item__remove-image" style="display:none;"></button>' ).text( ceTrustBadgesData.labelRemoveImage ) );
		$item.append( $media );

		$item.append(
			$( '<label></label>' )
				.text( ceTrustBadgesData.labelLabel )
				.append( $( '<input type="text" maxlength="60" class="ce-trust-badges-items-item__label" value="">' ) )
		);

		var $row = $( '<div class="ce-trust-badges-items-item__row"></div>' );
		$row.append(
			$( '<label></label>' )
				.text( ceTrustBadgesData.labelLicense )
				.append( $( '<input type="text" maxlength="40" class="ce-trust-badges-items-item__license" value="">' ) )
		);
		$row.append(
			$( '<label></label>' )
				.text( ceTrustBadgesData.labelUrl )
				.append( $( '<input type="url" class="ce-trust-badges-items-item__url" value="">' ) )
		);
		$item.append( $row );

		var $actions = $( '<div class="ce-trust-badges-items-item__actions"></div>' );
		$actions.append( $( '<button type="button" class="button ce-trust-badges-items-item__up">&uarr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-trust-badges-items-item__down">&darr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-trust-badges-items-item__remove"></button>' ).text( ceTrustBadgesData.labelRemove ) );
		$item.append( $actions );

		return $item;
	}

	/**
	 * Inicializa un control ya insertado en el DOM del Customizer.
	 */
	function initControl( container ) {
		var $list = container.find( '.ce-trust-badges-items-list' );
		var $input = container.find( '.ce-trust-badges-items-value' );
		var $addBtn = container.find( '.ce-trust-badges-items-add' );

		if ( ! $list.length || ! $input.length ) {
			return;
		}

		$addBtn.on( 'click', function ( e ) {
			e.preventDefault();
			$list.append( buildItem() );
			serialize( $list, $input );
		} );

		// Cualquier edición de un campo de texto re-serializa de
		// inmediato — el hidden input (y el preview del Customizer,
		// con transport 'refresh') siempre refleja el valor real.
		$list.on( 'input change', 'input', function () {
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-trust-badges-items-item__select-image', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-trust-badges-items-item' );

			var frame = wp.media( {
				title: ceTrustBadgesData.mediaTitle,
				button: { text: ceTrustBadgesData.mediaButton },
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var thumbUrl = ( attachment.sizes && attachment.sizes.thumbnail ) ? attachment.sizes.thumbnail.url : attachment.url;
				updateImagePreview( $item, attachment.id, thumbUrl );
				serialize( $list, $input );
			} );

			frame.open();
		} );

		$list.on( 'click', '.ce-trust-badges-items-item__remove-image', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-trust-badges-items-item' );
			updateImagePreview( $item, 0, '' );
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-trust-badges-items-item__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.ce-trust-badges-items-item' ).remove();
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-trust-badges-items-item__up', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-trust-badges-items-item' );
			var $prev = $item.prev( '.ce-trust-badges-items-item' );
			if ( $prev.length ) {
				$item.insertBefore( $prev );
				serialize( $list, $input );
			}
		} );

		$list.on( 'click', '.ce-trust-badges-items-item__down', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-trust-badges-items-item' );
			var $next = $item.next( '.ce-trust-badges-items-item' );
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
		if ( 'ce_trust_badges_items' !== control.params.type ) {
			return;
		}
		control.deferred.embedded.done( function () {
			initControl( control.container );
		} );
	} );
} )( jQuery );

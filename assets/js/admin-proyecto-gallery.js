/**
 * CE CONSTRUCTION — ADMIN: GALERÍA MIXTA DE PROYECTO (metabox)
 *
 * Sprint UX-8, Entregable UX-8.1 ("Galería mixta del Proyecto: imagen
 * y/o video, reordenable"). Ver docs/DECISIONS.md D-108.
 *
 * Reemplaza al selector de solo-imágenes que existía desde QA-016
 * (Sprint 8, Entregable 8.5) por un repeater de ítems mixtos —
 * imagen (wp.media, multi-selección), video de la Biblioteca de
 * Medios (wp.media, selección única, acotado a tipo 'video'), o
 * video por URL externa (campo de texto, resuelto por oEmbed en
 * el servidor al renderizar, no aquí) — reordenables con botones
 * "mover antes/después" (mismo criterio ya usado por
 * admin-hero-slides.js/admin-stats-items.js/admin-trust-badges.js:
 * sin jQuery UI Sortable).
 *
 * Serializa el estado actual como JSON en el <input type="hidden"
 * id="ce_proyecto_media">, leído/saneado en el servidor por
 * ce_construction_decode_proyecto_media_json() (inc/helpers.php) al
 * guardar el proyecto — ver ce_construction_save_meta_boxes()
 * (inc/meta-boxes.php).
 *
 * Se ejecuta EXCLUSIVAMENTE en la pantalla de edición del CPT
 * `proyecto` (ver condición de carga en inc/enqueue.php) — nunca en
 * el frontend público del tema ni en otras pantallas del admin.
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	/**
	 * Reconstruye el array de ítems a partir del orden y los datos
	 * reales del DOM (tras añadir/quitar/reordenar), y lo escribe
	 * como JSON en el hidden input `#ce_proyecto_media`.
	 */
	function serialize( $list, $input ) {
		var items = [];

		$list.find( '.ce-proyecto-media-item' ).each( function () {
			var $item = $( this );
			var type = $item.data( 'type' );

			if ( 'image' === type || 'video-local' === type ) {
				items.push( { type: type, id: parseInt( $item.data( 'id' ), 10 ) || 0 } );
			} else if ( 'video-embed' === type ) {
				items.push( { type: 'video-embed', url: String( $item.data( 'url' ) || '' ) } );
			}
		} );

		$input.val( JSON.stringify( items ) );
	}

	/**
	 * Construye el <li> de un ítem de imagen recién añadido.
	 */
	function buildImageItem( id, thumbUrl ) {
		var $item = $( '<li class="ce-proyecto-media-item"></li>' )
			.attr( 'data-type', 'image' )
			.attr( 'data-id', id );

		$item.append( $( '<span class="ce-proyecto-media-item__preview"></span>' ).append( $( '<img>' ).attr( 'src', thumbUrl ).attr( 'alt', '' ) ) );
		$item.append( $( '<span class="ce-proyecto-media-item__label"></span>' ).text( ceProyectoGalleryData.labelImage || 'Imagen' ) );
		$item.append( buildActions() );

		return $item;
	}

	/**
	 * Construye el <li> de un ítem de video (local o embebido).
	 */
	function buildVideoItem( type, idOrUrl, label ) {
		var $item = $( '<li class="ce-proyecto-media-item"></li>' ).attr( 'data-type', type );

		if ( 'video-local' === type ) {
			$item.attr( 'data-id', idOrUrl );
		} else {
			$item.attr( 'data-url', idOrUrl );
		}

		$item.append( $( '<span class="ce-proyecto-media-item__preview ce-proyecto-media-item__preview--icon"></span>' ).append( $( '<span class="dashicons dashicons-video-alt3"></span>' ) ) );
		$item.append( $( '<span class="ce-proyecto-media-item__label"></span>' ).text( label ) );
		$item.append( buildActions() );

		return $item;
	}

	function buildActions() {
		var $actions = $( '<span class="ce-proyecto-media-item__actions"></span>' );
		$actions.append( $( '<button type="button" class="button ce-proyecto-media-item__up">&uarr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-proyecto-media-item__down">&darr;</button>' ) );
		$actions.append( $( '<button type="button" class="button ce-proyecto-media-item__remove">&times;</button>' ) );
		return $actions;
	}

	/**
	 * Inicializa el metabox ya insertado en el DOM.
	 */
	function initControl() {
		var $list  = $( '#ce-proyecto-media-list' );
		var $input = $( '#ce_proyecto_media' );

		if ( ! $list.length || ! $input.length ) {
			return;
		}

		$( '#ce-proyecto-media-add-images' ).on( 'click', function ( e ) {
			e.preventDefault();

			var frame = wp.media( {
				title: ceProyectoGalleryData.mediaTitleImages,
				button: { text: ceProyectoGalleryData.mediaButtonImages },
				multiple: true,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				frame.state().get( 'selection' ).each( function ( attachment ) {
					var data = attachment.toJSON();
					var thumbUrl = ( data.sizes && data.sizes.thumbnail ) ? data.sizes.thumbnail.url : data.url;
					$list.append( buildImageItem( data.id, thumbUrl ) );
				} );
				serialize( $list, $input );
			} );

			frame.open();
		} );

		$( '#ce-proyecto-media-add-video-local' ).on( 'click', function ( e ) {
			e.preventDefault();

			var frame = wp.media( {
				title: ceProyectoGalleryData.mediaTitleVideo,
				button: { text: ceProyectoGalleryData.mediaButtonVideo },
				multiple: false,
				library: { type: 'video' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var label = attachment.filename || attachment.title || ceProyectoGalleryData.labelVideo;
				$list.append( buildVideoItem( 'video-local', attachment.id, label ) );
				serialize( $list, $input );
			} );

			frame.open();
		} );

		$( '#ce-proyecto-media-add-video-url' ).on( 'click', function ( e ) {
			e.preventDefault();

			var $urlInput = $( '#ce-proyecto-media-video-url-input' );
			var url = ( $urlInput.val() || '' ).trim();

			if ( ! url ) {
				window.alert( ceProyectoGalleryData.labelInvalidUrl );
				return;
			}

			$list.append( buildVideoItem( 'video-embed', url, url ) );
			serialize( $list, $input );
			$urlInput.val( '' );
		} );

		$list.on( 'click', '.ce-proyecto-media-item__remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.ce-proyecto-media-item' ).remove();
			serialize( $list, $input );
		} );

		$list.on( 'click', '.ce-proyecto-media-item__up', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-proyecto-media-item' );
			var $prev = $item.prev( '.ce-proyecto-media-item' );
			if ( $prev.length ) {
				$item.insertBefore( $prev );
				serialize( $list, $input );
			}
		} );

		$list.on( 'click', '.ce-proyecto-media-item__down', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.ce-proyecto-media-item' );
			var $next = $item.next( '.ce-proyecto-media-item' );
			if ( $next.length ) {
				$item.insertAfter( $next );
				serialize( $list, $input );
			}
		} );
	}

	$( initControl );
} )( jQuery );

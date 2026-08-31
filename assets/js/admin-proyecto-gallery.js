/**
 * CE CONSTRUCTION — ADMIN: GALERÍA DE PROYECTO (metabox)
 *
 * QA-016 (Sprint 8, Entregable 8.5 — corrección Media).
 *
 * Antes de esta corrección, esta lógica vivía como un <script> inline
 * impreso directamente por ce_render_proyecto_gallery() (ver
 * inc/meta-boxes.php), sin pasar por wp_enqueue_script() ni declarar
 * 'jquery' como dependencia formal — funcionaba solo porque jQuery ya
 * estaba cargado de casualidad por otro script del admin de WordPress
 * en esa misma pantalla, sin ninguna garantía explícita de orden de
 * carga. Se extrae aquí, sin cambiar ningún comportamiento (mismo
 * selector, mismo evento, mismo marcado generado), y se encola en
 * inc/enqueue.php con array( 'jquery', 'media-editor' ) como
 * dependencia declarada — mismo patrón ya usado por
 * admin-hero-slides.js/admin-trust-badges.js para sus propios
 * selectores de imagen vía wp.media.
 *
 * Se ejecuta EXCLUSIVAMENTE en la pantalla de edición del CPT
 * `proyecto` (ver condición de carga en inc/enqueue.php) — nunca en
 * el frontend público del tema ni en otras pantallas del admin.
 *
 * Localización: el título de la ventana de wp.media, antes
 * hardcodeado en el <script> inline ('Seleccionar imágenes'), ahora
 * se recibe vía wp_localize_script() (ceProyectoGalleryData.mediaTitle)
 * — mismo patrón ya usado en admin-hero-slides.js/admin-trust-badges.js
 * — para que quede cubierto por el catálogo de traducción del tema en
 * vez de quedar fuera de él.
 *
 * @package CE_Construction
 */

(function ( $ ) {
	'use strict';

	$( function () {
		var $uploadBtn = $( '#ce-gallery-upload-btn' );
		if ( ! $uploadBtn.length ) {
			return;
		}

		$uploadBtn.on( 'click', function ( e ) {
			e.preventDefault();

			var frame = wp.media( {
				title: ceProyectoGalleryData.mediaTitle,
				multiple: true,
			} );

			frame.on( 'select', function () {
				var selection = frame.state().get( 'selection' );
				var ids = selection.map( function ( attachment ) {
					return attachment.id;
				} );

				$( '#ce_proyecto_galeria' ).val( ids.join( ',' ) );

				var $preview = $( '#ce-gallery-preview' );
				$preview.empty();
				selection.each( function ( attachment ) {
					$preview.append(
						$( '<img>' )
							.attr( 'src', attachment.attributes.sizes.thumbnail.url )
							.css( { width: '80px', height: '80px', objectFit: 'cover' } )
					);
				} );
			} );

			frame.open();
		} );
	} );
} )( jQuery );

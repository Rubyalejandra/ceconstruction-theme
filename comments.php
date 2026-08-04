<?php
/**
 * comments.php — Plantilla de comentarios.
 *
 * Usada por single.php (Entradas de blog) y, si el admin habilita
 * comentarios en una página puntual, también por page.php. Antes de
 * este archivo, ambos usaban el fallback de compatibilidad nativo de
 * WordPress (theme-compat/comments.php), funcional pero sin el estilo
 * del sistema de diseño del tema.
 *
 * WordPress exige que este archivo, si existe, verifique
 * post_password_required() y no muestre nada si la página está
 * protegida por contraseña y esta no se ha introducido.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}

/**
 * Callback de renderizado de cada comentario individual, usado por
 * wp_list_comments() más abajo. Se define aquí (no en inc/helpers.php)
 * porque es marcado de presentación específico de esta plantilla,
 * sin reutilización prevista en otro contexto del tema.
 */
if ( ! function_exists( 'ce_construction_render_comment' ) ) {
	function ce_construction_render_comment( $comment, $args, $depth ) {
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
		?>
		<<?php echo esc_attr( $tag ); ?> <?php comment_class( 'ce-comment' ); ?> id="comment-<?php comment_ID(); ?>">
			<article class="ce-comment__body" id="div-comment-<?php comment_ID(); ?>">
				<div class="ce-comment__avatar">
					<?php echo get_avatar( $comment, 56 ); ?>
				</div>
				<div class="ce-comment__content-wrap">
					<div class="ce-comment__meta">
						<span class="ce-comment__author"><?php comment_author(); ?></span>
						<span class="ce-comment__date">
							<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
								<?php
								printf(
									/* translators: 1: fecha del comentario, 2: hora del comentario */
									esc_html__( '%1$s a las %2$s', 'ce-construction' ),
									esc_html( get_comment_date( '', $comment ) ),
									esc_html( get_comment_time() )
								);
								?>
							</a>
						</span>
						<?php if ( '0' === $comment->comment_approved ) : ?>
							<span class="ce-badge"><?php esc_html_e( 'Pendiente de aprobación', 'ce-construction' ); ?></span>
						<?php endif; ?>
					</div>

					<div class="ce-comment__text">
						<?php comment_text(); ?>
					</div>

					<?php
					comment_reply_link( array_merge( $args, array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="ce-comment__reply">',
						'after'     => '</div>',
						'reply_text' => '<i class="fa-solid fa-reply" aria-hidden="true"></i> ' . __( 'Responder', 'ce-construction' ),
					) ) );
					?>
				</div>
			</article>
		<?php
		// La etiqueta de cierre la imprime WordPress automáticamente
		// al terminar cada <li> del árbol de comentarios anidados.
	}
}
?>

<div id="comments" class="ce-comments">

	<?php if ( have_comments() ) : ?>

		<h2 class="ce-comments__title ce-mb-4">
			<?php
			$comments_number = get_comments_number();
			if ( 1 === (int) $comments_number ) {
				esc_html_e( '1 comentario', 'ce-construction' );
			} else {
				printf(
					/* translators: %s: número de comentarios */
					esc_html( _n( '%s comentario', '%s comentarios', $comments_number, 'ce-construction' ) ),
					esc_html( number_format_i18n( $comments_number ) )
				);
			}
			?>
		</h2>

		<ol class="ce-comments__list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 56,
				'callback'    => 'ce_construction_render_comment',
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> ' . esc_html__( 'Anteriores', 'ce-construction' ),
			'next_text' => esc_html__( 'Siguientes', 'ce-construction' ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',
		) );
		?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

		<p class="ce-comments__closed"><?php esc_html_e( 'Los comentarios están cerrados para esta entrada.', 'ce-construction' ); ?></p>

	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'        => __( 'Deja un comentario', 'ce-construction' ),
		'title_reply_before' => '<h3 class="ce-comments__form-title ce-mb-4">',
		'title_reply_after'  => '</h3>',
		'class_form'         => 'ce-form ce-comments__form',
		'comment_field'      => '<div class="ce-field"><label for="comment">' . esc_html__( 'Comentario', 'ce-construction' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" rows="5" required></textarea></div>',
		'fields'             => array(
			'author' => '<div class="ce-form__row ce-form__row--2"><div class="ce-field"><label for="author">' . esc_html__( 'Nombre', 'ce-construction' ) . ' <span class="required">*</span></label><input id="author" name="author" type="text" required></div>',
			'email'  => '<div class="ce-field"><label for="email">' . esc_html__( 'Correo', 'ce-construction' ) . ' <span class="required">*</span></label><input id="email" name="email" type="email" required></div></div>',
		),
		'class_submit'       => 'ce-btn ce-btn--primary',
		'label_submit'       => __( 'Publicar comentario', 'ce-construction' ),
		'submit_button'      => '<button type="submit" class="%3$s" id="%1$s">%4$s</button>',
	) );
	?>

</div>

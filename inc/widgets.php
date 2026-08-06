<?php
/**
 * Widgets personalizados del tema.
 *
 * Diseñados para las áreas de sidebar del footer ya registradas en
 * inc/setup.php (footer-1, footer-2) — en particular footer-1, que
 * quedó renderizable desde la corrección QA-006 (v0.4.1) pero sin
 * ningún widget que un administrador pudiera agregarle todavía.
 *
 * Reutilizan deliberadamente helpers y clases CSS ya existentes
 * (ce_get_social_links(), .ce-footer__social, .ce-footer__contact-item)
 * en vez de imprimir markup o CSS nuevo — sin código duplicado.
 *
 * @package CE_Construction
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Información de Contacto.
 * Si un campo se deja vacío en el widget, cae de vuelta al valor
 * ya configurado en el Customizer (mismo dato, sin duplicar captura).
 */
class CE_Construction_Widget_Contact extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'ce_construction_widget_contact',
			__( 'CE: Información de Contacto', 'ce-construction' ),
			array(
				'description' => __( 'Teléfono, correo, dirección y horario. Los campos vacíos usan el valor ya configurado en el Personalizador.', 'ce-construction' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		$title    = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$phone    = ! empty( $instance['phone'] ) ? $instance['phone'] : get_theme_mod( 'ce_phone', '' );
		$email    = ! empty( $instance['email'] ) ? $instance['email'] : get_theme_mod( 'ce_email', '' );
		$address  = ! empty( $instance['address'] ) ? $instance['address'] : get_theme_mod( 'ce_address', '' );
		$schedule = ! empty( $instance['schedule'] ) ? $instance['schedule'] : get_theme_mod( 'ce_schedule', '' );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- before/after_widget vienen de register_sidebar(), controlados por el tema.

		if ( $title ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $title, $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- idem.
		}

		if ( $address ) {
			echo '<div class="ce-footer__contact-item"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>' . esc_html( $address ) . '</span></div>';
		}
		if ( $phone ) {
			echo '<div class="ce-footer__contact-item"><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></div>';
		}
		if ( $email ) {
			echo '<div class="ce-footer__contact-item"><i class="fa-solid fa-envelope" aria-hidden="true"></i><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></div>';
		}
		if ( $schedule ) {
			echo '<div class="ce-footer__contact-item"><i class="fa-regular fa-clock" aria-hidden="true"></i><span>' . nl2br( esc_html( $schedule ) ) . '</span></div>';
		}

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- idem.
	}

	public function form( $instance ) {
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$phone    = isset( $instance['phone'] ) ? $instance['phone'] : '';
		$email    = isset( $instance['email'] ) ? $instance['email'] : '';
		$address  = isset( $instance['address'] ) ? $instance['address'] : '';
		$schedule = isset( $instance['schedule'] ) ? $instance['schedule'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título:', 'ce-construction' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>"><?php esc_html_e( 'Teléfono (vacío = usa el del Personalizador):', 'ce-construction' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php esc_html_e( 'Correo (vacío = usa el del Personalizador):', 'ce-construction' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>"><?php esc_html_e( 'Dirección (vacío = usa la del Personalizador):', 'ce-construction' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>" type="text" value="<?php echo esc_attr( $address ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'schedule' ) ); ?>"><?php esc_html_e( 'Horario (vacío = usa el del Personalizador):', 'ce-construction' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'schedule' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'schedule' ) ); ?>" rows="3"><?php echo esc_textarea( $schedule ); ?></textarea>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['phone']     = sanitize_text_field( $new_instance['phone'] );
		$instance['email']     = sanitize_text_field( $new_instance['email'] );
		$instance['address']   = sanitize_text_field( $new_instance['address'] );
		$instance['schedule']  = sanitize_textarea_field( $new_instance['schedule'] );
		return $instance;
	}
}

/**
 * Widget: Redes Sociales.
 * Sin campos de URL propios: reutiliza ce_get_social_links() para
 * evitar una segunda fuente de verdad frente al Personalizador
 * (CE: Redes Sociales, ya existente en inc/customizer.php).
 */
class CE_Construction_Widget_Social extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'ce_construction_widget_social',
			__( 'CE: Redes Sociales', 'ce-construction' ),
			array(
				'description' => __( 'Muestra los iconos de redes sociales ya configurados en el Personalizador.', 'ce-construction' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		if ( ! function_exists( 'ce_get_social_links' ) ) {
			return;
		}

		$links = ce_get_social_links();
		if ( empty( $links ) ) {
			return;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- before/after_widget vienen de register_sidebar().

		if ( $title ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $title, $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- idem.
		}

		echo '<div class="ce-footer__social">';
		foreach ( $links as $data ) {
			printf(
				'<a href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s"><i class="%3$s" aria-hidden="true"></i></a>',
				esc_url( $data['url'] ),
				esc_attr( $data['label'] ),
				esc_attr( $data['icon'] )
			);
		}
		echo '</div>';

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- idem.
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título:', 'ce-construction' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'Los enlaces se toman automáticamente del Personalizador → CE: Redes Sociales.', 'ce-construction' ); ?></p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance           = array();
		$instance['title']  = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}

/**
 * Registro de los widgets del tema.
 */
function ce_construction_register_widgets() {
	register_widget( 'CE_Construction_Widget_Contact' );
	register_widget( 'CE_Construction_Widget_Social' );
}
add_action( 'widgets_init', 'ce_construction_register_widgets' );

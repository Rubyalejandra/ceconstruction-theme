<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="ce-sr-only" href="#ce-main-content"><?php esc_html_e( 'Saltar al contenido principal', 'ce-construction' ); ?></a>

<header class="ce-header" id="ce-header">

	<!-- Barra superior: contacto rápido -->
	<div class="ce-header__top">
		<div class="ce-container">
			<div class="ce-header__contact">
				<?php if ( get_theme_mod( 'ce_phone' ) ) : ?>
					<a href="tel:<?php echo esc_attr( ce_get_phone_href() ); ?>">
						<i class="fa-solid fa-phone" aria-hidden="true"></i>
						<?php echo esc_html( get_theme_mod( 'ce_phone' ) ); ?>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'ce_email' ) ) : ?>
					<a href="mailto:<?php echo esc_attr( get_theme_mod( 'ce_email' ) ); ?>">
						<i class="fa-solid fa-envelope" aria-hidden="true"></i>
						<?php echo esc_html( get_theme_mod( 'ce_email' ) ); ?>
					</a>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'ce_schedule' ) ) : ?>
					<span><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo esc_html( get_theme_mod( 'ce_schedule' ) ); ?></span>
				<?php endif; ?>
			</div>
			<?php ce_render_social_icons( 'header' ); ?>
		</div>
	</div>

	<!-- Header principal -->
	<div class="ce-header__main">
		<div class="ce-container">

			<div class="ce-header__logo">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
			</div>

			<nav class="ce-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'ce-construction' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'ce-nav__list',
					'fallback_cb'    => false,
					'depth'          => 2,
				) );
				?>
			</nav>

			<div class="ce-header__actions">
				<?php if ( get_theme_mod( 'ce_phone' ) ) : ?>
					<a class="ce-header__phone" href="tel:<?php echo esc_attr( ce_get_phone_href() ); ?>">
						<i class="fa-solid fa-phone" aria-hidden="true"></i>
						<?php echo esc_html( get_theme_mod( 'ce_phone' ) ); ?>
					</a>
				<?php endif; ?>

				<a href="#ce-quote-form" class="ce-btn ce-btn--primary ce-btn--sm">
					<?php esc_html_e( 'Cotizar', 'ce-construction' ); ?>
				</a>

				<button class="ce-nav-toggle" aria-label="<?php esc_attr_e( 'Abrir menú', 'ce-construction' ); ?>" aria-expanded="false" aria-controls="ce-nav-mobile">
					<span></span><span></span><span></span>
				</button>
			</div>

		</div>
	</div>
</header>

<!-- Menú móvil off-canvas -->
<div class="ce-nav-overlay"></div>
<nav id="ce-nav-mobile" class="ce-nav-mobile" aria-label="<?php esc_attr_e( 'Menú móvil', 'ce-construction' ); ?>">
	<button class="ce-nav-mobile__close ce-modal__close" aria-label="<?php esc_attr_e( 'Cerrar menú', 'ce-construction' ); ?>">
		<i class="fa-solid fa-xmark" aria-hidden="true"></i>
	</button>
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'ce-nav-mobile__list',
		'fallback_cb'    => false,
		'depth'          => 2,
	) );
	?>
	<a href="#ce-quote-form" class="ce-btn ce-btn--primary ce-btn--block ce-mt-5">
		<?php esc_html_e( 'Cotización Gratuita', 'ce-construction' ); ?>
	</a>
</nav>

<main id="ce-main-content">
<?php
if ( ! is_front_page() ) :
	?>
	<div class="ce-container">
		<?php ce_construction_breadcrumbs(); ?>
	</div>
	<?php
endif;

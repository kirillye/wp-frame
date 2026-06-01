<?php
/**
 * The header for our theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="wpf-site">

	<a class="wpf-screen-reader-text" href="#primary">
		<?php esc_html_e( 'Перейти к содержимому', 'wp-frame' ); ?>
	</a>

	<header id="masthead" class="wpf-header" role="banner">
		<div class="wpf-container wpf-flex wpf-flex--between wpf-header__inner">

			<!-- Branding -->
			<div class="wpf-branding">
				<?php the_custom_logo(); ?>

				<?php if ( is_front_page() && is_home() ) : ?>
					<h1 class="wpf-branding__title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>
				<?php else : ?>
					<p class="wpf-branding__title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div><!-- .wpf-branding -->

			<!-- Nav slot -->
			<div class="wpf-header__nav-slot">

				<button type="button" class="wpf-nav__toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="wpf-screen-reader-text"><?php esc_html_e( 'Открыть меню', 'wp-frame' ); ?></span>
					<span class="wpf-nav__burger" aria-hidden="true"></span>
				</button>

				<div class="wpf-nav__backdrop" data-wpf-nav-close tabindex="-1" aria-hidden="true"></div>

				<nav id="site-navigation" class="wpf-nav" aria-label="<?php esc_attr_e( 'Основное меню', 'wp-frame' ); ?>">
					<button type="button" class="wpf-nav__close" data-wpf-nav-close>
						<span class="wpf-screen-reader-text"><?php esc_html_e( 'Закрыть меню', 'wp-frame' ); ?></span>
						<span aria-hidden="true">&times;</span>
					</button>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'container'      => false,
							'menu_class'     => 'wpf-nav__list',
							'submenu_class'  => 'wpf-nav__submenu',
							'fallback_cb'    => false,
							'depth'          => 3,
							'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					);
					?>
				</nav>

				<!-- CTA — скрыта на мобиле -->
				<button
					class="wpf-btn wpf-btn--primary wpf-btn--sm wpf-header__cta"
					type="button"
					data-modal-open="contact"
					aria-label="<?php esc_attr_e( 'Связаться с нами', 'wp-frame' ); ?>"
				>
					<?php esc_html_e( 'Связаться', 'wp-frame' ); ?>
				</button>

			</div><!-- .wpf-header__nav-slot -->

		</div><!-- .wpf-container -->
	</header><!-- #masthead -->

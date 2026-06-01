<?php
/**
 * Theme setup: theme support, nav menus, image sizes, content width.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Set up theme defaults and register support for various WordPress features.
 *
 * @return void
 */
function wpf_setup(): void {
	// Make theme available for translation.
	load_theme_textdomain( 'wp-frame', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails.
	add_theme_support( 'post-thumbnails' );

	// Register navigation menus.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'wp-frame' ),
			'footer'  => esc_html__( 'Footer Menu', 'wp-frame' ),
		)
	);

	// Switch default core markup to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for core custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Custom image sizes.
	add_image_size( 'wpf-card', 800, 450, true );
	add_image_size( 'wpf-hero', 1440, 600, true );
	add_image_size( 'wpf-thumb', 400, 400, true );
}
add_action( 'after_setup_theme', 'wpf_setup' );

/**
 * Set the content width in pixels.
 *
 * @global int $content_width
 *
 * @return void
 */
function wpf_content_width(): void {
	$GLOBALS['content_width'] = 1140;
}
add_action( 'after_setup_theme', 'wpf_content_width', 0 );

/**
 * Register widget areas.
 *
 * @return void
 */
function wpf_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'wp-frame' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'wp-frame' ),
			'before_widget' => '<section id="%1$s" class="wpf-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="wpf-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'wpf_widgets_init' );

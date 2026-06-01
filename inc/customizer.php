<?php
/**
 * WP Frame Theme Customizer.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function wpf_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.wpf-branding__title a',
				'render_callback' => 'wpf_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.wpf-branding__description',
				'render_callback' => 'wpf_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'wpf_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function wpf_customize_partial_blogname(): void {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function wpf_customize_partial_blogdescription(): void {
	bloginfo( 'description' );
}

/**
 * Enqueue JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @return void
 */
function wpf_customize_preview_js(): void {
	wp_enqueue_script(
		'wpf-customizer',
		get_template_directory_uri() . '/js/customizer.js',
		array( 'customize-preview' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'customize_preview_init', 'wpf_customize_preview_js' );

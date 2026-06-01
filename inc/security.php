<?php
/**
 * Security enhancements: clean up wp_head, disable unnecessary features.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Clean up WordPress head from unnecessary output.
 *
 * @return void
 */
function wpf_cleanup_wp_head(): void {
	// Remove WordPress version.
	remove_action( 'wp_head', 'wp_generator' );

	// Remove Windows Live Writer manifest.
	remove_action( 'wp_head', 'wlwmanifest_link' );

	// Remove RSD (Really Simple Discovery) link.
	remove_action( 'wp_head', 'rsd_link' );

	// Remove shortlink.
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	// Remove adjacent posts rel links.
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

	// Remove emoji detection script.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'wpf_cleanup_wp_head' );

/**
 * Deregister jQuery on the frontend for performance.
 *
 * @return void
 */
function wpf_deregister_jquery(): void {
	if ( ! is_admin() ) {
		wp_deregister_script( 'jquery' );
	}
}
add_action( 'wp_enqueue_scripts', 'wpf_deregister_jquery', 1 );

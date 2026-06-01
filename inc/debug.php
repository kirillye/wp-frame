<?php
/**
 * Debug utilities — logging, dump & die.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Log a message to WP debug.log.
 * Only works when WP_DEBUG is true.
 *
 * @param mixed  $data  Data to log.
 * @param string $label Optional label prefix.
 * @return void
 */
function wpf_log( mixed $data, string $label = 'DEBUG' ): void {
	if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
		return;
	}

	$tag     = 'MYT';
	$message = is_string( $data ) ? $data : print_r( $data, true );

	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( "[{$tag}:{$label}] {$message}" );
}

/**
 * Dump variables and die (admin-only).
 *
 * @param mixed ...$vars Variables to dump.
 * @return void
 */
function wpf_dd( mixed ...$vars ): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<pre style="background:#1e293b;color:#e2e8f0;padding:1rem;border-radius:8px;overflow:auto;font-size:13px;white-space:pre-wrap;">';
	foreach ( $vars as $var ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions
		var_dump( $var );
	}
	echo '</pre>';

	wp_die();
}

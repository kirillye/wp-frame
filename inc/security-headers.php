<?php
/**
 * HTTP security headers (frontend).
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send baseline security headers on the front-end.
 * CSP: optional Report-Only via filter `wpf_enable_csp_report_only` (default false).
 *
 * @return void
 */
function wpf_send_security_headers(): void {
	if ( is_admin() ) {
		return;
	}

	if ( ! headers_sent() ) {
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
	}

	if ( is_ssl() && ! headers_sent() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}

	/**
	 * CSP в режиме только отчётов. По умолчанию выключено — включайте после проверки счётчиков в head.
	 *
	 * @param bool $enable Whether to send CSP-Report-Only.
	 */
	if ( apply_filters( 'wpf_enable_csp_report_only', false ) && ! headers_sent() ) {
		$csp = implode(
			'; ',
			array(
				"default-src 'self'",
				"script-src 'self' 'unsafe-inline' https://mc.yandex.ru https://www.googletagmanager.com https://www.google-analytics.com https://googletagmanager.com",
				"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
				"font-src 'self' https://fonts.gstatic.com data:",
				"img-src 'self' data: https:",
				"connect-src 'self' https://mc.yandex.ru https://www.google-analytics.com",
				"frame-ancestors 'self'",
				"base-uri 'self'",
				"form-action 'self'",
			)
		);
		header( 'Content-Security-Policy-Report-Only: ' . $csp );
	}
}
add_action( 'send_headers', 'wpf_send_security_headers' );

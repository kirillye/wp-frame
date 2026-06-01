<?php
/**
 * Spam Guard — four levels of form protection.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Entry point: check all spam protection levels.
 * Call at the start of wpf_handle_form_submit().
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response|null  Null = clean, Response = reject.
 */
function wpf_spam_guard( WP_REST_Request $request ): ?WP_REST_Response {

	// Temporary override for testing: define MYT_DISABLE_SPAM_GUARD in wp-config.php.
	if ( defined( 'MYT_DISABLE_SPAM_GUARD' ) && MYT_DISABLE_SPAM_GUARD ) {
		return null;
	}

	$ip = wpf_get_client_ip();

	// 1. Rate limiting.
	if ( ! wpf_rate_limit_check( $ip ) ) {
		wpf_log( "Rate limit: {$ip}", 'SPAM' );
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Слишком много запросов. Попробуйте через час.', 'wp-frame' ),
		], 429 );
	}

	// 2. Honeypot.
	if ( wpf_honeypot_triggered( $request ) ) {
		wpf_log( "Honeypot triggered: {$ip}", 'SPAM' );
		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Заявка принята! Мы свяжемся с вами в ближайшее время.', 'wp-frame' ),
		], 200 );
	}

	// 3. Timing check.
	if ( ! wpf_timing_check( $request ) ) {
		wpf_log( "Timing check failed: {$ip}", 'SPAM' );
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Пожалуйста, заполните форму и отправьте снова.', 'wp-frame' ),
		], 429 );
	}

	// 4. Referer check.
	if ( ! wpf_referer_check( $request ) ) {
		wpf_log( 'Bad referer: ' . ( $request->get_header( 'referer' ) ?? '—' ), 'SPAM' );
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Ошибка безопасности.', 'wp-frame' ),
		], 403 );
	}

	return null;
}

// ─── 1. Rate Limiting ────────────────────────────────────────────────

/**
 * Sliding window rate limiter: max $max requests per $window seconds from $ip.
 *
 * @param string $ip     Client IP.
 * @param int    $max    Max requests (default 3).
 * @param int    $window Window in seconds (default 1 hour).
 * @return bool
 */
function wpf_rate_limit_check(
	string $ip,
	int $max = 3,
	int $window = HOUR_IN_SECONDS
): bool {
	$key  = 'wpf_rl_' . md5( $ip );
	$now  = time();

	$timestamps = get_transient( $key );
	if ( ! is_array( $timestamps ) ) {
		$timestamps = [];
	}

	$timestamps = array_values(
		array_filter( $timestamps, fn( int $t ) => ( $now - $t ) < $window )
	);

	if ( count( $timestamps ) >= $max ) {
		return false;
	}

	$timestamps[] = $now;
	set_transient( $key, $timestamps, $window );

	return true;
}

// ─── 2. Honeypot ─────────────────────────────────────────────────────

/**
 * Check if the hidden honeypot field was filled (bot detection).
 *
 * @param WP_REST_Request $request The request object.
 * @return bool
 */
function wpf_honeypot_triggered( WP_REST_Request $request ): bool {
	$fields = (array) ( $request->get_param( 'fields' ) ?? [] );
	return ! empty( $fields['wpf_hp'] );
}

// ─── 3. Timing Check ─────────────────────────────────────────────────

/**
 * Verify signed submit token — ensures at least $min_seconds elapsed.
 *
 * @param WP_REST_Request $request     The request object.
 * @param int             $min_seconds Minimum elapsed seconds (default 3).
 * @return bool
 */
function wpf_timing_check(
	WP_REST_Request $request,
	int $min_seconds = 3
): bool {
	$token = sanitize_text_field( $request->get_param( 'submit_token' ) ?? '' );

	if ( ! $token || ! str_contains( $token, '.' ) ) {
		return false;
	}

	[ $ts_str, $hash ] = explode( '.', $token, 2 );

	$expected = hash_hmac( 'sha256', $ts_str, wp_salt( 'auth' ) );

	if ( ! hash_equals( $expected, $hash ) ) {
		return false;
	}

	$elapsed = time() - (int) $ts_str;

	return $elapsed >= $min_seconds && $elapsed <= HOUR_IN_SECONDS;
}

/**
 * Generate a signed submit token for the frontend.
 *
 * @return string
 */
function wpf_generate_submit_token(): string {
	$ts = (string) time();
	return $ts . '.' . hash_hmac( 'sha256', $ts, wp_salt( 'auth' ) );
}

// ─── 4. Referer Check ────────────────────────────────────────────────

/**
 * Verify that the request originated from the same site.
 *
 * @param WP_REST_Request $request The request object.
 * @return bool
 */
function wpf_referer_check( WP_REST_Request $request ): bool {
	$referer   = $request->get_header( 'referer' ) ?? '';
	if ( '' === $referer ) {
		return true;
	}

	$site_host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$ref_host  = (string) wp_parse_url( $referer, PHP_URL_HOST );

	return $site_host === $ref_host;
}

// ─── Утилита: получение IP ───────────────────────────────────────────

/**
 * Get real client IP with Cloudflare/proxy support.
 *
 * @return string
 */
function wpf_get_client_ip(): string {
	$ip = sanitize_text_field(
		$_SERVER['HTTP_CF_CONNECTING_IP']    // Cloudflare.
		?? $_SERVER['HTTP_X_REAL_IP']        // nginx upstream.
		?? $_SERVER['HTTP_X_FORWARDED_FOR']  // load balancer / proxy.
		?? $_SERVER['REMOTE_ADDR']
		?? ''
	);

	return trim( explode( ',', $ip )[0] );
}

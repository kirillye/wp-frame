<?php
/**
 * Form Handler — REST endpoint, save entry, dispatch channels.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── REST-эндпоинт ─────────────────────────────────────────────────

add_action( 'rest_api_init', 'wpf_register_form_rest_route' );

/**
 * Register REST route for form submission.
 *
 * @return void
 */
function wpf_register_form_rest_route(): void {
	register_rest_route( 'wpf/v1', '/form/submit', [
		'methods'             => 'POST',
		'callback'            => 'wpf_handle_form_submit',
		'permission_callback' => '__return_true',
		'args'                => [
			'form_id' => [
				'required'          => true,
				'sanitize_callback' => 'sanitize_key',
			],
			'fields' => [
				'required'          => true,
			],
			'nonce' => [
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
			],
		],
	] );

	// Свежие nonce + submit_token для форм. Эндпоинт некэшируем (no-store),
	// поэтому HTML страницы можно кэшировать сколько угодно — креды
	// подтягиваются клиентом перед отправкой. Без этого full-page cache
	// отдаёт протухший nonce (403) и устаревший submit_token (timing fail).
	register_rest_route( 'wpf/v1', '/form/token', [
		'methods'             => 'GET',
		'callback'            => 'wpf_issue_form_token',
		'permission_callback' => '__return_true',
	] );
}

// ─── Выдача свежих кредов формы ─────────────────────────────────────

/**
 * Issue a fresh REST nonce and signed submit token.
 * Response is marked no-store so page caches never serve a stale token.
 *
 * @return WP_REST_Response
 */
function wpf_issue_form_token(): WP_REST_Response {
	$response = new WP_REST_Response( [
		'nonce'        => wp_create_nonce( THEME_PREFIX_ . 'form_submit' ),
		'submit_token' => wpf_generate_submit_token(),
	], 200 );

	$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0' );

	return $response;
}

// ─── Основной обработчик ────────────────────────────────────────────

/**
 * Handle form submission: security check, save, dispatch channels.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response
 */
function wpf_handle_form_submit( WP_REST_Request $request ): WP_REST_Response {

	// 1. Проверка нонса.
	if ( ! wp_verify_nonce( $request->get_param( 'nonce' ), THEME_PREFIX_ . 'form_submit' ) ) {
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Ошибка безопасности. Обновите страницу и попробуйте снова.', 'wp-frame' ),
		], 403 );
	}

	// 2. Защита от спама (если подключён).
	if ( function_exists( 'wpf_spam_guard' ) ) {
		$spam_response = wpf_spam_guard( $request );
		if ( null !== $spam_response ) {
			return $spam_response;
		}
	}

	// 3. Санитизация полей.
	$form_id    = sanitize_key( $request->get_param( 'form_id' ) );
	$raw_fields = $request->get_param( 'fields' );
	$fields     = wpf_sanitize_form_fields( $raw_fields );

	if ( empty( $fields ) ) {
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Данные формы пусты.', 'wp-frame' ),
		], 422 );
	}

	// 4. Сохранение заявки.
	$entry_id = wpf_save_form_entry( $form_id, $fields, $request );

	if ( is_wp_error( $entry_id ) ) {
		return new WP_REST_Response( [
			'success' => false,
			'message' => __( 'Ошибка сохранения заявки.', 'wp-frame' ),
		], 500 );
	}

	$entry_number = (int) get_post_meta( $entry_id, 'wpf_entry_number', true );

	// 5. Отправка по каналам.
	$results = wpf_dispatch_channels( $entry_id, $form_id, $fields, $entry_number );

	// 6. Обновление статусов каналов.
	update_post_meta( $entry_id, 'wpf_ch_email',    (int) $results['email'] );
	update_post_meta( $entry_id, 'wpf_ch_telegram', (int) $results['telegram'] );
	update_post_meta( $entry_id, 'wpf_ch_bitrix',   (int) $results['bitrix'] );

	return new WP_REST_Response( [
		'success'      => true,
		'entry_number' => $entry_number,
		'message'      => __( 'Заявка принята! Мы свяжемся с вами в ближайшее время.', 'wp-frame' ),
	], 200 );
}

// ─── Сохранение заявки ──────────────────────────────────────────────

/**
 * Save form entry as a CPT post.
 *
 * @param string         $form_id Form identifier.
 * @param array          $fields  Sanitized form fields.
 * @param WP_REST_Request $request The request object.
 * @return int|WP_Error
 */
function wpf_save_form_entry(
	string $form_id,
	array $fields,
	WP_REST_Request $request
): int|\WP_Error {

	$entry_number = wpf_next_entry_number();
	$number_pad   = str_pad( (string) $entry_number, 4, '0', STR_PAD_LEFT );

	// Первое поле как подпись (имя / email / телефон).
	$first_val = reset( $fields );
	$label     = is_string( $first_val ) ? esc_html( mb_substr( $first_val, 0, 40 ) ) : '';

	$post_id = wp_insert_post( [
		'post_type'   => 'wpf_form_entry',
		'post_title'  => "#{$number_pad} — {$form_id}" . ( $label ? " — {$label}" : '' ),
		'post_status' => 'publish',
		'post_date'   => current_time( 'mysql' ),
	], true );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	// IP с учётом Cloudflare / proxy.
	$ip = wpf_get_client_ip();

	update_post_meta( $post_id, 'wpf_entry_number', $entry_number );
	update_post_meta( $post_id, 'wpf_form_id',      $form_id );
	update_post_meta( $post_id, 'wpf_form_fields',   wp_json_encode( $fields, JSON_UNESCAPED_UNICODE ) );
	update_post_meta( $post_id, 'wpf_source_url',    sanitize_url( $request->get_header( 'referer' ) ?? '' ) );
	update_post_meta( $post_id, 'wpf_user_ip',       $ip );

	return $post_id;
}

// ─── Порядковый ID — атомарный счётчик ──────────────────────────────

/**
 * Get the next sequential entry number (atomic, SQL-based).
 *
 * @return int
 */
function wpf_next_entry_number(): int {
	global $wpdb;

	$option_name = 'wpf_entry_counter';

	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$wpdb->options} (option_name, option_value, autoload)
			 VALUES ( %s, '1', 'no' )
			 ON DUPLICATE KEY UPDATE option_value = option_value + 1",
			$option_name
		)
	);

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
			$option_name
		)
	);
}

// ─── Санитизация полей ──────────────────────────────────────────────

/**
 * Sanitize form fields array.
 *
 * @param mixed $raw Raw fields data.
 * @return array
 */
function wpf_sanitize_form_fields( mixed $raw ): array {
	if ( ! is_array( $raw ) ) {
		return [];
	}

	$clean = [];
	foreach ( $raw as $key => $value ) {
		$key = wpf_sanitize_form_key( $key );
		if ( '' === $key ) {
			continue;
		}

		$lower_key = mb_strtolower( $key );

		$clean[ $key ] = match ( true ) {
			str_contains( $lower_key, 'email' ) || str_contains( $lower_key, 'почта' ) || str_contains( $lower_key, 'e-mail' )
				=> sanitize_email( (string) $value ),
			str_contains( $lower_key, 'url' ) || str_contains( $lower_key, 'ссылка' )
				=> sanitize_url( (string) $value ),
			str_contains( $lower_key, 'телефон' ) || str_contains( $lower_key, 'phone' )
				=> preg_replace( '/\D/', '', (string) $value ),
			is_array( $value )
				=> array_map( 'sanitize_text_field', $value ),
			default
				=> sanitize_text_field( (string) $value ),
		};
	}

	return $clean;
}

/**
	* Sanitize form field key — preserves Unicode (Cyrillic).
	* Unlike sanitize_key(), doesn't strip non-ASCII characters.
	*
	* @param string $key Raw key.
	* @return string
	*/
function wpf_sanitize_form_key( string $key ): string {
	// Convert to lowercase (multibyte-aware).
	$key = mb_strtolower( trim( $key ), 'UTF-8' );

	// Remove NULL bytes and control characters.
	$key = str_replace( "\0", '', $key );
	$key = preg_replace( '/[\x00-\x1f\x7f]/u', '', $key );

	// Remove leading/trailing whitespace that might have crept in.
	$key = trim( $key );

	// Max 80 characters.
	return mb_substr( $key, 0, 80, 'UTF-8' );
}

// ─── Диспетчер каналов ──────────────────────────────────────────────

/**
 * Dispatch entry to all active channels.
 *
 * @param int    $entry_id     Post ID.
 * @param string $form_id      Form identifier.
 * @param array  $fields       Sanitized fields.
 * @param int    $entry_number Sequential number.
 * @return array{email: bool, telegram: bool, bitrix: bool}
 */
function wpf_dispatch_channels(
	int $entry_id,
	string $form_id,
	array $fields,
	int $entry_number
): array {
	$results = [
		'email'    => false,
		'telegram' => false,
		'bitrix'   => false,
	];

	// Email.
	if ( wpf_get_setting( 'mail_enabled', true ) && wpf_get_setting( 'mail_to' ) ) {
		$results['email'] = wpf_channel_email( $entry_id, $form_id, $fields, $entry_number );
	}

	// Telegram.
	if ( wpf_get_setting( 'tg_enabled' ) && wpf_get_setting( 'tg_token' ) ) {
		$results['telegram'] = wpf_channel_telegram( $form_id, $fields, $entry_number );
	}

	// Bitrix24.
	if ( wpf_get_setting( 'b24_enabled' ) && wpf_get_setting( 'b24_webhook' ) ) {
		$lead_id = wpf_channel_bitrix24( $form_id, $fields, $entry_number );
		$results['bitrix'] = $lead_id > 0;
		if ( $lead_id > 0 ) {
			update_post_meta( $entry_id, 'wpf_bitrix_lead_id', $lead_id );
		}
	}

	return $results;
}


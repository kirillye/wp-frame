<?php
/**
 * Theme Settings — AJAX test handlers for Email/Telegram/Bitrix24.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_wpf_test_email', 'wpf_ajax_test_email' );

/**
 * Test email channel.
 *
 * @return void
 */
function wpf_ajax_test_email(): void {
	check_ajax_referer( THEME_PREFIX_ . 'admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Нет доступа.', 403 );
	}

	$to = wpf_get_setting( 'mail_to' );

	if ( ! $to ) {
		wp_send_json_error( __( 'Укажите Email получателя на вкладке Email.', 'wp-frame' ) );
	}

	$site_name = get_bloginfo( 'name' );

	$sent = wp_mail(
		$to,
		'Тест — настройки темы работают',
		sprintf(
			'<p>%s <strong>%s</strong>.</p><p>%s</p>',
			__( 'Это тестовое письмо с сайта', 'wp-frame' ),
			esc_html( $site_name ),
			__( 'Если вы его видите — Email-канал настроен верно.', 'wp-frame' )
		),
		[ 'Content-Type: text/html; charset=UTF-8' ]
	);

	if ( $sent ) {
		wp_send_json_success(
			sprintf(
				/* translators: %s: email address */
				__( 'Письмо отправлено на %s', 'wp-frame' ),
				esc_html( $to )
			)
		);
	} else {
		wp_send_json_error( __( 'wp_mail() вернул false. Проверьте настройки SMTP.', 'wp-frame' ) );
	}
}

add_action( 'wp_ajax_wpf_test_telegram', 'wpf_ajax_test_telegram' );

/**
 * Test Telegram channel.
 *
 * @return void
 */
function wpf_ajax_test_telegram(): void {
	check_ajax_referer( THEME_PREFIX_ . 'admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Нет доступа.', 403 );
	}

	$token   = wpf_get_setting( 'tg_token' );
	$chat_id = wpf_get_setting( 'tg_chat_id' );

	if ( ! $token || ! $chat_id ) {
		wp_send_json_error( __( 'Укажите Bot Token и Chat ID на вкладке Telegram.', 'wp-frame' ) );
	}

	$response = wp_remote_post(
		"https://api.telegram.org/bot{$token}/sendMessage",
		[
			'timeout' => 10,
			'body'    => [
				'chat_id'    => $chat_id,
				'text'       => '<b>' . __( 'Тест — настройки темы работают', 'wp-frame' ) . '</b>'
					. "\n\n" . __( 'Сайт', 'wp-frame' ) . ': ' . get_bloginfo( 'name' )
					. "\n" . __( 'Время', 'wp-frame' ) . ': ' . wp_date( 'd.m.Y H:i:s' ),
				'parse_mode' => 'HTML',
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'Ошибка: ' . $response->get_error_message() );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! empty( $body['ok'] ) ) {
		wp_send_json_success(
			sprintf(
				/* translators: %s: chat ID */
				__( 'Сообщение отправлено в чат %s', 'wp-frame' ),
				esc_html( $chat_id )
			)
		);
	} else {
		wp_send_json_error(
			'Telegram API: ' . ( $body['description'] ?? __( 'Неизвестная ошибка', 'wp-frame' ) )
		);
	}
}

add_action( 'wp_ajax_wpf_test_bitrix', 'wpf_ajax_test_bitrix' );

/**
 * Test Bitrix24 channel.
 *
 * @return void
 */
function wpf_ajax_test_bitrix(): void {
	check_ajax_referer( THEME_PREFIX_ . 'admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Нет доступа.', 403 );
	}

	$webhook = wpf_get_setting( 'b24_webhook' );

	if ( ! $webhook ) {
		wp_send_json_error( __( 'Укажите Webhook URL на вкладке Bitrix24.', 'wp-frame' ) );
	}

	$response = wp_remote_post(
		rtrim( $webhook, '/' ) . '/crm.lead.add.json',
		[
			'timeout' => 15,
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( [
				'fields' => [
					'TITLE'     => __( 'Тест', 'wp-frame' ) . ' — ' . get_bloginfo( 'name' ),
					'COMMENTS'  => __( 'Тестовый лид из панели настроек темы. Можно удалить.', 'wp-frame' ),
					'SOURCE_ID' => wpf_get_setting( 'b24_source', 'WEB' ),
					'STATUS_ID' => 'NEW',
				],
			] ),
		]
	);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'Ошибка: ' . $response->get_error_message() );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$code = wp_remote_retrieve_response_code( $response );

	if ( 200 === $code && isset( $body['result'] ) ) {
		wp_send_json_success(
			sprintf(
				/* translators: %d: lead ID */
				__( 'Лид создан, ID: %d', 'wp-frame' ),
				(int) $body['result']
			)
		);
	} else {
		wp_send_json_error(
			'Bitrix24: ' . ( $body['error_description'] ?? "HTTP {$code}" )
		);
	}
}

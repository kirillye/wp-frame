<?php
/**
 * Channel: Telegram — send form entry via Telegram Bot API.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send form entry to Telegram chat.
 *
 * @param string $form_id      Form identifier.
 * @param array  $fields       Sanitized form fields.
 * @param int    $entry_number Sequential number.
 * @return bool
 */
function wpf_channel_telegram(
	string $form_id,
	array $fields,
	int $entry_number
): bool {

	$token   = wpf_get_setting( 'tg_token' );
	$chat_id = wpf_get_setting( 'tg_chat_id' );

	if ( ! $token || ! $chat_id ) {
		return false;
	}

	$number_pad = str_pad( (string) $entry_number, 4, '0', STR_PAD_LEFT );

	$lines   = [];
	$lines[] = "📋 <b>Заявка #{$number_pad}</b> · <code>{$form_id}</code>";
	$lines[] = '─────────────────';

	foreach ( $fields as $key => $value ) {
		$label = ucfirst( str_replace( [ '_', '-' ], ' ', $key ) );
		$val   = is_array( $value ) ? implode( ', ', $value ) : (string) $value;
		$lines[] = '<b>' . htmlspecialchars( $label, ENT_QUOTES ) . ':</b> ' . htmlspecialchars( $val, ENT_QUOTES );
	}

	$lines[] = '─────────────────';
	$lines[] = '🕐 ' . wp_date( 'd.m.Y H:i:s' );
	$lines[] = '🌐 ' . esc_url( home_url() );

	$text = implode( "\n", $lines );

	$response = wp_remote_post(
		"https://api.telegram.org/bot{$token}/sendMessage",
		[
			'timeout' => 10,
			'body'    => [
				'chat_id'                  => $chat_id,
				'text'                     => $text,
				'parse_mode'               => 'HTML',
				'disable_web_page_preview' => true,
			],
		]
	);

	if ( is_wp_error( $response ) ) {
		wpf_log( 'Telegram error: ' . $response->get_error_message(), 'TG' );
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$ok   = (bool) ( $body['ok'] ?? false );

	if ( ! $ok ) {
		wpf_log( 'Telegram API error: ' . ( $body['description'] ?? 'unknown' ), 'TG' );
	}

	return $ok;
}

<?php
/**
 * Channel: Bitrix24 — create lead via Bitrix24 REST API.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a lead in Bitrix24 via incoming webhook.
 *
 * @param string $form_id      Form identifier.
 * @param array  $fields       Sanitized form fields.
 * @param int    $entry_number Sequential number.
 * @return int  Lead ID on success, 0 on failure.
 */
function wpf_channel_bitrix24(
	string $form_id,
	array $fields,
	int $entry_number
): int {

	$webhook = wpf_get_setting( 'b24_webhook' );

	if ( ! $webhook ) {
		return 0;
	}

	$number_pad = str_pad( (string) $entry_number, 4, '0', STR_PAD_LEFT );

	$name    = $fields['name']    ?? $fields['username'] ?? $fields['full_name'] ?? '';
	$phone   = $fields['phone']   ?? $fields['tel']      ?? '';
	$email   = $fields['email']   ?? '';
	$message = $fields['message'] ?? $fields['comment']  ?? $fields['text'] ?? '';

	$comment_lines = [];
	foreach ( $fields as $key => $value ) {
		$label           = ucfirst( str_replace( [ '_', '-' ], ' ', $key ) );
		$comment_lines[] = "{$label}: " . ( is_array( $value ) ? implode( ', ', $value ) : $value );
	}

	$lead_data = [
		'fields' => [
			'TITLE'      => "Заявка #{$number_pad} — {$form_id} — " . get_bloginfo( 'name' ),
			'NAME'       => sanitize_text_field( (string) $name ),
			'COMMENTS'   => implode( "\n", $comment_lines ),
			'SOURCE_ID'  => wpf_get_setting( 'b24_source', 'WEB' ),
			'STATUS_ID'  => 'NEW',
		],
	];

	if ( $phone ) {
		$lead_data['fields']['PHONE'] = [
			[ 'VALUE' => sanitize_text_field( (string) $phone ), 'VALUE_TYPE' => 'WORK' ],
		];
	}

	if ( $email ) {
		$lead_data['fields']['EMAIL'] = [
			[ 'VALUE' => sanitize_email( (string) $email ), 'VALUE_TYPE' => 'WORK' ],
		];
	}

	if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
		$lead_data['fields']['SOURCE_DESCRIPTION'] = sanitize_url( $_SERVER['HTTP_REFERER'] );
	}

	$response = wp_remote_post(
		rtrim( $webhook, '/' ) . '/crm.lead.add.json',
		[
			'timeout'     => 15,
			'headers'     => [ 'Content-Type' => 'application/json' ],
			'body'        => wp_json_encode( $lead_data ),
		]
	);

	if ( is_wp_error( $response ) ) {
		wpf_log( 'Bitrix24 error: ' . $response->get_error_message(), 'B24' );
		return 0;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || isset( $body['error'] ) ) {
		wpf_log( 'Bitrix24 API error: ' . ( $body['error_description'] ?? "HTTP {$code}" ), 'B24' );
		return 0;
	}

	return (int) ( $body['result'] ?? 0 );
}

<?php
/**
 * Channel: Email — send form entry via wp_mail().
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send form entry via email.
 *
 * @param int    $entry_id     Post ID.
 * @param string $form_id      Form identifier.
 * @param array  $fields       Sanitized form fields.
 * @param int    $entry_number Sequential number.
 * @return bool
 */
function wpf_channel_email(
	int $entry_id,
	string $form_id,
	array $fields,
	int $entry_number
): bool {

	$number_pad = str_pad( (string) $entry_number, 4, '0', STR_PAD_LEFT );
	$to         = wpf_get_setting( 'mail_to' );
	$subject    = "Заявка #{$number_pad} с формы «{$form_id}»";

	$body   = wpf_email_html_template( $form_id, $fields, $entry_number, $entry_id );
	$from   = wpf_get_setting( 'mail_from' );
	$from_name = wpf_get_setting( 'mail_from_name', get_bloginfo( 'name' ) );

	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		"From: {$from_name} <{$from}>",
	];

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( ! $sent ) {
		wpf_log( "Email не отправлен для заявки #{$entry_id}", 'EMAIL' );
	}

	return $sent;
}

/**
 * HTML template for email body.
 *
 * @param string $form_id      Form identifier.
 * @param array  $fields       Form fields.
 * @param int    $entry_number Sequential number.
 * @param int    $entry_id     Post ID.
 * @return string
 */
function wpf_email_html_template(
	string $form_id,
	array $fields,
	int $entry_number,
	int $entry_id
): string {
	$number_pad = str_pad( (string) $entry_number, 4, '0', STR_PAD_LEFT );
	$admin_url  = get_edit_post_link( $entry_id, 'raw' );
	$date       = wp_date( 'd.m.Y H:i' );

	$rows = '';
	foreach ( $fields as $key => $value ) {
		$label = ucfirst( str_replace( [ '_', '-' ], ' ', $key ) );
		$val   = is_array( $value ) ? implode( ', ', $value ) : $value;
		$rows .= '<tr>
			<td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;color:#64748b;width:35%">'
				. esc_html( $label ) . '</td>
			<td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;color:#1e293b">'
				. nl2br( esc_html( (string) $val ) ) . '</td>
		</tr>';
	}

	return <<<HTML
	<!DOCTYPE html>
	<html lang="ru">
	<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width"></head>
	<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,sans-serif">
	  <div style="max-width:600px;margin:32px auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08)">

		<div style="background:#2563eb;padding:24px 32px">
		  <h1 style="margin:0;color:#fff;font-size:20px">Новая заявка #{$number_pad}</h1>
		  <p style="margin:4px 0 0;color:#bfdbfe;font-size:14px">Форма: {$form_id} · {$date}</p>
		</div>

		<div style="padding:24px 32px">
		  <table style="width:100%;border-collapse:collapse;font-size:15px">
			{$rows}
		  </table>
		</div>

		<div style="padding:16px 32px 24px;border-top:1px solid #e2e8f0">
		  <a href="{$admin_url}" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;font-size:14px">
			Открыть заявку в CMS →
		  </a>
		</div>

	  </div>
	</body>
	</html>
	HTML;
}

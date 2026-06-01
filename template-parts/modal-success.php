<?php
/**
 * Template part: Success modal — shown after successful form submission.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpf-modal" id="wpf-modal-success" role="dialog" aria-modal="true" aria-labelledby="wpf-modal-success-title" hidden>
	<div class="wpf-modal__overlay" data-modal-close></div>
	<div class="wpf-modal__content wpf-modal__content--success">
		<button class="wpf-modal__close" data-modal-close aria-label="<?php esc_attr_e( 'Закрыть', 'wp-frame' ); ?>">&times;</button>

		<div class="wpf-modal__success-icon" aria-hidden="true">
			<svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="32" cy="32" r="30" stroke="var(--wpf-color-success, #2e7d32)" stroke-width="3" fill="none"/>
				<path d="M18 32l10 10 18-20" stroke="var(--wpf-color-success, #2e7d32)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</div>

		<h2 id="wpf-modal-success-title" class="wpf-modal__title">
			<?php esc_html_e( 'Заявка принята!', 'wp-frame' ); ?>
		</h2>

		<p id="wpf-modal-success-message" class="wpf-modal__text">
			<?php esc_html_e( 'Мы свяжемся с вами в ближайшее время.', 'wp-frame' ); ?>
		</p>

		<button class="wpf-btn wpf-btn--primary" data-modal-close>
			<?php esc_html_e( 'Закрыть', 'wp-frame' ); ?>
		</button>
	</div>
</div>

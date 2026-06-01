<?php
/**
 * CTA section — Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-home-cta" aria-labelledby="wpf-cta-title">
	<div class="wpf-container">
		<div class="wpf-home-cta__inner">

			<h2 id="wpf-cta-title" class="wpf-home-cta__title">
				<?php esc_html_e( 'Начни проект прямо сейчас', 'wp-frame' ); ?>
			</h2>

			<p class="wpf-home-cta__text">
				<?php esc_html_e( 'Тема готова к использованию. Установи, настрой каналы доставки форм и запусти свой сайт.', 'wp-frame' ); ?>
			</p>

			<div class="wpf-flex wpf-flex--center wpf-gap-md wpf-home-cta__actions">
				<button class="wpf-btn wpf-btn--white" type="button" data-modal-open="contact">
					<?php esc_html_e( 'Связаться с нами', 'wp-frame' ); ?>
				</button>
				<button class="wpf-btn wpf-btn--outline-white" type="button" data-modal-open="callback">
					<?php esc_html_e( 'Заказать звонок', 'wp-frame' ); ?>
				</button>
			</div>

		</div>
	</div>
</section>

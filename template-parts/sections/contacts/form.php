<?php
/**
 * Форма на странице «Контакты» (та же логика, что и в модальном окне на главной).
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-contacts-form" aria-labelledby="wpf-contacts-form-title">
	<div class="wpf-container wpf-contacts-form__container">
		<h2 id="wpf-contacts-form-title" class="wpf-contacts-form__heading">
			<?php esc_html_e( 'Напишите нам', 'wp-frame' ); ?>
		</h2>
		<p class="wpf-contacts-form__lead">
			<?php esc_html_e( 'Заполните форму — ответим в ближайшее время.', 'wp-frame' ); ?>
		</p>
		<form class="wpf-form" data-form-id="contact" data-success="<?php echo esc_attr( __( 'Спасибо! Мы свяжемся с вами.', 'wp-frame' ) ); ?>" novalidate>
			<div class="wpf-form__hp" aria-hidden="true">
				<input type="text" name="wpf_hp" tabindex="-1" autocomplete="off">
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="wpf-contacts-form-name"><?php esc_html_e( 'Имя', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="text" id="wpf-contacts-form-name" name="name" data-label="<?php echo esc_attr__( 'Имя', 'wp-frame' ); ?>" required minlength="2">
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="wpf-contacts-form-phone"><?php esc_html_e( 'Телефон', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="tel" id="wpf-contacts-form-phone" name="phone" data-label="<?php echo esc_attr__( 'Телефон', 'wp-frame' ); ?>" required>
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="wpf-contacts-form-email"><?php esc_html_e( 'Email', 'wp-frame' ); ?></label>
				<input class="wpf-form__input" type="email" id="wpf-contacts-form-email" name="email" data-label="<?php echo esc_attr__( 'Email', 'wp-frame' ); ?>">
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="wpf-contacts-form-message"><?php esc_html_e( 'Сообщение', 'wp-frame' ); ?></label>
				<textarea class="wpf-form__input" id="wpf-contacts-form-message" name="message" data-label="<?php echo esc_attr__( 'Сообщение', 'wp-frame' ); ?>" rows="3"></textarea>
			</div>
			<div class="wpf-form__footer">
				<button type="submit" class="wpf-btn wpf-btn--primary wpf-btn--block">
					<span class="wpf-form__btn-text"><?php esc_html_e( 'Отправить', 'wp-frame' ); ?></span>
					<span class="wpf-form__btn-loader" hidden><?php esc_html_e( 'Отправляю...', 'wp-frame' ); ?></span>
				</button>
				<p class="wpf-form__success" hidden></p>
				<p class="wpf-form__global-error" hidden></p>
			</div>
		</form>
	</div>
</section>

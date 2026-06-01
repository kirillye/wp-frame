<?php
/**
 * Модальные окна маркетинговых блоков (связаться, звонок, о проекте).
 * Подключать на шаблонах, где есть кнопки с data-modal-open.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Модальные окна -->
<div class="wpf-modal" id="wpf-modal-contact" role="dialog" aria-modal="true" aria-labelledby="wpf-modal-contact-title" hidden>
	<div class="wpf-modal__overlay" data-modal-close></div>
	<div class="wpf-modal__content">
		<button class="wpf-modal__close" data-modal-close aria-label="<?php esc_attr_e( 'Закрыть', 'wp-frame' ); ?>">&times;</button>
		<h2 id="wpf-modal-contact-title" class="wpf-modal__title"><?php esc_html_e( 'Связаться с нами', 'wp-frame' ); ?></h2>
		<p class="wpf-modal__lead">
			<?php esc_html_e( 'Оставьте заявку и мы ответим в ближайшее время.', 'wp-frame' ); ?>
		</p>
		<form class="wpf-form" data-form-id="contact" data-success="<?php echo esc_attr( __( 'Спасибо! Мы свяжемся с вами.', 'wp-frame' ) ); ?>" novalidate>
			<div class="wpf-form__hp" aria-hidden="true">
				<input type="text" name="wpf_hp" tabindex="-1" autocomplete="off">
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-contact-name"><?php esc_html_e( 'Имя', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="text" id="f-contact-name" name="name" data-label="<?php echo esc_attr__( 'Имя', 'wp-frame' ); ?>" required minlength="2">
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-contact-phone"><?php esc_html_e( 'Телефон', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="tel" id="f-contact-phone" name="phone" data-label="<?php echo esc_attr__( 'Телефон', 'wp-frame' ); ?>" required>
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-contact-email"><?php esc_html_e( 'Email', 'wp-frame' ); ?></label>
				<input class="wpf-form__input" type="email" id="f-contact-email" name="email" data-label="<?php echo esc_attr__( 'Email', 'wp-frame' ); ?>">
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-contact-message"><?php esc_html_e( 'Сообщение', 'wp-frame' ); ?></label>
				<textarea class="wpf-form__input" id="f-contact-message" name="message" data-label="<?php echo esc_attr__( 'Сообщение', 'wp-frame' ); ?>" rows="3"></textarea>
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
</div>

<div class="wpf-modal" id="wpf-modal-callback" role="dialog" aria-modal="true" aria-labelledby="wpf-modal-callback-title" hidden>
	<div class="wpf-modal__overlay" data-modal-close></div>
	<div class="wpf-modal__content">
		<button class="wpf-modal__close" data-modal-close aria-label="<?php esc_attr_e( 'Закрыть', 'wp-frame' ); ?>">&times;</button>
		<h2 id="wpf-modal-callback-title" class="wpf-modal__title"><?php esc_html_e( 'Заказать звонок', 'wp-frame' ); ?></h2>
		<p class="wpf-modal__lead">
			<?php esc_html_e( 'Перезвоним в течение 15 минут.', 'wp-frame' ); ?>
		</p>
		<form class="wpf-form" data-form-id="callback" data-success="<?php echo esc_attr( __( 'Спасибо! Перезвоним в ближайшее время.', 'wp-frame' ) ); ?>" novalidate>
			<div class="wpf-form__hp" aria-hidden="true">
				<input type="text" name="wpf_hp" tabindex="-1" autocomplete="off">
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-callback-name"><?php esc_html_e( 'Имя', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="text" id="f-callback-name" name="name" data-label="<?php echo esc_attr__( 'Имя', 'wp-frame' ); ?>" required minlength="2">
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__group">
				<label class="wpf-form__label" for="f-callback-phone"><?php esc_html_e( 'Телефон', 'wp-frame' ); ?> *</label>
				<input class="wpf-form__input" type="tel" id="f-callback-phone" name="phone" data-label="<?php echo esc_attr__( 'Телефон', 'wp-frame' ); ?>" required>
				<span class="wpf-form__error"></span>
			</div>
			<div class="wpf-form__footer">
				<button type="submit" class="wpf-btn wpf-btn--primary wpf-btn--block">
					<span class="wpf-form__btn-text"><?php esc_html_e( 'Заказать звонок', 'wp-frame' ); ?></span>
					<span class="wpf-form__btn-loader" hidden><?php esc_html_e( 'Отправляю...', 'wp-frame' ); ?></span>
				</button>
				<p class="wpf-form__success" hidden></p>
				<p class="wpf-form__global-error" hidden></p>
			</div>
		</form>
	</div>
</div>

<div class="wpf-modal" id="wpf-modal-info" role="dialog" aria-modal="true" aria-labelledby="wpf-modal-info-title" hidden>
	<div class="wpf-modal__overlay" data-modal-close></div>
	<div class="wpf-modal__content">
		<button class="wpf-modal__close" data-modal-close aria-label="<?php esc_attr_e( 'Закрыть', 'wp-frame' ); ?>">&times;</button>
		<h2 id="wpf-modal-info-title" class="wpf-modal__title"><?php esc_html_e( 'О проекте', 'wp-frame' ); ?></h2>
		<p>
			<?php esc_html_e( 'Тема с префиксом', 'wp-frame' ); ?>
			<code>wpf</code> <?php echo esc_html__( ', стек: PHP 8.2+, WordPress, Vanilla JS ES6+, BEM.', 'wp-frame' ); ?>
		</p>
		<p><?php esc_html_e( 'Реализовано: типографика, админ-панель (Email/Telegram/Bitrix24), cookie-баннер, системы форм через REST API, защита от спама.', 'wp-frame' ); ?></p>
	</div>
</div>

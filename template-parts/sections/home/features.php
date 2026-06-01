<?php
/**
 * Features section — what's included in the starter theme. Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$features = [
	[
		'title' => __( 'Без сборки', 'wp-frame' ),
		'text'  => __( 'Нативные ES-модули через type="module". Никакого бандлера и dev-сервера — код отдаётся как есть.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
	],
	[
		'title' => __( 'BEM методология', 'wp-frame' ),
		'text'  => __( 'Чёткое именование классов. Компоненты изолированы и не конфликтуют между собой.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
	],
	[
		'title' => __( 'CSS Custom Properties', 'wp-frame' ),
		'text'  => __( 'Полная система дизайн-токенов: цвета, отступы, типографика, тени и радиусы.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
	],
	[
		'title' => __( 'Система форм', 'wp-frame' ),
		'text'  => __( 'AJAX-формы с валидацией, спам-защитой и отправкой на Email, Telegram и Bitrix24.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
	],
	[
		'title' => __( 'Модальные окна', 'wp-frame' ),
		'text'  => __( 'Лёгкая JS-система модалей без зависимостей. Поддержка нескольких окон и маркетинг-сценариев.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>',
	],
	[
		'title' => __( 'Тёмная тема', 'wp-frame' ),
		'text'  => __( 'Автоматическая реакция на prefers-color-scheme и ручное переключение через data-theme.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
	],
	[
		'title' => __( 'Безопасность', 'wp-frame' ),
		'text'  => __( 'Nonce-защита, sanitize/escape всех данных, CSP-заголовки и проверка прав доступа.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
	],
	[
		'title' => __( 'Чистый PHP 8', 'wp-frame' ),
		'text'  => __( 'declare(strict_types=1), PHPDoc, WordPress Coding Standards, никаких глобальных состояний.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
	],
];
?>

<section class="wpf-home-section" aria-labelledby="wpf-features-title">
	<div class="wpf-container">

		<div class="wpf-home-section__header">
			<span class="wpf-home-section__label"><?php esc_html_e( 'Стартовая тема', 'wp-frame' ); ?></span>
			<h2 id="wpf-features-title" class="wpf-home-section__title">
				<?php esc_html_e( 'Что включено в тему', 'wp-frame' ); ?>
			</h2>
			<p class="wpf-home-section__desc">
				<?php esc_html_e( 'Всё что нужно для старта — уже готово и настроено. Остаётся только писать бизнес-логику.', 'wp-frame' ); ?>
			</p>
		</div>

		<div class="wpf-home-feat-grid">
			<?php foreach ( $features as $feat ) : ?>
				<div class="wpf-home-feat-item">
					<div class="wpf-home-feat-item__icon">
						<?php echo $feat['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, hardcoded ?>
					</div>
					<div class="wpf-home-feat-item__body">
						<div class="wpf-home-feat-item__title"><?php echo esc_html( $feat['title'] ); ?></div>
						<p class="wpf-home-feat-item__text"><?php echo esc_html( $feat['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>

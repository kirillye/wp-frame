<?php
/**
 * Tips / Quick-start section — Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tips = [
	[
		'title' => __( 'Установка и запуск', 'wp-frame' ),
		'text'  => __( 'Скопируй тему в wp-content/themes/ и активируй в панели администратора. Сборка не нужна — JS-модули отдаются как есть. Зависимости нужны только для линтеров:', 'wp-frame' ),
		'code'  => 'npm install && npm run lint:js',
	],
	[
		'title' => __( 'Создать новый компонент', 'wp-frame' ),
		'text'  => __( 'Создай PHP-файл в template-parts/ и CSS-файл в assets/css/components/. Добавь импорт в style.css через @layer:', 'wp-frame' ),
		'code'  => "@import 'components/wpf-my-component.css' layer(components);",
	],
	[
		'title' => __( 'Подключить форму', 'wp-frame' ),
		'text'  => __( 'Используй get_template_part() для подключения готовой формы или shortcode [wpf_form]. Каналы доставки настраиваются в настройках темы:', 'wp-frame' ),
		'code'  => "get_template_part( 'template-parts/sections/contacts/form' );",
	],
	[
		'title' => __( 'CSS-токены вместо магических чисел', 'wp-frame' ),
		'text'  => __( 'Все цвета, отступы и размеры шрифтов берутся из CSS Custom Properties. Не используй жёстко заданные значения:', 'wp-frame' ),
		'code'  => 'padding: var(--wpf-space-xl);  color: var(--wpf-color-primary);',
	],
	[
		'title' => __( 'Открыть модальное окно', 'wp-frame' ),
		'text'  => __( 'Добавь атрибут data-modal-open с ID окна на любой элемент. Зарегистрируй HTML окна в template-parts/modals-marketing.php:', 'wp-frame' ),
		'code'  => '<button data-modal-open="my-modal">Открыть окно</button>',
	],
	[
		'title' => __( 'Переключить тёмный режим', 'wp-frame' ),
		'text'  => __( 'Тема реагирует на системные настройки автоматически. Для ручного переключения установи атрибут на элемент <html>:', 'wp-frame' ),
		'code'  => "document.documentElement.setAttribute('data-theme', 'dark');",
	],
];
?>

<section class="wpf-home-section wpf-home-section--alt" id="tips" aria-labelledby="wpf-tips-title">
	<div class="wpf-container">

		<div class="wpf-home-section__header">
			<span class="wpf-home-section__label"><?php esc_html_e( 'Быстрый старт', 'wp-frame' ); ?></span>
			<h2 id="wpf-tips-title" class="wpf-home-section__title">
				<?php esc_html_e( 'Полезные подсказки', 'wp-frame' ); ?>
			</h2>
			<p class="wpf-home-section__desc">
				<?php esc_html_e( 'Шпаргалка по ключевым сценариям работы с темой.', 'wp-frame' ); ?>
			</p>
		</div>

		<div class="wpf-home-tips-list">
			<?php foreach ( $tips as $i => $tip ) : ?>
				<div class="wpf-home-tip">
					<div class="wpf-home-tip__number" aria-hidden="true"><?php echo esc_html( (string) ( $i + 1 ) ); ?></div>
					<div class="wpf-home-tip__body">
						<h3 class="wpf-home-tip__title"><?php echo esc_html( $tip['title'] ); ?></h3>
						<p class="wpf-home-tip__text"><?php echo esc_html( $tip['text'] ); ?></p>
						<?php if ( ! empty( $tip['code'] ) ) : ?>
							<code class="wpf-home-tip__code"><?php echo esc_html( $tip['code'] ); ?></code>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>

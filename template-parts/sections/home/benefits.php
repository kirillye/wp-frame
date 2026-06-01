<?php
/**
 * Benefits section — WordPress advantages. Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$benefits = [
	[
		'color' => 'blue',
		'title' => __( 'Open Source', 'wp-frame' ),
		'text'  => __( 'Бесплатный и открытый исходный код. Огромное сообщество разработчиков по всему миру.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
	],
	[
		'color' => 'purple',
		'title' => __( 'Экосистема плагинов', 'wp-frame' ),
		'text'  => __( '60 000+ плагинов для любых задач: SEO, формы, e-commerce, кэширование, безопасность.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
	],
	[
		'color' => 'green',
		'title' => __( 'SEO из коробки', 'wp-frame' ),
		'text'  => __( 'Чистая семантическая разметка, поддержка schema.org, мета-теги и интеграция с Yoast / RankMath.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
	],
	[
		'color' => 'orange',
		'title' => __( 'Простота для клиента', 'wp-frame' ),
		'text'  => __( 'Редактор Gutenberg понятен с первого раза. Клиент сам обновляет контент без помощи разработчика.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
	],
	[
		'color' => 'pink',
		'title' => __( 'Гибкость и масштаб', 'wp-frame' ),
		'text'  => __( 'От лендинга до крупного портала. REST API, WPGraphQL, headless — любая архитектура.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
	],
	[
		'color' => 'teal',
		'title' => __( 'Безопасность', 'wp-frame' ),
		'text'  => __( 'Регулярные обновления ядра, nonce-защита, escaping данных, система ролей и прав доступа.', 'wp-frame' ),
		'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
	],
];
?>

<section class="wpf-home-section wpf-home-section--alt" id="benefits" aria-labelledby="wpf-benefits-title">
	<div class="wpf-container">

		<div class="wpf-home-section__header">
			<span class="wpf-home-section__label"><?php esc_html_e( 'Почему WordPress', 'wp-frame' ); ?></span>
			<h2 id="wpf-benefits-title" class="wpf-home-section__title">
				<?php esc_html_e( 'Платформа которой доверяют', 'wp-frame' ); ?>
			</h2>
			<p class="wpf-home-section__desc">
				<?php esc_html_e( 'WordPress работает на 43% всех сайтов в интернете — это результат 20 лет развития, надёжности и огромного сообщества.', 'wp-frame' ); ?>
			</p>
		</div>

		<div class="wpf-grid wpf-grid--3">
			<?php foreach ( $benefits as $benefit ) : ?>
				<article class="wpf-home-bcard">
					<div class="wpf-home-bcard__icon wpf-home-bcard__icon--<?php echo esc_attr( $benefit['color'] ); ?>">
						<?php echo $benefit['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, hardcoded ?>
					</div>
					<h3 class="wpf-home-bcard__title"><?php echo esc_html( $benefit['title'] ); ?></h3>
					<p class="wpf-home-bcard__text"><?php echo esc_html( $benefit['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>

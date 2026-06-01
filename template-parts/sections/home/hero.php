<?php
/**
 * Hero section — Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tech_stack = [ 'WordPress', 'PHP 8+', 'ES Modules', 'BEM', 'CSS Layers', 'REST API' ];
?>

<section class="wpf-home-hero" aria-labelledby="wpf-home-hero-title">

	<!-- Abstract floating blobs -->
	<div class="wpf-home-hero__shapes" aria-hidden="true">
		<div class="wpf-home-hero__shape wpf-home-hero__shape--1"></div>
		<div class="wpf-home-hero__shape wpf-home-hero__shape--2"></div>
		<div class="wpf-home-hero__shape wpf-home-hero__shape--3"></div>
	</div>

	<div class="wpf-container">
		<div class="wpf-home-hero__inner">

			<div class="wpf-home-hero__badge">
				<span class="wpf-home-hero__badge-dot" aria-hidden="true"></span>
				<?php esc_html_e( 'WordPress Starter Theme', 'wp-frame' ); ?>
			</div>

			<h1 id="wpf-home-hero-title" class="wpf-home-hero__title">
				<?php esc_html_e( 'Разрабатывай сайты на', 'wp-frame' ); ?>
				<span><?php esc_html_e( 'WordPress', 'wp-frame' ); ?></span>
				<?php esc_html_e( 'быстрее', 'wp-frame' ); ?>
			</h1>

			<p class="wpf-home-hero__subtitle">
				<?php esc_html_e( 'Профессиональная стартовая тема с чистой архитектурой, современными инструментами и продуманной системой компонентов. Готово к работе с первой строки.', 'wp-frame' ); ?>
			</p>

			<div class="wpf-home-hero__actions">
				<a href="#benefits" class="wpf-btn wpf-btn--primary">
					<?php esc_html_e( 'Узнать о теме', 'wp-frame' ); ?>
				</a>
				<button class="wpf-btn wpf-btn--secondary" type="button" data-modal-open="contact">
					<?php esc_html_e( 'Связаться', 'wp-frame' ); ?>
				</button>
			</div>

			<div class="wpf-home-hero__tech">
				<?php foreach ( $tech_stack as $item ) : ?>
					<span class="wpf-home-hero__tech-pill"><?php echo esc_html( $item ); ?></span>
				<?php endforeach; ?>
			</div>

		</div>
	</div>

	<!-- Wave divider -->
	<div class="wpf-home-hero__wave" aria-hidden="true">
		<svg viewBox="0 0 1440 64" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
			<path d="M0,32 C240,64 480,0 720,32 C960,64 1200,0 1440,32 L1440,64 L0,64 Z" fill="var(--wpf-color-bg)" />
		</svg>
	</div>
</section>

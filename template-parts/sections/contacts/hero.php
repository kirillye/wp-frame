<?php
/**
 * Секция Hero — страница «Контакты».
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-contacts-hero" aria-labelledby="wpf-contacts-hero-title">
	<div class="wpf-container">
		<h1 id="wpf-contacts-hero-title" class="wpf-contacts-hero__title">
			<?php the_title(); ?>
		</h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="wpf-contacts-hero__subtitle">
				<?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

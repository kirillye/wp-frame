<?php
/**
 * Секция Hero — страница «О нас».
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-about-hero" aria-labelledby="wpf-about-hero-title">
	<div class="wpf-container">
		<h1 id="wpf-about-hero-title" class="wpf-about-hero__title">
			<?php the_title(); ?>
		</h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="wpf-about-hero__subtitle">
				<?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

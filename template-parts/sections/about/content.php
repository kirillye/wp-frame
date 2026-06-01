<?php
/**
 * Контент страницы «О нас» и миниатюра записи при наличии.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-about-body" aria-label="<?php echo esc_attr__( 'О компании', 'wp-frame' ); ?>">
	<div class="wpf-container">

		<?php wpf_post_thumbnail(); ?>

		<div class="wpf-post__content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="wpf-post__page-links">' . esc_html__( 'Страницы:', 'wp-frame' ),
					'after'  => '</div>',
				)
			);
			?>
		</div>

	</div>
</section>

<?php
/**
 * Секция с контентом страницы — шаблон «Главная».
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = get_post_field( 'post_content', get_the_ID() );
if ( $content === '' || $content === null ) {
	return;
}
?>

<section class="wpf-page-home-body" aria-label="<?php echo esc_attr__( 'Содержание страницы', 'wp-frame' ); ?>">
	<div class="wpf-container wpf-post__content">
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
</section>

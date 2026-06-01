<?php
/**
 * Секция Body одного кейса: контент из редактора + возврат к списку.
 *
 * Выполняется внутри цикла.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$archive_url = get_post_type_archive_link( THEME_PREFIX_ . 'portfolio' );
?>

<section class="wpf-case-body">
	<div class="wpf-container wpf-container--narrow">
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

		<?php if ( $archive_url ) : ?>
			<p class="wpf-case-body__back">
				<a href="<?php echo esc_url( $archive_url ); ?>">&larr; <?php esc_html_e( 'Все кейсы', 'wp-frame' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'wpf-post' ); ?>>
	<header class="wpf-post__header">
		<?php the_title( '<h1 class="wpf-post__title">', '</h1>' ); ?>
	</header><!-- .wpf-post__header -->

	<?php wpf_post_thumbnail(); ?>

	<div class="wpf-post__content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="wpf-post__page-links">' . esc_html__( 'Pages:', 'wp-frame' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .wpf-post__content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="wpf-post__footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="wpf-screen-reader-text">%s</span>', 'wp-frame' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="wpf-post__edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .wpf-post__footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->

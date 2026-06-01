<?php
/**
 * Template part for displaying posts.
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
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="wpf-post__title">', '</h1>' );
		else :
			the_title( '<h2 class="wpf-post__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="wpf-post__meta">
				<?php
				wpf_posted_on();
				wpf_posted_by();
				?>
			</div><!-- .wpf-post__meta -->
		<?php endif; ?>
	</header><!-- .wpf-post__header -->

	<?php wpf_post_thumbnail(); ?>

	<div class="wpf-post__content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="wpf-screen-reader-text"> "%s"</span>', 'wp-frame' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="wpf-post__page-links">' . esc_html__( 'Pages:', 'wp-frame' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .wpf-post__content -->

	<footer class="wpf-post__footer">
		<?php wpf_entry_footer(); ?>
	</footer><!-- .wpf-post__footer -->
</article><!-- #post-<?php the_ID(); ?> -->

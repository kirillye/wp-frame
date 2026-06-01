<?php
/**
 * Template part for displaying results in search pages.
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
		<?php the_title( sprintf( '<h2 class="wpf-post__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
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
		<?php the_excerpt(); ?>
	</div><!-- .wpf-post__content -->

	<footer class="wpf-post__footer">
		<?php wpf_entry_footer(); ?>
	</footer><!-- .wpf-post__footer -->
</article><!-- #post-<?php the_ID(); ?> -->

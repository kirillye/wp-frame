<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

get_header();
?>

<main id="primary" class="wpf-main" tabindex="-1">
	<div class="wpf-container wpf-page-layout">
		<div class="wpf-page-layout__content">
			<?php
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content', get_post_type() );

				the_post_navigation(
					array(
						'class'     => 'wpf-post-nav',
						'prev_text' => '<span class="wpf-post-nav__subtitle">' . esc_html__( 'Previous:', 'wp-frame' ) . '</span> <span class="wpf-post-nav__title">%title</span>',
						'next_text' => '<span class="wpf-post-nav__subtitle">' . esc_html__( 'Next:', 'wp-frame' ) . '</span> <span class="wpf-post-nav__title">%title</span>',
					)
				);

				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile;
			?>
		</div><!-- .wpf-page-layout__content -->

		<?php get_sidebar(); ?>
	</div><!-- .wpf-page-layout -->
</main>

<?php
get_footer();

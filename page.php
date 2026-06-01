<?php
/**
 * The template for displaying all pages.
 *
 * Generic fallback for pages without a dedicated page-{slug}.php template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
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

				get_template_part( 'template-parts/content', 'page' );

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

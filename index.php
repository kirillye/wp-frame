<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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
			if ( have_posts() ) :

				if ( is_home() && ! is_front_page() ) :
					?>
					<header class="wpf-page-header">
						<h1 class="wpf-page-header__title wpf-screen-reader-text">
							<?php single_post_title(); ?>
						</h1>
					</header>
					<?php
				endif;

				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content', get_post_type() );

				endwhile;

				wpf_pagination();

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif;
			?>
		</div><!-- .wpf-page-layout__content -->

		<?php get_sidebar(); ?>
	</div><!-- .wpf-page-layout -->
</main>

<?php
get_footer();

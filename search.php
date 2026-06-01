<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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
			<?php if ( have_posts() ) : ?>

				<header class="wpf-page-header">
					<h1 class="wpf-page-header__title">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'wp-frame' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
						?>
					</h1>
				</header>

				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'search' );
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

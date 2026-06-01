<?php
/**
 * The template for displaying archive pages.
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
			<?php if ( have_posts() ) : ?>

				<header class="wpf-page-header">
					<?php
					the_archive_title( '<h1 class="wpf-page-header__title">', '</h1>' );
					the_archive_description( '<div class="wpf-page-header__description">', '</div>' );
					?>
				</header>

				<?php
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

<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
	<div class="wpf-container">
		<section class="wpf-error-404">
			<header class="wpf-page-header">
				<h1 class="wpf-page-header__title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wp-frame' ); ?></h1>
			</header>

			<div class="wpf-error-404__content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'wp-frame' ); ?></p>

				<?php
				get_search_form();

				the_widget( 'WP_Widget_Recent_Posts' );
				?>

				<div class="widget widget_categories">
					<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'wp-frame' ); ?></h2>
					<ul>
						<?php
						wp_list_categories(
							array(
								'orderby'    => 'count',
								'order'      => 'DESC',
								'show_count' => 1,
								'title_li'   => '',
								'number'     => 10,
							)
						);
						?>
					</ul>
				</div><!-- .widget -->

				<?php
				/* translators: %1$s: smiley */
				$wpf_archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'wp-frame' ), convert_smilies( ':)' ) ) . '</p>';
				the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$wpf_archive_content" );

				the_widget( 'WP_Widget_Tag_Cloud' );
				?>
			</div><!-- .wpf-error-404__content -->
		</section><!-- .wpf-error-404 -->
	</div>
</main>

<?php
get_footer();

<?php
/**
 * Template Name: О нас
 * Template Post Type: page
 *
 * Секции:
 *   - hero    → template-parts/sections/about/hero.php
 *   - content → template-parts/sections/about/content.php
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>

<main id="primary" class="wpf-main" tabindex="-1">
	<?php get_template_part( 'template-parts/sections/about/hero' ); ?>
	<?php get_template_part( 'template-parts/sections/about/content' ); ?>
</main>

	<?php
endwhile;
?>

<?php
get_footer();

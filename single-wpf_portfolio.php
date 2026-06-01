<?php
/**
 * Single CPT «Портфолио» (Pattern A — только оркестрация).
 *
 * Секции:
 *   - hero → template-parts/sections/portfolio/hero.php
 *   - body → template-parts/sections/portfolio/body.php
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="wpf-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/sections/portfolio/hero' );
		get_template_part( 'template-parts/sections/portfolio/body' );
	endwhile;
	?>
</main>

<?php
get_footer();

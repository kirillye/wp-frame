<?php
/**
 * Архив CPT «Портфолио» (Pattern A — только оркестрация).
 *
 * Разметка вынесена в template-parts/sections/portfolio/.
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
	<?php get_template_part( 'template-parts/sections/portfolio/archive' ); ?>
</main>

<?php
get_footer();

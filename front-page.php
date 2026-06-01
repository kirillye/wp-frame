<?php
/**
 * Front page template — Homepage with full design sections.
 *
 * Sections:
 *   hero     → template-parts/sections/home/hero.php
 *   stats    → template-parts/sections/home/stats.php
 *   benefits → template-parts/sections/home/benefits.php
 *   features → template-parts/sections/home/features.php
 *   tips     → template-parts/sections/home/tips.php
 *   cta      → template-parts/sections/home/cta.php
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
	<?php get_template_part( 'template-parts/sections/home/hero' ); ?>
	<?php get_template_part( 'template-parts/sections/home/stats' ); ?>
	<?php get_template_part( 'template-parts/sections/home/benefits' ); ?>
	<?php get_template_part( 'template-parts/sections/home/features' ); ?>
	<?php get_template_part( 'template-parts/sections/home/tips' ); ?>
	<?php get_template_part( 'template-parts/sections/home/cta' ); ?>
</main>

<?php get_template_part( 'template-parts/modals', 'marketing' ); ?>

<?php get_footer();

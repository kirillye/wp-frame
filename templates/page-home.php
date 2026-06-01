<?php
/**
 * Template Name: Главная
 * Template Post Type: page
 *
 * Секции:
 *   - hero    → template-parts/sections/home/hero.php
 *   - content → template-parts/sections/home/content.php
 *
 * Примечание: если в «Настройки → Чтение» выбрана статическая главная и в теме есть
 * front-page.php, для самой главной страницы сайта используется front-page.php.
 * Этот шаблон удобен для отдельной страницы с разметкой «как на главной».
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
	<?php get_template_part( 'template-parts/sections/home/hero' ); ?>
	<?php get_template_part( 'template-parts/sections/home/stats' ); ?>
	<?php get_template_part( 'template-parts/sections/home/benefits' ); ?>
	<?php get_template_part( 'template-parts/sections/home/features' ); ?>
	<?php get_template_part( 'template-parts/sections/home/tips' ); ?>
	<?php get_template_part( 'template-parts/sections/home/cta' ); ?>
</main>

	<?php
	endwhile;

	get_template_part( 'template-parts/modals', 'marketing' );
?>

<?php get_footer();

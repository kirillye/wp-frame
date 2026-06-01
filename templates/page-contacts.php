<?php
/**
 * Template Name: Контакты
 * Template Post Type: page
 *
 * Секции:
 *   - hero → template-parts/sections/contacts/hero.php
 *   - info → template-parts/sections/contacts/info.php
 *   - form → template-parts/sections/contacts/form.php
 *
 * Телефон, email и адрес берутся из «Настройки темы», если поля в админке заполнены.
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
	<?php get_template_part( 'template-parts/sections/contacts/hero' ); ?>
	<?php get_template_part( 'template-parts/sections/contacts/info' ); ?>
	<?php get_template_part( 'template-parts/sections/contacts/form' ); ?>
</main>

	<?php
endwhile;
?>

<?php
get_footer();

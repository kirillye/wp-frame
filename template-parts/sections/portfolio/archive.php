<?php
/**
 * Секция: сетка кейсов на архиве портфолио.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="wpf-portfolio" aria-labelledby="wpf-portfolio-title">
	<div class="wpf-container">
		<header class="wpf-portfolio__header">
			<h1 id="wpf-portfolio-title" class="wpf-portfolio__title">
				<?php echo esc_html( post_type_archive_title( '', false ) ); ?>
			</h1>
			<?php
			$desc = get_the_archive_description();
			if ( $desc ) :
				?>
				<div class="wpf-portfolio__description"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="wpf-portfolio__grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/sections/portfolio/card' );
				endwhile;
				?>
			</div>

			<?php wpf_pagination(); ?>
		<?php else : ?>
			<p class="wpf-portfolio__empty"><?php esc_html_e( 'Кейсы пока не добавлены.', 'wp-frame' ); ?></p>
		<?php endif; ?>
	</div>
</section>

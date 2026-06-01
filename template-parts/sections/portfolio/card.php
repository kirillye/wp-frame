<?php
/**
 * Карточка одного кейса (используется в цикле архива).
 *
 * Выполняется внутри цикла — текущая запись уже установлена.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wpf_terms = get_the_term_list( get_the_ID(), THEME_PREFIX_ . 'portfolio_cat', '', ', ' );
?>

<article <?php post_class( 'wpf-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="wpf-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				'wpf-card',
				array(
					'class'   => 'wpf-card__image',
					'loading' => 'lazy',
					'alt'     => the_title_attribute( array( 'echo' => false ) ),
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="wpf-card__body">
		<?php if ( $wpf_terms && ! is_wp_error( $wpf_terms ) ) : ?>
			<div class="wpf-card__meta"><?php echo wp_kses_post( $wpf_terms ); ?></div>
		<?php endif; ?>

		<h2 class="wpf-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() ) : ?>
			<p class="wpf-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</article>

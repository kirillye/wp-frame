<?php
/**
 * Секция Hero одного кейса: заголовок, мета из ACF, обложка.
 *
 * Пример ACF-полей (привязка location: post_type == wpf_portfolio):
 *   - wpf_portfolio_client (text)
 *   - wpf_portfolio_year   (text/number)
 *   - wpf_portfolio_url     (url)
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$client = (string) wpf_field( 'wpf_portfolio_client' );
$year   = (string) wpf_field( 'wpf_portfolio_year' );
$url    = (string) wpf_field( 'wpf_portfolio_url' );
?>

<section class="wpf-case-hero" aria-labelledby="wpf-case-title">
	<div class="wpf-container">
		<?php wpf_breadcrumbs(); ?>

		<h1 id="wpf-case-title" class="wpf-case-hero__title"><?php the_title(); ?></h1>

		<?php if ( $client !== '' || $year !== '' || $url !== '' ) : ?>
			<dl class="wpf-case-hero__meta">
				<?php if ( $client !== '' ) : ?>
					<div class="wpf-case-hero__meta-item">
						<dt><?php esc_html_e( 'Клиент', 'wp-frame' ); ?></dt>
						<dd><?php echo esc_html( $client ); ?></dd>
					</div>
				<?php endif; ?>
				<?php if ( $year !== '' ) : ?>
					<div class="wpf-case-hero__meta-item">
						<dt><?php esc_html_e( 'Год', 'wp-frame' ); ?></dt>
						<dd><?php echo esc_html( $year ); ?></dd>
					</div>
				<?php endif; ?>
				<?php if ( $url !== '' ) : ?>
					<div class="wpf-case-hero__meta-item">
						<dt><?php esc_html_e( 'Сайт', 'wp-frame' ); ?></dt>
						<dd><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $url ); ?></a></dd>
					</div>
				<?php endif; ?>
			</dl>
		<?php endif; ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="wpf-case-hero__cover">
				<?php
				the_post_thumbnail(
					'wpf-hero',
					array(
						'class'   => 'wpf-case-hero__image',
						'loading' => 'eager',
						'alt'     => the_title_attribute( array( 'echo' => false ) ),
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>

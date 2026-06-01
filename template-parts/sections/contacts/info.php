<?php
/**
 * Текст и контакты: настройки темы опционально, затем текст из редактора страницы.
 *
 * Выполняется внутри основного цикла страницы «Контакты».
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone    = trim( (string) wpf_get_setting( 'contact_phone', '' ) );
$email    = trim( (string) wpf_get_setting( 'contact_email', '' ) );
$address  = trim( (string) wpf_get_setting( 'contact_address', '' ) );
$worktime = trim( (string) wpf_get_setting( 'contact_worktime', '' ) );

$post_content = isset( $post->post_content ) ? trim( (string) $post->post_content ) : '';

if (
	$phone === '' && $email === '' && $address === ''
	&& $worktime === '' && $post_content === ''
) {
	return;
}
?>

<section class="wpf-contacts-info" aria-label="<?php echo esc_attr__( 'Контактная информация', 'wp-frame' ); ?>">
	<div class="wpf-container">
		<?php if ( $address !== '' || $phone !== '' || $email !== '' || $worktime !== '' ) : ?>
			<ul class="wpf-contacts-info__list">
				<?php if ( $address !== '' ) : ?>
					<li class="wpf-contacts-info__item">
						<strong><?php esc_html_e( 'Адрес', 'wp-frame' ); ?>:</strong>
						<?php echo nl2br( esc_html( $address ) ); ?>
					</li>
				<?php endif; ?>
				<?php if ( $phone !== '' ) : ?>
					<li class="wpf-contacts-info__item">
						<strong><?php esc_html_e( 'Телефон', 'wp-frame' ); ?>:</strong>
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
					</li>
				<?php endif; ?>
				<?php if ( $email !== '' ) : ?>
					<li class="wpf-contacts-info__item">
						<strong><?php esc_html_e( 'Email', 'wp-frame' ); ?>:</strong>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
					</li>
				<?php endif; ?>
				<?php if ( $worktime !== '' ) : ?>
					<li class="wpf-contacts-info__item">
						<strong><?php esc_html_e( 'Время работы', 'wp-frame' ); ?>:</strong>
						<?php echo esc_html( $worktime ); ?>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $post_content !== '' ) : ?>
			<div class="wpf-post__content">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="wpf-post__page-links">' . esc_html__( 'Страницы:', 'wp-frame' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>

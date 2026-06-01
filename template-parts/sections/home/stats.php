<?php
/**
 * Stats strip — Homepage.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = [
	[ 'value' => '43%',  'label' => __( 'сайтов в мире на WordPress', 'wp-frame' ) ],
	[ 'value' => '60k+', 'label' => __( 'плагинов в официальном каталоге', 'wp-frame' ) ],
	[ 'value' => 'PHP 8','label' => __( 'минимальная версия для темы', 'wp-frame' ) ],
	[ 'value' => '0',    'label' => __( 'лишних JS-зависимостей', 'wp-frame' ) ],
];
?>

<section class="wpf-home-section" aria-label="<?php esc_attr_e( 'Статистика', 'wp-frame' ); ?>">
	<div class="wpf-container">
		<div class="wpf-home-stats">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="wpf-home-stat">
					<div class="wpf-home-stat__value"><?php echo esc_html( $stat['value'] ); ?></div>
					<div class="wpf-home-stat__label"><?php echo esc_html( $stat['label'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
/**
 * CPT «Портфолио» — пример drop-in модуля.
 *
 * Чтобы отключить на проекте — удалить этот файл. Чтобы включить на другом —
 * скопировать (обычно в дочернюю тему: inc/cpt/portfolio.php).
 *
 * Поля кейса описываются в ACF UI и привязываются к этому типу через
 * location: post_type == wpf_portfolio (JSON ложится в acf-json/).
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Зарегистрировать тип записи «Портфолио» и таксономию категорий кейсов.
 *
 * @return void
 */
function wpf_cpt_portfolio(): void {
	wpf_register_cpt(
		'portfolio',
		array(
			'labels'      => array(
				'name'               => esc_html__( 'Портфолио', 'wp-frame' ),
				'singular_name'      => esc_html__( 'Кейс', 'wp-frame' ),
				'menu_name'          => esc_html__( 'Портфолио', 'wp-frame' ),
				'all_items'          => esc_html__( 'Все кейсы', 'wp-frame' ),
				'add_new'            => esc_html__( 'Добавить кейс', 'wp-frame' ),
				'add_new_item'       => esc_html__( 'Добавить кейс', 'wp-frame' ),
				'edit_item'          => esc_html__( 'Редактировать кейс', 'wp-frame' ),
				'new_item'           => esc_html__( 'Новый кейс', 'wp-frame' ),
				'view_item'          => esc_html__( 'Посмотреть кейс', 'wp-frame' ),
				'search_items'       => esc_html__( 'Искать кейсы', 'wp-frame' ),
				'not_found'          => esc_html__( 'Кейсы не найдены', 'wp-frame' ),
				'not_found_in_trash' => esc_html__( 'В корзине кейсов нет', 'wp-frame' ),
			),
			'menu_icon'   => 'dashicons-portfolio',
			'menu_position' => 25,
		)
	);

	// Таксономия категорий кейсов. Ключ — с префиксом (wpf_portfolio_cat),
	// URL-slug чистый (portfolio-category).
	register_taxonomy(
		THEME_PREFIX_ . 'portfolio_cat',
		THEME_PREFIX_ . 'portfolio',
		array(
			'labels'            => array(
				'name'          => esc_html__( 'Категории кейсов', 'wp-frame' ),
				'singular_name' => esc_html__( 'Категория', 'wp-frame' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'portfolio-category' ),
		)
	);
}
add_action( 'init', 'wpf_cpt_portfolio' );

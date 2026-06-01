<?php
/**
 * CPT: Form Entry — хранилище заявок с форм.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Регистрация CPT ───────────────────────────────────────────────

add_action( 'init', 'wpf_register_form_entry_cpt' );

/**
 * Register the Form Entry custom post type.
 *
 * @return void
 */
function wpf_register_form_entry_cpt(): void {
	register_post_type( 'wpf_form_entry', [
		'labels'          => [
			'name'               => __( 'Заявки', 'wp-frame' ),
			'singular_name'      => __( 'Заявка', 'wp-frame' ),
			'menu_name'          => __( 'Заявки', 'wp-frame' ),
			'add_new'            => __( 'Добавить', 'wp-frame' ),
			'all_items'          => __( 'Все заявки', 'wp-frame' ),
			'view_item'          => __( 'Просмотр заявки', 'wp-frame' ),
			'not_found'          => __( 'Заявок нет', 'wp-frame' ),
		],
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'supports'            => [ 'title' ],
		'capability_type'     => 'post',
		'capabilities'        => [
			'create_posts' => 'do_not_allow',
		],
		'map_meta_cap'        => true,
		'menu_icon'           => 'dashicons-email-alt',
		'menu_position'       => 25,
		'delete_with_user'    => false,
	] );
}


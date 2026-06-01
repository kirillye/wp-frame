<?php
/**
 * CPT-инфраструктура: фабрика регистрации + автозагрузка drop-in модулей.
 *
 * Сами типы записей не описываются здесь. Каждый CPT — отдельный файл в
 * inc/cpt/{name}.php (в ядре или в дочерней теме), который сам регистрируется
 * на хук init через wpf_register_cpt(). Нет файла — нет типа. Это позволяет
 * включать портфолио/услуги/etc. по необходимости, не трогая ядро.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Зарегистрировать кастомный тип записи с префиксом и разумными дефолтами.
 *
 * Ключ типа — всегда {prefix}_{entity} (wpf_portfolio). URL-slug по умолчанию
 * чистый ({entity}), переопределяется через $args['rewrite'].
 *
 * @param string $entity Сущность без префикса, snake_case (например, 'portfolio').
 * @param array  $args   Аргументы register_post_type(), перекрывают дефолты.
 * @return void
 */
function wpf_register_cpt( string $entity, array $args = array() ): void {
	$slug = THEME_PREFIX_ . $entity; // wpf_portfolio

	$defaults = array(
		'public'       => true,
		'show_in_rest' => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-admin-post',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'      => array( 'slug' => $entity ),
	);

	register_post_type( $slug, array_merge( $defaults, $args ) );
}

/**
 * Подключить drop-in CPT-модули из ядра и дочерней темы.
 *
 * Выполняется на этапе загрузки темы (до init); каждый модуль вешает свою
 * регистрацию на init самостоятельно. require_once + array_unique защищают от
 * двойного подключения, когда parent и child совпадают.
 *
 * @return void
 */
function wpf_load_cpt_modules(): void {
	$dirs = array_unique(
		array(
			get_template_directory(),   // ядро
			get_stylesheet_directory(), // дочерняя тема клиента
		)
	);

	foreach ( $dirs as $base ) {
		foreach ( glob( $base . '/inc/cpt/*.php' ) ?: array() as $file ) {
			require_once $file;
		}
	}
}
wpf_load_cpt_modules();

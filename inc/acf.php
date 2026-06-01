<?php
/**
 * ACF-инфраструктура: путь сохранения/загрузки JSON + автозагрузка bootstrap-ов.
 *
 * Сами группы полей здесь не описываются. Каждая группа — отдельный файл
 * inc/acf/page-{slug}.php (или inc/acf/{cpt}.php) в ядре или дочерней теме,
 * который сам регистрируется на хук acf/init через acf_add_local_field_group().
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Сохранять Local JSON в дочернюю тему (в одиночной теме — в саму тему),
 * чтобы правки полей клиента не затирались при обновлении ядра.
 *
 * @return string
 */
function wpf_acf_save_json(): string {
	return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'wpf_acf_save_json' );

/**
 * Загружать JSON из ядра и из дочерней темы.
 *
 * @param array $paths Текущие пути загрузки.
 * @return array
 */
function wpf_acf_load_json( array $paths ): array {
	$paths[] = get_template_directory() . '/acf-json';
	$paths[] = get_stylesheet_directory() . '/acf-json';

	return array_unique( $paths );
}
add_filter( 'acf/settings/load_json', 'wpf_acf_load_json' );

/**
 * Подключить bootstrap-ы групп полей из ядра и дочерней темы.
 *
 * Выполняется на этапе загрузки темы (до acf/init); каждый модуль вешает свою
 * регистрацию на acf/init самостоятельно.
 *
 * @return void
 */
function wpf_load_acf_modules(): void {
	$dirs = array_unique(
		array(
			get_template_directory(),   // ядро
			get_stylesheet_directory(), // дочерняя тема клиента
		)
	);

	foreach ( $dirs as $base ) {
		foreach ( glob( $base . '/inc/acf/*.php' ) ?: array() as $file ) {
			require_once $file;
		}
	}
}
wpf_load_acf_modules();

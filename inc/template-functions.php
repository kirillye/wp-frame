<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Безопасное получение значения ACF с фоллбэком.
 *
 * Единственный разрешённый способ читать ACF (см. CLAUDE.md). Сам проверяет
 * наличие плагина и пустые значения, поэтому в шаблонах не нужны ни
 * function_exists(), ни проверки на null.
 *
 * @param string   $key     Ключ/имя поля (например, 'wpf_portfolio_client').
 * @param int|null $post_id ID записи. null — текущая запись в цикле.
 * @param mixed    $default Значение по умолчанию, если поля/значения нет.
 * @return mixed
 */
function wpf_field( string $key, ?int $post_id = null, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $key, $post_id ?? get_the_ID() );

	return ( null === $value || '' === $value ) ? $default : $value;
}

/**
 * Add custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function wpf_body_classes( array $classes ): array {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Context classes.
	if ( is_front_page() ) {
		$classes[] = 'is-front';
	}

	if ( is_archive() ) {
		$classes[] = 'is-archive';
	}

	if ( is_single() ) {
		$classes[] = 'is-single';
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'has-sidebar';
	} else {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'wpf_body_classes' );

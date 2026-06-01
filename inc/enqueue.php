<?php
/**
 * Script and style enqueuing.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

/**
 * Version for static assets: cache bust by file mtime.
 *
 * @param string $relative_path Relative path from theme root (e.g. 'assets/css/style.css').
 * @return string
 */
function wpf_asset_version( string $relative_path ): string {
	$path = trailingslashit( THEME_DIR ) . ltrim( $relative_path, '/' );
	if ( is_readable( $path ) ) {
		$m = filemtime( $path );
		return false !== $m ? (string) $m : THEME_VERSION;
	}
	return THEME_VERSION;
}

/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function wpf_enqueue_assets(): void {
	$css_ver = wpf_asset_version( 'assets/css/style.css' );
	$js_ver  = wpf_asset_version( 'assets/js/main.js' );

	wp_enqueue_style(
		THEME_PREFIX . '-style',
		THEME_URI . '/assets/css/style.css',
		array(),
		$css_ver
	);

	wp_enqueue_script(
		THEME_PREFIX . '-main',
		THEME_URI . '/assets/js/main.js',
		array(),
		$js_ver,
		true
	);

	wp_add_inline_script(
		THEME_PREFIX . '-main',
		'const wpfData = ' . wp_json_encode(
			array(
				'restUrl'     => esc_url_raw( rest_url( 'wpf/v1/' ) ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'homeUrl'     => home_url(),
				'templateUri' => THEME_URI,
				// nonce/submit_token для форм НЕ вшиваем: под full-page cache они
				// протухают. Формы берут свежие через GET wpf/v1/form/token.
				'i18n'        => array(
					'menuToggle' => esc_html__( 'Меню', 'wp-frame' ),
					'loading'    => esc_html__( 'Загрузка...', 'wp-frame' ),
					'notFound'   => esc_html__( 'Ничего не найдено', 'wp-frame' ),
				),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'wpf_enqueue_assets' );

/**
 * Cookie banner script only when needed (no CLS: template hidden until accept).
 *
 * @return void
 */
function wpf_enqueue_cookies_script(): void {
	if ( ! wpf_get_setting( 'cookie_enabled', true ) ) {
		return;
	}

	$cookie_key = THEME_PREFIX_ . 'cookies_accepted';
	if ( isset( $_COOKIE[ $cookie_key ] ) && '1' === $_COOKIE[ $cookie_key ] ) {
		return;
	}

	$handle = THEME_PREFIX . '-cookies';
	$path   = 'assets/js/modules/wpfCookies.js';
	$ver    = wpf_asset_version( $path );

	wp_enqueue_script(
		$handle,
		THEME_URI . '/' . $path,
		array(),
		$ver,
		true
	);

	$inline_key = 'const wpfCookieKey = ' . wp_json_encode( $cookie_key ) . ';';
	wp_add_inline_script( $handle, $inline_key, 'before' );
}
add_action( 'wp_enqueue_scripts', 'wpf_enqueue_cookies_script', 15 );

/**
 * Conditional script enqueuing.
 *
 * @return void
 */
function wpf_enqueue_conditional(): void {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'wpf_enqueue_conditional', 20 );

/**
 * Add type="module" for the main script (ES modules).
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @param string $src    The script source.
 * @return string
 */
function wpf_add_script_attrs( string $tag, string $handle, string $src ): string {
	$module_handles = array( THEME_PREFIX . '-main' );

	if ( in_array( $handle, $module_handles, true ) ) {
		$tag = str_replace( "type='text/javascript'", 'type="module"', $tag );
		$tag = str_replace( ' src=', ' type="module" src=', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'wpf_add_script_attrs', 10, 3 );

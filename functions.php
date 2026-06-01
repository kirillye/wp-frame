<?php
/**
 * WP Frame functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access denied.
}

// Theme prefix constants.
define( 'THEME_PREFIX', 'wpf' );
define( 'THEME_PREFIX_', 'wpf_' );
define( 'THEME_PREFIX_CSS', 'wpf-' );

// Paths and version (WP Stack / Theme Init).
define( 'THEME_VERSION', wp_get_theme()->get( 'Version' ) ?: '1.0.0' );
define( 'THEME_DIR', get_template_directory() );
define( 'THEME_URI', get_template_directory_uri() );

// Core includes.
require THEME_DIR . '/inc/setup.php';
require THEME_DIR . '/inc/enqueue.php';
require THEME_DIR . '/inc/security.php';
require THEME_DIR . '/inc/security-headers.php';
require THEME_DIR . '/inc/template-tags.php';
require THEME_DIR . '/inc/template-functions.php';
require THEME_DIR . '/inc/customizer.php';

// CPT-инфраструктура: фабрика + автозагрузка drop-in модулей из inc/cpt/.
require THEME_DIR . '/inc/cpt.php';

// ACF-инфраструктура: пути JSON + автозагрузка bootstrap-ов из inc/acf/.
require THEME_DIR . '/inc/acf.php';

// Настройки темы до SEO (используется wpf_get_setting в JSON-LD).
require THEME_DIR . '/inc/admin/theme-settings.php';

require THEME_DIR . '/inc/seo.php';

// Forms infrastructure.
require THEME_DIR . '/inc/forms/cpt-form-entry.php';
require THEME_DIR . '/inc/forms/channels/email.php';
require THEME_DIR . '/inc/forms/channels/telegram.php';
require THEME_DIR . '/inc/forms/channels/bitrix24.php';

// Spam Guard (must load before form-handler: provides wpf_get_client_ip, wpf_spam_guard).
require THEME_DIR . '/inc/forms/spam-guard.php';

// Form handler (depends on spam-guard + channels).
require THEME_DIR . '/inc/forms/form-handler.php';
require THEME_DIR . '/inc/forms/form-admin.php';

// Debug utilities (safe to load always: wpf_log checks WP_DEBUG internally).
require THEME_DIR . '/inc/debug.php';

// Admin-only UI includes.
if ( is_admin() ) {
	require THEME_DIR . '/inc/admin/settings-render.php';
	require THEME_DIR . '/inc/admin/settings-test.php';
}

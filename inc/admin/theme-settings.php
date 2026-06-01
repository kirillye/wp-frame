<?php
/**
 * Theme Settings — core: registration, save, get_setting().
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MYT_SETTINGS_OPTION', THEME_PREFIX_ . 'settings' );

/**
 * Read a theme setting.
 *
 * Priority: wp-config.php constant > DB option > $default.
 *
 * @param string $key     Setting key (tg_token, mail_to, b24_webhook...).
 * @param mixed  $default Default value if nothing is set.
 * @return mixed
 */
function wpf_get_setting( string $key, mixed $default = '' ): mixed {
	$cache_key = THEME_PREFIX_ . 'settings_options_cache';

	if ( ! isset( $GLOBALS[ $cache_key ] ) ) {
		$GLOBALS[ $cache_key ] = (array) get_option( MYT_SETTINGS_OPTION, [] );
	}

	$options = $GLOBALS[ $cache_key ];

	$const_name = strtoupper( THEME_PREFIX ) . '_' . strtoupper( $key );

	if ( defined( $const_name ) ) {
		return constant( $const_name );
	}

	return $options[ $key ] ?? $default;
}

/**
 * Reset static cache for wpf_get_setting().
 *
 * @return void
 */
function wpf_reset_settings_cache(): void {
	$cache_key = THEME_PREFIX_ . 'settings_options_cache';
	unset( $GLOBALS[ $cache_key ] );

	wp_cache_delete( MYT_SETTINGS_OPTION, 'options' );
}

add_action( 'admin_menu', 'wpf_register_settings_page' );

/**
 * Register the theme settings page in WP Admin.
 *
 * @return void
 */
function wpf_register_settings_page(): void {
	add_menu_page(
		__( 'Настройки темы', 'wp-frame' ),
		__( 'Настройки темы', 'wp-frame' ),
		'manage_options',
		THEME_PREFIX . '-settings',
		'wpf_render_settings_page',
		'dashicons-admin-generic',
		80
	);
}

add_action( 'admin_enqueue_scripts', 'wpf_settings_assets' );

/**
 * Enqueue admin settings assets only on our page.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function wpf_settings_assets( string $hook ): void {
	if ( 'toplevel_page_' . THEME_PREFIX . '-settings' !== $hook ) {
		return;
	}

	$css_ver = wpf_asset_version( 'assets/css/admin-settings.css' );
	$js_ver  = wpf_asset_version( 'assets/js/admin-settings.js' );

	wp_enqueue_style(
		THEME_PREFIX_ . 'admin-settings',
		THEME_URI . '/assets/css/admin-settings.css',
		[],
		$css_ver
	);

	wp_enqueue_script(
		THEME_PREFIX_ . 'admin-settings',
		THEME_URI . '/assets/js/admin-settings.js',
		[],
		$js_ver,
		true
	);

	wp_localize_script(
		THEME_PREFIX_ . 'admin-settings',
		THEME_PREFIX . 'Admin',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( THEME_PREFIX_ . 'admin_nonce' ),
			'i18n'    => [
				'testing'        => __( 'Отправка...', 'wp-frame' ),
				'success'        => __( 'Успешно', 'wp-frame' ),
				'error'          => __( 'Ошибка', 'wp-frame' ),
				'sending'        => __( 'Сохраняю...', 'wp-frame' ),
				'exportError'    => __( 'Не удалось получить JSON экспорта.', 'wp-frame' ),
				'importEmpty'    => __( 'Вставьте JSON в поле импорта.', 'wp-frame' ),
				'importError'    => __( 'Импорт не выполнен. Проверьте JSON и права доступа.', 'wp-frame' ),
				'importSuccess'  => __( 'Настройки импортированы. Страница будет перезагружена.', 'wp-frame' ),
				'connectionFail' => __( 'Ошибка соединения', 'wp-frame' ),
			],
		]
	);
}

add_action( 'admin_init', 'wpf_save_settings' );

/**
 * Normalize settings array from raw input (POST or import JSON).
 *
 * @param array $raw Partial or full keys.
 * @return array
 */
function wpf_normalize_settings_array( array $raw ): array {

	return [
		// ── Email ──────────────────────────────────
		'mail_enabled'   => ! empty( $raw['mail_enabled'] ),
		'mail_to'        => sanitize_email( $raw['mail_to'] ?? '' ),
		'mail_from'      => sanitize_email( $raw['mail_from'] ?? '' ),
		'mail_from_name' => sanitize_text_field( $raw['mail_from_name'] ?? '' ),

		// ── Telegram ───────────────────────────────
		'tg_enabled'     => ! empty( $raw['tg_enabled'] ),
		'tg_token'       => sanitize_text_field( $raw['tg_token'] ?? '' ),
		'tg_chat_id'     => sanitize_text_field( $raw['tg_chat_id'] ?? '' ),

		// ── Bitrix24 ───────────────────────────────
		'b24_enabled'    => ! empty( $raw['b24_enabled'] ),
		'b24_webhook'    => sanitize_url( $raw['b24_webhook'] ?? '' ),
		'b24_source'     => sanitize_key( $raw['b24_source'] ?? 'WEB' ),

		// ── Cookies ────────────────────────────────
		'cookie_enabled'       => ! empty( $raw['cookie_enabled'] ),
		'cookie_text'          => sanitize_textarea_field( $raw['cookie_text'] ?? '' ),
		'cookie_policy_url'    => sanitize_url( $raw['cookie_policy_url'] ?? '' ),
		'cookie_policy_anchor' => sanitize_text_field( $raw['cookie_policy_anchor'] ?? '' ),
		'cookie_btn_text'      => sanitize_text_field( $raw['cookie_btn_text'] ?? '' ),

		// ── Контакты ───────────────────────────────
		'contact_address'      => sanitize_textarea_field( $raw['contact_address'] ?? '' ),
		'contact_phone'        => sanitize_text_field( $raw['contact_phone'] ?? '' ),
		'contact_email'        => sanitize_email( $raw['contact_email'] ?? '' ),
		'contact_worktime'     => sanitize_text_field( $raw['contact_worktime'] ?? '' ),

		// ── Соцсети ────────────────────────────────
		'social_telegram'      => sanitize_url( $raw['social_telegram'] ?? '' ),
		'social_whatsapp'      => preg_replace( '/\D/', '', $raw['social_whatsapp'] ?? '' ),
		'social_vk'            => sanitize_url( $raw['social_vk'] ?? '' ),
		'social_instagram'     => sanitize_url( $raw['social_instagram'] ?? '' ),
		'social_youtube'       => sanitize_url( $raw['social_youtube'] ?? '' ),
		'social_tiktok'        => sanitize_url( $raw['social_tiktok'] ?? '' ),
		'social_ok'            => sanitize_url( $raw['social_ok'] ?? '' ),
		'social_max'           => sanitize_url( $raw['social_max'] ?? '' ),

		// ── Скрипты (trusted admin code) ───────────
		'script_ym'            => wpf_sanitize_script_code( $raw['script_ym'] ?? '' ),
		'script_ga'            => wpf_sanitize_script_code( $raw['script_ga'] ?? '' ),
		'script_head_custom'   => wpf_sanitize_script_code( $raw['script_head_custom'] ?? '' ),
	];
}

/**
 * Handle settings form submission.
 *
 * @return void
 */
function wpf_save_settings(): void {
	$nonce_field = THEME_PREFIX_ . 'settings_nonce';

	if ( ! isset( $_POST[ $nonce_field ] ) ) {
		return;
	}

	if ( ! isset( $_POST['action'] ) || THEME_PREFIX_ . 'save_settings' !== $_POST['action'] ) {
		return;
	}

	check_admin_referer( THEME_PREFIX_ . 'settings_save', $nonce_field );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Нет доступа.', 'wp-frame' ) );
	}

	$post_opt = $_POST[ THEME_PREFIX_ . 'opt' ] ?? [];
	// Суперглобали GPCS в WP при загрузке приводятся к «слэшнутому» виду (совместимость с magic quotes):
	// см. wp_magic_quotes() → addslashes() по строкам. Без wp_unslash() каждое сохранение наращивает слэши (\', потом \\' и т.д.).
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- снимаем слэши WP, затем wpf_normalize_settings_array().
	$raw = is_array( $post_opt ) ? wp_unslash( $post_opt ) : [];

	$settings = wpf_normalize_settings_array( $raw );

	$old = (array) get_option( MYT_SETTINGS_OPTION, [] );

	// Защита от затирания ключей.
	foreach ( [ 'tg_token', 'b24_webhook' ] as $secret_key ) {
		if ( '' === $settings[ $secret_key ] && ! empty( $old[ $secret_key ] ) ) {
			$settings[ $secret_key ] = $old[ $secret_key ];
		}
	}

	update_option( MYT_SETTINGS_OPTION, $settings, false );
	wpf_reset_settings_cache();

	$redirect = add_query_arg(
		[
			'page'  => THEME_PREFIX . '-settings',
			'tab'   => sanitize_key( wp_unslash( $_POST['current_tab'] ?? 'email' ) ),
			'saved' => '1',
		],
		admin_url( 'admin.php' )
	);

	wp_safe_redirect( $redirect );
	exit;
}

// ─── Санитайзер для скриптов ─────────────────────────────────────────

/**
 * Minimal sanitization for script/metrics fields.
 * Preserves HTML tags — needed for <script>, <noscript>, pixels.
 * Apply ONLY to admin-trusted fields.
 *
 * @param string $code Raw script code.
 * @return string
 */
function wpf_sanitize_script_code( string $code ): string {
	$code = str_replace( "\0", '', $code );
	// Нельзя сначала заменять все \r на \n: тогда \r\n превратится в \n\n (лишние пустые строки).
	// \R в PCRE схлопывает CRLF в один перевод строки.
	$code = preg_replace( '/\R/u', "\n", $code );
	return trim( $code );
}

// ─── Инъекция скриптов в wp_head ─────────────────────────────────────

add_action( 'wp_head', 'wpf_inject_head_scripts', 1 );

/**
 * Inject metric/analytics codes into <head>.
 *
 * @return void
 */
function wpf_inject_head_scripts(): void {
	if ( is_admin() ) {
		return;
	}

	$ym     = wpf_get_setting( 'script_ym' );
	$ga     = wpf_get_setting( 'script_ga' );
	$custom = wpf_get_setting( 'script_head_custom' );

	foreach ( array( $ym, $ga, $custom ) as $block ) {
		if ( is_string( $block ) && '' !== $block ) {
			echo $block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

// ─── Хелперы для шаблонов ────────────────────────────────────────────

/**
 * Return phone as tel: link.
 *
 * @param string $class CSS class for the link.
 * @return string
 */
function wpf_phone_link( string $class = '' ): string {
	$phone = wpf_get_setting( 'contact_phone' );
	if ( ! $phone ) {
		return '';
	}

	$digits = preg_replace( '/\D/', '', $phone );
	if ( 10 === strlen( $digits ) ) {
		$digits = '7' . $digits;
	}

	$attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<a href="tel:+' . esc_attr( $digits ) . '"' . $attr . '>'
		. esc_html( $phone )
		. '</a>';
}

/**
 * Return email as mailto: link.
 *
 * @param string $class CSS class for the link.
 * @return string
 */
function wpf_email_link( string $class = '' ): string {
	$email = wpf_get_setting( 'contact_email' );
	if ( ! $email ) {
		return '';
	}

	$attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '"' . $attr . '>'
		. esc_html( antispambot( $email ) )
		. '</a>';
}

/**
 * Get array of active social links.
 *
 * @return array
 */
function wpf_social_links(): array {
	$map = [
		'telegram'  => 'Telegram',
		'whatsapp'  => 'WhatsApp',
		'vk'        => 'ВКонтакте',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'ok'        => 'Одноклассники',
		'max'       => 'Max',
		'tiktok'    => 'TikTok',
	];

	$links = [];

	foreach ( $map as $key => $label ) {
		if ( 'whatsapp' === $key ) {
			$phone = wpf_get_setting( 'social_whatsapp' );
			if ( $phone ) {
				$links[] = [
					'key'   => 'whatsapp',
					'label' => 'WhatsApp',
					'url'   => 'https://wa.me/' . $phone,
				];
			}
			continue;
		}

		$url = wpf_get_setting( "social_{$key}" );
		if ( $url ) {
			$links[] = [
				'key'   => $key,
				'label' => $label,
				'url'   => $url,
			];
		}
	}

	return $links;
}

/**
 * Output social links.
 *
 * @param string $class_prefix CSS class prefix for the list.
 * @return void
 */
function wpf_social_links_output( string $class_prefix = 'wpf-social' ): void {
	$socials = wpf_social_links();

	if ( empty( $socials ) ) {
		return;
	}
	?>
	<ul class="<?php echo esc_attr( $class_prefix ); ?>">
		<?php foreach ( $socials as $s ) : ?>
			<li class="<?php echo esc_attr( $class_prefix . '__item' ); ?>">
				<a href="<?php echo esc_url( $s['url'] ); ?>"
				   class="<?php echo esc_attr( $class_prefix . '__link ' . $class_prefix . '__link--' . $s['key'] ); ?>"
				   target="_blank"
				   rel="noopener noreferrer"
				   aria-label="<?php echo esc_attr( $s['label'] ); ?>">
					<?php echo esc_html( $s['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

// ─── Экспорт / импорт настроек (AJAX) ────────────────────────────────

add_action( 'wp_ajax_wpf_export_settings', 'wpf_ajax_export_settings' );

/**
 * Export theme settings as JSON (without secret tokens).
 *
 * @return void
 */
function wpf_ajax_export_settings(): void {
	check_ajax_referer( THEME_PREFIX_ . 'admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Нет доступа.', 'wp-frame' ), 403 );
	}

	$options = (array) get_option( MYT_SETTINGS_OPTION, [] );
	unset( $options['tg_token'], $options['b24_webhook'] );

	wp_send_json_success(
		[
			'version'  => THEME_VERSION,
			'site'     => home_url(),
			'exported' => gmdate( 'c' ),
			'settings' => $options,
		]
	);
}

add_action( 'wp_ajax_wpf_import_settings', 'wpf_ajax_import_settings' );

/**
 * Import theme settings from JSON payload.
 *
 * @return void
 */
function wpf_ajax_import_settings(): void {
	check_ajax_referer( THEME_PREFIX_ . 'admin_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Нет доступа.', 'wp-frame' ), 403 );
	}

	$raw = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
	if ( '' === $raw ) {
		wp_send_json_error( __( 'Данные не переданы.', 'wp-frame' ) );
	}

	$import = json_decode( $raw, true );
	if ( ! is_array( $import ) || ! isset( $import['settings'] ) || ! is_array( $import['settings'] ) ) {
		wp_send_json_error( __( 'Неверный формат JSON.', 'wp-frame' ) );
	}

	$current = (array) get_option( MYT_SETTINGS_OPTION, [] );
	$merged  = array_merge( $current, $import['settings'] );
	$merged  = wpf_normalize_settings_array( $merged );

	foreach ( [ 'tg_token', 'b24_webhook' ] as $secret_key ) {
		if ( '' === $merged[ $secret_key ] && ! empty( $current[ $secret_key ] ) ) {
			$merged[ $secret_key ] = $current[ $secret_key ];
		}
	}

	update_option( MYT_SETTINGS_OPTION, $merged, false );
	wpf_reset_settings_cache();

	wp_send_json_success(
		[
			'message' => sprintf(
				/* translators: %d: number of keys processed */
				__( 'Импорт выполнен. Обновлено полей: %d', 'wp-frame' ),
				count( $merged )
			),
		]
	);
}

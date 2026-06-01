<?php
/**
 * Cookie Banner template — displayed when user hasn't accepted.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Баннер отключён в настройках.
if ( ! wpf_get_setting( 'cookie_enabled', true ) ) {
	return;
}

// Уже принял — не рендерим (нет CLS).
$cookie_key = THEME_PREFIX_ . 'cookies_accepted';
if ( isset( $_COOKIE[ $cookie_key ] ) && '1' === $_COOKIE[ $cookie_key ] ) {
	return;
}

$text          = wpf_get_setting( 'cookie_text', 'Мы используем cookies, продолжая оставаться на сайте, вы соглашаетесь с {policy} их использования.' );
$policy_url    = wpf_get_setting( 'cookie_policy_url', '/policy/' );
$policy_anchor = wpf_get_setting( 'cookie_policy_anchor', 'политикой' );
$btn_text      = wpf_get_setting( 'cookie_btn_text', 'Понятно' );

$policy_link = '<a href="' . esc_url( $policy_url ) . '" class="wpf-cookie-banner__link">'
				. esc_html( $policy_anchor ) . '</a>';
$text_safe   = str_replace( '{policy}', $policy_link, esc_html( $text ) );
?>
<div class="wpf-cookie-banner"
     id="wpf-cookie-banner"
     role="region"
     aria-label="<?php esc_attr_e( 'Уведомление о файлах cookie', 'wp-frame' ); ?>">

    <p class="wpf-cookie-banner__text">
        <?php echo $text_safe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </p>

    <button type="button"
            class="wpf-cookie-banner__btn"
            id="wpf-cookie-accept"
            aria-label="<?php esc_attr_e( 'Принять использование cookies', 'wp-frame' ); ?>">
        <?php echo esc_html( $btn_text ); ?>
    </button>

</div>

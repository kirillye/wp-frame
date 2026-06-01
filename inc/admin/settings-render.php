<?php
/**
 * Theme Settings — render the admin page with tabs.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the full settings page with tabs and forms.
 *
 * @return void
 */
function wpf_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$active_tab = sanitize_key( $_GET['tab'] ?? 'email' );
	$saved      = isset( $_GET['saved'] ) && '1' === $_GET['saved'];

	$opt = (array) get_option( MYT_SETTINGS_OPTION, [] );

	$v = function ( string $key, mixed $default = '' ) use ( $opt ): mixed {
		return wpf_get_setting( $key, $opt[ $key ] ?? $default );
	};

	$overridden = function ( string $key ): bool {
		$const = strtoupper( THEME_PREFIX ) . '_' . strtoupper( $key );
		return defined( $const );
	};

	$tabs = [
		'email'    => __( 'Email', 'wp-frame' ),
		'telegram' => __( 'Telegram', 'wp-frame' ),
		'bitrix'   => __( 'Bitrix24', 'wp-frame' ),
		'cookies'  => __( 'Cookies', 'wp-frame' ),
		'contacts' => __( 'Контакты', 'wp-frame' ),
		'social'   => __( 'Соцсети', 'wp-frame' ),
		'scripts'  => __( 'Скрипты', 'wp-frame' ),
	];

	?>
	<div class="wrap wpf-settings-wrap">

		<h1 class="wp-heading-inline"><?php esc_html_e( 'Настройки темы', 'wp-frame' ); ?></h1>
		<hr class="wp-header-end">

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e( 'Настройки сохранены.', 'wp-frame' ); ?></strong></p>
			</div>
		<?php endif; ?>

		<?php
		$const_warnings = [];
		foreach (
			[
				'tg_token'    => 'TG_TOKEN',
				'b24_webhook' => 'B24_WEBHOOK',
				'mail_to'     => 'MAIL_TO',
			] as $key => $suffix
		) {
			if ( $overridden( $key ) ) {
				$const_warnings[] = strtoupper( THEME_PREFIX ) . '_' . $suffix;
			}
		}

		if ( $const_warnings ) :
			?>
			<div class="notice notice-warning">
				<p>
					<?php esc_html_e( 'Эти настройки переопределены константами в wp-config.php и недоступны для редактирования:', 'wp-frame' ); ?><br>
					<code><?php echo esc_html( implode( '</code>, <code>', $const_warnings ) ); ?></code>
				</p>
			</div>
		<?php endif; ?>

		<nav class="nav-tab-wrapper">
			<?php foreach ( $tabs as $slug => $label ) :
				$url = add_query_arg(
					[
						'page' => THEME_PREFIX . '-settings',
						'tab'  => $slug,
					],
					admin_url( 'admin.php' )
				);
				?>
				<a href="<?php echo esc_url( $url ); ?>"
				   class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<form method="POST" action="" class="wpf-settings-form" novalidate>

			<?php wp_nonce_field( THEME_PREFIX_ . 'settings_save', THEME_PREFIX_ . 'settings_nonce' ); ?>
			<input type="hidden" name="action" value="<?php echo esc_attr( THEME_PREFIX_ . 'save_settings' ); ?>">
			<input type="hidden" name="current_tab" value="<?php echo esc_attr( $active_tab ); ?>">

			<?php if ( 'email' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Включить Email-уведомления', 'wp-frame' ); ?></th>
						<td>
							<label class="wpf-toggle">
								<input type="checkbox" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[mail_enabled]' ); ?>" value="1" <?php checked( $v( 'mail_enabled', true ) ); ?>>
								<span class="wpf-toggle__slider"></span>
								<span class="wpf-toggle__label"><?php esc_html_e( 'Отправлять заявки на email', 'wp-frame' ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_to"><?php esc_html_e( 'Email получателя *', 'wp-frame' ); ?></label></th>
						<td>
							<?php wpf_settings_override_notice( $overridden( 'mail_to' ) ); ?>
							<input type="email" id="mail_to" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[mail_to]' ); ?>" value="<?php echo esc_attr( $v( 'mail_to' ) ); ?>" class="regular-text" placeholder="manager@site.ru" <?php disabled( $overridden( 'mail_to' ) ); ?>>
							<p class="description"><?php esc_html_e( 'Куда приходят заявки с форм.', 'wp-frame' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_from"><?php esc_html_e( 'Email отправителя', 'wp-frame' ); ?></label></th>
						<td>
							<input type="email" id="mail_from" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[mail_from]' ); ?>" value="<?php echo esc_attr( $v( 'mail_from' ) ); ?>" class="regular-text" placeholder="noreply@site.ru">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_from_name"><?php esc_html_e( 'Имя отправителя', 'wp-frame' ); ?></label></th>
						<td>
							<input type="text" id="mail_from_name" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[mail_from_name]' ); ?>" value="<?php echo esc_attr( $v( 'mail_from_name' ) ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						</td>
					</tr>
				</table>
				<?php wpf_settings_test_button( 'email', __( 'Отправить тестовое письмо', 'wp-frame' ) ); ?>
			</div>

			<?php elseif ( 'telegram' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Включить Telegram', 'wp-frame' ); ?></th>
						<td>
							<label class="wpf-toggle">
								<input type="checkbox" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[tg_enabled]' ); ?>" value="1" <?php checked( $v( 'tg_enabled' ) ); ?>>
								<span class="wpf-toggle__slider"></span>
								<span class="wpf-toggle__label"><?php esc_html_e( 'Отправлять заявки в Telegram', 'wp-frame' ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="tg_token"><?php esc_html_e( 'Bot Token *', 'wp-frame' ); ?></label></th>
						<td>
							<?php wpf_settings_override_notice( $overridden( 'tg_token' ) ); ?>
							<?php wpf_secret_field( 'tg_token', $v( 'tg_token' ), $overridden( 'tg_token' ), '110201543:AAHdqTcvCH1vGWJxfSeofSzwf...' ); ?>
							<p class="description"><?php esc_html_e( 'Получить у', 'wp-frame' ); ?> <a href="https://t.me/BotFather" target="_blank">@BotFather</a> → /newbot → скопировать токен.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="tg_chat_id"><?php esc_html_e( 'Chat ID *', 'wp-frame' ); ?></label></th>
						<td>
							<?php wpf_settings_override_notice( $overridden( 'tg_chat_id' ) ); ?>
							<input type="text" id="tg_chat_id" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[tg_chat_id]' ); ?>" value="<?php echo esc_attr( $v( 'tg_chat_id' ) ); ?>" class="regular-text" placeholder="-1001234567890" <?php disabled( $overridden( 'tg_chat_id' ) ); ?>>
						</td>
					</tr>
				</table>
				<?php wpf_settings_test_button( 'telegram', __( 'Отправить тестовое сообщение', 'wp-frame' ) ); ?>
			</div>

			<?php elseif ( 'bitrix' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Включить Bitrix24', 'wp-frame' ); ?></th>
						<td>
							<label class="wpf-toggle">
								<input type="checkbox" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[b24_enabled]' ); ?>" value="1" <?php checked( $v( 'b24_enabled' ) ); ?>>
								<span class="wpf-toggle__slider"></span>
								<span class="wpf-toggle__label"><?php esc_html_e( 'Создавать лиды в Bitrix24', 'wp-frame' ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="b24_webhook"><?php esc_html_e( 'Webhook URL *', 'wp-frame' ); ?></label></th>
						<td>
							<?php wpf_settings_override_notice( $overridden( 'b24_webhook' ) ); ?>
							<?php wpf_secret_field( 'b24_webhook', $v( 'b24_webhook' ), $overridden( 'b24_webhook' ), 'https://your.bitrix24.ru/rest/1/xxxxxxxx/' ); ?>
							<p class="description"><?php esc_html_e( 'CRM → Разработчикам → Входящие вебхуки → Добавить.', 'wp-frame' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="b24_source"><?php esc_html_e( 'Источник лида', 'wp-frame' ); ?></label></th>
						<td>
							<select id="b24_source" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[b24_source]' ); ?>" class="regular-text">
								<?php
								$sources = [
									'WEB'     => __( 'Сайт (WEB)', 'wp-frame' ),
									'CALL'    => __( 'Звонок (CALL)', 'wp-frame' ),
									'EMAIL'   => __( 'Письмо (EMAIL)', 'wp-frame' ),
									'PARTNER' => __( 'Партнер (PARTNER)', 'wp-frame' ),
									'OTHER'   => __( 'Другое (OTHER)', 'wp-frame' ),
								];
								$current = $v( 'b24_source', 'WEB' );
								foreach ( $sources as $val => $label ) :
									?>
									<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $current, $val ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<?php wpf_settings_test_button( 'bitrix', __( 'Создать тестовый лид', 'wp-frame' ) ); ?>
			</div>

			<?php elseif ( 'cookies' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Показывать баннер', 'wp-frame' ); ?></th>
						<td>
							<label class="wpf-toggle">
								<input type="checkbox" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[cookie_enabled]' ); ?>" value="1" <?php checked( $v( 'cookie_enabled', true ) ); ?>>
								<span class="wpf-toggle__slider"></span>
								<span class="wpf-toggle__label"><?php esc_html_e( 'Отображать cookie-баннер на сайте', 'wp-frame' ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cookie_text"><?php esc_html_e( 'Текст баннера', 'wp-frame' ); ?></label></th>
						<td>
							<textarea id="cookie_text" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[cookie_text]' ); ?>" rows="3" class="large-text" placeholder="Мы используем cookies, продолжая оставаться на сайте, вы соглашаетесь с {policy} их использования."><?php echo esc_textarea( $v( 'cookie_text' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Используйте токен {policy} чтобы вставить ссылку на политику конфиденциальности.', 'wp-frame' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cookie_policy_url"><?php esc_html_e( 'URL политики', 'wp-frame' ); ?></label></th>
						<td>
							<input type="url" id="cookie_policy_url" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[cookie_policy_url]' ); ?>" value="<?php echo esc_attr( $v( 'cookie_policy_url', '/policy/' ) ); ?>" class="regular-text" placeholder="/policy/">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cookie_policy_anchor"><?php esc_html_e( 'Текст ссылки политики', 'wp-frame' ); ?></label></th>
						<td>
							<input type="text" id="cookie_policy_anchor" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[cookie_policy_anchor]' ); ?>" value="<?php echo esc_attr( $v( 'cookie_policy_anchor', 'политикой' ) ); ?>" class="regular-text" placeholder="политикой">
							<p class="description"><?php esc_html_e( 'Слово, которое будет ссылкой (вместо {policy}).', 'wp-frame' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="cookie_btn_text"><?php esc_html_e( 'Текст кнопки', 'wp-frame' ); ?></label></th>
						<td>
							<input type="text" id="cookie_btn_text" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[cookie_btn_text]' ); ?>" value="<?php echo esc_attr( $v( 'cookie_btn_text', 'Понятно' ) ); ?>" class="regular-text" placeholder="Понятно">
						</td>
					</tr>
				</table>
			</div>

			<?php elseif ( 'contacts' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="contact_address"><?php esc_html_e( 'Адрес', 'wp-frame' ); ?></label></th>
						<td>
							<textarea id="contact_address" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[contact_address]' ); ?>" rows="3" class="large-text" placeholder="г. Москва, ул. Ленина, д. 1, оф. 100"><?php echo esc_textarea( $v( 'contact_address' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="contact_phone"><?php esc_html_e( 'Телефон', 'wp-frame' ); ?></label></th>
						<td>
							<input type="text" id="contact_phone" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[contact_phone]' ); ?>" value="<?php echo esc_attr( $v( 'contact_phone' ) ); ?>" class="regular-text" placeholder="+7 (495) 123-45-67">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="contact_email"><?php esc_html_e( 'Email для контактов', 'wp-frame' ); ?></label></th>
						<td>
							<input type="email" id="contact_email" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[contact_email]' ); ?>" value="<?php echo esc_attr( $v( 'contact_email' ) ); ?>" class="regular-text" placeholder="info@site.ru">
							<p class="description"><?php esc_html_e( 'Публичный контактный email (на сайте).', 'wp-frame' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="contact_worktime"><?php esc_html_e( 'Режим работы', 'wp-frame' ); ?></label></th>
						<td>
							<input type="text" id="contact_worktime" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[contact_worktime]' ); ?>" value="<?php echo esc_attr( $v( 'contact_worktime' ) ); ?>" class="regular-text" placeholder="Пн–Пт: 9:00–18:00">
						</td>
					</tr>
				</table>
			</div>

			<?php elseif ( 'social' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<p style="padding: 16px 0 4px; color: #646970;"><?php esc_html_e( 'Вставьте полные URL профилей. Пустые поля не попадают в вывод.', 'wp-frame' ); ?></p>
				<table class="form-table" role="presentation">
					<tr><th scope="row"><label for="social_telegram">Telegram</label></th>
						<td><input type="url" id="social_telegram" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_telegram]' ); ?>" value="<?php echo esc_attr( $v( 'social_telegram' ) ); ?>" class="regular-text" placeholder="https://t.me/username"></td>
					</tr>
					<tr><th scope="row"><label for="social_whatsapp">WhatsApp</label></th>
						<td><input type="text" id="social_whatsapp" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_whatsapp]' ); ?>" value="<?php echo esc_attr( $v( 'social_whatsapp' ) ); ?>" class="regular-text" placeholder="79161234567">
							<p class="description"><?php esc_html_e( 'Только цифры: 79161234567 → wa.me/79161234567', 'wp-frame' ); ?></p></td>
					</tr>
					<tr><th scope="row"><label for="social_vk"><?php esc_html_e( 'ВКонтакте', 'wp-frame' ); ?></label></th>
						<td><input type="url" id="social_vk" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_vk]' ); ?>" value="<?php echo esc_attr( $v( 'social_vk' ) ); ?>" class="regular-text" placeholder="https://vk.com/company"></td>
					</tr>
					<tr><th scope="row"><label for="social_instagram">Instagram</label></th>
						<td><input type="url" id="social_instagram" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_instagram]' ); ?>" value="<?php echo esc_attr( $v( 'social_instagram' ) ); ?>" class="regular-text" placeholder="https://instagram.com/username"></td>
					</tr>
					<tr><th scope="row"><label for="social_youtube">YouTube</label></th>
						<td><input type="url" id="social_youtube" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_youtube]' ); ?>" value="<?php echo esc_attr( $v( 'social_youtube' ) ); ?>" class="regular-text" placeholder="https://youtube.com/@channel"></td>
					</tr>
					<tr><th scope="row"><label for="social_ok"><?php esc_html_e( 'Одноклассники', 'wp-frame' ); ?></label></th>
						<td><input type="url" id="social_ok" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_ok]' ); ?>" value="<?php echo esc_attr( $v( 'social_ok' ) ); ?>" class="regular-text" placeholder="https://ok.ru/group/..."></td>
					</tr>
					<tr><th scope="row"><label for="social_max">Max</label></th>
						<td><input type="url" id="social_max" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_max]' ); ?>" value="<?php echo esc_attr( $v( 'social_max' ) ); ?>" class="regular-text" placeholder="https://max.ru/..."></td>
					</tr>
					<tr><th scope="row"><label for="social_tiktok">TikTok</label></th>
						<td><input type="url" id="social_tiktok" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[social_tiktok]' ); ?>" value="<?php echo esc_attr( $v( 'social_tiktok' ) ); ?>" class="regular-text" placeholder="https://tiktok.com/@username"></td>
					</tr>
				</table>
			</div>

			<?php elseif ( 'scripts' === $active_tab ) : ?>
			<div class="wpf-tab-panel">
				<div class="notice notice-warning inline" style="margin: 16px 0 0">
					<p><?php esc_html_e( 'Код вставляется на все страницы сайта. Вставляйте только проверенный код от доверенных сервисов.', 'wp-frame' ); ?></p>
				</div>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="script_ym"><?php esc_html_e( 'Яндекс Метрика', 'wp-frame' ); ?></label></th>
						<td>
							<p class="description"><?php esc_html_e( 'Вставьте код целиком из Метрики. На сайте он выводится без изменений.', 'wp-frame' ); ?></p>
							<textarea id="script_ym" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[script_ym]' ); ?>" rows="12" class="large-text code wpf-script-code" spellcheck="false" placeholder="<?php echo esc_attr__( 'Весь блок из Метрики: счётчик, script и noscript', 'wp-frame' ); ?>"><?php echo esc_textarea( (string) $v( 'script_ym' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="script_ga"><?php esc_html_e( 'Google Analytics / GTM', 'wp-frame' ); ?></label></th>
						<td>
							<textarea id="script_ga" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[script_ga]' ); ?>" rows="10" class="large-text code wpf-script-code" spellcheck="false" placeholder="<?php echo esc_attr__( 'Код gtag или фрагмент GTM как в интерфейсе Google', 'wp-frame' ); ?>"><?php echo esc_textarea( (string) $v( 'script_ga' ) ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="script_head_custom"><?php esc_html_e( 'Дополнительный <head>-код', 'wp-frame' ); ?></label></th>
						<td>
							<textarea id="script_head_custom" name="<?php echo esc_attr( THEME_PREFIX_ . 'opt[script_head_custom]' ); ?>" rows="8" class="large-text code wpf-script-code" spellcheck="false" placeholder="<?php echo esc_attr__( 'Другие теги в head по инструкции сервиса (Roistat, пиксель и т.д.)', 'wp-frame' ); ?>"><?php echo esc_textarea( (string) $v( 'script_head_custom' ) ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<?php endif; ?>

			<div class="wpf-settings-io" style="margin-top: 2rem; padding: 1rem 0; border-top: 1px solid #c3c4c7;">
				<h2><?php esc_html_e( 'Экспорт / импорт настроек', 'wp-frame' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Экспорт не содержит токенов Telegram и вебхуков Bitrix24. После импорта страница перезагрузится.', 'wp-frame' ); ?></p>
				<p>
					<button type="button" class="button button-secondary" id="wpf-export-btn">
						<?php esc_html_e( 'Экспортировать настройки', 'wp-frame' ); ?>
					</button>
				</p>
				<p>
					<label for="wpf-import-data"><strong><?php esc_html_e( 'JSON для импорта', 'wp-frame' ); ?></strong></label>
				</p>
				<textarea id="wpf-import-data" rows="8" class="large-text code" style="font-family: monospace; font-size: 12px;" placeholder='{"version":"1.0.0","settings":{...}}'></textarea>
				<p>
					<button type="button" class="button button-secondary" id="wpf-import-btn">
						<?php esc_html_e( 'Импортировать', 'wp-frame' ); ?>
					</button>
					<span id="wpf-import-status" style="margin-left: 12px;"></span>
				</p>
			</div>

			<div class="wpf-settings-footer">
				<?php submit_button( __( 'Сохранить настройки', 'wp-frame' ), 'primary', 'submit', false ); ?>
			</div>

		</form>
	</div>
	<?php
}

/**
 * Render a secret (password) field with show/hide toggle.
 *
 * @param string $key         Option key.
 * @param string $value       Current value.
 * @param bool   $disabled    Whether the field is disabled.
 * @param string $placeholder Placeholder text.
 * @return void
 */
function wpf_secret_field( string $key, string $value, bool $disabled, string $placeholder = '' ): void {
	$field_id   = esc_attr( $key );
	$field_name = esc_attr( THEME_PREFIX_ . 'opt[' . $key . ']' );
	$has_value  = ! empty( $value );
	?>
	<div class="wpf-secret-field">
		<input type="password" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="<?php echo $has_value ? '••••••••••••••••' : esc_attr( $placeholder ); ?>" autocomplete="new-password" <?php disabled( $disabled ); ?>>
		<?php if ( ! $disabled ) : ?>
			<button type="button" class="wpf-secret-toggle button button-secondary" data-target="<?php echo esc_attr( $field_id ); ?>" title="<?php esc_attr_e( 'Показать / Скрыть', 'wp-frame' ); ?>">👁</button>
		<?php endif; ?>
		<?php if ( $has_value && ! $disabled ) : ?>
			<span class="wpf-secret-set"><?php esc_html_e( 'Ключ задан', 'wp-frame' ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a test button with result area.
 *
 * @param string $channel Channel slug.
 * @param string $label   Button label.
 * @return void
 */
function wpf_settings_test_button( string $channel, string $label ): void {
	?>
	<div class="wpf-test-block">
		<button type="button" class="wpf-test-btn button" data-channel="<?php echo esc_attr( $channel ); ?>">
			<?php echo esc_html( $label ); ?>
		</button>
		<span class="wpf-test-result" data-channel="<?php echo esc_attr( $channel ); ?>" hidden></span>
	</div>
	<?php
}

/**
 * Render override notice for constant-defined settings.
 *
 * @param bool $is_overridden Whether the setting is overridden.
 * @return void
 */
function wpf_settings_override_notice( bool $is_overridden ): void {
	if ( ! $is_overridden ) {
		return;
	}
	?>
	<p class="wpf-override-notice">
		<?php esc_html_e( 'Значение задано константой в wp-config.php и недоступно для редактирования.', 'wp-frame' ); ?>
	</p>
	<?php
}

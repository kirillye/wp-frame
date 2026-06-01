<?php
/**
 * Form Admin — columns, meta box, sorting, restrictions for Form Entry CPT.
 *
 * @package WP_Frame
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Колонки списка заявок ──────────────────────────────────────────

add_filter( 'manage_wpf_form_entry_posts_columns', 'wpf_entry_columns' );

/**
 * Customize admin columns for form entries.
 *
 * @param array $columns Default columns.
 * @return array
 */
function wpf_entry_columns( array $columns ): array {
	return [
		'cb'                => $columns['cb'],
		'wpf_entry_number'  => __( '№', 'wp-frame' ),
		'wpf_form_id'       => __( 'Форма', 'wp-frame' ),
		'wpf_entry_preview' => __( 'Данные', 'wp-frame' ),
		'wpf_channels'      => __( 'Каналы', 'wp-frame' ),
		'date'              => __( 'Дата', 'wp-frame' ),
	];
}

add_action( 'manage_wpf_form_entry_posts_custom_column', 'wpf_entry_column_content', 10, 2 );

/**
 * Render custom column content.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 * @return void
 */
function wpf_entry_column_content( string $column, int $post_id ): void {
	match ( $column ) {
		'wpf_entry_number' => ( function () use ( $post_id ): void {
			$num = (int) get_post_meta( $post_id, 'wpf_entry_number', true );
			echo '<strong>#' . esc_html( str_pad( (string) $num, 4, '0', STR_PAD_LEFT ) ) . '</strong>';
		} )(),
		'wpf_form_id' => ( function () use ( $post_id ): void {
			echo '<code>' . esc_html( get_post_meta( $post_id, 'wpf_form_id', true ) ) . '</code>';
		} )(),
		'wpf_entry_preview' => ( function () use ( $post_id ): void {
			$raw    = get_post_meta( $post_id, 'wpf_form_fields', true );
			$fields = (array) json_decode( $raw, true );
			$skip   = [ 'wpf_hp', 'submit_token' ];
			$parts  = [];
			$count  = 0;
			foreach ( $fields as $key => $val ) {
				if ( in_array( $key, $skip, true ) ) {
					continue;
				}
				if ( $count >= 3 ) {
					break;
				}
				$parts[] = '<b>' . esc_html( $key ) . ':</b> ' . esc_html( mb_substr( (string) $val, 0, 40 ) );
				$count++;
			}
			echo implode( ' &nbsp;·&nbsp; ', $parts );
		} )(),
		'wpf_channels' => ( function () use ( $post_id ): void {
			$icons  = get_post_meta( $post_id, 'wpf_ch_email', true )    ? '✉️ ' : '✖️ ';
			$icons .= get_post_meta( $post_id, 'wpf_ch_telegram', true ) ? '✈️ ' : '✖️ ';
			$icons .= get_post_meta( $post_id, 'wpf_ch_bitrix', true )   ? '🏢'  : '✖️ ';
			echo esc_html( $icons );
		} )(),
		default => null,
	};
}

// ─── Сортировка по номеру заявки ────────────────────────────────────

add_filter( 'manage_edit-wpf_form_entry_sortable_columns', 'wpf_entry_sortable_columns' );

/**
 * Make entry number column sortable.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function wpf_entry_sortable_columns( array $columns ): array {
	$columns['wpf_entry_number'] = 'wpf_entry_number';
	return $columns;
}

add_action( 'pre_get_posts', 'wpf_entry_orderby' );

/**
 * Apply sorting by entry number meta value.
 *
 * @param WP_Query $query The query object.
 * @return void
 */
function wpf_entry_orderby( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( 'wpf_form_entry' !== $query->get( 'post_type' ) ) {
		return;
	}
	if ( 'wpf_entry_number' !== $query->get( 'orderby' ) ) {
		return;
	}

	$query->set( 'meta_key', 'wpf_entry_number' );
	$query->set( 'orderby', 'meta_value_num' );
}

// ─── Meta Box: данные заявки ────────────────────────────────────────

add_action( 'add_meta_boxes', 'wpf_entry_add_meta_box' );

/**
 * Register the entry data meta box.
 *
 * @return void
 */
function wpf_entry_add_meta_box(): void {
	add_meta_box(
		'wpf_entry_data',
		__( 'Данные заявки', 'wp-frame' ),
		'wpf_entry_render_meta_box',
		'wpf_form_entry',
		'normal',
		'high'
	);
}

/**
 * Render the entry data meta box — universal, reads all stored meta.
 *
 * @param WP_Post $post The post object.
 * @return void
 */
function wpf_entry_render_meta_box( WP_Post $post ): void {
	$number     = (int) get_post_meta( $post->ID, 'wpf_entry_number', true );
	$form_id    = get_post_meta( $post->ID, 'wpf_form_id', true );
	$raw_fields = get_post_meta( $post->ID, 'wpf_form_fields', true );
	$fields     = (array) json_decode( $raw_fields, true );
	$source_url = get_post_meta( $post->ID, 'wpf_source_url', true );
	$user_ip    = get_post_meta( $post->ID, 'wpf_user_ip', true );
	$ch_email   = (bool) get_post_meta( $post->ID, 'wpf_ch_email', true );
	$ch_tg      = (bool) get_post_meta( $post->ID, 'wpf_ch_telegram', true );
	$ch_b24     = (bool) get_post_meta( $post->ID, 'wpf_ch_bitrix', true );
	$b24_lead   = (int) get_post_meta( $post->ID, 'wpf_bitrix_lead_id', true );

	$pad_number = str_pad( (string) $number, 4, '0', STR_PAD_LEFT );

	?>
	<table class="wpf-entry-meta-table widefat fixed striped" style="border-collapse:collapse;">
		<tbody>
			<!-- Шапка: номер + форма -->
			<tr>
				<th style="width:180px;"><?php esc_html_e( '№ заявки', 'wp-frame' ); ?></th>
				<td><strong>#<?php echo esc_html( $pad_number ); ?></strong></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Форма', 'wp-frame' ); ?></th>
				<td><code><?php echo esc_html( $form_id ); ?></code></td>
			</tr>

			<!-- Поля формы (универсальный вывод, системные ключи скрыты) -->
			<?php
			// Системные ключи, которые не нужно показывать как поля формы.
			$skip_keys = [ 'wpf_hp', 'submit_token' ];
			$has_visible = false;
			foreach ( $fields as $key => $value ) {
				if ( ! in_array( $key, $skip_keys, true ) ) {
					$has_visible = true;
					break;
				}
			}
			if ( $has_visible ) : ?>
				<?php foreach ( $fields as $key => $value ) : ?>
					<?php if ( in_array( $key, $skip_keys, true ) ) { continue; } ?>
					<tr>
						<th><?php echo esc_html( $key ); ?></th>
						<td>
							<?php if ( is_array( $value ) ) : ?>
								<?php echo esc_html( implode( ', ', $value ) ); ?>
							<?php else : ?>
								<?php echo esc_html( (string) $value ); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2"><em><?php esc_html_e( 'Нет данных полей формы.', 'wp-frame' ); ?></em></td>
				</tr>
			<?php endif; ?>

			<!-- Служебная информация -->
			<tr>
				<th colspan="2" style="background:#f0f0f1;font-weight:600;">
					<?php esc_html_e( 'Служебная информация', 'wp-frame' ); ?>
				</th>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Страница отправки', 'wp-frame' ); ?></th>
				<td>
					<?php if ( $source_url ) : ?>
						<a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener">
							<?php echo esc_html( $source_url ); ?>
						</a>
					<?php else : ?>
						<em><?php esc_html_e( 'Не указана', 'wp-frame' ); ?></em>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'IP адрес', 'wp-frame' ); ?></th>
				<td><code><?php echo esc_html( $user_ip ); ?></code></td>
			</tr>

			<!-- Каналы -->
			<tr>
				<th colspan="2" style="background:#f0f0f1;font-weight:600;">
					<?php esc_html_e( 'Каналы отправки', 'wp-frame' ); ?>
				</th>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Email', 'wp-frame' ); ?></th>
				<td>
					<span style="color:<?php echo $ch_email ? '#007cba' : '#a00'; ?>;">
						<?php echo $ch_email ? '✔ ' . esc_html__( 'Отправлен', 'wp-frame' ) : '✖ ' . esc_html__( 'Не отправлен', 'wp-frame' ); ?>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Telegram', 'wp-frame' ); ?></th>
				<td>
					<span style="color:<?php echo $ch_tg ? '#007cba' : '#a00'; ?>;">
						<?php echo $ch_tg ? '✔ ' . esc_html__( 'Отправлен', 'wp-frame' ) : '✖ ' . esc_html__( 'Не отправлен', 'wp-frame' ); ?>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Bitrix24', 'wp-frame' ); ?></th>
				<td>
					<span style="color:<?php echo $ch_b24 ? '#007cba' : '#a00'; ?>;">
						<?php echo $ch_b24 ? '✔ ' . esc_html__( 'Лид создан', 'wp-frame' ) : '✖ ' . esc_html__( 'Не создан', 'wp-frame' ); ?>
					</span>
					<?php if ( $b24_lead > 0 ) : ?>
						&nbsp;<code>ID: <?php echo (int) $b24_lead; ?></code>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

// ─── Запретить редактирование заявок вручную ────────────────────────

add_action( 'admin_head', 'wpf_form_entry_admin_head' );

/**
 * Add CSS to disable editing UI for form entries.
 *
 * @return void
 */
function wpf_form_entry_admin_head(): void {
	global $post_type;

	if ( 'wpf_form_entry' !== $post_type ) {
		return;
	}

	echo '<style>
		#submitdiv .submitbox #major-publishing-actions { pointer-events:none; opacity:.5; }
		#titlediv { pointer-events:none; opacity:.7; }
	</style>';
}

// ─── Массовые действия: удалить безвозвратно ─────────────────────────

add_filter( 'bulk_actions-edit-wpf_form_entry', 'wpf_form_entry_bulk_actions' );

/**
 * Add bulk actions for form entries list.
 *
 * @param array $actions Default bulk actions.
 * @return array
 */
function wpf_form_entry_bulk_actions( array $actions ): array {
	$actions['wpf_delete_forever'] = __( 'Удалить безвозвратно', 'wp-frame' );
	return $actions;
}

add_filter( 'handle_bulk_actions-edit-wpf_form_entry', 'wpf_form_entry_handle_bulk_delete', 10, 3 );

/**
 * Permanently delete selected form entries.
 *
 * @param string $redirect_url Redirect URL.
 * @param string $action       Bulk action name.
 * @param array  $post_ids     Selected post IDs.
 * @return string
 */
function wpf_form_entry_handle_bulk_delete( string $redirect_url, string $action, array $post_ids ): string {
	if ( 'wpf_delete_forever' !== $action ) {
		return $redirect_url;
	}

	$deleted = 0;

	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;
		if ( $post_id <= 0 ) {
			continue;
		}
		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			continue;
		}
		if ( wp_delete_post( $post_id, true ) ) {
			++$deleted;
		}
	}

	return add_query_arg( 'wpf_entries_deleted', $deleted, $redirect_url );
}

add_action( 'admin_notices', 'wpf_form_entry_bulk_admin_notices' );

/**
 * Show notice after bulk deletion of form entries.
 *
 * @return void
 */
function wpf_form_entry_bulk_admin_notices(): void {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only query arg from redirect.
	if ( ! isset( $_GET['wpf_entries_deleted'] ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'edit-wpf_form_entry' !== $screen->id ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$count = (int) $_GET['wpf_entries_deleted'];
	if ( $count < 1 ) {
		return;
	}

	printf(
		'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: %d: number of permanently deleted entries */
				__( 'Удалено заявок безвозвратно: %d.', 'wp-frame' ),
				$count
			)
		)
	);
}

/**
 * Theme Settings — admin UI.
 *
 * - Show/hide secret fields (tokens, webhooks)
 * - AJAX test buttons (Email, Telegram, Bitrix24)
 * - Unsaved changes warning
 *
 * @package WP_Frame
 */

document.addEventListener( 'DOMContentLoaded', () => {

	// ── Toggle secret field visibility ─────────────────────────

	document.querySelectorAll( '.wpf-secret-toggle' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const input = document.getElementById( btn.dataset.target );
			if ( ! input ) {
				return;
			}

			if ( input.type === 'password' ) {
				input.type = 'text';
				btn.textContent = '🙈';
			} else {
				input.type = 'password';
				btn.textContent = '👁';
			}
		} );
	} );

	// ── Test buttons ───────────────────────────────────────────

	document.querySelectorAll( '.wpf-test-btn' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', async () => {
			const channel = btn.dataset.channel;
			const result = document.querySelector(
				`.wpf-test-result[data-channel="${channel}"]`
			);
			const origText = btn.textContent;

			btn.disabled = true;
			btn.textContent = wpfAdmin.i18n.testing;

			if ( result ) {
				result.hidden = true;
				result.className = 'wpf-test-result';
			}

			try {
				const fd = new FormData();
				fd.append( 'action', 'wpf_test_' + channel );
				fd.append( 'nonce', wpfAdmin.nonce );

				const res = await fetch( wpfAdmin.ajaxUrl, {
					method: 'POST',
					body: fd,
				} );
				const json = await res.json();

				if ( result ) {
					result.hidden = false;
					result.textContent = json.data
						? json.data
						: ( json.success ? 'OK' : 'Error' );
					result.classList.add(
						json.success ? 'is-success' : 'is-error'
					);
				}
			} catch ( e ) {
				if ( result ) {
					result.hidden = false;
					result.textContent = 'Ошибка соединения';
					result.classList.add( 'is-error' );
				}
			} finally {
				btn.disabled = false;
				btn.textContent = origText;
			}
		} );
	} );

	const exportBtn = document.getElementById( 'wpf-export-btn' );
	if ( exportBtn ) {
		exportBtn.addEventListener( 'click', async () => {
			exportBtn.disabled = true;
			const orig = exportBtn.textContent;
			try {
				const fd = new FormData();
				fd.append( 'action', 'wpf_export_settings' );
				fd.append( 'nonce', wpfAdmin.nonce );
				const res = await fetch( wpfAdmin.ajaxUrl, {
					method: 'POST',
					body: fd,
				} );
				const json = await res.json();
				if ( ! json || ! json.success ) {
					const msg =
						typeof json?.data === 'string'
							? json.data
							: json?.data?.message;
					window.alert( msg ? msg : wpfAdmin.i18n.exportError );
					return;
				}
				const payload = JSON.stringify( json.data, null, '\t' );
				const blob = new Blob( [ payload ], {
					type: 'application/json',
				} );
				const a = document.createElement( 'a' );
				a.href = URL.createObjectURL( blob );
				a.download = 'wpf-settings-export.json';
				a.click();
				URL.revokeObjectURL( a.href );
			} catch ( e ) {
				window.alert( wpfAdmin.i18n.connectionFail );
			} finally {
				exportBtn.disabled = false;
				exportBtn.textContent = orig;
			}
		} );
	}

	const importBtn = document.getElementById( 'wpf-import-btn' );
	const importArea = document.getElementById( 'wpf-import-data' );
	const importStatus = document.getElementById( 'wpf-import-status' );
	if ( importBtn && importArea ) {
		importBtn.addEventListener( 'click', async () => {
			const raw = importArea.value.trim();
			if ( ! raw ) {
				window.alert( wpfAdmin.i18n.importEmpty );
				return;
			}
			importBtn.disabled = true;
			if ( importStatus ) {
				importStatus.textContent = wpfAdmin.i18n.sending;
			}
			try {
				const fd = new FormData();
				fd.append( 'action', 'wpf_import_settings' );
				fd.append( 'nonce', wpfAdmin.nonce );
				fd.append( 'data', raw );
				const res = await fetch( wpfAdmin.ajaxUrl, {
					method: 'POST',
					body: fd,
				} );
				const json = await res.json();
				if ( ! json || ! json.success ) {
					if ( importStatus ) {
						importStatus.textContent = '';
					}
					const msg =
						typeof json?.data === 'string'
							? json.data
							: json?.data?.message;
					window.alert( msg ? msg : wpfAdmin.i18n.importError );
					return;
				}
				if ( importStatus ) {
					const okMsg =
						json.data && json.data.message
							? json.data.message
							: wpfAdmin.i18n.importSuccess;
					importStatus.textContent = okMsg;
				}
				window.setTimeout( () => {
					window.location.reload();
				}, 400 );
			} catch ( e ) {
				if ( importStatus ) {
					importStatus.textContent = '';
				}
				window.alert( wpfAdmin.i18n.connectionFail );
			} finally {
				importBtn.disabled = false;
			}
		} );
	}

	// ── Unsaved changes warning ────────────────────────────────

	let changed = false;

	document.querySelectorAll(
		'.wpf-settings-form input, .wpf-settings-form select'
	).forEach( ( el ) => {
		el.addEventListener( 'change', () => {
			changed = true;
		} );
	} );

	window.addEventListener( 'beforeunload', ( e ) => {
		if ( ! changed ) {
			return;
		}
		e.preventDefault();
		e.returnValue = '';
	} );

	const form = document.querySelector( '.wpf-settings-form' );
	if ( form ) {
		form.addEventListener( 'submit', () => {
			changed = false;
		} );
	}
} );

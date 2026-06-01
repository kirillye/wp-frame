/**
 * Universal AJAX request helper for WordPress admin-ajax.
 *
 * @package WP_Frame
 */

/**
 * Send an AJAX request to WordPress admin-ajax.
 *
 * @param {string} action - The wp_ajax action name.
 * @param {Object} data   - Additional data to send.
 * @returns {Promise<Object>}
 */
async function wpfAjaxRequest(action, data = {}) {
	const formData = new FormData();
	formData.append('action', action);
	formData.append('nonce', wpfData.nonce);

	Object.entries(data).forEach(([key, val]) => {
		formData.append(key, val);
	});

	const response = await fetch(wpfData.ajaxUrl, {
		method: 'POST',
		body: formData,
	});

	if (!response.ok) {
		throw new Error(`HTTP error: ${response.status}`);
	}

	const result = await response.json();

	if (!result.success) {
		throw new Error(result.data?.message ?? 'Server error');
	}

	return result.data;
}

export { wpfAjaxRequest };

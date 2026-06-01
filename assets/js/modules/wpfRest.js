/**
 * REST API helpers for WordPress REST endpoints.
 *
 * @package WP_Frame
 */

/**
 * GET request to the theme REST API.
 *
 * @param {string} endpoint - Path relative to the REST base (e.g. 'courses').
 * @param {Object} params   - Query parameters.
 * @returns {Promise<Object>}
 */
async function wpfRestGet(endpoint, params = {}) {
	const url = new URL(wpfData.restUrl + endpoint);

	Object.entries(params).forEach(([key, val]) => {
		if (val !== '' && val !== null && val !== undefined) {
			url.searchParams.set(key, val);
		}
	});

	const response = await fetch(url.toString(), {
		method: 'GET',
		headers: {
			'X-WP-Nonce': wpfData.nonce,
			'Content-Type': 'application/json',
		},
	});

	if (!response.ok) {
		const err = await response.json().catch(() => ({}));
		throw new Error(err.message ?? `HTTP ${response.status}`);
	}

	return response.json();
}

/**
 * POST request to the theme REST API.
 *
 * @param {string} endpoint - Path relative to the REST base.
 * @param {Object} body     - Request body.
 * @returns {Promise<Object>}
 */
async function wpfRestPost(endpoint, body = {}) {
	const response = await fetch(wpfData.restUrl + endpoint, {
		method: 'POST',
		headers: {
			'X-WP-Nonce': wpfData.nonce,
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(body),
	});

	if (!response.ok) {
		const err = await response.json().catch(() => ({}));
		throw new Error(err.message ?? `HTTP ${response.status}`);
	}

	return response.json();
}

/**
 * Fetch a fresh REST nonce + submit token from the non-cached endpoint.
 * Use before any state-changing POST on pages that may be full-page cached:
 * the nonce/token embedded in the HTML can be stale once the page is cached.
 *
 * @returns {Promise<{nonce: string, submit_token: string}>}
 */
async function wpfGetFreshToken() {
	// GET without Content-Type to avoid a CORS preflight; cache:no-store
	// so the browser never reuses a previous token.
	const response = await fetch(wpfData.restUrl + 'form/token', {
		method: 'GET',
		headers: { Accept: 'application/json' },
		cache: 'no-store',
	});

	if (!response.ok) {
		throw new Error(`HTTP ${response.status}`);
	}

	return response.json();
}

export { wpfRestGet, wpfRestPost, wpfGetFreshToken };

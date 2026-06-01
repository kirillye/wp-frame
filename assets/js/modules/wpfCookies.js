/**
 * Cookie Banner — accept handler with smooth hide.
 *
 * @package WP_Frame
 */

(function () {
	'use strict';

	const banner = document.getElementById('wpf-cookie-banner');
	if (!banner) return;

	const btn = document.getElementById('wpf-cookie-accept');
	if (!btn) return;

	const cookieKey =
		typeof wpfCookieKey === 'string' && wpfCookieKey ? wpfCookieKey : 'wpf_cookies_accepted';

	btn.addEventListener('click', () => {
		document.cookie =
			cookieKey + '=1;max-age=31536000;path=/;SameSite=Lax';

		banner.style.transition = 'opacity 300ms ease, transform 300ms ease';
		banner.style.opacity = '0';
		banner.style.transform = 'translateY(16px)';

		setTimeout(() => banner.remove(), 320);
	});
})();

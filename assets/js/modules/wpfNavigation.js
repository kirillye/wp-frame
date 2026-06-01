/**
 * Navigation — бургер, выезд меню слева, затемнение фона (ES6 module).
 *
 * @package WP_Frame
 */

const wpfNavigation = (() => {
	'use strict';

	const MQ_MOBILE_MAX = '(max-width: 768px)';

	/** @type {HTMLElement|null} */
	let navSlot = null;

	/** @type {HTMLElement|null} */
	let nav = null;

	/** @type {HTMLElement|null} */
	let toggleBtn = null;

	/** @type {HTMLElement|null} */
	let backdropEl = null;

	/** @type {MediaQueryList|null} */
	let mobileMediaQuery = null;

	/** @type {boolean} */
	let isOpen = false;

	/** @type {boolean} */
	let globalListenersAttached = false;

	/**
	 * @returns {boolean}
	 */
	function isMobileNav() {
		return window.matchMedia(MQ_MOBILE_MAX).matches;
	}

	function applyOpenState(open) {
		isOpen = open;

		if (toggleBtn) {
			toggleBtn.setAttribute('aria-expanded', String(open));
		}

		document.body.classList.toggle('wpf-nav-is-open', open);
	}

	function closeMenu() {
		if (!isOpen) {
			return;
		}
		applyOpenState(false);
	}

	function toggleMenu() {
		if (isMobileNav()) {
			applyOpenState(!isOpen);
		}
	}

	/**
	 * @param {KeyboardEvent} event
	 */
	function handleEscape(event) {
		if (event.key !== 'Escape' && event.key !== 'Esc') {
			return;
		}
		closeMenu();
	}

	/**
	 * @param {MouseEvent} event
	 */
	function handleDocumentClick(event) {
		const target = event.target;
		if (!(target instanceof Node)) {
			return;
		}
		if (!navSlot || !toggleBtn) {
			return;
		}
		if (!isOpen) {
			return;
		}
		if (!navSlot.contains(target)) {
			closeMenu();
		}
	}

	function handleViewportChange() {
		if (!isMobileNav()) {
			closeMenu();
		}
	}

	function attachGlobalListenersOnce() {
		if (globalListenersAttached) {
			return;
		}

		document.addEventListener('click', handleDocumentClick);
		document.addEventListener('keydown', handleEscape);

		mobileMediaQuery = window.matchMedia(MQ_MOBILE_MAX);
		mobileMediaQuery.addEventListener('change', handleViewportChange);

		globalListenersAttached = true;
	}

	function bindCloseTriggers(container) {
		if (!container) {
			return;
		}

		const triggers = container.querySelectorAll('[data-wpf-nav-close]');

		triggers.forEach((element) => {
			element.addEventListener('click', (event) => {
				event.preventDefault();
				closeMenu();
			});
		});
	}

	function initHoverSubmenus() {
		if (!nav) {
			return;
		}

		const items = nav.querySelectorAll('.wpf-nav__item');

		items.forEach((item) => {
			item.addEventListener('mouseenter', () => {
				item.classList.add('wpf-nav__item--hover');
			});

			item.addEventListener('mouseleave', () => {
				item.classList.remove('wpf-nav__item--hover');
			});
		});
	}

	/**
	 * Initialize the navigation module.
	 */
	function init() {
		nav = document.querySelector('#site-navigation.wpf-nav');

		toggleBtn = document.querySelector('.wpf-nav__toggle');

		if (!nav || !toggleBtn) {
			return;
		}

		navSlot = toggleBtn.closest('.wpf-header__nav-slot');
		if (!navSlot || !navSlot.contains(nav)) {
			return;
		}

		backdropEl = navSlot.querySelector('.wpf-nav__backdrop[data-wpf-nav-close]');

		closeMenu();

		toggleBtn.addEventListener('click', (event) => {
			event.preventDefault();
			event.stopPropagation();
			toggleMenu();
		});

		attachGlobalListenersOnce();

		bindCloseTriggers(nav);

		if (backdropEl instanceof HTMLElement) {
			backdropEl.addEventListener('click', () => closeMenu());
		}

		initHoverSubmenus();
	}

	/**
	 * Destroy the navigation module (cleanup).
	 */
	function destroy() {
		document.removeEventListener('click', handleDocumentClick);
		document.removeEventListener('keydown', handleEscape);

		if (mobileMediaQuery) {
			mobileMediaQuery.removeEventListener('change', handleViewportChange);
		}

		globalListenersAttached = false;
		mobileMediaQuery = null;

		document.body.classList.remove('wpf-nav-is-open');
	}

	return {
		init,
		destroy,
	};
})();

export { wpfNavigation };

/**
 * Modal module — open/close with data attributes.
 *
 * @package WP_Frame
 */

const wpfModal = (() => {
	'use strict';

	let openModals = 0;

	/**
	 * Open a modal by id.
	 *
	 * @param {HTMLElement} modal - Modal element.
	 */
	function open(modal) {
		modal.hidden = false;
		modal.classList.add('is-open');
		openModals++;

		document.body.style.overflow = 'hidden';

		// Focus on close button or first focusable element.
		const closeBtn = modal.querySelector('[data-modal-close]');
		if (closeBtn) closeBtn.focus();
	}

	/**
	 * Close a modal.
	 *
	 * @param {HTMLElement} modal - Modal element.
	 */
	function close(modal) {
		modal.classList.remove('is-open');
		modal.hidden = true;
		openModals = Math.max(0, openModals - 1);

		if (openModals === 0) {
			document.body.style.overflow = '';
		}
	}

	/**
	 * Initialize modal system.
	 */
	function init() {
		// Open buttons: [data-modal-open="contact"].
		document.querySelectorAll('[data-modal-open]').forEach((btn) => {
			btn.addEventListener('click', () => {
				const id = btn.dataset.modalOpen;
				const modal = document.getElementById('wpf-modal-' + id);
				if (modal) open(modal);
			});
		});

		// Close buttons and overlay: [data-modal-close].
		document.querySelectorAll('[data-modal-close]').forEach((el) => {
			el.addEventListener('click', () => {
				const modal = el.closest('.wpf-modal');
				if (modal) close(modal);
			});
		});

		// Close on Escape.
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				const opened = document.querySelector('.wpf-modal.is-open');
				if (opened) close(opened);
			}
		});
	}

	return { init, open, close };
})();

export { wpfModal };

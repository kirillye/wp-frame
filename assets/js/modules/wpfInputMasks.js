/**
 * Input masks — phone (RF) and email validation.
 *
 * @package WP_Frame
 */

const wpfInputMasks = (() => {
	'use strict';

	// ═══════════════════════════════════════════════════════
	//  PHONE MASK
	// ═══════════════════════════════════════════════════════

	/**
	 * Extract digits, strip country code 7/8, max 10.
	 *
	 * @param {string} value - Raw input value.
	 * @returns {string}
	 */
	function extractDigits(value) {
		let d = value.replace(/\D/g, '');
		if (d[0] === '7' || d[0] === '8') d = d.slice(1);
		return d.slice(0, 10);
	}

	/**
	 * Format 10 digits to "+7 (XXX) XXX-XX-XX".
	 *
	 * @param {string} digits - 0-10 digit string.
	 * @returns {string}
	 */
	function formatPhone(digits) {
		if (!digits.length) return '';

		let r = '+7 (';
		r += digits.slice(0, 3);
		if (digits.length > 3) r += ') ' + digits.slice(3, 6);
		if (digits.length > 6) r += '-' + digits.slice(6, 8);
		if (digits.length > 8) r += '-' + digits.slice(8, 10);
		return r;
	}

	/**
	 * Set error text in the nearest .wpf-form__error element.
	 *
	 * @param {HTMLElement} input   - Input element.
	 * @param {string}      message - Error message.
	 */
	function setFieldError(input, message) {
		const group = input.closest('.wpf-form__group');
		if (!group) return;
		const errEl = group.querySelector('.wpf-form__error');
		if (errEl) errEl.textContent = message;
	}

	/**
	 * Initialize phone mask on an input.
	 *
	 * @param {HTMLInputElement} input - The input element.
	 */
	function initPhone(input) {
		input.placeholder = '+7 (___) ___-__-__';
		input.maxLength = 18;
		input.autocomplete = 'tel';
		input.setAttribute('inputmode', 'tel');

		function cursorToEnd() {
			requestAnimationFrame(() => {
				const len = input.value.length;
				input.setSelectionRange(len, len);
			});
		}

		function apply(digits) {
			input.value = formatPhone(digits);
			cursorToEnd();
		}

		function validate() {
			const digits = extractDigits(input.value);
			if (!digits.length) {
				input.classList.remove('is-valid', 'is-invalid');
				setFieldError(input, '');
				return;
			}
			if (digits.length === 10) {
				input.classList.add('is-valid');
				input.classList.remove('is-invalid');
				setFieldError(input, '');
			} else {
				input.classList.remove('is-valid');
				input.classList.add('is-invalid');
				setFieldError(input, `Введите ещё ${10 - digits.length} цифр`);
			}
		}

		// Keyboard input.
		input.addEventListener('keydown', (e) => {
			if (e.ctrlKey || e.metaKey) return;
			if (['Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) return;

			if (e.key === 'Backspace') {
				e.preventDefault();
				apply(extractDigits(input.value).slice(0, -1));
				return;
			}

			if (e.key === 'Delete') {
				e.preventDefault();
				apply('');
				return;
			}

			if (!/^\d$/.test(e.key)) {
				e.preventDefault();
				return;
			}

			if (extractDigits(input.value).length >= 10) {
				e.preventDefault();
			}
		});

		// Mobile/autofill input.
		input.addEventListener('input', () => {
			apply(extractDigits(input.value));
		});

		// Paste.
		input.addEventListener('paste', (e) => {
			e.preventDefault();
			const pasted = (e.clipboardData || window.clipboardData).getData('text');
			const combined = extractDigits(input.value) + extractDigits(pasted);
			apply(combined.slice(0, 10));
		});

		// Focus / blur.
		input.addEventListener('focus', () => {
			if (!input.value) apply('');
			cursorToEnd();
		});

		input.addEventListener('blur', () => {
			const digits = extractDigits(input.value);
			if (!digits.length) {
				input.value = '';
				input.classList.remove('is-valid', 'is-invalid');
				setFieldError(input, '');
			} else {
				validate();
			}
		});
	}

	// ═══════════════════════════════════════════════════════
	//  EMAIL VALIDATION
	// ═══════════════════════════════════════════════════════

	const EMAIL_RE = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

	function isEmailValid(v) {
		return EMAIL_RE.test(v.trim());
	}

	function getEmailError(value) {
		const v = value.trim();
		if (!v.includes('@')) return 'Не хватает символа @';
		const [local, ...rest] = v.split('@');
		const domain = rest.join('@');
		if (!local) return 'Введите имя перед @';
		if (!domain) return 'Введите домен после @';
		if (!domain.includes('.')) return 'Укажите домен, например: mail.ru';
		if (domain.startsWith('.')) return 'Домен не может начинаться с точки';
		if (domain.endsWith('.')) return 'Домен не может заканчиваться точкой';
		if (/\.{2,}/.test(domain)) return 'Лишние точки в домене';
		return 'Некорректный email';
	}

	/**
	 * Initialize email validation on an input.
	 *
	 * @param {HTMLInputElement} input - The input element.
	 */
	function initEmail(input) {
		input.placeholder = input.placeholder || 'example@mail.ru';
		input.autocomplete = 'email';
		input.spellcheck = false;
		input.setAttribute('inputmode', 'email');
		input.setAttribute('autocapitalize', 'none');

		let blurred = false;

		function validate(force = false) {
			const value = input.value.trim();

			if (!value) {
				input.classList.remove('is-valid', 'is-invalid');
				setFieldError(input, '');
				return;
			}

			if (isEmailValid(value)) {
				input.classList.add('is-valid');
				input.classList.remove('is-invalid');
				setFieldError(input, '');
				return;
			}

			if (force || blurred || value.length >= 8) {
				input.classList.remove('is-valid');
				input.classList.add('is-invalid');
				setFieldError(input, getEmailError(value));
			}
		}

		input.addEventListener('input', () => validate());
		input.addEventListener('blur', () => {
			blurred = true;
			validate(true);
		});
		input.addEventListener('focus', () => {
			if (input.classList.contains('is-invalid')) {
				input.classList.remove('is-invalid');
				setFieldError(input, '');
			}
		});
	}

	// ═══════════════════════════════════════════════════════
	//  PUBLIC API
	// ═══════════════════════════════════════════════════════

	/**
	 * Initialize all masks on the page.
	 */
	function init() {
		document.querySelectorAll('[data-mask="phone"], input[type="tel"]')
			.forEach(initPhone);

		document.querySelectorAll('[data-mask="email"], input[type="email"]')
			.forEach(initEmail);
	}

	return { init, initPhone, initEmail };
})();

export { wpfInputMasks };

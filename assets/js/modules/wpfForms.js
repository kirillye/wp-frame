/**
 * Universal form handler — validates and submits forms via REST API.
 *
 * @package WP_Frame
 */

import { wpfModal } from './wpfModal.js';
import { wpfGetFreshToken } from './wpfRest.js';

/**
 * Collect form field values into a plain object.
 * Uses data-label attribute as key (Russian label), falls back to name attribute.
 *
 * @param {HTMLFormElement} form - The form element.
 * @returns {Object}
 */
function wpfCollectFields(form) {
	const data = {};
	const elements = form.querySelectorAll('[name]');

	elements.forEach((el) => {
		const key = el.dataset.label || el.name;
		if (!key) return;

		if (el.type === 'checkbox') {
			if (!el.checked) return;
			if (Array.isArray(data[key])) {
				data[key].push(el.value);
			} else if (data[key]) {
				data[key] = [data[key], el.value];
			} else {
				data[key] = el.value;
			}
		} else if (el.type === 'radio') {
			if (el.checked) data[key] = el.value;
		} else {
			data[key] = el.value.trim();
		}
	});

	return data;
}

/**
 * Client-side validation — highlights required fields.
 *
 * @param {HTMLFormElement} form - The form element.
 * @returns {boolean}
 */
function wpfValidateForm(form) {
	let valid = true;

	form.querySelectorAll('[required]').forEach((el) => {
		const errorEl = el.closest('.wpf-form__group')
			?.querySelector('.wpf-form__error');
		const empty = el.value.trim() === '';

		// Phone validation (type="tel"): check actual digit count, not just emptiness.
		// Input mask sets "+7 (" format which is never truly empty.
		if (el.type === 'tel') {
			const digits = el.value.replace(/\D/g, '').replace(/^[78]/, '');
			if (!el.value.trim() || digits.length < 10) {
				el.classList.add('is-error');
				if (errorEl) errorEl.textContent = digits.length ? `Введите ещё ${10 - digits.length} цифр` : 'Обязательное поле';
				valid = false;
				return; // continue forEach
			} else {
				el.classList.remove('is-error');
				el.classList.add('is-valid');
				if (errorEl) errorEl.textContent = '';
				return;
			}
		}

		if (empty) {
			el.classList.add('is-error');
			if (errorEl) errorEl.textContent = 'Обязательное поле';
			valid = false;
		} else {
			el.classList.remove('is-error');
			if (errorEl) errorEl.textContent = '';
		}

		// Email validation.
		if (el.type === 'email' && el.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value)) {
			el.classList.add('is-error');
			if (errorEl) errorEl.textContent = 'Некорректный email';
			valid = false;
		}
	});

	return valid;
}

/**
 * Toggle loading state on form.
 *
 * @param {HTMLFormElement} form  - The form element.
 * @param {boolean}         loading - Loading state.
 */
function wpfSetLoading(form, loading) {
	const btn = form.querySelector('[type="submit"]');
	const btnText = form.querySelector('.wpf-form__btn-text');
	const loader = form.querySelector('.wpf-form__btn-loader');

	if (btn) btn.disabled = loading;
	if (btnText) btnText.hidden = loading;
	if (loader) loader.hidden = !loading;
	form.classList.toggle('is-loading', loading);
}

/**
 * Show success: close form modal, open success modal, reset form.
 *
 * @param {HTMLFormElement} form    - The form element.
 * @param {string}          message - Success message from server.
 */
function wpfShowSuccess(form, message) {
	// 1. Close the parent modal that contains the form.
	const formModal = form.closest('.wpf-modal');
	if (formModal) {
		wpfModal.close(formModal);
	}

	// 2. Reset the form.
	form.reset();
	// Remove validation classes.
	form.querySelectorAll('.is-error, .is-valid').forEach((el) => {
		el.classList.remove('is-error', 'is-valid');
	});

	// 3. Open success modal with message.
	const successModal = document.getElementById('wpf-modal-success');
	const successMessage = document.getElementById('wpf-modal-success-message');

	if (successModal) {
		if (successMessage && message) {
			successMessage.textContent = message;
		}
		wpfModal.open(successModal);
	}
}

/**
 * Show global error message.
 *
 * @param {HTMLFormElement} form    - The form element.
 * @param {string}          message - Error message.
 */
function wpfShowError(form, message) {
	const errEl = form.querySelector('.wpf-form__global-error');
	if (errEl) {
		errEl.textContent = message;
		errEl.hidden = false;
	}
}

/**
 * Submit a single form.
 *
 * @param {HTMLFormElement} form - The form element.
 */
async function wpfSubmitForm(form) {
	if (!wpfValidateForm(form)) return;

	const formId = form.getAttribute('data-form-id') || 'unknown';
	const fields = wpfCollectFields(form);

	wpfSetLoading(form, true);
	form.querySelector('.wpf-form__global-error')?.toggleAttribute('hidden', true);

	try {
		// Свежие креды из некэшируемого эндпоинта: HTML страницы может быть
		// отдан из full-page cache, где вшитый nonce/токен уже протух.
		const { nonce, submit_token: submitToken } = await wpfGetFreshToken();

		const response = await fetch(wpfData.restUrl + 'form/submit', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				form_id: formId,
				fields,
				nonce,
				submit_token: submitToken,
			}),
		});

		const json = await response.json();

		if (json.success) {
			wpfShowSuccess(form, json.message || '');
		} else {
			wpfShowError(form, json.message || 'Ошибка отправки. Попробуйте позже.');
		}
	} catch (err) {
		console.error('[wpfForms]', err);
		wpfShowError(form, 'Ошибка соединения. Проверьте интернет и попробуйте снова.');
	} finally {
		wpfSetLoading(form, false);
	}
}

/**
 * Initialize form handling.
 */
function wpfFormsInit() {
	// Submit handler for all forms with data-form-id.
	document.querySelectorAll('.wpf-form[data-form-id]').forEach((form) => {
		form.addEventListener('submit', (e) => {
			e.preventDefault();
			wpfSubmitForm(form);
		});
	});

	// Clear errors on input.
	document.querySelectorAll('.wpf-form__input').forEach((el) => {
		el.addEventListener('input', () => {
			el.classList.remove('is-error');
			const errEl = el.closest('.wpf-form__group')
				?.querySelector('.wpf-form__error');
			if (errEl) errEl.textContent = '';
		});
	});
}

export { wpfFormsInit };

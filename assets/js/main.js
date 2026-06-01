/**
 * Main entry point for theme JavaScript.
 *
 * @package WP_Frame
 */

import { wpfNavigation } from './modules/wpfNavigation.js';
import { wpfFormsInit } from './modules/wpfForms.js';
import { wpfInputMasks } from './modules/wpfInputMasks.js';
import { wpfModal } from './modules/wpfModal.js';

/**
 * Toggle .is-scrolled on the sticky header when page is scrolled.
 */
function initHeaderScroll() {
	const header = document.getElementById( 'masthead' );
	if ( ! header ) return;

	const toggle = () => header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
	toggle();
	window.addEventListener( 'scroll', toggle, { passive: true } );
}

/**
 * Initialize all modules when the DOM is ready.
 */
document.addEventListener('DOMContentLoaded', () => {
	wpfNavigation.init();
	wpfInputMasks.init();
	wpfFormsInit();
	wpfModal.init();
	initHeaderScroll();
});

/**
 * Advanced Smart FAQ – Front-end JavaScript
 *
 * Handles the accordion toggle and optional real-time search filter.
 * No dependencies (vanilla JS). Respects `asfaqSettings` localized data.
 *
 * @package AdvancedSmartFAQ
 * @since   1.0.0
 */

( function () {
	'use strict';

	/**
	 * Settings from wp_localize_script (with safe defaults).
	 */
	var settings = window.asfaqSettings || {};
	var ANIMATION_SPEED = parseInt( settings.animationSpeed, 10 ) || 300;
	var ENABLE_SEARCH   = settings.enableSearch !== undefined ? settings.enableSearch : true;

	/**
	 * Initialize when DOM is ready.
	 */
	document.addEventListener( 'DOMContentLoaded', function () {
		initAccordions();

		if ( ENABLE_SEARCH ) {
			initSearch();
		}
	} );

	/* =====================================================================
	   Accordion
	   ===================================================================== */

	/**
	 * Set up accordion toggle for all FAQ containers on the page.
	 */
	function initAccordions() {
		var containers = document.querySelectorAll( '.asfaq-container' );

		containers.forEach( function ( container ) {
			var buttons = container.querySelectorAll( '.asfaq-question' );

			buttons.forEach( function ( button ) {
				button.addEventListener( 'click', function () {
					toggleItem( button, container );
				} );

				// Allow keyboard activation (Enter / Space are handled natively by <button>).
			} );
		} );
	}

	/**
	 * Toggle a single FAQ item open or closed.
	 *
	 * @param {HTMLElement} button    The question button.
	 * @param {HTMLElement} container The FAQ container.
	 */
	function toggleItem( button, container ) {
		var isExpanded = button.getAttribute( 'aria-expanded' ) === 'true';
		var answerId   = button.getAttribute( 'aria-controls' );
		var answer     = document.getElementById( answerId );
		var item       = button.closest( '.asfaq-item' );

		if ( ! answer ) {
			return;
		}

		if ( isExpanded ) {
			// Close this item.
			closeAnswer( button, answer, item );
		} else {
			// Close all other items in the same container first.
			var openButtons = container.querySelectorAll( '.asfaq-question[aria-expanded="true"]' );
			openButtons.forEach( function ( openBtn ) {
				var openAnswerId = openBtn.getAttribute( 'aria-controls' );
				var openAnswer   = document.getElementById( openAnswerId );
				var openItem     = openBtn.closest( '.asfaq-item' );
				closeAnswer( openBtn, openAnswer, openItem );
			} );

			// Open the clicked item.
			openAnswer( button, answer, item );
		}
	}

	/**
	 * Open an answer panel with smooth animation.
	 *
	 * @param {HTMLElement} button The question button.
	 * @param {HTMLElement} answer The answer panel.
	 * @param {HTMLElement} item   The FAQ item wrapper.
	 */
	function openAnswer( button, answer, item ) {
		button.setAttribute( 'aria-expanded', 'true' );
		answer.removeAttribute( 'hidden' );
		answer.classList.add( 'asfaq-open' );
		item.classList.add( 'asfaq-active' );

		// Animate max-height.
		answer.style.maxHeight = '0';

		// Force reflow so the browser registers the starting max-height.
		void answer.offsetHeight;

		answer.style.transition = 'max-height ' + ANIMATION_SPEED + 'ms ease';
		answer.style.maxHeight  = answer.scrollHeight + 'px';

		// After animation, remove inline max-height so the panel can resize naturally.
		setTimeout( function () {
			answer.style.maxHeight = 'none';
		}, ANIMATION_SPEED );
	}

	/**
	 * Close an answer panel with smooth animation.
	 *
	 * @param {HTMLElement} button The question button.
	 * @param {HTMLElement} answer The answer panel.
	 * @param {HTMLElement} item   The FAQ item wrapper.
	 */
	function closeAnswer( button, answer, item ) {
		button.setAttribute( 'aria-expanded', 'false' );
		item.classList.remove( 'asfaq-active' );

		// Set explicit height first so transition has a starting value.
		answer.style.maxHeight  = answer.scrollHeight + 'px';
		void answer.offsetHeight;
		answer.style.transition = 'max-height ' + ANIMATION_SPEED + 'ms ease';
		answer.style.maxHeight  = '0';

		setTimeout( function () {
			answer.setAttribute( 'hidden', '' );
			answer.classList.remove( 'asfaq-open' );
			answer.style.maxHeight  = '';
			answer.style.transition = '';
		}, ANIMATION_SPEED );
	}

	/* =====================================================================
	   Search / Filter
	   ===================================================================== */

	/**
	 * Initialize real-time search filtering for all FAQ containers.
	 */
	function initSearch() {
		var inputs = document.querySelectorAll( '.asfaq-search-input' );

		inputs.forEach( function ( input ) {
			var container   = input.closest( '.asfaq-container' );
			var noResults   = container ? container.querySelector( '.asfaq-no-search-results' ) : null;
			var debounceTimer;

			input.addEventListener( 'input', function () {
				clearTimeout( debounceTimer );
				debounceTimer = setTimeout( function () {
					filterFAQs( input, container, noResults );
				}, 150 );
			} );
		} );
	}

	/**
	 * Filter FAQ items based on search input.
	 *
	 * @param {HTMLInputElement} input     The search input.
	 * @param {HTMLElement}      container The FAQ container.
	 * @param {HTMLElement|null} noResults The "no results" message element.
	 */
	function filterFAQs( input, container, noResults ) {
		if ( ! container ) {
			return;
		}

		var query = input.value.toLowerCase().trim();
		var items = container.querySelectorAll( '.asfaq-item' );
		var visibleCount = 0;

		items.forEach( function ( item ) {
			var question = ( item.getAttribute( 'data-question' ) || '' );
			var answer   = ( item.getAttribute( 'data-answer' ) || '' );

			if ( query === '' || question.indexOf( query ) !== -1 || answer.indexOf( query ) !== -1 ) {
				item.classList.remove( 'asfaq-hidden' );
				visibleCount++;
			} else {
				item.classList.add( 'asfaq-hidden' );
			}
		} );

		// Toggle "no results" message.
		if ( noResults ) {
			if ( visibleCount === 0 && query !== '' ) {
				noResults.removeAttribute( 'hidden' );
			} else {
				noResults.setAttribute( 'hidden', '' );
			}
		}
	}

} )();

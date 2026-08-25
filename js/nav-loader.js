/**
 * Show the theme's existing loading overlay (".loading-overlay", the
 * bouncing-dots animation used on initial page load) whenever a visitor
 * clicks a link that navigates to another page - not just on first load.
 *
 * Reuses Porto's own loading-overlay.js plugin (registered as the
 * "porto-loading-overlay" script) and its custom events:
 *   $('body').trigger('loading-overlay:show')
 *   $('body').trigger('loading-overlay:hide')
 * so no new markup/CSS is introduced - same animation everywhere.
 */
jQuery(function ($) {
	'use strict';

	var $body = $('body');

	// Make sure body has an initialized loading overlay even on pages
	// where the theme's "Loading Overlay" option is off (loadingOverlay()
	// is a no-op if already initialized, see loading-overlay.js).
	$body.loadingOverlay();

	var currentHostname = window.location.hostname;

	function isPlainLeftClick(e) {
		return e.button === 0 && !e.metaKey && !e.ctrlKey && !e.shiftKey && !e.altKey;
	}

	$(document).on('click', 'a[href]', function (e) {
		if (!isPlainLeftClick(e)) {
			return;
		}

		var $link = $(this);
		var href = $link.attr('href');

		if (
			!href ||
			href.charAt(0) === '#' ||
			href.indexOf('javascript:') === 0 ||
			href.indexOf('mailto:') === 0 ||
			href.indexOf('tel:') === 0 ||
			href.indexOf('sms:') === 0 ||
			$link.attr('target') === '_blank' ||
			$link.is('[download]') ||
			$link.hasClass('no-loading-overlay') ||
			$link.closest('.no-loading-overlay').length
		) {
			return;
		}

		// Skip links that AJAX-intercept their own click instead of
		// navigating (WooCommerce ajax add-to-cart, cart item remove,
		// wishlist, compare, quick view, etc. - these use real hrefs as
		// a no-JS fallback but are actually handled entirely via JS).
		// Missing this was the cause of a real bug: removing a cart item
		// (an <a class="remove" ... data-product_id="..."> in
		// woocommerce/cart/cart-v2.php and mini-cart.php) was showing
		// the full-screen loader for the full 8s safety timeout below,
		// since WooCommerce's own AJAX removal never navigates away.
		if (
			$link.hasClass('ajax_add_to_cart') ||
			$link.hasClass('add_to_wishlist') ||
			$link.hasClass('add_to_compare') ||
			$link.hasClass('open-quick-view') ||
			$link.hasClass('remove') ||
			$link.is('[data-toggle], [data-bs-toggle], [data-product_id], [data-cart_item_key], [role="button"]')
		) {
			return;
		}

		// External links / different host: browser is leaving the site,
		// no point showing our overlay for that.
		if (this.hostname && this.hostname !== currentHostname) {
			return;
		}

		$body.trigger('loading-overlay:show');

		// Safety net: if something (an unrelated click handler higher up,
		// a blocked popup, a validation error) stops the navigation from
		// actually happening, don't leave the overlay stuck forever.
		window.setTimeout(function () {
			$body.trigger('loading-overlay:hide');
		}, 8000);
	});

	// If the page is restored from the back/forward cache, make sure the
	// overlay isn't left showing from the click that navigated away.
	$(window).on('pageshow', function (e) {
		var orig = e.originalEvent;
		if (orig && orig.persisted) {
			$body.trigger('loading-overlay:hide');
		}
	});
});

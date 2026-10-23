/* Bali Sands header menu: hover/click/keyboard dropdowns and the mobile drawer. */
( function () {
	'use strict';

	var mobile = window.matchMedia( '(max-width: 1180px)' );

	function init( menu ) {
		if ( menu.bsReady ) {
			return;
		}
		menu.bsReady = true;

		var burger = menu.querySelector( '.bs-burger' );
		var items = Array.prototype.slice.call( menu.querySelectorAll( '.has-panel' ) );
		var closeTimer = null;

		function setOpen( item, open ) {
			item.classList.toggle( 'is-open', open );
			item.querySelector( '.bs-nav__toggle' ).setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open && ! mobile.matches ) {
				// Keep the panel inside the window near the right edge.
				var panel = item.querySelector( '.bs-panel' );
				panel.classList.remove( 'is-flipped' );
				if ( panel.getBoundingClientRect().right > document.documentElement.clientWidth - 8 ) {
					panel.classList.add( 'is-flipped' );
				}
			}
		}

		function closeAll( except ) {
			items.forEach( function ( item ) {
				if ( item !== except ) {
					setOpen( item, false );
				}
			} );
		}

		function setDrawer( open ) {
			menu.classList.toggle( 'is-menu-open', open );
			burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}

		items.forEach( function ( item ) {
			var toggle = item.querySelector( '.bs-nav__toggle' );

			toggle.addEventListener( 'click', function () {
				// A mouse click on a menu that hover already opened keeps it open.
				var open = item.bsHovered ? true : ! item.classList.contains( 'is-open' );
				item.bsHovered = false;
				closeAll( item );
				setOpen( item, open );
			} );

			// Desktop: open on hover, like the design.
			item.addEventListener( 'mouseenter', function () {
				if ( mobile.matches ) {
					return;
				}
				clearTimeout( closeTimer );
				closeAll( item );
				item.bsHovered = ! item.classList.contains( 'is-open' );
				setOpen( item, true );
			} );
			item.addEventListener( 'mouseleave', function () {
				if ( mobile.matches ) {
					return;
				}
				item.bsHovered = false;
				closeTimer = setTimeout( function () {
					setOpen( item, false );
				}, 180 );
			} );

			// Close when focus moves out of the item.
			item.addEventListener( 'focusout', function ( e ) {
				if ( ! mobile.matches && ! item.contains( e.relatedTarget ) ) {
					setOpen( item, false );
				}
			} );
		} );

		burger.addEventListener( 'click', function () {
			setDrawer( ! menu.classList.contains( 'is-menu-open' ) );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key !== 'Escape' ) {
				return;
			}
			var open = menu.querySelector( '.has-panel.is-open' );
			if ( open ) {
				setOpen( open, false );
				open.querySelector( '.bs-nav__toggle' ).focus();
			} else if ( menu.classList.contains( 'is-menu-open' ) ) {
				setDrawer( false );
				burger.focus();
			}
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! menu.contains( e.target ) ) {
				closeAll();
				setDrawer( false );
			}
		} );

		// Following an in-page link (e.g. /#quote) closes the drawer.
		menu.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( 'a' );
			if ( link && link.hash && link.pathname === window.location.pathname ) {
				closeAll();
				setDrawer( false );
			}
		} );

		mobile.addEventListener( 'change', function () {
			closeAll();
			setDrawer( false );
		} );
	}

	function initAll( root ) {
		( root || document ).querySelectorAll( '[data-bs-menu]' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initAll();
		} );
	} else {
		initAll();
	}

	// Elementor editor preview re-renders the widget.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/bs-header-menu.default', function ( $scope ) {
				initAll( $scope[ 0 ] );
			} );
		}
	} );
}() );

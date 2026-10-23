/* Bali Sands header: hover/click/keyboard dropdowns and the mobile drawer. */
( function () {
	'use strict';

	var header = document.getElementById( 'bs-header' );
	if ( ! header ) {
		return;
	}
	var nav = header.querySelector( '.bs-nav' );
	var burger = header.querySelector( '.bs-burger' );
	var items = Array.prototype.slice.call( header.querySelectorAll( '.has-panel' ) );
	var mobile = window.matchMedia( '(max-width: 1180px)' );
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

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key !== 'Escape' ) {
			return;
		}
		var open = header.querySelector( '.has-panel.is-open' );
		if ( open ) {
			setOpen( open, false );
			open.querySelector( '.bs-nav__toggle' ).focus();
		} else if ( header.classList.contains( 'is-menu-open' ) ) {
			setDrawer( false );
			burger.focus();
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( ! header.contains( e.target ) ) {
			closeAll();
			setDrawer( false );
		}
	} );

	function setDrawer( open ) {
		if ( ! burger ) {
			return;
		}
		header.classList.toggle( 'is-menu-open', open );
		burger.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	}

	if ( burger ) {
		burger.addEventListener( 'click', function () {
			setDrawer( ! header.classList.contains( 'is-menu-open' ) );
		} );
	}

	// Following an in-page link (e.g. /#quote) closes the drawer.
	if ( nav ) {
		nav.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( 'a' );
			if ( link && link.hash && link.pathname === window.location.pathname ) {
				closeAll();
				setDrawer( false );
			}
		} );
	}

	mobile.addEventListener( 'change', function () {
		closeAll();
		setDrawer( false );
	} );
}() );

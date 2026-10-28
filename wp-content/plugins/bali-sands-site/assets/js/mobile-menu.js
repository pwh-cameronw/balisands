/**
 * Phone and tablet menu (includes/mobile-menu.php): opened by the header's
 * menu button below 1025px, in place of JetMenu's own dropdown.
 */
( function () {
	var BREAKPOINT = 1024;
	var TOGGLES = '.bs-main-menu .jet-mega-menu-toggle, .bs-mobile-menu-toggle';
	var panel, lastFocus;

	function isMobile() {
		return window.innerWidth <= BREAKPOINT;
	}

	function open() {
		lastFocus = document.activeElement;
		panel.hidden = false;
		document.documentElement.classList.add( 'bs-mm-locked' );
		// Next frame, so the fade-in runs.
		window.requestAnimationFrame( function () {
			panel.classList.add( 'is-open' );
		} );
		// The panel itself takes focus (no ring after a tap); Tab goes on to the close button.
		panel.focus( { preventScroll: true } );
	}

	function close() {
		panel.classList.remove( 'is-open' );
		panel.hidden = true;
		document.documentElement.classList.remove( 'bs-mm-locked' );
		if ( lastFocus && lastFocus.focus ) {
			lastFocus.focus();
		}
	}

	function toggleSection( button ) {
		var list = document.getElementById( button.getAttribute( 'aria-controls' ) );
		var expanded = button.getAttribute( 'aria-expanded' ) === 'true';
		button.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
		list.hidden = expanded;
		button.closest( '.bs-mm__item' ).classList.toggle( 'is-open', ! expanded );
	}

	function start() {
		panel = document.getElementById( 'bs-mobile-menu' );
		if ( ! panel ) {
			return;
		}
		document.documentElement.classList.add( 'bs-mm-ready' );

		// Capture phase, so JetMenu doesn't also open its own dropdown.
		[ 'click', 'touchend' ].forEach( function ( type ) {
			document.addEventListener( type, function ( e ) {
				var toggle = e.target.closest && e.target.closest( TOGGLES );
				if ( ! toggle || ! isMobile() ) {
					return;
				}
				e.preventDefault();
				e.stopImmediatePropagation();
				if ( panel.hidden ) {
					open();
				}
			}, true );
		} );

		panel.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( '.bs-mm__close' ) ) {
				close();
				return;
			}
			var button = e.target.closest( '.bs-mm__toggle' );
			if ( button ) {
				toggleSection( button );
				return;
			}
			// Following a link (e.g. /#quote on the same page) closes the panel.
			if ( e.target.closest( 'a' ) ) {
				close();
			}
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key && ! panel.hidden ) {
				close();
			}
		} );

		// Back to desktop width: put the page back to normal.
		window.addEventListener( 'resize', function () {
			if ( ! isMobile() && ! panel.hidden ) {
				close();
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );

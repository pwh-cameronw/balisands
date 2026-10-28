/**
 * Footer on phones: each link column (.bs-footer-acc) opens and closes from
 * its heading. On wider screens the headings stay ordinary links.
 */
( function () {
	var phone = window.matchMedia( '(max-width: 767px)' );

	function setup( column ) {
		var title = column.querySelector( '.bs-footer-acc__head .elementor-heading-title' );
		if ( ! title ) {
			return;
		}

		function sync() {
			if ( phone.matches ) {
				title.setAttribute( 'role', 'button' );
				title.setAttribute( 'tabindex', '0' );
				title.setAttribute( 'aria-expanded', column.classList.contains( 'is-open' ) ? 'true' : 'false' );
			} else {
				title.removeAttribute( 'role' );
				title.removeAttribute( 'tabindex' );
				title.removeAttribute( 'aria-expanded' );
			}
		}

		function toggle( e ) {
			if ( ! phone.matches ) {
				return;
			}
			e.preventDefault();
			column.classList.toggle( 'is-open' );
			sync();
		}

		title.addEventListener( 'click', toggle );
		title.addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key || ' ' === e.key ) {
				toggle( e );
			}
		} );
		if ( phone.addEventListener ) {
			phone.addEventListener( 'change', sync );
		}
		sync();
	}

	function start() {
		var columns = document.querySelectorAll( '.bs-footer .bs-footer-acc' );
		for ( var i = 0; i < columns.length; i++ ) {
			setup( columns[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );

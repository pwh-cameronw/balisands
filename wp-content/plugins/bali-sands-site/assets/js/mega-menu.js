/**
 * Landscape supplies mega menu: hovering or focusing a category tab
 * (.bs-mega__tab--N) shows its pane (.bs-mega__pane--N).
 */
( function () {
	function index( el, prefix ) {
		var m = el.className.match( new RegExp( prefix + '(\\d+)' ) );
		return m ? m[ 1 ] : null;
	}

	function init( menu ) {
		var tabs = menu.querySelectorAll( '.bs-mega__tab' );
		var panes = menu.querySelectorAll( '.bs-mega__pane' );

		function show( n ) {
			var i;
			for ( i = 0; i < tabs.length; i++ ) {
				tabs[ i ].classList.toggle( 'is-active', index( tabs[ i ], 'bs-mega__tab--' ) === n );
			}
			for ( i = 0; i < panes.length; i++ ) {
				panes[ i ].classList.toggle( 'is-active', index( panes[ i ], 'bs-mega__pane--' ) === n );
			}
		}

		for ( var i = 0; i < tabs.length; i++ ) {
			( function ( tab ) {
				var n = index( tab, 'bs-mega__tab--' );
				tab.addEventListener( 'mouseenter', function () { show( n ); } );
				tab.addEventListener( 'focus', function () { show( n ); } );
			}( tabs[ i ] ) );
		}
	}

	function start() {
		var menus = document.querySelectorAll( '.bs-mega--tabs' );
		for ( var i = 0; i < menus.length; i++ ) {
			init( menus[ i ] );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );

/* Bali Sands widgets: live m³ calculator and the "Open today" badge. */
( function () {
	'use strict';

	function num( value ) {
		var n = parseFloat( String( value ).replace( ',', '.' ) );
		return isFinite( n ) && n > 0 ? n : 0;
	}

	function initCalc( form ) {
		if ( form.dataset.bsReady ) {
			return;
		}
		form.dataset.bsReady = '1';
		var tipper = num( form.dataset.tipper ) || 8;
		var order = form.querySelector( '[data-out="order"]' );
		var loads = form.querySelector( '[data-out="loads"]' );
		// Not form.elements.length: that is the number of controls.
		var field = function ( name ) {
			return num( form.querySelector( '[name="' + name + '"]' ).value );
		};

		function update() {
			var m3 = field( 'length' ) * field( 'width' ) * field( 'depth' ) / 1000;
			var amount = Math.max( 0.5, Math.ceil( m3 * 2 ) / 2 );
			order.textContent = amount.toFixed( 1 ) + ' m³';
			loads.textContent = amount <= tipper ? 'Fits in one tipper load' : Math.ceil( amount / tipper ) + ' tipper loads';
		}
		form.addEventListener( 'input', update );
		update();
	}

	function fmt( minutes ) {
		var h = Math.floor( minutes / 60 ), m = minutes % 60;
		var h12 = h % 12 || 12;
		return h12 + ( m ? ':' + ( m < 10 ? '0' : '' ) + m : '' ) + ( h >= 12 ? 'pm' : 'am' );
	}

	function nowIn( tz ) {
		try {
			var parts = new Intl.DateTimeFormat( 'en-AU', { timeZone: tz, weekday: 'short', hour: 'numeric', minute: 'numeric', hourCycle: 'h23' } ).formatToParts( new Date() );
			var get = function ( type ) {
				for ( var i = 0; i < parts.length; i++ ) {
					if ( parts[ i ].type === type ) {
						return parts[ i ].value;
					}
				}
				return '';
			};
			var day = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ].indexOf( get( 'weekday' ).slice( 0, 3 ) );
			return { day: day, mins: parseInt( get( 'hour' ), 10 ) * 60 + parseInt( get( 'minute' ), 10 ) };
		} catch ( e ) {
			return null;
		}
	}

	function initOpen( el ) {
		var data;
		try {
			data = JSON.parse( el.dataset.bsHours );
		} catch ( e ) {
			return;
		}
		var now = nowIn( data.tz );
		if ( ! now || now.day < 0 ) {
			return;
		}
		var today = data.hours[ now.day ];
		var open = now.mins < today[ 1 ];
		var label = open ?
			'Open today until ' + fmt( today[ 1 ] ) :
			'Closed now · opens ' + fmt( data.hours[ ( now.day + 1 ) % 7 ][ 0 ] ) + ' tomorrow';
		el.classList.toggle( 'is-closed', ! open );
		el.querySelector( '.bs-open__label' ).textContent = label;
	}

	function init( root ) {
		( root || document ).querySelectorAll( '[data-bs-calc]' ).forEach( initCalc );
		( root || document ).querySelectorAll( '[data-bs-hours]' ).forEach( initOpen );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init();
		} );
	} else {
		init();
	}

	// Re-run inside the Elementor editor preview when a widget re-renders.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			[ 'bs-calculator', 'bs-hero' ].forEach( function ( name ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/' + name + '.default', function ( $scope ) {
					init( $scope[ 0 ] );
				} );
			} );
		}
	} );
}() );

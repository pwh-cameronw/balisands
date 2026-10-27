/**
 * [bs_calculator]: cubic metres from length × width × depth, rounded up to the
 * next half cube (minimum 0.5 m³), plus how many tipper loads that is.
 */
( function () {
	'use strict';

	function num( input ) {
		var v = parseFloat( String( input.value ).replace( ',', '.' ) );
		return isFinite( v ) && v > 0 ? v : 0;
	}

	function update( calc ) {
		var get = function ( key ) {
			return calc.querySelector( '[data-calc="' + key + '"]' );
		};
		var tipper = parseFloat( calc.getAttribute( 'data-tipper' ) ) || 8;
		var m3 = num( get( 'length' ) ) * num( get( 'width' ) ) * num( get( 'depth' ) ) / 1000;
		var order = Math.max( 0.5, Math.ceil( m3 * 2 ) / 2 );
		var loads = Math.ceil( order / tipper );

		get( 'order' ).textContent = order.toFixed( 1 ) + ' m³';
		get( 'load' ).textContent = loads <= 1 ? 'Fits in one tipper load' : loads + ' tipper loads';
	}

	function init() {
		var calcs = document.querySelectorAll( '.bs-calc' );
		Array.prototype.forEach.call( calcs, function ( calc ) {
			calc.addEventListener( 'input', function () {
				update( calc );
			} );
			update( calc );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );

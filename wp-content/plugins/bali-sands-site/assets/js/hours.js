/**
 * Opening hours: highlight today's row (.bs-hours__row.bs-day-1 = Monday …
 * bs-day-7 = Sunday) using the yard's time zone, so it is right for visitors
 * anywhere and on cached pages.
 */
( function () {
	function today() {
		try {
			var name = new Intl.DateTimeFormat( 'en-AU', { weekday: 'short', timeZone: 'Australia/Perth' } ).format( new Date() );
			var days = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];
			var i = days.indexOf( name.slice( 0, 3 ) );
			if ( i > -1 ) {
				return i + 1;
			}
		} catch ( e ) {}
		return ( ( new Date().getDay() + 6 ) % 7 ) + 1;
	}

	function mark() {
		var rows = document.querySelectorAll( '.bs-hours__row.bs-day-' + today() );
		for ( var i = 0; i < rows.length; i++ ) {
			rows[ i ].classList.add( 'is-today' );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', mark );
	} else {
		mark();
	}
}() );

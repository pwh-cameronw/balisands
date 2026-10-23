<?php
/**
 * Yard details: phone, address, hours and the strings built from them.
 *
 * Values are edited on the ACF Pro options page (Yard details). ACF stores
 * option fields as `options_{name}` in wp_options, so they are read straight
 * from there and the site still works with the defaults when ACF is absent.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default yard details, taken from the current site.
 *
 * @return array<string,string>
 */
function bs_yard_defaults() {
	return array(
		'bs_phone'          => '(08) 9393 2832',
		'bs_email'          => get_option( 'admin_email' ),
		'bs_street'         => '462 Nicholson Road',
		'bs_locality'       => 'Forrestdale WA 6112',
		'bs_address_short'  => '462 Nicholson Rd, Forrestdale',
		'bs_abn'            => '36 155 602 547',
		'bs_maps_url'       => 'https://www.google.com/maps/search/?api=1&query=462+Nicholson+Road+Forrestdale+WA+6112',
		'bs_timezone'       => 'Australia/Perth',
		'bs_week_open'      => '06:00',
		'bs_week_close'     => '16:30',
		'bs_sun_open'       => '08:00',
		'bs_sun_close'      => '14:00',
		'bs_topbar_left'    => "Forrestdale yard · Open 7 days\nDelivery across Perth metro",
		'bs_topbar_right'   => 'Trailer loading at the yard',
		'bs_footer_blurb'   => 'Landscape supplies and Bali imports from our Forrestdale yard. Open seven days, delivery across Perth.',
		'bs_tipper_m3'      => '8',
	);
}

/**
 * Read one yard detail.
 *
 * @param string $key Field name, e.g. `bs_phone`.
 * @return string
 */
function bs_yard( $key ) {
	$value = get_option( 'options_' . $key, '' );
	if ( '' === $value || false === $value || null === $value ) {
		$defaults = bs_yard_defaults();
		$value    = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}
	return apply_filters( 'bs_yard_value', (string) $value, $key );
}

/**
 * `tel:` link for the yard phone number.
 *
 * @return string
 */
function bs_tel_href() {
	$digits = preg_replace( '/[^0-9+]/', '', bs_yard( 'bs_phone' ) );
	return 'tel:' . $digits;
}

/**
 * Normalise "6:00", "06:00" or ACF's "06:00:00" to minutes after midnight.
 *
 * @param string $time Time string.
 * @return int
 */
function bs_time_to_minutes( $time ) {
	$parts = array_map( 'intval', explode( ':', (string) $time ) );
	return ( $parts[0] ?? 0 ) * 60 + ( $parts[1] ?? 0 );
}

/**
 * Format minutes after midnight as "6am" or "4:30pm".
 *
 * @param int $minutes Minutes after midnight.
 * @return string
 */
function bs_format_minutes( $minutes ) {
	$h      = intdiv( $minutes, 60 );
	$m      = $minutes % 60;
	$suffix = $h >= 12 ? 'pm' : 'am';
	$h12    = $h % 12 ? $h % 12 : 12;
	return $h12 . ( $m ? ':' . str_pad( (string) $m, 2, '0', STR_PAD_LEFT ) : '' ) . $suffix;
}

/**
 * Opening hours per weekday (0 = Sunday), as [open, close] minutes.
 *
 * @return array<int,int[]>
 */
function bs_opening_hours() {
	$week = array( bs_time_to_minutes( bs_yard( 'bs_week_open' ) ), bs_time_to_minutes( bs_yard( 'bs_week_close' ) ) );
	$sun  = array( bs_time_to_minutes( bs_yard( 'bs_sun_open' ) ), bs_time_to_minutes( bs_yard( 'bs_sun_close' ) ) );
	return array( $sun, $week, $week, $week, $week, $week, $week );
}

/**
 * "Mon–Sat 6am–4:30pm · Sun 8am–2pm".
 *
 * @return string
 */
function bs_hours_summary() {
	$h = bs_opening_hours();
	return sprintf(
		'Mon–Sat %s–%s · Sun %s–%s',
		bs_format_minutes( $h[1][0] ),
		bs_format_minutes( $h[1][1] ),
		bs_format_minutes( $h[0][0] ),
		bs_format_minutes( $h[0][1] )
	);
}

/**
 * Hours as two footer lines: "Mon–Sat 6am–4:30pm", "Sun & public hols 8am–2pm".
 *
 * @return string[]
 */
function bs_hours_lines() {
	$h = bs_opening_hours();
	return array(
		'Mon–Sat ' . bs_format_minutes( $h[1][0] ) . '–' . bs_format_minutes( $h[1][1] ),
		'Sun & public hols ' . bs_format_minutes( $h[0][0] ) . '–' . bs_format_minutes( $h[0][1] ),
	);
}

/**
 * Open/closed status right now, in the yard's timezone.
 *
 * The badge is re-checked in the browser (see bs-widgets.js) so cached pages
 * stay correct; this is the first paint.
 *
 * @return array{open:bool,label:string}
 */
function bs_open_status() {
	try {
		$tz = new DateTimeZone( bs_yard( 'bs_timezone' ) );
	} catch ( Exception $e ) {
		$tz = wp_timezone();
	}
	$now   = new DateTime( 'now', $tz );
	$day   = (int) $now->format( 'w' );
	$mins  = (int) $now->format( 'G' ) * 60 + (int) $now->format( 'i' );
	$hours = bs_opening_hours();

	if ( $mins < $hours[ $day ][1] ) {
		return array(
			'open'  => true,
			'label' => 'Open today until ' . bs_format_minutes( $hours[ $day ][1] ),
		);
	}
	$next = $hours[ ( $day + 1 ) % 7 ];
	return array(
		'open'  => false,
		'label' => 'Closed now · opens ' . bs_format_minutes( $next[0] ) . ' tomorrow',
	);
}

/**
 * Hours data for the browser-side open/closed check.
 *
 * @return string JSON.
 */
function bs_hours_json() {
	return wp_json_encode(
		array(
			'tz'    => bs_yard( 'bs_timezone' ),
			'hours' => bs_opening_hours(),
		)
	);
}

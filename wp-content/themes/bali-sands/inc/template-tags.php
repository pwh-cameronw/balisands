<?php
/**
 * Header and footer helpers.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yard detail from the Bali Sands Core plugin, or '' without it.
 *
 * @param string $key Field name.
 * @return string
 */
function bali_sands_yard( $key ) {
	return function_exists( 'bs_yard' ) ? bs_yard( $key ) : '';
}

/**
 * Split a textarea option into trimmed, non-empty lines.
 *
 * @param string $text Text.
 * @return string[]
 */
function bali_sands_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) ) );
}

/**
 * Wavy edge, matching the homepage dividers.
 *
 * @param string $from Colour above.
 * @param string $to   Colour below.
 */
function bali_sands_wave( $from, $to ) {
	printf(
		'<div class="bs-wave" style="background-color:%s" aria-hidden="true"><svg viewBox="0 0 1280 64" preserveAspectRatio="none" focusable="false"><path d="M0,0 H1280 V26 C1110,62 930,60 760,36 C590,12 420,8 260,30 C160,44 70,48 0,38 Z" fill="%s"/></svg></div>',
		esc_attr( $to ),
		esc_attr( $from )
	);
}

/**
 * Footer column: a menu location if one is assigned, else that department's ranges.
 *
 * @param string $location   Menu location.
 * @param string $department Department slug for the fallback.
 */
function bali_sands_footer_links( $location, $department ) {
	$tree = function_exists( 'bs_menu_tree' ) ? bs_menu_tree( $location ) : array();
	echo '<ul class="bs-footer__links">';
	if ( $tree ) {
		foreach ( $tree as $node ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $node['item']->url ), esc_html( $node['item']->title ) );
		}
	} elseif ( function_exists( 'bs_get_ranges' ) ) {
		foreach ( bs_get_ranges( $department ) as $range ) {
			if ( $range['link'] ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( $range['link'] ), esc_html( $range['title'] ) );
			} else {
				printf( '<li>%s</li>', esc_html( $range['title'] ) );
			}
		}
	}
	echo '</ul>';
}

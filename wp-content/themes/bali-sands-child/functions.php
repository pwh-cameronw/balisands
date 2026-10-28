<?php
/**
 * Bali Sands — Hello Elementor child theme.
 *
 * Everything visual is set in Elementor's global colours and fonts. This file
 * only loads the child stylesheet and the Gravity Forms skin.
 *
 * @package BaliSandsChild
 */

defined( 'ABSPATH' ) || exit;

define( 'BALI_SANDS_CHILD_VERSION', '1.2.2' );

add_action( 'wp_enqueue_scripts', 'bali_sands_child_styles', 20 );

/**
 * Child stylesheet and the Gravity Forms skin (after Hello Elementor's own CSS).
 */
function bali_sands_child_styles() {
	wp_enqueue_style(
		'bali-sands-child',
		get_stylesheet_uri(),
		array(),
		bali_sands_child_asset_ver( 'style.css' )
	);

	if ( class_exists( 'GFForms' ) ) {
		wp_enqueue_style(
			'bali-sands-gravity-forms',
			get_stylesheet_directory_uri() . '/assets/css/gravity-forms.css',
			array(),
			bali_sands_child_asset_ver( 'assets/css/gravity-forms.css' )
		);
	}
}

/**
 * Asset version from the file's modified time, so browsers and caches pick up every update.
 *
 * @param string $path Path relative to the theme folder.
 * @return string
 */
function bali_sands_child_asset_ver( $path ) {
	$mtime = @filemtime( get_stylesheet_directory() . '/' . $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	return $mtime ? BALI_SANDS_CHILD_VERSION . '.' . $mtime : BALI_SANDS_CHILD_VERSION;
}

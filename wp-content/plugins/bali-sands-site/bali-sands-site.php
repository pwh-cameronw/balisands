<?php
/**
 * Plugin Name:       Bali Sands Site
 * Description:       Builds the Bali Sands site on Hello Elementor: category pages, menus, Elementor global colours and fonts, the homepage, the header, footer and product page, and the Gravity Forms. Also shows each category page's featured image beside it in the menu.
 * Version:           1.4.5
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Bali Sands
 * License:           GPL-2.0-or-later
 * Text Domain:       bali-sands-site
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

define( 'BSS_VERSION', '1.4.5' );
define( 'BSS_FILE', __FILE__ );
define( 'BSS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BSS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Asset version from the file's modified time, so browsers and caches pick up
 * every update to a stylesheet or script.
 *
 * @param string $path Path relative to the plugin folder.
 * @return string
 */
function bss_asset_ver( $path ) {
	$mtime = @filemtime( BSS_DIR . $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	return $mtime ? BSS_VERSION . '.' . $mtime : BSS_VERSION;
}

require_once BSS_DIR . 'includes/data.php';
require_once BSS_DIR . 'includes/menu-thumbnails.php';
require_once BSS_DIR . 'includes/calculator.php';
require_once BSS_DIR . 'includes/products.php';
require_once BSS_DIR . 'includes/elements.php';
require_once BSS_DIR . 'includes/layout-home.php';
require_once BSS_DIR . 'includes/layout-category.php';
require_once BSS_DIR . 'includes/layout-about.php';
require_once BSS_DIR . 'includes/layout-product.php';
require_once BSS_DIR . 'includes/layout-mega-menu.php';
require_once BSS_DIR . 'includes/layout-theme.php';
require_once BSS_DIR . 'includes/setup.php';

register_activation_hook( __FILE__, 'bss_activate' );

/**
 * Point the admin at the setup screen after activation.
 */
function bss_activate() {
	set_transient( 'bss_setup_notice', 1, WEEK_IN_SECONDS );
	bss_register_products();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

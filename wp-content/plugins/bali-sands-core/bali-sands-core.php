<?php
/**
 * Plugin Name:       Bali Sands Core
 * Description:       Product ranges, yard details, homepage Elementor widgets, the quote form and one-click site setup for Bali Sands.
 * Version:           1.1.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Text Domain:       bali-sands
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

define( 'BS_CORE_VERSION', '1.1.0' );
define( 'BS_CORE_FILE', __FILE__ );
define( 'BS_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BS_CORE_URL', plugin_dir_url( __FILE__ ) );

require BS_CORE_DIR . 'includes/yard.php';
require BS_CORE_DIR . 'includes/post-types.php';
require BS_CORE_DIR . 'includes/fields.php';
require BS_CORE_DIR . 'includes/gravity-forms.php';
require BS_CORE_DIR . 'includes/header-menu.php';
require BS_CORE_DIR . 'includes/elementor.php';
require BS_CORE_DIR . 'includes/setup.php';
require BS_CORE_DIR . 'includes/setup-header.php';

register_activation_hook( __FILE__, 'bs_core_activate' );

/**
 * Register content types and point the admin at the setup screen.
 */
function bs_core_activate() {
	bs_register_post_types();
	set_transient( 'bs_setup_notice', 1, DAY_IN_SECONDS );
}

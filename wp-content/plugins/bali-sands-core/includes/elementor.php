<?php
/**
 * Elementor integration: widget category, widgets and their assets.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

add_action( 'elementor/elements/categories_registered', 'bs_elementor_category' );

/**
 * @param \Elementor\Elements_Manager $manager Elements manager.
 */
function bs_elementor_category( $manager ) {
	$manager->add_category(
		'bali-sands',
		array(
			'title' => __( 'Bali Sands', 'bali-sands' ),
			'icon'  => 'eicon-site-logo',
		)
	);
}

add_action( 'elementor/widgets/register', 'bs_elementor_widgets' );

/**
 * @param \Elementor\Widgets_Manager $manager Widgets manager.
 */
function bs_elementor_widgets( $manager ) {
	require_once BS_CORE_DIR . 'includes/widgets/class-bs-widget-base.php';

	$widgets = array(
		'hero'        => 'BS_Widget_Hero',
		'wave'        => 'BS_Widget_Wave',
		'range-grid'  => 'BS_Widget_Range_Grid',
		'calculator'  => 'BS_Widget_Calculator',
		'media-split' => 'BS_Widget_Media_Split',
		'cards'       => 'BS_Widget_Cards',
		'reviews'     => 'BS_Widget_Reviews',
		'quote'       => 'BS_Widget_Quote',
		'header-menu' => 'BS_Widget_Header_Menu',
	);
	foreach ( $widgets as $file => $class ) {
		require_once BS_CORE_DIR . 'includes/widgets/class-bs-widget-' . $file . '.php';
		$manager->register( new $class() );
	}
}

add_action( 'wp_enqueue_scripts', 'bs_register_widget_assets' );
add_action( 'elementor/frontend/after_register_styles', 'bs_register_widget_assets' );

/**
 * Register (not enqueue) widget assets; widgets pull them in via get_*_depends().
 */
function bs_register_widget_assets() {
	if ( wp_style_is( 'bs-widgets', 'registered' ) ) {
		return;
	}
	wp_register_style( 'bs-widgets', BS_CORE_URL . 'assets/css/widgets.css', array(), BS_CORE_VERSION );
	wp_register_script( 'bs-widgets', BS_CORE_URL . 'assets/js/widgets.js', array(), BS_CORE_VERSION, true );
}

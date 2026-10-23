<?php
/**
 * Bali Sands child theme.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

define( 'BALI_SANDS_THEME_VERSION', '1.0.0' );

require get_stylesheet_directory() . '/inc/template-tags.php';

add_action( 'after_setup_theme', 'bali_sands_setup', 20 );

function bali_sands_setup() {
	register_nav_menus(
		array(
			'primary'         => __( 'Header (dropdown menu)', 'bali-sands' ),
			'footer_supplies' => __( 'Footer: Landscape supplies', 'bali-sands' ),
			'footer_imports'  => __( 'Footer: Bali imports', 'bali-sands' ),
			'legal'           => __( 'Footer: legal links', 'bali-sands' ),
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 132,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}

// The child theme draws its own header and footer, so Hello's are switched off.
add_filter( 'hello_elementor_header_footer', '__return_false' );

add_action( 'wp_enqueue_scripts', 'bali_sands_assets', 20 );

function bali_sands_assets() {
	wp_enqueue_style( 'bali-sands-fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'bali-sands', get_stylesheet_directory_uri() . '/assets/css/site.css', array(), BALI_SANDS_THEME_VERSION );
}

add_filter( 'wp_resource_hints', 'bali_sands_resource_hints', 10, 2 );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Hint type.
 * @return array
 */
function bali_sands_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}

add_action( 'admin_notices', 'bali_sands_plugin_notice' );

function bali_sands_plugin_notice() {
	if ( function_exists( 'bs_yard' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'The Bali Sands theme needs the Bali Sands Core plugin for the yard details, menus and homepage widgets. Activate it under Plugins.', 'bali-sands' ) . '</p></div>';
}

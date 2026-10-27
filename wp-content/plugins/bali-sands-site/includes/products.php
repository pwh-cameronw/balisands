<?php
/**
 * Products: the individual items shown as cards on the category pages.
 *
 * - Post type "Products" (bs_product): title, featured image (the card photo),
 *   the editor (full write-up on the product's own page) and menu order.
 * - ACF fields "Short description" and "Best for" (the card text).
 * - Taxonomy "Product categories" (bs_product_cat), hierarchical, mirroring the
 *   category pages: Landscape supplies › Soils › Garden & Veggie Mixes, etc.
 * - Taxonomy "Sold as" (bs_sold_as): Bulk m³ / Bagged, shown as pills.
 *
 * The cards are a JetEngine listing ("Product card") shown by a Listing Grid on
 * each category page, filtered to that page's product category.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'bss_register_products' );
add_action( 'acf/init', 'bss_register_product_fields' );
add_action( 'wp_enqueue_scripts', 'bss_product_styles' );
add_shortcode( 'bs_product_count', 'bss_product_count_shortcode' );

/**
 * Register the post type and taxonomies.
 */
function bss_register_products() {
	register_post_type(
		'bs_product',
		array(
			'labels'        => array(
				'name'               => __( 'Products', 'bali-sands-site' ),
				'singular_name'      => __( 'Product', 'bali-sands-site' ),
				'add_new_item'       => __( 'Add New Product', 'bali-sands-site' ),
				'edit_item'          => __( 'Edit Product', 'bali-sands-site' ),
				'new_item'           => __( 'New Product', 'bali-sands-site' ),
				'view_item'          => __( 'View Product', 'bali-sands-site' ),
				'search_items'       => __( 'Search Products', 'bali-sands-site' ),
				'not_found'          => __( 'No products found', 'bali-sands-site' ),
				'all_items'          => __( 'All Products', 'bali-sands-site' ),
				'featured_image'     => __( 'Card photo', 'bali-sands-site' ),
				'set_featured_image' => __( 'Set card photo', 'bali-sands-site' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-products',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'products',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'bs_product_cat',
		'bs_product',
		array(
			'labels'            => array(
				'name'          => __( 'Product categories', 'bali-sands-site' ),
				'singular_name' => __( 'Product category', 'bali-sands-site' ),
				'all_items'     => __( 'All product categories', 'bali-sands-site' ),
				'edit_item'     => __( 'Edit product category', 'bali-sands-site' ),
				'add_new_item'  => __( 'Add new product category', 'bali-sands-site' ),
				'parent_item'   => __( 'Parent category', 'bali-sands-site' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug'         => 'product-category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);

	register_taxonomy(
		'bs_sold_as',
		'bs_product',
		array(
			'labels'            => array(
				'name'          => __( 'Sold as', 'bali-sands-site' ),
				'singular_name' => __( 'Sold as', 'bali-sands-site' ),
				'all_items'     => __( 'All options', 'bali-sands-site' ),
				'add_new_item'  => __( 'Add new option', 'bali-sands-site' ),
			),
			'hierarchical'      => true, // Checkboxes in the editor.
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);
}

/**
 * ACF fields for the card text (works with free ACF and ACF Pro).
 */
function bss_register_product_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_bss_product',
			'title'    => __( 'Product card', 'bali-sands-site' ),
			'fields'   => array(
				array(
					'key'          => 'field_bss_short_desc',
					'label'        => __( 'Short description', 'bali-sands-site' ),
					'name'         => 'bs_short_desc',
					'type'         => 'textarea',
					'rows'         => 3,
					'instructions' => __( 'One or two sentences shown on the product card.', 'bali-sands-site' ),
				),
				array(
					'key'          => 'field_bss_best_for',
					'label'        => __( 'Best for', 'bali-sands-site' ),
					'name'         => 'bs_best_for',
					'type'         => 'text',
					'instructions' => __( 'Shown as "Best for: …" on the card, e.g. Garden beds · shrubs · natives', 'bali-sands-site' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'bs_product',
					),
				),
			),
			'position' => 'acf_after_title',
		)
	);
}

/**
 * Product card styles.
 */
function bss_product_styles() {
	wp_enqueue_style( 'bss-products', BSS_URL . 'assets/css/products.css', array(), bss_asset_ver( 'assets/css/products.css' ) );
}

/**
 * [bs_product_count category="soils" noun="soils"] — e.g. "8 soils".
 * Counts published Products in the category and its sub-categories.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bss_product_count_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'category' => '',
			'noun'     => 'products',
		),
		$atts,
		'bs_product_count'
	);
	if ( '' === $atts['category'] ) {
		return '';
	}
	$ids = get_posts(
		array(
			'post_type'      => 'bs_product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'bs_product_cat',
					'field'    => 'slug',
					'terms'    => sanitize_title( $atts['category'] ),
				),
			),
		)
	);
	return esc_html( count( $ids ) . ' ' . $atts['noun'] );
}

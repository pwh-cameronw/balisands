<?php
/**
 * Product ranges: the twelve homepage tiles, split by department.
 *
 * `bs_range` is a plain post type, so JetEngine Listings and Query Builder
 * can use it as-is when the category pages are built.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'bs_register_post_types' );

/**
 * Register the range post type and the department taxonomy.
 */
function bs_register_post_types() {
	register_post_type(
		'bs_range',
		array(
			'labels'              => array(
				'name'          => __( 'Product ranges', 'bali-sands' ),
				'singular_name' => __( 'Product range', 'bali-sands' ),
				'add_new_item'  => __( 'Add product range', 'bali-sands' ),
				'edit_item'     => __( 'Edit product range', 'bali-sands' ),
				'all_items'     => __( 'All ranges', 'bali-sands' ),
				'menu_name'     => __( 'Product ranges', 'bali-sands' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-screenoptions',
			'supports'            => array( 'title', 'excerpt', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'hierarchical'        => false,
		)
	);

	register_taxonomy(
		'bs_department',
		'bs_range',
		array(
			'labels'            => array(
				'name'          => __( 'Departments', 'bali-sands' ),
				'singular_name' => __( 'Department', 'bali-sands' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
		)
	);
}

add_filter( 'enter_title_here', 'bs_range_title_placeholder', 10, 2 );

/**
 * @param string  $text Placeholder.
 * @param WP_Post $post Post being edited.
 * @return string
 */
function bs_range_title_placeholder( $text, $post ) {
	return 'bs_range' === $post->post_type ? __( 'Range name, e.g. Sands', 'bali-sands' ) : $text;
}

add_filter( 'gettext', 'bs_range_excerpt_label', 10, 3 );

/**
 * Call the excerpt box what it is used for on the tiles.
 *
 * @param string $translation Translated text.
 * @param string $text        Original text.
 * @param string $domain      Text domain.
 * @return string
 */
function bs_range_excerpt_label( $translation, $text, $domain ) {
	if ( 'default' === $domain && 'Excerpt' === $text && is_admin() && function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen && 'bs_range' === $screen->post_type ) {
			return __( 'Tile subtitle (e.g. Brickies · paving · white)', 'bali-sands' );
		}
	}
	return $translation;
}

/**
 * Ranges in one department, in menu order.
 *
 * @param string $department Department slug.
 * @param int    $limit      Max ranges, -1 for all.
 * @return array<int,array{title:string,sub:string,image:string,swatch:string,link:string}>
 */
function bs_get_ranges( $department, $limit = -1 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'bs_range',
			'posts_per_page' => $limit,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'bs_department',
					'field'    => 'slug',
					'terms'    => $department,
				),
			),
		)
	);

	$ranges = array();
	foreach ( $posts as $post ) {
		$from     = get_post_meta( $post->ID, 'bs_swatch_from', true );
		$to       = get_post_meta( $post->ID, 'bs_swatch_to', true );
		$ranges[] = array(
			'title'  => get_the_title( $post ),
			'sub'    => $post->post_excerpt,
			'image'  => (string) get_the_post_thumbnail_url( $post, 'medium_large' ),
			'swatch' => ( $from && $to ) ? sprintf( 'linear-gradient(150deg,%s,%s)', $from, $to ) : '',
			'link'   => (string) get_post_meta( $post->ID, 'bs_link', true ),
		);
	}
	return $ranges;
}

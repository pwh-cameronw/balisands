<?php
/**
 * Category page (design 7a, "Landscape supplies › Soils") as Elementor data,
 * plus the JetEngine "Product card" listing it uses.
 *
 * Intro with breadcrumbs → "Pick by the job" → the products (a JetEngine
 * Listing Grid of Products in this category) → a "Not sure how much to
 * order?" band → "Other landscape supplies". As on the homepage, every
 * heading, paragraph and button is its own widget and every section's
 * background is a Global Color.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Product card
 * ---------------------------------------------------------------------- */

/**
 * Card shell shared by the JetEngine listing and the fallback cards.
 * Styles: assets/css/products.css (.bs-product…).
 *
 * @param string $seed  Id seed.
 * @param array  $photo Photo widget.
 * @param array  $body  Body widgets.
 * @return array
 */
function bss_product_shell( $seed, $photo, $body ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'overflow'    => 'hidden',
				'css_classes' => 'bs-card bs-product',
			),
			bss_bg( 'bs_white' ),
			bss_border( 'bs_line', 1, 12 )
		),
		array(
			$photo,
			bss_container(
				$seed . '/body',
				bss_merge(
					array(
						'flex_gap'    => bss_gap( 8 ),
						'css_classes' => 'bs-product__body',
					),
					bss_pad( array( 16, 16, 18, 16 ) )
				),
				$body
			),
		)
	);
}

/**
 * "View details" / "Get a price" row.
 *
 * @param string $seed    Id seed.
 * @param array  $details The "View details" widget.
 * @return array
 */
function bss_product_actions( $seed, $details ) {
	return bss_container(
		$seed . '/actions',
		array(
			'flex_direction' => 'row',
			'flex_gap'       => bss_gap( 8 ),
			'padding'        => bss_box( 12, 0, 0, 0 ),
			'css_classes'    => 'bs-product__actions',
		),
		array(
			$details,
			bss_button(
				$seed . '/price',
				'Get a price',
				home_url( '/#quote' ),
				'outline',
				bss_merge( bss_font( 'bs_button_sm' ), array( 'text_padding' => bss_box( 11, 8 ) ) )
			),
		)
	);
}

/**
 * The JetEngine "Product card" listing item: dynamic widgets reading the
 * current Product (title, featured image, ACF fields, "Sold as" terms, link).
 *
 * @return array
 */
function bss_product_listing_elements() {
	$seed = 'listing/product';
	$card = bss_product_shell(
		$seed,
		bss_widget(
			$seed . '/photo',
			'jet-listing-dynamic-image',
			array(
				'dynamic_image_source' => 'post_thumbnail',
				'dynamic_image_size'   => 'medium_large',
				'linked_image'         => 'yes',
				'image_link_source'    => '_permalink',
				'_css_classes'         => 'bs-product__photo',
			)
		),
		array(
			bss_widget(
				$seed . '/name',
				'jet-listing-dynamic-field',
				array(
					'dynamic_field_source'      => 'object',
					'dynamic_field_post_object' => 'post_title',
					'field_tag'                 => 'h3',
					'_css_classes'              => 'bs-product__name',
				)
			),
			bss_widget(
				$seed . '/desc',
				'jet-listing-dynamic-field',
				array(
					'dynamic_field_source'    => 'meta',
					'dynamic_field_post_meta' => 'bs_short_desc',
					'field_tag'               => 'p',
					'hide_if_empty'           => 'yes',
					'_css_classes'            => 'bs-product__desc',
				)
			),
			bss_container(
				$seed . '/best',
				array(
					'flex_direction' => 'row',
					'flex_wrap'      => 'wrap',
					'flex_gap'       => bss_gap( 4, 0 ),
					'css_classes'    => 'bs-product__best',
				),
				array(
					bss_heading( $seed . '/best-label', 'Best for:', 'span', 'bs_meta', 'secondary', array( '_element_width' => 'auto' ) ),
					bss_widget(
						$seed . '/best-value',
						'jet-listing-dynamic-field',
						array(
							'dynamic_field_source'    => 'meta',
							'dynamic_field_post_meta' => 'bs_best_for',
							'field_tag'               => 'span',
							'hide_if_empty'           => 'yes',
							'_element_width'          => 'auto',
							'_css_classes'            => 'bs-product__best-value',
						)
					),
				)
			),
			bss_widget(
				$seed . '/sold-as',
				'jet-listing-dynamic-terms',
				array(
					'from_tax'        => 'bs_sold_as',
					'show_all_terms'  => 'yes',
					'terms_delimiter' => '',
					'terms_linked'    => '',
					'_css_classes'    => 'bs-product__pills',
				)
			),
			bss_product_actions(
				$seed,
				bss_widget(
					$seed . '/details',
					'jet-listing-dynamic-link',
					array(
						'dynamic_link_source' => '_permalink',
						'link_label'          => 'View details',
						'_css_classes'        => 'bs-product__details',
					)
				)
			),
		)
	);
	return array( $card );
}

/**
 * A fixed (non-JetEngine) product card, used only if JetEngine isn't active.
 *
 * @param string $seed    Id seed.
 * @param array  $product bss_products() row.
 * @return array
 */
function bss_product_card_static( $seed, $product ) {
	$labels = bss_sold_as();
	$id     = bss_product_id( $product[0] );
	$url    = $id ? get_permalink( $id ) : home_url( '/#quote' );

	$pills = array();
	foreach ( $product[3] as $sold ) {
		$pills[] = bss_heading( $seed . '/pill-' . $sold, $labels[ $sold ], 'span', 'bs_pill', 'primary', array( '_element_width' => 'auto', '_css_classes' => 'bs-pill' ) );
	}

	return bss_product_shell(
		$seed,
		bss_image( $seed . '/photo', $id ? (int) get_post_thumbnail_id( $id ) : 0, array( '_css_classes' => 'bs-product__photo' ) ),
		array(
			bss_heading( $seed . '/name', esc_html( $product[0] ), 'h3', 'bs_card_lg', 'bs_ink' ),
			bss_heading( $seed . '/desc', esc_html( $product[1] ), 'p', 'bs_card_text', 'text' ),
			bss_heading( $seed . '/best', 'Best for: ' . esc_html( $product[2] ), 'div', 'bs_meta', 'secondary' ),
			bss_container(
				$seed . '/pills',
				array(
					'flex_direction' => 'row',
					'flex_wrap'      => 'wrap',
					'flex_gap'       => bss_gap( 6 ),
					'css_classes'    => 'bs-product__pills',
				),
				$pills
			),
			bss_product_actions(
				$seed,
				bss_button( $seed . '/details', 'View details', $url, 'green', bss_merge( bss_font( 'bs_button_sm' ), array( 'text_padding' => bss_box( 13, 8 ) ) ) )
			),
		)
	);
}

/**
 * The product cards for a category: a JetEngine Listing Grid when the
 * "Product card" listing exists, otherwise fixed cards.
 *
 * @param string $key Category page key (= product category slug source).
 * @return array
 */
function bss_category_products( $key ) {
	$pages      = bss_pages();
	$slug       = $pages[ $key ][2];
	$listing_id = (int) get_option( 'bss_product_listing_id' );

	if ( $listing_id && class_exists( 'Jet_Engine' ) ) {
		return bss_widget(
			'cat/products-grid',
			'jet-listing-grid',
			array(
				'lisitng_id'           => (string) $listing_id, // JetEngine's own spelling.
				'columns'              => 4,
				'columns_tablet'       => 2,
				'columns_mobile'       => 1,
				'posts_num'            => 48,
				'equal_columns_height' => 'yes',
				'horizontal_gap'       => bss_size( 18 ),
				'vertical_gap'         => bss_size( 18 ),
				'not_found_message'    => 'No products in this category yet.',
				'posts_query'          => array(
					array(
						'_id'                => bss_id( 'cat/products-query' ),
						'type'               => 'tax_query',
						'tax_query_taxonomy' => 'bs_product_cat',
						'tax_query_compare'  => 'IN',
						'tax_query_field'    => 'slug',
						'tax_query_terms'    => $slug,
					),
				),
				'_css_classes'         => 'bs-products',
			)
		);
	}

	$cards = array();
	foreach ( bss_products() as $i => $product ) {
		// In this category or one of its sub-categories.
		if ( $product[4] === $key || ( isset( $pages[ $product[4] ] ) && $pages[ $product[4] ][1] === $key ) ) {
			$cards[] = bss_product_card_static( 'cat/product' . $i, $product );
		}
	}
	return bss_grid( 'cat/products-grid', array( 4, 2, 1 ), 18, $cards );
}

/* -------------------------------------------------------------------------
 * Other sections
 * ---------------------------------------------------------------------- */

/**
 * Small linked card for "Other landscape supplies": image + title.
 *
 * @param string $seed  Id seed.
 * @param string $page  Page key.
 * @param string $title Title.
 * @return array
 */
function bss_mini_card( $seed, $page, $title ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'html_tag'    => 'a',
				'link'        => bss_link( bss_page_url( $page ) ),
				'overflow'    => 'hidden',
				'css_classes' => 'bs-card',
			),
			bss_bg( 'bs_sand' ),
			bss_border( 'bs_line', 1, 12 )
		),
		array(
			bss_image(
				$seed . '/image',
				bss_page_thumb_id( $page ),
				array(
					'width'      => bss_size( 100, '%' ),
					'height'     => bss_size( 90 ),
					'object-fit' => 'cover',
				)
			),
			bss_heading( $seed . '/title', $title, 'h3', 'bs_card', 'bs_ink', array( '_padding' => bss_box( 12, 14 ) ) ),
		)
	);
}

/**
 * The category page elements, top to bottom.
 *
 * @param string $key Page key (a key of bss_category_pages()).
 * @return array
 */
function bss_category_elements( $key ) {
	$all   = bss_category_pages();
	$cat   = $all[ $key ];
	$pages = bss_pages();
	$title = $pages[ $key ][0];
	$group = $cat['group'];

	/* Intro --------------------------------------------------------------- */

	$crumbs = sprintf(
		'<p><a href="%s">Home</a> / <a href="%s">%s</a> / <strong>%s</strong></p>',
		esc_url( home_url( '/' ) ),
		esc_url( bss_page_url( $group ) ),
		esc_html( ucfirst( strtolower( $pages[ $group ][0] ) ) ),
		esc_html( $title )
	);

	$intro = bss_section(
		'cat/intro',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--cat-intro bs-on-dark' ),
			bss_pad( array( 44, 40, 30, 40 ), array( 36, 24, 28, 24 ), array( 28, 16, 24, 16 ) )
		),
		array(
			bss_text( 'cat/crumbs', $crumbs, 'bs_crumb', 'bs_mint', array( '_css_classes' => 'bs-crumbs' ) ),
			bss_container(
				'cat/intro-row',
				array(
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'column',
					'flex_align_items'      => 'center',
					'flex_gap'              => bss_gap( 48, 28 ),
					'margin'                => bss_box( 24, 0, 0, 0 ),
				),
				array(
					bss_column(
						'cat/intro-text',
						52,
						array(
							bss_heading( 'cat/title', esc_html( $title ), 'h1', 'bs_display', 'bs_white' ),
							bss_text( 'cat/lead', '<p>' . esc_html( $cat['intro'][0] ) . '</p>', 'bs_lead', 'bs_white', bss_merge( bss_mt( 18 ), bss_max_width( 520 ) ) ),
							bss_text( 'cat/lead-2', '<p>' . esc_html( $cat['intro'][1] ) . '</p>', 'text', 'bs_on_green', bss_merge( bss_mt( 14 ), bss_max_width( 520 ) ) ),
							bss_button_row(
								'cat/buttons',
								array(
									bss_button( 'cat/btn-price', 'Get a delivered price', home_url( '/#quote' ), 'amber' ),
									bss_button( 'cat/btn-calc', 'How much do I need?', home_url( '/#calculator' ), 'outline-light' ),
								),
								28
							),
						)
					),
					bss_column(
						'cat/photo',
						48,
						array(),
						array(
							'min_height'                => bss_size( 340 ),
							'min_height_mobile'         => bss_size( 240 ),
							'border_radius'             => bss_box( 14 ),
							'overflow'                  => 'hidden',
							'background_background'     => 'gradient',
							'background_color'          => $cat['photo'][0],
							'background_color_b'        => $cat['photo'][1],
							'background_gradient_type'  => 'linear',
							'background_gradient_angle' => bss_size( 150, 'deg' ),
							'background_position'       => 'center center',
							'background_size'           => 'cover',
						)
					),
				)
			),
		)
	);

	/* Pick by the job ----------------------------------------------------- */

	$job_cards = array();
	foreach ( $cat['jobs'] as $i => $job ) {
		$seed        = 'cat/job' . $i;
		$job_cards[] = bss_container(
			$seed,
			bss_merge( bss_pad( array( 18, 18, 20, 18 ) ), bss_bg( 'bs_sand' ), bss_border( 'bs_line', 1, 12 ) ),
			array(
				bss_heading( $seed . '/title', $job[0], 'h3', 'bs_card', 'bs_ink' ),
				bss_heading( $seed . '/text', $job[1], 'p', 'bs_card_text', 'text', bss_mt( 6 ) ),
			)
		);
	}

	$jobs = bss_section(
		'cat/jobs',
		'bs_white',
		bss_merge(
			bss_section_pad( 40, 20, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_eyebrow( 'cat/jobs-eyebrow', 'Not sure which one?', 'secondary' ),
			bss_heading( 'cat/jobs-title', 'Pick by the job', 'h2', 'bs_h2_sm', 'bs_ink', bss_mt( 10 ) ),
			bss_grid( 'cat/jobs-grid', array( 4, 2, 1 ), 14, $job_cards, array( 'margin' => bss_box( 22, 0, 0, 0 ) ) ),
		)
	);

	/* The range ----------------------------------------------------------- */

	$range = bss_section(
		'cat/range',
		'bs_white',
		array_merge(
			array( '_element_id' => 'range' ),
			bss_section_pad( 40, 64, false )
		),
		array(
			bss_split_header(
				'cat/range-header',
				array(
					bss_eyebrow( 'cat/range-eyebrow', 'The range', 'primary' ),
					bss_heading( 'cat/range-title', 'Our ' . strtolower( esc_html( $title ) ), 'h2', 'bs_h2_sm', 'bs_ink', bss_mt( 10 ) ),
				),
				bss_text( 'cat/range-count', '<p>[bs_product_count category="' . esc_attr( $pages[ $key ][2] ) . '" noun="' . esc_attr( $cat['noun'] ) . '"]</p>', 'bs_badge', 'bs_muted' )
			),
			bss_category_products( $key ),
		)
	);

	/* Not sure how much to order? (layout-product.php) ------------------- */

	$cta = bss_order_band( 'cat', 'bs_white' );

	/* Other categories in the same group ----------------------------------- */

	$others = array();
	$cards  = bss_home_cards();
	foreach ( 'supplies' === $group ? $cards['supplies'] : $cards['imports'] as $i => $card ) {
		if ( $card[0] !== $key ) {
			$others[] = bss_mini_card( 'cat/other' . $i, $card[0], $card[1] );
		}
	}

	$other = bss_section(
		'cat/others',
		'bs_white',
		bss_merge(
			// Extra room top and bottom for the waves (in from the band above, out into the footer).
			bss_pad( array( 100, 40, 124, 40 ), array( 92, 24, 108, 24 ), array( 56, 16, 72, 16 ) ),
			bss_wave_top( 'secondary' ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_heading( 'cat/others-title', 'Other ' . strtolower( $pages[ $group ][0] ), 'h2', 'bs_h3', 'bs_ink' ),
			bss_grid( 'cat/others-grid', array( max( 1, count( $others ) ), 3, 2 ), 14, $others, array( 'margin' => bss_box( 20, 0, 0, 0 ) ) ),
		)
	);

	return array( $intro, $jobs, $range, $cta, $other );
}

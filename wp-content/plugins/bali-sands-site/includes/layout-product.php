<?php
/**
 * Product page ("View details") as an Elementor Pro Theme Builder Single
 * template, shown on every Product.
 *
 * Built in the same style as the category page (design 7a): green intro with
 * breadcrumbs, photo, title, card text, "Sold as" pills and buttons → the
 * product's write-up beside an "Ordering" card → the terracotta "Not sure how
 * much to order?" band → "More from this range" (a JetEngine Listing Grid of
 * the other Products in the same category).
 *
 * The product's own details come from Elementor Pro's theme widgets (Post
 * Title, Featured Image, Post Content) and dynamic tags (the ACF fields), so
 * every Product fills the same layout. Breadcrumbs and pills are shortcodes
 * from includes/products.php.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Elementor dynamic tag, as stored in a widget's "__dynamic__" settings.
 *
 * @param string $seed     Id seed.
 * @param string $name     Tag name, e.g. "post-title" or "acf-text".
 * @param array  $settings Tag settings.
 * @return string
 */
function bss_tag( $seed, $name, $settings = array() ) {
	return sprintf(
		'[elementor-tag id="%s" name="%s" settings="%s"]',
		bss_id( 'tag/' . $seed ),
		$name,
		rawurlencode( wp_json_encode( (object) $settings ) )
	);
}

/**
 * Dynamic tag settings for one of the Product's ACF fields.
 *
 * @param string $name  Field name (bs_short_desc, bs_best_for).
 * @param array  $extra Extra tag settings (before, after, fallback).
 * @return array
 */
function bss_acf_tag_settings( $name, $extra = array() ) {
	$keys = array(
		'bs_short_desc' => 'field_bss_short_desc',
		'bs_best_for'   => 'field_bss_best_for',
	);
	// Elementor Pro's ACF tags store the field as "field key:field name".
	return array_merge( array( 'key' => $keys[ $name ] . ':' . $name ), $extra );
}

/**
 * The terracotta "Not sure how much to order?" band (category and product pages).
 *
 * @param string $prefix Id seed prefix ("cat", "product").
 * @param string $above  Global colour id of the section above (for the wave).
 * @return array
 */
function bss_order_band( $prefix, $above ) {
	$biz = bss_business();
	return bss_section(
		$prefix . '/cta',
		'secondary',
		bss_merge(
			array(
				'flex_direction'          => 'row',
				'flex_direction_tablet'   => 'column',
				'flex_align_items'        => 'center',
				'flex_align_items_tablet' => 'flex-start',
				'flex_gap'                => bss_gap( 40, 24 ),
				'css_classes'             => 'bs-section bs-section--' . $prefix . '-cta bs-on-dark',
			),
			bss_section_pad( 24, 44, true ),
			bss_wave_top( $above, true )
		),
		array(
			bss_column(
				$prefix . '/cta-text',
				60,
				array(
					bss_heading( $prefix . '/cta-title', 'Not sure how much to order?', 'h2', 'bs_h2_sm', 'bs_white' ),
					bss_text( $prefix . '/cta-intro', '<p>Work it out with the calculator, or call the yard with the size of the area and we\'ll tell you what you need.</p>', 'text', 'bs_white', bss_merge( bss_mt( 12 ), bss_max_width( 560 ) ) ),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				$prefix . '/cta-buttons',
				40,
				array(
					bss_button( $prefix . '/btn-calculator', 'Use the calculator', home_url( '/#calculator' ), 'white' ),
					bss_button( $prefix . '/btn-call', $biz['phone'], $biz['tel'], 'outline-light' ),
				),
				array(
					'width_tablet'                => bss_size( 100, '%' ),
					'flex_direction'              => 'row',
					'flex_wrap'                   => 'wrap',
					'flex_justify_content'        => 'flex-end',
					'flex_justify_content_tablet' => 'flex-start',
					'flex_gap'                    => bss_gap( 12 ),
				)
			),
		)
	);
}

/**
 * "More from this range": the other Products in this Product's category (JetEngine),
 * or links to the landscape supplies categories without JetEngine.
 *
 * @return array
 */
function bss_product_more() {
	$listing_id = (int) get_option( 'bss_product_listing_id' );

	if ( $listing_id && class_exists( 'Jet_Engine' ) ) {
		$eyebrow = 'Keep browsing';
		$title   = 'More from this range';
		$body    = bss_widget(
			'product/more-grid',
			'jet-listing-grid',
			array(
				'lisitng_id'           => (string) $listing_id, // JetEngine's own spelling.
				'columns'              => 4,
				'columns_tablet'       => 2,
				'columns_mobile'       => 1,
				'posts_num'            => 4,
				'equal_columns_height' => 'yes',
				'horizontal_gap'       => bss_size( 18 ),
				'vertical_gap'         => bss_size( 18 ),
				'hide_widget_if'       => 'empty_query',
				'posts_query'          => array(
					// Same product category as the Product being viewed (JetEngine macro).
					array(
						'_id'                => bss_id( 'product/more-query-cat' ),
						'type'               => 'tax_query',
						'tax_query_taxonomy' => 'bs_product_cat',
						'tax_query_compare'  => 'IN',
						'tax_query_field'    => 'term_id',
						'tax_query_terms'    => '%current_terms|bs_product_cat%',
					),
					// …but not the Product itself.
					array(
						'_id'          => bss_id( 'product/more-query-not' ),
						'type'         => 'posts_params',
						'posts_not_in' => '%current_id%',
					),
				),
				'_css_classes'         => 'bs-products',
			)
		);
	} else {
		$eyebrow = 'Keep browsing';
		$title   = 'Landscape supplies';
		$cards   = array();
		foreach ( bss_home_cards()['supplies'] as $i => $card ) {
			$cards[] = bss_mini_card( 'product/other' . $i, $card[0], $card[1] );
		}
		$body = bss_grid( 'product/others-grid', array( max( 1, count( $cards ) ), 3, 2 ), 14, $cards );
	}

	return bss_section(
		'product/more',
		'bs_white',
		bss_merge(
			// Extra room top and bottom for the waves (in from the band above, out into the footer).
			bss_pad( array( 100, 40, 124, 40 ), array( 92, 24, 108, 24 ), array( 56, 16, 72, 16 ) ),
			bss_wave_top( 'secondary' ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_eyebrow( 'product/more-eyebrow', $eyebrow, 'primary' ),
			bss_heading( 'product/more-title', $title, 'h2', 'bs_h2_sm', 'bs_ink', array( '_margin' => bss_box( 10, 0, 22, 0 ) ) ),
			$body,
		)
	);
}

/**
 * The product page template elements, top to bottom.
 *
 * @return array
 */
function bss_product_page_elements() {
	$biz = bss_business();

	/* Intro: photo, title, card text, pills, buttons ------------------------ */

	$photo = bss_widget(
		'product/photo',
		'theme-post-featured-image',
		array(
			'image_size'   => 'large',
			'_css_classes' => 'bs-product-page__photo',
			'__dynamic__'  => array( 'image' => bss_tag( 'product/photo', 'post-featured-image' ) ),
		)
	);

	$details = array(
		bss_widget(
			'product/title',
			'theme-post-title',
			bss_merge(
				array(
					'header_size' => 'h1',
					'__dynamic__' => array( 'title' => bss_tag( 'product/title', 'post-title' ) ),
				),
				bss_font( 'bs_h1_sm' ),
				bss_color( 'title_color', 'bs_white' )
			)
		),
		bss_heading(
			'product/lead',
			'',
			'p',
			'bs_lead',
			'bs_white',
			bss_merge(
				bss_mt( 16 ),
				array( '__dynamic__' => array( 'title' => bss_tag( 'product/lead', 'acf-text', bss_acf_tag_settings( 'bs_short_desc' ) ) ) )
			)
		),
		bss_heading(
			'product/best',
			'',
			'div',
			'bs_badge',
			'bs_mint',
			bss_merge(
				bss_mt( 14 ),
				array( '__dynamic__' => array( 'title' => bss_tag( 'product/best', 'acf-text', bss_acf_tag_settings( 'bs_best_for', array( 'before' => 'Best for: ' ) ) ) ) )
			)
		),
		bss_widget(
			'product/sold-as',
			'shortcode',
			array(
				'shortcode' => '[bs_product_sold_as]',
				'_margin'   => bss_box( 16, 0, 0, 0 ),
			)
		),
		bss_button_row(
			'product/buttons',
			array(
				bss_button( 'product/btn-price', 'Get a delivered price', home_url( '/#quote' ), 'amber' ),
				bss_button( 'product/btn-calc', 'How much do I need?', home_url( '/#calculator' ), 'outline-light' ),
			),
			28
		),
		bss_text(
			'product/call',
			'<p>Or call the yard on <a href="' . esc_attr( $biz['tel'] ) . '">' . esc_html( $biz['phone'] ) . '</a></p>',
			'bs_badge',
			'bs_on_green',
			bss_mt( 16 )
		),
	);

	$intro = bss_section(
		'product/intro',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--product-intro bs-on-dark' ),
			bss_pad( array( 44, 40, 30, 40 ), array( 36, 24, 28, 24 ), array( 28, 16, 24, 16 ) )
		),
		array(
			bss_text( 'product/crumbs', '<p>[bs_product_crumbs]</p>', 'bs_crumb', 'bs_mint', array( '_css_classes' => 'bs-crumbs' ) ),
			bss_container(
				'product/intro-row',
				array(
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'column',
					'flex_align_items'      => 'center',
					'flex_gap'              => bss_gap( 48, 28 ),
					'margin'                => bss_box( 24, 0, 0, 0 ),
				),
				array(
					bss_column( 'product/intro-photo', 50, array( $photo ) ),
					bss_column( 'product/intro-text', 50, $details ),
				)
			),
		)
	);

	/* The write-up, beside the Ordering card ------------------------------ */

	$ordering = bss_container(
		'product/ordering',
		bss_merge(
			array( 'flex_gap' => bss_gap( 0 ) ),
			bss_pad( array( 24, 24, 26, 24 ) ),
			bss_bg( 'bs_sand' ),
			bss_border( 'bs_line', 1, 12 )
		),
		array(
			bss_heading( 'product/ordering-title', 'Ordering', 'h2', 'bs_card_lg', 'bs_ink' ),
			bss_icon_list(
				'product/ordering-list',
				array(
					array( 'Delivered across Perth metro, 0.5–8m³ per tipper', '', 'fas fa-check' ),
					array( 'Loaded onto your trailer at the yard', '', 'fas fa-check' ),
					array( 'Bagged lines ready to pick up', '', 'fas fa-check' ),
					array( 'Open 7 days: ' . $biz['hours'], '', 'fas fa-check' ),
				),
				bss_merge(
					array(
						'space_between' => bss_size( 10 ),
						'icon_size'     => bss_size( 12 ),
						'_margin'       => bss_box( 14, 0, 0, 0 ),
					),
					bss_color( 'icon_color', 'primary' ),
					bss_color( 'text_color', 'text' ),
					bss_font( 'bs_card_text', 'icon_typography' )
				)
			),
			bss_text( 'product/ordering-address', '<p>' . esc_html( $biz['address'] ) . '</p>', 'bs_card_text', 'bs_muted', bss_mt( 14 ) ),
			bss_container(
				'product/ordering-buttons',
				array(
					'flex_gap' => bss_gap( 10 ),
					'margin'   => bss_box( 20, 0, 0, 0 ),
				),
				array(
					bss_button( 'product/ordering-price', 'Get a price', home_url( '/#quote' ), 'green', array( 'align' => 'justify' ) ),
					bss_button( 'product/ordering-call', 'Call ' . $biz['phone'], $biz['tel'], 'outline', array( 'align' => 'justify' ) ),
				)
			),
		)
	);

	$about = bss_section(
		'product/about',
		'bs_white',
		bss_merge(
			array(
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'flex-start',
				'flex_gap'              => bss_gap( 56, 32 ),
			),
			bss_section_pad( 40, 64, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_column(
				'product/about-text',
				62,
				array(
					bss_eyebrow( 'product/about-eyebrow', 'About this product', 'secondary' ),
					bss_widget(
						'product/content',
						'theme-post-content',
						bss_merge(
							array( '_css_classes' => 'bs-product-page__content' ),
							bss_mt( 14 ),
							bss_color( 'text_color', 'text' ),
							bss_font( 'bs_story' )
						)
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column( 'product/about-side', 38, array( $ordering ), array( 'width_tablet' => bss_size( 100, '%' ) ) ),
		)
	);

	return array( $intro, $about, bss_order_band( 'product', 'bs_white' ), bss_product_more() );
}

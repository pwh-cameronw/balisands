<?php
/**
 * The Landscape supplies mega menu content (a JetMenu "jet-menu" post, built
 * in Elementor), in two panes:
 *
 * - left, on sand: a tab per category from bss_mega_menu() (thumbnail and
 *   name, linking to the category page);
 * - right: the hovered category's title, description, "See all →" and a card
 *   per Product in that category (photo and name, from [bs_mega_products]),
 *   or, while the category has no Products yet, a card per sub-category.
 *
 * Hovering or focusing a tab shows its pane (assets/js/mega-menu.js); without
 * the script the first pane shows. Images are the pages' featured images.
 * Styles: assets/css/menu.css (.bs-mega…).
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bss_mega_menu_script' );

/**
 * Tab switching for the mega menu.
 */
function bss_mega_menu_script() {
	wp_enqueue_script( 'bss-mega-menu', BSS_URL . 'assets/js/mega-menu.js', array(), bss_asset_ver( 'assets/js/mega-menu.js' ), true );
}

/**
 * A small square thumbnail (a page's featured image).
 *
 * @param string $seed Id seed.
 * @param string $page Page key.
 * @param int    $size Width and height (px).
 * @param int    $radius Corner radius (px).
 * @return array
 */
function bss_mega_thumb( $seed, $page, $size, $radius ) {
	return bss_image(
		$seed,
		bss_page_thumb_id( $page ),
		array(
			'image_size'            => 'thumbnail',
			'width'                 => bss_size( $size ),
			'height'                => bss_size( $size ),
			'object-fit'            => 'cover',
			'image_border_radius'   => bss_box( $radius ),
			'_element_width'        => 'initial',
			'_element_custom_width' => bss_size( $size ),
			'_flex_size'            => 'none',
			'_css_classes'          => 'bs-mega__thumb',
		)
	);
}

/**
 * One tab in the left-hand list.
 *
 * @param int   $i      Index.
 * @param array $column bss_mega_menu() row.
 * @return array
 */
function bss_mega_tab( $i, $column ) {
	$seed = 'mega/tab' . $i;
	return bss_container(
		$seed,
		bss_merge(
			array(
				'html_tag'         => 'a',
				'link'             => bss_link( bss_page_url( $column[1] ) ),
				'flex_direction'   => 'row',
				'flex_align_items' => 'center',
				'flex_wrap'        => 'nowrap',
				'flex_gap'         => bss_gap( 14 ),
				// bs-mega__tab--N pairs with bs-mega__pane--N; the first starts active.
				'css_classes'      => 'bs-mega__tab bs-mega__tab--' . $i . ( 0 === $i ? ' is-active' : '' ),
			),
			bss_pad( array( 12, 20 ) )
		),
		array(
			bss_mega_thumb( $seed . '/thumb', $column[1], 30, 6 ),
			bss_heading( $seed . '/label', esc_html( $column[0] ), 'div', 'bs_card', 'bs_ink', array( '_flex_size' => 'grow' ) ),
			bss_heading( $seed . '/arrow', '→', 'div', 'bs_card_lg', 'bs_ink', array( '_element_width' => 'auto', '_css_classes' => 'bs-mega__arrow' ) ),
		)
	);
}

/**
 * One category's pane: title, description, "See all →" and sub-category cards.
 *
 * @param int   $i      Index.
 * @param array $column bss_mega_menu() row.
 * @return array
 */
function bss_mega_pane( $i, $column ) {
	$seed  = 'mega/pane' . $i;
	$pages = bss_pages();

	$cards = array();
	foreach ( $column[2] as $j => $child ) {
		$card_seed = $seed . '/card' . $j;
		$cards[]   = bss_container(
			$card_seed,
			bss_merge(
				array(
					'html_tag'         => 'a',
					'link'             => bss_link( bss_page_url( $child ) ),
					'flex_direction'   => 'row',
					'flex_align_items' => 'center',
					'flex_wrap'        => 'nowrap',
					'flex_gap'         => bss_gap( 14 ),
					'css_classes'      => 'bs-mega__card',
				),
				bss_pad( array( 8 ) ),
				bss_bg( 'bs_white' ),
				bss_border( 'bs_line', 1, 12 )
			),
			array(
				bss_mega_thumb( $card_seed . '/thumb', $child, 52, 8 ),
				// Sentence case, as in the menu design: "Gravel & blue metal".
				bss_heading( $card_seed . '/label', esc_html( ucfirst( strtolower( $pages[ $child ][0] ) ) ), 'div', 'bs_card', 'bs_ink', array( '_flex_size' => 'grow' ) ),
			)
		);
	}

	return bss_container(
		$seed,
		array( 'css_classes' => 'bs-mega__pane bs-mega__pane--' . $i . ( 0 === $i ? ' is-active' : '' ) ),
		array(
			bss_container(
				$seed . '/head',
				array(
					'flex_direction'       => 'row',
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'center',
					'flex_gap'             => bss_gap( 16 ),
				),
				array(
					bss_heading(
						$seed . '/title',
						esc_html( $column[0] ),
						'div',
						'bs_phone',
						'bs_ink',
						array(
							'link'         => bss_link( bss_page_url( $column[1] ) ),
							'_css_classes' => 'bs-mega__title',
						)
					),
					bss_text_link( $seed . '/all', 'See all →', bss_page_url( $column[1] ), array( '_element_width' => 'auto', '_css_classes' => 'bs-mega__all' ) ),
				)
			),
			bss_heading( $seed . '/desc', esc_html( $column[3] ), 'p', 'bs_card_text', 'bs_muted', bss_mt( 6 ) ),
			// The category's Products (live, see bss_mega_products_shortcode()). When it has
			// any, they replace the sub-category cards below (assets/css/menu.css).
			bss_widget(
				$seed . '/products',
				'shortcode',
				array(
					'shortcode'    => '[bs_mega_products category="' . esc_attr( bss_pages()[ $column[1] ][2] ) . '"]',
					'_css_classes' => 'bs-mega__products-widget',
				)
			),
			$cards ? bss_grid( $seed . '/cards', array( 3, 2, 1 ), 12, $cards, array( 'margin' => bss_box( 18, 0, 0, 0 ), 'css_classes' => 'bs-mega__cards' ) ) : null,
		)
	);
}

/**
 * The mega menu content.
 *
 * @return array
 */
function bss_mega_supplies_elements() {
	$tabs  = array();
	$panes = array();
	foreach ( bss_mega_menu() as $i => $column ) {
		$tabs[]  = bss_mega_tab( $i, $column );
		$panes[] = bss_mega_pane( $i, $column );
	}

	return array(
		bss_container(
			'mega/panel',
			bss_merge(
				array(
					'flex_direction'   => 'row',
					'flex_wrap'        => 'nowrap',
					'flex_align_items' => 'stretch',
					'css_classes'      => 'bs-mega bs-mega--tabs',
				),
				bss_bg( 'bs_white' )
			),
			array(
				bss_container(
					'mega/nav',
					bss_merge(
						array(
							'width'         => bss_size( 290 ),
							'flex_size'     => 'none',
							'padding'       => bss_box( 10, 0 ),
							'border_border' => 'solid',
							'border_width'  => bss_box( 0, 1, 0, 0 ),
							'css_classes'   => 'bs-mega__nav',
						),
						bss_color( 'border_color', 'bs_line' ),
						bss_bg( 'bs_sand' )
					),
					$tabs
				),
				bss_container(
					'mega/panes',
					bss_merge(
						array(
							'flex_size'   => 'grow',
							'css_classes' => 'bs-mega__panes',
						),
						bss_pad( array( 24, 28, 28, 28 ) )
					),
					$panes
				),
			),
			false
		),
	);
}

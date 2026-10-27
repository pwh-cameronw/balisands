<?php
/**
 * The Landscape supplies mega menu content (a JetMenu "jet-menu" post, built
 * in Elementor): a column per category from bss_mega_menu() with its photo,
 * title and sub-category links, and a "See all landscape supplies" footer.
 *
 * Images are the category pages' featured images; links go to the pages.
 * The panel's border and shadow are in assets/css/menu.css.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * One mega menu column.
 *
 * @param string $seed   Id seed.
 * @param array  $column bss_mega_menu() row.
 * @return array
 */
function bss_mega_column( $seed, $column ) {
	list( $title, $page, $children ) = $column;
	$pages = bss_pages();

	$links = array();
	foreach ( $children as $child ) {
		$links[] = array( $pages[ $child ][0], bss_page_url( $child ) );
	}

	return bss_container(
		$seed,
		array( 'css_classes' => 'bs-mega__col' ),
		array(
			bss_image(
				$seed . '/image',
				bss_page_thumb_id( $page ),
				array(
					'image_size'          => 'medium',
					'link_to'             => 'custom',
					'link'                => bss_link( bss_page_url( $page ) ),
					'width'               => bss_size( 100, '%' ),
					'height'              => bss_size( 92 ),
					'object-fit'          => 'cover',
					'image_border_radius' => bss_box( 8 ),
					'_css_classes'        => 'bs-mega__image',
				)
			),
			bss_heading(
				$seed . '/title',
				esc_html( $title ),
				'div',
				'bs_card',
				'bs_ink',
				bss_merge(
					array( 'link' => bss_link( bss_page_url( $page ) ) ),
					bss_mt( 12 ),
					bss_color( 'title_hover_color', 'primary' )
				)
			),
			bss_icon_list(
				$seed . '/links',
				$links,
				bss_merge(
					array(
						'space_between' => bss_size( 4 ),
						'_margin'       => bss_box( 8, 0, 0, 0 ),
					),
					bss_color( 'text_color', 'text' ),
					bss_color( 'text_color_hover', 'primary' ),
					bss_font( 'bs_card_text', 'icon_typography' )
				)
			),
		)
	);
}

/**
 * The mega menu content, top to bottom.
 *
 * @return array
 */
function bss_mega_supplies_elements() {
	$columns = array();
	foreach ( bss_mega_menu() as $i => $column ) {
		$columns[] = bss_mega_column( 'mega/col' . $i, $column );
	}

	$foot = bss_container(
		'mega/foot',
		array(
			'flex_direction'       => 'row',
			'flex_justify_content' => 'space-between',
			'flex_align_items'     => 'center',
			'flex_wrap'            => 'wrap',
			'flex_gap'             => bss_gap( 16, 8 ),
			'margin'               => bss_box( 20, 0, 0, 0 ),
			'padding'              => bss_box( 14, 0, 0, 0 ),
			'border_border'        => 'solid',
			'border_width'         => bss_box( 1, 0, 0, 0 ),
			'border_color'         => bss_palette()['bs_line'][1],
			'__globals__'          => array( 'border_color' => 'globals/colors?id=bs_line' ),
		),
		array(
			bss_heading( 'mega/foot-text', 'Delivered across Perth, or loaded onto your trailer at the yard.', 'div', 'bs_card_text', 'bs_muted', array( '_element_width' => 'auto' ) ),
			bss_text_link( 'mega/see-all', 'See all landscape supplies →', bss_page_url( 'supplies' ), array( '_element_width' => 'auto' ) ),
		)
	);

	return array(
		bss_container(
			'mega/panel',
			bss_merge(
				array( 'css_classes' => 'bs-mega' ),
				bss_pad( array( 22, 24, 18, 24 ), null, array( 16, 16, 14, 16 ) ),
				bss_bg( 'bs_white' )
			),
			array(
				bss_grid( 'mega/grid', array( count( $columns ), 3, 2 ), 22, $columns ),
				$foot,
			),
			false
		),
	);
}

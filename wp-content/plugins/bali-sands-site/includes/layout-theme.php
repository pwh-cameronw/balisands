<?php
/**
 * Site header and footer (design 6a) as Elementor Pro Theme Builder templates.
 *
 * Header: green top bar, logo + wordmark, JetMenu "Mega Menu" widget (falls
 * back to Elementor Pro's Nav Menu if JetMenu isn't active) and the "Get a
 * quote" button. Backgrounds run full width; the content is boxed to the site
 * width, so on wide screens it lines up with the page sections below. Footer: four columns and the small print. All separate
 * widgets; backgrounds are Global Colors.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main menu widget: JetMenu Mega Menu when available, otherwise Elementor Pro Nav Menu.
 *
 * @param int $menu_id Menu term id.
 * @return array
 */
function bss_main_menu_widget( $menu_id ) {
	if ( class_exists( 'Jet_Menu' ) ) {
		return bss_widget(
			'header/menu',
			'jet-mega-menu',
			array(
				'menu'         => (string) $menu_id,
				'_css_classes' => 'bs-main-menu',
			)
		);
	}

	$menu = $menu_id ? wp_get_nav_menu_object( $menu_id ) : null;
	return bss_widget(
		'header/menu',
		'nav-menu',
		bss_merge(
			array(
				'menu'                         => $menu ? $menu->slug : '',
				'layout'                       => 'horizontal',
				'pointer'                      => 'none',
				'dropdown'                     => 'tablet',
				'padding_horizontal_menu_item' => bss_size( 10 ),
				'_css_classes'                 => 'bs-main-menu',
			),
			bss_font( 'bs_nav', 'menu_typography' ),
			bss_color( 'color_menu_item', 'bs_ink' ),
			bss_color( 'color_menu_item_hover', 'primary' )
		)
	);
}

/**
 * Header template elements.
 *
 * @param int $menu_id Main menu term id.
 * @return array
 */
function bss_header_elements( $menu_id ) {
	$biz = bss_business();

	$topbar_list = static function ( $seed, $items, $extra = array() ) {
		return bss_icon_list(
			$seed,
			$items,
			bss_merge(
				array(
					'view'           => 'inline',
					'space_between'  => bss_size( 44 ),
					'divider'        => 'yes',
					'divider_style'  => 'solid',
					'divider_weight' => bss_size( 1 ),
					'divider_height' => bss_size( 13 ),
					'divider_color'  => 'rgba(238,242,234,0.5)',
					'_element_width' => 'auto',
				),
				bss_color( 'text_color', 'bs_topbar_text' ),
				bss_color( 'text_color_hover', 'bs_white' ),
				bss_font( 'bs_topbar', 'icon_typography' ),
				$extra
			)
		);
	};

	$topbar = bss_container(
		'header/topbar',
		bss_merge(
			array(
				// Boxed to the site width (Site Settings → Layout), like the page sections and footer.
				'content_width'               => 'boxed',
				'flex_direction'              => 'row',
				'flex_justify_content'        => 'space-between',
				'flex_justify_content_mobile' => 'center',
				'flex_align_items'            => 'center',
				'flex_wrap'                   => 'wrap',
				'flex_gap'                    => bss_gap( 22, 6 ),
			),
			bss_pad( array( 11, 40 ), array( 11, 24 ), array( 9, 16 ) ),
			bss_bg( 'primary' )
		),
		array(
			$topbar_list(
				'header/topbar-left',
				array(
					array( 'Forrestdale yard · Open 7 days' ),
					array( 'Delivery across Perth metro' ),
				),
				array( 'hide_mobile' => 'hidden-mobile' )
			),
			$topbar_list(
				'header/topbar-right',
				array(
					array( 'Trailer loading at the yard' ),
					array( $biz['phone'], $biz['tel'] ),
				)
			),
		)
	);

	$brand = bss_container(
		'header/brand',
		array(
			'html_tag'           => 'a',
			'link'               => bss_link( home_url( '/' ) ),
			'flex_direction'     => 'row',
			'flex_align_items'   => 'center',
			'flex_gap'           => bss_gap( 11 ),
			'width'              => bss_size( 28, '%' ),
			'width_tablet'       => bss_size( 60, '%' ),
			'width_mobile'       => bss_size( 75, '%' ),
			'padding'            => bss_box( 16, 0 ),
			'padding_mobile'     => bss_box( 12, 0 ),
		),
		array(
			bss_image(
				'header/logo',
				bss_media_id( 'logo' ),
				array(
					'image_size'   => 'full',
					'width'        => bss_size( 51 ),
					'width_mobile' => bss_size( 40 ),
					'_element_width' => 'auto',
				)
			),
			bss_heading( 'header/wordmark', 'Bali Sands', 'div', 'bs_brand', 'bs_ink', array( '_element_width' => 'auto' ) ),
		)
	);

	$nav = bss_container(
		'header/nav',
		array(
			'flex_direction'       => 'row',
			'flex_justify_content' => 'flex-end',
			'flex_align_items'     => 'center',
			'flex_gap'             => bss_gap( 6 ),
			'width'                => bss_size( 72, '%' ),
			'width_tablet'         => bss_size( 40, '%' ),
			'width_mobile'         => bss_size( 25, '%' ),
		),
		array(
			bss_main_menu_widget( $menu_id ),
			bss_button(
				'header/quote',
				'Get a quote',
				home_url( '/#quote' ),
				'amber',
				array(
					'text_padding' => bss_box( 13, 20 ),
					'_margin'      => bss_box( 0, 0, 0, 6 ),
					'hide_mobile'  => 'hidden-mobile',
				)
			),
		)
	);

	$bar = bss_container(
		'header/bar',
		bss_merge(
			array(
				'content_width'        => 'boxed',
				'flex_direction'       => 'row',
				'flex_justify_content' => 'space-between',
				'flex_align_items'     => 'center',
				'flex_wrap'            => 'nowrap',
				'z_index'              => 20,
			),
			bss_pad( array( 0, 40 ), array( 0, 24 ), array( 0, 16 ) ),
			bss_bg( 'bs_white' ),
			array(
				'border_border' => 'solid',
				'border_width'  => bss_box( 0, 0, 1, 0 ),
			),
			bss_color( 'border_color', 'bs_line' )
		),
		array( $brand, $nav )
	);

	return array(
		bss_container(
			'header/wrap',
			array(
				'css_classes' => 'bs-header',
				'z_index'     => 50,
			),
			array( $topbar, $bar ),
			false
		),
	);
}

/**
 * Footer template elements.
 *
 * @return array
 */
function bss_footer_elements() {
	$biz   = bss_business();
	$menus = (array) get_option( 'bss_menus', array() );

	$menu_list = static function ( $seed, $menu_name ) use ( $menus ) {
		$menu = ! empty( $menus[ $menu_name ] ) ? wp_get_nav_menu_object( $menus[ $menu_name ] ) : null;
		return bss_widget(
			$seed,
			'nav-menu',
			bss_merge(
				array(
					'menu'                         => $menu ? $menu->slug : '',
					'layout'                       => 'vertical',
					'pointer'                      => 'none',
					'dropdown'                     => 'none',
					'toggle'                       => '',
					'padding_horizontal_menu_item' => bss_size( 0 ),
					'padding_vertical_menu_item'   => bss_size( 0 ),
					'menu_space_between'           => bss_size( 0 ),
					'_margin'                      => bss_box( 12, 0, 0, 0 ),
				),
				bss_font( 'bs_footer', 'menu_typography' ),
				bss_color( 'color_menu_item', 'bs_footer_text' ),
				bss_color( 'color_menu_item_hover', 'bs_white' )
			)
		);
	};

	$column = static function ( $seed, $percent, $elements ) {
		return bss_container(
			$seed,
			array(
				'width'        => bss_size( $percent, '%' ),
				'width_tablet' => bss_size( 46, '%' ),
				'width_mobile' => bss_size( 100, '%' ),
			),
			$elements
		);
	};

	$columns = bss_container(
		'footer/columns',
		array(
			'flex_direction' => 'row',
			'flex_wrap'      => 'nowrap',
			'flex_wrap_tablet' => 'wrap',
			'flex_gap'       => bss_gap( 36, 32 ),
		),
		array(
			$column(
				'footer/col-about',
				31,
				array(
					bss_heading( 'footer/wordmark', 'Bali Sands', 'div', 'bs_brand', 'bs_white', array( 'link' => bss_link( home_url( '/' ) ) ) ),
					bss_text( 'footer/blurb', '<p>Landscape supplies and Bali imports from our Forrestdale yard. Open seven days, delivery across Perth.</p>', 'bs_footer', 'bs_footer_text', bss_merge( bss_mt( 12 ), bss_max_width( 270 ) ) ),
				)
			),
			$column(
				'footer/col-supplies',
				23,
				array(
					bss_heading( 'footer/supplies-title', 'Landscape supplies', 'div', 'bs_footer_head', 'bs_white', array( 'link' => bss_link( bss_page_url( 'supplies' ) ) ) ),
					$menu_list( 'footer/supplies-menu', 'Footer: Landscape supplies' ),
				)
			),
			$column(
				'footer/col-imports',
				23,
				array(
					bss_heading( 'footer/imports-title', 'Bali imports', 'div', 'bs_footer_head', 'bs_white', array( 'link' => bss_link( bss_page_url( 'imports' ) ) ) ),
					$menu_list( 'footer/imports-menu', 'Footer: Bali imports' ),
				)
			),
			$column(
				'footer/col-visit',
				23,
				array(
					bss_heading( 'footer/visit-title', 'Find the yard', 'div', 'bs_footer_head', 'bs_white' ),
					bss_text(
						'footer/visit',
						'<p>' . esc_html( $biz['street'] ) . '<br>' . esc_html( $biz['suburb'] ) . '<br><a href="' . esc_attr( $biz['tel'] ) . '">' . esc_html( $biz['phone'] ) . '</a><br>Mon–Sat 6am–4:30pm<br>Sun &amp; public hols 8am–2pm</p>',
						'bs_footer',
						'bs_footer_text',
						bss_mt( 12 )
					),
				)
			),
		)
	);

	// "Website By:" credit with the Perth Web Hosting logo (opens in a new tab).
	$credit = bss_container(
		'footer/credit',
		array(
			'flex_direction'              => 'row',
			'flex_justify_content'        => 'flex-end',
			'flex_justify_content_mobile' => 'flex-start',
			'flex_align_items'            => 'center',
			'flex_gap'                    => bss_gap( 10 ),
			'width'                       => bss_size( 40, '%' ),
			'width_mobile'                => bss_size( 100, '%' ),
		),
		array(
			bss_heading( 'footer/credit-label', 'Website By:', 'div', 'bs_fine', 'bs_footer_muted', array( '_element_width' => 'auto' ) ),
			bss_widget(
				'footer/credit-logo',
				'image',
				array(
					'image'          => array(
						'url' => BSS_URL . 'assets/img/pwh-logo-noborder.svg',
						'id'  => '',
						'alt' => 'Perth Web Hosting',
					),
					'image_size'     => 'full',
					'link_to'        => 'custom',
					'link'           => bss_link( 'https://www.perthwebhosting.net.au', true ),
					'width'          => bss_size( 110 ),
					'_element_width' => 'auto',
				)
			),
		)
	);

	$small_print = bss_container(
		'footer/small-print',
		array(
			'flex_direction'       => 'row',
			'flex_justify_content' => 'space-between',
			'flex_align_items'     => 'center',
			'flex_wrap'            => 'wrap',
			'flex_gap'             => bss_gap( 16, 10 ),
			'margin'               => bss_box( 32, 0, 0, 0 ),
			'padding'              => bss_box( 18, 0, 0, 0 ),
			'border_border'        => 'solid',
			'border_width'         => bss_box( 1, 0, 0, 0 ),
			'border_color'         => 'rgba(255,255,255,0.14)',
		),
		array(
			bss_heading( 'footer/copyright', '© ' . gmdate( 'Y' ) . ' Bali Sands · ABN ' . $biz['abn'], 'div', 'bs_fine', 'bs_footer_muted', array( '_element_width' => 'auto' ) ),
			$credit,
		)
	);

	return array(
		bss_section(
			'footer',
			'bs_ink',
			bss_merge(
				array( 'css_classes' => 'bs-footer bs-on-dark' ),
				// No wave here: each page's last section ends in an Ink wave (Shape Divider → Bottom),
				// so the curve always matches the colour of the section above the footer.
				bss_pad( array( 48, 40, 28, 40 ), array( 40, 24, 28, 24 ), array( 36, 16, 24, 16 ) )
			),
			array( $columns, $small_print )
		),
	);
}

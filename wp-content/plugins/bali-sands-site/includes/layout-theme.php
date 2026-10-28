<?php
/**
 * Site header and footer (design 6a) as Elementor Pro Theme Builder templates.
 *
 * Header: green top bar, the logo, JetMenu "Mega Menu" widget (falls
 * back to Elementor Pro's Nav Menu if JetMenu isn't active) and the "Get a
 * quote" button. Backgrounds run full width; the content is boxed to the site
 * width, so on wide screens it lines up with the page sections below. Footer: four columns and the small print. All separate
 * widgets; backgrounds are Global Colors. On phones the footer has its own
 * layout: white logo, call and directions buttons, address and hours, then the link
 * columns as accordions (assets/css/footer.css, assets/js/footer.js).
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bss_footer_assets' );

/**
 * The footer's phone layout: accordion styles and script.
 */
function bss_footer_assets() {
	wp_enqueue_style( 'bss-footer', BSS_URL . 'assets/css/footer.css', array(), bss_asset_ver( 'assets/css/footer.css' ) );
	wp_enqueue_script( 'bss-footer', BSS_URL . 'assets/js/footer.js', array(), bss_asset_ver( 'assets/js/footer.js' ), true );
}

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
					// The logo on its own (no wordmark), 30% up on the original 51px / 40px.
					'image_size'     => 'full',
					'width'          => bss_size( 66 ),
					'width_mobile'   => bss_size( 52 ),
					'_element_width' => 'auto',
				)
			),
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
					'menu_space_between'           => bss_size( 3 ),
					'_margin'                      => bss_box( 14, 0, 0, 0 ),
				),
				bss_font( 'bs_footer', 'menu_typography' ),
				bss_color( 'color_menu_item', 'bs_footer_text' ),
				bss_color( 'color_menu_item_hover', 'bs_white' )
			)
		);
	};

	$column = static function ( $seed, $percent, $elements, $extra = array() ) {
		return bss_container(
			$seed,
			bss_merge(
				array(
					'width'        => bss_size( $percent, '%' ),
					'width_tablet' => bss_size( 46, '%' ),
					'width_mobile' => bss_size( 100, '%' ),
				),
				$extra
			),
			$elements
		);
	};

	// On phones a link column is an accordion: tap its heading to open its links (assets/js/footer.js).
	$accordion  = array( 'css_classes' => 'bs-footer-acc' );
	$acc_head   = array( '_css_classes' => 'bs-footer-acc__head' );
	$phone_only = array(
		'hide_desktop' => 'hidden-desktop',
		'hide_tablet'  => 'hidden-tablet',
	);

	// "Mon–Sat   6am – 4:30pm": a muted label and the hours in white.
	$hours_row = static function ( $seed, $label, $hours, $margin ) {
		return bss_container(
			$seed,
			array(
				'flex_direction' => 'row',
				'flex_wrap'      => 'nowrap',
				'flex_gap'       => bss_gap( 12 ),
				'margin'         => bss_box( $margin, 0, 0, 0 ),
			),
			array(
				bss_heading(
					$seed . '/label',
					$label,
					'div',
					'bs_footer',
					'bs_footer_text',
					array(
						'_element_width'        => 'initial',
						'_element_custom_width' => bss_size( 72 ),
					)
				),
				bss_heading( $seed . '/hours', $hours, 'div', 'bs_footer', 'bs_white', array( '_element_width' => 'auto' ) ),
			)
		);
	};

	$columns = bss_container(
		'footer/columns',
		array(
			'flex_direction'   => 'row',
			'flex_wrap'        => 'nowrap',
			'flex_wrap_tablet' => 'wrap',
			'flex_gap'         => bss_gap( 36, 32 ),
			// Phones: the accordions sit flush, divided by lines (assets/css/footer.css).
			'flex_gap_mobile'  => bss_gap( 0 ),
		),
		array(
			$column(
				'footer/col-about',
				31,
				array(
					// The white logo (no wordmark), linking home. It ships in the plugin: assets/img/logo-white.webp.
					bss_widget(
						'footer/logo-white',
						'image',
						array(
							'image'          => array(
								'url' => BSS_URL . 'assets/img/logo-white.webp',
								'id'  => '',
								'alt' => 'Bali Sands',
							),
							'image_size'     => 'full',
							'link_to'        => 'custom',
							'link'           => bss_link( home_url( '/' ) ),
							'width'          => bss_size( 90 ),
							'align'          => 'left',
							'_element_width' => 'auto',
						)
					),
					bss_text( 'footer/blurb', '<p>Landscape supplies and Bali imports from our Forrestdale yard. Open seven days, delivery across Perth.</p>', 'bs_footer', 'bs_footer_text', bss_merge( bss_mt( 16 ), bss_max_width( 290 ), array( 'hide_mobile' => 'hidden-mobile' ) ) ),
					// Desktop and tablet: quote and call buttons (phones have their own pair below).
					bss_container(
						'footer/buttons',
						array(
							'flex_direction' => 'row',
							'flex_wrap'      => 'wrap',
							'flex_gap'       => bss_gap( 10 ),
							'margin'         => bss_box( 20, 0, 0, 0 ),
							'hide_mobile'    => 'hidden-mobile',
						),
						array(
							bss_button( 'footer/btn-quote', 'Get a quote', home_url( '/#quote' ), 'amber', bss_merge( bss_font( 'bs_button_sm' ), array( 'text_padding' => bss_box( 13, 18 ) ) ) ),
							bss_button(
								'footer/btn-phone',
								$biz['phone'],
								$biz['tel'],
								'outline-light',
								bss_merge(
									bss_font( 'bs_button_sm' ),
									array(
										'text_padding' => bss_box( 12, 16 ),
										'border_width' => bss_box( 1 ),
										// A softer border than the usual white outline.
										'border_color' => 'rgba(255,255,255,0.35)',
										'__globals__'  => array( 'border_color' => '' ),
									)
								)
							),
						)
					),
					// Phones: call and directions buttons, then the address and hours (the "Find the yard" column is hidden there).
					bss_container(
						'footer/mobile-contact',
						bss_merge( array( 'margin' => bss_box( 20, 0, 4, 0 ) ), $phone_only ),
						array(
							bss_grid(
								'footer/mobile-buttons',
								array( 2, 2, 2 ),
								10,
								array(
									bss_button( 'footer/btn-call', 'Call the yard', $biz['tel'], 'amber', array( 'align' => 'justify', 'text_padding' => bss_box( 15, 10 ) ) ),
									bss_button( 'footer/btn-directions', 'Directions', $biz['maps'], 'outline-light', array( 'align' => 'justify', 'text_padding' => bss_box( 13, 10 ), 'link' => bss_link( $biz['maps'], true ) ) ),
								)
							),
							bss_text( 'footer/mobile-visit', '<p>' . esc_html( $biz['short'] ) . ' WA 6112<br>' . esc_html( $biz['hours'] ) . '</p>', 'bs_footer', 'bs_footer_text', bss_mt( 16 ) ),
						)
					),
				)
			),
			$column(
				'footer/col-supplies',
				23,
				array(
					bss_heading( 'footer/supplies-title', 'Landscape supplies', 'div', 'bs_eyebrow', 'accent', bss_merge( array( 'link' => bss_link( bss_page_url( 'supplies' ) ) ), $acc_head ) ),
					$menu_list( 'footer/supplies-menu', 'Footer: Landscape supplies' ),
				),
				$accordion
			),
			$column(
				'footer/col-imports',
				23,
				array(
					bss_heading( 'footer/imports-title', 'Bali imports', 'div', 'bs_eyebrow', 'accent', bss_merge( array( 'link' => bss_link( bss_page_url( 'imports' ) ) ), $acc_head ) ),
					$menu_list( 'footer/imports-menu', 'Footer: Bali imports' ),
				),
				$accordion
			),
			// Phones only: Delivery, About us and Contact.
			$column(
				'footer/col-company',
				23,
				array(
					bss_heading( 'footer/company-title', 'Company', 'div', 'bs_eyebrow', 'accent', $acc_head ),
					$menu_list( 'footer/company-menu', 'Footer: Company' ),
				),
				bss_merge( $accordion, $phone_only )
			),
			$column(
				'footer/col-visit',
				23,
				array(
					bss_heading( 'footer/visit-title', 'Find the yard', 'div', 'bs_eyebrow', 'accent' ),
					bss_text( 'footer/visit-address', '<p><strong>' . esc_html( $biz['street'] ) . '<br>' . esc_html( $biz['suburb'] ) . '</strong></p>', 'bs_footer', 'bs_white', array_merge( bss_mt( 14 ), array( '_css_classes' => 'bs-footer-address' ) ) ),
					$hours_row( 'footer/hours-week', 'Mon–Sat', '6am – 4:30pm', 10 ),
					$hours_row( 'footer/hours-sun', 'Sun &amp; hols', '8am – 2pm', 2 ),
					bss_heading( 'footer/directions', 'Get directions →', 'div', 'bs_link', 'accent', array_merge( bss_mt( 16 ), array( 'link' => bss_link( $biz['maps'], true ) ) ) ),
				),
				array( 'hide_mobile' => 'hidden-mobile' )
			),
		)
	);

	// "Website by" credit with the Perth Web Hosting logo (opens in a new tab).
	$credit = bss_container(
		'footer/credit',
		array(
			'css_classes'                 => 'bs-footer-credit',
			'flex_direction'              => 'row',
			'flex_justify_content'        => 'flex-end',
			'flex_justify_content_mobile' => 'flex-start',
			'flex_align_items'            => 'center',
			'flex_gap'                    => bss_gap( 10 ),
			'width'                       => bss_size( 40, '%' ),
			'width_mobile'                => bss_size( 100, '%' ),
		),
		array(
			bss_heading( 'footer/credit-label', 'Website by', 'div', 'bs_fine', 'bs_footer_muted', array( '_element_width' => 'auto' ) ),
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
			bss_heading( 'footer/copyright', '© ' . gmdate( 'Y' ) . ' Bali Sands · ABN ' . $biz['abn'], 'div', 'bs_fine', 'bs_footer_muted', array( '_element_width' => 'auto', 'hide_mobile' => 'hidden-mobile' ) ),
			bss_text(
				'footer/copyright-mobile',
				'<p>© ' . gmdate( 'Y' ) . ' Bali Sands · <a href="' . esc_url( bss_page_url( 'privacy' ) ) . '">Privacy</a> · <a href="' . esc_url( bss_page_url( 'terms' ) ) . '">Terms</a></p>',
				'bs_fine',
				'bs_footer_muted',
				array(
					'_element_width' => 'auto',
					'_css_classes'   => 'bs-footer-fine',
					'hide_desktop'   => 'hidden-desktop',
					'hide_tablet'    => 'hidden-tablet',
				)
			),
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

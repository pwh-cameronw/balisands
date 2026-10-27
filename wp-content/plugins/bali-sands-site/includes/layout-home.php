<?php
/**
 * The homepage (design 6a) as Elementor data.
 *
 * Every heading, paragraph, image and button is its own Elementor widget, and
 * every section is a container whose background is a Global Color, so a
 * section's colour can be changed in its Style tab (or for the whole site in
 * Site Settings → Global Colors). The wavy edges are Elementor shape dividers
 * on the top of each section (Style → Shape Divider).
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard section padding: desktop [top, bottom], with the top increased under a wave.
 *
 * @param int  $top    Top padding (desktop).
 * @param int  $bottom Bottom padding (desktop).
 * @param bool $wave   Section has a wave along the top.
 * @return array
 */
function bss_section_pad( $top, $bottom, $wave ) {
	$extra = $wave ? 60 : 0;
	return bss_pad(
		array( $top + $extra, 40, $bottom, 40 ),
		array( (int) round( $top * 0.8 ) + $extra, 24, (int) round( $bottom * 0.8 ), 24 ),
		array( (int) round( $top * 0.6 ) + ( $wave ? 32 : 0 ), 16, (int) round( $bottom * 0.6 ), 16 )
	);
}

/**
 * Eyebrow label (small uppercase line above a heading).
 *
 * @param string $seed  Id seed.
 * @param string $text  Text.
 * @param string $color Global colour id.
 * @return array
 */
function bss_eyebrow( $seed, $text, $color ) {
	return bss_heading( $seed, $text, 'div', 'bs_eyebrow', $color );
}

/**
 * "View all →" style text link.
 *
 * @param string $seed  Id seed.
 * @param string $text  Text.
 * @param string $url   URL.
 * @param array  $extra Extra settings.
 * @return array
 */
function bss_text_link( $seed, $text, $url, $extra = array() ) {
	return bss_heading( $seed, $text, 'div', 'bs_link', 'primary', bss_merge( array( 'link' => bss_link( $url ) ), $extra ) );
}

/**
 * Row of buttons.
 *
 * @param string $seed    Id seed.
 * @param array  $buttons Button widgets.
 * @param int    $margin  Top margin.
 * @return array
 */
function bss_button_row( $seed, $buttons, $margin ) {
	return bss_container(
		$seed,
		array(
			'flex_direction' => 'row',
			'flex_wrap'      => 'wrap',
			'flex_gap'       => bss_gap( 12 ),
			'margin'         => bss_box( $margin, 0, 0, 0 ),
		),
		$buttons
	);
}

/**
 * Half-width column for two-column sections.
 *
 * @param string $seed     Id seed.
 * @param int    $percent  Desktop width (%).
 * @param array  $elements Children.
 * @param array  $extra    Extra settings.
 * @return array
 */
function bss_column( $seed, $percent, $elements, $extra = array() ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'width'        => bss_size( $percent, '%' ),
				'width_mobile' => bss_size( 100, '%' ),
			),
			$extra
		),
		$elements
	);
}

/**
 * One linked category card: image, title, subtitle. Links to the category page.
 *
 * @param string $seed  Id seed.
 * @param string $page  Page key.
 * @param string $title Card title.
 * @param string $sub   Subtitle.
 * @return array
 */
function bss_category_card( $seed, $page, $title, $sub ) {
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
					'width'         => bss_size( 100, '%' ),
					'height'        => bss_size( 190 ),
					'height_tablet' => bss_size( 220 ),
					'height_mobile' => bss_size( 160 ),
					'object-fit'    => 'cover',
				)
			),
			bss_container(
				$seed . '/body',
				bss_pad( array( 12, 14, 14, 14 ) ),
				array(
					bss_heading( $seed . '/title', $title, 'h3', 'bs_card', 'bs_ink' ),
					bss_heading( $seed . '/sub', $sub, 'div', 'bs_small', 'bs_muted', bss_mt( 4 ) ),
				)
			),
		)
	);
}

/**
 * The homepage elements, top to bottom.
 *
 * @param int $form_id Gravity Forms quote form id (0 if Gravity Forms isn't active).
 * @return array
 */
function bss_home_elements( $form_id ) {
	$biz   = bss_business();
	$cards = bss_home_cards();

	/* Hero ---------------------------------------------------------------- */

	$hero = bss_section(
		'hero',
		'primary',
		bss_merge(
			bss_section_pad( 56, 60, false ),
			array(
				'flex_direction'        => 'row',
				'flex_direction_mobile' => 'column',
				'flex_align_items'      => 'center',
				'flex_gap'              => bss_gap( 48, 32 ),
				'css_classes'           => 'bs-section bs-section--hero bs-on-dark',
			)
		),
		array(
			bss_column(
				'hero/text',
				50,
				array(
					bss_heading( 'hero/title', 'Landscape supplies &amp; Bali imports in Perth', 'h1', 'bs_display', 'bs_white' ),
					bss_text(
						'hero/intro',
						'<p>Sand, soil, stone and mulch by the scoop or the truckload. Plus pots, statues, water features and huts straight from Bali. One yard in Forrestdale, open seven days.</p>',
						'bs_lead',
						'bs_white',
						bss_merge( bss_mt( 24 ), bss_max_width( 520 ) )
					),
					bss_button_row(
						'hero/buttons',
						array(
							bss_button( 'hero/btn-range', 'Shop the range', '#supplies', 'terracotta' ),
							bss_button( 'hero/btn-calc', 'How much do I need?', '#calculator', 'outline' ),
						),
						30
					),
					bss_icon_list(
						'hero/ticks',
						array(
							array( 'Delivery 7 days', '', 'fas fa-check' ),
							array( 'Bulk or bagged', '', 'fas fa-check' ),
							array( 'Trade accounts', '', 'fas fa-check' ),
						),
						bss_merge(
							array(
								'view'          => 'inline',
								'space_between' => bss_size( 28 ),
								'icon_size'     => bss_size( 12 ),
								'text_indent'   => bss_size( 6 ),
							),
							bss_mt( 30 ),
							bss_color( 'icon_color', 'accent' ),
							bss_color( 'text_color', 'bs_white' ),
							bss_font( 'bs_badge', 'icon_typography' )
						)
					),
				)
			),
			bss_column(
				'hero/photo',
				50,
				array(
					bss_container(
						'hero/card',
						bss_merge(
							array(
								'width'                         => bss_size( 300 ),
								'width_mobile'                  => bss_size( 100, '%' ),
								'border_radius'                 => bss_box( 10 ),
								'box_shadow_box_shadow_type'    => 'yes',
								'box_shadow_box_shadow'         => array(
									'horizontal' => 0,
									'vertical'   => 10,
									'blur'       => 30,
									'spread'     => 0,
									'color'      => 'rgba(0,0,0,0.18)',
								),
							),
							bss_pad( array( 14, 16 ) ),
							bss_bg( 'bs_white' )
						),
						array(
							bss_heading( 'hero/card-title', $biz['short'], 'div', 'bs_card', 'bs_ink' ),
							bss_heading( 'hero/card-hours', $biz['hours'], 'div', 'bs_small', 'bs_muted', bss_mt( 4 ) ),
						)
					),
				),
				bss_merge(
					array(
						'min_height'           => bss_size( 460 ),
						'min_height_mobile'    => bss_size( 300 ),
						'flex_justify_content' => 'flex-end',
						'flex_align_items'     => 'flex-start',
						'overflow'             => 'hidden',
						'border_radius'        => bss_box( 14 ),
						'background_position'  => 'center center',
						'background_size'      => 'cover',
						'css_classes'          => 'bs-photo-slot',
					),
					bss_pad( array( 18 ) ),
					bss_bg( 'bs_photo' )
				)
			),
		)
	);

	/* Landscape supplies -------------------------------------------------- */

	$supply_cards = array();
	foreach ( $cards['supplies'] as $i => $card ) {
		$supply_cards[] = bss_category_card( 'supplies/card' . $i, $card[0], $card[1], $card[2] );
	}

	$supplies = bss_section(
		'supplies',
		'bs_white',
		bss_merge(
			array( '_element_id' => 'supplies' ),
			bss_section_pad( 64, 30, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_split_header(
				'supplies/header',
				array(
					bss_eyebrow( 'supplies/eyebrow', 'Landscape supplies', 'secondary' ),
					bss_heading( 'supplies/title', 'Everything for the job, in one yard', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
				),
				bss_text_link( 'supplies/all', 'View all supplies →', bss_page_url( 'supplies' ) )
			),
			bss_grid( 'supplies/grid', array( 6, 3, 2 ), 16, $supply_cards ),
		)
	);

	/* Bali imports -------------------------------------------------------- */

	$import_cards = array();
	foreach ( $cards['imports'] as $i => $card ) {
		$import_cards[] = bss_category_card( 'imports/card' . $i, $card[0], $card[1], $card[2] );
	}

	$imports = bss_section(
		'imports',
		'bs_white',
		bss_merge(
			array( '_element_id' => 'imports' ),
			bss_section_pad( 30, 64, false )
		),
		array(
			bss_split_header(
				'imports/header',
				array(
					bss_eyebrow( 'imports/eyebrow', 'Bali imports', 'primary' ),
					bss_heading( 'imports/title', 'Then finish it off with a bit of Bali', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
				),
				bss_text_link( 'imports/all', 'View all imports →', bss_page_url( 'imports' ) )
			),
			bss_grid( 'imports/grid', array( 6, 3, 2 ), 16, $import_cards ),
		)
	);

	/* Calculator ---------------------------------------------------------- */

	$calculator = bss_section(
		'calculator',
		'primary',
		bss_merge(
			array(
				'_element_id'           => 'calculator',
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'center',
				'flex_gap'              => bss_gap( 40, 28 ),
				'css_classes'           => 'bs-section bs-section--calculator bs-on-dark',
			),
			bss_section_pad( 36, 44, true ),
			bss_wave_top( 'bs_white', true )
		),
		array(
			bss_column(
				'calculator/text',
				50,
				array(
					bss_eyebrow( 'calculator/eyebrow', 'Calculator', 'bs_mint' ),
					bss_heading( 'calculator/title', 'How many cubes do you need?', 'h2', 'bs_h2', 'bs_white', bss_mt( 10 ) ),
					bss_text( 'calculator/intro', '<p>Put in the size of the area and get the cubic metres back. Get it right before you come down and you won\'t need a second trip.</p>', 'text', 'bs_on_green', bss_mt( 14 ) ),
					bss_text( 'calculator/help', '<p>Not sure which product to use? Call the yard on <a href="' . esc_attr( $biz['tel'] ) . '"><strong>' . esc_html( $biz['phone'] ) . '</strong></a> and we\'ll tell you.</p>', 'text', 'bs_on_green', bss_mt( 14 ) ),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'calculator/card',
				50,
				array(
					bss_widget( 'calculator/tool', 'shortcode', array( 'shortcode' => '[bs_calculator length="6" width="4" depth="50" tipper="8"]' ) ),
					bss_text( 'calculator/note', '<p>Order a little over rather than under. A second delivery costs more than the extra sand.</p>', 'bs_card_text', 'bs_muted', bss_mt( 6 ) ),
					bss_button_row(
						'calculator/buttons',
						array(
							bss_button( 'calculator/btn-quote', 'Get a quote', '#quote', 'amber', array( 'align' => 'justify', '_flex_size' => 'grow' ) ),
							bss_button( 'calculator/btn-call', 'Call the yard', $biz['tel'], 'outline', array( 'align' => 'justify', '_flex_size' => 'grow' ) ),
						),
						18
					),
				),
				bss_merge(
					array(
						'width_tablet'  => bss_size( 100, '%' ),
						'border_radius' => bss_box( 12 ),
						'css_classes'   => 'bs-on-light',
					),
					bss_pad( array( 26 ), null, array( 20 ) ),
					bss_bg( 'bs_white' )
				)
			),
		)
	);

	/* Delivery ------------------------------------------------------------ */

	$fact = static function ( $seed, $title, $text ) {
		return bss_container(
			$seed,
			bss_merge( bss_pad( array( 16 ) ), bss_bg( 'bs_sand' ), bss_border( 'bs_line', 1, 10 ) ),
			array(
				bss_heading( $seed . '/title', $title, 'div', 'bs_card', 'bs_ink' ),
				bss_heading( $seed . '/text', $text, 'div', 'bs_small', 'bs_muted', bss_mt( 4 ) ),
			)
		);
	};

	$delivery = bss_section(
		'delivery',
		'bs_white',
		bss_merge(
			array(
				'_element_id'           => 'delivery',
				'flex_direction'        => 'row',
				'flex_direction_mobile' => 'column',
				'flex_align_items'      => 'center',
				'flex_gap'              => bss_gap( 48, 32 ),
			),
			bss_section_pad( 72, 72, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_column(
				'delivery/photo',
				50,
				array(),
				bss_merge(
					array(
						'min_height'          => bss_size( 400 ),
						'min_height_mobile'   => bss_size( 260 ),
						'border_radius'       => bss_box( 14 ),
						'overflow'            => 'hidden',
						'background_position' => 'center center',
						'background_size'     => 'cover',
						'css_classes'         => 'bs-photo-slot',
					),
					bss_bg( 'bs_photo' )
				)
			),
			bss_column(
				'delivery/text',
				50,
				array(
					bss_eyebrow( 'delivery/eyebrow', 'Delivery', 'secondary' ),
					bss_heading( 'delivery/title', 'Delivered across Perth, or take it home yourself', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
					bss_text( 'delivery/intro', '<p>Tipper loads from half a cube upwards, dropped on the verge or the driveway. Bigger Bali pieces can be delivered too. Or bring the ute or trailer and we\'ll load you up at the yard.</p>', 'text', 'text', bss_mt( 16 ) ),
					bss_grid(
						'delivery/facts',
						array( 2, 2, 1 ),
						12,
						array(
							$fact( 'delivery/fact-bulk', 'Bulk loads', '0.5–8m³ per tipper' ),
							$fact( 'delivery/fact-bagged', 'Bagged', 'For the small jobs' ),
						),
						array( 'margin' => bss_box( 24, 0, 0, 0 ) )
					),
					bss_button_row(
						'delivery/buttons',
						array(
							bss_button( 'delivery/btn-options', 'Delivery options', bss_page_url( 'delivery' ), 'green' ),
							bss_button( 'delivery/btn-quote', 'Get a quote', '#quote', 'outline' ),
						),
						26
					),
				)
			),
		)
	);

	/* Why people come back ------------------------------------------------ */

	$reason = static function ( $n, $title, $text ) {
		$seed = 'reasons/card' . $n;
		return bss_container(
			$seed,
			bss_merge( bss_pad( array( 24 ) ), bss_bg( 'bs_white' ), bss_border( 'bs_line', 1, 12 ) ),
			array(
				bss_heading(
					$seed . '/number',
					(string) $n,
					'div',
					'bs_card_lg',
					'bs_white',
					bss_merge(
						array(
							'align'                  => 'center',
							'_element_width'         => 'initial',
							'_element_custom_width'  => bss_size( 44 ),
							'_padding'               => bss_box( 10, 0 ),
							'_border_radius'         => bss_box( 10 ),
							'_background_background' => 'classic',
						),
						bss_color( '_background_color', 'primary' )
					)
				),
				bss_heading( $seed . '/title', $title, 'h3', 'bs_card_lg', 'bs_ink', bss_mt( 16 ) ),
				bss_heading( $seed . '/text', $text, 'p', 'bs_card_text', 'bs_muted', bss_mt( 8 ) ),
			)
		);
	};

	$reasons = bss_section(
		'reasons',
		'secondary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--reasons bs-on-dark' ),
			bss_section_pad( 64, 64, true ),
			bss_wave_top( 'bs_white', true )
		),
		array(
			bss_heading( 'reasons/title', 'Why people come back', 'h2', 'bs_h2', 'bs_white', array( 'align' => 'center' ) ),
			bss_grid(
				'reasons/grid',
				array( 4, 2, 1 ),
				18,
				array(
					$reason( 1, 'Delivery 7 days', 'Tipped on the verge or the driveway, anywhere in the metro.' ),
					$reason( 2, 'Two shops in one', 'Pick up the base materials and the pots and statues in the same trip.' ),
					$reason( 3, 'Imported direct', 'Bali pieces come straight from the makers, priced close to wholesale.' ),
					$reason( 4, 'Ask us first', 'Tell us about the job at the counter and we\'ll tell you what you need and how much.' ),
				),
				array( 'margin' => bss_box( 34, 0, 0, 0 ) )
			),
		)
	);

	/* Reviews ------------------------------------------------------------- */

	$review = static function ( $n ) {
		$seed = 'reviews/card' . $n;
		return bss_container(
			$seed,
			bss_merge( bss_pad( array( 24 ) ), bss_bg( 'bs_white' ), bss_border( 'bs_line', 1, 12 ) ),
			array(
				bss_heading( $seed . '/stars', '★★★★★', 'div', 'bs_link', 'accent', array( 'typography_letter_spacing' => bss_size( 1.6 ) ) ),
				bss_text( $seed . '/quote', '<p>[Paste a real Google review here.]</p>', 'text', 'text', bss_mt( 14 ) ),
				bss_heading( $seed . '/name', '[Customer name]', 'div', 'bs_link', 'bs_ink', bss_mt( 16 ) ),
			)
		);
	};

	$reviews = bss_section(
		'reviews',
		'bs_white',
		bss_merge(
			bss_section_pad( 64, 64, true ),
			bss_wave_top( 'secondary' )
		),
		array(
			bss_split_header(
				'reviews/header',
				array( bss_heading( 'reviews/title', 'What customers say', 'h2', 'bs_h2', 'bs_ink' ) ),
				bss_text_link( 'reviews/all', 'All Google reviews →', '#' )
			),
			bss_grid( 'reviews/grid', array( 3, 1, 1 ), 18, array( $review( 1 ), $review( 2 ), $review( 3 ) ) ),
		)
	);

	/* Quote --------------------------------------------------------------- */

	$form_shortcode = $form_id
		? '[gravityform id="' . (int) $form_id . '" title="false" description="false" ajax="true" theme="gravity-theme"]'
		: '[gravityform id="0" title="false" description="false" ajax="true" theme="gravity-theme"]';

	$quote = bss_section(
		'quote',
		'primary',
		bss_merge(
			array(
				'_element_id'           => 'quote',
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'flex-start',
				'flex_gap'              => bss_gap( 48, 32 ),
				'css_classes'           => 'bs-section bs-section--quote bs-on-dark',
			),
			// Waves top and bottom: in from the reviews above, out into the footer (Ink) below.
			bss_pad( array( 124, 40, 124, 40 ), array( 111, 24, 111, 24 ), array( 70, 16, 70, 16 ) ),
			bss_wave_top( 'bs_white', true ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_column(
				'quote/text',
				45,
				array(
					bss_eyebrow( 'quote/eyebrow', 'Get a quote', 'accent' ),
					bss_heading( 'quote/title', 'Tell us about the job', 'h2', 'bs_h2', 'bs_white', bss_mt( 10 ) ),
					bss_text( 'quote/intro', '<p>Add your address if you want it delivered. It changes the price and which truck we send. Not sure what you need? Describe the job and we\'ll work it out.</p>', 'text', 'bs_white', bss_mt( 16 ) ),
					bss_heading( 'quote/phone', $biz['phone'], 'div', 'bs_phone', 'bs_white', bss_merge( bss_mt( 24 ), array( 'link' => bss_link( $biz['tel'] ) ) ) ),
					bss_heading( 'quote/address', $biz['address'], 'div', 'bs_card_text', 'bs_white', bss_mt( 4 ) ),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'quote/form',
				55,
				array(
					bss_widget( 'quote/gravity-form', 'shortcode', array( 'shortcode' => $form_shortcode, '_css_classes' => 'bs-gf' ) ),
				),
				bss_merge(
					array( 'width_tablet' => bss_size( 100, '%' ) ),
					bss_pad( array( 28 ), null, array( 20 ) ),
					bss_bg( 'bs_white' ),
					bss_border( 'bs_line', 1, 14 )
				)
			),
		)
	);

	return array( $hero, $supplies, $imports, $calculator, $delivery, $reasons, $reviews, $quote );
}

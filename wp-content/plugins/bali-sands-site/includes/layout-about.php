<?php
/**
 * About page (design 8a, "About us — rebuilt in the built-site style") as Elementor data.
 *
 * Green intro with breadcrumbs → Our story (with an early-days photo) → Two
 * shops in one yard (sand) → How our imports get here (terracotta) → Come and
 * see us, with a map. Every heading, paragraph, photo panel and button is its
 * own widget; every section's background is a Global Color; the waves are
 * Elementor shape dividers.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Photo panel: a container with a placeholder colour. Set a photo under
 * Style → Background → Image.
 *
 * @param string $seed   Id seed.
 * @param int    $height Desktop min height (px).
 * @param int    $radius Corner radius (px), 0 for none.
 * @param string $color  Global colour id for the placeholder.
 * @param int    $image  Attachment id to show now (0 = none yet).
 * @return array
 */
function bss_photo_panel( $seed, $height, $radius, $color, $image = 0 ) {
	$settings = bss_merge(
		array(
			'min_height'          => bss_size( $height ),
			'min_height_mobile'   => bss_size( (int) round( $height * 0.75 ) ),
			'overflow'            => 'hidden',
			'background_position' => 'center center',
			'background_size'     => 'cover',
			'background_repeat'   => 'no-repeat',
		),
		$radius ? array( 'border_radius' => bss_box( $radius ) ) : array(),
		bss_bg( $color )
	);
	if ( $image ) {
		$settings['background_image'] = array(
			'url' => (string) wp_get_attachment_image_url( $image, 'large' ),
			'id'  => $image,
		);
	}
	return bss_container( $seed, $settings );
}

/**
 * Green numbered square.
 *
 * @param string $seed Id seed.
 * @param int    $n    Number.
 * @param int    $size Width/height (px).
 * @return array
 */
function bss_number_badge( $seed, $n, $size = 44 ) {
	return bss_heading(
		$seed,
		(string) $n,
		'div',
		'bs_link',
		'bs_white',
		bss_merge(
			array(
				'align'                  => 'center',
				'_element_width'         => 'initial',
				'_element_custom_width'  => bss_size( $size ),
				'_padding'               => bss_box( (int) round( ( $size - 15 ) / 2 ), 0 ),
				'_border_radius'         => bss_box( 8 ),
				'_background_background' => 'classic',
			),
			bss_color( '_background_color', 'primary' )
		)
	);
}

/**
 * One "Label   value" row (address, hours, phone).
 *
 * @param string $seed  Id seed.
 * @param string $label Label.
 * @param string $value Value (HTML allowed, e.g. a tel: link).
 * @return array
 */
function bss_detail_row( $seed, $label, $value ) {
	return bss_container(
		$seed,
		array(
			'flex_direction' => 'row',
			'flex_wrap'      => 'wrap',
			'flex_gap'       => bss_gap( 26, 2 ),
		),
		array(
			bss_heading(
				$seed . '/label',
				$label,
				'div',
				'bs_link',
				'bs_ink',
				array(
					'_element_width'        => 'initial',
					'_element_custom_width' => bss_size( 150 ),
					'_padding'              => bss_box( 4, 0, 0, 0 ),
				)
			),
			bss_text( $seed . '/value', '<p>' . $value . '</p>', 'text', 'text', array( '_element_width' => 'auto' ) ),
		)
	);
}

/**
 * The About page elements, top to bottom.
 *
 * @return array
 */
function bss_about_elements() {
	$about = bss_about();
	$biz   = bss_business();

	/* Intro --------------------------------------------------------------- */

	$intro = bss_section(
		'about/intro',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--about-intro bs-on-dark' ),
			bss_pad( array( 44, 40, 30, 40 ), array( 36, 24, 28, 24 ), array( 28, 16, 24, 16 ) )
		),
		array(
			bss_text(
				'about/crumbs',
				sprintf( '<p><a href="%s">Home</a> / <strong>About us</strong></p>', esc_url( home_url( '/' ) ) ),
				'bs_crumb',
				'bs_mint',
				array( '_css_classes' => 'bs-crumbs' )
			),
			bss_container(
				'about/intro-row',
				array(
					'flex_direction'        => 'row',
					'flex_direction_mobile' => 'column',
					'flex_align_items'      => 'center',
					'flex_gap'              => bss_gap( 48, 28 ),
					'margin'                => bss_box( 24, 0, 0, 0 ),
				),
				array(
					bss_column(
						'about/intro-text',
						50,
						array(
							bss_eyebrow( 'about/eyebrow', esc_html( $about['eyebrow'] ), 'accent' ),
							bss_heading( 'about/title', esc_html( $about['title'] ), 'h1', 'bs_h1_sm', 'bs_white', bss_mt( 14 ) ),
							bss_text( 'about/lead', '<p>' . esc_html( $about['lead'] ) . '</p>', 'bs_lead', 'bs_white', bss_merge( bss_mt( 18 ), bss_max_width( 520 ) ) ),
						)
					),
					bss_column( 'about/photo', 50, array( bss_photo_panel( 'about/photo-panel', 380, 14, 'bs_photo_dark' ) ) ),
				)
			),
		)
	);

	/* Our story ----------------------------------------------------------- */

	$story_paras = array();
	foreach ( $about['story'] as $i => $para ) {
		$story_paras[] = bss_text( 'about/story-' . $i, '<p>' . esc_html( $para ) . '</p>', 'bs_story', 'text', $i ? bss_mt( 18 ) : array() );
	}

	$story = bss_section(
		'about/story',
		'bs_white',
		bss_merge(
			array(
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_gap'              => bss_gap( 56, 28 ),
			),
			bss_section_pad( 48, 64, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_column(
				'about/story-left',
				42,
				array(
					bss_eyebrow( 'about/story-eyebrow', 'Our story', 'secondary' ),
					bss_heading( 'about/story-title', 'How it started', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
					bss_container(
						'about/story-photo-wrap',
						array( 'margin' => bss_box( 26, 0, 0, 0 ) ),
						array( bss_photo_panel( 'about/story-photo', 260, 12, 'bs_photo_light' ) )
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'about/story-text',
				58,
				$story_paras,
				array(
					'width_tablet'   => bss_size( 100, '%' ),
					'padding'        => bss_box( 30, 0, 0, 0 ),
					'padding_tablet' => bss_box( 0 ),
				)
			),
		)
	);

	/* Two shops in one yard ----------------------------------------------- */

	$side_cards = array();
	foreach ( $about['sides'] as $i => $side ) {
		list( $page, $label, $color, $heading, $text, $link, $photo ) = $side;
		$seed         = 'about/side' . $i;
		$side_cards[] = bss_container(
			$seed,
			bss_merge( array( 'overflow' => 'hidden' ), bss_bg( 'bs_white' ), bss_border( 'bs_line', 1, 14 ) ),
			array(
				bss_photo_panel( $seed . '/photo', 220, 0, 'bs_photo', $photo ? bss_media_id( $photo ) : 0 ),
				bss_container(
					$seed . '/body',
					bss_pad( array( 24, 26, 28, 26 ), null, array( 20 ) ),
					array(
						bss_eyebrow( $seed . '/eyebrow', $label, $color ),
						bss_heading( $seed . '/title', $heading, 'h3', 'bs_h3', 'bs_ink', bss_mt( 10 ) ),
						bss_text( $seed . '/text', '<p>' . esc_html( $text ) . '</p>', 'text', 'text', bss_mt( 10 ) ),
						bss_text_link( $seed . '/link', $link, bss_page_url( $page ), bss_mt( 18 ) ),
					)
				),
			)
		);
	}

	$sides = bss_section(
		'about/sides',
		'bs_sand',
		bss_merge(
			bss_section_pad( 28, 56, true ),
			bss_wave_top( 'bs_white', true )
		),
		array(
			bss_heading( 'about/sides-title', 'Two shops in one yard', 'h2', 'bs_h2', 'bs_ink', array( 'align' => 'center' ) ),
			bss_grid( 'about/sides-grid', array( 2, 2, 1 ), 20, $side_cards, array( 'margin' => bss_box( 30, 0, 0, 0 ) ) ),
		)
	);

	/* How our imports get here -------------------------------------------- */

	$steps = array();
	foreach ( $about['sourcing'] as $i => $step ) {
		$seed    = 'about/step' . $i;
		$steps[] = bss_container(
			$seed,
			bss_merge( array( 'overflow' => 'hidden', 'border_radius' => bss_box( 12 ) ), bss_bg( 'bs_white' ) ),
			array(
				bss_photo_panel( $seed . '/photo', 170, 0, 'bs_photo_light' ),
				bss_container(
					$seed . '/body',
					bss_pad( array( 20, 22, 24, 22 ) ),
					array(
						bss_number_badge( $seed . '/number', $i + 1, 36 ),
						bss_heading( $seed . '/title', $step[0], 'h3', 'bs_card_lg', 'bs_ink', bss_mt( 12 ) ),
						bss_heading( $seed . '/text', $step[1], 'p', 'bs_card_text', 'text', bss_mt( 6 ) ),
					)
				),
			)
		);
	}

	$sourcing = bss_section(
		'about/sourcing',
		'secondary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--about-sourcing bs-on-dark' ),
			bss_section_pad( 28, 52, true ),
			bss_wave_top( 'bs_sand' )
		),
		array(
			bss_heading( 'about/sourcing-eyebrow', 'How our imports get here', 'div', 'bs_eyebrow', 'bs_label_warm', array( 'align' => 'center' ) ),
			bss_heading( 'about/sourcing-title', 'From the workshop in Bali to our yard', 'h2', 'bs_h2', 'bs_white', bss_merge( bss_mt( 10 ), array( 'align' => 'center' ) ) ),
			bss_grid( 'about/sourcing-grid', array( 3, 3, 1 ), 18, $steps, array( 'margin' => bss_box( 30, 0, 0, 0 ) ) ),
		)
	);

	/* Come and see us ----------------------------------------------------- */

	$visit = bss_section(
		'about/visit',
		'bs_white',
		bss_merge(
			array(
				'_element_id'           => 'visit',
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'center',
				'flex_gap'              => bss_gap( 48, 32 ),
			),
			// Waves top and bottom: in from the terracotta band, out into the footer (Ink).
			bss_pad( array( 100, 40, 124, 40 ), array( 92, 24, 111, 24 ), array( 56, 16, 70, 16 ) ),
			bss_wave_top( 'secondary', true ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_column(
				'about/visit-text',
				50,
				array(
					bss_eyebrow( 'about/visit-eyebrow', 'Come and see us', 'secondary' ),
					bss_heading( 'about/visit-title', esc_html( $about['visit']['title'] ), 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
					bss_text( 'about/visit-intro', '<p>' . esc_html( $about['visit']['text'] ) . '</p>', 'text', 'text', bss_mt( 14 ) ),
					bss_container(
						'about/details',
						array(
							'flex_gap' => bss_gap( 8 ),
							'margin'   => bss_box( 22, 0, 0, 0 ),
						),
						array(
							bss_detail_row( 'about/address', 'Address', esc_html( $biz['address'] ) ),
							bss_detail_row( 'about/hours-week', 'Mon – Sat', '6:00am – 4:30pm' ),
							bss_detail_row( 'about/hours-sun', 'Sun &amp; public hols', '8:00am – 2:00pm' ),
							bss_detail_row( 'about/phone', 'Phone', '<a href="' . esc_attr( $biz['tel'] ) . '">' . esc_html( $biz['phone'] ) . '</a>' ),
						)
					),
					bss_button_row(
						'about/visit-buttons',
						array(
							bss_button( 'about/btn-directions', 'Get directions', $biz['maps'], 'green', array( 'link' => bss_link( $biz['maps'], true ) ) ),
							bss_button( 'about/btn-quote', 'Get a quote', home_url( '/#quote' ), 'amber' ),
						),
						26
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'about/map',
				50,
				array(
					bss_widget(
						'about/map-embed',
						'google_maps',
						array(
							'address'       => $biz['address'],
							'zoom'          => bss_size( 14 ),
							'height'        => bss_size( 380 ),
							'height_mobile' => bss_size( 280 ),
							'_css_classes'  => 'bs-map',
						)
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
		)
	);

	return array( $intro, $story, $sides, $sourcing, $visit );
}

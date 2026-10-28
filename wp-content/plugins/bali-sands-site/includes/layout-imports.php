<?php
/**
 * Bali Imports page (design 14a, "Bali imports — main category page") as
 * Elementor data.
 *
 * Photo hero → "Six ways to bring Bali home" (a card per category page, with
 * its featured image) → "New arrivals" (the newest Bali imports Products,
 * [bs_new_arrivals]) → "No two pieces are the same" → "How to buy". As on the
 * other pages, every heading, paragraph and button is its own widget and every
 * section's background is a Global Color.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * The six category cards: [page key, title, description].
 *
 * @return array<int,array{0:string,1:string,2:string}>
 */
function bss_imports_categories() {
	return array(
		array( 'imports-gazebos', 'Gazebos & outdoor furniture', 'Bamboo huts, gazebos and fencing panels, plus teak, rattan and rootwood furniture.' ),
		array( 'imports-water', 'Water features', 'Ceramic, stone and GRC fountains and wall features.' ),
		array( 'imports-pots', 'Pots', 'Glazed, limestone, hand-carved and lightweight GRC.' ),
		array( 'imports-statues', 'Statues', 'Buddha, animal and character statues and wall plaques.' ),
		array( 'imports-birdbath', 'Bird baths', 'Stone, marble and hand-carved basins and lanterns.' ),
		array( 'imports-gifts', 'Handycrafts & giftware', 'Wall art, mirrors and homewares from Bali and Java.' ),
	);
}

/**
 * One "Shop by category" card: photo, title, description and "Shop now →",
 * linking to the category page.
 *
 * @param string $seed Id seed.
 * @param array  $cat  bss_imports_categories() row.
 * @return array
 */
function bss_imports_card( $seed, $cat ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'html_tag'                   => 'a',
				'link'                       => bss_link( bss_page_url( $cat[0] ) ),
				'overflow'                   => 'hidden',
				'css_classes'                => 'bs-card bs-imports-card',
				'box_shadow_box_shadow_type' => 'yes',
				'box_shadow_box_shadow'      => array(
					'horizontal' => 0,
					'vertical'   => 4,
					'blur'       => 14,
					'spread'     => 0,
					'color'      => 'rgba(32,28,23,0.06)',
				),
			),
			bss_bg( 'bs_white' ),
			bss_border( 'bs_line', 1, 14 )
		),
		array(
			bss_image(
				$seed . '/image',
				bss_page_thumb_id( $cat[0] ),
				array(
					'image_size'    => 'large',
					'width'         => bss_size( 100, '%' ),
					'height'        => bss_size( 240 ),
					'height_mobile' => bss_size( 200 ),
					'object-fit'    => 'cover',
				)
			),
			bss_container(
				$seed . '/body',
				bss_merge(
					array(
						'flex_size'   => 'grow',
						'css_classes' => 'bs-imports-card__body',
					),
					bss_pad( array( 20, 22, 22, 22 ) )
				),
				array(
					bss_heading( $seed . '/title', esc_html( $cat[1] ), 'h3', 'bs_phone', 'bs_ink', array( '_css_classes' => 'bs-imports-card__title' ) ),
					bss_heading( $seed . '/desc', esc_html( $cat[2] ), 'p', 'bs_card_text', 'text', bss_mt( 6 ) ),
					bss_heading( $seed . '/more', 'Shop now →', 'div', 'bs_link', 'primary', array( '_css_classes' => 'bs-imports-card__more' ) ),
				)
			),
		)
	);
}

/**
 * One "How to buy" step card.
 *
 * @param string $seed  Id seed.
 * @param int    $n     Step number.
 * @param string $title Title.
 * @param string $text  Text.
 * @return array
 */
function bss_imports_step( $seed, $n, $title, $text ) {
	return bss_container(
		$seed,
		bss_merge(
			array( 'border_radius' => bss_box( 12 ) ),
			bss_pad( array( 24 ) ),
			bss_bg( 'bs_white' )
		),
		array(
			bss_heading(
				$seed . '/n',
				(string) $n,
				'div',
				'bs_card_lg',
				'bs_ink',
				bss_merge(
					array(
						'align'                  => 'center',
						'_element_width'         => 'initial',
						'_element_custom_width'  => bss_size( 44 ),
						'_padding'               => bss_box( 12, 0 ),
						'_border_radius'         => bss_box( 10 ),
						'_background_background' => 'classic',
					),
					bss_color( '_background_color', 'accent' )
				)
			),
			bss_heading( $seed . '/title', esc_html( $title ), 'h3', 'bs_card_lg', 'bs_ink', bss_mt( 16 ) ),
			bss_heading( $seed . '/text', esc_html( $text ), 'p', 'bs_card_text', 'text', bss_mt( 8 ) ),
		)
	);
}

/**
 * The Bali Imports page elements, top to bottom.
 *
 * @return array
 */
function bss_imports_elements() {
	$biz     = bss_business();
	$enquiry = add_query_arg( 'topic', rawurlencode( 'Bali imports' ), bss_page_url( 'contact' ) ) . '#enquiry';

	/* Hero: photo with a green fade, sand wave below -------------------------- */

	$hero_image = bss_media_id( 'imports' );
	$hero       = bss_section(
		'imports/hero',
		'primary',
		bss_merge(
			array(
				'css_classes'                         => 'bs-section bs-section--imports-hero bs-on-dark',
				'min_height'                          => bss_size( 400 ),
				'min_height_mobile'                   => bss_size( 460 ),
				'flex_justify_content'                => 'center',
				'background_image'                    => array(
					'url' => $hero_image ? (string) wp_get_attachment_image_url( $hero_image, 'full' ) : '',
					'id'  => $hero_image ? $hero_image : '',
				),
				'background_position'                 => 'center center',
				'background_size'                     => 'cover',
				'background_repeat'                   => 'no-repeat',
				// Dark green fading to clear, left to right, so the text stays readable.
				'background_overlay_background'       => 'gradient',
				'background_overlay_color'            => 'rgba(24,36,28,0.92)',
				'background_overlay_color_stop'       => bss_size( 0, '%' ),
				'background_overlay_color_b'          => 'rgba(24,36,28,0.10)',
				'background_overlay_color_b_stop'     => bss_size( 100, '%' ),
				'background_overlay_gradient_type'    => 'linear',
				'background_overlay_gradient_angle'   => bss_size( 90, 'deg' ),
				'background_overlay_gradient_angle_mobile' => bss_size( 180, 'deg' ),
			),
			bss_pad( array( 32, 40, 100, 40 ), array( 32, 24, 92, 24 ), array( 24, 16, 72, 16 ) ),
			bss_wave_bottom( 'bs_sand' )
		),
		array(
			bss_container(
				'imports/hero-text',
				array(
					'width'        => bss_size( 640 ),
					'width_mobile' => bss_size( 100, '%' ),
				),
				array(
					bss_text( 'imports/crumbs', '<p><a href="' . esc_url( home_url( '/' ) ) . '">Home</a> / <strong>Bali imports</strong></p>', 'bs_crumb', 'bs_mint', array( '_css_classes' => 'bs-crumbs' ) ),
					bss_heading( 'imports/eyebrow', 'Imported direct from Bali &amp; Java', 'div', 'bs_eyebrow', 'accent', bss_mt( 32 ) ),
					bss_heading( 'imports/title', 'Bali imports', 'h1', 'bs_display', 'bs_white', bss_mt( 14 ) ),
					bss_text( 'imports/lead', '<p>Gazebos, furniture, water features, pots and statues, handmade in Bali and Java and shipped straight to our Forrestdale yard. Most pieces are one of a kind, so come and choose yours in person.</p>', 'bs_lead', 'bs_white', bss_mt( 18 ) ),
					bss_button_row(
						'imports/buttons',
						array(
							bss_button( 'imports/btn-range', 'Browse the range', '#range', 'amber' ),
							bss_button( 'imports/btn-visit', 'Visit the yard', bss_page_url( 'contact' ), 'outline-light' ),
						),
						28
					),
				)
			),
		)
	);

	/* Shop by category -------------------------------------------------------- */

	$cards = array();
	foreach ( bss_imports_categories() as $i => $cat ) {
		$cards[] = bss_imports_card( 'imports/cat' . $i, $cat );
	}

	$categories = bss_section(
		'imports/categories',
		'bs_sand',
		array_merge(
			array( '_element_id' => 'range' ),
			bss_section_pad( 40, 64, false )
		),
		array(
			bss_eyebrow( 'imports/cat-eyebrow', 'Shop by category', 'primary' ),
			bss_heading( 'imports/cat-title', 'Six ways to bring Bali home', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
			bss_grid( 'imports/cat-grid', array( 3, 2, 1 ), 28, $cards, array( 'margin' => bss_box( 28, 0, 0, 0 ) ) ),
		)
	);

	/* New arrivals ------------------------------------------------------------ */

	$arrivals = bss_section(
		'imports/arrivals',
		'bs_white',
		bss_merge(
			bss_section_pad( 24, 56, true ),
			bss_wave_top( 'bs_sand' )
		),
		array(
			bss_split_header(
				'imports/arrivals-header',
				array(
					bss_eyebrow( 'imports/arrivals-eyebrow', 'Fresh off the container', 'secondary' ),
					bss_heading( 'imports/arrivals-title', 'New arrivals', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
				),
				bss_text_link( 'imports/arrivals-all', 'See all new arrivals →', home_url( '/product-category/bali-imports/' ) )
			),
			// The four newest Bali imports Products (includes/products.php).
			bss_widget(
				'imports/arrivals-list',
				'shortcode',
				array( 'shortcode' => '[bs_new_arrivals category="bali-imports" count="4"]' )
			),
		)
	);

	/* Handmade ---------------------------------------------------------------- */

	$handmade = bss_section(
		'imports/handmade',
		'bs_sand',
		bss_merge(
			array(
				'flex_direction'        => 'row',
				'flex_direction_mobile' => 'column',
				'flex_align_items'      => 'center',
				'flex_gap'              => bss_gap( 48, 28 ),
			),
			bss_section_pad( 36, 64, true ),
			bss_wave_top( 'bs_white', true )
		),
		array(
			bss_column(
				'imports/handmade-photo',
				50,
				array(
					// Set a photo under Style → Background → Image (a maker carving stone or weaving).
					bss_photo_panel( 'imports/handmade-panel', 400, 14, 'bs_photo' ),
				)
			),
			bss_column(
				'imports/handmade-text',
				50,
				array(
					bss_eyebrow( 'imports/handmade-eyebrow', 'Handmade', 'secondary' ),
					bss_heading( 'imports/handmade-title', 'No two pieces are the same', 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
					bss_text( 'imports/handmade-1', '<p>Our pots, statues and furniture are carved, glazed and woven by hand, so size, colour and finish vary from piece to piece. The photos online show the style. The piece you take home is the one you choose at the yard.</p>', 'bs_story', 'text', bss_mt( 16 ) ),
					bss_text( 'imports/handmade-2', '<p>We buy direct from the makers and ship full containers to Forrestdale, so prices stay close to wholesale.</p>', 'bs_story', 'text', bss_mt( 14 ) ),
				)
			),
		)
	);

	/* How to buy -------------------------------------------------------------- */

	$steps = array(
		bss_imports_step( 'imports/step1', 1, 'Come and choose', 'Walk the yard and pick the exact piece. We\'re open seven days.' ),
		bss_imports_step( 'imports/step2', 2, 'Or send an enquiry', 'Tell us what you\'re after and we\'ll send photos of what\'s in stock.' ),
		bss_imports_step( 'imports/step3', 3, 'Take it or have it delivered', 'We load your trailer, or deliver bigger pieces like huts and water features across Perth.' ),
	);

	$how = bss_section(
		'imports/how',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--imports-how bs-on-dark' ),
			// Extra room top and bottom for the waves (in from the sand section, out into the footer).
			bss_pad( array( 84, 40, 116, 40 ), array( 80, 24, 104, 24 ), array( 52, 16, 76, 16 ) ),
			bss_wave_top( 'bs_sand' ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_heading( 'imports/how-title', 'How to buy', 'h2', 'bs_h2', 'bs_white', array( 'align' => 'center' ) ),
			bss_grid( 'imports/how-grid', array( 3, 1, 1 ), 18, $steps, array( 'margin' => bss_box( 30, 0, 0, 0 ) ) ),
			bss_container(
				'imports/how-buttons',
				array(
					'flex_direction'       => 'row',
					'flex_wrap'            => 'wrap',
					'flex_justify_content' => 'center',
					'flex_gap'             => bss_gap( 12 ),
					'margin'               => bss_box( 30, 0, 0, 0 ),
				),
				array(
					bss_button( 'imports/btn-enquiry', 'Send an enquiry', $enquiry, 'amber' ),
					bss_button( 'imports/btn-call', $biz['phone'], $biz['tel'], 'outline-light' ),
				)
			),
		)
	);

	return array( $hero, $categories, $arrivals, $handmade, $how );
}

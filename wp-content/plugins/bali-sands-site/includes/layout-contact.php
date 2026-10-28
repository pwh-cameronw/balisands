<?php
/**
 * Contact page (design 10a, "Contact us") as Elementor data.
 *
 * Green intro with breadcrumbs and three quick-contact cards → the quote form
 * beside the yard's details (white) → Find us, with a map (sand). Every
 * heading, paragraph and button is its own widget; every section's background
 * is a Global Color; the waves are Elementor shape dividers.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * One quick-contact card on the green intro: label, big line, small line.
 *
 * @param string $seed  Id seed.
 * @param string $label Eyebrow label.
 * @param string $title Main line.
 * @param string $text  Small line under it.
 * @param string $url   Link for the main line ('' = none).
 * @param bool   $new_tab Open the link in a new tab.
 * @return array
 */
function bss_contact_card( $seed, $label, $title, $text, $url = '', $new_tab = false ) {
	return bss_container(
		$seed,
		bss_merge(
			bss_pad( array( 22, 24, 24, 24 ), null, array( 18, 20, 20, 20 ) ),
			bss_bg( 'bs_white' ),
			array( 'border_radius' => bss_box( 12 ) )
		),
		array(
			bss_eyebrow( $seed . '/label', $label, 'secondary' ),
			bss_heading( $seed . '/title', $title, 'div', 'bs_phone', 'bs_ink', bss_merge( bss_mt( 10 ), $url ? array( 'link' => bss_link( $url, $new_tab ) ) : array() ) ),
			bss_heading( $seed . '/text', $text, 'p', 'bs_card_text', 'bs_muted', bss_mt( 4 ) ),
		)
	);
}

/**
 * The Contact page elements, top to bottom.
 *
 * @param int $form_id Gravity Forms quote form id (0 if Gravity Forms isn't active).
 * @return array
 */
function bss_contact_elements( $form_id ) {
	$contact = bss_contact();
	$biz     = bss_business();

	/* Intro --------------------------------------------------------------- */

	$intro = bss_section(
		'contact/intro',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--contact-intro bs-on-dark' ),
			bss_pad( array( 44, 40, 40, 40 ), array( 36, 24, 36, 24 ), array( 28, 16, 28, 16 ) )
		),
		array(
			bss_text(
				'contact/crumbs',
				sprintf( '<p><a href="%s">Home</a> / <strong>Contact us</strong></p>', esc_url( home_url( '/' ) ) ),
				'bs_crumb',
				'bs_mint',
				array( '_css_classes' => 'bs-crumbs' )
			),
			bss_container(
				'contact/intro-text',
				bss_merge( array( 'margin' => bss_box( 24, 0, 0, 0 ) ), bss_max_width( 720 ) ),
				array(
					bss_eyebrow( 'contact/eyebrow', esc_html( $contact['eyebrow'] ), 'accent' ),
					bss_heading( 'contact/title', esc_html( $contact['title'] ), 'h1', 'bs_h1_sm', 'bs_white', bss_mt( 14 ) ),
					bss_text( 'contact/lead', '<p>' . esc_html( $contact['lead'] ) . '</p>', 'bs_lead', 'bs_white', bss_mt( 18 ) ),
				)
			),
			bss_grid(
				'contact/cards',
				array( 3, 3, 1 ),
				16,
				array(
					bss_contact_card( 'contact/card-call', 'Call the yard', esc_html( $biz['phone'] ), 'The quickest way to a price', $biz['tel'] ),
					bss_contact_card( 'contact/card-visit', 'Visit us', esc_html( $biz['short'] ), 'Load your trailer or pick your piece', $biz['maps'], true ),
					bss_contact_card( 'contact/card-hours', 'Open', 'Mon – Sat 6am – 4:30pm', 'Sun &amp; public hols 8am – 2pm' ),
				),
				array(
					'margin'                   => bss_box( 32, 0, 0, 0 ),
					// One row each on tablet, so the long lines don't squeeze.
					'grid_columns_grid_tablet' => bss_size( 1, 'fr' ),
					'grid_rows_grid_tablet'    => bss_size( 3, 'fr' ),
				)
			),
		)
	);

	/* Form + details ------------------------------------------------------ */

	$form_shortcode = '[gravityform id="' . (int) $form_id . '" title="false" description="false" ajax="true" theme="gravity-theme"]';

	$form = bss_section(
		'contact/form',
		'bs_white',
		bss_merge(
			array(
				'_element_id'           => 'quote',
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'flex-start',
				'flex_gap'              => bss_gap( 48, 36 ),
			),
			bss_section_pad( 40, 64, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_column(
				'contact/form-col',
				58,
				array(
					bss_eyebrow( 'contact/form-eyebrow', 'Get a quote', 'secondary' ),
					bss_heading( 'contact/form-title', esc_html( $contact['form_title'] ), 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
					bss_text( 'contact/form-intro', '<p>' . esc_html( $contact['form_text'] ) . '</p>', 'text', 'text', bss_mt( 14 ) ),
					bss_container(
						'contact/form-card',
						bss_merge(
							array( 'margin' => bss_box( 24, 0, 0, 0 ) ),
							bss_pad( array( 28 ), null, array( 20 ) ),
							bss_bg( 'bs_sand' ),
							bss_border( 'bs_line', 1, 14 )
						),
						array(
							bss_widget( 'contact/gravity-form', 'shortcode', array( 'shortcode' => $form_shortcode, '_css_classes' => 'bs-gf' ) ),
						)
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'contact/details-col',
				42,
				array(
					bss_container(
						'contact/details-card',
						bss_merge(
							bss_pad( array( 28, 28, 30, 28 ), null, array( 22, 20, 24, 20 ) ),
							bss_border( 'bs_line', 1, 14 )
						),
						array(
							bss_eyebrow( 'contact/details-eyebrow', 'The yard', 'primary' ),
							bss_heading( 'contact/details-title', 'Bali Sands', 'h3', 'bs_h3', 'bs_ink', bss_mt( 10 ) ),
							bss_container(
								'contact/details',
								array(
									'flex_gap' => bss_gap( 10 ),
									'margin'   => bss_box( 18, 0, 0, 0 ),
								),
								array(
									bss_detail_row( 'contact/address', 'Address', esc_html( $biz['street'] ) . '<br>' . esc_html( $biz['suburb'] ) ),
									bss_detail_row( 'contact/phone', 'Phone', '<a href="' . esc_attr( $biz['tel'] ) . '">' . esc_html( $biz['phone'] ) . '</a>' ),
									bss_detail_row( 'contact/hours-week', 'Mon – Sat', '6:00am – 4:30pm' ),
									bss_detail_row( 'contact/hours-sun', 'Sun &amp; public hols', '8:00am – 2:00pm' ),
									bss_detail_row( 'contact/abn', 'ABN', esc_html( $biz['abn'] ) ),
								)
							),
							bss_button_row(
								'contact/details-buttons',
								array(
									bss_button( 'contact/btn-call', 'Call ' . $biz['phone'], $biz['tel'], 'green' ),
									bss_button( 'contact/btn-directions', 'Get directions', $biz['maps'], 'outline', array( 'link' => bss_link( $biz['maps'], true ) ) ),
								),
								24
							),
						)
					),
					bss_container(
						'contact/delivery-note',
						bss_merge(
							array( 'margin' => bss_box( 18, 0, 0, 0 ) ),
							bss_pad( array( 20, 24, 22, 24 ) ),
							bss_bg( 'bs_sand' ),
							array( 'border_radius' => bss_box( 12 ) )
						),
						array(
							bss_heading( 'contact/delivery-title', 'Need it delivered?', 'h3', 'bs_card_lg', 'bs_ink' ),
							bss_heading( 'contact/delivery-text', esc_html( $contact['delivery'] ), 'p', 'bs_card_text', 'text', bss_mt( 6 ) ),
							bss_text_link( 'contact/delivery-link', 'Delivery details →', bss_page_url( 'delivery' ), bss_mt( 14 ) ),
						)
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
		)
	);

	/* Find us ------------------------------------------------------------- */

	$map = bss_section(
		'contact/map',
		'bs_sand',
		bss_merge(
			array( '_element_id' => 'visit' ),
			// Waves top and bottom: in from the white section, out into the footer (Ink).
			bss_pad( array( 100, 40, 124, 40 ), array( 92, 24, 111, 24 ), array( 56, 16, 70, 16 ) ),
			bss_wave_top( 'bs_white', true ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_split_header(
				'contact/map-header',
				array(
					bss_eyebrow( 'contact/map-eyebrow', 'Find us', 'secondary' ),
					bss_heading( 'contact/map-title', esc_html( $contact['map_title'] ), 'h2', 'bs_h2', 'bs_ink', bss_mt( 10 ) ),
				),
				bss_text_link( 'contact/map-link', 'Open in Google Maps →', $biz['maps'], array( 'link' => bss_link( $biz['maps'], true ) ) )
			),
			bss_widget(
				'contact/map-embed',
				'google_maps',
				array(
					'address'       => $biz['address'],
					'zoom'          => bss_size( 14 ),
					'height'        => bss_size( 420 ),
					'height_mobile' => bss_size( 300 ),
					'_css_classes'  => 'bs-map',
				)
			),
		)
	);

	return array( $intro, $form, $map );
}

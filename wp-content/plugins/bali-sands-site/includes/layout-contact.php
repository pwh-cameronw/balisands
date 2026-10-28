<?php
/**
 * Contact page (design 10a, "Contact us — call, visit or send an enquiry") as
 * Elementor data, plus the opening-hours "today" highlight.
 *
 * Intro with three ways to reach the yard (call, email, visit) → the enquiry
 * form (Gravity Forms "Contact enquiry": topic pills, fields that change with
 * the topic) beside opening hours and a trade customers card → "Getting here"
 * with a map. As on the other pages, every heading, paragraph and button is
 * its own widget and every section's background is a Global Color.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bss_contact_assets' );
add_filter( 'gform_submit_button', 'bss_contact_submit_note', 10, 2 );

/**
 * Opening hours styles, and the script that highlights today's row (Perth time).
 */
function bss_contact_assets() {
	wp_enqueue_style( 'bss-contact', BSS_URL . 'assets/css/contact.css', array(), bss_asset_ver( 'assets/css/contact.css' ) );
	wp_enqueue_script( 'bss-hours', BSS_URL . 'assets/js/hours.js', array(), bss_asset_ver( 'assets/js/hours.js' ), true );
}

/**
 * "We reply the same business day." beside the Contact enquiry form's button.
 *
 * @param string $button Button HTML.
 * @param array  $form   Form.
 * @return string
 */
function bss_contact_submit_note( $button, $form ) {
	if ( (int) $form['id'] !== (int) get_option( 'bss_form_contact' ) ) {
		return $button;
	}
	return '<span class="bs-gf-note">We reply the same business day.</span>' . $button;
}

/**
 * One of the three "ways to reach us" cards.
 *
 * @param string $seed   Id seed.
 * @param string $label  Eyebrow label.
 * @param string $value  Main line (HTML allowed).
 * @param string $font   Global font id for the main line.
 * @param string $text   Supporting text ('' = none).
 * @param array  $button Button widget.
 * @return array
 */
function bss_contact_card( $seed, $label, $value, $font, $text, $button ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'border_radius' => bss_box( 14 ),
				'css_classes'   => 'bs-contact-card',
			),
			bss_pad( array( 22, 24 ) ),
			bss_bg( 'bs_white' )
		),
		array(
			bss_eyebrow( $seed . '/label', $label, 'secondary' ),
			bss_heading( $seed . '/value', $value, 'div', $font, 'bs_ink', bss_mt( 12 ) ),
			'' !== $text ? bss_heading( $seed . '/text', $text, 'p', 'bs_card_text', 'text', bss_mt( 6 ) ) : null,
			$button,
		)
	);
}

/**
 * The Contact page elements, top to bottom.
 *
 * @param int $form_id Gravity Forms "Contact enquiry" form id (0 = not created yet).
 * @return array
 */
function bss_contact_elements( $form_id ) {
	$biz     = bss_business();
	$contact = bss_page_url( 'contact' );

	/* Intro: three ways to reach us ---------------------------------------- */

	$crumbs = sprintf(
		'<p><a href="%s">Home</a> / <strong>Contact us</strong></p>',
		esc_url( home_url( '/' ) )
	);

	$button_gap = array( '_margin' => bss_box( 16, 0, 0, 0 ) );

	$cards = array(
		bss_contact_card(
			'contact/call',
			'Call the yard',
			'<a href="' . esc_attr( $biz['tel'] ) . '">' . esc_html( $biz['phone'] ) . '</a>',
			'bs_h3',
			'Best for delivery bookings and stock checks.',
			bss_button( 'contact/call-btn', 'Call now', $biz['tel'], 'amber', bss_merge( $button_gap, array( 'text_padding' => bss_box( 13, 18 ) ) ) )
		),
		bss_contact_card(
			'contact/email',
			'Email us',
			'<a href="mailto:' . esc_attr( $biz['email'] ) . '">[' . esc_html( $biz['email'] ) . ']</a>',
			'bs_phone',
			'Send photos or plans and we\'ll quote from them.',
			bss_button( 'contact/email-btn', 'Send an email', 'mailto:' . $biz['email'], 'outline', bss_merge( $button_gap, array( 'text_padding' => bss_box( 11, 16 ) ) ) )
		),
		bss_contact_card(
			'contact/visit',
			'Visit the yard',
			esc_html( $biz['address'] ),
			'bs_phone',
			'',
			bss_button(
				'contact/visit-btn',
				'Get directions',
				$biz['maps'],
				'outline',
				bss_merge(
					$button_gap,
					array(
						'text_padding' => bss_box( 11, 16 ),
						'link'         => bss_link( $biz['maps'], true ),
					)
				)
			)
		),
	);

	$intro = bss_section(
		'contact/intro',
		'primary',
		bss_merge(
			array( 'css_classes' => 'bs-section bs-section--contact-intro bs-on-dark' ),
			bss_pad( array( 44, 40, 34, 40 ), array( 36, 24, 30, 24 ), array( 28, 16, 26, 16 ) )
		),
		array(
			bss_text( 'contact/crumbs', $crumbs, 'bs_crumb', 'bs_mint', array( '_css_classes' => 'bs-crumbs' ) ),
			bss_heading( 'contact/title', 'Get in touch', 'h1', 'bs_display', 'bs_white', bss_mt( 22 ) ),
			bss_text( 'contact/lead', '<p>The quickest way to book a load is to call the yard. For anything else, send us a message and we\'ll get back to you the same business day.</p>', 'bs_lead', 'bs_on_green', bss_merge( bss_mt( 14 ), bss_max_width( 600 ) ) ),
			bss_grid( 'contact/cards', array( 3, 1, 1 ), 16, $cards, array( 'margin' => bss_box( 32, 0, 0, 0 ) ) ),
		)
	);

	/* Enquiry form, opening hours, trade ----------------------------------- */

	$form = bss_container(
		'contact/form-card',
		bss_merge(
			array(
				'_element_id'   => 'enquiry',
				'border_radius' => bss_box( 16 ),
			),
			bss_pad( array( 32 ), array( 28 ), array( 22, 18 ) ),
			bss_bg( 'bs_sand' ),
			bss_border( 'bs_line', 1, 16 )
		),
		array(
			bss_eyebrow( 'contact/form-eyebrow', 'Send an enquiry', 'secondary' ),
			bss_heading( 'contact/form-title', 'What can we help with?', 'h2', 'bs_h2_sm', 'bs_ink', bss_mt( 10 ) ),
			bss_widget(
				'contact/form',
				'shortcode',
				array(
					'shortcode'    => '[gravityform id="' . (int) $form_id . '" title="false" description="false" ajax="true" theme="gravity-theme"]',
					'_css_classes' => 'bs-gf bs-gf--contact',
					'_margin'      => bss_box( 18, 0, 0, 0 ),
				)
			),
		)
	);

	$rows = array();
	foreach ( bss_hours() as $i => $day ) {
		$seed   = 'contact/hours' . $i;
		$rows[] = bss_container(
			$seed,
			array(
				'flex_direction'       => 'row',
				'flex_justify_content' => 'space-between',
				'flex_align_items'     => 'center',
				'flex_gap'             => bss_gap( 12 ),
				'padding'              => bss_box( 9, 10 ),
				'border_radius'        => bss_box( 6 ),
				// bs-day-1 = Monday … bs-day-7 = Sunday: assets/js/hours.js marks today's row.
				'css_classes'          => 'bs-hours__row bs-day-' . ( $i + 1 ),
			),
			array(
				bss_heading( $seed . '/day', $day[0], 'div', 'bs_card_text', 'bs_ink', array( '_element_width' => 'auto', '_css_classes' => 'bs-hours__day' ) ),
				bss_heading( $seed . '/hrs', $day[1], 'div', 'bs_card_text', 'bs_ink', array( '_element_width' => 'auto' ) ),
			)
		);
	}

	$hours = bss_container(
		'contact/hours',
		bss_merge(
			bss_pad( array( 24 ) ),
			bss_border( 'bs_line', 1, 16 ),
			array( 'css_classes' => 'bs-hours' )
		),
		array(
			bss_container(
				'contact/hours-head',
				array(
					'flex_direction'       => 'row',
					'flex_justify_content' => 'space-between',
					'flex_align_items'     => 'center',
					'flex_wrap'            => 'wrap',
					'flex_gap'             => bss_gap( 12, 6 ),
					'margin'               => bss_box( 0, 0, 14, 0 ),
				),
				array(
					bss_heading( 'contact/hours-title', 'Opening hours', 'h2', 'bs_card_lg', 'bs_ink', array( '_element_width' => 'auto' ) ),
					bss_heading( 'contact/hours-open', 'Open 7 days', 'div', 'bs_badge', 'primary', array( '_element_width' => 'auto', '_css_classes' => 'bs-open-dot' ) ),
				)
			),
			bss_container( 'contact/hours-rows', array(), $rows ),
			bss_text( 'contact/hours-note', '<p>Public holidays 8:00am – 2:00pm. Closed [Christmas Day, Good Friday].</p>', 'bs_small', 'bs_muted', array( '_padding' => bss_box( 10, 10, 0, 10 ) ) ),
		)
	);

	$trade = bss_container(
		'contact/trade',
		bss_merge(
			bss_pad( array( 24 ) ),
			bss_bg( 'bs_sand' ),
			bss_border( 'bs_line', 1, 16 )
		),
		array(
			bss_heading( 'contact/trade-title', 'Trade customers', 'h2', 'bs_card_lg', 'bs_ink' ),
			bss_text( 'contact/trade-text', '<p>Landscapers, builders and nurseries can apply for a trade account.</p>', 'bs_card_text', 'text', bss_mt( 8 ) ),
			// Picks "Trade account" in the form (the topic field is filled from ?topic=).
			bss_text_link( 'contact/trade-link', 'Apply for trade access →', add_query_arg( 'topic', rawurlencode( 'Trade account' ), $contact ) . '#enquiry', bss_mt( 12 ) ),
		)
	);

	$enquiry = bss_section(
		'contact/enquiry',
		'bs_white',
		bss_merge(
			array(
				'flex_direction'        => 'row',
				'flex_direction_tablet' => 'column',
				'flex_align_items'      => 'flex-start',
				'flex_gap'              => bss_gap( 40, 24 ),
			),
			bss_section_pad( 40, 64, true ),
			bss_wave_top( 'primary' )
		),
		array(
			bss_column( 'contact/enquiry-form', 62, array( $form ), array( 'width_tablet' => bss_size( 100, '%' ) ) ),
			bss_column(
				'contact/enquiry-side',
				38,
				array( $hours, $trade ),
				array(
					'width_tablet' => bss_size( 100, '%' ),
					'flex_gap'     => bss_gap( 16 ),
				)
			),
		)
	);

	/* Getting here ---------------------------------------------------------- */

	$steps = array(
		'[About 25 minutes from Perth CBD via Tonkin Hwy — confirm].',
		'Drive straight in. There\'s plenty of room for trailers, utes and trucks.',
		'Head to the counter and we\'ll load you up with the loader.',
	);
	$step_rows = array();
	foreach ( $steps as $i => $step ) {
		$seed        = 'contact/step' . $i;
		$step_rows[] = bss_container(
			$seed,
			array(
				'flex_direction' => 'row',
				'flex_wrap'      => 'nowrap',
				'flex_gap'       => bss_gap( 12 ),
			),
			array(
				bss_heading(
					$seed . '/n',
					(string) ( $i + 1 ),
					'div',
					'bs_card_lg',
					'bs_label_warm',
					array(
						'_element_width'        => 'initial',
						'_element_custom_width' => bss_size( 20 ),
						'_padding'              => bss_box( 2, 0, 0, 0 ),
					)
				),
				bss_text( $seed . '/text', '<p>' . esc_html( $step ) . '</p>', 'text', 'bs_white', array( '_element_width' => 'auto' ) ),
			)
		);
	}

	$getting_here = bss_section(
		'contact/here',
		'secondary',
		bss_merge(
			array(
				'flex_direction'          => 'row',
				'flex_direction_tablet'   => 'column',
				'flex_align_items'        => 'center',
				'flex_align_items_tablet' => 'stretch',
				'flex_gap'                => bss_gap( 40, 28 ),
				'css_classes'             => 'bs-section bs-section--contact-here bs-on-dark',
			),
			// Extra room top and bottom for the waves (in from the white section, out into the footer).
			bss_pad( array( 84, 40, 112, 40 ), array( 80, 24, 100, 24 ), array( 52, 16, 72, 16 ) ),
			bss_wave_top( 'bs_white', true ),
			bss_wave_bottom( 'bs_ink' )
		),
		array(
			bss_column(
				'contact/here-text',
				45,
				array(
					bss_eyebrow( 'contact/here-eyebrow', 'Getting here', 'bs_label_warm' ),
					bss_heading( 'contact/here-title', esc_html( $biz['street'] . ', Forrestdale' ), 'h2', 'bs_h2_sm', 'bs_white', bss_mt( 10 ) ),
					bss_container(
						'contact/steps',
						array(
							'flex_gap' => bss_gap( 12 ),
							'margin'   => bss_box( 20, 0, 0, 0 ),
						),
						$step_rows
					),
					bss_button_row(
						'contact/here-buttons',
						array(
							bss_button( 'contact/btn-maps', 'Open in Google Maps', $biz['maps'], 'white', array( 'link' => bss_link( $biz['maps'], true ) ) ),
							bss_button( 'contact/btn-delivery', 'Delivery areas', bss_page_url( 'delivery' ), 'outline-light' ),
						),
						24
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
			bss_column(
				'contact/here-map',
				55,
				array(
					bss_widget(
						'contact/map',
						'google_maps',
						array(
							'address'       => $biz['address'],
							'zoom'          => bss_size( 14 ),
							'height'        => bss_size( 360 ),
							'height_mobile' => bss_size( 280 ),
							'_css_classes'  => 'bs-map',
						)
					),
				),
				array( 'width_tablet' => bss_size( 100, '%' ) )
			),
		)
	);

	return array( $intro, $enquiry, $getting_here );
}

<?php
/**
 * ACF Pro field groups and the Yard details options page.
 *
 * Everything is read with get_post_meta()/get_option(), so these only add
 * the editing UI; the front end does not depend on ACF being active.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'bs_register_acf' );

/**
 * Register the options page and local field groups.
 */
function bs_register_acf() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Yard details', 'bali-sands' ),
				'menu_title' => __( 'Yard details', 'bali-sands' ),
				'menu_slug'  => 'bs-yard',
				'capability' => 'edit_theme_options',
				'icon_url'   => 'dashicons-store',
				'position'   => 22,
			)
		);
	}

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$d = bs_yard_defaults();

	acf_add_local_field_group(
		array(
			'key'      => 'group_bs_yard',
			'title'    => __( 'Yard details', 'bali-sands' ),
			'fields'   => array(
				bs_acf_tab( 'contact', 'Contact' ),
				bs_acf_text( 'bs_phone', 'Phone', $d['bs_phone'] ),
				bs_acf_field( 'bs_email', 'Enquiry email', 'email', array( 'instructions' => 'Quote form notifications go here.' ) ),
				bs_acf_text( 'bs_street', 'Street address', $d['bs_street'] ),
				bs_acf_text( 'bs_locality', 'Suburb, state and postcode', $d['bs_locality'] ),
				bs_acf_text( 'bs_address_short', 'Short address (photo card)', $d['bs_address_short'] ),
				bs_acf_field( 'bs_maps_url', 'Google Maps link', 'url', array( 'placeholder' => $d['bs_maps_url'] ) ),
				bs_acf_text( 'bs_abn', 'ABN', $d['bs_abn'] ),
				bs_acf_tab( 'hours', 'Opening hours' ),
				bs_acf_time( 'bs_week_open', 'Mon–Sat opens' ),
				bs_acf_time( 'bs_week_close', 'Mon–Sat closes' ),
				bs_acf_time( 'bs_sun_open', 'Sun & public holidays opens' ),
				bs_acf_time( 'bs_sun_close', 'Sun & public holidays closes' ),
				bs_acf_text( 'bs_timezone', 'Timezone', $d['bs_timezone'], 'Used for the "Open today" badge.' ),
				bs_acf_tab( 'site', 'Header & footer' ),
				bs_acf_field( 'bs_topbar_left', 'Green bar, left', 'textarea', array( 'rows' => 2, 'placeholder' => $d['bs_topbar_left'], 'instructions' => 'One item per line.' ) ),
				bs_acf_field( 'bs_topbar_right', 'Green bar, right', 'textarea', array( 'rows' => 2, 'placeholder' => $d['bs_topbar_right'], 'instructions' => 'One item per line. The phone number is added after these.' ) ),
				bs_acf_field( 'bs_footer_blurb', 'Footer blurb', 'textarea', array( 'rows' => 3, 'placeholder' => $d['bs_footer_blurb'] ) ),
				bs_acf_field( 'bs_tipper_m3', 'Tipper load size (m³)', 'number', array( 'placeholder' => $d['bs_tipper_m3'], 'instructions' => 'Used by the calculator to work out how many loads.' ) ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'bs-yard',
					),
				),
			),
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_bs_range',
			'title'    => __( 'Tile', 'bali-sands' ),
			'fields'   => array(
				bs_acf_field( 'bs_swatch_from', 'Swatch colour (top)', 'color_picker', array( 'instructions' => 'Shown when the range has no featured image.' ) ),
				bs_acf_field( 'bs_swatch_to', 'Swatch colour (bottom)', 'color_picker' ),
				bs_acf_field( 'bs_link', 'Link', 'url', array( 'instructions' => 'Category page for this range. Leave empty until the page exists.' ) ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'bs_range',
					),
				),
			),
			'position' => 'side',
		)
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_bs_menu_item',
			'title'    => __( 'Dropdown', 'bali-sands' ),
			'fields'   => array(
				bs_acf_field( 'bs_thumb', 'Thumbnail', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'instructions' => 'Dropdown rows. The row text under the name is the menu item Description.' ) ),
				bs_acf_field( 'bs_swatch_from', 'Swatch colour (top)', 'color_picker', array( 'instructions' => 'Used when there is no thumbnail.' ) ),
				bs_acf_field( 'bs_swatch_to', 'Swatch colour (bottom)', 'color_picker' ),
				bs_acf_text( 'bs_see_all', '"See all" label', '', 'Top-level items only, e.g. See all sands.' ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'nav_menu_item',
						'operator' => '==',
						'value'    => 'all',
					),
				),
			),
		)
	);
}

/**
 * Build an ACF field array.
 *
 * @param string $name  Field name (also the meta key).
 * @param string $label Label.
 * @param string $type  ACF field type.
 * @param array  $extra Extra settings.
 * @return array
 */
function bs_acf_field( $name, $label, $type, $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/**
 * @param string $name        Field name.
 * @param string $label       Label.
 * @param string $placeholder Placeholder (the default value).
 * @param string $help        Instructions.
 * @return array
 */
function bs_acf_text( $name, $label, $placeholder = '', $help = '' ) {
	return bs_acf_field(
		$name,
		$label,
		'text',
		array(
			'placeholder'  => $placeholder,
			'instructions' => $help,
		)
	);
}

/**
 * @param string $name  Field name.
 * @param string $label Label.
 * @return array
 */
function bs_acf_time( $name, $label ) {
	$d = bs_yard_defaults();
	return bs_acf_field(
		$name,
		$label,
		'time_picker',
		array(
			'display_format' => 'g:i a',
			'return_format'  => 'H:i',
			'instructions'   => 'Default ' . bs_format_minutes( bs_time_to_minutes( $d[ $name ] ) ),
			'wrapper'        => array( 'width' => '50' ),
		)
	);
}

/**
 * @param string $slug  Tab key suffix.
 * @param string $label Label.
 * @return array
 */
function bs_acf_tab( $slug, $label ) {
	return array(
		'key'   => 'field_bs_tab_' . $slug,
		'label' => $label,
		'type'  => 'tab',
	);
}

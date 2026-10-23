<?php
/**
 * Setup step: the site header as an Elementor Pro Theme Builder template.
 *
 * Green top bar (two Icon List widgets), Site Logo + Site Title, and the
 * BS Header menu widget, shown on the entire site.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

/**
 * Header templates (other than ours) that are set to show anywhere.
 *
 * @return WP_Post[]
 */
function bs_other_active_headers() {
	$ours = (int) get_option( 'bs_header_template_id' );
	$out  = array();
	$posts = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_elementor_template_type', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 'header', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	foreach ( $posts as $post ) {
		if ( $post->ID !== $ours && get_post_meta( $post->ID, '_elementor_conditions', true ) ) {
			$out[] = $post;
		}
	}
	return $out;
}

/**
 * Create (or rebuild) the header template and show it site-wide.
 *
 * @param bool $rebuild Overwrite our header's Elementor data.
 * @param bool $replace Take display conditions off other header templates.
 * @return string Log line.
 */
function bs_setup_elementor_header( $rebuild, $replace ) {
	if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		return 'Elementor header skipped: Elementor Pro is not active. The theme\'s built-in header is used instead.';
	}

	$id     = (int) get_option( 'bs_header_template_id' );
	$exists = $id && get_post( $id );
	if ( ! $exists ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'post_title'  => 'Bali Sands header',
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return 'Elementor header could not be created.';
		}
		update_option( 'bs_header_template_id', $id );
		wp_set_object_terms( $id, 'header', 'elementor_library_type' );
		update_post_meta( $id, '_elementor_template_type', 'header' );
	}

	if ( ! $exists || $rebuild ) {
		update_post_meta( $id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( bs_header_elements() ) ) );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
		}
		update_post_meta( $id, '_elementor_pro_version', ELEMENTOR_PRO_VERSION );
		delete_post_meta( $id, '_elementor_css' );
	}

	$replaced = array();
	if ( $replace ) {
		foreach ( bs_other_active_headers() as $other ) {
			delete_post_meta( $other->ID, '_elementor_conditions' );
			$replaced[] = '"' . $other->post_title . '"';
		}
	}
	update_post_meta( $id, '_elementor_conditions', array( 'include/general' ) );
	bs_regenerate_theme_conditions();

	$line = $exists ? ( $rebuild ? 'Elementor header: rebuilt' : 'Elementor header: already exists, layout left as it is' ) : 'Elementor header: created';
	$line .= ' (Templates → Theme Builder → Header → "Bali Sands header"), shown on the entire site.';
	if ( $replaced ) {
		$line .= ' Turned off the old header ' . implode( ', ', $replaced ) . ': it is still under Templates if you want it back.';
	} elseif ( ! $replace && bs_other_active_headers() ) {
		$line .= ' Another header template also has display conditions, so Elementor may show that one instead.';
	}
	return $line;
}

/**
 * Rebuild Elementor Pro's display-conditions cache after editing conditions directly.
 */
function bs_regenerate_theme_conditions() {
	$module = '\ElementorPro\Modules\ThemeBuilder\Module';
	if ( class_exists( $module ) ) {
		$manager = $module::instance()->get_conditions_manager();
		if ( method_exists( $manager, 'get_cache' ) ) {
			$cache = $manager->get_cache();
			if ( method_exists( $cache, 'regenerate' ) ) {
				$cache->regenerate();
				return;
			}
		}
	}
	// Fallback: write the cache in the shape Elementor Pro stores it.
	$cache = array();
	$posts = get_posts(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_key'       => '_elementor_conditions', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $posts as $post ) {
		$type       = get_post_meta( $post->ID, '_elementor_template_type', true );
		$conditions = get_post_meta( $post->ID, '_elementor_conditions', true );
		if ( $type && $conditions ) {
			$cache[ $type ][ $post->ID ] = $conditions;
		}
	}
	update_option( 'elementor_pro_theme_builder_conditions', $cache );
}

/**
 * Responsive padding helper: [top, right, bottom, left] in px.
 *
 * @param int[] $p Sides.
 * @return array
 */
function bs_el_pad( $p ) {
	return array(
		'unit'     => 'px',
		'top'      => (string) $p[0],
		'right'    => (string) $p[1],
		'bottom'   => (string) $p[2],
		'left'     => (string) $p[3],
		'isLinked' => false,
	);
}

/**
 * Typography settings for an Elementor control group.
 *
 * @param string $prefix Group prefix, e.g. "typography" or "icon_typography".
 * @param string $weight Font weight.
 * @param float  $size   Font size (px).
 * @param array  $extra  Extra settings (keys without prefix).
 * @return array
 */
function bs_el_type( $prefix, $weight, $size, $extra = array() ) {
	$out = array(
		$prefix . '_typography'  => 'custom',
		$prefix . '_font_family' => 'Open Sans',
		$prefix . '_font_weight' => $weight,
		$prefix . '_font_size'   => array(
			'unit'  => 'px',
			'size'  => $size,
			'sizes' => array(),
		),
		$prefix . '_line_height' => array(
			'unit'  => 'em',
			'size'  => 1,
			'sizes' => array(),
		),
	);
	foreach ( $extra as $k => $v ) {
		$out[ $prefix . '_' . $k ] = $v;
	}
	return $out;
}

/**
 * One inline Icon List for the green bar.
 *
 * @param string $seed  Id seed.
 * @param array  $items [text, url] pairs.
 * @param string $side  left|right.
 * @return array
 */
function bs_el_topbar_list( $seed, $items, $side ) {
	$rows = array();
	foreach ( $items as $i => $item ) {
		$rows[] = array(
			'_id'           => bs_el_id( $seed . $i ),
			'text'          => $item[0],
			'selected_icon' => array(
				'value'   => '',
				'library' => '',
			),
			'link'          => array(
				'url'         => $item[1],
				'is_external' => '',
				'nofollow'    => '',
			),
		);
	}
	return array(
		'id'         => bs_el_id( $seed ),
		'elType'     => 'widget',
		'widgetType' => 'icon-list',
		'settings'   => array_merge(
			array(
				'view'              => 'inline',
				'icon_list'         => $rows,
				'space_between'     => array(
					'unit' => 'px',
					'size' => 44,
				),
				'divider'           => 'yes',
				'divider_style'     => 'solid',
				'divider_weight'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'divider_height'    => array(
					'unit' => 'px',
					'size' => 13,
				),
				'divider_color'     => 'rgba(238,242,234,0.5)',
				'text_color'        => '#EEF2EA',
				'text_color_hover'  => '#FFFFFF',
				'icon_align'        => 'left' === $side ? 'left' : 'right',
				'icon_align_mobile' => 'center',
				'_css_classes'      => 'bs-tb bs-tb--' . $side,
			),
			bs_el_type( 'icon_typography', '600', 12.5 )
		),
		'elements'   => array(),
	);
}

/**
 * The header template's Elementor elements.
 *
 * @return array
 */
function bs_header_elements() {
	$gap = static function ( $col, $row ) {
		return array(
			'unit'     => 'px',
			'size'     => $col,
			'column'   => (string) $col,
			'row'      => (string) $row,
			'isLinked' => false,
		);
	};

	$left  = array();
	foreach ( bs_lines( bs_yard( 'bs_topbar_left' ) ) as $text ) {
		$left[] = array( $text, '' );
	}
	$right = array();
	foreach ( bs_lines( bs_yard( 'bs_topbar_right' ) ) as $text ) {
		$right[] = array( $text, '' );
	}
	$right[] = array( bs_yard( 'bs_phone' ), bs_tel_href() );

	$topbar = array(
		'id'       => bs_el_id( 'h-topbar' ),
		'elType'   => 'container',
		'settings' => array(
			'content_width'               => 'full',
			'flex_direction'              => 'row',
			'flex_justify_content'        => 'space-between',
			'flex_justify_content_mobile' => 'center',
			'flex_align_items'            => 'center',
			'flex_wrap'                   => 'wrap',
			'flex_gap'                    => $gap( 22, 6 ),
			'padding'                     => bs_el_pad( array( 11, 40, 11, 40 ) ),
			'padding_tablet'              => bs_el_pad( array( 11, 24, 11, 24 ) ),
			'padding_mobile'              => bs_el_pad( array( 9, 16, 9, 16 ) ),
			'background_background'       => 'classic',
			'background_color'            => '#2F4034',
			'css_classes'                 => 'bs-etopbar',
		),
		'elements' => array(
			bs_el_topbar_list( 'h-tb-left', $left, 'left' ),
			bs_el_topbar_list( 'h-tb-right', $right, 'right' ),
		),
		'isInner'  => true,
	);

	$logo = array(
		'id'         => bs_el_id( 'h-logo' ),
		'elType'     => 'widget',
		'widgetType' => 'theme-site-logo',
		'settings'   => array(
			'width'         => array(
				'unit' => 'px',
				'size' => 51,
			),
			'width_mobile'  => array(
				'unit' => 'px',
				'size' => 40,
			),
			'_margin'       => bs_el_pad( array( 16, 0, 16, 0 ) ),
			'_margin_mobile' => bs_el_pad( array( 12, 0, 12, 0 ) ),
			'_css_classes'  => 'bs-ebrand',
		),
		'elements'   => array(),
	);

	$title = array(
		'id'         => bs_el_id( 'h-title' ),
		'elType'     => 'widget',
		'widgetType' => 'theme-site-title',
		'settings'   => array_merge(
			array(
				'header_size' => 'div',
				'title_color' => '#201C17',
			),
			bs_el_type(
				'typography',
				'800',
				22,
				array(
					'text_transform'   => 'uppercase',
					'letter_spacing'   => array(
						'unit' => 'px',
						'size' => 1.3,
					),
					'font_size_mobile' => array(
						'unit' => 'px',
						'size' => 17,
					),
				)
			)
		),
		'elements'   => array(),
	);

	$menu = array(
		'id'         => bs_el_id( 'h-menu' ),
		'elType'     => 'widget',
		'widgetType' => 'bs-header-menu',
		'settings'   => array(
			'menu' => '',
		),
		'elements'   => array(),
	);

	$bar = array(
		'id'       => bs_el_id( 'h-bar' ),
		'elType'   => 'container',
		'settings' => array(
			'content_width'         => 'full',
			'flex_direction'        => 'row',
			'flex_justify_content'  => 'flex-start',
			'flex_align_items'      => 'center',
			'flex_wrap'             => 'nowrap',
			'flex_gap'              => $gap( 11, 0 ),
			'padding'               => bs_el_pad( array( 0, 40, 0, 40 ) ),
			'padding_tablet'        => bs_el_pad( array( 0, 24, 0, 24 ) ),
			'padding_mobile'        => bs_el_pad( array( 0, 16, 0, 16 ) ),
			'background_background' => 'classic',
			'background_color'      => '#FFFFFF',
			'border_border'         => 'solid',
			'border_width'          => bs_el_pad( array( 0, 0, 1, 0 ) ),
			'border_color'          => '#ECE6DA',
			'css_classes'           => 'bs-ebar',
			'z_index'               => 20,
		),
		'elements' => array( $logo, $title, $menu ),
		'isInner'  => true,
	);

	return array(
		array(
			'id'       => bs_el_id( 'h-wrap' ),
			'elType'   => 'container',
			'settings' => array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'flex_gap'       => $gap( 0, 0 ),
				'padding'        => bs_el_pad( array( 0, 0, 0, 0 ) ),
				'css_classes'    => 'bs-eheader',
				'z_index'        => 50,
			),
			'elements' => array( $topbar, $bar ),
			'isInner'  => false,
		),
	);
}

/**
 * Split a textarea value into trimmed, non-empty lines.
 *
 * @param string $text Text.
 * @return string[]
 */
function bs_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) ) );
}

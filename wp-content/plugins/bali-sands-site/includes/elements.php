<?php
/**
 * Small builders for Elementor element data (Flexbox containers and core widgets).
 *
 * Every colour and font is written as a reference to an Elementor Global Color /
 * Global Font (the "__globals__" key), with the current value as a fallback, so
 * changing a global in Site Settings updates every element that uses it.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stable 7-character Elementor element id.
 *
 * @param string $seed Seed.
 * @return string
 */
function bss_id( $seed ) {
	return substr( md5( 'bali-sands-site/' . $seed ), 0, 7 );
}

/**
 * Dimensions control value (padding, margin, border width, radius).
 *
 * @param int|float      $top    Top.
 * @param int|float|null $right  Right (defaults to top).
 * @param int|float|null $bottom Bottom (defaults to top).
 * @param int|float|null $left   Left (defaults to right).
 * @param string         $unit   Unit.
 * @return array
 */
function bss_box( $top, $right = null, $bottom = null, $left = null, $unit = 'px' ) {
	$right  = null === $right ? $top : $right;
	$bottom = null === $bottom ? $top : $bottom;
	$left   = null === $left ? $right : $left;
	return array(
		'unit'     => $unit,
		'top'      => (string) $top,
		'right'    => (string) $right,
		'bottom'   => (string) $bottom,
		'left'     => (string) $left,
		'isLinked' => ( $top === $right && $right === $bottom && $bottom === $left ),
	);
}

/**
 * Slider control value.
 *
 * @param int|float $size Size.
 * @param string    $unit Unit.
 * @return array
 */
function bss_size( $size, $unit = 'px' ) {
	return array(
		'unit'  => $unit,
		'size'  => $size,
		'sizes' => array(),
	);
}

/**
 * Container gap control value.
 *
 * @param int      $column Column gap.
 * @param int|null $row    Row gap (defaults to column gap).
 * @return array
 */
function bss_gap( $column, $row = null ) {
	$row = null === $row ? $column : $row;
	return array(
		'unit'     => 'px',
		'size'     => $column,
		'column'   => (string) $column,
		'row'      => (string) $row,
		'isLinked' => $column === $row,
	);
}

/**
 * Link control value.
 *
 * @param string $url     URL.
 * @param bool   $new_tab Open in a new tab.
 * @return array
 */
function bss_link( $url, $new_tab = false ) {
	return array(
		'url'               => (string) $url,
		'is_external'       => $new_tab ? 'on' : '',
		'nofollow'          => '',
		'custom_attributes' => '',
	);
}

/**
 * Merge settings arrays, combining their "__globals__" maps.
 *
 * @param array ...$parts Settings arrays.
 * @return array
 */
function bss_merge( ...$parts ) {
	$out = array();
	foreach ( $parts as $part ) {
		foreach ( (array) $part as $key => $value ) {
			if ( '__globals__' === $key ) {
				$out['__globals__'] = array_merge( isset( $out['__globals__'] ) ? $out['__globals__'] : array(), $value );
			} else {
				$out[ $key ] = $value;
			}
		}
	}
	return $out;
}

/**
 * A colour control linked to a Global Color.
 *
 * @param string $control Control name, e.g. "title_color".
 * @param string $color   Global colour id from bss_palette().
 * @return array
 */
function bss_color( $control, $color ) {
	$palette = bss_palette();
	return array(
		$control      => isset( $palette[ $color ] ) ? $palette[ $color ][1] : '',
		'__globals__' => array( $control => 'globals/colors?id=' . $color ),
	);
}

/**
 * A typography group linked to a Global Font.
 *
 * @param string $font   Global font id from bss_type_scale().
 * @param string $prefix Group control prefix, e.g. "typography" or "icon_typography".
 * @return array
 */
function bss_font( $font, $prefix = 'typography' ) {
	return array(
		$prefix . '_typography' => 'custom',
		'__globals__'           => array( $prefix . '_typography' => 'globals/typography?id=' . $font ),
	);
}

/**
 * Classic background linked to a Global Color.
 *
 * @param string $color  Global colour id.
 * @param string $prefix Background group prefix.
 * @return array
 */
function bss_bg( $color, $prefix = 'background' ) {
	return bss_merge(
		array( $prefix . '_background' => 'classic' ),
		bss_color( $prefix . '_color', $color )
	);
}

/**
 * Solid border.
 *
 * @param string    $color  Global colour id.
 * @param int|float $width  Width (px).
 * @param int       $radius Radius (px).
 * @param string    $prefix Control prefix ("" for containers, "_" for widget Advanced tab).
 * @return array
 */
function bss_border( $color, $width = 1, $radius = 0, $prefix = '' ) {
	$out = bss_merge(
		array(
			$prefix . 'border_border' => 'solid',
			$prefix . 'border_width'  => bss_box( $width ),
		),
		bss_color( $prefix . 'border_color', $color )
	);
	if ( $radius ) {
		$out[ $prefix . 'border_radius' ] = bss_box( $radius );
	}
	return $out;
}

/**
 * Responsive padding: desktop, tablet, mobile, each [top, right, bottom, left].
 *
 * @param array      $desktop Desktop.
 * @param array|null $tablet  Tablet.
 * @param array|null $mobile  Mobile.
 * @param string     $control Control name.
 * @return array
 */
function bss_pad( $desktop, $tablet = null, $mobile = null, $control = 'padding' ) {
	$out = array( $control => bss_box( ...$desktop ) );
	if ( $tablet ) {
		$out[ $control . '_tablet' ] = bss_box( ...$tablet );
	}
	if ( $mobile ) {
		$out[ $control . '_mobile' ] = bss_box( ...$mobile );
	}
	return $out;
}

/**
 * Top margin for a widget (spacing inside a gap-0 column).
 *
 * @param int $px Pixels.
 * @return array
 */
function bss_mt( $px ) {
	return array( '_margin' => bss_box( $px, 0, 0, 0 ) );
}

/**
 * Widget width in a flex container ("initial" width with a max for desktop, full on mobile).
 *
 * @param int    $px   Width (px).
 * @param string $unit Unit.
 * @return array
 */
function bss_max_width( $px, $unit = 'px' ) {
	return array(
		'_element_width'              => 'initial',
		'_element_custom_width'       => bss_size( $px, $unit ),
		'_element_width_mobile'       => 'initial',
		'_element_custom_width_mobile' => bss_size( 100, '%' ),
	);
}

/**
 * Elementor container.
 *
 * @param string $seed     Id seed.
 * @param array  $settings Settings (merged over full-width, column, no gap, no padding).
 * @param array  $elements Children.
 * @param bool   $inner    Nested container.
 * @return array
 */
function bss_container( $seed, $settings, $elements = array(), $inner = true ) {
	return array(
		'id'       => bss_id( 'c/' . $seed ),
		'elType'   => 'container',
		'settings' => bss_merge(
			array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'flex_gap'       => bss_gap( 0 ),
				'padding'        => bss_box( 0 ),
			),
			$settings
		),
		'elements' => array_values( array_filter( $elements ) ),
		'isInner'  => $inner,
	);
}

/**
 * A top-level page section: full-width background, content boxed to the site width.
 *
 * @param string $seed     Id seed.
 * @param string $bg       Global colour id for the background.
 * @param array  $settings Extra settings.
 * @param array  $elements Children.
 * @return array
 */
function bss_section( $seed, $bg, $settings, $elements ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'content_width' => 'boxed',
				'css_classes'   => 'bs-section bs-section--' . str_replace( '/', '-', $seed ),
			),
			bss_bg( $bg ),
			$settings
		),
		$elements,
		false
	);
}

/**
 * Elementor "waves" shape divider along the top of a section, coloured like the section above.
 *
 * @param string $color Global colour id of the section above.
 * @param bool   $flip  Flip horizontally.
 * @return array
 */
function bss_wave_top( $color, $flip = false ) {
	return bss_merge(
		array(
			'shape_divider_top'               => 'waves',
			'shape_divider_top_width'         => bss_size( 100, '%' ),
			'shape_divider_top_height'        => bss_size( 60 ),
			'shape_divider_top_height_mobile' => bss_size( 32 ),
			'shape_divider_top_flip'          => $flip ? 'yes' : '',
		),
		bss_color( 'shape_divider_top_color', $color )
	);
}

/**
 * Elementor "waves" shape divider along the bottom of a section, coloured like
 * the section below it (on a page's last section: the footer's Ink).
 *
 * @param string $color Global colour id of the section below.
 * @param bool   $flip  Flip horizontally.
 * @return array
 */
function bss_wave_bottom( $color, $flip = false ) {
	return bss_merge(
		array(
			'shape_divider_bottom'               => 'waves',
			'shape_divider_bottom_width'         => bss_size( 100, '%' ),
			'shape_divider_bottom_height'        => bss_size( 60 ),
			'shape_divider_bottom_height_mobile' => bss_size( 32 ),
			'shape_divider_bottom_flip'          => $flip ? 'yes' : '',
		),
		bss_color( 'shape_divider_bottom_color', $color )
	);
}

/**
 * Elementor widget.
 *
 * @param string $seed     Id seed.
 * @param string $type     Widget type.
 * @param array  $settings Settings.
 * @return array
 */
function bss_widget( $seed, $type, $settings ) {
	return array(
		'id'         => bss_id( 'w/' . $seed ),
		'elType'     => 'widget',
		'widgetType' => $type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/**
 * Heading widget.
 *
 * @param string $seed  Id seed.
 * @param string $text  Text (may contain <br>).
 * @param string $tag   h1–h6, div, span, p.
 * @param string $font  Global font id.
 * @param string $color Global colour id.
 * @param array  $extra Extra settings.
 * @return array
 */
function bss_heading( $seed, $text, $tag, $font, $color, $extra = array() ) {
	return bss_widget(
		$seed,
		'heading',
		bss_merge(
			array(
				'title'       => $text,
				'header_size' => $tag,
			),
			bss_font( $font ),
			bss_color( 'title_color', $color ),
			$extra
		)
	);
}

/**
 * Text editor widget.
 *
 * @param string $seed  Id seed.
 * @param string $html  HTML.
 * @param string $font  Global font id.
 * @param string $color Global colour id.
 * @param array  $extra Extra settings.
 * @return array
 */
function bss_text( $seed, $html, $font, $color, $extra = array() ) {
	return bss_widget(
		$seed,
		'text-editor',
		bss_merge(
			array( 'editor' => $html ),
			bss_font( $font ),
			bss_color( 'text_color', $color ),
			$extra
		)
	);
}

/**
 * Button widget in one of the design's styles.
 *
 * @param string $seed    Id seed.
 * @param string $text    Label.
 * @param string $url     Link.
 * @param string $variant green | amber | terracotta | white | outline | outline-light.
 * @param array  $extra   Extra settings.
 * @return array
 */
function bss_button( $seed, $text, $url, $variant, $extra = array() ) {
	$styles = array(
		'green'         => array( 'primary', 'bs_white', 'primary', 'bs_green_hover', 'bs_white', 'bs_green_hover' ),
		'amber'         => array( 'accent', 'bs_ink', 'accent', 'bs_amber_hover', 'bs_ink', 'bs_amber_hover' ),
		'terracotta'    => array( 'secondary', 'bs_white', 'secondary', 'bs_terracotta_hover', 'bs_white', 'bs_terracotta_hover' ),
		'white'         => array( 'bs_white', 'bs_ink', 'bs_white', 'bs_sand', 'bs_ink', 'bs_sand' ),
		'outline'       => array( 'bs_white', 'bs_ink', 'bs_ink', 'bs_ink', 'bs_white', 'bs_ink' ),
		'outline-light' => array( '', 'bs_white', 'bs_white', 'bs_white', 'primary', 'bs_white' ),
	);
	list( $bg, $fg, $border, $hover_bg, $hover_fg, $hover_border ) = $styles[ $variant ];
	$outlined = 0 === strpos( $variant, 'outline' );

	$settings = bss_merge(
		array(
			'text'          => $text,
			'link'          => bss_link( $url ),
			'border_radius' => bss_box( 6 ),
			'text_padding'  => $outlined ? bss_box( 16, 24 ) : bss_box( 18, 26 ),
			'border_border' => 'solid',
			'border_width'  => $outlined ? bss_box( 2 ) : bss_box( 0 ),
		),
		bss_font( 'accent' ),
		bss_color( 'button_text_color', $fg ),
		bss_color( 'border_color', $border ),
		bss_color( 'hover_color', $hover_fg ),
		bss_color( 'button_hover_border_color', $hover_border ),
		array( 'button_background_hover_background' => 'classic' ),
		bss_color( 'button_background_hover_color', $hover_bg ),
		$bg ? bss_bg( $bg ) : array(
			'background_background' => 'classic',
			'background_color'      => 'rgba(255,255,255,0)',
		),
		$extra
	);

	return bss_widget( $seed, 'button', $settings );
}

/**
 * Image widget.
 *
 * @param string $seed   Id seed.
 * @param int    $att_id Attachment ID (0 = none).
 * @param array  $extra  Extra settings.
 * @return array
 */
function bss_image( $seed, $att_id, $extra = array() ) {
	$url = $att_id ? wp_get_attachment_image_url( $att_id, 'full' ) : '';
	return bss_widget(
		$seed,
		'image',
		bss_merge(
			array(
				'image'      => array(
					'url' => $url ? $url : '',
					'id'  => $att_id ? $att_id : '',
					'alt' => '',
				),
				'image_size' => 'medium_large',
			),
			$extra
		)
	);
}

/**
 * Icon List widget (inline or stacked).
 *
 * @param string $seed  Id seed.
 * @param array  $items [text, url, icon] rows ('' icon = none).
 * @param array  $extra Extra settings.
 * @return array
 */
function bss_icon_list( $seed, $items, $extra = array() ) {
	$rows = array();
	foreach ( $items as $i => $item ) {
		$rows[] = array(
			'_id'           => bss_id( $seed . '/row' . $i ),
			'text'          => $item[0],
			'link'          => bss_link( isset( $item[1] ) ? $item[1] : '' ),
			'selected_icon' => array(
				'value'   => isset( $item[2] ) ? $item[2] : '',
				'library' => ! empty( $item[2] ) ? 'fa-solid' : '',
			),
		);
	}
	return bss_widget(
		$seed,
		'icon-list',
		bss_merge(
			array( 'icon_list' => $rows ),
			$extra
		)
	);
}

/**
 * Row container with a heading block on the left and a text link on the right
 * (the "eyebrow + H2 … View all →" headers).
 *
 * @param string     $seed  Id seed.
 * @param array      $left  Left-hand widgets.
 * @param array|null $right Right-hand widget (sized to its content).
 * @return array
 */
function bss_split_header( $seed, $left, $right ) {
	if ( $right ) {
		$right['settings'] = bss_merge( $right['settings'], array( '_element_width' => 'auto' ) );
	}
	return bss_container(
		$seed,
		array(
			'flex_direction'          => 'row',
			'flex_direction_mobile'   => 'column',
			'flex_justify_content'    => 'space-between',
			'flex_align_items'        => 'flex-end',
			'flex_align_items_mobile' => 'flex-start',
			'flex_wrap'               => 'nowrap',
			'flex_gap'                => bss_gap( 24, 14 ),
			'margin'                  => bss_box( 0, 0, 24, 0 ),
		),
		array(
			bss_container(
				$seed . '/left',
				array(
					'width'        => bss_size( 75, '%' ),
					'width_mobile' => bss_size( 100, '%' ),
				),
				$left
			),
			$right,
		)
	);
}

/**
 * CSS-grid container.
 *
 * @param string $seed     Id seed.
 * @param int[]  $cols     Columns: [desktop, tablet, mobile].
 * @param int    $gap      Gap (px).
 * @param array  $elements Children.
 * @param array  $extra    Extra settings.
 * @return array
 */
function bss_grid( $seed, $cols, $gap, $elements, $extra = array() ) {
	return bss_container(
		$seed,
		bss_merge(
			array(
				'container_type'           => 'grid',
				'grid_columns_grid'        => bss_size( $cols[0], 'fr' ),
				'grid_columns_grid_tablet' => bss_size( $cols[1], 'fr' ),
				'grid_columns_grid_mobile' => bss_size( $cols[2], 'fr' ),
				'grid_rows_grid'           => bss_size( (int) ceil( count( $elements ) / $cols[0] ), 'fr' ),
				'grid_rows_grid_tablet'    => bss_size( (int) ceil( count( $elements ) / $cols[1] ), 'fr' ),
				'grid_rows_grid_mobile'    => bss_size( (int) ceil( count( $elements ) / $cols[2] ), 'fr' ),
				'grid_gaps'                => array(
					'unit'     => 'px',
					'size'     => $gap,
					'column'   => (string) $gap,
					'row'      => (string) $gap,
					'isLinked' => true,
				),
				'grid_auto_flow'           => 'row',
			),
			$extra
		),
		$elements
	);
}

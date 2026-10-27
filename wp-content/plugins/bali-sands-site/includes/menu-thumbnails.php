<?php
/**
 * Menu images: shows each category page's featured image (and its menu description)
 * beside it in the menu.
 *
 * Works with any menu rendered through wp_nav_menu(), including the JetMenu
 * "Mega Menu" widget and Elementor Pro's "Nav Menu" widget.
 *
 * Rules (Appearance → Menus):
 * - Sub-menu items that link to a page (or any post) with a featured image get the image.
 * - The item's Description (turn it on under Screen Options) shows under the label.
 * - Add the CSS class "bs-thumb" to a top-level item to give it an image too.
 * - Add the CSS class "bs-no-thumb" to any item to leave its image off.
 * - The CSS class "bs-see-all" styles an item as the "See all …" link at the foot of a dropdown.
 * To change an image, change the page's featured image.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wp_nav_menu_objects', 'bss_menu_thumbnails', 20, 2 );
add_action( 'wp_enqueue_scripts', 'bss_menu_thumbnail_styles' );

/**
 * Prepend the featured image to qualifying menu item titles.
 *
 * @param WP_Post[] $items Menu items.
 * @param stdClass  $args  wp_nav_menu() arguments.
 * @return WP_Post[]
 */
function bss_menu_thumbnails( $items, $args ) {
	if ( is_admin() ) {
		return $items;
	}

	foreach ( $items as $item ) {
		$classes = array_filter( (array) $item->classes );
		if ( in_array( 'bs-no-thumb', $classes, true ) || false !== strpos( $item->title, 'bs-menu-thumb' ) ) {
			continue;
		}
		if ( ! $item->menu_item_parent && ! in_array( 'bs-thumb', $classes, true ) ) {
			continue;
		}

		$thumb_id = 'post_type' === $item->type ? (int) get_post_thumbnail_id( (int) $item->object_id ) : 0;

		/**
		 * Filter the attachment shown beside a menu item. Return 0 to show none.
		 *
		 * @param int      $thumb_id Attachment ID.
		 * @param WP_Post  $item     Menu item.
		 * @param stdClass $args     wp_nav_menu() arguments.
		 */
		$thumb_id = (int) apply_filters( 'bss_menu_thumbnail_id', $thumb_id, $item, $args );
		if ( ! $thumb_id ) {
			continue;
		}

		$img = wp_get_attachment_image(
			$thumb_id,
			'thumbnail',
			false,
			array(
				'class'       => 'bs-menu-thumb__img',
				'alt'         => '',
				'aria-hidden' => 'true',
				'loading'     => 'lazy',
			)
		);
		if ( ! $img ) {
			continue;
		}

		$desc = trim( (string) $item->description );
		$text = '<span class="bs-menu-thumb__label">' . $item->title . '</span>';
		if ( '' !== $desc ) {
			$text .= '<span class="bs-menu-thumb__desc">' . esc_html( $desc ) . '</span>';
		}

		$item->title     = '<span class="bs-menu-thumb">' . $img . '</span><span class="bs-menu-thumb__text">' . $text . '</span>';
		$item->classes[] = 'bs-has-thumb';

		// The description now sits beside the image; clear it so JetMenu doesn't print it again underneath.
		$item->description = '';
	}

	return $items;
}

/**
 * Menu image and dropdown panel styles.
 */
function bss_menu_thumbnail_styles() {
	wp_enqueue_style( 'bss-menu', BSS_URL . 'assets/css/menu.css', array(), bss_asset_ver( 'assets/css/menu.css' ) );
}

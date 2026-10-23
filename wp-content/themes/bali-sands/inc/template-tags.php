<?php
/**
 * Header and footer helpers.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yard detail from the Bali Sands Core plugin, or '' without it.
 *
 * @param string $key Field name.
 * @return string
 */
function bali_sands_yard( $key ) {
	return function_exists( 'bs_yard' ) ? bs_yard( $key ) : '';
}

/**
 * Split a textarea option into trimmed, non-empty lines.
 *
 * @param string $text Text.
 * @return string[]
 */
function bali_sands_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $text ) ) ) );
}

/**
 * Two-level tree of a menu location: top items with their children.
 *
 * @param string $location Theme location.
 * @return array<int,array{item:WP_Post,children:WP_Post[]}>
 */
function bali_sands_menu_tree( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $locations[ $location ], array( 'update_post_term_cache' => false ) );
	if ( ! $items ) {
		return array();
	}
	_wp_menu_item_classes_by_context( $items );

	$tree = array();
	foreach ( $items as $item ) {
		if ( ! (int) $item->menu_item_parent ) {
			$tree[ $item->ID ] = array(
				'item'     => $item,
				'children' => array(),
			);
		}
	}
	foreach ( $items as $item ) {
		$parent = (int) $item->menu_item_parent;
		if ( $parent && isset( $tree[ $parent ] ) ) {
			$tree[ $parent ]['children'][] = $item;
		}
	}
	return array_values( $tree );
}

/**
 * CSS background for a dropdown row thumbnail: photo, swatch or plain.
 *
 * @param WP_Post $item Menu item.
 * @return string
 */
function bali_sands_thumb_style( $item ) {
	$thumb = (int) get_post_meta( $item->ID, 'bs_thumb', true );
	if ( $thumb ) {
		$url = wp_get_attachment_image_url( $thumb, 'thumbnail' );
		if ( $url ) {
			return "background-image:url('" . esc_url( $url ) . "')";
		}
	}
	$from = get_post_meta( $item->ID, 'bs_swatch_from', true );
	$to   = get_post_meta( $item->ID, 'bs_swatch_to', true );
	if ( $from && $to ) {
		return sprintf( 'background-image:linear-gradient(150deg,%s,%s)', $from, $to );
	}
	return '';
}

/**
 * Print the header navigation (desktop dropdowns and the mobile drawer share it).
 */
function bali_sands_nav() {
	$tree = bali_sands_menu_tree( 'primary' );
	if ( ! $tree ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			printf( '<a class="bs-nav__link" href="%s">%s</a>', esc_url( admin_url( 'nav-menus.php?action=locations' ) ), esc_html__( 'Assign a header menu', 'bali-sands' ) );
		}
		return;
	}
	echo '<ul class="bs-nav__list">';
	foreach ( $tree as $node ) {
		$item    = $node['item'];
		$classes = array_filter( (array) $item->classes );
		$current = array_intersect( $classes, array( 'current-menu-item', 'current-menu-ancestor' ) ) ? ' aria-current="page"' : '';
		$title   = $item->title;

		if ( in_array( 'bs-cta', $classes, true ) ) {
			printf( '<li class="bs-nav__item bs-nav__item--cta"><a class="bs-nav__cta" href="%s">%s</a></li>', esc_url( $item->url ), esc_html( $title ) );
			continue;
		}

		if ( ! $node['children'] ) {
			printf( '<li class="bs-nav__item"><a class="bs-nav__link" href="%s"%s>%s</a></li>', esc_url( $item->url ), $current, esc_html( $title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			continue;
		}

		$panel   = 'bs-panel-' . $item->ID;
		$see_all = get_post_meta( $item->ID, 'bs_see_all', true );
		$see_all = $see_all ? $see_all : sprintf( /* translators: %s: menu item, lower case */ __( 'See all %s', 'bali-sands' ), strtolower( $title ) );
		?>
		<li class="bs-nav__item has-panel">
			<button type="button" class="bs-nav__link bs-nav__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel ); ?>">
				<?php echo esc_html( $title ); ?><span class="bs-nav__chev" aria-hidden="true"></span>
			</button>
			<div class="bs-panel" id="<?php echo esc_attr( $panel ); ?>">
				<ul class="bs-panel__list">
					<?php foreach ( $node['children'] as $child ) : ?>
						<li>
							<a class="bs-panel__row" href="<?php echo esc_url( $child->url ); ?>">
								<span class="bs-panel__thumb" style="<?php echo esc_attr( bali_sands_thumb_style( $child ) ); ?>" aria-hidden="true"></span>
								<span class="bs-panel__text">
									<span class="bs-panel__name"><?php echo esc_html( $child->title ); ?></span>
									<?php if ( $child->description ) : ?>
										<span class="bs-panel__sub"><?php echo esc_html( $child->description ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $item->url && '#' !== $item->url ) : ?>
					<a class="bs-panel__all" href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $see_all ); ?> →</a>
				<?php endif; ?>
			</div>
		</li>
		<?php
	}
	echo '</ul>';
}

/**
 * Wavy edge, matching the homepage dividers.
 *
 * @param string $from Colour above.
 * @param string $to   Colour below.
 */
function bali_sands_wave( $from, $to ) {
	printf(
		'<div class="bs-wave" style="background-color:%s" aria-hidden="true"><svg viewBox="0 0 1280 64" preserveAspectRatio="none" focusable="false"><path d="M0,0 H1280 V26 C1110,62 930,60 760,36 C590,12 420,8 260,30 C160,44 70,48 0,38 Z" fill="%s"/></svg></div>',
		esc_attr( $to ),
		esc_attr( $from )
	);
}

/**
 * Footer column: a menu location if one is assigned, else that department's ranges.
 *
 * @param string $location   Menu location.
 * @param string $department Department slug for the fallback.
 */
function bali_sands_footer_links( $location, $department ) {
	$tree = bali_sands_menu_tree( $location );
	echo '<ul class="bs-footer__links">';
	if ( $tree ) {
		foreach ( $tree as $node ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $node['item']->url ), esc_html( $node['item']->title ) );
		}
	} elseif ( function_exists( 'bs_get_ranges' ) ) {
		foreach ( bs_get_ranges( $department ) as $range ) {
			if ( $range['link'] ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( $range['link'] ), esc_html( $range['title'] ) );
			} else {
				printf( '<li>%s</li>', esc_html( $range['title'] ) );
			}
		}
	}
	echo '</ul>';
}

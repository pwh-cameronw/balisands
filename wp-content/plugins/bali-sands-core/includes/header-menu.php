<?php
/**
 * The header dropdown menu (design 6a): thumbnail rows, "See all" links,
 * mobile drawer. Used by the theme's own header and by the BS Header menu
 * Elementor widget in the Elementor Pro header.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bs_header_menu_assets' );

function bs_header_menu_assets() {
	wp_enqueue_style( 'bs-header-menu', BS_CORE_URL . 'assets/css/header-menu.css', array(), BS_CORE_VERSION );
	wp_enqueue_script( 'bs-header-menu', BS_CORE_URL . 'assets/js/header-menu.js', array(), BS_CORE_VERSION, true );
}

add_action( 'wp_head', 'bs_header_menu_js_class', 1 );

/**
 * Mark the document as JS-enabled before paint, so the no-JS hover fallback steps aside.
 */
function bs_header_menu_js_class() {
	echo "<script>document.documentElement.classList.add('bs-js');</script>\n";
}

/**
 * Resolve a menu location name or menu ID to a menu term ID.
 *
 * @param string|int $menu Theme location (e.g. "primary") or nav menu ID.
 * @return int
 */
function bs_menu_id( $menu ) {
	if ( is_numeric( $menu ) ) {
		return (int) $menu;
	}
	$locations = get_nav_menu_locations();
	return empty( $locations[ $menu ] ) ? 0 : (int) $locations[ $menu ];
}

/**
 * Two-level tree of a menu: top items with their children.
 *
 * @param string|int $menu Theme location or nav menu ID.
 * @return array<int,array{item:WP_Post,children:WP_Post[]}>
 */
function bs_menu_tree( $menu ) {
	$id = bs_menu_id( $menu );
	if ( ! $id ) {
		return array();
	}
	$items = wp_get_nav_menu_items( $id, array( 'update_post_term_cache' => false ) );
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
function bs_menu_thumb_style( $item ) {
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
 * Print the menu: burger button plus the nav (desktop dropdowns / mobile drawer).
 *
 * The drawer is positioned against the nearest positioned ancestor, so the
 * header bar around it should be `position: relative`.
 *
 * @param string|int $menu Theme location or nav menu ID.
 */
function bs_header_menu( $menu = 'primary' ) {
	$tree = bs_menu_tree( $menu );
	if ( ! $tree ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			printf( '<a class="bs-menu__notice" href="%s">%s</a>', esc_url( admin_url( 'nav-menus.php' ) ), esc_html__( 'No menu yet: run Tools → Bali Sands setup, or pick a menu', 'bali-sands' ) );
		}
		return;
	}
	$uid = 'bs-nav-' . wp_unique_id();
	?>
	<div class="bs-menu" data-bs-menu>
		<button type="button" class="bs-burger" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>">
			<span class="bs-burger__lines" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'bali-sands' ); ?></span>
		</button>
		<nav class="bs-nav" id="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'Main', 'bali-sands' ); ?>">
			<ul class="bs-nav__list">
				<?php
				foreach ( $tree as $node ) {
					bs_header_menu_item( $node );
				}
				?>
			</ul>
		</nav>
	</div>
	<?php
}

/**
 * One top-level item: plain link, "Get a quote" button, or dropdown.
 *
 * @param array $node Tree node.
 */
function bs_header_menu_item( $node ) {
	$item    = $node['item'];
	$classes = array_filter( (array) $item->classes );
	$current = array_intersect( $classes, array( 'current-menu-item', 'current-menu-ancestor' ) ) ? ' aria-current="page"' : '';

	if ( in_array( 'bs-cta', $classes, true ) ) {
		printf( '<li class="bs-nav__item bs-nav__item--cta"><a class="bs-nav__cta" href="%s">%s</a></li>', esc_url( $item->url ), esc_html( $item->title ) );
		return;
	}
	if ( ! $node['children'] ) {
		printf( '<li class="bs-nav__item"><a class="bs-nav__link" href="%s"%s>%s</a></li>', esc_url( $item->url ), $current, esc_html( $item->title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	$panel   = 'bs-panel-' . wp_unique_id() . '-' . $item->ID;
	$see_all = get_post_meta( $item->ID, 'bs_see_all', true );
	$see_all = $see_all ? $see_all : sprintf( /* translators: %s: menu item, lower case */ __( 'See all %s', 'bali-sands' ), strtolower( $item->title ) );
	?>
	<li class="bs-nav__item has-panel">
		<button type="button" class="bs-nav__link bs-nav__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel ); ?>">
			<?php echo esc_html( $item->title ); ?><span class="bs-nav__chev" aria-hidden="true"></span>
		</button>
		<div class="bs-panel" id="<?php echo esc_attr( $panel ); ?>">
			<ul class="bs-panel__list">
				<?php foreach ( $node['children'] as $child ) : ?>
					<li>
						<a class="bs-panel__row" href="<?php echo esc_url( $child->url ); ?>">
							<span class="bs-panel__thumb" style="<?php echo esc_attr( bs_menu_thumb_style( $child ) ); ?>" aria-hidden="true"></span>
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

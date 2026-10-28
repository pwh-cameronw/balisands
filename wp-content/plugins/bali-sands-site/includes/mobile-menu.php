<?php
/**
 * The phone and tablet menu: a full-screen panel opened by the header's menu
 * button (JetMenu's ☰ toggle, or any element with the class
 * "bs-mobile-menu-toggle"), up to 1024px wide.
 *
 * Green panel with the wordmark and a close button; the Main menu's top-level
 * items as large rows; items with a sub-menu open (amber +/−) to tiles of
 * their sub-items with each page's featured image; a terracotta footer with
 * the phone number and "Get a quote".
 *
 * The items come from the Main menu (Appearance → Menus), so menu changes show
 * here too. "See all …" items (class bs-see-all) are left out, and so is any
 * third level. Script: assets/js/mobile-menu.js; styles:
 * assets/css/mobile-menu.css.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'bss_mobile_menu_assets' );
add_action( 'wp_footer', 'bss_mobile_menu_render' );

/**
 * Styles and script.
 */
function bss_mobile_menu_assets() {
	wp_enqueue_style( 'bss-mobile-menu', BSS_URL . 'assets/css/mobile-menu.css', array(), bss_asset_ver( 'assets/css/mobile-menu.css' ) );
	wp_enqueue_script( 'bss-mobile-menu', BSS_URL . 'assets/js/mobile-menu.js', array(), bss_asset_ver( 'assets/js/mobile-menu.js' ), true );
}

/**
 * The Main menu as [item, [sub-items]] rows, top level in order.
 *
 * @return array<int,array{0:WP_Post,1:WP_Post[]}>
 */
function bss_mobile_menu_tree() {
	$menus = (array) get_option( 'bss_menus', array() );
	$items = ! empty( $menus['main'] ) ? wp_get_nav_menu_items( $menus['main'] ) : array();
	if ( ! $items ) {
		return array();
	}

	$top      = array();
	$children = array();
	foreach ( $items as $item ) {
		if ( in_array( 'bs-see-all', (array) $item->classes, true ) ) {
			continue;
		}
		if ( ! $item->menu_item_parent ) {
			$top[] = $item;
		} else {
			$children[ (int) $item->menu_item_parent ][] = $item;
		}
	}

	$tree = array();
	foreach ( $top as $item ) {
		$tree[] = array( $item, isset( $children[ $item->ID ] ) ? $children[ $item->ID ] : array() );
	}
	return $tree;
}

/**
 * Whether a menu item is the page being viewed, or one of its ancestors.
 *
 * @param WP_Post   $item Menu item.
 * @param WP_Post[] $subs Its sub-items.
 * @return bool
 */
function bss_mobile_menu_is_current( $item, $subs = array() ) {
	if ( ! is_singular() ) {
		return false;
	}
	$id  = get_queried_object_id();
	$ids = array_merge( array( $id ), get_post_ancestors( $id ) );
	foreach ( array_merge( array( $item ), $subs ) as $one ) {
		if ( 'post_type' === $one->type && in_array( (int) $one->object_id, $ids, true ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Print the panel (hidden until opened).
 */
function bss_mobile_menu_render() {
	$tree = bss_mobile_menu_tree();
	if ( ! $tree ) {
		return;
	}
	$biz = bss_business();

	// The section with the current page starts open; otherwise the first one with a sub-menu.
	$open = null;
	foreach ( $tree as $i => $row ) {
		if ( $row[1] && bss_mobile_menu_is_current( $row[0], $row[1] ) ) {
			$open = $i;
			break;
		}
	}
	if ( null === $open ) {
		foreach ( $tree as $i => $row ) {
			if ( $row[1] ) {
				$open = $i;
				break;
			}
		}
	}
	?>
	<div class="bs-mm" id="bs-mobile-menu" tabindex="-1" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'bali-sands-site' ); ?>" hidden>
		<div class="bs-mm__head">
			<a class="bs-mm__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">Bali Sands</a>
			<button type="button" class="bs-mm__close" aria-label="<?php esc_attr_e( 'Close menu', 'bali-sands-site' ); ?>"><span aria-hidden="true">&times;</span></button>
		</div>

		<nav class="bs-mm__nav" aria-label="<?php esc_attr_e( 'Main menu', 'bali-sands-site' ); ?>">
			<ul class="bs-mm__list">
				<?php
				foreach ( $tree as $i => $row ) :
					list( $item, $subs ) = $row;
					$sub_id              = 'bs-mm-sub-' . $i;
					$is_open             = $subs && $i === $open;
					$current             = bss_mobile_menu_is_current( $item ) ? ' aria-current="page"' : '';
					?>
					<li class="bs-mm__item<?php echo $subs ? ' bs-mm__item--has-sub' : ''; ?><?php echo $is_open ? ' is-open' : ''; ?>">
						<div class="bs-mm__row">
							<a class="bs-mm__link" href="<?php echo esc_url( $item->url ); ?>"<?php echo $current; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $item->title ); ?></a>
							<?php if ( $subs ) : ?>
								<button type="button" class="bs-mm__toggle" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $sub_id ); ?>">
									<span class="screen-reader-text">
									<?php
									/* translators: %s: menu item, e.g. Landscape supplies */
									echo esc_html( sprintf( __( 'Show %s', 'bali-sands-site' ), $item->title ) );
									?>
									</span>
								</button>
							<?php endif; ?>
						</div>
						<?php if ( $subs ) : ?>
							<ul class="bs-mm__tiles" id="<?php echo esc_attr( $sub_id ); ?>"<?php echo $is_open ? '' : ' hidden'; ?>>
								<?php
								foreach ( $subs as $sub ) :
									$thumb = 'post_type' === $sub->type ? (int) get_post_thumbnail_id( (int) $sub->object_id ) : 0;
									?>
									<li>
										<a class="bs-mm__tile" href="<?php echo esc_url( $sub->url ); ?>">
											<span class="bs-mm__thumb">
											<?php
											if ( $thumb ) {
												echo wp_get_attachment_image(
													$thumb,
													'thumbnail',
													false,
													array(
														'alt'     => '',
														'loading' => 'lazy',
													)
												);
											}
											?>
											</span>
											<span class="bs-mm__tile-label"><?php echo esc_html( $sub->title ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="bs-mm__foot">
			<svg class="bs-mm__wave" viewBox="0 0 400 24" preserveAspectRatio="none" aria-hidden="true"><path d="M0,24 V10 C80,0 170,0 250,8 C320,15 370,14 400,6 V24 Z"></path></svg>
			<div class="bs-mm__foot-inner">
				<p class="bs-mm__note">Open 7 days · Delivery across Perth</p>
				<div class="bs-mm__buttons">
					<a class="bs-mm__btn bs-mm__btn--white" href="<?php echo esc_attr( $biz['tel'] ); ?>"><?php echo esc_html( $biz['phone'] ); ?></a>
					<a class="bs-mm__btn bs-mm__btn--amber" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>">Get a quote</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

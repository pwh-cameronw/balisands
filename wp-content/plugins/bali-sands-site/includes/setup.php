<?php
/**
 * Tools → Bali Sands setup: builds the site in one click.
 *
 * 1. Copies the photos from the old Wix site into the Media Library and makes
 *    placeholder swatch images for the landscape supplies categories.
 * 2. Creates the category pages (with featured images) under "Landscape
 *    Supplies" and "Bali Imports", plus Delivery, About, Contact and legal pages.
 * 3. Creates the Main menu (with dropdowns) and the footer menus.
 * 4. Sets Elementor's Global Colors, Global Fonts, buttons and content width.
 * 5. Creates the Gravity Forms quote form.
 * 6. Creates the Home page in Elementor and sets it as the front page.
 * 7. With Elementor Pro: creates the header, footer and product page in the Theme Builder.
 *
 * Safe to run again: it only adds what is missing. The homepage, header and
 * footer layouts (and the other built layouts) are only replaced when their
 * "Rebuild" box is ticked.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'bss_page_excerpts' );
add_action( 'admin_menu', 'bss_setup_menu' );
add_action( 'admin_notices', 'bss_setup_notice' );

/**
 * Pages get an Excerpt box: it's the category's short description (menu subtitle).
 */
function bss_page_excerpts() {
	add_post_type_support( 'page', 'excerpt' );
}

/* -------------------------------------------------------------------------
 * Lookups used by the layouts
 * ---------------------------------------------------------------------- */

/**
 * Page id for a bss_pages() key, or 0.
 *
 * @param string $key Page key.
 * @return int
 */
function bss_page_id( $key ) {
	$map = (array) get_option( 'bss_pages', array() );
	return ! empty( $map[ $key ] ) && get_post( $map[ $key ] ) ? (int) $map[ $key ] : 0;
}

/**
 * Page permalink for a bss_pages() key, or the home URL.
 *
 * @param string $key Page key.
 * @return string
 */
function bss_page_url( $key ) {
	$id = bss_page_id( $key );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

/**
 * Featured image id of a bss_pages() page, or 0.
 *
 * @param string $key Page key.
 * @return int
 */
function bss_page_thumb_id( $key ) {
	$id = bss_page_id( $key );
	return $id ? (int) get_post_thumbnail_id( $id ) : 0;
}

/**
 * Product post id for a bss_products() name, or 0.
 *
 * @param string $name Product name.
 * @return int
 */
function bss_product_id( $name ) {
	$map = (array) get_option( 'bss_products', array() );
	return ! empty( $map[ $name ] ) && get_post( $map[ $name ] ) ? (int) $map[ $name ] : 0;
}

/**
 * Attachment id for a media key, or 0.
 *
 * @param string $key Media key.
 * @return int
 */
function bss_media_id( $key ) {
	$map = (array) get_option( 'bss_media', array() );
	return ! empty( $map[ $key ] ) && get_post( $map[ $key ] ) ? (int) $map[ $key ] : 0;
}

/* -------------------------------------------------------------------------
 * Admin screen
 * ---------------------------------------------------------------------- */

/**
 * Register Tools → Bali Sands setup.
 */
function bss_setup_menu() {
	add_management_page( __( 'Bali Sands setup', 'bali-sands-site' ), __( 'Bali Sands setup', 'bali-sands-site' ), 'manage_options', 'bss-setup', 'bss_setup_page' );
}

/**
 * After activation, point to the setup screen.
 */
function bss_setup_notice() {
	if ( ! get_transient( 'bss_setup_notice' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'tools_page_bss-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Bali Sands Site is active.', 'bali-sands-site' ),
		esc_url( admin_url( 'tools.php?page=bss-setup' ) ),
		esc_html__( 'Run the site setup →', 'bali-sands-site' )
	);
}

/**
 * Setup screen.
 */
function bss_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$log = null;
	if ( isset( $_POST['bss_run_setup'] ) && check_admin_referer( 'bss_run_setup' ) ) {
		$log = bss_run_setup(
			array(
				'rebuild_home'       => ! empty( $_POST['bss_rebuild_home'] ),
				'rebuild_categories' => ! empty( $_POST['bss_rebuild_categories'] ),
				'rebuild_about'      => ! empty( $_POST['bss_rebuild_about'] ),
				'rebuild_theme'      => ! empty( $_POST['bss_rebuild_theme'] ),
				'rebuild_product'    => ! empty( $_POST['bss_rebuild_product'] ),
			)
		);
		delete_transient( 'bss_setup_notice' );
	}

	$checks = array(
		array( __( 'Hello Elementor parent theme installed', 'bali-sands-site' ), wp_get_theme( 'hello-elementor' )->exists(), true ),
		array( __( 'Bali Sands child theme active', 'bali-sands-site' ), 'hello-elementor' === get_template() && 'hello-elementor' !== get_stylesheet(), true ),
		array( __( 'Elementor', 'bali-sands-site' ), did_action( 'elementor/loaded' ) > 0, true ),
		array( __( 'Elementor Pro (header, footer and product page)', 'bali-sands-site' ), defined( 'ELEMENTOR_PRO_VERSION' ), true ),
		array( __( 'JetMenu (main menu)', 'bali-sands-site' ), class_exists( 'Jet_Menu' ), true ),
		array( __( 'Gravity Forms (quote form)', 'bali-sands-site' ), class_exists( 'GFAPI' ), true ),
		array( __( 'JetEngine (product cards on category pages)', 'bali-sands-site' ), class_exists( 'Jet_Engine' ), true ),
		array( __( 'ACF (product card fields)', 'bali-sands-site' ), function_exists( 'acf_add_local_field_group' ), true ),
		array( __( 'PHP GD (placeholder swatch images)', 'bali-sands-site' ), function_exists( 'imagecreatetruecolor' ), false ),
	);
	$home_id = (int) get_option( 'bss_home_id' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bali Sands setup', 'bali-sands-site' ); ?></h1>
		<p style="max-width:720px"><?php esc_html_e( 'Creates the category pages, menus, Elementor global colours and fonts, the quote form, the homepage, and the header and footer. Safe to run again: it only adds what is missing.', 'bali-sands-site' ); ?></p>

		<h2><?php esc_html_e( 'Before you run it', 'bali-sands-site' ); ?></h2>
		<table class="widefat striped" style="max-width:720px">
			<tbody>
			<?php foreach ( $checks as $c ) : ?>
				<tr>
					<td><?php echo esc_html( $c[0] ); ?></td>
					<td style="width:140px">
						<?php
						if ( $c[1] ) {
							echo '<span style="color:#2f7d32">✓ ' . esc_html__( 'Ready', 'bali-sands-site' ) . '</span>';
						} elseif ( $c[2] ) {
							echo '<strong style="color:#b32d2e">' . esc_html__( 'Needed', 'bali-sands-site' ) . '</strong>';
						} else {
							echo '<span style="color:#787c82">' . esc_html__( 'Not available', 'bali-sands-site' ) . '</span>';
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:20px">
			<?php wp_nonce_field( 'bss_run_setup' ); ?>
			<?php if ( $home_id && get_post( $home_id ) ) : ?>
				<p><label><input type="checkbox" name="bss_rebuild_home" value="1"> <?php esc_html_e( 'Rebuild the homepage layout (replaces any Elementor edits to the homepage)', 'bali-sands-site' ); ?></label></p>
			<?php endif; ?>
			<?php if ( get_option( 'bss_category_layouts' ) ) : ?>
				<p><label><input type="checkbox" name="bss_rebuild_categories" value="1">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: page names */
						__( 'Rebuild the category page layouts (%s) — replaces any Elementor edits to them', 'bali-sands-site' ),
						implode( ', ', wp_list_pluck( array_intersect_key( bss_pages(), bss_category_pages() ), 0 ) )
					)
				);
				?>
				</label></p>
			<?php endif; ?>
			<?php if ( get_option( 'bss_about_built' ) ) : ?>
				<p><label><input type="checkbox" name="bss_rebuild_about" value="1"> <?php esc_html_e( 'Rebuild the About page layout (replaces any Elementor edits to it)', 'bali-sands-site' ); ?></label></p>
			<?php endif; ?>
			<?php if ( get_option( 'bss_header_id' ) || get_option( 'bss_footer_id' ) ) : ?>
				<p><label><input type="checkbox" name="bss_rebuild_theme" value="1"> <?php esc_html_e( 'Rebuild the header and footer layouts (replaces any Elementor edits to them)', 'bali-sands-site' ); ?></label></p>
			<?php endif; ?>
			<?php if ( get_option( 'bss_product_page_id' ) ) : ?>
				<p><label><input type="checkbox" name="bss_rebuild_product" value="1"> <?php esc_html_e( 'Rebuild the product page layout (replaces any Elementor edits to it)', 'bali-sands-site' ); ?></label></p>
			<?php endif; ?>
			<?php submit_button( __( 'Run setup', 'bali-sands-site' ), 'primary', 'bss_run_setup', false ); ?>
		</form>

		<?php if ( $log ) : ?>
			<h2><?php esc_html_e( 'Result', 'bali-sands-site' ); ?></h2>
			<ul style="list-style:disc;padding-left:20px;max-width:720px">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'View the homepage', 'bali-sands-site' ); ?></a></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Run every step.
 *
 * @param array $opts rebuild_home, rebuild_categories, rebuild_about, rebuild_theme, rebuild_product.
 * @return string[] Log lines.
 */
function bss_run_setup( $opts ) {
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 ); // phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged -- photo downloads.
	}

	$log   = array();
	$log[] = bss_setup_experiments();
	$log[] = bss_setup_media();
	$log[] = bss_setup_pages();
	$log[] = bss_setup_menus();
	$log[] = bss_setup_kit();

	$form  = bss_setup_quote_form();
	$log[] = $form['log'];

	$log[] = bss_setup_home( $form['id'], ! empty( $opts['rebuild_home'] ) );
	$log[] = bss_setup_product_terms();
	$log[] = bss_setup_products();
	$log[] = bss_setup_product_listing( ! empty( $opts['rebuild_categories'] ) );
	$log[] = bss_setup_category_layouts( ! empty( $opts['rebuild_categories'] ) );
	$log[] = bss_setup_about( ! empty( $opts['rebuild_about'] ) );
	$log[] = bss_setup_theme_template( 'header', 'Bali Sands header', ! empty( $opts['rebuild_theme'] ) );
	$log[] = bss_setup_theme_template( 'footer', 'Bali Sands footer', ! empty( $opts['rebuild_theme'] ) );
	$log[] = bss_setup_theme_template( 'product_page', 'Product page', ! empty( $opts['rebuild_product'] ) );

	if ( did_action( 'elementor/loaded' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	flush_rewrite_rules(); // So /products/… and /product-category/… URLs work.
	return array_values( array_filter( $log ) );
}

/* -------------------------------------------------------------------------
 * Steps
 * ---------------------------------------------------------------------- */

/**
 * The layouts use Elementor's Flexbox and Grid containers.
 *
 * @return string
 */
function bss_setup_experiments() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return 'Elementor is not active: install and activate it, then run setup again.';
	}
	update_option( 'elementor_experiment-container', 'active' );
	update_option( 'elementor_experiment-container_grid', 'active' );
	return 'Elementor: Flexbox and Grid containers switched on.';
}

/**
 * Copy the Wix photos into the Media Library and make the swatch placeholders (once each).
 *
 * @return string
 */
function bss_setup_media() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$map    = (array) get_option( 'bss_media', array() );
	$added  = 0;
	$failed = array();

	foreach ( bss_wix_media() as $key => $item ) {
		if ( bss_media_id( $key ) ) {
			continue;
		}
		$tmp = download_url( 'https://static.wixstatic.com/media/' . $item[0], 60 );
		if ( is_wp_error( $tmp ) ) {
			$failed[] = $item[1];
			continue;
		}
		$ext = strtolower( pathinfo( $item[0], PATHINFO_EXTENSION ) );
		$id  = media_handle_sideload(
			array(
				'name'     => 'bali-sands-' . $key . '.' . ( 'jpeg' === $ext ? 'jpg' : $ext ),
				'tmp_name' => $tmp,
			),
			0,
			$item[1]
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$failed[] = $item[1];
			continue;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $item[1] );
		$map[ $key ] = $id;
		update_option( 'bss_media', $map, false );
		++$added;
	}

	$swatches = 0;
	if ( function_exists( 'imagecreatetruecolor' ) ) {
		foreach ( bss_pages() as $key => $page ) {
			if ( ! is_array( $page[3] ) || bss_media_id( 'swatch-' . $key ) ) {
				continue;
			}
			$id = bss_make_swatch( $page[3][0], $page[3][1], $page[0] . ' (placeholder)', 'swatch-' . $key );
			if ( $id ) {
				$map[ 'swatch-' . $key ] = $id;
				update_option( 'bss_media', $map, false );
				++$swatches;
			}
		}
	}

	$line = sprintf( 'Media: %d photos copied from the old Wix site, %d placeholder swatches made for the landscape supplies categories.', $added, $swatches );
	if ( $failed ) {
		$line .= ' Could not download: ' . implode( ', ', $failed ) . '. Run setup again, or upload these by hand.';
	}
	return $line;
}

/**
 * Make a 600×600 diagonal-gradient JPEG and add it to the Media Library.
 *
 * @param string $from  Start colour (hex).
 * @param string $to    End colour (hex).
 * @param string $title Attachment title.
 * @param string $slug  File name slug.
 * @return int Attachment id, or 0.
 */
function bss_make_swatch( $from, $to, $title, $slug ) {
	$size = 600;
	$im   = imagecreatetruecolor( $size, $size );
	if ( ! $im ) {
		return 0;
	}
	$a     = sscanf( ltrim( $from, '#' ), '%2x%2x%2x' );
	$b     = sscanf( ltrim( $to, '#' ), '%2x%2x%2x' );
	$steps = 2 * $size - 2;
	for ( $k = 0; $k <= $steps; $k++ ) {
		$t     = $k / $steps;
		$color = imagecolorallocate(
			$im,
			(int) round( $a[0] + ( $b[0] - $a[0] ) * $t ),
			(int) round( $a[1] + ( $b[1] - $a[1] ) * $t ),
			(int) round( $a[2] + ( $b[2] - $a[2] ) * $t )
		);
		imageline( $im, $k, 0, 0, $k, $color );
	}

	$tmp = wp_tempnam( $slug . '.jpg' );
	$ok  = imagejpeg( $im, $tmp, 90 );
	imagedestroy( $im );
	if ( ! $ok ) {
		return 0;
	}

	$id = media_handle_sideload(
		array(
			'name'     => 'bali-sands-' . $slug . '.jpg',
			'tmp_name' => $tmp,
		),
		0,
		$title
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	return (int) $id;
}

/**
 * Create the pages that don't exist yet, and give each its featured image.
 *
 * @return string
 */
function bss_setup_pages() {
	$map   = (array) get_option( 'bss_pages', array() );
	$paths = array();
	$added = 0;

	foreach ( bss_pages() as $key => $page ) {
		list( $title, $parent_key, $slug, $image, $excerpt ) = $page;

		$paths[ $key ] = ( $parent_key && isset( $paths[ $parent_key ] ) ? $paths[ $parent_key ] . '/' : '' ) . $slug;
		$id            = bss_page_id( $key );

		if ( ! $id ) {
			$existing = get_page_by_path( $paths[ $key ] );
			$id       = $existing ? (int) $existing->ID : 0;
		}

		if ( ! $id ) {
			$content = $excerpt ? '<!-- wp:paragraph --><p>' . esc_html( $excerpt ) . '</p><!-- /wp:paragraph -->' : '';
			$id      = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_parent'  => $parent_key ? bss_page_id( $parent_key ) : 0,
					'post_excerpt' => $excerpt,
					'post_content' => $content,
					'menu_order'   => count( $map ),
				)
			);
			if ( is_wp_error( $id ) || ! $id ) {
				continue;
			}
			++$added;
		}

		$map[ $key ] = (int) $id;
		update_option( 'bss_pages', $map, false );

		if ( ! has_post_thumbnail( $id ) ) {
			$thumb = is_array( $image ) ? bss_media_id( 'swatch-' . $key ) : ( $image ? bss_media_id( $image ) : 0 );
			if ( $thumb ) {
				set_post_thumbnail( $id, $thumb );
			}
		}
	}

	return sprintf( 'Pages: %d added. Categories sit under "Landscape Supplies" and "Bali Imports"; each has a featured image (used beside it in the menu).', $added );
}

/**
 * Add one page link to a menu.
 *
 * @param int    $menu_id Menu id.
 * @param string $page    Page key.
 * @param string $label   Label.
 * @param int    $parent  Parent menu item id.
 * @param int    $pos     Position.
 * @param string $desc    Description.
 * @param string $classes CSS classes.
 * @return int Menu item id, or 0.
 */
function bss_add_menu_page( $menu_id, $page, $label, $parent, $pos, $desc = '', $classes = '' ) {
	$page_id = bss_page_id( $page );
	if ( ! $page_id ) {
		return 0;
	}
	$item = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-object-id'   => $page_id,
			'menu-item-object'      => 'page',
			'menu-item-type'        => 'post_type',
			'menu-item-title'       => $label,
			'menu-item-description' => $desc,
			'menu-item-classes'     => $classes,
			'menu-item-parent-id'   => $parent,
			'menu-item-position'    => $pos,
			'menu-item-status'      => 'publish',
		)
	);
	return is_wp_error( $item ) ? 0 : (int) $item;
}

/**
 * Create the Main menu and footer menus (once each).
 *
 * @return string
 */
function bss_setup_menus() {
	$menus   = (array) get_option( 'bss_menus', array() );
	$created = array();
	$pages   = bss_pages();

	if ( empty( $menus['main'] ) || ! wp_get_nav_menu_object( $menus['main'] ) ) {
		$existing = wp_get_nav_menu_object( 'Main menu' );
		if ( $existing ) {
			$menus['main'] = (int) $existing->term_id;
		} else {
			$menu_id = wp_create_nav_menu( 'Main menu' );
			if ( ! is_wp_error( $menu_id ) ) {
				$pos = 0;
				foreach ( bss_main_menu() as $top ) {
					$parent = bss_add_menu_page( $menu_id, $top[1], $top[0], 0, ++$pos );
					if ( ! $parent ) {
						continue;
					}
					foreach ( $top[2] as $child ) {
						bss_add_menu_page( $menu_id, $child, $pages[ $child ][0], $parent, ++$pos, $pages[ $child ][4] );
					}
					if ( $top[3] ) {
						bss_add_menu_page( $menu_id, $top[1], $top[3], $parent, ++$pos, '', 'bs-see-all bs-no-thumb' );
					}
				}
				$menus['main'] = (int) $menu_id;
				$created[]     = 'Main menu';
			}
		}
	}

	foreach ( bss_footer_menus() as $name => $items ) {
		if ( ! empty( $menus[ $name ] ) && wp_get_nav_menu_object( $menus[ $name ] ) ) {
			continue;
		}
		$existing = wp_get_nav_menu_object( $name );
		if ( $existing ) {
			$menus[ $name ] = (int) $existing->term_id;
			continue;
		}
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		$pos = 0;
		foreach ( $items as $label => $page ) {
			bss_add_menu_page( $menu_id, $page, $label, 0, ++$pos );
		}
		$menus[ $name ] = (int) $menu_id;
		$created[]      = $name;
	}

	update_option( 'bss_menus', $menus, false );

	// Hello Elementor's own header uses "menu-1" when there's no Theme Builder header.
	if ( ! empty( $menus['main'] ) ) {
		$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['menu-1'] ) ) {
			$locations['menu-1'] = $menus['main'];
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	return $created
		? 'Menus created: ' . implode( ', ', $created ) . '. Edit them under Appearance → Menus.'
		: 'Menus: already exist, left as they are.';
}

/**
 * Global Colors, Global Fonts, buttons, links and content width on the active Elementor kit.
 *
 * @return string
 */
function bss_setup_kit() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}
	$kits   = \Elementor\Plugin::$instance->kits_manager;
	$kit_id = (int) $kits->get_active_id();
	if ( ( ! $kit_id || ! get_post( $kit_id ) ) && method_exists( $kits, 'create_default' ) ) {
		$kit_id = (int) $kits->create_default();
		update_option( 'elementor_active_kit', $kit_id );
	}
	if ( ! $kit_id || ! get_post( $kit_id ) ) {
		return 'Elementor global colours and fonts skipped: no active Elementor kit. Open Elementor once, then run setup again.';
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	$system = array( 'primary', 'secondary', 'text', 'accent' );

	// Colours: ours replace same-id entries, any others the site already has are kept.
	$colors = array(
		'system' => array(),
		'custom' => array(),
	);
	foreach ( bss_palette() as $id => $c ) {
		$colors[ in_array( $id, $system, true ) ? 'system' : 'custom' ][] = array(
			'_id'   => $id,
			'title' => $c[0],
			'color' => $c[1],
		);
	}
	$settings['system_colors'] = $colors['system'];
	$settings['custom_colors'] = bss_keep_others( isset( $settings['custom_colors'] ) ? $settings['custom_colors'] : array(), $colors['custom'] );

	// Fonts.
	$fonts = array(
		'system' => array(),
		'custom' => array(),
	);
	foreach ( bss_type_scale() as $id => $f ) {
		$row = array(
			'_id'                    => $id,
			'title'                  => $f['title'],
			'typography_typography'  => 'custom',
			'typography_font_family' => 'Open Sans',
			'typography_font_weight' => $f['weight'],
		);
		if ( isset( $f['size'] ) ) {
			$row['typography_font_size'] = bss_size( $f['size'] );
		}
		if ( isset( $f['t'] ) ) {
			$row['typography_font_size_tablet'] = bss_size( $f['t'] );
		}
		if ( isset( $f['m'] ) ) {
			$row['typography_font_size_mobile'] = bss_size( $f['m'] );
		}
		if ( isset( $f['lh'] ) ) {
			$row['typography_line_height'] = bss_size( $f['lh'], 'em' );
		}
		if ( isset( $f['ls'] ) ) {
			$row['typography_letter_spacing'] = bss_size( $f['ls'] );
		}
		if ( ! empty( $f['upper'] ) ) {
			$row['typography_text_transform'] = 'uppercase';
		}
		$fonts[ in_array( $id, $system, true ) ? 'system' : 'custom' ][] = $row;
	}
	$settings['system_typography'] = $fonts['system'];
	$settings['custom_typography'] = bss_keep_others( isset( $settings['custom_typography'] ) ? $settings['custom_typography'] : array(), $fonts['custom'] );

	// Site-wide styles, all pointing at the globals above.
	$settings = bss_merge(
		$settings,
		array(
			'container_width'                    => bss_size( 1200 ),
			'body_background_background'         => 'classic',
			'button_border_radius'               => bss_box( 6 ),
			'button_padding'                     => bss_box( 18, 26 ),
			'button_background_background'       => 'classic',
			'button_hover_background_background' => 'classic',
		),
		bss_color( 'body_background_color', 'bs_white' ),
		bss_color( 'body_color', 'text' ),
		bss_font( 'text', 'body_typography' ),
		bss_color( 'link_normal_color', 'primary' ),
		bss_color( 'link_hover_color', 'bs_green_hover' ),
		bss_font( 'bs_display', 'h1_typography' ),
		bss_color( 'h1_color', 'bs_ink' ),
		bss_font( 'bs_h2', 'h2_typography' ),
		bss_color( 'h2_color', 'bs_ink' ),
		bss_font( 'bs_card_lg', 'h3_typography' ),
		bss_color( 'h3_color', 'bs_ink' ),
		bss_font( 'accent', 'button_typography' ),
		bss_color( 'button_text_color', 'bs_white' ),
		bss_color( 'button_background_color', 'primary' ),
		bss_color( 'button_hover_text_color', 'bs_white' ),
		bss_color( 'button_hover_background_color', 'bs_green_hover' )
	);

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );

	return 'Elementor Site Settings: Global Colors, Global Fonts (Open Sans), buttons, links and the 1200px content width set.';
}

/**
 * Merge repeater rows by _id: ours replace theirs, theirs that we don't define are kept after ours.
 *
 * @param array $theirs Existing rows.
 * @param array $ours   Our rows.
 * @return array
 */
function bss_keep_others( $theirs, $ours ) {
	$ids = wp_list_pluck( $ours, '_id' );
	foreach ( (array) $theirs as $row ) {
		if ( isset( $row['_id'] ) && ! in_array( $row['_id'], $ids, true ) ) {
			$ours[] = $row;
		}
	}
	return $ours;
}

/**
 * The Gravity Forms quote form (once), also placed on the Contact page.
 *
 * @return array{id:int,log:string}
 */
function bss_setup_quote_form() {
	if ( ! class_exists( 'GFAPI' ) ) {
		return array(
			'id'  => 0,
			'log' => 'Quote form skipped: Gravity Forms is not active. Activate it and run setup again, then tick "Rebuild the homepage layout" (or edit the homepage\'s form Shortcode widget).',
		);
	}

	$form_id = (int) get_option( 'bss_form_quote' );
	if ( $form_id && GFAPI::get_form( $form_id ) ) {
		$log = 'Quote form: already exists (Gravity form #' . $form_id . ').';
	} else {
		$field = static function ( $id, $type, $label, $extra = array() ) {
			return array_merge(
				array(
					'id'                   => $id,
					'type'                 => $type,
					'label'                => $label,
					'placeholder'          => $label,
					'labelPlacement'       => 'hidden_label',
					'layoutGridColumnSpan' => 12,
					'isRequired'           => false,
				),
				$extra
			);
		};

		$form = array(
			'title'                => 'Get a quote',
			'description'          => '',
			'labelPlacement'       => 'top_label',
			'descriptionPlacement' => 'below',
			'button'               => array(
				'type' => 'text',
				'text' => 'Send enquiry',
			),
			'fields'               => array(
				$field( 1, 'text', 'Your name', array( 'isRequired' => true, 'layoutGridColumnSpan' => 6, 'autocompleteAttribute' => 'name' ) ),
				$field( 2, 'phone', 'Phone', array( 'isRequired' => true, 'layoutGridColumnSpan' => 6, 'phoneFormat' => 'international' ) ),
				$field( 3, 'email', 'Email', array( 'layoutGridColumnSpan' => 6 ) ),
				$field( 4, 'text', 'Delivery address (optional)', array( 'layoutGridColumnSpan' => 6 ) ),
				$field(
					5,
					'select',
					'What are you after?',
					array(
						'placeholder' => 'What are you after? Supplies, imports or both',
						'choices'     => array(
							array( 'text' => 'Landscape supplies', 'value' => 'Landscape supplies' ),
							array( 'text' => 'Bali imports', 'value' => 'Bali imports' ),
							array( 'text' => 'Both', 'value' => 'Both' ),
						),
					)
				),
				$field( 6, 'textarea', 'Tell us about the job', array( 'isRequired' => true ) ),
			),
			'notifications'        => array(
				'bss0quote0admin' => array(
					'id'       => 'bss0quote0admin',
					'name'     => 'Quote request to the yard',
					'isActive' => true,
					'event'    => 'form_submission',
					'toType'   => 'email',
					'to'       => '{admin_email}',
					'replyTo'  => '{Email:3}',
					'subject'  => 'Quote request from {Your name:1}',
					'message'  => '{all_fields}',
				),
			),
			'confirmations'        => array(
				'bss0quote0thanks' => array(
					'id'        => 'bss0quote0thanks',
					'name'      => 'Default Confirmation',
					'isDefault' => true,
					'type'      => 'message',
					'message'   => 'Thanks — we\'ve got your enquiry and will be in touch shortly. In a hurry? Call the yard on (08) 9393 2832.',
				),
			),
		);

		$form_id = GFAPI::add_form( $form );
		if ( is_wp_error( $form_id ) ) {
			return array(
				'id'  => 0,
				'log' => 'Quote form could not be created: ' . $form_id->get_error_message(),
			);
		}
		$form_id = (int) $form_id;
		update_option( 'bss_form_quote', $form_id, false );
		$log = 'Quote form: created (Gravity form #' . $form_id . '). Enquiries go to the site admin email; change it under Forms → Get a quote → Settings → Notifications.';
	}

	// Contact page: add the form below its intro, once.
	$contact = bss_page_id( 'contact' );
	if ( $contact && false === strpos( (string) get_post_field( 'post_content', $contact ), '[gravityform' ) ) {
		wp_update_post(
			array(
				'ID'           => $contact,
				'post_content' => get_post_field( 'post_content', $contact ) . "\n<!-- wp:group {\"className\":\"bs-gf\"} --><div class=\"wp-block-group bs-gf\"><!-- wp:shortcode -->[gravityform id=\"" . $form_id . "\" title=\"false\" description=\"false\" ajax=\"true\" theme=\"gravity-theme\"]<!-- /wp:shortcode --></div><!-- /wp:group -->",
			)
		);
	}

	return array(
		'id'  => $form_id,
		'log' => $log,
	);
}

/**
 * Write Elementor data to a post.
 *
 * @param int    $post_id  Post id.
 * @param array  $elements Elements.
 * @param string $type     Elementor document type.
 */
function bss_save_elementor( $post_id, $elements, $type ) {
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', $type );
	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		update_post_meta( $post_id, '_elementor_pro_version', ELEMENTOR_PRO_VERSION );
	}
	delete_post_meta( $post_id, '_elementor_css' );
	delete_post_meta( $post_id, '_elementor_element_cache' );
}

/**
 * Create the Home page (or rebuild its layout) and make it the front page.
 *
 * @param int  $form_id Quote form id.
 * @param bool $rebuild Overwrite existing layout.
 * @return string
 */
function bss_setup_home( $form_id, $rebuild ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}
	$id     = (int) get_option( 'bss_home_id' );
	$exists = $id && get_post( $id );

	if ( ! $exists ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Home',
				'post_name'   => 'home',
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return 'Homepage could not be created.';
		}
		update_option( 'bss_home_id', $id, false );
	}

	if ( ! $exists || $rebuild ) {
		update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
		bss_save_elementor( $id, bss_home_elements( $form_id ), 'wp-page' );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $id );

	if ( $exists && ! $rebuild ) {
		return 'Homepage: already exists, layout left as it is. It is the front page.';
	}
	return $exists ? 'Homepage: layout rebuilt.' : 'Homepage: created in Elementor and set as the front page.';
}

/**
 * Product categories mirroring the category pages (once each), and the "Sold as" options.
 *
 * Landscape supplies › Sands › Paving Sand, … and Bali imports › Pots, …
 * Each term's slug matches its page slug; the page id is kept in term meta.
 *
 * @return string
 */
function bss_setup_product_terms() {
	$terms = (array) get_option( 'bss_terms', array() );
	$all   = bss_pages();
	$added = 0;

	foreach ( $all as $key => $page ) {
		// Only pages in the Landscape Supplies / Bali Imports trees.
		$root = $key;
		while ( '' !== $all[ $root ][1] ) {
			$root = $all[ $root ][1];
		}
		if ( ! in_array( $root, array( 'supplies', 'imports' ), true ) ) {
			continue;
		}
		if ( ! empty( $terms[ $key ] ) && get_term( $terms[ $key ], 'bs_product_cat' ) ) {
			continue;
		}

		$existing = get_term_by( 'slug', $page[2], 'bs_product_cat' );
		if ( $existing ) {
			$terms[ $key ] = (int) $existing->term_id;
		} else {
			$parent = $page[1] && ! empty( $terms[ $page[1] ] ) ? (int) $terms[ $page[1] ] : 0;
			$name   = 'supplies' === $key ? 'Landscape supplies' : ( 'imports' === $key ? 'Bali imports' : $page[0] );
			$term   = wp_insert_term(
				$name,
				'bs_product_cat',
				array(
					'slug'   => $page[2],
					'parent' => $parent,
				)
			);
			if ( is_wp_error( $term ) ) {
				continue;
			}
			$terms[ $key ] = (int) $term['term_id'];
			++$added;
		}
		if ( bss_page_id( $key ) ) {
			update_term_meta( $terms[ $key ], 'bss_page_id', bss_page_id( $key ) );
		}
	}
	update_option( 'bss_terms', $terms, false );

	foreach ( bss_sold_as() as $slug => $label ) {
		if ( ! get_term_by( 'slug', $slug, 'bs_sold_as' ) ) {
			wp_insert_term( $label, 'bs_sold_as', array( 'slug' => $slug ) );
		}
	}

	return sprintf( 'Product categories: %d added (Products → Product categories), matching the category pages.', $added );
}

/**
 * Add the starting Products (once each) with their ACF fields, categories and a placeholder photo.
 *
 * @return string
 */
function bss_setup_products() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$map   = (array) get_option( 'bss_products', array() );
	$terms = (array) get_option( 'bss_terms', array() );
	$media = (array) get_option( 'bss_media', array() );
	$added = 0;
	$now   = time();

	foreach ( bss_products() as $i => $product ) {
		list( $name, $short, $best, $sold, $category, $photo ) = $product;
		if ( bss_product_id( $name ) ) {
			continue;
		}

		$id = wp_insert_post(
			array(
				'post_type'    => 'bs_product',
				'post_status'  => 'publish',
				'post_title'   => $name,
				'post_content' => bss_product_content( $best ),
				'menu_order'   => $i,
				// Newest first is the listing's default order, so date them to keep the design's order.
				'post_date'    => wp_date( 'Y-m-d H:i:s', $now - $i * 60 ),
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}

		// ACF stores a reference to the field key alongside each value.
		update_post_meta( $id, 'bs_short_desc', $short );
		update_post_meta( $id, '_bs_short_desc', 'field_bss_short_desc' );
		update_post_meta( $id, 'bs_best_for', $best );
		update_post_meta( $id, '_bs_best_for', 'field_bss_best_for' );

		if ( ! empty( $terms[ $category ] ) ) {
			wp_set_object_terms( $id, array( (int) $terms[ $category ] ), 'bs_product_cat' );
		}
		wp_set_object_terms( $id, $sold, 'bs_sold_as' );

		// One shared placeholder photo per colour.
		if ( function_exists( 'imagecreatetruecolor' ) ) {
			$swatch = 'swatch-product-' . substr( md5( implode( '', $photo ) ), 0, 8 );
			if ( ! bss_media_id( $swatch ) ) {
				$att = bss_make_swatch( $photo[0], $photo[1], 'Product photo (placeholder)', $swatch );
				if ( $att ) {
					$media[ $swatch ] = $att;
					update_option( 'bss_media', $media, false );
				}
			}
			if ( bss_media_id( $swatch ) ) {
				set_post_thumbnail( $id, bss_media_id( $swatch ) );
			}
		}

		$map[ $name ] = (int) $id;
		update_option( 'bss_products', $map, false );
		++$added;
	}

	return sprintf( 'Products: %d added (Products in the admin menu). Edit their text, photo, categories and "Sold as" there.', $added );
}

/**
 * Starting write-up for a new Product's page ("View details"): headings with
 * [square-bracket] prompts for the yard's own details.
 *
 * @param string $best The "Best for" text.
 * @return string Block markup.
 */
function bss_product_content( $best ) {
	$blocks = array(
		array( 'What\'s in it', '[What goes into it, where it comes from, and what it looks and feels like.]' ),
		array( 'How to use it', '[How deep to spread it, whether to dig it in, and anything to do before planting or laying.]' ),
		array( 'How much you\'ll need', '[Rough coverage, e.g. 1m³ covers about 10m² at 100mm deep. The calculator works it out for any area.]' ),
		array( 'Good for', $best . '.' ),
	);
	$out = '';
	foreach ( $blocks as $block ) {
		$out .= '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $block[0] ) . '</h3><!-- /wp:heading -->'
			. '<!-- wp:paragraph --><p>' . esc_html( $block[1] ) . '</p><!-- /wp:paragraph -->';
	}
	return $out;
}

/**
 * The JetEngine "Product card" listing (Elementor), used by the category pages' Listing Grids.
 *
 * @param bool $rebuild Overwrite its layout.
 * @return string
 */
function bss_setup_product_listing( $rebuild ) {
	if ( ! class_exists( 'Jet_Engine' ) ) {
		return 'Product card listing skipped: JetEngine is not active, so category pages show fixed product cards. Activate JetEngine, then run setup with "Rebuild the category page layouts" ticked.';
	}
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}

	$id     = (int) get_option( 'bss_product_listing_id' );
	$exists = $id && get_post( $id );

	if ( ! $exists ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'jet-engine',
				'post_status' => 'publish',
				'post_title'  => 'Product card',
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return 'Product card listing could not be created.';
		}
		update_option( 'bss_product_listing_id', $id, false );
	}

	if ( ! $exists || $rebuild ) {
		$source = array(
			'source'    => 'posts',
			'post_type' => 'bs_product',
			'tax'       => 'bs_product_cat',
		);
		update_post_meta( $id, '_listing_data', $source );
		update_post_meta( $id, '_listing_type', 'elementor' );
		bss_save_elementor( $id, bss_product_listing_elements(), 'jet-listing-items' );
		update_post_meta(
			$id,
			'_elementor_page_settings',
			array(
				'listing_source'    => 'posts',
				'listing_post_type' => 'bs_product',
				'listing_tax'       => 'bs_product_cat',
			)
		);
	}

	$state = $exists ? ( $rebuild ? 'rebuilt' : 'already exists, layout left as it is' ) : 'created';
	return sprintf( 'Product card listing: %s (JetEngine → Listings → "Product card").', $state );
}

/**
 * Give the designed category pages (bss_category_pages()) their Elementor layout.
 *
 * @param bool $rebuild Overwrite layouts already built.
 * @return string
 */
function bss_setup_category_layouts( $rebuild ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}
	$built = (array) get_option( 'bss_category_layouts', array() );
	$pages = bss_pages();
	$done  = array();

	foreach ( array_keys( bss_category_pages() ) as $key ) {
		$id = bss_page_id( $key );
		if ( ! $id || ( ! empty( $built[ $key ] ) && ! $rebuild ) ) {
			continue;
		}
		update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
		bss_save_elementor( $id, bss_category_elements( $key ), 'wp-page' );
		$built[ $key ] = 1;
		$done[]        = $pages[ $key ][0];
	}
	update_option( 'bss_category_layouts', $built, false );

	return $done
		? 'Category pages built in Elementor: ' . implode( ', ', $done ) . '.'
		: 'Category pages: layouts already built, left as they are.';
}

/**
 * Give the About page its Elementor layout (once, or again when asked).
 *
 * @param bool $rebuild Overwrite the layout.
 * @return string
 */
function bss_setup_about( $rebuild ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}
	$id = bss_page_id( 'about' );
	if ( ! $id ) {
		return 'About page not found: run setup again.';
	}
	if ( get_option( 'bss_about_built' ) && ! $rebuild ) {
		return 'About page: layout already built, left as it is.';
	}
	update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
	bss_save_elementor( $id, bss_about_elements(), 'wp-page' );
	update_option( 'bss_about_built', 1, false );
	return 'About page: built in Elementor. Text in [square brackets] needs the real details.';
}

/**
 * Create (or rebuild) a Theme Builder template and set where it shows: the
 * header and footer on the whole site, the product page on every Product.
 *
 * @param string $type    header | footer | product_page.
 * @param string $title   Template title.
 * @param bool   $rebuild Overwrite existing layout.
 * @return string
 */
function bss_setup_theme_template( $type, $title, $rebuild ) {
	if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		if ( 'header' === $type ) {
			return 'Header and footer skipped: Elementor Pro is not active (Hello Elementor\'s basic header and footer show until it is).';
		}
		return 'product_page' === $type ? 'Product page skipped: Elementor Pro is not active, so Products use Hello Elementor\'s plain single layout.' : '';
	}

	// Elementor template type, display condition, where it shows (for the log).
	$config = array(
		'header'       => array( 'header', 'include/general', 'shown on the entire site' ),
		'footer'       => array( 'footer', 'include/general', 'shown on the entire site' ),
		'product_page' => array( 'single-post', 'include/singular/bs_product', 'shown on every Product\'s "View details" page' ),
	);
	list( $doc_type, $condition, $where ) = $config[ $type ];

	$option = 'bss_' . $type . '_id';
	$id     = (int) get_option( $option );
	$exists = $id && get_post( $id );

	if ( ! $exists ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'elementor_library',
				'post_status' => 'publish',
				'post_title'  => $title,
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return $title . ' could not be created.';
		}
		update_option( $option, $id, false );
		wp_set_object_terms( $id, $doc_type, 'elementor_library_type' );
	}

	if ( ! $exists || $rebuild ) {
		if ( 'header' === $type ) {
			$menus    = (array) get_option( 'bss_menus', array() );
			$elements = bss_header_elements( ! empty( $menus['main'] ) ? (int) $menus['main'] : 0 );
		} elseif ( 'footer' === $type ) {
			$elements = bss_footer_elements();
		} else {
			$elements = bss_product_page_elements();
		}
		bss_save_elementor( $id, $elements, $doc_type );

		if ( 'product_page' === $type ) {
			// Preview the template in Elementor with the first Product.
			$preview = get_posts(
				array(
					'post_type'      => 'bs_product',
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'fields'         => 'ids',
				)
			);
			update_post_meta(
				$id,
				'_elementor_page_settings',
				array(
					'preview_type' => 'single/bs_product',
					'preview_id'   => $preview ? (int) $preview[0] : '',
				)
			);
		}
	}

	update_post_meta( $id, '_elementor_conditions', array( $condition ) );
	bss_regenerate_theme_conditions();

	$state = $exists ? ( $rebuild ? 'rebuilt' : 'already exists, layout left as it is' ) : 'created';
	$line  = sprintf( '"%s": %s (Templates → Theme Builder), %s.', $title, $state, $where );
	if ( 'header' === $type && ! class_exists( 'Jet_Menu' ) ) {
		$line .= ' JetMenu is not active, so the header uses Elementor Pro\'s Nav Menu; activate JetMenu and tick "Rebuild the header and footer" to switch.';
	}
	return $line;
}

/**
 * Rebuild Elementor Pro's display-conditions cache after setting conditions directly.
 */
function bss_regenerate_theme_conditions() {
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

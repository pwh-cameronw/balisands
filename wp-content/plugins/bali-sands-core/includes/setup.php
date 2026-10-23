<?php
/**
 * Tools → Bali Sands setup: builds the site content in one click.
 *
 * Every step is safe to re-run. Existing ranges, the menu and the quote form
 * are left alone; the homepage layout is only rebuilt when asked.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'bs_setup_menu' );

function bs_setup_menu() {
	add_management_page( __( 'Bali Sands setup', 'bali-sands' ), __( 'Bali Sands setup', 'bali-sands' ), 'manage_options', 'bs-setup', 'bs_setup_page' );
}

add_action( 'admin_notices', 'bs_setup_notice' );

function bs_setup_notice() {
	if ( ! get_transient( 'bs_setup_notice' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'tools_page_bs-setup' === $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s <a href="%s">%s</a></p></div>',
		esc_html__( 'Bali Sands Core is active.', 'bali-sands' ),
		esc_url( admin_url( 'tools.php?page=bs-setup' ) ),
		esc_html__( 'Run the site setup →', 'bali-sands' )
	);
}

/**
 * Setup screen.
 */
function bs_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$log = null;
	if ( isset( $_POST['bs_run_setup'] ) && check_admin_referer( 'bs_run_setup' ) ) {
		$log = bs_run_setup(
			array(
				'rebuild_home'    => ! empty( $_POST['bs_rebuild_home'] ),
				'rebuild_header'  => ! empty( $_POST['bs_rebuild_header'] ),
				'replace_headers' => ! empty( $_POST['bs_replace_headers'] ),
			)
		);
		delete_transient( 'bs_setup_notice' );
	}

	$checks = array(
		array( 'Child theme "Bali Sands" active', 'bali-sands' === get_stylesheet(), true ),
		array( 'Elementor', did_action( 'elementor/loaded' ) > 0, true ),
		array( 'Gravity Forms (quote form)', class_exists( 'GFAPI' ), true ),
		array( 'ACF Pro (Yard details, tile and dropdown fields)', function_exists( 'acf_add_options_page' ), false ),
		array( 'Elementor Pro (site header in Theme Builder)', defined( 'ELEMENTOR_PRO_VERSION' ), false ),
		array( 'JetEngine (optional: category page listings later)', class_exists( 'Jet_Engine' ), false ),
	);
	$home_id   = (int) get_option( 'bs_home_page_id' );
	$header_id = (int) get_option( 'bs_header_template_id' );
	$others    = defined( 'ELEMENTOR_PRO_VERSION' ) ? bs_other_active_headers() : array();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bali Sands setup', 'bali-sands' ); ?></h1>
		<p><?php esc_html_e( 'Creates the product ranges, the dropdown menu, the quote form, the homepage and (with Elementor Pro) the site header, and sets Elementor\'s colours and fonts. Safe to run again: it only adds what is missing.', 'bali-sands' ); ?></p>

		<h2><?php esc_html_e( 'Before you run it', 'bali-sands' ); ?></h2>
		<table class="widefat striped" style="max-width:640px">
			<tbody>
			<?php foreach ( $checks as $c ) : ?>
				<tr>
					<td><?php echo esc_html( $c[0] ); ?></td>
					<td style="width:140px">
						<?php
						if ( $c[1] ) {
							echo '<span style="color:#2f7d32">✓ ' . esc_html__( 'Ready', 'bali-sands' ) . '</span>';
						} else {
							echo $c[2] ? '<strong style="color:#b32d2e">' . esc_html__( 'Needed', 'bali-sands' ) . '</strong>' : '<span style="color:#787c82">' . esc_html__( 'Not active', 'bali-sands' ) . '</span>';
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<form method="post" style="margin-top:20px">
			<?php wp_nonce_field( 'bs_run_setup' ); ?>
			<?php if ( $home_id && get_post( $home_id ) ) : ?>
				<p><label><input type="checkbox" name="bs_rebuild_home" value="1"> <?php esc_html_e( 'Rebuild the homepage layout (replaces any Elementor edits to the homepage)', 'bali-sands' ); ?></label></p>
			<?php endif; ?>
			<?php if ( $header_id && get_post( $header_id ) ) : ?>
				<p><label><input type="checkbox" name="bs_rebuild_header" value="1"> <?php esc_html_e( 'Rebuild the Elementor header (replaces any Elementor edits to "Bali Sands header")', 'bali-sands' ); ?></label></p>
			<?php endif; ?>
			<?php if ( $others ) : ?>
				<p><label><input type="checkbox" name="bs_replace_headers" value="1" checked> 
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: template names */
						__( 'Stop showing the other header template (%s), so the Bali Sands header is used. It stays under Templates.', 'bali-sands' ),
						implode( ', ', wp_list_pluck( $others, 'post_title' ) )
					)
				);
				?>
				</label></p>
			<?php endif; ?>
			<?php submit_button( __( 'Run setup', 'bali-sands' ), 'primary', 'bs_run_setup', false ); ?>
		</form>

		<?php if ( $log ) : ?>
			<h2><?php esc_html_e( 'Result', 'bali-sands' ); ?></h2>
			<ul style="list-style:disc;padding-left:20px">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $home_id || get_option( 'bs_home_page_id' ) ) : ?>
				<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'View the homepage', 'bali-sands' ); ?></a></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Run every setup step.
 *
 * @param array $opts rebuild_home, rebuild_header, replace_headers (bools).
 * @return string[] Log lines.
 */
function bs_run_setup( $opts = array() ) {
	$opts = wp_parse_args(
		$opts,
		array(
			'rebuild_home'    => false,
			'rebuild_header'  => false,
			'replace_headers' => false,
		)
	);
	$log = array();
	bs_register_post_types();

	$log[] = bs_setup_media();
	$log[] = bs_setup_ranges();
	$log[] = bs_setup_menu_items();
	$log[] = bs_setup_logo();

	$form = bs_create_quote_form();
	$log[] = is_wp_error( $form ) ? 'Quote form skipped: ' . $form->get_error_message() . ' Run setup again once it is active.' : 'Quote form ready (Gravity form #' . $form . ').';

	$log[] = bs_setup_homepage( $opts['rebuild_home'] );
	$log[] = bs_setup_elementor_header( $opts['rebuild_header'], $opts['replace_headers'] );
	$log[] = bs_setup_elementor_kit();

	if ( did_action( 'elementor/loaded' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	return array_filter( $log );
}

/* -------------------------------------------------------------------------
 * Content
 * ---------------------------------------------------------------------- */

/**
 * Photos from the current Wix site, keyed by Wix media id.
 *
 * @return array<string,string>
 */
function bs_setup_media_ids() {
	return array(
		'logo'      => '351dd2_961af1e610c14cf7bcbfffe63205eb2b~mv2.png',
		'gazebos'   => '351dd2_e7b39f77cf1d46f5be873da5ec2ac51c~mv2.jpg',
		'water'     => '351dd2_aff19043db7247238f84cff3c8b0ea93~mv2.jpeg',
		'birdbaths' => '351dd2_7c1534af6a2a42ecb6027d75aa83dc61~mv2.jpeg',
		'pots'      => '351dd2_a6ffe29bdddc4f67829b81c09dd5d1f4~mv2.jpg',
		'statues'   => '351dd2_ac0941d04e64498396a6f9bdeac965f0~mv2.jpg',
		'gifts'     => '351dd2_3c2e9dbe0ae547baaca9e61201888fb2~mv2.jpeg',
	);
}

/**
 * Copy the Wix photos into the Media Library (once).
 *
 * @return string Log line.
 */
function bs_setup_media() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$map     = (array) get_option( 'bs_media_map', array() );
	$added   = 0;
	$failed  = array();
	$titles  = array(
		'logo'      => 'Bali Sands logo',
		'gazebos'   => 'Gazebos and outdoor furniture',
		'water'     => 'Water features',
		'birdbaths' => 'Bird baths',
		'pots'      => 'Pots',
		'statues'   => 'Statues',
		'gifts'     => 'Handycrafts and giftware',
	);
	foreach ( bs_setup_media_ids() as $key => $wix_id ) {
		if ( ! empty( $map[ $key ] ) && get_post( $map[ $key ] ) ) {
			continue;
		}
		$tmp = download_url( 'https://static.wixstatic.com/media/' . $wix_id, 60 );
		if ( is_wp_error( $tmp ) ) {
			$failed[] = $titles[ $key ];
			continue;
		}
		$ext  = pathinfo( $wix_id, PATHINFO_EXTENSION );
		$file = array(
			'name'     => 'bali-sands-' . $key . '.' . ( 'jpeg' === $ext ? 'jpg' : $ext ),
			'tmp_name' => $tmp,
		);
		$id   = media_handle_sideload( $file, 0, $titles[ $key ] );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$failed[] = $titles[ $key ];
			continue;
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $titles[ $key ] );
		$map[ $key ] = $id;
		++$added;
	}
	update_option( 'bs_media_map', $map, false );

	$line = sprintf( 'Photos: %d copied from the old site into the Media Library.', $added );
	if ( $failed ) {
		$line .= ' Could not download: ' . implode( ', ', $failed ) . '. Upload these by hand, or run setup again.';
	}
	return $line;
}

/**
 * Attachment ID for a setup photo, or 0.
 *
 * @param string $key Media key.
 * @return int
 */
function bs_setup_media_id( $key ) {
	$map = (array) get_option( 'bs_media_map', array() );
	return ! empty( $map[ $key ] ) && get_post( $map[ $key ] ) ? (int) $map[ $key ] : 0;
}

/**
 * The twelve ranges, grouped by department.
 *
 * @return array
 */
function bs_setup_range_data() {
	return array(
		'landscape-supplies' => array(
			'name'   => 'Landscape supplies',
			'ranges' => array(
				array( 'Sands', 'Brickies · paving · white', '#e8d3a9', '#cbb083' ),
				array( 'Soils', 'Garden · lawn · veggie', '#6b5340', '#3f3025' ),
				array( 'Stones', 'Gravel · pebbles · metal', '#9aa0a2', '#6a7073' ),
				array( 'Mulch', 'Pine · jarrah · black', '#8a5a3b', '#5a3722' ),
				array( 'Limestone & pavers', 'Blocks · pavers · cement', '#ded4bd', '#b7a98a' ),
				array( 'Edging, tools & retic', 'Edging · tools · fittings', '#4c6b4f', '#2f4034' ),
			),
		),
		'bali-imports'       => array(
			'name'   => 'Bali imports',
			'ranges' => array(
				array( 'Gazebos & furniture', 'Huts · teak · rattan', '', '', 'gazebos' ),
				array( 'Water features', 'Ceramic · stone · GRC', '', '', 'water' ),
				array( 'Bird baths', 'Stone · marble · basins', '', '', 'birdbaths' ),
				array( 'Pots', 'Glazed · limestone · GRC', '', '', 'pots' ),
				array( 'Statues', 'Buddha · animals · plaques', '', '', 'statues' ),
				array( 'Handycrafts & gifts', 'Wall art · mirrors · homewares', '', '', 'gifts' ),
			),
		),
	);
}

/**
 * Create departments and ranges that do not exist yet.
 *
 * @return string Log line.
 */
function bs_setup_ranges() {
	$added = 0;
	foreach ( bs_setup_range_data() as $slug => $dept ) {
		$term = term_exists( $slug, 'bs_department' );
		if ( ! $term ) {
			$term = wp_insert_term( $dept['name'], 'bs_department', array( 'slug' => $slug ) );
		}
		if ( is_wp_error( $term ) ) {
			continue;
		}
		$term_id = (int) $term['term_id'];

		foreach ( $dept['ranges'] as $order => $r ) {
			$existing = get_posts(
				array(
					'post_type'      => 'bs_range',
					'post_status'    => 'any',
					'title'          => $r[0],
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			if ( $existing ) {
				$post_id = (int) $existing[0];
			} else {
				$post_id = wp_insert_post(
					array(
						'post_type'    => 'bs_range',
						'post_status'  => 'publish',
						'post_title'   => $r[0],
						'post_excerpt' => $r[1],
						'menu_order'   => $order + 1,
					)
				);
				if ( is_wp_error( $post_id ) || ! $post_id ) {
					continue;
				}
				wp_set_object_terms( $post_id, $term_id, 'bs_department' );
				if ( $r[2] ) {
					update_post_meta( $post_id, 'bs_swatch_from', $r[2] );
					update_post_meta( $post_id, 'bs_swatch_to', $r[3] );
				}
				++$added;
			}
			// Attach the photo later too, in case the download failed the first time.
			if ( ! empty( $r[4] ) && ! has_post_thumbnail( $post_id ) && bs_setup_media_id( $r[4] ) ) {
				set_post_thumbnail( $post_id, bs_setup_media_id( $r[4] ) );
			}
		}
	}
	return sprintf( 'Product ranges: %d added (12 in total across Landscape supplies and Bali imports).', $added );
}

/**
 * Dropdown menu content, as drawn in design 6a.
 *
 * @return array
 */
function bs_setup_menu_data() {
	$g = static function ( $a, $b ) {
		return array( 'from' => $a, 'to' => $b );
	};
	return array(
		array( 'Sands', '/#landscape-supplies', 'See all sands', array(
			array( 'Brickies & plasterers sand', 'For mortar, render and plaster', $g( '#e3b97c', '#c48e4f' ) ),
			array( 'Paving sand', 'Bedding and joint sand under pavers', $g( '#e9d3a6', '#cdb07c' ) ),
			array( 'White washed & play sand', 'Sandpits and top dressing', $g( '#f6efe1', '#ddd0b6' ) ),
			array( 'Fill sand', 'Levelling, backfill and site fill', $g( '#f0d38c', '#d3ad5a' ) ),
		) ),
		array( 'Soils', '/#landscape-supplies', 'See all soils', array(
			array( 'Garden & veggie mixes', 'Ready-to-plant blends for beds', $g( '#6b5340', '#3f3025' ) ),
			array( 'Lawn top dress', 'Sandy loam for levelling lawns', $g( '#b99a6c', '#87693f' ) ),
			array( 'Compost & manure', 'Bagged or bulk, composted', $g( '#5a4632', '#2f241a' ) ),
			array( 'Soil improvers', 'Clay and wetters for Perth sand', $g( '#a68a6a', '#73593d' ) ),
		) ),
		array( 'Stones', '/#landscape-supplies', 'See all stones', array(
			array( 'Gravel & blue metal', 'Drainage, paths and concrete', $g( '#9aa0a2', '#6a7073' ) ),
			array( 'Decorative pebbles', 'River, white and coloured stone', $g( '#d6d2c8', '#a6a092' ) ),
			array( 'Road base', 'Sub-base under paving and slabs', $g( '#c9b79a', '#94815f' ) ),
		) ),
		array( 'Mulch', '/#landscape-supplies', 'See all mulch', array(
			array( 'Pine bark', 'Long-lasting, natural colour', $g( '#8a5a3b', '#5a3722' ) ),
			array( 'Jarrah & forest fines', 'Fine texture for garden beds', $g( '#6e3f28', '#45251a' ) ),
			array( 'Black mulch', 'Coloured mulch for a clean look', $g( '#3a3430', '#1d1a18' ) ),
		) ),
		array( 'Paving', '/#landscape-supplies', 'See paving & building', array(
			array( 'Limestone blocks', 'Retaining walls and garden edges', $g( '#ded4bd', '#b7a98a' ) ),
			array( 'Pavers', 'Limestone and concrete pavers', $g( '#cfc6b5', '#9e937e' ) ),
			array( 'Cement & bagged products', 'GP cement, concrete and mortar mix', $g( '#b8b4ac', '#8d8980' ) ),
			array( 'Edging, tools & retic', 'Garden edging, tools and fittings', $g( '#4c6b4f', '#2f4034' ) ),
		) ),
		array( 'Bali imports', '/#bali-imports', 'See all Bali imports', array(
			array( 'Gazebos & outdoor furniture', 'Bali huts, teak, rattan and rootwood', 'gazebos' ),
			array( 'Water features', 'Ceramic, stone and GRC', 'water' ),
			array( 'Bird baths', 'Stone, marble and carved basins', 'birdbaths' ),
			array( 'Pots', 'Glazed, limestone, hand-carved and GRC', 'pots' ),
			array( 'Statues', 'Buddha, animals and wall plaques', 'statues' ),
			array( 'Handycrafts & giftware', 'Wall art, mirrors and homewares', 'gifts' ),
		) ),
		array( 'Delivery', '/#delivery', '', array() ),
		array( 'Contact', '/#quote', '', array() ),
		array( 'Get a quote', '/#quote', '', array(), 'bs-cta' ),
	);
}

/**
 * Create the primary menu (once) and assign it to the header.
 *
 * @return string Log line.
 */
function bs_setup_menu_items() {
	$menu = wp_get_nav_menu_object( 'Main menu' );
	$line = 'Menu: "Main menu" already exists, left as it is.';

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( 'Main menu' );
		if ( is_wp_error( $menu_id ) ) {
			return 'Menu skipped: ' . $menu_id->get_error_message();
		}
		$pos = 0;
		foreach ( bs_setup_menu_data() as $top ) {
			$parent = wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $top[0],
					'menu-item-url'      => home_url( $top[1] ),
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => ++$pos,
					'menu-item-classes'  => $top[4] ?? '',
				)
			);
			if ( is_wp_error( $parent ) ) {
				continue;
			}
			if ( $top[2] ) {
				update_post_meta( $parent, 'bs_see_all', $top[2] );
			}
			foreach ( $top[3] as $child ) {
				$item = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'       => $child[0],
						'menu-item-description' => $child[1],
						'menu-item-url'         => home_url( $top[1] ),
						'menu-item-type'        => 'custom',
						'menu-item-status'      => 'publish',
						'menu-item-parent-id'   => $parent,
						'menu-item-position'    => ++$pos,
					)
				);
				if ( is_wp_error( $item ) ) {
					continue;
				}
				if ( is_array( $child[2] ) ) {
					update_post_meta( $item, 'bs_swatch_from', $child[2]['from'] );
					update_post_meta( $item, 'bs_swatch_to', $child[2]['to'] );
				} else {
					update_post_meta( $item, 'bs_media_key', $child[2] );
				}
			}
		}
		$menu = wp_get_nav_menu_object( $menu_id );
		$line = 'Menus: "Main menu" created with six dropdowns, plus the two footer lists.';
	}

	// Photos may have arrived after the menu was made.
	$items = wp_get_nav_menu_items( $menu->term_id );
	foreach ( $items ? $items : array() as $item ) {
		$key = get_post_meta( $item->ID, 'bs_media_key', true );
		if ( $key && ! get_post_meta( $item->ID, 'bs_thumb', true ) && bs_setup_media_id( $key ) ) {
			update_post_meta( $item->ID, 'bs_thumb', bs_setup_media_id( $key ) );
		}
	}

	$footer = array(
		'footer_supplies' => array( 'Footer: Landscape supplies', '/#landscape-supplies', array( 'Sands', 'Soils', 'Stones', 'Mulch', 'Limestone, pavers & cement', 'Edging, tools & retic' ) ),
		'footer_imports'  => array( 'Footer: Bali imports', '/#bali-imports', array( 'Gazebos & outdoor furniture', 'Water features', 'Bird baths', 'Pots', 'Statues', 'Handycrafts & giftware' ) ),
	);
	$footer_ids = array();
	foreach ( $footer as $location => $def ) {
		$existing = wp_get_nav_menu_object( $def[0] );
		if ( $existing ) {
			$footer_ids[ $location ] = $existing->term_id;
			continue;
		}
		$id = wp_create_nav_menu( $def[0] );
		if ( is_wp_error( $id ) ) {
			continue;
		}
		foreach ( $def[2] as $i => $title ) {
			wp_update_nav_menu_item(
				$id,
				0,
				array(
					'menu-item-title'    => $title,
					'menu-item-url'      => home_url( $def[1] ),
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => $i + 1,
				)
			);
		}
		$footer_ids[ $location ] = $id;
	}

	if ( 'bali-sands' === get_stylesheet() ) {
		$locations            = (array) get_theme_mod( 'nav_menu_locations', array() );
		if ( empty( $locations['primary'] ) ) {
			$locations['primary'] = $menu->term_id;
		}
		foreach ( $footer_ids as $location => $id ) {
			if ( empty( $locations[ $location ] ) ) {
				$locations[ $location ] = $id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	} else {
		$line .= ' Activate the Bali Sands theme and run setup again to put it in the header.';
	}
	return $line;
}

/**
 * Use the downloaded logo as the site logo if none is set.
 *
 * @return string Log line.
 */
function bs_setup_logo() {
	$logo = bs_setup_media_id( 'logo' );
	if ( 'bali-sands' !== get_stylesheet() || ! $logo || get_theme_mod( 'custom_logo' ) ) {
		return '';
	}
	set_theme_mod( 'custom_logo', $logo );
	return 'Logo: set from the old site.';
}

/* -------------------------------------------------------------------------
 * Homepage (Elementor data)
 * ---------------------------------------------------------------------- */

/**
 * Create the homepage (or rebuild its layout) and make it the front page.
 *
 * @param bool $rebuild Overwrite existing Elementor data.
 * @return string Log line.
 */
function bs_setup_homepage( $rebuild ) {
	$page_id = (int) get_option( 'bs_home_page_id' );
	$exists  = $page_id && get_post( $page_id );

	if ( ! $exists ) {
		$page_id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'Home',
				'post_name'   => 'home',
			)
		);
		if ( is_wp_error( $page_id ) || ! $page_id ) {
			return 'Homepage could not be created.';
		}
		update_option( 'bs_home_page_id', $page_id );
	}

	if ( ! $exists || $rebuild ) {
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
		update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
		update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( bs_homepage_elements() ) ) );
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
		}
		delete_post_meta( $page_id, '_elementor_css' );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );

	if ( $exists && ! $rebuild ) {
		return 'Homepage: already exists, layout left as it is. Set as the front page.';
	}
	return $exists ? 'Homepage: layout rebuilt.' : 'Homepage: created and set as the front page.';
}

/**
 * Stable 7-character Elementor element id.
 *
 * @param string $seed Seed.
 * @return string
 */
function bs_el_id( $seed ) {
	return substr( md5( 'bali-sands-' . $seed ), 0, 7 );
}

/**
 * A full-width, zero-padding container holding one widget.
 *
 * @param string      $seed     Id seed.
 * @param string      $widget   Widget type.
 * @param array       $settings Widget settings.
 * @param string|null $bg       Background colour.
 * @return array
 */
function bs_el_section( $seed, $widget, $settings, $bg = null ) {
	$zero = array(
		'unit'     => 'px',
		'top'      => '0',
		'right'    => '0',
		'bottom'   => '0',
		'left'     => '0',
		'isLinked' => true,
	);
	$container = array(
		'content_width' => 'full',
		'padding'       => $zero,
		'margin'        => $zero,
		'flex_gap'      => array(
			'unit'     => 'px',
			'size'     => 0,
			'column'   => '0',
			'row'      => '0',
			'isLinked' => true,
		),
		'css_classes'   => 'bs-row bs-row--' . $seed,
	);
	if ( $bg ) {
		$container['background_background'] = 'classic';
		$container['background_color']      = $bg;
	}
	return array(
		'id'       => bs_el_id( 'c-' . $seed ),
		'elType'   => 'container',
		'settings' => $container,
		'elements' => array(
			array(
				'id'         => bs_el_id( 'w-' . $seed ),
				'elType'     => 'widget',
				'widgetType' => $widget,
				'settings'   => $settings,
				'elements'   => array(),
			),
		),
		'isInner'  => false,
	);
}

/**
 * Elementor repeater rows need an _id each.
 *
 * @param string $seed Seed.
 * @param array  $rows Rows.
 * @return array
 */
function bs_el_rows( $seed, $rows ) {
	foreach ( $rows as $i => &$row ) {
		$row['_id'] = bs_el_id( $seed . $i );
	}
	return $rows;
}

/**
 * The homepage, top to bottom, as Elementor elements (design 6a).
 *
 * @return array
 */
function bs_homepage_elements() {
	$sand  = '#faf6ee';
	$white = '#ffffff';
	$green = '#2f4034';
	$link  = static function ( $url ) {
		return array(
			'url'         => $url,
			'is_external' => '',
			'nofollow'    => '',
		);
	};
	$wave  = static function ( $seed, $shape, $from, $to ) {
		return bs_el_section(
			$seed,
			'bs-wave',
			array(
				'shape' => $shape,
				'from'  => $from,
				'to'    => $to,
			)
		);
	};

	return array(
		bs_el_section(
			'hero',
			'bs-hero',
			array(
				'title'      => 'Landscape supplies & Bali imports in Perth',
				'text'       => 'Sand, soil, stone and mulch by the scoop or the truckload. Plus pots, statues, water features and huts straight from Bali. One yard in Forrestdale, open seven days.',
				'btn1_text'  => 'Shop the range',
				'btn1_link'  => $link( '#landscape-supplies' ),
				'btn1_style' => 'green',
				'btn2_text'  => 'How much do I need?',
				'btn2_link'  => $link( '#calculator' ),
				'btn2_style' => 'outline',
				'checks'     => bs_el_rows(
					'check',
					array(
						array( 'text' => 'Delivery 7 days' ),
						array( 'text' => 'Bulk or bagged' ),
						array( 'text' => 'Trade accounts' ),
					)
				),
				'card_text'  => '{hours}',
			),
			$sand
		),
		$wave( 'wave1', 'a', $sand, $white ),
		bs_el_section(
			'supplies',
			'bs-range-grid',
			array(
				'department' => 'landscape-supplies',
				'eyebrow'    => 'Landscape supplies',
				'tone'       => 'earth',
				'title'      => 'Everything for the job, in one yard',
				'more_text'  => 'View all supplies',
				'more_link'  => $link( '' ),
				'anchor'     => 'landscape-supplies',
				'spacing'    => 'first',
			),
			$white
		),
		bs_el_section(
			'imports',
			'bs-range-grid',
			array(
				'department' => 'bali-imports',
				'eyebrow'    => 'Bali imports',
				'tone'       => 'green',
				'title'      => 'Then finish it off with a bit of Bali',
				'more_text'  => 'View all imports',
				'more_link'  => $link( '' ),
				'anchor'     => 'bali-imports',
				'spacing'    => 'last',
			),
			$white
		),
		$wave( 'wave2', 'b', $white, $green ),
		bs_el_section(
			'calculator',
			'bs-calculator',
			array(
				'eyebrow'    => 'Calculator',
				'title'      => 'How many cubes do you need?',
				'length'     => 6,
				'width'      => 4,
				'depth'      => 50,
				'btn1_text'  => 'Get a quote',
				'btn1_link'  => $link( '#quote' ),
				'btn1_style' => 'amber',
				'btn2_text'  => 'Call the yard',
				'btn2_link'  => $link( '{tel}' ),
				'btn2_style' => 'outline',
				'anchor'     => 'calculator',
			),
			$green
		),
		$wave( 'wave3', 'a', $green, $white ),
		bs_el_section(
			'delivery',
			'bs-media-split',
			array(
				'eyebrow'    => 'Delivery',
				'title'      => 'Delivered across Perth, or take it home yourself',
				'cards'      => bs_el_rows(
					'fact',
					array(
						array(
							'title' => 'Bulk loads',
							'text'  => '0.5–8m³ per tipper',
						),
						array(
							'title' => 'Bagged',
							'text'  => 'For the small jobs',
						),
					)
				),
				'btn1_text'  => 'Delivery options',
				'btn1_link'  => $link( '#quote' ),
				'btn1_style' => 'green',
				'btn2_text'  => 'Get a quote',
				'btn2_link'  => $link( '#quote' ),
				'btn2_style' => 'outline',
				'anchor'     => 'delivery',
			),
			$white
		),
		$wave( 'wave4', 'b', $white, $sand ),
		bs_el_section(
			'reasons',
			'bs-cards',
			array(
				'title' => 'Why people come back',
				'cards' => bs_el_rows(
					'card',
					array(
						array(
							'title' => 'Delivery 7 days',
							'text'  => 'Tipped on the verge or the driveway, anywhere in the metro.',
						),
						array(
							'title' => 'Two shops in one',
							'text'  => 'Pick up the base materials and the pots and statues in the same trip.',
						),
						array(
							'title' => 'Imported direct',
							'text'  => 'Bali pieces come straight from the makers, priced close to wholesale.',
						),
						array(
							'title' => 'Ask us first',
							'text'  => "Tell us about the job at the counter and we'll tell you what you need and how much.",
						),
					)
				),
			),
			$sand
		),
		$wave( 'wave5', 'a', $sand, $white ),
		bs_el_section(
			'reviews',
			'bs-reviews',
			array(
				'title'     => 'What customers say',
				'more_text' => 'All Google reviews',
				'more_link' => $link( '' ),
				'reviews'   => bs_el_rows(
					'review',
					array_fill(
						0,
						3,
						array(
							'stars' => 5,
							'text'  => '[Paste a real Google review here.]',
							'name'  => '[Customer name]',
						)
					)
				),
			),
			$white
		),
		$wave( 'wave6', 'b', $white, $sand ),
		bs_el_section(
			'quote',
			'bs-quote',
			array(
				'eyebrow' => 'Get a quote',
				'title'   => 'Tell us about the job',
				'form_id' => '0',
				'anchor'  => 'quote',
			),
			$sand
		),
	);
}

/* -------------------------------------------------------------------------
 * Elementor site settings
 * ---------------------------------------------------------------------- */

/**
 * Global colours, Open Sans and a 1280px content width on the active kit.
 *
 * @return string Log line.
 */
function bs_setup_elementor_kit() {
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id || ! get_post( $kit_id ) ) {
		return 'Elementor colours and fonts skipped: no active Elementor kit yet. Open Elementor once, then run setup again.';
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	$color = static function ( $id, $title, $hex ) {
		return array(
			'_id'   => $id,
			'title' => $title,
			'color' => $hex,
		);
	};
	$font  = static function ( $id, $title, $weight ) {
		return array(
			'_id'                       => $id,
			'title'                     => $title,
			'typography_typography'     => 'custom',
			'typography_font_family'    => 'Open Sans',
			'typography_font_weight'    => $weight,
		);
	};

	$settings['system_colors'] = array(
		$color( 'primary', 'Garden green', '#2F4034' ),
		$color( 'secondary', 'Terracotta', '#8A5A3B' ),
		$color( 'text', 'Body text', '#4D463B' ),
		$color( 'accent', 'Quote amber', '#E2A63B' ),
	);
	$settings['custom_colors'] = array(
		$color( 'bs_ink', 'Ink', '#201C17' ),
		$color( 'bs_muted', 'Muted text', '#6B6355' ),
		$color( 'bs_sand', 'Sand', '#FAF6EE' ),
		$color( 'bs_line', 'Line', '#ECE6DA' ),
		$color( 'bs_sage', 'Sage', '#B6CFAE' ),
		$color( 'bs_white', 'White', '#FFFFFF' ),
	);
	$settings['system_typography'] = array(
		$font( 'primary', 'Headings', '800' ),
		$font( 'secondary', 'Subheadings', '700' ),
		$font( 'text', 'Body', '400' ),
		$font( 'accent', 'Buttons', '700' ),
	);
	$settings['body_typography_typography']  = 'custom';
	$settings['body_typography_font_family'] = 'Open Sans';
	$settings['body_color']                  = '#4D463B';
	$settings['body_background_background']  = 'classic';
	$settings['body_background_color']       = '#FFFFFF';
	$settings['container_width']             = array(
		'unit' => 'px',
		'size' => 1280,
	);

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );

	return 'Elementor: global colours, Open Sans and 1280px content width set.';
}

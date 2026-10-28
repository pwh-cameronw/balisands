<?php
/**
 * Site content and design tokens, from Claude Design "Bali Sands Homepage", option 6a
 * ("Homepage v3 — working-yard feel").
 *
 * These are only the starting values the setup writes into WordPress and
 * Elementor. After setup, edit everything in Elementor or WordPress, not here.
 *
 * @package BaliSandsSite
 */

defined( 'ABSPATH' ) || exit;

/**
 * Business details used in the header, footer and homepage.
 *
 * @return array<string,string>
 */
function bss_business() {
	return array(
		'phone'   => '(08) 9393 2832',
		'tel'     => 'tel:+61893932832',
		'street'  => '462 Nicholson Road',
		'suburb'  => 'Forrestdale WA 6112',
		'address' => '462 Nicholson Road, Forrestdale WA 6112',
		'short'   => '462 Nicholson Rd, Forrestdale',
		'hours'   => 'Mon–Sat 6am–4:30pm · Sun 8am–2pm',
		'abn'     => '36 155 602 547',
		'maps'    => 'https://www.google.com/maps/search/?api=1&query=462+Nicholson+Road+Forrestdale+WA+6112',
		// From the design; the client to confirm (shown in [square brackets] on the Contact page).
		'email'   => 'sales@balisands.com.au',
	);
}

/**
 * Opening hours, Monday first: [day, hours].
 *
 * @return array<int,array{0:string,1:string}>
 */
function bss_hours() {
	return array(
		array( 'Monday', '6:00am – 4:30pm' ),
		array( 'Tuesday', '6:00am – 4:30pm' ),
		array( 'Wednesday', '6:00am – 4:30pm' ),
		array( 'Thursday', '6:00am – 4:30pm' ),
		array( 'Friday', '6:00am – 4:30pm' ),
		array( 'Saturday', '6:00am – 4:30pm' ),
		array( 'Sunday', '8:00am – 2:00pm' ),
	);
}

/**
 * Contact enquiry form topics (design 10a), in order. The first is pre-selected.
 *
 * @return string[]
 */
function bss_contact_topics() {
	return array( 'Landscape supplies', 'Bali imports', 'Delivery quote', 'Trade account', 'Something else' );
}

/**
 * About page content (design 8a, "About us — rebuilt in the built-site style").
 * Text in [square brackets] needs the client's real details.
 *
 * sides: [page key, label, label colour, heading, text, link text, photo media key ('' = placeholder)]
 *
 * @return array
 */
function bss_about() {
	return array(
		'eyebrow'  => 'About Bali Sands',
		'title'    => 'A local supply yard with a bit of Bali out the back',
		'lead'     => 'We\'re a family-run yard on Nicholson Road in Forrestdale. Landscapers and builders come in for sand, soil and mulch, and people keep coming back for the pots, statues and huts.',
		'story'    => array(
			'[Founder name] started Bali Sands in [year], [selling sand and soil to local builders and landscapers]. On a trip to Bali in [year], [he/she/they] brought back a first container of pots and statues. They sold quickly, and the yard has carried both ever since.',
			'We still buy from the same makers in Bali and Java and ship direct to Forrestdale. Our prices stay close to wholesale, and because the pieces are handmade, no two are quite the same.',
			'[Add a line about the team today: who runs the yard, who drives the trucks, who answers the phone.]',
		),
		'sides'    => array(
			array( 'supplies', 'Landscape supplies', 'secondary', 'The materials for the job', 'Sand, soil, stone, mulch, limestone, pavers, cement, edging and retic. Buy it by the bag or by the cube, load your trailer at the yard, or have it tipped on your driveway anywhere in Perth.', 'Shop supplies →', '' ),
			array( 'imports', 'Bali imports', 'primary', 'The finishing touches', 'Gazebos and furniture, water features, bird baths, pots, statues and giftware. We pick them out in Bali and Java and ship them straight to Forrestdale.', 'Shop imports →', 'imports' ),
		),
		'sourcing' => array(
			array( 'Chosen in person', 'We visit the workshops and choose every order ourselves.' ),
			array( 'Shipped direct', 'Full containers come straight to Forrestdale, with no middlemen adding to the price.' ),
			array( 'Pick your own', 'Most pieces are handmade, so come down and choose the exact one you want.' ),
		),
		'visit'    => array(
			'title' => 'The best way to choose is in person',
			'text'  => 'Walk the yard, see the colour of the sand, and pick your pot or statue from the real thing rather than a photo. Tell us about the job at the counter and we\'ll tell you what you need.',
		),
	);
}

/**
 * Elementor global colours: id => [title, hex]. The first four are Elementor's system colours.
 *
 * @return array<string,array{0:string,1:string}>
 */
function bss_palette() {
	return array(
		'primary'         => array( 'Garden green', '#2F4034' ),
		'secondary'       => array( 'Terracotta', '#8A5A3B' ),
		'text'            => array( 'Body text', '#4D463B' ),
		'accent'          => array( 'Quote amber', '#E2A63B' ),
		'bs_ink'          => array( 'Ink', '#201C17' ),
		'bs_muted'        => array( 'Muted text', '#6B6355' ),
		'bs_white'        => array( 'White', '#FFFFFF' ),
		'bs_sand'         => array( 'Sand background', '#FAF6EE' ),
		'bs_line'         => array( 'Card border', '#ECE6DA' ),
		'bs_field'        => array( 'Field border', '#D9D1C1' ),
		'bs_green_hover'  => array( 'Green (hover)', '#24332A' ),
		'bs_amber_hover'  => array( 'Amber (hover)', '#CF9327' ),
		'bs_terracotta_hover' => array( 'Terracotta (hover)', '#74492F' ),
		'bs_green_tint'   => array( 'Menu hover tint', '#EEF4EA' ),
		'bs_open'         => array( 'Open dot', '#4F9A4C' ),
		'bs_mint'         => array( 'Label on green', '#B6CFAE' ),
		'bs_topbar_text'  => array( 'Top bar text', '#EEF2EA' ),
		'bs_on_green'     => array( 'Text on green', '#FFFFFFD9' ),
		'bs_footer_text'  => array( 'Footer text', '#C9C0AE' ),
		'bs_footer_muted' => array( 'Footer small print', '#8A7F6A' ),
		'bs_photo'        => array( 'Photo placeholder', '#E2D6BF' ),
		'bs_pill_off'     => array( 'Pill (not sold)', '#F3EFE7' ),
		'bs_pill_off_text' => array( 'Pill text (not sold)', '#A39A8A' ),
		'bs_label_warm'   => array( 'Label on terracotta', '#F3D9A8' ),
		'bs_photo_dark'   => array( 'Photo placeholder (on green)', '#3A4C3E' ),
		'bs_photo_light'  => array( 'Photo placeholder (light)', '#E6DCCB' ),
	);
}

/**
 * Elementor global fonts (all Open Sans): id => settings. The first four are Elementor's system fonts.
 *
 * Keys: title, weight, size (px), lh (em), ls (px letter spacing), upper, t (tablet size), m (mobile size).
 *
 * @return array<string,array>
 */
function bss_type_scale() {
	return array(
		'primary'        => array( 'title' => 'Headings', 'weight' => '800' ),
		'secondary'      => array( 'title' => 'Subheadings', 'weight' => '700' ),
		'text'           => array( 'title' => 'Body', 'weight' => '400', 'size' => 16, 'lh' => 1.65 ),
		'accent'         => array( 'title' => 'Buttons', 'weight' => '700', 'size' => 15, 'lh' => 1, 'm' => 14 ),
		'bs_display'     => array( 'title' => 'Page title (H1)', 'weight' => '800', 'size' => 58, 'lh' => 1.04, 'ls' => -0.58, 't' => 46, 'm' => 36 ),
		'bs_h2'          => array( 'title' => 'Section heading (H2)', 'weight' => '800', 'size' => 36, 'lh' => 1.12, 't' => 30, 'm' => 27 ),
		'bs_eyebrow'     => array( 'title' => 'Eyebrow label', 'weight' => '700', 'size' => 13, 'lh' => 1, 'ls' => 1.56, 'upper' => true ),
		'bs_lead'        => array( 'title' => 'Lead paragraph', 'weight' => '400', 'size' => 18, 'lh' => 1.65, 'm' => 16 ),
		'bs_card'        => array( 'title' => 'Card title', 'weight' => '700', 'size' => 15.5, 'lh' => 1.25 ),
		'bs_card_lg'     => array( 'title' => 'Card title (large)', 'weight' => '700', 'size' => 18, 'lh' => 1.3 ),
		'bs_small'       => array( 'title' => 'Small text', 'weight' => '400', 'size' => 12.5, 'lh' => 1.4 ),
		'bs_card_text'   => array( 'title' => 'Card text', 'weight' => '400', 'size' => 14, 'lh' => 1.6 ),
		'bs_link'        => array( 'title' => 'Text link', 'weight' => '700', 'size' => 14, 'lh' => 1 ),
		'bs_nav'         => array( 'title' => 'Menu', 'weight' => '600', 'size' => 14, 'lh' => 1 ),
		'bs_topbar'      => array( 'title' => 'Top bar', 'weight' => '600', 'size' => 12.5, 'lh' => 1 ),
		'bs_brand'       => array( 'title' => 'Wordmark', 'weight' => '800', 'size' => 22, 'lh' => 1, 'ls' => 1.32, 'upper' => true, 'm' => 17 ),
		'bs_badge'       => array( 'title' => 'Badge / ticks', 'weight' => '600', 'size' => 13.5, 'lh' => 1.4 ),
		'bs_figure'      => array( 'title' => 'Big figure', 'weight' => '800', 'size' => 36, 'lh' => 1 ),
		'bs_phone'       => array( 'title' => 'Phone number', 'weight' => '700', 'size' => 22, 'lh' => 1.3 ),
		'bs_footer'      => array( 'title' => 'Footer text', 'weight' => '400', 'size' => 13.5, 'lh' => 2 ),
		'bs_footer_head' => array( 'title' => 'Footer heading', 'weight' => '700', 'size' => 13, 'lh' => 1 ),
		'bs_fine'        => array( 'title' => 'Small print', 'weight' => '400', 'size' => 12, 'lh' => 1.4 ),
		'bs_h2_sm'       => array( 'title' => 'Section heading, small', 'weight' => '800', 'size' => 32, 'lh' => 1.15, 't' => 28, 'm' => 25 ),
		'bs_h3'          => array( 'title' => 'Block heading (H3)', 'weight' => '800', 'size' => 26, 'lh' => 1.2, 't' => 24, 'm' => 22 ),
		'bs_crumb'       => array( 'title' => 'Breadcrumbs', 'weight' => '400', 'size' => 13, 'lh' => 1.4 ),
		'bs_meta'        => array( 'title' => 'Card meta ("Best for")', 'weight' => '600', 'size' => 13, 'lh' => 1.45 ),
		'bs_pill'        => array( 'title' => 'Pill label', 'weight' => '700', 'size' => 12, 'lh' => 1 ),
		'bs_button_sm'   => array( 'title' => 'Button, small', 'weight' => '700', 'size' => 13.5, 'lh' => 1 ),
		'bs_h1_sm'       => array( 'title' => 'Page title, long (H1)', 'weight' => '800', 'size' => 54, 'lh' => 1.05, 'ls' => -0.54, 't' => 42, 'm' => 32 ),
		'bs_story'       => array( 'title' => 'Story paragraph', 'weight' => '400', 'size' => 17, 'lh' => 1.8, 'm' => 16 ),
	);
}

/**
 * Photos copied from the current Wix site: key => [Wix media id, title].
 *
 * @return array<string,array{0:string,1:string}>
 */
function bss_wix_media() {
	return array(
		'logo'      => array( '351dd2_961af1e610c14cf7bcbfffe63205eb2b~mv2.png', 'Bali Sands logo' ),
		'imports'   => array( '351dd2_a7edc3ab647d481ba3d088569a16260c~mv2.jpeg', 'Bali imports — carved stone panel' ),
		'gazebos'   => array( '351dd2_e7b39f77cf1d46f5be873da5ec2ac51c~mv2.jpg', 'Gazebos and outdoor furniture' ),
		'water'     => array( '351dd2_aff19043db7247238f84cff3c8b0ea93~mv2.jpeg', 'Water features' ),
		'birdbaths' => array( '351dd2_7c1534af6a2a42ecb6027d75aa83dc61~mv2.jpeg', 'Bird baths' ),
		'pots'      => array( '351dd2_a6ffe29bdddc4f67829b81c09dd5d1f4~mv2.jpg', 'Pots' ),
		'statues'   => array( '351dd2_ac0941d04e64498396a6f9bdeac965f0~mv2.jpg', 'Statues' ),
		'gifts'     => array( '351dd2_3c2e9dbe0ae547baaca9e61201888fb2~mv2.jpeg', 'Handycrafts and giftware' ),
	);
}

/**
 * WordPress pages.
 *
 * key => [
 *   0 title,
 *   1 parent key ('' = top level),
 *   2 slug,
 *   3 featured image: a bss_wix_media() key, or [from, to] hex pair for a placeholder swatch,
 *   4 short description (page excerpt; also the menu row subtitle),
 * ]
 *
 * Landscape supplies photos don't exist yet, so those pages get a colour swatch
 * as their featured image. Replace it with a photo and the menu and homepage
 * cards follow (the homepage card image is set separately in Elementor).
 *
 * @return array<string,array>
 */
function bss_pages() {
	return array(
		'supplies'         => array( 'Landscape Supplies', '', 'landscape-supplies', array( '#8A5A3B', '#4A3422' ), 'Sand, soil, stone and mulch by the scoop or the truckload.' ),

		'sands'            => array( 'Sands', 'supplies', 'sands', array( '#E8D3A9', '#CBB083' ), 'Brickies · paving · white' ),
		'sands-brickies'   => array( 'Brickies & Plasterers Sand', 'sands', 'brickies-plasterers-sand', array( '#E3B97C', '#C48E4F' ), 'For mortar, render and plaster' ),
		'sands-paving'     => array( 'Paving Sand', 'sands', 'paving-sand', array( '#E9D3A6', '#CDB07C' ), 'Bedding and joint sand under pavers' ),
		'sands-white'      => array( 'White Washed & Play Sand', 'sands', 'white-washed-play-sand', array( '#F6EFE1', '#DDD0B6' ), 'Sandpits and top dressing' ),
		'sands-fill'       => array( 'Fill Sand', 'sands', 'fill-sand', array( '#F0D38C', '#D3AD5A' ), 'Levelling, backfill and site fill' ),

		'soils'            => array( 'Soils', 'supplies', 'soils', array( '#6B5340', '#3F3025' ), 'Garden · lawn · veggie' ),
		'soils-garden'     => array( 'Garden & Veggie Mixes', 'soils', 'garden-veggie-mixes', array( '#6B5340', '#3F3025' ), 'Ready-to-plant blends for beds' ),
		'soils-topdress'   => array( 'Lawn Top Dress', 'soils', 'lawn-top-dress', array( '#B99A6C', '#87693F' ), 'Sandy loam for levelling lawns' ),
		'soils-compost'    => array( 'Compost & Manure', 'soils', 'compost-manure', array( '#5A4632', '#2F241A' ), 'Bagged or bulk, composted' ),
		'soils-improvers'  => array( 'Soil Improvers', 'soils', 'soil-improvers', array( '#A68A6A', '#73593D' ), 'Clay and wetters for Perth sand' ),

		'stones'           => array( 'Stones', 'supplies', 'stones', array( '#9AA0A2', '#6A7073' ), 'Gravel · pebbles · metal' ),
		'stones-gravel'    => array( 'Gravel & Blue Metal', 'stones', 'gravel-blue-metal', array( '#9AA0A2', '#6A7073' ), 'Drainage, paths and concrete' ),
		'stones-pebbles'   => array( 'Decorative Pebbles', 'stones', 'decorative-pebbles', array( '#D6D2C8', '#A6A092' ), 'River, white and coloured stone' ),
		'stones-roadbase'  => array( 'Road Base', 'stones', 'road-base', array( '#C9B79A', '#94815F' ), 'Sub-base under paving and slabs' ),

		'mulch'            => array( 'Mulch', 'supplies', 'mulch', array( '#8A5A3B', '#5A3722' ), 'Pine · jarrah · black' ),
		'mulch-pine'       => array( 'Pine Bark', 'mulch', 'pine-bark', array( '#8A5A3B', '#5A3722' ), 'Long-lasting, natural colour' ),
		'mulch-jarrah'     => array( 'Jarrah & Forest Fines', 'mulch', 'jarrah-forest-fines', array( '#6E3F28', '#45251A' ), 'Fine texture for garden beds' ),
		'mulch-black'      => array( 'Black Mulch', 'mulch', 'black-mulch', array( '#3A3430', '#1D1A18' ), 'Coloured mulch for a clean look' ),

		'paving'           => array( 'Limestone, Pavers & Cement', 'supplies', 'limestone-pavers-cement', array( '#DED4BD', '#B7A98A' ), 'Blocks · pavers · cement' ),
		'paving-limestone' => array( 'Limestone Blocks', 'paving', 'limestone-blocks', array( '#DED4BD', '#B7A98A' ), 'Retaining walls and garden edges' ),
		'paving-pavers'    => array( 'Pavers', 'paving', 'pavers', array( '#CFC6B5', '#9E937E' ), 'Limestone and concrete pavers' ),
		'paving-cement'    => array( 'Cement & Bagged Products', 'paving', 'cement-bagged-products', array( '#B8B4AC', '#8D8980' ), 'GP cement, concrete and mortar mix' ),

		'edging'           => array( 'Edging, Tools & Retic', 'supplies', 'edging-tools-retic', array( '#4C6B4F', '#2F4034' ), 'Edging · tools · fittings' ),

		'imports'          => array( 'Bali Imports', '', 'bali-imports', 'imports', 'Pots, statues, water features and huts straight from Bali.' ),
		'imports-gazebos'  => array( 'Gazebos & Outdoor Furniture', 'imports', 'gazebos-outdoor-furniture', 'gazebos', 'Bali huts, teak, rattan and rootwood' ),
		'imports-water'    => array( 'Water Features', 'imports', 'water-features', 'water', 'Ceramic, stone and GRC' ),
		'imports-birdbath' => array( 'Bird Baths', 'imports', 'bird-baths', 'birdbaths', 'Stone, marble and carved basins' ),
		'imports-pots'     => array( 'Pots', 'imports', 'pots', 'pots', 'Glazed, limestone, hand-carved and GRC' ),
		'imports-statues'  => array( 'Statues', 'imports', 'statues', 'statues', 'Buddha, animals and wall plaques' ),
		'imports-gifts'    => array( 'Handycrafts & Giftware', 'imports', 'handycrafts-giftware', 'gifts', 'Wall art, mirrors and homewares' ),

		'delivery'         => array( 'Delivery', '', 'delivery', '', 'Tipper loads from half a cube upwards, across the Perth metro.' ),
		'about'            => array( 'About', '', 'about', '', 'A local yard in Forrestdale, with a little bit of Bali out the back.' ),
		'contact'          => array( 'Contact', '', 'contact', '', 'Tell us about the job and we\'ll work out what you need.' ),

		'privacy'          => array( 'Privacy Policy', '', 'privacy', '', '[Privacy policy to come.]' ),
		'terms'            => array( 'Terms & Conditions', '', 'terms', '', '[Terms and conditions to come.]' ),
	);
}

/**
 * Main menu: [label, page key, [child page keys] or "mega", "see all" label].
 *
 * Children are shown with their page's featured image and description.
 * "mega" builds the item's sub-menu from bss_mega_menu() (a column per
 * category, its sub-categories under it); JetMenu shows it as a mega menu.
 *
 * @return array<int,array>
 */
function bss_main_menu() {
	return array(
		array( 'Landscape supplies', 'supplies', 'mega', 'See all landscape supplies' ),
		array( 'Bali imports', 'imports', array( 'imports-gazebos', 'imports-water', 'imports-birdbath', 'imports-pots', 'imports-statues', 'imports-gifts' ), 'See all Bali imports' ),
		array( 'Delivery', 'delivery', array(), '' ),
		array( 'About us', 'about', array(), '' ),
		array( 'Contact', 'contact', array(), '' ),
	);
}

/**
 * The Landscape supplies mega menu: one tab per category, with the
 * sub-categories as cards in its pane.
 * [tab title, page key, [sub-category page keys], one-line description].
 *
 * @return array<int,array>
 */
function bss_mega_menu() {
	return array(
		array( 'Sands', 'sands', array( 'sands-brickies', 'sands-paving', 'sands-white', 'sands-fill' ), 'Bedding, mortar, sandpits and fill.' ),
		array( 'Soils', 'soils', array( 'soils-garden', 'soils-topdress', 'soils-compost', 'soils-improvers' ), 'Garden beds, lawns and veggie patches.' ),
		array( 'Stones', 'stones', array( 'stones-gravel', 'stones-pebbles', 'stones-roadbase' ), 'Drainage, paths, driveways and decoration.' ),
		array( 'Mulch', 'mulch', array( 'mulch-pine', 'mulch-jarrah', 'mulch-black' ), 'Keeps the moisture in and the weeds down.' ),
		array( 'Limestone, pavers & cement', 'paving', array( 'paving-limestone', 'paving-pavers', 'paving-cement' ), 'Retaining walls, paving and concrete.' ),
		array( 'Edging, tools & retic', 'edging', array(), 'Garden edging, hand tools and reticulation fittings.' ),
	);
}

/**
 * Footer menus: menu name => [label => page key].
 *
 * @return array<string,array<string,string>>
 */
function bss_footer_menus() {
	return array(
		'Footer: Landscape supplies' => array(
			'Sands'                      => 'sands',
			'Soils'                      => 'soils',
			'Stones'                     => 'stones',
			'Mulch'                      => 'mulch',
			'Limestone, pavers & cement' => 'paving',
			'Edging, tools & retic'      => 'edging',
		),
		'Footer: Company'            => array(
			'Delivery' => 'delivery',
			'About us' => 'about',
			'Contact'  => 'contact',
		),
		'Footer: Bali imports'       => array(
			'Gazebos & outdoor furniture' => 'imports-gazebos',
			'Water features'              => 'imports-water',
			'Bird baths'                  => 'imports-birdbath',
			'Pots'                        => 'imports-pots',
			'Statues'                     => 'imports-statues',
			'Handycrafts & giftware'      => 'imports-gifts',
		),
	);
}

/**
 * Category page layouts (design 7a), keyed by bss_pages() key.
 *
 * Only Soils is designed so far. Another category gets the same layout by
 * adding an entry here (and running setup with its Rebuild box ticked).
 *
 * - group:    parent page key (for the breadcrumb and "Other …" cards)
 * - intro:    [lead paragraph, second paragraph]
 * - photo:    [from, to] gradient shown until a photo is set
 * - jobs:     "Pick by the job" cards: [title, text]
 * - noun:     plural used in "8 soils"
 *
 * The product cards are Products (see bss_products()) in this category,
 * shown by a JetEngine Listing Grid.
 *
 * @return array<string,array>
 */
function bss_category_pages() {
	return array(
		'soils' => array(
			'group' => 'supplies',
			'noun'  => 'soils',
			'intro' => array(
				'Perth\'s sandy ground doesn\'t hold water or nutrients for long. The right soil gives new beds, lawns and veggie patches a proper start. All our soils are blended for local conditions.',
				'Buy by the bag for small jobs, or by the cubic metre for anything bigger. We deliver across the Perth metro, or you can load up at the yard.',
			),
			'photo' => array( '#6B5340', '#3F3025' ),
			'jobs'  => array(
				array( 'New garden bed', 'Garden mix, 200–300mm deep' ),
				array( 'Veggie patch', 'Veggie mix, 300mm deep' ),
				array( 'Levelling a lawn', 'Lawn top dress, 10–20mm' ),
				array( 'Laying new turf', 'Turf underlay, 100mm deep' ),
			),
		),
	);
}

/**
 * Starting Products (from design 7a): added once by the setup, then edited under Products.
 *
 * [name, short description, best for, sold as (bss_sold_as() keys), product category (bss_pages() key), placeholder photo [from, to]]
 *
 * A product in a sub-category (e.g. Garden & Veggie Mixes) also shows on its
 * parent's page (Soils), because the listing includes sub-categories.
 *
 * @return array<int,array>
 */
function bss_products() {
	$soil = array( '#7A6149', '#4A382A' );
	$bali = array( '#B9A27E', '#7C6648' );
	return array(
		array( 'Garden mix', 'Sandy loam blended with compost. A general-purpose soil for new garden beds and topping up old ones.', 'Garden beds · shrubs · natives', array( 'bulk', 'bagged' ), 'soils-garden', $soil ),
		array( 'Veggie mix', 'Extra compost and manure for heavy feeders. Ready to plant straight into raised beds.', 'Raised beds · vegetables · herbs', array( 'bulk', 'bagged' ), 'soils-garden', $soil ),
		array( 'Lawn top dress', 'Fine sandy loam that spreads evenly to level bumps and fill hollows in established lawns.', 'Lawn levelling · top dressing', array( 'bulk', 'bagged' ), 'soils-topdress', $soil ),
		array( 'Turf underlay', 'A sandy base with added organics, spread before laying new roll-on turf.', 'New lawns · roll-on turf', array( 'bulk' ), 'soils-topdress', $soil ),
		array( 'Premium potting mix', 'Free-draining mix with slow-release fertiliser, made for pots and planters.', 'Pots · planters · indoor plants', array( 'bagged' ), 'soils-garden', $soil ),
		array( 'Compost', 'Composted green waste that adds organic matter and helps sandy Perth soil hold water.', 'Soil improving · mulching', array( 'bulk', 'bagged' ), 'soils-compost', $soil ),
		array( 'Cow manure', 'Aged and composted, so it won\'t burn plants. Dig it in before planting or use it as a top-up.', 'Feeding beds · roses · fruit trees', array( 'bulk', 'bagged' ), 'soils-compost', $soil ),
		array( 'Clay & soil improver', 'Bentonite clay blend that stops water running straight through sandy soil.', 'Sandy soil · water retention', array( 'bulk', 'bagged' ), 'soils-improvers', $soil ),

		// Bali imports: placeholders for the "New arrivals" on the Bali Imports page (design 14a),
		// to be replaced with real pieces. Not sold in bulk or bags, so no "Sold as".
		array( '[Carved stone Buddha, 1.2m]', '[One-off hand-carved piece. Add the real size, stone and price guide.]', '[Gardens · courtyards · entries]', array(), 'imports-statues', $bali ),
		array( '[Glazed pot set of 3]', '[Add the colours, sizes and whether they\'re sold as a set.]', '[Patios · entries · feature planting]', array(), 'imports-pots', $bali ),
		array( '[Teak bench, 1.8m]', '[Add the timber, finish and dimensions.]', '[Verandahs · gardens · poolside]', array(), 'imports-gazebos', $bali ),
		array( '[Three-tier water feature]', '[Add the material, height and pump details.]', '[Courtyards · gardens · entries]', array(), 'imports-water', $bali ),
	);
}

/**
 * "Sold as" terms: slug => label (shown as pills on the product cards).
 *
 * @return array<string,string>
 */
function bss_sold_as() {
	return array(
		'bulk'   => 'Bulk m³',
		'bagged' => 'Bagged',
	);
}

/**
 * Homepage category cards: [page key, card title, card subtitle].
 *
 * @return array<string,array<int,array{0:string,1:string,2:string}>>
 */
function bss_home_cards() {
	return array(
		'supplies' => array(
			array( 'sands', 'Sands', 'Brickies · paving · white' ),
			array( 'soils', 'Soils', 'Garden · lawn · veggie' ),
			array( 'stones', 'Stones', 'Gravel · pebbles · metal' ),
			array( 'mulch', 'Mulch', 'Pine · jarrah · black' ),
			array( 'paving', 'Limestone & pavers', 'Blocks · pavers · cement' ),
			array( 'edging', 'Edging, tools & retic', 'Edging · tools · fittings' ),
		),
		'imports'  => array(
			array( 'imports-gazebos', 'Gazebos & furniture', 'Huts · teak · rattan' ),
			array( 'imports-water', 'Water features', 'Ceramic · stone · GRC' ),
			array( 'imports-birdbath', 'Bird baths', 'Stone · marble · basins' ),
			array( 'imports-pots', 'Pots', 'Glazed · limestone · GRC' ),
			array( 'imports-statues', 'Statues', 'Buddha · animals · plaques' ),
			array( 'imports-gifts', 'Handycrafts & gifts', 'Wall art · mirrors · homewares' ),
		),
	);
}

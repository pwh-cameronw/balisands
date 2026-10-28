# Bali Sands — WordPress build (design 6a)

The homepage from Claude Design **"Bali Sands Homepage" → 6a, "Homepage v3 — working-yard feel"**, rebuilt for:

| Part | Used for |
| --- | --- |
| **Hello Elementor** (parent theme) + **Bali Sands child theme** | Theme |
| **Elementor** + **Elementor Pro** | Homepage, header and footer (Theme Builder) |
| **JetMenu** (Crocoblock) | Main menu with dropdowns and the Landscape supplies mega menu |
| **Gravity Forms** | "Get a quote" form |
| **JetEngine** (Crocoblock) | Product cards on the category pages (Listing Grid) |
| **ACF** (free or Pro) | Product card fields (Short description, Best for) |
| **Bali Sands Site** (custom plugin) | Builds the site in one click, plus menu images and the m³ calculator |

WooCommerce is **not** used. Categories are ordinary WordPress pages.

## What's in this repo

| Folder | What it is |
| --- | --- |
| `wp-content/themes/bali-sands-child/` | Child theme |
| `wp-content/plugins/bali-sands-site/` | Site plugin |

To install from WordPress's uploader, zip each folder (`bali-sands-child.zip`, `bali-sands-site.zip`), keeping the folder itself at the top of the zip. Or copy both folders into the site's `wp-content/`.

**Hello Elementor** and **Elementor** (free) come from wordpress.org (**Appearance → Themes → Add New** and **Plugins → Add New**). **Elementor Pro, JetMenu, JetEngine and Gravity Forms are paid, licensed plugins, so they aren't included.** Download them from your Elementor, Crocoblock and Gravity Forms accounts. ACF (free) installs from **Plugins → Add New**.

## Install (fresh WordPress 6.3+, PHP 7.4+)

1. **Appearance → Themes → Add New → Upload**: install **Hello Elementor** (Add New, search for it), then upload, install and **activate** `bali-sands-child.zip`.
2. **Plugins → Add New → Upload**: install and activate **Elementor**, Elementor Pro, JetMenu, JetEngine, ACF, Gravity Forms, then `bali-sands-site.zip`.
3. **Tools → Bali Sands setup**. Check that everything shows ✓, then click **Run setup**. It:
   - copies the logo and Bali imports photos from the old Wix site into the Media Library. It also makes colour-swatch placeholder images for the landscape supplies categories, which have no photos yet.
   - creates the pages. Each category is a page with a featured image:
     - **Landscape Supplies** → Sands, Soils, Stones, Mulch, Limestone Pavers & Cement, and Edging Tools & Retic. Each of these has its own sub-category pages, e.g. Sands → Paving Sand.
     - **Bali Imports** → Gazebos & Outdoor Furniture, Water Features, Bird Baths, Pots, Statues, and Handycrafts & Giftware.
     - Delivery, About and Contact.
   - creates the **Main menu**: Landscape supplies (a mega menu), Bali imports (a dropdown), Delivery, About us and Contact. On a site that already has the Main menu, it adds any of these plain links that are missing (e.g. About us) in their place, and leaves the other items alone. It also creates the two footer menus (Landscape supplies and Bali imports).
   - creates the **Landscape supplies mega menu** in JetMenu: a column each for Sands, Soils, Stones, Mulch and Paving, with the category's photo, title and sub-categories.
   - the footer's small print has "Website By:" and the Perth Web Hosting logo, linking to perthwebhosting.net.au in a new tab. The logo file ships inside the plugin (`assets/img/pwh-logo-noborder.svg`), because WordPress blocks SVG uploads to the Media Library by default.
   - sets Elementor's **Global Colors** and **Global Fonts** (Open Sans), plus default buttons, links and a 1200px content width.
   - creates the **Get a quote** Gravity form for the homepage, and the **Contact enquiry** form for the Contact page.
   - creates the **Home** page in Elementor and makes it the front page.
   - creates the **Product categories** (matching the category pages) and adds the 8 soils from the design as **Products**.
   - creates the JetEngine **Product card** listing.
   - builds the **Soils** category page in Elementor (design 7a), with a JetEngine Listing Grid of the soils.
   - builds the **About** page in Elementor (design 8a).
   - builds the **Contact** page in Elementor (design 10a).
   - builds the **Bali Imports** page in Elementor (design 14a).
   - creates the **header** and **footer** in Elementor Pro's Theme Builder and shows them on the whole site.
   - creates the **Product page** in the Theme Builder (a Single template) and shows it on every Product's **View details** page.

   It's safe to run again: it only adds what's missing. The homepage, category pages, About page, header, footer and product page layouts are only replaced if you tick their **Rebuild** box.

   **Updating an installed site:** upload the new `bali-sands-site.zip` (WordPress offers to replace the old version), then run setup again. It adds anything new (e.g. the Product page template) and leaves everything else as it is. To switch from the old menu (Sands, Soils, Stones, Mulch and Paving as separate items) to the mega menu, tick **Rebuild the main menu**. That replaces the Main menu's items, so it undoes any changes made to them in Appearance → Menus.

## How it's put together

### Homepage: one widget per element
Every heading, paragraph, image, button and card is its own Elementor widget, inside Flexbox/Grid containers. No section is a single drop-in widget. The one exception is the calculator's three inputs and live result: that is a `[bs_calculator]` Shortcode widget, because it needs JavaScript. Its heading, text, note and buttons are still separate widgets.

Sections, top to bottom: Hero → Landscape supplies → Bali imports → Calculator → Delivery → Why people come back → Reviews → Get a quote.

On phones, the hero's two buttons sit side by side and share the width. So do the pairs at the top of the category pages and product pages. That's a rule in the child theme's `style.css`, so it applies without rebuilding any page.

### Category pages (design 7a)
The **Soils** page is built in Elementor from design 7a. Its sections, top to bottom:
- **Intro:** breadcrumbs, title, two paragraphs, buttons and a photo panel.
- **Pick by the job:** four cards.
- **Our soils:** a count ("8 soils") and a JetEngine **Listing Grid** of the Products in the *Soils* product category, sub-categories included.
- **Not sure how much to order?:** a terracotta band.
- **Other landscape supplies:** linked cards for the other categories.

To give another category the same layout, add its content to `bss_category_pages()` in `includes/data.php`. Then run setup with **Rebuild the category page layouts** ticked. Or duplicate the Soils page in Elementor and change the text and the Listing Grid's category.

### About page (design 8a)
The About page is built in Elementor from design 8a, "About us — rebuilt in the built-site style". Its sections, top to bottom:
- **Intro (green):** breadcrumbs, the "About Bali Sands" label, title, lead paragraph and a photo panel.
- **Our story:** "How it started" with an early-days photo panel, and three paragraphs.
- **Two shops in one yard (sand):** a card each for Landscape supplies and Bali imports, linking to their pages.
- **How our imports get here (terracotta):** three numbered cards with photo panels.
- **Come and see us:** "The best way to choose is in person". It has address, hours and phone, **Get directions** (opens Google Maps in a new tab) and **Get a quote** buttons, and a Google Maps embed.

Text in **[square brackets]** in "Our story" needs the client's real details. The photo panels are containers; set a photo under **Style → Background → Image**. The Bali imports card already shows the carved-panel photo from the old site.

### Bali Imports page (design 14a)
The **Bali Imports** page is built in Elementor from design 14a. Its sections, top to bottom:
- **Hero:** the carved stone panel photo with a dark green fade. It has breadcrumbs, "Imported direct from Bali & Java", the title, a lead paragraph, and **Browse the range** and **Visit the yard** buttons.
- **Shop by category:** "Six ways to bring Bali home". It has a card for each category page, with the page's featured image, a description and "Shop now →".
- **New arrivals:** the four newest **Products** in the *Bali imports* product category (or its sub-categories), newest first. The first two are tagged "Just landed" and the rest "New", and each links to its product page. It's the `[bs_new_arrivals]` Shortcode widget, so it updates as products are added.
- **Handmade:** "No two pieces are the same". The photo panel is a container; set a photo under **Style → Background → Image**.
- **How to buy:** three steps, then **Send an enquiry** (which opens the Contact form with *Bali imports* picked) and the phone number.

Setup adds four placeholder Bali imports products with the design's names in [square brackets], so New arrivals has something to show. Replace them with real pieces under **Products**: give each a photo, put it in the right Bali imports category, and delete the placeholders.

### Contact page (design 10a)
The Contact page is built in Elementor from design 10a, "Contact us — call, visit or send an enquiry". Its sections, top to bottom:
- **Intro (green):** breadcrumbs, "Get in touch", a lead paragraph and three cards:
  - **Call the yard:** the phone number and a **Call now** button.
  - **Email us:** the email address and a **Send an email** button.
  - **Visit the yard:** the address and **Get directions**, which opens Google Maps in a new tab.
- **Send an enquiry:** the **Contact enquiry** Gravity form, beside an **Opening hours** card and a **Trade customers** card.
- **Getting here (terracotta):** the address, three steps for arriving at the yard, **Open in Google Maps** and **Delivery areas** buttons, and a Google Maps embed.

**The enquiry form** (*Forms → Contact enquiry*) starts with a row of topic pills: Landscape supplies, Bali imports, Delivery quote, Trade account and Something else. The fields below change with the topic, using Gravity Forms conditional logic:
- *Landscape supplies* and *Delivery quote* add the delivery suburb and how many m³.
- *Trade account* adds the business name and ABN.
- Each topic gets its own message box with a prompt to suit.

A link can pre-select a topic with `?topic=` in the URL. For example, the trade card links to `/contact/?topic=Trade%20account#enquiry`. Under the button it says "We reply the same business day." After sending, the form shows a thank-you message with a tick. Enquiries go to the site admin email; change that under *Forms → Contact enquiry → Settings → Notifications*.

**Opening hours** lists each day. Today's row is highlighted with "Today", using Perth time, so it's right even on cached pages. The hours are separate Heading widgets, so you can edit them in Elementor. They come from `bss_hours()` in the plugin's `includes/data.php`.

### Products
Each product card is a **Product** post (admin menu → **Products**):

| Card part | Where it's edited |
| --- | --- |
| Name | The product title |
| Photo | **Card photo** (featured image) |
| Description | ACF field **Short description** |
| "Best for: …" | ACF field **Best for** |
| Bulk m³ / Bagged pills | **Sold as** checkboxes (sidebar) |
| Which category page it appears on | **Product categories** checkboxes (sidebar) |
| **View details** page write-up | The main editor content (the "About this product" part of the product's own page at `/products/…`) |

To add a product, go to **Products → Add New**: fill in the fields, tick its category and "Sold as", and publish. It appears on its category page straight away. A product in a sub-category (e.g. *Garden & Veggie Mixes*) also shows on the parent page (*Soils*). Set **Order** (Page Attributes) to control position: lower numbers come first once the Listing Grid is set to order by menu order.

The card layout is the JetEngine listing **Product card** (*JetEngine → Listings*), built in Elementor with JetEngine's Dynamic Image, Dynamic Field, Dynamic Terms and Dynamic Link widgets. Its styling is in the plugin's `assets/css/products.css`, which reads the Global Colors and Fonts.

### Product page (View details)
Every Product's own page uses one layout: the Theme Builder template **Product page** (*Templates → Theme Builder → Single*), shown on all Products. Change it once in Elementor and every product page follows. It's built in the same style as the Soils page. Sections, top to bottom:
- **Intro (green):** breadcrumbs (Home / Landscape supplies / Soils / … / the product), the **Card photo**, the product name, its *Short description*, "Best for: …", the *Sold as* pills, and **Get a delivered price** / **How much do I need?** buttons.
- **About this product:** the product's main editor content, beside an **Ordering** card (delivery, trailer loading, hours, address, **Get a price** and call buttons).
- **Not sure how much to order?:** the same terracotta band as the category pages.
- **More from this range:** a JetEngine Listing Grid of up to four other Products from the same product category, using the **Product card** listing. Without JetEngine it shows links to the landscape supplies categories instead.

The name, photo, short description and "Best for" come from Elementor Pro's Post Title, Featured Image and ACF dynamic tags. The breadcrumbs and pills are the shortcodes `[bs_product_crumbs]` and `[bs_product_sold_as]`.

New products get a starting write-up with four headings (What's in it, How to use it, How much you'll need, Good for). Text in **[square brackets]** needs the yard's real details. Products made by an earlier version keep their one-line write-up until it's edited.

### Header logo
The header shows the logo on its own, with no "BALI SANDS" wordmark. The logo is 66px wide (52px on phones). To change it, edit the header template's logo Image widget. **Updating an installed site:** run setup with **Rebuild only the header layout** ticked.

### Header width
The header's backgrounds (green top bar, white bar) run the full width of the screen. Its content (logo, menu and **Get a quote**) is boxed to the site's content width, 1200px, so it sits centred and lines up with the page sections and footer. To change the width, go to **Site Settings → Layout → Content Width**. It applies to the whole site.

**Updating from 1.3.0 or earlier:** run setup with **Rebuild the header and footer layouts** ticked. Or, in the header template, select the top bar and the white bar containers and set **Layout → Content Width** to *Boxed*.

### Footer layout (desktop and tablet)
- **First column:** the white logo, the blurb, and **Get a quote** and phone number buttons.
- **Landscape supplies** and **Bali imports:** amber uppercase labels (Global Font *Eyebrow label*, colour *Quote amber*) over their footer menus.
- **Find the yard:** the address in bold, an hours table (Mon–Sat / Sun & hols), and an amber **Get directions →** link that opens Google Maps.
- **Small print:** the copyright and ABN, with "Website by" and the Perth Web Hosting logo.

On phones the footer keeps its own layout (see below), with white headings on the accordions.

### Footer logo
The footer's first column starts with the **white Bali Sands logo**, above the line "Landscape supplies and Bali imports from our Forrestdale yard…". It replaces the "BALI SANDS" wordmark and links to the homepage. It's 90px wide. The file ships in the plugin as `assets/img/logo-white.webp`; to use a different one, pick an image in the footer template's logo Image widget. The header keeps the colour logo.

### Footer on phones
Below 768px wide the footer has its own layout, built from phone-only widgets in the footer template (Elementor **Advanced → Responsive → Hide on desktop / tablet**):
- **Brand:** the white logo (the same one as on desktop).
- **Buttons:** **Call the yard** (amber) and **Directions**, which opens Google Maps.
- **Visit:** the address and opening hours.
- **Link lists:** **Landscape supplies**, **Bali imports** and **Company** (Delivery, About us, Contact; the menu *Footer: Company*) as accordions. Tap a heading to open its links; the amber + turns to −.
- **Small print:** "© Bali Sands · Privacy · Terms", with the "Website by" credit on the same line.

On phones the blurb, the "Find the yard" column and the ABN line are hidden. Desktop and tablet are unchanged. The accordion code is in the plugin's `assets/css/footer.css` and `assets/js/footer.js`. Any footer column with the CSS class `bs-footer-acc`, whose heading has `bs-footer-acc__head`, works as an accordion.

Setup also creates **Privacy Policy** and **Terms & Conditions** pages for the Privacy and Terms links. They hold placeholder text in [square brackets] until the real policies are written.

**Updating an installed site:** run setup with **Rebuild only the footer layout** ticked. That creates the Company menu and the Privacy and Terms pages, and leaves the header alone.

### Changing section backgrounds
Each section is a top-level container whose background is a **Global Color**. To change one section, select it and go to **Style → Background**, then pick another global colour (or a custom one). To change a colour everywhere, go to **Site Settings → Global Colors**.

The wavy edges are Elementor **shape dividers**, under **Style → Shape Divider → Top** on each section. A wave's colour matches the section above it. If you change a section's background, change the wave colour on the section below to match.

The wave into the footer is a **Bottom** shape divider in *Ink* on each page's last section, not part of the footer. That way it always matches whatever section sits above the footer. When you build a new page in Elementor, give its last section the same bottom wave and about 60px of extra bottom padding.

### Global styles (one place)
**Elementor → Site Settings**:
- **Global Colors**: Garden green (primary), Terracotta, Body text, Quote amber, Ink, Sand background, Card border, etc.
- **Global Fonts**: all Open Sans. Headings, Body, Buttons, Page title (H1), Section heading (H2), Eyebrow label, Card title, Menu, Footer text, etc.

Every widget the setup creates is linked to these globals, so changing a global updates the whole site.

The child theme's Gravity Forms styles, the calculator and the menu dropdown also read the same globals through Elementor's CSS variables.

### Categories → pages
The category cards on the homepage, the menu items and the footer lists all link to the category **pages**. To add a category:
1. Create a page and set its parent (e.g. *Landscape Supplies* or *Bali Imports*).
2. Set its **Featured image** and its **Excerpt** (the short description).
3. Add it to **Appearance → Menus → Main menu** under the right dropdown.

### Landscape supplies mega menu
In the Main menu, **Landscape supplies** opens a **mega menu** (JetMenu) in two panes:
- **Left, on sand:** a tab for each category: Sands, Soils, Stones, Mulch, Limestone, pavers & cement, and Edging, tools & retic. Each tab has its page's photo and name.
- **Right:** the category under the pointer. It shows the category's title, a one-line description, **See all →**, and a card for each **Product** in that category (its card photo and name, linking to the product page), in the same order as the category page's grid (up to 9).
  - The products are live: the `[bs_mega_products category="soils"]` Shortcode widget in each pane lists them when the menu loads. A product added or reordered under **Products** shows up in the menu straight away.
  - While a category has no products yet, its pane shows a card per **sub-category** page instead. Once you add products to it, they replace the sub-category cards automatically.

Hovering or tabbing to a category switches the right pane (the plugin's `assets/js/mega-menu.js`). Clicking a tab, title or card opens that page. The photos are the pages' featured images.

- **Editing the panel:** go to **Appearance → Menus**, hover the *Landscape supplies* item, click **JetMenu**, then **Edit Mega Menu Item Content**. It opens in Elementor. Every tab, title, description and card is its own widget.
  - A tab and its pane are paired by their CSS classes (`bs-mega__tab--2` goes with `bs-mega__pane--2`). Keep the numbers matching if you add or reorder categories.
  - The descriptions are set in `bss_mega_menu()` in the plugin's `includes/data.php`.
- **Panel size and position:** it opens just under the white header bar and spans the header's full content width, from the logo to **Get a quote**. The styles are in the plugin's `assets/css/menu.css`.
- **On phones and tablets:** the tab list is hidden. Each category's title (a link) shows with its sub-categories as two columns of links below it.
- **Without JetMenu:** Landscape supplies shows as an ordinary nested menu (category › sub-categories) from the Main menu's items.

To update the panel on an installed site, run setup with **Rebuild only the Landscape supplies mega menu content** ticked. That leaves the menu items alone. **Rebuild the main menu** rebuilds both the items and the panel.

### Mobile menu (phones and tablets)
Below 1025px wide, the header's ☰ button opens the plugin's own full-screen menu instead of JetMenu's dropdown.
- **Panel:** green, with the wordmark and a round ✕ button.
- **Rows:** the Main menu's top-level items as large rows.
- **Sub-menus:** items with a sub-menu (Landscape supplies, Bali imports) have an amber **+** button. It opens a grid of tiles, each with the page's featured image and its name. The section for the page you're on starts open, otherwise the first one does.
- **Footer:** terracotta with a wavy top, "Open 7 days · Delivery across Perth", and the phone number and **Get a quote** buttons.

It closes with ✕, Escape or following a link.

The items come from **Appearance → Menus → Main menu**, so changes there show up in it. "See all …" items and anything three levels deep are left out. The code is in the plugin's `includes/mobile-menu.php`, `assets/css/mobile-menu.css` and `assets/js/mobile-menu.js`. Any element with the CSS class `bs-mobile-menu-toggle` also opens it.

### Menu images
In the main menu, each dropdown row shows **the linked page's featured image**, its title and its menu **Description** (turn Description on under *Screen Options* in Appearance → Menus). To change an image, change the page's featured image.
- Add the CSS class `bs-no-thumb` to a menu item to hide its image.
- Add `bs-thumb` to a top-level item to show one.
- The "See all …" link at the bottom of each dropdown has the class `bs-see-all`.

This works with JetMenu's **Mega Menu** widget, which is the one in the header. It also works with Elementor Pro's Nav Menu.

## Check after setup
These are the parts I couldn't test without a live WordPress site and the licensed plugins:

- **JetMenu dropdown styling.** The plugin's CSS targets JetMenu's class names (`.jet-mega-menu-*`) to give the dropdown its rounded green-bordered panel. If a JetMenu update renames them, set the panel style in the Mega Menu widget's **Style** tab instead. The images still show either way.
- **JetMenu widget's menu**: in the header, check the Mega Menu widget has **Main menu** selected.
- **Mega menu**: hover *Landscape supplies*. The five-column panel should open just under the header bar and span from the logo to **Get a quote**. If you see an ordinary dropdown instead, open **Appearance → Menus**, click **JetMenu** on the *Landscape supplies* item, and check **Mega Submenu Enabled** is on and its content is "Mega menu: Landscape supplies". If the panel opens too narrow or in the wrong place, check `assets/css/menu.css` is loading (it sets the panel's width and position).
- **Mobile menu**: on phones and tablets, the ☰ button opens the full-screen menu (see "Mobile menu" above). Check Landscape supplies opens to the five categories and their sub-categories. The plugin's mobile styles start at 1024px wide, which matches the Mega Menu widget's mobile breakpoint set to **Tablet**. If the widget uses a different breakpoint, set it to Tablet (Content tab).
- **JetEngine Listing Grid** on the Soils page: check it uses the **Product card** listing, and that its **Posts Query** has a *Tax Query* on **Product categories** → slug → `soils`. Set the order (e.g. by menu order, ascending) under the same Posts Query.
- **Product card listing**: open it once in Elementor to check that each dynamic widget points at the right field: title, *Short description*, *Best for*, *Sold as* terms and permalink.
- **Gravity Forms look**: the form is embedded with `theme="gravity-theme"` (Gravity Forms 2.5+).
- **Product page**: open a product's **View details** page and check that:
  - the photo, name, short description and "Best for" line all show. If the short description or "Best for" is blank, open the template in Elementor, then reselect the field in that widget's dynamic tag (ACF Field → *Short description* / *Best for*).
  - "More from this range" lists other products from the same category, and not the product itself. Its Listing Grid's **Posts Query** has a *Tax Query* on Product categories using the macro `%current_terms|bs_product_cat%` (Term ID), and **Posts & Author Parameters → Exclude posts** set to `%current_id%`.

## Still needed from the client
- **Photos.** These spots still need real photos:
  - The hero photo (product bays at the yard) and the delivery photo (loader filling a truck). Set each as the background image of that panel.
  - One photo per landscape supplies category and sub-category. Set it as the page's featured image, and swap it into the homepage card's Image widget.
  - Soils page: the intro photo (soil bays or a scoop of garden mix). Each soil Product also needs its own photo, set as its **Card photo**. The same photo shows large on the product's own page, so a landscape photo at least 1200px wide works best.
- **Product write-ups.** Each product page's "About this product" text: what's in it, how to use it and roughly how much to order.
- **About page.** The founder's name, the year the yard started and what it first sold, the year of the first Bali trip, and a line about the team today. Plus photos:
  - the owners or team at the yard (intro)
  - the yard in the early days (Our story)
  - the supplies bays or loader (Landscape supplies card)
  - makers at work, a container being packed or unloaded, and pieces set out in the yard (How our imports get here)
- **Bali imports.** Replace the four [placeholder] products with real pieces, and add a photo to the "No two pieces are the same" panel (a maker carving stone or weaving).
- **Soils range.** The eight soils, their descriptions and the bulk/bagged labels came from the design. Check them against what the yard sells, and add products for the other categories.
- **Reviews.** The three review cards are placeholders. Paste in real Google reviews and set the "All Google reviews →" link.
- **Delivery facts.** Check "0.5–8m³ per tipper", and the 8m³ tipper size the calculator uses: it's the `tipper="8"` attribute in the calculator's Shortcode widget.
- **Opening badge.** "Open today until 4:30pm" in the hero is static text.
- **Dropdown sub-items** came from the design. Check them against what the yard actually stocks.
- **Pages.** Category and Delivery pages only have a one-line intro for now. **Privacy Policy** and **Terms & Conditions** (linked from the footer on phones) need the real policies.
- **Quote emails** go to the site admin email. Change this under Forms → Get a quote → Settings → Notifications. The same goes for the Contact page's **Contact enquiry** form (Forms → Contact enquiry).
- **Contact page.** Confirm the email address shown as **[sales@balisands.com.au]**: it's from the design. Fix the text in the Email card's heading, and the address in the **Send an email** button's link (`mailto:`), or change `email` in `bss_business()` and rebuild the page. Also confirm the public holiday closures (**[Christmas Day, Good Friday]**) and the first "Getting here" step (**[About 25 minutes from Perth CBD via Tonkin Hwy — confirm]**). Remove the square brackets once the text is right.

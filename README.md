# Bali Sands website

The Bali Sands homepage (design **6a** from the Claude Design handoff), built for a fresh WordPress site running **Hello Elementor**. It has two parts:

| Folder | What it is |
| --- | --- |
| `wp-content/themes/bali-sands/` | Hello Elementor **child theme**. Header with the green info bar and dropdown menus, footer with the wavy top edge, Open Sans. |
| `wp-content/plugins/bali-sands-core/` | **Plugin**. Product ranges, Yard details, eight homepage Elementor widgets, the Gravity Forms quote form and the one-click setup screen. |

The homepage is an ordinary Elementor page, so everything on it can be edited in Elementor.

## Install

1. On a fresh WordPress (6.3+, PHP 7.4+), install and activate:
   - the **Hello Elementor** theme (it's the parent; leave it installed, but don't activate it)
   - **Elementor** (required)
   - **Gravity Forms** (required for the quote form)
   - **ACF Pro** (recommended: it adds the editing screens for Yard details, tiles and dropdown thumbnails)
   - Elementor Pro and JetEngine are optional. Nothing here needs them yet.
   - WooCommerce is **not** used.
2. Copy both folders from `wp-content/` into the site's `wp-content/`.
3. **Appearance → Themes**: activate **Bali Sands**.
4. **Plugins**: activate **Bali Sands Core**.
5. Open Elementor once (any page), so it creates its site-settings kit.
6. **Tools → Bali Sands setup → Run setup.** It:
   - copies the logo and the six Bali imports photos from the old Wix site into the Media Library
   - creates the 12 product ranges (6 Landscape supplies, 6 Bali imports)
   - creates the **Main menu** with its six dropdowns, plus the two footer lists, and assigns them
   - creates the **Get a quote** Gravity form
   - creates the **Home** page from the design and sets it as the front page
   - sets Elementor's global colours, Open Sans and the 1280px content width

   It's safe to run again. It only adds what's missing. The homepage layout is only replaced if you tick **Rebuild the homepage layout**.

## Where things are edited

| What | Where |
| --- | --- |
| Phone, email, address, ABN, Maps link, opening hours, green-bar text, footer blurb, tipper size | **Yard details** (ACF Pro options page). The "Open today until 4:30pm" badge, footer hours and calculator all read from here. |
| Homepage text, buttons, photos, review cards | **Pages → Home → Edit with Elementor.** Each section is a *BS* widget under the "Bali Sands" category. |
| The 12 range tiles (name, subtitle, photo, swatch colour, link, order) | **Product ranges.** Featured image = tile photo. *Excerpt* = subtitle. *Order* = position. |
| Dropdown rows | **Appearance → Menus → Main menu.** The grey text under each row is the menu item **Description** (turn it on under *Screen Options*). Thumbnail, swatch and "See all" label are ACF fields on each item. |
| Footer lists | Menus *Footer: Landscape supplies* and *Footer: Bali imports* |
| Footer legal links | Assign a menu to **Footer: legal links** (until you do, it shows the Privacy Policy page if there is one) |
| Who gets quote emails | Yard details → **Enquiry email** (falls back to the site admin email) |

### Homepage widgets

| Widget | Section |
| --- | --- |
| BS Hero | Open badge, headline, buttons, ticks, yard photo and address card |
| BS Range tiles | A row of range tiles for one department. Used twice. |
| BS Calculator | Green band with the live m³ calculator |
| BS Photo + text | Delivery section |
| BS Numbered cards | "Why people come back" |
| BS Reviews | "What customers say" |
| BS Quote form | "Tell us about the job" and the Gravity form |
| BS Wave divider | The wavy edges. Set *Colour above* and *Colour below* to match the sections either side. |

Links can use `{tel}` (the yard phone number) and `{maps}` (the Maps link). Text can use `{phone}`, `{address}` and `{hours}`.

## Still needed before launch

- **Photos.** The hero photo (product bays at the yard) and the delivery photo (loader filling a truck) are empty slots for now. The six Landscape supplies tiles show material-colour swatches until they get a photo of each product.
- **Reviews.** The three review cards hold placeholders. Paste in real Google reviews and set the *All Google reviews* link.
- **Delivery facts.** Check "0.5–8m³ per tipper" and the tipper size (8m³) the calculator uses.
- **Dropdown sub-items** ("Brickies & plasterers sand", "Pine bark" and so on) came from the design. Check them against what the yard actually stocks.
- **Links.** There are no category pages yet, so the menu and footer links jump to the homepage tile rows. The *View all supplies / imports* links stay hidden until you give them a URL. Give each range a *Link* once its page exists.
- **Public holidays.** The open badge works from weekday hours only.
- Legal pages (privacy, terms, refunds), then assign a **Footer: legal links** menu.

See [`docs/CLIENT-CHECKLIST.md`](docs/CLIENT-CHECKLIST.md) for everything still needed from the client.

## Notes for developers

- Range and menu fields are read with `get_post_meta()` and `get_option('options_…')`, so the front end works without ACF. ACF only adds the editing screens.
- `bs_range` is a plain post type with a `bs_department` taxonomy, ready for JetEngine Listings when the category pages are built.
- If an Elementor Pro Theme Builder header or footer is published, it replaces the theme's own.

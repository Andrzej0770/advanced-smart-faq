=== Advanced Smart FAQ ===
Contributors: advancedsmartfaq
Tags: faq, accordion, schema, structured data, seo
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern, powerful FAQ system with accordion UI, SEO schema markup, category filtering, real-time search, and fully customizable settings.

== Description ==

**Advanced Smart FAQ** is a production-quality WordPress FAQ plugin designed for real websites. It provides a beautiful, accessible accordion interface, automatic SEO-optimized JSON-LD structured data, and flexible shortcode options — all without any bloat.

= Key Features =

* **Custom Post Type** – Manage FAQs from a dedicated admin menu with title, answer, ordering, and categories.
* **FAQ Categories** – Organize your FAQs into categories and filter them on the front end.
* **Shortcode System** – Use `[smart_faq]` with attributes for limit, category, and style.
* **Modern Accordion UI** – Clean, accessible, mobile-friendly accordion with smooth CSS animations.
* **FAQ Schema (JSON-LD)** – Automatically outputs FAQPage structured data for Google rich results.
* **Real-time Search** – Optional live search input lets visitors filter questions instantly.
* **Admin Settings** – Configure schema output, animation speed, default limit, and search toggle from Settings → Smart FAQ.
* **Accessible** – Proper ARIA attributes, keyboard navigation, and screen-reader support.
* **Lightweight** – Zero dependencies. Vanilla JavaScript and minimal CSS.
* **Dark Mode** – Automatically adapts to the visitor's system color scheme preference.
* **Secure** – All input is sanitized and output is escaped following WordPress coding standards.

= Shortcode Examples =

Display all FAQs (default limit from settings):

`[smart_faq]`

Display 5 FAQs:

`[smart_faq limit="5"]`

Display FAQs from a specific category:

`[smart_faq category="general"]`

Combine attributes:

`[smart_faq limit="10" category="seo" style="accordion"]`

= How the Schema Works =

When "Enable FAQ Schema" is turned on in Settings → Smart FAQ, the plugin automatically injects a `<script type="application/ld+json">` block into the `<head>` of any page or post that contains the `[smart_faq]` shortcode. This structured data helps search engines understand your FAQ content and may enable rich results in Google Search.

== Installation ==

1. Upload the `advanced-smart-faq` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **FAQs → Add New** to create your first FAQ entry.
4. Optionally organize FAQs into categories under **FAQs → Categories**.
5. Add the `[smart_faq]` shortcode to any page or post.
6. Configure plugin options under **Settings → Smart FAQ**.

== Frequently Asked Questions ==

= How do I display FAQs? =

Use the `[smart_faq]` shortcode in any page or post. You can customize it with attributes like `limit`, `category`, and `style`.

= How do I control the display order? =

Edit each FAQ and set the **Order** value in the Page Attributes meta box. FAQs are sorted by menu order (ascending) by default.

= Does this plugin support Gutenberg? =

Yes! The FAQ post type has REST API support (`show_in_rest => true`), so you can use the block editor to write your FAQ answers.

= Can I use multiple shortcodes on the same page? =

Yes. Each shortcode instance renders independently with its own settings.

= How do I disable the search box? =

Go to **Settings → Smart FAQ** and uncheck "Enable FAQ Search".

= Is the plugin translation ready? =

Yes. All user-facing strings use the `advanced-smart-faq` text domain and are ready for translation.

== Screenshots ==

1. **FAQ Accordion** – Modern, clean accordion on the front end.
2. **FAQ Admin** – Manage FAQs from a dedicated post type screen.
3. **Settings Page** – Configure schema, animation, limit, and search.
4. **Real-time Search** – Visitors can filter FAQs by typing.
5. **Schema Output** – JSON-LD structured data in the page source.

== Changelog ==

= 1.0.0 =
* Initial release.
* Custom post type with category taxonomy.
* Accordion shortcode with limit, category, and style attributes.
* FAQPage JSON-LD schema output.
* Admin settings page with WordPress Settings API.
* Real-time search filtering.
* Accessible, mobile-friendly, dark-mode-aware design.

== Upgrade Notice ==

= 1.0.0 =
Initial release of Advanced Smart FAQ.

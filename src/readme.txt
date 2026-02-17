=== SMK Sidebar Generator ===
Contributors: _smartik_
Tags: sidebar, generator, sidebar, widget, custom sidebar, conditional sidebar
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 6.9.1
Stable tag: 3.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create unlimited custom sidebars and widget areas. Display different sidebars on specific pages, posts, or custom post types with conditional logic. No coding required.

== Description ==

**SMK Sidebar Generator** is a powerful yet easy-to-use WordPress plugin that lets you create unlimited custom sidebars and conditionally display them anywhere on your website - without writing a single line of code.

Whether you need different sidebars for your blog, shop, landing pages, or specific posts, this plugin gives you complete control over your widget areas.

[youtube https://www.youtube.com/watch?v=VvKjYLDu_W0]

= Why Choose SMK Sidebar Generator? =

* **100% Free** - All features included, no premium version upsells
* **No Coding Required** - Create and manage sidebars entirely from the WordPress admin
* **Lightweight** - Clean code that won't slow down your site
* **Works With Any Theme** - Sidebars automatically inherit your theme's styling
* **Actively Maintained** - Regular updates and WordPress compatibility

= Key Features =

**Unlimited Sidebars**
Create as many sidebars as you need. Perfect for blogs, business sites, WooCommerce stores, or any WordPress website.

**Replace Theme Sidebars**
Seamlessly replace your theme's default sidebars with custom ones. No theme file editing required.

**Conditional Display**
Show different sidebars based on:

* Specific pages
* Specific posts
* Custom post types
* Post type archives
* Taxonomy archives

**Drag and Drop Management**
Reorder your sidebars with an intuitive drag-and-drop interface. Easily organize and prioritize your widget areas.

**Responsive Admin Interface**
Modern, clean admin UI that works great on any device - desktop, tablet, or mobile.

**Shortcode Support**
Display any sidebar anywhere using a simple shortcode: `[smk_sidebar id="your-sidebar-id"]`

**Developer Friendly**
Use PHP functions in your theme templates:

`<?php
// Display sidebar by ID
if ( function_exists( 'smk_sidebar' ) ) {
    smk_sidebar( 'your-sidebar-id' );
}

// Or use WordPress native function
dynamic_sidebar( 'your-sidebar-id' );

// Get all registered sidebars
$sidebars = smk_get_all_sidebars();
?>`

= Perfect For =

* **Bloggers** - Different sidebars for categories, tags, or specific posts
* **Business Sites** - Unique widget areas for services, about, contact pages
* **WooCommerce Stores** - Shop-specific sidebars separate from blog sidebars
* **Membership Sites** - Conditional sidebars for different content areas
* **Developers** - Template functions and shortcodes for theme integration

= Support and Documentation =

* [GitHub Repository](https://github.com/awps/smk-sidebar-generator) - Report bugs and contribute
* [Support Forum](https://wordpress.org/support/plugin/smk-sidebar-generator/) - Get help from the community

== Installation ==

= Automatic Installation (Recommended) =

1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "SMK Sidebar Generator"
3. Click **Install Now**, then **Activate**
4. Navigate to **Appearance > SMK Sidebars** to create your first sidebar

= Manual Installation =

1. Download the plugin ZIP file
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Activate the plugin
5. Navigate to **Appearance > SMK Sidebars** to start creating sidebars

= Using FTP =

1. Download and extract the plugin ZIP file
2. Upload the `smk-sidebar-generator` folder to `/wp-content/plugins/`
3. Activate through the **Plugins** menu in WordPress
4. Configure at **Appearance > SMK Sidebars**

== Frequently Asked Questions ==

= How do I create a new sidebar? =

1. Go to **Appearance > SMK Sidebars** in your WordPress admin
2. Click the **"Add New Sidebar"** button
3. Enter a name and optional description
4. Save your changes
5. Go to **Appearance > Widgets** to add widgets to your new sidebar

= How do I display a sidebar on a specific page? =

1. Create or edit a sidebar in **Appearance > SMK Sidebars**
2. Select which theme sidebar to replace in the "Sidebars to replace" dropdown
3. Check "Enable conditions"
4. Click "Add condition" and select the page(s) where you want this sidebar to appear
5. Save changes

= Can I use the sidebar in page builders like Elementor or Beaver Builder? =

Yes! Use the shortcode `[smk_sidebar id="your-sidebar-id"]` in any text widget or shortcode module. You can find each sidebar's shortcode displayed in its settings panel.

= Will this plugin slow down my website? =

No. SMK Sidebar Generator is lightweight and only loads its assets on the admin pages where needed. On the frontend, it simply filters which sidebar to display with minimal overhead.

= Does it work with my theme? =

Yes! The plugin works with any properly coded WordPress theme. Your generated sidebars will automatically inherit your theme's sidebar styling.

= Can I display a sidebar using PHP in my theme? =

Yes, use either method in your theme template files:

`<?php smk_sidebar( 'your-sidebar-id' ); ?>`

Or the native WordPress function:

`<?php dynamic_sidebar( 'your-sidebar-id' ); ?>`

= How do I find my sidebar ID? =

The sidebar ID is displayed in each sidebar's settings panel, along with the shortcode. You can also use `smk_get_all_sidebars()` to get an array of all sidebar IDs and names.

= Can I show different sidebars for different categories? =

Yes! When adding conditions, select the post type archive or use taxonomy conditions to target specific categories, tags, or custom taxonomies.

= Is this plugin compatible with WooCommerce? =

Yes! You can create shop-specific sidebars and use conditions to display them only on WooCommerce pages like the shop, product pages, cart, or checkout.

= Can I translate this plugin? =

Yes! The plugin is translation-ready. You can contribute translations on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/smk-sidebar-generator/) or create your own using the included POT file.

= Where can I report bugs or request features? =

Please use the [GitHub Issues page](https://github.com/awps/smk-sidebar-generator/issues) for bug reports and feature requests.

== Screenshots ==

1. **Main Admin Interface** - Clean, modern dashboard showing all your custom sidebars with drag-and-drop reordering
2. **Conditional Settings** - Set up rules to display sidebars on specific pages, posts, or post types
3. **Sidebar Removal** - Safely delete sidebars with a time-limited restore option to prevent accidents

== Changelog ==

= 3.6.0 =

* Fixed: Shortcode now properly returns content instead of echoing
* Security: Added proper escaping to prevent XSS vulnerabilities
* Fixed: Undefined variable warning in HTML class helper
* Fixed: Removed deprecated extract() function from shortcode
* Compatibility: Tested with WordPress 6.9.1
* New: Complete admin UI redesign with modern CSS (Flexbox, Grid, CSS Variables)
* New: Responsive layout for mobile and tablet devices
* New: Accessible form labels with proper for/id attributes
* Improved: Sidebar list with better spacing, hover states, and empty state messages
* Improved: Condition rows layout with inline drag handle
* Fixed: Sidebar deletion not saving properly
* Fixed: Accordion padding conflicts with WordPress admin styles
* Dev: Updated GitHub Actions to v4, improved deploy.sh error handling
* Dev: Moved tagy config to package.json, removed tagy.js

= 3.5.2 =
* Compatibility with WordPress 6.4

= 3.5.0 =
* PHP 8+ Compatibility
* Various code improvements

= 3.4.3 =
* Bug fix: Individual taxonomy conditional fix

= 3.4.2 =
* Bug fix: WP 5.7 compatibility

= 3.4.0 =
* Fix: Incorrect conditional replacement
* Compatibility with WordPress 5.5

= 3.1 =
* Added localization support
* Added shortcode with ID to each sidebar

= 3.0 =
* Complete rewrite from scratch
* New conditional sidebar system for pages, posts, and custom post types
* Sidebars now inherit theme styling automatically
* Modular code architecture for custom condition extensions

= 2.3.2 =
* UI fix: Correct info display when new sidebar is created

= 2.3.1 =
* Fixed shortcode ID issue
* Added "How to use?" documentation tab

= 2.3 =
* Added import/export functions
* Improved shortcode to work with any sidebar ID

= 2.2 =
* Added sidebar removal confirmation
* Bug fixes for sidebar creation

= 2.1 =
* Added `smk_get_all_sidebars()` helper function

= 2.0 =
* Initial release

== Upgrade Notice ==

= 3.6.0 =
Major update with redesigned admin interface, security improvements, and bug fixes. Tested with WordPress 6.9.1. Recommended for all users.

= 3.5.0 =
PHP 8+ compatibility update. Recommended for users on PHP 8.0 or higher.

= 3.0 =
Complete rewrite with new features. Please test on a staging site before updating on production.

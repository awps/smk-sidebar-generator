=== SMK Sidebar Generator ===
Contributors: _smartik_
Tags: sidebar, widget, generator, custom, unlimited
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 6.4.1
Stable tag: 3.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

== Description ==
SMK Sidebar Generator is a versatile WordPress plugin designed to empower users with the ability to create and manage
an unlimited number of sidebars effortlessly. With this intuitive tool, you can dynamically customize your website's
layout by placing the generated sidebars on any page without the need for extensive coding.

https://www.youtube.com/watch?v=VvKjYLDu_W0

####Key Features:
**Unlimited Sidebars:**
Create and manage as many sidebars as needed for maximum flexibility in organizing content.

**Replace Theme Sidebars:**
Effortlessly replace existing sidebars created by themes or other plugins for each generated sidebar. The controls allow seamless integration and customization.

**Conditional Sidebar Replacement:**
Customize the display of sidebars by conditionally replacing them based on post types or specific posts. This feature gives you precise control over the appearance of sidebars on different content.

**Compatibility with Page Builders and Themes:**
SMK Sidebar Generator is fully compatible with any page builder and theme. Enjoy a seamless integration experience without worrying about conflicts with your chosen design tools.

**Easy-to-Use Controls:**
The plugin provides an intuitive interface for managing sidebars, with user-friendly controls for replacing, organizing, and displaying content with minimal effort.

**Drag-and-Drop Sorting:**
Customize sidebar positions effortlessly using a user-friendly drag-and-drop interface.


####How to install this plugin?
Follow the standard WordPress plugin installation process by placing the 'smk-sidebar-generator' folder in the 'wp-content/plugins/' directory. For more detailed instructions, refer to: [WordPress Instructions](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins-1)

####Developer helpers.
**While SMK Sidebar Generator is designed for seamless use within the WordPress Admin, developers can take advantage of the following optional functions:**

<pre>
// Display sidebar with ID using SMK function:
function_exists('smk_sidebar') ? smk_sidebar('sidebarID') : '';

// Display sidebar with ID using WordPress function.
dynamic_sidebar('sidebarID');

// Display sidebar with ID using shortcode.
[smk_sidebar id="sidebarID"]
</pre>

== Installation ==

1. Upload the `smk-sidebar-generator` folder to the `/wp-content/plugins/` directory
2. Activate the SMK Sidebar Generator plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the SMK Sidebars menu that appears in your admin menu

== Screenshots ==
1. Admin panel
2. Conditions
3. Removal && time-limited option to restore.


== Changelog ==
= 3.5.2 =
* Compatibility with the latest WordPress version 6.4

= 3.5.0 =
* PHP 8+ Compatibility
* Various code improvements

= 3.4.3 =
* Bug fix: Individual taxonomy conditional fix

= 3.4.2 =
* Bug fix: WP 5.7 compatibility

= 3.4.0 =
* Fix: Incorrect conditional replacement.
* Compatibility with WordPress 5.5

= 3.1 =
* Added localization support(if you want to translate it in your language, create a pull requests on Github).
* Added shortcode with ID to each sidebar.

= 3.0 =
* **Complete rewrite from scratch.** The plugin now allows to create an unlimited number of sidebars without the need to touch a single line of code in your theme.
* Now you can use conditions to apply the sidebar on any page, post ar CPT you wish. _Soon will be added support for taxonomies, 404 page and other(please suggest)_.
* The widgets now use the theme style and tags. That means the newly generated sidebars will look good on any theme, no need for additional styling.
* Modular code. You can create and register your own conditions. That's mainly not required but can be handy for some developers.

= 2.3.2 =
* Quick fix UI. When a new sidebar is created, it display an incorrect info and it was fixed only after page refresh.
* Removed unused files, since version 3.0 is on development `smk_sidebar_metabox.php` was removed, as it was never used and is not required for the next versions.

= 2.3.1 =
* Quick fix for shortcode smk_sidebar ID. Shortcode did not work because the ID was not set correctly.
* Added new tab "How to use?" and links to docs.

= 2.3 =
* **Added import/export functions.**
* Changes to `smk_sidebar` shortcode. Previously to get a sidebar required only an integer number, now you can get any sidebar using the shortcode just giving the id, even if the sidebar is not generated using Sidebar Generator plugin.
* Added plugin version to enqueued scripts and style.

= 2.2 =
* Confirm sidebar remove.
* Bug fix: Sidebars could not be added when all previous sidebars were removed.
* Bug fix: Fixed ajax name validation.

= 2.1.1 =
* enqueue styles and scripts only on plugin page, not on all WP dashboard pages.
* `admin_enqueue_scripts` make use of `SMK_SBG_URI` constant.

= 2.1 =
* `smk_get_all_sidebars()` function is included in plugin. Anyways, you must include it in your theme `functions.php`, because if you'll deactivate the plugin it will return a fatal error.

= 2.0 =
* Initial release

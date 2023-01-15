=== SMK Sidebar Generator ===
Contributors: _smartik_
Tags: sidebar, widget, generator, custom, unlimited
Requires at least: 4.0
Tested up to: 6.2
Stable tag: __STABLE_TAG__
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

== Description ==
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.


<!--**Demo video:** http://youtu.be/fluNdMnSCKA-->

<!--iframe width="560" height="315" src="//www.youtube.com/embed/fluNdMnSCKA" frameborder="0" allowfullscreen></iframe-->

####Features:
* Unlimited number of sidebars.
* Replace default theme sidebars using the conditions or globally just by selecting the sidebar that you want to replace.
* Show the generated sidebars on any page you wish without touching a single line of code in your theme.
* Drag to sort sidebar position.

####How to install this plugin?
Like any other Wordpress plugin. <br />
Drop `smk-sidebar-generator` to `wp-content/plugins/`.<br />
More info here: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

####Developer helpers.
**You actually don't need any of these. The plugin can be managed fully from WP Admin without writing a single line of code.**

Display a sidebar using `smk_sidebar` function:
<pre>
if(function_exists('smk_sidebar'){
   smk_sidebar('sidebarID');
}
</pre>

Display a sidebar using wp native function:
<pre>
dynamic_sidebar('sidebarID'));
</pre>

Display a sidebar using built-in shortcode:
<pre>
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

= 3.5 =
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

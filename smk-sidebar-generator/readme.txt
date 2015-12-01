=== SMK Sidebar Generator ===
Contributors: _smartik_
Tags: sidebar, widget, generator, custom, unlimited
Requires at least: 3.2
Tested up to: 4.1.1
Stable tag: 3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

== Description ==
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

#### Version 3.0 is here!
The new version 3.x has many advantages compared with the old 2.x. First and the most important is that it remove the need to add some special code to the theme in order to display the generated sidebar. That's because it now can override the default sidebar and apply special conditions for any page on your site.


<!--**Demo video:** http://youtu.be/fluNdMnSCKA-->

<!--iframe width="560" height="315" src="//www.youtube.com/embed/fluNdMnSCKA" frameborder="0" allowfullscreen></iframe-->

* Author : Smartik - http://smartik.ws/
* License : GPLv2
* Development branch: https://github.com/Smartik89/Wordpress-Sidebar-Generator
* Issue tracker: https://github.com/Smartik89/Wordpress-Sidebar-Generator/issues

####Features:
* Unlimited number of sidebars.
* Replace default theme sidebars using the conditions or globaly just by selecting the sidebar that you want to replace.
* Show the generated sidebars on any page you wish without touching a single line of code in your theme.
* Drag to sort sidebar position.

####How to install this plugin?
Like any other Wordpress plugin. <br />
Drop `smk-sidebar-generator` to `wp-content/plugins/`.<br />
More info here: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

####Backward compatibility. 

Because you probably still need them, these functions are still here to not break your site.
**Note:** The following code is for vesion 2.x In the latest version of this plugin they are not required. Do not use them anymore!!!

**Display a sidebar using `smk_sidebar` function:**
<pre>
if(function_exists('smk_sidebar'){
 smk_sidebar('sidebarID');
}
</pre>
**Display a sidebar using wp native function:**
<pre>
if(function_exists('dynamic_sidebar') && dynamic_sidebar('sidebarID')) : 
	endif;
</pre>

**Display a sidebar using built-in shortcode:**
<pre>
[smk_sidebar id="sidebarID"]
</pre>

== Installation ==

1. Upload the `smk-sidebar-generator` folder to the `/wp-content/plugins/` directory
2. Activate the SMK Sidebar Generator plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the SMK Sidebars menu that appears in your admin menu

== Screenshots ==
1. Admin panel


== Changelog ==

= 3.1 =
* Added localization support(if you want to translate it in your language, create a pull requests on Github).
* Added shortcode with ID to each sidebar.

= 3.0 =
* **Complete rewrite from scratch.** The plugin now allows to create an unlimited number of sidebars without the need to touch a single line of code in your theme.
* Now you can use conditions to apply the sidebar on any page, post ar CPT you wish. _Soon will be added support for taxonomies, 404 page and other(please suggest)_.
* The widgets now use the theme style and tags. That means the newly generated sidebars will look good on any theme, no need for additional styling.
* Modular code. You can create and register your own conditions. That's mainly not required but can be handy for some developers.

= 2.3.2 =
* Quick fix UI. When a new sidebar is created, it display an incorect info and it was fixed only after page refresh.
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

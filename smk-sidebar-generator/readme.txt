=== SMK Sidebar Generator ===
Contributors: _smartik_
Tags: sidebar, widget, generator, custom, unlimited
Requires at least: 3.2
Tested up to: 3.8
Stable tag: 2.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

== Description ==
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

**Important info: I'm working for version 3.0 which will support custom sidebars without touching a single line of code. If you want to get acces to first beta, click the following link, subscribe and I will send it to you when it's ready:** http://eepurl.com/QeMOf

**Demo video:** http://youtu.be/fluNdMnSCKA

<iframe width="560" height="315" src="//www.youtube.com/embed/fluNdMnSCKA" frameborder="0" allowfullscreen></iframe>

* Author : Smartik - http://smartik.ws/
* License : GPLv2
* Project page and usage instructions: https://github.com/Smartik89/Wordpress-Sidebar-Generator

####Features:
* Full AJAX (add, remove, save, validation, etc.)
* Drag to sort sidebar position.
* Name validation(characters and duplicate).
* Display sidebars using WP built-in function, a custom function or a shortcode.
* Get registered sidebars anywhere you need them.(theme options, metaboxes, widgets, etc.)

####How to install this plugin?
Like any other Wordpress plugin. <br />
Drop `smk-sidebar-generator` to `wp-content/plugins/`.<br />
More info here: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

**Get all sidebars in an array:**
Add this function in your theme `functions.php`:
<pre>
if(! function_exists('smk_get_all_sidebars') ) {
	function smk_get_all_sidebars(){
		global $wp_registered_sidebars;
		$all_sidebars = array();
		if ( $wp_registered_sidebars && ! is_wp_error( $wp_registered_sidebars ) ) {
			
			foreach ( $wp_registered_sidebars as $sidebar ) {
				$all_sidebars[ $sidebar['id'] ] = $sidebar['name'];
			}
			
		}
		return $all_sidebars;
	}
}
</pre>
Now using this function you can get all sidebars in an array(`[id] => [name]`):
<pre>print_r( smk_get_all_sidebars() )</pre>
*result of the above code(example)*
<pre>
array(
  "sidebarID" => "Default Sidebar",
  "anotherID" => "Sidebar Name",
  "smk_sbg_18" => "Sidebar Name 1",
  "smk_sbg_7" => "Sidebar Name Something"
)
</pre>
*You can output this anywhere in page/post metaboxes, theme options, etc.*

*Example with php `foreach`:*
<pre>
echo '&lt;select>';
  foreach($the_sidebars as $key => $value){
    echo '&lt;option value="'. $key .'">'. $value .'&lt;/option>';
  }
echo '&lt;/select>';
</pre>


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

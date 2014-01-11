This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

**Demo video:** http://youtu.be/fluNdMnSCKA

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
[smk_sidebar id="18"]
</pre>
*18 is an example, this is the sidebar number, it is created automatically when a new sidebar is generated*

##TO DO:
* Multilanguage support
* Create demo theme

##Releases and Changelog 
https://github.com/Smartik89/Wordpress-Sidebar-Generator/releases

##Installation
1. Upload the `smk-sidebar-generator` folder to the `/wp-content/plugins/` directory
2. Activate the SMK Sidebar Generator plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the SMK Sidebars menu that appears in your admin menu

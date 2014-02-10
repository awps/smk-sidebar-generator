##SMK Sidebars Generator
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.

**Demo video:** http://youtu.be/fluNdMnSCKA

* Current version : **2.3**
* Author : Smartik - http://smartik.ws/
* License : GPLv2
 
**Note:** To use this plugin you'll need to modify the source code of your theme or paste the generated shortcode where you want to show a specific sidebar. Read below for instructions.

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

####Quick Installation
1. Upload the `smk-sidebar-generator` folder to the `/wp-content/plugins/` directory
2. Activate the SMK Sidebar Generator plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the SMK Sidebars menu that appears in your admin menu


###Install it from Wordpress.org:
* Search from WP Dashboard->Plugins(Add New) for: `SMK Sidebar Generator` and install it.
* ... or download it from here: http://wordpress.org/plugins/smk-sidebar-generator/
 
<img src="http://i.imgur.com/hSOdoGc.jpg" />


**Get all sidebars in an array:**
Add this function in your theme `functions.php`:
```php
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
```
Now using this function you can get all sidebars in an array(`[id] => [name]`):
```php 
print_r( smk_get_all_sidebars() )
```
*result of the above code(example)*
```php
array(
  "sidebarID" => "Default Sidebar",
  "anotherID" => "Sidebar Name",
  "smk_sbg_18" => "Sidebar Name 1",
  "smk_sbg_7" => "Sidebar Name Something"
)
```
*You can output this anywhere in page/post metaboxes, theme options, etc.*
*Example with php `foreach`:*
```php
echo '<select>';
  foreach($the_sidebars as $key => $value){
    echo '<option value="'. $key .'">'. $value .'</option>';
  }
echo '</select>';
```


**Display a sidebar using `smk_sidebar` function:**
```php
if(function_exists('smk_sidebar'){
 smk_sidebar('sidebarID');
}
```
**Display a sidebar using wp native function:**
```php
if(function_exists('dynamic_sidebar') && dynamic_sidebar('sidebarID')) : 
				endif;
```

**Display a sidebar using built-in shortcode:**
```php
[smk_sidebar id="sidebarID"]
```

##Releases and Changelog 
https://github.com/Smartik89/Wordpress-Sidebar-Generator/releases


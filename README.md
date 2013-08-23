##Custom Sidebars Generator
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.
* Current version : **2.0 beta**
* Last edit : August 13, 2013 20:14
* Author : Smartik - http://smartik.ws/
 
**Note:** Right now this is not a ready-to-use plugin. To use this plugin you need a theme that supports it or if you are a theme developer you must add the necesarry code in order to be compatible with your theme.
Beta is there only because I want to include other features before it goes final, because right now it requires theme modifications to add support for this plugin.

**Get all sidebars in an array**
```php
if( class_exists('SMK_Sidebar_Generator') ) {
    $the_sidebars = SMK_Sidebar_Generator::get_all_sidebars();
}
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
*Now you can output this anywhere in page/post metaboxes, theme options, etc.*

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
if(function_exists('dynamic_sidebar'){
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
[smk_sidebar id="18"]
```
*18 is an example, this is the sidebar number, it is created automatically when a new sidebar is generated*

##TO DO:
* Multilanguage support
* Create demo theme

##Releases and Changelog 
https://github.com/Smartik89/Wordpress-Sidebar-Generator/releases

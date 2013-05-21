##Custom Sidebars Generator
This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish. This is a modified version of "Sidebar Generator" plugin by Kyle Getson. http://wordpress.org/extend/plugins/sidebar-generator/
* Current version : **1.0.3**
* Last edit : May 08, 2013 18:49
* Author : Smartik - http://smartik.ws/
* Credits : This is a modified version of "Sidebar Generator" plugin by Kyle Getson. - http://www.kylegetson.com/
 
**Note:** This is not a plugin, it's a piece of code for theme developers.

##How to use
**Include `class.SidebarGenerator.php` in `functions.php`**
```php
require_once (get_template_directory().'/class.SidebarGenerator.php');
```

**Get all sidebars in an array**
```php
$all_sidebars = SidebarGenerator::get_all_sidebars();
```

this will return all sidebars in an array. Example:
```php
array(
  "Default Sidebar" => "Default Sidebar",
  "Sidebar Name" => "Sidebar Name",
  "Sidebar Name 1" => "Sidebar Name 1",
  "Sidebar Name 2" => "Sidebar Name 2"
)
```
Now you can output this anywhere in page/post metaboxes, theme options, etc.

*Example with php `foreach`:*
```php
echo '<select>';
  foreach($all_sidebars as $key => $value){
    echo '<option value="'. $key .'">'. $value .'</option>';
  }
echo '</select>';
```

**Display a sidebar**
```php
smk_custom_dynamic_sidebar('Sidebar Name');
```

**Display a sidebar using shortcodes**
```php
[smk_sidebar name="Sidebar Name"]
```

##Changelog 
**v1.0.3 - May 08, 2013**
* Bug Fix: User can now delete a just created sidebar.

**v1.0.2 - May 08, 2013**
* Fix: Do some checks before adding a new sidebar.
* Display the right message when the user can not delete a sidebar.

**v1.0.1 - Apr 26, 2013**
* Fix: User get warnings if sidebars were not created yet.

**v1.0 - Mar 30, 2013**
* First release

##How to use
**Include `class.SidebarGenerator.php` in `functions.php`**
```php
require_once (get_template_directory_uri().'/class.SidebarGenerator.php');
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

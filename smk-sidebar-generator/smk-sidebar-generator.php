<?php
/*
Plugin Name: SMK Sidebar Generator
Plugin URI: https://github.com/Smartik89/Wordpress-Sidebar-Generator
Description: This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.
Author: Smartik
Version: 2.3.2
Author URI: http://smartik.ws/
*/

//Do not allow direct access to this file.
if( ! function_exists('add_action') ) die('Not funny!');

//Some usefull constants
if(!defined('SMK_SBG_VERSION')) define( 'SMK_SBG_VERSION', '2.3.2' );
if(!defined('SMK_SBG_PATH')) define( 'SMK_SBG_PATH', plugin_dir_path(__FILE__) );
if(!defined('SMK_SBG_URI')) define( 'SMK_SBG_URI', plugin_dir_url(__FILE__) );

//SMK Sidebar Generator Class
if( ! class_exists('SMK_Sidebar_Generator')) {
class SMK_Sidebar_Generator {

	/* 
	Plugin menu/page title
	----------------------------------------------------------- */
	var $sbg_name;

	/*
	Plugin register
	----------------------------------------------------------- */
	var $settings_reg;
	var $plugin_option;


	/*
	----------------------------------------------------------------------
	Constructor
	----------------------------------------------------------------------
	*/
	public function __construct(){

		//Plugin name, this is the name for menu and page title.
		$this->sbg_name = __('Sidebar Generator', 'smk_sbg');

		//Plugin register
		$this->settings_reg = 'smk_sidebar_generator_register';
		$this->plugin_option = 'smk_sidebar_generator_option';

		//Actions
		add_action( 'admin_menu', array(&$this, 'admin_menu') );				//Create admin menu		
		add_action( 'admin_init', array(&$this, 'reg_setting') );				//Register setting
		add_action( 'admin_enqueue_scripts', array(&$this,'admin_scripts' ) );	//Admin scritps
		add_action( 'widgets_init', array(&$this, 'register_sidebars') );		//Register all sidebars
		add_action( 'wp_ajax_validate_name', array(&$this, 'validate_name') );	//Validate name
		add_action( 'wp_ajax_import_all_sidebars', array(&$this, 'import_all_sidebars') );     //Export all sidebars

	}


	/*
	----------------------------------------------------------------------
	Register sidebars
	----------------------------------------------------------------------
	*/
	public function register_sidebars() {

		//Catch saved options
		$sidebars = get_option( $this->plugin_option );

		//Make sure if we have valid sidebars
		if( isset($sidebars['sidebars']) && is_array($sidebars['sidebars']) && !empty($sidebars['sidebars']) ){

			//Register each sidebar
			foreach ($sidebars['sidebars'] as $sidebar) {
				if( isset($sidebar) && !empty($sidebar) ){

					register_sidebar(
						array(
							'name'          => $sidebar['name'],
							'id'            => 'smk_sidebar_' . $sidebar['id'],
							'description'   => '',
							'before_widget' => '<div id="%1$s" class="widget smk_sidebar_' . $sidebar['id'] .' %2$s">',
							'after_widget'  => '</div>',
							'before_title'  => '<h3 class="widget-title">',
							'after_title'   => '</h3>'
						)
					);

				}
			}

		}

	}


	/*
	----------------------------------------------------------------------
	Admin menu
	----------------------------------------------------------------------
	*/
	public function admin_menu(){
		add_submenu_page('themes.php', $this->sbg_name, $this->sbg_name, 'manage_options', strtolower( __CLASS__ ), array(&$this, 'admin_page') );
	}


	/*
	----------------------------------------------------------------------
	Register setting
	----------------------------------------------------------------------
	*/
	public function reg_setting() {
		register_setting( $this->settings_reg, $this->plugin_option );
	}


	/*
	----------------------------------------------------------------------
	Validate name
	----------------------------------------------------------------------
	*/
	public function validate_name() {
		$sidebars = get_option( $this->plugin_option );

		$new_name = trim( $_POST['new_name'] );
		$exists   = 'ok';//Do not exist

		if( isset($sidebars) && is_array($sidebars) ){
			if( isset($sidebars['sidebars']) && is_array($sidebars['sidebars']) ){
				foreach ($sidebars['sidebars'] as $key => $v) {
					if( in_array(strtolower( $new_name ), array_map('strtolower', $v) ) ){
						$exists = 'fail';//Exist
					}
				}
			}
		}
		
		echo $exists;

		die();
	}


	/*
	----------------------------------------------------------------------
	Enqueue scripts and styles
	----------------------------------------------------------------------
	*/
	public function admin_scripts() {

		global $pagenow;

		if( 'themes.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == strtolower( __CLASS__ ) ){

			//Styles
	 		wp_register_style( 'smk_sbg_styles', SMK_SBG_URI . 'assets/styles.css', '', SMK_SBG_VERSION );
	 		wp_enqueue_style( 'smk_sbg_styles' );

	 		//Scripts
	 		wp_register_script( 'smk_sbg_scripts', SMK_SBG_URI . 'assets/scripts.js', array('jquery', 'jquery-ui-core'), SMK_SBG_VERSION );

			//Enqueue scripts
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-slider');

	 		wp_enqueue_script( 'smk_sbg_scripts' );
	 		wp_localize_script('smk_sbg_scripts', 'smk_sbg_lang', array(
					'remove'        => __('Remove', 'smk_sbg'),
					'not_saved_msg' => __("You've made changes, don't forget to save.", 'smk_sbg'),
					'ok'            => __("Changes were saved successfully.", 'smk_sbg'),
					'fail'          => __("An unexpected error ocurred.", 'smk_sbg'),
					'created'       => __("The sidebar was successfully created.", 'smk_sbg'),
					's_exists'      => __("The sidebar already exists. Please change the name.", 'smk_sbg'),
					'empty'         => __("Please enter a name for this sidebar.", 'smk_sbg'),
					's_remove'      => __("Are you sure? If you remove this sidebar it can't be restored.", 'smk_sbg'),
					's_removed'     => __("Sidebar Removed", 'smk_sbg'),
					'data_imported' => __("Data imported successfully.", 'smk_sbg'),
					'spin'          => '<span class="smk_sbg_spin"></span>',
				)
			);
		}

	}


	/*
	----------------------------------------------------------------------
	Admin page
	----------------------------------------------------------------------
	*/
	public function admin_page(){
	
	$text = array(
			__('Add new', 'smk_sbg'),//0
			__('Save Changes', 'smk_sbg'),//1
			__('Sidebar Name:', 'smk_sbg'),//2
			__('ID:', 'smk_sbg'),//3
			__('Shortcode:', 'smk_sbg'),//4
			__('Remove', 'smk_sbg'),//5
			__('Import', 'smk_sbg'),//6
		);

	echo '<div class="wrap smk_sbg_main_block">';

		//delete_option($this->plugin_option);//DO NOT UNCOMMENT THIS OR ALL SIDEBARS WILL BE DELETED

		//Messages
		echo '<div class="smk_sbg_message"></div>';

		//Page UI
		echo '<div class="sbg_clearfix">';
			echo '<div class="sbg_grid_50">';
				screen_icon();
				echo '<h2>'. $this->sbg_name .' <span class="smk_sbg_version">v.' . SMK_SBG_VERSION .'</span></h2>';
			echo '</div>';
			echo '<div class="sbg_grid_50">';
				//Main menu
				echo '<div class="smk_sbg_main_menu">
						<span data-id="tab_main_form" class="active">'. __('Sidebars', 'smk_sbg') .'</span>
						<span data-id="tab_export">'. __('Export', 'smk_sbg') .'</span>
						<span data-id="tab_import">'. __('Import', 'smk_sbg') .'</span>
						<span data-id="tab_how_to">'. __('How to use?', 'smk_sbg') .'</span>
				</div>';
			echo '</div>';
		echo '</div>';

		
		
		// TAB main form
		echo '<div id="tab_main_form" class="smk_sbg_tab active">';
			//Form to update/save options
			echo '<form method="post" action="options.php" class="smk_sbg_main_form">';

			//Add settings fields(ex: nonce)
			settings_fields( $this->settings_reg );

			//Create the sidebar/Save changes
			echo '<div class="smk_sbg_hf_block sbg_clearfix">';
				echo '<input type="text" class="smk_sbg_name" />';
				echo '<span class="smk_sbg_button smk_sbg_add_new" data-option="'. $this->plugin_option .'">'. $text[0] .'</span>';
				echo '<input type="submit" name="submit" id="submit" class="smk_sbg_button smk_sbg_save_button" value="'. $text[1] .'">';
			echo '</div>';

			//Columns labels
			echo '<div class="smk_sbg_hf_block smk_hf_top0 sbg_clearfix">';
				echo '<span class="smk_sbg_col_title sbg_name">'. $text[2] .'</span>';
				echo '<span class="smk_sbg_col_title sbg_id">'. $text[3] .'</span>';
				echo '<span class="smk_sbg_col_title sbg_shortcode">'. $text[4] .'</span>';
			echo '</div>';

				//Catch saved options
				$sidebars = get_option( $this->plugin_option );

				//Set the counter. We need it to set the sidebar ID
				$count = isset($sidebars['count']) ? $sidebars['count'] : 1;
				echo '<input type="hidden" class="smk_sbg_count" name="'. $this->plugin_option .'[count]" value="'. $count .'" />';

				//All created sidebars will be included in this block
				echo '<div class="smk_sbg_all_sidebars">';

					//Make sure we have valid sidebars
					if( isset($sidebars['sidebars']) && is_array($sidebars['sidebars']) && !empty($sidebars['sidebars']) ){

						//Display each sidebar
						foreach ($sidebars['sidebars'] as $sidebar) {
							if( isset($sidebar) && !empty($sidebar) ){

								echo '<div class="smk_sbg_one_sidebar">
									<span class="smk_sbg_handle"></span>
									<input class="smk_sbg_form_created_id" type="hidden" value="'. $sidebar['id'] .'" name="'. $this->plugin_option .'[sidebars]['. $sidebar['id'] .'][id]" />
									<input class="smk_sbg_form_created" type="text" value="'. $sidebar['name'] .'" name="'. $this->plugin_option .'[sidebars]['. $sidebar['id'] .'][name]" />
									<span class="smk_sbg_code smk_sbg_code_id"><code>smk_sidebar_' . $sidebar['id'] . '</code></span>
									<span class="smk_sbg_code smk_sbg_code_shortcode"><code>[smk_sidebar id="smk_sidebar_' . $sidebar['id'] . '"]</code></span>
								<span class="smk_sbg_remove_sidebar">'. $text[5] .'</span></div>';

							}
						}

					}

				echo '</div>';


			echo '</form>';
		echo '</div>';	

		//TAB Export
		echo '<div id="tab_export" class="smk_sbg_tab additional">';
			//Export form
			echo '<div class="smk_sbg_label">' . __('Copy text from textarea:','smk_sbg') .'</div>';
			echo '<form method="post" action="" class="smk_sbg_export_form">';
				echo '<textarea name="exp_data" class="sbg_textarea sbg_textarea_export" onclick="this.focus();this.select()">'. base64_encode( serialize(get_option($this->plugin_option)) ) .'</textarea>';
			echo '</form>';
		echo '</div>';	

		//TAB Import
		echo '<div id="tab_import" class="smk_sbg_tab additional">';
			//Import form
			echo '<div class="smk_sbg_label">' . __('Paste exported data in textarea:','smk_sbg') .'</div>';
			echo '<form method="post" action="" class="smk_sbg_import_form">';
				echo '<textarea name="exp_data" class="sbg_textarea sbg_textarea_import"></textarea>';
				echo '<input type="submit" name="submit" id="export_submit" class="button button-primary smk_sbg_import_button" value="'. $text[6] .'">';
			echo '</form>';
		echo '</div>';

		//TAB Import
		echo '<div id="tab_how_to" class="smk_sbg_tab additional">';
			//Import form
			echo '<h2>' . __('How to use?','smk_sbg') .'</h2>';
			echo '<h3>' . __('Shortcode:','smk_sbg') .'</h3>';
			echo '<p>' . __('Paste the shortcode anywhere you want, in posts, pages etc. If the input accept shortcodes, then the sidebar will be displayed.','smk_sbg') .'</p>';
			echo '<pre>[smk_sidebar id="SIDEBAR_ID"]</pre>';
			echo '<h3>' . __('Function:','smk_sbg') .'</h3>';
			echo '<p>' . __('You can use the built-in function, but for this you should modify theme files and make sure to check if the function exists before use it.','smk_sbg') .'</p>';
			echo '<pre>
if(function_exists("smk_sidebar"){
 	smk_sidebar("SIDEBAR_ID");
}
			</pre>';
			echo '<h3>' . __('WP Native function:','smk_sbg') .'</h3>';
			echo '<p>' . __('You can use the built-in function <em>smk_sidebar</em>, but anyways I recommend using WP native function to avoid conflicts.','smk_sbg') .'</p>';
			echo "<pre>
if(function_exists('dynamic_sidebar') && dynamic_sidebar('SIDEBAR_ID')) : 
endif;
			</pre>";

			echo '<h3>' . __('For more info visit the following links:','smk_sbg') .'</h3>';
			echo '<div class="sbg_docs_links">';
				echo '<a href="http://wordpress.org/plugins/smk-sidebar-generator/" target="_blank">Official Plugin Page</a>';
				echo '<a href="https://github.com/Smartik89/Wordpress-Sidebar-Generator" target="_blank">Github Repository</a>';
			echo '</div>';

		echo '</div>';	

		// smk_ppprint( get_option($this->plugin_option) );

	echo '</div>';

	}

	public static function get_all_sidebars(){
		global $wp_registered_sidebars;
		
		$all_sidebars = array();
		
		if ( $wp_registered_sidebars && ! is_wp_error( $wp_registered_sidebars ) ) {
			
			foreach ( $wp_registered_sidebars as $sidebar ) {
				$all_sidebars[ $sidebar['id'] ] = $sidebar['name'];
			}
			
		}
		
		return $all_sidebars;
	}

	public static function import_all_sidebars(){
		if(isset( $_POST )){
			//delete_option('smk_sidebar_generator_option');
			$exported_data = (isset($_POST['content'])) ? trim($_POST['content']) : false;

			if( isset($exported_data) && !empty($exported_data) ){
				if(is_serialized(base64_decode($exported_data))){
					$exported_data = unserialize( base64_decode($exported_data) );

					$saved = get_option('smk_sidebar_generator_option');
					if( is_array($exported_data) ){
						if(is_array($saved) && isset($saved['sidebars'])){
							$sidebars['sidebars'] = wp_parse_args($exported_data['sidebars'], $saved['sidebars']);
							$new = wp_parse_args($sidebars, $saved);
						}
						else{
							$new = $exported_data;
						}
						update_option('smk_sidebar_generator_option', $new);
						echo 'imported';

					}
					else{
						echo __('Data is not valid.','smk_sbg');
					}
				}
				else{
					echo __('Data is not valid.','smk_sbg');
				}
				

			}
			else{
				echo __('Please enter data.','smk_sbg');
			}

			// print_r($saved);
		}
		die();
	}
	
	
	}//Class end
}//class_exists check end



/*
----------------------------------------------------------------------
!!! IMPORTANT !!! Init that class
----------------------------------------------------------------------
*/
new SMK_Sidebar_Generator();


/*
----------------------------------------------------------------------
Function
----------------------------------------------------------------------
*/
function smk_sidebar($id){
	if(function_exists('dynamic_sidebar') && dynamic_sidebar($id)) : 
	endif;
	return true;
}

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

/*
----------------------------------------------------------------------
Shortcode
----------------------------------------------------------------------
*/
// [smk_sidebar id="X"] //X is the sidebar ID
function smk_sidebar_shortcode( $atts ) {
	
	extract( shortcode_atts( array(
		'id' => null,
	), $atts ) );

	smk_sidebar($id);

}
add_shortcode( 'smk_sidebar', 'smk_sidebar_shortcode' );
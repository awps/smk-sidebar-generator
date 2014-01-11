<?php
/*
Plugin Name: SMK Sidebar Generator
Plugin URI: https://github.com/Smartik89/Wordpress-Sidebar-Generator
Description: This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish.
Author: Smartik
Version: 2.1.1
Author URI: http://smartik.ws/
*/

//Do not allow direct access to this file.
if( ! function_exists('add_action') ) die('Not funny!');

//Some usefull constants
if(!defined('SMK_SBG_VERSION')) define( 'SMK_SBG_VERSION', '2.1.1' );
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
		$exists   = 0;//Do not exist

		if( is_array($sidebars) ){
			if( is_array($sidebars['sidebars']) ){
				foreach ($sidebars['sidebars'] as $key => $v) {
					if( in_array(strtolower( $new_name ), array_map('strtolower', $v) ) ){
						$exists = 1;//Exist
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

	 		wp_enqueue_style( 'smk_sbg_styles', SMK_SBG_URI . 'assets/styles.css' );
	 	
			//Enqueue scripts
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-slider');

	 		wp_enqueue_script( 'smk_sbg_scripts', SMK_SBG_URI . 'assets/scripts.js' );
	 		wp_localize_script('smk_sbg_scripts', 'smk_sbg_lang', array(
					'remove'        => __('Remove', 'smk_sbg'),
					'not_saved_msg' => __("You've made changes, don't forget to save.", 'smk_sbg'),
					'ok'            => __("Changes were saved succefully.", 'smk_sbg'),
					'fail'          => __("An unexpected error ocurred.", 'smk_sbg'),
					'created'       => __("The sidebar was succefully created.", 'smk_sbg'),
					's_exists'      => __("The sidebar already exists. Please change the name.", 'smk_sbg'),
					'empty'         => __("Please enter a name for this sidebar.", 'smk_sbg'),
					's_remove'      => __("Are you sure? If you remove this sidebar it can't be restored.", 'smk_sbg'),
					's_removed'     => __("Sidebar Removed", 'smk_sbg'),
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
			__('Add new', 'smk_sbg'),
			__('Save Changes', 'smk_sbg'),
			__('Sidebar Name:', 'smk_sbg'),
			__('Function:', 'smk_sbg'),
			__('Shortcode:', 'smk_sbg'),
			__('Remove', 'smk_sbg'),
		);

	echo '<div class="wrap smk_sbg_main_block">';

		//delete_option($this->plugin_option);//DO NOT UNCOMMENT THIS OR ALL SIDEBARS WILL BE DELETED

		//Page UI
		screen_icon();
		echo '<h2>'. $this->sbg_name .' <span class="smk_sbg_version">v.' . SMK_SBG_VERSION .'</span></h2>';

		//Messages
		echo '<div class="smk_sbg_message"></div>';

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
			echo '<span class="smk_sbg_col_title sbg_function">'. $text[3] .'</span>';
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
								<span class="smk_sbg_code"><code>smk_sidebar("smk_sbg_' . $sidebar['id'] . '");</code></span>
								<span class="smk_sbg_code"><code>[smk_sidebar id="' . $sidebar['id'] . '"]</code></span>
							<span class="smk_sbg_remove_sidebar">'. $text[5] .'</span></div>';

						}
					}

				}

			echo '</div>';


		echo '</form>';
		
		//smk_ppprint( get_option($this->plugin_option) );

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
// [smk_sidebar id="X"] //X is the sidebar number
function smk_sidebar_shortcode( $atts ) {
	
	extract( shortcode_atts( array(
		'id' => null,
	), $atts ) );

	smk_sidebar('smk_sbg_' . $id);

}
add_shortcode( 'smk_sidebar', 'smk_sidebar_shortcode' );

/*
----------------------------------------------------------------------
Include metabox class
----------------------------------------------------------------------
*/
require_once(SMK_SBG_PATH . 'smk_sidebar_metabox.php');
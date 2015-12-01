<?php
/* 
 * Plugin Name: SMK Sidebar Generator
 * Plugin URI:  https://github.com/Smartik89/Wordpress-Sidebar-Generator
 * Description: Generate an unlimited number of sidebars and assign them to any page using the conditional options without touching a single line of code. 
 * Author:      Smartik
 * Version:     3.1
 * Author URI:  http://smartik.ws/
 * Licence:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Copyright:   (c) 2015 Smartik. All rights reserved
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

/**
 * Plugin version
 *
 * Get the current plugin version.
 * 
 * @return string 
 */
function smk_sidebar_version(){
	if( is_admin() ){
		$data = get_file_data( __FILE__, array( 'Version' ) );
		return empty( $data ) ? '' : $data[0];
	}
	else{
		return false;
	}
}

/**
 * All conditions
 *
 * All condtions will be accessible from this function
 *
 * @return array All conditions type => class_name 
 */
function smk_sidebar_conditions_filter(){
	return apply_filters( 'smk_sidebar_conditions_filter', array() );
}

/**
 * Register a condition
 *
 * Register a condition and inject it in the main array
 *
 * @param string $name Condition class name
 * @return void 
 */
class Smk_Sidebar_Generator_Register_Condition{
	public $name;
	public $allCond;

	public function __construct( $name ){
		$this->name = $name;
		$this->allCond = smk_sidebar_conditions_filter();
		add_filter( 'smk_sidebar_conditions_filter', array( $this, 'add') );
	}
	public function add(){
		if( class_exists( $this->name ) ){
			$class = new $this->name;
			if( ! array_key_exists($class->type, $this->allCond) ){
				return array( $class->type => $this->name );
			}
		}
	}
}

/**
 * Register condition helper
 *
 * @param string $name Condition class name
 * @use Smk_Sidebar_Generator_Register_Condition
 * @return void 
 */
function smk_register_condition( $name ){
	new Smk_Sidebar_Generator_Register_Condition( $name );
}


//------------------------------------//--------------------------------------//

/**
 * Translate plugin
 *
 * Load plugin languages
 *
 */
add_action('plugins_loaded', 'smk_sidebar_load_textdomain');
function smk_sidebar_load_textdomain() {
	load_plugin_textdomain( 'smk-sidebar-generator', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

/*
-------------------------------------------------------------------------------
Smk Sidebar function
-------------------------------------------------------------------------------
*/
function smk_sidebar($id){
	if(function_exists('dynamic_sidebar') && dynamic_sidebar($id)) : 
	endif;
	return true;
}

/*
-------------------------------------------------------------------------------
Smk All Sidebars
-------------------------------------------------------------------------------
*/
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

/* Plugin path
------------------------------------------------*/
$path = plugin_dir_path( __FILE__ );

/* HTML helper
------------------------------------------------*/
require_once $path . 'html.php';

/* Conditions
------------------------------------------------*/
require_once $path . 'condition.php';
require_once $path . 'condition-cpt.php';

/* Init conditions
------------------------------------------------*/
smk_register_condition( 'Smk_Sidebar_Generator_Condition_Cpt' );

/* Plugin work
------------------------------------------------*/
require_once $path . 'abstract.php';
require_once $path . 'render.php';
require_once $path . 'apply.php';

/* Init plugin
------------------------------------------------*/
$smk_sidebar_generator = new Smk_Sidebar_Generator;
$smk_sidebar_generator->init();

/* Apply conditions
------------------------------------------------*/
$applySidebars = new Smk_Sidebar_Generator_Apply;
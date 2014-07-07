<?php
/* 
 * SMK Sidebar Generator Abstract
 * 
 * -------------------------------------------------------------------------------------
 * @Author:     Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright:  (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:               2014-03-12 21:17:04
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-07-08 01:35:57
 *
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

// Start object
if( ! class_exists('Smk_Sidebar_Generator_Abstract')) {
	abstract class Smk_Sidebar_Generator_Abstract {

		//------------------------------------//--------------------------------------//
		
		/**
		 * Plugin version
		 *
		 * Return the current plugin version.
		 *
		 * @return string 
		 */
		protected $version;

		//------------------------------------//--------------------------------------//
		
		public function __construct(){
			$this->version = $this->version();
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Plugin Settings
		 *
		 * Inner plugin settings.
		 * 
		 * @return array 
		 */
		abstract protected function pluginSettings();

		//------------------------------------//--------------------------------------//
		
		/**
		 * Page
		 *
		 * Create the admin page
		 *
		 * @return string 
		 */
		abstract public function page();

		//------------------------------------//--------------------------------------//
		
		/**
		 * Init the object
		 *
		 * Create a new instance of this plugin.
		 *
		 * @return void 
		 */
		public function init(){
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'admin_init', array( $this, 'registerSetting' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 99 );
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Register setting
		 *
		 * Register setting. This allows to update the option on form submit.
		 *
		 * @hook admin_init
		 * @return void 
		 */
		public function registerSetting() {
			$settings = $this->pluginSettings();
			register_setting( $settings['settings_register_name'], $settings['option_name'] );
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar Widgets
		 *
		 * Get all sidebar with all widgets assigned to it.
		 *
		 * @return array
		 */
		public function sidebarWidgets(){
			return wp_get_sidebars_widgets();
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * All saved widgets types
		 *
		 * Get all saved widget types.
		 *
		 * @return array
		 */
		public function widgetsTypes(){
			$all = $this->sidebarWidgets();
			$widgets = array();
			foreach ($all as $part) {
				foreach ($part as $key => $widget) {
					$widget_option_name = 'widget_'. substr($widget, 0, -2);
					$widgets[ $widget_option_name ] = $widget_option_name;
				}
			}
			return $widgets;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Widgets Options
		 *
		 * Get all data(options) for each widget type.
		 *
		 * @return array
		 */
		public function widgetsOptions(){
			$options = array();
			foreach ($this->widgetsTypes() as $key => $value) {
				$options[ $value ] = get_option( $value );
			}
			return $options;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * All registered sidebars
		 *
		 * Get all registered sidebars.
		 *
		 * @return array
		 */
		public function allRegisteredSidebars(){
			global $wp_registered_sidebars;	
			$all_sidebars = array();
			
			if ( $wp_registered_sidebars && ! is_wp_error( $wp_registered_sidebars ) ) {
				
				foreach ( $wp_registered_sidebars as $sidebar ) {
					$all_sidebars[ $sidebar['id'] ] = $sidebar;
				}
				
			}
			
			return $all_sidebars;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Menu
		 *
		 * Create a new submenu for this plugin.
		 *	
		 * @hook admin_menu
		 * @uses $this->page() to get the page display.
		 * @return void 
		 */
		public function menu(){
			$settings = $this->pluginSettings();

			add_submenu_page(
				$settings['menu_parent'], 
				$settings['name'], 
				$settings['name'], 
				$settings['capability'], 
				$settings['slug'], 
				array( $this, 'page' ) 
			);
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Enqueue
		 *
		 * Enqueue scripts and styles
		 *
		 * @hook admin_enqueue_scripts
		 * @return void
		 */
		public function enqueue(){
			if( $this->isPluginPage() ){
				$depend = array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-slider');
				wp_register_script( 'smk-sidebar-generator', $this->uri() . 'assets/scripts.js', $depend, $this->version );

				wp_enqueue_script('smk-sidebar-generator');
			}
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Is plugin page
		 *
		 * Check if the current page is plugin page.
		 *
		 * @return bool 
		 */
		public function isPluginPage(){
			$settings = $this->pluginSettings();
			return isset( $_GET['page'] ) && $_GET['page'] == $settings['slug'] ? true : false;
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Plugin version
		 *
		 * Get the current plugin version.
		 * 
		 * @return string 
		 */
		public function version(){
			if( is_admin() ){
				$data = get_file_data( __FILE__, array( 'Version' ) );
				return empty( $data ) ? '' : $data[0];
			}
			else{
				return false;
			}
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Plugin path
		 *
		 * Absolute plugin path.
		 * 
		 * @return string 
		 */
		public function path(){
			return plugin_dir_path( __FILE__ );
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Plugin URI
		 *
		 * Absolute plugin URI.
		 * 
		 * @return string 
		 */
		public function uri(){
			return plugin_dir_url( __FILE__ );
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Debug
		 *
		 * Debud saved data
		 * 
		 * @param array $data The data to debug.
		 * @return string 
		 */
		public function debug($data = array()){
			array_walk_recursive( $data, array( $this, 'debugFilter' ) );
			echo '<pre>';
				print_r($data);
			echo '</pre>';
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Debug filter
		 *
		 * Debud filter special characters.
		 * 
		 * @param array $data The data to filter.
		 * @return array 
		 */
		public function debugFilter(&$data){
			$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
		}

	} // class
} // class_exists
<?php
/* 
 * SMK Sidebar Generator Abstract
 * 
 * -------------------------------------------------------------------------------------
 * @Author:     Andrew Surdu
 * @Author URI: https://zerowp.com/
 * @Copyright:  (c) 2014-present Andrew Surdu. All rights reserved
 * -------------------------------------------------------------------------------------
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
		
		/**
		 * Html helpers
		 *
		 * Allows to create different HTML elements
		 *
		 * @return string 
		 */
		protected $html;

		//------------------------------------//--------------------------------------//
		
		public function __construct(){
			$this->version = smk_sidebar_version();
			$this->html = new Smk_Sidebar_Generator_Html;
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
			add_action( 'widgets_init', array( $this, 'registerGeneratedSidebars' ) );
			
			$this->setup();

			// update from v2
			$this->updateSidebarsFromV2();
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Add to init object
		 *
		 * Add to init object.
		 *
		 * @return void 
		 */
		public function setup(){}

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
			register_setting( $settings['settings_register_name'], $settings['option_name']/*, array( &$this, 'sanitizeData' )*/ );
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sanitize data
		 *
		 * Sanitize the data sent to the server. Unset all invalid or empty(none) conditions.
		 *
		 * @return array The sanitized data 
		 */
		// public function sanitizeData( $data ) {
		// 	if( is_array( $data ) && !empty( $data ) ){
		// 		$new_data = $data;
		// 		foreach ($data as $sidebar_id => $sidebar_settings) {
		// 			if( !empty($sidebar_settings['conditions']) && is_array($sidebar_settings['conditions']) ){
		// 				foreach ($sidebar_settings['conditions'] as $key => $condition) {
		// 					if( !empty($condition['if']) && $condition['if'] == 'none' ){
		// 						unset( $new_data[ $sidebar_id ]['conditions'][ $key ] );
		// 					}
		// 					else{
		// 						continue;
		// 					}
		// 				}
		// 			}
		// 		}
		// 		$data = $new_data;
		// 	}
		// 	return $data;
		// }

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
		 * All generated sidebars
		 *
		 * Get all generated sidebars.
		 *
		 * @return array
		 */
		public function allGeneratedSidebars(){
			$all = get_option( $this->pluginSettings('option_name'), array() );
			if( !empty( $all['sidebars'] ) ){
				return $all['sidebars'];
			}
			else{
				return array();
			}
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Register sidebars
		 *
		 * Register all generated sidebars
		 *
		 * @hook widgets_init
		 * @return void 
		 */
		public function registerGeneratedSidebars() {

			//Catch saved options
			$sidebars = get_option( $this->pluginSettings('option_name'), array() );

			//Make sure if we have valid sidebars
			if ( !empty( $sidebars['sidebars'] ) && is_array( $sidebars['sidebars'] ) ){

				//Register each sidebar
				foreach ($sidebars['sidebars'] as $sidebar) {
					if( isset($sidebar) && !empty($sidebar) ){

						register_sidebar(
							array(
								'name'          => $sidebar['name'],
								'id'            => $sidebar['id'],
								'description'   => $sidebar['description'],
								'before_widget' => '<div id="%1$s" class="widget %2$s">',
								'after_widget'  => '</div>',
								'before_title'  => '<h3 class="widget-title">',
								'after_title'   => '</h3>'
							)
						);

					}
				}

			}

		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * All static sidebars
		 *
		 * Get all static sidebars.
		 *
		 * @return array
		 */
		public function allStaticSidebars(){
			$all = $this->allRegisteredSidebars();
			$generated = $this->allGeneratedSidebars();
			$static = array();
			foreach ( $all as $key => $value) {
				if( ! array_key_exists($key, $generated) ){
					$static[ $key ] = $value;
				}
			}
			return $static;
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
			
				wp_register_style( 'smk-sidebar-generator', $this->uri() . 'assets/styles.css', '', $this->version );
				wp_register_script( 'smk-sidebar-generator', $this->uri() . 'assets/scripts.js', $depend, $this->version, true );
				wp_enqueue_style( 'smk-sidebar-generator' );
				wp_enqueue_script( 'smk-sidebar-generator' );
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
		 * The prefix for sidebar ID
		 *
		 * Generate the prefix for sidebar ID based on current WP setup
		 * 
		 * @return string 
		 */
		public function prefix(){
			$theme             = get_option( 'current_theme', '' );
			$wordpress_version = get_bloginfo( 'version', '' );
			// Make the prefix
			$string = 's' . substr( $theme, 0, 1 ) . $wordpress_version;
			$string = preg_replace('/[^\w-]/', '', $string);
			return  strtolower( $string ) . '_';
		}

		//------------------------------------//--------------------------------------//

		/**
		 * Sidebars from v2
		 *
		 * Get all sidebars generated by Smk Sidebars generator v2, move and convert them
		 * for v3 and delete old data. Backwards compatibility function.
		 * 
		 * @return void 
		 */
		public function updateSidebarsFromV2(){
			if( false === get_transient( 'smk_sidebar_generator_option_v2' ) ){
				$option_name = $this->pluginSettings('option_name');
				$old = get_option( 'smk_sidebar_generator_option', array() );
				$new = get_option( $option_name, array() );
				$final = $new;

				$v2_sidebars = ! empty( $old['sidebars'] ) ? $old['sidebars'] : array();
				$v3_sidebars = ! empty( $new['sidebars'] ) ? $new['sidebars'] : array();

				$v2 = array();
				foreach ($v2_sidebars as $key => $value) {
					$v2['smk_sidebar_' . $key]['id'] = 'smk_sidebar_' . $value['id'];
					$v2['smk_sidebar_' . $key]['name'] = $value['name'];
					$v2['smk_sidebar_' . $key]['description'] = '';
				}

				$final_sidebars['sidebars'] = wp_parse_args( $v3_sidebars, $v2 );

				$final = wp_parse_args( $final_sidebars, $new );

				update_option( $option_name, $final );
				delete_option( 'smk_sidebar_generator_option' );
				set_transient( 'smk_sidebar_generator_option_v2', true );
			}
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
		public function debug($data = array(), $title = ''){
			if( is_array($data) ){
				array_walk_recursive( $data, array( $this, 'debugFilter' ) );
			}
			if( !empty($title) ){
				echo '<h3>'. $title .'</h3>';
			}
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

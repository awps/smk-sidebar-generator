<?php
/* 
 * Smk Sidebar Generator Render
 *
 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:   2014-07-08 00:56:11
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-07-08 01:24:08
 *
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

// Start object
if( class_exists('Smk_Sidebar_Generator_Abstract')) {
	class Smk_Sidebar_Generator extends Smk_Sidebar_Generator_Abstract {

		//------------------------------------//--------------------------------------//
		
		/**
		 * Plugin Settings
		 *
		 * Inner plugin settings.
		 * 
		 * @return array 
		 */
		protected function pluginSettings( $key = '' ){
			$settings = array(
				'name'                   => __('Sidebar Generator', 'smk_sbg'),
				'slug'                   => strtolower( __CLASS__ ),
				'version'                => $this->version,
				'capability'             => 'manage_options',
				'menu_parent'            => 'themes.php',
				'option_name'            => 'smk_sidebar_generator',
				'settings_register_name' => 'smk_sidebar_generator_register',
			);

			if( !empty($key) && array_key_exists($key, $settings) ){
				return $settings[ $key ];
			}
			else{
				return $settings;
			}
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Page
		 *
		 * Create the admin page
		 *
		 * @return string 
		 */
		public function page(){
			$this->pageOpen();
			settings_fields( $this->pluginSettings('settings_register_name') );
				
				$this->allSidebarsList();
				
			submit_button();
			$this->pageClose();

			$this->debug( $this->allRegisteredSidebars() );
			$this->debug( wp_get_widget_defaults() );
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Page Open
		 *
		 * Outputs the top part of HTML for this page. 
		 *
		 * @return string 
		 */
		public function pageOpen($echo = true){
			$html = '<div class="wrap">';
			$html .= '<form method="post" action="options.php" class="smk_sbg_main_form">';
			if( $echo ) { echo $html; } else { return $html; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Page Close
		 *
		 * Outputs the bottom part of HTML for this page. 
		 *
		 * @return string 
		 */
		public function pageClose($echo = true){
			$html = '</form>';
			$html .= '</div>';
			if( $echo ) { echo $html; } else { return $html; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * All sidebars list
		 *
		 * All sidebars list
		 *
		 * @return string The HTML.
		 */
		public function allSidebarsList($echo = true){
			$list = '<div id="smk-sidebars" class="accordion-container"><ul>';
				foreach ( (array) $this->allRegisteredSidebars() as $id => $s ) {
					$list .= $this->aSingleListItem( $s );
				}
			$list .= '</ul></div>';
			if( $echo ) { echo $list; } else { return $list; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * A single sidebar list
		 *
		 * Output the HTML for a single sidebar list.
		 *
		 * @param array $sidebar_data Sidebar data
		 * @return string The HTML.
		 */
		public function aSingleListItem($sidebar_data){
			$settings = $this->pluginSettings();
			if( !empty($sidebar_data) ) : 
			return '
				<li class="control-section accordion-section">
					<h3 class="accordion-section-title hndle">'. $sidebar_data['name'] .'</h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">
						<label>'. __('Name:', 'smk_sbg') .'</label>
						<input class="sbg-name" type="text" value="'. $sidebar_data['name'] .'" name="'. $settings['option_name'] .'[sidebars]['. $sidebar_data['id'] .'][name]">
						<label>'. __('ID:', 'smk_sbg') .'</label>
						<input class="sbg-id" type="text" value="'. $sidebar_data['id'] .'" name="'. $settings['option_name'] .'[sidebars]['. $sidebar_data['id'] .'][id]">
						<label>'. __('Description:', 'smk_sbg') .'</label>
						<input class="sbg-id widefat" type="text" value="'. $sidebar_data['description'] .'" name="'. $settings['option_name'] .'[sidebars]['. $sidebar_data['id'] .'][description]">
						</div>
					</div>
				</li>
			';
			endif;
		}

	}
}
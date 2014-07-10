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
 * @Last Modified time: 2014-07-11 01:35:58
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
			echo '<div class="smk-sidebars-list-template" style="display: none;">';
				$this->sidebarListTemplate();
			echo '</div>';
			$this->pageOpen();
				settings_fields( $this->pluginSettings('settings_register_name') );

				$counter = get_option( $this->pluginSettings('option_name'), array() );
				$counterval = ! empty( $counter['counter'] ) ? absint( $counter['counter'] ) : intval( '0' );
				echo $this->html->input(
					'smk-sidebar-generator-counter', // ID
					$this->pluginSettings( 'option_name' ) . '[counter]', 
					absint( $counterval ), 
					array(
						'type' => 'hidden',
					)
				);
				
				$this->allSidebarsList();
				
			submit_button();
			$this->pageClose();

			$this->debug( $this->allStaticSidebars() );
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
			$html = '<div class="wrap sbg-clearfix">';
			$html .= '<h2>'. $this->pluginSettings( 'name' ) .'
				<span class="add-new-h2 add-new-sidebar">'. __('Add new', 'smk_sbg') .'</span>
			</h2>';
			$html .= '<div class="smk-sidebars-grid">';
			$html .= '<h3>
					'. __('Sidebars', 'smk_sbg') .'
					<span class="tip dashicons-before dashicons-editor-help" title="'. __('All available sidebars.', 'smk_sbg') .'"></span>
				</h3>';
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
			$html .= $this->allRemovedSidebarsList( false );
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
			$all = $this->allGeneratedSidebars();
			$list = '<div id="smk-sidebars" class="accordion-container smk-sidebars-list">';
			$list .= '<ul class="connected-sidebars-lists">';
				if( !empty( $all ) ){
					foreach ( (array) $all as $id => $s ) {
						$list .= $this->aSingleListItem( $s );
					}
				}
			$list .= '</ul>';
			$list .= '</div>';
			if( $echo ) { echo $list; } else { return $list; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * A single sidebar list
		 *
		 * Output the HTML for a single sidebar list.
		 *
		 * @param array $sidebar_data Sidebar data
		 * @param array $settings Sidebar custom settings
		 * @param bool $sidebars_fields Show or hide sidebars fields. This is required only for gen. sidebars.
		 * @return string The HTML.
		 */
		public function aSingleListItem($sidebar_data, $settings = false, $sidebars_fields = true){
			$settings = ( $settings && is_array( $settings ) ) ? $settings : $this->pluginSettings();
			$class    = ( !empty( $settings['class'] ) ) ? ' '. $settings['class'] : '';
			$name     = $settings['option_name'] .'[sidebars]['. $sidebar_data['id'] .']';
			
			// To replace
			$static   = $this->allStaticSidebars();
			$static_sidebars = '';
			$replace = !empty( $sidebar_data['replace'] ) ? $sidebar_data['replace'] : array();
			foreach ($static as $key => $value) {
				$selected = ( in_array($key, $replace) ) ? ' selected="selected"' : '';
				$static_sidebars .= '<option value="'. $key .'"'. $selected .'>'. $value['name'] .'</option>';
			}

			// All pages
			$all_pages = get_pages();
			$pages_options = '';
			foreach ( $all_pages as $page ) {
				$pages_options .= '<option value="' . $page->ID . '">';
				$pages_options .= $page->post_title;
				$pages_options .= '</option>';
			}

			if( !empty($sidebar_data) ) : 
			$the_sidebar = '
				<li id="'. $sidebar_data['id'] .'" class="control-section accordion-section'. $class .'">
					<h3 class="accordion-section-title hndle">
						<span class="name">'. $sidebar_data['name'] .'</span>&nbsp;
						<span class="description">'. $sidebar_data['description'] .'</span>&nbsp;
						<div class="moderate-sidebar">
							<span class="smk-delete-sidebar">'. __('Delete', 'smk_sbg') .'</span>
							<span class="smk-restore-sidebar">'. __('Restore', 'smk_sbg') .'</span>
						</div>
					</h3>';
					
					if( $sidebars_fields ) {
						$the_sidebar .= '<div class="accordion-section-content" style="display: none;">
							<div class="inside">
								
								<div class="smk-sidebar-row">
								<label>'. __('Name:', 'smk_sbg') .'</label>'. 
								$this->html->input(
									'', // ID
									$name. '[name]', 
									$sidebar_data['name'], 
									array(
										'type' => 'text',
										'class' => array( 'smk-sidebar-name' ),
									)
								) 
								.'
								</div>

								<div class="smk-sidebar-row" style="display: none;">
								<label>'. __('ID:', 'smk_sbg') .'</label>'. 
								$this->html->input(
									'', // ID
									$name. '[id]', 
									$sidebar_data['id'], 
									array(
										'type' => 'text',
										'class' => array( 'smk-sidebar-id' ),
									)
								) 
								.'
								</div>
								
								<div class="smk-sidebar-row">
								<label>'. __('Description:', 'smk_sbg') .'</label>'. 
								$this->html->input(
									'', // ID
									$name. '[description]', 
									$sidebar_data['description'], 
									array(
										'type' => 'text',
										'class' => array( 'smk-sidebar-description', 'widefat' ),
									)
								) 
								.'
								</div>
								
								<div class="smk-sidebar-row">
								<label>'. __('Sidebars to replace:', 'smk_sbg') .'</label>
								<select multiple="multiple" name="'. $name .'[replace][]">'.
									$static_sidebars
								.'</select>
								</div>
								
								<h3>'. __('Conditions:', 'smk_sbg') .'</h3>
								<div class="smk-sidebar-row">
								<span>'. __('Replace if', 'smk_sbg') .' </span>
								<select name="'. $name .'[conditions][0][if]">
									<option value="page">Page</option>
									<option value="post">Post</option>
								</select>
								 - 
								<select name="'. $name .'[conditions][0][equal]">
									<option value="is">'. __('is equal to', 'smk_sbg') .'</option>
									<option value="is not">'. __('is not equal to', 'smk_sbg') .'</option>
								</select>
								 - 
								<select name="'. $name .'[conditions][0][to]">'.
									$pages_options
								.'</select>
								</div>

							</div>';

						$the_sidebar .= '</div>';
					}

				$the_sidebar .= '</li>';
			return $the_sidebar;
			endif;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar Template
		 *
		 * Create the template for new sidebars generation
		 *
		 * @return string The HTML.
		 */
		public function sidebarListTemplate($echo = true){
			$sidebar_data = array( 
				'name'        => __('New sidebar __index__', 'smk_sbg'),
				'id'          => '__id__',
				'description' => '',
			);

			$settings = array( 
				'option_name' => $this->pluginSettings('option_name'),
				'class'       => 'sidebar-template',
			);
			$item = $this->aSingleListItem( $sidebar_data, $settings );
			if( $echo ) { echo $item; } else { return $item; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * All removed sidebars list
		 *
		 * All removed sidebars list
		 *
		 * @return string The HTML.
		 */
		public function allRemovedSidebarsList($echo = true){
			$list = '<div class="smk-sidebars-grid removed-sidebars">
				<h3>
					'. __('Removed', 'smk_sbg') .'
					<span class="tip dashicons-before dashicons-editor-help" title="'. __('These sidebars will be removed on the next page refresh.', 'smk_sbg') .'"></span>
				</h3>
				<div id="smk-removed-sidebars" class="accordion-container smk-sidebars-list">
					<ul class="connected-sidebars-lists"></ul>
				</div>
			</div>';
			if( $echo ) { echo $list; } else { return $list; }
		}



	}
}
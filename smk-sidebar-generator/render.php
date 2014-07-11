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
 * @Last Modified time: 2014-07-12 00:23:06
 *
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

// Start object
if( class_exists('Smk_Sidebar_Generator_Abstract')) {
	class Smk_Sidebar_Generator extends Smk_Sidebar_Generator_Abstract {

		public function setup(){
			add_action( 'wp_ajax_smk_sbg_load_equalto', array( $this, 'equaltoAjax' ) );
		}

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

			// $this->debug( $this->allStaticSidebars() );
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
		 * @return string The HTML.
		 */
		public function aSingleListItem($sidebar_data, $settings = false){
			$settings = ( $settings && is_array( $settings ) ) ? $settings : $this->pluginSettings();
			$name     = $settings['option_name'] .'[sidebars]['. $sidebar_data['id'] .']';
			
			// All pages
			$all_pages = get_pages();
			$pages_options = '';
			foreach ( $all_pages as $page ) {
				$pages_options .= '<option value="' . $page->ID . '">';
				$pages_options .= $page->post_title;
				$pages_options .= '</option>';
			}

			if( !empty($sidebar_data) ) : 
				$the_sidebar = $this->sidebarAccordion('open', $sidebar_data, $settings, false);

					$the_sidebar .= $this->fieldName($name, $sidebar_data);
					$the_sidebar .= $this->fieldId($name, $sidebar_data);
					$the_sidebar .= $this->fieldDescription($name, $sidebar_data);
					$the_sidebar .= $this->fieldToReplace($name, $sidebar_data);
					
					// Conditions
					$the_sidebar .= '<div class="smk-sidebar-row conditions-all">';

						$the_sidebar .= '<h3>'. __('Conditions:', 'smk_sbg') .'</h3>';

						if( !empty($sidebar_data['conditions']) ){
							foreach ( (array) $sidebar_data['conditions'] as $index => $condition) {
								$the_sidebar .= '<div class="condition-parent sbg-clearfix">';
									$the_sidebar .= $this->fieldConditionMain( $name, $sidebar_data, $index );
									$the_sidebar .= ' - '. __('is equal to', 'smk_sbg') .' - ';
									$the_sidebar .= $this->fieldConditionEqualTo($name, $sidebar_data, $index, $condition['if']);
								$the_sidebar .= ' <span class="condition-clone button"> + </span>';
								$the_sidebar .= ' <span class="condition-remove"> x </span>';
								$the_sidebar .= '</div>';
							}
						}
						else{
								$the_sidebar .= '<div class="condition-parent sbg-clearfix">';
									$the_sidebar .= $this->fieldConditionMain( $name, $sidebar_data, 0 );
									$the_sidebar .= ' - '. __('is equal to', 'smk_sbg') .' - ';
									$the_sidebar .= $this->fieldConditionEqualTo($name, $sidebar_data, 0, 'all');
								$the_sidebar .= ' <span class="condition-clone button"> + </span>';
								$the_sidebar .= ' <span class="condition-remove"> x </span>';
								$the_sidebar .= '</div>';
						}
						
					$the_sidebar .= '</div>';

				$the_sidebar .= $this->sidebarAccordion('close', $sidebar_data, $settings, false);
			return $the_sidebar;
			endif;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar Accordion
		 *
		 * Global parts of a single sidebar accordion
		 * 
		 * @param string $part `open` or `close`
		 * @return string The HTML.
		 */
		public function sidebarAccordion($part, $sidebar_data = array(), $settings = array(), $echo = true){

			$class    = ( !empty( $settings['class'] ) ) ? ' '. $settings['class'] : '';
			if( $part == 'open' ){
				$the_sidebar = '
				<li id="'. $sidebar_data['id'] .'" class="control-section accordion-section'. $class .'">
					<h3 class="accordion-section-title hndle">
						<span class="name">'. $sidebar_data['name'] .'</span>&nbsp;
						<span class="description">'. $sidebar_data['description'] .'</span>&nbsp;
						<div class="moderate-sidebar">
							<span class="smk-delete-sidebar">'. __('Delete', 'smk_sbg') .'</span>
							<span class="smk-restore-sidebar">'. __('Restore', 'smk_sbg') .'</span>
						</div>
					</h3>
					<div class="accordion-section-content" style="display: none;">
						<div class="inside">';
			}
			elseif( $part == 'close' ){
						$the_sidebar = '</div>
					</div>
				</li>';
			}
			else{
				$the_sidebar = '';
			}

			if( $echo ) { echo $the_sidebar; } else { return $the_sidebar; }
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field name
		 *
		 * Display sidebar name field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldName($name, $sidebar_data){
			return '<div class="smk-sidebar-row">
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
			.'</div>';
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field ID
		 *
		 * Display sidebar ID field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldId($name, $sidebar_data){
			return '<div class="smk-sidebar-row" style="display: none;">
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
			.'</div>';
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field description
		 *
		 * Display sidebar description field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldDescription($name, $sidebar_data){
			return '<div class="smk-sidebar-row">
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
			.'</div>';
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field To Replace
		 *
		 * Display sidebar To Replace field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldToReplace($name, $sidebar_data){

			// To replace
			$static   = $this->allStaticSidebars();
			$static_sidebars = '';
			$replace = !empty( $sidebar_data['replace'] ) ? $sidebar_data['replace'] : array();
			foreach ($static as $key => $value) {
				$static_sidebars[ $key ] = $value['name'];
			}

			return '<div class="smk-sidebar-row">
				<label>'. __('Sidebars to replace:', 'smk_sbg') .'</label>'. 
				$this->html->select(
					'', // ID
					$name. '[replace][]', 
					$replace,
					array(
						'multiple' => 'multiple',
						'options' => $static_sidebars,
					)
				) 
			.'</div>';
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field Condition main
		 *
		 * Display sidebar Condition main field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldConditionMain($name, $sidebar_data, $index = 0){

			$pt_args = array(
				'public'   => true,
				'_builtin' => false
			);
			$pt = array(
				'post_type::post' => _x('Post', 'Post type name', 'smk_sbg'),
				'post_type::page' => _x('Page', 'Post type name', 'smk_sbg'),
			);
			$post_types = get_post_types( $pt_args, 'objects' );
			foreach ($post_types as $post_type) {
				$pt[ 'post_type::' . $post_type->name ] = $post_type->label;
			}

			return '<span>'. __('Replace if', 'smk_sbg') .' </span>'.
				$this->html->select(
					'', // ID
					$name. '[conditions]['. absint( $index ) .'][if]', 
					$sidebar_data['conditions'][ absint( $index ) ]['if'], 
					array(
						'options' => array(
							'all' => __('All', 'smk_sbg'),
							array(
								'label' => __('Post types', 'smk_sbg'),
								'options' => $pt,
							)
						),
						'class' => array('condition-if'),
					)
				);
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field Condition EqualTo
		 *
		 * Display sidebar Condition EqualTo field
		 *
		 * @param string $name HTML field name
		 * @param string $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldConditionEqualTo($name, $sidebar_data, $index = 0, $type){

			$saved = ! empty( $sidebar_data['conditions'][ absint( $index ) ]['equalto'] ) ? 
			            $sidebar_data['conditions'][ absint( $index ) ]['equalto'] : '';

			return 
				$this->html->select(
					'', // ID
					$name. '[conditions]['. absint( $index ) .'][equalto]', 
					$saved, 
					array(
						'options' => $this->getEqualToOptions($type),
						// 'multiple' => 'multiple',
						// 'size' => 10,
						'class' => array('condition-equalto'),
					)
				);
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Get Equal to Options
		 *
		 * @param string $type Example pot_type::page 
		 * @return array
		 */
		public function getEqualToOptions($type){

			$options['all'] = __('All', 'smk_sbg');
			$the_type       = explode('::', $type);

			if( !empty( $the_type[0] ) && !empty( $the_type[1] ) ){
				switch ( $the_type[0] ) {
					case 'post_type':
						$posts = $this->getAllPostsFor( $the_type[1] );
						$options =  $options + (array) $posts;
						break;
					
					default:
						# code...
						break;
				}
				
			}

			return $options;
		}

		public function equaltoAjax(){	
			$data = $_POST['data'];
			$type = $data['condition_if'];
			$opt = $this->getEqualToOptions($type);

			echo json_encode( $opt );

			die();
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Get all posts from a post type
		 *
		 * @param string $post_type The post type name
		 * @return array
		 */
		public function getAllPostsFor($post_type){

			$all_posts = array();

			$posts = get_posts(array(
				'post_type'        => $post_type,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
			));

			foreach ( $posts as $post ) {
				$id = $post->ID;
				$all_posts[ $id ] = $post->post_title;
			}
			wp_reset_postdata();
			return $all_posts;
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

	}
}
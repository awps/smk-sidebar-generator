<?php
/* 
 * Smk Sidebar Generator Render
 *
 * -------------------------------------------------------------------------------------
 * @Author: Andrew Surdu
 * @Author URI: https://zerowp.com/
 * @Copyright: (c) 2014-present Andrew Surdu. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

// Start object
if( class_exists('Smk_Sidebar_Generator_Abstract')) {
	class Smk_Sidebar_Generator extends Smk_Sidebar_Generator_Abstract {

		public function setup(){
			add_action( 'wp_ajax_smk-sidebar-generator_load_equalto', array( $this, 'equaltoAjax' ) );
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
				'name'                   => __('Sidebar Generator', 'smk-sidebar-generator'),
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
			echo '<div class="smk-sidebars-condition-template" style="display: none;">';
				echo $this->aSingleCondition('__cond_name__', '', 0, 'all');
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


			// Debug start
			// $this->debug( $this->allStaticSidebars(), 'All static sidebars' );
			// $this->debug( $this->allGeneratedSidebars(), 'All generated sidebars' );
			// global $sidebars_widgets;
			// $this->debug( $sidebars_widgets, 'All sidebars and their widgets' );
			// $this->debug( smk_sidebar_conditions_filter(), 'All conditions' );
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
				<span class="add-new-h2 add-new-sidebar" data-sidebars-prefix="'. $this->prefix() .'">'. __('Add new', 'smk-sidebar-generator') .'</span>
			</h2>';
			$html .= '<div class="smk-sidebars-grid">';
			$html .= '<h3>
					'. __('Sidebars', 'smk-sidebar-generator') .'
					<span class="tip dashicons-before dashicons-editor-help" title="'. __('All available sidebars.', 'smk-sidebar-generator') .'"></span>
				</h3>';
			$html .= '<form method="post" action="options.php" class="smk-sidebar-generator_main_form">';
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

					$the_sidebar .= '<div class="sbg-clearfix">';
						$the_sidebar .= $this->fieldName($name, $sidebar_data);
						$the_sidebar .= $this->fieldDescription($name, $sidebar_data);
					$the_sidebar .= '</div>'; //.sbg-clearfix

					$the_sidebar .= '<div class="sbg-clearfix">';
					$the_sidebar .= $this->fieldId($name, $sidebar_data);
					$the_sidebar .= $this->fieldToReplace($name, $sidebar_data);
				
					// Conditions
					$the_sidebar .= '<div class="smk-sidebar-row conditions-all smk-sidebar-grid-8">';

						$conditions_checked = isset( $sidebar_data['enable-conditions'] ) ? ' checked="checked"' : '';
						$the_sidebar .= '<label>
							<input type="checkbox" name="'. $name. '[enable-conditions]" value="enabled" '. $conditions_checked .' class="smk-sidebar-enable-conditions" />'.  
							__('Enable conditions:', 'smk-sidebar-generator')
						.'</label>';


						$disbled_conditions = empty($conditions_checked) ? ' disabled-conditions' : '';
						$the_sidebar .= '<div class="created-conditions'. $disbled_conditions .'">';
							if( !empty($sidebar_data['conditions']) ){
								foreach ( (array) $sidebar_data['conditions'] as $index => $condition) {
									$the_sidebar .= $this->aSingleCondition($name, $sidebar_data, $index, $condition['if']);
								}
							}
							else{
								$the_sidebar .= $this->aSingleCondition($name, $sidebar_data, 0, 'all');
							}
						$the_sidebar .= '</div>'; //.created-conditions
					
					$disbled_conditions_btn = empty($conditions_checked) ? ' disabled="disabled"' : '';
					$the_sidebar .= ' <button class="condition-add button"'. $disbled_conditions_btn .' data-name="'. $name .'" data-sidebar-id="'. $sidebar_data['id'] .'">'. __('Add condition', 'smk-sidebar-generator') .'</button>';
					$the_sidebar .= '</div>'; //.conditions-all
					$the_sidebar .= '</div>'; //.sbg-clearfix

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
						<span class="smk-sidebar-section-icon dashicons dashicons-editor-justify"></span> 
						<span class="name">'. $sidebar_data['name'] .'</span>&nbsp;
						<span class="description">'. $sidebar_data['description'] .'</span>&nbsp;
						<div class="moderate-sidebar">
							<span class="smk-delete-sidebar">'. __('Delete', 'smk-sidebar-generator') .'</span>
							<span class="smk-restore-sidebar">'. __('Restore', 'smk-sidebar-generator') .'</span>
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
		 * A single condition
		 *
		 * Display a single condition
		 *
		 * @param string $name HTML field name
		 * @param string|array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function aSingleCondition($name, $sidebar_data, $index = 0, $condition_if = 'all'){
			$the_sidebar = '<div class="condition-parent sbg-clearfix">';
				$the_sidebar .= '<span class="smk-sidebar-condition-icon dashicons dashicons-menu"></span>';
				$the_sidebar .= '<div class="conditions-first">';
					$the_sidebar .= $this->fieldConditionMain( $name, $sidebar_data, $index );
				$the_sidebar .= '</div>';
				$the_sidebar .= '<div class="conditions-second">';
					$the_sidebar .= $this->fieldConditionEqualTo($name, $sidebar_data, $index, $condition_if);
				$the_sidebar .= '</div>';
			$the_sidebar .= ' <span class="condition-remove" title="'. __('Remove condition', 'smk-sidebar-generator') .'"> <i class="dashicons dashicons-no-alt"></i> </span>';
			$the_sidebar .= '</div>';
			return $the_sidebar;
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field name
		 *
		 * Display sidebar name field
		 *
		 * @param string $name HTML field name
		 * @param array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldName($name, $sidebar_data){
			return '<div class="smk-sidebar-row smk-sidebar-grid-4">
				<label>'. __('Name:', 'smk-sidebar-generator') .'</label>'. 
				$this->html->input(
					'', // ID
					$name. '[name]', 
					$sidebar_data['name'], 
					array(
						'type' => 'text',
						'class' => array( 'smk-sidebar-name', 'widefat' ),
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
		 * @param array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldId($name, $sidebar_data){
			return '<div class="smk-sidebar-row" style="display: none;">
				<label>'. __('ID:', 'smk-sidebar-generator') .'</label>'. 
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
		 * @param array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldDescription($name, $sidebar_data){
			return '<div class="smk-sidebar-row smk-sidebar-grid-8">
				<label>'. __('Description:', 'smk-sidebar-generator') .'</label>'. 
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
		 * @param array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldToReplace($name, $sidebar_data){

			// To replace
			$static   = $this->allStaticSidebars();
			$static_sidebars = [];

			foreach ($static as $key => $value) {
				$static_sidebars[ $key ] = $value['name'];
			}

			$replace = !empty( $sidebar_data['replace'] ) ? $sidebar_data['replace'] : array();

			return '<div class="smk-sidebar-row smk-sidebar-grid-4">
				<label>'. __('Sidebars to replace:', 'smk-sidebar-generator') .'</label>'. 
				$this->html->select(
					'', // ID
					$name. '[replace][]', 
					$replace,
					array(
						'multiple' => 'multiple',
						'options'  => $static_sidebars,
						'size'     => 9,
						'class'    => array( 'sidebars-to-replace-select' ),
					)
				) 
			.'
			<br />
			<label>'. __('Shortcode:', 'smk-sidebar-generator') .'</label>
			<code class="smk-sidebar-shortcode">smk_sidebar="'. $sidebar_data['id'] .'"</code>
			</div>';
		}

		//------------------------------------//--------------------------------------//
		
		/**
		 * Sidebar field Condition main
		 *
		 * Display sidebar Condition main field
		 *
		 * @param string $name HTML field name
		 * @param array $sidebar_data Data for current sidebar
		 * @return string The HTML
		 */
		public function fieldConditionMain($name, $sidebar_data, $index = 0){

			$options = array( 'none' => __('None', 'smk-sidebar-generator') );
			$all_conditions = smk_sidebar_conditions_filter();
			if( !empty($all_conditions) && is_array($all_conditions) ){
				foreach ($all_conditions as $type => $class) {
					if( class_exists($class) ){
						$newclass     = new $class;
						$newoptions   = $newclass->getMainData();
						if( !empty($newoptions) && is_array($newoptions) ){
							$options[] = $newoptions;
						}
					}
				}
			}

			$saved = ! empty( $sidebar_data['conditions'][ absint( $index ) ]['if'] ) ? 
			            $sidebar_data['conditions'][ absint( $index ) ]['if'] : '';

			return '<span class="condition-label">'. __('Replace if', 'smk-sidebar-generator') .' </span>'.
				$this->html->select(
					'', // ID
					$name. '[conditions]['. absint( $index ) .'][if]', 
					$saved, 
					array(
						'options' => $options,
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

			$saved = ! empty( $sidebar_data['conditions'][ absint( $index ) ]['equalto'] ) ? $sidebar_data['conditions'][ absint( $index ) ]['equalto'] : '';

			return '<span class="condition-label">'. __('and is equal to', 'smk-sidebar-generator') .'</span>' . 
				$this->html->select(
					'', // ID
					$name. '[conditions]['. absint( $index ) .'][equalto][]', 
					$saved, 
					array(
						'options' => $this->getEqualToOptions($type),
						'multiple' => 'multiple',
						'size' => 5,
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
			$the_type       = explode('::', $type);
			$options        = array();
			$all_conditions = smk_sidebar_conditions_filter();

			if( !empty($all_conditions) && is_array($all_conditions) ){
				if( array_key_exists($the_type[0], $all_conditions) ){
					$class = $all_conditions[ $the_type[0] ];
					if( class_exists($class) ){
						$newclass     = new $class;
						$newoptions   = $newclass->getSecondaryData( $type );
						if( !empty($newoptions) && is_array($newoptions) ){
							$options = $options + (array) $newoptions;
						}
					}
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
		 * All removed sidebars list
		 *
		 * All removed sidebars list
		 *
		 * @return string The HTML.
		 */
		public function allRemovedSidebarsList($echo = true){
			$list = '<div class="smk-sidebars-grid removed-sidebars">
				<h3>
					'. __('Removed', 'smk-sidebar-generator') .'
					<span class="tip dashicons-before dashicons-editor-help" title="'. __('These sidebars will be removed on the next page refresh.', 'smk-sidebar-generator') .'"></span>
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
				'name'        => sprintf( __('New sidebar %s', 'smk-sidebar-generator'), '__index__' ),
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

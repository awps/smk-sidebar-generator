<?php
// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

// Start object
if( ! class_exists('Smk_Sidebar_Generator_Apply')) {
	class Smk_Sidebar_Generator_Apply extends Smk_Sidebar_Generator{
		
		public function __construct(){
			parent::__construct();
			add_filter( 'sidebars_widgets', array( $this, 'letsDoIt') );
		}

		public function letsDoIt( $sidebars ){
			$all_possible_conditions = smk_sidebar_conditions_filter();
			$generated_data = $this->allGeneratedSidebars();

			foreach ($generated_data as $sidebar_id => $sidebar_settings) {
				$sidebars_to_replace = !empty($sidebar_settings['replace']) ? $sidebar_settings['replace'] : false;
				$conditions          = !empty($sidebar_settings['conditions']) && is_array($sidebar_settings['conditions']) ? $sidebar_settings['conditions'] : false;
				$enabled_conditions  = !empty($sidebar_settings['enable-conditions']) && 'enabled' == $sidebar_settings['enable-conditions'] ? $sidebar_settings['enable-conditions'] : false;
				
				// If conditions are enabled
				if( $sidebars_to_replace && $enabled_conditions && $conditions ){
					foreach ($conditions as $condition) {
						if( !empty($condition['if']) && 'none' !== $condition['if'] ){
							$the_type = explode('::', $condition['if']);
							if( !empty( $the_type[0] ) && !empty( $the_type[1] ) ){
								if( array_key_exists($the_type[0], $all_possible_conditions) ){
									$class = $all_possible_conditions[ $the_type[0] ];
									if( class_exists($class) ){
										$newclass = new $class;
										$second_condition = ( !empty($condition['equalto']) ) ? $condition['equalto'] : array();
										$can_replace = $newclass->canReplace( $the_type[1], $second_condition );
										if( $can_replace ){
											foreach ($sidebars_to_replace as $sidebar_to_replace_id) {
												if( array_key_exists($sidebar_to_replace_id, $sidebars) && isset($sidebars[ $sidebar_id ]) ){
													$sidebars[ $sidebar_to_replace_id ] = $sidebars[ $sidebar_id ];
												}
											}
										}
									}
								}
							}
						}
					}
				}

				// If conditions are not enabled and is selected at least one sidebar to replace
				elseif( $sidebars_to_replace ){
					foreach ($sidebars_to_replace as $sidebar_to_replace_id) {
						if( array_key_exists($sidebar_to_replace_id, $sidebars) && isset($sidebars[ $sidebar_id ]) ){
							$sidebars[ $sidebar_to_replace_id ] = $sidebars[ $sidebar_id ];
						}
					}
				}
			}

			return $sidebars;
		}

	}
}
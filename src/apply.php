<?php
// Do not allow direct access to this file.
if( ! function_exists('add_action') )
	die();

// Start object
if( ! class_exists('Smk_Sidebar_Generator_Apply')) {
	class Smk_Sidebar_Generator_Apply extends Smk_Sidebar_Generator{

		/**
		 * Cached condition class instances
		 *
		 * @var array
		 */
		private $condition_instances = array();

		public function __construct(){
			parent::__construct();
			add_filter( 'sidebars_widgets', array( $this, 'letsDoIt') );
		}

		/**
		 * Apply sidebar replacements
		 *
		 * Filters the sidebars_widgets array to replace static sidebars with
		 * generated sidebars based on configured conditions.
		 *
		 * @param array $sidebars The sidebars and their widgets.
		 * @return array Modified sidebars array with replacements applied.
		 */
		public function letsDoIt( $sidebars ){
			$all_possible_conditions = smk_sidebar_conditions_filter();
			$generated_data = $this->allGeneratedSidebars();

			foreach ( $generated_data as $sidebar_id => $sidebar_settings ) {
				$sidebars_to_replace = ! empty( $sidebar_settings['replace'] ) ? $sidebar_settings['replace'] : false;

				if ( ! $sidebars_to_replace ) {
					continue;
				}

				$conditions         = ! empty( $sidebar_settings['conditions'] ) && is_array( $sidebar_settings['conditions'] ) ? $sidebar_settings['conditions'] : false;
				$enabled_conditions = ! empty( $sidebar_settings['enable-conditions'] ) && 'enabled' === $sidebar_settings['enable-conditions'];

				// If conditions are not enabled, replace unconditionally
				if ( ! $enabled_conditions || ! $conditions ) {
					$sidebars = $this->replaceSidebars( $sidebars, $sidebar_id, $sidebars_to_replace );
					continue;
				}

				// Check each condition
				foreach ( $conditions as $condition ) {
					if ( empty( $condition['if'] ) || 'none' === $condition['if'] ) {
						continue;
					}

					$the_type = explode( '::', $condition['if'] );
					if ( empty( $the_type[0] ) || empty( $the_type[1] ) ) {
						continue;
					}

					if ( ! array_key_exists( $the_type[0], $all_possible_conditions ) ) {
						continue;
					}

					$condition_instance = $this->getConditionInstance( $the_type[0], $all_possible_conditions[ $the_type[0] ] );
					if ( ! $condition_instance ) {
						continue;
					}

					$second_condition = ! empty( $condition['equalto'] ) ? $condition['equalto'] : array();

					if ( $condition_instance->canReplace( $the_type[1], $second_condition ) ) {
						$sidebars = $this->replaceSidebars( $sidebars, $sidebar_id, $sidebars_to_replace );
						break; // One matching condition is enough
					}
				}
			}

			return $sidebars;
		}

		/**
		 * Replace target sidebars with source sidebar widgets
		 *
		 * @param array  $sidebars            The sidebars array.
		 * @param string $source_sidebar_id   The sidebar ID to copy widgets from.
		 * @param array  $sidebars_to_replace Array of sidebar IDs to replace.
		 * @return array Modified sidebars array.
		 */
		private function replaceSidebars( $sidebars, $source_sidebar_id, $sidebars_to_replace ) {
			if ( ! isset( $sidebars[ $source_sidebar_id ] ) ) {
				return $sidebars;
			}

			foreach ( $sidebars_to_replace as $target_sidebar_id ) {
				if ( array_key_exists( $target_sidebar_id, $sidebars ) ) {
					$sidebars[ $target_sidebar_id ] = $sidebars[ $source_sidebar_id ];
				}
			}

			return $sidebars;
		}

		/**
		 * Get or create a condition class instance
		 *
		 * @param string $type       The condition type key.
		 * @param string $class_name The condition class name.
		 * @return object|false The condition instance or false if class doesn't exist.
		 */
		private function getConditionInstance( $type, $class_name ) {
			if ( ! isset( $this->condition_instances[ $type ] ) ) {
				if ( ! class_exists( $class_name ) ) {
					$this->condition_instances[ $type ] = false;
				} else {
					$this->condition_instances[ $type ] = new $class_name;
				}
			}

			return $this->condition_instances[ $type ];
		}

	}
}
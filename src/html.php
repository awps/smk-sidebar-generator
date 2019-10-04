<?php
/* 
 * Title
 *
 * Description
 *
 * -------------------------------------------------------------------------------------
 * @Author: Andrew Surdu
 * @Author URI: https://zerowp.com/
 * @Copyright: (c) 2014-present Andrew Surdu. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 */
if( ! class_exists('Smk_Sidebar_Generator_Html') ){
	class Smk_Sidebar_Generator_Html{

		public function input( $id = '', $name = '', $value = '', $atts = array() ){
			$main = array(
				'id' => $id,
				'name' => $name,
				'value' => esc_html( $value ),
			);
			$all_args = wp_parse_args($atts, $main);

			return '<input'. $this->mergeAttributes($all_args) .' />';
		}

		public function select( $id = '', $name = '', $value = '', $atts = array() ){
			$main = array(
				'id' => $id,
				'name' => $name,
			);
			$all_args = wp_parse_args($atts, $main);

			$field = '<select'. $this->mergeAttributes($all_args, array('value') ) .'>';
				if( !empty( $atts['options'] ) && is_array( $atts['options'] ) ){
					foreach ( $atts['options'] as $key => $option ) {
						if( !is_array($option) ){
							$selected = ( in_array($key, (array) $value) ) ? ' selected="selected"' : '';
							$field .= '<option value="'. $key .'"'. $selected .'>'. $option .'</option>';
						}
						else{
							$optg_label = !empty($option['label']) ? $option['label'] : '';
							if( !empty( $option['options']) ){
								$field .= '<optgroup label="'. $optg_label .'">';
									foreach ( (array) $option['options'] as $gokey => $govalue) {
										$selected = ( in_array($gokey, (array) $value) ) ? ' selected="selected"' : '';
										$field .= '<option value="'. $gokey .'"'. $selected .'>'. $govalue .'</option>';
									}
								$field .= '</optgroup>';
							}
						}
					}
				}
			$field .= '</select>';

			return $field;
		}



		protected function mergeAttributes($atts = array(), $exclude = array()){

			// Dissalow certain attributes.
			if( !empty($exclude) && is_array($exclude) ){
				foreach ( (array) $exclude as $ex) {
					unset( $atts[$ex] );
				}
			}

			//If have attributes, proceed.
			if( !empty($atts) ){
				
				$return = array();
				foreach ($atts as $att => $val) {
					$att = trim( $att );
					switch ($att) {
						case 'class':
							$return[] = $this->makeAttribute($att, $this->getHtmlClass($val) );
							break;
						
						case 'options':
							break;
						
						default:
							if( !empty($att) ){
								$return[] = $this->makeAttribute($att, $val);
							}
							break;
					}
				}

				$final = implode(' ', $return);
				return ( !empty($final) ) ? ' '. $final : '';

			}
		}

		protected function getHtmlClass($att_val){
			if( is_array($att_val) ){
				foreach ($att_val as $class) {
					$classes[] = sanitize_html_class($class);
				}
			}
			elseif( is_string($att_val) ){
				$classes[] = sanitize_html_class($att_val);
			}
			else{
				$classes[] = array();
			}
			return implode( ' ', $classes );
		}

		protected function makeAttribute($attribute, $value = ''){
			if( !empty($value) )
				return ( ! is_bool($value) && !is_array($value) ) ? $attribute .'="'. esc_attr( $value ) .'"' : $attribute;
		}

	}
}

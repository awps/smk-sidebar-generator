<?php
/* 
 * Title
 *
 * Description
 *
 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:   2014-07-08 14:13:52
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-07-08 14:14:23
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

		protected function mergeAttributes($atts = array(), $exclude = array()){

			// Dissalow certain attributes.
			if( !empty($exclude) && is_array($exclude) ){
				foreach ($exclude as $ex) {
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
				return ( ! is_bool($value) ) ? $attribute .'="'. esc_attr( $value ) .'"' : $attribute;
		}

	}
}
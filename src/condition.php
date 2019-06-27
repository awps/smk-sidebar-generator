<?php
abstract class Smk_Sidebar_Generator_Condition{
	public $type;
	public $name;

	public function __construct(){
		$this->name = get_class( $this );
	}

	// Return key => value options
	public function prepareMainData(){
		return array();
	}

	// Return key => value options
	public function prepareSecondaryData( $main_value ){
		$array = array( 'all' => __('All', 'smk-sidebar-generator') );
		return $array;
	}

	public function getMainData(){
		$dataif = $this->prepareMainData();
		$newdata = array();
		if( !empty($dataif) && is_array($dataif) ){
			foreach ($dataif as $key => $value) {
				$newdata[ $this->type . '::' . $key ] = $value;
			}
			return array(
				'label' => $this->name,
				'options' => $newdata,
			);
		}
		else{
			return array();
		}
	}

	public function selected( $main_value ){
		$the_type = explode('::', $main_value);
		if( !empty( $the_type[0] ) && !empty( $the_type[1] ) ){
			return $the_type[1];
		}
		else{
			return false;
		}
	}

	public function getSecondaryData( $main_value ){
		return $this->prepareSecondaryData( $main_value );
	}

	// $first_selection = type::this_selection
	// $second_selection = equalto
	// Should return true or false
	public function canReplace( $first_selection, $second_selection ){
		$can = false;
		return $can;
	}
}
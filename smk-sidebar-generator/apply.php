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
			$generated_data = $this->allGeneratedSidebars();

			$default = $sidebars['smk_theme_sidebar'];
			$replace = $sidebars['sg411_191mih'];

			if( is_single() ){
				$sidebars['smk_theme_sidebar'] = $replace;
			}
			return $sidebars;
		}

	}
}
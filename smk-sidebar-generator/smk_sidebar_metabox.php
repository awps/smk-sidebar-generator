<?php
if( ! class_exists('SMK_Sidebar_Metabox') ) {
class SMK_Sidebar_Metabox {
	
	/* 
	The page where to show the meta box
	----------------------------------------------------------- */
	var $page;
	var $align;

	/*
	----------------------------------------------------------------------
	Constructor
	----------------------------------------------------------------------
	*/
    public function __construct( $page, $align ) {

		$this->page = $page;
		
		if( ! is_array( $this->page ) )
			$this->page = array( $this->page );
		
		$this->align = $align;
		
		add_action( 'admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts' ) );	//Admin scritps
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ));
    }
	

	/*
	----------------------------------------------------------------------
	Enqueue necessary scripts and styles
	----------------------------------------------------------------------
	*/
	function admin_enqueue_scripts() {
		wp_enqueue_script( 'smk_sbg_metabox_scripts', plugins_url('assets/metabox.js', __FILE__) );
	}
	

	/*
	----------------------------------------------------------------------
	adds the meta box for every post type in $page
	----------------------------------------------------------------------
	*/
	function add_box() {
		foreach ( $this->page as $page ) {
			if ( $this->are_meta_boxes_visible( $page ) )  {
				add_meta_box( 'smk_sbg_metabox', __('Select sidebar', 'smk_sbg'), array( $this, 'meta_box_callback' ), $page, 'side', 'default' );
			}
		}
	}
    
    /**
     * Should we display metaboxes for page
     */   
	function are_meta_boxes_visible( $page ) {
		if ( 'page' == $page ) {
			if ( 'post.php' == $GLOBALS['pagenow'] ) {  //admin edit post page            
				$edited_page_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : 0;
				if ( ! $this->has_page_template_meta_boxes( get_post_meta( $edited_page_id, '_wp_page_template', TRUE ) ) ) {
					return false;
				}
			}
		}
		return true;                
    }
                
    /**
     * Check if to display elements for selecting sidebar for post
     */             
	function has_page_template_meta_boxes($page_template_name) {
		$result = false;
		foreach ($this->align as $key => $value) {
			if (! is_array($value) ) {
				continue;
			}
			
			if ( ! in_array( $page_template_name, $value ) ) {
				continue;
			}
			
			if ($key != 'no') {
				$result = true;
			}
		}
		return $result;
	}        	    			

	/*
	----------------------------------------------------------------------
	outputs the meta box
	----------------------------------------------------------------------
	*/
	function meta_box_callback() {
		// Use nonce for verification
		wp_nonce_field( 'smk_sbg_metabox_nonce_action', 'smk_sbg_metabox_nonce_field' );
		global $post;

		echo '<div class="smk_sbg_visual_align smk_sbg_metabox_row">';
			/* Align
			---------------------------------------*/
			foreach ( $this->page as $page ) {

				echo '<div><strong>';
				_e( 'Sidebar position', 'smk_sbg' );
				echo '</strong></div>';

				$alignment = get_post_meta( $post->ID, 'smk_sbg_align', true );
				$alignment = ( isset($alignment) && ('no' || 'left' || 'right' || 'left-right') == $alignment ) ? $alignment : 'no';
				if( 'page' != $page ){
					
					if( is_array( $this->align ) ){
						
						foreach ($this->align as $key => $value) {
							if( $value == $alignment ){
								$active = ' smk_sbg_active';
							}
							else{
								$active = '';
							}
							echo '<img src="'. SMK_SBG_URI .'assets/align/'. $value . '.png" data-align="'. $value .'" class="smk_sbg_img_align'. $active .'" />';
						}
						
					}
					
				}
				else{
					if( is_array( $this->align ) ){
						
						foreach ($this->align as $key => $value) {
							if( $key == $alignment ){
								$active = ' smk_sbg_active';
							}
							else{
								$active = '';
							}
							if( is_array($value) ){
								echo '<img src="'. SMK_SBG_URI .'assets/align/'. $key . '.png" data-align="'. $key .'" data-templates="'. implode(", ", $value) .'" class="smk_sbg_img_align'. $active .'" />';
							}
							else{
								echo '<img src="'. SMK_SBG_URI .'assets/align/'. $key . '.png" data-align="'. $key .'" class="smk_sbg_img_align'. $active .'" />';
							}
						}

					}
				}
			}

			// The actual fields for data entry
			// Use get_post_meta to retrieve an existing value from the database and use the value for the form
			echo '<input type="hidden" id="smk_sbg_align" name="smk_sbg_align" value="'.esc_attr( $alignment ).'" />';

		echo '</div>';

		/* Define class .smk_sbg_hidden
		----------------------------------------------*/
		if( 'no' == $alignment ){
			$hidden_s1 = ' smk_sbg_hidden';
			$hidden_s2 = ' smk_sbg_hidden';
		}
		elseif( 'left' == $alignment || 'right' == $alignment ){
			$hidden_s1 = '';
			$hidden_s2 = ' smk_sbg_hidden';
		}
		elseif( 'left-right' == $alignment ){
			$hidden_s1 = '';
			$hidden_s2 = '';
		}


		/* Get all sidebars
		----------------------------------------------*/
		$the_sidebars = SMK_Sidebar_Generator::get_all_sidebars();
		if( is_array($the_sidebars) ){
			$select_str = __('-- Select a sidebar --', 'smk_sbg');
			$the_sidebars = array_merge( array( '' => $select_str ), $the_sidebars );
		}

		/* Create select menus
		----------------------------------------------*/
		$sbg_menus = array(
				'smk_sbg_1' => __('Sidebar 1', 'smk_sbg'),
				'smk_sbg_2' => __('Sidebar 2', 'smk_sbg'),
			);

		$counter = 1;
		foreach ($sbg_menus as $s_id => $s_str) {
			
			//Get saved data
			$meta = get_post_meta( $post->ID, $s_id, true );
			$meta = ( isset($meta) ) ? $meta : '';

			//Hide unused field
			if( 1 == $counter ){
				$hide = $hidden_s1;
			}
			else{
				$hide = $hidden_s2;
			}

			//Display The field
			if( is_array($the_sidebars) ){
				echo '<div id="smk_sbg_sidebar_select_'. $counter .'" class="smk_sbg_metabox_row'. $hide .'">';
					echo '<div><strong>'. $s_str .'</strong></div>'. self::select($s_id, $s_id, $meta, $the_sidebars);
				echo '</div>';
			} 
	 		
	 		$counter++;
		}

	}


	/*
	----------------------------------------------------------------------
	Select field
	----------------------------------------------------------------------
	*/
	public static function select($id, $name, $value, $options = array()) {

		$output = '';

		if(is_array($options)) {
			$output .= '<select class="widefat" name="'.esc_attr($name).'" id="'.esc_attr($id).'">';

			foreach ($options as $key => $val) 
			{
				$the_value = $val;

				//if ( ! is_numeric($key) )
					$the_value = $key;

				$output .= '<option id="'.esc_attr($id).'_' . $key . '_smk_select" value="'.$the_value.'" ' . selected($value, $the_value, false) . ' >'. esc_html($val) .'</option>';	 
			 }

			$output .= '</select>';
		}
		
		
		return $output;

	}
	

	/*
	----------------------------------------------------------------------
	saves the captured data
	----------------------------------------------------------------------
	*/
	function save_box( $post_id ) {
		$post_type = get_post_type();
		
		// verify nonce
		if ( ! isset( $_POST['smk_sbg_metabox_nonce_field'] ) )
			return $post_id;
		if ( ! ( in_array( $post_type, $this->page ) || wp_verify_nonce( $_POST['smk_sbg_metabox_nonce_field'],  'smk_sbg_metabox_nonce_action' ) ) ) 
			return $post_id;

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		
		$sbg_data = array(
				'smk_sbg_align',
				'smk_sbg_1',
				'smk_sbg_2',
			);

		// loop through fields and save the data
		foreach ( $sbg_data as $field ) 
		{
		
			$new = false;
			$old = get_post_meta( $post_id, $field, true );

			if ( isset( $_POST[$field] ) )
				$new = $_POST[$field];

			if ( isset( $new ) && '' == $new && $old ) 
			{
				delete_post_meta( $post_id, $field, $old );
			}
			elseif ( isset( $new ) && $new != $old ) 
			{
				update_post_meta( $post_id, $field, $new );
			}


		} // end foreach

	}//End function save_box()
	
}//End class SMK_Sidebar_Metabox()
}//End "class_exists" check
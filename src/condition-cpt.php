<?php
class Smk_Sidebar_Generator_Condition_Cpt extends Smk_Sidebar_Generator_Condition{
	public $type = 'post_type';

	public function __construct(){
		$this->name = __('Post types', 'smk-sidebar-generator');
	}

	// key => value
	public function prepareMainData(){
		$pt_args = array(
			'public'   => true,
			'_builtin' => false
		);
		$pt = array(
			'post' => _x('Posts', 'Post type name', 'smk-sidebar-generator'),
			'page' => _x('Pages', 'Post type name', 'smk-sidebar-generator'),
		);
		$post_types = get_post_types( $pt_args, 'objects' );
		if( !empty($post_types) ){
			foreach ($post_types as $post_type) {
				$pt[ $post_type->name ] = $post_type->label;
			}
		}
		return $pt;
	}

	// key => value
	public function prepareSecondaryData( $main_value ){
		$the_type  = $this->selected( $main_value );
		$all_posts = array();

		if( 'post' == $the_type ){
			$all_posts['all_single'] = ' - '. __('All single', 'smk-sidebar-generator') .' - ';
		}
		elseif( 'page' == $the_type ){
			$all_posts['all_pages'] = ' - '. __('All pages', 'smk-sidebar-generator') .' - ';
		}
		else{
			$all_posts['all_archives_single'] = ' - '. __('Any(archives or single)', 'smk-sidebar-generator') .' - ';
			$all_posts['all_single'] = ' - '. __('All single', 'smk-sidebar-generator') .' - ';
		}

		if( !empty($the_type) ){
			$posts = get_posts(array(
				'post_type'        => $the_type,
				'post_status'      => 'publish',
				'posts_per_page'   => -1,
			));

			foreach ( $posts as $post ) {
				setup_postdata( $post );
				$id = $post->ID;
				$all_posts[ $id ] = $post->post_title;
			}
			// wp_reset_postdata();
		}
		return $all_posts;
	}

	/**
	 * Check if can be replaced
	 *
	 * Check if the current condition settings meets the criteria and can replace a sidebar. Rturn true if is allowed to replace the sidebar.
	 *
	 * @param string $first_selection The first selection is the second string from the explode type::this_selection. "this_selection" is the post type
	 * @param array $second_selection = equalto !!! IT is an ARRAY or empty array.
	 * @return bool True if can replace
	 */
	public function canReplace( $first_selection, $second_selection ){
		$can = false;
		
		// BLOG. "post"
		if( 'post' == $first_selection ){
			if( empty($second_selection) ){
				if( is_home() || is_archive() || is_singular( 'post' ) ){
					$can = true;
				}
			}
			else{
				if( in_array('all_single', (array) $second_selection) && is_singular( 'post' ) ){
					$can = true;
				}
				elseif( is_single( $second_selection ) ){
					$can = true;
				}
			}
		}

		// PAGES. "page"
		elseif( 'page' == $first_selection ){
			if( ( empty($second_selection) || in_array('all_pages', (array) $second_selection) ) && is_page() ){
				$can = true;
			}
			elseif( is_page( $second_selection ) ){
				$can = true;
			}
		}

		// Custom Post Type
		else{
			if( empty($second_selection) ){
				if( is_singular( $first_selection ) || is_post_type_archive( $first_selection )  ){
					$can = true;
				}
			}
			elseif( 'all_archives_single' && ( is_singular( $first_selection ) || is_post_type_archive( $first_selection ) ) ){
				$can = true;
			}
			elseif( 'all_archives' && is_post_type_archive( $first_selection ) ){
				$can = true;
			}
			elseif( is_single( $second_selection ) ){
				$can = true;
			}
		}

		return $can;
	}

}
<?php
/* 
 * Plugin Name: SMK Sidebar Generator
 * Plugin URI:  https://github.com/Smartik89/Wordpress-Sidebar-Generator
 * Description: Generate an unlimited number of sidebars and assign them to any page using the conditional options without touching a single line of code. 
 * Author:      Smartik
 * Version:     3.0-beta
 * Author URI:  http://smartik.ws/
 * Licence:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * -------------------------------------------------------------------------------------
 * @Author: Smartik
 * @Author URI: http://smartik.ws/
 * @Copyright: (c) 2014 Smartik. All rights reserved
 * -------------------------------------------------------------------------------------
 *
 * @Date:   2014-07-08 00:49:24
 * @Last Modified by:   Smartik
 * @Last Modified time: 2014-07-08 14:14:43
 *
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') ) 
	die();

/**
 * Plugin version
 *
 * Get the current plugin version.
 * 
 * @return string 
 */
function smk_sidebar_version(){
	if( is_admin() ){
		$data = get_file_data( __FILE__, array( 'Version' ) );
		return empty( $data ) ? '' : $data[0];
	}
	else{
		return false;
	}
}

$path = plugin_dir_path( __FILE__ );

require_once $path . 'html.php';
require_once $path . 'abstract.php';
require_once $path . 'render.php';

$smk_sidebar_generator = new Smk_Sidebar_Generator;
$smk_sidebar_generator->init();
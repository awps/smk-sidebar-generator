<?php
/*
Theme functions will be here.
*/

/*
----------------------------------------------------------------------
Create metaboxes
----------------------------------------------------------------------
*/
if( class_exists('SMK_Sidebar_Metabox') ) {
	
	new SMK_Sidebar_Metabox( 'post', 
		array('no', 'left', 'right', 'left-right')
	);

	new SMK_Sidebar_Metabox( 'portfolio',
		array('no', 'left', 'right')
	);

	new SMK_Sidebar_Metabox( 'page',
		array(
			'no' => array(
					'default',
					'template-blog.php',
					'template-simple.php',
				),
			'left' => array(
					'default',
					'template-blog.php',
				),
			'right' => array(
					'default',
					'template-blog.php',
					'template-simple.php',
				),
			'left-right' => array(
					'default',
					'template-blog.php',
				),
		)
	);

}
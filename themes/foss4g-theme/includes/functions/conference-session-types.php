<?php
/**************************************************/
/* Regular Session Submissions
/**************************************************/

// set up session custom post types
function create_session_post_type() {
	register_post_type( 'session',
		array(
			'labels' => array(
				'name' => __( 'Sessions' ),
				'singular_name' => __( 'Session' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 12,
		'supports' => array( 'title', 'editor', 'author', 'custom-fields')
		)
	);
}



/**************************************************/
/* Workshop Submissions
/**************************************************/

// workshop custom post types
function create_workshop_post_type() {
	register_post_type( 'workshop',
		array(
			'labels' => array(
				'name' => __( 'Workshops' ),
				'singular_name' => __( 'Workshop' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 13,
		'supports' => array( 'title', 'editor','author', 'custom-fields')
		)
	);
}
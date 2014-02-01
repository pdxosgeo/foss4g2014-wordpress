<?php

// registers the sponsor post type
add_action( 'init', 'create_sponsor_post_type' );
function create_sponsor_post_type() {
	register_post_type( 'sponsor',
		array(
			'labels' => array(
				'name' => __( 'Sponsors' ),
				'singular_name' => __( 'Sponsor' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 10,
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies' => array('level')
		)
	);

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Levels', 'taxonomy general name' ),
		'singular_name'     => _x( 'Level', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Levels' ),
		'all_items'         => __( 'All Sponsor Levels' ),
		'parent_item'       => __( 'Parent Level' ),
		'parent_item_colon' => __( 'Parent Level:' ),
		'edit_item'         => __( 'Edit Level' ),
		'update_item'       => __( 'Update Level' ),
		'add_new_item'      => __( 'Add New Level' ),
		'new_item_name'     => __( 'New Level Name' ),
		'menu_name'         => __( 'Levels' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'level' ),
	);

	register_taxonomy( 'level', array( 'sponsor' ), $args );	
}

// registers the regular session talks - no taxonomy
add_action( 'init', 'create_session_post_type' );
function create_session_post_type() {
	register_post_type( 'session',
		array(
			'labels' => array(
				'name' => __( 'Sessions' ),
				'singular_name' => __( 'Session' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 20,
		'supports' => array( 'title', 'editor', 'custom-fields')
		)
	);
}

// registers the workshops - no taxonomy
add_action( 'init', 'create_workshop_post_type' );
function create_session_post_type() {
	register_post_type( 'workshop',
		array(
			'labels' => array(
				'name' => __( 'Workshops' ),
				'singular_name' => __( 'Workshop' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 30,
		'supports' => array( 'title', 'editor', 'custom-fields')
		)
	);
}



//redirect after post submission
function custom_redirect( $url ) {
    global $post;
    return get_permalink( $post->ID=29 ); // needs to be id of specific page (presumably a thankyou page)
}
add_filter( 'wpuf_after_post_redirect', 'custom_redirect' );

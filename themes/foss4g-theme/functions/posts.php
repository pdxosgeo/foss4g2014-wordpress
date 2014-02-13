<?php

/**************************************************/
/* Sponsors
/**************************************************/

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
		'taxonomies' => array('level'),
		'register_meta_box_cb' => 'add_sponsor_meta'
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

// add meta box to sponsors post type for sponsor URL
function add_sponsor_meta() {
    add_meta_box('sponsor_url', 'Sponsor URL (http://...)', 'sponsor_url', 'sponsor', 'normal', 'high');
}
// The Event Location Metabox
function sponsor_url() {
	global $post;
	echo '<input type="hidden" name="sponsormeta_noncename" id="sponsormeta_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$URL = get_post_meta($post->ID, '_URL', true);
	echo '<input type="text" name="_URL" value="' . $URL  . '" class="widefat" />';
}
// Save the Metabox Data
function save_sponsor_url($post_id, $post) {
	if ( !wp_verify_nonce( $_POST['sponsormeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	$sponsor_url['_URL'] = $_POST['_URL'];
	foreach ($sponsor_url as $key => $value) {
		if( $post->post_type == 'revision' ) return; 
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}
add_action('save_post', 'save_sponsor_url', 1, 2);



/**************************************************/
/* Regular Session Submissions
/**************************************************/

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
		'menu_position' => 11,
		'supports' => array( 'title', 'editor', 'custom-fields')
		)
	);
}



/**************************************************/
/* Workshop Submissions
/**************************************************/

// registers the workshops - no taxonomy
add_action( 'init', 'create_workshop_post_type' );
function create_workshop_post_type() {
	register_post_type( 'workshop',
		array(
			'labels' => array(
				'name' => __( 'Workshops' ),
				'singular_name' => __( 'Workshop' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 12,
		'supports' => array( 'title', 'editor', 'custom-fields')
		)
	);
}



/**************************************************/
/* Redirect after posting new workshop or session
/**************************************************/

//redirect after post submission
function custom_redirect( $url ) {
    global $post;
    return get_permalink( $post->ID=184 ); // needs to be id of specific page (presumably a thankyou page)
}
add_filter( 'wpuf_after_post_redirect', 'custom_redirect' );
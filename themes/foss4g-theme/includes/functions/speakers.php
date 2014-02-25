<?php
/**************************************************/
/* Speakers Custom Post Type
/**************************************************/


function create_speaker_post_type() {
	register_post_type( 'speaker',
		array(
			'labels' => array(
				'name' => __( 'Speakers' ),
				'singular_name' => __( 'Speaker' )
			),
		'public' => true,
		'has_archive' => true,
		'menu_position' => 10,
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'taxonomies' => array('group')
		)
	);

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Groups', 'taxonomy general name' ),
		'singular_name'     => _x( 'Group', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Speaker Groups' ),
		'all_items'         => __( 'All Speaker Groups' ),
		'parent_item'       => __( 'Parent Group' ),
		'parent_item_colon' => __( 'Parent Group:' ),
		'edit_item'         => __( 'Edit Group' ),
		'update_item'       => __( 'Update Group' ),
		'add_new_item'      => __( 'Add New Group' ),
		'new_item_name'     => __( 'New Group Name' ),
		'menu_name'         => __( 'Speaker Groups' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'group' ),
	);

	register_taxonomy( 'group', array( 'speaker' ), $args );	
}
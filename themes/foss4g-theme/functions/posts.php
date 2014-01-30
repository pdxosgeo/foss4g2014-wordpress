<?php

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
		'supports' => array( 'title', 'editor', 'thumbnail' ),
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

	register_taxonomy( 'level', array( 'book' ), $args );	
}
<?php
// registers the travel_grant  post type
add_action( 'init', 'create_travel_grant_post_type' );
function create_travel_grant_post_type() {
  register_post_type( 'travel_grant',
    array(
      'labels' => array(
        'name' => __( 'Grant Requests' ),
        'singular_name' => __( 'Grant Request' )
      ),
    'public' => true,
    'has_archive' => true,
    'menu_position' => 12,
    'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
    )
  );
}

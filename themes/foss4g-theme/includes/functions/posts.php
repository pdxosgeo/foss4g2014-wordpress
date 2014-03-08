<?php
/* General Post Functions */

// register all custom post types
add_action( 'init', 'create_speaker_post_type' );
add_action( 'init', 'create_sponsor_post_type' );
add_action( 'init', 'create_session_post_type' );
add_action( 'init', 'create_workshop_post_type' );

// redirect after post submission
function custom_redirect( $url ) {
    global $post;
    return get_permalink( $post->ID=184 ); // needs to be id of specific page (presumably a thankyou page)
}
add_filter( 'wpuf_after_post_redirect', 'custom_redirect' );
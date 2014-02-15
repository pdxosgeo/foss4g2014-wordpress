<?php

add_theme_support( 'menus' );

add_action( 'widgets_init', 'foss4g2014_widgets_init' );

register_nav_menus( array(
    'primary' => __( 'Primary', 'foss4g' ),
) );

function custom_excerpt_length( $length ) {
	return 150;
}

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

add_theme_support( 'post-thumbnails' );

add_post_type_support('page', 'excerpt');
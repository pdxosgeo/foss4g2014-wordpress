<?php

function foss4g_queue() {
	wp_enqueue_script( 'site-js',  get_template_directory_uri() . '/js/main.js', array('bootstrap-js', 'jquery') );
	wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/css/bootstrap.css' );
	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery') );
	wp_enqueue_style( 'site-css',  get_stylesheet_uri() );
	
}
add_action( 'wp_enqueue_scripts', 'foss4g_queue' );
add_action( 'widgets_init', 'foss4g2014_widgets_init' );

register_nav_menus( array(
    'primary' => __( 'Primary', 'foss4g' ),
) );

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
add_filter('show_admin_bar', '__return_false');

add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );

add_post_type_support('page', 'excerpt');

function session_type($time) {
	$s = ($time<12 ? 'Morning' : 'Afternoon');
	return $s;
}
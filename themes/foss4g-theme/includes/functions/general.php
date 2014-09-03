<?php

function foss4g_queue() {
  wp_enqueue_script( 'site-js',  get_template_directory_uri() . '/js/main.js', array('bootstrap-js', 'jquery', 'tabletop-js') );
  wp_enqueue_style( 'bootstrap-css', get_template_directory_uri() . '/css/bootstrap.css' );
  wp_enqueue_script( 'tabletop-js', get_template_directory_uri() . '/js/tabletop.js' );
  wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery') );
  wp_enqueue_style( 'site-css',  get_stylesheet_uri() );
  if(is_home()) {
    wp_enqueue_script( 'home-map-js', get_template_directory_uri() . '/js/home-map.js', array('jquery') );
  }
  if(is_page_template('template-schedule.php')) {
    wp_enqueue_script( 'schedule-js', get_template_directory_uri() . '/js/schedule.js', array('jquery') );
  }
  if(is_page_template('template-mapgallery.php')) {
    wp_enqueue_style( 'blueimp-gallery-css', get_template_directory_uri() . '/css/blueimp-gallery.min.css' );
    wp_enqueue_style( 'bootstrap-image-gallery-css', get_template_directory_uri() . '/css/bootstrap-image-gallery.min.css' );
    wp_enqueue_style( 'mapgallery-css', get_template_directory_uri() . '/css/mapgallery.css' );    
    wp_enqueue_script( 'jquery-blueimp-gallery-js', get_template_directory_uri() . '/js/jquery.blueimp-gallery.min.js', array('jquery') );        
    wp_enqueue_script( 'bootstrap-image-gallery-js', get_template_directory_uri() . '/js/bootstrap-image-gallery.js', array('jquery', 'jquery-blueimp-gallery-js') );    
    wp_enqueue_script( 'isotope-js', get_template_directory_uri() . '/js/jquery.isotope.min.js', array('jquery') );        
    wp_enqueue_script( 'jquery-cookie-js', get_template_directory_uri() . '/js/jquery.cookie.js', array('jquery') );
  }
}
add_action( 'wp_enqueue_scripts', 'foss4g_queue' );
add_action( 'widgets_init', 'foss4g2014_widgets_init' );

register_nav_menus( array(
    'primary' => __( 'Primary', 'foss4g' ),
) );

// add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
add_filter('show_admin_bar', '__return_false');

add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );

add_post_type_support('page', 'excerpt');

function session_type($time) {
  $s = ($time<12 ? 'Morning' : 'Afternoon');
  return $s;
}
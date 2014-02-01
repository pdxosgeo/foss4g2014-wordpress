<?php

/* General Sidebar Widget */
function foss4g2014_widgets_init() {
	register_sidebar( array (
		'name' => __('Sidebar Widget Area', 'foss4g'),
		'id' => 'primary-widget-area',
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => "</li>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		) );
}


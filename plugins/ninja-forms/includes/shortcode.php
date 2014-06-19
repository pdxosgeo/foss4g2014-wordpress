<?php

function ninja_forms_shortcode( $atts ){
	$form = ninja_forms_return_echo( 'ninja_forms_display_form', $atts['id'] );
	return $form;
}
add_shortcode( 'ninja_forms_display_form', 'ninja_forms_shortcode' );

function ninja_forms_field_shortcode( $atts ){
	global $ninja_forms_processing;
	$field_id = $atts['id'];
	if ( is_object ( $ninja_forms_processing ) ) {
		$value = $ninja_forms_processing->get_field_value( $field_id );
		$value = apply_filters( 'ninja_forms_field_shortcode', $value, $atts );
		if( is_array( $value ) ){
			$value = implode( ',', $value );
		}
	} else {
		$value = '';
	}
	return $value;
}
add_shortcode( 'ninja_forms_field', 'ninja_forms_field_shortcode' );

function ninja_forms_sub_date_shortcode( $atts ){
	global $ninja_forms_processing;
	if( isset( $atts['format'] ) ){
		$date_format = $atts['format'];
	}else{
		$date_format = 'm/d/Y';
	}
	$date = date( $date_format );
	return $date;
}
add_shortcode( 'ninja_forms_sub_date', 'ninja_forms_sub_date_shortcode' );


function ninja_forms_pre_process_shortcode($content) {
   global $shortcode_tags;
   // Remove our previously registered shortcode.
   //remove_shortcode( 'ninja_forms_display_form' );
   // Backup current registered shortcodes and clear them all out
   $current_shortcodes = $shortcode_tags;
   $shortcode_tags = array();
   add_shortcode( 'ninja_forms_display_form', 'ninja_forms_shortcode' );
   // Do the shortcode (only the one above is registered)
   $content = do_shortcode($content);
   // Put the original shortcodes back
   $shortcode_tags = $current_shortcodes;
   return $content;
}
add_filter('the_content', 'ninja_forms_pre_process_shortcode', 9999);
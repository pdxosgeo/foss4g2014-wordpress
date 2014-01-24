<?php

add_action( 'init', 'ninja_forms_register_display_form_visibility', 99 );
function ninja_forms_register_display_form_visibility(){
	add_filter( 'ninja_forms_display_form_visibility', 'ninja_forms_display_form_visibility', 10, 2 );
}

function ninja_forms_display_form_visibility( $display, $form_id ){
	global $ninja_forms_processing;

	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];

	if( is_object( $ninja_forms_processing ) ){
		$hide_complete = $ninja_forms_processing->get_form_setting( 'hide_complete' );
	}else{
		if( isset( $form_data['hide_complete'] ) ){
			$hide_complete = $form_data['hide_complete'];
		}else{
			$hide_complete = 0;
		}
	}

	//If the plugin setting 'hide complete' has been set and a success message exists, hide the form.
	if( $hide_complete == 1 AND ( is_object( $ninja_forms_processing ) AND $ninja_forms_processing->get_form_setting( 'processing_complete' ) == 1 ) AND $ninja_forms_processing->get_form_ID() == $form_id ){
		$display = 0;
	}

	return $display;
}
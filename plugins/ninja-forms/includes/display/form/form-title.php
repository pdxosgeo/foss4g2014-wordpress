<?php
/**
 * Outputs the HTML of the form title.
 * The form title can be filtered with 'ninja_forms_form_title'.
**/
add_action( 'init', 'ninja_forms_register_display_form_title' );
function ninja_forms_register_display_form_title(){
	add_action( 'ninja_forms_display_form_title', 'ninja_forms_display_form_title', 10 );
}

function ninja_forms_display_form_title( $form_id ){
	$form_row = ninja_forms_get_form_by_id( $form_id );
	$form_data = $form_row['data'];

	if( isset( $form_data['show_title'] ) ){
		$show_title = $form_data['show_title'];
	}else{
		$show_title = 0;
	}

	if( isset( $form_data['form_title'] ) ){
		$form_title = $form_data['form_title'];
	}else{
		$form_title = '';
	}

	$title_class = 'ninja-forms-form-title';

	$title_class = apply_filters( 'ninja_forms_form_title_class', $title_class, $form_id );

	$form_title = '<h2 class="' . $title_class . '">'.$form_title.'</h2>';

	$form_title = apply_filters( 'ninja_forms_form_title', $form_title, $form_id );
	if($show_title == 1){
		echo $form_title;
	}
}
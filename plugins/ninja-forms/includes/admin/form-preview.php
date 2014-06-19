<?php

add_action( 'init', 'ninja_forms_preview_form' );
function ninja_forms_preview_form() {
	global $ninja_forms_append_page_form_id;
	if( isset( $_REQUEST['form_id'] ) AND isset($_REQUEST['preview']) ) { //I
		$form_id = absint( $_REQUEST['form_id'] );
	} else {
		$form_id = '';
	}

	$form_data = ninja_forms_get_form_by_id( $form_id );

	//if( '' != $form_data['data'] ) {
	if(isset($form_data['data']) AND !empty($form_data['data'])){ // In order to prevent notices and errors, it's best to use these two checks when you are evaluating arrays.
		$ninja_forms_append_page_form_id = array($form_id);
		add_filter( 'the_content', 'ninja_forms_append_to_page', 9999 );
	}
}



function ninja_forms_preview_link( $form_id = '', $echo = true ) {
	if( $form_id == '' ){
		if( isset( $_REQUEST['form_id'] ) ){
			$form_id = absint( $_REQUEST['form_id'] );
		}else{
			$form_id = '';
		}
	}
	$base = home_url();

	$form_data = ninja_forms_get_form_by_id( $form_id );

	//if( '' == $form_data['data']['append_page'] ) {
	if(!isset($form_data['data']['append_page']) OR empty($form_data['data']['append_page'])){ // See the comment above about this check. !empty will ensure that it's not either empty quotes or null.
		$opt =  nf_get_settings();
		if ( isset ( $opt['preview_id'] ) ) {
			$page_id = $opt['preview_id'];
		} else {
			$page_id = '';
		}
	} else {
		$page_id = $form_data['data']['append_page'];
	}

	if( $echo ){
		$preview_link = '<a target="_blank" href="' . $base . '/?page_id=' . $page_id . '&preview=true&form_id=' . $form_id . '">' . __( 'Preview Form', 'ninja-forms' ) . '</a>';
	}else{
		$preview_link = $base . '/?page_id=' . $page_id . '&preview=true&form_id=' . $form_id;
	}

	return $preview_link;

}
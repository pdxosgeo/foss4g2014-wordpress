<?php
function ninja_forms_setup_processing_class( $form_id = '' ){
	global $ninja_forms_processing;
	//Set the form id
	if ( $form_id == '' ) {
		if ( isset ( $_REQUEST['_form_id'] ) ) {
			$form_id = absint( $_REQUEST['_form_id'] );
		} else if ( isset ( $_SESSION['ninja_forms_transient_id'] ) ) {
			$cache = get_transient( $_SESSION['ninja_forms_transient_id'] );
			if ( $cache !== false ) {
				$form_id = $cache['form_id'];
			}
		}
	}

	//Initiate our processing class with our designated global variable.
	$ninja_forms_processing = new Ninja_Forms_Processing($form_id);
	$ninja_forms_processing->setup_submitted_vars();
}

function ninja_forms_pre_process(){
	global $ninja_forms_processing;

	$ajax = $ninja_forms_processing->get_form_setting('ajax');
	$form_id = $ninja_forms_processing->get_form_ID();

	do_action('ninja_forms_before_pre_process');

	if(!$ninja_forms_processing->get_all_errors()){
		do_action('ninja_forms_pre_process');
	}

	if(!$ninja_forms_processing->get_all_errors()){
		ninja_forms_process();
	}else{
		if($ajax == 1){
			$json = ninja_forms_json_response();
			//header('Content-Type', 'application/json');
			echo $json;
			die();
		}else{
			//echo 'pre-processing';
			//print_r($ninja_forms_processing->get_all_errors());
		}
	}
}
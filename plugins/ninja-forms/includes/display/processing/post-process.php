<?php
function ninja_forms_post_process(){
	global $wpdb, $ninja_forms_processing;

	$ajax = $ninja_forms_processing->get_form_setting('ajax');
	$form_id = $ninja_forms_processing->get_form_ID();

	if(!$ninja_forms_processing->get_all_errors()){

		do_action('ninja_forms_post_process');

		if( !$ninja_forms_processing->get_all_errors() ){

			$ninja_forms_processing->update_form_setting( 'processing_complete', 1 );
			$success = $ninja_forms_processing->get_form_setting( 'success_msg' );

			$ninja_forms_processing->add_success_msg( 'success_msg', $success );

			$json = ninja_forms_json_response();
			
			if($ajax == 1){
				//header('Content-Type', 'application/json');
				echo $json;
				die();
			}else{

				if( $ninja_forms_processing->get_form_setting( 'landing_page' ) != '' ){
					ninja_forms_set_transient();
					header( 'Location: '.$ninja_forms_processing->get_form_setting( 'landing_page' ) );
					die();
				}
			}
		}else{
			if($ajax == 1){
				//header('Content-Type', 'application/json');
				echo $json;
				die();
			}else{
				//echo 'post-processing';
				//print_r($ninja_forms_processing->get_all_errors());
			}
		}
	}else{
		if($ajax == 1){
			//header('Content-Type', 'application/json');
			echo $json;
			die();
		}else{
			//echo 'post-processing';
			//print_r($ninja_forms_processing->get_all_errors());
		}
	}
}
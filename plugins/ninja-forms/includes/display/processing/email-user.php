<?php
add_action('init', 'ninja_forms_register_email_user');
function ninja_forms_register_email_user(){
	add_action('ninja_forms_post_process', 'ninja_forms_email_user', 999);
}

function ninja_forms_email_user(){
	global $ninja_forms_processing;

	do_action( 'ninja_forms_email_user' );

	$form_ID = $ninja_forms_processing->get_form_ID();
	$form_title = $ninja_forms_processing->get_form_setting('form_title');
	$user_mailto = array();
	$all_fields = $ninja_forms_processing->get_all_fields();
	if(is_array($all_fields) AND !empty($all_fields)){
		foreach($all_fields as $field_id => $user_value){
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );

			if(isset($field_row['data']['send_email'])){
				$send_email = $field_row['data']['send_email'];
			}else{
				$send_email = 0;
			}

			if($send_email){
				array_push($user_mailto, $user_value);
			}
		}
	}

	$email_from 		= $ninja_forms_processing->get_form_setting('email_from');
	$email_from_name 	= $ninja_forms_processing->get_form_setting( 'email_from_name' );
	$email_type 		= $ninja_forms_processing->get_form_setting('email_type');
	$subject 			= $ninja_forms_processing->get_form_setting('user_subject');
	$message 			= $ninja_forms_processing->get_form_setting('user_email_msg');
	$default_email 		= get_option( 'admin_email' );

	if(!$subject){
		$subject = $form_title;
	}
	if(!$message){
		$message = __('Thank you for filling out this form.', 'ninja-forms');
	}
	if(!$email_from){
		$email_from = $default_email;
	}
	if(!$email_type){
		$email_type = '';
	}

	if( $email_type !== 'plain' ){
		$message = wpautop( $message );
	}

	$email_from = $email_from_name.' <'.$email_from.'>';

	$email_from = htmlspecialchars_decode($email_from);
	$email_from = htmlspecialchars_decode($email_from);

	$headers = array();
	$headers[] = 'From: '.$email_from;
	$headers[] = 'Content-Type: text/'.$email_type;
	$headers[] = 'charset=utf-8';

	if($ninja_forms_processing->get_form_setting('user_attachments')){
		$attachments = $ninja_forms_processing->get_form_setting('user_attachments');
	}else{
		$attachments = '';
	}

	if(is_array($user_mailto) AND !empty($user_mailto)){
		wp_mail($user_mailto, $subject, $message, $headers, $attachments);
	}
}
<?php
add_action( 'init', 'ninja_forms_register_email_admin' );
function ninja_forms_register_email_admin() {
	add_action( 'ninja_forms_post_process', 'ninja_forms_email_admin', 999 );
}

function ninja_forms_email_admin() {
	global $ninja_forms_processing;

	do_action( 'ninja_forms_email_admin' );

	$form_ID 			= $ninja_forms_processing->get_form_ID();
	$form_title 		= $ninja_forms_processing->get_form_setting( 'form_title' );
	$admin_mailto 		= $ninja_forms_processing->get_form_setting( 'admin_mailto' );
	$email_from_name 	= $ninja_forms_processing->get_form_setting( 'email_from_name' );
	$email_from 		= $ninja_forms_processing->get_form_setting( 'email_from' );
	$email_type 		= $ninja_forms_processing->get_form_setting( 'email_type' );
	$subject 			= $ninja_forms_processing->get_form_setting( 'admin_subject' );
	$message 			= $ninja_forms_processing->get_form_setting( 'admin_email_msg' );
	$email_reply 		= $ninja_forms_processing->get_form_setting( 'admin_email_replyto' );

	if ( $ninja_forms_processing->get_form_setting( 'admin_email_name' ) ){
		$email_from_name = $ninja_forms_processing->get_form_setting( 'admin_email_name' );
	}

	if ( $email_from_name AND $email_reply ) {
		$email_reply = $email_from_name . ' <' . $email_reply . '>';
	}

	if ( !$subject ){
		$subject = $form_title;
	}
	if ( !$message ){
		$message = '';
	}
	if ( !$email_type ){
		$email_type = '';
	}

	if ( $email_type !== 'plain' ){
		$message = wpautop( $message );
	}

	$email_from = $email_from_name.' <'.$email_from.'>';

	$email_from = apply_filters( 'ninja_forms_admin_email_from', $email_from, $email_reply, $form_ID );

	$headers = array();
	$headers[] = 'From: ' . $email_from;
	if( $email_reply ) {
		$headers[] = 'Reply-To: ' . $email_reply;
	}
	$headers[] = 'Content-Type: text/' . $email_type; 
	$headers[] = 'charset=utf-8';

	if ($ninja_forms_processing->get_form_setting( 'admin_attachments' ) ) {
		$attachments = $ninja_forms_processing->get_form_setting( 'admin_attachments' );
	} else {
		$attachments = '';
	}

	if ( is_array( $admin_mailto ) AND !empty( $admin_mailto ) ){
		foreach( $admin_mailto as $to ){
			$sent = wp_mail( $to, $subject, $message, $headers, $attachments );
		}
	}
}
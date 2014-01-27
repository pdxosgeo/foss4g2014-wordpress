<?php
add_action('init', 'ninja_forms_register_filter_email_add_fields', 15 );
function ninja_forms_register_filter_email_add_fields(){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing ) ){
		if( $ninja_forms_processing->get_form_setting( 'user_email_fields' ) == 1 ){
			add_filter( 'ninja_forms_user_email', 'ninja_forms_filter_email_add_fields' );
		}
	}

	if( is_object( $ninja_forms_processing ) ){
		if( $ninja_forms_processing->get_form_setting( 'admin_email_fields' ) == 1 ){
			add_filter( 'ninja_forms_admin_email', 'ninja_forms_filter_email_add_fields' );
		}
	}
}

function ninja_forms_filter_email_add_fields( $message ){
	global $ninja_forms_processing, $ninja_forms_fields;

	$form_id = $ninja_forms_processing->get_form_ID();
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	//$all_fields = $ninja_forms_processing->get_all_fields();
	$tmp_array = array();
	if( is_array( $all_fields ) ){
		foreach( $all_fields as $field ){
			if( $ninja_forms_processing->get_field_value( $field['id'] ) ){
				$tmp_array[$field['id']] = $ninja_forms_processing->get_field_value( $field['id'] );
			}
		}
	}
	$all_fields = apply_filters( 'ninja_forms_email_all_fields_array', $tmp_array, $form_id );

	$email_type = $ninja_forms_processing->get_form_setting( 'email_type' );
	if(is_array($all_fields) AND !empty($all_fields)){
		if($email_type == 'html'){
			$message .= "<br><br>";
			$message .= __( 'User Submitted Values:', 'ninja-forms' );
			$message .= "<table>";
		}else{
			$message = str_replace("<p>", "\r\n", $message);
			$message = str_replace("</p>", "", $message);
			$message = str_replace("<br>", "\r\n", $message);
			$message = str_replace("<br />", "\r\n", $message);
			$message = strip_tags($message);
			$message .= "\r\n \r\n";
			$message .= __('User Submitted Values:', 'ninja-forms');
			$message .= "\r\n";
		}
		foreach( $all_fields as $field_id => $user_value ){

			$field_row = $ninja_forms_processing->get_field_settings( $field_id );
			$field_label = $field_row['data']['label'];
			$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
			$user_value = apply_filters( 'ninja_forms_email_user_value', $user_value, $field_id );
			$field_type = $field_row['type'];

			if( $ninja_forms_fields[$field_type]['process_field'] ){
				if( is_array( $user_value ) AND !empty( $user_value ) ){
					$x = 0;
					foreach($user_value as $val){
						if(!is_array($val)){
							if($x > 0){
								$field_label = '----';
								$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
							}
							if($email_type == 'html'){
								$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$val."</td></tr>";
							}else{
								$message .= $field_label." - ".$val."\r\n";
							}
						}else{
							foreach($val as $v){
								if(!is_array($v)){
									if($x > 0){
										$field_label = '----';
										$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
									}
									if($email_type == 'html'){
										$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$v."</td></tr>";
									}else{
										$message .= $field_label." - ".$v."\r\n";
									}
								}else{
									foreach($v as $a){
										if($x > 0){
											$field_label = '----';
											$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
										}
										if($email_type == 'html'){
											$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$a."</td></tr>";
										}else{
											$message .= $field_label." - ".$a."\r\n";
										}
									}
								}
							}
						}
						$x++;
					}
				}else{
					if($email_type == 'html'){
						$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$user_value."</td></tr>";
					}else{
						$message .= $field_label." - ".$user_value."\r\n";
					}
				}

			}
		}
		if($email_type == 'html'){
			$message .= "</table>";
		}
	}
	$message = apply_filters( 'ninja_forms_email_field_list', $message, $form_id );

	return $message;
}
<?php
add_action('init', 'ninja_forms_register_save_sub');
function ninja_forms_register_save_sub(){
	add_action('ninja_forms_post_process', 'ninja_forms_save_sub');
}

function ninja_forms_save_sub(){
	global $ninja_forms_processing, $ninja_forms_fields;

	// save forms by default
	$save = true;

	// check if there's some legacy save settings saved in the database
	if ( 0 === $ninja_forms_processing->get_form_setting('save_subs') ) {
		$save = false;
	}
	$save = apply_filters ( 'ninja_forms_save_submission', $save, $ninja_forms_processing->get_form_ID() );

	if( $save ){

		$action = $ninja_forms_processing->get_action();
		$user_id = $ninja_forms_processing->get_user_ID();

		$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
		$form_id = $ninja_forms_processing->get_form_ID();

		$field_data = $ninja_forms_processing->get_all_fields();

		$sub_data = array();

		if(is_array($field_data) AND !empty($field_data)){
			foreach($field_data as $field_id => $user_value){
				$field_row = $ninja_forms_processing->get_field_settings($field_id);
				$field_type = $field_row['type'];
				if ( isset ( $ninja_forms_fields[$field_type]['save_sub'] ) ) {

					$save_sub = $ninja_forms_fields[$field_type]['save_sub'];

					if( $save_sub ){
						ninja_forms_remove_from_array($sub_data, "field_id", $field_id, TRUE);
						$user_value = apply_filters( 'ninja_forms_save_sub', $user_value, $field_id );
						if( is_array( $user_value ) ){
							$user_value = ninja_forms_esc_html_deep( $user_value );
						}else{
							$user_value = esc_html( $user_value );
						}
						array_push( $sub_data, array( 'field_id' => $field_id, 'user_value' => $user_value ) );
					}
				}
			}
		}

		$args = array(
			'form_id' => $form_id,
			'user_id' => $user_id,
			'action' => $action,
			'data' => serialize( $sub_data ),
			'status' => 1,
		);

		$args = apply_filters( 'ninja_forms_save_sub_args', $args );

		if($sub_id != ''){
			$args['sub_id'] = $sub_id;
			ninja_forms_update_sub($args);
			do_action( 'ninja_forms_update_sub', $sub_id );
		}else{
			$sub_id = ninja_forms_insert_sub( $args );
			$ninja_forms_processing->update_form_setting( 'sub_id', $sub_id );
			do_action( 'ninja_forms_insert_sub', $sub_id );
		}
	}
}
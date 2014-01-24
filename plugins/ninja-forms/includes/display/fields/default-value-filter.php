<?php

/*
 *
 * Function that filters default values, replacing defined strings with the approparite values.
 *
 * @since 2.2.49
 * @return $data
 */

function ninja_forms_default_value_filter( $data, $field_id ) {
	global $current_user, $ninja_forms_fields, $post;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	} else if ( isset ( $ninja_forms_fields[$field_type]['default_value'] ) ){ 
		$default_value = $ninja_forms_fields[$field_type]['default_value'];
	} else {
		$default_value = '';
	}

	get_currentuserinfo();
	$user_ID 			= $current_user->ID;
	$user_firstname 	= $current_user->user_firstname;
    $user_lastname 		= $current_user->user_lastname;
    $user_display_name 	= $current_user->display_name;
    $user_email 		= $current_user->user_email;
    if ( is_object ( $post ) ) {
	    $post_ID 			= $post->ID;
	    $post_title 		= $post->post_title;
	    $post_url			= get_permalink( $post_ID );
    } else {
    	$post_ID      		= '';
    	$post_title 		= '';
    	$post_url 			= '';
    }


    switch( $default_value ){
		case '_user_id':
			$default_value = $user_ID;
			break;
		case '_user_firstname':
			$default_value = $user_firstname;
			break;
		case '_user_lastname':
			$default_value = $user_lastname;
			break;
		case '_user_display_name':
			$default_value = $user_display_name;
			break;
		case '_user_email':
			$default_value = $user_email;
			break;
		case 'post_id':
			$default_value = $post_ID;
			break;
		case 'post_title':
			$default_value = $post_title;
			break;
		case 'post_url':
			$default_value = $post_url;
			break;
		case 'today':
			$plugin_settings = get_option( 'ninja_forms_settings' );
			if ( isset ( $plugin_settings['date_format'] ) ) {
				$date_format = $plugin_settings['date_format'];
			} else {
				$date_format = 'm/d/Y';
			}
			$default_value = date( $date_format, strtotime( 'now' ) );
			break;
	}

	$data['default_value'] = $default_value;

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_default_value_filter', 7, 2 );
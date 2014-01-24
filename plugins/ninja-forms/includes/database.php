<?php

// Begin Form Interaction Functions

function ninja_forms_insert_field( $form_id, $args = array() ){
	global $wpdb;
	$insert_array = array();

	$insert_array['type'] = $args['type'];
	$insert_array['form_id'] = $form_id;

	if( isset( $args['data'] ) ){
		$insert_array['data'] = $args['data'];
	}else{
		$insert_array['data'] = '';
	}

	if( isset( $args['order'] ) ){
		$insert_array['order'] = $args['order'];
	}else{
		$insert_array['order'] = 999;
	}

	if( isset( $args['fav_id'] ) ){
		$insert_array['fav_id'] = $args['fav_id'];
	}

	if( isset( $args['def_id'] ) ){
		$insert_array['def_id'] = $args['def_id'];
	}

	$new_field = $wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $insert_array );
	$new_id = $wpdb->insert_id;
	return $new_id;
}

function ninja_forms_get_form_by_id($form_id){
	global $wpdb;
	$form_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_TABLE_NAME." WHERE id = %d", $form_id), ARRAY_A);
	$form_row['data'] = unserialize($form_row['data']);
	$form_row['data'] = ninja_forms_stripslashes_deep($form_row['data']);
	return $form_row;
}

function ninja_forms_get_all_forms( $debug = false ){
	global $wpdb;
	if( isset( $_REQUEST['debug'] ) AND $_REQUEST['debug'] == true ){
		$debug = true;
	}
	$form_results = $wpdb->get_results("SELECT * FROM ".NINJA_FORMS_TABLE_NAME, ARRAY_A);
	if(is_array($form_results) AND !empty($form_results)){
		$x = 0;
		$count = count($form_results) - 1;
		while($x <= $count){
			if( isset( $form_results[$x]['data'] ) ){
				$form_results[$x]['data'] = unserialize($form_results[$x]['data']);
				if( substr( $form_results[$x]['data']['form_title'], 0, 1 ) == '_' ){
					if( !$debug ){
						unset( $form_results[$x] );
					}
				}
			}
			$x++;
		}
	}
	$form_results = array_values($form_results);
	return $form_results;
}

function ninja_forms_get_form_by_field_id( $field_id ){
	global $wpdb;
	$form_id = $wpdb->get_row($wpdb->prepare("SELECT form_id FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
	$form_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_TABLE_NAME." WHERE id = %d", $form_id), ARRAY_A);
	$form_row['data'] = unserialize($form_row['data']);
	return $form_row;
}

function ninja_forms_get_form_ids_by_post_id( $post_id ){
	global $wpdb;
	$form_ids = array();
	if( is_page( $post_id ) ){
		$form_results = ninja_forms_get_all_forms();
		if(is_array($form_results) AND !empty($form_results)){
			foreach($form_results as $form){
				$form_data = $form['data'];
				if(isset($form_data['append_page']) AND !empty($form_data['append_page'])){
					if($form_data['append_page'] == $post_id){
						$form_ids[] = $form['id'];
					}
				}
			}
		}
		$form_id = get_post_meta( $post_id, 'ninja_forms_form', true );
		if( !empty( $form_id ) ){
			$form_ids[] = $form_id;
		}
	}else if( is_single( $post_id ) ){
		$form_id = get_post_meta( $post_id, 'ninja_forms_form', true );
		if( !empty( $form_id ) ){
			$form_ids[] = $form_id;
		}
	}

	return $form_ids;
}

function ninja_forms_get_form_by_sub_id( $sub_id ){
	global $wpdb;
	$sub_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE id = %d", $sub_id ), ARRAY_A );
	$form_id = $sub_row['form_id'];
	$form_row = ninja_forms_get_form_by_id( $form_id );
	return $form_row;
}

// The ninja_forms_delete_form( $form_id ) function is in includes/admin/ajax.php

function ninja_forms_update_form( $args ){
	global $wpdb;
	$update_array = $args['update_array'];
	$where = $args['where'];
	$wpdb->update(NINJA_FORMS_TABLE_NAME, $update_array, $where);
}

// Begin Field Interaction Functions

function ninja_forms_get_field_by_id($field_id){
	global $wpdb;
	$field_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
	if( $field_row != null ){
		$field_row['data'] = unserialize($field_row['data']);
		return $field_row;
	}else{
		return false;
	}
}

function ninja_forms_get_fields_by_form_id($form_id, $orderby = 'ORDER BY `order` ASC'){
	global $wpdb;
	$field_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = %d ".$orderby, $form_id), ARRAY_A);
	if(is_array($field_results) AND !empty($field_results)){
		$x = 0;
		$count = count($field_results) - 1;
		while($x <= $count){
			$field_results[$x]['data'] = unserialize($field_results[$x]['data']);
			$x++;
		}
	}
	return $field_results;
}

function ninja_forms_get_all_fields(){
	global $wpdb;
	$field_results = $wpdb->get_results("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME, ARRAY_A);
	if(is_array($field_results) AND !empty($field_results)){
		$x = 0;
		$count = count($field_results) - 1;
		while($x <= $count){
			$field_results[$x]['data'] = unserialize($field_results[$x]['data']);
			$x++;
		}
	}
	return $field_results;
}

function ninja_forms_update_field($args){
	global $wpdb;
	$update_array = $args['update_array'];
	$where = $args['where'];
	$wpdb->update(NINJA_FORMS_FIELDS_TABLE_NAME, $update_array, $where);
}

function ninja_forms_delete_field( $field_id ){
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
}

// Begin Favorite Fields Interaction Functions

function ninja_forms_get_fav_by_id($fav_id){
	global $wpdb;
	$fav_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $fav_id), ARRAY_A);
	$fav_row['data'] = unserialize($fav_row['data']);

	return $fav_row;
}

function ninja_forms_delete_fav_by_id($fav_id){
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $fav_id), ARRAY_A);
}

function ninja_forms_get_all_favs(){
	global $wpdb;
	$fav_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE row_type = %d ORDER BY name ASC", 1), ARRAY_A);
	if(is_array($fav_results) AND !empty($fav_results)){
		$x = 0;
		$count = count($fav_results) - 1;
		while($x <= $count){
			$fav_results[$x]['data'] = unserialize($fav_results[$x]['data']);
			$x++;
		}
	}
	return $fav_results;
}

// Begin Defined Fields Functions

function ninja_forms_get_def_by_id($def_id){
	global $wpdb;
	$def_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $def_id), ARRAY_A);
	$def_row['data'] = unserialize($def_row['data']);
	return $def_row;
}

function ninja_forms_get_all_defs(){
	global $wpdb;
	$def_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE row_type = %d", 0), ARRAY_A);
	if(is_array($def_results) AND !empty($def_results)){
		$x = 0;
		$count = count($def_results) - 1;
		while($x <= $count){
			$def_results[$x]['data'] = unserialize($def_results[$x]['data']);
			$x++;
		}
	}
	return $def_results;
}

// Begin Submission Interaction Functions

function ninja_forms_get_subs($args = array()){
	global $wpdb;
	$plugin_settings = get_option( 'ninja_forms_settings' );
	if ( isset ( $plugin_settings['date_format'] ) ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'm/d/Y';
	}
	if(is_array($args) AND !empty($args)){
		$where = '';
		if(isset($args['form_id'])){
			$where = '`form_id` = '.$args['form_id'];
			unset($args['form_id']);
		}
		if(isset($args['user_id'])){
			if($where != ''){
				$where .= ' AND ';
			}
			$where .= '`user_id` = '.$args['user_id'];
			unset($args['user_id']);
		}
		if(isset($args['status'])){
			if($where != ''){
				$where .= ' AND ';
			}
			$where .= '`status` = '.$args['status'];
			unset($args['status']);
		}
		if(isset($args['action'])){
			if($where != ''){
				$where .= ' AND ';
			}
			$where .= '`action` = "'.$args['action'].'"';
			unset($args['action']);
		}
		if(isset($args['begin_date']) AND $args['begin_date'] != ''){
			$begin_date = $args['begin_date'];
			if ( $date_format == 'd/m/Y' ) {
				$begin_date = str_replace( '/', '-', $begin_date );
			} else if ( $date_format == 'm-d-Y' ) {
				$begin_date = str_replace( '-', '/', $begin_date );
			}
			$begin_date = strtotime($begin_date);
			$begin_date = date("Y-m-d G:i:s", $begin_date);
			unset($args['begin_date']);
		}else{
			unset($args['begin_date']);
			$begin_date = '';
		}
		if(isset($args['end_date']) AND $args['end_date'] != ''){
			$end_date = $args['end_date'];
			if ( $date_format == 'd/m/Y' ) {
				$end_date = str_replace( '/', '-', $end_date );
			} else if ( $date_format == 'm-d-Y' ) {
				$end_date = str_replace( '-', '/', $end_date );
			}
			$end_date = strtotime($end_date);
			$end_date = date("Y-m-d G:i:s", $end_date);
			unset($args['end_date']);
		}else{
			unset($args['end_date']);
			$end_date = '';
		}
	}

	if($begin_date != ''){
		if($where != ''){
			$where .= ' AND ';
		}
		$where .= "date_updated > '".$begin_date."'";
	}
	if($end_date != ''){
		if($where != ''){
			$where .= ' AND ';
		}
		$where .= "date_updated < '".$end_date."'";
	}

	$limit = '';
	if(isset($args['limit'])){
		$limit = " LIMIT ".$args['limit'];
		unset($args['limit']);
	}

	$subs_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE ".$where." ORDER BY `date_updated` DESC ".$limit, NINJA_FORMS_SUBS_TABLE_NAME), ARRAY_A);

	if(is_array($subs_results) AND !empty($subs_results)){
		$x = 0;
		$sub_count = count($subs_results) - 1;
		while($x <= $sub_count){
			$subs_results[$x]['data'] = unserialize($subs_results[$x]['data']);
			$x++;
		}
	}

	//Now that we have our sub results, let's loop through them and remove any that don't match our args array.
	if(is_array($args) AND !empty($args)){ //Make sure that our args variable still has something left in it. If not, we don't need to run anything else.
		if(is_array($subs_results) AND !empty($subs_results)){
			foreach($subs_results as $key => $val){ //Initiate a loop that will run for all of our submissions.
				//Set our $data variable. This variable contains an array that looks like: array('field_id' => 13, 'user_value' => 'Hello World!').
				if(!is_array($subs_results[$key]['data'])){
					$subs_results[$key]['data'] = unserialize($subs_results[$key]['data']);
				}
				$data = $subs_results[$key]['data'];

				if(is_array($data) AND !empty($data)){ //Check to make sure that the $data variable isn't empty, or not an array.
					$unset = false; //We initially assume that the submission should be kept, hence, $unset is set to false.
					$x = 1; //Initiate our counter.
					foreach($data as $d){ //Loop through our $data variable.

						if(isset($args[$d['field_id']])){ //If the field id is found within the args array, then we should check its value.
							if($args[$d['field_id']] != $d['user_value']){ //If the values are not equal, we set $unset to true.
								
								$unset = true;
							}
						}

						if($x == count($data)){ //If we are on the last item, this is our last chance to find the field id in the args array.
							if(!isset($args[$d['field_id']])){ //If the field id is not found within the args array, then we know it doesn't exist.
								
								//$unset = true; //We've reached the last item without finding our field id in the sent args array. Set $untrue to true.
							}
						}

						$x++;
					}

					if($unset){
						unset($subs_results[$key]); //If $unset ias been set to true above, unset the given submission before returning the results.
					}
				}
			}
		}
	}
	return $subs_results;
}

function ninja_forms_get_sub_by_id($sub_id){
	global $wpdb;
	$sub_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE id = %d", $sub_id), ARRAY_A);
	if( $sub_row ){
		$sub_row['data'] = unserialize($sub_row['data']);
	}
	return $sub_row;
}

function ninja_forms_get_all_subs( $form_id = '' ){
	global $wpdb;
	if( $form_id != '' ){
		$sub_results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE form_id = %d", $form_id), ARRAY_A);
	}else{
		$sub_results = $wpdb->get_results( "SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME, ARRAY_A );
	}
		return $sub_results;
}

function ninja_forms_insert_sub($args){
	global $wpdb;

	$update_array = $args;

	$wpdb->insert( NINJA_FORMS_SUBS_TABLE_NAME, $update_array );
	return $wpdb->insert_id;
}

function ninja_forms_update_sub($args){
	global $wpdb;
	$update_array = array();
	$sub_id = $args['sub_id'];
	unset( $args['sub_id'] );
	if ( !is_serialized( $args['data'] ) ) {
		$args['data'] = serialize( $args['data'] );
	}
	$update_array = $args;
	$date_updated = $date_updated = date( 'Y-m-d H:i:s', strtotime ( 'now' ) );
	$update_array['date_updated'] = $date_updated;

	$wpdb->update(NINJA_FORMS_SUBS_TABLE_NAME, $update_array, array('id' => $sub_id));
}

// The ninja_forms_delete_sub( $sub_id ) function is in includes/admin/ajax.php

function ninja_forms_addslashes_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_addslashes_deep', $value) :
        addslashes($value);
    return $value;
}

function utf8_encode_recursive( $input ){
    if ( is_array( $input ) )    {
        return array_map( __FUNCTION__, $input );
    }else{
        return utf8_encode( $input );
    }
}

function ninja_forms_str_replace_deep($search, $replace, $subject){
    if( is_array( $subject ) ){
        foreach( $subject as &$oneSubject )
            $oneSubject = ninja_forms_str_replace_deep($search, $replace, $oneSubject);
        unset($oneSubject);
        return $subject;
    } else {
        return str_replace($search, $replace, $subject);
    }
}

function ninja_forms_html_entity_decode_deep( $value, $flag = ENT_COMPAT ){
    $value = is_array($value) ?
        array_map('ninja_forms_html_entity_decode_deep', $value) :
        html_entity_decode( $value, $flag );
    return $value;
}

function ninja_forms_htmlspecialchars_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_htmlspecialchars_deep', $value) :
        htmlspecialchars( $value );
    return $value;
}

function ninja_forms_stripslashes_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_stripslashes_deep', $value) :
        stripslashes($value);
    return $value;
}

function ninja_forms_esc_html_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_esc_html_deep', $value) :
        esc_html($value);
    return $value;
}

function ninja_forms_strip_tags_deep($value ){
 	$value = is_array($value) ?
        array_map('ninja_forms_strip_tags_deep', $value) :
        strip_tags($value);
    return $value;
}

function ninja_forms_json_response(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();

	$errors = $ninja_forms_processing->get_all_errors();
	$success = $ninja_forms_processing->get_all_success_msgs();
	$fields = $ninja_forms_processing->get_all_fields();
	$form_settings = $ninja_forms_processing->get_all_form_settings();
	$extras = $ninja_forms_processing->get_all_extras();



	if( version_compare( phpversion(), '5.3', '>=' ) ){
		$json = json_encode( array( 'form_id' => $form_id, 'errors' => $errors, 'success' => $success, 'fields' => $fields, 'form_settings' => $form_settings, 'extras' => $extras ), JSON_HEX_QUOT | JSON_HEX_TAG  );
	}else{


		$errors = ninja_forms_html_entity_decode_deep( $errors );
		$success = ninja_forms_html_entity_decode_deep( $success );
		$fields = ninja_forms_html_entity_decode_deep( $fields );
		$form_settings = ninja_forms_html_entity_decode_deep( $form_settings );
		$extras = ninja_forms_html_entity_decode_deep( $extras );

		$errors = utf8_encode_recursive( $errors );
		$success = utf8_encode_recursive( $success );
		$fields = utf8_encode_recursive( $fields );
		$form_settings = utf8_encode_recursive( $form_settings );
		$extras = utf8_encode_recursive( $extras );

		$errors = ninja_forms_str_replace_deep( '"', "\u0022", $errors );
		$errors = ninja_forms_str_replace_deep( "'", "\u0027", $errors );
		$errors = ninja_forms_str_replace_deep( '<', "\u003C", $errors );
		$errors = ninja_forms_str_replace_deep( '>', "\u003E", $errors );

		$success = ninja_forms_str_replace_deep( '"', "\u0022", $success );
		$success = ninja_forms_str_replace_deep( "'", "\u0027", $success );
		$success = ninja_forms_str_replace_deep( '<', "\u003C", $success );
		$success = ninja_forms_str_replace_deep( '>', "\u003E", $success );

		$fields = ninja_forms_str_replace_deep( '"', "\u0022", $fields );
		$fields = ninja_forms_str_replace_deep( "'", "\u0027", $fields );
		$fields = ninja_forms_str_replace_deep( '<', "\u003C", $fields );
		$fields = ninja_forms_str_replace_deep( '>', "\u003E", $fields );

		$form_settings = ninja_forms_str_replace_deep( '"', "\u0022", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( "'", "\u0027", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( '<', "\u003C", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( '>', "\u003E", $form_settings );

		$extras = ninja_forms_str_replace_deep( '"', "\u0022", $extras );
		$extras = ninja_forms_str_replace_deep( "'", "\u0027", $extras );
		$extras = ninja_forms_str_replace_deep( '<', "\u003C", $extras );
		$extras = ninja_forms_str_replace_deep( '>', "\u003E", $extras );

		$json = json_encode( array( 'form_id' => $form_id, 'errors' => $errors, 'success' => $success, 'fields' => $fields, 'form_settings' => $form_settings, 'extras' => $extras ) );
		$json = str_replace( "\\\u0022", "\\u0022", $json );
		$json = str_replace( "\\\u0027", "\\u0027", $json );
		$json = str_replace( "\\\u003C", "\\u003C", $json );
		$json = str_replace( "\\\u003E", "\\u003E", $json );
	}

	return $json;
}

/*
 *
 * Function that sets up our transient variable.
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_set_transient(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();
	// Setup our transient variable.
	$transient = array();
	$transient['form_id'] = $form_id;
	$transient['field_values'] = $ninja_forms_processing->get_all_fields();
	$transient['form_settings'] = $ninja_forms_processing->get_all_form_settings();
	$all_fields_settings = array();
	if ( $ninja_forms_processing->get_all_fields() ) {
		foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
			$field_settings = $ninja_forms_processing->get_field_settings( $field_id );
			$all_fields_settings[$field_id] = $field_settings; 
		}		
	}

	$transient['field_settings'] = $all_fields_settings;

	// Set errors and success messages as $_SESSION variables.
	$success = $ninja_forms_processing->get_all_success_msgs();
	$errors = $ninja_forms_processing->get_all_errors();

	$transient['success_msgs'] = $success;
	$transient['error_msgs'] = $errors;
	$transient_id = $_SESSION['ninja_forms_transient_id'];
	//delete_transient( 'ninja_forms_test' );
	set_transient( $transient_id, $transient, DAY_IN_SECONDS );
}

/*
 *
 * Function that deletes our transient variable
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_delete_transient(){
	delete_transient( $_SESSION['ninja_forms_transient_id'] );
}
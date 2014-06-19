<?php
if(isset($_REQUEST['ninja_forms_export_subs_to_csv']) AND $_REQUEST['ninja_forms_export_subs_to_csv'] != ''){
	add_action('admin_init', 'ninja_forms_subs_bulk_export');
}

function ninja_forms_subs_bulk_export(){
	if(isset($_REQUEST['sub_id']) AND $_REQUEST['sub_id'] != ''){
		$sub_ids = array( esc_html( $_REQUEST['sub_id'] ) );
		ninja_forms_export_subs_to_csv($sub_ids);
	}
}

function ninja_forms_export_subs_to_csv( $sub_ids = '', $return = false ){
	global $ninja_forms_fields, $ninja_forms_processing;
	$plugin_settings = nf_get_settings();
	if(isset($plugin_settings['date_format'])){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = 'm/d/Y';
	}
	//Create a $label_array that contains all of the field labels.
	//Get the Form ID.
	if ( isset ( $ninja_forms_processing ) ) {
		$form_id = $ninja_forms_processing->get_form_ID();
	} else if ( isset($_REQUEST['form_id'] ) ){
		$form_id = absint( $_REQUEST['form_id'] );
	}
	//Get the fields attached to the Form ID
	$field_results = ninja_forms_get_fields_by_form_id($form_id);
	//Set the label array to a blank
	$label_array = array();
	$value_array = array();
	$sub_id_array = array();

	$label_array[0][] = "Date";
	if(is_array($field_results) AND !empty($field_results)){
		foreach($field_results as $field){
			$field_type = $field['type'];
			$field_id = $field['id'];
			if ( isset ( $ninja_forms_fields[$field_type]['process_field'] ) ) {
				$process_field = $ninja_forms_fields[$field_type]['process_field'];
			} else {
				$process_field = true;
			}
			
			if(isset($field['data']['label'])){
				$label = $field['data']['label'];
			}else{
				$label = '';
			}
			if($process_field){
				$label_array[0][$field_id] = apply_filters( 'ninja_forms_export_sub_label', $label, $field_id );
			}
		}
	}

	if(is_array($sub_ids) AND !empty($sub_ids)){
		$x = 0;
		foreach($sub_ids as $id){
			$sub_row = ninja_forms_get_sub_by_id($id);
			$sub_id_array[$x] = $id;
			$date_updated = date($date_format, strtotime($sub_row['date_updated']));
			$value_array[$x][] = $date_updated;
			if(is_array($sub_row['data']) AND !empty($sub_row['data'])){
				foreach( $label_array[0] as $field_id => $label ){
					if( $field_id != 0 ){
						$found = false;
						foreach( $sub_row['data'] as $data ){
                            $data['user_value'] = apply_filters( 'ninja_forms_export_sub_pre_value', $data['user_value'], $field_id );
                            $data['user_value'] = ninja_forms_stripslashes_deep( $data['user_value'] );
							$data['user_value'] = ninja_forms_html_entity_decode_deep( $data['user_value'], ENT_QUOTES );
							if( $data['field_id'] == $field_id ){
								if( is_array( $data['user_value'] ) ){
									$user_value = ninja_forms_implode_r( ',', $data['user_value'] );
								}else{
									$user_value = $data['user_value'];
								}
								$found = true;
							}
						}
						if( !$found ){
							$user_value = '';
						}
						$value_array[$x][] = apply_filters( 'ninja_forms_export_sub_value', $user_value, $field_id );
					}
				}
			}
			$x++;
		}
	}

	$value_array = ninja_forms_stripslashes_deep( $value_array );
	$value_array = apply_filters( 'ninja_forms_export_subs_value_array', $value_array, $sub_id_array );
	$label_array = ninja_forms_stripslashes_deep( $label_array );
	$label_array = apply_filters( 'ninja_forms_export_subs_label_array', $label_array, $sub_id_array );

	$array = array($label_array, $value_array);
	$today = date($date_format);
	$filename = apply_filters( 'ninja_forms_export_subs_csv_file_name', 'ninja_forms_subs_' . $today );
	$filename = $filename . ".csv";

	if( $return ){
		return str_putcsv( $array, 
			ninja_forms_get_csv_delimiter(), 
			ninja_forms_get_csv_enclosure(), 
			ninja_forms_get_csv_terminator() );
	}else{
		header("Content-type: application/csv");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");
		echo apply_filters('ninja_forms_csv_bom',"\xEF\xBB\xBF") ; // Byte Order Mark
		echo str_putcsv( $array, 
			ninja_forms_get_csv_delimiter(), 
			ninja_forms_get_csv_enclosure(), 
			ninja_forms_get_csv_terminator() );

		die();
	}


}

function ninja_forms_implode_r($glue, $pieces){
	$out = '';
	foreach ( $pieces as $piece ) {
		if ( is_array ( $piece ) ) {
			if ( $out == '' ) {
				$out = ninja_forms_implode_r($glue, $piece);
			} else {
				$out .= ninja_forms_implode_r($glue, $piece); // recurse
			}			
		} else {
			if ( $out == '' ) {
				$out .= $piece;
			} else {
				$out .= $glue.$piece;
			}
		}
	}
	return $out;
}


/**
 * Get the csv delimiter
 * 
 * @return string
 */
function ninja_forms_get_csv_delimiter() {
	return apply_filters( 'ninja_forms_csv_delimiter', ',' );
}


/**
 * Get the csv enclosure
 * 
 * @return string
 */
function ninja_forms_get_csv_enclosure() {
	return apply_filters( 'ninja_forms_csv_enclosure', '"' );
}


/**
 * Get the csv delimiter
 * 
 * @return string
 */
function ninja_forms_get_csv_terminator() {
	return apply_filters( 'ninja_forms_csv_terminator', "\n" );
}

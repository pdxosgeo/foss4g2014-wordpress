<?php
add_action('init', 'ninja_forms_register_edit_field_required');
function ninja_forms_register_edit_field_required(){
	add_action('ninja_forms_edit_field_after_registered', 'ninja_forms_field_required', 10);
}

function ninja_forms_field_required($field_id){
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_req = $reg_field['edit_req'];
	$field_req = $reg_field['req'];
	if($edit_req){
		if(isset($field_data['req'])){
			$req = $field_data['req'];
		}else{
			$req = '';
		}
		$options = array(
			array('name' => __( 'No', 'ninja-forms' ), 'value' => '0'),
			array('name' => __( 'Yes', 'ninja-forms' ), 'value' => '1'),
		);
		ninja_forms_edit_field_el_output($field_id, 'select', __( 'Required', 'ninja-forms' ), 'req', $req, 'thin', $options);
	}
	if($field_req){
		ninja_forms_edit_field_el_output($field_id, 'hidden', '', 'req', 1);
	}
}
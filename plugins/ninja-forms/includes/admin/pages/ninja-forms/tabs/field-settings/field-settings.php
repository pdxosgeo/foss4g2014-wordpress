<?php

function ninja_forms_register_tab_field_settings(){
	if(isset($_REQUEST['form_id'])){
		$form_id = absint( $_REQUEST['form_id'] );
	}else{
		$form_id = '';
	}

	$args = array(
		'name' => __( 'Field Settings', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'display_function' => 'ninja_forms_tab_field_settings',
		'save_function' => 'ninja_forms_save_field_settings',
		'disable_no_form_id' => true,
		'show_save' => false,
		'tab_reload' => false,
	);
	ninja_forms_register_tab('field_settings', $args);
}

add_action('admin_init', 'ninja_forms_register_tab_field_settings');

function ninja_forms_tab_field_settings(){
	global $wpdb;
	if(isset($_REQUEST['form_id'])){
		$form_id = absint( $_REQUEST['form_id'] );
	}else{
		$form_id = '';
	}
	if($form_id != ''){
		?>
		<input type="hidden" name="_ninja_forms_field_order" id="ninja_forms_field_order" value="">
		<?php
		do_action( 'ninja_forms_edit_field_before_ul', $form_id );
		do_action( 'ninja_forms_edit_field_ul', $form_id );
		do_action( 'ninja_forms_edit_field_after_ul', $form_id );
	}
}

function ninja_forms_save_field_settings($form_id, $data){
	global $wpdb, $ninja_forms_fields, $ninja_forms_admin_update_message;

	$order = esc_html( $_POST['_ninja_forms_field_order'] );

	$order = str_replace("ninja_forms_field_", "", $order);
	$order = explode(',', $order);
	if(is_array($order)){
		$order_array = array();
		$x = 0;
		foreach($order as $id){
			$order_array[$id] = $x;
			$x++;
		}
	}

	$tmp_array = array();
	foreach ( $data as $field_id => $vals ) {
		$field_id = str_replace('ninja_forms_field_', '', $field_id);
		$tmp_array[$field_id] = $vals;
	}

	$data = $tmp_array;

	if(isset($ninja_forms_fields) AND is_array($ninja_forms_fields)){
		foreach($ninja_forms_fields as $slug => $field){
			if($field['save_function'] != ''){
				$save_function = $field['save_function'];
				$arguments['form_id'] = $form_id;
				$arguments['data'] = $data;
				$data = call_user_func_array($save_function, $arguments);
			}
		}
	}

	if($form_id != '' AND $form_id != 0 AND $form_id != 'new'){
		foreach($data as $field_id => $vals){
			$order = $order_array[$field_id];
			$field_row = ninja_forms_get_field_by_id( $field_id );
			$field_data = $field_row['data'];
			foreach( $vals as $k => $v ){
				$field_data[$k] = $v;
			}
			$data_array = array('data' => serialize( $field_data ), 'order' => $order);
			$wpdb->update( NINJA_FORMS_FIELDS_TABLE_NAME, $data_array, array( 'id' => $field_id ));
		}
		$date_updated = date( 'Y-m-d H:i:s', strtotime ( 'now' ) );
		$data_array = array( 'date_updated' => $date_updated );
		$wpdb->update( NINJA_FORMS_TABLE_NAME, $data_array, array( 'id' => $form_id ) );
	}

	$update_msg = __( 'Field Settings Saved', 'ninja-forms' );
	return $update_msg;
}
<?php

/*
 * Import a serialized ninja form
 *
 * @since unknown
 * @returns int
 */
function ninja_forms_import_form( $data ){
	global $wpdb;
	$form = unserialize( $data );

	// change the update date to today
	$form['date_updated'] = date('Y-m-d H:i:s');

	// get the form fields
	$form_fields = $form['field'];

	unset($form['field']);
	$form = apply_filters( 'ninja_forms_before_import_form', $form );
	$form['data'] = serialize( $form['data'] );
	$form['id'] = NULL;
	$wpdb->insert(NINJA_FORMS_TABLE_NAME, $form);
	$form_id = $wpdb->insert_id;
	$form['id'] = $form_id;
	if(is_array($form_fields)){
		for ($x=0; $x < count( $form_fields ); $x++) {
			$form_fields[$x]['form_id'] = $form_id;
			$form_fields[$x]['data'] = serialize( $form_fields[$x]['data'] );
			$old_field_id = $form_fields[$x]['id'];
			$form_fields[$x]['id'] = NULL;
			$wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $form_fields[$x] );
			$form_fields[$x]['id'] = $wpdb->insert_id;
			$form_fields[$x]['old_id'] = $old_field_id;
			$form_fields[$x]['data'] = unserialize( $form_fields[$x]['data'] );
		}
	}
	$form['data'] = unserialize( $form['data'] );
	$form['field'] = $form_fields;
	do_action( 'ninja_forms_after_import_form', $form );
	return $form['id'];
}
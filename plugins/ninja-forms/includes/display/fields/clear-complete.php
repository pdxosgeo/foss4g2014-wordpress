<?php

/**
 * Function that resets the field values to default if the form has been submitted.
 *
 * @since 2.5
 * @return void
 */

function nf_clear_complete( $form_id ) {
	global $ninja_forms_processing;

	if ( ! isset ( $ninja_forms_processing ) or $ninja_forms_processing->get_form_setting( 'clear_complete' ) == 0 or $ninja_forms_processing->get_form_setting( 'processing_complete' ) != 1 )
		return false;

	$all_fields = $ninja_forms_processing->get_all_fields();
	foreach ( $all_fields as $field_id => $user_value ) {
		$default_value = $ninja_forms_processing->get_field_setting( $field_id, 'default_value' );
		$ninja_forms_processing->update_field_value( $field_id, $default_value );
	}
}

add_action( 'ninja_forms_display_init', 'nf_clear_complete', 999 );
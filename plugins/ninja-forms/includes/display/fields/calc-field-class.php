<?php

/*
 *
 * Function that adds a calc filter class to the field if appropriate.
 *
 * @since 2.4
 * @return void
 */

function ninja_forms_calc_listen_field_class( $form_id ) {
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$field_results = $ninja_forms_loading->get_all_fields();
	} else {
		$field_results = $ninja_forms_processing->get_all_fields();
	}

	foreach( $field_results as $field_id => $user_value ) {
		if ( isset ( $ninja_forms_loading ) ) {
			$field_row = $ninja_forms_loading->get_field_settings( $field_id );
		} else {
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );
		}
		
		if ( isset ( $field_row['type'] ) ) {
			$field_type = $field_row['type'];
		} else {
			$field_type = '';
		}

		$field_data = $field_row['data'];
		$field_data = apply_filters( 'ninja_forms_field', $field_data, $field_id );
		$calc_listen = '';

		$sub_total = false;
		$tax = false;
		foreach($field_results as $f_id => $user_value ){

			if ( isset ( $ninja_forms_loading ) ) {
				$field = $ninja_forms_loading->get_field_settings( $f_id );
			} else {
				$field = $ninja_forms_processing->get_field_settings( $f_id );
			}

			$data = $field['data'];

			if ( isset ( $field['type'] ) ) {
				$field_type = $field['type'];
			} else {
				$field_type = '';
			}

			// Check for advanced calculation fields that reference this field. If we find one, and use_calc_adv is set to 1, add a special class to this field.
			if ( $field_type == '_calc' ) {

				// Check to see if this is a sub_total calculation
				if ( isset ( $data['calc_method'] ) ) {
					$calc_method = $data['calc_method'];
				} else {
					$calc_method = 'auto';
				}

				switch ( $calc_method ) {
					case 'auto':
						if ( isset ( $field_data['calc_auto_include'] ) AND $field_data['calc_auto_include'] == 1 ) {
							$calc_listen = 'ninja-forms-field-calc-listen ninja-forms-field-calc-auto';
						}
						break;
					case 'fields':
						foreach ( $data['calc'] as $calc ) {
							if ( $calc['field'] == $field_id ) {
								if ( $calc_listen == '' ) {
									$calc_listen = 'ninja-forms-field-calc-listen';
								}
								break;
							}
						}
						break;
					case 'eq':
						$eq = $data['calc_eq'];
						if (preg_match("/\bfield_".$field_id."\b/i", $eq ) ) {
							if ( $calc_listen == '' ) {
								$calc_listen = 'ninja-forms-field-calc-listen';
							}
							break;
						}
						break;
				}
			}
		}

		if ( isset ( $field_data['payment_sub_total'] ) AND $field_data['payment_sub_total'] == 1 ) {
			if ( $calc_listen == '' ) {
				$calc_listen = 'ninja-forms-field-calc-listen';
			}
		}

		// Check to see if this is a tax field;
		if ( $field_type == '_tax' ) {
			if ( $calc_listen == '' ) {
				$calc_listen = 'ninja-forms-field-calc-listen';
			}
		}

		if ( isset ( $ninja_forms_loading ) ){
			$field_class = $ninja_forms_loading->get_field_setting( $field_id, 'field_class' );
			$field_class .= ' '.$calc_listen;
			$ninja_forms_loading->update_field_setting( $field_id, 'field_class', $field_class );
		} else {
			$field_class = $ninja_forms_processing->get_field_setting( $field_id, 'field_class' );
			$field_class .= ' '.$calc_listen;
			$ninja_forms_processing->update_field_setting( $field_id, 'field_class', $calc_listen );
		}
	}
}

add_action( 'ninja_forms_display_init', 'ninja_forms_calc_listen_field_class' );
<?php

/*
 *
 * Function that filters our fields looking for calculations that need to be made upon page load.
 *
 * @since 2.2.28
 * @returns $data
 */

function ninja_forms_field_calc_filter( $calc_data, $field_id ){
	global $ninja_forms_processing;
	
	if ( !is_object ( $ninja_forms_processing ) ) {
		$field_row = ninja_forms_get_field_by_id( $field_id );
		$form_id = $field_row['form_id'];
		if ( $field_row['type'] == '_calc' ) {
				
			// Figure out which method we are using to calculate this field.
			if ( isset ( $calc_data['calc_method'] ) ) {
				$calc_method = $calc_data['calc_method'];
			} else {
				$calc_method = 'auto';
			}

			// Get our advanced field op settings if they exist.
			if ( isset ( $calc_data['calc'] ) ) {
				$calc_fields = $calc_data['calc'];
			} else {
				$calc_fields = array();
			}

			// Get our calculation equation if it exists.
			if ( isset ( $calc_data['calc_eq'] ) ) {
				$calc_eq = $calc_data['calc_eq'];
			} else {
				$calc_eq = '';
			}

			//if ( !isset ( $ninja_forms_processing ) ) {
				
				$all_fields = ninja_forms_get_fields_by_form_id( $form_id );

				remove_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11 );
				// Figure out if there is a sub_total and a tax field. If there are, and this is a total field set to calc_method auto, we're using an equation, not auto.
				$tax = false;
				$sub_total = false;
				foreach ( $all_fields as $field ) {

					$data = apply_filters( 'ninja_forms_field', $field['data'], $field['id'] );
					if ( $field['type'] == '_tax' ) {
						// There is a tax field; save its field_id.
						$tax = $field['id'];
					} else if ( isset ( $data['payment_sub_total'] ) AND $data['payment_sub_total'] == 1 ) {
						// There is a sub_total field; save its field_id.
						$sub_total = $field['id'];
					}
				}

				// If the tax and sub_total have been found, and this is a total field set to auto, change the calc_method and calc_eq.
				if ( $tax AND $sub_total AND isset ( $calc_data['payment_total'] ) AND $calc_data['payment_total'] == 1 AND $calc_method == 'auto' ) {
					$calc_method = 'eq';
					$tax_field = ninja_forms_get_field_by_id( $tax );
					$tax_rate = $tax_field['data']['default_value'];
					if ( strpos( $tax_rate, "%" ) !== false ) {
						$tax_rate = str_replace( "%", "", $tax_rate );
						$tax_rate = $tax_rate / 100;
					}
					$calc_eq = 'field_'.$sub_total.' + ( field_'.$sub_total.' * '.$tax_rate.' )';
				}

				// Figure out how many calculation fields we have and run

				$result = $calc_data['default_value'];
				
				foreach ( $all_fields as $field ) {
					if ( $field['id'] != $field_id ) {
						$field_data = apply_filters( 'ninja_forms_field', $field['data'], $field['id'] );
						if ( isset ( $field_data['default_value'] ) ) {
							$field_value = $field_data['default_value'];
						} else {
							$field_value = '';
						}

						switch ( $calc_method ) {
							case 'auto': // We are automatically totalling the fields that have a calc_auto_include set to 1.
								if ( isset ( $field_data['calc_auto_include'] ) AND $field_data['calc_auto_include'] == 1 ) {
									
									if ( $field['type'] == '_calc' ) {
										$calc_value = ninja_forms_calc_field_loop( $field['id'], '', $result );
									} else {
										$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );							
									}

									if ( $calc_value !== false ) {
										$result = ninja_forms_calc_evaluate( 'add', $result, $calc_value );						
									}
									
								}
								break;
							case 'fields': // We are performing a specific set of operations on a set of fields.
								if ( is_array ( $calc_fields ) ) {
									foreach ( $calc_fields as $c ) {
										if ( $c['field'] == $field['id'] ) {
											if ( $field['type'] == '_calc' ) {
												$result = ninja_forms_calc_field_loop( $field['id'], '', $result );
											} else {
												$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
												if ( $calc_value !== false ) {
													$result = ninja_forms_calc_evaluate( $c['op'], $result, $calc_value );
												}
											}
										}
									}
								}
								break;
							case 'eq':
								if (preg_match("/\bfield_".$field['id']."\b/i", $calc_eq ) ) {
									if ( $field['type'] == '_calc' ) {
										$calc_value = ninja_forms_calc_field_loop( $field['id'], $calc_eq );
									} else {
										$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
									}
									if ( $calc_value !== false ) {
										$calc_eq = preg_replace('/\bfield_'.$field['id'].'\b/', $calc_value, $calc_eq );
									}
								}
								break;
						}				
					}
				}

				if ( $calc_method == 'eq' and $calc_eq != '' ) {
					$eq = new eqEOS();
					$result = $eq->solveIF($calc_eq);
	 			}
	 			if ( isset ( $calc_data['calc_places'] ) ) {
	 				$places = $calc_data['calc_places'];
	 				$result = number_format( round( $result, $places ), $places );
	 			}

				$calc_data['default_value'] = $result;
					
				add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
			//}
		}
	}
	return $calc_data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );

/*
 *
 * Function that takes a calc field and loops through until it returns a number value.
 * It is called in the ninja_forms_field_calc_filter function above.
 *
 * @since 2.2.30
 * @returns string $result
 */

function ninja_forms_calc_field_loop( $field_id, $calc_eq = '', $result = '' ){
	global $ninja_forms_processing;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$calc_data = $field_row['data'];

	$calc_data = apply_filters( 'ninja_forms_field', $calc_data, $field_id );

	// Figure out which method we are using to calculate this field.
	if ( isset ( $calc_data['calc_method'] ) ) {
		$calc_method = $calc_data['calc_method'];
	} else {
		$calc_method = 'auto';
	}

	// Get our advanced field op settings if they exist.
	if ( isset ( $calc_data['calc'] ) ) {
		$calc_fields = $calc_data['calc'];
	} else {
		$calc_fields = array();
	}

	// Get our calculation equation if it exists.
	if ( isset ( $calc_data['calc_eq'] ) ) {
		$calc_eq = $calc_data['calc_eq'];
	} else {
		$calc_eq = array();
	}

	$form_id = $field_row['form_id'];
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );

	// Figure out if there is a sub_total and a tax field. If there are, and this is a total field set to calc_method auto, we're using an equation, not auto.
	$tax = false;
	$sub_total = false;
	
	foreach ( $all_fields as $field ) {

		$data = apply_filters( 'ninja_forms_field', $field['data'], $field['id'] );
		if ( $field['type'] == '_tax' ) {
			// There is a tax field; save its field_id.
			$tax = $field['id'];
		} else if ( isset ( $data['payment_sub_total'] ) AND $data['payment_sub_total'] == 1 ) {
			// There is a sub_total field; save its field_id.
			$sub_total = $field['id'];
		}
	}
	
	// If the tax and sub_total have been found, and this is a total field set to auto, change the calc_method and calc_eq.
	if ( $tax AND $sub_total AND isset ( $calc_data['payment_total'] ) AND $calc_data['payment_total'] == 1 AND $calc_method == 'auto' ) {
		$calc_method = 'eq';
		$tax_field = ninja_forms_get_field_by_id( $tax );
		$tax_rate = $tax_field['data']['default_value'];
		if ( strpos( $tax_rate, "%" ) !== false ) {
			$tax_rate = str_replace( "%", "", $tax_rate );
			$tax_rate = $tax_rate / 100;
		}
		$calc_eq = 'field_'.$sub_total.' + ( field_'.$sub_total.' * '.$tax_rate.' )';
	}

	// Figure out how many calculation fields we have and run
	foreach ( $all_fields as $field ) {
		if ( $field['id'] != $field_id ) {
			//add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 20, 2 );
			$field_data = apply_filters( 'ninja_forms_field', $field['data'], $field['id'] );
			//remove_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 20, 2 );
			
			if ( isset ( $field_data['default_value'] ) ) {
				$field_value = $field_data['default_value'];
			} else {
				$field_value = '';
			}
			switch ( $calc_method ) {
				case 'auto': // We are automatically totalling the fields that have a calc_auto_include set to 1.
					if ( isset ( $field_data['calc_auto_include'] ) AND $field_data['calc_auto_include'] == 1 ) {
						if ( $field['type'] == '_calc' ) {
							$calc_value = ninja_forms_calc_field_loop( $field['id'], '', $result );
						} else {
							$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );							
						}

						if ( $calc_value !== false ) {
							$result = ninja_forms_calc_evaluate( 'add', $result, $calc_value );						
						}
					}
					break;
				case 'fields': // We are performing a specific set of operations on a set of fields.
					if ( is_array ( $calc_fields ) ) {
						foreach ( $calc_fields as $c ) {
							if ( $c['field'] == $field['id'] ) {
								if ( $field['type'] == '_calc' ) {
									$result = ninja_forms_calc_field_loop( $field['id'], '', $result );
								} else {
									$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
									if ( $calc_value !== false ) {
										$result = ninja_forms_calc_evaluate( $c['op'], $result, $calc_value );
									}
								}
							}
						}
					}
					break;
				case 'eq':
					if (preg_match("/\bfield_".$field['id']."\b/i", $calc_eq ) ) {
						if ( $field['type'] == '_calc' ) {
							$calc_value = ninja_forms_calc_field_loop( $field['id'], $calc_eq );
						} else {
							$calc_value = ninja_forms_field_calc_value( $field['id'], $field_value, $calc_method );
						}
						if ( $calc_value !== false ) {
							$calc_eq = preg_replace('/\bfield_'.$field['id'].'\b/', $calc_value, $calc_eq );
						}
					}
					break;
			}
		}
	}
	if ( $calc_method == 'eq' and $calc_eq != '' ) {
		$eq = new eqEOS();
		$result = $eq->solveIF($calc_eq);
	}

	if ( $result == '' ) {
		$result = 0;
	}

	return $result;
}

/*
 *
 * Function that filters the list options span and adds the appropriate listener class if there is a calc needed for the field.
 *
 * @since 2.2.28
 * @returns $class
 */

function ninja_forms_calc_filter_list_options_span( $class, $field_id ){
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$add_class = false;
	// Check to see if this field has cal_auto_include set to 1. If it does, we want to output a class name.
	if ( isset ( $field_row['data']['calc_auto_include'] ) AND !empty ( $field_row['data']['calc_auto_include'] ) ) {
		$add_class = true;
	}

	$form = ninja_forms_get_form_by_field_id( $field_id );
	$form_id = $form['id'];
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	foreach ( $all_fields as $field ){
		if ( $field['type'] == '_calc' ) {
			if ( isset ( $field['data']['calc_method'] ) ) {
				$calc_method = $field['data']['calc_method'];
			} else {
				$calc_method = 'auto';
			}

			switch ( $calc_method ) {
				case 'fields':
					if ( isset ( $field['data']['calc'] ) ) {
						foreach ( $field['data']['calc'] as $calc ) {
							if ( $calc['field'] == $field_id ) {
								$add_class = true;
								break;
							}
						}
					}
					break;
				case 'eq':
					$eq = $field['data']['calc_eq'];
					if (preg_match("/\bfield_".$field_id."\b/i", $eq ) ) {
						$add_class = true;
						break;
					}
					break;
			}
		}
	}
	if ( $add_class ) {
		$class .= ' ninja-forms-field-list-options-span-calc-listen';		
	}

	return $class;
}

add_filter( 'ninja_forms_display_list_options_span_class', 'ninja_forms_calc_filter_list_options_span', 10, 2 );

/*
 *
 * Function that takes two variables and our calculation string operator and returns the result.
 *
 * @since 2.2.28
 * @returns int value
 */

function ninja_forms_calc_evaluate($op, $value1, $value2 ){
	switch ( $op ) {
		case 'add':
			return $value1 + $value2;
			break;
		case 'subtract':
			return $value1 - $value2;
			break;
		case 'multiply':
			return $value1 * $value2;
			break;
		case 'divide':
			return $value1 / $value2;
			break;
	}
}

/*
 *
 * Function that returns the calculation value of a field given by field_id if it is to be included in the auto total.
 *
 * @since 2.2.30
 * @returns calc_value
 */

function ninja_forms_field_calc_value( $field_id, $field_value = '', $calc_method = 'auto' ) {
	global $ninja_forms_processing;
	
	if ( isset ( $ninja_forms_processing ) ){
		$field = $ninja_forms_processing->get_field_settings( $field_id );
	} else {
		$field = ninja_forms_get_field_by_id( $field_id );	
	}
	
	//$field_data = $field['data'];
	//remove_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
	$field_data = apply_filters( 'ninja_forms_field', $field['data'], $field_id );
	//add_filter( 'ninja_forms_field', 'ninja_forms_field_calc_filter', 11, 2 );
	
	if ( isset ( $field_data['default_value'] ) ) {
		$default_value = $field_data['default_value'];
	} else { 
		$default_value = '';
	}

	if ( $field_value == '' ) {
		$field_value = $default_value;
	}
	
	$calc_value = 0;
	if ( $field['type'] == '_list' ) {
		if ( isset ( $field_data['list']['options'] ) ) {
			foreach ( $field_data['list']['options'] as $option ) {
				if ( isset ( $field_data['list_show_value'] ) AND $field_data['list_show_value'] == 1 ) {
					$option_value = $option['value'];
				} else {
					$option_value = $option['label'];
				}
				if ( $option_value == $field_value OR ( is_array ( $field_value ) AND in_array ( $option_value, $field_value ) ) ) {
					$calc_value += $option['calc'];
				}
			}
		}
	} else if ( $field['type'] == '_checkbox' ) {
		if ( $field_value == 'checked' ){
			$calc_value = $field_data['calc_value']['checked'];
		} else {
			if ( $calc_method == 'auto' ) {
				return false;
			} else {
				$calc_value = $field_data['calc_value']['unchecked'];
			}
		}
	} else {
		if ( !$field_value OR $field_value == '' ) {
			$field_value = 0;
		}
		$calc_value = (float) preg_replace('/[^0-9.]*/','',$field_value);
	}
	
	if ( is_string( $calc_value ) AND strpos( $calc_value, "%" ) !== false ) {
		$calc_value = str_replace( "%", "", $calc_value );
		$calc_value = $calc_value / 100;
	}
	if ( $calc_value == '' OR !$calc_value ) {
		$calc_value = 0;
	}

	return $calc_value;
}
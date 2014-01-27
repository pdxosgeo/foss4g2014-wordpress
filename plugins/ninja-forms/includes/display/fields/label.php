<?php
/**
 * Outputs the HTML of the field label if it is set to display.
 * Also outputs the required symbol if it is set.
**/

function ninja_forms_display_field_label( $field_id, $data ){
	global $ninja_forms_fields, $ninja_forms_loading, $ninja_forms_processing;

	$plugin_settings = get_option("ninja_forms_settings");

	if ( isset ( $ninja_forms_loading ) ) {
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else if ( isset ( $ninja_forms_processing ) ) {
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}
	
	$field_type = $field_row['type'];

	if( isset ( $data['label'] ) ) {
		$label = stripslashes($data['label']);
	} else if ( isset ( $ninja_forms_fields[$field_type]['default_label'] ) ) {
		$label = $ninja_forms_fields[$field_type]['default_label'];
	} else {
		$label = '';
	}

	if( isset ( $data['label_pos'] ) ) {
		$label_pos = stripslashes($data['label_pos']);
	} else {
		$label_pos = '';
	}

	if ( isset( $plugin_settings['req_field_symbol'] ) ) {
		$req_symbol = $plugin_settings['req_field_symbol'];
	} else {
		$req_symbol = '';
	}

	if ( isset ( $data['req'] ) ) {
		$req = $data['req'];
	} else {
		$req = '';
	}

	if ( isset ( $data['display_label'] ) ) {
		$display_label = $data['display_label'];
	} else {
		$display_label = true;
	}

	$label_class = '';

	$label_class = apply_filters( 'ninja_forms_label_class', $label_class, $field_id );

	if ( $display_label ) {
		if ( $req == 1 ) {
			$req_span = "<span class='ninja-forms-req-symbol'>$req_symbol</span>";
		} else {
			$req_span = '';
		}
		?>
		<label for="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>_label" class="<?php echo $label_class;?>"><?php echo $label;?> <?php echo $req_span;?>
		<?php
		if ( $label_pos != 'left' ) {
			do_action( 'ninja_forms_display_field_help', $field_id, $data );
		}
		?>
		</label>
		<?php
	}
}

add_action('ninja_forms_display_field_label', 'ninja_forms_display_field_label', 10, 2);

function ninja_forms_display_label_inside( $data, $field_id ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_processing ) and $ninja_forms_processing->get_field_value( $field_id ) )
		return $data;

	if ( isset ( $ninja_forms_loading ) ) {
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else if ( isset ( $ninja_forms_processing ) ) {
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}

	if ( isset ( $field_row['type'] ) ) {
		$field_type = $field_row['type'];
	} else {
		$field_type = '';
	}

	if ( isset( $data['label_pos'] ) ) {
		$label_pos = $data['label_pos'];
	} else {
		$label_pos = '';
	}

	// Get the required field symbol.
	$settings = get_option( 'ninja_forms_settings' );
	if ( isset ( $settings['req_field_symbol'] ) ) {
		$req_symbol = $settings['req_field_symbol'];
	} else {
		$req_symbol = '*';
	}

	if ( isset ( $data['req'] ) and $data['req'] == 1 and $data['label_pos'] == 'inside' ) {
		$data['label'] .= ' '.$req_symbol;
		$data['req'] = 0;
	}

	if ( isset( $data['label'] ) ) {
		$label = $data['label'];
	} else {
		$label = '';
	}

	if ( $field_type != '_list' ) {
		if ( $label_pos == 'inside' ) {
			if ( isset ( $ninja_forms_loading ) ) {
				$ninja_forms_loading->update_field_value( $field_id, $label );
			} else {
				$ninja_forms_processing->update_field_value( $field_id, $label );
			}
		}
	}

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_display_label_inside', 5, 2 );
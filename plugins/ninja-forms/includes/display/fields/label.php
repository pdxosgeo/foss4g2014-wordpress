<?php
/**
 * Outputs the HTML of the field label if it is set to display.
 * Also outputs the required symbol if it is set.
**/

function ninja_forms_display_field_label( $field_id, $data ){
	global $ninja_forms_fields;

	$plugin_settings = get_option("ninja_forms_settings");

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if(isset($data['label'])){
		$label = stripslashes($data['label']);
	}else if ( isset ( $ninja_forms_fields[$field_type]['default_label'] ) ){
		$label = $ninja_forms_fields[$field_type]['default_label'];
	} else {
		$label = '';
	}

	if(isset($data['label_pos'])){
		$label_pos = stripslashes($data['label_pos']);
	}else{
		$label_pos = '';
	}

	if(isset($plugin_settings['req_field_symbol'])){
		$req_symbol = $plugin_settings['req_field_symbol'];
	}else{
		$req_symbol = '';
	}

	if(isset($data['req'])){
		$req = $data['req'];
	}else{
		$req = '';
	}

	if(isset($data['display_label'])){
		$display_label = $data['display_label'];
	}else{
		$display_label = true;
	}

	$label_class = '';

	$label_class = apply_filters( 'ninja_forms_label_class', $label_class, $field_id );

	if($display_label){
		if($req == 1){
			$req_span = "<span class='ninja-forms-req-symbol'>$req_symbol</span>";
		}else{
			$req_span = '';
		}
		?>
		<label for="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>_label" class="<?php echo $label_class;?>"><?php echo $label;?> <?php echo $req_span;?>
		<?php
		if( $label_pos != 'left' ){
			do_action( 'ninja_forms_display_field_help', $field_id, $data );
		}
		?>
		</label>
		<?php
	}
}

add_action('ninja_forms_display_field_label', 'ninja_forms_display_field_label', 10, 2);

function ninja_forms_display_label_inside( $data, $field_id ){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing ) ){
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}else{
		$field_row = ninja_forms_get_field_by_id( $field_id );
	}

	$field_data = $field_row['data'];
	if( isset( $field_data['label_pos'] ) ){
		$label_pos = $field_data['label_pos'];
	}else{
		$label_pos = '';
	}

	if( isset( $field_data['label'] ) ){
		$label = $field_data['label'];
	}else{
		$label = '';
	}

	if ( $field_row['type'] != '_list' ) {
		if( $label_pos == 'inside' ){
			$data['default_value'] = $label;
		}
	}

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_display_label_inside', 5, 2 );
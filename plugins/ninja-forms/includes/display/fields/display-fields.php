<?php
/**
 * Outputs the HTML for each field within a given form_id.
 * It is attached to the ninja_forms_display_fields hook which is excuted by ninja_forms_display_form() in display-form.php
**/
add_action('init', 'ninja_forms_register_display_fields');
function ninja_forms_register_display_fields(){
	add_action('ninja_forms_display_fields', 'ninja_forms_display_fields', 10, 2);
}

function ninja_forms_display_fields($form_id){
	global $ninja_forms_fields, $ninja_forms_processing, $current_user;
	get_currentuserinfo();
	//var_dump( $ninja_forms_processing );

	if(isset($current_user)){
		$user_id = $current_user->ID;
	}else{
		$user_id = '';
	}

	$plugin_settings = get_option("ninja_forms_settings");
	$form_row = ninja_forms_get_form_by_id($form_id);
	$field_results = ninja_forms_get_fields_by_form_id($form_id);
	$field_results = apply_filters('ninja_forms_display_fields_array', $field_results, $form_id);
	if(is_array($field_results) AND !empty($field_results)){
		foreach($field_results as $field){
			if( isset( $ninja_forms_fields[$field['type']] ) ){
				$type = $ninja_forms_fields[$field['type']];

				$field_id = $field['id'];
				if(isset($field['data']['req'])){
					$req = $field['data']['req'];
				}else{
					$req = '';
				}

				$default_label_pos = $type['default_label_pos'];
				$display_wrap = $type['display_wrap'];
				$display_label = $type['display_label'];
				$sub_edit_function = $type['sub_edit_function'];
				$display_function = $type['display_function'];

				//Check to see if we are currently editing a form submission.
				//If we are, then $display_function should be set to the sub_edit_function instead.
				if( is_object( $ninja_forms_processing)){
					$sub_id = $ninja_forms_processing->get_form_setting('sub_id');
				}else if(isset($_REQUEST['sub_id'])){
					$sub_id = absint( $_REQUEST['sub_id'] );
				}else{
					$sub_id = '';
				}

				if ( $sub_id != '' AND $sub_edit_function != '' AND is_admin() ){
					$display_function = $sub_edit_function;
				}

				$process_field = $type['process_field'];
				$data = $field['data'];


				//These filters can be used to temporarily modify the settings of a field, i.e. default_value.
				$data = apply_filters( 'ninja_forms_field', $data, $field_id );
				//Check the show_field value of our $data array. If it is set to false, don't output the field.
				if(isset($data['show_field'])){
					$show_field = $data['show_field'];
				}else{
					$show_field = true;
				}

				if( isset( $data['display_style'] ) ){
					$display_style = $data['display_style'];
				}else{
					$display_style = '';
				}

				if( isset( $data['visible'] ) ){
					$visible = $data['visible'];
				}else{
					$visible = true;
				}

				if ( $display_style != '' ) {
					$display_style = 'style="'.$display_style.'"';
				}

				if ( $display_function != '' AND $show_field ) {
					if ( isset( $data['label_pos'] ) ) {
							$label_pos = $data['label_pos'];
					}else{
							$label_pos = '';
					}
					if( $label_pos == '' ) {
						$label_pos = $default_label_pos;
					}

					do_action( 'ninja_forms_display_before_field', $field_id, $data );

					//Check to see if display_wrap has been disabled. If it hasn't, show the wrapping DIV.
					if($display_wrap){
						$field_wrap_class = ninja_forms_get_field_wrap_class($field_id);
						$field_wrap_class = apply_filters( 'ninja_forms_field_wrap_class', $field_wrap_class, $field_id );
						do_action( 'ninja_forms_display_before_opening_field_wrap', $field_id, $data );
						?>
						<div class="<?php echo $field_wrap_class;?>" <?php echo $display_style;?> id="ninja_forms_field_<?php echo $field_id;?>_div_wrap" data-visible="<?php echo $visible;?>">
						<?php
						do_action( 'ninja_forms_display_after_opening_field_wrap', $field_id, $data );
					}

					//Check to see if display_label has been disabled. If it hasn't, show the label.
					if( $display_label ){
						if( $label_pos == 'left' OR $label_pos == 'above' ){ // Check the label position variable. If it is left or above, show the label.
							do_action( 'ninja_forms_display_before_field_label', $field_id, $data );
							do_action( 'ninja_forms_display_field_label', $field_id, $data );
							do_action( 'ninja_forms_display_after_field_label', $field_id, $data );
						}
					}

					//Check to see if there is a registered display function. If so, call it.
					if($display_function != ''){

						do_action( 'ninja_forms_display_before_field_function', $field_id, $data );
						$arguments['field_id'] = $field_id;
						$arguments['data'] = $data;
						call_user_func_array($display_function, $arguments);
						do_action( 'ninja_forms_display_after_field_function', $field_id, $data );
						if( $label_pos == 'left' OR $label_pos == 'inside'){
							do_action( 'ninja_forms_display_field_help', $field_id, $data );
						}
					}

					//Check to see if display_label has been disabled. If it hasn't, show the label.
					if($display_label){
						if($label_pos == 'right' OR $label_pos == 'below'){ // Check the label position variable. If it is right or below, show the label.
							do_action( 'ninja_forms_display_before_field_label', $field_id, $data );
							do_action( 'ninja_forms_display_field_label', $field_id, $data );
							do_action( 'ninja_forms_display_after_field_label', $field_id, $data );
						}
					}

					//Check to see if display_wrap has been disabled. If it hasn't close the wrapping DIV
					if($display_wrap){
						do_action( 'ninja_forms_display_before_closing_field_wrap', $field_id, $data );
						?>
						</div>
						<?php
						do_action( 'ninja_forms_display_after_closing_field_wrap', $field_id, $data );
					}
					do_action( 'ninja_forms_display_after_field', $field_id, $data );
				}
			}
		}
	}
}

/**
 * The next two functions are used to get CSS class names based upon field settings.
 *
**/

function ninja_forms_get_field_wrap_class($field_id){
	global $ninja_forms_processing;
	$field_wrap_class = 'field-wrap';
	$field_row = ninja_forms_get_field_by_id($field_id);
	$form_id = $field_row['form_id'];
	$data = $field_row['data'];

	$type_slug = $field_row['type'];

	if(strpos($type_slug, "_") === 0){
		$type_slug = substr($type_slug, 1);
	}

	$field_wrap_class .= " ".$type_slug."-wrap";
	if(isset($data['label_pos'])){
		$label_pos = $data['label_pos'];
	}else{
		$label_pos = 'left';
	}
	$field_wrap_class .= " label-".$label_pos;


	$x = 0;
	$custom_class = '';

	if(isset($data['class']) AND !empty($data['class'])){
		$class_array = explode(",", $data['class']);
		foreach($class_array as $class){
			$custom_class .= $class;
			if($x != (count($class_array) - 1)){
				$custom_class .= " ";
			}
			$x++;
		}
	}

	if($custom_class != ''){
		$custom_class = str_replace( ' ', '-wrap ', $custom_class );
		$field_wrap_class .= ' '.$custom_class.'-wrap';
	}

	if( is_object( $ninja_forms_processing) AND is_array($ninja_forms_processing->get_errors_by_location($field_id))){
		foreach($ninja_forms_processing->get_errors_by_location($field_id) as $error){
			$field_wrap_class .= ' ninja-forms-error';
			break;
		}
	}
	return apply_filters( 'ninja_forms_display_field_wrap_class', $field_wrap_class, $field_id );
}


function ninja_forms_get_field_class($field_id){
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_data = $field_row['data'];
	$field_data = apply_filters( 'ninja_forms_field', $field_data, $field_id );
	$field_class = 'ninja-forms-field';

	$x = 0;
	$custom_class = '';

	if(isset($field_data['class']) AND !empty($field_data['class'])){
		$class_array = explode(",", $field_data['class']);
		foreach($class_array as $class){
			$custom_class .= $class;
			if($x != (count($class_array) - 1)){
				$custom_class .= " ";
			}
			$x++;
		}
	}

	$req_class = '';
	if(isset($field_data['req']) AND $field_data['req'] == 1){
		$req_class = 'ninja-forms-req';
	}

	$form_id = $field_row['form_id'];
	$watch_fields = array();
	$field_results = ninja_forms_get_fields_by_form_id($form_id);
	$calc_listen = '';
	$sub_total = false;
	$tax = false;
	foreach($field_results as $field){
		$data = $field['data'];
		if(isset($data['conditional']) AND is_array($data['conditional'])){
			foreach($data['conditional'] as $conditional){
				if(isset($conditional['cr']) AND is_array($conditional['cr'])){
					foreach($conditional['cr'] as $cr){
						$watch_fields[$cr['field']] = 1;
					}
				}
			}
		}
		// Check for advanced calculation fields that reference this field. If we find one, and use_calc_adv is set to 1, add a special class to this field.
		if ( $field['type'] == '_calc' ) {

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
	if ( $field_row['type'] == '_tax' ) {
		if ( $calc_listen == '' ) {
			$calc_listen = 'ninja-forms-field-calc-listen';
		}
	}

	// Check to see if we are dealing with a field that has the user_info_field_group set.
	if ( isset ( $data['user_info_field_group_name'] ) and $data['user_info_field_group_name'] != '' ) {
		$user_info_group_class = $data['user_info_field_group_name'].'-address';
	} else {
		$user_info_group_class = '';
	}

	// Check to see if we are dealing with an address field.
	if ( isset ( $data['user_address_1'] ) and $data['user_address_1'] == 1 ) {
		$address_class = 'address address1';
	}	

	if ( isset ( $data['user_address_2'] ) and $data['user_address_2'] == 1 ) {
		$address_class = 'address address2';
	}	

	if ( isset ( $data['user_city'] ) and $data['user_city'] == 1 ) {
		$address_class = 'address city';
	}	

	if ( isset ( $data['user_state'] ) and $data['user_state'] == 1 ) {
		$address_class = 'address state';
	}

	if ( isset ( $data['user_city'] ) and $data['user_city'] == 1 ) {
		$address_class = 'address city';
	}

	$listen_class = '';
	if(isset($watch_fields[$field_id]) AND $watch_fields[$field_id] == 1){
		$listen_class = "ninja-forms-field-conditional-listen";
	}

	if($req_class != ''){
		$field_class .= " ".$req_class;
	}

	if($custom_class != ''){
		$field_class .= " ".$custom_class;
	}

	if($listen_class != ''){
		$field_class .= " ".$listen_class;
	}

	if ( $calc_listen != '' ) {
		$field_class .= ' '.$calc_listen;
	}

	if ( $user_info_group_class != '' ) {
		$field_class .=" ".$user_info_group_class;
	}
	
	if ( $address_class != '' ) {
		$field_class .=" ".$address_class;
	}

	return apply_filters( 'ninja_forms_display_field_class', $field_class, $field_id );
}
<?php
add_action( 'wp_ajax_ninja_forms_save_metabox_state', 'ninja_forms_save_metabox_state' );
function ninja_forms_save_metabox_state(){
	$plugin_settings = nf_get_settings();
	$page = esc_html( $_REQUEST['page'] );
	$tab = esc_html( $_REQUEST['tab'] );
	$slug = esc_html( $_REQUEST['slug'] );
	$state = esc_html( $_REQUEST['state'] );
	$plugin_settings['metabox_state'][$page][$tab][$slug] = $state;
	update_option( 'ninja_forms_settings', $plugin_settings );
	//$plugin_settings = nf_get_settings();
	//echo "SETTING: ".$plugin_settings['metabox_state'][$page][$tab][$slug];
	die();
}

add_action('wp_ajax_ninja_forms_new_field', 'ninja_forms_new_field');
function ninja_forms_new_field(){
	global $wpdb, $ninja_forms_fields;

	$type = esc_html( $_REQUEST['type'] );
	$form_id = absint( $_REQUEST['form_id'] );

	if( isset( $ninja_forms_fields[$type]['name'] ) ){
		$type_name = $ninja_forms_fields[$type]['name'];
	}else{
		$type_name = '';
	}

	if( isset( $ninja_forms_fields[$type]['default_label'] ) ){
		$default_label = $ninja_forms_fields[$type]['default_label'];
	}else{
		$default_label = '';
	}

	if( isset( $ninja_forms_fields[$type]['edit_options'] ) ){
		$edit_options = $ninja_forms_fields[$type]['edit_options'];
	}else{
		$edit_options = '';
	}

	if ( $default_label != '' ) {
		$label = $default_label;
	} else {
		$label = $type_name;
	}

	$input_limit_msg = __( 'character(s) left', 'ninja-forms' );

	$data = serialize( array( 'label' => $label, 'input_limit_msg' => $input_limit_msg ) );

	$order = 999;

	if($form_id != 0 AND $form_id != ''){
		$args = array(
			'type' => $type,
			'data' => $data,
		);

		$new_id = ninja_forms_insert_field( $form_id, $args );
		$new_html = ninja_forms_return_echo('ninja_forms_edit_field', $new_id);
		header("Content-type: application/json");
		$array = array ('new_id' => $new_id, 'new_type' => $type_name, 'new_html' => $new_html, 'edit_options' => $edit_options, 'new_type_slug' => $type );
		echo json_encode($array);
		die();
	}
}

add_action('wp_ajax_ninja_forms_remove_field', 'ninja_forms_remove_field');
function ninja_forms_remove_field(){
	global $wpdb;
	$field_id = absint( $_REQUEST['field_id'] );
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id));
	die();
}

add_action('wp_ajax_ninja_forms_delete_form', 'ninja_forms_delete_form');
function ninja_forms_delete_form( $form_id = '' ){
	global $wpdb;
	if( $form_id == '' ){
		$ajax = true;
		$form_id = absint( $_REQUEST['form_id'] );
	}else{
		$ajax = false;
	}

	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_TABLE_NAME." WHERE id = %d", $form_id));
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = %d", $form_id));

	if( $ajax ){
		die();
	}

}

add_action('wp_ajax_ninja_forms_add_list_option', 'ninja_forms_add_list_options');
function ninja_forms_add_list_options(){
	global $wpdb;
	$field_id = absint( $_REQUEST['field_id'] );
	$x = absint( $_REQUEST['x'] );
	$hidden_value = esc_html( $_REQUEST['hidden_value'] );
	ninja_forms_field_list_option_output($field_id, $x, '', $hidden_value);
	die();
}

add_action('wp_ajax_ninja_forms_insert_fav', 'ninja_forms_insert_fav');
function ninja_forms_insert_fav(){
	global $wpdb, $ninja_forms_fields;
	$fav_id = absint( $_REQUEST['fav_id'] );
	$form_id = absint( $_REQUEST['form_id'] );

	$fav_row = ninja_forms_get_fav_by_id($fav_id);

	$data = serialize($fav_row['data']);
	$type = $fav_row['type'];
	$type_name = $ninja_forms_fields[$type]['name'];

	if($form_id != 0 AND $form_id != ''){
		$args = array(
			'type' => $type,
			'data' => $data,
			'fav_id' => $fav_id,
		);
		$new_id = ninja_forms_insert_field( $form_id, $args );
		$new_html = ninja_forms_return_echo('ninja_forms_edit_field', $new_id);
		header("Content-type: application/json");
		$array = array ('new_id' => $new_id, 'new_type' => $type_name, 'new_html' => $new_html);
		echo json_encode($array);
	}
	die();
}

add_action('wp_ajax_ninja_forms_insert_def', 'ninja_forms_insert_def');
function ninja_forms_insert_def(){
	global $wpdb, $ninja_forms_fields;
	$def_id = absint( $_REQUEST['def_id'] );
	$form_id = absint( $_REQUEST['form_id'] );

	$def_row = ninja_forms_get_def_by_id($def_id);

	$data = serialize($def_row['data']);
	$type = $def_row['type'];
	$type_name = $ninja_forms_fields[$type]['name'];

	if($form_id != 0 AND $form_id != ''){
		$args = array(
			'type' => $type,
			'data' => $data,
			'def_id' => $def_id,
		);
		$new_id = ninja_forms_insert_field( $form_id, $args );
		$new_html = ninja_forms_return_echo('ninja_forms_edit_field', $new_id);
		header("Content-type: application/json");
		$array = array ('new_id' => $new_id, 'new_type' => $type_name, 'new_html' => $new_html);
		echo json_encode($array);
	}
	die();
}

add_action('wp_ajax_ninja_forms_add_fav', 'ninja_forms_add_fav');
function ninja_forms_add_fav(){
	global $wpdb;

	$field_data = $_REQUEST['field_data'];
	$field_id = absint( $_REQUEST['field_id'] );

	$field_row = ninja_forms_get_field_by_id($field_id);

	$field_type = $field_row['type'];
	$form_id = 1;

	$data = array();

	foreach($field_data as $key => $val){
		$key = stripslashes( $key );
		$key = str_replace('"', '', $key);
		if(strpos($key, '[')){
			$key = str_replace(']', '', $key);
			$key = explode('[', $key);
			$multi = array();
			$temp  =& $multi;
			$x = 0;
			$count = count($key) - 1;
			foreach ($key as $item){
				$temp[$item] = array();
				if($x < $count){
					$temp =& $temp[$item];
				}else{
					$temp[$item] = $val;
				}
				$x++;
			}
			$data = ninja_forms_array_merge_recursive($data, $multi);
		}else{
			$data[$key] = $val;
		}
	}

	$name = stripslashes( esc_html( $_REQUEST['fav_name'] ) );
	if ( !isset ( $data['label'] ) or empty ( $data['label'] ) ) {
		$data['label'] = $name;		
	}

	$data = ninja_forms_stripslashes_deep( $data );

	$data = serialize($data);
	$wpdb->insert(NINJA_FORMS_FAV_FIELDS_TABLE_NAME, array('row_type' => 1, 'type' => $field_type, 'order' => 0, 'data' => $data, 'name' => $name));
	$fav_id = $wpdb->insert_id;
	$update_array = array('fav_id' => $fav_id);
	$wpdb->update( NINJA_FORMS_FIELDS_TABLE_NAME, $update_array, array( 'id' => $field_id ));

	$new_html = '<p class="button-controls" id="ninja_forms_insert_fav_field_'.$fav_id.'_p">
				<a class="button add-new-h2 ninja-forms-insert-fav-field" id="ninja_forms_insert_fav_field_'.$fav_id.'" name=""  href="#">'.__($name, 'ninja-forms').'</a>
			</p>';

	header("Content-type: application/json");
	$array = array ('fav_id' => $fav_id, 'fav_name' => $name, 'link_html' => $new_html);
	echo json_encode($array);

	die();
}

add_action('wp_ajax_ninja_forms_add_def', 'ninja_forms_add_def');
function ninja_forms_add_def(){
	global $wpdb;
	$field_data = $_REQUEST['field_data'];
	$field_id = absint( $_REQUEST['field_id'] );

	$field_row = ninja_forms_get_field_by_id($field_id);

	$field_type = $field_row['type'];
	$row_type = 0;

	$data = array();

	foreach($field_data as $key => $val){
		$key = str_replace('"', '', $key);
		if(strpos($key, '[')){
			$key = str_replace(']', '', $key);
			$key = explode('[', $key);
			$multi = array();
			$temp  =& $multi;
			$x = 0;
			$count = count($key) - 1;
			foreach ($key as $item){
				$temp[$item] = array();
				if($x < $count){
					$temp =& $temp[$item];
				}else{
					$temp[$item] = $val;
				}
				$x++;
			}
			$data = ninja_forms_array_merge_recursive($data, $multi);
		}else{
			$data[$key] = $val;
		}
	}

	$name = stripslashes( esc_html( $_REQUEST['def_name'] ) );
	$data['label'] = $name;
	$data = serialize($data);
	$wpdb->insert(NINJA_FORMS_FAV_FIELDS_TABLE_NAME, array('row_type' => $row_type, 'type' => $field_type, 'data' => $data, 'name' => $name));
	$def_id = $wpdb->insert_id;
	$update_array = array('def_id' => $def_id);
	$wpdb->update( NINJA_FORMS_FIELDS_TABLE_NAME, $update_array, array( 'id' => $field_id ));

	$new_html = '<p class="button-controls" id="ninja_forms_insert_def_field_'.$def_id.'_p">
				<a class="button add-new-h2 ninja-forms-insert-def-field" id="ninja_forms_insert_def_field_'.$def_id.'" name=""  href="#">'.__($name, 'ninja-forms').'</a>
			</p>';
	header("Content-type: application/json");
	$array = array ('def_id' => $def_id, 'def_name' => $name, 'link_html' => $new_html);
	echo json_encode($array);

	die();
}

add_action('wp_ajax_ninja_forms_remove_fav', 'ninja_forms_remove_fav');
function ninja_forms_remove_fav(){
	global $wpdb, $ninja_forms_fields;

	$field_id = absint( $_REQUEST['field_id'] );
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$fav_id = $field_row['fav_id'];
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $fav_id));
	$wpdb->update(NINJA_FORMS_FIELDS_TABLE_NAME, array('fav_id' => '' ), array('fav_id' => $fav_id));
	$type_name = $ninja_forms_fields[$field_type]['name'];
	header("Content-type: application/json");
	$array = array ('fav_id' => $fav_id, 'type_name' => $type_name);
	echo json_encode($array);

	die();
}

add_action('wp_ajax_ninja_forms_remove_def', 'ninja_forms_remove_def');
function ninja_forms_remove_def(){
	global $wpdb, $ninja_forms_fields;

	$field_id = absint( $_REQUEST['field_id'] );
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$def_id = $field_row['def_id'];
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $def_id));
	$wpdb->update(NINJA_FORMS_FIELDS_TABLE_NAME, array('def_id' => '' ), array('def_id' => $def_id));
	$type_name = $ninja_forms_fields[$field_type]['name'];
	header("Content-type: application/json");
	$array = array ('def_id' => $def_id, 'type_name' => $type_name);
	echo json_encode($array);

	die();
}

add_action( 'wp_ajax_ninja_forms_side_sortable', 'ninja_forms_side_sortable' );
function ninja_forms_side_sortable(){
	$plugin_settings = nf_get_settings();
	$page = esc_html( $_REQUEST['page'] );
	$tab = esc_html( $_REQUEST['tab'] );
	$order = ninja_forms_esc_html_deep( $_REQUEST['order'] );

	$plugin_settings['sidebars'][$page][$tab] = $order;
	update_option( 'ninja_forms_settings', $plugin_settings );

	die();
}

add_action('wp_ajax_ninja_forms_view_sub', 'ninja_forms_view_sub');
function ninja_forms_view_sub(){
	global $ninja_forms_fields;
	/*
	$plugin_settings = nf_get_settings();
	if(isset($plugin_settings['date_format'])){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = 'm/d/Y';
	}
	$sub_id = $_REQUEST['sub_id'];
	$sub_row = ninja_forms_get_sub_by_id($sub_id);
	$data = $sub_row['data'];

	$date_updated = strtotime($sub_row['date_updated']);
	$date_updated = date($date_format, $date_updated);
	*/
	$new_html = '<input type="hidden" id="ninja_forms_sub_id" value="'.$sub_id.'">';
	/*
	foreach($data as $field_id => $user_value){
		$new_html .= '<div id="" name="" class="description description-wide">';
		$field_row = ninja_forms_get_field_by_id($field_id);
		if(isset($field_row['data']['label'])){
			$field_label = $field_row['data']['label'];
		}else{
			$field_label = '';
		}
		$field_type = $field_row['type'];
		if($ninja_forms_fields[$field_type]['process_field']){
			$sub_edit = $ninja_forms_fields[$field_type]['sub_edit'];
			$user_value2 = '';
			if(is_array($user_value)){
				$x = 1;
				foreach($user_value as $val){
					$user_value2 .= esc_html(stripslashes($val));
					if($x != count($user_value)){
						$user_value2 .= ",";
					}
					$x++;
				}
			}else{
				$user_value2 = esc_html(stripslashes($user_value));
			}
			$new_html .= '<label for="ninja_forms_field_'.$field_id.'">'.$field_label;
			if($sub_edit == 'text'){
				$new_html .= '<input type="text" id="ninja_forms_field_'.$field_id.'" name="" value="'.$user_value2.'" class="code widefat">';
			}else if($sub_edit == 'textarea'){

			}
			$new_html .= '</label>';
			$new_html .= '</div>';
		}
	}
	*/
	header("Content-type: application/json");
	$array = array('new_html' => $new_html);
	echo json_encode($array);
	//echo "hello world";
	die();
}

add_action('wp_ajax_ninja_forms_edit_sub', 'ninja_forms_edit_sub');
function ninja_forms_edit_sub(){
	global $wpdb;
	$sub_id = absint( $_REQUEST['sub_id'] );
	$sub_data = $_REQUEST['sub_data'];

	$args = array(
		'sub_id' => $sub_id,
		'data' => $sub_data,
	);

	ninja_forms_update_sub($args);
	die();
}

add_action('wp_ajax_ninja_forms_delete_sub', 'ninja_forms_delete_sub');
//add_action('wp_ajax_nopriv_ninja_forms_delete_sub', 'ninja_forms_delete_sub');
function ninja_forms_delete_sub($sub_id = ''){
	global $wpdb;
	if($sub_id == ''){
		$ajax = true;
		$sub_id = absint( $_REQUEST['sub_id'] );
	}else{
		$ajax = false;
	}

	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE id = %d", $sub_id));
	if( $ajax ){
		die();
	}
}

add_action('wp_ajax_ninja_forms_ajax_submit', 'ninja_forms_ajax_submit');
add_action('wp_ajax_nopriv_ninja_forms_ajax_submit', 'ninja_forms_ajax_submit');
function ninja_forms_ajax_submit(){
	global $ninja_forms_processing;
	//add_action( 'init', 'test' );
	//add_action( 'init', 'ninja_forms_setup_processing_class', 5 );
	//add_action( 'init', 'ninja_forms_pre_process', 999 );
	//ninja_forms_setup_processing_class();
	//ninja_forms_pre_process();
	//die();
}

function ninja_forms_array_merge_recursive() {
	$arrays = func_get_args();
	$base = array_shift($arrays);

	foreach ($arrays as $array) {
		reset($base); //important
		while (list($key, $value) = @each($array)) {
			if (is_array($value) && @is_array($base[$key])) {
				$base[$key] = ninja_forms_array_merge_recursive($base[$key], $value);
			} else {
				$base[$key] = $value;
			}
		}
	}

	return $base;
}

function ninja_forms_import_list_options(){
	$options = $_REQUEST['options'];
	$field_id = absint( $_REQUEST['field_id'] );
	$options = str_replace('\,', '-comma-replace-placeholder-', $options );
	$options = ninja_forms_csv_explode( $options );

	if( is_array( $options ) ){
		$tmp_array = array();
		$x = 0;
		foreach( $options as $option ){
			$label = stripslashes( $option[0] );
			$value = stripslashes( $option[1] );
			$calc = stripslashes( $option[2] );
			$label = str_replace( "''", "", $label );
			$label = str_replace( "-comma-replace-placeholder-", ",", $label );
			$value = str_replace( "''", "", $value );
			$value = str_replace( "-comma-replace-placeholder-", ",", $value );
			$calc = str_replace( "''", "", $calc );
			$calc = str_replace( "-comma-replace-placeholder-", ",", $calc );
			$tmp_array[$x]['label'] = $label;
			$tmp_array[$x]['value'] = $value;
			$tmp_array[$x]['calc'] = $calc;
			$x++;
		}
		$x = 0;
		foreach( $tmp_array as $option ){
			$hidden = 0;
			ninja_forms_field_list_option_output($field_id, $x, $option, $hidden);
			$x++;
		}
	}

	die();
}

add_action( 'wp_ajax_ninja_forms_import_list_options', 'ninja_forms_import_list_options' );

/*
 *
 * Function that outputs a list of terms so that the user can exclude terms from a list selector.
 *
 * @since 2.2.51
 * @return void
 */

function ninja_forms_list_terms_checkboxes( $field_id = '', $tax_name = '' ){
	if ( $field_id == '' AND isset ( $_POST['field_id'] ) ) {
		$field_id = absint( $_POST['field_id'] );
	}	

	if ( $tax_name == '' AND isset ( $_POST['tax_name'] ) ) {
		$tax_name = esc_html( $_POST['tax_name'] );
	}

	if ( $field_id != '' AND $tax_name != '' ) {
		$field = ninja_forms_get_field_by_id( $field_id );
		if ( isset ( $field['data']['exclude_terms'] ) ) {
			$exclude_terms = $field['data']['exclude_terms'];
		} else {
			$exclude_terms = '';
		}

		$terms = get_terms( $tax_name, array( 'hide_empty' => false ) );
		if ( is_array ( $terms ) AND !empty ( $terms ) ) {
			?>
			<h4><?php _e( 'Do not show these terms', 'ninja-forms' );?>:</h4>
            <input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[exclude_terms]" value="">
			<?php
			foreach ( $terms as $term ) {
				?>
				<div>
					<label>
						<input type="checkbox" <?php checked( in_array ( $term->term_id, $exclude_terms ), true );?> name="ninja_forms_field_<?php echo $field_id;?>[exclude_terms][]" value="<?php echo $term->term_id;?>">
						<?php echo $term->name;?>
					</label>
				</div>
				<?php
			}
		}
	}

	if ( isset ( $_POST['from_ajax'] ) AND absint( $_POST['from_ajax'] ) == 1 ) {
		die();
	}
}

add_action( 'wp_ajax_ninja_forms_list_terms_checkboxes', 'ninja_forms_list_terms_checkboxes' );

/*
 *
 * Function that outputs a calculation row
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_add_calc_row(){
	$field_id = absint( $_REQUEST['field_id'] );
	$c = array( 'calc' => '', 'operator' => 'add', 'value' => '', 'when' => '' );
	$x = absint( $_REQUEST['x'] );

	ninja_forms_output_field_calc_row( $field_id, $c, $x );
	die();
}

add_action( 'wp_ajax_ninja_forms_add_calc_row', 'ninja_forms_add_calc_row' );

/**
 * 
 * Covert a multi-line CSV string into a 2d array. Follows RFC 4180, allows
 * "cells with ""escaped delimiters""" and multi-line enclosed cells
 * It assumes the CSV file is properly formatted, and doesn't check for errors
 * in CSV format.
 * @param string $str The CSV string
 * @param string $d The delimiter between values
 * @param string $e The enclosing character
 * @param bool $crlf Set to true if your CSV file should return carriage return
 * 						and line feed (CRLF should be returned according to RFC 4180
 * @return array 
 */
function ninja_forms_csv_explode( $str, $d=',', $e='"', $crlf=TRUE ) {
	// Convert CRLF to LF, easier to work with in regex
	if( $crlf ) $str = str_replace("\r\n","\n",$str);
	// Get rid of trailing linebreaks that RFC4180 allows
	$str = trim($str);
	// Do the dirty work
	if ( preg_match_all(
		'/(?:
			'.$e.'((?:[^'.$e.']|'.$e.$e.')*+)'.$e.'(?:'.$d.'|\n|$)
				# match enclose, then match either non-enclose or double-enclose
				# zero to infinity times (possesive), then match another enclose,
				# followed by a comma, linebreak, or string end
			|	####### OR #######
			([^'.$d.'\n]*+)(?:['.$d.'\n]|$)
				# match anything thats not a comma or linebreak zero to infinity
				# times (possesive), then match either a comma or a linebreak or
				# string end
		)/x', 
		$str, $ms, PREG_SET_ORDER
	) === FALSE ) return FALSE;
	// Initialize vars, $r will hold our return data, $i will track which line we're on
	$r = array(); $i = 0;
	// Loop through results
	foreach( $ms as $m ) {
		// If the first group of matches is empty, the cell has no quotes
		if( empty($m[1]) )
			// Put the CRLF back in if needed
			$r[$i][] = ($crlf == TRUE) ? str_replace("\n","\r\n",$m[2]) : $m[2];
		else {
			// The cell was quoted, so we want to convert any "" back to " and
			// any LF back to CRLF, if needed
			$r[$i][] = ($crlf == TRUE) ?
				str_replace(
					array("\n",$e.$e),
					array("\r\n",$e),
					$m[1]) :
				str_replace($e.$e, $e, $m[1]);
		}
		// If the raw match doesn't have a delimiter, it must be the last in the
		// row, so we increment our line count.
		if( substr($m[0],-1) != $d )
			$i++;
	}
	// An empty array will exist due to $ being a zero-length match, so remove it
	array_pop( $r );
	return $r;

}
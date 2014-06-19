<?php
/**
 * Outputs any error messages with the location of field_id.
**/
add_action('init', 'ninja_forms_register_field_processing_error');
function ninja_forms_register_field_processing_error(){
	add_action('ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_processing_error');
}

function ninja_forms_display_field_processing_error( $field_id ){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing)){

		$field_errors = $ninja_forms_processing->get_errors_by_location($field_id);
		if ( $field_errors ) {
			$style = '';
		} else {
			$style = 'display:none;';
		}
		
	}else{
		$field_errors = '';
		$style = 'display:none;';
	}

	?>
	<div id="ninja_forms_field_<?php echo $field_id;?>_error" style="<?php echo $style;?>" class="ninja-forms-field-error">
	<?php
	if(is_array($field_errors)){
		foreach($field_errors as $error){
			echo '<p>'.$error['msg'].'</p>';
		}
	}
	?>
	</div>
	<?php
}
<?php
add_action( 'ninja_forms_edit_field_ul', 'ninja_forms_edit_field_output_ul' );
function ninja_forms_edit_field_output_ul( $form_id ){
	$fields = ninja_forms_get_fields_by_form_id( $form_id );
	?>
	<div id="ninja-forms-viewport">
		<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e('Save Field Settings', 'ninja-forms'); ?>" />

		<ul class="menu ninja-forms-field-list" id="ninja_forms_field_list">
	  		<?php
				if( is_array( $fields ) AND !empty( $fields ) ){
					foreach( $fields as $field ){
						ninja_forms_edit_field( $field['id'] );
					}
				}
			?>
		</ul>
	</div>
	<?php
}
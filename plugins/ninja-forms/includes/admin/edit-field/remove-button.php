<?php
add_action( 'ninja_forms_edit_field_before_closing_li', 'ninja_forms_edit_field_remove_button' );
function ninja_forms_edit_field_remove_button( $field_id ){
	?>
	<div class="menu-item-actions description-wide submitbox">
		<a class="submitdelete deletion ninja-forms-field-remove" id="ninja_forms_field_<?php echo $field_id;?>_remove" name="" href="#"><?php _e('Remove', 'ninja-forms'); ?></a>
	</div>
	<?php
}
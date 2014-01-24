<?php

/*
 *
 * This field handles post status
 *
 * @since 2.2.51
 * @return void
 */

// For backwards compatibilty, make sure that this function doesn't already exist.
if ( !function_exists ( 'ninja_forms_register_field_post_status' ) ) {
	function ninja_forms_register_field_post_status(){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;

		$args = array(
			'name' => 'Status',
			'display_function' => 'ninja_forms_field_post_status_display',		
			'group' => 'create_post',	
			'edit_label' => true,
			'edit_label_pos' => true,
			'edit_req' => true,
			'edit_custom_class' => true,
			'edit_help' => true,
			'edit_meta' => false,
			'sidebar' => 'post_fields',
			'edit_conditional' => true,
			'conditional' => array(
				'value' => array(
					'type' => 'list',
				),
			),
			'limit' => 1,
			//'save_sub' => false,
			'pre_process' => 'ninja_forms_field_post_status_pre_process',
		);

		if( function_exists( 'ninja_forms_register_field' ) ){
			ninja_forms_register_field('_post_status', $args);
		}
	}

	add_action( 'init', 'ninja_forms_register_field_post_status' );

	function ninja_forms_field_post_status_pre_process( $field_id, $user_value ){
		global $ninja_forms_processing;
		$ninja_forms_processing->update_form_setting( 'post_status', $user_value );
	}

	function ninja_forms_field_post_status_display( $field_id, $data ){
		global $post, $ninja_forms_processing;

		if ( isset ( $data['default_value'] ) ) {
			$default_value = $data['default_value'];
		} else {
			$default_value = '';
		}

		if( is_object( $post ) and $default_value == '' ){
			$default_value = $post->post_status;
		}
		?>
		<select name="ninja_forms_field_<?php echo $field_id;?>"  rel="<?php echo $field_id;?>" >
			<option value="draft" <?php selected( $default_value, 'draft' );?>>Draft</option>
			<option value="pending" <?php selected( $default_value, 'pending' );?>>Pending</option>
			<option value="publish" <?php selected( $default_value, 'publish' );?>>Published</option>
		</select>
		<?php
	}
}
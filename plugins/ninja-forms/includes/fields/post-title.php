<?php
/*
 *
 * This field handles post title
 *
 * @since 2.2.51
 * @return void
 */

// For backwards compatibilty, make sure that this function doesn't already exist.
if ( !function_exists ( 'ninja_forms_register_field_post_title' ) ) {
	function ninja_forms_register_field_post_title(){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;
		
		$args = array(
			'name' => 'Title',
			//'edit_function' => 'ninja_forms_field_checkbox_edit',
			'display_function' => 'ninja_forms_field_post_title_display',		
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
					'type' => 'text',
				),
			),
			'limit' => 1,
			//'save_sub' => false,
			'pre_process' => 'ninja_forms_field_post_title_pre_process',
		);
		if( function_exists( 'ninja_forms_register_field' ) ){
			ninja_forms_register_field('_post_title', $args);
		}

		add_action( 'ninja_forms_pre_process', 'ninja_forms_post_title_do_shortcode' , 20 );
	}

	add_action('init', 'ninja_forms_register_field_post_title');

	function ninja_forms_field_post_title_display($field_id, $data){
		$field_class = ninja_forms_get_field_class($field_id);
		
		if(isset($data['default_value'])){
			$default_value = $data['default_value'];
		}else{
			$default_value = '';
		}
		
		if(isset($data['label_pos'])){
			$label_pos = $data['label_pos'];
		}else{
			$label_pos = "left";
		}

		if(isset($data['label'])){
			$label = $data['label'];
		}else{
			$label = '';
		}
		
		if ( $label_pos == 'inside' AND $default_value == '' ) {
			$default_value = $label;
		}	
		
		?>
		<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>"  />
		<?php
	}

	function ninja_forms_field_post_title_pre_process($field_id, $user_value){
		global $ninja_forms_processing;

		$ninja_forms_processing->update_form_setting('post_title', $user_value);
	}

	function ninja_forms_post_title_do_shortcode(){
		global $ninja_forms_processing;

		$post_title = $ninja_forms_processing->get_form_setting( 'post_title' );
		$post_title = do_shortcode( $post_title );
		$ninja_forms_processing->update_form_setting( 'post_title', $post_title );
	}
}
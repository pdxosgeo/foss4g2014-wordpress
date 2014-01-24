<?php

/*
 *
 * This field handles post content
 *
 * @since 2.2.51
 * @return void
 */

// For backwards compatibilty, make sure that this function doesn't already exist.

if ( !function_exists ( 'ninja_forms_register_field_post_content') ) {
	function ninja_forms_register_field_post_content(){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;
		
		$args = array(
			'name' => 'Content',
			'edit_options' => array(
				array(
					'type' => 'checkbox', //What type of input should this be?
					'name' => 'content_rte', //What should it be named. This should always be a programmatic name, not a label.
					'label' => __('Show Rich Text Editor?', 'ninja-forms'),
					'width' => 'wide',
					//'class' => 'widefat', //Additional classes to be added to the input element.
				),
				array(
					'type' => 'checkbox',
					'name' => 'content_media',
					'label' => __( 'Show Media Upload Button', 'ninja-forms' ),
				),		
				array(
					'type' => 'rte', //What type of input should this be?
					'name' => 'default_value', //What should it be named. This should always be a programmatic name, not a label.
					'label' => __('Default Value', 'ninja-forms'),
					'width' => 'wide',
					'class' => 'widefat', //Additional classes to be added to the input element.
				),
			),
			'display_function' => 'ninja_forms_field_post_content_display',		
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
			'pre_process' => 'ninja_forms_field_post_content_pre_process',
		);

		if( function_exists( 'ninja_forms_register_field' ) ){
			ninja_forms_register_field('_post_content', $args);
		}

		add_action( 'ninja_forms_pre_process', 'ninja_forms_post_content_pre_process' , 9 );
		add_action( 'ninja_forms_pre_process', 'ninja_forms_post_content_do_shortcode' , 20 );
	}

	add_action('init', 'ninja_forms_register_field_post_content');


	function ninja_forms_field_post_content_display($field_id, $data){
		if(isset($data['default_value'])){
			$default_value = $data['default_value'];
		}else{
			$default_value = '';
		}

		$default_value = htmlspecialchars_decode( $default_value );

		if(isset($data['content_rte'])){
			$content_rte = $data['content_rte'];
		}else{
			$content_rte = 0;
		}

		if( isset ( $data['content_media'] ) AND $data['content_media'] == 1 ){
			$content_media = true;
		}else{
			$content_media = false;
		}

		if(isset($data['class'])){
			$class = $data['class'];
		}else{
			$class = '';
		}

		if($content_rte == 1){
			$settings = array( 'media_buttons' => $content_media );
			$args = apply_filters( 'ninja_forms_content_rte', $settings );
			wp_editor( $default_value, 'ninja_forms_field_'.$field_id, $args );
		}else{
			?>
			<textarea name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $class;?>" rel="<?php echo $field_id;?>" ><?php echo $default_value;?></textarea>
			<?php
		}
	}

	function ninja_forms_post_content_pre_process(){
		global $ninja_forms_processing;

		$post_content = $ninja_forms_processing->get_form_setting( 'post_content' );
		
		if($post_content != ''){
			
			//Loop through each submitted form field and replace any instances of [label].
			if($ninja_forms_processing->get_all_fields()){
				foreach($ninja_forms_processing->get_all_fields() as $key => $val){
					$field_row = ninja_forms_get_field_by_id($key);
					$data = $field_row['data'];
					$label = $data['label'];

					$value = '';
					if(is_array($val) AND !empty($val)){
						$x = 0;
						foreach($val as $v){
							if(!is_array($v)){
								$value .= $v;
								if($x != count($val)){
									$value .= ',';
								}
							}
							$x++;
						}
					}else{
						$value = $val;
					}
					$post_content = str_replace('['.$label.']', $value, $post_content);
				}
			}
			
		}
		
		$ninja_forms_processing->update_form_setting('post_content', $post_content);
	}

	function ninja_forms_field_post_content_pre_process( $field_id, $user_value ){
		global $ninja_forms_processing;

		$user_value = $_POST['ninja_forms_field_'.$field_id];

		$post_content = $ninja_forms_processing->get_form_setting( 'post_content' );

		if($ninja_forms_processing->get_form_setting('post_content_location') == 'append'){
			$post_content = $user_value.$post_content;
		}else{
			$post_content = $post_content.$user_value;
		}

		$ninja_forms_processing->update_form_setting('post_content', $post_content);
	}

	function ninja_forms_post_content_do_shortcode(){
		global $ninja_forms_processing, $shortcode_tags;

		$post_content = $ninja_forms_processing->get_form_setting( 'post_content' );
		$current_shortcodes = $shortcode_tags;
		$shortcode_tags = array();
		add_shortcode( 'ninja_forms_field', 'ninja_forms_field_shortcode' );
		// Do the shortcode (only the one above is registered)
		$post_content = do_shortcode( $post_content );
		// Put the original shortcodes back
		$shortcode_tags = $current_shortcodes;

		$ninja_forms_processing->update_form_setting( 'post_content', $post_content );
	}
}
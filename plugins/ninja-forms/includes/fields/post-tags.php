<?php
/*
 *
 * This field handles post tags
 *
 * @since 2.2.51
 * @return void
 */

// For backwards compatibilty, make sure that this function doesn't already exist.
if ( !function_exists ( 'ninja_forms_register_field_post_tags' ) ) {
	function ninja_forms_register_field_post_tags(){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;
		
		$args = array(
			'name' => 'Tags',
			//'edit_function' => 'ninja_forms_field_checkbox_edit',
			'edit_options' => array(
				array(
					'name' => 'adv_tags',
					'type' => 'checkbox',
					'label' => __( 'Show Advanced Tag Selector', 'ninja-forms' ),
					'default' => 1,
				),
			),
			'display_function' => 'ninja_forms_field_post_tags_display',		
			'group' => 'create_post',	
			'edit_label' => true,
			'edit_label_pos' => false,
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
			'pre_process' => 'ninja_forms_field_post_tags_pre_process',
		);

		if( function_exists( 'ninja_forms_register_field' ) ){
			ninja_forms_register_field('_post_tags', $args);
		}
	}

	add_action( 'init', 'ninja_forms_register_field_post_tags' );

	/*
	 *
	 * Function that prevents help text from being shown after the tags field element.
	 *
	 * @since 0.8
	 * @returns array $data
	 */

	function ninja_forms_field_post_tags_filter_field( $data, $field_id ){
		$field = ninja_forms_get_field_by_id( $field_id );
		$field_type = $field['type'];
		if ( isset ( $data['show_help'] ) ) {
			$data['tags_show_help'] = $data['show_help'];
		} else { 
			$data['tags_show_help'] = 0;
		}
		if ( $field_type == '_post_tags' ) {
			$data['show_help'] = 0;	
		}
		
		return $data;
	}

	add_filter( 'ninja_forms_field', 'ninja_forms_field_post_tags_filter_field', 10, 2 );

	function ninja_forms_field_post_tags_display($field_id, $data){
		global $ninja_forms_processing;

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
		
		if(isset($data['adv_tags'])){
			$adv_tags = $data['adv_tags'];
		}else{
			$adv_tags = 0;
		}

		if($label_pos == 'inside'){
			$default_value = $label;
		}

		if($default_value == ''){
			if( is_object( $ninja_forms_processing)){
				$post_tags = $ninja_forms_processing->get_form_setting('post_tags');
			}else{
				$form_row = ninja_forms_get_form_by_field_id($field_id);
				$post_tags = $form_row['data']['post_tags'];
			}
		}else{
			$post_tags = $default_value;
		}

		if($post_tags){
			$post_tags = explode(',', $post_tags);
		}

		if($adv_tags == 1){
			$string_tag = '';
		
			if(is_array( $post_tags ) AND !empty( $post_tags ) ){
				for ($x=0; $x < count( $post_tags ) - 1 ; $x++) { 
					if ( $post_tags[$x] == '' ) {
						unset( $post_tags[$x] );
					}
				}
				$x = 0;
				foreach( $post_tags as $tag ){
					if(is_object($tag)){
						$tag_name = $tag->name;
					}else{
						$tag_name = $tag;
					}
					if($x > 0){
						$string_tag .= ', ';
					}
					$string_tag .= $tag_name;
					$x++;
				}
			}
			$data['show_help'] = $data['tags_show_help'];
			do_action( 'ninja_forms_display_field_help', $field_id, $data );

			?>
			<div class="tagsdiv" id="post_tag">
				<div class="jaxtag">
					<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_post_tag_hidden" value="<?php echo $string_tag;?>" rel="<?php echo $field_id;?>" >
		 			<div class="ajaxtag hide-if-no-js">
						<label class="screen-reader-text" for="new-tag-post_tag"><?php _e( 'Tags', 'ninja_forms' );?></label>
						<div class="taghint" style=""><?php _e( 'Add New Tag', 'ninja-forms' );?></div>
						<p><input type="text" id="ninja_forms_post_tag" class="newtag form-input-tip" size="16" autocomplete="off" value="">
						<input type="button" id="ninja_forms_post_add_tag" class="button" value="Add"></p>
					</div>
					<p class="howto"><?php _e( 'Separate tags with commas', 'ninja-forms' );?></p>
					</div>
				<div class="tagchecklist">
					<?php
					if(is_array( $post_tags ) AND !empty( $post_tags ) ){
						$x = 0;
						foreach( $post_tags as $tag ){
							if(is_object($tag)){
								$tag_name = $tag->name;
							}else{
								$tag_name = $tag;
							}
							?>
							<span id="<?php echo $tag_name;?>">
								<a id="post_tag-<?php echo $x;?>" class="ninja-forms-del-tag">X</a>&nbsp;<?php echo $tag_name;?>
							</span>
							<?php
							$x++;
						}
					}
					?>
				</div>
			
				<br />
				<a href="#" class="" id="ninja_forms_show_tag_cloud">Choose from the most used tags</a>
			
				<div id="ninja_forms_tag_cloud" style="display:none;">
					<?php
					$args = array(
						'echo' => false,
						'format' => 'array',
					);
					$tag_cloud = wp_tag_cloud($args);
					if( is_array( $tag_cloud ) AND !empty( $tag_cloud ) ){
						foreach( $tag_cloud as $tag ){

							$first_quote = strpos( $tag, "href='");
							$first_quote = $first_quote + 6;
							$second_quote = strpos( $tag, "'", $first_quote );
							$length = $second_quote - $first_quote;
							$url = substr( $tag, $first_quote, $length );
							$tag = str_replace( $url, '#', $tag );

							$first_quote = strpos( $tag, "class='");
							$first_quote = $first_quote + 7;
							$second_quote = strpos( $tag, "'", $first_quote );
							$length = $second_quote - $first_quote;
							$orig_class = substr( $tag, $first_quote, $length );
							$class = $orig_class.' ninja-forms-tag';
							$tag = str_replace( $orig_class, $class, $tag );

							echo $tag." ";
						}
					}
					?>
				</div>
			</div>
			<?php
		}else{
			if( is_array( $post_tags ) ){
				$post_tags = implode( ',', $post_tags );			
			}
			?>
			<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $post_tags;?>" rel="<?php echo $field_id;?>"  />
			<?php
		}
	}

	function ninja_forms_field_post_tags_pre_process($field_id, $user_value){
		global $ninja_forms_processing;

		$ninja_forms_processing->update_form_setting('post_tags', $user_value);
	}
}
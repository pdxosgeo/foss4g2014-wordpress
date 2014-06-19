<?php
/*
 *
 * This field handles post terms
 *
 * @since 2.2.51
 * @return void
 */

// For backwards compatibilty, make sure that this function doesn't already exist.
if ( !function_exists ( 'ninja_forms_register_field_post_terms' ) ) {
	function ninja_forms_register_field_post_terms( $form_id = '' ){
		global $ninja_forms_processing;
		
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;

		$all_taxonomies = get_taxonomies( '','names' );
		
		unset( $all_taxonomies['post_tag'] );
		unset( $all_taxonomies['nav_menu'] );
		unset( $all_taxonomies['link_category'] );
		unset( $all_taxonomies['post_format'] );
		
		if( is_array( $all_taxonomies ) AND !empty( $all_taxonomies) ){
			foreach( $all_taxonomies as $tax ){
				$val = get_taxonomies( array( 'name' => $tax ), 'objects' );
				$val = $val[$tax];
				$args = array(
					'name' => $val->label,
					'edit_options' => array(
						array(
							'name' => 'adv_'.$tax,
							'type' => 'checkbox',
							'label' => __( 'Show advanced term selector', 'ninja-forms' ),
							'default' => 1,
						),
						array(
							'name' => 'add_'.$tax,
							'type' => 'checkbox',
							'label' => __( 'Allow users to create terms?', 'ninja-forms' ),
							'desc' => __( 'Requires advanced term selector', 'ninja-forms' ),
							'default' => 1,
						),
					),
					'display_function' => 'ninja_forms_field_post_terms_display',		
					'group' => 'create_post',	
					'edit_label' => true,
					'edit_label_pos' => true,
					'edit_req' => true,
					'edit_custom_class' => true,
					'edit_help' => true,
					'sidebar' => 'post_fields',
					'edit_conditional' => true,
					'conditional' => array(
						'value' => array(
							'type' => 'text',
						),
					),
					'limit' => 1,
					//'save_sub' => false,
					'pre_process' => 'ninja_forms_field_post_terms_pre_process',
					'tax' => $tax,
				);
				if( function_exists( 'ninja_forms_register_field' ) ){
					ninja_forms_register_field( '_post_'.$tax, $args );
				}
				add_filter( 'ninja_forms_display_field_type', 'ninja_forms_post_field_type', 10, 2 );
			}
		}
	}

	add_action( 'init', 'ninja_forms_register_field_post_terms', 20 );

	function ninja_forms_post_field_type( $type, $field_id ) {
		global $ninja_forms_fields;
		$field_row = ninja_forms_get_field_by_id( $field_id );
		$field_type = $field_row['type'];
		if ( isset ( $ninja_forms_fields[$field_type]['tax'] ) ) {
			$type = 'list';
		}
		return $type;
	}

	function ninja_forms_field_post_terms_display($field_id, $data){
		global $ninja_forms_fields;

		$form_row = ninja_forms_get_form_by_field_id($field_id);
		$form_data = $form_row['data'];
		$field_class = ninja_forms_get_field_class($field_id);
		$field_row = ninja_forms_get_field_by_id( $field_id );
		$field_type = $field_row['type'];
		$post_tax = $ninja_forms_fields[$field_type]['tax'];

		$val = get_taxonomies(array('name' => $post_tax), 'objects');
		$val = $val[$post_tax];

		$post_tax_singular = $val->labels->singular_name;
		$post_tax_name = $val->labels->name;

		if( isset( $data['default_value'] ) ){
			if( isset( $data['default_value']['terms'] ) ){
				$terms = $data['default_value']['terms'];
			}else{
				$terms = $data['default_value'];
			}
		}else{
			if( isset( $form_data[$post_tax.'_terms'] ) ){
				$terms = $form_data[$post_tax.'_terms'];
			}
		}

		if( !isset( $terms ) OR $terms == '' ){
			$terms = array();
		}

		if( isset( $data['adv_'.$post_tax] ) ){
			$adv_term = $data['adv_'.$post_tax];
		}else{
			$adv_term = 0;
		}

		if( isset( $data['add_'.$post_tax] ) ){
			$add_term = $data['add_'.$post_tax];
		}else{
			$add_term = 0;
		}

		if($adv_term == 1){
			//$all_tax = get_object_taxonomies($post->post_type);
			delete_option( $post_tax.'_children' );
			$all_terms = get_categories( array( 'parent' => 0, 'hide_empty' => false, 'taxonomy' => $post_tax ) );
			$all_terms = apply_filters( 'ninja_forms_display_all_terms', $all_terms, $post_tax );
			$pop_terms = get_categories( array( 'parent' => 0, 'orderby' => 'count', 'number' => 5, 'taxonomy' => $post_tax) );
			?>
			<div id="taxonomy_<?php echo $field_id;?>" class="termdiv">
				<ul id="<?php echo $field_id;?>_tabs" class="term-tabs">
					<li class="tabs" id="all_<?php echo $field_id;?>_tab"><a href="#" name="<?php echo $field_id;?>" id="<?php echo $field_id;?>_all_link" class="ninja-forms-terms-tab"><?php _e('All', 'ninja-forms');?> <?php echo $post_tax_name;?></a></li>
					<li class="hide-if-no-js"><a href="#" name="<?php echo $field_id;?>" id="<?php echo $field_id;?>_pop_link" class="ninja-forms-terms-tab">Most Used</a></li>
				</ul>

				<div id="<?php echo $field_id;?>_pop" class="<?php echo $field_id;?>-tabs-panel tabs-panel" style="display:none;">
					<ul id="<?php echo $field_id;?>checklist-pop" class="termchecklist form-no-clear">
						<?php
						if( is_array( $pop_terms ) AND !empty( $pop_terms ) ){
							foreach($pop_terms as $term){
								?>
								<li id="<?php echo $field_id;?>_<?php echo $term->term_id;?>" class="popular-term">
									<label class="selectit">
										<input value="<?php echo $term->term_id;?>" type="checkbox" name="" id="<?php echo $field_id;?>-<?php echo $term->term_id;?>" class="<?php echo $field_id;?>-checkbox term-<?php echo $term->term_id;?>" <?php checked( in_array( $term->term_id, $terms ) ) ?>> <?php echo $term->name;?>
									</label>
								</li>
								<?php
							}
						}
						?>	
					</ul>
				</div>

				<li id="term_<?php echo $field_id;?>_li_template" class="new-<?php echo $field_id;?>" style="display:none;">
					<label class="selectit">
						<input value="" type="hidden" id="ninja_forms_field_<?php echo $field_id;?>[new][][parent]" class="term-parent">
						<input value="" type="checkbox" id="ninja_forms_field_<?php echo $field_id;?>[new][][name]" checked="checked" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>"> <span></span>
					</label>
				</li>

				<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>" value="">

				<div id="<?php echo $field_id;?>_all" class="<?php echo $field_id;?>-tabs-panel tabs-panel" style="display: block;">
					<ul id="<?php echo $field_id;?>_checklist" class="termchecklist form-no-clear">
					<?php
						if(is_array( $all_terms ) AND !empty( $all_terms ) ){
											foreach( $all_terms as $term ){
								?>
								<li id="<?php echo $field_id;?>_<?php echo $term->term_id;?>_li" class="popular-term">
									<label class="selectit">
										<input value="<?php echo $term->term_id;?>" type="checkbox" name="ninja_forms_field_<?php echo $field_id;?>[terms][]" id="term-<?php echo $term->term_id;?>" class="<?php echo $field_id;?>-checkbox term-<?php echo $term->term_id;?> <?php echo $field_class;?>" rel="<?php echo $field_id;?>" <?php checked( in_array( $term->term_id, $terms ) ) ?>> <span><?php echo $term->name;?></span>
									</label>
									<?php
					
									$child_terms = get_categories( array( 'taxonomy' => $post_tax, 'parent' => $term->term_id, 'hide_empty' => false ) );
									$child_terms = apply_filters( 'ninja_forms_display_child_terms', $child_terms, $term->term_id );
			
									if( is_array( $child_terms ) AND !empty( $child_terms ) ){
										?>
										<ul class="children termchecklist form-no-clear" id="term_<?php echo $term->term_id;?>_children">
										<?php
										foreach( $child_terms as $child_term ){
											?>
											<li id="<?php echo $field_id;?>_<?php echo $child_term->term_id;?>_li" class="popular-term">
												<label class="selectit">
													<input value="<?php echo $child_term->term_id;?>" type="checkbox" name="ninja_forms_field_<?php echo $field_id;?>[terms][]" id="term-<?php echo $child_term->term_id;?>" class="<?php echo $field_id;?>-checkbox term-<?php echo $child_term->term_id;?> <?php echo $field_class;?>" rel="<?php echo $field_id;?>" <?php checked( in_array( $child_term->term_id, $terms ) ) ?>> <span><?php echo $child_term->name;?></span>
												</label>
											</li>
											<?php
										}
										?>
										</ul>
										<?php
									}
									?>
								</li>
								<?php
							}
						}
						?>	
					</ul>
				</div>
				<?php
				if($add_term == 1){
				?>
				<div id="term-adder" class="wp-hidden-children">
					<h4>
						<a id="<?php echo $field_id;?>_add_toggle" href="#" class="term-add-toggle hide-if-no-js">+ <?php _e( 'Add New', 'ninja-forms');?> <?php echo $post_tax_singular;?></a>
					</h4>
					<p id="<?php echo $field_id;?>_add" class="term-add wp-hidden-child" style="display:none;">
						<input type="hidden" id="new_<?php echo $field_id;?>_default" value="">
						<input type="text" name="" id="new_<?php echo $field_id;?>_label" class="new-term-label" value="">
						<br />
						<select name="" id="<?php echo $field_id;?>_parent" class="">
							<option value="-1">— <?php _e( 'Parent', 'ninja-forms');?> <?php echo $post_tax_singular;?> —</option>
							<?php
							if(is_array( $all_terms ) AND !empty( $all_terms ) ){
								foreach( $all_terms as $term ){
									?>
									<option value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option>
									<?php
								}
							}
							?>
						</select>
						<br />
						<input type="button" id="<?php echo $field_id;?>_add_submit" name="new_<?php echo $field_id;?>_tax" class="button term-add-submit" value="Add New Term">
					</p>
				</div>
				<?php
				}
				?>
			</div>
			<?php

		}else{

			$all_terms = get_terms($post_tax, array( 'parent' => 0, 'hide_empty' => false));
			$all_terms = apply_filters( 'ninja_forms_display_all_terms', $all_terms, $post_tax );
			if( !is_object( $all_terms ) AND !isset( $all_terms->errors ) ){
				$x = 0;
				?>
				<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>" value="">
				<ul class="termchecklist">
				<?php
				foreach( $all_terms as $t ){
					?>
					<li>
						<label for="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>">
							<input type="checkbox" id="ninja_forms_field_<?php echo $field_id;?>_<?php echo $x;?>" name="ninja_forms_field_<?php echo $field_id;?>[terms][]" value="<?php echo $t->term_id;?>" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>" <?php checked( in_array($t->term_id, $terms) );?>> <?php echo $t->name;?>
						</label>
						<?php
							$child_terms = get_categories( array( 'taxonomy' => $post_tax, 'child_of' => $t->term_id, 'hide_empty' => false ) );
							$child_terms = apply_filters( 'ninja_forms_display_child_terms', $child_terms, $t->term_id );
							if( is_array( $child_terms ) AND !empty( $child_terms ) ){
								?>
								<ul class="children termchecklist form-no-clear" id="">
								<?php
								foreach( $child_terms as $child_term ){
									?>
									<li>
										<label>
											<input value="<?php echo $child_term->term_id;?>" type="checkbox" name="ninja_forms_field_<?php echo $field_id;?>[terms][]" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>" <?php checked( in_array( $child_term->term_id, $terms ) ) ?>> <span><?php echo $child_term->name;?></span>
										</label>
									</li>
									<?php
								}
								?>
								</ul>
								<?php
							}
							?>
					</li>
					<?php
					$x++;
				}
				?>
				</ul>
				<?php
			}
		}
	}

	function ninja_forms_field_post_terms_pre_process($field_id, $user_value){
		global $ninja_forms_fields, $ninja_forms_processing;

		$field_row = ninja_forms_get_field_by_id( $field_id );
		$field_type = $field_row['type'];
		$post_tax = $ninja_forms_fields[$field_type]['tax'];

		$tmp_array = array();
		if( isset( $user_value['new'] ) AND is_array( $user_value['new'] ) AND !empty( $user_value['new'] ) ){
			foreach( $user_value['new'] as $key => $new ){
				if( isset( $new['name'] ) ){
					$term_name = esc_html( $new['name'] );
					if( is_numeric( $new['parent'] ) ){
						$parent = $new['parent'];
					}else{
						if( isset($user_value['new'][$new['parent']]['name'] ) ){
							$parent = $user_value['new'][$new['parent']]['term_id'];
						}else{
							$parent = '';
						}
					}
					if( $parent == -1 ){
						$parent = '';
					}
					if( $parent != '' ){
						$args = array(
							'parent' => $parent,
						);
					}else{
						$args = array();
					}
					
					$term_id = get_term( $term_name, $post_tax );

					if( is_null( $term_id ) AND !is_object( $term_id ) ){
						$id = wp_insert_term( $term_name, $post_tax, $args );
						$term_id = $id['term_id'];
					}

					wp_cache_flush();
					delete_option($post_tax."_children");
					
					$user_value['new'][$key]['term_id'] = $term_id;
					array_push( $tmp_array, $term_id );
					
				}
			}
		}

		if( isset( $user_value['terms'] ) AND is_array( $user_value['terms'] ) AND !empty( $user_value['terms'] ) ){
			foreach( $user_value['terms'] as $term ){
				array_push( $tmp_array, $term );
			}
		}

		if( !empty( $tmp_array ) ){
			$ninja_forms_processing->update_field_value( $field_id, $tmp_array );
			$ninja_forms_processing->update_form_setting( $post_tax.'_terms', $tmp_array );		
		}
	}
}
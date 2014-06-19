<?php

function ninja_forms_edit_field_output_li( $field_id ) {
	global $wpdb, $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$current_tab = ninja_forms_get_current_tab();
	if ( isset ( $_REQUEST['page'] ) ) {
		$current_page = esc_html( $_REQUEST['page'] );
	} else {
		$current_page = '';
	}
	
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$plugin_settings = nf_get_settings();
	
	if ( isset( $ninja_forms_fields[$field_type]['use_li'] ) and $ninja_forms_fields[$field_type]['use_li'] ) {

		if ( isset( $field_row['fav_id'] ) and $field_row['fav_id'] != 0 ) {
			$fav_id = $field_row['fav_id'];
			$fav_row = ninja_forms_get_fav_by_id( $fav_id );
			if ( empty( $fav_row['name'] ) ) {
				$args = array(
					'update_array' => array(
						'fav_id' => '',
					),
					'where' => array(
						'id' => $field_id,
					),
				);

				ninja_forms_update_field( $args );
				$fav_id = '';
			}
		} else {
			$fav_id = '';
		}

		if ( isset( $field_row['def_id'] ) and $field_row['def_id'] != 0 ) {
			$def_id = $field_row['def_id'];
		} else {
			$def_id = '';
		}

		$form_id = $field_row['form_id'];
		$field_results = ninja_forms_get_fields_by_form_id( $form_id );

		if ( isset( $ninja_forms_fields[$field_type] ) ) {
			$reg_field = $ninja_forms_fields[$field_type];
			$type_name = $reg_field['name'];
			$edit_function = $reg_field['edit_function'];
			$edit_options = $reg_field['edit_options'];

			if ( $reg_field['nesting'] ) {
				$nesting_class = 'ninja-forms-nest';
			} else {
				$nesting_class = 'ninja-forms-no-nest';
			}
			$conditional = $reg_field['conditional'];

			$type_class = $field_type.'-li';

			if ( $def_id != 0 and $def_id != '' ) {
				$def_row = ninja_forms_get_def_by_id( $def_id );
				if ( !empty( $def_row['name'] ) ) {
					$type_name = $def_row['name'];
				}
			}

			if ( $fav_id != 0 and $fav_id != '' ) {
				$fav_row = ninja_forms_get_fav_by_id( $fav_id );
				if ( !empty( $fav_row['name'] ) ) {
					$fav_class = 'ninja-forms-field-remove-fav';
					$type_name = $fav_row['name'];
				}
			} else {
				$fav_class = 'ninja-forms-field-add-fav';
			}

			if ( isset( $field_data['label'] ) and $field_data['label'] != '' ) {
				$li_label = $field_data['label'];
			} else {
				$li_label = $type_name;
			}

			$li_label = apply_filters( 'ninja_forms_edit_field_li_label', $li_label, $field_id );

			$li_label = stripslashes( $li_label );
			$li_label = ninja_forms_esc_html_deep( $li_label );

			if ( 
			isset( $reg_field ) &&
			isset( $reg_field['conditional'] ) &&
			isset( $reg_field['conditional']['value'] ) &&
			isset( $reg_field['conditional']['value']['type'] ) ) {
				$conditional_value_type = $reg_field['conditional']['value']['type'];
			} else {
				$conditional_value_type = '';
			}
?>
			<li id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $nesting_class;?> <?php echo $type_class;?>">
				<input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>_conditional_value_type" value="<?php echo $conditional_value_type;?>">
				<input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>_fav_id" name="" class="ninja-forms-field-fav-id" value="<?php echo $fav_id;?>">
				<dl class="menu-item-bar">
					<dt class="menu-item-handle" id="ninja_forms_metabox_field_<?php echo $field_id;?>" >
						<span class="item-title ninja-forms-field-title" id="ninja_forms_field_<?php echo $field_id;?>_title"><?php echo $li_label;?></span>
						<span class="item-controls">
							<span class="item-type"><?php echo $type_name;?></span>
							<a class="item-edit metabox-item-edit" id="ninja_forms_field_<?php echo $field_id;?>_toggle" title="<?php _e( 'Edit Menu Item', 'ninja-forms' ); ?>" href="#"><?php _e( 'Edit Menu Item' , 'ninja-forms' ); ?></a>
						</span>
					</dt>
				</dl>
				<?php
				$slug = 'field_'.$field_id;
				if ( isset ( $plugin_settings['metabox_state'][$current_page][$current_tab][$slug] ) ) {
					$state = $plugin_settings['metabox_state'][$current_page][$current_tab][$slug];
				} else {
					$state = 'display:none;';
				}
								
				?>
				<div class="menu-item-settings type-class inside" id="ninja_forms_field_<?php echo $field_id;?>_inside" style="<?php echo $state;?>">
					<table id="field-info"><tr><td width="65%"><?php _e( 'Field ID', 'ninja-forms' ); ?>: <strong><?php echo $field_id;?></strong></td><!-- <td width="15%"><a href="#" class="ninja-forms-field-add-def" id="ninja_forms_field_<?php echo $field_id;?>_def" class="ninja-forms-field-add-def">Add Defined</a></td><td width="15%"><a href="#" class="ninja-forms-field-remove-def" id="ninja_forms_field_<?php echo $field_id;?>_def">Remove Defined</a></td> --> <td width="5%"><a href="#" class="<?php echo $fav_class;?>" id="ninja_forms_field_<?php echo $field_id;?>_fav">Star</a></td></tr></table>
			<?php

			do_action( 'ninja_forms_edit_field_before_registered', $field_id );

			$arguments = func_get_args();
			array_shift( $arguments ); // We need to remove the first arg ($function_name)
			$arguments['field_id'] = $field_id;
			$arguments['data'] = $field_data;

			if ( $edit_function != '' ) {
				call_user_func_array( $edit_function, $arguments );
			}

			if ( is_array( $edit_options ) and !empty( $edit_options ) ) {
				foreach ( $edit_options as $opt ) {
					$type = $opt['type'];

					if ( isset( $opt['label'] ) ) {
						$label = $opt['label'];
					} else {
						$label = '';
					}

					if ( isset( $opt['name'] ) ) {
						$name = $opt['name'];
					} else {
						$name = '';
					}

					if ( isset( $opt['width'] ) ) {
						$width = $opt['width'];
					} else {
						$width = '';
					}

					if ( isset( $opt['options'] ) ) {
						$options = $opt['options'];
					} else {
						$options = '';
					}

					if ( isset( $opt['class'] ) ) {
						$class = $opt['class'];
					} else {
						$class = '';
					}

					if ( isset( $opt['default'] ) ) {
						$default = $opt['default'];
					} else {
						$default = '';
					}

					if ( isset( $opt['desc'] ) ) {
						$desc = $opt['desc'];
					} else {
						$desc = '';
					}

					if ( isset( $field_data[$name] ) ) {
						$value = $field_data[$name];
					} else {
						$value = $default;
					}

					ninja_forms_edit_field_el_output( $field_id, $type, $label, $name, $value, $width, $options, $class, $desc );
				}
			}

			do_action( 'ninja_forms_edit_field_after_registered', $field_id );
		}
	} else {
		if ( isset( $ninja_forms_fields[$field_type] ) ) {
			$reg_field = $ninja_forms_fields[$field_type];
			$edit_function = $reg_field['edit_function'];
			$arguments = func_get_args();
			array_shift( $arguments ); // We need to remove the first arg ($function_name)
			$arguments['field_id'] = $field_id;
			$arguments['data'] = $field_data;

			if ( $edit_function != '' ) {
				call_user_func_array( $edit_function, $arguments );
			}
		}
	}
}
add_action( 'ninja_forms_edit_field_li', 'ninja_forms_edit_field_output_li' );

function ninja_forms_edit_field_close_li( $field_id ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if ( isset( $ninja_forms_fields[$field_type]['use_li'] ) and $ninja_forms_fields[$field_type]['use_li'] ) {

		do_action( 'ninja_forms_edit_field_before_closing_li', $field_id );
?>
			</div><!-- .menu-item-settings-->
		</li>
		<?php
	}
}
add_action( 'ninja_forms_edit_field_after_li', 'ninja_forms_edit_field_close_li' );

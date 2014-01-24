<?php
//add_action('admin_init', 'ninja_forms_register_sidebar_def_fields');

function ninja_forms_register_sidebar_def_fields(){
	$args = array(
		'name' => __( 'Defined Fields', 'ninja-forms' ),
		'page' => 'ninja-forms',
		'tab' => 'field_settings',
		'display_function' => 'ninja_forms_sidebar_def_fields'
	);
	ninja_forms_register_sidebar('def_fields', $args);
}

function ninja_forms_sidebar_def_fields(){
	global $wpdb, $ninja_forms_fields;
	$field_results = ninja_forms_get_all_defs();

	if(is_array($field_results)){
		foreach($field_results as $field){
			$data = $field['data'];
			$name = $field['name'];
			$field_id = $field['id'];
			$type = $field['type'];
			$reg_field = $ninja_forms_fields[$type];
			$limit = $reg_field['limit'];

			?>
			<p class="button-controls" id="ninja_forms_insert_def_field_<?php echo $field_id;?>_p">
				<a class="button-secondary ninja-forms-insert-def-field" id="ninja_forms_insert_def_field_<?php echo $field_id;?>" name="_<?php echo $limit;?>"  href="#" rel="<?php echo $type;?>"><?php _e($name, 'ninja-forms');?></a>
			</p>
			<?php
		}
	}
}
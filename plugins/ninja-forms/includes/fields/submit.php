<?php
function ninja_forms_register_field_submit(){
	$args = array(
		'name' => __( 'Submit', 'ninja-forms' ),
		'display_function' => 'ninja_forms_field_submit_display',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'display_label' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'process_field' => false,
		'limit' => 1,
	);

	ninja_forms_register_field('_submit', $args);
}

add_action('init', 'ninja_forms_register_field_submit');

function ninja_forms_field_submit_display($field_id, $data){

	if(isset($data['show_field'])){
		$show_field = $data['show_field'];
	}else{
		$show_field = true;
	}

	$field_class = ninja_forms_get_field_class($field_id);
	if(isset($data['label']) AND $data['label'] != ''){
		$label = $data['label'];
	}else{
		$label = 'Submit';
	}

	?>
	<input type="submit" name="_ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" id="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $label;?>" rel="<?php echo $field_id;?>" >
	<?php

}
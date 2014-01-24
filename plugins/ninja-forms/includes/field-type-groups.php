<?php

add_action( 'init', 'ninja_forms_register_common_field_type_groups', 8 );
function ninja_forms_register_common_field_type_groups(){
	$args = array(
		'name' => 'Standard Fields',
	);
	ninja_forms_register_field_type_group( 'standard_fields', $args );

	$args = array(
		'name' => 'Layout Elements',
	);
	ninja_forms_register_field_type_group( 'layout_elements', $args );

	$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
	if ( !$add_field )
		return false;

	$args = array(
		'name' => 'Post Creation',
	);
	ninja_forms_register_field_type_group( 'create_post', $args );
}
<?php

function ninja_forms_register_display_open_form_tag() {
	add_action( 'ninja_forms_display_open_form_tag', 'ninja_forms_display_open_form_tag' );
}
add_action( 'init', 'ninja_forms_register_display_open_form_tag' );

function ninja_forms_display_open_form_tag( $form_id ) {

	$form_row = ninja_forms_get_form_by_id( $form_id );

	if ( isset ( $form_row['data']['ajax'] ) )
		$ajax = $form_row['data']['ajax'];
	else
		$ajax = 0;

	if ( $ajax == 1 ) {
		$url = admin_url( 'admin-ajax.php' );
		$url = add_query_arg( 'action', 'ninja_forms_ajax_submit', $url );
		//$url = add_query_arg('action', 'test', $url);
	} else {
		$url = '';
	}

	$display = 1;

	$display = apply_filters( 'ninja_forms_display_form_visibility', $display, $form_id );

	if ( $display != 1 )
		$hide_class = " ninja-forms-no-display";
	else
		$hide_class = "";

	$form_class = '';

	$form_class = apply_filters( 'ninja_forms_form_class', $form_class, $form_id );

	if ( ! empty( $form_class ) )
		$form_class = ' ' . $form_class;

	?>
	<form id="ninja_forms_form_<?php echo $form_id;?>" enctype="multipart/form-data" method="post" name="" action="<?php echo $url;?>" class="ninja-forms-form<?php echo $form_class;?><?php echo $hide_class;?>">

	<?php
}
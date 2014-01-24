<?php
/*
 * Function to register a new field for user's country
 *
 * @since 2.2.37
 * @returns void
 */

function ninja_forms_register_field_credit_card(){

	$reg_field = apply_filters( 'ninja_forms_enable_credit_card_field', false );

	$args = array(
		'name' => __( 'Credit Card', 'ninja-forms' ),
		'sidebar' => '',
		'display_function' => 'ninja_forms_field_credit_card_display',
		'group' => 'standard_fields',
		'edit_conditional' => true,
		'edit_custom_class' => false,
		'edit_options' => array(),
		//'post_process' => 'ninja_forms_field_credit_card_test',
		'save_sub' => false,
		'process_field' => false,
		'edit_label_pos' => false,
		'edit_options' => array(
			array(
				'type' => 'hidden',
				'name' => 'payment_field_group',
				'default' => 1,
			),
		),
	);

	if ( $reg_field ) {
		ninja_forms_register_field( '_credit_card', $args );
	}
}

add_action( 'init', 'ninja_forms_register_field_credit_card' );

/*
 * Function to display our credit_card field on the front-end.
 *
 * @since 2.2.37
 * @returns void
 */

function ninja_forms_field_credit_card_display( $field_id, $data ) {
	global $ninja_forms_processing;

	if( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if ( isset ( $ninja_forms_processing ) ){
		$name = $ninja_forms_processing->get_extra_value( '_credit_card_name' );
		$expires = $ninja_forms_processing->get_extra_value( '_credit_card_expires' );
	} else {
		$name = '';
		$expires = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id );
	$post_field = apply_filters( 'ninja_forms_post_credit_card_field', false );
	?>
		<div class="ninja-forms-credit-card-number"> <!-- Open Credit Card Wrap -->
			<label><?php _e( 'Card Number', 'ninja-forms' ); ?></label>
			<span><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'ninja-forms' ); ?></span>
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_number"'; } ?> class="">
		</div>
		<div class="ninja-forms-credit-card-cvc"> <!-- [open_cvc_wrap] -->
			<label><?php _e( 'CVC', 'ninja-forms' ); ?></label>
			<span><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'ninja-forms' ); ?></span>
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_cvc"'; } ?> class="">
		</div>
		<div class="ninja-forms-credit-card-name"> <!-- [open_nameoncard_wrap] -->
			<label><?php _e( 'Name on the Card', 'ninja-forms' ); ?></label>
			<span><?php _e( 'The name printed on the front of your credit card.', 'ninja-forms' ); ?></span>
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_name"'; } ?> class="" value="<?php echo $name;?>">
		</div>
		<div class="ninja-forms-credit-card-expires"> <!-- [open_expires_wrap] -->
			<label><?php _e( 'Expiration (MM/YYYY)', 'ninja-forms' ); ?></label>
			<span><?php _e( 'The date your credit card expires, typically on the front of the card.', 'ninja-forms' ); ?></span>
			<input type="text" <?php if ( $post_field ){ echo 'name="_credit_card_expires"'; } ?> class="ninja-forms-mask" title="99/9999" value="<?php echo $expires;?>">
		</div> <!-- [close_expires_wrap] -->
	<?php
}

/*
 *
 * Function that filters the display script field data so that the mask is included for the expires field.
 *
 * @since 2.2.37
 * @returns array $data
 */

function ninja_forms_field_credit_card_expire_filter( $data, $field_id ){
	$field = ninja_forms_get_field_by_id( $field_id );
	if ( $field['type'] == '_credit_card' ) {
		$data['mask'] = '99/9999';
	}
	return $data;
}

add_action( 'ninja_forms_display_script_field_data', 'ninja_forms_field_credit_card_expire_filter', 10, 2 );
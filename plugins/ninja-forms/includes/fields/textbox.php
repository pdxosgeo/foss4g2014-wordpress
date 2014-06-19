<?php
function ninja_forms_register_field_textbox(){
	$args = array(
		'name' => __( 'Textbox', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_text_edit',
		'edit_options' => array(
			array(
				'type' => 'checkbox',
				'name' => 'datepicker',
				'label' => __( 'Datepicker', 'ninja-forms' ),
			),
			array(
				'type' => 'checkbox',
				'name' => 'email',
				'label' => __( 'Is this an email address?', 'ninja-forms' ),
			),
			array(
				'type' => 'checkbox',
				'name' => 'send_email',
				'label' => __( 'Send a response email to this email address?', 'ninja-forms' ),
			),
			// array(
			// 	'type' => 'checkbox',
			// 	'name' => 'from_email',
			// 	'label' => __( 'Use this as the "From" email address for Administrative recipients of this form?', 'ninja-forms' ),
			// ),
			array(
				'type' => 'checkbox',
				'name' => 'replyto_email',
				'label' => __( 'Use this email address as the Reply-To address?', 'ninja-forms' ),
			),
			array(
				'type' => 'hidden',
				'name' => 'first_name',
			),
			array(
				'type' => 'hidden',
				'name' => 'last_name',
			),
			array(
				'type' => 'checkbox',
				'name' => 'from_name',
				'label' => __( 'Use this as the "From" and Reply-To email name for Administrative recipients of this form?', 'ninja-forms' ),
			),
			array(
				'type' => 'hidden',
				'name' => 'user_address_1',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_address_2',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_city',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_zip',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_phone',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_email',
			),
			array(
				'type' => 'hidden',
				'name' => 'user_info_field_group',
				'default' => 1,
			),
			array(
				'type' => 'checkbox',
				'label' => __( 'This is the user\'s state', 'ninja-forms' ),
				'name' => 'user_state',
			),
		),
		'display_function' => 'ninja_forms_field_text_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_desc' => true,
		'edit_meta' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_text_pre_process',
	);

	ninja_forms_register_field( '_text', $args );
}

add_action( 'init', 'ninja_forms_register_field_textbox' );

function ninja_forms_field_text_edit( $field_id, $data ){
	$plugin_settings = nf_get_settings();

	if( isset( $plugin_settings['currency_symbol'] ) ){
		$currency_symbol = $plugin_settings['currency_symbol'];
	}else{
		$currency_symbol = "$";
	}

	if( isset( $plugin_settings['date_format'] ) ){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = "m/d/Y";
	}
	$custom = '';
	// Default Value
	if( isset( $data['default_value'] ) ){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}
	if( $default_value == 'none' ){
		$default_value = '';
	}

	?>
	<div class="description description-thin">
		<span class="field-option">
		<label for="">
			<?php _e( 'Default Value' , 'ninja-forms'); ?>
		</label><br />
			<select id="default_value_<?php echo $field_id;?>" name="" class="widefat ninja-forms-_text-default-value">
				<option value="" <?php if( $default_value == ''){ echo 'selected'; $custom = 'no';}?>><?php _e('None', 'ninja-forms'); ?></option>
				<option value="_user_id" <?php if($default_value == '_user_id'){ echo 'selected'; $custom = 'no';}?>><?php _e('User ID (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_firstname" <?php if($default_value == '_user_firstname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Firstname (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_lastname" <?php if($default_value == '_user_lastname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Lastname (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_display_name" <?php if($default_value == '_user_display_name'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Display Name (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_email" <?php if($default_value == '_user_email'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Email (If logged in)', 'ninja-forms'); ?></option>
				<option value="post_id" <?php if($default_value == 'post_id'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page ID (If available)', 'ninja-forms'); ?></option>
				<option value="post_title" <?php if($default_value == 'post_title'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page Title (If available)', 'ninja-forms'); ?></option>
				<option value="post_url" <?php if($default_value == 'post_url'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page URL (If available)', 'ninja-forms'); ?></option>
				<option value="today" <?php if($default_value == 'today'){ echo 'selected'; $custom = 'no';}?>><?php _e('Today\'s Date', 'ninja-forms'); ?></option>
				<option value="_custom" <?php if($custom != 'no'){ echo 'selected';}?>><?php _e('Custom', 'ninja-forms'); ?> -></option>
			</select>
		</span>
	</div>
	<div class="description description-thin">

		<label for="" id="default_value_label_<?php echo $field_id;?>" style="<?php if($custom == 'no'){ echo 'display:none;';}?>">
			<span class="field-option">
			<?php _e( 'Default Value' , 'ninja-forms'); ?><br />
			<input type="text" class="widefat code" name="ninja_forms_field_<?php echo $field_id;?>[default_value]" id="ninja_forms_field_<?php echo $field_id;?>_default_value" value="<?php echo $default_value;?>" />
			</span>
		</label>

	</div>


	<?php
	$custom = '';
	// Field Mask
	if( isset( $data['mask'] ) ){
		$mask = $data['mask'];
	}else{
		$mask = '';
	}
	?>
	<div class="description description-thin">
		<span class="field-option">
		<label for="">
			<?php _e( 'Input Mask' , 'ninja-forms'); ?>
		</label><br />
			<select id="mask_<?php echo $field_id;?>"  name="" class="widefat ninja-forms-_text-mask">
				<option value="" <?php if($mask == ''){ echo 'selected'; $custom = 'no';}?>><?php _e('None', 'ninja-forms'); ?></option>
				<option value="(999) 999-9999" <?php if($mask == '(999) 999-9999'){ echo 'selected'; $custom = 'no';}?>><?php _e('Phone - (555) 555-5555', 'ninja-forms'); ?></option>
				<option value="date" <?php if($mask == 'date'){ echo 'selected'; $custom = 'no';}?>><?php _e('Date', 'ninja-forms'); ?> - <?php echo $date_format;?></option>
				<option value="currency" <?php if($mask == 'currency'){ echo 'selected'; $custom = 'no';}?>><?php _e('Currency', 'ninja-forms'); ?> - <?php echo $currency_symbol;?></option>
				<option value="_custom" <?php if($custom != 'no'){ echo 'selected';}?>><?php _e('Custom', 'ninja-forms'); ?> -></option>
			</select>

		</span>
	</div>
	<div class="description description-thin">
		<span class="field-option">
		<label for=""  id="mask_label_<?php echo $field_id;?>" style="<?php if($custom == 'no'){ echo 'display:none;';}?>">
			<?php _e( 'Custom Mask Definition' , 'ninja-forms'); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" name="" class="ninja-forms-mask-help"><?php _e( 'Help', 'ninja-forms' ); ?></a><br />
			<input type="text" id="ninja_forms_field_<?php echo $field_id;?>_mask" name="ninja_forms_field_<?php echo $field_id;?>[mask]" class="widefat code" value="<?php echo $mask; ?>" />
		</label>
		</span>
	</div>
	<?php
}

function ninja_forms_field_text_display( $field_id, $data ){
	global $current_user;
	$field_class = ninja_forms_get_field_class( $field_id );

	if ( isset( $data['email'] ) ) {
		$field_class .= ' email';
	}

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

	if( isset( $data['mask'] ) ){
		$mask = $data['mask'];
	}else{
		$mask = '';
	}	

	if( isset( $data['input_limit'] ) ){
		$input_limit = $data['input_limit'];
	}else{
		$input_limit = '';
	}

	if( isset( $data['input_limit_type'] ) ){
		$input_limit_type = $data['input_limit_type'];
	}else{
		$input_limit_type = '';
	}

	if( isset( $data['input_limit_msg'] ) ){
		$input_limit_msg = $data['input_limit_msg'];
	}else{
		$input_limit_msg = '';
	}

	switch( $mask ){
		case '':
			$mask_class = '';
			break;
		case 'date':
			$mask_class = 'ninja-forms-date';
			break;
		case 'currency':
			$mask_class =  'ninja-forms-currency';
			break;
		default:
			$mask_class = 'ninja-forms-mask';
			break;
	}

	if( isset( $data['datepicker'] ) AND $data['datepicker'] == 1 ){
		$mask_class = 'ninja-forms-datepicker';
	}

	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" data-mask="<?php echo $mask;?>" data-input-limit="<?php echo $input_limit;?>" data-input-limit-type="<?php echo $input_limit_type;?>" data-input-limit-msg="<?php echo $input_limit_msg;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?> <?php echo $mask_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
	<?php

}

function ninja_forms_field_text_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;
	$plugin_settings = nf_get_settings();
	if( isset( $plugin_settings['invalid_email'] ) ){
		$invalid_email = __( $plugin_settings['invalid_email'], 'ninja-forms' );
	}else{
		$invalid_email = __( 'Please enter a valid email address.', 'ninja-forms' );
	}
	$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	$data = $field_row['data'];
	if( isset( $data['email'] ) AND $data['email'] == 1 AND $user_value != '' ){
		if ( ! is_email( $user_value ) ) {
    		$ninja_forms_processing->add_error( 'email-'.$field_id, $invalid_email, $field_id );
		}
	}

	if( ( isset( $data['replyto_email'] ) AND $data['replyto_email'] == 1 ) OR ( isset( $data['from_email'] ) AND $data['from_email'] == 1 ) ) {
		$user_value = $ninja_forms_processing->get_field_value( $field_id );
		$ninja_forms_processing->update_form_setting( 'admin_email_replyto', $user_value );
	}

	if( isset( $data['from_name'] ) AND $data['from_name'] == 1 ){
		$user_value = $ninja_forms_processing->get_field_value( $field_id );
		if( $ninja_forms_processing->get_form_setting( 'admin_email_name' ) ){
			$admin_email_name = $ninja_forms_processing->get_form_setting( 'admin_email_name' );
			$admin_email_name .= " ".$user_value;
		}else{
			$admin_email_name = $user_value;
		}
		$ninja_forms_processing->update_form_setting( 'admin_email_name', $admin_email_name );
	}
}
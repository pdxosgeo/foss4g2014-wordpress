<?php
/*
 * TODO: Add a custom ajax spinner uploader so that it can be customized without the need for code.
 *
 *
*/

//add_action('init', 'ninja_forms_register_tab_ajax_settings');

function ninja_forms_register_tab_ajax_settings(){
	$args = array(
		'name' => __( 'Ajax Settings', 'ninja-forms' ),
		'page' => 'ninja-forms-settings',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_ajax_settings',
	);
	ninja_forms_register_tab('ajax_settings', $args);

}

add_action('init', 'ninja_forms_register_ajax_settings_metabox');

function ninja_forms_register_ajax_settings_metabox(){
	$args = array(
		'page' => 'ninja-forms-settings',
		'tab' => 'ajax_settings',
		'slug' => 'msg_format',
		'title' => __( 'Ajax Message Settings', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'msg_format',
				'type' => 'radio',
				'label' => __( 'Ajax Message Format', 'ninja-forms' ),
				'desc' => __( '(Advanced setting: Ninja Forms will require you to create two javascript functions: one to handle the beforeSubmit call and one to handle the server response. These should be named ninja_forms_custom_ajax_before_submit and ninja_forms_custom_ajax_response.)', 'ninja-forms' ),
				'options' => array(
					array('name' => __( 'Inline Messages (Default)', 'ninja-forms' ), 'value' => 'inline'),
					//array('name' => 'jQuery Modal Messages', 'value' => 'modal'),
					array('name' => __( 'Custom Message Display (Advanced)', 'ninja-forms' ), 'value' => 'custom'),
				),
				'help_text' => __( 'Ninja Forms Test', 'ninja-forms' ),
				'default' => 'inline',
			),
			/*
			array(
				'name' => 'ajax_spinner',
				'type' => '',
				'display_function' => 'ninja_forms_ajax_spinner_settings',
			),
			*/
		),
	);
	ninja_forms_register_tab_metabox($args);
}

function ninja_forms_ajax_spinner_settings( $form_id, $data ){
	if( isset( $data['ajax_spinner'] ) ){
		$ajax_spinner = $data['ajax_spinner'];
	}else{
		$ajax_spinner = 'default';
	}
	?>
	<tr>
		<th><?php _e( 'Ajax Loading Gif', 'ninja-forms' );?></th>
		<td>
			<ul style="list-type:none;">
				<li>
					<label>
						<input type="radio" name="ajax_spinner" value="default" <?php checked( $ajax_spinner, 'default' );?>> <?php _e( 'Default', 'ninja-forms' );?>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="ajax_spinner" value="custom" <?php checked( $ajax_spinner, 'custom' );?>> <?php _e( 'Custom', 'ninja-forms' );?>
					</label>
				</li>
				<li>
					<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
					<input type="file" name="custom_ajax_spinner">
				</li>
			</ul>
		</td>
	</tr>
	<?php
}

function ninja_forms_save_ajax_settings($data){
	$plugin_settings = nf_get_settings();
	foreach($data as $key => $val){
		$plugin_settings[$key] = $val;
	}
	update_option("ninja_forms_settings", $plugin_settings);
	$update_msg = __( 'Settings Saved', 'ninja-forms' );
	return $update_msg;
}
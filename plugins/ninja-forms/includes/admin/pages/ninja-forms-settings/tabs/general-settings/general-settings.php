<?php
add_action( 'init', 'ninja_forms_register_tab_general_settings', 9 );

function ninja_forms_register_tab_general_settings(){
	$args = array(
		'name' => __( 'General', 'ninja-forms' ),
		'page' => 'ninja-forms-settings',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_general_settings',
	);
	ninja_forms_register_tab( 'general_settings', $args );
}

add_action('init', 'ninja_forms_register_general_settings_metabox');

function ninja_forms_register_general_settings_metabox(){

	$plugin_settings = nf_get_settings();
	if ( isset ( $plugin_settings['version'] ) ) {
		$current_version = $plugin_settings['version'];
	} else {
		$current_version = NINJA_FORMS_VERSION;
	}

	$args = array(
		'page' => 'ninja-forms-settings',
		'tab' => 'general_settings',
		'slug' => 'general_settings',
		'title' => __( 'General Settings', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'version',
				'type' => 'desc',
				'label' => __( 'Version', 'ninja-forms' ),
				'desc' => $current_version,
			),
			array(
				'name' => 'date_format',
				'type' => 'text',
				'label' => __( 'Date Format', 'ninja-forms' ),
				'desc' => __( 'e.g. m/d/Y, d/m/Y - Tries to follow the <a href="http://www.php.net/manual/en/function.date.php" target="_blank">PHP date() function</a> specifications, but not every format is supported.', 'ninja-forms' ),
			),
			array(
				'name' => 'currency_symbol',
				'type' => 'text',
				'label' => __( 'Currency Symbol', 'ninja-forms' ),
				'desc' => __( 'e.g. $, &pound;, &euro;', 'ninja-forms' ),
			),
		),
	);
	ninja_forms_register_tab_metabox( $args );

}

function ninja_forms_save_general_settings( $data ){
	$plugin_settings = nf_get_settings();

	foreach( $data as $key => $val ){
		$plugin_settings[$key] = $val;
	}

	update_option( 'ninja_forms_settings', $plugin_settings );
	$update_msg = __( 'Settings Saved', 'ninja-forms' );
	return $update_msg;
}
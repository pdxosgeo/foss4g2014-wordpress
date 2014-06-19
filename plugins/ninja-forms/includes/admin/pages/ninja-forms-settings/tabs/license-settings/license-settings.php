<?php

add_action('init', 'ninja_forms_register_tab_license_settings');

function ninja_forms_register_tab_license_settings(){
	$args = array(
		'name' => __( 'Licenses', 'ninja-forms' ),
		'page' => 'ninja-forms-settings',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_license_settings',
		'tab_reload' => true,
	);
	ninja_forms_register_tab( 'license_settings', $args );
}

add_action('init', 'ninja_forms_register_license_settings_metabox');

function ninja_forms_register_license_settings_metabox(){
	$args = array(
		'page' => 'ninja-forms-settings',
		'tab' => 'license_settings',
		'slug' => 'license_settings',
		'title' => __( 'Licenses', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'license_key',
				'type' => 'desc',
				'desc' => __('To activate licenses for Ninja Forms extensions you must first <a target="_blank" href="http://ninjaforms.com/documentation/extension-docs/installing-extensions/">install and activate</a> the chosen extension. License settings will then appear below.', 'ninja-forms'),
			),
		),
	);
	ninja_forms_register_tab_metabox( $args );
}

function ninja_forms_save_license_settings( $data ){
	$plugin_settings = nf_get_settings();

	foreach( $data as $key => $val ){
		$plugin_settings[$key] = $val;
	}

	update_option( 'ninja_forms_settings', $plugin_settings );
	$update_msg = __( 'Licenses Saved', 'ninja-forms' );
	return $update_msg;
}
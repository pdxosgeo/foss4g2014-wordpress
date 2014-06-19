<?php

/*
 *
 * Function that checks to see if the new defined files from version 2.2.30 are present. If they aren't, add them.
 *
 * @since 2.2.30
 * @returns void
 */

function ninja_forms_defined_fields_check(){
	$screen = get_current_screen();
	if ( $screen->base == 'toplevel_page_ninja-forms' ) {
		global $wpdb;
		// Run our update.
		// Make sure that our defined fields don't already exist.
		$tax = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Tax' ), ARRAY_A );
		if( !isset($tax['id']) ){
			$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
				(NULL, 0, \'_tax\', 0, \'a:11:{s:5:"label";s:3:"Tax";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:19:"payment_field_group";s:1:"1";s:11:"payment_tax";s:1:"1";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:11:"conditional";s:0:"";s:11:"calc_option";s:1:"0";s:4:"calc";s:0:"";}\', \'Tax\'),
				(NULL, 0, \'_text\', 0, \'a:19:{s:5:"label";s:10:"First Name";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"1";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'First Name\'),
				(NULL, 0, \'_text\', 0, \'a:19:{s:5:"label";s:9:"Last Name";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"1";s:9:"from_name";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Last Name\'),
				(NULL, 0, \'_text\', 0, \'a:23:{s:5:"label";s:9:"Address 1";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"1";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Address 1\'),
				(NULL, 0, \'_text\', 0, \'a:23:{s:5:"label";s:9:"Address 2";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"1";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Address 2\'),
				(NULL, 0, \'_text\', 0, \'a:23:{s:5:"label";s:4:"City";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"1";s:8:"user_zip";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'City\'),
				(NULL, 0, \'_list\', 0, \'a:16:{s:5:"label";s:5:"State";s:9:"label_pos";s:4:"left";s:10:"multi_size";s:1:"5";s:15:"list_show_value";s:1:"1";s:4:"list";a:1:{s:7:"options";a:51:{i:0;a:4:{s:5:"label";s:7:"Alabama";s:5:"value";s:2:"AL";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:1;a:4:{s:5:"label";s:6:"Alaska";s:5:"value";s:2:"AK";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:2;a:4:{s:5:"label";s:7:"Arizona";s:5:"value";s:2:"AZ";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:3;a:4:{s:5:"label";s:8:"Arkansas";s:5:"value";s:2:"AR";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:4;a:4:{s:5:"label";s:10:"California";s:5:"value";s:2:"CA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:5;a:4:{s:5:"label";s:8:"Colorado";s:5:"value";s:2:"CO";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:6;a:4:{s:5:"label";s:11:"Connecticut";s:5:"value";s:2:"CT";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:7;a:4:{s:5:"label";s:8:"Delaware";s:5:"value";s:2:"DE";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:8;a:4:{s:5:"label";s:20:"District of Columbia";s:5:"value";s:2:"DC";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:9;a:4:{s:5:"label";s:7:"Florida";s:5:"value";s:2:"FL";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:10;a:4:{s:5:"label";s:7:"Georgia";s:5:"value";s:2:"GA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:11;a:4:{s:5:"label";s:6:"Hawaii";s:5:"value";s:2:"HI";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:12;a:4:{s:5:"label";s:5:"Idaho";s:5:"value";s:2:"ID";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:13;a:4:{s:5:"label";s:8:"Illinois";s:5:"value";s:2:"IL";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:14;a:4:{s:5:"label";s:7:"Indiana";s:5:"value";s:2:"IN";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:15;a:4:{s:5:"label";s:4:"Iowa";s:5:"value";s:2:"IA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:16;a:4:{s:5:"label";s:6:"Kansas";s:5:"value";s:2:"KS";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:17;a:4:{s:5:"label";s:8:"Kentucky";s:5:"value";s:2:"KY";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:18;a:4:{s:5:"label";s:9:"Louisiana";s:5:"value";s:2:"LA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:19;a:4:{s:5:"label";s:5:"Maine";s:5:"value";s:2:"ME";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:20;a:4:{s:5:"label";s:8:"Maryland";s:5:"value";s:2:"MD";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:21;a:4:{s:5:"label";s:13:"Massachusetts";s:5:"value";s:2:"MA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:22;a:4:{s:5:"label";s:8:"Michigan";s:5:"value";s:2:"MI";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:23;a:4:{s:5:"label";s:9:"Minnesota";s:5:"value";s:2:"MN";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:24;a:4:{s:5:"label";s:11:"Mississippi";s:5:"value";s:2:"MS";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:25;a:4:{s:5:"label";s:8:"Missouri";s:5:"value";s:2:"MO";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:26;a:4:{s:5:"label";s:7:"Montana";s:5:"value";s:2:"MT";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:27;a:4:{s:5:"label";s:8:"Nebraska";s:5:"value";s:2:"NE";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:28;a:4:{s:5:"label";s:6:"Nevada";s:5:"value";s:2:"NV";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:29;a:4:{s:5:"label";s:12:"New Hampsire";s:5:"value";s:2:"NH";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:30;a:4:{s:5:"label";s:10:"New Jersey";s:5:"value";s:2:"NJ";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:31;a:4:{s:5:"label";s:10:"New Mexico";s:5:"value";s:2:"NM";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:32;a:4:{s:5:"label";s:8:"New York";s:5:"value";s:2:"NY";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:33;a:4:{s:5:"label";s:14:"North Carolina";s:5:"value";s:2:"NC";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:34;a:4:{s:5:"label";s:12:"North Dakota";s:5:"value";s:2:"ND";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:35;a:4:{s:5:"label";s:4:"Ohio";s:5:"value";s:2:"OH";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:36;a:4:{s:5:"label";s:8:"Oklahoma";s:5:"value";s:2:"OK";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:37;a:4:{s:5:"label";s:6:"Oregon";s:5:"value";s:2:"OR";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:38;a:4:{s:5:"label";s:12:"Pennsylvania";s:5:"value";s:2:"PA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:39;a:4:{s:5:"label";s:12:"Rhode Island";s:5:"value";s:2:"RI";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:40;a:4:{s:5:"label";s:14:"South Carolina";s:5:"value";s:2:"SC";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:41;a:4:{s:5:"label";s:12:"South Dakota";s:5:"value";s:2:"SD";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:42;a:4:{s:5:"label";s:9:"Tennessee";s:5:"value";s:2:"TN";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:43;a:4:{s:5:"label";s:5:"Texas";s:5:"value";s:2:"TX";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:44;a:4:{s:5:"label";s:4:"Utah";s:5:"value";s:2:"UT";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:45;a:4:{s:5:"label";s:7:"Vermont";s:5:"value";s:2:"VT";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:46;a:4:{s:5:"label";s:8:"Virginia";s:5:"value";s:2:"VA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:47;a:4:{s:5:"label";s:10:"Washington";s:5:"value";s:2:"WA";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:48;a:4:{s:5:"label";s:13:"West Virginia";s:5:"value";s:2:"WV";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:49;a:4:{s:5:"label";s:9:"Wisconsin";s:5:"value";s:2:"WI";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}i:50;a:4:{s:5:"label";s:7:"Wyoming";s:5:"value";s:2:"WY";s:4:"calc";s:0:"";s:8:"selected";s:1:"0";}}}s:9:"list_type";s:8:"dropdown";s:10:"user_state";s:1:"1";s:21:"user_info_field_group";s:1:"1";s:13:"populate_term";s:0:"";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'State\'),
				(NULL, 0, \'_text\', 0, \'a:23:{s:5:"label";s:15:"Zip / Post Code";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"1";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Zip / Post Code\'),
				(NULL, 0, \'_country\', 0, \'a:10:{s:5:"label";s:7:"Country";s:9:"label_pos";s:4:"left";s:13:"default_value";s:2:"US";s:21:"user_info_field_group";s:1:"1";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Country\'),
				(NULL, 0, \'_text\', 0, \'a:25:{s:5:"label";s:5:"Email";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:0:"";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"1";s:10:"send_email";s:1:"1";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"0";s:10:"user_phone";s:1:"0";s:10:"user_email";s:1:"1";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Email\'),
				(NULL, 0, \'_text\', 0, \'a:25:{s:5:"label";s:5:"Phone";s:9:"label_pos";s:4:"left";s:13:"default_value";s:0:"";s:4:"mask";s:14:"(999) 999-9999";s:10:"datepicker";s:1:"0";s:5:"email";s:1:"0";s:10:"send_email";s:1:"0";s:10:"from_email";s:1:"0";s:10:"first_name";s:1:"0";s:9:"last_name";s:1:"0";s:9:"from_name";s:1:"0";s:14:"user_address_1";s:1:"0";s:14:"user_address_2";s:1:"0";s:9:"user_city";s:1:"0";s:8:"user_zip";s:1:"0";s:10:"user_phone";s:1:"1";s:10:"user_email";s:1:"0";s:21:"user_info_field_group";s:1:"1";s:3:"req";s:1:"0";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Phone\'),
				(NULL, 0, \'_calc\', 0, \'a:20:{s:9:"calc_name";s:9:"sub_total";s:13:"default_value";s:0:"";s:17:"calc_display_type";s:4:"text";s:5:"label";s:9:"Sub Total";s:9:"label_pos";s:4:"left";s:26:"calc_display_text_disabled";s:1:"1";s:17:"calc_display_html";s:26:"<p>[ninja_forms_calc]</p>\n";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:11:"calc_method";s:4:"auto";s:4:"calc";s:0:"";s:7:"calc_eq";s:0:"";s:19:"payment_field_group";s:1:"1";s:13:"payment_total";s:1:"0";s:17:"payment_sub_total";s:1:"1";s:11:"calc_places";s:1:"2";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Sub Total\'),
				(NULL, 0, \'_calc\', 0, \'a:20:{s:9:"calc_name";s:5:"total";s:13:"default_value";s:0:"";s:17:"calc_display_type";s:4:"text";s:5:"label";s:5:"Total";s:9:"label_pos";s:4:"left";s:26:"calc_display_text_disabled";s:1:"1";s:17:"calc_display_html";s:26:"<p>[ninja_forms_calc]</p>\n";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:11:"calc_method";s:4:"auto";s:4:"calc";a:5:{i:0;a:2:{s:2:"op";s:3:"add";s:5:"field";s:2:"70";}i:1;a:2:{s:2:"op";s:3:"add";s:5:"field";s:2:"69";}i:2;a:2:{s:2:"op";s:3:"add";s:5:"field";s:2:"15";}i:3;a:2:{s:2:"op";s:3:"add";s:5:"field";s:2:"61";}i:4;a:2:{s:2:"op";s:3:"add";s:5:"field";s:2:"70";}}s:7:"calc_eq";s:5:"5 + 5";s:19:"payment_field_group";s:1:"1";s:13:"payment_total";s:1:"1";s:17:"payment_sub_total";s:1:"0";s:11:"calc_places";s:1:"2";s:17:"calc_auto_include";s:1:"0";s:11:"calc_option";s:1:"0";s:11:"conditional";s:0:"";}\', \'Total\');';
				$wpdb->query($sql);
		}
		$credit_card = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Credit Card' ), ARRAY_A );
		if( !isset( $credit_card['id'] ) ){
			$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES (92, 0, \'_credit_card\', 0, \'a:6:{s:5:"label";s:11:"Credit Card";s:19:"payment_field_group";s:1:"1";s:3:"req";s:1:"0";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";s:11:"conditional";s:0:"";}\', \'Credit Card\');';
			$wpdb->query($sql);
		}
	}
}

add_action( 'current_screen', 'ninja_forms_defined_fields_check' );


function ninja_forms_activation(){
	global $wpdb;

	wp_schedule_event( time(), 'daily', 'ninja_forms_daily_action' );

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$plugin_settings = nf_get_settings();

	if( isset( $plugin_settings['version'] ) ){
		$current_version = $plugin_settings['version'];
	}else{
		$current_version = '';
	}

	$forms = '';

	if( ( $current_version != '' ) AND version_compare( $current_version, '2.0' , '<' ) ){
		if($wpdb->get_var("SHOW COLUMNS FROM ".NINJA_FORMS_TABLE_NAME." LIKE 'title'") == 'title') {
			$forms = ninja_forms_activation_old_forms_check();

			if($wpdb->get_var("SHOW TABLES LIKE '".NINJA_FORMS_TABLE_NAME."'") == NINJA_FORMS_TABLE_NAME) {
				$wpdb->query("DROP TABLE ".NINJA_FORMS_TABLE_NAME);
			}

			if($wpdb->get_var("SHOW TABLES LIKE '".NINJA_FORMS_FIELDS_TABLE_NAME."'") == NINJA_FORMS_FIELDS_TABLE_NAME) {
				$wpdb->query("DROP TABLE ".NINJA_FORMS_FIELDS_TABLE_NAME);
			}

			if($wpdb->get_var("SHOW TABLES LIKE '".NINJA_FORMS_SUBS_TABLE_NAME."'") == NINJA_FORMS_SUBS_TABLE_NAME) {
				$wpdb->query("DROP TABLE ".NINJA_FORMS_SUBS_TABLE_NAME);
			}
		}
	}


	$sql = "CREATE TABLE IF NOT EXISTS ".NINJA_FORMS_TABLE_NAME." (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `data` longtext CHARACTER SET utf8 NOT NULL,
	  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";

	dbDelta($sql);

	$sql = "CREATE TABLE IF NOT EXISTS ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`row_type` int(11) NOT NULL,
	`type` varchar(255) CHARACTER SET utf8 NOT NULL,
	`order` int(11) NOT NULL,
	`data` longtext CHARACTER SET utf8 NOT NULL,
	`name` varchar(255) CHARACTER SET utf8 NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

	dbDelta($sql);

	$email_address = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Email Address' ), ARRAY_A );
	if( !isset($email_address['id']) ){
		$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
		(1, 0, \'_text\', 0, \'a:11:{s:5:\"label\";s:13:\"Email Address\";s:9:\"label_pos\";s:4:\"left\";s:13:\"default_value\";s:0:\"\";s:4:\"mask\";s:0:\"\";s:10:\"datepicker\";s:1:\"0\";s:5:\"email\";s:1:\"1\";s:10:\"send_email\";s:1:\"1\";s:3:\"req\";s:1:\"0\";s:5:\"class\";s:0:\"\";s:9:\"show_help\";s:1:\"0\";s:9:\"help_text\";s:0:\"\";}\', \'Email Address\')';
		$wpdb->query($sql);
	}

	$state_dropdown = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'State Dropdown' ), ARRAY_A );
	if( !isset($state_dropdown['id']) ){
		$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
		(2, 0, \'_list\', 0, \'a:10:{s:5:\"label\";s:14:\"State Dropdown\";s:9:\"label_pos\";s:4:\"left\";s:9:\"list_type\";s:8:\"dropdown\";s:10:\"multi_size\";s:1:\"5\";s:15:\"list_show_value\";s:1:\"1\";s:4:\"list\";a:1:{s:7:\"options\";a:51:{i:0;a:3:{s:5:\"label\";s:7:\"Alabama\";s:5:\"value\";s:2:\"AL\";s:8:\"selected\";s:1:\"0\";}i:1;a:3:{s:5:\"label\";s:6:\"Alaska\";s:5:\"value\";s:2:\"AK\";s:8:\"selected\";s:1:\"0\";}i:2;a:3:{s:5:\"label\";s:7:\"Arizona\";s:5:\"value\";s:2:\"AZ\";s:8:\"selected\";s:1:\"0\";}i:3;a:3:{s:5:\"label\";s:8:\"Arkansas\";s:5:\"value\";s:2:\"AR\";s:8:\"selected\";s:1:\"0\";}i:4;a:3:{s:5:\"label\";s:10:\"California\";s:5:\"value\";s:2:\"CA\";s:8:\"selected\";s:1:\"0\";}i:5;a:3:{s:5:\"label\";s:8:\"Colorado\";s:5:\"value\";s:2:\"CO\";s:8:\"selected\";s:1:\"0\";}i:6;a:3:{s:5:\"label\";s:11:\"Connecticut\";s:5:\"value\";s:2:\"CT\";s:8:\"selected\";s:1:\"0\";}i:7;a:3:{s:5:\"label\";s:8:\"Delaware\";s:5:\"value\";s:2:\"DE\";s:8:\"selected\";s:1:\"0\";}i:8;a:3:{s:5:\"label\";s:20:\"District of Columbia\";s:5:\"value\";s:2:\"DC\";s:8:\"selected\";s:1:\"0\";}i:9;a:3:{s:5:\"label\";s:7:\"Florida\";s:5:\"value\";s:2:\"FL\";s:8:\"selected\";s:1:\"0\";}i:10;a:3:{s:5:\"label\";s:7:\"Georgia\";s:5:\"value\";s:2:\"GA\";s:8:\"selected\";s:1:\"0\";}i:11;a:3:{s:5:\"label\";s:6:\"Hawaii\";s:5:\"value\";s:2:\"HI\";s:8:\"selected\";s:1:\"0\";}i:12;a:3:{s:5:\"label\";s:5:\"Idaho\";s:5:\"value\";s:2:\"ID\";s:8:\"selected\";s:1:\"0\";}i:13;a:3:{s:5:\"label\";s:8:\"Illinois\";s:5:\"value\";s:2:\"IL\";s:8:\"selected\";s:1:\"0\";}i:14;a:3:{s:5:\"label\";s:7:\"Indiana\";s:5:\"value\";s:2:\"IN\";s:8:\"selected\";s:1:\"0\";}i:15;a:3:{s:5:\"label\";s:4:\"Iowa\";s:5:\"value\";s:2:\"IA\";s:8:\"selected\";s:1:\"0\";}i:16;a:3:{s:5:\"label\";s:6:\"Kansas\";s:5:\"value\";s:2:\"KS\";s:8:\"selected\";s:1:\"0\";}i:17;a:3:{s:5:\"label\";s:8:\"Kentucky\";s:5:\"value\";s:2:\"KY\";s:8:\"selected\";s:1:\"0\";}i:18;a:3:{s:5:\"label\";s:9:\"Louisiana\";s:5:\"value\";s:2:\"LA\";s:8:\"selected\";s:1:\"0\";}i:19;a:3:{s:5:\"label\";s:5:\"Maine\";s:5:\"value\";s:2:\"ME\";s:8:\"selected\";s:1:\"0\";}i:20;a:3:{s:5:\"label\";s:8:\"Maryland\";s:5:\"value\";s:2:\"MD\";s:8:\"selected\";s:1:\"0\";}i:21;a:3:{s:5:\"label\";s:13:\"Massachusetts\";s:5:\"value\";s:2:\"MA\";s:8:\"selected\";s:1:\"0\";}i:22;a:3:{s:5:\"label\";s:8:\"Michigan\";s:5:\"value\";s:2:\"MI\";s:8:\"selected\";s:1:\"0\";}i:23;a:3:{s:5:\"label\";s:9:\"Minnesota\";s:5:\"value\";s:2:\"MN\";s:8:\"selected\";s:1:\"0\";}i:24;a:3:{s:5:\"label\";s:11:\"Mississippi\";s:5:\"value\";s:2:\"MS\";s:8:\"selected\";s:1:\"0\";}i:25;a:3:{s:5:\"label\";s:8:\"Missouri\";s:5:\"value\";s:2:\"MO\";s:8:\"selected\";s:1:\"0\";}i:26;a:3:{s:5:\"label\";s:7:\"Montana\";s:5:\"value\";s:2:\"MT\";s:8:\"selected\";s:1:\"0\";}i:27;a:3:{s:5:\"label\";s:8:\"Nebraska\";s:5:\"value\";s:2:\"NE\";s:8:\"selected\";s:1:\"0\";}i:28;a:3:{s:5:\"label\";s:6:\"Nevada\";s:5:\"value\";s:2:\"NV\";s:8:\"selected\";s:1:\"0\";}i:29;a:3:{s:5:\"label\";s:12:\"New Hampsire\";s:5:\"value\";s:2:\"NH\";s:8:\"selected\";s:1:\"0\";}i:30;a:3:{s:5:\"label\";s:10:\"New Jersey\";s:5:\"value\";s:2:\"NJ\";s:8:\"selected\";s:1:\"0\";}i:31;a:3:{s:5:\"label\";s:10:\"New Mexico\";s:5:\"value\";s:2:\"NM\";s:8:\"selected\";s:1:\"0\";}i:32;a:3:{s:5:\"label\";s:8:\"New York\";s:5:\"value\";s:2:\"NY\";s:8:\"selected\";s:1:\"0\";}i:33;a:3:{s:5:\"label\";s:14:\"North Carolina\";s:5:\"value\";s:2:\"NC\";s:8:\"selected\";s:1:\"0\";}i:34;a:3:{s:5:\"label\";s:12:\"North Dakota\";s:5:\"value\";s:2:\"ND\";s:8:\"selected\";s:1:\"0\";}i:35;a:3:{s:5:\"label\";s:4:\"Ohio\";s:5:\"value\";s:2:\"OH\";s:8:\"selected\";s:1:\"0\";}i:36;a:3:{s:5:\"label\";s:8:\"Oklahoma\";s:5:\"value\";s:2:\"OK\";s:8:\"selected\";s:1:\"0\";}i:37;a:3:{s:5:\"label\";s:6:\"Oregon\";s:5:\"value\";s:2:\"OR\";s:8:\"selected\";s:1:\"0\";}i:38;a:3:{s:5:\"label\";s:12:\"Pennsylvania\";s:5:\"value\";s:2:\"PA\";s:8:\"selected\";s:1:\"0\";}i:39;a:3:{s:5:\"label\";s:12:\"Rhode Island\";s:5:\"value\";s:2:\"RI\";s:8:\"selected\";s:1:\"0\";}i:40;a:3:{s:5:\"label\";s:14:\"South Carolina\";s:5:\"value\";s:2:\"SC\";s:8:\"selected\";s:1:\"0\";}i:41;a:3:{s:5:\"label\";s:12:\"South Dakota\";s:5:\"value\";s:2:\"SD\";s:8:\"selected\";s:1:\"0\";}i:42;a:3:{s:5:\"label\";s:9:\"Tennessee\";s:5:\"value\";s:2:\"TN\";s:8:\"selected\";s:1:\"0\";}i:43;a:3:{s:5:\"label\";s:5:\"Texas\";s:5:\"value\";s:2:\"TX\";s:8:\"selected\";s:1:\"0\";}i:44;a:3:{s:5:\"label\";s:4:\"Utah\";s:5:\"value\";s:2:\"UT\";s:8:\"selected\";s:1:\"0\";}i:45;a:3:{s:5:\"label\";s:7:\"Vermont\";s:5:\"value\";s:2:\"VT\";s:8:\"selected\";s:1:\"0\";}i:46;a:3:{s:5:\"label\";s:8:\"Virginia\";s:5:\"value\";s:2:\"VA\";s:8:\"selected\";s:1:\"0\";}i:47;a:3:{s:5:\"label\";s:10:\"Washington\";s:5:\"value\";s:2:\"WA\";s:8:\"selected\";s:1:\"0\";}i:48;a:3:{s:5:\"label\";s:13:\"West Virginia\";s:5:\"value\";s:2:\"WV\";s:8:\"selected\";s:1:\"0\";}i:49;a:3:{s:5:\"label\";s:9:\"Wisconsin\";s:5:\"value\";s:2:\"WI\";s:8:\"selected\";s:1:\"0\";}i:50;a:3:{s:5:\"label\";s:7:\"Wyoming\";s:5:\"value\";s:2:\"WY\";s:8:\"selected\";s:1:\"0\";}}}s:3:\"req\";s:1:\"0\";s:5:\"class\";s:0:\"\";s:9:\"show_help\";s:1:\"0\";s:9:\"help_text\";s:0:\"\";}\', \'State Dropdown\')';
		$wpdb->query($sql);
	}

	$anti_spam = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Anti-Spam' ), ARRAY_A );
	if( !isset($anti_spam['id']) ){
		$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
		(3, 0, \'_spam\', 0, \'a:6:{s:9:"label_pos";s:4:"left";s:5:"label";s:18:"Anti-Spam Question";s:6:"answer";s:16:"Anti-Spam Answer";s:5:"class";s:0:"";s:9:"show_help";s:1:"0";s:9:"help_text";s:0:"";}\', \'Anti-Spam\')';
		$wpdb->query($sql);
	}

	$submit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE name = %s AND row_type = 0", 'Submit' ), ARRAY_A );
	if( !isset($submit['id']) ){
		$sql = 'INSERT INTO `'.NINJA_FORMS_FAV_FIELDS_TABLE_NAME.'` (`id`, `row_type`, `type`, `order`, `data`, `name`) VALUES
		(4, 0, \'_submit\', 0, \'a:4:{s:5:\"label\";s:6:\"Submit\";s:5:\"class\";s:0:\"\";s:9:\"show_help\";s:1:\"0\";s:9:\"help_text\";s:0:\"\";}\', \'Submit\');';
		$wpdb->query($sql);
	}

	$sql = "CREATE TABLE IF NOT EXISTS ".NINJA_FORMS_FIELDS_TABLE_NAME." (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `form_id` int(11) NOT NULL,
	  `type` varchar(255) CHARACTER SET utf8 NOT NULL,
	  `order` int(11) NOT NULL,
	  `data` longtext CHARACTER SET utf8 NOT NULL,
	  `fav_id` int(11) DEFAULT NULL,
	  `def_id` int(11) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";

	dbDelta($sql);

	$sql = "CREATE TABLE IF NOT EXISTS ".NINJA_FORMS_SUBS_TABLE_NAME." (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) DEFAULT NULL,
	  `form_id` int(11) NOT NULL,
	  `status` int(11) NOT NULL,
	  `action` varchar(255) NOT NULL,
	  `data` longtext CHARACTER SET utf8 NOT NULL,
	  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;";

	dbDelta($sql);

	if( version_compare( $current_version, '2.0' , '<' ) ){
		if( isset( $plugin_settings['upload_dir'] ) ){
			$base_upload_dir = $plugin_settings['upload_dir'];
		}else{
			$base_upload_dir = '';
		}
		if( isset( $plugin_settings['upload_size'] ) ){
			$max_file_size = $plugin_settings['upload_size'];
		}else{
			$max_file_size = 2;
		}

		$opt = array(
			'license_key' => '',
			'license_status' => 'inactive',
			'date_format' => 'm/d/Y',
			'currency_symbol' => '$',
			'clear_complete' => 1,
			'hide_complete' => 1,
			'req_div_label' => __('Fields marked with a * are required.', 'ninja-forms'),
			'req_field_symbol' => '*',
			'req_error_label' => __( 'Please ensure all required fields are completed.', 'ninja-forms' ),
			'req_field_error' => __( 'This is a required field.', 'ninja-forms' ),
			'spam_error' => __( 'Please answer the anti-spam question correctly.', 'ninja-forms' ),
			'honeypot_error' => __('If you are a human, please leave this field blank.', 'ninja-forms' ),
			'timed_submit_error' => __('If you are a human, please slow down.', 'ninja-forms' ),
			'javascript_error' => __( 'You need JavaScript to submit this form. Please enable it and try again.', 'ninja-forms' ),
			'invalid_email' => __( 'Please enter a valid email address.', 'ninja-forms' ),
			'process_label' => __('Processing', 'ninja-forms'),
			'login_link' => __('Login', 'ninja-forms'),
			'username_label' => __('Username', 'ninja-forms'),
			'reset_password' => __('Reset Password (Opens in a new window)', 'ninja-forms'),
			'password_label' => __('Password', 'ninja-forms'),
			'repassword_label' => __('Re-enter Password', 'ninja-forms'),
			'password_mismatch' => __('Passwords do not match.', 'ninja-forms'),
			'login_button_label' => __('Login', 'ninja-forms'),
			'cancel_button_label' => __('Cancel', 'ninja-forms'),
			'login_error' => __('Login failed, please try again.', 'ninja-forms'),
			'register_link' => __('Register', 'ninja-forms'),
			'email_label' => __('Email Address', 'ninja-forms'),
			'register_button_label' => __('Register', 'ninja-forms'),
			'register_error' => __('There was an error registering you for this site.', 'ninja-forms'),
			'register_spam_q' => __('4 + 9 = ', 'ninja-forms'),
			'register_spam_a' => __('13', 'ninja-forms'),
			'register_spam_error' => __('Please answer the anti-spam question correctly.', 'ninja-forms'),
			'msg_format' => 'inline',
			'base_upload_dir' => $base_upload_dir,
			'max_file_size' => $max_file_size,
		);

	}else{
		$opt = $plugin_settings;
	}


	$title = ninja_forms_get_preview_page_title();
    $preview_page = get_page_by_title( $title );
	if( !$preview_page ) {
		// Create preview page object
		$preview_post = array(
			'post_title' => $title,
			'post_content' => 'This is a preview of how this form will appear on your website',
			'post_status' => 'draft',
			'post_type' => 'page'
		);

		// Insert the page into the database
		$page_id = wp_insert_post( $preview_post );
 	}else{
 		$page_id = $preview_page->ID;
 	}

	$opt['preview_id'] = $page_id;

 	$opt['version'] = NINJA_FORMS_VERSION;

 	update_option( "ninja_forms_settings", $opt );


 	if( is_array( $forms ) AND !empty( $forms ) ){
 		foreach( $forms as $form ){
	 		$form['data'] = serialize( $form['data'] );
	 		if ( isset( $form['field'] ) ){
	 			$form_fields = $form['field'];
	 		}else{
	 			$form_fields = '';
	 		}

	 		if( isset( $form['subs'] ) ){
	 			$form_subs = $form['subs'];
	 		}else{
	 			$form_subs = '';
	 		}

	 		unset( $form['field'] );
	 		unset( $form['subs'] );

			$wpdb->insert(NINJA_FORMS_TABLE_NAME, $form);
			$form_id = $wpdb->insert_id;

			if( is_array( $form_fields ) AND !empty( $form_fields ) ){
				for( $x=0; $x < count( $form_fields ); $x++ ) {
					$form_fields[$x]['form_id'] = $form_id;
					$form_fields[$x]['data'] = serialize( $form_fields[$x]['data'] );
					unset( $form_fields[$x]['id'] );
					if( isset( $form_fields[$x]['old_id'] ) ){
						$old_id = $form_fields[$x]['old_id'];
						unset( $form_fields[$x]['old_id'] );
					}
					$wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $form_fields[$x] );
					$new_id = $wpdb->insert_id;
					if( is_array( $form_subs ) AND !empty( $form_subs ) ){
						for ($i=0; $i < count( $form_subs ); $i++) {
							$form_subs[$i]['form_id'] = $form_id;
							if( is_array( $form_subs[$i]['data'] ) AND !empty( $form_subs[$i]['data'] ) ){
								for ($y=0; $y < count( $form_subs[$i]['data'] ); $y++){
									if( isset( $form_subs[$i]['data'][$y]['old_id'] ) AND $form_subs[$i]['data'][$y]['old_id'] == $old_id ){
										unset( $form_subs[$i]['data'][$y]['old_id'] );
										$form_subs[$i]['data'][$y]['field_id'] = $new_id;
									}
								}
							}
						}
					}
				}
			}

			if( is_array( $form_subs ) AND !empty( $form_subs ) ){
				for ($i=0; $i < count( $form_subs ); $i++) {
					$form_subs[$i]['data'] = serialize( $form_subs[$i]['data'] );
					$wpdb->insert( NINJA_FORMS_SUBS_TABLE_NAME, $form_subs[$i] );
				}
			}
 		}
 	}

 	// check for an existing form
 	$starter_form_exists = ninja_forms_starter_form_exists();

 	if ( ! $starter_form_exists ) {
 		// if a starter form doesn't exist them create it
 		ninja_forms_add_starter_form();
 	}


}

function ninja_forms_activation_old_forms_check(){
	global $wpdb;
	//Get the current plugin settings.
	$plugin_settings = nf_get_settings();

	$current_version = $plugin_settings['version'];

	//if( version_compare( $current_version, '2.0' , '<' ) ){

		if($wpdb->get_var("SHOW COLUMNS FROM ".NINJA_FORMS_TABLE_NAME." LIKE 'title'") == 'title') {
			$all_forms = $wpdb->get_results( "SELECT * FROM ".NINJA_FORMS_TABLE_NAME, ARRAY_A );
			if( is_array( $all_forms ) AND !empty( $all_forms ) ){
				$forms = array();
				$x = 0;
				foreach( $all_forms as $form ){
					$form_id = $form['id'];
					$forms[$x]['data']['form_title'] = $form['title'];
					if( $form['show_title'] == 'checked' ){
						$show_title = 1;
					}else{
						$show_title = 0;
					}
					$forms[$x]['data']['show_title'] = $show_title;
					$admin_mailto = explode(',', $form['mailto'] );
					$forms[$x]['data']['admin_mailto'] = $admin_mailto;
					$forms[$x]['data']['user_subject'] = $form['subject'];
					$forms[$x]['data']['success_msg'] = $form['success_msg'];
					if( $form['send_email'] == 'checked' ){
						$send_email = 1;
					}else{
						$send_email = 0;
					}
					$forms[$x]['data']['send_email'] = $send_email;
					$forms[$x]['data']['landing_page'] = $form['landing_page'];
					$form['append_page'] = unserialize( $form['append_page'] );
					if( isset( $form['append_page'][0] ) ){
						$append_page = $form['append_page'][0];
					}else{
						$append_page = '';
					}
					$forms[$x]['data']['append_page'] = $append_page;
					$forms[$x]['data']['email_from'] = $form['email_from'];
					$forms[$x]['data']['user_email'] = $form['email_msg'];
					if( $form['multi'] == 'checked' ){
						$multi_part = 1;
					}else{
						$multi_part = 0;
					}
					$forms[$x]['data']['multi_part'] = $multi_part;
					if( $form['post'] == 'checked' ){
						$create_post = 1;
					}else{
						$create_post = 0;
					}
					$forms[$x]['data']['create_post'] = $create_post;
					$form['post_options'] = unserialize( $form['post_options'] );
					$forms[$x]['data']['post_logged_in'] = $form['post_options']['login'];
					$forms[$x]['data']['post_as'] = $form['post_options']['user'];
					$forms[$x]['data']['post_type'] = $form['post_options']['post_type'];
					$forms[$x]['data']['post_status'] = $form['post_options']['post_status'];
					if( $form['save_status'] == 'checked' ){
						$save_progress = 1;
					}else{
						$save_progress = 0;
					}

					$forms[$x]['data']['save_progress'] = $save_progress;
					$form['save_status_options'] = unserialize( $form['save_status_options'] );
					$forms[$x]['data']['clear_incomplete_saves'] = $form['save_status_options']['delete'];
					$forms[$x]['data']['save_msg'] = $form['save_status_options']['msg'];

					$form_fields = $wpdb->get_results("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = ".$form_id, ARRAY_A );
					if( is_array( $form_fields ) AND !empty( $form_fields ) ){
						$y = 0;
						foreach( $form_fields as $field ){
							$unset = false;
							$field_type = $field['type'];
							$forms[$x]['field'][$y]['old_id'] = $field['id'];
							$forms[$x]['field'][$y]['form_id'] = $field['form_id'];
							$forms[$x]['field'][$y]['order'] = $field['field_order'];
							$forms[$x]['field'][$y]['data']['label'] = $field['label'];

							$field['extra'] = unserialize( $field['extra'] );

							if( isset( $field['value'] ) ){
								$default_value = $field['value'];
							}else{
								$default_value = '';
							}

							if( $default_value == 'none' ){
								$default_value = '';
							}


							switch( $field_type ){
								case 'textbox':
									$field_type = '_text';
									break;
								case 'list':
									$field_type = '_list';
									$forms[$x]['field'][$y]['data']['multi_size'] = 5;
									break;
								case 'checkbox':
									$field_type = '_checkbox';
									break;
								case 'textarea':
									$field_type = '_textarea';
									break;
								case 'hr':
									$field_type = '_hr';
									break;
								case 'heading':
									$default_value = $field['label'];
									$forms[$x]['field'][$y]['data']['label'] = 'Text';
									$field_type = '_desc';
									break;
								case 'spam':
									$field_type = '_spam';
									$forms[$x]['field'][$y]['data']['spam_answer'] = $default_value;
									break;
								case 'desc':
									$field_type = '_desc';
									break;
								case 'submit':
									$field_type = '_submit';
									break;
								case 'hidden':
									$field_type = '_hidden';
									break;
								case 'file':
									$field_type = '_upload';
									if( isset( $field['extra']['extra']['upload_types'] ) ){
										$forms[$x]['field'][$y]['data']['upload_types'] = $field['extra']['extra']['upload_types'];
									}
									if( isset( $field['extra']['extra']['upload_rename'] ) ){
										$forms[$x]['field'][$y]['data']['upload_rename'] = $field['extra']['extra']['upload_rename'];
									}
									if( isset( $field['extra']['extra']['email_attachment'] ) ){
										$forms[$x]['field'][$y]['data']['email_attachment'] = $field['extra']['extra']['email_attachment'];
									}
									$forms[$x]['field'][$y]['data']['upload_multi'] = 0;
									break;
								case 'divider':
									$field_type = '_page_divider';
									$forms[$x]['field'][$y]['data']['page_name'] = $field['label'];
									break;
								case 'progressbar':
									$forms[$x]['data']['mp_progress_bar'] = 1;
									$unset = true;
									break;
								case 'posttitle':
									$field_type = '_post_title';
									break;
								case 'postcontent':
									$field_type = '_post_content';
									break;
								case 'postcat':
									$field_type = '_post_category';
									break;
								case 'posttags':
									$field_type = '_post_tags';
									break;

							}

							$forms[$x]['field'][$y]['type'] = $field_type;

							$forms[$x]['field'][$y]['data']['default_value'] = $default_value;
							$forms[$x]['field'][$y]['data']['req'] = $field['req'];
							$forms[$x]['field'][$y]['data']['class'] = $field['class'];
							$forms[$x]['field'][$y]['data']['help_text'] = $field['help'];

							if( isset( $field['extra']['extra']['desc_cont'] ) ){
								$forms[$x]['field'][$y]['data']['desc_el'] = $field['extra']['extra']['desc_cont'];
							}
							if( isset( $field['extra']['extra']['label_pos'] ) ){
								$forms[$x]['field'][$y]['data']['label_pos'] = $field['extra']['extra']['label_pos'];
							}else{
								$forms[$x]['field'][$y]['data']['label_pos'] = 'left';
							}

							if( isset( $field['extra']['extra']['show_help'] ) ){
								if( $field['extra']['extra']['show_help'] == 'checked' ){
									$show_help = 1;
								}else{
									$show_help = 0;
								}
							}else{
								$show_help = 0;
							}

							$forms[$x]['field'][$y]['data']['show_help'] = $show_help;

							if( isset( $field['extra']['extra']['meta_key'] ) ){
								$forms[$x]['field'][$y]['data']['meta_value'] = $field['extra']['extra']['meta_key'];
							}
							if( isset( $field['extra']['extra']['rte'] ) ){
								if( $field['extra']['extra']['rte'] == 'checked' ){
									$textarea_rte = 1;
								}else{
									$textarea_rte = 0;
								}

								$forms[$x]['field'][$y]['data']['textarea_rte'] = $textarea_rte;
							}
							if( isset( $field['extra']['extra']['list_type'] ) ){
								$forms[$x]['field'][$y]['data']['list_type'] = $field['extra']['extra']['list_type'];
							}
							if( isset( $field['extra']['extra']['list_item'] ) AND is_array( $field['extra']['extra']['list_item'] ) ){
								$n = 0;
								foreach( $field['extra']['extra']['list_item'] as $item ){
									$forms[$x]['field'][$y]['data']['list']['options'][$n]['label'] = $item;
									$forms[$x]['field'][$y]['data']['list']['options'][$n]['value'] = $item;
									$n++;
								}
							}

							if( $unset ){
								unset( $forms[$x]['field'][$y] );
								$y--;
							}
							$y++;
						}
					}

					$sub_results = $wpdb->get_results( "SELECT * FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE `form_id` = ".$form_id, ARRAY_A );
					if( is_array( $sub_results ) AND !empty( $sub_results ) ){
						$i = 0;
						foreach( $sub_results as $sub ){

							if( $sub['sub_status'] == 'complete' ){
								$status = 1;
							}else{
								$status = 0;
							}
							$forms[$x]['subs'][$i]['status'] = $status;
							$forms[$x]['subs'][$i]['user_id'] = $sub['user_id'];

							if( $status == 0 ){
								$forms[$x]['subs'][$i]['action'] = 'save';
								if( isset( $sub['email'] ) ){
									$user = get_user_by( 'email', $sub['email'] );
									if( $user ){
										$forms[$x]['subs'][$i]['user_id'] = $user->ID;
									}else{
										$password = wp_generate_password( 12, true );
										$userdata = array(
											'user_login' => $sub['email'],
											'user_pass' => $password,
											'user_email' => $sub['email'],
											'role' => 'subscriber',
										);
										$user_id = wp_insert_user($userdata);
										$forms[$x]['subs'][$i]['user_id'] = $user_id;
										$blog_name = get_bloginfo( 'name' );
										$reg_subject = $blog_name.' '.__( 'Ninja Forms Password', 'ninja-forms' );
										$reg_msg = __( 'You are receiving this email because you have an incomplete form. Your username is now your email address and your password has been reset. It is now ', 'ninja-forms' );
										wp_mail( $sub['email'], $reg_subject, $reg_msg . $password );
									}
								}
							}else{
								$forms[$x]['subs'][$i]['action'] = 'submit';
							}


							$forms[$x]['subs'][$i]['form_id'] = $sub['form_id'];
							$forms[$x]['subs'][$i]['date_updated'] = $sub['date_updated'];

							$form_values = unserialize( $sub['form_values'] );
							if( is_array( $form_values ) AND !empty( $form_values ) ){
								$n = 0;
								foreach( $form_values as $data ){
									$user_value = $data['value'];
									foreach( $forms[$x]['field'] as $field ){
										if( $field['old_id'] == $data['id'] ){
											if( $field['type'] == '_upload' ){
												$user_value = array();
												$user_value[0]['user_file_name'] = $data['value'];
												$user_value[0]['file_name'] = $data['value'];
												$user_value[0]['file_path'] = '';
												$user_value[0]['file_url'] = '';
											}
										}
									}
									$forms[$x]['subs'][$i]['data'][$n]['old_id'] = $data['id'];
									$forms[$x]['subs'][$i]['data'][$n]['user_value'] = $user_value;
									$n++;
								}
							}
							$i++;
						}
					}
					$x++;
				}
			}
		}else{
			return false;
		}
	//}else{
		//return false;
	//}
	return $forms;
}


/*
 * Check to see if a form exists.
 *
 * @since 2.3.3
 * @return bool
 */
function ninja_forms_starter_form_exists() {
	$forms = ninja_forms_get_all_forms();
	if( empty( $forms ) ) {
		return false;
	}
	return true;
}


/*
 * Add a starter form. Return the ID.
 *
 * @since 2.3.3
 * @returns int
 */
function ninja_forms_add_starter_form() {
    // load starter form
    $file = file_get_contents( NINJA_FORMS_DIR . "/includes/forms/starter-form.nff" );
    $file = apply_filters( 'ninja_forms_starter_form_contents', $file );

    // create new form
    ninja_forms_import_form( $file );
}


/*
 * Get the preview page title
 *
 * @since 2.5.2
 * @returns string
 */
function ninja_forms_get_preview_page_title() {
    return apply_filters( 'ninja_forms_preview_page_title', 'ninja_forms_preview_page' );
}

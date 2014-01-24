<?php // the default and option related stuff
/* -------------------------------------------------------------------------------------------------------------*/	
function amr_excluded_userkey ($i) {
global $excluded_nicenames;
/* exclude some less than useful keys to reduce the list a bit */
		if (!empty($excluded_nicenames[$i])) { return (true);}

		if (stristr ($i, 'autosave_draft_ids')) return (true);
		if (stristr ($i, 'time')) return (false);  // maybe last login? or at least last time screen shown
		if (stristr ($i, 'user-settings')) return (true);
		if (stristr ($i, 'user_pass')) return (true);
		
//		if (stristr ($i, 'user_activation_key')) return (true); //shows if have done lost password
		if (stristr ($i, 'admin_color')) return (true);
		if (stristr ($i, 'meta-box-order_')) return (true);	
		if (stristr ($i, 'last_post_id')) return (true);	
		if (stristr ($i, 'nav_menu')) return (true);
//		if (stristr ($i, 'default_password_nag')) return (true);		//may want to use this to tell if they have reset their password

// DEPRECATED:
/* and exclude some deprecated fields, since wordpress creates both for backward compatibility ! */		
		if (stristr ($i, 'user_description')) return (true);
		if (stristr ($i, 'user_lastname')) return (true);
		if (stristr ($i, 'user_firstname')) return (true);
		if (stristr ($i, 'user_level')) return (true);
		if (stristr ($i, 'metabox')) return (true);		
		if (stristr ($i, 'comment_shortcuts')) return (true);	
		if (stristr ($i, 'plugins_last_view')) return (true);	
		if (stristr ($i, 'rich_editing')) return (true);
		if (stristr ($i, 'closedpostboxes')) return (true);
		if (stristr ($i, 'columnshidden')) return (true);
		if (stristr ($i, 'screen_layout')) return (true);
		if (stristr ($i, 'metaboxhidden_')) return (true);	
		if (stristr ($i, 'metaboxorder_')) return (true);	
		if (stristr ($i, '_per_page')) return (true);		
		if (stristr ($i, 'usersettings')) return (true);

		return (false);		
	}
/* ---------------------------------------------------------------------------*/
function amr_linktypes () {
	$linktypes = array (
		'none' 				=> __('none', 'amr-users'),
		'edituser'			=> __('edit user', 'amr-users'),
		'mailto'			=> __('mail to', 'amr-users'),
		'postsbyauthor' 	=> __('posts by author in admin', 'amr-users'),
		'authorarchive' 	=> __('author archive', 'amr-users'),
		'commentsbyauthor' 	=> __('comments by author (*)', 'amr-users'), // requires extra functionality
		'url' 				=> __('users url', 'amr-users'),
		'wplist' 			=> __('wp user list filtered by user', 'amr-users'),//eg for other user details that may be in list, but not in  ?
		'bbpressprofile' 	=> __('bbpress user profile page', 'amr-users')
	
		);

	$linktypes = apply_filters('amr-users-linktypes',$linktypes); 
	return ($linktypes);
	}
/* ---------------------------------------------------------------------------*/	
function ameta_defaultnicenames () {
global $orig_mk;

unset($nicenames);
$nicenames = array (
	'ID' 					=> __('Id', 'amr-users'),
	'avatar' 				=> __('Avatar','amr-users'),
	'user_login' 			=> __('User name','amr-users'),
	'user_nicename'			=> __('Nice name','amr-users'),
	'user_email' 			=> __('Email','amr-users'),
	'user_url' 				=> __('Url','amr-users'),
	'user_registered' 		=> __('Registered days ago','amr-users'),
	'user_registration_date' => __('Registration date','amr-users'),
	'user_status' 			=> __('User status','amr-users'),
	'display_name' 			=> __('Display Name','amr-users'),
	'first_name' 			=> __('First name','amr-users'),
	'last_name' 			=> __('Last name','amr-users'),
	'nick_name' 			=> __('Nick Name','amr-users'),
	'post_count' 			=> __('Post Count','amr-users'),
	'comment_count' 		=> __('Comment Count','amr-users'),
	'first_role' 			=> __('First Role', 'amr-users'),
	//'ausers_last_login' => __('Last Login', 'amr-users')
);


return ($nicenames);
}
/* ---------------------------------------------------------------*/
function ameta_default_list_options () { // default lists  $aopt
/* setup some list defaults */

ameta_cache_enable(); //in case cache tables got deleted
ameta_cachelogging_enable();

if (amr_is_network_admin()) {
	$default = array (
	'list' => 
		array ( '1' => 
				array(
				'selected' => array ( 
					'user_login' => 2, 
					'user_email' => 3,
					'user_registered' => 5,
					'blogcount_as_subscriber' => 10,
					'blogcount_as_administrator' => 15,
					'bloglist_as_subscriber' => 20,
					'bloglist_as_administrator' => 25,		
					'bloglist' => 100
					),
				'sortdir' => array ( /* some fields should always be sorted in a certain order, so keep that fact, even if not sorting by it*/
					'user_registered' => 'SORT_DESC'),
				'sortby' => array ( 
					'user_registered' => '1'
					),
				'before' => array (    
					'last_name' => '<br />'
					),			
				'links' => array (    
					'user_email' => 'mailto',
					'user_login' => 'edituser',
					'bloglist' => 'wplist'
					),
				)
		)
		);


}
else {
		$sortdir = array ( /* some fields should always be sorted in a certain order, so keep that fact, even if not sorting by it*/
							'user_registered' => 'SORT_DESC'
							);

		$default = array (
			'list' => 
				array ( '1' => 
						array(
						'selected' => array ( 
							'avatar' => 10, 
							'user_login' => 20, 
							'user_email' => 30,
							'display_name' => 40,
							'user_registered' => 50,
							'first_role' => 60
							),
						'sortdir' => array ( /* some fields should always be sorted in a certain order, so keep that fact, even if not sorting by it*/
							'user_registered' => 'SORT_DESC'),
						'sortby' => array ( 
							'user_email' => '1'
							),				
						'links' => array (    
							'user_email' => 'mailto',
							'user_login' => 'edituser', 	
							'user_url' => 'url', 	
							'avatar' => 'authorarchive',
							
							),
						'excluded' => array ( 
							'ID' => '1', 
							'first_role' => 'Administrator'
							),	
						),
						'2' => 
						array(
						'selected' => array ( 
							'avatar' => 10, 
							'display_name' => 20,
							'user_url' => 30,
							'user_registered' => 40
							),
						'excluded' => array ( 
							'ID' => '1', 
							),
						'sortby' => array ( 
							'user_registered' => '2'
							),
						'links' => array (    
							'avatar' => 'url',
							'display_name' => 'authorarchive',
							'url' => 'url',

							)					
						)
					)
		//			,
		//	'stats' => array ( '1' => 
		//				array(
		//					'selected' => $selected,
		//					'totals' => array ( /* within the selected */
		//						'ym_status' ,
		//						'account_type'
		//						)
		//				),
		//			)
				);
	}
	ausers_update_option('amr-users',$default);
	
	return ($default);

}	
/* ----------------------------------------------------------------------------------------------*/	
function ameta_default_main () {
/* setup some defaults */

$default = array (
	'notonuserupdate' => true,
	'checkedpublic' => true, /* so message should only show up if we have retrieved options from DB and did not have this field - must have been an upgrade, not a reset, and not a new activation. */
    'rows_per_page' => 20,
	'avatar_size' => 16,
	'no_credit' => 'no_credit',
	'csv_text' =>  ('<img src="'.plugins_url('amr-users/images/file_export.png').'" alt="'.__('Csv', 'amr-users') .'"/>'),
	'refresh_text' =>  ('<img src="'.plugins_url('amr-users/images/rebuild.png').'" alt="'.__('Refresh user list cache', 'amr-users') .'"/>'),
	'noaccess_text' => __('You do not have access to this list, or are not logged in.', 'amr-users'),
	//'givecreditmessage' => amr_users_random_message(),
	'sortable' =>	array ( '1' => true,
				'2' => true,
				),		
	'names' => 
		array ( '1' => __("Users: Details", 'amr-users'),
				'2' => __("Users: Directory", 'amr-users'),
				),
	'html_type' =>
		array ( '1' => 'table',
				'2' => 'simple',
				),	
	'filter_html_type' =>
		array ( '1' => 'intableheader',
				'2' => 'above',
				),				
	'public' => 	
		array ( '1' => false,
				'2' => true,
				),
	'show_refresh' => 	
		array ( '1' => false,
				'2' => false,
				),			
	'show_headings'	=>
		array ( '1' => true,
				'2' => false,
				),		
	'list_avatar_size' => 	
		array ( '1' => 16,
				'2' => 100,
				),
	'show_pagination'	=>
		array ( '1' => true,
				'2' => true,
				),				
	);
	
	if (amr_is_network_admin()) {
		unset($default['names']['2']);
		unset($default['names']['3']);
	}
	ausers_update_option('amr-users-main', $default);			
	return ($default);

}	
/* --------------------------------------------------------------------------------------------*/	
function ausers_get_option($option) { // allows user reports to be run either at site level and/or at blog level
global $ausersadminurl, $amr_nicenames;
	
	if (amr_is_network_admin() )
		$result = get_site_option('network_'.$option);
	else 
		$result = get_option($option);	

	if (empty($result)) { // it's new, get defaults
		//if ($option == 'amr-users-no-lists' ) 	return ameta_default_main(); // old - leave for upgrade check 
		if ($option == 'amr-users-main' ) 		{ // and it's empty
			//-------------------------
			//if (WP_DEBUG) echo '<br />Renaming stored option "amr-users-no-lists" to "amr-users-main" ';
			$amain = get_site_option('amr-users-no-lists');   // might return default ok, if not will have done upgrade check 
			if (empty($amain)) {
				$amain = ausers_get_option('amr-users-no-lists');
				if (empty($amain)) {
					$amain = ameta_default_main();
				}
			}

			$amain['version'] = AUSERS_VERSION;
			ausers_update_option('amr-users-main',$amain);
			ausers_delete_option('amr-users-no-lists');
			return $amain;
			//-------------------------
		}
		if ($option == 'amr-users' ) 					
			return (ameta_default_list_options());
		if ($option == 'amr-users-nicenames-excluded') 	
			return array(
				'attachment_count' 		=> true,
				'activation_key' 		=> true,
				'dismissed_wp_pointers'	=> true,
				'default_password_nag'	=> true,
				'nav_menu_item_count'	=> true,
				'revision_count'		=> true,
				'comment_count'			=> true,
				'show_admin_bar_front'	=> true,
				'show_welcome_panel'	=> true,
				'user_activation_key'	=> true,
				'user_status'			=> true,
				'yim'					=> true,
				'aim'					=> true,
				'jabber'				=> true,
				'reply_count'			=> true,
				'topic_count'			=> true,
				'forum_count'			=> true,
				'use_ssl'				=> true
				);
		if ($option == 'amr-users-original-keys') 		return array();
		if ($option == 'amr-users-custom-headings') 	return array();
		if ($option == 'amr-users-prefixes-in-use') 	return array();
		if ($option == 'amr-users-nicenames' ) 	{		
			$amr_nicenames = ameta_defaultnicenames();  			
			}  		
	}		
	return($result);
}
/* ------------------------------------------------------------------------------------------------*/
function ausers_update_option($option, $value) { // allows user reports to be run either at site level and/or at blog level
global $ausersadminurl;

	if (is_network_admin()) {
		$result = update_site_option('network_'.$option, $value);
		if ($result) {
			echo '<br/> Unexpected error updating option: '; var_dump($result);
			
		}
	}

//	if (stristr($ausersadminurl,'network') == FALSE) {	
	//	$result = update_option($option, $value);
//	}
	else {
	
		$result = update_option($option, $value);	
	}
	//if (WP_DEBUG) {	echo 'Option update '.$option;}
	if (!($option== 'amr-users-cache-status')) {
		ausers_delete_htmltransients() ;
		}
	return($result);
}
/* ------------------------------------------------------------------------------------------------*/
function ausers_delete_option($option) { 
global $ausersadminurl;
	
	if (is_network_admin() or (stristr($ausersadminurl,'network'))) 	
		$result = delete_site_option('network_'.$option);
	else 
		$result = delete_option($option);	
	return($result);
}
/* -------------------------------------------------------------------------------------------*/	
function ameta_options (){ // set up all  the options

global $aopt,
	$amain,
	$amr_nicenames, 
	$amr_your_prefixes,
	$excluded_nicenames,
	$ausersadminurl,
	$wpdb;

	if (empty($amain)) 
		$amain 			= ausers_get_option('amr-users-main');
		$amr_your_prefixes 	= ausers_get_option('amr-users-prefixes-in-use');
		$amr_nicenames 		= ausers_get_option('amr-users-nicenames');
		$excluded_nicenames = ausers_get_option('amr-users-nicenames-excluded');

	foreach ($excluded_nicenames as $i=>$v)	{
		if ($v) unset ($amr_nicenames[$i]);
	}

	$aopt = ausers_get_option ('amr-users');

	return;
}

/* -----------------------------------------------------------------------------------*/ 	

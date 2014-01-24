<?php
if (!function_exists('amrmeta_validate_rows_per_page') ) {  // consider using std wp users_per_page
	function amrmeta_validate_rows_per_page()	{ /* basically the number of lists & names */
	global $aopt;
	global $amain;
		
		if (function_exists( 'filter_var') ) {
			$int_ok = (filter_var($_POST["rows_per_page"], FILTER_VALIDATE_INT, 
				array("options" => array("min_range"=>1, "max_range"=>999))));
		}
		else $int_ok = (is_numeric($_POST["rows_per_page"]) ? $_POST["rows_per_page"] : false);
		if ($int_ok) {
			$amain['rows_per_page'] =  $int_ok;
			return (true);
		}			
		else {
			$amain['rows_per_page'] = 25;	
			}
}
}
/* -------------------------------------------------------------------------------------------------------------*/
if (!function_exists('amrmeta_validate_avatar_size') ) {
function amrmeta_validate_avatar_size()	{ /* basically the number of lists & names */
	global $aopt;
	global $amain;
		
		if (function_exists( 'filter_var') ) {
			$int_ok = (filter_var($_POST["avatar_size"], FILTER_VALIDATE_INT, 
				array("options" => array("min_range"=>1, "max_range"=>400))));
		}
		else $int_ok = (is_numeric($_POST["avatar_size"]) ? $_POST["avatar_size"] : false);
		if ($int_ok) {
			$amain['avatar_size'] =  $int_ok;
			return (true);
		}			
		else {
			return (__('Invalid avatar size','amr-users'));	
			}
}
}
/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_mainoptions()	{ 
	global $amain;
	global $aopt;
	
	$amain['no_credit'] = 'no_credit';	
	if (isset($_POST['no_credit']) ) {
		if ($_POST['no_credit'] == 'give_credit') {
		$amain['no_credit'] = 'give_credit';
		$amain['givecreditmessage'] = amr_users_random_message();
		}
	}

	
	if (!empty($_POST["use_wp_query"]) ) {
		$amain['use_wp_query'] = true;
	}
	else $amain['use_wp_query'] = false;
	
	if (isset($_POST["do_not_use_css"]) ) {
		$amain['do_not_use_css'] = true;
	}
	else $amain['do_not_use_css'] = false;
	
	if (isset($_POST['use_css_on_pages']) ) {
		$check = explode(',',$_POST['use_css_on_pages']);
		foreach ($check as $i => $value) {
			
			$check[$i] = intval($value);				
		}
		$amain['use_css_on_pages'] = implode(',',$check);
	}
	else $amain['use_css_on_pages'] = '';
	
	if (isset($_POST['csv_text'])) {
		$return = amrmeta_validate_text('csv_text');
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}
	if (isset($_POST['refresh_text'])) {
		$return = amrmeta_validate_text('refresh_text');
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}
	
	if (isset($_POST["rows_per_page"]) ) {
		$return = amrmeta_validate_rows_per_page();
		if ( is_wp_error($return) )	echo '<h2>'.$return->get_error_message().'</h2>';
	}	

	if (isset($_POST["avatar_size"]) ) { 
		$return = amrmeta_validate_avatar_size();
		if ( is_wp_error($return) )	echo '<h2>'.$return->get_error_message().'</h2>';		
	}
	
	$amain['version'] = AUSERS_VERSION;
	
	if (isset($_POST))	{ 
		ausers_update_option ('amr-users-main', $amain) ;
		//ausers_update_option ('amr-users', $aopt);
	}
	return;
}
/* ---------------------------------------------------------------------*/
function amr_meta_reset() {
global $aopt;
global $amain;
global $amr_nicenames,
	$ausersadminurl;

	if (ausers_delete_option ('amr-users')) 
		echo '<h2>'.__('Deleting number of lists and names in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting number of lists and names in database.','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users-main')) 
		echo '<h2>'.__('Deleting all main settings in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users-nicenames')) 
		echo '<h2>'.__('Deleting all nice name settings in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-nicenames-excluded')) 
		echo '<h2>'.__('Deleting all nice name exclusion settings in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-show-in-wplist')) 
		echo '<h2>'.__('Deleting the show in wp list settings','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users-cache-status')) 
		echo '<h2>'.__('Deleting cache status in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-original-keys')) 
		echo '<h2>'.__('Deleting original keys mapping in database','amr-users').'</h2>';	

	if (ausers_delete_option ('amr-users-custom-headings')) 
		echo '<h2>'.__('Deleting custom-headings in database','amr-users').'</h2>';	
	if (ausers_delete_option ('amr-users-filtering')) 
		echo '<h2>'.__('Deleting amr-users-filtering in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-prefixes-in-use')) 
		echo '<h2>'.__('Deleting amr-users-prefixes-in-use in database','amr-users').'</h2>';
			
	$c = new adb_cache();
	//$c->clear_all_cache();
	$c->deactivate();
	echo '<h2>'.__('All cached listings cleared.','amr-users').'</h2>';
	unset ($aopt);
	unset ($amain);
	unset ($amr_nicenames);
	
	echo '<h2><a href="'.$ausersadminurl.'?page=ameta-admin-general.php&tab=fields'.'">'
	.__('Click to find your user fields again.','amr-users')
	.'</a></h2>';
	die;
}
/* ---------------------------------------------------------------------*/	
function amr_meta_general_page_display() { 
	global $amain;

	//amr_mimic_meta_box('related', 'Related plugins','amru_related', true);

	if (empty($amain)) $amain = ausers_get_option('amr-users-main');

		if (empty($amain['csv_text'])) 
			$amain['csv_text'] = '<img src="'.plugins_url('amr-users/images/file_export.png').'" alt="'.__('Csv','amr-users') .'"/>'; 
	
		if (empty($amain['refresh_text'])) 
			$amain['refresh_text'] = '<img src="'.plugins_url('amr-users/images/rebuild.png').'" alt="'.__('Refresh user list cache','amr-users') .'"/>' ;
		if (empty($amain['noaccess_text']))
		$amain['noaccess_text'] =  __('You do not have access to this list, or are not logged in.', 'amr-users');
		
		if (!(isset ($amain['checkedpublic']))) {
			echo '<input type="hidden" name="checkedpublic" value="true"/>'; }

		if (isset ($amain['do_not_use_css']) and ($amain['do_not_use_css'])) 
			$do_not_use_css = ' checked="checked" ';
		else 
			$do_not_use_css = '';
		
		echo PHP_EOL.'<div class="clear wrap">';	
	
	
		amr_users_say_thanks_opportunity_form();
		echo '<br />';

		
		echo '<h3>';
		_e('How to fetch data?');
		echo '</h3><input type="radio"  name="use_wp_query" value="1" ';
		if (!empty($amain['use_wp_query'])) echo ' checked="checked" ';
		echo '> ';	
		_e('Fetch user data with wp_query? &nbsp; ', 'amr-users');	
		echo ' <em>';_e('WordPress does some extra work which requires more memory','amr-users');echo '</em>';
		echo '<br />';		
	//	echo '</label>';
	//	echo '<label for="use_wp_query">';

		echo '<input type="radio"  name="use_wp_query" value="0" ';
		if (empty($amain['use_wp_query'])) echo ' checked="checked" ';
		echo '> ';
		_e('Fetch user data directly? &nbsp; ', 'amr-users');
		echo ' <em>';_e('This seems to use less memory, better for very large databases.','amr-users');echo '</em>';
		echo '<br /><br />';		
	
		//echo '</label>';
		echo '<h3 id="general">'.__('General & Styling', 'amr-users').'</h3>';		
		echo '<label for="do_not_use_css">';
		_e('No css ', 'amr-users');

		echo '</label>
			<input type="checkbox" size="2" id="do_not_use_css" 
					name="do_not_use_css" ';
		echo empty($amain['do_not_use_css']) ? '' :' checked="checked" '; 
		echo '/>';
		echo '<em> ';
		_e('Do not use css provided, my theme css is good enough', 'amr-users'); 
		echo '</em>';
		echo '<br /><br />';
		if (!empty($amain['do_not_use_css'])) {
			$disabled = ' disabled="disabled" ';
		}
		else { 
			$disabled = '';
		}
		echo '<label for="use_css_on_pages">';
		_e('Use css on these pages only ', 'amr-users'); 
		echo ' <em>';
		_e('(Else all if using css)', 'amr-users'); 
		_e('(comma separated integers)', 'amr-users');
		echo ' </em>';
		echo '</label><br />
			<input '
//			.$disabled			
			.' type="text" size="130" id="use_css_on_pages" 
					name="use_css_on_pages" ';
		echo empty($amain['use_css_on_pages']) ? '' :' value="'.$amain['use_css_on_pages'].'" '; 
		echo '/><br /><br />';
		echo PHP_EOL.
		'<label for="csv_text">';
		_e('Text for csv link', 'amr-users'); 
		echo ' <em>';
		_e('(May be plain text or an icon link)', 'amr-users');
		echo ' </em>';
		echo '</label><br />'.PHP_EOL.
		'<input type="text" size="130" id="csv_text" 
					name="csv_text" value="';
		echo esc_attr($amain['csv_text']); 
		echo '"/>'.' '.__('Preview:','amr-users').' '.
		'<a href="#" title="'.__('This will be a link','amr-users').'" >'. $amain['csv_text'].'</a>';
		echo '<br /><br />'.PHP_EOL.
		'<label for="refresh_text">';
		_e('Text for cache refresh link', 'amr-users'); 
		echo '</label><br />'.PHP_EOL.
		'<input type="text" size="130" id="refresh_text" 
					name="refresh_text" value="';
		echo esc_attr($amain['refresh_text']); 
		echo '"/>'.
		' '.__('Preview:','amr-users').' '.
		'<a href="#" title="'.__('This will be a link','amr-users').'" >'. $amain['refresh_text'].'</a>';
		echo '<br /><br />'.PHP_EOL.
		'<label for="noaccess_text">';
		_e('Message when user does not have access or not logged in.', 'amr-users'); 
		echo '</label><br />';
		echo '<textarea rows="5" cols="130" id="noaccess_text" 
					name="noaccess_text" />';
		echo esc_attr($amain['noaccess_text']); 
		echo '</textarea>';
		echo '<br /><br />
			<label for="rows_per_page">';
		_e('Default rows per page:', 'amr-users'); 
		echo '</label><br />
			<input type="text" size="2" id="rows_per_page" 
					name="rows_per_page" value="';
		echo empty($amain['rows_per_page']) ? 50 :$amain['rows_per_page']; 
		echo '"/><br /><br />
			<label for="avatar_size">';
		_e('Avatar size:', 'amr-users');		
		echo ' 20,40, 80, 160, 200 </label>'.
		'<a title="gravatar size info" href="http://en.gravatar.com/site/implement/images/">'.__('Info').'</a>'
		.'<br />
			<input type="text" size="2" id="avatar_size" 
					name="avatar_size" value="';
		echo ((empty($amain['avatar_size'])) ? '' :$amain['avatar_size'] ); // because it is new and I hate notices
		echo '"/>';
			
		echo ausers_submit();
			echo '<br />'.PHP_EOL.
			'</div><!-- end of clear wrap -->	'
			.PHP_EOL.'<div class="clear"> </div>'.PHP_EOL;	
			

}
/* ---------------------------------------------------------------------*/
function amr_meta_general_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;
	
	
	$tabs['settings'] 	= __('General','amr-users');
	$tabs['fields'] 	= __('Fields & Nice Names', 'amr-users');
	$tabs['overview']	= __('Overview &amp; tools', 'amr-users');
	
	if (isset($_GET['tab'])) {
		if ($_GET['tab'] == 'fields'){
			amr_users_do_tabs ($tabs,'fields');
			amr_meta_nice_names_page();
			return;
		}
		elseif ($_GET['tab'] == 'overview'){
			amr_users_do_tabs ($tabs,'overview');
			amr_meta_overview_page();
			return;
		}
		else amr_users_do_tabs ($tabs,'settings');
	}	
	else amr_users_do_tabs ($tabs,'settings');	
	//amr_meta_main_admin_header('General');
	amr_meta_admin_headings (); // does the nonce check etc

	if (isset ($_POST['action']) and  ($_POST['action'] == "save")) { 
		if (isset ($_POST['reset'])){ 
			amr_meta_reset(); 
			return;
		}	
		else amrmeta_validate_mainoptions();
	}
	amr_meta_general_page_display(); /* else do the main header page */


}
<?php

require_once ('ameta-includes.php');
require_once ('ameta-admin-import-export-list.php');

include ('ameta-admin-cache-status.php');
include ('ameta-admin-testyourdb.php');
include ('ameta-admin-cache-logs.php');
include ('ameta-admin-nice-names.php');
include ('ameta-admin-cache-settings.php');
include ('ameta-admin-general.php');
include ('ameta-admin-configure.php');
/* -------------------------------------------*/
function amr_wplist_sortable($columns) {
	$colstoadd = ausers_get_option ('amr-users-show-in-wplist');
	$orig_mk = ausers_get_option('amr-users-original-keys') ;
	$wpfields = amr_get_usermasterfields();
	if (empty($colstoadd)) return $columns;
  	foreach ($colstoadd as $field => $show) {
		if ($show) {
			if (in_array($field, $orig_mk) or (in_array($field, $wpfields))// or 
			//($field == 'user_registration_date')
			) {  // for compatibility
				$columns[$field] = $field;
			}	
		}
	}
	return $columns;
}
/* -------------------------------------------*/
function amr_q_orderby( $query ) {  // but only in the main user list page or real query
	if( ! is_admin() )
		return;

	$wpfields = amr_get_usermasterfields();
	$orderby = $query->get( 'orderby');  // wp will have sanitised?
	if ($orderby == 'user_registration_date')  // for compatibility - may have to swop them one day!
		$orderby = 'user_registered';
	if (!(in_array($orderby, $wpfields ))) { // assume its a meta field
		$query->set('meta_key',$orderby);
		$query->set('orderby','meta_value');
	}
}
/* ----------------------------------------------------------------------------------- */	
function amr_add_user_columns ($columns) {
	$colstoadd = ausers_get_option ('amr-users-show-in-wplist');
	$nicenames = ausers_get_option ('amr-users-nicenames');
	foreach ($colstoadd as $field => $show) {
		if ($show) {
			if (!empty($nicenames[$field]))
				$columns[$field] =  $nicenames[$field];
			else 
				$columns[$field] = $field;
		}
	}
	return $columns;
}
/* ----------------------------------------------------------------------------------- */	
function amr_show_user_columns($value, $column_name, $user_id) {
	$colstoadd = ausers_get_option ('amr-users-show-in-wplist');
	
	if (!empty($colstoadd[$column_name])) {
		$user_info = get_userdata($user_id);
		if (empty($value)) 
			$value = $user_info->$column_name;
		if (function_exists('ausers_format_'.$column_name)) {
			$text =  (call_user_func('ausers_format_'.$column_name, $value, $user_info));
			return $text;
		}	
		else {
			$text = amr_wp_list_format_cell ($column_name, $value, $user_info);
			return $text;
			}
	}		
	else 
	return $value;
}
/* ----------------------------------------------------------------------------------- */	
function amr_meta_menu() { /* parent, page title, menu title, access level, file, function */
	/* Note have to have different files, else wordpress runs all the functions together */
	global 
		$amain,
		$amr_pluginpage,
		$ausersadminurl,
		$ausersadminusersurl;

	if (is_network_admin() ) {
		$ausersadminurl = network_admin_url('admin.php');
		$ausersadminusersurl = network_admin_url('users.php');
		}
	else {
		$ausersadminurl = admin_url('admin.php');
		$ausersadminusersurl = admin_url('users.php');
	}
	if (empty($amain)) 
		$amain = ausers_get_option('amr-users-main');
	
	/* add the options page at admin level of access */
	$menu_title = $page_title = __('User Lists', 'amr-users');

	$parent_slug =  'amr-users';
	$function = 	'amrmeta_about_page';
	$menu_slug = 	'amr-users';	
	$capability = 	'manage_options';

	$settings_page = $ausersadminurl.'?page=amr-users';
	
	$amr_pluginpage = add_menu_page($page_title, $menu_title , $capability, $menu_slug, $function);
	add_action('load-'.$amr_pluginpage, 'amru_on_load_page');
	add_action('admin_init-'.$amr_pluginpage, 'amr_load_scripts' );

	$parent_slug = $menu_slug;
	$amr_pluginpage = add_submenu_page($parent_slug, 
			__('About','amr-users'), __('About','amr-users'), 'manage_options',
			$menu_slug, $function);	
			
	$amr_pluginpage = add_submenu_page($parent_slug, 
			__('User List Settings','amr-users'), __('General Settings','amr-users'), 'manage_options',
			'ameta-admin-general.php', 'amr_meta_general_page');	
			
	$amr_pluginpage = add_submenu_page($parent_slug, 
			__('Configure a list','amr-users'), __('Configure a list','amr-users'), 'manage_options',
			'ameta-admin-configure.php', 'amrmeta_configure_page');		
			
	add_action( 'admin_head-'.$amr_pluginpage, 'ameta_admin_style' );	
			
	$amr_pluginpage = add_submenu_page($parent_slug, 
			__('Cache Settings','amr-users'), __('Cacheing','amr-users'), 'manage_options',
			'ameta-admin-cache-settings.php', 'amrmeta_cache_settings_page');	

	add_action( 'admin_head-'.$amr_pluginpage, 'ameta_admin_style' );
	 
	if (empty($amain)) $amain = ausers_get_option('amr-users-main');  /*  Need to get this early so we can do menus */
			
	if (current_user_can('list_users') or current_user_can('edit_users'))  {
			if (isset ($amain['names'])) { /* add a separate menu item for each list */
				
				foreach ($amain['names'] as $i => $name) {
					if (isset ($amain['names'][$i])) {
						$page = add_submenu_page(
						'users.php', // parent slug
						__('User lists', 'amr-users'), // title
						$amain['names'][$i], //menu title
						'list_users', // capability
						'ameta-list.php?ulist='.$i, //menu slug - must be ? why ??, priv problem if &
						'amr_list_user_meta'); // function
						
					  /* Using registered $page handle to hook stylesheet loading */
						add_action( 'admin_print_styles-' . $page, 'add_ameta_stylesheet' );
						add_action( 'admin_head-'.$page, 'ameta_admin_style' );	
					}
				}
			}
		}
	
	}
/* ---------------------------------------------------------------*/
function amr_meta_admin_headings () {
global $aopt;
	
	amr_check_for_upgrades();  // so we only do if an upgrade and will only do if admin
	ameta_options();
	echo ausers_form_start();

	if (isset ($_POST['action']) and  ($_POST['action'] == "save")) { 
		check_admin_referer('amr-meta','amr-meta');
	}
}
/* ----------------------------------------------------------------- */	
function amrmeta_validate_text($texttype)	{ /*  the names of lists */
	global $amain;

	if (!empty($_POST[$texttype]))  {
		$amain[$texttype] = wp_kses($_POST[$texttype], ameta_allowed_html());	
	}
	else $amain[$texttype] =  '';
	return true;
}
/* ---------------------------------------------------------------*/
function ameta_allowed_html () {
//	return ('<p><br /><hr /><h2><h3><<h4><h5><h6><strong><em>');
	return (array(
		'br' => array(),
		'em' => array(),
		'span' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'strong' => array(),
		'p' => array(),
		'abbr' => array(
		'title' => array ()),
		'img' => array('src'=>array(), 'alt'=>array() ),
		'acronym' => array(
			'title' => array ()),
		'b' => array(),
		'blockquote' => array(
			'cite' => array ()),
		'cite' => array (),
		'code' => array(),
		'del' => array(
			'datetime' => array ()),
		'em' => array (), 'i' => array (),
		'q' => array(
			'cite' => array ()),
		'strike' => array(),
		'div' => array()

		)); 
	}
/* ----------------------------------------------------------------------------------- */
function amr_load_scripts () {
	wp_enqueue_script('jquery');
}	
/* --------------------------------------------------------------------------------------------*/	
function amrmeta_validate_names()	{ /*  the names of lists */
	global $amain;

	if (is_array($_POST['name']))  {
		foreach ($_POST['name'] as $i => $n) {		/* for each list */	
			$amain['names'][$i] = $n;		
		}
		return (true);
	}
	else { 
		amr_flag_error (adb_cache::get_error('nonamesarray'));
		return (false);
	}	
}	
/* -------------------------------------------------------------------------------------------------------------*/	
function ausers_submit () {	
	return ('
	<p style="clear: both; class="submit">
		<input type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
		<input type="submit" name="reset" class="button"  value="'. __('Reset all options', 'amr-users') .'" />
	</p>');
	}
		/* ---------------------------------------------------------------------*/
function alist_update () {	
	return ('
	<p class="clear submit">
		<input type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
	</p>');
	}
/* ---------------------------------------------------------------------*/
function alist_rebuild () {	
	return ('<p style="clear: both;" class="submit">
			<input type="submit" class="button-primary" name="rebuildback" value="'.__('Rebuild cache in background', 'amr-users').'" />
			</p>');
	}
/* ---------------------------------------------------------------------*/
function alist_rebuildreal ($i=1) {	
	return (PHP_EOL.'<div class="clear"></div><!-- end class clear -->'.PHP_EOL.'<div><h3>'
		.'</h3>'.__('For large databases, rebuilding in realtime can take a long time. Consider running a background cache instead.','amr-users').'<p>'
		.__('If you choose realtime, keep the page open after clicking the button.','amr-users').'</p>'
		.'<div style="clear: both; padding: 20px;" class="submit">
			<input type="hidden" name="rebuildreal" value="'.$i.'" />
			<input type="submit" name="rebuild" value="'.__('Rebuild in realtime', 'amr-users').'" />
			<input type="submit" class="button-primary" name="rebuildback" value="'.__('Rebuild in background', 'amr-users').'" />
			</div><!-- end  -->'.PHP_EOL
			);
	}
/* ---------------------------------------------------------------------*/
function amr_rebuildwarning ( $list ) {
	
	$logcache = new adb_cache();

	if ($logcache->cache_in_progress($logcache->reportid($list,'user'))) {
		$text = sprintf(__('Cache of %s already in progress','amr-users'),$list);
		$logcache->log_cache_event($text);
		echo $text;
		return;
	}	
	else {
		$text = $logcache->cache_already_scheduled($list);  
		if (!empty($text)) {
			$new_text = __('Report ','amr-users').$list.': '.$text;
			$logcache->log_cache_event($new_text); 
			amr_users_message($new_text);	
			//return;	 - let it run anyway
		}
	}	
	echo alist_rebuildreal($list);	
	return;
	
	}
/* ---------------------------------------------------------------------*/
function amr_userlist_submenu ( $listindex ) {
	global $amain;
	//echo PHP_EOL.'<div class="clear"> ';
	//echo '<b>'.sprintf(__('Configure list %s: %s','amr-users'),$listindex,$amain['names'][$listindex]).
	echo 
		au_buildcache_view_link(__('Rebuild cache now','amr-users'),$listindex,$amain['names'][$listindex])
		.' | '.au_headings_link($listindex,$amain['names'][$listindex])
		.' | '.au_filter_link($listindex,$amain['names'][$listindex])
		.' | '.au_custom_nav_link($listindex,$amain['names'][$listindex])
		.' | '.au_grouping_link($listindex,$amain['names'][$listindex])
		.' | '.au_view_link(__('View','amr-users'), $listindex,$amain['names'][$listindex]);
//		.'</b>';
//		.'</div>';
}
/* ---------------------------------------------------------------------*/
function au_add_userlist_page($text, $i,$name) {
global $ausersadminurl;	
	$url = admin_url('post-new.php?post_type=page&post_title='.__('Members', 'amr-users').'&content=[userlist list='.$i.']');
	$t = '<a style="color:green;" href="'.wp_nonce_url($url,'amr-meta')
		.'" title="'.__('Add a new page with shortcode for this list', 'amr-users').'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/
function au_configure_link($text, $i,$name) {
global $ausersadminurl;	
	//$url = add_query_arg('ulist', $i, admin_url('admin.php?page=ameta-admin-configure.php'));
	
	$url = add_query_arg(array('ulist' => $i, 
			'page' =>'ameta-admin-configure.php'),
			$ausersadminurl	);
	
	
	$t = '<a style="color:#D54E21;" href="'.wp_nonce_url($url,'amr-meta')
		.'" title="'.sprintf(__('Configure List %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_delete_link ($text, $i,$name) {
	$url = remove_query_arg('copylist');
	
	$t = '<a href="'
		.wp_nonce_url(add_query_arg( array(
		'page'=>'ameta-admin-general.php&tab=overview',
		'deletelist' =>$i),$url),'amr-meta')
		.'" title="'.sprintf(__('Delete List %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
	}
/* ---------------------------------------------------------------------*/	
function au_copy_link ($text, $i,$name) {
	$url = remove_query_arg('deletelist');
	$t = '<a href="'.wp_nonce_url(add_query_arg('copylist',$i,$url),'amr-meta')
		.'" title="'.sprintf(__('Copy list to new %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
	}	
/* ---------------------------------------------------------------------*/	
function au_view_link($text, $i, $title) {
	$t = '<a style="text-decoration: none;" href="'
// must be a ?	.add_query_arg('ulist',$i,'users.php?page=ameta-list.php')
		.'users.php?page=ameta-list.php?ulist='.$i
	.'" title="'.$title.'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_csv_link($text, $i, $title) {
//global $ausersadminurl;
	$t = '<a style="color:#D54E21;" href="'
	.wp_nonce_url(add_query_arg(array('page'=>'ameta-list.php?ulist='.$i,'csv'=>$i)),'amr-meta').'" title="'.$title.'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_cachelog_link() {
	global $ausersadminurl;
	$t = '<a href="'
	.wp_nonce_url(add_query_arg('page','ameta-admin-cache-settings.php&tab=logs',''),'amr-meta').'" title="'.__('Log of cache requests','amr-users').'" >'.__('Cache Log','amr-users').'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_cachestatus_link() {
	$t = '<a href="'
	.wp_nonce_url(add_query_arg('page','ameta-admin-cache-settings.php&tab=status',''),'amr-meta').'" title="'.__('Cache Status','amr-users').'" >'.__('Cache Status','amr-users').'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function amru_related() {
	echo '<p>'.
	__('Related plugins are continually being developed in response to requests. They are packaged separately so you only add what you need.','amr-users')
	.'<p>';
	echo '<ul>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-cron-manager/" >amr cron manager</a> - ';
	_e('Improve visibility and manage the cron schedules','amr-users');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus/" >amr users plus</a> - ';
	_e('Adds functionality such as complex filtering','amr-users');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-s2/" >amr users plus s2</a> - ';
	_e('Adds subscribers in the separate subscribe2 table to the user lists','amr-users');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-cimy/" >amr users plus cimy</a> - ';
	_e('Makes the separate "cimy extra fields" table look like normal user meta data','amr-users');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-ym/" >amr users plus ym</a> - ';
	_e('Adds bulk ym updates and better formatting of ym fields.','amr-users');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-multisite/" >'.__('amr users multi site','amr-users').'</a> - ';
	_e('Makes amr users operate in the network pages across the sites.','amr-users');
	echo '</li>';

	echo '</ul>';
	echo '<a href="http://wpusersplugin.com/related-plugins" >'.
	__('... there may be more.','amr-users')
	.'</a>';
	
	}
/* ---------------------------------------------------------------------*/	
function a_currentclass($page){
	if ((isset($_REQUEST['am_page'])) and ($_REQUEST['am_page']===$page))
	return (' class="current" ');
	else return('');
}
/* ---------------------------------------------------------------------*/	
function amr_meta_support_links () {
	echo PHP_EOL.'<ul class="subsubsub" style="float:right;">';
	echo '<li><a target="_blank" href="http://wpusersplugin.com/support">';
	_e('Support','amr-users');
	echo '</a>|</li>
	<li><a target="_blank" href="http://wordpress.org/extend/plugins/amr-users/">';
	_e('Rate it','amr-users');
		echo '</a>|</li>
	<li>
	<a target="_blank" href="http://wpusersplugin.com/feed/">';
	_e('Rss feed','amr-users');
	echo '</a>|</li>
	<li><a target="_blank" href="https://www.paypal.com/sendmoney?email=anmari@anmari.com">';
	_e('Say thanks to anmari@anmari.com','amr-users');

	echo '</a></li></ul><br/>';
}
/* ---------------------------------------------------------------------*/	
function amr_meta_main_admin_header($title, $capability='manage_options') { //capbility canbe filtered for csv so far

	echo PHP_EOL.'<div id="icon-users" class="icon32"><br/></div>'.PHP_EOL;	
	
	echo PHP_EOL.'<h2>'.$title
	.'</h2>'
	.PHP_EOL;
	
	if (!( current_user_can('manage_options') or current_user_can($capability) )) 
		wp_die(__('You do not have sufficient permissions to update list settings.','amr-users'));
	
	if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
			echo '<h2>Problem creating DB tables</h2>';
}
/* ---------------------------------------------------------------------*/	
function amrmeta_admin_header() {
global $ausersadminurl;

	amr_meta_main_admin_header('User Lists');
	
	echo '<ul class="subsubsub">';	
	$t = __('General', 'amr-users');
	echo PHP_EOL.'<li><a  href="'
	.$ausersadminurl.'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Test your db', 'amr-users');
	echo PHP_EOL.'<li><a  href="'
	.wp_nonce_url(add_query_arg('am_page','testyourdb',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Overview', 'amr-users');
	echo PHP_EOL.'<li>&nbsp;<span class="step">1.</span><a  href="'
	.wp_nonce_url(add_query_arg('am_page','overview',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Nice Names', 'amr-users');
	echo '<li>&nbsp;<span class="step">'
	.'2.</span><a '.a_currentclass('nicenames').' href="'
	.wp_nonce_url(add_query_arg('am_page','nicenames',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|&nbsp;<span class="step">'
	.'3.</span></li></ul>';	
	$t = __('Rebuild Cache in Background', 'amr-users');
		
	
	list_configurable_lists();
	echo '<ul class="subsubsub"><li>&nbsp;<span class="step">4.</span>'.au_buildcachebackground_link().'|</li>';	
	echo '<li>&nbsp;<span class="step">5.</span>'.au_cachelog_link().'|</li>';	
	echo '<li>&nbsp;<span class="step">6.</span>'.au_cachestatus_link().'</li>';	
	echo '</ul>';
	return;
}
/* ---------------------------------------------------------------------*/
function amrmeta_mainhelp($contextual_help, $screen_id, $screen) {
global $amr_pluginpage;

	if ($screen_id == $amr_pluginpage) {
		$contextual_help = '<h3>'.__('Fields and Nice Names','amr-users').'</h3>'.amrmeta_nicenameshelp();	
		$contextual_help .= '<h3>'.__('Lists','amr-users').'</h3>'.amrmeta_overview_help();
		$contextual_help .= '<h3>'.__('List Settings','amr-users').'</h3>'.amrmeta_confighelp();

		return $contextual_help;
	}
	if ($screen_id == 'ameta-admin-configure.php') {
		$contextual_help .= '<h3>'.__('List Settings','amr-users').'</h3>'.amrmeta_confighelp();
		return $contextual_help;
	}
}
/* ---------------------------------------------------------------------*/
function amrmeta_overview_help() {
	
	$contextual_help = 
	'<h3>'.__('Lists','amr-users').'</h3>'
	.'<ol><li>'.__('Defaults lists are provided as examples only.  Please configure them to your requirements.', 'amr-users').'</li><li>'

	.__('Update any new list details and configure the list.', 'amr-users').'</li><li>'
	.__('Each new list is copied from the last configured list.  This may be useful if configuring a range of similar lists - add the lists one by one - slowly incrementing the number of lists.', 'amr-users').'</li>'
	.'<li>'
	.__('List settings from compatible systems can be imported', 'amr-users').'</li>'
	.'</ol>';

	return $contextual_help;
	}
/* ---------------------------------------------------------------------*/
function amr_rebuild_in_realtime_with_info ($list) {  // nlr ?
	if (amr_build_user_data_maybe_cache ($list)) {; 
		echo '<div class="update">'.sprintf(__('Cache rebuilt for %s ','amr-users'),$list).'</div>'; /* check that allowed */
		echo au_view_link(__('View Report','amr-users'), $list, __('View the recently cached report','amr-users'));
	}
	else echo '<div class="update">'.sprintf(__('Check cache log for completion of list %s ','amr-users'),$list).'</div>'; /* check that allowed */
}
/* ---------------------------------------------------------------------*/
function amru_on_load_page() {
	global $pluginpage;
		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore

	}
/* ----------------------------------------------------------------------------------- */
function amr_remove_footer_admin () {
	echo '';
	}	
/* ----------------------------------------------------------------------------------- */
function amr_users_do_tabs ($tabs, $current_tab) {
	// check for tabs  
	    // display the icon and page title  
    echo '<div id="icon-options-general" class="icon32"><br /></div>';  
	if ($tabs !='') {  
		
		// wrap each in anchor html tags  
		$links = array();  
		foreach( $tabs as $tab => $name ) {  
			// set anchor class  
			$class      = ($tab == $current_tab ? 'nav-tab nav-tab-active' : 'nav-tab');  
			$page       = $_GET['page'];  
			// the link  
			$links[]    = "<a class='$class' href='?page=$page&tab=$tab'>$name</a>";  
		}  
	  
		echo PHP_EOL.'<h2 class="nav-tab-wrapper">';  
			foreach ( $links as $link ) {  
				echo $link;  
			}  
		echo '</h2>'.PHP_EOL;  
	} 
}
/* ----------------------------------------------------------------------------------- */
function amrmeta_instructions() {

	$html = '<ol>'
	.'<li>'.__('Create sample users with sample data.', 'amr-users')
	.'</li>'
	.'<li>'
	.__('There must be at least one entry for each field you want to see.', 'amr-users').'</li>'
	.'<li>'.__('Execute "find the fields".', 'amr-users').'</li>'
	.'<li>'.__('Configure the lists.', 'amr-users').'</li>'
	.'<li>'.__('Rebuild cache.', 'amr-users').'</li>'
	.'<li>'.__('Add user plugins ? Add fields ? Add data ? : ', 'amr-users')
	.__('Cache must be updated for changes to show. (duh!)', 'amr-users')
	.'</li>'
	.'</ol>';
	return( $html);
}
/* ---------------------------------------------------------------------*/
function amrmeta_about_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;
		
	//amr_meta_main_admin_header('About amr user lists'.' (version:'.AUSERS_VERSION.')');
	$tabs['about'] = __('About','amr-users').' ('.AUSERS_VERSION.')';
	$tabs['userdb'] = __('Your user db', 'amr-users');
	$tabs['news'] = __('News', 'amr-users');
	
	if (isset($_GET['tab'])) {
		if ($_GET['tab'] == 'userdb') {
			amr_users_do_tabs ($tabs,'userdb');
			amr_meta_test_your_db_page();
			return;
		}
		elseif ($_GET['tab'] == 'news') {
			amr_users_do_tabs ($tabs,'news');
				
			echo '<h2>'.__('News', 'amr-users').'</h2>';

			amr_users_feed('http://wpusersplugin.com/feed/', 3, __('amr wpusersplugin news', 'amr-users'));
			amr_users_feed('http://webdesign.anmari.com/feed/', 3, __('other anmari news', 'amr-users'));
			return;
		}
	}	
	amr_users_do_tabs ($tabs,'about');
	amr_meta_support_links ();
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc
	
	echo '<p><h3>'.__('Shortcodes to add to pages:', 'amr-users').'</h3></p>'
	.'<p><span style="color:green;">&nbsp;  [userlist] &nbsp;&nbsp;or &nbsp;&nbsp;[userlist list=n]</span></p>';
	echo '<h3>'.__('Instructions.', 'amr-users').'</h3>'.amrmeta_instructions();
	echo '<h3>'.__('Fields and Nice Names', 'amr-users').'</h3>'.amrmeta_nicenameshelp();
	echo amrmeta_overview_help();
	echo '<h3>'.__('List Settings','amr-users').'</h3>'.amrmeta_confighelp();



}
/* ---------------------------------------------------------------------*/
	//styling options page
function ameta_admin_style() {

?>
<!-- Admin styles for amr-users settings screen - admin_print_styles trashed the admin menu-->
<style type="text/css" media="screen">

table th.show {
	width: 20px;
}

legend {
	  font-size: 1.1em;
	  font-weight: bold;
}  
label { 
	cursor: auto;
	display: block;
	float: left;
	width: 200px;
 }
.widefat li label {

	width: 500px;
}
form label.lists {
	display: block;  /* block float the labels to left column, set a width */
	clear: left;
	float: left;  
	text-align: right; 
	width:40%;
	margin-right:0.5em;
	padding-top:0.2em;
	padding-bottom:1em;
	padding-left:2em;
 }
.userlistfields th a { cursor: help;}

.if-js-closed .inside {
	display:none;
}
.subsubsub span.step {
	font-weight: bold;
	font-size: 1.5em;
	color: green;
}
.tooltip {
  cursor: help; text-decoration: none;
}


</style>
	
<?php
}

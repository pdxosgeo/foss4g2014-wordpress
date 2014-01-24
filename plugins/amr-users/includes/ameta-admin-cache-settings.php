<?php
function ameta_schedule_regular_cacheing ($freq) { 
/* This should be done once only or once if settings changed, or perhaps only if requested  */
	global $amain;

	$network = ausers_job_prefix();

	ameta_cron_unschedule();
	if (!($freq == 'notauto')) {

		wp_schedule_event(time(),
			$amain['cache_frequency'],
			'amr_'.$network.'regular_reportcacheing',
			array());   /* update once a day for now */
		$timestamp = wp_next_scheduled( 'amr_'.$network.'regular_reportcacheing' );
		$logcache = new adb_cache();
		$text = __('Activated regular cacheing of lists: ','amr-users'). $freq;
		$time = date('Y-m-d H:i:s', $timestamp);
		$text2 = (__('Next cache run on user change or soon after: ','amr-users'). $time);
		echo '<p class="message">'.$text .'</p><p>'.$text2.'</p>';
		$result = $logcache->log_cache_event($text);
		$result = $logcache->log_cache_event($text2);
		return (true);
	}
	return (false);
}
/* ---------------------------------------------------------------*/

function ameta_cron_unschedule	() { /* This should be done once on activation only or once if settings changed, or perhaps only if requested  */
	$network = ausers_job_prefix();
	if (function_exists ('wp_clear_scheduled_hook')) {
		wp_clear_scheduled_hook('amr_'.$network.'regular_reportcacheing');
		$logcache = new adb_cache();
		$text = __('Deactivated any existing regular cacheing of lists','amr-users');
		$logcache->log_cache_event($text);
		echo '<pclass="message">'.$text .'</p>';
	}

}

/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_cache_settings()	{ 
	global $amain;
	global $aopt;
	
	$amain['notonuserupdate'] = true;
	if (isset($_POST['notonuserupdate'])) { 
		if ($_POST['notonuserupdate'] == 'true')
			$amain['notonuserupdate'] = true;
		else 	
			$amain['notonuserupdate'] = false;
	}
	else $amain['notonuserupdate'] = false;
	
	if (!isset ($amain['cache_frequency'] )) 
		$amain['cache_frequency'] = 'notauto';
	if (isset($_POST['cache_frequency'])) {
		if (!($_POST['cache_frequency'] == $amain['cache_frequency'])) {
			$amain['cache_frequency'] = $_POST['cache_frequency'];	
			ameta_schedule_regular_cacheing	($_POST['cache_frequency']); 

		}

	}	
	else $amain['cache_frequency'] = 'notauto';
	
	$amain['version'] = AUSERS_VERSION;
	
	if (isset($_POST))	{ 
		ausers_update_option ('amr-users-main', $amain) ;
		//ausers_update_option ('amr-users', $aopt);
	}
	return;
}
/* ---------------------------------------------------------------------*/
function amrmeta_cache_settings_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;
	
	if (empty($amain)) $amain = ausers_get_option('amr-users-main');
	$tabs['settings'] 	= __('Cache Settings','amr-users');
	$tabs['logs'] 		= __('Cache Logs', 'amr-users');
	$tabs['status'] 	= __('Cache Status', 'amr-users');
	
	if (isset($_GET['tab'])) {
		if ($_GET['tab'] == 'logs') {
			amr_users_do_tabs ($tabs,'logs');
			amrmeta_cache_logs_page();
			return;
		}
		elseif ($_GET['tab'] == 'status') {
			amr_users_do_tabs ($tabs,'status');
			amrmeta_cachestatus_page();
			return;

		}
	}	
	amr_users_do_tabs ($tabs,'settings');	
	//amr_meta_main_admin_header('Cache Settings');
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc	
		
	if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
		echo '<h2>Problem creating DB tables</h2>';	
		
	if (isset ($_POST['action']) and  ($_POST['action'] == "save")) { 
		amrmeta_validate_cache_settings();
	}
	
	if (isset ($_REQUEST['rebuildback'])) { 
		echo '<p>'.__('Background cache request received', 'amr-users').'</p>';
		if (isset($_REQUEST['rebuildreal'])) {
			$ulist = (int) $_REQUEST['rebuildreal'];
			amr_request_cache_with_feedback($ulist);
		}
		else 
			amr_request_cache_with_feedback(); 
			return;	
		}/* then we have a request to kick off run */
	elseif (isset ($_REQUEST['rebuildreal'])) { /* can only do one list at a time in realtime */			
			$ulist = (int) $_REQUEST['rebuildreal'];
			amr_rebuild_in_realtime_with_info ($ulist);
			//echo amr_build_cache_for_one($_REQUEST['rebuildreal']); 
			//echo '<h2>'.sprintf(__('Cache rebuilt for %s ','amr-users'),$_REQUEST['rebuildreal']).'</h2>'; /* check that allowed */
			//echo au_view_link(__('View Report','amr-users'), $_REQUEST['rebuildreal'], __('View the recently cached report','amr-users'));
			return;
		}/* then we have a request to kick off cron */


	else {	
	

	
/* validation will have been done */
		$freq = array ('notauto'=> __('No scheduled auto cacheing', 'amr-users'), 
		
					'hourly'    => __('Hourly', 'amr-users'), 
					'twicedaily'=> __('Twice daily', 'amr-users'), 
					'daily'     => __('Daily', 'amr-users'),
//					'monthly'     => __('Monthly', 'amr-users')
						);		
		
		if (!isset ($amain['cache_frequency'])) 
			$freqchosen = 'notauto'; 
		else 
			$freqchosen = $amain['cache_frequency'];
			
		echo '<h3>';
		_e('Activate regular cache rebuild ? ', 'amr-users'); 
		echo '</h3><span><em>';
		echo '<p>';
		_e('This cacheing grabs all the raw data it can find and does some preprocessing. ', 'amr-users'); 
		echo '<br />';

		_e('The data is stored in a flat db table for later formatting and reporting. ', 'amr-users'); 
		echo '</p>';
		echo '<p>';
		_e('The cache log will tell you the last few times that the cache was rebuilt and why. ', 'amr-users'); 
		echo '<a href="'.admin_url( 'admin.php?page=ameta-admin-cache-settings.php&tab=logs' ).'">'.__('Go to cache log','amr-users').'</a>';
		echo '<br />';
		_e('A cron plugin may also be useful.', 'amr-users'); 
		echo ' <a href="http://wpusersplugin.com/related-plugins/amr-cron-manager/">amr cron manager</a>';
		echo '</p>';
		echo'<p><a target="_blank" href="http://wpusersplugin.com/3458/cacheing-amr-users/">'.__('More information','amr-users').'</a></p>';

		echo '</em>	</span>	<p>';
/*		echo '<label for="notonuserupdate">
			<input type="checkbox" size="2" id="notonuserupdate" 
				name="notonuserupdate" ';
		echo (empty($amain['notonuserupdate'])) ? '' :' checked="checked" '; 
		echo '/>';
		_e('Do NOT re-cache on user update', 'amr-users'); 
		echo '</label>';
*/
		echo '<label for="notonuserupdate">
			<input type="radio" size="2" id="notonuserupdate" 
				name="notonuserupdate" value="true"';
		echo (empty($amain['notonuserupdate'])) ? '' :' checked="checked" '; 
		echo '/>';
		_e('Do NOT re-cache on user update', 'amr-users'); 
		echo '</label>';
		echo '<br />';
		echo '<label for="doonuserupdate">
			<input type="radio" size="2" id="doonuserupdate" 
				name="notonuserupdate" value="false"';
		echo (($amain['notonuserupdate'])) ? '' :' checked="checked" '; 
		echo '/>';
		_e('Do re-cache on user update', 'amr-users'); 
		echo '</label>';				
		echo '</p><br />';
		
		echo '<p><em><b>';
		_e('If you have very frequent user updates consider only cacheing at regular intervals', 'amr-users'); 
		echo '</b> ';
		_e('This will help prevent excessive database activity', 'amr-users'); 
		echo '<br />';
		_e('EG: Are you tracking every page ? every login.. you do not want it recaching all the time?!', 'amr-users'); 	
		_e('Rather cache hourly only.  A refresh can be requested.', 'amr-users'); 	
		echo '<br />';
		_e('Wordpress transients are also used to cache the html in public lists and front end', 'amr-users'); 	
		echo '</em></p>';
		echo '<p><em><b>';
		_e('To switch off all auto cacheing, select "Do not.." above AND "No..." below.', 'amr-users'); 
		echo '</b><br />';
		_e('Lists will then be re-generated on manual refresh request only.', 'amr-users'); 
		echo '</em></p>';
		foreach ($freq as $i=> $f) { 
				echo '<label><input type="radio" name="cache_frequency" value="'.$i.'" ';
 				if ($i == $freqchosen) echo ' checked="checked" ';  
				echo '/>';
				echo $f; 
				echo '</label><br />';			
			} 
		echo alist_update();
		echo alist_rebuild();	
	
	}
	echo ausers_form_end();	
				
}		
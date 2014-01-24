<?php
function amr_about_users () {
global $wpdb,$charset_collate;	
global $ausersadminurl;		

	if (!defined('WP_DEBUG')) define('WP_DEBUG', true);
	$_REQUEST['mem'] = true;  // to make track progress work
	
	register_shutdown_function('amr_shutdown');
	set_time_limit(360);  // should we make this an option.... 
	//$time_start = microtime(true);
	
	echo '<p>';
		_e('Compare the memory limits to the memory stats shown in your cache status', 'amr-users');
	echo '<a href="'.$ausersadminurl.'?page=ameta-admin-cache-settings.php&tab=status'.'"> '.__('go', 'amr-users').'</a>';
	echo '</p>';	

	echo '<p>';
	_e('If the user and user meta numbers are large, you may experience problems with large lists.', 'amr-users');
	echo '<br /><br />';	

	_e('If this happens, try: increasing php memory, clean up users (get rid of the spammy users), clean up usermeta.  You may have records from inactive plugins.', 'amr-users');
	
	echo '</p>';
	
	$wpdb->show_errors();
	if (is_multisite() and is_network_admin()) {
		$where = '';
		$wheremeta = '';
		_e('This is a multi-site network.  All users shown here.', 'amr-users');
		echo '<br />';
	}
	else { $where = ' INNER JOIN ' . $wpdb->usermeta .  
       ' ON      ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id 
        WHERE   ' . $wpdb->usermeta .'.meta_key =\'' . $wpdb->prefix . 'capabilities\'' ;

		printf(__('This website with blog_id=%s and prefix=%s has:', 'amr-users'),$GLOBALS['blog_id'],$wpdb->prefix );
		$wheremeta = '';
	}	
	echo '<ul>';
	echo '<li>';
	printf(__('Plugin version: %s','amr-users'),AUSERS_VERSION);
		echo '</li>';
	echo '<li>';
		printf(__('Php version: %s ', 'amr-users'),phpversion()); 
	echo '</li>';
	echo '<li>';
		printf(__('Wp version: %s ', 'amr-users'),get_bloginfo( 'version', 'display' )); 
	echo '</li>';
	echo '<li>';
		printf(__('Wordpress Memory limit: %s ', 'amr-users'),WP_MEMORY_LIMIT); 
	echo '</li>';
	echo '<li>';
		printf(__('Php Memory Limit: %s ', 'amr-users'),ini_get('memory_limit')); 
	echo '</li>';
	echo '<li>';
		printf(__('Charset: %s ', 'amr-users'),get_bloginfo( 'charset', 'display' )); 
	echo '</li>';
	if (!empty($charset_collate)) { 
		echo '<li>';
			printf(__('Collation: %s ', 'amr-users'),$charset_collate); 
		echo '</li>';
	}
//------------------------------------------
	echo '<li>';
	track_progress('Before count users:<br/>');
	$result = count_users();
	printf (__('There are %s total users: ','amr-users'), $result['total_users']);

	foreach($result['avail_roles'] as $role => $count)
		echo $count. ' '. $role.', ';
	echo '<br />';	
	track_progress('After count users:<br/>');	
	echo '</li>';
//------------------------------------------
	if (is_multisite() ) {
		if (amr_is_network_admin()) {			
			$sql = "SELECT count(*) FROM " . $wpdb->blogs;	
			$text = __('%d sites', 'amr-users');
			amr_count_sql (	$sql, $text, '<li>','</li>');
		}
		else echo '<li>In multisite, but not in <a href="'.network_admin_url('admin.php?page=amr-users&tab=userdb').'" >network admin</a></li>.';
	}

	echo '</ul>';
//------------------------------------------	
	if (!empty($where))  // then we already know we are in a sub blog
			$wheremeta = " WHERE ".$wpdb->usermeta.".user_id IN ".
		"(SELECT distinct user_id FROM ".$wpdb->usermeta." WHERE ".$wpdb->usermeta .".meta_key ='" . $wpdb->prefix . "capabilities')";
	echo '<p><b>'.__('These queries could be slow.  Be patient.  Wait:').'</b></p>';
	echo '<input id="submit"  class="button-secondary subsubsub" name="getstats" type="submit" value="';
		_e('Get meta stats', 'amr-users'); 
		echo '" /> ';
		
		echo '<input id="submit"  class="button-secondary subsubsub" name="testqueries" type="submit" value="';
		_e('Run user test query', 'amr-users'); 
		echo '" /> ';
		
		echo '<input id="submit"  class="button-secondary subsubsub" name="testquerymeta" type="submit" value="';
		_e('Run user meta test query', 'amr-users'); 
		echo '" />';
		
		/*echo '<input id="submit"  class="button-secondary subsubsub" name="testwpmetaquery" type="submit" value="';
		_e('Run wp meta test query', 'amr-users'); 
		echo '" />';*/
		echo '<br /><br />';
//------------------------------------------		
	
	if (isset($_REQUEST['getstats'])) {
		
		echo '<h4>'.__('Meta stats:','amr-users').'</h4>';
		echo '<ul>';
		echo '<li>';
		track_progress('Before meta stats:<br/>');	


/* TOO SLOW		
		echo '<li>';		
		$sql = "SELECT COUNT(DISTINCT meta_key) FROM $wpdb->usermeta ".$wheremeta ;  // 97 seconds on 1.1 million records		
		$total = $wpdb->get_var( $sql );		
		printf(__('%s different user meta keys.', 'amr-users'),number_format($total,0,'.',',')); 
		track_progress('After count distinct usermeta:');
		unset($results);
		echo '</li>';
*/		
		
		echo '<li>';
		$sql =  "SELECT DISTINCT meta_key FROM $wpdb->usermeta";  // uses more mem but faster
		
		echo '<br /><em>Executing query:<br /> '.$sql.'</em><br />';
		
		$results = $wpdb->get_results($sql, ARRAY_A);
		$total = count($results);
		printf(__('%s different user meta keys.', 'amr-users'),number_format($total,0,'.',',')); 
		track_progress('After alternate count distinct usermeta:');
		echo '</li>';
		
		$sql = "SELECT count(*) FROM $wpdb->usermeta ".$wheremeta; 
		$text = __('%d user meta records.', 'amr-users');
		echo '<br /><em>Executing query:<br /> '.$sql.'</em><br />';
		amr_count_sql (	$sql, $text, '<li>','</li>');		
		echo '</ul>';
		flush();
	}
//------------------------------------------	
	
	if (isset($_REQUEST['testqueries'])) {
		echo '<hr /><b>'.__('Running some test queries:', 'amr-users').'</b>';

		$_REQUEST['mem'] = true;  // to make track progress work
		track_progress('Test full user query memory impact:');
		$sql = "SELECT ID, user_login, user_email, display_name FROM $wpdb->users".$where;
		echo '<br /><em>Executing query:<br /> '.$sql.'</em><br />';
		$results = $wpdb->get_col( $sql, 0 );	
		echo '<br />Queried all from user master:'. count($results);
		track_progress('After users - how was it?');
	}
	if (isset($_REQUEST['testquerymeta'])) {	
		track_progress('Test user meta query:');

		$sql = 'SELECT user_id, meta_key, meta_value '.
		" FROM $wpdb->usermeta ".$wheremeta
		. " AND $wpdb->usermeta.meta_key "
		." in ("
		."'first_name', 'last_name', 'nickname', 'description' "
		.") " ;
		
		echo '<br /><em>Executing query:<br /> '.$sql.'</em><br />';
		$results = $wpdb->get_results( $sql, ARRAY_A );
		
		echo '<br />Queried user meta master:'. count($results);		
		track_progress('After usermeta - how was it?');
		echo '<hr /><b>'.__('If these queries completed, the "fetch users directly" method should work, even if the "wp_query" method fails.', 'amr-users').__('See "How to fetch data" in the general settings.', 'amr-users').'</b>';
	}
	if (isset($_REQUEST['testwpmetaquery'])) {  // put in request line - just testing for now
		
		track_progress('Test wp query impact with test query:');
		
		$parameters = array (
		'role' => 'subscriber',
		'fields' => array('ID', 'user_login', 'user_email', 'display_name')  // can only be main fields, so not that helpful
		
		);
		$all_users_query = new WP_User_Query( $parameters );
		$results = $all_users_query->get_results();
		
		var_dump($results[1]);

		echo '<br />Queried all from user master:'. count($results);
		track_progress('After users - how was it?');
	}
	echo '<p>'.__('Page complete.  Please note memory and incremental runtimes.', 'amr-users').'</p>';
}
/* ----------------------------------------------------------------------------------- */
function amr_count_sql ($sql, $text, $before, $after) { 
global $wpdb;					

/*	$results = $wpdb->get_col( $sql, 0 );	
	echo $before;
	foreach ($results as $i => $total) {
		printf($text,number_format($total,0,'.',','));
	}
	unset($results);
*/	
	$total = $wpdb->get_var( $sql );  // note prepare not necessary as we specified the input query - no user input
	
	echo $before;
	$text2 = sprintf($text,number_format($total,0,'.',','));
	echo $text2;
	track_progress('After '.$text2.': ');
	echo $after;
	flush();
}
/* ----------------------------------------------------------------------------------- */
function amr_count_blogs ($sql, $text) { 
global $wpdb;					
	$sql = "SELECT count(*) FROM " . $wpdb->blogs;	

	$results = $wpdb->get_col( $sql, 0 );	
	foreach ($results as $i => $total) {
					printf(__('%s sites', 'amr-users'),number_format($total,0,'.',','));
	}
	unset($results);
}
/* ----------------------------------------------------------------------------------- */
function amr_test_your_db() { 
	amr_mimic_meta_box('about', __('About your user database', 'amr-users'),'amr_about_users', false);
}
/* ---------------------------------------------------------------------*/	
function amr_meta_test_your_db_page() { /* the main setting spage  - num of lists and names of lists */
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc
	amr_test_your_db();
}
/* ---------------------------------------------------------------------*/	
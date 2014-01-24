<?php 
/* ---------------------------------------------------------*/
function amru_get_users( $args ) { /*  get all user data and attempt to extract out any object values into arrays for listing  */
global $wpdb;

// just do simply for now, as we have filtering later to chope out bits
	$_REQUEST['mem'] = true;  // to show memory
		$where = '';
		$wheremeta = '';
		
	if (is_multisite() ) {
		if ( amr_is_network_admin()) {
		$where = ' INNER JOIN ' . $wpdb->usermeta .  
       ' ON      ' . $wpdb->users 
	   . '.ID = ' . $wpdb->usermeta . '.user_id 
        WHERE   ' . $wpdb->usermeta .'.meta_key =\'' . $wpdb->prefix . 'capabilities\'' ;
		
		// not using
		/*
		$wheremeta = " WHERE ".$wpdb->usermeta.".user_id IN ".
		"(SELECT distinct user_id FROM ".$wpdb->usermeta
		." WHERE ".$wpdb->usermeta .".meta_key ='" . $wpdb->prefix . "capabilities')";
		*/
		}
		else { // is multi site but not network admin - limit the users

					$where = ' INNER JOIN ' . $wpdb->usermeta .  
       ' ON      ' . $wpdb->users 
	   . '.ID = ' . $wpdb->usermeta . '.user_id 
        WHERE   ' . $wpdb->usermeta .'.meta_key =\'' . $wpdb->prefix . 'capabilities\'' ;

		}
	}


	//track_progress('Start amr get users');	
	//$query = $wpdb->prepare( "SELECT * FROM $wpdb->usermeta".$where); // WHERE meta_key = %s", $meta_key );
	$query = "SELECT * FROM $wpdb->usermeta".$where; // we controlled the input so prepare not necessary
	$metalist = $wpdb->get_results($query, OBJECT_K);

	//track_progress('After get users meta');

// arghh - sometimes we need usrs that do not have the meta values, so does this mean we have to get all users ?	
	//$query = $wpdb->prepare( "SELECT ID, user_login, user_nicename, user_email, user_url, user_registered, display_name FROM $wpdb->users".$where); // WHERE meta_key = %s", $meta_key );
	$query = "SELECT ID, user_login, user_nicename, user_email, user_url, user_registered, display_name FROM $wpdb->users".$where; 
	$users = $wpdb->get_results($query, OBJECT_K);  // so returns id as key - NOT WORKING IN EVERY SITE
	
	//track_progress('After get users without meta');
	
	foreach ($users as $i => $u) {

		if (isset($metalist[$i])) {
			$users[$i] = (object) array_merge((array) $u, (array) $metalist[$i]);			
			unset($metalist[$i]);
		}
		
	}		
	//track_progress('After combining users with their meta');
	return ($users);

}
/* ---------------------------------------------------------*/ 	

function amr_get_alluserdata( $list ) { /*  get all user data and attempt to extract out any object values into arrays for listing  */

global $excluded_nicenames, 
	$amain,
	$aopt, // the list options (selected, included, excluded)
	$orig_mk, // original meta key mapping - nicename key to original metakey
	$amr_current_list;
	
	$amr_current_list = $list;	
	$main_fields = amr_get_usermasterfields();  // mainwpuser fields less any excluded in nice names
	
// 	maybe use, but no major improvement for normal usage add_filter( 'pre_user_query', 'amr_add_where'); 
		
	if (!$orig_mk = ausers_get_option('amr-users-original-keys')) 
		$orig_mk = array();
//	
//	track_progress ('Meta fields we could use to improve selection: '.print_r($orig_mk, true));
	$combofields = amr_get_combo_fields($list);  

	$role = '';
	$mkeys = array();
	if (!empty($aopt['list'][$list]['included'])) { 	
		// if we have fields that are in main user table, we could add - but unliket as selection criateria - more in search	
		foreach ($aopt['list'][$list]['included'] as $newk=> $choose ) {

			if (isset ($orig_mk[$newk])) 
				$keys[$orig_mk[$newk]] = true;
		
			if ($newk == 'first_role') {
				if (is_array($choose)) 
					$role = array_pop($choose);
				else 
					$role = $choose;
			}
		
			if (isset ($orig_mk[$newk]) and ($newk == $orig_mk[$newk])) {// ie it is an original meta field
				if (is_array($choose)) {
					if (count($choose) == 1) {
						$choose = array_pop($choose);
						$compare = '=';
					}
					else $compare = 'IN';
				}
				else $compare = '=';
				
				$meta_query[] = array (
					'key' => $newk,
					'value' => $choose,
					'compare' => $compare
				);
			}
		}
	}
// now try for exclusions 	
	if (!empty($aopt['list'][$list]['excluded'])) { 
		foreach ($aopt['list'][$list]['excluded'] as $newk=> $choose ) {
			if (isset ($orig_mk[$newk])) {
				$keys[$orig_mk[$newk]] = true; // we need to fetch a meta value
				if ($newk == $orig_mk[$newk]) {// ie it is an original meta field 1 to 1
					if (is_array($choose)) {
						if (count($choose) == 1) {
							$choose = array_pop($choose);
							$compare = '!=';
						}
						else $compare = 'NOT IN';
					}
					else $compare = '!=';
					
					$meta_query[] = array (
						'key' => $newk,
						'value' => $choose,
						'compare' => $compare
					);
				}				
			}
		} // end for each
	}
// now need to make sure we find all the meta keys we need

	foreach (array('selected','excludeifblank','includeonlyifblank' ,'sortby' ) as $v) {


		if (!empty($aopt['list'][$list][$v])) { 
			foreach ($aopt['list'][$list][$v] as $newk=> $choose ) {		
				if (isset ($orig_mk[$newk])) {// ie it is FROM an original meta field
					$keys[$orig_mk[$newk]] = true;
				}

			}
		}
	}
	
	if (!empty($aopt['list'][$list]['grouping'])) { 
			foreach ($aopt['list'][$list]['grouping'] as $i=> $newk ) {			
				if (isset ($orig_mk[$newk])) {// ie it is FROM an original meta field
					$keys[$orig_mk[$newk]] = true;
				}
				
			}
	}
	
	$args = array();
	$users = array();  // to handle in weird situation of no users - eg if db corrupt!
	if (!empty ($role) ) 		$args['role'] = $role;
	if (!empty ($meta_query) ) 	$args['meta_query'] = $meta_query;
	//if (!empty ($fields) ) $args['fields'] = $fields;
	
	//$args['fields'] = 'all_with_meta'; //might be too huge , but fast - DOES NOT GET META DATA ?? and/or only gets single values
	
	//track_progress ('Simple meta selections to pass to query: '.print_r($args, true));

	if (is_network_admin() or amr_is_network_admin() ) {
		//if (WP_DEBUG) {echo '<br/>';if (is_network_admin()) echo 'network admin'; else echo 'NOT network admin but treating as is';}
		$args['blog_id'] = '0';
	}
	
	if (isset($amain['use_wp_query'])) {	

		$all = get_users($args); // later - add selection if possible here to reduce memory requirements 
		//if (WP_DEBUG) {echo '<br/>Fetched with wordpress query.  No. of records found: <b>'.count($all).'</b><br /> using args: '; var_dump($args); }
		}
	else {	
		//if (WP_DEBUG) echo '<br/>if WP_DEBUG: Fetching with own query ';
		$all = amru_get_users($args); // later - add selection if possible here to reduce memory requirements 
		//if (WP_DEBUG) {echo '<br/>Fetched with own query.  No. of records found: <b>'.count($all).'</b><br /> using args: '; var_dump($args); }

	}
	
	//track_progress('after get wp users, we have '.count($all));
	
	foreach ($all as $i => $userobj) { // build our user array and add any missing meta
// save the main data, toss the rest

		foreach ($main_fields as $i2=>$v2) {			
			//$users[$i][$v2] = $userobj->$v2;   		
			if (!empty($userobj->$v2)) 
				$users[$userobj->ID][$v2] = $userobj->$v2;    //OBJECT_K does not always seem to key the array correctly
		}
// -------------------------------------------------------------------		
// we just need to expand the meta data
		if (!empty($keys)) { // - the list of metadata keys.  If we have some meta data requested, and most of the time we will
			foreach ($keys as $i2 => $v2) {	
				//if (!isset($userobj->$i2)) {  // in some versions the overloading does not work - only fetches 1
					//$userobj->$i2 = get_user_meta($userobj->ID, $i2, false);
					//wordpress does some kind of overloading to fetch meta data  BUT above only fetches single
					
				$test = get_user_meta($userobj->ID, $i2, false); // get as array in case there are multiple values
				
				if (!empty($test)) { 

					if (is_array($test)) {  // because we are now checking for multiple values so it returns an array

						if (count($test) == 1) { // one record, single value returned
						
							$temp = array_pop($test);  // get that one record
							// oh dear next code broke those nasty complex s2membercustom fields
							// but it's the way to deal with non associative arrays
							if (is_array($temp)) { // if that one record is an array - hope to hell that's the end of the nested arrays, but now it wont be
								
								if (!amr_is_assoc($temp)) { // if it is a numeric keyed array, cannot handle as per associative array									
									$temp = implode (', ',$temp); // must be a list of values ? implode here or later?
									// or should we force it into a mulit meta array ?
								}
								// else	leave as is for further processing							
							}
							
							$userobj->$i2 = $temp;  // save it as our value
							//$userobj->$i2 = array_pop($test); // cannot indirectly update an overloaded value

						}
						else { 
						// we got multple meta records - ASSUME for now it is a good implementation and the values are 'simple'
						// otherwise they really should create their meta data a better way. Can't solve everyones problems.
							$userobj->$i2 = implode(', ',$test);
						}
					}
					else 
						$userobj->$i2 = $test;	
					
					$temp = maybe_unserialize ($userobj->$i2); // in case anyone done anything weird
					$temp = objectToArray ($temp); /* must do all so can cope with incomplete objects  eg: if creatingplugin has been uninstalled*/
					$key = str_replace(' ','_', $i2); /* html does not like spaces in the names*/
					if (is_array($temp)) { 
					
						foreach ($temp as $i3 => $v3) {

							$key = $i2.'-'.str_replace(' ','_', $i3);/* html does not like spaces in the names*/
							
							if (is_array($v3)) {  
							// code just in case another plugin nests deeper, until we know tehre is one, let us be more efficient
								if (amr_is_assoc($v3)) { // does not yet handle, just dump values for now
									// really shouldn't be nsted this deep associativey - bad
									$users[$i][$key] = implode(", ", $v3);
								}
								else { // is numeric array eg s2member custom multi choice
									$users[$userobj->ID][$key] = implode(", ", $v3);
								}
							}
							else $users[$userobj->ID][$key] = $v3;
						}
					}	
					else {
						$users[$userobj->ID][$key] = $temp;
						//if (WP_DEBUG) {echo '<br/>Not an array'; var_dump($temp);}
					}
					unset($temp);
					// we could add some include / exclude checking here?
				}	
			} /// end for each keys
		} // 
		unset($all[$i]);
	} // end for each all
	unset($all);
	
	$users = apply_filters('amr_get_users', $users); 
	// allow addition or removal of normal wp users who will have userid, and /or any other data
	
	//track_progress('after get users meta check '.(count($users)));

	$post_types=get_post_types();			
	/* get the extra count data */
	if (amr_need_the_field($list,'comment_count')) 
		$c = get_commentnumbers_by_author();
	else $c= array();		
	//track_progress('after get comments check');
	if (!empty($users)) {
		foreach ($users as $iu => $u) {
		// do the comments
			//if (WP_DEBUG) {echo '<br />user=';var_dump($u); }
			if (isset ($c[$u['ID']])) {
				$users[$iu]['comment_count'] = $c[$u['ID']]; /*** would like to cope with situation of no userid */
				}
		// do the post counts		
			foreach ( $post_types as $post_type ) {		
				if (amr_need_the_field($list,$post_type.'_count')) {				
					$users[$iu][$post_type.'_count'] = amr_count_user_posts($u['ID'], $post_type);
	//					if ()WP_DEBUG) echo '<br />**'.$post_type.' '.$list[$iu][$post_type.'_count'];
	//					$list[$iu]['post_count'] = get_usernumposts($u['ID']); /* wordpress function */
					if ($users[$iu][$post_type.'_count'] == 0) unset($users[$iu][$post_type.'_count']);
				}				
			}
			if (amr_need_the_field($list,'first_role')) { 
				$user_object = new WP_User($u['ID']);
				if (!empty($user_object->roles)) 
					$users[$iu]['first_role'] = amr_which_role($user_object); 
				if (empty ($users[$iu]['first_role'] )) 
					unset($users[$iu]['first_role']);	
			}
		}
	}
	//track_progress('after post types and roles:'.count($users));
	unset($c);
	$users = apply_filters('amr_get_users_with_meta', $users); // allow addition of users from other tables with own meta data
	
	//track_progress('after user filter, have'.count($users));

	if (empty($users)) return (false);
	
return ($users);	
}
/* ------------------------------------------------------------------*/	

function amr_get_userdata($id){
	$data = get_userdata($id);    
	if (!empty($data->data)) return($data->data); // will not have meta data
	else return ($data);
};
/* -------------------------------------------*/	

function ameta_cache_enable () {
	/* Create a cache table if t does not exist */
		global $wpdb, $charset_collate;
	/* 	if the cache table does not exist, then create it . be VERY VERY CAREFUL about editing this sql */
	
		if (empty($charset_collate)) 
			$cachecollation = ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ';
		else 
			$cachecollation = $charset_collate;
	
		$table_name = ameta_cachetable_name();
		
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
			  id bigint NOT NULL AUTO_INCREMENT,
			  reportid varchar(20) NOT NULL,
			  line bigint(20) NOT NULL,
			  csvcontent text NOT NULL,
			  PRIMARY KEY  (id),
			  UNIQUE KEY reportid (reportid,line ) )
			  ".$cachecollation." ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);		
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				error_log($table_name.' not created');
				return false;
			}
			else return true;
		}
	return true;
}
	/* ---------------------------------*/

function ameta_cachelogtable_name() {
	global $wpdb;
	global $table_prefix;
	
		if (is_network_admin() or amr_is_network_admin())
			$table_name = $wpdb->base_prefix . "network_amr_reportcachelogging";
		else
			$table_name = $wpdb->prefix . "amr_reportcachelogging";
		return($table_name);
	}
	/* ---------------------------------*/

function ameta_cachetable_name() {
	global $wpdb;
	global $table_prefix;
		if (is_network_admin() or amr_is_network_admin())
			$table_name = $wpdb->base_prefix . "network_amr_reportcache";
		else
			$table_name = $wpdb->prefix . "amr_reportcache";
		return($table_name);
	}
	/* ---------------------------------*/

function ameta_cachelogging_enable() {
	/* Create a cache logging register table if t does not exist */
		global $wpdb, $charset_collate;
				
		if (empty($charset_collate)) 
			$cachecollation = ' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci ';
		else 
			$cachecollation = $charset_collate;
		
	/* 	if the cache table does not exist, then create it . be VERY VERY CAREFUL about editing this sql */
		$table_name = ameta_cachelogtable_name();
		if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
			  id bigint NOT NULL AUTO_INCREMENT,
			  eventtime datetime NOT NULL,
			  eventdescription text NOT NULL,
			  PRIMARY KEY  (id) )
			  ".$cachecollation. "
			;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			dbDelta($sql);
			
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				error_log($table_name.' not created');
				return false;
			}
			else return true;

		}
		return true;
}
/* ---------------------------------*/

function amr_build_user_data_maybe_cache($ulist='1') {  //returns the lines of data, including the headings
global $amr_refreshed_heading;  // seems heading not used right when we are filtering. workaround for now.
	/* Get the fields to use for the chosen list type */

global $aopt, $amrusers_fieldfiltering;
global $amain;
global $wp_post_types;
global $time_start;
global $cache;
global $amr_current_list;
	$amr_current_list = $ulist;

	if (get_transient('amr_users_cache_'.$ulist)) {
		track_progress('Stop - run for '.$ulist.' in progress already according to transient');
		return false;
	}
	else track_progress('Set in progress flag for '.$ulist);
	set_transient('amr_users_cache_'.$ulist,true, 10); // 10 seconds allowed for now
	
	$network = ausers_job_prefix();
//	track_progress('Getting data for network='.$network);
	register_shutdown_function('amr_shutdown');
	set_time_limit(360);  // should we make this an option.... 
	$time_start = microtime(true);

	ameta_options();

	$date_format = get_option('date_format');
	$time_format = get_option('time_format');

	add_filter('pub_priv_sql_capability', 'amr_allow_count');// checked by the get_posts_by_author_sql
	if (!isset($amrusers_fieldfiltering)) 
		$amrusers_fieldfiltering = false;
	if (function_exists('amr_check_for_realtime_filtering'))
		amr_check_for_realtime_filtering($ulist);
	if (empty($aopt['list'][$ulist])) {
		track_progress('No configuration for list '.$ulist);
		return false;
	}
	$l = $aopt['list'][$ulist]; /* *get the config  with any additional filtering */

	$rptid = amr_rptid($ulist);
	if (!$amrusers_fieldfiltering) { // then do cache stuff
		/* now record the cache attempt  */
		$cache = new adb_cache();
		$r = $cache->clear_cache($rptid);
//		If (!($r)) echo '<br />Cache does not exist or not cleared for '.$rptid;
		$r = $cache->record_cache_start($rptid, $amain['names'][$ulist]);
//		If (!($r)) echo '<br />Cache start not recorded '.$rptid;
//		$cache->log_cache_event(sprintf(__('Started cacheing report %s','amr-users'),$rptid));
	}// end cache

		//track_progress('before get all users needed');
		$list = amr_get_alluserdata($ulist); /* keyed by user id, and only the non excluded main fields and the ones that we asked for  */
		
		$total = count($list);
		//track_progress('after get all user data'.$total);
		
		$head = '';
		$tablecaption = '';

		if ($total > 0) {
			if (isset ($l['selected']) and (count($l['selected']) > 0))  {

				$head .= PHP_EOL.'<div class="wrap" style ="clear: both; text-align: center; font-size:largest;"><!-- heading wrap -->'
				.PHP_EOL.'<strong>'
				.$amain['names'][$ulist].'</strong>';
				/* to look like wordpress */
				$tablecaption .= '<caption> '.$amain['names'][$ulist].'</caption>';
				$head .= '<ul class="report_explanation" style="list-style-type:none;">';
		/* check for filtering */

				if (isset ($l['excluded']) and (count($l['excluded']) > 0)) {/* do headings */
					$head .= '<li><em>'.__('Excluding where:','amr-users').'</em> ';
					foreach ($l['excluded'] as $k=>$ex) {
						if (is_array($ex)) 
							$head .= ' '.agetnice($k).'='.implode(__(' or ','amr-users'),$ex).',';
						else 
							$head .= ' '.agetnice($k).'='.$ex.', ';
						if (empty($list)) return;	
						foreach ($list as $iu=>$user) {
							if (isset ($user[$k])) { /* then we need to check the values and exclude the whole user if necessary  */
								if (is_array($ex)) {
									if (in_array($user[$k], $ex)) {								
										unset ($list[$iu]);
									}
								}
								else {
									if ($user[$k] == $ex) {
										unset ($list[$iu]);
									}
								}
							}
						}
					}
					$head = rtrim($head,',');
					$head .= '</li>';

				}

				if (isset ($l['excludeifblank']) and (count($l['excludeifblank']) > 0)) 	{
					$head .= '<li><em>'.__('Exclude if blank:','amr-users').'</em> ';
					foreach ($l['excludeifblank'] as $k=>$tf) {
						$head .= ' '.agetnice($k).',';
						foreach ($list as $iu=>$user) { /* now check each user */

							if (empty($user[$k])) { /* if does not exists or empty then we need to check the values and exclude the whole user if necessary  */
								unset ($list[$iu]);
							}
						}
					}
					$head = rtrim($head,',');
					$head .='</li>';
				}
				
				//if (WP_DEBUG) track_progress('after excluding users:'.count($list));
				
				if (isset ($l['includeonlyifblank']) and (count($l['includeonlyifblank']) > 0)) 	{
					$head .= '<li><em>'.__('Include only if blank:','amr-users').'</em> ';
					foreach ($l['includeonlyifblank'] as $k=>$tf) {
						$head .= ' '.agetnice($k).',';
						foreach ($list as $iu=>$user) { /* now check each user */					
							if (!empty($user[$k])) { /* if does not exists or empty then we need to check the values and exclude the whole user if necessary  */
								unset ($list[$iu]);
							}
						}
					}
					$head = rtrim($head,',');
					$head .='</li>';
				}
				//if (WP_DEBUG) track_progress('after checking include if blank:'.count($list));
				if (isset ($l['included']) and (count($l['included']) > 0)) {
					$head .= '<li><em>'.__('Including where:','amr-users').'</em> ';
					foreach ($l['included'] as $k=>$in) {
						//if (WP_DEBUG) {echo '<br />Check include:'.$k;var_dump($in);}
						$inc = implode(__(' or ','amr-users'),$in);
						$head .= ' '.agetnice($k).'='.$inc.',';
						//if (WP_DEBUG) {echo '<br />'.$head;}
						if (!empty($list)) {
							foreach ($list as $iu => $user) { /* for each user */
								if (isset ($user[$k])) {/* then we need to check the values and include the user if a match */
									// user[k] could be csv multiple values 
									// inclusions an array, so need to do a like? or a 
									$is_in = false;
									//if (WP_DEBUG) {echo '<br />Check user:'.$user[$k];}								
									foreach ($in as $i) {
										$instr = strpos($user[$k], $i);
										if (!($instr === false)) {

											$is_in = true;
										}
									}
									if (!($is_in)) {
										unset ($list[$iu]);
									}
								}
								else unset ($list[$iu]);
							}
						}
					}
					$head = rtrim($head,',');
					$head .='</li>';
				}
				
				//if (WP_DEBUG) {	track_progress('after checking includes '.count($list));//echo '<br />'.$head;				}
				if (isset ($l['sortby']) and (count($l['sortby']) > 0)) {
					$head .= '<li class="sort"><em>'.__(' Cache sorted by: ','amr-users').'</em>';			/* class used to replace in the front end sort info */
					asort ($l['sortby']);  // sort the sortbys first, so that $cols is in right order
					
					
					$cols= array();
					foreach ($l['sortby'] as $sbyi => $sbyv) {
						if (isset($l['sortdir'][$sbyi]))
							//$cols[$sbyi] = array(SORT_DESC);  20111214
							$cols[$sbyi] = SORT_DESC;
						else
							//$cols[$sbyi] =  array(SORT_ASC);  20111214
							$cols[$sbyi] =  SORT_ASC;
						$head .= agetnice($sbyi).',';
					}
					//track_progress('after sortby '.$ulist);
					$head = rtrim($head,',');
					$head .='</li>';
					
					//track_progress('before msort cols =  '.count($cols));
					if (!empty($cols)) 
						$list = auser_multisort($list, $cols );
					//track_progress('after msort '.$ulist);	

				}
				
				unset($cols);
				if (empty($list))
					$tot = 0;
				else				
					$tot = count($list);
				//track_progress('after sorting '.$tot.' users');
				
				
				if ($tot === $total) {
					$text = sprintf(__('All %1s Users processed.', 'amr-users'), $total);
					
				}	
				else {
					$text = sprintf( __('%1s Users processed from total of %2s', 'amr-users'),$tot, $total);
				}
				//$tottext = 	sprintf(__('%1s records in list', 'amr-users'), $tot);			
					
				$head .=  '<li class="selected">'.$text.'</li>';

				
				$head .='</ul>'.
				PHP_EOL.
				'</div><!-- heading wrap -->'.PHP_EOL;
				
				//if (WP_DEBUG) {echo '<br />'.$head;}	
								
				$html = $head;
				if (empty($amr_refreshed_heading)) 
					$amr_refreshed_heading = $head; 
				else 
					$amr_refreshed_heading = $head.$amr_refreshed_heading;	
				$html = $head;

				$count = 0;

				//now make the fields into columns

				if ($tot > 0) { //if (empty($list)) echo '<br />1What happened list is empty ';
					if (!empty($l['grouping'][1])) 
						$grouping_field = $l['grouping'][1];
					$sel = ($l['selected']);  
					asort ($sel); /* get the selected fields in the display  order requested */

					foreach ($sel as $s2=>$sv) {
						if ($sv > 0) $sel2[$s2] = $sv;
					}
					
					// here we can jump in and save the filter values, if we are NOT already doing a real timefilter
					// if do filtering , then build up filter for values now
					if (!$amrusers_fieldfiltering  and function_exists('amr_save_filter_fieldvalues')) {
						$combofields = amr_get_combo_fields($ulist);
						if (empty($list)) echo '<br />What happened list is empty ';
						amr_save_filter_fieldvalues($ulist, $list, $combofields);
					}


					/* get the col headings ----------------------------*/
					$lines[0] = amr_build_cols ($sel2); // tech headings
					$lines[1] = amr_build_col_headings ($sel2);

					// the headings lines
					foreach ($lines[1] as $jj => $kk) {
						
						if (empty($kk)) 
							$lines[1][$jj] = '""'; /* there is no value */
						else 
							$lines[1][$jj] = '"'.str_replace('"','""',$kk).'"'; /* Note for csv any quote must be doubleqouoted */
					}

					if (!$amrusers_fieldfiltering) { // then do cache stuff
					/* cache the col headings ----------------------------*/

						//$csv = implode (",", $iline);
						$cache->cache_report_line($rptid,0,$lines[0]); /* cache the internal column headings */
//					$cols = amr_users_get_column_headings  ($ulist, $line, $iline);
						//$csv = implode (",", $line);
						$cache->cache_report_line($rptid,1,$lines[1]); /* cache the column headings */
						//unset($cols);
						//unset($line);unset($iline);
						unset($lines);

						//track_progress('before cacheing list');

					}
					
					$count = 1;
															
					if (!empty($list)) {
											
						foreach ($list as $j => $u) {
							//if (WP_DEBUG) echo '<br />Building list add: '.$j; var_dump($u);;
							$count  = $count +1;
							unset ($line);
							if (!empty($u['ID'])) 
								$line[0] = $u['ID']; /* should be the user id */
							else 
								$line[0] = '';
								
							foreach ($sel2 as $is => $v) {  /* defines the column order */
							
								$colno = (int) $v;
								if (!(isset($u[$is])))
									$value = ''; /* there is no value */
								else
									$value =  $u[$is];
									
								/* unfortunately for fields, this must be done here */	
								if (!empty($value)) {
									if (!empty($l['before'][$is]))
										$value = html_entity_decode($l['before'][$is]).$value;
									if (!empty($l['after'][$is]))
										$value = $value.html_entity_decode($l['after'][$is]);
								}
								
								if (!empty($line[$colno]))
									$line[$colno] .= $value;
								else
									$line[$colno] = $value;
							}
							if (function_exists('ausers_build_format_column')) { // code to call extra function added as per andy.bounsall request, not yet fully tested by me
								foreach ($line as $colno => $value) {
									$line[$colno] = ausers_build_format_column($ulist, $colno, $value);
								}
							}
							/* ******  PROBLEM - ok now? must be at end*/	
							/* *** amr - can we save the grouping field value similar to the index maybe ? */	
							if ((!empty($grouping_field)) and (!empty($u[$grouping_field]))) 
								$line[99998] = $u[$grouping_field]; 
//							else 
//								$line[99998] = '';		
// save the index value if have it							
							if (!empty($u['index'])) 
								$line[99999] = $u['index']; 
							else 
								$line[99999] = '';

							$lines[$count] = $line;
							unset ($line);

						}
					}	
					if (empty($lines)) {echo '<br / >Problem - no lines';}
					//else if (WP_DEBUG) {echo '<br />'; var_dump($lines);}

					unset($list); // do not need list, we got the lines now


				if (!$amrusers_fieldfiltering) { // then do cache stuff
					$cache->cache_report_lines($rptid, 2, $lines);
				}

				}
				else $html .= sprintf( __('No users found for list %s', 'amr-users'), $ulist);
			}
			else 
				$html .=  '<h2 style="clear:both; ">'.sprintf( __('No fields chosen for display in settings for list %s', 'amr-users'), $ulist).'</h2>';
		}
		else $html .= __('No users in database! - que pasar?', 'amr-users');
		unset($s);
		//track_progress('nearing end');

		if (!$amrusers_fieldfiltering) { // if we are not just doing a real time filtering where we will not have full data then do cache stuff
			$cache->record_cache_end($rptid, $count-1);
			$cache->record_cache_peakmem($rptid);
			$cache->record_cache_headings($rptid, $html);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$cache->log_cache_event('<em>'
			.sprintf(__('Completed %s in %s microseconds', 'amr-users'),$rptid, number_format($time,2))
			.'</em>');
		}

		if (!empty($amain['public'][$ulist])) { // returns url if set to go to file
			$csvurl = amr_generate_csv($ulist, true, false,'csv','"',',',chr(13).chr(10), true );
		}

		delete_transient('amr_users_cache_'.$ulist); // so another can run
		track_progress('Release in progress flag for '.$ulist);
		delete_transient('amr-users-html-for-list-'.$ulist); // to force use of new one
		
		// we built up the html when filtering, but then trashed again ?
			
		if (!empty($lines)) 
			return ($lines);
		else return false;
}
/* -----------------------------------------------------------------------------------*/

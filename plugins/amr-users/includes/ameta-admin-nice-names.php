<?php

/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_nicenames()	{
	global $amr_nicenames;
	
		if (empty ($amr_nicenames)) echo 'Unexpected problem - No nicenames !!!';
		
		$amr_nicenames['ID'] = '';
		
		if (isset($_POST['nn'])) { 
			
			if (is_array($_POST['nn'])) {
				foreach ($_POST['nn'] as $i => $v) {
					if (empty($v)) $amr_nicenames[$i] = '';
					else { 
						if	($s = esc_attr($v))  		
							$amr_nicenames[$i] = $s;
						else { 
							echo '<h2>Error in string:'.$s.'</h2>';
							return(false);
							}	
					}
					}
	
				}
			else {
				echo '<h2>Array of names not passed</h2>';
				return(false);
				}
			}
		ausers_update_option ('amr-users-nicenames', $amr_nicenames);		

		$excluded = array(); 
		if ((isset($_POST['nex'])) and (is_array($_POST['nex']))) {
			foreach ($_POST['nex'] as $i => $v) {
				if ($v) $excluded[$i] = true; 
			}
		}
		ausers_update_option('amr-users-nicenames-excluded', $excluded);	
		
		$showinwplist = array(); 
		if ((isset($_POST['wp'])) and (is_array($_POST['wp']))) {
			foreach ($_POST['wp'] as $i => $v) {
				if ($v) $showinwplist[$i] = true; 
			}
		}
		ausers_update_option('amr-users-show-in-wplist', $showinwplist);
		
		echo amr_users_message(__('Options Updated', 'amr-users')); 	
		return (true);	
	}
/* -------------------------------------------------------------------------------------------------------------*/
function ameta_listnicefield ($nnid, $nnval, $v, $v2=NULL) {
	
		echo "\n\t".'<li><label class="lists" for="nn'.$nnid.'"  '.(is_null($v2)?'>':' class="nested" >') .$v.' '.$v2.'</label>'
		.'<input type="text" size="50" id="nn'.$nnid.'"  name="nn['.$nnid.']"  value= "'.$nnval.'" /></li>'; 
	}
/* ---------------------------------------------------------------------*/
function alist_rebuild_names () {	
	return ('
	<div style="float:left; padding: 0 10px;" class="submit">
		<input type="hidden" name="action" value="save" />
		<input type="submit" class="button" name="rebuild" value="'. __('Find any new fields.', 'amr-users') .'" />
	</div>');
	}
/* ---------------------------------------------------------------------*/
function alist_rebuild_names_update () {	
	return ('
	<div style="float:left; padding: 0 10px;" class="submit">
		<input type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
		<input type="submit" class="button" name="rebuild" value="'. __('Find any new fields.', 'amr-users') .'" />
		<input type="submit" class="button" name="resetnice" value="'. __('Reset and make new nice names', 'amr-users') .'" />
	</div>');
	}	
/* ---------------------------------------------------------------------*/
function ameta_list_nicenames_for_input($nicenames) {
	/* get the standard names and then the  meta names  */
		if (!($excluded = ausers_get_option('amr-users-nicenames-excluded'))) 
			$excluded = array();
		if (!($showinwplist= ausers_get_option('amr-users-show-in-wplist')))
			$showinwplist = array();
			
		$orig_mk = ausers_get_option('amr-users-original-keys') ;	
		$wpfields = amr_get_usermasterfields();		
			
		ksort($nicenames);	
		
		echo PHP_EOL.'<div class="clear"> </div>'.PHP_EOL;	
		echo '<div><!-- nice names list-->';
		echo '<h3>'.__('Nicer names for list headings','amr-users').'</h3>'
		.'<ul>'
		.'<li>'
		.__('Extracts all user meta records (almost, some specifically excluded.)','amr-users')
		.' <strong>'.__('Sample data MUST exist!','amr-users').'</strong>'
		.'</li>'
		.'<li>'
		.__('Digs deep into composite records, to extract out "fields" that should have been simple meta.','amr-users')
			.'</li>'
		.'<li>'
		.__('Composite user meta are usually created by plugins who do not do things the way wordpress intended.','amr-users')
		.'</li>'
		.'<li>'		
		.__('If the necessary add ons have been activated, will dig deeper or look further into other tables.','amr-users')
		.'</li> '
		.'</ul>'
		.'<table class="widefat">';
		echo '<tr><th> </th><th>'
		.__('Nice Name','amr-users')
		.'</th>'
		.'<th>'
		.'<a title="'.__('Yes the main wordpress user list','amr-users').'" href="'.network_admin_url('users.php').'">'
		.__('Show in wp user list?','amr-users')
		.'</a>'
		.'<br /><em>'.__('wp fields only','amr-users').'</em>'
		.'</th>'
		.'<th>'
		.__('Exclude from Reports?','amr-users')
		.'</th>'
		.'</tr>';
		foreach ($nicenames as $i => $v ) {
			echo "\n\t".'<tr>'
			.'<td><label for="nn'.$i.'" >'.$i.'</label></td><td>'
			.'<input type="text" size="40" id="nn'.$i.'"  name="nn['.$i.']"  value= "'.$v.'" />';
			echo '</td><td>';

			echo '<input type="checkbox" id="wp'.$i.'"  name="wp['.$i.']"';
			if (!empty($showinwplist[$i])) echo ' value=true checked="checked" ';
			echo ' />';
				
			if ((empty($orig_mk[$i]) or (!($orig_mk[$i] == $i))) and
				(!in_array($i,$wpfields))){ 
				echo ' <a href="#" title="'.__('This field may/may not show in the wp list.  It is not a simple user meta field','amr-users').'">!</a> ';}
			echo '</td><td>';
			if ($i==='ID') echo ' ' ;
			else {
				echo '<input type="checkbox" id="nex'.$i.'"  name="nex['.$i.']"';
				if (!empty($excluded[$i])) echo ' value=true checked="checked" ';
				echo ' />';
			}
			
			echo '</td>';
			echo '</tr>';
			
		}	
		echo "\n\t".'</table>'
		.PHP_EOL.
		'</div><!-- nice names list-->'.PHP_EOL;
		return;	
		
	}
/* ---------------------------------------------------------------------*/	
function amrmeta_check_find_fields() {
global $amr_nicenames;

	$amr_nicenames = ausers_get_option ('amr-users-nicenames');  // refetch so have all includidng excluded
	
	if (is_wp_error($amr_nicenames) or (empty ($amr_nicenames))) { /* ***  Check if we have nicenames already built */
		echo '<h3 style="clear:both;">'.__('List of possible fields not yet built.', 'amr-users').'</h3>';
		track_progress('Before counting users');
		$result = count_users();
		track_progress('After counting users');
		$total_users = $result['total_users'];
		if ($total_users > 1000) { 
			amr_users_message(	__('You have many users. Please be patient when you rebuild.', 'amr-users'));
			echo '<p>';
			foreach ($result['avail_roles'] as $i => $t) {
				echo '<br />'.__($i).' '.$t;
			}
			echo '<p>';
			echo alist_rebuild_names();
			return;
		}
		else {
			echo '<h3 style="clear:both;">'.__('Automatically rebuilding list of possible fields now.', 'amr-users').'</h3>';
			track_progress('Before rebuilding names');
			$amr_nicenames = ameta_rebuildnicenames();
			
			track_progress('After rebuilding names');
			echo '<h3 style="clear:both;">'.__('List Rebuilt', 'amr-users').'</h3>';
		}
		ausers_update_option ('amr-users-nicenames', $amr_nicenames); 
	}
}
/* ---------------------------------------------------------------------*/
function amrmeta_nicenameshelp() {
// style="background-image: url(images/screen-options-right-up.gif);"

	$html = '<ol>'
	.'<li>'.__('If you are not seeing all the fields you expect to see, then rebuild the list. ', 'amr-users')
	.'</li>'
	.'<li>'
	.__('Please note that what you see is dependant on the data in your system. If there is no meta data for a field you are expecting to see, it is impossible for that field to appear.', 'amr-users').'</li>'
	.'<li>'.__('If you add another user related plugin that adds meta data, first add some data to at least one user.  Then you may need to rebuild the list of fields below and/or reconfigure your reports if you want to see the new data.', 'amr-users').'</li>'
	.'</ol>';
	return( $html);
}
/* ----------------------------------------------------------------------------------- */	
function amr_get_alluserkeys(  ) {

global $wpdb,$amr_nicenames;

/*  get all user data and attempt to extract out any object values into arrays for listing  */
	$keys = array(
		'avatar'=>'avatar',
		'comment_count'=>'comment_count',
		'post_count'=>'post_count');
		
	$post_types=get_post_types();  
	
	foreach ($post_types as $posttype) $keys[$posttype] = $posttype.'_count';
	
	$all = amr_get_usermasterfields(); 

	echo '<h3>'.sprintf(__('You have %s main user table fields', 'amr-users'),count($all)).'</h3>';

		foreach ($all as $i2 => $v2){	
			if (!amr_excluded_userkey($v2) ) {
				$keys[$v2] = $v2;	
				if (isset($keys[$v2])) {
					echo ' &#10003;'.$v2;
				}
				else echo '<br />'.__('Added to report DB:', 'amr-users').' '.$v2;
			}
			else {
				if (isset($keys[$v2])) unset($keys[$v2]);
				echo '<br />'.__('Excluded:', 'amr-users').' "'.$v2.'"<br />';
			}

		}
		/* Do the meta first  */
	$q =  "SELECT DISTINCTROW meta_key, meta_value FROM $wpdb->usermeta";
	// need the meta value for those like ym where there is one key but there may be complex stuff in the meta.

	if ($mkeys = amr_get_next_level_keys( $q)) {
	
		//if (WP_DEBUG) {echo '<br />For Debug: next level keys'; var_dump($mkeys);} 

		if (is_array($mkeys)) {
			$keys = array_merge ($keys, $mkeys);	
			echo '<h3>'.count($mkeys).' distinct "fields" dug out from the meta key/value combination records. </h3>';
		}
		//if (WP_DEBUG) {echo '<br />For Debug: Merged keys'; var_dump($keys);} 
	}

	unset($mkeys);
	

	
	echo '<h3>'.__('Check for fields from non wp tables.', 'amr-users').'</h3>';
	$keys2 = apply_filters('amr_get_fields', $keys); //eg: 'avatar'=>'avatar',
	foreach ($keys2 as $k => $v) {
		if (!isset($keys[$k]) and !isset($amr_nicenames[$v])) 
			echo '"'.$v.'" added.<br />';
	}

	return($keys2);
}
/** ----------------------------------------------------------------------------------- */
function amr_get_next_level_keys( $q) {
/*  get all user data and attempt to extract out any object values into arrays for listing  */
global $wpdb, $orig_mk;

	if (!$orig_mk = ausers_get_option('amr-users-original-keys')) 
		$orig_mk = array();
	
	$all = $wpdb->get_results($q, ARRAY_A); 
//	print_r ($all);
	if (is_wp_error($all)) {amr_flag_error ($all); return;}
	if (!is_array ($all)) return;
	echo '<br /><h3>'.sprintf(__('You have %u distinct meta key / meta value records. ','amr-users'),count($all)).'</h3>';
	_e('...Deserialising and rationalising...looking for new fields.', 'amr-users');
	foreach ($all as $i2 => $v2) {  /* array of meta key, meta value*/
			/* Exclude non useful stuff */
//			print_r ($v2);
			$mk = $v2['meta_key'];
			$mv = $v2['meta_value'];	

			if (!amr_excluded_userkey($mk) ) {
				
				if (!empty($mv)) {
					$temp = maybe_unserialize ($mv);
					$temp = objectToArray ($temp); /* *must do all so can cope with incomplete objects */
					$key = str_replace(' ','_', $mk); /* html does not like spaces in the names*/
				
					if ((is_array($temp)) and (amr_is_assoc($temp) ) ){
						foreach ($temp as $i3 => $v3) {
							
							if (is_array($v3) and function_exists('amr_dig_deeper')) { // *** needs work still
								//if (WP_DEBUG) echo'<br /> ** go down a level for '.$i3;
								$key2 = $key.'_'.str_replace(' ','_', $mk); /* html does not like spaces in the names*/	
								$subkeys = amr_get_next_level_down($mk, $key2, $v3);
								//if (WP_DEBUG) echo '<br /> **** got back '.$subkeys;
								$keys = array_merge($keys,$subkeys);
							}
							else {	

								$mkey = $key.'-'.str_replace(' ','_', $i3); /* html does not like spaces in the names*/
								$keys[$mkey] = $mkey;
								if (!isset($orig_mk[$mkey])) {
									$orig_mk[$mkey] = $mk;
									echo '<br />'.__('Added complex meta to report DB: ','amr-users').$mkey;
								}
								else {
									//echo ' &#10003;'.$mkey;
								}
								}
							}
						}
					else { 
						$keys[$key] = $key; 
						if (empty ($orig_mk[$key])) {
							$orig_mk[$key] = $mk;
							echo '<br />'.__('Added meta to report DB: ','amr-users').$key;
						}
						else {  
							//echo ' &#10003;'.$key;
						}
					}
				}	
				else {
					if (!isset ($keys[$mk])) {
					//if (!isset ($orig_mk[$key])) {
						$keys[$mk] = $mk;
						$orig_mk[$mk] = $mk;			// same same			
						echo '<br />'.__('Added to report DB: ','amr-users').$mk;
					}
				}
			}
			
	}		
	unset($all);
	//if (WP_DEBUG) {echo '<br />In Debug Only: Original keys mapping: '; var_dump($orig_mk);}
	ausers_update_option('amr-users-original-keys', $orig_mk);
	echo '<br />';
	//if (WP_DEBUG) {echo '<br />For Debug: Merged keys'; var_dump($keys);} 
return ($keys);	
}
/* -------------------------------------------------------------------------------------------------------------*/	
function ameta_rebuildnicenames (){
	global $wpdb,$amr_nicenames;
/*  */
//	amr_users_message (__('Rebuilding List of possible fields.  This could take a while - I have to query evey meta record, of which there can be multiple for each main record.  Please be patient...', 'amr-users'));
	/* check if we have some options already in Database. - use their names, if not, use default, else overwrite .*/
	flush(); /* try does not always work */
	$oldnn = ausers_get_option('amr-users-nicenames');
	$nn = ameta_defaultnicenames();  /* get the default list names required */

	/*	Add any new fields in */
	unset($list);
	$list = amr_get_alluserkeys();  /* maybe only do this if a refresh is required ? No only happens on admin anyway ? */
	echo '<h3>'.__('Try to make some nicer names.', 'amr-users').'</h3>';	
	/**** wp has changed - need to alllow for prefix now on fields.  Actually due to wpmu - keep the prefix, let the user remove it!  */
	foreach ($list as $i => $v) {
		if (empty( $nn[$v])) 	{ /* set a reasonable default nice name */
			if (!empty($oldnn[$v])) {
				$nn[$v] = $oldnn[$v];
				//echo '<br />'. sprintf(__('Use existing name %s for %s', 'amr-users'),$nn[$v],$v);
			}
			else {  // take the last part of the field only - no not nice too unpredictable
				//$lastdash = strripos($v,'-');
				//$nn[$v] = substr($v, $lastdash);
				$nn[$v] = $v;
				if (function_exists ('amr_check_ym_custom_nicenames'))  // look and fix ym custom fields 
					$nn[$v] = amr_check_ym_custom_nicenames($nn[$v]);
				$nn[$v] = str_replace('s2member_custom_fields','s2m',$nn[$v]); // if it is a s2member field - reduce length of name
				$nn[$v] = (str_replace('s2member', 's2m',$nn[$v]));	
				$nn[$v] = (str_replace('capabilities', 'Cap',$nn[$v]));	
				$nn[$v] = (str_replace('-', ' ',$nn[$v]));
		//		if (isset ($wpdb->prefix)) {$nn[$v] = str_replace ($wpdb->prefix, '', $nn[$v]);} 
				/* Note prefix has underscore*/
				
				$nn[$v] = (str_replace('_', ' ',$nn[$v]));		
				$nn[$v] = ucwords ($nn[$v]);	
				echo '<br />'. sprintf(__('Created name %s for %s', 'amr-users'),$nn[$v],$v);
			}
		}
	}
	unset($list);
	
	amr_check_for_table_prefixes($nn) ;
	ausers_update_option('amr-users-nicenames',$nn);
	$amr_nicenames = $nn;
	return($nn);
}
/* ----------------------------------------------------------------------------------- */	
function amr_check_for_table_prefixes ($nn) {
// use a field that is always there and has the table prefixes
	$prefixes_in_use = array();
	$checkfield = 'user-settings-time';
	foreach ($nn as $i=> $n) {
		if (stristr($i, $checkfield)) {
			$prefixes_in_use[] = str_replace($checkfield, '', $i);
		}
	}
	ausers_update_option('amr-users-prefixes-in-use', $prefixes_in_use);
}
/* ----------------------------------------------------------------------------------- */	
function amr_meta_nice_names_page() {
	/* may be able to work generically */
	global $amr_nicenames;
	global $ausersadminurl;
	
	//amr_meta_main_admin_header('Find fields, make nice names' );
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc
	
	if (isset($_POST['action']) and !($_POST['action'] === "save")) return;
	
	echo PHP_EOL.'<div class="clear" style="clear:both;">&nbsp;</div>'.PHP_EOL;
	if (isset($_POST['update']) and ($_POST['update'] === "Update")) {/* Validate the input and save */
			if (amrmeta_validate_nicenames()) { // updates inside the function now
			}
			else echo '<h2>'.__('Validation failed', 'amr-users').'</h2>'; 	
		}
	if (isset($_POST['resetnice'])) { 
		if (ausers_delete_option ('amr-users-nicenames')) 
			echo '<h2>'.__('Deleting all nice name settings in database','amr-users').'</h2>';
		if (ausers_delete_option ('amr-users-nicenames-excluded')) 
			echo '<h2>'.__('Deleting all nice name exclusion settings in database','amr-users').'</h2>';	
		if (ausers_delete_option ('amr-users-original-keys')) 
			echo '<h2>'.__('Deleting original keys mapping in database','amr-users').'</h2>';	
	}
	if (isset($_POST['rebuild']) or isset($_POST['resetnice'])) {/* Rebuild the nicenames - could take a while */	
				$amr_nicenames = ameta_rebuildnicenames ();
				echo '<h3>'.__('Rebuild Complete.', 'amr-users').'</h3>'; 
				return;
		}
	else {
		amrmeta_check_find_fields();
	}

	echo alist_rebuild_names_update();
	
	$amr_nicenames = ausers_get_option('amr-users-nicenames');
	ameta_list_nicenames_for_input($amr_nicenames); 

	}	//end amrmeta nice names option_page
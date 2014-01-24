<?php
//include ('amr-users-csv.php');
include ('ameta-admin-overview.php');
/* --------------------------------------------------------------------------------------------*/
function amr_manage_headings_submit () {
	if (amr_users_can_edit('headings'))
			$headings_submit = 
			PHP_EOL.'<div style="float:left;"> <!-- headings -->'
			.'<input type="submit" name="update_headings" id="update_headings" class="button-primary" value="'
			.__('Update Column Headings','amr-users').'"/>&nbsp;'
			.'<input type="submit" name="reset_headings" id="reset_headings" class="button" value="'
			.__('Reset Column Headings','amr-users').'"/>'
			.'</div> <!-- end headings -->'.PHP_EOL;
		else $headings_submit = '';	
		return $headings_submit;
}
/* --------------------------------------------------------------------------------------------*/	
function amr_allow_update_headings ($cols,$icols,$ulist, $sortable) {
global $aopt;

	if (!empty($_POST['reset_headings'])) {// check for updates to headings
		amr_users_reset_column_headings ($ulist);
	}
	$cols = amr_users_get_column_headings  ($ulist, $cols, $icols);	
	
	if (!empty($_POST['update_headings'])) {// check for updates to headings
	
		foreach ($icols as $ic => $cv) {
			if (isset($_POST['headings'][$ic])) {				
				$customcols[$cv] = esc_html($_POST['headings'][$ic]);				
				if ($customcols[$cv] === $icols[$ic]) {// if same as default, do not save  !! NOT COLS
					unset($customcols[$cv]);
				}
			}
		}

		if (!empty($customcols)) amr_users_store_column_headings  ($ulist, $customcols);
	}
	
	$cols = amr_users_get_column_headings  ($ulist, $cols, $icols);
	
	$html = '';		
	foreach ($icols as $ic => $cv) { /* use the icols as our controlling array, so that we have the internal field names */
		if (!($ic == 'checkbox')) {   			
			$v 		= '<input type="text" size="'.
			min(strlen($cols[$ic]), 80)
			.'" name="headings['.$ic.']" value="'.$cols[$ic].'" />';
		}
		else $v = 	$cols[$ic];	
		
		$html 	.= '<td>'.$v.'</td>';
		
	}	
	$hhtml = '<tr>'.$html.'</tr>'; /* setup the html for the table headings */		
	return ($hhtml);		
}
/* -------------------------------------------------------------------------------------------------------------*/
function amrmeta_validate_listfields()	{
	global $aopt;

/* We are only coming here if there is a SAVE, now there may be blanked out fields in all areas - except must have something selected*/

	if ( get_magic_quotes_gpc() ) {
		$_POST      = array_map( 'stripslashes_deep', $_POST );
	}
				
	if (isset($_POST['list'])) {
		if (is_array($_POST['list'])) {/*  do we have selected, etc*/
			foreach ($_POST['list'] as $i => $arr) {		/* for each list */	
				
				if (is_array($arr))  {/*  */

					if (is_array($arr['selected']))  {/*  do we have  name, selected, etc*/		
						unset($aopt['list'][$i]['selected']);	
						foreach ($arr['selected'] as $j => $v) {
							$v = trim($v);
							if ((empty($v)) or ($v == '0')  ) unset ($aopt['list'][$i]['selected'][$j] );
							else {
								if ($s = filter_var($v, FILTER_VALIDATE_FLOAT,
									array("options" => array("min_range"=>1, "max_range"=>999))))
									$aopt['list'][$i]['selected'][$j] = $s;
								else {
									echo '<h2>Error in display order for '.$j.$s.'</h2>';
									return(false);
								}
							}							
						}
//						asort ($aopt['list'][$i]['selected']); /* sort at update time so we don't have to sosrt every display time */
					}
					else {
						echo '<h2>'.__('No fields selected for display','amr-users').'</h2>'; return (false);
					}
					
					/* Now check included */
					unset($aopt['list'][$i]['included']);	
					if (!empty($arr['included']) and is_array($arr['included']))  {		
						
						foreach ($arr['included'] as $j => $v) {
							if (a_novalue($v)) 
								unset($aopt['list'][$i]['included'][$j]);
							else {
								$aopt['list'][$i]['included'][$j] 
									= explode (',', filter_var($v, FILTER_SANITIZE_STRING));
								$aopt['list'][$i]['included'][$j] = 
									array_map('trim', $aopt['list'][$i]['included'][$j] );
								}
						}	
					}
															
					unset($aopt['list'][$i]['includeonlyifblank']);
					if (isset($arr['includeonlyifblank']) and is_array($arr['includeonlyifblank']))  {						
						foreach ($arr['includeonlyifblank'] as $j => $v) {
							$aopt['list'][$i]['includeonlyifblank'][$j] = true; 
							}	
						}	
					
					/* Now check excluded */
					unset($aopt['list'][$i]['excluded']);
					if (isset($arr['excluded']) and is_array($arr['excluded']))  {		
						foreach ($arr['excluded'] as $j => $v) {
							if (a_novalue($v)) unset($aopt['list'][$i]['excluded'][$j]);
							else 
							$aopt['list'][$i]['excluded'][$j] 
								= explode(',', filter_var($v, FILTER_SANITIZE_STRING));
							}	
						}	
					/* Now check what to do with blanks */
					unset($aopt['list'][$i]['excludeifblank']);
					if (isset($arr['excludeifblank']) and is_array($arr['excludeifblank']))  {						
						foreach ($arr['excludeifblank'] as $j => $v) {
							$aopt['list'][$i]['excludeifblank'][$j] = true;
							}	
						}	
						
							
						
					/* Now check sortby */
					unset ($aopt['list'][$i]['sortby']	);		/* unset all sort by's in case non eare set in the form */	
					if (isset($arr['sortby']) and is_array($arr['sortby']))  {
						foreach ($arr['sortby'] as $j => $v) {						
							if (a_novalue($v)) unset ($aopt['list'][$i]['sortby'][$j]);
							else $aopt['list'][$i]['sortby'][$j]  = $v;	
						}	
					}
					/* Now check sortdir */
					unset ($aopt['list'][$i]['sortdir']	);		/* unset all sort directions */		
					if (isset($arr['sortdir']) and is_array($arr['sortdir']))  {				
						foreach ($arr['sortdir'] as $j => $v) {									
							if (!(a_novalue($v))) $aopt['list'][$i]['sortdir'][$j] = $v;
							else $aopt['list'][$i]['sortdir'][$j] = 'SORT_ASC';
						}	
					}
										/* Now check before*/
					unset ($aopt['list'][$i]['before']	);		/* unset all  */		
					if (isset($arr['before']) and is_array($arr['before']))  {				
						foreach ($arr['before'] as $j => $v) {									
							if (!(a_novalue($v))) 
								$aopt['list'][$i]['before'][$j] = (esc_html($v));
							else $aopt['list'][$i]['before'][$j] = '';
						}	
					}
															/* Now check after*/
					unset ($aopt['list'][$i]['after']	);		/* unset all  */		
					if (isset($arr['after']) and is_array($arr['after']))  {				
						foreach ($arr['after'] as $j => $v) {									
							if (!(a_novalue($v))) 
							$aopt['list'][$i]['after'][$j] = esc_html($v);
							else $aopt['list'][$i]['after'][$j] = '';
						}	
					}
															/* Now check links*/
					unset ($aopt['list'][$i]['links']	);		/* unset all  */		
					if (isset($arr['links']) and is_array($arr['links']))  {				
						foreach ($arr['links'] as $j => $v) {									
							if (!empty($v)) $aopt['list'][$i]['links'][$j] = ($v);
							else $aopt['list'][$i]['links'][$j] = 'none';
						}	
					}
				}
			}
		
		ausers_update_option ('amr-users', $aopt);
	}
	else {
		echo '<h3>'.__('At least some display order must be specified for the list to be meaningful','amr-users').'</h3>';
		return (false);
		}
	}
	amr_users_message(__('Options Updated', 'amr-users'));
	
return (true);	
}
/* ---------------------------------------------------------------------*/
function amrmeta_listfields( $listindex = 1) {
	global $aopt;
	global $amain;
	global $amr_nicenames, 
	$excluded_nicenames,
	$ausersadminurl;

	$linktypes = amr_linktypes();

	/* check if we have some options already in Database. - use their names, if not, use default, else overwrite .*/
	if (!($checkifusingdefault = ausers_get_option ('amr-users-nicenames')) or (empty($amr_nicenames))) {
		//$text = __('Possible fields not configured! default list being used. Please build complete nicenames list.','amr-users');
		amrmeta_check_find_fields();		
		exit;
	}

	$config = &$aopt['list'][$listindex];

	$sel = &$config['selected'];
	/* sort our controlling index by the selected display order for ease of viewing */
	
	foreach ($amr_nicenames as $i => $n) {  
		if ((isset ($config['selected'][$i])) or
			(isset ($config['sortby'][$i])) or
			(isset ($config['included'][$i])) or
			(isset ($config['includeonlyifblank'][$i])) or
			(isset ($config['excluded'][$i])) or
			(isset ($config['excludeifblank'][$i])) or
			(isset ($config['sortdir'][$i])) 
			)
			$keyfields[$i] = $i;
		
	}
	if (isset ($keyfields))	
		$nicenames = auser_sortbyother($amr_nicenames, $keyfields); /* sort for display with the selected fields first */
	else 
		$nicenames = $amr_nicenames;

	if (count ($sel) > 0) {	
		uasort ($sel,'amr_usort');
		$nicenames = auser_sortbyother($nicenames, $sel); /* sort for display with the selected fields first */
	} 

		echo '<br /><p class="clear"><input id="submit" class="button-primary" type="submit" name="updateoverview" value="';
		_e('Update overview settings', 'amr-users'); 
		echo '" />'.
		 '&nbsp;<a href="'.wp_nonce_url($ausersadminurl.'?page=ameta-admin-general.php&tab=overview','amr-meta')
		.'" title="'
		.__('Go to overview of all lists', 'amr-users').'" >'
		.__('Manage lists', 'amr-users')
		.'</a>'
		.'&nbsp;|&nbsp;<a href="'.wp_nonce_url($ausersadminurl.'?page=ameta-admin-general.php&tab=fields','amr-meta')
		.'" title="'
		.__('Find Fields (must have sample data in them)', 'amr-users').'" >'
		.__('Find Fields', 'amr-users')
		.'</a>'
		.'</p>';			
		amr_meta_overview_onelist_headings();
		amr_meta_overview_onelist_headings_middle();
		amr_meta_overview_onelist_settings($listindex);
		amr_meta_overview_onelist_headings_end();

		echo '<br /><br />';
		echo PHP_EOL.'<div class="wrap">'.PHP_EOL
		.'<input id="submit" class="button-primary" type="submit" name="update" value="';
		_e('Update field settings', 'amr-users'); 
		echo '" />&nbsp;';
		amr_userlist_submenu ( $listindex );
		echo '<br />'; 
		
		echo PHP_EOL.'<div class="clear userlistfields">';

		echo '<table class="widefat" style="padding-right: 2px;"><thead  style="text-align:center;"><tr>'
			.PHP_EOL.'<th style="text-align:right;">'.__('Field name','amr-users').'</th>'
			.PHP_EOL.'<th style="width:1em;"><a href="#" title="'.__('Blank to hide, Enter a number to select and specify column order.  Eg: 1 2 6 8', 'amr-users').'"> '.__('Display order','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Html to appear before if there is a value', 'amr-users').'"> '.__('Before:','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Html to appear after if there is a value', 'amr-users').'"> '.__('After:','amr-users').'</a></th>'

			.PHP_EOL.'<th style="width:2em;"><a href="#" title="'.__('Type of link to be generated on the field value', 'amr-users').'"> '.__('Link Type:','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Eg: value1,value2', 'amr-users'). ' '
			.__('Do not use spaces unless your field values have spaces.', 'amr-users')
			.'"> '
			.__('Include:','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Tick to include a user ONLY if there is no value', 'amr-users').'"> '.__('Include ONLY if Blank:','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Eg: value1,value2.', 'amr-users')
			.' '.__('Display the field to set up the exclusion, then you can undisplay it afterwards.', 'amr-users'). ' '
			.__('Do not use spaces unless your field values have spaces.', 'amr-users')
			.'"> '.__('But Exclude:','amr-users').'</a></th>'
			.PHP_EOL.'<th><a href="#" title="'.__('Tick to exclude a user if there is no value', 'amr-users').'"> '.__('Exclude if Blank:','amr-users').'</a></th>'

			.PHP_EOL.'<th style="width:1em;"><a href="#" title="'
				.__('Enter integers, need not be contiguous', 'amr-users')
				.' '
				.__('Maximum 2 sort level. Can switch off display.', 'amr-users')
				.'"> '.__('Sort Order:','amr-users').'</a></th>'
			.PHP_EOL.'<th style="width:2em;"><a href="#" title="'.__('For sort order.  Default is ascending', 'amr-users').'"> '.__('Sort Descending:','amr-users').'</a></th>'

			.PHP_EOL.'</tr></thead><tbody>';
	
			foreach ( $nicenames as $i => $f )		{		/* list through all the possible fields*/			
				echo PHP_EOL.'<tr>';
				$l = 'l'.$listindex.'-'.$i;
				if ($i === 'comment_count') $f .= '<a title="'.__('Explanation of comment total functionality','amr-users')
				.'" href="http://wpusersplugin.com/comment-totals-by-authors/">**</a>';
				echo '<td style="text-align:right;">'.$f .'</td>';
					echo '<td><input type="text" size="1" id="'.$l.'" name="list['.$listindex.'][selected]['.$i.']"'. 
				' value="';
				if (isset($sel[$i]) or 
					(!empty($config['included'][$i])) or 
					(!empty($config['excluded'][$i])) or 
					(!empty($config['excludeifblank'][$i])) or 
					(!empty($config['includeonlyifblank'][$i])) or 
					(!empty($config['sortby'][$i])) or
					(!empty($config['sortdir'][$i])) 
					)  {
									
					if (isset($sel[$i]))	echo $sel[$i];			
					echo '" /></td>';

					if (!empty($sel[$i]) ) {
						/* don't need label - use previous lable*/	
						echo '<td><input type="text" size="10"  name="list['.$listindex.'][before]['.$i.']"';
						if (isset ($config['before'][$i])) echo ' value="'
						.stripslashes($config['before'][$i]).'"';  //handle slashes returned by quotes
						echo ' /></td>';  // do not use htmlentities2 here - break foreigh chars

						echo '<td><input type="text" size="10"  name="list['.$listindex.'][after]['.$i.']"';
						if (isset ($config['after'][$i])) echo ' value="'
						.stripslashes($config['after'][$i]).'"';
						echo ' /></td>';
					}
					else echo '<td>-</td><td>-</td>';
					
					if (isset($sel[$i]) and (!strpos($sel[$i],'.'))) {
					// if not a partial cell, then can have link type
					//if (isset($sel[$i]) and !strpos($sel[$i],'.')) {			
						echo '<td><select id="links'.$l.'" '
						.' name="list['.$listindex.'][links]['.$i.']" >';
						foreach ($linktypes as $lti => $linktype ) {
							 echo ' <option value="'.$lti.'" ';
							 if (!empty ($config['links'][$i]) and ($config['links'][$i] === $lti ))  
								echo ' selected = "selected" ';
							 echo ' >'.$linktype.'</option>';
							
						}	
						echo '</select></td>';
					}
					else echo '<td>-</td>';

//	echo '<td><select name="list['.$listindex.'][included]['.$i.']"';
//	echo amr_users_dropdown ($choices, $config['included'][$i]);
//	echo '</select>';
					
					echo '<td><input type="text" size="20"  name="list['.$listindex.'][included]['.$i.']"';
					if (isset ($config['included'][$i])) echo ' value="'.implode(',',$config['included'][$i]) .'"';
					
					echo ' /></td>';
					
					$l = 'c'.$listindex.'-'.$i;
					echo '<td><input type="checkbox"  name="list['.$listindex.'][includeonlyifblank]['.$i.']"';
					if (isset ($config['includeonlyifblank'][$i]))	{
						echo ' checked="checked" />';
						if (isset ($config['excludeifblank'][$i])) /* check for inconsistency and flag */
							echo '<span style="color:#D54E21; font-size:larger;">*</span>';
					}
					else echo '/>';
					echo '</td>';
					
					$l = 'x'.$listindex.'-'.$i;
					echo '<td><input type="text" size="20" id="'.$l.'" name="list['.$listindex.'][excluded]['.$i.']"';
					if (isset ($config['excluded'][$i])) {
						if (is_array($config['excluded'][$i])) 
							$val = implode(',',$config['excluded'][$i]);
						else $val = $config['excluded'][$i];	
						echo ' value="'.$val .'"';
					}
					echo ' /></td>';

					$l = 'b'.$listindex.'-'.$i;
					echo '<td><input type="checkbox" id="'.$l.'" name="list['.$listindex.'][excludeifblank]['.$i.']"';
					if (isset ($config['excludeifblank'][$i]))	{
						echo ' checked="checked" />';
						if (isset ($config['includeonlyifblank'][$i])) /* check for inconsistency and flag */
							echo '<span style="color:#D54E21; font-size:larger;">*</span>';
					}
					else echo '/>';
					echo '</td>';


					$l = 's'.$listindex.'-'.$i;
					echo '<td>'
					.'<input type="text" size="2" id="'.$l.'" name="list['.$listindex.'][sortby]['.$i.']"';
					if (isset ($config['sortby'][$i]))  echo ' value="'.$config['sortby'][$i] .'"';
					echo ' /></td>'
					.'<td><input type="checkbox" id="sd'.$l.'" name="list['.$listindex.'][sortdir]['.$i.']"';
					 echo ' value="SORT_DESC"';
					if (isset ($config['sortdir'][$i]))  echo ' checked="checked"';
					echo ' />'
					.'</td>';

				
}
				else {
					echo '" /></td>';
					echo '<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>';
				}
				

				echo '</tr>';
			}
		echo PHP_EOL.'</tbody></table>';
		echo PHP_EOL.'</div><!-- end userlistfield -->';
		echo PHP_EOL.'</div><!-- end wrap -->';
	return;	
	}
	/* ---------------------------------------------------------------------*/	
function au_grouping_link($i,$name) {
global $ausersadminurl,$ausersadminusersurl;		
	if (!function_exists('amr_grouping_admin_form')) {
			return ('<a style="color: #AAAAAA;" href="http://wpusersplugin.com/related-plugins/amr-users-plus-grouping/" '.
			'title="'
			.__('Activate or acquire amr-user-plus-grouping addon for listing users in a group by any field','amr-users').'" ' 
			.'>'
			.__('Edit grouping','amr-users').'</a>');
	}
	

	if (isset($_REQUEST['grouping']) ) { 
		$url = $ausersadminusersurl.'?page=ameta-list.php?ulist='.$i;
		$url = wp_nonce_url($url,'amr-meta');	
		return ('<b><a style="color: #006600;" href="'.htmlentities($url)
		.'">'.__('Exit grouping', 'amr-users').'</a></b>');
	}
	
	
	$url = $ausersadminurl.'?page=ameta-admin-configure.php';
	$url = (add_query_arg(array(
		'grouping'=>1,
		'ulist'=>$i), $url));
//		
	
	$t = '<a style="color:#D54E21; " href="'
//		.wp_nonce_url($url,'amr-meta')
		.$url
		.'" title="'.sprintf(__('Grouping %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit grouping', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_custom_nav_link($i,$name) {
global $ausersadminurl, $ausersadminusersurl;		
	if (!function_exists('amr_custom_navigation_admin_form')) {
			return ('<a style="color: #AAAAAA;" href="http://wpusersplugin.com/related-plugins/amr-users-plus/" '.
			'title="'.__('Activate or acquire amr-user-plus addon for custom (eg: alphabetical) navigation','amr-users').'" ' 
			.'>'
			.__('Edit navigation', 'amr-users').'</a>');
	}
	

	if (isset($_REQUEST['custom_navigation']) ) { 
		$url = $ausersadminusersurl.'?page=ameta-list.php?ulist='.$i;
		$url = wp_nonce_url($url,'amr-meta');	
		return ('<b><a style="color: #006600;" href="'.htmlentities($url)
		.'">'.__('Exit navigation', 'amr-users').'</a></b>');
	}
	
	
	$url = $ausersadminurl.'?page=ameta-admin-configure.php';
	$url = (add_query_arg(array(
		'custom_navigation'=>1,
		'ulist'=>$i), $url));
//		
	
	$t = '<a style="color:#D54E21; " href="'
//		.wp_nonce_url($url,'amr-meta')
		.$url
		.'" title="'.sprintf(__('Custom navigation %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit navigation', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_filter_link($i,$name) {
global $ausersadminurl,$ausersadminusersurl;	
	if (!function_exists('amr_offer_filtering')) {
			return ('<a style="color: #AAAAAA;" href="http://wpusersplugin.com/related-plugins/amr-users-plus/" '.
			'title="'
			.__('Activate or acquire amr-user-plus addon for real time filtering','amr-users').'" ' 
			.'>'.__('Edit filtering', 'amr-users').'</a>');
	}
	
	if (isset($_REQUEST['filtering'])) 
	return ('<b><a style="color: #006600;" href="'.htmlentities($ausersadminusersurl.'?page=ameta-list.php?ulist='.$i)
	.'">'.__('Exit filtering', 'amr-users').'</a></b>');
	
	$t = '<a style="color:#D54E21; " href="'
		.htmlentities(add_query_arg(array('filtering'=>1),$ausersadminusersurl.'?page=ameta-list.php?ulist='.$i))
		.'" title="'.sprintf(__('Realtime filtering %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit filtering', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_headings_link( $i,$name) {
global $ausersadminurl,$ausersadminusersurl;
	$url = $ausersadminusersurl.'?page=ameta-list.php?ulist='.$i; 
	// doesn't like add_query_arg for ulistsomehow
	$url = wp_nonce_url($url,'amr-meta');

	if (isset($_REQUEST['headings'])) 
		return ('<a href="'.$url
		.'">'.__('Exit headings', 'amr-users').'</a>');
		
	$url = add_query_arg(array( 'headings' => 1),$url); 	
	$t = '<a style="color:#D54E21;" href="'
		.$url
		.'" title="'.sprintf(__('Edit the column headings %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit headings', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_buildcache_link($text, $i,$name) { // to refresh now!
global $ausersadminurl;
	$t = '<a style="color: green;" href="'.
		wp_nonce_url(
		add_query_arg(array(
		'page'=>'ameta-admin-configure.php',
		'rebuildwarning'=>'1',
		'ulist'=>$i),$ausersadminurl),
		'amr-meta')
		.'" title="'.__('Rebuild list', 'amr-users').'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_buildcache_view_link($text, $i,$name) { // to refresh now!
global $ausersadminusersurl;
	$t = '<a style="color: green;" href="'.
		add_query_arg(array(
		'page'=>'ameta-list.php?ulist='.$i,
		'refresh'=>'1')
		,$ausersadminusersurl.'')
		.'" title="'.__('Rebuild list in realtime - could be slow!', 'amr-users').'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_buildcachebackground_link() {//*** fix
	global $ausersadminusersurl;
	$t = '<a href="'.wp_nonce_url($ausersadminusersurl.'&amp;am_page=rebuildcache','amr-meta')
		.'" title="'.__('Build Cache in Background', 'amr-users').'" >'
		.__('Build Cache for all', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function amrmeta_confighelp() {
// style="background-image: url(images/screen-options-right-up.gif);"


	$html = '<p>'.__('Almost all possible user fields that have data are listed in field list (nice names).  If you have not yet created data for another plugin used in your main site, then there may be no related data here.  Yes this is a looooong list, and if you have a sophisticated membership system, it may be even longer than others.  The fields that you are working with will be sorted to the top, once you have defined their display order.', 'amr-users')
	.'</p><p>'
	.__('After a configuration change, the cached listing must be rebuilt for the view to reflect the change.', 'amr-users')
	.'</p><ol><li>'
	.__('Enter a number in the display order column to select a field for display and to define the display order.', 'amr-users')
	.'</li><li>'
	.__('Enter a number (1-2) to define the sort order for your list', 'amr-users')
	.'</li><li>'
	.__('Use decimals to define ordered fields in same column (eg: first name, last name)', 'amr-users')
	.'</li><li>'
	.__('If a sort order should be descending, such as counts or dates, click "sort descending" for that field.', 'amr-users')
	.'</li><li>'
	.__('From the view list, you will see the data values.  If you wish to include or exclude a record by a value, note the value, then enter that value in the Include or Exclude Column.  Separate the values with a comma, but NO spaces.', 'amr-users')
	.__('Note: Exclude and Include blank override any other value selection.', 'amr-users')
	.'</li></ol>';
	
	return($html);
}
/* ----------------------------------------------------------------------------------- */	
function list_configurable_lists() {
global $amain,$ausersadminurl;
	echo PHP_EOL.'<div class="clear"> </div>'.PHP_EOL;	
	echo '<div class="tablenav top"><div class="alignleft actions"><!-- list selection -->'
	.PHP_EOL
	.'<input type="hidden" name="page" value="ameta-admin.php"/>' 
	.'<select  id="list" name="ulist" >';

	if (isset($_REQUEST['ulist'])) 
			$current= (int) $_REQUEST['ulist'];
		else 
			$current=1;
		
 	if (isset ($amain['names'])) {
		foreach ($amain['names'] as $i => $name) {
					echo '<option value="'.$i.'"';
					if ($i === $current) echo ' selected="selected" ';
					echo '>'.$amain['names'][$i].'</option>';
			}
	};
	echo '</select>&nbsp;';
	echo PHP_EOL.'<input id="submit" class="button action" type="submit" name="configure" value="';
	//echo PHP_EOL.'<input id="submit" class="button-secondary subsubsub" type="submit" name="configure" value="';
	_e('Configure', 'amr-users'); 
	echo '" />';
	echo PHP_EOL.'<input type="hidden" name="copylist" value="'.$current.'"/>' ;
	echo '&nbsp;<input id="submit" class="button action" type="submit" name="addnew" value="';
	//echo '&nbsp;<input id="submit" class="button-secondary subsubsub" type="submit" name="addnew" value="';
	_e('Add new', 'amr-users'); 
	echo '" />';
	echo '</div></div><!-- list selection -->'.PHP_EOL;
	return;
}	
/* ----------------------------------------------------------------------------------- */	
function amrmeta_configure_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;	

	ameta_options();  // should handle emptiness etc
	
	if ((isset($_POST['addnew'])) and (isset($_POST['copylist']))) {  		
		$copyfrom = intval($_REQUEST['copylist']);
		$amain['names'][] = __('New list copy of ').$amain['names'][$copyfrom];
		$aopt['list'][] = $aopt['list'][$copyfrom];
		$ulist = array_pop(array_keys($amain['names']));
		$amain['names'][$ulist] .= ' #'.$ulist;
		ausers_update_option('amr-users', $aopt);
		ausers_update_option('amr-users-main', $amain);
		amr_users_message('Added list: '.$ulist);
		$_REQUEST['ulist'] = $ulist;
	}
	else {	
		if (!empty($_REQUEST['ulist']) ) {							
			$ulist = (int) $_REQUEST['ulist'];	
		}
		else {  // what if list 1 deleted?
			$ulist = '1';
			if (empty($amain['names'])) return false;   // omg they deleted all the lists
			reset($amain['names']);
			$ulist = key($amain['names']);
		}
	}
	
	amr_meta_main_admin_header('Configure a user list');
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check  and formstartetc


//	else
	if (isset ($_REQUEST['rebuild'])) { /* can only do one list at a time in realtime */
		amr_rebuild_in_realtime_with_info ($ulist);
		echo ausers_form_end();
		return;
	}/* then we have a request to kick off cron */

	elseif (!empty($_REQUEST['rebuildback']))  { /*  */	
		amr_request_cache_with_feedback(); 	
		echo ausers_form_end();		
		return;			
	}
	
	elseif (!empty($_REQUEST['rebuildwarning']))  { /*  */	
		amr_rebuildwarning($ulist); 
		echo ausers_form_end();
		return;			
	}	
	
	elseif (isset ($_REQUEST['custom_navigation'])) {
		if (function_exists('amrmeta_custom_navigation_page')) {

			amrmeta_custom_navigation_page($ulist);
			echo ausers_form_end();
			return;
		}
		else echo 'Function not active';
	}
	
	elseif (isset ($_REQUEST['grouping'])) {
		if (function_exists('amr_grouping_admin_form')) {

			amr_grouping_admin_form($ulist);
			echo ausers_form_end();
			return;
		}
		else _e('Grouping Function not active', 'amr-users');
	}	
	elseif (amr_users_can_edit ('filtering')) {
		amrmeta_filtering_page($ulist);
		
	}
	
	elseif (isset ($_POST['action']) and  ($_POST['action'] == "save")) { 
	
		if (isset ($_POST['updateoverview']) ) {
			
			amrmeta_validate_overview();
		
		}
		elseif (isset ($_POST['update']) ) {
			
			if (!amrmeta_validate_listfields($ulist)) {
				amr_users_message(__('List Fields Validation failed', 'amr-users')); 
			}	
		}		
		
		elseif (isset ($_POST['configure']) ) {
			// ulist already set above, so will just configure			
		}
	}

	list_configurable_lists();  // to allow selection of which to configure

	amrmeta_listfields($ulist);
	echo ausers_form_end();
}
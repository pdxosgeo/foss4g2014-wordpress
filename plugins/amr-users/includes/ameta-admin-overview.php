<?php

require_once ('ameta-admin-import-export-list.php');


/* ---------------------------------------------------------------*/
function amr_handle_copy_delete () {	
	global $amain, $aopt;
	if (!current_user_can('administrator')) {
		_e('Inadequate access','amr-users');
		return;
	}
	if (isset($_GET['copylist'])) {  	
		$source = (int) $_REQUEST['copylist'];
		if (!isset($amain['names'][$source])) echo 'Error copying list '.$source; 
		$next = 1;  // get the current max index
		foreach($amain['names'] as $j=>$name) { 
			$next = max($next,$j);
		}
		$next = $next +1;
		//
		foreach($amain as $j=>$setting) {
			if (is_array($setting)) { echo '<br />copying '.$j.' from list '.$source;
				if (!empty($amain[$j][$source]) ) 
					$amain[$j][$next] = $amain[$j][$source];
			}
		}
		$amain['names'][$next] .= __(' - copy');
		$amain['no-lists'] = count($amain['names']);
		if (!empty($aopt['list'][$source]) ) {
					echo '<br />copying settings from list '.$source;
					$aopt['list'][$next] = $aopt['list'][$source];
		}
		ausers_update_option ('amr-users-main', $amain);
		ausers_update_option ('amr-users', $aopt); 
		
	}
	elseif (isset($_GET['deletelist'])) { 
		$source = (int) $_REQUEST['deletelist'];
		
		if (!isset($amain['names'][$source])) 
			amr_users_message ( sprintf(__('Error deleting list %S','amr-users'),$source)); 
		else {
			foreach($amain as $j=>$setting) {
				if (is_array($setting)) { 
					if (WP_DEBUG) echo '<br />deleting '.$j.' from list '.$source;
					if (isset($amain[$j][$source]) ) 
						unset ($amain[$j][$source]);
				}
			}
		}

		
		$amain['no-lists'] = count($amain['names']);
		if (!empty($aopt['list'][$source]) ) { 
			
			unset($aopt['list'][$source]);
			
		}
		$acache = new adb_cache();
		$acache->clear_cache ($acache->reportid($source) );
		ausers_update_option ('amr-users-main', $amain);
		ausers_update_option ('amr-users', $aopt); 
		amr_users_message(__('List and the cache deleted.','amr-users'));
	}
	

}	
/* ----------------------------------------------------------------------------------- */
function amrmeta_validate_overview()	{ 
	global $amain;
	global $aopt;

	if (isset($_REQUEST['addnew'])) {  		
		if ((count ($amain['names'])) < 1)
			$amain['names'][1] = __('New list');
		else 
			$amain['names'][] = __('New list');
		$amain['no-lists'] = count ($amain['names']);
	}		

	if (isset($_POST['name'])) {
		$return = amrmeta_validate_names();
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}

	
	if (isset($_POST['checkedpublic'])) { /* admin has seen the message and navigated to the settings screen and saved */
		$amain['checkedpublic'] = true;
	}
//	unset($amain['public']);	
//	unset($amain['sortable']);
//	unset($amain['customnav']);



	//
	if (isset($_POST['list_avatar_size'])) {	
		if (is_array($_POST['list_avatar_size']))  {
			foreach ($_POST['list_avatar_size'] as $i=>$value) 
				$amain['list_avatar_size'][$i] = ( int) $value;
		}
	}
	if (isset($_POST['list_rows_per_page'])) {	
		if (is_array($_POST['list_rows_per_page']))  {
			foreach ($_POST['list_rows_per_page'] as $i=>$value) 
				$amain['list_rows_per_page'][$i] = ( int) $value;
		}
	}
	
	if (isset($_POST['html_type'])) {	
		if (is_array($_POST['html_type']))  {
			foreach ($_POST['html_type'] as $i=>$value) {
				if (in_array( $value, array('table','simple'))) {
					$amain['html_type'][$i] =  $value;					
				}	
			}
		}
	}
	//

	if (isset($_POST['filter_html_type'])) {	
		if (is_array($_POST['filter_html_type']))  {
			foreach ($_POST['filter_html_type'] as $i=>$value) {
				if (in_array( $value, array('intableheader','above','none'))) {
					$amain['filter_html_type'][$i] =  $value;					
				}	
			}
		}
	}
//	
	foreach ($amain['names'] as $i=>$n) { // clear booleans in case not set
		if ((!isset($_REQUEST['ulist'])) or ($_REQUEST['ulist'] == $i)) { // in case we are only doing 1 list - insingle view
			$amain['show_search'][$i] = false;
			$amain['show_perpage'][$i] = false;
			$amain['show_pagination'][$i] = false;
			$amain['show_headings'][$i] = false;
			$amain['show_csv'][$i] = false;
			$amain['show_refresh'][$i] = false;
			$amain['public'][$i] = false;
			$amain['customnav'][$i] = false;
			$amain['sortable'][$i] = false;
		}
	}	
	if (isset($_POST['sortable'])) {	
		if (is_array($_POST['sortable']))  {
			foreach ($_POST['sortable'] as $i=>$y) 
				$amain['sortable'][$i] = true;
		}
	}
	if (isset($_POST['public'])) {	
		if (is_array($_POST['public']))  {
			foreach ($_POST['public'] as $i=>$y) 
				$amain['public'][$i] = true;
		}

	}
	amr_users_clear_all_public_csv ($amain['public']);
	amr_users_message(__('Csv lists privacy check done.  Any no longer public lists deleted. ','amr-users'));

	
	if (isset($_POST['show_search'])) {	
		if (is_array($_POST['show_search']))  {
			foreach ($_POST['show_search'] as $i=>$y) 
				$amain['show_search'][$i] = true;
		}
	}
	if (isset($_POST['customnav'])) {	
		if (is_array($_POST['customnav']))  {
			foreach ($_POST['customnav'] as $i=>$y) 
				$amain['customnav'][$i] = true;
		}
	}
	if (isset($_POST['show_perpage'])) {	
		if (is_array($_REQUEST['show_perpage']))  {
			foreach ($_REQUEST['show_perpage'] as $i=>$y) 
				$amain['show_perpage'][$i] = true;
		}
	}
	if (isset($_POST['show_pagination'])) {	
		if (is_array($_REQUEST['show_pagination']))  {
			foreach ($_REQUEST['show_pagination'] as $i=>$y) 
				$amain['show_pagination'][$i] = true;
		}
	}
	if (isset($_POST['show_headings'])) {	
		if (is_array($_REQUEST['show_headings']))  {
			foreach ($_REQUEST['show_headings'] as $i=>$y) 
				$amain['show_headings'][$i] = true;
		}
	}
	if (isset($_POST['show_csv'])) {	
		if (is_array($_REQUEST['show_csv']))  {
			foreach ($_REQUEST['show_csv'] as $i=>$y) 
				$amain['show_csv'][$i] = true;
		}
	}
	if (isset($_POST['show_refresh'])) {	
		if (is_array($_REQUEST['show_refresh']))  {
			foreach ($_REQUEST['show_refresh'] as $i=>$y) 
				$amain['show_refresh'][$i] = true;
		}
	}
	
	$amain['version'] = AUSERS_VERSION;
	
	if (isset($_POST)) {	
		ausers_update_option ('amr-users-main', $amain);
		//ausers_update_option ('amr-users', $aopt);
	}
	
	amr_users_message(__('Options Updated', 'amr-users'));	
		
	return;
}
    /* -------------------------------------------------------------------------------------------------------------*/
function amr_meta_overview_onelist_headings() { 

	if (function_exists('amr_offer_filtering'))
	$greyedout = '';
	else
	$greyedout = ' style="color: #AAAAAA;" ';
	
	echo '<table class="widefat"><thead>';
	/*	<tr><th>&nbsp;</th><th colspan="6" style="text-align:center;">'.__('Show ?').'</th><th colspan="">&nbsp;</th></tr> */
	echo 	'<tr>';
			echo '<th>';
			_e('No.', 'amr-users'); 
			echo '</th>';
			echo '<th>';
			_e('Name of List', 'amr-users'); 
			echo '</th>';
			
	//if (!is_network_admin()) {		// some users want to be able to make network listings public			
			echo '<th class="show">';
			_e('Public', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('List may be viewed in public pages', 'amr-users'); 
			echo '">?</a></th>';			
			
			echo '<th>';
			_e(' Public Html Type', 'amr-users'); 
			echo '</th>';	
	//}		
			echo '<th>';
			_e('Rows per page', 'amr-users'); 
			echo '</th>';
			echo '<th>';
			_e('Avatar size', 'amr-users'); 
			echo '<a class="tooltip" title="gravatar size info" href="http://en.gravatar.com/site/implement/images/">?</a>';
			echo '</th>';			
			echo'<th class="show">';
			_e('Search', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show user search form.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Per page', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show per page option.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Pagination', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show pagination, else just show top n results.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Headings', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show column headings.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Csv link', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show a link to csv export file', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Refresh', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('If list is public, show a link to refresh the cache', 'amr-users'); 
			echo '">?</a></th>';
			

			echo '<th class="show">';
			_e('Sortable', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('Offer sorting of the cached list by clicking on the columns.', 'amr-users'); 
			echo '">?</a></th>';

			echo '<th class="show" '.$greyedout.'>';
			_e('Custom navigation', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('Show custom navigation to find users. ', 'amr-users'); 
			_e('Requires the amr-users-plus addon.', 'amr-users'); 
			echo '">?</a></th>';			
						
			echo '<th class="show" '.$greyedout.'>';
			_e('Filtering Location', 'amr-users'); 
			echo ' <a class="tooltip" href="#" title="';
			_e('Show filtering. ', 'amr-users'); 
			_e('Requires the amr-users-plus addon.', 'amr-users'); 
			echo '">?</a></th>';


			
}
/* ---------------------------------------------------------------------*/	
function amr_meta_overview_onelist_headings_middle() { 
	echo '</tr></thead><tbody>';
}
/* ---------------------------------------------------------------------*/	
function amr_meta_overview_onelist_headings_end() { 
	echo '</tbody></table>';
}
/* ---------------------------------------------------------------------*/	
function amr_meta_overview_onelist_settings($i) { /* the main setting spage  - num of lists and names of lists */
	global $amain, $aopt;
	
	$status= '';
	
	if (function_exists('amr_offer_filtering')) {
		$greyedout = '';
		$plusstatus = '';			
	}	
	else {
		$greyedout = ' style="color: #AAAAAA;" ';
		$plusstatus = ' disabled="disabled"';
	}
	
	
	echo '<tr>';
	echo '<td>';
	echo $i;
	echo '</td>';
	
	echo '<td><input type="text" size="45" id="name'
	.$i.'" name="name['. $i.']"  value="'.$amain['names'][$i].'" />';
	echo '<br />';
	
	if ($_REQUEST['page'] == 'ameta-admin-general.php') { 
		echo au_configure_link(__('Configure','amr-users'),$i,$amain['names'][$i]);
		echo ' |'.au_copy_link('&nbsp;&nbsp;'.__('Copy','amr-users'),$i,$amain['names'][$i]);
	}
	else {
		echo au_buildcache_link('&nbsp;&nbsp;'.__('Rebuild','amr-users'),$i,$amain['names'][$i]);
	}
	
	echo ' |'.au_delete_link('&nbsp;&nbsp;'.__('Delete','amr-users'),$i,$amain['names'][$i])
		.' |'.au_view_link('&nbsp;&nbsp;'.__('View','amr-users'),$i,$amain['names'][$i]);
		
	if (!is_network_admin()) {
		echo ' |'.au_add_userlist_page('&nbsp;&nbsp;'.__('Add page'), $i,$amain['names'][$i]);	
		echo '</td>';	
	}	
	
	echo '<td align="center">';
	echo '<input type="checkbox" id="public'
		.$i.'" name="public['. $i .']" value="1" ';


	if (!empty($amain['public'][$i])) {
			echo 'checked="checked" />';
			$status = '';	
			}
	
	echo '</td>';
	
	
	echo '<td align="left">';
	if (empty($amain['html_type'][$i])) 
		$amain['html_type'][$i] = 'table';
	foreach (array('table','simple') as $type) {
		echo '<input type="radio" id="html_type'.$i.'" name="html_type['. $i .']" value="'.$type.'" ';
		if (($amain['html_type'][$i]) == $type) echo 'checked="Checked"'; 
		echo '/>';
		_e($type);
		echo '<br />';
	}
	echo '</td>';
	
	//}
	if (empty($amain['list_rows_per_page'][$i])) 
			$amain['list_rows_per_page'][$i] = $amain['rows_per_page'];
	echo '<td><input type="text" size="3" id="rows_per_page'
	.$i.'" name="list_rows_per_page['. $i.']"  value="'.$amain['list_rows_per_page'][$i].'" /></td>';	

	if (empty($amain['avatar_size'])) $amain['avatar_size'] = 10;
	if (empty($amain['list_avatar_size'][$i])) 
			$amain['list_avatar_size'][$i] = $amain['avatar_size'];	
	echo '<td><input type="text" size="3" id="avatar_size'
	.$i.'" name="list_avatar_size['. $i.']"  value="'.$amain['list_avatar_size'][$i].'" /></td>';
	
//	
		echo '<td align="center"><input type="checkbox" id="show_search'
			.$i.'" name="show_search['. $i .']" value="1" '.$status;
		if (!empty($amain['show_search'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
//
		echo '<td align="center"><input type="checkbox" id="show_perpage'
			.$i.'" name="show_perpage['. $i .']" value="1" '.$status;
		if (!empty($amain['show_perpage'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
//
		echo '<td align="center"><input type="checkbox" id="show_pagination'
			.$i.'" name="show_pagination['. $i .']" value="1" '.$status;
		if (!empty($amain['show_pagination'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
		//
		echo '<td align="center"><input type="checkbox" id="show_headings'
			.$i.'" name="show_headings['. $i .']" value="1" '.$status;
		if (!empty($amain['show_headings'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
		//
		echo '<td align="center"><input type="checkbox" id="show_csv'
			.$i.'" name="show_csv['. $i .']" value="1" '.$status;
		if (!empty($amain['show_csv'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
							//
		echo '<td align="center"><input type="checkbox" id="show_refresh'
			.$i.'" name="show_refresh['. $i .']" value="1" '.$status;
		if (!empty($amain['show_refresh'][$i])) echo 'checked="Checked"'; 
		echo '/></td>';
			

	
//			
	echo '<td align="center">
		<input type="checkbox" id="sortable'.$i.'" name="sortable['.$i.']"  ';
	echo '	value="1" ';
	if (!empty($amain['sortable'][$i])) echo 'checked="Checked"'; 
	echo '/></td>';
	
	//	
	echo '<td align="center">
		<input type="checkbox" id="customnav'.$i.'" name="customnav['.$i.']"  '	.$plusstatus;
	echo '	value="1" ';
	if (!empty($amain['customnav'][$i])) echo 'checked="Checked"'; 
	echo '/></td>';	

	
	echo '<td align="left">';
	if (empty($amain['filter_html_type'][$i])) 
		$amain['filter_html_type'][$i] = 'none';	
	foreach (array(
			'intableheader' => __('in table','amr-users'),
			'above' 		=> __('above','amr-users'), 
			'none' 			=> __('none','amr-users')) as $val => $type) {
			echo '<input type="radio" id="filter_html_type'.$i.'" name="filter_html_type['. $i .']" value="'.$val.'" '
			.$plusstatus ;
			if (($amain['filter_html_type'][$i]) == $val) echo 'checked="Checked"'; 
			echo '/>';
			echo $type;
			echo '<br />';
		}
	echo '</td>';	

		
	

}
/* ---------------------------------------------------------------------*/	
function amr_meta_overview_page() { /* the main setting spage  - num of lists and names of lists */
	global $amain;
	global $aopt;
	
	if (empty($amain)) $amain = ausers_get_option('amr-users-main');
	
	//amr_meta_main_admin_header('Overview of configured user lists'.' '.AUSERS_VERSION);
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc
	
	if ( isset( $_POST['import-list'] )) {
			amr_meta_handle_import();		
	}	
	elseif (isset ($_POST['action']) and  ($_POST['action'] == "save")) { 
		if (!empty($_POST['reset'])) {
			amr_meta_reset();
			return;
		}
		elseif ( isset( $_POST['export-list'] )  ) {
			amr_meta_handle_export();
		}

		else
			amrmeta_validate_overview();
	}

	else amr_handle_copy_delete();

	if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
			echo '<h2>'.__('Problem creating DB tables','amr-users').'</h2>';

	if (!(isset ($amain['checkedpublic']))) {
		echo '<input type="hidden" name="checkedpublic" value="true"/>';
	}

	echo PHP_EOL.'<div class="wrap"><!-- one wrap -->'.PHP_EOL;

	
	if (!isset ($amain['names'])) { 
		echo '<h2>'
		.__('There is a problem - Some overview list settings got lost somehow.  Try reset options.','amr-users')
		. '</h2>';
	}
	else {
			amr_meta_overview_onelist_headings();
			amr_meta_overview_onelist_headings_middle();
			
			foreach ($amain['names'] as $i => $name) {
			//for ($i = 1; $i <= $amain['no-lists']; $i++)	{
				amr_meta_overview_onelist_settings($i);
				echo '</tr>';
			}
			amr_meta_overview_onelist_headings_end();	

	};
		
	echo '</div><!-- end of one wrap --> <br />'.PHP_EOL;
			
	//echo '<div style="clear: both; float:right; padding-right:100px;" class="submit">';
	echo ausers_submit();
	echo '<input class="button-primary" type="submit" name="addnew" value="'. __('Add new', 'amr-users') .'" />';

	amr_list_export_form();

	echo ausers_form_end();	
	
	amr_list_import_form();	// different form

}						
/* ---------------------------------------------------------------------*/		
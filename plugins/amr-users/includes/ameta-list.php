<?php
include ('amr-users-csv.php');

function get_commentnumbers_by_author(  ) {
     global $wpdb;
	 /*** Rudimentary - if going to be used frequently (eg outside of admin area , then could do with optimistaion / cacheing */

	$approved = "comment_approved = '1'";
	$c = array();
	$comments = $wpdb->get_results(
	"SELECT user_id, comment_author_email, count(1) as \"comment_count\" FROM $wpdb->comments WHERE $approved AND user_id > 0 GROUP BY user_id, comment_author_email;" );
	foreach ($comments as $i => $v) {
		$c[$v->user_id] = $v->comment_count;
	}
	unset ($comments);
    return $c;

}
/* -----------------------------------------------------------------------------------*/
function amr_rows_per_page($rpp){  //check if rows_per_page were requested or changed, set default if nothing passed
	if (!empty($_REQUEST['rows_per_page'])) {

		return ((int) ($_REQUEST['rows_per_page']));
	}
	else {
		if (!empty($rpp)) 
			return($rpp);
		else return(50);
	}
}
/* -----------------------------------------------------------------------------------*/
function amr_count_user_posts($userid, $post_type) {  // wordpress function does not allow for custom post types
    global $wpdb;
	if (!post_type_exists( $post_type )) 
		return (false);
    $where = get_posts_by_author_sql($post_type, true, $userid);

    $count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

    return apply_filters('get_usernumposts', $count, $userid);
	}
/* -----------------------------------------------------------------------------------*/
function amr_allow_count () { //used to allow the counting function to cost posts
	return ('read_private_posts'); //will allows us to count the taxonmies then
}
/* -----------------------------------------------------------------------------------*/
function amr_need_the_field($ulist,$field) {
global $aopt;
	$l = $aopt['list'][$ulist]; /* *get the config */

	if ((isset ($l['selected'][$field])) or
	   (isset ($l['included'][$field])) or
	   (isset ($l['excluded'][$field])) or
	   (isset ($l['includeonlyifblank'][$field])) or
	   (isset ($l['excludeifblank'][$field])) or
	   (isset ($l['sortby'][$field])) or 
	   ($field == 'ID') or // always need the id
	   ($field == 'index') or
	   (isset ($l['grouping'][1]) and ($l['grouping'][1] == $field)) 
	)
	{		
		return  true;
	}
	else
	return false;
}
/* -----------------------------------------------------------------------------------*/
function amr_rptid ($ulist) {
	if ($ulist < 10) $rptid = 'user-0'.$ulist;
	else $rptid = 'user-'.$ulist;
	return $rptid;
}
//-----------------------------------------------------------------------------------------------
function amr_check_use_transient ($ulist, $options) {
global $post;

	if (is_admin() ) return false;
	if (!amr_first_showing ())  // can only  use transient if no dynamic filters, no search, no sort, nothing special happening
		return false;

	if (!empty($post) ) {//we are in a page, BUT might be widget
		if (!empty($options['widget'])) // not in our widget anyway
			return ($ulist); // just use the ulist for the transient
		else 
			return ($ulist.'-'.$post->ID);  // since we can now pass shortcode parameters, the html can differ per page for same list
	}		
	return ($ulist);
}
//-----------------------------------------------------------------------------------------------
function amr_first_showing() { // use GET not REQUEST to be sure it is dynamic input
	if ((!isset($_REQUEST['filter'])) and
		(!isset($_REQUEST['su'])) and
		(!isset($_REQUEST['clear_filtering'])) and
		(!isset($_REQUEST['listpage'])) and
		(!isset($_REQUEST['rows_per_page'])) and
		(!isset($_REQUEST['refresh'] ))and
		(!isset($_REQUEST['dir'])) and
		(!isset($_REQUEST['sort'])) 

	)
	return true;  // ie then we can try using the transient
	else {
		return false;
	}
}
/* ------------------------------------------------------------------*/
function amr_undo_csv (&$value) {  // for front end display
// for historical reasons, cached data stored in csv format
// until this is revised, we need to uncsv some values
// eg those with double double quotes
	$value = str_replace('""', '"', $value);
}
/* ------------------------------------------------------------------*/
function amr_get_lines_to_array (
	$c, 
	$rptid, 
	$start, 
	$rows, 
	$icols, /* the controlling array */
	$shuffle=false
	) {
global $amr_search_result_count;

	if (!empty($_REQUEST['su'])) {		// check for search request
		$s = filter_var ($_REQUEST['su'], FILTER_SANITIZE_STRING );
		$lines = $c->search_cache_report_lines ($rptid, $rows, $s, $shuffle);
		$amr_search_result_count = count($lines);
	}
	else { 
		$lines = $c->get_cache_report_lines ($rptid, $start, $rows, $shuffle );
	}

	if (!($lines>0)) {amr_flag_error($c->get_error('norecords'));	return (false);	}
	foreach ($lines as $il =>$l) {
		if (!defined('str_getcsv')) {
	
			$lineitems = amr_str_getcsv( ($l['csvcontent']), '","','"','\\'); /* break the line into the cells */
			
		}	
		else {
			$lineitems = str_getcsv( $l['csvcontent'], ',','"','\\'); /* break the line into the cells */
		}
		array_walk ($lineitems,'amr_undo_csv');
		
		$linehtml = '';
		
		$linessaved[$il] = amr_convert_indices ($lineitems, $icols);
		
	}
	unset($lines);	
	
	return ($linessaved);
}
/* ------------------------------------------------------------------*/
function amr_convert_indices ($lineitems, $icols) {

		foreach ($icols as $ic => $c) { /* use the icols as our controlling array, so that we have the internal field names */
		
			if (isset($lineitems[$ic])) {
				$w = $lineitems[$ic];
			}
			else $w = '';
			$line[$c] = stripslashes($w);
		}
		return ($line);

}
/* ------------------------------------------------------------------*/
function amr_check_for_sort_request ($list, $cols=null) {
/* check for any sort request and then sort our cache by those requests */
	$dir=SORT_ASC;
	if ((!empty($_REQUEST['dir'])) and ($_REQUEST['dir'] === 'SORT_DESC' ))  
		$dir=SORT_DESC;
	//20111214
	if (!empty($_REQUEST['lastsort'])) { 
		$lastsort = esc_attr($_REQUEST['lastsort']); 
	}
	else 
		$lastsort = 'ID';
	if (!empty($_REQUEST['lastdir'])) { 
		$lastdir = esc_attr($_REQUEST['lastdir']); 
	}
	else 
		$lastdir = SORT_ASC;
	//..20111214
	if (!empty($_REQUEST['sort'])) {
		//$cols = array($_REQUEST['sort'] => array($dir), 'ID' => array($dir) );   20111214
		$cols = array($_REQUEST['sort'] => $dir, $lastsort => $lastdir );
		$list = auser_multisort($list, $cols );
		return($list);
	}
	else return($list);
}
/* ------------------------------------------------------------------*/
function alist_one_widget ($type='user', $i=1, $do_headings=false, $do_csv=false, $max=10){
/* a widget version of alist one*/
	/* Get the fields to use for the chosen list type */
global $aopt;
global $amain;

	$c = new adb_cache();
	$rptid = $c->reportid($i, $type);

		$line = $c->get_cache_report_lines ($rptid, '0', '2'); /* get the internal heading names  for internal plugin use only */  /* get the user defined heading names */

		if (!defined('str_getcsv')) 
			$icols = amr_str_getcsv( $line[0]['csvcontent'], ',','"','\\');
		else 
			$icols = str_getcsv( $line[0]['csvcontent'], ',','"','\\');
//		if (!defined('str_getcsv')) $cols = amr_str_getcsv( $line[1]['csvcontent'], '","','"','\\');
//		else $cols = str_getcsv( $line[1]['csvcontent'], ',','"','\\');

		foreach ($icols as $ic => $cv) { /* use the icols as our controlling array, so that we have the internal field names */
				$v = $cols[$ic];

				$html .= '<th>'.$v.'</th>';
			}
		$hhtml = '<thead><tr>'.$html.'</tr></thead>'; /* setup the html for the table headings */
		$fhtml = '<tfoot><tr>'.$html.'</tr>'

		.'</tfoot>'; /* setup the html for the table headings */

		$html='';
		$totalitems = $c->get_cache_totallines($rptid);
		$lines = $c->get_cache_report_lines ($rptid, $start+1, $max );


		if (!($lines>0)) {
			amr_flag_error($c->get_error('numoflists'));
			return (false);
		}
		foreach ($lines as $il =>$l) {

			$id = $lineitems[0]; /*  *** pop the first one - this should always be the id */

			$user = amr_get_userdata($id);
			unset($linehtml);
			foreach ($icols as $ic => $c) { /* use the icols as our controlling array, so that we have the internal field names */

			
				$v = $lineitems[$ic];
				$linehtml .= '<td>'.amr_format_user_cell($c, $v, $user, $l). '</td>';
			}
			$html .=  PHP_EOL.'<tr>'.$linehtml.'</tr>';
		}

		$html = '<table>'.$hhtml.$fhtml.'<tbody>'.$html.'</tbody></table>';

	return ($html);
}
/* ------------------------------------------------------------------*/
function amr_list_user_meta(){   /* Echos out the paginated version of the requested list */
global $aopt;
global $amain;
global $amr_nicenames;
global $thiscache;


	if (isset($_POST['info_update']) or amr_is_bulk_request ('ym_update')) {
		amr_ym_bulk_update();
		return;
	}
	ameta_options();
	if (!isset ($aopt['list'])) {
		_e ("No lists Defined", 'amr-users');
		return false;
		}
	if (isset ($_REQUEST['ulist'])) {
		$l = (int) $_REQUEST['ulist'];
	}
	else {
		if (isset($_REQUEST['page']))  { /*  somehow needs to be ? instead of & in wordpress admin, so we don't get as separate  */
			$param = 'ulist=';
			$l = substr (stristr( $_REQUEST['page'], $param), strlen($param));
			}
		else {
			//echo '<br />what is happening ?';
			//var_dump($_REQUEST);
			}
	}
	if ($l < 1) $l = 1;	/* just do the first list */
	//if (WP_DEBUG) echo '<br /> List requested  ='.$l;
	
	if (isset ($_REQUEST['csv'])) { 
		amr_meta_handle_export_request ();		
		return;
	}
	
	$thiscache = new adb_cache();  // nlr?

	amr_list_user_admin_headings($l);	// will only do if in_admin

	echo ausers_form_start();
	
	if (empty($_REQUEST['filtering']) and (empty($_REQUEST['headings'])) and empty ($_REQUEST['csvsubset'])) 
		ausers_bulk_actions();	// will check capabilities

	echo alist_one('user',$l, array());  /* list the user list with the explanatory headings */

	if (empty($_REQUEST['filtering']) and (empty($_REQUEST['headings'])) and empty ($_REQUEST['csvsubset']))  
		ausers_bulk_actions(); // will check capabilities
	
	if (function_exists('amr_ym_bulk_update_form') and amr_is_ym_in_list ($l)) // only show form if we have a ym field
		amr_ym_bulk_update_form();
		
	echo ausers_form_end();

	return;
}
/* -----------------------------------------------------------------------------------*/
function amr_try_build_cache_now ($c, $i, $rptid) { // the cache object, the report id, the list number
global $amain;
		if ($c->cache_in_progress($rptid)) {
			echo ( '<div style="clear:both;"><strong>'.$amain['names'][$i].' ('.$rptid.') '.$c->get_error('inprogress').'</strong></div>');
			return (false);
		}
		else {

				return amr_build_user_data_maybe_cache($i);

			return true;
		}
}

/* -----------------------------------------------------------------------------------*/
function alist_one($type='user', $ulist=1 ,$options) {

//options  can be headings, csv, show_search, show_perpage
	/* Get the fields to use for the chosen list type */
global $aopt,
	$amain,
	$amrusers_fieldfiltering,
	$amr_current_list,
	$amr_search_result_count;
global $amr_refreshed_heading; 	

	if (empty ($aopt['list'][$ulist])) {
		printf(__('No such list: %s','amr-users'),$ulist); 
		$ulist = amr_debug_no_such_list ();   // issue debuf messages and use first list found 
	}
	else $l = $aopt['list'][$ulist]; /* *get the config */
	
	do_action('amr-add-criteria-to-list', $ulist);   
	// allows one to force criteria into the request field for example (eg: show only logged in user)
	
	$transient_suffix = amr_check_use_transient ($ulist, $options) ;
	if ($transient_suffix) { // no filters, no search, no sort, nothing special happening
		//if (WP_DEBUG) echo '<br />using transient: '.$transient_suffix.'<br />';
		$html = get_transient('amr-users-html-for-list-'.$transient_suffix);
		if (!empty($html)) {
			if (current_user_can('administrator')) {
				echo '<br /><a href="'.add_query_arg('refresh','1').'" title="'
				.__('Note to logged in admin only: Now using temporary saved html (transient) for frontend.  Click to refresh.','amr-users').'">!</a>';
			}
			return( $html);
		}	
	}
	$caption 	= '';
	$sortedbynow = '';
	
	if (empty($amain['list_rows_per_page'][$ulist]))  
		$amain['list_rows_per_page'][$ulist] = $amain['rows_per_page'];
		
	$rowsperpage = amr_rows_per_page($amain['list_rows_per_page'][$ulist]); // will check request

//  use $options as our 'request' input so shortcode parameters will work.
// allow _REQUEST to override $options

	/*$request_override_allowed = array(
		'filter',
		'fieldvaluefilter',
		'fieldnamefilter',
		'sort'); */

	
// figure out what we are doing - searching, filtering -------------------------------------------------------

	$search = '';	
	
	if (!empty($options['su']))
		$search = strip_tags ($options['su']);
	elseif (isset($_REQUEST['clear_filtering'])) { 	// we do not need these then
		unset($_REQUEST['fieldnamefilter']);
		unset($_REQUEST['fieldvaluefilter']);
		unset($_REQUEST['filter']);
		//do we need to unset the individual cols? maybe not
	}
	else {
	
		foreach ($_REQUEST as $param => $value) { // we do not know the column names, so just transfer all?
		// some might be an array
			if (is_array($value)) {
				foreach ($value as $i => $val) {
				$options[$param][$i] = sanitize_text_field($val);
				}
			}	
			else $options[$param] = sanitize_text_field($value);
		}	
	}	
	
	$amrusers_fieldfiltering = false;
	if (!empty($options['filter'])) { 
		//if (WP_DEBUG) {echo '<h1>Filtering</h1>';}
		foreach (array('fieldnamefilter', 'fieldvaluefilter') as $i=> $filtertype) {
			
			if (isset($options[$filtertype])) { 
			// if (WP_DEBUG) {echo '<br />doing: '.$filtertype; var_dump($options);}
				foreach ($options[$filtertype] as $i => $col) {
					if (empty($options[$col])) {//ie showing all
						unset($options[$filtertype][$i]);
						unset($options[$col]);
					}
					else $amrusers_fieldfiltering = true;  // set as we are maybe doing realtime filtering flag
				};
			}
		}	
	}
	
	$c = new adb_cache();
	$rptid = $c->reportid($ulist, $type);

	if ($amrusers_fieldfiltering) {
		$lines = amr_build_user_data_maybe_cache($ulist); // since we are filtering, we will run realtime, but not save, else we would lose the normal report
	
		if (empty($lines)) return;
		$totalitems = count($lines);
		//if (WP_DEBUG) echo '<br /> field filtering & $totalitems='.$totalitems;
	}
	else { 
		if ((!($c->cache_exists($rptid))) or (isset($options['refresh']))) {
			//if (amr_debug()) _e('If debug only: Either refresh requested OR no cache exists.  A rebuild will be initiated .... ','amr-users');
			$success = amr_try_build_cache_now ($c, $ulist, $rptid) ;
			//$lines = amr_build_user_data_maybe_cache($ulist);  
			$totalitems = $c->get_cache_totallines($rptid);
			//now need the lines, but first, paging check will tell us how many
			$amrusers_fieldfiltering = false; // already done if it must be
		}
		else {
			$totalitems = $c->get_cache_totallines($rptid);
			
		}
	}
	
	//---------- setup paging variables
	if ($totalitems < 1) {
			_e('No lines found.','amr-users');
			echo amr_users_get_refresh_link($ulist);
			return;
	}
	if ($rowsperpage > $totalitems)
		$rowsperpage  = $totalitems;

	$lastpage = ceil($totalitems / $rowsperpage);
	
	if (!empty ($_REQUEST['listpage'])) // if we requested a page MUST use that
		$page = (int) $_REQUEST['listpage'];	
	else { // is a random page stipulated ?
		if (isset($options['show_randompage'])) { // allows a random page
			$page = rand (1, $lastpage);
		}
		else {// else.....start at the very beginning, a very good place to start...
			$page=1;
		}
	}		
	if ($page > $lastpage) 
		$page = $lastpage;
	if ($page == 1)
		$start = 1;
	else
		$start = 1 + (($page - 1) * $rowsperpage);
	
	$shuffle = false;
	if (!empty($options['shuffle'])) {
		$shuffle = true;
	}
	$filtercol = array();
	
//------------------------------------------------------------------------------------------		get the data
		if (!$amrusers_fieldfiltering) { // because already have lines if were doing field level filtering	
			$headinglines = $c->get_cache_report_lines ($rptid, 0, 2); /* get the internal heading names  for internal plugin use only */  /* get the user defined heading names */

			if (!defined('str_getcsv'))
				$icols = amr_str_getcsv( ($headinglines[0]['csvcontent']), ',','"','\\');
			else
				$icols = str_getcsv( $headinglines[0]['csvcontent'], ',','"','\\');

			$icols = array_unique($icols);	//since may end up with two indices, eg if filtering and grouping by same value	
				
			if (!defined('str_getcsv'))
				$cols = amr_str_getcsv( $headinglines[1]['csvcontent'], '","','"','\\');
			else
				$cols = str_getcsv( $headinglines[1]['csvcontent'], ',','"','\\');


			if (isset($options['filter']) or !empty($options['sort']) or (!empty($options['su']))) {
				$lines = amr_get_lines_to_array (
					$c, 
					$rptid, 
					2, 
					$totalitems+1 , 
					$icols /* the controlling array */, 
					$shuffle); 	
				
			}
			else {
				if (isset($options['start_empty'])) {
					$lines = array();
					$totalitems = 0;
				}
				else	
					$lines = amr_get_lines_to_array(
						$c, 
						$rptid, 
						$start+1, 
						$rowsperpage, 
						$icols,
						$shuffle );
				
			}

		}
		else {  // we are field filtering
			unset ($lines[0]); // the tech lines and the headings line
			unset ($lines[1]);
			
			$totalitems = count($lines); // must be here, only reset for field filtering
			$s = $l['selected'];
			asort ($s); /* get the selected fields in the display  order requested */
			$cols 	= amr_build_col_headings($s);
			$icols 	= amr_build_cols ($s);

			foreach ($lines as $i => $j) {
				$lines[$i] = amr_convert_indices ($j, $icols);
			}
		}
		
//------------------------------------------------------------------------------------------		display time filter check
		if (isset($options['filter']) or isset ($options['csvsubset'])) {
		// then we are filtering
			//if (amr_debug()) {
			//	var_dump($options['filter']);
			//	echo '<br />Check for filtering at display time <br />'; var_dump($icols);
			//	}

			foreach ($icols as $cindex => $col) {
				if (!empty ($options[$col]) ) { 
					if ((!(isset ($options['fieldnamefilter']) and in_array($col, $options['fieldnamefilter']))) and
					   (!(isset ($options['fieldvaluefilter']) and in_array($col, $options['fieldvaluefilter'])))) {
					
						$filtercol[$col] = esc_attr($options[$col]);

					}
				}
				
			}
			
			if (!empty($options['index'])) {
				$filtercol['index'] = strip_tags($options['index']);
			}
			if (false and !$amrusers_fieldfiltering and empty($filtercol) and current_user_can('manage_options')) {  
			//NO LONGER REQUIRED, keep for debug only helpful maybe message nlr or perhaps only if by url?  But could be trying own html? and be confused
				echo '<p>';
				_e('This Message shows to admin only!','amr-users');
				echo '<br />';
				_e('Filter requested.','amr-users');
				_e('Maybe you chose "show all", which is OKAY... or are attempting some own html or link ? .','amr-users');
				echo '<br />';
				_e('No valid filter column given.','amr-users');
				echo '<br />';	_e('Column filter Usage is :','amr-users');	
				echo '<br /><strong>';
				echo '?filter=hide&column_name=value<br />';
				echo '?filter=show&column_name=value</br> ';
				echo '?filter=1&column_name=value';  
				echo '</strong></br> ';
				_e('Note: Hide only works if the column is currently being displayed.' ,'amr-users');
				_e('For this list, expecting column_name to be one of ','amr-users');
				echo '<br />'.implode('<br />',$icols).'<br />';
				echo '</p>';
			}

			if (!empty($filtercol)) { // for each of the filter columns that are not field filters
				foreach ($filtercol as $fcol => $value) {
					
					//if (amr_debug()) {echo '<hr>Apply filters for field "'.$fcol. '" against... '.$value; }
					foreach ($lines as $i=> $line) {
						//if (WP_DEBUG) {echo '<br>line=';  var_dump($line);}
						if ($value === '*') {
							if (empty($line[$fcol]) ) unset ($lines[$i]);
							else {}
						}
						elseif ($value === '-') {
							if (!empty($line[$fcol]) ) 
								unset ($lines[$i]);
							else {}
						}
						elseif (empty($line[$fcol]) ) 	{
							unset ($lines[$i]);
						}
						else {
							if ($fcol == 'ID') { // id can have  filtering  - allows link to profile page 
								if (!($line[$fcol] == $value) ) {/// amr ??
									unset ($lines[$i]);
								}
							}
							else {
								//if (WP_DEBUG) echo '<br />Fliter: '.var_dump($value);
								$instring = strpos($line[$fcol],$value ); 
								// fuzzy filtering - hmm why - maybe not???
								// is it to avoid situation where value may have spaces before/after ???
								// used strstr before, but strpos faster
								if ($instring === false) { // note strpos may return 0 if in front of string
									unset ($lines[$i]);
								}
							}
						
						}
						//else if (!($line[$fcol] == $value)) {  strisstr will catch these ?
						//}

						if ((!empty ($options['filter']) and $options['filter'] == 'hide') ) {  
							unset($lines[$i][$fcol]);
						}
					} // if hiding, delete that column
					if (!empty ($options['filter']) and ($options['filter'] == 'hide') ) {
						foreach ($icols as $cindex=> $col) {
							
							if ($fcol == $col) {
								unset ($icols[$cindex]);
								unset ($cols[$cindex]);
							}
						}
					} // end delete col
					//if (WP_DEBUG) echo '<br />Lines left '.count($lines);
				}
//-----------------------------------------------------------------------------
				$amr_search_result_count = count($lines);
				
				$totalitems = $amr_search_result_count;
				// slice the right section of the returned values based on rowsperpage and currentpage
				// update the paging variables
				if (($amr_search_result_count > 0) and ($rowsperpage > $amr_search_result_count))
					$rowsperpage  = $amr_search_result_count;

				$lastpage = ceil($amr_search_result_count / $rowsperpage);
				if ($page > $lastpage)
					$page = $lastpage;
				if ($page == 1)
					$start = 1;
				else {
					$start = 1 + (($page - 1) * $rowsperpage);
						
					}
					
			}
			//echo '<br />count lines = '.$amr_search_result_count. ' '.$start. ' '. $rowsperpage;
						
			$lines = array_slice($lines, $start-1, $rowsperpage,true);	
		}  //end if

//------------------------------------------------------------------------------------------	 check for sort or search
		if (!empty($options['sort']) or (!empty($search))) {
		/* then we want to sort, so have to fetch ALL the lines first and THEN sort.  Keep page number in case workingthrough the list  ! */
		// if searching also want all the lines first so can search within and do pagination correctly

			if ($lines) { 
				$linesunsorted = amr_check_for_sort_request ($lines);
				$linesunsorted = array_values($linesunsorted); /* reindex as our indexing is stuffed and splice will not work properly */
				//if (!empty($search)) $totalitems = count($linesunsorted);	//save total here before splice
				$lines = array_splice($linesunsorted, $start-1, $rowsperpage );
				unset($linesunsorted); // free up memory?

				/* now fix the cache headings*/
				$sortedbynow = '';
				if (!empty($options['sort'])) {
					foreach ($icols as $i=>$t) {
						if ($t == $options['sort'])
							$sortedbynow = strip_tags($cols[$i]) ;
					}
					$sortedbynow = '<li><em>'
						.__('Sorted by:','amr-users').'</em>'.$sortedbynow.'</li><li class="sort">';
				}
				
			}
		}

	//maybe could do csv filter here ?

	If (!empty($_REQUEST['csvsubset'])) {
		$tofile = amr_is_tofile($ulist);
		$csvlines = amr_csvlines_to_csvbatch($lines);
		$html = amr_lines_to_csv($csvlines, $ulist, true, false,'csv','"',',',chr(13).chr(10), $tofile );
		//echo $html;
		return $html;
	}
	//---------------------------------------------------------------------------------------------finished filtering and sorting
		$html = amr_display_final_list (
			$lines, $icols, $cols,
			$page, $rowsperpage, $totalitems,
			$caption,
			$search, $ulist, $c, $filtercol,
			$sortedbynow, 
			$options);
		if ($transient_suffix) { // ie no filters, no search, no sort, nothing special happening
			$expiration = (empty($amain['transient_expiration']) ? 60 : $amain['transient_expiration']);	//allow setting later
			set_transient('amr-users-html-for-list-'.$transient_suffix, $html ,$expiration );
			track_progress('Transient set for html for list '.$transient_suffix);
		}
				
		return $html;
}
/* ----------------------------------------------------------------------------------- */
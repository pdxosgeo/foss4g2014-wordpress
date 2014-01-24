<?php 

if (!(defined('PHP_EOL'))) { /* for new lines in code, so we can switch off */
    define('PHP_EOL',"\n");
}
function amr_is_assoc($array) {
  return (bool)count(array_filter(array_keys($array), 'is_string'));
}

/* ---------------------------------------------------------------------*/	
  // Only validates empty or completely associative arrays
//function amr_is_assoc ($arr) {
//     return (is_array($arr) && count(array_filter(array_keys($arr),'is_string')) == count($arr));
//}
/* -----------------------------------------------------------------------------------*/
function amr_debug() {
	if (WP_DEBUG and is_user_logged_in()) {
		return true;
	}
	else return false;
}
/* -----------------------------------------------------------------------------------*/
function amr_debug_no_such_list () { // called when the list searched for is not found in db option.
global $aopt, $amain;

	if (empty($aopt)) {
		echo '<br />';
		_e('Error finding amr-user option in database','amr-users');
		return false;
	}
	else {
		if (empty($aopt['list'])) {
			echo '<br />';
			_e('amr-user option has no lists in database. Possible corruption.','amr-users');
			return false;
		}
		else {
			foreach ($aopt['list'] as $ulist => $details) {
				echo '<br />';
				printf(__('Found list number : %s','amr-users'),$ulist);
				if (empty ($firstlist)) $firstlist = $ulist;
				
			}
			echo '<br />';
			_e('Using first list found','amr-users');
			return $firstlist;
		}
	}

}
/* -----------------------------------------------------------*/
function amr_adjust_query_args () {  // cleanup and add to carry through as appropriate

	$base = remove_query_arg (array('refresh','listpage'));
	
	if (!empty($_REQUEST['filter'])) {
		unset($_POST['su']); unset($_REQUEST['su']); // do not do search and filter at same time.		
		
		$argstoadd = $_POST;
		foreach ($argstoadd as $i => $value) {
			if (empty($value)) unset($argstoadd[$i]);
		};
		//unset($argstoadd['fieldvaluefilter']);
		//unset ($argstoadd['refresh']);
		$base = add_query_arg($argstoadd, $base);
		//var_dump($base); 
	}	
	if (!empty($_REQUEST['su'])) {  
		$search = filter_var ($_REQUEST['su'], FILTER_SANITIZE_STRING );
		//$search = strip_tags ($_REQUEST['su']);
		$base = add_query_arg('su',$search ,$base);
	}
	if (!empty($_REQUEST['rows_per_page'])) {

		$base = add_query_arg('rows_per_page',(int) $_REQUEST['rows_per_page'],$base);  // int will force to a number
	}	
//	if (!empty($_SERVER['QUERY_STRING']) ) $format = '&listpage=%#%'; // ?page=%#% : %#% is replaced by the page number
//	else $format = '?listpage=%#%';
	return $base;
}
/* -----------------------------------------------------------------------------------*/
function amr_remove_grouping_field ($icols) {
global $aopt, $amr_current_list;
	if (!empty($aopt['list'][$amr_current_list]['grouping'])) {	
			$grouping_field = $aopt['list'][$amr_current_list]['grouping'][1];
	
		foreach ($icols as $i => $col) {
			if ($col == $grouping_field) {
				unset($icols[$i]);
			}
		}
	}
	return($icols);
}	
/* --------------------------------------------------------------------------------------------*/	
function amr_build_cols ($s) {  // get the technical column names, which could be combo fields
// we call this 3 times, explore whether can rationalise the calls
global $amain, $aopt, $amr_current_list;
	$iline = array();
	$iline[0] = 'ID';

	foreach ($s as $is => $cl) { // for each selected and sorted
		$colno = (int) $cl;
		if (!empty($iline[$colno])) { // then it's a combo
			$iline[$colno] .= $is; 
		}
		else $iline[$colno] = $is;
	}
	if (! empty($aopt['list'][$amr_current_list]['grouping'] )) {// if we are doing grouping
		$grouping_field = $aopt['list'][$amr_current_list]['grouping'][1];
		$iline [99998] = $grouping_field;
	}
	if (! empty($amain['customnav'][$amr_current_list] )) {// if we are doing custom navigation, need to record the index
		//if (!isset($aopt['list'][$amr_current_list]['selected']['index'])) {
			$iline[99999] = 'index';
		//}
	}	

	$iline = array_unique($iline);	
	
	return ($iline);
}		
/* ---------------------------------------------------------------------*/
function amr_build_col_headings ($s) {  // get the user column nice names, which could be combo fields	
global $aopt, $amain, $amr_current_list, $amr_nicenames;	
	$line = array();
	$line[0] = 'ID'; // must be first

	foreach ($s as $is => $cl) { // for each selected and sorted		
		$colno = (int) $cl;
		$value = agetnice($is); 
		if (!empty($line[$colno])) {
			$line[$colno] = $line[$colno].'&nbsp;'.$value; 						
			}
		else $line[$colno] = $value;
	}
	
	return ($line);
}
/* ---------------------------------------------------------------------*/
function amr_users_can_edit ($type) {
		if (is_admin() and isset($_GET[$type])
		and (current_user_can('manage_options') or current_user_can('manage_userlists') ) )
		return true;
		else return false;
}
/* ----------------------------------------------------------------------------------- */
function ausers_form_end() {
	$html = PHP_EOL.'</form><!-- end amr users form -->';
	$html .= PHP_EOL.'</div><!-- end amr users form wrap -->';
	return ($html);
}
/* ----------------------------------------------------------------------------------- */
function ausers_form_start() {
global $amain;
	if (isset($_REQUEST['clear_filtering']) or !empty($_REQUEST['su'])) 
		$base = get_permalink();
	else  
		$base = remove_query_arg(array('refresh', 'listpage', 'rows_per_page','filter','su', 'fieldvaluefilter','index'));
	
	if (!empty($_REQUEST['rows_per_page'])) { 

		if (!($_REQUEST['rows_per_page'] == $amain['rows_per_page']) )
			$base = add_query_arg('rows_per_page',(int) $_REQUEST['rows_per_page'],$base);
	}
	$html = PHP_EOL.'<div class="wrap"><!-- form wrap -->'.PHP_EOL;
	$html .= PHP_EOL.'<form id="userlist" action="'.$base.'" method="post">';
	$html .= PHP_EOL.'<input type="hidden" name="action" value="save" />';
	$html .= PHP_EOL.wp_nonce_field('amr-meta','amr-meta',true,false);
	return ($html);

}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_convert_mem($size) {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),4).' '.$unit[$i];
 }
/* -------------------------------------------------------------------------------------------------------------*/
function track_progress($text) {
global $time_start;
global $cache;
	//**** return;
	if (!is_admin()) return;
	if (!(WP_DEBUG or isset($_REQUEST['mem']) )) return; // only do something if debugging or reqquested

	if (!isset($time_start)) {
		$time_start = microtime(true);
		$diff = 0;
	}
	else {
		$now = microtime(true);
		$diff = round(($now - $time_start),3);
	}
	$mem = memory_get_peak_usage(true);
	$mem = amr_convert_mem($mem);
	$t = sprintf(__('At %s seconds,  peak mem= %s','amr-users'),number_format($diff,3),$mem) ;
	$mem = memory_get_usage (true);
	$mem = amr_convert_mem($mem);
	$t .= ' real_mem='.$mem;
	$t .=' - '.$text;
	echo '<br />'.$t;
	error_log($t);  //debug only
	if (!empty ($cache)) $cache->log_cache_event($t);
}
/* ---------------------------------------------------------------------*/
function amr_js_cdata( $data) { //inline js
	echo "<script type='text/javascript'>\n";
	echo "/* <![CDATA[ */\n";
	echo $data;
	echo "/* ]]> */\n";
	echo "</script>\n";
	} 
/* ---------------------------------------------------------------------*/
function amr_loading_message_js() {
	$js = 'jQuery(function ($) {
		$(window).load(function(){  
			$(".loading").hide(); 
		}
	})';
	 return (amr_js_cdata( $js));
	  
}
//---------------------------------------------------------------------------------------
function amr_get_combo_fields($list) {
global $aopt;

	$s = $aopt['list'][$list]['selected'];  
	asort ($s);

	foreach ($s as $is => $cl) { // for each selected and sorted
		$colno = (int) $cl;  // reduce to an integer to get the column number
		$combofields[$colno][] = $is;  // make a note of the fields in a column in case there are multple
	}
	$iline = amr_build_cols ($s);	 
	foreach ($combofields as $colno => $field) { // convert from column number to tech column name
		if (isset($iline[$colno])) 
			$combofields[$iline[$colno]] = $field;
		unset($combofields[$colno]);
	}
	return($combofields);
}
/* ---------------------------------------------------------------------*/
function amr_get_icols($c, $rptid) {
	$line = $c->get_cache_report_lines ($rptid, '0', '1'); /* get the internal heading names  for internal plugin use only */  /* get the user defined heading names */				
		if (!defined('str_getcsv')) 
			$icols = amr_str_getcsv( ($line[0]['csvcontent']), ',','"','\\');
		else 
			$icols = str_getcsv( $line[0]['csvcontent'], ',','"','\\');
		return ($icols);
}
/* ---------------------------------------------------------------------*/
function amr_get_usermasterfields() {
global $wpdb,$wp_version ;
	
		$main_fields = array(
		'ID',
		'user_login',
		'user_nicename',
		'user_email',
		'user_url',
		'user_registered',
		'user_status',
		'display_name',
		'user_activation_key');	// unlikley to use for selection normally?	

	return $main_fields;
}
/* ---------------------------------------------------------------------*/
function amr_get_createdfields(  ) {
	return (array('post_count','comment_count','avatar','first_role'));
}
/* ---------------------------------------------------------------------*/
if (!function_exists('esc_textarea') ) {
	function esc_textarea( $text ) {
	$safe_text = htmlspecialchars( $text, ENT_QUOTES );
	}
}	

/* ---------------------------------------------------------------------*/	
// not in use ?
function amr_users_dropdown ($choices, $current) { // does the options of the select
 	if (empty($choices)) return'';
	foreach ($choices as $opt => $value){	
		echo '<option value="'.$value.'"';
		if ($value === $current) echo ' selected="selected" ';
		echo '>'.$choices[$opt].'</option>';
	}
}	
/* ---------------------------------------------------------------------------*/
if (!function_exists('amr_setDefaultTZ')) {/* also used in other amr plugins */
	function amr_setDefaultTZ() {
		if (function_exists ('get_option')) {
	/* Set the default php timezone, for various reasons wordpress does not do this, buut assumes  UTC*/
		$current_offset = get_option('gmt_offset');
		$tzstring = get_option('timezone_string');
		}
		else if (function_exists ('date_default_timezone_get'))  $tzstring = date_default_timezone_get();
		else $tzstring = 'UTC';

	/* (wop code: Remove old Etc mappings.  Fallback to gmt_offset. */
		if ( false !== strpos($tzstring,'Etc/GMT') )
			$tzstring = '';
		if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
			if ( 0 == $current_offset )
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}
	}
}
/* -------------------------------------------------------------------------------------------------------------*/
function ausers_delete_htmltransients() {
global $amain;	
	if (empty($amain)) return;
	if (empty($amain['names'])) return; ///something wrong
	foreach ($amain['names'] as $i => $list) {
		delete_transient('amr-users-html-for-list-'.$i);
		
	}
}
/* -------------------------------------------------------------------------------------------------------------*/
function agetnice ($v){
global $amr_nicenames;
	if (isset ($amr_nicenames[$v])) 
		return ($amr_nicenames[$v]);
	else return ucwords(str_replace('_',' ',$v));	
	/*** ideally check for table prefix and string out for newer wordpress versions ***/
}
/** -----------------------------------------------------------------------------------*/ 
function amr_is_network_admin() {  // probably overkill, but rather safe than sorry
	global 	$ausersadminurl,
			$ausers_do_network;	
	
	if (is_network_admin()) return true;
	if (!empty($ausers_do_network)) {
		return true;
	}
	if (stristr($ausersadminurl,'/wp-admin/network/') == FALSE) 
		return false;
	
	return (true);
}
/* -----------------------------------------------------------------------------------*/ 	
function ausers_job_prefix () {
	if (amr_is_network_admin()	) 
		return ('network_');
	else return ('');
}
/* -----------------------------------------------------------------------------------*/
if (!function_exists('in_current_page')) {
function in_current_page($item, $thispage, $rowsperpage ){
/* checks if the item by number should be in the current page or not */
	$ipage =  ceil ($item/$rowsperpage);
	return ($ipage == $thispage);
}
}
/* ---------------------------------------------------------------------*/	
if (!function_exists('amr_check_memory')) {
function amr_check_memory() { /* */

	if (!function_exists('memory_get_peak_usage')) return(false);

		$mem_usage = memory_get_peak_usage(true);       
        $html = amru_convert_mem($mem_usage);

		return($html);
	}
}
/* -----------------------------------------------------------------------------------*/ 
function amru_convert_mem($mem_usage) {
	$html = '';
	if ($mem_usage < 1024)
            $html .= $mem_usage." bytes";
        elseif ($mem_usage < 1048576)
            $html .= round($mem_usage/1024,2)." KB"; /* kilobytes*/
        else
            $html .= round($mem_usage/1048576,2)." MB"; /* megabytes */
	return ($html);		
}
/* -----------------------------------------------------------------------------------*/ 	
if (!(function_exists('objectToArray'))) { //    * Convert an object to an array
	function objectToArray( $object ) {
	/* useful for converting any meta values that are objects into arrays */

		 if (gettype ($object) == 'object') {
			$s =  (get_object_vars ($object));
				if (isset ($s['__PHP_Incomplete_Class_Name'])) unset ($s['__PHP_Incomplete_Class_Name']);
			/*		forced access */
				return($s);
			 }
		else if (is_array ($object)) 
			return array_map( 'objectToArray', $object ); /* repeat function on each value of array */
		else 
			return ($object );
		}
}
/* ---------------------------------------------------------------------- */
function amr_getset_timezone () {
	global $tzobj;
	
	if ($tz = get_option ('timezone_string') ) {
		if (empty($tz)) $tz = 'UTC';
		$tzobj = timezone_open($tz);	
	}	
	else 
		$tzobj = timezone_open('UTC');
	return $tzobj;
}
/* ---------------------------------------------------------------------- */
function amr_users_reset_column_headings ($ulist) {
	if ($amr_users_column_headings = ausers_get_option('amr-users-custom-headings')) {
		unset($amr_users_column_headings[$ulist]); 
		$results = ausers_update_option('amr-users-custom-headings', $amr_users_column_headings);
	}
	else $results = true;
	return ($results);
}
/* ---------------------------------------------------------------------- */
function amr_users_store_column_headings ($ulist, $customcols ) {
	if (!($amr_users_column_headings = ausers_get_option('amr-users-custom-headings'))) {
	
		$amr_users_column_headings = array();
	}
	
	$amr_users_column_headings[$ulist] = $customcols;
	$results = ausers_update_option('amr-users-custom-headings', $amr_users_column_headings);
	if ($results) {
		amr_users_message(__('Custom Column Headings Updated','amr-users'));
			
	}
	else amr_users_message(__('Column headings not updated - no change or error.','amr-users'));
		
		return ($results);
}
/* ---------------------------------------------------------------------- */
function amr_users_get_column_headings ($ulist, $cols, $icols ) {
	global $amr_users_column_headings;
	
	if ($amr_users_column_headings = ausers_get_option('amr-users-custom-headings')) { 
		if (!empty($amr_users_column_headings[$ulist]) ) {
			$customcols = $amr_users_column_headings[$ulist];
			foreach ($icols as $ic => $cv) { 
				if (isset($customcols[$cv])) { 
					$cols[$ic] = $customcols[$cv];
				}
			}
			return ($cols);	
		}
	}
	return ($cols);
}
/* ---------------------------------------------------------------------*/	
function amr_mimic_meta_box($id, $title, $callback , $toggleable = true) {
	global $screen_layout_columns;

	//	$style = 'style="display:none;"';
		$h = (2 == $screen_layout_columns) ? ' has-right-sidebar' : '';
		echo '<div style="clear:both;" class="metabox-holder'.$h.'">';
		echo '<div class="postbox-container" style="width: 49%;">';
		echo '<div class="meta-box-sortables" style="min-height: 10px;">';
		echo '<div id="' . $id . '" class="postbox ' ;
		if ($toggleable) { echo 'if-js-closed' ;}
		echo '">' . "\n";
		echo '<div class="handlediv" title="' . __('Click to toggle','amr-users') . '"><br /></div>';
		
		echo "<h3 class='hndle'><span>".$title."</span></h3>\n";
		echo '<div class="inside">' . "\n";
		call_user_func($callback);
		echo "</div></div></div></div></div>";
		
	}
//}
/* -------------------------------------------------------------------------------------------------------------*/	
function amr_which_role($user_object, $role_no=1) {
/* The wordpress user role area is described in the wordpress code as a big mess  - I think the role business is one reason why */
/* This code is largely copied from  wordpress */
/* Wordpress alllows multiple or no roles.  However most users expect to see 1 role only */
global $wp_roles;

	if (empty($user_object->roles)) return (false);
	$roles = $user_object->roles;
	$role = array_shift($roles);

	if (isset($wp_roles->role_names[$role])) 
		$rolename = translate_user_role($wp_roles->role_names[$role] );
	else $role_name = $role;

	return ($rolename);
}
/* -------------------------------------------------------------------------------------------------------------*/	
if (!function_exists('a_novalue')) {
	function a_novalue ($v) {
	/* since empty returns true on 0 and 0 is valid , use this instead */
	return (empty($v) or (strlen($v) <1));
	};
}
/* ---------------------------------------------------------------------*/	
if (function_exists('amr_flag_error')) return;
else {
	function amr_flag_error ($text) {
		echo '<div class="error"><p>'.$text.'</p></div>';
	}
}
/* ---------------------------------------------------------------------*/	
function amr_users_message($text) {
	echo PHP_EOL.'<div class="updated"><p>';  
	echo $text;
	echo '</p></div><!-- end updated -->'.PHP_EOL;
}

/* ---------------------------------------------------------------------*/
function amr_users_feed($uri, 
		$num=5, 
		$text='Recent News',
		$icon="http://webdesign.anmari.com/images/amrusers-rss.png") {
	
	$feedlink = '<h3><a href="'.$uri.'">'.$text.'</a><img src="'.$icon.'" alt="Rss icon" style="vertical-align:middle;" /></h3>';	

	if (!function_exists ('fetch_feed')) { 
		echo $feedlink;
		return (false);
		}
	if (!empty($text)) {?>
		<div><!-- rss widget -->
		<h3><?php _e($text);?><a href="<?php echo $uri; ?>" title="<?php echo $text; ?>" >
		<img src="<?php echo $icon;?>"  alt="Rss icon" style="vertical-align:middle;"/></a></h3><?php
	}
	// Get RSS Feed(s)
	include_once(ABSPATH . WPINC . '/feed.php');
	include_once(ABSPATH . WPINC . '/formatting.php');
	// Get a SimplePie feed object from the specified feed source.
	$rss = fetch_feed($uri);
	if ( is_wp_error($rss) )   {
		echo $rss->get_error_message();
		echo $feedlink;
		return (false);
	}


	// Figure out how many total items there are, but limit it to 5. 
	$maxitems = $rss->get_item_quantity($num); 

	// Build an array of all the items, starting with element 0 (first element).
	$rss_items = $rss->get_items(0, $maxitems); 
	?>

	<ul class="rss_widget">
	    <?php if ($maxitems == 0) echo '<li>'.__('No items','amr-users').'</li>';
	    else {
	    // Loop through each feed item and display each item as a hyperlink.
	    foreach ( $rss_items as $item ) { 
			$url = $item->get_permalink(); 
			?>
	    <li> <?php //echo $item->get_date('F j').'&nbsp;'; ?>
	        <a href="<?php echo $url; ?>" title="<?php echo $item->get_date('j F Y'); ?>" >
	        <?php echo $item->get_title(); ?> </a> 
			<?php $teaser = $item->get_description();
			$teaser = strip_tags(substr($teaser,0,stripos($teaser, 'Related posts')), null);
			$teaser = substr($teaser,0, 200 - strlen($item->get_title()));
			echo $teaser.'<a href="'.$url.'">...</a>'; ?>
			<?php //echo $item->get_description(); ?>
	    </li>
	    <?php
		}?>
		<li>...</li>
		<?php 
		}?>
	</ul>
	</div><!-- end rss widget -->
	<?php 
}
/* -----------------------------------------------------------*/
function amr_str_getcsv ($string, $sep, $e1, $e2 ) {  /*** a pseudo function only  */
		$arr = explode( $sep, $string);
		$arr[0] = ltrim($arr[0], '"');
		$end = count($arr);
		$arr[$end-1] = rtrim($arr[$end-1],'"');
		return($arr);
	}
/* -------------------------------------------------------------------------------------------------------------*/
function auser_sortbyother( $sort, $other) {
	/* where  other is in an order that we want the sort array to be in .  Note nulls or emptyies to end */
		// Obtain a list of columns

		if (empty($other)) return ($sort);
		$temp = $sort; 
		foreach ($other as $key => $row) {
			if (!empty ($temp[$key]) )
				$s2[$key]  = $temp[$key];
			unset ($temp[$key]);
		}

		if (count($temp) > 0) return (array_merge ($s2, $temp));
		else return ($s2);
	}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_usort( $a, $b) {
	/* comparision function  - don't mess with it - it works - sorts strings to end, else in ascending order */
		if ($a == $b) return (0);
		else if (is_string($a) and (strlen($a) == 0)) return (1);
		else if (is_string($b) and (strlen($a) == 0)) return (-1);
		else return ($a<$b) ? -1: 1;
	}
//}
/* -------------------------------------------------------------------------------------------------------------*/
function ausers_bulk_actions() {
global $two;
	if (!(current_user_can('remove_users'))) return;

	$actions = array('delete'=>__('Delete','amr-users')); // use wp translation

	if (!isset($two)) $two = '';

	echo PHP_EOL.'<div class="clear"> </div>';
	echo PHP_EOL.'<div><!--  bulk action -->';
	echo "<select name='action$two'>\n";
	echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions','amr-users' ) . "</option>\n";
	foreach ( $actions as $name => $title ) {
		$class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

		echo "\t<option value='$name'$class>$title</option>\n";
	}
	echo "</select>\n";

	submit_button( 
		__( 'Apply' ), //text
		'button-secondary action', // type
		'dobulk'.$two, //name
		false, // wrap in p tag or not
		array( 'id' => "doaction$two" ) // other attributes
		);

	$two = '2';
	echo PHP_EOL.'</div><!-- end bulk action -->'.PHP_EOL;
}
/* -----------------------------------------------------------*/
function amr_is_ym_in_list ($list) {
	global $aopt;
	
	if (!is_admin() and !current_user_can('promote_users')) return false;
	if (empty($aopt['list'][$list]['selected'])) return false;
	
	foreach($aopt['list'][$list]['selected'] as $field => $col) {
		if (stristr($field, 'ym_')) // if there is at least one ym field
			return true;
	}	
	return false;
}
/* -----------------------------------------------------------*/
function amr_is_bulk_request ($type) {
	if (((isset($_REQUEST['dobulk']) and	($_REQUEST['dobulk'] == 'Apply'))
	 or (isset($_REQUEST['dobulk2']) and ($_REQUEST['dobulk2'] == 'Apply' ) ))
	and 
	((!empty($_REQUEST['action']) and ($_REQUEST['action'] == $type))
	or
	(!empty($_REQUEST['action2']) and ($_REQUEST['action2'] == $type)	))
	)
	return true;
	else return false;

}
/* -----------------------------------------------------------*/
function amr_redirect_if_delete_requested () { 
	if (amr_is_bulk_request ('delete'))	{
		if (function_exists('amr_ym_bulk_update') and isset($_REQUEST['ps']))
			$_REQUEST['users'] = $_REQUEST['ps'];  // 'ps is required by ym
	
		if (isset($_REQUEST['users'])) wp_redirect(
			add_query_arg(array(
			'users'=>$_REQUEST['users'] , 
			'action'=>'delete'
			),
			wp_nonce_url(network_admin_url('users.php'),'bulk-users')));
		else {
			_e('No users selected','amr-users');
		}
		exit;
	}	
}

add_action('admin_menu','amr_redirect_if_delete_requested');

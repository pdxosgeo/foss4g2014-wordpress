<?php
/*
The csv file functions for the plugin
*/
/* -------------------------------------------------------------------------------------------------*/
function amr_csvlines_to_csvbatch ($lines) { // convert array to csv separated stuff
	$csvlines=array();
	foreach ($lines as $k => $line) {
		$csvlines[]['csvcontent'] = amr_cells_to_csv ($line);
	}
	return $csvlines;
}
/* -------------------------------------------------------------------------------------------------*/
function amr_cells_to_csv ($line) { // convert a line of cells to csv

	foreach ($line as $jj => $kk) {
		if (empty($kk)) 
			$line[$jj] = '""'; /* ***there is no value */
			//$line[$jj] = ''; /* there is no value */
		else {
			$line[$jj] = '"'.str_replace('"','""',$kk).'"';   
			// for any csv a doublequote must be represented by two double quotes, or backslashed - BUT only want for csv, and some systems can backslash?
			// when cacheing rewritten not to be so csv oriented, move this to the csv generation
			//$line[$jj] = '"'.$kk.'"';   //- gets addslashed later, BUT not adding slashes to ' doc "dutch" tor ' - why not ?
			}
	}
	$csv = implode (',', $line); 
	return $csv;		
}
/* -------------------------------------------------------------------------------------------------*/
function amr_is_tofile ($ulist) {
global $amain;

	if (empty($amain['public'][$ulist])) { 
			//check_admin_referer('amr-meta');
			$tofile = false;
	}
	else $tofile = true;
	return $tofile;
}
/* -------------------------------------------------------------------------------------------------*/
function amr_meta_handle_export_request () {
global $amain;
	check_admin_referer('amr-meta');
	$ulist = (int) $_REQUEST['csv'];
	
	$tofile = amr_is_tofile($ulist);
		
	$capability = apply_filters('amr-users-export-csv', 'list_users', $ulist);	
	amr_meta_main_admin_header(__('Export a user list','amr-users'), $capability); // pass capability
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check  and formstartetc
	
	if (isset ($_REQUEST['csvfiltered']))  { 
			echo amr_generate_csv($ulist, true, true, 'txt',"'",chr(9),chr(13).chr(10) ,$tofile);
		}
	else
		echo amr_generate_csv($ulist, true, false,'csv','"',',',chr(13).chr(10), $tofile );
			
	echo ausers_form_end();	
	return;
}
/* -------------------------------------------------------------------------------------------------*/
function amr_meta_handle_csv ($csv, $suffix='csv') {
// check if there is a csv request on this page BEFORE we do anything else ?
if (( isset ($_POST['csv']) ) and (isset($_POST['reqcsv']))) {
	/* since data passed by the form, a security check here is unnecessary, since it will just create headers for whatever is passed .*/
		if ((isset ($_POST['suffix'])) and ($_POST['suffix'] == 'txt')) 
			$suffix = 'txt';
		else 
			$suffix = 'csv';
		amr_to_csv (htmlspecialchars_decode($_POST['csv']),$suffix);
/*		amr_to_csv (html_entity_decode($_POST['csv'])); */
	}
	
}	
/* -------------------------------------------------------------------------------------------------*/
function amr_to_csv ($csv, $suffix) {
/* create a csv file for download */
	if (!isset($suffix)) $suffix = 'csv';
	$file = 'userlist-'.date('Ymd_Hi').'.'.$suffix;
	if (amr_is_network_admin()) $file = 'network_'.$file;
	header("Content-Description: File Transfer");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$file");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $csv;
	exit(0);   /* Terminate the current script sucessfully */
}		
/* -------------------------------------------------------------------------------------------------*/
function amr_undo_db_slashes (&$line) {  
//wp adds to content when inserting into db
//somehow when extracting for csv the backslashes are not being dealt with
// is okay when listing on front end
	$line['csvcontent'] = str_replace('\"','"',$line['csvcontent']);
}
/* -------------------------------------------------------------------------------------------------*/
function amr_get_csv_lines($ulist) {
/* get the whole cached file - write to file? but security / privacy ? */
/* how big */

	$c = new adb_cache();
	$rptid = $c->reportid($ulist);
	$total = $c->get_cache_totallines ($rptid );
	$lines = $c->get_cache_report_lines($rptid,1,$total+1); /* we want the heading line (line1), but not the internal nameslines (line 0) , plus all the data lines, so neeed total + 1 */
	return ($lines);
	}
	
/* -------------------------------------------------------------------------------------------------*/
function amr_lines_to_csv($lines,  // these lines have 'csvcontent'
	$ulist,  // receive lines and output to csv
	$strip_endings, // allows filter?
	$strip_html = false, 
	$suffix, 
	$wrapper, 
	$delimiter, 
	$nextrow, 
	$tofile=false) {
	if (isset($lines) and is_array($lines)) 
		$t = count($lines);
	else 
		$t = 0;

	$csv = '';	
	if ($t > 0) {
	
		array_walk($lines,'amr_undo_db_slashes');
	
		if ($strip_endings) {
			foreach ($lines as $k => $line) {
				$csv .= apply_filters( 'amr_users_csv_line', $line['csvcontent'] ).$nextrow;
			}
		}
		else {
			foreach ($lines as $k => $line)
			$csv .= $line['csvcontent'].$nextrow;
			}
			
		$csv = str_replace ('","', $wrapper.$delimiter.$wrapper, $csv);	
		/* we already have in std csv - allow for other formats */
		$csv = str_replace ($nextrow.'"', $nextrow.$wrapper, $csv);
		$csv = str_replace ('"'.$nextrow, $wrapper.$nextrow, $csv);
		if ($csv[0] == '"') $csv[0] = $wrapper;
	}
	if (amr_debug()) {
		echo '<br />In Debug only: Csv setup: Report: '.$ulist.' '
		.sprintf(__('%s lines found, 1 heading line, the rest data.','amr-users'),$t);	
		$bytes = mb_strlen($csv);
		echo ' Size = '.amru_convert_mem($bytes).'<br />';
	}
	
	if ($tofile) {
		$csvfile = amr_users_to_csv($ulist, $csv, $suffix);
		$csvurl = amr_users_get_csv_link($ulist);
		//return ($csvurl);
		$html = '<br />'.__('Public user list csv file: ','amr-users' ).'<br />'.$csvurl;
		
	}
	else {
		echo '<p>'.sprintf(__('List %s, %s lines, plus heading line'),$ulist, $t).'</p>';
		$html = amr_csv_form($csv, $suffix);
		
	}
	return $html;
	}
/* -------------------------------------------------------------------------------------------------*/
function amr_generate_csv($ulist,
	$strip_endings, // allows filter?
	$strip_html = false, 
	$suffix, 
	$wrapper, 
	$delimiter, 
	$nextrow, 
	$tofile=false) {

	$lines = amr_get_csv_lines($ulist);		
	// could break it here into a get line part and a generate csv part so culd call for filtered csv
	
	$html = amr_lines_to_csv($lines, $ulist,  
	$strip_endings, // allows filter?
	$strip_html, 
	$suffix, 
	$wrapper, 
	$delimiter, 
	$nextrow, 
	$tofile);
	
	
	return($html);
}
/* ---------------------------------------------------------------------*/	
function amr_csv_form($csv, $suffix) {
	/* accept a long csv string and output a form with it in the data - this is to keep private - avoid the file privacy issue */

	if ($suffix=='txt') 
		$text = __('Export CSV as .txt','amr-users'); // for excel users
	else
		$text = __('Export to CSV','amr-users');
		
	return (
		'<input type="hidden" name="suffix" value="'.$suffix . '" />'
		.'<input type="hidden" name="csv" value="'.htmlspecialchars($csv) . '" />'
		.  '<input style="font-size: 1.5em !important;" type="submit" name="reqcsv" value="'
		.$text.'" class="button" />'
		);
}
/* ---------------------------------------------------------------------- */
function amr_users_get_csv_link($ulist) {	//  * Return the full path to the  file 
global $amain;

	$text = (empty ($amain['csv_text'] ) ? '' : $amain['csv_text']);
	
	$csvfile = amr_users_setup_csv_filename($ulist, 'csv');
	$url = amr_users_get_csv_url($csvfile);
	if (file_exists($csvfile))	return (
		PHP_EOL.'<div class="csvlink">
		<p><a class="csvlink" title="'.__('Csv Export','amr-users').'" href="'.$url.'">'
		.$text
		.'</a></p>'.PHP_EOL.
		'</div><!-- end csv link -->'.PHP_EOL
	) ;
	else {
		return '';
	}
}	
/* ---------------------------------------------------------------------- */
function amr_users_get_refresh_link($ulist) {	//  * Return the full path to the  file 
global $amain;

	$text = (empty ($amain['refresh_text'] ) ? '' : $amain['refresh_text']);

	$url = remove_query_arg(array('sort','dir','listpage'));
	$url = add_query_arg(array('refresh'=>'1'),$url);
	return (
	PHP_EOL.'<div class="refreshlink">
	<p><a class="refreshlink" title="'.__('Refresh Cache','amr-users').'" href="'.$url.'">'
	.$text
	.'</a></p>
	</div>'.PHP_EOL
	) ;

}
/* ---------------------------------------------------------------------- */
function amr_users_to_csv($ulist, $text, $suffix) {  // get the file name and write the csv text
	$csvfile = amr_users_setup_csv_filename($ulist, $suffix);
	@unlink($csvfile); // delete old csv file;
	$success = file_put_contents($csvfile, $text.chr(13), LOCK_EX);
	if ($success) 
		return ($csvfile );
	else 
		return (false);
}
/* ---------------------------------------------------------------------------------- */
function amr_users_get_csv_path() { //	 * Attempt to create the log directory if it doesn't exist.
	$upload_dir = wp_upload_dir();
	$csv_path = $upload_dir['basedir']. '/users_csv';	

	if (!file_exists($csv_path)) { /* if there is no folder */
		if (wp_mkdir_p($csv_path, 0705)) {
			printf('<br/>'
				.__('Your csv directory %s has been created','amr-users'),'<code>'.$csv_path.'</code>');
			file_put_contents($csv_path.'/index.php', 'Silence is golden', LOCK_EX);
			return $csv_path;
		}
		else {
				echo ( '<br/>'.sprintf(__('Error creating csv directory %s. Please check permissions','amr-users'),$csv_path)); 
				return $upload_dir;
			}
	}		
	return $csv_path;
}
/* ---------------------------------------------------------------------------------- */
function amr_users_get_csv_url($csvfile) {
	$upload = wp_upload_dir();
	$upload_url = $upload['baseurl'];
	$upload_dir = $upload['basedir'];	
	$csvurl = str_replace($upload_dir,$upload_url, $csvfile); // get the part after theupload dir
	return $csvurl;
}
/* ---------------------------------------------------------------------- */
function amr_users_setup_csv_filename($ulist, $suffix) {	//  * Return the full path to the  file 

	// to avoid too much overwriting, only logged in users get their own filter file for public lists
	$type = 'user_list_';
	
	if (is_user_logged_in()) {
		if (!empty($_REQUEST['csvsubset'])) {  // check fo
			$current_user = wp_get_current_user();
			$name = $current_user->user_login;
			$type='user_list_'.$name.'_filter_';
		}
	}
	
	
	$csvfile 	= amr_users_get_csv_path() .'/'
	.$type.$ulist
	.'.'.$suffix;
	//if (is_network_admin()) $csvfile = 'network_'.$csvfile;
	return $csvfile ;
}
/* ---------------------------------------------------------------------- */
function amr_users_clear_all_public_csv ($except) { // array of user list numbers
	$csv_path = amr_users_get_csv_path();
	$csv_files = glob($csv_path.'/user_list_*.csv');
	
	foreach ($except as $exception=> $public) {
		if ($public) 
			$except[$exception] = $csv_path.'/user_list_'.$exception.'.csv';
		else 
			unset($except[$exception])	;
	}
	
	if (!empty($csv_files)) {
		foreach ($csv_files as $file) {
			if (!in_array($file,$except)) unlink ($file);
		}
	}
}
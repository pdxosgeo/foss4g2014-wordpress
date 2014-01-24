<?php 
if (class_exists('adb_cache')) return;
{	global $wpdb;
	class adb_cache {
	var $table_name;
			
	/* A database table is used for the cacheing in order to keep the user data private - otherwise a csv file would be used */
	/* ---------------------------------------------------------------------- */
	function adb_cache() {
		global $wpdb, $tzobj;
		
		amr_getset_timezone (); // sets the global timezone

		$network = ausers_job_prefix();
		//track_progress('Cache Class initiated Network='.$network);
		$this->table_name = 	$wpdb->prefix.$network."amr_reportcache";
		$this->eventlog_table = $wpdb->prefix.$network."amr_reportcachelogging";
		$this->localizationName = 'amr-users';
		$this->errors = new WP_Error();
		$this->errors->add('norecords', __('No records found in this list','amr-users'));
		$this->errors->add('numoflists', __('Number of Lists must be between 1 and 40.','amr-users'));
		$this->errors->add('rowsperpage', __('Rows per page must be between 1 and 999.','amr-users'));
		$this->errors->add('nonamesarray',__('Unexpected Problem reading names of lists - no array','amr-users'));
		$this->errors->add('nocache',__('No cache exists for this report.','amr-users'));
		$this->errors->add('nocacheany',__('No cache exists for any reports.','amr-users'));
		$this->errors->add('inprogress',__('Cache update in progress.  Please wait a few seconds then refresh.','amr-users'));
		$this->tz = new DateTimeZone(date_default_timezone_get());

	}
	/* ---------------------------------------------------------------------- */
	function record_cache_peakmem ($reportid) {
	/* record the peak memory usage */
		$status = ausers_get_option ('amr-users-cache-status');
		$this->peakmem = $status[$reportid]['peakmem'] = amr_check_memory();	
		return(ausers_update_option ('amr-users-cache-status', $status));
	}
	/* ---------------------------------------------------------------------- */
	function record_cache_headings ($reportid, $html) {
	/* record the peak memory usage */
		$status = ausers_get_option ('amr-users-cache-status');
		$this->headings = $status[$reportid]['headings'] = $html;	
		return(ausers_update_option ('amr-users-cache-status', $status));
	}
	/* ---------------------------------------------------------------------- */
	function get_cache_summary ($reportid) {
	/* record the peak memory usage */
		$status = ausers_get_option ('amr-users-cache-status');
		if (isset( $status[$reportid]['headings'])) $html = $status[$reportid]['headings'];	
		else $html = '';
		return($html);
	}
	/* ---------------------------------------------------------------------- */
	function record_cache_start ($reportid, $name) {
		$status = ausers_get_option ('amr-users-cache-status');
		unset ($status[$reportid]);
		unset ($this);
		$this->start = $status[$reportid]['start'] = time();
		$this->name = $status[$reportid]['name'] = $name;
		return(ausers_update_option ('amr-users-cache-status', $status));
	}
	/* ---------------------------------------------------------------------- */
	function record_cache_end ($reportid, $lines) {
		$status 			= ausers_get_option ('amr-users-cache-status');
		$this->end 			= $status[$reportid]['end'] = time();
		$this->lines 		= $status[$reportid]['lines'] = $lines;
		$this->timetaken 	= $this->end - $this->start;
		return(ausers_update_option ('amr-users-cache-status', $status));
	}
	/* ---------------------------------------------------------------------- */
	function cache_in_progress ($reportid) {
	global $tzobj;
		$r = intval(substr($reportid,5));   /* *** skip the 'users' and take the rest */	
		$inprogress = get_transient('amr_users_cache_'.$r);
		if (!($inprogress)) {
			$this->log_cache_event('Cache record, but no transient yet? '.$reportid );
			return false; 
		}
		$status = ausers_get_option ('amr-users-cache-status');
		//var_dump($status);
		if ((isset($status[$reportid]['start'])) and 
			(!isset($status[$reportid]['end']))) {


			$now = time();
			$diff =  $now - $status[$reportid]['start'];
			if ($diff > 60*5) {
			
				$d = date_create(strftime('%c',$status[$reportid]['start']));
				date_timezone_set( $d, $tzobj );
				$text = sprintf(__('Report %s started %s ago','amr-users' ), $reportid, human_time_diff($status[$reportid]['start'], time()));
				$text .= ' '.__('Something may be wrong - delete cache status, try again, check server logs and/or memory limit','amr-users');
					
				$this->log_cache_event($text);
				$fun = '<a href="http://upload.wikimedia.org/wikipedia/commons/1/12/Apollo13-wehaveaproblem_edit_1.ogg" >'.__('Houston, we have a problem','amr-users').'</a>';
				$text = $fun.'<br/>'.$text;
				amr_users_message($text);
				return(false);
			}
			else return (true);
		}
		else return(false);			
	}
	/* ---------------------------------------------------------------------- */
	function amr_say_when ($timestamp, $report='') {
	global $tzobj;
	//	if (WP_DEBUG) echo '<br />Timestamp = '.$timestamp;
			//$d = date_create(strftime('%C-%m-%d %H:%I:%S',$timestamp)); //do not use %c - may be locale issues
	$d = new datetime('@'.$timestamp); //do not use %c - may be locale issues, wdindows no likely %C
	
	if (is_object($d)) {
		if (!is_object($tzobj)) amr_getset_timezone ();
		date_timezone_set( $d, $tzobj );
		$timetext = $d->format(get_option('date_format').' '.get_option('time_format'));
		$text = sprintf(__('Cache already scheduled for %s, in %s time', 'amr-users'),
		$timetext.' '.timezone_name_get($tzobj),human_time_diff(time(),$timestamp));
		}
	else {
		$text = 'Unknown error in formatting timestamp got next cache: '.$timestamp.' '.print_r($d, true);	}
	return ($text);		
}	
/* ---------------------------------------------------------------------- */	
	function cache_already_scheduled ($report) {	
	$network = ausers_job_prefix();
	$args['report'] = $report;
	
	if ($timestamp = wp_next_scheduled('amr_'.$network.'reportcacheing',$args)) {
		$text = $this->amr_say_when ($timestamp) ;

		return $text;
	}	

	if ($timestamp = wp_next_scheduled('amr_'.$network.'reportcacheing', array())) {
		$text = $this->amr_say_when ($timestamp) ;
		return $text;
	}
	return false;
}
	/* ---------------------------------------------------------------------- */
	function last_cache ($reportid) { /* the last successful cache */
	global $tzobj;
		$status = ausers_get_option ('amr-users-cache-status');
		if ((isset($status[$reportid]['start'])) and 
			(isset($status[$reportid]['end'])))
			return(strftime('%c',round($status[$reportid]['end'])));
		else return(false);			
	}
	/* ---------------------------------------------------------------------- */
	function get_column_headings ($reportid, $line, $csvcontent ) {
		global $wpdb;	
		$wpdb->show_errors();	
		
		$csvcontent = $wpdb->escape(($csvcontent));
		
		$sql = "UPDATE " . $this->table_name .
            ' SET csvcontent = "'. $csvcontent .'"
			  WHERE  reportid = "'.$reportid.'" 
			  AND line = "'. $line.'"';

		$results = $wpdb->query( $sql );
		
		if (is_wp_error($results)) {
			echo __('Error updating report headings.','amr-users').$results->get_error_message();
			die (__('Killing myself.. please check log and status and try again later.','amr-users'));
			
			}
		return ($results);
	}
	/* ---------------------------------------------------------------------- */
	function update_column_headings ($reportid,  $csvcontent ) {
		global $wpdb;	
		$wpdb->show_errors();	
		
		$csvcontent = $wpdb->escape(($csvcontent));
		
		$sql = "UPDATE " . $this->table_name .
            ' SET csvcontent = "'. $csvcontent .'"
			  WHERE  reportid = "'.$reportid.'" 
			  AND line = "2"';

		$results = $wpdb->query( $sql );
		
		if (is_wp_error($results)) {
			echo __('Error updating report headings.','amr-users').$results->get_error_message();
			die (__('Killing myself.. please check log and status and try again later.','amr-users'));
			
			}
		return ($results);
	}
		/* ---------------------------------------------------------------------- */
	function cache_report_lines ($reportid, $start, $lines ) { // cache multiple - break into btaches
	$total = count($lines);
	$batchsize = min(1000,$total);
	$batchstart = 0;
	$row = $start;
	
	while ($batchstart < $total) {
		if (($batchstart + $batchsize) > $total) 
			$batchsize = $total - $batchstart ;
		$results = $this->cache_batch_lines ($reportid, $row, array_slice($lines,$batchstart,$batchsize));
		if (!$results)	{
			$this->log_cache_event('No results '.$results);	
			track_progress('No results '.$results);
			return ($results);
		}
		$this->log_cache_event('Cached next batch of '.$batchsize.' from '.$batchstart);		
		track_progress('Cached next batch of '.$batchsize.' from '.$batchstart);		
		$batchstart = $batchstart + $batchsize;	
		$row = 	$row+	$batchsize;
	}
	return ($results);
	
	}
	/* ---------------------------------------------------------------------- */
	function cache_batch_lines ($reportid, $start, $lines ) { // cache  a batch
		global $wpdb;	
		$wpdb->show_errors();	
		
		$sql = "INSERT INTO " . $this->table_name .
            " ( reportid, line, csvcontent ) " .
            "VALUES ";
			
		$sep = ',';	
		$row  = $start;
		$args = array();
		foreach ($lines as $i => $line ) {
		
			$csv = amr_cells_to_csv ($line);

			if (!($row === $start)) 
				$sql .= $sep;
			$sql .= "(%s,%d,%s)";	
			$args[] = $reportid;
			$args[] = $row;
			$args[] = $csv;  // for any csv a doublequote must be represented by two double quotes ***
			// not ideal for other purposes, but until we redo the data 'warehouse' method this is it
			//$sql .=	"('" . $reportid . "','" . $row. "','" . $csv . "')";		
			$row = $row+1;
		}
		$sql = $wpdb->prepare( $sql , $args); //esc_sql($sql);

		$results = $wpdb->query( $sql );
		
		if (is_wp_error($results)) {
			echo __('Error inserting - maybe clashing with a background run?','amr-users').$results->get_error_message();
			die (__('Killing myself.. please check log and status and try again later.','amr-users'));
			
			}
		return ($results);
	}
	/* ---------------------------------------------------------------------- */
	function cache_report_line ($reportid, $rowno, $line ) {
		global $wpdb;	
		$wpdb->show_errors();	
		$csv = implode (',', $line); 
		
		$sql = $wpdb->prepare("INSERT INTO " . $this->table_name .
            " ( reportid, line, csvcontent ) " .
            "VALUES ('%s','%d','%s')",
			$reportid,$rowno,$csv 
			);

		$results = $wpdb->query( $sql );
		
		if (is_wp_error($results)) {
			echo __('Error inserting - maybe clashing with a background run?','amr-users').$results->get_error_message();
			die (__('Killing myself.. please check log and status and try again later.','amr-users'));
			
			}
		return ($results);
	}
		/* ---------------------------------------------------------------------- */
	function delete_all_logs () {
	global $wpdb;
		$sql = "TRUNCATE " . $this->eventlog_table ;
		$results = $wpdb->query( $sql );
		if ($results) $text = __('Logs deleted','amr-users');
		else $text =__('No logs or Error deleting Logs.','amr-users');
	    $text = $text.'<br/>'
		.'<a href="">'.__('Return', 'amr-users').'</a>'.PHP_EOL;
		amr_users_message($text);
			
	}
	/* ---------------------------------------------------------------------- */
	function log_cache_event($text) {

		global $wpdb, $blogid;	
		$network = ausers_job_prefix();
		$wpdb->show_errors();			
		$datetime = date_create('now', $this->tz);
		if (!empty($network)) $text = $network.' blogid='.$wpdb->blogid.' '.$text;
		
		/* clean up oldder log entries first  if there are any */
		$old = date_create();
		$old = clone ($datetime);
		date_modify($old, '-1 day');
		$sql = "DELETE FROM " . $this->eventlog_table .
            " WHERE eventtime <= '" . date_format($old,'Y-m-d H:i:s') . "'";
		$results = $wpdb->query( $sql );
		/* now log our new message  */
		$sql = "INSERT INTO " . $this->eventlog_table .
            " ( eventtime, eventdescription ) " .
            "VALUES ('" . date_format($datetime,'Y-m-d H:i:s') . "','" . $text . "')";

		$results = $wpdb->query( $sql );
		return ($results);
	}
	/* ---------------------------------------------------------------------- */
	function clear_cache ($reportid ) {
	global $wpdb;		
      $sql = "DELETE FROM " . $this->table_name .
             " WHERE reportid = '" . $reportid . "'";

      $results = $wpdb->query( $sql );

	  $opt = ausers_get_option('amr-users-cache-status');
	  
	  //track_progress('Reportid = '.$reportid.' opt='.print_r($opt[$reportid], true));
	  
	  if (isset($opt[$reportid])) unset ($opt[$reportid]);
	  $result = ausers_update_option('amr-users-cache-status', $opt);	
	  
	  return ($results);
	}
	/* ---------------------------------------------------------------------- */
	function clear_all_cache () {
	global $wpdb;		
      $sql = "TRUNCATE " . $this->table_name;
      $results = $wpdb->query( $sql );
	  if ($results) 
		$text = __('Cache cleared. ','amr-users');
	  else 
		$text =__('Error clearing cache, or no cache to clear. ','amr-users');
	  $result = ausers_delete_option('amr-users-cache-status');
	  if ($result) 
		$text .= __('Cache status in db cleared','amr-users');
	  else 
		$text .=__('Error clearing cache in db, or no cache to clear','amr-users');
	  
	  $text = $text.'<br/>'
	.'<a href="">'.__('Return', 'amr-users').'</a>';
	
	  amr_users_message( $text);
	  return ($results);
	}
	/* ---------------------------------------------------------------------- */
	function cache_exists ($reportid ) {
	global $wpdb;			
		$sql = "SELECT line FROM " . $this->table_name .
             " WHERE reportid = '" . $reportid . "' LIMIT 1;";
		$wpdb->show_errors();
		$results = $wpdb->query( $sql );
	
	  return ($results);

	}
	/* -------------------------------------------------------------------------------------------------------------*/
	function reportid ( $i, $type='user') {
	if ($i < 10) return ($type.'-0'.$i);
	return ($type.'-'.$i);
	}
	/* -------------------------------------------------------------------------------------------------------------*/	
	function reportname ($i ) {
	global $amain;
		if (empty($amain)) $amain = ausers_get_option ('amr-users-main');
		return($amain['names'][$i]);
	}
	/* -------------------------------------------------------------------------------------------------------------*/
	function get_cache_totallines ($reportid ) {
		$status = ausers_get_option ('amr-users-cache-status');
		if (!isset($status[$reportid]['lines'])) return(''); /* maybe no cache */
		return($status[$reportid]['lines']); 
	}
	/* -------------------------------------------------------------------------------------------------------------*/
	function get_cache_report_lines ($reportid, $start=1,  $rowsperpage, $shuffle=false ) { /* we don't want the internal names in line 0, we just want the headings and the data from line 1 onwards*/
		global $wpdb;	
		$wpdb->show_errors();	
		
		if ($shuffle) {
			$orderby = '';
			}
		else	
			$orderby = ' ORDER BY line';
			
		$sql = 'SELECT line, csvcontent FROM ' . $this->table_name
             .' WHERE reportid = "'. $reportid . '"'
			.' AND line >= "'.$start
			.$orderby
			.'" LIMIT '.$rowsperpage.';';

		$results = $wpdb->get_results( $sql, ARRAY_A );
		if (empty($results)) 
			return (false);
		if ($shuffle) { 
			shuffle($results);
		}	
		return ($results);
	}
	/* -------------------------------------------------------------------------------------------------------------*/	
	function search_cache_report_lines ($reportid,   $rowsperpage, $searchtext, $shuffle=false ) { /* we don't want the internal names in line 0, we just want the headings and the data from line 1 onwards*/
	// note search text has been sanitised
		global $wpdb;	
		$start=2;  // there are two lines of headings - exclude both
		$s = (html_entity_decode(stripcslashes($searchtext))); 

		if (($s[0] == '"') AND ($s[strlen($s) - 1] == '"'))  {  
			$phrase = trim ($s, '"');
			$likes =  ' csvcontent LIKE "%'.$phrase.'%"  ';
		}
		else {
			$s = explode(' ',$s); 
			$likes = '';
			foreach ($s as $i => $word) {
				$s[$i] = '  csvcontent LIKE "%'.$word.'%"  ';
			}
			$likes = '('.implode (' OR ', $s).')';
			//
		}
		
		$wpdb->show_errors();	
		
		if ($shuffle) 
			$orderby = '';
		else	
			$orderby = ' ORDER BY line';
		
		$sql = 'SELECT line, csvcontent FROM ' . $this->table_name
             .' WHERE reportid = "'. $reportid . '"'
//			.' AND csvcontent LIKE "%'.$searchtext.'%" '
			.' AND '.$likes
			.' AND line >= "'.$start
			.$orderby
			.'" LIMIT '.$rowsperpage.';';
			
			
		//if (WP_DEBUG) { echo '<br />'.$sql; }	

		$results = $wpdb->get_results( $sql, ARRAY_A );
		if (empty($results)) return (false);
		return ($results);
	}
/* ---------------------------------------------------------------------- */		
	function cache_log () { /* Display the cache reporting log */
		global $wpdb;	
		
		$sql = 'SELECT id, eventtime, eventdescription FROM ' . $this->eventlog_table
			.' ORDER BY id DESC'
			.';';

		$html = '';	
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if (empty($results)) return (false);
		foreach ($results as $i => $r ) {
			$html .= '<li>'.$r['eventtime'].' - '.$r['eventdescription'].'</li>';
		}
		$html = '<ul>'.$html.'</ul>';
		return ($html);
	}
/* ---------------------------------------------------------------------- */	
	function cache_status () {
	/* show the cache status and offer to rebuild */
		global $wpdb;	
		global $amain;
		$problem = false;
		
		if (is_admin()) {
			if (!($amain = ausers_get_option ('amr-users-main'))) 	 
				$amain = ameta_default_main();
		
			$wpdb->show_errors();		
			$sql = 'SELECT DISTINCT reportid AS "rid", COUNT(reportid) AS "lines" FROM ' . $this->table_name.' GROUP BY reportid';
			$results = $wpdb->get_results( $sql, ARRAY_A );  /* Now e have a summary of what isin the cache table - rid, lines */

			if ( is_wp_error($results) )	{	
				echo '<h2>'.$results->get_error_message().'</h2>';		
				return (false);			}
			else {		
						
				if (!empty($results)) {  //var_dump($results);  var_dump($amain);
					foreach ($results as $i => $rpt) {
						$r = intval(substr($rpt['rid'],5));   /* *** skip the 'users' and take the rest */						
						$summary[$r]['rid'] =  $rpt['rid'];
						$summary[$r]['lines'] = $rpt['lines']  - 2; /* as first two liens are headers anyway*/
						$summary[$r]['name'] = $amain['names'][intval($r)];
						}
				}		
				else  {
					echo adb_cache::get_error('nocacheany'); 
					// attempt a realtime run  NO!!! Don't do this - for large databases that are failing anyway will be no good.
					//foreach ($amain['names'] as $i => $name) {
					//	amr_build_user_data_maybe_cache($i);
					//}
				}

				$status = ausers_get_option ('amr-users-cache-status');	/* Now pickup the record of starts etc reportid, start   and reportid end*/	
				if (!empty($status)) {
						foreach ($status as $rd => $se) {
						$r = intval(substr($rd,5));   /* *** skip the 'users' and take the rest */						
						if (empty( $se['end'])) {
							$now = time();
							$diff =  $now - $se['start'];
							if ($diff > 60*5) { 
								$problem = true;
								$summary[$r]['end'] = __('Taking too long, may have been aborted... delete cache status, try again, check server logs and/or memory limit', 'amr-users');	
								delete_transient('amr_users_cache_'.$r); // so another can run							
							}
							else {
								$summary[$r]['end'] = sprintf(__('Started %s', 'amr-users'), human_time_diff($now,$se['start'] ));		
							}			
							
							$summary[$r]['time_since'] = __('?','amr-users');
							$summary[$r]['time_taken'] = __('?','amr-users');
							$summary[$r]['peakmem'] = __('?','amr-users');
							$summary[$r]['rid'] = $rd;
							$r = intval(substr($rd,5));   /* *** skip the 'users' and take the rest */		
							$summary[$r]['name'] = $amain['names'][intval($r)];
						}
						else {
							if (empty($se['end'])) {
								$summary[$r]['end'] = 'In progress';
							}
							else {
								$datetime = new datetime( date ('Y-m-d H:i:s',$se['end'] ));
								if (empty ($tzobj))
									$tzobj = amr_getset_timezone ();
								$datetime->setTimezone($tzobj);
								$summary[$r]['end'] = $datetime->format('D, j M G:i') ;
								
							}
							//$summary[$r]['end'] = empty($se['end']) ? 'In progress' : date_i18n('D, j M H:i:s',$se['end']);  /* this is in unix timestamp not "our time" , so just say how long ago */
							$summary[$r]['start'] = date_i18n('D, j M Y H:i:s',$se['start']);  /* this is in unix timestamp not "our time" , so just say how long ago */

							$dt = new DateTime('now', $this->tz);
							$now = date_format( $dt,'D, j M Y G:i e');
							$summary[$r]['time_since'] = human_time_diff ($se['end'],time()); /* the time that the last cache ended */		
							$summary[$r]['time_taken'] = $se['end'] - $se['start']; /* the time that the last cache ended */	
							$summary[$r]['peakmem'] = $se['peakmem'];
							$summary[$r]['headings'] = $se['headings'];
						}
					}
				}				
				else if (!empty($summary)) {
					foreach ($summary as $rd => $rpt) { 
						$summary[$rd]['time_since'] = $summary[$rd]['time_taken'] = $summary[$rd]['end'] = $summary[$rd]['peakmem'] = '';
					}
				}		
				if (!empty($summary)) { 	
					echo  PHP_EOL.'<div class="wrap" style="padding-top: 20px;">'
					.'<h3>'.$now.'</h3>'
					.PHP_EOL.'<table class="widefat" style="width:auto; ">'
						//.'<caption>'.__('Report Cache Status','amr-users').' </caption>'
						.'<thead><tr><th>'.__('Report Id', 'amr-users')
						.'</th><th>'.__('Name', 'amr-users')
						.'</th><th>'.__('Lines', 'amr-users')
						.'</th><th style="text-align: right;">'.__('Ended?', 'amr-users')
						.'</th><th style="text-align: right;">'.__('How long ago?', 'amr-users')
						.'</th><th style="text-align: right;">'.__('Seconds taken', 'amr-users')
						.'</th><th style="text-align: right;">'.__('Peak Memory', 'amr-users')
						.'</th><th style="text-align: right;">'.__('Details', 'amr-users')
						.'</th></tr></thead>';	
					foreach ($summary as $rd => $rpt) {
						If (!isset($rpt['headings'])) $rpt['headings'] =  ' ';
						If (!isset($rpt['lines'])) $rpt['lines'] =  ' ';
						If (isset($rpt['rid'])) {
						echo '<tr>'
						.'<td>'.$rpt['rid'].'</td>'
						.'<td>'.au_view_link($rpt['name'], $rd, '').'</td>'
						.'<td align="right">'.$rpt['lines'].'</td>'
						.'<td align="right">'.$rpt['end'].'</td>'
						.'<td align="right">'.$rpt['time_since'].'</td>'
						.'<td align="right">'.$rpt['time_taken'].'</td>'
						.'<td align="right">'.$rpt['peakmem'].'</td>'
						.'<td align="right">'.$rpt['headings'].'</td>'
						.'</tr>';
						}
					}
				
					echo PHP_EOL.'</table>'.PHP_EOL.'</div><!-- end wrap -->'.PHP_EOL;
					
				}
			}
			
		}
		else echo '<h3>not admin?</h3>';
		if ($problem) {
			$fun = '<a target="_blank" title="'.__('Link to audio file of the astronauts of Apollo 13 reporting a problem.', 'amr-users').'" href="http://upload.wikimedia.org/wikipedia/commons/1/12/Apollo13-wehaveaproblem_edit_1.ogg" >'.__('Houston, we have a problem','amr-users').'</a>';
			$text = __('The background job\'s may be having problems.', 'amr-users');
			$text .= '<br />'.__('Delete all the cache records and try again', 'amr-users');
			$text .= '<br />'.__('Check the server logs and your php wordpress memory limit.', 'amr-users');
			$text .= '<br />'.__('The TPC memory usage plugin may be useful to assess whether the problem is memory.', 'amr-users');
			$text = $fun.'<br/>'.$text;
			amr_users_message( $text);
		}
		
	
	}
/* ---------------------------------------------------------------------- */		
	function deactivate () {
	global $wpdb;			
		$sql = "DROP TABLE " .$this->table_name.', '.$this->eventlog_table;
		$wpdb->show_errors();
		$results = $wpdb->query( $sql );
	
	  return ($results);

	}	
/* ---------------------------------------------------------------------- */		
	/* get_error - Returns an error message based on the passed code
	Parameters - $code (the error code as a string)
	Returns an error message */
	function get_error($code = '') {
		$errorMessage = $this->errors->get_error_message($code);
		if ($errorMessage == null) {
			return __("Unknown error.", $this->localizationName);
		}
		return $errorMessage;
	}

}
	
	/* ---------------------------------------------------------------------- */	
}

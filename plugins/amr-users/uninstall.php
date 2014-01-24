<?php
/**
 * Uninstall functionality 
 * 
 * Removes the plugin cleanly in WP 2.7 and up
 */
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) 
	exit();
else amr_users_uninstall();
	/* -----------------------------------------------------------*/

	function ameta_cache_drop ($table_name) {
	/* Create a cache table if t does not exist */
		global $wpdb;
	/* 	if the cache table does not exist, then create it . be VERY VERY CAREFUL about editing this sql */

		$sql = "DROP TABLE " . $table_name . ";";
		$results = $wpdb->query( $sql );
		return ($results);

	}
		/* -----------------------------------------------------------*/
/* This is the amr ical uninstall file */
	function amr_users_uninstall() {	
	global $wpdb;

	if (function_exists ('delete_option')) {  	// what about multi site?

		if (delete_option ('amr-users')) echo '<h3>'.__('Deleting number of lists and names in database','amr-users').'</h3>';
		if (delete_option ('amr-users'.'-no-lists')) echo '<h3>'.__('Deleting all lists settings in database','amr-users').'</h3>';
		if (delete_option ('amr-users-nicenames')) echo '<h3>'.__('Deleting all nice name settings in database','amr-users').'</h3>';
		if (delete_option ('amr-users-cache-status')) echo '<h3>'.__('Deleting cache status in database','amr-users').'</h3>';
		if (delete_option ('amr-users-cachedlists')) echo '<h3>'.__('Deleting cached lists info in database','amr-users').'</h3>';
	}

	if (function_exists ('wp_clear_scheduled_hook')) {
		wp_clear_scheduled_hook('amr_regular_reportcacheing');
		if (is_multisite()) wp_clear_scheduled_hook('amr_network_regular_reportcacheing');
		echo '<h3>'.__('Removed scheduled action','amr-users').'</h3>';
	}

	if (ameta_cache_drop($wpdb->prefix . "amr_reportcache")) echo '<h3>'.__('Deleted cache table','amr-users').'</h3>';
	if (ameta_cache_drop($wpdb->prefix . "amr_reportcachelogging")) echo '<h3>'.__('Deleted cache log table','amr-users').'</h3>';;		
	return (true);	 
					
	}
/* -------------------------------------------------------------------------------------------------------------*/

<?php
/*
Plugin Name: amr users
Plugin URI: http://wpusersplugin.com/
Author URI: http://webdesign.anmari.com
Description: Configurable users listings by meta keys and values, comment count and post count. Includes  display, inclusion, exclusion, sorting configuration and an option to export to CSV. If you found this useful, please <a href="http://wordpress.org/extend/plugins/amr-users/">  or rate it</a>, or write a post.
Author: anmari
Version: 3.7.1
Text Domain: amr-users
License: GPL2

 Copyright 2009,2010,2011,2012  anmari  (email : anmari@anmari.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*

Technical Notes:

Uses Tables:
wpprefix_amr_reportcache  (id, reportid, line, csvcontent)
wpprefix_amr_reportcachelogging (id, eventtime, eventdescription)

wp options:
amr-users (aopt)
	[list]

amr-users-main - the main options
	[rowsperpage]
etc

amr-users-nicenames

amr-users-cache-status [reportid]
		[start]
		[end]
		[name]
		[lines]
		[peakmem]
		[headings]  (in html)
*/
define ('AUSERS_VERSION', '3.7.1');
define( 'AUSERS_URL', plugin_dir_url( __FILE__ ) );
define ('AUSERS_DIR', plugin_dir_path( __FILE__ )  );
define( 'AMETA_BASENAME', plugin_basename( __FILE__ ) );

require_once ('includes/ameta-query.php');

require_once ('includes/ameta-list.php');
require_once ('includes/amr-users-headings-forms.php');
require_once ('includes/amr-users-widget.php');
require_once ('includes/amr-users-custom-html.php');
//if (is_admin()) // some admin functions required on front end

require_once ('includes/ameta-admin.php');
require_once ('includes/ameta-options.php');
require_once ('includes/ameta-includes.php');
require_once ('includes/ameta-upgrades.php');
require_once ('includes/ameta-cache.php');
require_once ('includes/amr-users-csv.php');
require_once ('includes/amr-users-credits.php');
require_once ('includes/ameta-building.php');

amr_setDefaultTZ(); /* essential to get correct times as per wordpress install - why does wp not do this by default? Ideally should be set in php.ini, but many people may not have access */
//date_default_timezone_set(get_option('timezone_string'));

function ausers_load_pluggables() { // make pluggables load later so that they are 'pluggable'
	require_once('includes/ausers-pluggable.php');
}
/* ----------------------------------------------------------------------------------- */
function ausers_add_actions() {
global $amain;

	
	add_filter('manage_users_columns',			'amr_add_user_columns');
	add_filter('manage_users_custom_column', 	'amr_show_user_columns', 10, 3 );

	if (empty($amain)) $amain = ausers_get_option('amr-users-main');

	if (!empty($amain['notonuserupdate'])) return; // do not trigger cache on user update
	
	add_action('profile_update','amr_user_change');
	add_action('user_register','amr_user_change');
	add_action('deleted_user','amr_user_change'); // also for wpmu
	add_action('added_user_meta','amr_user_meta_change');
	add_action('updated_user_meta','amr_user_meta_change');
	add_action('deleted_user_meta','amr_user_meta_change');
	add_action('make_spam_user','amr_user_meta_change');
	add_action('make_ham_user','amr_user_meta_change');
	add_action('remove_user_from_blog','amr_user_change');
	add_action('add_user_to_blog','amr_user_change');

	
}
/* ----------------------------------------------------------------------------------- */
function add_ameta_stylesheet () {

      $myStyleUrl = AUSERS_URL.'css/amrusersfront.css';
      $myStyleFile = AUSERS_DIR. 'css/amrusersfront.css';
	  

      if ( file_exists($myStyleFile) ) { 
            wp_register_style('alist', $myStyleUrl);
            wp_enqueue_style( 'alist', $myStyleUrl);
      }
}
/* ----------------------------------------------------------------------------------- */
function add_ameta_printstylesheet () {
      $myStyleUrl = AUSERS_URL.'css/alist_print.css';
      $myStyleFile = AUSERS_DIR. 'css/alist_print.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('alist_print', $myStyleUrl);
            wp_enqueue_style( 'alist_print', $myStyleUrl, false, false, 'print');
        }
}
/* ----------------------------------------------------------------------------------- */
function amr_network_userlist($atts) {
global $ausers_do_network;	
	$ausers_do_network = true;
	return (amr_userlist($atts));
}
/* ----------------------------------------------------------------------------------- */
function amr_userlist($atts) {
global $ausers_do_network;	
global $amain, $aopt;
	
	remove_filter( 'the_content', 'wpautop');   // doesn't work for everyone - why not ?

	$ausers_do_network = false;
	ameta_options(); // amain will be set
	
	$criteria = array(
		'show_csv' ,
		'show_headings',
		'show_search',
		'show_perpage', 
		'show_pagination', 
		'show_refresh',
		'show_randompage',
		'shuffle',
		'start_empty');
	
//	if (WP_DEBUG)  {
//		echo 'If Debug only: Attributes from shortcode: '; var_dump($atts);
//	}
// compatibility
	if (!empty ($atts['headings']) ) {
		$atts['show_headings'] = $atts['headings'];
		unset ($atts['headings']);
	}

	if (isset($_REQUEST['list'])) { /* allow admin users to test lists from the front end, by adding list=x to the url */
		$num = (int)$_REQUEST['list'];
		if (($num > 0) and ($num <= count($amain['names']))) 
			$list= $num;
	}
	else if (!empty($atts['list'])) 
		$list = (int) $atts['list'];
	else 
		$list = 1;
// else use whatever was in shortcode
	//
	$options = array();
	foreach ($criteria as $i) {
		if (isset($amain[$i][$list])) {
			$options[$i] = $amain[$i][$list];
		}
	}	
	// override with shortcode
	
	if (!empty($atts)) { 
		foreach ($atts as $i => $value) {  
			$options[$i] = $atts[$i];
			if ($options[$i] === 'false')  // allow for the word false to be used instead of 0
				$options[$i] = false;
		}	
	}

	if (ausers_ok_to_show_list($list)) {
		$html = alist_one('user', $list, $options);
//		if ($options['show_search'] or $options['show_perpage'])	{
			$html = ausers_form_start()  // bracket with a
			.$html
			.ausers_form_end();
//		}
		return ($html);
	}
	else
	return('<p><strong>'
	.__('Not logged in or no permission for non public user list','amr-users')
	.'</strong></p>');
//		return('<!-- '.__('Inadequate permission for non public user list','amr-users').' -->');
}
/* ----------------------------------------------------------------------------------- */
function ausers_ok_to_show_list($list) {
global $amain;
	if (is_user_logged_in()
		and ((current_user_can('list_users')
			or current_user_can('edit_users')) )) { // only do the list if it is public
		return true;
	}
	if  (!empty($amain['public'][$list])) return true;
	else return false;

}
/* ----------------------------------------------------------------------------------- */
function ausers_plugin_action($links, $file) { //	Adds a link directly to the settings page from the plugin page
	global $ausersadminurl;
	/* create link */
		if (( $file == AMETA_BASENAME ) or ($file == 'amr-users-multisite/amr-users-multisite.php')) {
			array_unshift($links,'<a href="'.$ausersadminurl.'?page=amr-users'.'">'. __('Settings','amr-users').'</a>' );
		}
	return $links;
	} // end plugin_action
/* ---------------------------------------------------------------*/
function amr_user_change ($userid='') { /* wordpress passes the user id as a argument on a "profile update action */
global $amr_already_got_user_change;
	if (!empty($amr_already_got_user_change)) return; //avoid triggering multiple times in one screen update
	$amr_already_got_user_change = true;
	$logcache = new adb_cache();
	$logcache->log_cache_event(
	'<em style="color: green;">'.sprintf(__('Update of User %s - user reporting cache update requested','amr-users'),$userid).'</em>');
	return (amr_request_cache());
}
/* ---------------------------------------------------------------*/
function amr_user_meta_change ($metaid) { /* wordpress passes the user id as a argument on a "profile update action */
global $amr_already_got_user_change;
	if (!empty($amr_already_got_user_change)) return; //avoid triggering multiple times in one screen update
	$amr_already_got_user_change = true;
	$logcache = new adb_cache();
	$logcache->log_cache_event(
	'<em style="color: green;">'.sprintf(__('Update of user meta record %s - user reporting cache update requested','amr-users'),$metaid).'</em>');
	return (amr_request_cache());
}
/* ---------------------------------------------------------------*/
function amr_request_cache_with_feedback ($list=null) {
global	$ausersadminurl;

	$result = amr_request_cache($list);
	if (!empty($result)) {

			amr_users_message($result);?>

			<ul><li><?php _e('Report Cache has been scheduled.','amr-users');?>
			</li><li><?php _e('If you have a lot of records, it may take a while.','amr-users'); ?>
			</li><li><?php _e('Please check the cache log - refresh for updates and do not reschedule until all the reports have completed. ','amr-users'); ?>
			</li><li><?php _e('If you think it is taking too long, problems may be occuring in the background job, such as running out of memory.  Check server logs and/or Increase wordpress\s php memory limit','amr-users'); ?>
			</li><li><?php _e('The cache status or the TPC Memory Usage plugin may be useful to assess this.','amr-users'); ?>
			</li><li><?php echo au_cachelog_link(); ?>
			</li><li><?php echo au_cachestatus_link();?>
			</a></li>
			</ul>
	<?php
	}
	else {
		echo '<h2>Error requesting cache:'. $result.'</h2>';  /**** */
		}
	return($result);
// time()+3600 = one hour from now.
}
/* ---------------------------------------------------------------*/
function amr_request_cache ($list=null) {
	global $aopt;
	global $amain;
	$logcache = new adb_cache();
	$network = ausers_job_prefix();
	if (!empty($list)) {
		if ($logcache->cache_in_progress($logcache->reportid($list,'user'))) {
			$text = sprintf(__('Cache of %s already in progress','amr-users'),$list);
			$logcache->log_cache_event($text);
			return $text;
		}
		if ($text = $logcache->cache_already_scheduled($list) ) {
			$new_text = __('Report ','amr-users').$list.': '.$text;
			$logcache->log_cache_event($new_text);
			return $new_text;
		}

		$time = time()+5;
		$text = sprintf($network.__('Schedule background cacheing of report: %s','amr-users'),$list);
		$logcache->log_cache_event($text);
		$args[] = $list;
		wp_schedule_single_event($time, 'amr_'.$network.'reportcacheing', $args); /* request for now a single run of the build function */
		return($text);

	}
	else {
		ameta_options();
		if (empty ($aopt['list']) ) {
			$text = $network.__('Error: No stored options found.','amr-users');
			$logcache->log_cache_event($text);
			return $text;
		}
		else $no_rpts = count ($aopt['list']);

		$logcache->log_cache_event('<b>'.$network.sprintf(__('Received background cache request for %s reports','amr-users'),$no_rpts).'</b>');

		$returntext = '';
		$time_increment = 60;
		$nexttime = time();
		foreach ($aopt['list'] as $i => $l) {

			if (!empty($amain['names'][$i])) { 
			//if ($i <= count($amain['names'])) {  
				$args = array('report'=>$i);
				if ($text = $logcache->cache_already_scheduled($i)) {
					$new_text = __('All reports: ','amr-users').$text;
					$logcache->log_cache_event($new_text);
					$returntext .= $new_text.'<br />';
					return $returntext;
				}
				else {
					wp_schedule_single_event($nexttime, 'amr_'.$network.'reportcacheing', $args); /* request for now a single run of the build function */
					$nexttime = $nexttime + $time_increment;
					unset ($args);
					$text = sprintf(__('Schedule background cacheing of report: %s','amr-users'),$i);
					$logcache->log_cache_event($text);
					$returntext .= $text.'<br />';
				}
			}

		}
		return ($returntext);
	}
//$result = spawn_cron( time()); /* kick it off soon */
// time()+3600 = one hour from now.
}
/* ----------------------------------------------------------------------------------- */
function add_amr_script() { //* Enqueue style-file, if it exists.

			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');

}
/* ----------------------------------------------------------------------------------- */
function add_amr_stylesheet() {
global $amain;
	if (empty($amain)) $amain = ausers_get_option('amr-users-main');
	if (isset($amain['do_not_use_css']) and ($amain['do_not_use_css'])) return;

	if (!empty($amain['use_css_on_pages']) ) {
		$do_css = false;
		$csspages = explode(',',$amain['use_css_on_pages']);
		foreach ($csspages as $csspage) {
			if ($do_css = is_page($csspage)) break;
		}			
		if (!$do_css) return;			
	}

    $myStyleUrl = AUSERS_URL.'css/amrusersfront.css';
    $myStyleFile = AUSERS_DIR.'css/amrusersfront.css';
    if ( file_exists($myStyleFile) ) {
            wp_register_style('amrusers', $myStyleUrl);
            wp_enqueue_style( 'amrusers');
        }
    }
/* ----------------------------------------------------------------------------------- */
function amr_users_widget_init() {
//    register_sidebar_widget("AmR iCal Widget", "amr_ical_list_widget");
//    register_widget_control("AmR iCal Widget", "amr_ical_list_widget_control");
	register_widget('amr_users_widget');
}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_users_filter_csv_line( $csv_line ) {   //why doing this again? is it system specfifc line endings
#
   return preg_replace( '@\r\n@Usi', ' ', $csv_line );
#
}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_shutdown () {

	if ($error = error_get_last()) {
        if (isset($error['type']) && ($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR)) {
           // ob_end_clean(); // doesn't work apparently - either nothing to flush or too late

            if (!headers_sent()) {
                header('HTTP/1.1 500 Internal Server Error');
            }

            echo '<h1>A shutdown was triggered, possibly a fatal error was caught.  Please note the message below when reporting this.</h1>';
            echo '<code>' . print_r($error, true) . '</code>';
			error_log(print_r($error, true));
        }
    }

	}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_users_deactivation () {
	global $amain;
	if (function_exists ('wp_clear_scheduled_hook')) {
		wp_clear_scheduled_hook('amr_regular_reportcacheing');
		foreach ($amain['names'] as $i => $name )
			wp_clear_scheduled_hook('amr_reportcacheing', array('report'=>$i));

	}
	$c = new adb_cache();
	$c->deactivate();
	}
/* -------------------------------------------------------------------------------------------------------------*/

	load_plugin_textdomain('amr-users', PLUGINDIR
		.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
		
	if  ((!function_exists ('is_admin')) /* eg maybe bbpress*/ or (is_admin())) {
		add_action('admin_menu', 			'amr_meta_menu');
		add_filter('plugin_action_links', 	'ausers_plugin_action', -10, 2);	
		add_filter ('contextual_help',		'amrmeta_mainhelp',10,3);		
	}
	else {
		add_shortcode('userlist', 			'amr_userlist');
		add_shortcode('network_userlist', 	'amr_network_userlist');
	}	
		
	add_action ('after_setup_theme',		'ausers_load_pluggables');
	add_action ('init',						'ausers_add_actions', 99);		
	add_action ('wp_print_styles', 			'add_amr_stylesheet');

//	add_action ('wp_print_scripts', 	'add_amr_script');
	add_action ('amr_regular_reportcacheing','amr_request_cache');
	add_action ('amr_reportcacheing',		'amr_build_user_data_maybe_cache');  /* the single job option */

	add_action ('widgets_init', 			'amr_users_widget_init');
	add_filter ('amr_users_csv_line', 		'amr_users_filter_csv_line' );
	add_action ('plugins_loaded',			'amr_meta_handle_export');
	add_action ('plugins_loaded',			'amr_meta_handle_csv');
	add_action( 'manage_users_sortable_columns', 'amr_wplist_sortable' );
	//add_filter( 'request', 					'amr_q_orderby' ); // - is affecting the posts query - must limit to users only! duh

	/* ---------------------------------------------------------------------------------*/
	/* When the plugin is activated, create the table if necessary */
	register_activation_hook(__FILE__,		'ameta_cache_enable');
	register_activation_hook(__FILE__,		'ameta_cachelogging_enable');
	if ( function_exists('register_uninstall_hook') ) 
		register_uninstall_hook( __FILE__, 'amr_users_check_uninstall' );

	/* The deactivation hook is executed when the plugin is deactivated */
    register_deactivation_hook(__FILE__,	'amr_users_deactivation');
	/* ---------------------------------------------------------------------------------*/


?>
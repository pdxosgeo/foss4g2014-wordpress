<?php 
/* -------------------------------------------------------------------------------------------------------------*/	
function amr_check_for_upgrades () {   // NB must be in order of the oldest changes first // called from ausers_get_option
// should already have values then - and will not be new ?
global $amain, $aopt;

	if (empty($amain)) $amain = ausers_get_option('amr-users-main');
	//if (WP_DEBUG) echo '<div class="message">Debug mode: check doing upgrade check </div>';
	// must be in admin and be admin
	if (!is_admin() or !(current_user_can('manage_options')) ) return;
			// handle a series of updates in order 
			
	if (!isset($amain['version'])) 
		$amain['version'] = '0'; // really old?
	if (version_compare($amain['version'],AUSERS_VERSION,'='))
		return;  // if same version, don't repeat check
	
	$prev = $amain['version'];
	echo PHP_EOL.'<div class="updated"><p>';  // closing div at end 
	
		printf(__('Previous version was %s. ', 'amr-users'),$prev );
		_e('New version activated. ', 'amr-users');
		_e('We may need to process some updates.... checking now... ', 'amr-users');

	
	// do old changes first - user may not have updated for a while....

	if ((!isset($amain['version'])) or  
	 (version_compare($amain['version'],'3.1','<'))) { // convert old options from before 3.1
	 
		echo '<br />';
		printf(__('Prev version less than %s', 'amr-users'),'3.1.');
		if (!isset($amain['csv_text'])) 
			$amain['csv_text'] = ('<img src="'
				.plugins_url('amr-users/images/file_export.png')
				.'" alt="'.__('Csv') .'"/>');
		if (!isset($amain['refresh_text'])) 
			$amain['refresh_text'] =  ('<img src="'
			.plugins_url('amr-users/images/rebuild.png')
			.'" alt="'.__('Refresh user list cache', 'amr-users').'"/>');
				
		ausers_update_option('amr-users-main',$amain );	
		echo '<br />'.__('Image links updated.', 'amr-users');

		echo '</p>';
	}
	//
	if ((!isset($amain['version'])) or  
	 (version_compare($amain['version'],'3.3.1','<'))) { // check for before 3.3.1
		echo '<br />';
		printf(__('Prev version less than %s', 'amr-users'),'3.3.1.');
		$c = new adb_cache();
		$c->deactivate();
		
		if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
		echo '<h2>'.__('Problem creating amr user DB tables', 'amr-users').'</h2>';
		echo '<br />';
		_e('Cacheing tables recreated.', 'amr-users'); 
	}
	//
	if ((!isset($amain['version'])) or  
	 (version_compare($amain['version'],'3.3.6','<'))) { // check for before 3.3.6, 
		echo '<br />';
		printf(__('Prev version less than %s', 'amr-users'),'3.3.6. ');
		echo '</p>'.__('Minor sub option name change for avatar size', 'amr-users').'</p>';
		if (!empty($amain['avatar-size']))
			$amain['avatar_size'] = $amain['avatar-size']; //minor name fix for consistency
		else
			$amain['avatar_size'] = '16';
		unset($amain['avatar-size']);
		ausers_update_option('amr-users-main',$amain );	
		 
	}
// 3.4.4  July 2012
	if ((!isset($amain['version'])) or  
	 (version_compare($amain['version'],'3.4.4','<'))) { // check for before 3.3., 
		echo '<br />';
		printf(__('Prev version less than %s', 'amr-users'),'3.4.4 ');
		echo '<p><b>'.__('New Pagination option default to yes for all lists.', 'amr-users').'</b></p>';

		if (!isset($amain['show_pagination'])) {
			foreach ($amain['names'] as $i => $n) { 
				$amain['show_pagination'][$i] = true;
			}
		}		 
	}
	
	$amain['version'] = AUSERS_VERSION;
	ausers_update_option('amr-users-main',$amain );	 // was 'amr-users-no-lists'

		
	echo '<p>'.__('Finished Update Checks', 'amr-users').' ';
	echo ' <a href="http://wordpress.org/extend/plugins/amr-users/changelog/">'
	.__('Please read the changelog','amr-users' ).'</a>';
	echo '</p>'.PHP_EOL;
	echo '<br />'.__('As a precaution we will now rebuild the nice names.', 'amr-users');
	echo '<br />'.__('Relax .... you won\'t lose anything.', 'amr-users');
	ameta_rebuildnicenames ();
	echo '</div><!-- end updated -->'.PHP_EOL;
	
}
/* -------------------------------------------------------------------------------------------------------------*/	

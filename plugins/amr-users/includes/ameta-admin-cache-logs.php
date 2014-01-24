<?php
function amrmeta_cache_logs_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;
	
//	amr_meta_main_admin_header('Cache Logs');
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc		

	$c = new adb_cache();
					
	if (isset($_POST['trashlog']) )  { /*  jobs having a problem - allow try again option */
		$c->delete_all_logs();
		//return;	
	}	
	else {				
		echo alist_trashlogs ();			
		echo $c->cache_log();						
	}	

	echo ausers_form_end();
}

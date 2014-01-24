<?php
function alist_trashlogs () {	
	return ('<div class="submit">
			<input type="submit" class="button-primary" name="trashlog" value="'.__('Delete the cache log records', 'amr-users').'" />
			</div>');
	}
/* ---------------------------------------------------------------------*/
function alist_trashcache () {	
	return ('<div class="submit">
			<input title="'.__('Delete the actual cache records.','amr-users').'" type="submit" class="button" name="trashcache" value="'.__('Delete all cache entries', 'amr-users').'" />
			</div>');
	}
/* ---------------------------------------------------------------------*/	
function alist_trashcache_status () {	
	return ('<div class="submit">
			<input title="'.__('Does not delete report cache, only the status records.','amr-users').'" type="submit" class="button" name="trashcachestatus" value="'.__('Delete all cache status records', 'amr-users').'" />
			</div>');
	}
	/* ---------------------------------------------------------------------*/
function amr_trash_the_cache () { 

	ausers_delete_option ('amr-users-cache-status');
	$text = __('Cache status records deleted, try building cache again', 'amr-users');
	$text = $text.'<br/>'
	.'<a href="">'.__('Return', 'amr-users').'</a>';
	amr_users_message($text);

}
/* ---------------------------------------------------------------------*/
function amrmeta_cachestatus_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;

	
	
	$c = new adb_cache();
//	amr_meta_main_admin_header('Cache Status');
	amr_meta_admin_headings ($plugin_page=''); // does the nonce check etc
	
	if (isset ($_REQUEST['rebuildback'])) { 
		echo '<p>'.__('Background cache request received', 'amr-users').'</p>';
		if (isset($_REQUEST['rebuildreal'])) {
			$ulist = (int) $_REQUEST['rebuildreal'];
			amr_request_cache_with_feedback($ulist);
			}
		else 
			amr_request_cache_with_feedback(); 
		return;	
	}/* then we have a request to kick off run */
	
	if (isset($_POST['trashlog']) )  { /*  jobs having a problem - allow try again option */	
		$c->delete_all_logs();
		//return;	
		}	
	elseif (isset($_POST['trashcache']) )  { /*  jobs havign a problem - allow try again option */
		$c->clear_all_cache();
		//return;	
		}	
	elseif (isset($_POST['trashcachestatus']) )  { /*  jobs havign a problem - allow try again option */
		amr_trash_the_cache ();
		//return;	
		}

		$c->cache_status();										
		echo alist_rebuild();
		echo alist_trashcache_status();
		echo alist_trashcache ();
			
	
	echo ausers_form_end();


}	

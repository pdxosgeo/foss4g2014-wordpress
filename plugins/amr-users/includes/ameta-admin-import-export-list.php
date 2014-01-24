<?php
function amr_list_import_form () { 
global $ausersadminurl;
	echo PHP_EOL.'<br /><br />';
	echo PHP_EOL.'<div id="icon-tools" class="icon32"><br/></div>'.PHP_EOL;	
	echo PHP_EOL.'<h3>'.__('Import a list\'s settings','amr-users').'</h3>';
	echo '<p><em>'.__('Imported settings must be from a compatible system.','amr-users');
	echo ' '.__('Ensure any fields used by the list are in your database and have been "found".','amr-users').'</em></p>';
	echo '<p id="async-upload-wrap">
	<form enctype="multipart/form-data" action="'.$ausersadminurl.'?page=ameta-admin-general.php&tab=overview'.'" method="POST">
	<input type="file" class="button" name="importfile">'
	.' <input type="submit"  value="'.__('Import').'" name="import-list" class="button-primary">
	</form><!-- end import form -->
	</p>';

}
/* ----------------------------------------------------------------------------------- */
function amr_list_export_form () {
	global $amain;
	global $aopt;
	
	if (isset ($amain['names'])) { 
		
		echo PHP_EOL.'<br /><br />';
		echo PHP_EOL.'<div id="icon-tools" class="icon32"><br/></div>'.PHP_EOL;	
		echo '<h3>';
		_e("Export a list's settings", 'amr-users'); 
		echo '</h3>';
	echo PHP_EOL."<p><select name='export-list-text'>";

	foreach ($amain['names'] as $i => $name) {
		$export_text = htmlspecialchars(amr_meta_build_export($i));
		echo PHP_EOL.'<option value="'.$export_text.'">'.$i.' '.$name.'</option>';
	}
	echo PHP_EOL."</select>".PHP_EOL;	
	echo '<input type="submit" value="'.__('Export').'" name="export-list" class="button-primary"></p>	';

	}
}
/* ----------------------------------------------------------------------------------- */
function amr_main_to_export () { // define the list of overview settings to export
	return array( 
		// we have a historically weird structure so we have to do it this way until we risk updating 
			'sortable',
			'names',
			'html_type',
			'public' ,
			'list_avatar_size',
			'list_rows_per_page',
			'show_search',
			'show_perpage',
			'show_pagination',
			'show_headings',
			'show_csv',
			'filterable',
			'show_refresh',
			'customnav'	);
}
/* ----------------------------------------------------------------------------------- */
function amr_meta_build_export($list)	{ 
global $amain, $aopt;

		$data = array();
		$data ['version'] = $amain['version']; // 
		$toexport = amr_main_to_export ();
		foreach ($toexport as $text) {
			if (isset($amain[$text][$list])) 
				$data[$text] = $amain[$text][$list];
		}
		if (isset ($aopt['list']))  
			$data['list'] = $aopt['list'][$list];
	
		$content = serialize($data);
		return($content);
	
}
/* ----------------------------------------------------------------------------------- */
function amr_meta_handle_export()	{ 
	
	if ( isset( $_POST['export-list'] ) and isset( $_POST['export-list-text'] )  ) {
		check_admin_referer('amr-meta','amr-meta');
	
		$filename = sanitize_title(get_bloginfo('name'))."-amr-users-list.txt";
		if (amr_is_network_admin()) $filename = 'network-'.$filename;
		$content = htmlspecialchars_decode($_POST['export-list-text']);
		header("Content-Description: File Transfer");
		header("Content-type: application/txt");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Pragma: no-cache");
		header("Expires: 0");

		echo $content;
		die();
	}
	
}
/* ----------------------------------------------------------------------------------- */
function amr_meta_handle_import()	{ 
global $amain, $aopt;
	
	
	
	if ( isset( $_POST['import-list'] )  ) {
	//	require_once(ABSPATH . 'wp-admin/includes/admin.php');
		if (empty($_FILES) ) {
			amr_users_message('No file specified','amr-users');
			return;
		}
		if (empty($_FILES['importfile'])) {
			amr_users_message('Problem in upload','amr-users');
			return;
		}
		
		$file = $_FILES['importfile'];
		
		$uploaded = wp_handle_upload($file, array('test_form' => FALSE));
		
		if ( is_wp_error($uploaded) ) {
			$errors['upload_error'] = $uploaded;
		}

		if (!empty($errors)) {
			amr_users_message('There was an error uploading your file.','amr-users');
			return;
		} 
		
		if (empty($uploaded['file'])) {
			amr_users_message('No import file chosen.','amr-users');
			return;
		}
		
		$content = file_get_contents($uploaded['file']);


		//var_dump ($content);
		$data = unserialize(stripslashes($content));
		
		//$data = unserialize($content);
		
		if ((!is_array($data)) or (!isset($data['version']) )) {
			echo '<div class="error"><p>';
			_e('Invalid data in settings file','amr-users');
			echo '</p></div';
			die;
		}
		if (version_compare($data['version'], $amain['version'], '==') ) {
			amr_users_message(sprintf(__('Your plugin version is %s. Imported settings are from plugin version: %s' ,'amr-users'),
			$amain['version'],
			$data['version'])
			.' &nbsp; Yay!');			
		}
		else {
			amr_users_message(sprintf(__('Your plugin version is %s, BUT imported settings are from plugin version: %s','amr-users' ),
			$amain['version'],
			$data['version'])
			.' '.__('Please test thoroughly.','amr-users'));	
		}	
		

		$toimport = amr_main_to_export(); // get the list of overview settings
		
		$amain['names'][] = 'justimported';
		foreach ($amain['names'] as $index => $name) {
			if ($name == 'justimported' ) {
				$thisindex = $index;
			}
		}
		
		
		
		foreach ($toimport as $setting) {
			if (isset($data[$setting]))
				$amain[$setting][$thisindex] = $data[$setting];
		}
		$aopt ['list'][$thisindex] = $data['list'];
			
		ausers_update_option('amr-users',$aopt);
		ausers_update_option('amr-users-main',$amain);
			
		amr_users_message(sprintf(__('List %s will be saved with imported data','amr-users'),$thisindex));
			
	}
}
	
	
	//update_option('amr-users', $amr_options);
					
/* ---------------------------------------------------------------------*/		
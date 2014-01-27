<?php
add_action( 'ninja_forms_insert_sub', 'ninja_forms_csv_attachment' );
add_action( 'ninja_forms_update_sub', 'ninja_forms_csv_attachment' );

function ninja_forms_csv_attachment( $sub_id ){
	global $ninja_forms_processing;

	// make sure this form is supposed to attach a CSV
	if( 1 == $ninja_forms_processing->get_form_setting( 'admin_attach_csv' ) AND 'submit' == $ninja_forms_processing->get_action() ) {
		
		// convert submission id to array
		$sub_ids = array($sub_id);
		
		// create CSV content
		$csv_content = ninja_forms_export_subs_to_csv( $sub_ids, true );
		
		// create temporary file
		$path = tempnam( get_temp_dir(), 'Sub' );
		$temp_file = fopen( $path, 'r+' );
		
		// write to temp file
		fwrite( $temp_file, $csv_content );
		fclose( $temp_file );
		
		// find the directory we will be using for the final file
		$path = pathinfo( $path );
		$dir = $path['dirname'];
		$basename = $path['basename'];
		
		// create name for file
		$new_name = apply_filters( 'ninja_forms_submission_csv_name', 'ninja-forms-submission' );
		
		// remove a file if it already exists
		if( file_exists( $dir.'/'.$new_name.'.csv' ) ) {
			unlink( $dir.'/'.$new_name.'.csv' );
		}
		
		// move file
		rename( $dir.'/'.$basename, $dir.'/'.$new_name.'.csv' );
		$file1 = $dir.'/'.$new_name.'.csv';
		
		// add new file to array of existing files
		$files = $ninja_forms_processing->get_form_setting( 'admin_attachments' );
		array_push( $files, $file1 );
		$ninja_forms_processing->update_form_setting( 'admin_attachments', $files );
	}
}
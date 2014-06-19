<?php
//add_action('ninja_forms_post_process', 'ninja_forms_error_test');
function ninja_forms_error_test(){
	global $ninja_forms_processing;
	$ninja_forms_processing->add_error('error_test', 'THIS IS MY ERROR');
}
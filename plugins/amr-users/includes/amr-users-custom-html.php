<?php /* custom html for user listings */
/* This example facilitates the hcard microformat */

function amr_get_html_to_use ($type) {

	if ($type == 'table') {
		$htm['table'] = PHP_EOL.'<table id="usertable" class="widefat userlist">'.PHP_EOL;
		$htm['tablec'] = '</table>';
		$htm['thead'] = PHP_EOL.'<thead class="thead">'.PHP_EOL;
		$htm['theadc'] = '</thead>';
		$htm['tfoot'] = PHP_EOL.'<tfoot class="tfoot">'.PHP_EOL;
		$htm['tfootc'] = PHP_EOL.'</tfoot>';
		$htm['tbody'] = PHP_EOL.'<tbody>'; 
		$htm['tbodyc'] = PHP_EOL.'</tbody>';
		$htm['tr'] = PHP_EOL.'<tr';  // row per user
		$htm['trc'] = PHP_EOL.'</tr>'.PHP_EOL;
		$htm['th'] = PHP_EOL.'<th';  //leave closing off to allow for class etc
		$htm['thc'] = PHP_EOL.'</th>';
		$htm['td'] = PHP_EOL.'<td';  //leave closing off to allow for class etc
		$htm['tdc'] = PHP_EOL.'</td>';
	}
	elseif ($type == 'simple') {
		$htm['table'] = PHP_EOL.'<div class="userlist">'.PHP_EOL;
		$htm['tablec'] = '</div>';
		$htm['thead'] = PHP_EOL.'<div class="thead">'.PHP_EOL;
		$htm['theadc'] = PHP_EOL.'</div>';
		$htm['tfoot'] = PHP_EOL.'<div class="tfoot">'.PHP_EOL;
		$htm['tfootc'] = PHP_EOL.'</div>';
		$htm['tbody'] = PHP_EOL.'<div class="tbody">'; 
		$htm['tbodyc'] = PHP_EOL.'</div>'.PHP_EOL;
		$htm['tr'] = PHP_EOL.'<div';  // row per user
		$htm['trc'] = PHP_EOL.'</div>'.PHP_EOL;
		$htm['th'] = PHP_EOL.'<span';  
		$htm['thc'] = PHP_EOL.'</span>';
		$htm['td'] = PHP_EOL.'<span';  ;  //leave closing off to allow for class etc
		$htm['tdc'] = PHP_EOL.'</span>';
	}
	else { // single user view
		$htm['table'] = PHP_EOL.'<div class="userlistsingle">'.PHP_EOL;
		$htm['tablec'] = '</div>';
		$htm['thead'] = PHP_EOL.'<div class="thead">'.PHP_EOL;
		$htm['theadc'] = PHP_EOL.'</div>';
		$htm['tfoot'] = PHP_EOL.'<div class="tfoot">'.PHP_EOL;
		$htm['tfootc'] = PHP_EOL.'</div>';
		$htm['tbody'] = PHP_EOL.'<div class="tbody">'; 
		$htm['tbodyc'] = PHP_EOL.'</div>'.PHP_EOL;
		$htm['tr'] = PHP_EOL.'<div';  // row per user
		$htm['trc'] = PHP_EOL.'</div>'.PHP_EOL;
		$htm['th'] = PHP_EOL.'<span';  
		$htm['thc'] = PHP_EOL.'</span>';
		$htm['td'] = PHP_EOL.'<span';  ;  //leave closing off to allow for class etc
		$htm['tdc'] = PHP_EOL.'</span>';
	}
	return ($htm);
}

<?php 
/*
These functions relate to new shortcode/lists that will do queries only.   Testing only at moment
*/

//$wp_list_table = _get_list_table('WP_Users_List_Table');
//$wp_list_table->views(); 
add_shortcode('user_query', 'amr_users_query');

/* -----------------------------------------------------------------------------------*/


class amr_users {

	public $display_values = array ('user_login',  'user_nicename', 'user_email','country');
	
	function __construct () {  // get the options and setup the globals that we need
	
	/*	
		$users = $this->user_query();
		
		var_dump($users);
		
		$html = $this->list_html($users);
		return $html;
		*/
	}
	
	/* -----------------------------------------------------------------------------------*/
	function list_html($list) { // take an array and list as table ot html 5

	$html = '';
	foreach ($list as $line => $userobj) {
			$html .= $this->list_line_html ($line, $userobj);
			$html .='<br />';
		}
	
	//echo $html;
	return ($html);
	}

	/* -----------------------------------------------------------------------------------*/
	function list_line_html($line,  $userobj) { // take an array and list as table ot html 5

		$html = '';
		foreach ($this->display_values as $i => $col) {
			if (isset($userobj->data->$col))  // to force wp to fetch
				$value = $userobj->data->$col;
			else 
				$value =$userobj->$col;  
			
			$html .= $this->print_value ($col, $value, $userobj->data, $line);
			$html .= ' ';
		}
		return $html;
	}

	/* -----------------------------------------------------------------------------------*/
	function print_value ($col, $v, $u, $line) {
			if (function_exists('ausers_format_'.$col) ) { 
			
				$text =  (call_user_func('ausers_format_'.$col, $v, $u, $line));
			}
			else
				$text = $v;
					
			return $text;	
	}

	/* -----------------------------------------------------------------------------------*/
	function user_query(  ) { 
	/*  get all user data  assume we working with nice user meta only ?  */
	/* get the option that tells us the fields to
	display, select( include), or exclude, sort by

	if selection fields are in main table - create a 'where'
	else create a wheremeta

	*/
	global $wpdb; 

		$args['fields'] =  	
		//'all_with_meta'; // if we need meta fields
		// 'all',  
//		array('ID', 'user_login',  'user_nicename', 'user_email',  'display_name','country');
		$this->display_values;

		$args['order_by'] = 'user_login';
		
		$args = array(
			'meta_query' => array(
		//		'relation' => 'OR',
				array(
					'key'     => 'select',
					'value'   => 'One',
					'compare' => 'like'
				),
				array(
				 'key'     => $wpdb->prefix.'capabilities',
					'value'   => '"author"',
					'compare' => 'not like',
				)
			)
		);
	 
	$user_query = new WP_User_Query( $args );

	echo '<h2>Query Results</h2>';
	var_dump($user_query->results);
	return ($user_query->results);

	}
	
	}

/* -----------------------------------------------------------------------------------*/
function amr_users_query( $args ) { 
	// query the users and return the list
	$amru = new amr_users();
	$users = $amru->user_query();
	$html = $amru->list_html($users);	
	return ($html);
	
}


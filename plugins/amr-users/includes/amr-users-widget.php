<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
/*
Description: Display a sweet, concise list of events from users sources, using a list type from the amr users plugin <a href="options-general.php?page=manage_amr_users">Manage Settings Page</a> and  <a href="widgets.php">Manage Widget</a> 

*/
class amr_users_widget extends WP_widget {
    /** constructor */
    function amr_users_widget() {
		$widget_ops = array (
			'description'=>__('Users', 'amr-users' ),
			'classname'=>__('users', 'amr-users' ));
        $this->WP_Widget(false, __('User list', 'amr-users' ), $widget_ops);	
    }
	
/* ============================================================================================== */	
	function widget ($args, $instance) { /* this is the piece that actualy does the widget display */

	extract ($args, EXTR_SKIP); /* this is for the before / after widget etc*/
	extract ($instance, EXTR_SKIP); /* title list */	

	//output...
	echo $before_widget;
	echo $before_title . $title . $after_title ;
	
	echo amr_userlist(array(
	'list'				=>$list,
	'show_headings'		=>false,
	'show_search'		=> false,
	'show_perpage' 		=> false,
	'show_pagination' 	=> $instance['show_pagination'],
	'show_csv' 			=> false,
	'widget' 			=> true
	));
	
	if (!empty($instance['moretext']) and (!empty($instance['memberpage']))) {
		$url = get_page_link($instance['memberpage']);
		echo '<a title="'.__('See more','amr-users').'" href="'.$url.'">'.$instance['moretext'].'</a>';
	}
	
	
	echo $after_widget; 

	}
/* ============================================================================================== */	
	
	function update($new_instance, $old_instance) {  /* this does the update / save */
	global $amain;
	
		$instance 					= $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['moretext'] 		= strip_tags($new_instance['moretext']);
		$instance['memberpage'] 	= $new_instance['memberpage'];
		$instance['show_pagination'] = $new_instance['show_pagination'];
		
		if (!empty($new_instance['list'])) 
			$instance['list'] = strip_tags($new_instance['list']);
		else 
			$instance['list'] = '2';
		
		return $instance;

	}
	
	
/* ============================================================================================== */
	function form($instance) { /* this does the display form */
	global $amain,$ausersadminurl;
	
        $instance = wp_parse_args( (array) $instance, array( 
			'title' => __('Users','amr-users'),
			'list'=>'2',
			'show_pagination' => false,
			'memberpage' => '',
			'moretext' => __('...more','amr-users')
		));
			
		$title 				= $instance['title'];	
		$memberpage 		= $instance['memberpage'];
		$moretext			= $instance['moretext'];
		$list 				= $instance['list'];
		$show_pagination 	= $instance['show_pagination'];
		if (isset($amain['names'][$list])) 
			$name = $amain['names'][$list];
		else $name = '<b style="color:red;" >'.__('Does not exist yet!', 'amr-users').'</b>';	
		
	
?>
	<input type="hidden" id="submit" name="submit" value="1" />
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'amr-users'); ?> 
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" 
	value="<?php echo esc_attr($title); ?>" />		</label></p>

	<p><label for="<?php echo $this->get_field_id('list'); ?>"><?php _e('User List', 'amr-users'); ?> 
	<input size="3" id="<?php echo $this->get_field_id('list'); ?>" name="<?php echo $this->get_field_name('list'); ?>" type="text" 
	value="<?php echo esc_attr($list); ?>" /></label><?php echo $name;?></p>
	
	<p><label for="<?php echo $this->get_field_id('show_pagination'); ?>">
	<input type="radio" <?php if ($show_pagination) echo 'checked="checked" ';
	?> id="<?php echo $this->get_field_id('show_pagination'); 
	?>" name="<?php echo $this->get_field_name('show_pagination'); 	
	?>" value="1" /> <?php _e('Show Pagination', 'amr-users'); ?> </label><br />
	<label for="<?php echo $this->get_field_id('show_pagination'); ?>"> 
	<input type="radio" <?php if (!$show_pagination) echo 'checked="checked" ';
	?> id="<?php echo $this->get_field_id('show_pagination'); 
	?>" name="<?php echo $this->get_field_name('show_pagination'); 	
	?>" value="0" /> <?php _e('No Pagination - Top x only', 'amr-users'); ?></label>
	</p>
	
	<p><label for="<?php echo $this->get_field_id('memberpage'); ?>">
	<?php
	_e('Member page to link to for rest of users:','amr-users');
	wp_dropdown_pages( array(
	'name' => $this->get_field_name('memberpage'),
	'selected' => $memberpage,
	'show_option_none' => __('None','amr-users') ));
	?></label></p>
	
	<p><label for="<?php echo $this->get_field_id('moretext'); ?>"><?php _e('More Text', 'amr-users'); ?> 
	<input class="widefat" id="<?php echo $this->get_field_id('moretext'); ?>" name="<?php 
	echo $this->get_field_name('moretext'); ?>" type="text" value="<?php echo esc_attr($moretext); ?>" />		</label></p>	
	
<?php 
	echo '<a href="'.$ausersadminurl.'?page=ameta-admin-general.php&tab=overview'
		.'" title="'.__('Go to overview of all lists', 'amr-users').'" >'
		.__('Manage lists', 'amr-users')
		.'</a>';
	}
/* ============================================================================================== */
}


?>
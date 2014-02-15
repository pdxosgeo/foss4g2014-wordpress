<?php
// add meta box to sponsors post type for pages
function add_page_meta() {
    add_meta_box('page_url', 'Sidebar Feature', 'page_url', 'page', 'normal', 'high');
}
// The Event Location Metabox
function page_url() {
	global $post;
	echo '<input type="hidden" name="sponsormeta_noncename" id="sponsormeta_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	$URL = get_post_meta($post->ID, '_URL', true);
	echo '<input type="text" name="_URL" value="' . $URL  . '" class="widefat" />';
}
// Save the Metabox Data
function save_sponsor_url($post_id, $post) {
	if ( !wp_verify_nonce( $_POST['sponsormeta_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	$page_url['_URL'] = $_POST['_URL'];
	foreach ($page_url as $key => $value) {
		if( $post->post_type == 'revision' ) return; 
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}
add_action('save_post', 'save_sponsor_url', 1, 2);
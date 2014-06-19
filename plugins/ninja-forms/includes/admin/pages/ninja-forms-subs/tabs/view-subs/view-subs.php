<?php
add_action( 'init', 'ninja_forms_register_tab_view_subs', 999 );
function ninja_forms_register_tab_view_subs(){
	$args = array(
		'name' => __( 'View Submissions', 'ninja-forms' ),
		'page' => 'ninja-forms-subs',
		'display_function' => 'ninja_forms_tab_view_subs',
		'save_function' => 'ninja_forms_save_view_subs',
		'show_save' => false,
	);
	ninja_forms_register_tab( 'view_subs', $args );
}

function ninja_forms_tab_view_subs(){
	global $ninja_forms_fields;
	$plugin_settings = nf_get_settings();

	if ( isset( $plugin_settings['date_format'] ) AND $plugin_settings['date_format'] != '' ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'm/d/Y';
	}

	$all_forms = ninja_forms_get_all_forms();
	if ( is_array( $all_forms ) AND isset( $all_forms[0] ) ) {
		$first_form_id = $all_forms[0]['id'];
	} else {
		$first_form_id = '';
	}

	if( isset( $_REQUEST['form_id'] ) AND $_REQUEST['form_id'] == '' ) {
		unset( $_SESSION['ninja_forms_form_id'] );
		$form_id = $first_form_id;
	} else if ( isset( $_REQUEST['form_id'] ) AND $_REQUEST['form_id'] != '' ) {
		$_SESSION['ninja_forms_form_id'] = absint( $_REQUEST['form_id'] );
		$form_id = absint( $_REQUEST ['form_id'] );
	} else if ( isset( $_SESSION['ninja_forms_form_id']) AND $_SESSION['ninja_forms_form_id'] != 'all' ) {
		$form_id = $_SESSION['ninja_forms_form_id'];
	} else {
		$form_id = $first_form_id;
	}

	if( isset( $_REQUEST['sub_id'] ) AND ! empty( $_REQUEST['sub_id'] ) ) {
		$sub_id = absint( $_REQUEST['sub_id'] );
	} else {
		$sub_id = '';
	}

	if ( isset( $_REQUEST['begin_date'] ) AND !empty( $_REQUEST['begin_date'] ) ) {
		$begin_date = esc_html( $_REQUEST['begin_date'] );
	} else if ( isset( $_SESSION['ninja_forms_begin_date'] ) AND ! empty($_SESSION['ninja_forms_begin_date'] ) ){
		if ( ( isset ( $_POST['submit'] ) AND !empty( $_REQUEST['begin_date'] ) ) OR !isset ( $_POST['submit'] ) ) {
			$begin_date = $_SESSION['ninja_forms_begin_date'];
		} else {
			$begin_date = '';
		}
	} else {
		$begin_date = '';
	}

	if ( isset( $_REQUEST['end_date'] ) AND !empty($_REQUEST['end_date'] ) ) {
		$end_date = esc_html( $_REQUEST['end_date'] );
	} else if ( isset( $_SESSION['ninja_forms_end_date'] ) AND !empty($_SESSION['ninja_forms_end_date']) ){
		if ( ( isset ( $_POST['submit'] ) AND !empty( $_REQUEST['end_date'] ) ) OR !isset ( $_POST['submit'] ) ) {
			$end_date = $_SESSION['ninja_forms_end_date'];
		} else {
			$end_date = '';
		}
	} else{
		$end_date = '';
	}

	if ( isset($_REQUEST['edit_sub_form'] ) ) {
		$edit_sub_form = absint( $_REQUEST['edit_sub_form'] );
	} else {
		$edit_sub_form = '';
	}

	if ( isset( $_REQUEST['limit'] ) AND !empty( $_REQUEST['limit'] ) ) {
		$limit = absint( $_REQUEST['limit'] );
		$_SESSION['ninja_forms_limit'] = $limit;
	} else if ( isset( $_SESSION['ninja_forms_limit'] ) AND ! empty($_SESSION['ninja_forms_limit'] ) ) {
		if ( ( isset ( $_POST['submit'] ) AND !empty( $_REQUEST['limit'] ) ) OR !isset ( $_POST['limit'] ) ) {
			$limit = $_SESSION['ninja_forms_limit'];
		} else {
			$limit = 20;
		}
	} else{
		$limit = 20;
	}

	if ( $form_id == '' ) {
		?>
		<h2><?php _e( 'View Form Submissions', 'ninja-forms' );?></h2>
		<p class="description description-wide">

		</p>
		<?php
	} else {

		if ( isset( $_REQUEST['paged']) AND !empty( $_REQUEST['paged'] ) ) {
			$current_page = absint( $_REQUEST['paged'] );
		} else {
			$current_page = 1;
		}

		if ( $current_page > 1 ) {
			$start = ( ( $current_page - 1 ) * $limit );
			// if( $sub_count < $limit ){
			// 	$end = $sub_count;
			// }else{
			// 	$end = $current_page * $limit;
			// 	//$end = $end - 1;
			// }

			// if( $end > $sub_count ){
			// 	$end = $sub_count;
			// }
		} else {
			$start = 0;
			//$end = $limit;
		}

		$args = apply_filters( 'ninja_forms_view_subs_args', array(
			'form_id' => $form_id,
			'begin_date' => $begin_date,
			'end_date' => $end_date,
			'limit' => $start.','.$limit,
			'status' => 1,
			//'11' => '05/06/2012',
		) );

		$sub_count = ninja_forms_get_sub_count( $args );

		$sub_count = apply_filters( 'ninja_forms_view_subs_count', $sub_count );

		$sub_results = ninja_forms_get_subs( $args );

		$sub_results = apply_filters( 'ninja_forms_view_subs_results', $sub_results );

		if( $sub_count < $limit ){
			$limit = $sub_count;
		}

		if ( isset( $_REQUEST['paged']) AND !empty( $_REQUEST['paged'] ) ) {
			$current_page = absint( $_REQUEST['paged'] );
		} else {
			$current_page = 1;
		}

		if ( $sub_count > $limit ) {
			$page_count = ceil( $sub_count / $limit );
		} else {
			$page_count = 1;
		}

		if ( $current_page > 1 ) {
			$start = ( ( $current_page - 1 ) * $limit );
			if ( $sub_count < $limit ) {
				$end = $sub_count;
			} else {
				$end = $current_page * $limit;
			}

			if ( $end > $sub_count ) {
				$end = $sub_count;
			}
		} else {
			$start = 0;
			$end = $limit;
		}

		$form_row = ninja_forms_get_form_by_id($form_id);
		$form_title = '';
		if ( is_array( $form_row ) AND ! empty( $form_row ) ) {
			if( isset( $form_row['data']['form_title'] ) ) {
				$form_title = $form_row['data']['form_title'];
			}
		}


			if ( $edit_sub_form != 1 ) {

			?>
			<div id="" class="tablenav top">
				<div class="alignleft actions">
				<select id="" class="" name="bulk_action">
					<option value=""><?php _e( 'Bulk Actions', 'ninja-forms' );?></option>
					<option value="delete"><?php _e( 'Delete', 'ninja-forms' );?></option>
					<option value="export"><?php _e( 'Export', 'ninja-forms' );?></option>
				</select>
				<input type="submit" name="submit" value="<?php _e( 'Apply', 'ninja-forms' ); ?>" class="button-secondary">
			</div>
			<div class="alignleft actions">
				<select id="" name="limit">
					<option value="20" <?php selected($limit, 20);?>>20</option>
					<option value="50" <?php selected($limit, 50);?>>50</option>
					<option value="100" <?php selected($limit, 100);?>>100</option>
					<option value="300" <?php selected($limit, 300);?>>300</option>
					<option value="500" <?php selected($limit, 500);?>>500</option>
					<option value="1000" <?php selected($limit, 500);?>>1000</option>
					<option value="5000" <?php selected($limit, 500);?>>5000</option>
				</select>
				<?php _e('Submissions Per Page', 'ninja-forms');?>
				<input type="submit" name="submit" value="<?php _e( 'Go', 'ninja-forms' ); ?>" class="button-secondary">
			</div>
				<div class="alignleft actions">
 					<input type="submit" name="submit" class="ninja-forms-download-all-subs button-secondary" value="<?php _e( 'Download All Submissions', 'ninja-forms' );?>">
				</div>
				<div id="" class="alignright navtable-pages">
					<?php
					if($sub_count != 0 AND $current_page <= $page_count){
					?>
					<span class="displaying-num"><?php if($start == 0){ echo 1; }else{ echo $start + 1; }?> - <?php echo $end;?> of <?php echo $sub_count;?> <?php if($sub_count == 1){ _e('Submission', 'ninja-forms'); }else{ _e( 'Submissions', 'ninja-forms' );}?></span>
					<?php
					}
						if( $page_count > 1 ) {

							$first_page = add_query_arg( array( 'paged' => 1 ) );
							$last_page = add_query_arg( array( 'paged' => $page_count ) );

							if ( $current_page > 1 ) {
								$prev_page = $current_page - 1;
								$prev_page = add_query_arg( array( 'paged' => $prev_page ) );
							} else {
								$prev_page = $first_page;
							}
							if ( $current_page != $page_count ) {
								$next_page = $current_page + 1;
								$next_page = add_query_arg( array( 'paged' => $next_page ) );
							} else {
								$next_page = $last_page;
							}

					?>
					<span class="pagination-links">
						<a class="first-page disabled" title="<?php _e( 'Go to the first page', 'ninja-forms' ); ?>" href="<?php echo $first_page;?>">«</a>
						<a class="prev-page disabled" title="<?php _e( 'Go to the previous page', 'ninja-forms' ); ?>" href="<?php echo $prev_page;?>">‹</a>
						<span class="paging-input"><input class="current-page" title="<?php _e( 'Current page', 'ninja-forms' ); ?>" type="text" name="paged" value="<?php echo $current_page;?>" size="2"> of <span class="total-pages"><?php echo $page_count;?></span></span>
						<a class="next-page" title="<?php _e( 'Go to the next page', 'ninja-forms' ); ?>" href="<?php echo $next_page;?>">›</a>
						<a class="last-page" title="<?php _e( 'Go to the last page', 'ninja-forms' ); ?>" href="<?php echo $last_page;?>">»</a>
					</span>
					<?php
						}
					?>
				</div>
			</div>
			<?php
			} else {
				$back_link = remove_query_arg( array( 'edit_sub_form' ) );
			?>
			<div id="" class="">
				<a href="<?php echo $back_link;?>" class="button-secondary"><?php _e( 'Back To Submissions', 'ninja-forms' ); ?></a>
			</div>
			<?php
			}
			?>

			<table border="1px" class="wp-list-table widefat fixed posts">
			<?php
			//Grab the first few fields attached to our form so that we can create column headers.
			$field_results = ninja_forms_get_fields_by_form_id($form_id);
			$col_count = 0;
			if ( is_array( $field_results ) AND ! empty( $field_results ) AND $edit_sub_form != 1 ) {
				foreach( $field_results as $key => $field ) {
					$field_type = $field['type'];
					if ( isset( $ninja_forms_fields[$field_type] ) ) {
						$reg_field = $ninja_forms_fields[$field_type];
						if ( ! $reg_field['process_field'] OR ! $reg_field['save_sub'] ) {
							unset($field_results[$key]);
						} else {
							if ( $col_count < 2 ) {
								$col_count++;
							}
						}
					}
				}

				$field_results = array_values( $field_results );
				$field_results = apply_filters( 'ninja_forms_view_subs_table_header', $field_results, $form_id );

				?>
				<thead>
					<tr>
						<th class="check-column"><input type="checkbox" id="" class="ninja-forms-select-all" title="ninja-forms-subs-bulk-action"></th>
						<th><?php _e('Date', 'ninja-forms');?></th>
						<?php
						do_action( 'ninja_forms_view_sub_table_header', $form_id );

				$x = 0;
				while( $x <= $col_count ){
					if( isset( $field_results[$x]['data']['label'] ) ) {

				?>
						<th><?php echo $field_results[$x]['data']['label'];?></th>
				<?php
					}
					$x++;
				}
				?>
					</tr>
				</thead>
		<?php
			}
		?>
				<tbody id="ninja_forms_subs_tbody">
		<?php
		if( is_array( $sub_results ) AND ! empty( $sub_results ) AND $edit_sub_form != 1 AND $current_page <= $page_count ){

			for ( $i = 0; $i < $limit; $i++) {
				if ( isset ( $sub_results[$i] ) ) {
					$sub = $sub_results[$i];
					$data = apply_filters( 'ninja_forms_view_sub_data', $sub['data'], $sub['id'] );
					?>
					<tr id="ninja_forms_sub_<?php echo $sub['id'];?>_tr">
						<th scope="row" class="check-column">
							<input type="checkbox" id="" name="ninja_forms_sub[]" value="<?php echo $sub['id'];?>" class="ninja-forms-subs-bulk-action">
						</th>
						<td>
							<?php
								$date = $sub['date_updated'];
								$date = strtotime($date);
								$date = date($date_format, $date);
								echo $date;
							?>
							<div class="row-actions">
								<?php
								/**
								 * ninja_forms_sub_table_row_actions hook
								 * hook in here to allow extra row actions
								 *
								 * @hooked ninja_forms_sub_table_row_actions_edit - 10
								 * @hooked ninja_forms_sub_table_row_actions_delete - 20
								 * @hooked ninja_forms_sub_table_row_actions_export - 30
								 */
								$row_actions = apply_filters( 'ninja_forms_sub_table_row_actions', array(), $data, $sub['id'], $form_id );
								echo implode(" | ", $row_actions);
								?>
							</div>
						</td>
					<?php
						do_action( 'ninja_forms_view_sub_table_row', $form_id, $sub['id'] );
						$x = 0;
						while($x <= $col_count){
							if(isset($field_results[$x]['id'])){
							$field_id = $field_results[$x]['id'];
						?>

							<td id="ninja_forms_sub_<?php echo $sub['id'];?>_field_<?php echo $field_id;?>">
							<?php
								if ( is_array( $data ) ) {
									foreach( $data as $d ) {
										if ( $field_id == $d['field_id'] ) {
											/**
											 * ninja_forms_view_sub_td hook
											 * hook in here to format the submission table data cells
											 *
											 * @hooked ninja_forms_strip_sub_td_slashes - 10
											 * @hooked ninja_forms_strip_sub_td_tags - 20
											 */
											$user_value = apply_filters('ninja_forms_view_sub_td', $d['user_value'], $d['field_id'], $sub['id'] );

											if(is_array($user_value) AND !empty($user_value)){
												$y = 1;
												foreach($user_value as $val){
													echo ninja_forms_stripslashes_deep($val);
													if($y != count($user_value)){
														echo ", ";
													}
													$y++;
												}
											}else{
												echo stripslashes($user_value);
											}
										}
									}
								}
							?>
							</td>
						<?php
							}
							$x++;
						}
					?>

					</tr>
					<?php
				}
			}
		} else if ( $edit_sub_form == 1 ) {
			$sub_row = ninja_forms_get_sub_by_id($sub_id);
			$data = $sub_row['data'];
			$date_updated = strtotime($sub_row['date_updated']);
			$date_updated = date($date_format, $date_updated);
			$sub_status = $sub_row['status'];


			?>
				<input type="hidden" name="_sub_id" value="<?php echo $sub_id;?>">
				<input type="hidden" name="_ninja_forms_edit_sub" value="1">
				<input type="hidden" name="_ninja_forms_sub_status" value="<?php echo $sub_status;?>">
				<input type="hidden" name="_form_id" value="<?php echo $form_id;?>">
				<?php

				add_filter('ninja_forms_field', 'ninja_forms_edit_sub_default_value', 15, 2);
				add_filter('ninja_forms_field', 'ninja_forms_edit_sub_hide_fields', 99, 2);
				add_filter( 'ninja_forms_display_form_form_data', 'ninja_forms_edit_sub_remove_ajax' );
				remove_action('ninja_forms_display_before_fields', 'ninja_forms_display_req_items');
				remove_action('ninja_forms_display_open_form_tag', 'ninja_forms_display_open_form_tag');
				remove_action('ninja_forms_display_close_form_tag', 'ninja_forms_display_close_form_tag');
				remove_action( 'ninja_forms_display_before_form', 'ninja_forms_display_response_message' );
				remove_action('ninja_forms_display_after_open_form_tag', 'ninja_forms_display_hidden_fields');
				ninja_forms_display_form($form_id);
				?>
				<tr id="">
					<td colspan="2"><input type="submit" name="submit" value="<?php _e( 'Save Submission', 'ninja-forms' ); ?>" class="button-primary"></td>
				</tr>
			<?php
		} else {
			?>
			<tr id="ninja_forms_subs_empty" style="">
				<td colspan="7">
					<?php _e( 'No submissions found', 'ninja-forms' ); ?>
				</td>
			</tr>
			<?php
		}
			?>

				</tbody>
				<?php
			//Grab the first few fields attached to our form so that we can create column headers.

			//$field_results = ninja_forms_get_fields_by_form_id($form_id);
			//$col_count = 0;
			if(is_array($field_results) AND !empty($field_results) AND $edit_sub_form != 1){
			/*
				foreach($field_results as $key => $field){
					$field_type = $field['type'];
					$reg_field = $ninja_forms_fields[$field_type];
					if(!$reg_field['process_field'] OR !$reg_field['save_sub']){
						unset($field_results[$key]);
					}else{
						if($col_count < 2){
							$col_count++;
						}
					}
				}

				*/
				?>
				<tfoot>
					<tr>
						<th class="check-column"><input type="checkbox" id="" class="ninja-forms-select-all" title="ninja-forms-subs-bulk-action"></th>
						<th><?php _e( 'Date', 'ninja-forms' ); ?></th>
				<?php
						do_action( 'ninja_forms_view_sub_table_header', $form_id );
				$x = 0;
				while($x <= $col_count){
					if(isset($field_results[$x]['data']['label'])){
				?>
						<th><?php echo $field_results[$x]['data']['label'];?></th>
				<?php
					}
					$x++;
				}
				?>
					</tr>
				</tfoot>
		<?php
			}
			?>
			</table>
		<div id="ninja_forms_sub_info_wrap" class="form-section" style="display:none;">
			<a href="#" id="" class="ninja-forms-back-sub"><?php _e( 'Back', 'ninja-forms' ); ?></a>
			<br />
			<br />
			<div id="ninja_forms_sub_info">


			</div>
			<input type="button" id="ninja_forms_edit_sub" value="<?php _e( 'Save', 'ninja-forms' ); ?>" class="button-primary"> &nbsp;&nbsp; <span id="ninja_forms_edit_sub_loading" style="display:none;"><img src="<?php echo NINJA_FORMS_URL."/images/loading.gif";?>" alt="loading"></span>
		</div>
			<?php

	}
}

if( isset ( $_POST['_ninja_forms_edit_sub'] ) AND absint( $_POST['_ninja_forms_edit_sub'] ) == 1 ) {
	add_action( 'init', 'ninja_forms_setup_processing_class', 5 );
	add_action( 'init', 'ninja_forms_set_save_sub' );
	add_action( 'init', 'ninja_forms_edit_sub_remove_ajax_processing', 7 );
	add_action( 'init', 'ninja_forms_edit_sub_pre_process', 999 );
}

function ninja_forms_edit_sub_remove_ajax_processing(){
	global $ninja_forms_processing;

	$ninja_forms_processing->update_form_setting( 'ajax', 0 );
}


function ninja_forms_edit_sub_pre_process(){
	global $ninja_forms_processing;

	do_action( 'ninja_forms_edit_sub_pre_process' );
	if( !$ninja_forms_processing->get_all_errors() ){
		ninja_forms_edit_sub_process();
	}
}

function ninja_forms_edit_sub_process(){
	global $ninja_forms_processing;
	do_action( 'ninja_forms_edit_sub_process' );
	if( !$ninja_forms_processing->get_all_errors() ){
		ninja_forms_edit_sub_post_process();
	}
}

function ninja_forms_edit_sub_post_process(){
	global $ninja_forms_processing;
	do_action( 'ninja_forms_edit_sub_post_process' );
}

add_action( 'init', 'ninja_forms_register_edit_sub_save_values' );
function ninja_forms_register_edit_sub_save_values(){
	add_action( 'ninja_forms_edit_sub_post_process', 'ninja_forms_edit_sub_save_values' );
}

function ninja_forms_edit_sub_save_values(){
	global $ninja_forms_processing;

	$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
	$form_id = $ninja_forms_processing->get_form_ID();
	//$user_id = $ninja_forms_processing->get_user_ID();

	$sub_row = ninja_forms_get_sub_by_id( $sub_id );
	if( isset( $sub_row['status'] ) ){
		$status = $sub_row['status'];
	}else{
		$status = '';
	}

	if( isset( $sub_row['action'] ) ){
		$action = $sub_row['action'];
	}else{
		$action = '';
	}

	$field_data = $ninja_forms_processing->get_all_fields();
	$sub_data = array();

	if ( is_array($field_data ) AND ! empty( $field_data ) ) {
		foreach( $field_data as $field_id => $user_value ) {
			array_push( $sub_data, array( 'field_id' => $field_id, 'user_value' => $user_value ) );
		}
	}

	$args = array(
		'form_id' => $form_id,
		//'user_id' => $user_id,
		'status' => $status,
		'action' => $action,
		'data' => serialize( $sub_data ),
		'sub_id' => $sub_id,
	);

	$args = apply_filters( 'ninja_forms_edit_sub_args', $args );

	ninja_forms_update_sub($args);
}

function ninja_forms_save_view_subs( $form_id, $data = array() ){
	global $ninja_forms_admin_update_message;
	$plugin_settings = nf_get_settings();
	if( isset( $_POST['submit'] ) AND $_REQUEST['page'] == 'ninja-forms-subs' ){
		switch( $_POST['submit'] ){
			case __( 'Apply', 'ninja-forms' ):
				if ( isset( $_POST['bulk_action'] ) ){
					if ( $_POST['bulk_action'] == 'delete' ){
						if ( isset( $_POST['ninja_forms_sub'] ) AND is_array( $_POST['ninja_forms_sub'] ) AND !empty( $_POST['ninja_forms_sub'] ) ){
							$subs = ninja_forms_esc_html_deep( $_POST['ninja_forms_sub'] );
							foreach( $subs as $sub_id ){
								ninja_forms_delete_sub($sub_id);
							}

							$ninja_forms_admin_update_message = count( $_POST['ninja_forms_sub'] ).' ';

							if ( count( $_POST['ninja_forms_sub'] ) > 1 ) {
								$ninja_forms_admin_update_message .= __( 'Submissions Deleted', 'ninja-forms' );
							} else {
								$ninja_forms_admin_update_message .= __( 'Submission Deleted', 'ninja-forms' );
							}
						}
					} elseif ( $_POST['bulk_action'] == 'export' ) {
						if ( isset($_POST['ninja_forms_sub'] ) AND is_array( $_POST['ninja_forms_sub'] ) AND !empty( $_POST['ninja_forms_sub'] ) ){
							$subs = ninja_forms_esc_html_deep( $_POST['ninja_forms_sub'] );
							ninja_forms_export_subs_to_csv( $subs );
						}
					}
				}
				break;
			case __( 'Download All Submissions', 'ninja-forms' ):

				if ( isset( $plugin_settings['date_format'] ) AND $plugin_settings['date_format'] != '' ) {
					$date_format = $plugin_settings['date_format'];
				} else {
					$date_format = 'm/d/Y';
				}
				if( isset( $_REQUEST['form_id'] ) AND !empty( $_REQUEST['form_id'] ) ){
					$form_id = absint( $_REQUEST['form_id'] );
				} else {
					$form_id = '';
				}

				if ( isset( $_REQUEST['ninja_forms_begin_date'] ) AND !empty( $_REQUEST['ninja_forms_begin_date'] ) ){
					$begin_date = esc_html( $_REQUEST['ninja_forms_begin_date'] );
				} else {
					$begin_date = '';
				}

				if ( isset( $_REQUEST['ninja_forms_end_date'] ) AND !empty( $_REQUEST['ninja_forms_end_date'] ) ){
					$end_date = esc_html( $_REQUEST['ninja_forms_end_date'] );
				} else {
					$end_date = '';
				}
				$args = array(
					'form_id' => $form_id,
					'begin_date' => $begin_date,
					'end_date' => $end_date,
					//'status' => 1,
					//'4' => 'unchecked',
				);
				$sub_results = ninja_forms_get_subs($args);
				$sub_results = apply_filters( 'ninja_forms_download_all_subs_results', $sub_results );
				if ( is_array( $sub_results ) AND ! empty( $sub_results ) ) {
					$sub_ids = array();
					foreach($sub_results as $sub){
						$sub_ids[] = $sub['id'];
					}
					ninja_forms_export_subs_to_csv($sub_ids);
				}
				break;
			case __( 'Save Sub', 'ninja-forms' ):
				break;
			case __( 'View Submissions', 'ninja-forms' ):
				break;
		}
	}
}

function ninja_forms_set_save_sub() {
	global $ninja_forms_processing;
	$ninja_forms_processing->update_form_setting( 'sub_id', absint( $_REQUEST['_sub_id'] ) );
	$ninja_forms_processing->set_action( 'edit_sub' );
}

function ninja_forms_edit_sub_default_value( $data, $field_id ) {
	$sub_id = absint( $_REQUEST['sub_id'] );
	$sub_row = ninja_forms_get_sub_by_id($sub_id);
	$sub_data = $sub_row['data'];
	if(is_array($sub_data) AND !empty($sub_data)){
		foreach($sub_data as $d){
			if($d['field_id'] == $field_id){
				$data['default_value'] = $d['user_value'];
				break;
			}
		}
	}

	return $data;
}

function ninja_forms_edit_sub_hide_fields( $data, $field_id ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_data = $field_row['data'];
	$field_type = $field_row['type'];
	$type = $ninja_forms_fields[$field_type];
	$process_field = $type['process_field'];
	if( ! $process_field ) {
		$data['show_field'] = false;
	}
	return $data;
}

function ninja_forms_view_subs_default_filter( $sub_results ) {
	if ( is_array( $sub_results ) AND !empty( $sub_results ) ){
		$tmp_array = array();
		for ($i=0; $i < count( $sub_results ); $i++) {
			if ( $sub_results[$i]['status'] == 1 ){
				$tmp_array[] = $sub_results[$i];
			}
		}
		$sub_results = $tmp_array;
	}

	return $sub_results;
}

add_filter( 'ninja_forms_view_subs_results', 'ninja_forms_view_subs_default_filter' );
add_filter( 'ninja_forms_download_all_subs_results', 'ninja_forms_view_subs_default_filter' );

function ninja_forms_edit_sub_remove_ajax( $form ){
	$form['data']['ajax'] = 0;
	return $form;
}


/**
 * Add an edit link in the submission table
 */
function ninja_forms_sub_table_row_actions_edit( $row_actions, $data, $sub_id, $form_id ) {

	// create the edit link
	$edit_link = add_query_arg(array('edit_sub_form' => 1, 'sub_id' => $sub_id, 'form_id' => $form_id));

	// turn on the output buffer
	ob_start();
	?>
	<span class="edit"><a href="<?php echo $edit_link;?>" id="ninja_forms_sub_<?php echo $sub_id;?>" class="ninja-forms-view-sub"><?php _e('Edit', 'ninja-forms' ); ?></a></span>
	<?php
	$action = ob_get_clean();

	// return the new html with the rest of the $row_actions array
	$row_actions['edit'] = $action;
	return $row_actions;

}
add_filter( 'ninja_forms_sub_table_row_actions', 'ninja_forms_sub_table_row_actions_edit', 10, 4 );


/**
 * Add a delete link in the submission table
 */
function ninja_forms_sub_table_row_actions_delete( $row_actions, $data, $sub_id, $form_id ) {

	// turn on the output buffer
	ob_start();
	?>
	<span class="trash"><a href="#" id="ninja_forms_sub_<?php echo $sub_id;?>" class="ninja-forms-delete-sub"><?php _e( 'Delete', 'ninja-forms' ); ?></a></span>
	<?php
	$action = ob_get_clean();

	// return the new html with the rest of the $row_actions array
	$row_actions['delete'] = $action;
	return $row_actions;

}
add_filter( 'ninja_forms_sub_table_row_actions', 'ninja_forms_sub_table_row_actions_delete', 20, 4 );


/**
 * Add an export link in the submission table
 */
function ninja_forms_sub_table_row_actions_export( $row_actions, $data, $sub_id, $form_id ) {

	// create the csv download link
	$csv_download_link = add_query_arg(array('ninja_forms_export_subs_to_csv' => 1, 'sub_id' => $sub_id, 'form_id' => $form_id));

	// turn on the output buffer
	ob_start();
	?>
	<span class="export"><a href="<?php echo $csv_download_link;?>" id="ninja_forms_sub_<?php echo $sub_id;?>" class="ninja-forms-export-sub"><?php _e( 'Export to CSV', 'ninja-forms' ); ?></a></span>
	<?php
	$action = ob_get_clean();

	// return the new html with the rest of the $row_actions array
	$row_actions['export'] = $action;
	return $row_actions;

}
add_filter( 'ninja_forms_sub_table_row_actions', 'ninja_forms_sub_table_row_actions_export', 30, 4 );


/**
 * Remove slashes from the submission form td
 *
 * @param  $field_value - the value of the field
 * @param  $field_id    - the field id
 * @param  $sub_id      - the submission id
 * @return string       - the value of the user history field
 */
function ninja_forms_strip_sub_td_slashes( $field_value, $field_id, $sub_id ) {

	// remove slashes
	$field_value = ninja_forms_stripslashes_deep( $field_value );
	return $field_value;
}
add_filter( 'ninja_forms_view_sub_td', 'ninja_forms_strip_sub_td_slashes', 10, 3 );


/**
 * Remove tags from the submission form td
 *
 * @param  $field_value - the value of the field
 * @param  $field_id    - the field id
 * @param  $sub_id      - the submission id
 * @return string       - the value of the user history field
 */
function ninja_forms_strip_sub_td_tag( $field_value, $field_id, $sub_id ) {

	// remove tags
	$field_value = ninja_forms_strip_tags_deep( $field_value );
	return $field_value;
}
add_filter( 'ninja_forms_view_sub_td', 'ninja_forms_strip_sub_td_tag', 20, 3 );


?>

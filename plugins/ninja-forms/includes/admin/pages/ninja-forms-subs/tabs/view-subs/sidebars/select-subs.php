<?php
add_action('init', 'ninja_forms_register_sidebar_select_subs');

function ninja_forms_register_sidebar_select_subs(){
	$args = array(
		'name' => __( 'Find Submissions', 'ninja-forms' ),
		'page' => 'ninja-forms-subs',
		'tab' => 'view_subs',
		'display_function' => 'ninja_forms_sidebar_select_subs',
		'save_function' => 'ninja_forms_save_sidebar_select_subs',
	);
	ninja_forms_register_sidebar('select_subs', $args);

	if( is_admin() AND isset( $_REQUEST['page'] ) AND $_REQUEST['page'] == 'ninja-forms-subs' ){
		if( !isset( $_REQUEST['paged'] ) AND !isset( $_REQUEST['form_id'] ) ){
			unset( $_SESSION['ninja_forms_form_id'] );
			unset( $_SESSION['ninja_forms_begin_date'] );
			unset( $_SESSION['ninja_forms_end_date'] );
		}
	}
}

function ninja_forms_sidebar_select_subs(){
	$form_results = ninja_forms_get_all_forms();

	if( isset( $_REQUEST['form_id']) AND $_REQUEST['form_id'] == '' ){
		unset($_SESSION['ninja_forms_form_id']);
		$form_id = '';
	}else if( isset( $_REQUEST['form_id'] ) AND $_REQUEST['form_id'] != '' ){
		$_SESSION['ninja_forms_form_id'] = absint( $_REQUEST['form_id'] );
		$form_id = absint( $_REQUEST['form_id'] );
	}else if( isset( $_SESSION['ninja_forms_form_id']) AND $_SESSION['ninja_forms_form_id'] != 'all' ){
		$form_id = $_SESSION['ninja_forms_form_id'];
	}else{
		$form_id = '';
	}
	
	if( isset( $_REQUEST['begin_date'] ) AND !empty( $_REQUEST['begin_date'] ) ){
		$begin_date = esc_html( $_REQUEST['begin_date'] );
		$_SESSION['ninja_forms_begin_date'] = esc_html( $_REQUEST['begin_date'] );
	}else if( isset( $_SESSION['ninja_forms_begin_date'] ) AND !empty($_SESSION['ninja_forms_begin_date'] ) ){
		if ( ( isset ( $_POST['submit'] ) AND !empty( $_REQUEST['begin_date'] ) ) OR !isset ( $_POST['submit'] ) ) {
			$begin_date = $_SESSION['ninja_forms_begin_date'];
		} else {
			$begin_date = '';
		}
	}else{
		$begin_date = '';
	}

	if(isset($_REQUEST['end_date']) AND !empty($_REQUEST['end_date'])){
		$end_date = esc_html( $_REQUEST['end_date'] );
		$_SESSION['ninja_forms_end_date'] = esc_html( $_REQUEST['end_date'] );
	}else if( isset( $_SESSION['ninja_forms_end_date'] ) AND !empty( $_SESSION['ninja_forms_end_date'] ) ){
		if ( ( isset ( $_POST['submit'] ) AND !empty( $_REQUEST['end_date'] ) ) OR !isset ( $_POST['submit'] ) ) {
			$end_date = $_SESSION['ninja_forms_end_date'];
		} else {
			$end_date = '';
		}
	}else{
		$end_date = '';
	}

	if(is_array($form_results)){
	?>
		<h4><?php _e( 'Select A Form', 'ninja-forms' );?></h4>	
		<p class="description">
			<select name="form_id" id="" class="">
			<?php
			foreach($form_results as $form){
				$data = $form['data'];
				$form_title = $data['form_title'];
				?>
				<option value="<?php echo $form['id'];?>" <?php if($form_id == $form['id']){ echo 'selected';}?>><?php echo $form_title;?></option>
				<?php
			}
			?>
			</select>
		</p>
		<h4><?php _e( 'Date Range', 'ninja-forms' );?> - <span class="howto">(<?php _e( 'Optional', 'ninja-forms' );?>)</span></h4>
		
		<p class="description">
			<?php _e( 'Begin Date', 'ninja-forms' );?>: <input type="text" id="" name="begin_date" class="ninja-forms-admin-date" value="<?php echo $begin_date;?>">
			<br />
			mm/dd/yyyy
		</p>
		
		<p class="description">
			<?php _e( 'End Date', 'ninja-forms' );?>: <input type="text" id="" name="end_date" class="ninja-forms-admin-date" value="<?php echo $end_date;?>">
			<br />
			mm/dd/yyyy
		</p>
		<br />
		<p class="description">
			<?php _e( 'If both Begin Date and End Date are left blank, all submissions will be displayed.', 'ninja-forms' );?>
		</p>
	</form>
<?php
	}
}

add_action( 'init', 'ninja_forms_register_select_sub_submit', 999 );
function ninja_forms_register_select_sub_submit(){
	$args = array(
		'page' => 'ninja-forms-subs',
		'tab' => 'view_subs',
		'sidebar' => 'select_subs',
		'type' => 'submit',
		'label' => __( 'View Submissions', 'ninja-forms' ),
		'class' => 'button-secondary',
	);
	if( function_exists( 'ninja_forms_register_sidebar_option' ) ){
		ninja_forms_register_sidebar_option( 'view_subs', $args );
	}
}

function ninja_forms_save_sidebar_select_subs(){
	/*
	remove_all_actions('ninja_forms_before_pre_process');
	remove_all_actions('ninja_forms_pre_process');
	remove_all_actions('ninja_forms_process');
	remove_all_actions('ninja_forms_post_process');
	*/
}
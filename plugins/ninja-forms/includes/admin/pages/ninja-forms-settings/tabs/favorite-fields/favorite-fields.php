<?php
//add_action('init', 'ninja_forms_register_tab_fav_settings');

function ninja_forms_register_tab_fav_settings(){
	$args = array(
		'name' => __( 'Favorite Fields', 'ninja-forms' ),
		'page' => 'ninja-forms-settings',
		'display_function' => 'ninja_forms_tab_fav_settings',
		'save_function' => 'ninja_forms_save_fav_settings',
	);
	ninja_forms_register_tab('favorite_fields', $args);

}

function ninja_forms_tab_fav_settings(){
?>
	<h2><?php _e( 'Favorite Field Settings', 'ninja-forms' );?></h2>
	<p class="description description-wide">
		<h3 class="section-title"><?php _e( 'Date Settings', 'ninja-forms' );?>:</h3>
		<div class="form-section">
			<label for="">
				<input type="text" class="code" name="form_title" id="" value="" />
				<img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
				<br />
			</label>
		</div>
	</p>
<?php
}
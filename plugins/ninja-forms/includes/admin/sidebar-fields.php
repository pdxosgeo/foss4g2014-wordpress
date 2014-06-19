<?php
function ninja_forms_sidebar_display_fields($slug){
	global $ninja_forms_fields, $current_tab;
	if(is_array($ninja_forms_fields) AND isset($_REQUEST['form_id'])){
		foreach($ninja_forms_fields as $field_slug => $field){
			if($field['sidebar'] == $slug){
				if(isset($field['limit'])){
					$limit = $field['limit'];
				}else{
					$limit = '';
				}
				?>
				<p class="button-controls">
					<a class="button-secondary ninja-forms-new-field" id="<?php _e($field_slug, 'ninja-forms');?>" name="_<?php echo $limit;?>"  href="#"><?php _e($field['name'], 'ninja-forms');?></a>
				</p>
				<?php
			}
		}
	}
}
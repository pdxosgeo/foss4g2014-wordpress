<?php

add_action( 'init', 'ninja_forms_register_inside_label_hidden' );
function ninja_forms_register_inside_label_hidden(){
	add_action( 'ninja_forms_display_after_field_function', 'ninja_forms_inside_label_hidden', 10, 2 );
}

function ninja_forms_inside_label_hidden( $field_id, $data ){
	if( isset( $data['label_pos'] ) AND $data['label_pos'] == 'inside' ){
		$plugin_settings = get_option( 'ninja_forms_settings' );

		if( isset( $data['label'] ) ){
			$label = $data['label'];
		}else{
			$label = '';
		}

		if(isset($data['req'])){
			$req = $data['req'];
		}else{
			$req = '';
		}

		if(isset($plugin_settings['req_field_symbol'])){
			$req_symbol = $plugin_settings['req_field_symbol'];
		}else{
			$req_symbol = '';
		}

		if($req == 1){
			$req_span = "<span class='ninja-forms-req-symbol'>$req_symbol</span>";
		}else{
			$req_span = '';
		}

		echo $req_span;
		?>
		<input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>_label_hidden" value="<?php echo $label;?>">
		<?php
	}
}
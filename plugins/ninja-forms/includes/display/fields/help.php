<?php
/**
 * Outputs the HTML of the help icon if it is set to display.
 *
**/

function ninja_forms_display_field_help( $field_id, $data ){
	$plugin_settings = get_option( 'ninja_forms_settings' );

	if( isset( $data['show_help'] ) ){
		$show_help = $data['show_help'];
	}else{
		$show_help = 0;
	}

	if( isset( $data['help_text'] ) ){
		$help_text = $data['help_text'];
	}else{
		$help_text = '';
	}

	$help_text = htmlentities( $help_text );

	if($show_help){
		?>
		<img class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="<?php echo $help_text;?>">
	<?php
	}
}	

add_action( 'ninja_forms_display_field_help', 'ninja_forms_display_field_help', 10, 2 );
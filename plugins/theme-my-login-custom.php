<?php 

function tml_registration_errors( $errors ) {
	if ( empty( $_POST['first_name'] ) )
		$errors->add( 'empty_first_name', '<strong>ERROR</strong>: Please enter your first name.' );
	if ( empty( $_POST['last_name'] ) )
		$errors->add( 'empty_last_name', '<strong>ERROR</strong>: Please enter your last name.' );
	return $errors;
}
add_filter( 'registration_errors', 'tml_registration_errors' );

function tml_user_register( $user_id ) {
	if ( !empty( $_POST['first_name'] ) )
		update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
	if ( !empty( $_POST['last_name'] ) )
		update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
}
add_action( 'user_register', 'tml_user_register' );
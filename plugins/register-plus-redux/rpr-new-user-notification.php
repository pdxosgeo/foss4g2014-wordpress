<?php
if ( function_exists( 'wp_new_user_notification' ) ) {
	if ( $rpr_admin_menu instanceof RPR_Admin_Menu ) add_action( 'admin_notices', array( $rpr_admin_menu, 'rpr_new_user_notification_warning' ), 10, 0 );
}

// Called after user completes registration from wp-login.php
// Called after admin creates user from wp-admin/user-new.php
// Called after admin creates new site, which also creates new user from wp-admin/network/edit.php (MS)
// Called after admin creates user from wp-admin/network/edit.php (MS)
if ( !function_exists( 'wp_new_user_notification' ) ) {
	/*.void.*/ function wp_new_user_notification( /*.int.*/ $user_id, $plaintext_pass = '' ) {
		global $pagenow;
		global $register_plus_redux;

		//trigger_error( sprintf( __( 'Register Plus Redux DEBUG: wp_new_user_notification($user_id=%s, $plaintext_pass=%s) from %s', 'register-plus-redux' ), $user_id, $plaintext_pass, $pagenow ) ); 
		if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) && !empty( $_POST['pass1'] ) )
			$plaintext_pass = stripslashes( (string) $_POST['pass1'] );
		if ( 'user-new.php' === $pagenow && !empty( $_POST['pass1'] ) )
			$plaintext_pass = stripslashes( (string) $_POST['pass1'] );
		//TODO: Code now only forces users registering to verify email, may want to add settings to have admin created users verify email too
		$verification_code = '';
		if ( 'wp-login.php' === $pagenow && '1' === $register_plus_redux->rpr_get_option( 'verify_user_email' ) ) {
			$verification_code = wp_generate_password( 20, FALSE );
			update_user_meta( $user_id, 'email_verification_code', $verification_code );
			update_user_meta( $user_id, 'email_verification_sent', gmdate( 'Y-m-d H:i:s' ) );
			$register_plus_redux->send_verification_mail( $user_id, $verification_code );
		}
		if ( ( 'wp-login.php' === $pagenow && '1' !== $register_plus_redux->rpr_get_option( 'disable_user_message_registered' ) ) || 
			( 'wp-login.php' !== $pagenow && '1' !== $register_plus_redux->rpr_get_option( 'disable_user_message_created' ) ) ) {
			if ( '1' !== $register_plus_redux->rpr_get_option( 'verify_user_email' ) && '1' !== $register_plus_redux->rpr_get_option( 'verify_user_admin' ) ) {
				$register_plus_redux->send_welcome_user_mail( $user_id, $plaintext_pass );
			}
		}
		if ( ( 'wp-login.php' === $pagenow && '1' !== $register_plus_redux->rpr_get_option( 'disable_admin_message_registered' ) ) || 
			( 'wp-login.php' !== $pagenow && '1' !== $register_plus_redux->rpr_get_option( 'disable_admin_message_created' ) ) ) {
			$register_plus_redux->send_admin_mail( $user_id, $plaintext_pass, $verification_code );
		}
	}
}
?>
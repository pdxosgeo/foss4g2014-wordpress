<?php
if ( !class_exists( 'RPR_Admin' ) ) {
	class RPR_Admin {
		public /*.void.*/ function __construct() {
			add_action( 'admin_init', array( $this, 'rpr_admin_init' ), 10, 1 ); // Runs after WordPress has finished loading but before any headers are sent.
			add_action( 'admin_init', array( $this, 'rpr_delete_unverified_users' ), 10, 1 ); // Runs after WordPress has finished loading but before any headers are sent.
		}

		public /*.void.*/ function rpr_admin_init() {
			global $register_plus_redux;
			// TODO: Write function to migrate register plus settings to redux
			// should not be in init, likely to use similar code to rename

			if ( !current_user_can( 'manage_options' ) ) return;

			if ( RPR_ACTIVATION_REQUIRED !== get_option( 'register_plus_redux_last_activated' ) ) {
				$register_plus_redux->rpr_activation();
			}

			$updated = FALSE;

			// Rename options as necessary, prior to defaulting any new options
			$rename_options = array(
				'registration_redirect' => 'registration_redirect_url'
			);
			foreach ( $rename_options as $old_name => $new_name ) {
				$old_value = $register_plus_redux->rpr_get_option( $old_name );
				$new_value = $register_plus_redux->rpr_get_option( $new_name );
				if ( NULL === $new_value && NULL !== $old_value ) {
					$register_plus_redux->rpr_set_option( $new_name, $old_value );
					$register_plus_redux->rpr_unset_option( $old_name );
					$updated = TRUE;
				}
			}

			// Load defaults for any options
			foreach ( Register_Plus_Redux::default_options() as $option => $default_value ) {
				if ( NULL === $register_plus_redux->rpr_get_option( $option ) ) {
					$register_plus_redux->rpr_set_option( $option, $default_value );
					$updated = TRUE;
				}
			}

			if ( TRUE === $updated ) $register_plus_redux->rpr_update_options( NULL );

			// Added 03/28/11 in 3.7.4 converting invitation_code_bank to own option
			/*.array[]string.*/ $nested_invitation_code_bank = $register_plus_redux->rpr_get_option( 'invitation_code_bank' );
			/*.array[]string.*/ $invitation_code_bank = get_option( 'register_plus_redux_invitation_code_bank-rv1' );
			// TODO: This may need some work, not sure isset will fire the way I think it does
			if ( FALSE === $invitation_code_bank && isset( $nested_invitation_code_bank ) ) {
				update_option( 'register_plus_redux_invitation_code_bank-rv1', $nested_invitation_code_bank );
				//TODO: Confirm old invitation codes are migrating successfully, then kill old option
				//$register_plus_redux->rpr_unset_option( 'invitation_code_bank' );
			}


			// Added 03/02/13 in 3.9.6 converting 'unverified_*' users to "Unverified" users
			/*.object.*/ $user_query = new WP_User_Query( array( 'meta_key' => 'stored_user_login' ) );
			if ( !empty( $user_query->results ) ) {
				global $wpdb;
				foreach ( $user_query->results as $user ) {			
					$stored_user_login = get_user_meta( $user->ID, 'stored_user_login', TRUE );
					$wpdb->update( $wpdb->users, array( 'user_login' => $stored_user_login ), array( 'ID' => $user->ID ) );
					$user->set_role( 'rpr_unverified' );
					delete_user_meta( $user->ID, 'stored_user_login' );
				}
			}

			// Added 03/18/13 in 3.9.9 remove capability 'rpr_can_login', rely on role 'rpr_unverified' alone
			global $wp_roles;
		 	foreach ( $wp_roles->get_names() as $role_name => $display_name ) {
		 		$wp_roles->remove_cap( $role_name, 'rpr_can_login' );
			}

			// Added 03/28/11 in 3.7.4 converting custom fields
			/*.array[]mixed.*/ $redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( empty( $redux_usermeta ) ) {
				/*.array[]mixed.*/ $redux_usermeta_rv1 = get_option( 'register_plus_redux_usermeta-rv1' );
				/*.array[]mixed.*/ $custom_fields = get_option( 'register_plus_redux_custom_fields' );
				if ( is_array( $redux_usermeta_rv1 ) ) {
					/*.array[]mixed.*/ $redux_usermeta = array();
					foreach ( $redux_usermeta_rv1 as $meta_field_rv1 ) {
						$meta_field = array();
						$meta_field['label'] = $meta_field_rv1['label'];
						$meta_field['meta_key'] = $meta_field_rv1['meta_key'];
						$meta_field['display'] = $meta_field_rv1['control'];
						$meta_field['options'] = $meta_field_rv1['options'];
						$meta_field['show_datepicker'] = '0';
						$meta_field['escape_url'] = '0';
						$meta_field['show_on_profile'] = $meta_field_rv1['show_on_profile'];
						$meta_field['show_on_registration'] = $meta_field_rv1['show_on_registration'];
						$meta_field['require_on_registration'] = $meta_field_rv1['required_on_registration'];
						if ( 'text' === $meta_field['display'] ) $meta_field['display'] = 'textbox';
						elseif ( 'date' === $meta_field['display'] ) {
							$meta_field['display'] = 'text';
							$meta_field['show_datepicker'] = '1';
						}
						elseif ( 'url' === $meta_field['display'] ) {
							$meta_field['display'] = 'text';
							$meta_field['escape_url'] = '1';
						}
						elseif ( 'static' === $meta_field['display'] ) $meta_field['display'] = 'text';
						$redux_usermeta[] = $meta_field;
					}
					// TODO: Confirm old custom fields are migrating successfully, then kill old option
					//delete_option( 'register_plus_redux_usermeta-rv1' );
					if ( !empty( $redux_usermeta ) ) update_option( 'register_plus_redux_usermeta-rv2', $redux_usermeta );
				} 
				elseif ( is_array( $custom_fields ) ) {
					/*.array[]mixed.*/ $redux_usermeta = array();
					foreach ( $custom_fields as $custom_field ) {
						$meta_field = array();
						$meta_field['label'] = $custom_field['custom_field_name'];
						$meta_field['meta_key'] = Register_Plus_Redux::sanitize_text( $custom_field['custom_field_name'] );
						$meta_field['display'] = $custom_field['custom_field_type'];
						$meta_field['options'] = $custom_field['custom_field_options'];
						$meta_field['show_datepicker'] = '0';
						$meta_field['escape_url'] = '0';
						$meta_field['show_on_profile'] = $custom_field['show_on_profile'];
						$meta_field['show_on_registration'] = $custom_field['show_on_registration'];
						$meta_field['require_on_registration'] = $custom_field['required_on_registration'];
						if ( 'text' === $meta_field['display'] ) $meta_field['display'] = 'textbox';
						elseif ( 'date' === $meta_field['display'] ) {
							$meta_field['display'] = 'text';
							$meta_field['show_datepicker'] = '1';
						}
						elseif ( 'url' === $meta_field['display'] ) {
							$meta_field['display'] = 'text';
							$meta_field['escape_url'] = '1';
						}
						elseif ( 'static' === $meta_field['display'] ) $meta_field['display'] = 'text';
						$redux_usermeta[] = $meta_field;
					}
					// TODO: Confirm old custom fields are migrating successfully, then kill old option
					//delete_option( 'register_plus_redux_custom_fields' );
					if ( !empty( $redux_usermeta ) ) update_option( 'register_plus_redux_usermeta-rv2', $redux_usermeta );
				}
			}

			update_option( 'register_plus_redux_version', RPR_VERSION );
		}

		public /*.void.*/ function rpr_delete_unverified_users() {
			global $register_plus_redux;
			if ( !current_user_can( 'delete_users' ) ) return;
			if ( absint( $register_plus_redux->rpr_get_option( 'delete_unverified_users_after' ) ) > 0 ) {
				/*.object.*/ $user_query = new WP_User_Query( array( 'role' => 'rpr_unverified' ) );
				if ( !empty( $user_query->results ) ) {
					$expirationtimestamp = strtotime( '-' . absint( $register_plus_redux->rpr_get_option( 'delete_unverified_users_after' ) ) . ' days' );
					//NOTE: necessary for wp_delete_user
					if ( !function_exists( 'wp_delete_user' ) ) require_once( ABSPATH . '/wp-admin/includes/user.php' );
					foreach ( $user_query->results as $user ) {
						if ( $expirationtimestamp > strtotime( $user->user_registered ) ) {
							if ( !empty( $user->email_verification_sent ) ) {
								if ( $expirationtimestamp > strtotime( $user->email_verification_sent ) ) {
									if ( !empty( $user->email_verified ) ) {
										if ( $expirationtimestamp > strtotime( $user->email_verified ) ) {
											wp_delete_user( $user->ID );
										}
									}
									else {
										wp_delete_user( $user->ID );
									}
								}
							}
							else {
								wp_delete_user( $user->ID );
							}
						}
					}
				}
			}
		}
	}
}

if ( class_exists( 'RPR_Admin' ) ) $rpr_admin = new RPR_Admin();
?>
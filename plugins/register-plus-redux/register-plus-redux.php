<?php
/*
Author: radiok
Plugin Name: Register Plus Redux
Author URI: http://radiok.info/
Plugin URI: http://radiok.info/blog/category/register-plus-redux/
Description: Enhances the user registration process with complete customization and additional administration options.
Version: 3.9.10
Text Domain: register-plus-redux
Domain Path: /languages
*/

// NOTE: Debug, no more echoing
// trigger_error( sprintf( 'Register Plus Redux DEBUG: function($parameter=%s) from %s', print_r( $value, TRUE ), $pagenow ) ); 
// trigger_error( sprintf( 'Register Plus Redux DEBUG: function($parameter=%s)', print_r( $value, TRUE ) ) ); 

// TODO: meta key could be changed and ruin look ups
// TODO: Disable functionality in wp-signup and wp-admin around rpr_active_for_network
// TODO: Custom messages may not work with Wordpress MS as it uses wpmu_welcome_user_notification not wp_new_user_notification 
// TODO: Verify wp_new_user_notification triggers when used in MS due to the $pagenow checks

// TODO: Enhancement- Configuration to set default display_name and/or lockdown display_name
// TODO: Enhancement- Create rpr-signups table and mirror wpms
// TODO: Enhancement- Signups table needs an edit view
// TODO: Enhancement- MS users aren't being linked to a site, this is by design, as a setting to automatically add users at specified level
// TODO: Enhancement- Alter admin pages to match registration/signup
// TODO: Enhancement- Widget is lame/near worthless

define( 'RPR_VERSION', '3.9.9' );
define( 'RPR_ACTIVATION_REQUIRED', '3.9.6' );

if ( !class_exists( 'Register_Plus_Redux' ) ) {
	class Register_Plus_Redux {
		private /*.array[string]mixed.*/ $options;

		public /*.void.*/ function __construct() {
			register_activation_hook( __FILE__, array( $this, 'rpr_activation' ) );
			register_deactivation_hook( __FILE__, array( 'Register_Plus_Redux', 'rpr_uninstall' ) );
			register_uninstall_hook( __FILE__, array( 'Register_Plus_Redux', 'rpr_uninstall' ) );

			add_action( 'init', array( $this, 'rpr_i18n_init' ), 10, 1 );

			if ( !is_multisite() ) {
				add_filter( 'pre_user_login', array( $this, 'rpr_filter_pre_user_login_swp' ), 10, 1 ); // Changes user_login to user_email
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'rpr_admin_enqueue_scripts' ), 10, 1 );

			add_action( 'show_user_profile', array( $this, 'rpr_show_custom_fields' ), 10, 1 ); // Runs near the end of the user profile editing screen.
			add_action( 'edit_user_profile', array( $this, 'rpr_show_custom_fields' ), 10, 1 ); // Runs near the end of the user profile editing screen in the admin menus. 
			add_action( 'profile_update', array( $this, 'rpr_save_custom_fields' ), 10, 1 ); // Runs when a user's profile is updated. Action function argument: user ID.

			add_action( 'admin_footer-profile.php', array( $this, 'rpr_admin_footer' ), 10, 0 ); // Runs in the HTML <head> section of the admin panel of a page or a plugin-generated page.
			add_action( 'admin_footer-user-edit.php', array( $this, 'rpr_admin_footer' ), 10, 0 ); // Runs in the HTML <head> section of the admin panel of a page or a plugin-generated page.
		}

		public /*.void.*/ function rpr_activation() {
			global $wp_roles;
			add_role( 'rpr_unverified', 'Unverified' );
			update_option( 'register_plus_redux_last_activated', RPR_ACTIVATION_REQUIRED );
		}

		public static /*.void.*/ function rpr_uninstall() {
			global $wp_roles;
			remove_role( 'rpr_unverified' );
			delete_option( 'register_plus_redux_last_activated' );
		}

		public static /*.mixed.*/ function default_options( $option = '' )
		{
			$blogname = stripslashes( wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) );
			$options = array(
				'verify_user_email' => is_multisite() ? '1' : '0',
				'message_verify_user_email' => is_multisite() ? 
					__( "<h2>%user_login% is your new username</h2>\n<p>But, before you can start using your new username, <strong>you must activate it</strong></p>\n<p>Check your inbox at <strong>%user_email%</strong> and click the link given.</p>\n<p>If you do not activate your username within two days, you will have to sign up again.</p>", 'register-plus-redux' ) :
					__( 'Please verify your account using the verification link sent to your email address.', 'register-plus-redux' ),
				'verify_user_admin' => '0',
				'message_verify_user_admin' => __( 'Your account will be reviewed by an administrator and you will be notified when it is activated.', 'register-plus-redux' ),
				'delete_unverified_users_after' => is_multisite() ? 0 : 7,
				'autologin_user' => '0',

				'username_is_email' => '0',
				'double_check_email' => '0',
				'user_set_password' => '0',
				'min_password_length' => 6,
				'disable_password_confirmation' => '0',
				'show_password_meter' => '0',
				'message_empty_password' => 'Strength Indicator',
				'message_short_password' => 'Too Short',
				'message_bad_password' => 'Bad Password',
				'message_good_password' => 'Good Password',
				'message_strong_password' => 'Strong Password',
				'message_mismatch_password' => 'Password Mismatch',
				'enable_invitation_code' => '0',
				'require_invitation_code' => '0',
				'invitation_code_case_sensitive' => '0',
				'invitation_code_unique' => '0',
				'enable_invitation_tracking_widget' => '0',
				'show_disclaimer' => '0',
				'message_disclaimer_title' => 'Disclaimer',
				'require_disclaimer_agree' => '1',
				'message_disclaimer_agree' => 'Accept the Disclaimer',
				'show_license' => '0',
				'message_license_title' => 'License Agreement',
				'require_license_agree' => '1',
				'message_license_agree' => 'Accept the License Agreement',
				'show_privacy_policy' => '0',
				'message_privacy_policy_title' => 'Privacy Policy',
				'require_privacy_policy_agree' => '1',
				'message_privacy_policy_agree' => 'Accept the Privacy Policy',
				'default_css' => '1',
				'required_fields_style' => 'border:solid 1px #E6DB55; background-color:#FFFFE0;',
				'required_fields_asterisk' => '0',
				'starting_tabindex' => 0,

				/*
				'datepicker_firstdayofweek' => 6,
				'datepicker_dateformat' => 'mm/dd/yyyy',
				'datepicker_startdate' => '',
				'datepicker_calyear' => '',
				'datepicker_calmonth' => 'cur',
				*/

				'disable_user_message_registered' => '0',
				'disable_user_message_created' => '0',
				'custom_user_message' => '0',
				'user_message_from_email' => get_option( 'admin_email' ),
				'user_message_from_name' => $blogname,
				'user_message_subject' => '[' . $blogname . '] ' . __( 'Your Login Information', 'register-plus-redux' ),
				'user_message_body' => "Username: %user_login%\nPassword: %user_password%\n\n%site_url%\n",
				'send_user_message_in_html' => '0',
				'user_message_newline_as_br' => '0',
				'custom_verification_message' => '0',
				'verification_message_from_email' => get_option( 'admin_email' ),
				'verification_message_from_name' => $blogname,
				'verification_message_subject' => '[' . $blogname . '] ' . __( 'Verify Your Account', 'register-plus-redux' ),
				'verification_message_body' => "Verification URL: %verification_url%\nPlease use the above link to verify your email address and activate your account\n",
				'send_verification_message_in_html' => '0',
				'verification_message_newline_as_br' => '0',

				'disable_admin_message_registered' => '0',
				'disable_admin_message_created' => '0',
				'admin_message_when_verified' => '0',
				'custom_admin_message' => '0',
				'admin_message_from_email' => get_option( 'admin_email' ),
				'admin_message_from_name' => $blogname,
				'admin_message_subject' => '[' . $blogname . '] ' . __( 'New User Registered', 'register-plus-redux' ),
				'admin_message_body' => "New user registered on your site %blogname%\n\nUsername: %user_login%\nE-mail: %user_email%\n",
				'send_admin_message_in_html' => '0',
				'admin_message_newline_as_br' => '0'
			);
			if ( !empty( $option ) ) {
				if ( array_key_exists( $option, $options ) ) {
					return $options[$option];
				}
				else {
					//TODO: Trigger event this would be odd
					return FALSE;
				}
			}
			return $options;
		}
		
		public /*.bool.*/ function rpr_update_options( /*.array[string]mixed.*/ $options ) {
			if ( empty( $options ) && empty( $this->options ) ) return FALSE;
			if ( !empty( $options ) ) {
				update_option( 'register_plus_redux_options', $options );
				$this->options = $options;
			}
			else {
				update_option( 'register_plus_redux_options', $this->options );
			}
			return TRUE;
		}

		private /*.void.*/ function rpr_load_options( $force_refresh = FALSE ) {
			if ( empty( $this->options ) || $force_refresh === TRUE ) {
				$this->options = get_option( 'register_plus_redux_options' );
			}
			if ( empty( $this->options ) ) {
				$this->rpr_update_options( Register_Plus_Redux::default_options() );
			}
		}

		public /*.mixed.*/ function rpr_get_option( /*.string.*/ $option ) {
			if ( empty( $option ) ) return NULL;
			$this->rpr_load_options( FALSE );
			if ( array_key_exists( $option, $this->options ) ) {
				return $this->options[$option];
			}
			return NULL;
		}

		public /*.bool.*/ function rpr_set_option( /*.string.*/ $option, /*.mixed.*/ $value, $save_now = FALSE ) {
			if ( empty( $option ) ) return FALSE;
			$this->rpr_load_options( FALSE );
			$this->options[$option] = $value;
			if ( $save_now === TRUE ) {
				$this->rpr_update_options( NULL );
			}
			return TRUE;
		}

		public /*.bool.*/ function rpr_unset_option( /*.string.*/ $option, $save_now = FALSE ) {
			if ( empty( $option ) ) return FALSE;
			$this->rpr_load_options( FALSE );
			unset( $this->options[$option] );
			if ( $save_now === TRUE ) {
				$this->rpr_update_options( NULL );
			}
			return TRUE;
		}

		public static /*.string.*/ function sanitize_text( /*.string.*/ $text ) {
			$text = str_replace( ' ', '_', $text );
			$text = strtolower( (string) $text );
			return sanitize_html_class( $text );
		}

		public /*.void.*/ function rpr_update_user_meta( /*.int.*/ $user_id, /*.array[string]mixed.*/ $meta_field, /*.mixed.*/ $meta_value ) {
			// convert array to string
			if ( is_array( $meta_value ) ) { 
				foreach ( $meta_value as &$value ) {
					$value = sanitize_text_field( $value );
				}
				$meta_value = implode( ',', $meta_value );
			}
			// sanitize url
			if ( '1' === $meta_field['escape_url'] ) {
				$meta_value = esc_url_raw( (string) $meta_value );
				$meta_value = preg_match( '/^(https?|ftps?|mailto|news|irc|gopher|nntp|feed|telnet):/is', $meta_value ) > 0 ? (string) $meta_value : 'http://' . (string) $meta_value;
			}
			
			$valid_value = TRUE;
			if ( 'text' === $meta_field['display'] ) $valid_value = FALSE;
			// poor man's way to ensure required fields aren't blanked out, really should have a separate config per field
			if ( '1' === $meta_field['require_on_registration'] && empty( $meta_value ) ) $valid_value = FALSE;
			// check text field against regex if specified
			if ( 'textbox' === $meta_field['display'] && !empty( $meta_field['options'] ) && 1 !== preg_match( (string) $meta_field['options'], $meta_value ) ) $valid_value = FALSE;
			if ( 'textarea' !== $meta_field['display'] ) $meta_value = sanitize_text_field( $meta_value );
			if ( 'textarea' === $meta_field['display'] ) $meta_value = wp_filter_kses( $meta_value );
			
			if ( $valid_value ) {
				update_user_meta( $user_id, $meta_field['meta_key'], $meta_value );
				if ( 'terms' === $meta_field['display'] ) update_user_meta( $user_id, $meta_field['meta_key'] . '_date', time() );
			}
		}

		public /*.void.*/ function rpr_i18n_init() {
			// Place your language file in the languages subfolder and name it "register-plus-redux-{language}.mo" replace {language} with your language value from wp-config.php
			load_plugin_textdomain( 'register-plus-redux', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		public /*.string.*/ function rpr_filter_pre_user_login_swp( /*.string.*/ $user_login ) {
			// TODO: Review, this could be overriding some other stuff
			if ( '1' === $this->rpr_get_option( 'username_is_email' ) ) {
				if ( isset( $_POST['user_email'] ) ) {
					$user_email = stripslashes( (string) $_POST['user_email'] );
					$user_email = strtolower( sanitize_user( $user_email ) );
					return $user_email;
				}
			}
			return $user_login;
		}

		public /*.void.*/ function rpr_admin_enqueue_scripts( /*.string.*/ $hook_suffix ) {
			if ( 'profile.php' == $hook_suffix || 'user-edit.php' == $hook_suffix ) {
				/*.array[]mixed.*/ $redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
				if ( is_array( $redux_usermeta ) ) {
					foreach ( $redux_usermeta as $meta_field ) {
						if ( '1' === $meta_field['show_on_registration'] && '1' === $meta_field['show_datepicker'] ) {
							wp_enqueue_style( 'jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/ui-lightness/jquery-ui.css', false ); 
							wp_enqueue_script( 'jquery-ui-datepicker' );
							break;
						}
					}
				}
			}
		}

		// $profileuser is a WP_User object
		public /*.void.*/ function rpr_show_custom_fields( $profileuser ) {
			$additional_fields_exist = FALSE;
			$terms_exist = FALSE;

			/*.array[]mixed.*/ $redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( '1' === $this->rpr_get_option( 'enable_invitation_code' ) || is_array( $redux_usermeta ) ) {
				if ( is_array( $redux_usermeta ) ) {
					foreach ( $redux_usermeta as $meta_field ) {
						if ( 'terms' !== $meta_field['display'] ) {
							$additional_fields_exist = TRUE;
							break;
						}
						else if ( 'terms' === $meta_field['display'] ) { $term_exist = TRUE; }
					}
				}
				if ( '1' === $this->rpr_get_option( 'enable_invitation_code' ) || $additional_fields_exist ) {
					echo '<h3>', __( 'Additional Information', 'register-plus-redux' ), '</h3>';
					echo '<table class="form-table">';
					if ( '1' === $this->rpr_get_option( 'enable_invitation_code' ) ) {
						echo "\n", '<tr>';
						echo "\n", '<th><label for="invitation_code">', __( 'Invitation Code', 'register-plus-redux' ), '</label></th>';
						echo "\n", '<td><input type="text" name="invitation_code" id="invitation_code" value="', esc_attr( $profileuser->invitation_code ), '" class="regular-text" ';
						if ( !current_user_can( 'edit_users' ) ) echo 'readonly="readonly" ';
						echo '/></td>';
						echo "\n", '</tr>';
					}
					if ( $additional_fields_exist ) {
						foreach ( $redux_usermeta as $meta_field ) {
							if ( current_user_can( 'edit_users' ) || '1' === $meta_field['show_on_profile'] ) {
								if ( 'terms' === $meta_field['display'] ) continue;
								$meta_key = (string) esc_attr( $meta_field['meta_key'] );
								$meta_value = get_user_meta( $profileuser->ID, $meta_key, TRUE );
								$meta_value = (string) get_user_meta( $profileuser->ID, $meta_key, TRUE );
								echo "\n", '<tr>';
								echo "\n", '<th><label for="', $meta_key, '">', esc_html( $meta_field['label'] );
								if ( '1' !== $meta_field['show_on_profile'] ) echo ' <span class="description">(hidden)</span>';
								if ( '1' ===  $meta_field['require_on_registration'] ) echo ' <span class="description">(required)</span>';
								echo '</label></th>';
								switch ( (string) $meta_field['display'] ) {
									case 'textbox':
										echo "\n", '<td><input type="text" name="', $meta_key, '" id="', $meta_key, '" ';
										if ( '1' === $meta_field['show_datepicker'] ) echo 'class="datepicker" ';
										echo 'value="', esc_attr( $meta_value ), '" class="regular-text" /></td>';
										break;
									case 'select':
										echo "\n", '<td>';
										echo "\n", '<select name="', $meta_key, '" id="', $meta_key, '" style="width: 15em;">';
										/*.array[]string.*/ $field_options = explode( ',', (string) $meta_field['options'] );
										foreach ( $field_options as $field_option ) {
											echo "\n", '<option value="', esc_attr( $field_option ), '"';
											if ( $meta_value === esc_attr( $field_option ) ) echo ' selected="selected"';
											echo '>', esc_html( $field_option ), '</option>';
										}
										echo "\n", '</select>';
										echo "\n", '</td>';
										break;
									case 'checkbox':
										echo "\n", '<td>';
										/*.array[]string.*/ $field_options = explode( ',', (string) $meta_field['options'] );
										/*.array[]string.*/ $meta_values = explode( ',', (string) $meta_value );
										foreach ( $field_options as $field_option ) {
											echo "\n", '<label><input type="checkbox" name="', $meta_key, '[]" value="', esc_attr( $field_option ), '" ';
											if ( in_array( esc_attr( $field_option ), $meta_values ) ) echo 'checked="checked" ';
											echo '/>&nbsp;', esc_html( $field_option ), '</label><br />';
										}
										echo "\n", '</td>';
										break;
									case 'radio':
										echo "\n", '<td>';
										/*.array[]string.*/ $field_options = explode( ',', (string) $meta_field['options'] );
										foreach ( $field_options as $field_option ) {
											echo "\n", '<label><input type="radio" name="', $meta_key, '" value="', esc_attr( $field_option ), '" ';
											if ( $meta_value === esc_attr( $field_option ) ) echo 'checked="checked" ';
											echo 'class="tog">&nbsp;', esc_html( $field_option ), '</label><br />';
										}
										echo "\n", '</td>';
										break;
									case 'textarea':
										echo "\n", '<td><textarea name="', $meta_key, '" id="', $meta_key, '" cols="25" rows="5">', esc_textarea( $meta_value ), '</textarea></td>';
										break;
									case 'hidden':
										echo "\n", '<td><input type="text" disabled="disabled" name="', $meta_key, '" id="', $meta_key, '" value="', esc_attr( $meta_value ), '" /></td>';
										break;
									case 'text':
										echo "\n", '<td><span class="description">', esc_html( $meta_field['label'] ), '</span></td>';
										break;
									default:
								}
								echo "\n", '</tr>';
							}
						}
					}
					echo '</table>';
				}
			}
			if ( is_array( $redux_usermeta ) ) {
				if ( !$terms_exist ) {
					foreach ( $redux_usermeta as $meta_field ) {
						if ( 'terms' === $meta_field['display'] ) { 
							$terms_exist = TRUE;
							break;
						}
					}
				}
				if ( $terms_exist ) {
					echo '<h3>', __( 'Terms', 'register-plus-redux' ), '</h3>';
					echo '<table class="form-table">';
					foreach ( $redux_usermeta as $meta_field ) {
						if ( 'terms' === $meta_field['display'] ) {
							$meta_key = (string) esc_attr( $meta_field['meta_key'] );
							$meta_value = (string) get_user_meta( $profileuser->ID, $meta_key, TRUE );
							$meta_value = !empty( $meta_value ) ? $meta_value : 'N';
							$meta_value_date = (int) get_user_meta( $profileuser->ID, $meta_key . '_date', TRUE );
							echo "\n", '<tr>';
							echo "\n", '<th>', esc_html( $meta_field['label'] );
							if ( '1' ===  $meta_field['require_on_registration'] ) echo ' <span class="description">(required)</span>';
							echo '</label></th>';
							echo "\n", '<td>';
							echo "\n", nl2br( $meta_field['terms_content'] ), '<br />';
							echo "\n", '<span class="description">', __( 'Last Revised:', 'register-plus-redux' ), ' ', date( "m/d/Y", $meta_field['date_revised'] ), '</span><br />';
							echo "\n", '<span class="description">', __( 'Accepted:', 'register-plus-redux' ), ' ', esc_html( $meta_value );
							if ( 'Y' === $meta_value ) echo ' on ', date( "m/d/Y", $meta_value_date );
							echo '</span>';
							echo "\n", '</td>';
							echo "\n", '</tr>';
						}
					}
					echo '</table>';
				}
			}
		}

		public /*.void.*/ function rpr_save_custom_fields( /*.int.*/ $user_id ) {
			// TODO: Error check invitation code?
			if ( isset( $_POST['invitation_code'] ) ) {
				$invitation_code = stripslashes( (string) $_POST['invitation_code'] );
				update_user_meta( $user_id, 'invitation_code', sanitize_text_field( $invitation_code ) );
			}
			/*.array[]mixed.*/ $redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					if ( 'text' !== $meta_field['display'] && 'terms' !== $meta_field['display'] ) {
						if ( current_user_can( 'edit_users' ) || '1' === $meta_field['show_on_profile'] ) {
							if ( 'checkbox' === $meta_field['display'] ) {
								$meta_value = isset( $_POST[ (string) $meta_field['meta_key']] ) ? (array) $_POST[ (string) $meta_field['meta_key']] : '';
								$meta_value = stripslashes_deep( $meta_value );
							}
							else {
								$meta_value = isset( $_POST[ (string) $meta_field['meta_key']] ) ? (string) $_POST[ (string) $meta_field['meta_key']] : '';
								$meta_value = stripslashes( $meta_value );
							}
							$this->rpr_update_user_meta( $user_id, $meta_field, $meta_value );
						}
					}
				}
			}
		}

		public /*.void.*/ function rpr_admin_footer() {
			/*.array[]mixed.*/ $redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			$show_custom_date_fields = FALSE;
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					if ( '1' === $meta_field['show_on_registration'] && '1' === $meta_field['show_datepicker'] ) {
						?>
						<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery(".datepicker").datepicker();
						});
						</script>
						<?php
						break;
					}
				}
			}
		}

		// $user is a WP_User object
		public /*.string.*/ function replace_keywords( /*.mixed.*/ $message, $user, $plaintext_pass = '', $verification_code = '' ) {
			global $pagenow;
			if ( empty( $message ) ) return '%blogname% %site_url% %http_referer% %http_user_agent% %registered_from_ip% %registered_from_host% %user_login% %user_email% %user_password% %verification_code% %verification_url%';

			preg_match_all( '/%=([^%]+)%/', (string) $message, $keys );
			if ( is_array( $keys ) && is_array( $keys[1] ) ) {
				foreach( $keys[1] as $key ) {
					$message = str_replace( "%=$key%", get_user_meta( $user->ID, $key, TRUE ), $message );
				}
			}

			// support renamed keywords for backcompat
			$message = str_replace( '%verification_link%', '%verification_url%', $message );

			$message = str_replace( '%blogname%', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $message );
			$message = str_replace( '%site_url%', site_url(), $message );
			$message = str_replace( '%?pagenow%', $pagenow, $message ); //debug keyword
			$message = str_replace( '%?user_info%', print_r( $user, TRUE ), $message ); //debug keyword
			$message = str_replace( '%?keys%', print_r( $keys, TRUE ), $message ); //debug keyword

			if ( !empty( $_SERVER ) ) {
				$message = str_replace( '%http_referer%', isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '', $message );
				$message = str_replace( '%http_user_agent%', isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '', $message );
				$message = str_replace( '%registered_from_ip%', isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '', $message );
				$message = str_replace( '%registered_from_host%', isset( $_SERVER['REMOTE_ADDR'] ) ? gethostbyaddr( $_SERVER['REMOTE_ADDR'] ) : '', $message );
			}
			if ( !empty( $user ) ) {
				$message = str_replace( '%user_login%', $user->user_login, $message );
				$message = str_replace( '%user_email%', $user->user_email, $message );
				$message = str_replace( '%stored_user_login%', $user->user_login, $message );
			}
			if ( !empty( $plaintext_pass ) ) {
				$message = str_replace( '%user_password%', $plaintext_pass, $message );
			}
			if ( !empty( $verification_code ) ) {
				$message = str_replace( '%verification_code%', $verification_code, $message );
				$message = str_replace( '%verification_url%', wp_login_url() . '?action=verifyemail&verification_code=' . $verification_code, $message );
			}

			preg_match_all( '/%([^%]+)%/', (string) $message, $keys );
			if ( is_array( $keys ) && is_array( $keys[1] ) ) {
				foreach( $keys[1] as $key ) {
					$message = str_replace( "%$key%", get_user_meta( $user->ID, $key, TRUE ), $message );
				}
			}
			return (string) $message;
		}

		public static /*.bool.*/ function rpr_active_for_network() {
			if ( !is_multisite() ) { return false; }
			$plugins = get_site_option( 'active_sitewide_plugins');
			if ( isset( $plugins[ plugin_basename( __FILE__ ) ] ) ) { return true; }
			return false;
		}

		public /*.void.*/ function send_verification_mail( /*.int.*/ $user_id, /*.string.*/ $verification_code ) {
			$user = get_userdata( $user_id );
			$subject = Register_Plus_Redux::default_options( 'verification_message_subject' );
			$message = Register_Plus_Redux::default_options( 'verification_message_body' );
			add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_text' ), 10, 1 );
			if ( '1' === $this->rpr_get_option( 'custom_verification_message' ) ) {
				$subject = esc_html( $this->rpr_get_option( 'verification_message_subject' ) );
				$message = $this->rpr_get_option( 'verification_message_body' );
				if ( '1' === $this->rpr_get_option( 'send_verification_message_in_html' ) && '1' === $this->rpr_get_option( 'verification_message_newline_as_br' ) ) {
					$message = nl2br( (string) $message );
				}
				$from_name = $this->rpr_get_option( 'verification_message_from_name' );
				if ( !empty( $from_name ) )
					add_filter( 'wp_mail_from_name', array( $this, 'rpr_filter_verification_mail_from_name' ), 10, 1 );
				if ( FALSE !== is_email( $this->rpr_get_option( 'verification_message_from_email' ) ) )
					add_filter( 'wp_mail_from', array( $this, 'rpr_filter_verification_mail_from' ), 10, 1 );
				if ( '1' === $this->rpr_get_option( 'send_verification_message_in_html' ) )
					add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_html' ), 10, 1 );
			}
			$subject = $this->replace_keywords( $subject, $user );
			$message = $this->replace_keywords( $message, $user, '', $verification_code );
			wp_mail( $user->user_email, $subject, $message );
		}

		public /*.void.*/ function send_welcome_user_mail( /*.int.*/ $user_id, /*.string.*/ $plaintext_pass ) {
			$user = get_userdata( $user_id );
			$subject = Register_Plus_Redux::default_options( 'user_message_subject' );
			$message = Register_Plus_Redux::default_options( 'user_message_body' );
			add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_text' ), 10, 1 );
			if ( '1' === $this->rpr_get_option( 'custom_user_message' ) ) {
				$subject = esc_html( $this->rpr_get_option( 'user_message_subject' ) );
				$message = $this->rpr_get_option( 'user_message_body' );
				if ( '1' === $this->rpr_get_option( 'send_user_message_in_html' ) && '1' === $this->rpr_get_option( 'user_message_newline_as_br' ) )
					$message = nl2br( (string) $message );
				$from_name = $this->rpr_get_option( 'user_message_from_name' );
				if ( !empty( $from_name ) )
					add_filter( 'wp_mail_from_name', array( $this, 'rpr_filter_welcome_user_mail_from_name' ), 10, 1 );
				if ( FALSE !== is_email( $this->rpr_get_option( 'user_message_from_email' ) ) )
					add_filter( 'wp_mail_from', array( $this, 'rpr_filter_welcome_user_mail_from' ), 10, 1 );
				if ( '1' === $this->rpr_get_option( 'send_user_message_in_html' ) )
					add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_html' ), 10, 1 );
			}
			$subject = $this->replace_keywords( $subject, $user );
			$message = $this->replace_keywords( $message, $user, $plaintext_pass );
			wp_mail( $user->user_email, $subject, $message );
		}

		public /*.void.*/ function send_admin_mail( /*.int.*/ $user_id, /*.string.*/ $plaintext_pass, $verification_code = '' ) {
			$user = get_userdata( $user_id );
			$subject = Register_Plus_Redux::default_options( 'admin_message_subject' );
			$message = Register_Plus_Redux::default_options( 'admin_message_body' );
			add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_text' ), 10, 1 );
			if ( '1' === $this->rpr_get_option( 'custom_admin_message' ) ) {
				$subject = esc_html( $this->rpr_get_option( 'admin_message_subject' ) );
				$message = $this->rpr_get_option( 'admin_message_body' );
				if ( '1' === $this->rpr_get_option( 'send_admin_message_in_html' ) && '1' === $this->rpr_get_option( 'admin_message_newline_as_br' ) )
					$message = nl2br( (string) $message );
				$from_name = $this->rpr_get_option( 'admin_message_from_name' );
				if ( !empty( $from_name ) )
					add_filter( 'wp_mail_from_name', array( $this, 'rpr_filter_admin_mail_from_name' ), 10, 1 );
				if ( FALSE !== is_email( $this->rpr_get_option( 'admin_message_from_email' ) ) )
					add_filter( 'wp_mail_from', array( $this, 'rpr_filter_admin_mail_from' ), 10, 1 );
				if ( '1' === $this->rpr_get_option( 'send_admin_message_in_html' ) )
					add_filter( 'wp_mail_content_type', array( $this, 'rpr_filter_mail_content_type_html' ), 10, 1 );
			}
			$subject = $this->replace_keywords( $subject, $user );
			$message = $this->replace_keywords( $message, $user, $plaintext_pass, $verification_code );
			wp_mail( get_option( 'admin_email' ), $subject, $message );
		}

		public /*.string.*/ function rpr_filter_verification_mail_from( /*.string.*/ $from_email ) {
			return is_email( $this->rpr_get_option( 'verification_message_from_email' ) );
		}

		public /*.string.*/ function rpr_filter_verification_mail_from_name( /*.string.*/ $from_name ) {
			return esc_html( $this->rpr_get_option( 'verification_message_from_name' ) );
		}

		public /*.string.*/ function rpr_filter_welcome_user_mail_from( /*.string.*/ $from_email ) {
			return is_email( $this->rpr_get_option( 'user_message_from_email' ) );
		}

		public /*.string.*/ function rpr_filter_welcome_user_mail_from_name( /*.string.*/ $from_name ) {
			return esc_html( $this->rpr_get_option( 'user_message_from_name' ) );
		}

		public /*.string.*/ function rpr_filter_admin_mail_from( /*.string.*/ $from_email ) {
			return is_email( $this->rpr_get_option( 'admin_message_from_email' ) );
		}

		public /*.string.*/ function rpr_filter_admin_mail_from_name( /*.string.*/ $from_name ) {
			return esc_html( $this->rpr_get_option( 'admin_message_from_name' ) );
		}

		public /*.string.*/ function rpr_filter_mail_content_type_text( /*.string.*/ $content_type ) {
			return 'text/plain';
		}

		public /*.string.*/ function rpr_filter_mail_content_type_html( /*.string.*/ $content_type ) {
			return 'text/html';
		}
	}
}

// include secondary php files outside of object otherwise $register_plus_redux will not be an instance yet
if ( class_exists( 'Register_Plus_Redux' ) ) {
	//rumor has it this may need to declared global in order to be available at plugin activation
	$register_plus_redux = new Register_Plus_Redux();

	if ( is_admin() ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-admin.php' );

	if ( is_admin() ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-admin-menu.php' );
	
	if ( is_admin() && file_exists( plugin_dir_path( __FILE__ ) . 'rpr-admin-menu-wip.php' ) ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-admin-menu-wip.php' );

	$do_include = FALSE;
	if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_tracking_widget' ) ) { $do_include = TRUE; }
	if ( $do_include && is_admin() ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-dashboard-widget.php' );

	//TODO: Determine which features require the following file
	$do_include = TRUE;
	if ( $do_include ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-login.php' );

	//TODO: Determine which features require the following file
	$do_include = TRUE;
	if ( $do_include & is_multisite() ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-signup.php' );

	$do_include = FALSE;
	if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_admin' ) ) { $do_include = TRUE; }
	if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) { $do_include = TRUE; }
	if ( is_array( get_option( 'register_plus_redux_usermeta-rv2' ) ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_code' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'autologin_user' ) ) { $do_include = TRUE; }
	if ( $do_include && is_multisite() && Register_Plus_Redux::rpr_active_for_network() ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-activate.php' );

	//NOTE: Requires rpr-admin.php for rpr_new_user_notification_warning make
	$do_include = FALSE;
	if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_email' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'disable_user_message_registered' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'disable_user_message_created' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'custom_user_message' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_admin' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'disable_admin_message_registered' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'disable_admin_message_created' ) ) { $do_include = TRUE; }
	if ( '1' === $register_plus_redux->rpr_get_option( 'custom_admin_message' ) ) { $do_include = TRUE; }
	if ( $do_include ) require_once( plugin_dir_path( __FILE__ ) . 'rpr-new-user-notification.php' );
}
?>
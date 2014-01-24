<?php
if ( !class_exists( 'RPR_Signup' ) ) {
	class RPR_Signup {
		public /*.void.*/ function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'rpr_signup_enqueue_scripts' ), 10, 0 );
			add_action( 'signup_header', array( $this, 'rpr_signup_header' ), 10, 0 );
			add_action( 'signup_extra_fields', array( $this, 'rpr_signup_extra_fields' ), 9, 1 ); // Higher priority to avoid getting bumped by other plugins
			add_action( 'after_signup_form', array( $this, 'rpr_after_signup_form' ), 10, 0 ); // Closest thing to signup_footer
			add_action( 'signup_hidden_fields', array( $this, 'rpr_signup_hidden_fields' ), 10, 0 );
			add_filter( 'wpmu_validate_user_signup', array( $this, 'rpr_filter_wpmu_validate_user_signup' ), 10, 1 );
			add_filter( 'add_signup_meta', array( $this, 'filter_add_signup_meta' ), 10, 1 ); // Store metadata until user is activated
			add_filter( 'wpmu_signup_user_notification', array( $this, 'filter_wpmu_signup_user_notification' ), 10, 4 ); // Store metadata until user is activated
			add_filter( 'wpmu_signup_blog_notification', array( $this, 'filter_wpmu_signup_blog_notification' ), 10, 7 ); // Store metadata until user is activated
			add_action( 'signup_finished', array( $this, 'rpr_signup_finished' ), 10, 0 );
			add_filter( 'random_password', array( $this, 'rpr_signup_filter_random_password' ), 10, 1 ); // Replace random password with user set password
			add_action( 'preprocess_signup_form', array( $this, 'rpr_preprocess_signup_form' ), 10, 0 );
		}

		public /*.void.*/ function rpr_signup_enqueue_scripts() {
			global $register_plus_redux;
			$enqueue_jquery = FALSE;
			if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) ) { $enqueue_jquery = TRUE; }
			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) && '1' === $register_plus_redux->rpr_get_option( 'show_password_meter' ) ) { $enqueue_jquery = TRUE; }
			if ( '1' !== $register_plus_redux->rpr_get_option( 'verify_user_email' ) ) { $enqueue_jquery = TRUE; }
			if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_email' ) && $register_plus_redux->rpr_get_option( 'message_verify_user_email' ) ) { $enqueue_jquery = TRUE; }
			if ( $enqueue_jquery ) wp_enqueue_script( 'jquery' );

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

		public /*.void.*/ function rpr_signup_header() {
			global $register_plus_redux;
			$redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					if ( !empty( $meta_field['show_on_registration'] ) ) {
						$meta_key = esc_attr( $meta_field['meta_key'] );
						if ( 'textbox' === $meta_field['display'] ) {
							if ( empty( $show_custom_text_fields ) )
								$show_custom_text_fields = '.mu_register #' . $meta_key;
							else
								$show_custom_text_fields .= ', .mu_register #' . $meta_key;
						}
						if ( 'select' === $meta_field['display'] ) {
							if ( empty( $show_custom_select_fields ) )
								$show_custom_select_fields = '.mu_register #' . $meta_key;
							else
								$show_custom_select_fields .= ', .mu_register #' . $meta_key;
						}
						if ( 'checkbox' === $meta_field['display'] ) {
							/*.array[]string.*/ $field_options = explode( ',', $meta_field['options'] );
							foreach ( $field_options as $field_option ) {
								if ( empty( $show_custom_checkbox_fields ) ) {
									$show_custom_checkbox_fields = '.mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . '-label';
								}
								else {
									$show_custom_checkbox_fields .= ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . '-label';
								}
							}
						}
						if ( 'radio' === $meta_field['display'] ) {
							/*.array[]string.*/ $field_options = explode( ',', $meta_field['options'] );
							foreach ( $field_options as $field_option ) {
								if ( empty( $show_custom_radio_fields ) ) {
									$show_custom_radio_fields = '.mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . '-label';
								}
								else {
									$show_custom_radio_fields .= ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . ', .mu_register #' . $meta_key . '-' . Register_Plus_Redux::sanitize_text( $field_option ) . '-label';
								}
							}
						}
						if ( 'textarea' === $meta_field['display'] ) {
							if ( empty( $show_custom_textarea_fields ) )
								$show_custom_textarea_fields = '.mu_register #' . $meta_key . '-label';
							else
								$show_custom_textarea_fields .= ', .mu_register #' . $meta_key . '-label';
						}
						if ( !empty( $meta_field['require_on_registration'] ) ) {
							if ( empty( $required_meta_fields ) )
								$required_meta_fields = '.mu_register #' . $meta_key;
							else
								$required_meta_fields .= ', .mu_register #' . $meta_key;
						}
					}
				}
			}

			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) $show_fields = '.mu_register #' . implode( ', .mu_register #', $register_plus_redux->rpr_get_option( 'show_fields' ) );
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) $required_fields = '.mu_register #' . implode( ', .mu_register #', $register_plus_redux->rpr_get_option( 'required_fields' ) );

			echo "\n", '<style type="text/css">';
			if ( '1' === $register_plus_redux->rpr_get_option( 'default_css' ) ) {
				if ( '1' === $register_plus_redux->rpr_get_option( 'double_check_email' ) ) echo "\n", '.mu_register #user_email2 { width:100%; font-size: 24px; margin:5px 0; }';
				if ( !empty( $show_fields ) ) echo "\n", $show_fields, ' { width:100%; font-size: 24px; margin:5px 0; }';
				if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'about', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) echo "\n", '.mu_register #description { width:100%; font-size:24px; height: 60px; margin:5px 0; }';
				if ( !empty( $show_custom_text_fields ) ) echo "\n", $show_custom_text_fields, ' { width:100%; font-size: 24px; margin:5px 0; }';
				if ( !empty( $show_custom_select_fields ) ) echo "\n", $show_custom_select_fields, ' { width:100%; font-size:24px; margin:5px 0; }';
				if ( !empty( $show_custom_checkbox_fields ) ) echo "\n", $show_custom_checkbox_fields, ' { font-size:18px; margin:5px 0; display: inline; }';
				if ( !empty( $show_custom_radio_fields ) ) echo "\n", $show_custom_radio_fields, ' { font-size:18px; margin:5px 0; display: inline; }';
				if ( !empty( $show_custom_textarea_fields ) ) echo "\n", $show_custom_textarea_fields, ' { width:100%; font-size:24px; height: 60px; margin:5px 0; }';
				if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) echo "\n", '.mu_register #pass1, .mu_register #pass2 { width:100%; font-size: 24px; margin:5px 0; }';
				if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_code' ) ) echo "\n", '.mu_register #invitation_code { width:100%; font-size: 24px; margin:5px 0; }';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_disclaimer' ) ) { echo "\n", '.mu_register #disclaimer { width: 100%; font-size:12px; margin:5px 0; display: block; '; if ( strlen( $register_plus_redux->rpr_get_option( 'message_disclaimer' ) ) > 525) echo 'height: 160px; overflow:scroll;'; echo ' }'; }
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_license' ) ) { echo "\n", '.mu_register #license { width: 100%; font-size:12px; margin:5px 0; display: block; '; if ( strlen( $register_plus_redux->rpr_get_option( 'message_license' ) ) > 525) echo 'height: 160px; overflow:scroll;'; echo ' }'; }
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_privacy_policy' ) ) { echo "\n", '.mu_register #privacy_policy { width: 100%; font-size:12px; margin:5px 0; display: block; '; if ( strlen( $register_plus_redux->rpr_get_option( 'message_license' ) ) > 525) echo 'height: 160px; overflow:scroll;'; echo ' }'; }
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_disclaimer' ) || '1' === $register_plus_redux->rpr_get_option( 'show_license' ) || '1' === $register_plus_redux->rpr_get_option( 'show_privacy_policy' ) ) echo "\n", '.mu_register .accept_check { display:block; margin:5px 0; }';
			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) {
				if ( '1' === $register_plus_redux->rpr_get_option( 'show_password_meter' ) ) {
					echo "\n", '.mu_register #pass-strength-result { width: 100%; margin: 10px 0; border: 1px solid; padding: 6px; text-align: center; font-weight: bold; display: block; }';
					echo "\n", '.mu_register #pass-strength-result { background-color: #eee; border-color: #ddd !important; }';
					echo "\n", '.mu_register #pass-strength-result.bad { background-color: #ffb78c; border-color: #ff853c !important; }';
					echo "\n", '.mu_register #pass-strength-result.good { background-color: #ffec8b; border-color: #fc0 !important; }';
					echo "\n", '.mu_register #pass-strength-result.short { background-color: #ffa0a0; border-color: #f04040 !important; }';
					echo "\n", '.mu_register #pass-strength-result.strong { background-color: #c3ff88; border-color: #8dff1c !important; }';
				}
			}
			if ( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ) {
				echo "\n", '.mu_register #user_login, .mu_register #user_email { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ), '} ';
				if ( '1' === $register_plus_redux->rpr_get_option( 'double_check_email' ) ) echo "\n", '.mu_register #user_email2 { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ), ' }';
				if ( !empty( $required_fields ) ) echo "\n", $required_fields, ' { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ) , ' }';
				if ( !empty( $required_meta_fields ) ) echo "\n", $required_meta_fields, ' { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ) , ' }';
				if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) echo "\n", '.mu_register #pass1, .mu_register #pass2 { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ), ' }';
				if ( '1' === $register_plus_redux->rpr_get_option( 'require_invitation_code' ) ) echo "\n", '.mu_register #invitation_code { ', esc_html( $register_plus_redux->rpr_get_option( 'required_fields_style' ) ), ' }';
			}
			if ( $register_plus_redux->rpr_get_option( 'custom_registration_page_css' ) ) echo "\n", esc_html( $register_plus_redux->rpr_get_option( 'custom_registration_page_css' ) );
			echo "\n", '</style>';
			if ( $register_plus_redux->rpr_get_option( 'custom_login_page_css' ) ) {
				echo "\n", '<style type="text/css">';
				echo "\n", esc_html( $register_plus_redux->rpr_get_option( 'custom_login_page_css' ) );
				echo "\n", '</style>';
			}
		}

		public /*.void.*/ function rpr_signup_extra_fields( /*.object.*/ $errors ) {
			global $register_plus_redux;
			if ( '1' === $register_plus_redux->rpr_get_option( 'double_check_email' ) ) {
				$user_email2 = isset( $_REQUEST['user_email2'] ) ? (string) $_REQUEST['user_email2'] : '';
				echo "\n", '<label id="user_email2-label" for="user_email2">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) ) echo '*';
				echo __( 'Confirm E-mail', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('user_email2') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" autocomplete="off" name="user_email2" id="user_email2" value="', esc_attr( $user_email2 ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'first_name', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$first_name = isset( $_REQUEST['first_name'] ) ? (string) $_REQUEST['first_name'] : '';
				echo "\n", '<label id="first_name-label" for="first_name">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'first_name', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'First Name', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('first_name') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="first_name" id="first_name" value="', esc_attr( $first_name ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'last_name', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$last_name = isset( $_REQUEST['last_name'] ) ? (string) $_REQUEST['last_name'] : '';
				echo "\n", '<label id="last_name-label" for="last_name">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'last_name', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'Last Name', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('last_name') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="last_name" id="last_name" value="', esc_attr( $last_name ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'user_url', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$user_url = isset( $_REQUEST['user_url'] ) ? (string) $_REQUEST['user_url'] : '';
				echo "\n", '<label id="user_url-label" for="user_url">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'user_url', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'Website', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('user_url') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="user_url" id="user_url" value="', esc_attr( $user_url ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'aim', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$aim = isset( $_REQUEST['aim'] ) ? (string) $_REQUEST['aim'] : '';
				echo "\n", '<label id="aim-label" for="aim">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'aim', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'AIM', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('aim') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="aim" id="aim" value="', esc_attr( $aim ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'yahoo', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$yahoo = isset( $_REQUEST['yahoo'] ) ? (string) $_REQUEST['yahoo'] : '';
				echo "\n", '<label id="yahoo-label" for="yahoo">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'yahoo', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'Yahoo IM', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('yahoo') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="yahoo" id="yahoo" value="', esc_attr( $yahoo ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'jabber', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$jabber = isset( $_REQUEST['jabber'] ) ? (string) $_REQUEST['jabber'] : '';
				echo "\n", '<label id="jabber-label" for="jabber">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'jabber', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'Jabber / Google Talk', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('jabber') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="jabber" id="jabber" value="', esc_attr( $jabber ), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'about', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$description = isset( $_REQUEST['description'] ) ? (string) $_REQUEST['description'] : '';
				echo "\n", '<label id="description-label" for="description">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'about', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) echo '*';
				echo __( 'About Yourself', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('description') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<textarea name="description" id="description" cols="25" rows="5">', esc_textarea( $description ), '</textarea>';
				echo '<br />', __( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'register-plus-redux' );
			}

			$redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					if ( !empty( $meta_field['show_on_registration'] ) ) {
						$meta_key = esc_attr( $meta_field['meta_key'] );
						if ( 'checkbox' === $meta_field['display'] ) {
							$meta_value = isset( $_REQUEST[$meta_key] ) ? (array) $_REQUEST[$meta_key] : '';
						}
						else {
							$meta_value = isset( $_REQUEST[$meta_key] ) ? (string) $_REQUEST[$meta_key] : '';
						}
						if ( ( $meta_field['display'] != 'hidden' ) && ( $meta_field['display'] != 'text' ) ) {
							echo "\n", '<label id="', $meta_key, '-label" for="', $meta_key, '">';
							if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && !empty( $meta_field['require_on_registration'] ) ) echo '*';
							echo esc_html( $meta_field['label'] ), ':</label>';
							if ( $errmsg = $errors->get_error_message($meta_key) ) {
								echo '<p class="error">', $errmsg, '</p>';
							}
						}
						switch ( $meta_field['display'] ) {
							case 'textbox':
								echo "\n", '<input type="text" name="', $meta_key, '" id="', $meta_key, '" ';
								if ( '1' === $meta_field['show_datepicker'] ) echo 'class="datepicker" ';
								echo 'value="', esc_attr( $meta_value ), '" />';
								break;
							case 'select':
								echo "\n", '<select name="', $meta_key, '" id="', $meta_key, '">';
								/*.array[]string.*/ $field_options = explode( ',', $meta_field['options'] );
								foreach ( $field_options as $field_option ) {
									echo "\n", '<option id="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '" value="', esc_attr( $field_option ), '"';
									if ( $meta_value === esc_attr( $field_option ) ) echo ' selected="selected"';
									echo '>', esc_html( $field_option ), '</option>';
								}
								echo "n</select>";
								break;
							case 'checkbox':
								/*.array[]string.*/ $field_options = explode( ',', $meta_field['options'] );
								foreach ( $field_options as $field_option ) {
									echo "\n", '<input type="checkbox" name="', $meta_key, '[]" id="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '" value="', esc_attr( $field_option ), '" ';
									if ( is_array( $meta_value ) && in_array( esc_attr( $field_option ), $meta_value ) ) echo 'checked="checked" ';
									if ( !is_array( $meta_value ) && ( $meta_value === esc_attr( $field_option ) ) ) echo 'checked="checked" ';
									echo '><label id="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '-label" class="', $meta_key, '" for="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '">&nbsp;', esc_html( $field_option ), '</label><br />';
								}
								break;
							case 'radio':
								/*.array[]string.*/ $field_options = explode( ',', $meta_field['options'] );
								foreach ( $field_options as $field_option ) {
									echo "\n", '<input type="radio" name="', $meta_key, '" id="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '" value="', esc_attr( $field_option ), '" ';
									if ( $meta_value === esc_attr( $field_option ) ) echo 'checked="checked" ';
									echo '><label id="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '-label" class="', $meta_key, '" for="', $meta_key, '-', Register_Plus_Redux::sanitize_text( $field_option ), '">&nbsp;', esc_html( $field_option ), '</label><br />';
								}
								break;
							case 'textarea':
								echo "\n", '<textarea name="', $meta_key, '" id="', $meta_key, '" cols="25" rows="5">', esc_textarea( $meta_value ), '</textarea>';
								break;
							case 'hidden':
								echo "\n", '<input type="hidden" name="', $meta_key, '" id="', $meta_key, '" value="', esc_attr( $meta_value ), '" />';
								break;
							case 'text':
								echo "\n", esc_html( $meta_field['label'] );
								break;
						}
					}
				}
			}

			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) {
				echo "\n", '<label id="pass1-label" for="pass1-label">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) ) echo '*';
				echo __( 'Password', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('pass1') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="password" autocomplete="off" name="pass1" id="pass1" />';
				if ( '1' !== $register_plus_redux->rpr_get_option( 'disable_password_confirmation' ) ) {
					echo "\n", '<label id="pass2-label" for="pass2-label">';
					if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) ) echo '*';
					echo __( 'Confirm Password', 'register-plus-redux' ), ':</label>';
					if ( $errmsg = $errors->get_error_message('pass2') ) {
						echo '<p class="error">', $errmsg, '</p>';
					}
					echo "\n", '<input type="password" autocomplete="off" name="pass2" id="pass2" />';
				}
				if ( '1' === $register_plus_redux->rpr_get_option( 'show_password_meter' ) ) {
					echo "\n", '<div id="pass-strength-result">', $register_plus_redux->rpr_get_option( 'message_empty_password' ), '</div>';
					echo "\n", '<p id="pass_strength_msg" style="display: inline;">', sprintf(__( 'Your password must be at least %d characters long. To make your password stronger, use upper and lower case letters, numbers, and the following symbols !@#$%%^&amp;*()', 'register-plus-redux' ), absint( $register_plus_redux->rpr_get_option( 'min_password_length' ) ) ), '</p>';
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_code' ) ) {
				$invitation_code = isset( $_REQUEST['invitation_code'] ) ? (string) $_REQUEST['invitation_code'] : '';
				echo "\n", '<label id="invitation_code-label" for="invitation_code">';
				if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) && '1' === $register_plus_redux->rpr_get_option( 'require_invitation_code' ) ) echo '*';
				echo __( 'Invitation Code', 'register-plus-redux' ), ':</label>';
				if ( $errmsg = $errors->get_error_message('invitation_code') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
				echo "\n", '<input type="text" name="invitation_code" id="invitation_code" value="', esc_attr( $invitation_code ), '" />';
				if ( '1' === $register_plus_redux->rpr_get_option( 'require_invitation_code' ) )
					echo "\n", '<p id="invitation_code_msg" style="display: inline;">', __( 'This website is currently closed to public registrations. You will need an invitation code to register.', 'register-plus-redux' ), '</p>';
				else
					echo "\n", '<p id="invitation_code_msg" style="display: inline;">', __( 'Have an invitation code? Enter it here. (This is not required)', 'register-plus-redux' ), '</p>';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_disclaimer' ) ) {
				$accept_disclaimer = isset( $_REQUEST['accept_disclaimer'] ) ? '1' : '0';
				echo "\n", '<label id="disclaimer-label" for="disclaimer">', esc_html( $register_plus_redux->rpr_get_option( 'message_disclaimer_title' ) ), ':</label>';
				echo "\n", '<div id="disclaimer" style="display: inline;">', nl2br( $register_plus_redux->rpr_get_option( 'message_disclaimer' ) ), '</div>';
				if ( '1' === $register_plus_redux->rpr_get_option( 'require_disclaimer_agree' ) ) {
					echo "\n", '<label id="accept_disclaimer-label"><input type="checkbox" name="accept_disclaimer" id="accept_disclaimer" value="1"'; if ( $accept_disclaimer ) echo ' checked="checked"'; echo ' />&nbsp;', esc_html( $register_plus_redux->rpr_get_option( 'message_disclaimer_agree' ) ), '</label>';
				}
				if ( $errmsg = $errors->get_error_message('disclaimer') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_license' ) ) {
				$accept_license = isset( $_REQUEST['accept_license'] ) ? '1' : '0';
				echo "\n", '<label id="license-label" for="license">', esc_html( $register_plus_redux->rpr_get_option( 'message_license_title' ) ), ':</label>';
				echo "\n", '<div id="license" style="display: inline;">', nl2br( $register_plus_redux->rpr_get_option( 'message_license' ) ), '</div>';
				if ( '1' === $register_plus_redux->rpr_get_option( 'require_license_agree' ) ) {
					echo "\n", '<label id="accept_license-label"><input type="checkbox" name="accept_license" id="accept_license" value="1"'; if ( $accept_license ) echo ' checked="checked"'; echo ' />&nbsp;', esc_html( $register_plus_redux->rpr_get_option( 'message_license_agree' ) ), '</label>';
				}
				if ( $errmsg = $errors->get_error_message('license') ) {
					echo '<p class="error">', $errmsg, '</p>';
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_privacy_policy' ) ) {
				$accept_privacy_policy = isset( $_REQUEST['accept_privacy_policy'] ) ? '1' : '0';
				echo "\n", '<label id="privacy_policy-label" for="privacy_policy">', esc_html( $register_plus_redux->rpr_get_option( 'message_privacy_policy_title' ) ), ':</label>';
				echo "\n", '<div id="privacy_policy" style="display: inline;">', nl2br( $register_plus_redux->rpr_get_option( 'message_privacy_policy' ) ), '</div>';
				if ( '1' === $register_plus_redux->rpr_get_option( 'require_privacy_policy_agree' ) ) {
					echo "\n", '<label id="accept_privacy_policy-label"><input type="checkbox" name="accept_privacy_policy" id="accept_privacy_policy" value="1"'; if ( $accept_privacy_policy ) echo ' checked="checked"'; echo ' />&nbsp;', esc_html( $register_plus_redux->rpr_get_option( 'message_privacy_policy_agree' ) ), '</label>';
				}
				if ( $errmsg = $errors->get_error_message('privacy_policy') ) {
					echo '<p class="error">', $errmsg, '</p>';
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
					foreach ( $redux_usermeta as $meta_field ) {
						if ( 'terms' === $meta_field['display'] && '1' === $meta_field['show_on_registration'] ) {
							$meta_value = isset( $_REQUEST[$meta_key] ) ? (string) $_REQUEST[$meta_key] : 'N';
							$meta_key = (string) esc_attr( $meta_field['meta_key'] );
							echo "\n", '<label id="', $meta_key, '-label">', esc_html( $meta_field['label'] ), '</label><br />';
							echo "\n", '<div id="', $meta_key, '-content" style="display: inline;">', nl2br( $meta_field['terms_content'] ), '</div>';
							if ( '1' === $meta_field['require_on_registration'] ) {
								echo "\n", '<label id="accept_', $meta_key, '-label"><input type="checkbox" name="', $meta_key, '" id="', $meta_key, '" value="Y"', checked( $meta_value, 'Y' ), ' />&nbsp;', esc_html( $meta_field['terms_agreement_text'] ), '</label>';
							}
							if ( $errmsg = $errors->get_error_message($meta_key) ) {
								echo '<p class="error">', $errmsg, '</p>';
							}
						}
					}
				}
			}
		}

		public /*.void.*/ function rpr_after_signup_form() {
			global $register_plus_redux;
			if ( '1' !== $register_plus_redux->rpr_get_option( 'verify_user_email' ) ) {
				?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					if (jQuery("input[type='hidden'][name='signup_for']").length > 0) {
						jQuery("input[name='stage']").val("user-signup");
					}
					if (jQuery("input[type='radio'][name='signup_for']:checked").val() === "blog") {
						jQuery("input[name='stage']").val("blog-signup");
					}
					if (jQuery("input[type='radio'][name='signup_for']:checked").val() === "user") {
						jQuery("input[name='stage']").val("user-signup");
					}
					jQuery(document).on("click", "input[type='radio'][name='signup_for']", function() {
						if (jQuery("input[type='radio'][name='signup_for']:checked").val() === "blog") {
							jQuery("input[name='stage']").val("blog-signup");
						}
						if (jQuery("input[type='radio'][name='signup_for']:checked").val() === "user") {
							jQuery("input[name='stage']").val("user-signup");
						}
					});
				});
				</script>
				<?php
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'username_is_email' ) ) {
				?>
				<script type="text/javascript">
				document.getElementById("setupform").removeChild(document.getElementById("user_name").previousSibling);
				document.getElementById("setupform").removeChild(document.getElementById("user_name").nextSibling);
				document.getElementById("setupform").removeChild(document.getElementById("user_name").nextSibling);
				document.getElementById("user_name").style.display = "none";
				</script>
				<?php
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'required_fields_asterisk' ) ) {
				?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery("#user_login").parent().prepend("*");
					jQuery("#user_email").parent().prepend("*");
				});
				</script>
				<?php
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) && '1' === $register_plus_redux->rpr_get_option( 'show_password_meter' ) ) {
				// TODO: Messages could be compromised, needs to be escaped, look into methods used by comments to display
				?>
				<script type="text/javascript">
					/* <![CDATA[ */
					pwsL10n={
						empty: "<?php echo $register_plus_redux->rpr_get_option( 'message_empty_password' ); ?>",
						short: "<?php echo $register_plus_redux->rpr_get_option( 'message_short_password' ); ?>",
						bad: "<?php echo $register_plus_redux->rpr_get_option( 'message_bad_password' ); ?>",
						good: "<?php echo $register_plus_redux->rpr_get_option( 'message_good_password' ); ?>",
						strong: "<?php echo $register_plus_redux->rpr_get_option( 'message_strong_password' ); ?>",
						mismatch: "<?php echo $register_plus_redux->rpr_get_option( 'message_mismatch_password' ); ?>"
					}
					/* ]]> */
					function check_pass_strength() {
						// HACK support username_is_email in function
						var user = jQuery("<?php if ( '1' === $register_plus_redux->rpr_get_option( 'username_is_email' ) ) echo '#user_email'; else echo '#user_login'; ?>").val();
						var pass1 = jQuery("#pass1").val();
						var pass2 = jQuery("#pass2").val();
						var strength;
						jQuery("#pass-strength-result").removeClass("short bad good strong mismatch");
						if (!pass1) {
							jQuery("#pass-strength-result").html( pwsL10n.empty );
							return;
						}
						strength = passwordStrength(pass1, user, pass2);
						switch (strength) {
							case 2:
								jQuery("#pass-strength-result").addClass("bad").html( pwsL10n['bad'] );
								break;
							case 3:
								jQuery("#pass-strength-result").addClass("good").html( pwsL10n['good'] );
								break;
							case 4:
								jQuery("#pass-strength-result").addClass("strong").html( pwsL10n['strong'] );
								break;
							case 5:
								jQuery("#pass-strength-result").addClass("mismatch").html( pwsL10n['mismatch'] );
								break;
							default:
								jQuery("#pass-strength-result").addClass("short").html( pwsL10n['short'] );
						}
					}
					function passwordStrength(password1, username, password2) {
						// HACK support disable_password_confirmation in function
						password2 = typeof password2 !== 'undefined' ? password2 : '';
						var shortPass = 1, badPass = 2, goodPass = 3, strongPass = 4, mismatch = 5, symbolSize = 0, natLog, score;
						// password 1 != password 2
						if ((password1 != password2) && password2.length > 0)
							return mismatch
						// password < <?php echo absint( $register_plus_redux->rpr_get_option( 'min_password_length' ) ); ?> 
						if (password1.length < <?php echo absint( $register_plus_redux->rpr_get_option( 'min_password_length' ) ); ?>)
							return shortPass
						// password1 == username
						if (password1.toLowerCase() == username.toLowerCase())
							return badPass;
						if (password1.match(/[0-9]/))
							symbolSize +=10;
						if (password1.match(/[a-z]/))
							symbolSize +=26;
						if (password1.match(/[A-Z]/))
							symbolSize +=26;
						if (password1.match(/[^a-zA-Z0-9]/))
							symbolSize +=31;
						natLog = Math.log(Math.pow(symbolSize, password1.length));
							score = natLog / Math.LN2;
						if (score < 40)
							return badPass
						if (score < 56)
							return goodPass
						return strongPass;
					}
					jQuery(document).ready( function() {
						jQuery("#pass1").val("").keyup( check_pass_strength );
						jQuery("#pass2").val("").keyup( check_pass_strength );
					});
				</script>
				<?php
			}

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

		public /*.void.*/ function rpr_signup_hidden_fields() {
			global $register_plus_redux;
			if ( '1' === $register_plus_redux->rpr_get_option( 'double_check_email' ) ) {
				$user_email2 = isset( $_REQUEST['user_email2'] ) ? (string) $_REQUEST['user_email2'] : '';
				echo "\n", '<input type="hidden" name="user_email2" value="', esc_attr($user_email2), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'first_name', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$first_name = isset( $_REQUEST['first_name'] ) ? (string) $_REQUEST['first_name'] : '';
				echo "\n", '<input type="hidden" name="first_name" value="', esc_attr($first_name), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'last_name', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$last_name = isset( $_REQUEST['last_name'] ) ? (string) $_REQUEST['last_name'] : '';
				echo "\n", '<input type="hidden" name="last_name" value="', esc_attr($last_name), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'user_url', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$user_url = isset( $_REQUEST['user_url'] ) ? (string) $_REQUEST['user_url'] : '';
				echo "\n", '<input type="hidden" name="user_url" value="', esc_attr($user_url), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'aim', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$aim = isset( $_REQUEST['aim'] ) ? (string) $_REQUEST['aim'] : '';
				echo "\n", '<input type="hidden" name="aim" value="', esc_attr($aim), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'yahoo', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$yahoo = isset( $_REQUEST['yahoo'] ) ? (string) $_REQUEST['yahoo'] : '';
				echo "\n", '<input type="hidden" name="yahoo" value="', esc_attr($yahoo), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'jabber', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$jabber = isset( $_REQUEST['jabber'] ) ? (string) $_REQUEST['jabber'] : '';
				echo "\n", '<input type="hidden" name="jabber" value="', esc_attr($jabber), '" />';
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'show_fields' ) ) && in_array( 'about', $register_plus_redux->rpr_get_option( 'show_fields' ) ) ) {
				$description = isset( $_REQUEST['description'] ) ? (string) $_REQUEST['description'] : '';
				echo "\n", '<input type="hidden" name="description" value="', esc_attr($description), '" />';
			}

			$redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					if ( !empty( $meta_field['show_on_registration'] ) ) {
						$meta_key = esc_attr( $meta_field['meta_key'] );
						$meta_value = isset( $_REQUEST[$meta_key] ) ? (string) $_REQUEST[$meta_key] : '';
						echo "\n", '<input type="hidden" name="', $meta_key, '" value="', esc_attr($meta_value), '" />';
					}
				}
			}

			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) {
				$pass1 = isset( $_REQUEST['pass1'] ) ? (string) $_REQUEST['pass1'] : '';
				echo "\n", '<input type="hidden" name="pass1" value="', esc_attr($pass1), '" />';
				$pass2 = isset( $_REQUEST['pass2'] ) ? (string) $_REQUEST['pass2'] : '';
				echo "\n", '<input type="hidden" name="pass2" value="', esc_attr($pass2), '" />';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_code' ) ) {
				$invitation_code = isset( $_REQUEST['invitation_code'] ) ? (string) $_REQUEST['invitation_code'] : '';
				echo "\n", '<input type="hidden" name="invitation_code" value="', esc_attr($invitation_code), '" />';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_disclaimer' ) ) {
				$accept_disclaimer = isset( $_REQUEST['accept_disclaimer'] ) ? '1' : '0';
				echo "\n", '<input type="hidden" name="accept_disclaimer" value="', esc_attr($accept_disclaimer), '" />';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_license' ) ) {
				$accept_license = isset( $_REQUEST['accept_license'] ) ? '1' : '0';
				echo "\n", '<input type="hidden" name="accept_license" value="', esc_attr($accept_license), '" />';
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_privacy_policy' ) ) {
				$accept_privacy_policy = isset( $_REQUEST['accept_privacy_policy'] ) ? '1' : '0';
				echo "\n", '<input type="hidden" name="accept_privacy_policy" value="', esc_attr($accept_privacy_policy), '" />';
			}
		}

		public /*.array[string]mixed.*/ function rpr_filter_wpmu_validate_user_signup( /*.array[string]mixed.*/ $result ) {
			global $register_plus_redux;
			global $pagenow;
			if ( $pagenow != 'wp-signup.php' ) return $result;
			//trigger_error( sprintf( __( 'Register Plus Redux DEBUG: filter_wpmu_validate_user_signup($result=%s) from %s', 'register-plus-redux' ), print_r($result), $pagenow ) ); 
			if ( '1' === $register_plus_redux->rpr_get_option( 'username_is_email' ) ) {
				global $wpdb;

				if ( is_array( $result['errors']->errors ) && isset( $result['errors']->errors['user_name'] ) ) unset( $result['errors']->errors['user_name'] );
				if ( is_array( $result['errors']->error_data ) && isset( $result['errors']->error_data['user_name'] ) ) unset( $result['errors']->error_data['user_name'] );

				$result['user_name'] = $result['user_email'];
				$result['orig_username'] = $result['user_email'];

				// Check if the username has been used already.
				if ( username_exists( $result['user_name'] ) )
					$result['errors']->add('user_email', __('Sorry, that username already exists!'));

				// Has someone already signed up for this username?
				/*.object.*/ $signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE user_login = %s", $result['user_name'] ) );
				if ( !empty( $signup ) ) {
					// If registered more than two days ago, cancel registration and let this signup go through.
					if ( ( current_time( 'timestamp', TRUE ) - mysql2date('U', $signup->registered) ) > 2 * DAY_IN_SECONDS )
						$wpdb->delete( $wpdb->signups, array( 'user_login' => $result['user_name'] ) );
					else
						$result['errors']->add('user_email', __('That username is currently reserved but may be available in a couple of days.'));

					if ( $signup->active === 0 && $signup->user_email === $result['user_email'] )
						$result['errors']->add('user_email_used', __('username and email used'));
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'double_check_email' ) ) {
				if ( empty( $_POST['user_email2'] ) ) {
					$result['errors']->add( 'user_email2', __( 'Please confirm your e-mail address.', 'register-plus-redux' ) );
				}
				elseif ( $_POST['user_email'] !== $_POST['user_email2'] ) {
					$result['errors']->add( 'user_email2', __( 'Your e-mail address does not match.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'first_name', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['first_name'] ) ) {
					$result['errors']->add( 'first_name', __( 'Please enter your first name.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'last_name', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['last_name'] ) ) {
					$result['errors']->add( 'last_name', __( 'Please enter your last name.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'user_url', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['user_url'] ) ) {
					$result['errors']->add( 'user_url', __( 'Please enter your website URL.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'aim', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['aim'] ) ) {
					$result['errors']->add( 'aim', __( 'Please enter your AIM username.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'yahoo', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['yahoo'] ) ) {
					$result['errors']->add( 'yahoo', __( 'Please enter your Yahoo IM username.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'jabber', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['jabber'] ) ) {
					$result['errors']->add( 'jabber', __( 'Please enter your Jabber / Google Talk username.', 'register-plus-redux' ) );
				}
			}
			if ( is_array( $register_plus_redux->rpr_get_option( 'required_fields' ) ) && in_array( 'about', $register_plus_redux->rpr_get_option( 'required_fields' ) ) ) {
				if ( empty( $_POST['description'] ) ) {
					$result['errors']->add( 'description', __( 'Please enter some information about yourself.', 'register-plus-redux' ) );
				}
			}
			$redux_usermeta = get_option( 'register_plus_redux_usermeta-rv2' );
			if ( is_array( $redux_usermeta ) ) {
				foreach ( $redux_usermeta as $meta_field ) {
					$meta_key = $meta_field['meta_key'];
					if ( !empty( $meta_field['show_on_registration'] ) && !empty( $meta_field['require_on_registration'] ) && empty( $_POST[$meta_key] ) ) {
						$result['errors']->add( $meta_key, sprintf( __( 'Please enter a value for %s.', 'register-plus-redux' ), $meta_field['label'] ) );
					}
					if ( !empty( $meta_field['show_on_registration'] ) && ( 'textbox' === $meta_field['display'] ) && !empty( $meta_field['options'] ) && !preg_match( $meta_field['options'], (string) $_POST[$meta_key] ) ) {
						$result['errors']->add( $meta_key, sprintf( __( 'Please enter new value for %s, value specified is not in the correct format.', 'register-plus-redux' ), $meta_field['label'] ) );
					}
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) {
				if ( empty( $_POST['pass1'] ) ) {
					$result['errors']->add( 'pass1', __( 'Please enter a password.', 'register-plus-redux' ) );
				}
				elseif ( strlen( (string) $_POST['pass1'] ) < absint( $register_plus_redux->rpr_get_option( 'min_password_length' ) ) ) {
					$result['errors']->add( 'pass1', sprintf( __( 'Your password must be at least %d characters in length.', 'register-plus-redux' ), absint( $register_plus_redux->rpr_get_option( 'min_password_length' ) ) ) );
				}
				elseif ( '1' !== $register_plus_redux->rpr_get_option( 'disable_password_confirmation' ) && $_POST['pass1'] !== $_POST['pass2'] ) {
					$result['errors']->add( 'pass1', __( 'Your password does not match.', 'register-plus-redux' ) );
				}
				else {
					if ( isset( $_POST['pass2'] ) ) unset( $_POST['pass2'] );
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'enable_invitation_code' ) ) {
				if ( empty( $_POST['invitation_code'] ) && '1' === $register_plus_redux->rpr_get_option( 'require_invitation_code' ) ) {
					$result['errors']->add( 'invitation_code', __( 'Please enter an invitation code.', 'register-plus-redux' ) );
				}
				elseif ( !empty( $_POST['invitation_code'] ) ) {
					$invitation_code_bank = get_option( 'register_plus_redux_invitation_code_bank-rv1' );
					if ( is_array( $invitation_code_bank ) ) {
						if ( '1' !== $register_plus_redux->rpr_get_option( 'invitation_code_case_sensitive' ) ) {
							$_POST['invitation_code'] = strtolower( (string) $_POST['invitation_code'] );
							foreach ( $invitation_code_bank as $index => $invitation_code )
								$invitation_code_bank[$index] = strtolower( $invitation_code );
						}
						if ( is_array( $invitation_code_bank ) && !in_array( (string) $_POST['invitation_code'], $invitation_code_bank ) ) {
							$result['errors']->add( 'invitation_code', __( 'That invitation code is invalid.', 'register-plus-redux' ) );
						}
						else {
							// reverts lowercase key to stored case
							$key = array_search( (string) $_POST['invitation_code'], $invitation_code_bank );
							$invitation_code_bank = get_option( 'register_plus_redux_invitation_code_bank-rv1' );
							$_POST['invitation_code'] = $invitation_code_bank[$key];
							if ( '1' === $register_plus_redux->rpr_get_option( 'invitation_code_unique' ) ) {
								global $wpdb;
								if ( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key = 'invitation_code' AND meta_value = %s;", (string) $_POST['invitation_code'] ) ) ) {
									$result['errors']->add( 'invitation_code', __( 'This invitation code is already in use, please enter a unique invitation code.', 'register-plus-redux' ) );
								}
							}
						}
					}
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_disclaimer' ) && '1' === $register_plus_redux->rpr_get_option( 'require_disclaimer_agree' ) ) {
				if ( empty( $_POST['accept_disclaimer'] ) ) {
					$result['errors']->add( 'disclaimer', sprintf( __( 'Please accept the %s', 'register-plus-redux' ), esc_html( $register_plus_redux->rpr_get_option( 'message_disclaimer_title' ) ) ) . '.' );
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_license' ) && '1' === $register_plus_redux->rpr_get_option( 'require_license_agree' ) ) {
				if ( empty( $_POST['accept_license'] ) ) {
					$result['errors']->add( 'license', sprintf( __( 'Please accept the %s', 'register-plus-redux' ), esc_html( $register_plus_redux->rpr_get_option( 'message_license_title' ) ) ) . '.' );
				}
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'show_privacy_policy' ) && '1' === $register_plus_redux->rpr_get_option( 'require_privacy_policy_agree' ) ) {
				if ( empty( $_POST['accept_privacy_policy'] ) ) {
					$result['errors']->add( 'privacy_policy', sprintf( __( 'Please accept the %s', 'register-plus-redux' ), esc_html( $register_plus_redux->rpr_get_option( 'message_privacy_policy_title' ) ) ) . '.' );
				}
			}
			return $result;
		}

		public /*.array[string]string.*/ function filter_add_signup_meta( /*.array[string]string.*/ $meta ) {
			foreach ( $_POST as $key => $value ) {
				$meta[$key] = $value;
			}
			$meta['signup_http_referer'] = $_SERVER['HTTP_REFERER'];
			$meta['signup_registered_from_ip'] = $_SERVER['REMOTE_ADDR'];
			$meta['signup_registered_from_host'] = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
			return $meta;
		}

		//TODO: Custom email verification messages here
		public /*.bool.*/ function filter_wpmu_signup_user_notification( /*.string.*/ $user, /*.string.*/ $user_email, /*.string.*/ $key, /*.array[string]string.*/ $meta ) {
			global $register_plus_redux;
			if ( '1' !== $register_plus_redux->rpr_get_option( 'verify_user_email' ) ) {
				$_REQUEST['key'] = $key;
			}
			return TRUE;

			$admin_email = get_site_option( 'admin_email' );
			if ( $admin_email == '' )
				$admin_email = 'support@' . $_SERVER['SERVER_NAME'];
			$from_name = get_site_option( 'site_name' ) == '' ? 'WordPress' : esc_html( get_site_option( 'site_name' ) );
			$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
			$message = sprintf( __( "To activate your user, please click the following link:\n\n%verification_url%\n\nAfter you activate, you will receive *another email* with your login." ), site_url( "wp-activate.php?key=$key" ) );
			$subject = sprintf( __( '[%from_name%] Activate %user_login%' ), $from_name, $user );
			wp_mail($user_email, $subject, $message, $message_headers);
			return TRUE;
		}

		public /*.bool.*/ function filter_wpmu_signup_blog_notification( /*.string.*/ $domain, /*.string.*/ $path, /*.string.*/ $title, /*.string.*/ $user, /*.string.*/ $user_email, /*.string.*/ $key, /*.array[string]string.*/ $meta ) {
			return $this->filter_wpmu_signup_user_notification( $user, $user_email, $key, $meta );
		}

		public /*.void.*/ function rpr_signup_finished() {
			global $register_plus_redux;
			if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_admin' ) && $register_plus_redux->rpr_get_option( 'message_verify_user_admin' ) ) {
				?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery("#content").html("<?php echo $register_plus_redux->rpr_get_option( 'message_verify_user_admin' ) ?>");
				});
				</script>
				<?php
			}
			if ( '1' === $register_plus_redux->rpr_get_option( 'verify_user_email' ) && $register_plus_redux->rpr_get_option( 'message_verify_user_email' ) ) {
				?>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery("#content").html("<?php echo $register_plus_redux->rpr_get_option( 'message_verify_user_email' ) ?>");
				});
				</script>
				<?php
			}
		}

		public /*.string.*/ function rpr_signup_filter_random_password( /*.string.*/ $password ) {
			global $register_plus_redux;
			global $pagenow;
			if ( 'wp-signup.php' === $pagenow && '1' === $register_plus_redux->rpr_get_option( 'user_set_password' ) ) {
				if ( isset( $_POST['pass1'] ) ) {
					$password = sanitize_text_field( (string) $_POST['pass1'] );
					//unset( $_POST['pass1'] );
				}
			}
			return $password;
		}

		public /*.void.*/ function rpr_preprocess_signup_form() {
			global $active_signup, $stage;
			switch ( $stage ) {
				case 'user-signup' :
					if ( $active_signup == 'all' || $_POST[ 'signup_for' ] == 'blog' && $active_signup == 'blog' || $_POST[ 'signup_for' ] == 'user' && $active_signup == 'user' ) {
						/* begin validate_user_signup stage */
						// validate signup form, do wpmu_validate_user_signup action
						$result = wpmu_validate_user_signup( isset( $_POST['user_name'] ) ? (string) $_POST['user_name'] : "", isset( $_POST['user_email'] ) ? (string) $_POST['user_email'] : "" );
						extract( $result );
						if ( $errors->get_error_code() ) {
							echo "signup_user";
							signup_user($user_name, $user_email, $errors);
							do_action( 'after_signup_form' );
							get_footer();
							exit();
						}
						if ( 'blog' === $_POST['signup_for'] ) {
							echo "signup_blog";
							signup_blog($user_name, $user_email);
							do_action( 'after_signup_form' );
							get_footer();
							exit();
						}
						// collect meta, commit user to database, send email
						wpmu_signup_user( $user_name, $user_email, apply_filters( 'add_signup_meta', array() ) );
						// previously, displayed confirm_user_signup message before signup_finished action
						do_action( 'signup_finished' );
						/* end validate_user_signup stage */
					}
					else {
						_e( 'User registration has been disabled.' );
						?>
						</div>
						</div>
						<?php do_action( 'after_signup_form' );
						get_footer();
						exit();
					}
					break;
				case 'blog-signup' :
					if ( $active_signup == 'all' || $active_signup == 'blog' ) {
						/* begin validate_blog_signup stage */
						$result = wpmu_validate_user_signup( isset( $_POST['user_name'] ) ? (string) $_POST['user_name'] : "", isset( $_POST['user_email'] ) ? (string) $_POST['user_email'] : "" );
						extract( $result );
						if ( $errors->get_error_code() ) {
							echo "signup_user";
							signup_user($user_name, $user_email, $errors);
							do_action( 'after_signup_form' );
							get_footer();
							exit();
						}
						$result = wpmu_validate_blog_signup( isset( $_POST['blogname'] ) ? (string) $_POST['blogname'] : "", isset( $_POST['blog_title'] ) ? (string) $_POST['blog_title'] : "" );
						extract( $result );
						if ( $errors->get_error_code() ) {
							signup_blog($user_name, $user_email, $blogname, $blog_title, $errors);
							do_action( 'after_signup_form' );
							get_footer();
							exit();
						}
						// collect meta, commit user to database, send email
						$meta = array ( 'lang_id' => 1, 
								'public'  => (int) $_POST['blog_public'] );
						wpmu_signup_blog( $domain, $path, $blog_title, $user_name, $user_email, apply_filters( 'add_signup_meta', $meta ) );
						// previously, displayed confirm_blog_signup message before signup_finished action
						do_action( 'signup_finished' );
						/* end validate_blog_signup stage */
					}
					else {
						_e( 'Site registration has been disabled.' );
						?>
						</div>
						</div>
						<?php do_action( 'after_signup_form' );
						get_footer();
						exit();
					}
					break;
				default :
					return;
			}
			/* begin wp-activate page */
			$key = (string) $_REQUEST['key'];
			// wpmu_create_user, wpmu_welcome_user_notification, add_new_user_to_blog, do wpmu_activate_user action
			$result = wpmu_activate_signup($key);
			if ( is_wp_error($result) ) {
				if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
				    $signup = $result->get_error_data();
					?>
					<h2><?php _e('Your account is now active!'); ?></h2>
					<?php
					echo '<p class="lead-in">';
					if ( $signup->domain . $signup->path == '' ) {
						printf( __('Your account has been activated. You may now <a href="%1$s">log in</a> to the site using your chosen username of &#8220;%2$s&#8221;. Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.'), network_site_url( 'wp-login.php', 'login' ), $signup->user_login, $signup->user_email, wp_lostpassword_url() );
					} else {
						printf( __('Your site at <a href="%1$s">%2$s</a> is active. You may now log in to your site using your chosen username of &#8220;%3$s&#8221;. Please check your email inbox at %4$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, wp_lostpassword_url() );
					}
					echo '</p>';
				} else {
					?>
					<h2><?php _e('An error occurred during the activation'); ?></h2>
					<?php
				    echo '<p>'.$result->get_error_message().'</p>';
				}
			} else {
				//TODO: Why not reference $result->blog_id?
				extract( $result );
				if ( isset( $blog_id ) ) $url = get_blogaddress_by_id( (int) $blog_id );
				$user = get_userdata( (int) $user_id );
				?>
				<h2><?php _e('Your account is now active!'); ?></h2>
				<div id="signup-welcome">
					<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
					<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $password; ?></p>
				</div>
				<?php if ( isset( $blog_id ) && $url != network_home_url('', 'http') ) : ?>
					<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Log in</a>'), $url, $url . 'wp-login.php' ); ?></p>
				<?php else: ?>
					<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.' ), network_site_url('wp-login.php', 'login'), network_home_url() ); ?></p>
				<?php endif;
			}
			?>
			</div>
			<script type="text/javascript">
				var key_input = document.getElementById('key');
				key_input && key_input.focus();
			</script>
			<?php get_footer(); ?>
			<?php
			exit();
		}
	}
}

if ( class_exists( 'RPR_Signup' ) ) $rpr_signup = new RPR_Signup();
?>
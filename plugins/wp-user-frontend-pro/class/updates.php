<?php

class WPUF_Updates {

    const base_url = 'http://wedevs.com/';
    const product_id = 'wpuf-pro';
    const option = 'wpuf_license';
    const slug = 'wp-user-frontend-pro';

    function __construct() {

        add_action( 'wpuf_admin_menu', array($this, 'admin_menu'), 99 );
        
        if ( is_multisite() ) {
            if ( is_main_site() ) {
                add_action( 'admin_notices', array($this, 'license_enter_notice') );
                add_action( 'admin_notices', array($this, 'license_check_notice') );
            }
        } else {
            add_action( 'admin_notices', array($this, 'license_enter_notice') );
            add_action( 'admin_notices', array($this, 'license_check_notice') );
        }

        add_filter( 'pre_set_site_transient_update_plugins', array($this, 'check_update') );
        add_filter( 'plugins_api', array(&$this, 'check_info'), 10, 3 );
    }

    /**
     * Add admin menu to User Frontend option
     * 
     * @return void
     */
    function admin_menu() {
        add_submenu_page( 'wpuf-admin-opt', __( 'Updates', 'wpuf' ), __( 'Updates', 'wpuf' ), 'activate_plugins', 'wpuf_updates', array($this, 'plugin_update') );
    }

    /**
     * Get license key
     * 
     * @return array
     */
    function get_license_key() {
        return get_option( self::option, array() );
    }

    /**
     * Prompts the user to add license key if it's not already filled out
     * 
     * @return void
     */
    function license_enter_notice() {
        if ( $key = $this->get_license_key() ) {
            return;
        }
        ?>
        <div class="error">
            <p><?php printf( __( 'Please <a href="%s">enter</a> your <strong>WP User Frontend</strong> plugin license key to get regular update and support.' ), admin_url( 'admin.php?page=wpuf_updates' ) ); ?></p>
        </div>
        <?php
    }

    /**
     * Check activation every 12 hours to the server
     * 
     * @return void
     */
    function license_check_notice() {
        if ( !$key = $this->get_license_key() ) {
            return;
        }

        $error = __( 'Pleae activate your copy' );

        $license_status = get_option( 'wpuf_license_status' );

        if ( $license_status && $license_status->activated ) {

            $status = get_transient( self::option );
            if ( false === $status ) {
                $status = $this->activation();

                $duration = 60 * 60 * 12; // 12 hour
                set_transient( self::option, $status, $duration );
            }

            if ( $status && $status->success ) {
                return;
            }
            
            // may be the request didn't completed
            if ( !isset( $status->error )) {
                return;
            }

            $error = $status->error;
        }
        ?>
        <div class="error">
            <p><strong><?php _e( 'WP User Frontend Error:', 'wpuf' ); ?></strong> <?php echo $error; ?></p>
        </div>
        <?php
    }

    /**
     * Activation request to the plugin server
     * 
     * @return object
     */
    function activation( $request = 'check' ) {
        if ( !$option = $this->get_license_key() ) {
            return;
        }

        $args = array(
            'request' => $request,
            'email' => $option['email'],
            'licence_key' => $option['key'],
            'product_id' => self::product_id,
            'instance' => home_url()
        );

        $base_url = add_query_arg( 'wc-api', 'software-api', self::base_url );
        $target_url = $base_url . '&' . http_build_query( $args );
        $response = wp_remote_get( $target_url, array( 'timeout' => 15 ) );
        $update = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            return false;
        }

        return json_decode( $update );
    }

    /**
     * Integrates into plugin update api check
     * 
     * @param object $transient
     * @return object
     */
    function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $remote_info = $this->get_info();

        if ( !$remote_info ) {
            return $transient;
        }

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( version_compare( $plugin_version, $remote_info->latest, '<' ) ) {

            $obj = new stdClass();
            $obj->slug = self::slug;
            $obj->new_version = $remote_info->latest;
            $obj->url = self::base_url;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->package = $remote_info->latest_url;
            }

            $basefile = plugin_basename( dirname( dirname( __FILE__ ) ) . '/wpuf.php' );
            $transient->response[$basefile] = $obj;
        }

        return $transient;
    }

    /**
     * Plugin changelog information popup
     * 
     * @param type $false
     * @param type $action
     * @param type $args
     * @return \stdClass|boolean
     */
    function check_info( $false, $action, $args ) {
        if ( self::slug == $args->slug ) {

            $remote_info = $this->get_info();

            $obj = new stdClass();
            $obj->slug = self::slug;
            $obj->new_version = $remote_info->latest;

            if ( isset( $remote_info->latest_url ) ) {
                $obj->download_link = $remote_info->latest_url;
            }

            $obj->sections = array(
                'description' => $remote_info->msg
            );

            return $obj;
        }

        return false;
    }

    /**
     * Collects current plugin information
     * 
     * @return array
     */
    function get_current_plugin_info() {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';

        $plugin_data = get_plugin_data( dirname( dirname( __FILE__ ) ) . '/wpuf.php' );
        $plugin_name = $plugin_data['Name'];
        $plugin_version = $plugin_data['Version'];

        return array($plugin_name, $plugin_version);
    }

    /**
     * Get plugin update information from server
     * 
     * @global string $wp_version
     * @global object $wpdb
     * @return boolean
     */
    function get_info() {
        global $wp_version, $wpdb;

        list( $plugin_name, $plugin_version) = $this->get_current_plugin_info();

        if ( is_multisite() ) {
            $user_count = get_user_count();
            $num_blogs = get_blog_count();
            $wp_install = network_site_url();
            $multisite_enabled = 1;
        } else {
            $user_count = count_users();
            $multisite_enabled = 0;
            $num_blogs = 1;
            $wp_install = home_url( '/' );
        }

        $locale = apply_filters( 'core_version_check_locale', get_locale() );

        if ( method_exists( $wpdb, 'db_version' ) )
            $mysql_version = preg_replace( '/[^0-9.].*/', '', $wpdb->db_version() );
        else
            $mysql_version = 'N/A';

        $license = $this->get_license_key();

        $params = array(
            'timeout' => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body' => array(
                'name' => $plugin_name,
                'slug' => self::slug,
                'type' => 'plugin',
                'version' => $plugin_version,
                'wp_version' => $wp_version,
                'php_version' => phpversion(),
                'action' => 'theme_check',
                'locale' => $locale,
                'mysql' => $mysql_version,
                'blogs' => $num_blogs,
                'users' => $user_count['total_users'],
                'multisite_enabled' => $multisite_enabled,
                'site_url' => $wp_install,
                'license' => isset( $license['key'] ) ? $license['key'] : '',
                'license_email' => isset( $license['email'] ) ? $license['email'] : '',
                'product_id' => self::product_id
            )
        );

        $response = wp_remote_post( self::base_url . '?action=wedevs_update_check', $params );
        $update = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
            return false;
        }

        return json_decode( $update );
    }

    /**
     * Plugin license enter admin UI
     * 
     * @return void
     */
    function plugin_update() {
        $errors = array();
        if ( isset( $_POST['submit'] ) ) {
            if ( empty( $_POST['email'] ) ) {
                $errors[] = __( 'Empty email address', 'wpuf' );
            }

            if ( empty( $_POST['license_key'] ) ) {
                $errors[] = __( 'Empty license key', 'wpuf' );
            }

            if ( !$errors ) {
                update_option( self::option, array('email' => $_POST['email'], 'key' => $_POST['license_key']) );
                delete_transient( self::option );

                $license_status = get_option( 'wpuf_license_status' );

                if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
                    $response = $this->activation( 'activation' );

                    if ( $response && isset( $response->activated ) && $response->activated ) {
                        update_option( 'wpuf_license_status', $response );
                    }
                }


                echo '<div class="updated"><p>' . __( 'Settings Saved', 'wpuf' ) . '</p></div>';
            }
        }

        $license = $this->get_license_key();
        $email = $license ? $license['email'] : '';
        $key = $license ? $license['key'] : '';
        ?>
        <div class="wrap">
            <?php screen_icon( 'plugins' ); ?>
            <h2><?php _e( 'Plugin Activation', 'wpuf' ); ?></h2>

            <p class="description">
                Enter the E-mail address that was used for purchasing the plugin and the license key.
                We recommend you to enter those details to get regular <strong>plugin update and support</strong>.
            </p>

            <?php
            if ( $errors ) {
                foreach ($errors as $error) {
                    ?>
                    <div class="error"><p><?php echo $error; ?></p></div>
                    <?php
                }
            }

            $license_status = get_option( 'wpuf_license_status' );
            if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
                ?>

                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th><?php _e( 'E-mail Address', 'wpuf' ); ?></th>
                            <td>
                                <input type="email" name="email" class="regular-text" value="<?php echo esc_attr( $email ); ?>" required>
                                <span class="description"><?php _e( 'Enter your purchase Email address', 'wpuf' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e( 'License Key', 'wpuf' ); ?></th>
                            <td>
                                <input type="text" name="license_key" class="regular-text" value="<?php echo esc_attr( $key ); ?>">
                                <span class="description"><?php _e( 'Enter your license key', 'wpuf' ); ?></span>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button( 'Save & Activate' ); ?>
                </form>
            <?php } else { ?>

                <div class="updated">
                    <p><?php _e( 'Plugin is activated', 'wpuf' ); ?></p>
                </div>

            <?php } ?>
        </div>
        <?php
    }

}
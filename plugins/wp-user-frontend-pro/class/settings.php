<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
class WPUF_Settings {

    private $settings_api;
    private static $_instance;

    function __construct() {

        if ( !class_exists( 'WeDevs_Settings_API' ) ) {
            require_once dirname( dirname( __FILE__ ) ) . '/lib/class.settings-api.php';
        }

        $this->settings_api = new WeDevs_Settings_API();

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }
    
    public static function init() {
        if ( !self::$_instance ) {
            self::$_instance = new WPUF_Settings();
        }

        return self::$_instance;
    }

    function admin_init() {
        
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Register the admin menu
     *
     * @since 1.0
     */
    function admin_menu() {
        $capability = wpuf_admin_role();

        add_menu_page( __( 'WP User Frontend', 'wpuf' ), __( 'User Frontend', 'wpuf' ), $capability, 'wpuf-admin-opt', array($this, 'plugin_page'), null, 55 );
        add_submenu_page( 'wpuf-admin-opt', __( 'Subscription', 'wpuf' ), __( 'Subscription', 'wpuf' ), $capability, 'wpuf_subscription', array($this, 'subscription_page') );
        add_submenu_page( 'wpuf-admin-opt', __( 'Transaction', 'wpuf' ), __( 'Transaction', 'wpuf' ), $capability, 'wpuf_transaction', array($this, 'transaction_page') );
        add_submenu_page( 'wpuf-admin-opt', __( 'Add-ons', 'wpuf' ), __( 'Add-ons', 'wpuf' ), $capability, 'wpuf_addons', array($this, 'addons_page') );
        do_action( 'wpuf_admin_menu' );
        add_submenu_page( 'wpuf-admin-opt', __( 'Settings', 'wpuf' ), __( 'Settings', 'wpuf' ), $capability, 'wpuf-settings', array($this, 'plugin_page') );
    }

    /**
     * WPUF Settings sections
     *
     * @since 1.0
     * @return array
     */
    function get_settings_sections() {
        return wpuf_settings_sections();
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        return wpuf_settings_fields();
    }

    function plugin_page() {
        ?>
        <div class="wrap">

            <?php screen_icon( 'options-general' ); ?>

            <?php
            settings_errors();

            $this->settings_api->show_navigation();
            $this->settings_api->show_forms();
            ?>

        </div>
        <?php
    }

    function transaction_page() {
        require_once dirname( dirname( __FILE__ ) ) . '/admin/transaction.php';
    }

    function subscription_page() {
        require_once dirname( dirname( __FILE__ ) ) . '/admin/subscription.php';
    }

    function addons_page() {
        require_once dirname( dirname( __FILE__ ) ) . '/admin/add-ons.php';
    }

}
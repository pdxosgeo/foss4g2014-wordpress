<?php

/**
 * WP User Frontend payment gateway handler
 *
 * @since 0.8
 * @package WP User Frontend
 */
class WPUF_Payment {

    function __construct() {
        add_action( 'init', array($this, 'send_to_gateway') );
        add_action( 'wpuf_payment_received', array($this, 'payment_notify_admin') );

        add_filter( 'the_content', array($this, 'payment_page') );
    }

    public static function get_payment_gateways() {

        // default, built-in gateways
        $gateways = array(
            'paypal' => array(
                'admin_label' => __( 'PayPal', 'wpuf' ),
                'checkout_label' => __( 'PayPal', 'wpuf' ),
                'icon' => apply_filters( 'wpuf_paypal_checkout_icon', plugins_url( '/images/paypal.png', dirname( __FILE__ ) ) )
             ),
            'bank' => array(
                'admin_label' => __( 'Bank Payment', 'wpuf' ),
                'checkout_label' => __( 'Bank Payment', 'wpuf' ),
            )
        );

        $gateways = apply_filters( 'wpuf_payment_gateways', $gateways );

        return $gateways;
    }

    /**
     * Get active payment gateways
     *
     * @return array
     */
    function get_active_gateways() {
        $all_gateways = wpuf_get_gateways( 'checkout' );
        $active_gateways = wpuf_get_option( 'active_gateways', 'wpuf_payment' );
        $active_gateways = is_array( $active_gateways ) ? $active_gateways : array();
        $gateways = array();

        foreach ($all_gateways as $id => $label) {
            if ( array_key_exists( $id, $active_gateways ) ) {
                $gateways[$id] = $label;
            }
        }

        return $gateways;
    }

    function payment_page( $content ) {
        global $post;

        $pay_page = intval( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );

        if ( $post->ID == $pay_page && isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'wpuf_pay' ) {

            if ( !is_user_logged_in() ) {
                return __( 'You are not logged in', 'wpuf' );
            }

            $type = ( $_REQUEST['type'] == 'post' ) ? 'post' : 'pack';
            $post_id = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;
            $pack_id = isset( $_REQUEST['pack_id'] ) ? intval( $_REQUEST['pack_id'] ) : 0;
            $is_free = false;
            
            if ( $pack_id ) {
                $pack_detail = WPUF_Subscription::get_subscription( $pack_id );
                
                if ( $pack_detail->cost == '0') {
                    $is_free = true;
                }
            }

            $gateways = $this->get_active_gateways();
            
            if ( isset( $_REQUEST['wpuf_payment_submit'] ) ) {
                $selected_gateway = $_REQUEST['wpuf_payment_method'];
            } else {
                $selected_gateway = 'paypal';
            }

            ob_start();
            
            if ( $pack_id && $is_free ) {
                $current_user = wp_get_current_user();
                
                $wpuf_subscription = WPUF_Subscription::init();
                
                if( ! WPUF_Subscription::has_used_free_pack( $current_user->ID, $pack_id) ) {
                    $wpuf_subscription->new_subscription( $current_user->ID, $pack_id );
                    WPUF_Subscription::add_used_free_pack( $current_user->ID, $pack_id );
                    
                    $message = apply_filters( 'wpuf_fp_activated_msg', __( 'Your free package has been activated. Enjoy!' ), 'wpuf' );
                } else {
                    $message = apply_filters( 'wpuf_fp_activated_error', __( 'You already have activated a free package previously.' ), 'wpuf' );
                }
                ?>
                    <div class="wpuf-info"><?php echo $message; ?></div>
                <?php
            } else {
                ?>
                <?php if ( count( $gateways ) ) { ?>
                    <form id="wpuf-payment-gateway" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
                        <?php wp_nonce_field( 'wpuf_payment_gateway' ) ?>
                        <?php do_action( 'wpuf_before_payment_gateway' ); ?>
                        <p>
                            <label for="wpuf-payment-method"><?php _e( 'Choose Your Payment Method', 'wpuf' ); ?></label><br />

                            <ul class="wpuf-payment-gateways">
                                <?php foreach ($gateways as $gateway_id => $gateway) { ?>
                                    <li class="wpuf-gateway-<?php echo $gateway_id; ?>">
                                        <label>
                                            <input name="wpuf_payment_method" type="radio" value="<?php echo esc_attr( $gateway_id ); ?>" <?php checked( $selected_gateway, $gateway_id ); ?>>
                                            <?php
                                            echo $gateway['label'];

                                            if ( !empty( $gateway['icon'] ) ) {
                                                printf(' <img src="%s" alt="image">', $gateway['icon'] );
                                            }
                                            ?>
                                        </label>

                                        <div class="wpuf-payment-instruction" style="display: none;">
                                            <div class="wpuf-instruction"><?php echo wpuf_get_option( 'gate_instruct_' . $gateway_id, 'wpuf_payment' ); ?></div>

                                            <?php do_action( 'wpuf_gateway_form_' . $gateway_id, $type, $post_id, $pack_id ); ?>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>

                        </p>
                        <?php do_action( 'wpuf_after_payment_gateway' ); ?>
                        <p>
                            <input type="hidden" name="type" value="<?php echo $type; ?>" />
                            <input type="hidden" name="action" value="wpuf_pay" />
                            <?php if ( $post_id ) { ?>
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
                            <?php } ?>

                            <?php if ( $pack_id ) { ?>
                                <input type="hidden" name="pack_id" value="<?php echo $pack_id; ?>" />
                            <?php } ?>
                            <input type="submit" name="wpuf_payment_submit" value="<?php _e( 'Proceed', 'wpuf' ); ?>"/>
                        </p>
                    </form>
                <?php } else { ?>
                    <?php _e( 'No Payment gateway found', 'wpuf' ); ?>
                <?php } ?>

                <?php
            }
            
            return ob_get_clean();
        }

        return $content;
    }

    /**
     * Send payment handler to the gateway
     *
     * This function sends the payment handler mechanism to the selected
     * gateway. If 'paypal' is selected, then a particular action is being
     * called. A  listener function can be invoked for that gateway to handle
     * the request and send it to the gateway.
     *
     * Need to use `wpuf_gateway_{$gateway_name}
     */
    function send_to_gateway() {
        if ( isset( $_POST['wpuf_payment_submit'] ) && $_POST['action'] == 'wpuf_pay' && wp_verify_nonce( $_POST['_wpnonce'], 'wpuf_payment_gateway' ) ) {

            $post_id = isset( $_REQUEST['post_id'] ) ? intval( $_REQUEST['post_id'] ) : 0;
            $pack_id = isset( $_REQUEST['pack_id'] ) ? intval( $_REQUEST['pack_id'] ) : 0;
            $gateway = $_POST['wpuf_payment_method'];
            $type = $_POST['type'];

            $userdata = wp_get_current_user();

            switch ($type) {
                case 'post':
                    $post = get_post( $post_id );
                    $amount = wpuf_get_option( 'cost_per_post', 'wpuf_payment' );
                    $item_number = get_post_meta( $post_id, '_wpuf_order_id', true );
                    $item_name = $post->post_title;
                    break;

                case 'pack':
                    $subscription = new WPUF_Subscription();
                    $pack = $subscription->get_subscription( $pack_id );

                    $amount = $pack->cost;
                    $item_name = $pack->name;
                    $item_number = $pack->id;
                    break;
            }

            $payment_vars = array(
                'currency' => wpuf_get_option( 'currency', 'wpuf_payment' ),
                'price' => $amount,
                'item_number' => $item_number,
                'item_name' => $item_name,
                'type' => $type,
                'user_info' => array(
                    'id' => $userdata->ID,
                    'email' => $userdata->user_email,
                    'first_name' => $userdata->first_name,
                    'last_name' => $userdata->last_name
                ),
                'date' => date( 'Y-m-d H:i:s' ),
                'post_data' => $_POST,
            );

            do_action( 'wpuf_gateway_' . $gateway, $payment_vars );
        }
    }

    /**
     * Insert payment info to database
     *
     * @global object $wpdb
     * @param array $data payment data to insert
     * @param int $transaction_id the transaction id in case of update
     */
    public static function insert_payment( $data, $transaction_id = 0 ) {
        global $wpdb;

        //check if it's already there
        $sql = "SELECT transaction_id
                FROM " . $wpdb->prefix . "wpuf_transaction
                WHERE transaction_id = '" . $wpdb->escape( $transaction_id ) . "' LIMIT 1";

        $result = $wpdb->get_row( $sql );

        if ( !$result ) {
            $wpdb->insert( $wpdb->prefix . 'wpuf_transaction', $data );
        } else {
            $wpdb->update( $wpdb->prefix . 'wpuf_transaction', $data, array('transaction_id' => $transaction_id) );
        }

        do_action( 'wpuf_payment_received', $data );
    }

    /**
     * Send payment received mail
     *
     * @param array $info payment information
     */
    function payment_notify_admin( $info ) {
        $headers = "From: " . get_bloginfo( 'name' ) . " <" . get_bloginfo( 'admin_email' ) . ">" . "\r\n\\";
        $subject = sprintf( __( '[%s] Payment Received', 'wpuf' ), get_bloginfo( 'name' ) );
        $msg = sprintf( __( 'New payment received at %s', 'wpuf' ), get_bloginfo( 'name' ) );

        $receiver = get_bloginfo( 'admin_email' );
        wp_mail( $receiver, $subject, $msg, $headers );
    }

}
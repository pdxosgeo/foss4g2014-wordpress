<?php

/**
 * WPUF subscription manager
 *
 * @since 0.2
 * @author Tareq Hasan
 * @package WP User Frontend
 */
class WPUF_Subscription {
    
    private static $_instance;

    function __construct() {
        add_filter( 'wpuf_add_post_args', array($this, 'set_pending'), 10, 1 );
        add_filter( 'wpuf_add_post_redirect', array($this, 'post_redirect'), 10, 2 );

        add_filter( 'wpuf_addpost_notice', array($this, 'force_pack_notice'), 20 );
        add_filter( 'wpuf_can_post', array($this, 'force_pack_permission'), 20 );

        add_action( 'personal_options_update', array($this, 'profile_subscription_update') );
        add_action( 'edit_user_profile_update', array($this, 'profile_subscription_update') );

        add_action( 'show_user_profile', array($this, 'profile_subscription_details'), 30 );
        add_action( 'edit_user_profile', array($this, 'profile_subscription_details'), 30 );

        add_action( 'wpuf_add_post_form_top', array($this, 'add_post_info') );

        add_action( 'wpuf_add_post_after_insert', array($this, 'monitor_new_post'), 10, 1 );
        add_action( 'wpuf_payment_received', array($this, 'payment_received') );

        add_shortcode( 'wpuf_sub_info', array($this, 'subscription_info') );
        add_shortcode( 'wpuf_sub_pack', array($this, 'subscription_packs') );
    }
    
    public static function init() {
        if ( !self::$_instance ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Get a subscription row from database
     *
     * @global object $wpdb
     * @param int $sub_id subscription pack id
     * @return object|bool
     */
    public static function get_subscription( $sub_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}wpuf_subscription WHERE id=$sub_id";
        $row = $wpdb->get_row( $sql );

        return $row;
    }

    /**
     * Get all the subscription package
     *
     * @global object $wpdb
     * @return object|bool
     */
    public function get_subscription_packs() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}wpuf_subscription ORDER BY created DESC";
        $result = $wpdb->get_results( $sql );

        return $result;
    }

    /**
     * Checks against the user, if he is valid for posting new post
     *
     * @global object $userdata
     * @return bool
     */
    public static function has_user_error() {
        global $userdata;

        // bail out if charging is not enabled
        if ( wpuf_get_option( 'charge_posting', 'wpuf_payment' ) != 'yes' ) {
            return false;
        }

        $error = false;

        // Check pack duration and post count
        $duration = ( $userdata->wpuf_sub_validity ) ? $userdata->wpuf_sub_validity : 0;
        $count = ( $userdata->wpuf_sub_pcount ) ? $userdata->wpuf_sub_pcount : 0;

        // are both them empty?
        if ( !$duration || !$count ) {
            return true;
        }

        //if duration is expired
        if ( $duration != 'unlimited' ) {
            $diff = strtotime( $duration ) - time();
            if ( $diff < 0 ) {
                return true;
            }
        }

        //no balance
        if ( $count != 'unlimited' && $count <= 0 ) {
            return true;
        }

        return false;
    }

    /**
     * Set the new post status if charging is active
     *
     * @param string $postdata
     * @return string
     */
    function set_pending( $postdata ) {

        if ( wpuf_get_option( 'charge_posting', 'wpuf_payment' ) == 'yes' ) {
            $postdata['post_status'] = 'pending';
        }

        return $postdata;
    }

    /**
     * Checks the posting validity after a new post
     *
     * @global object $userdata
     * @global object $wpdb
     * @param int $post_id
     */
    function monitor_new_post( $post_id ) {
        global $wpdb;

        $userdata = get_userdata( get_current_user_id() );

        if ( self::has_user_error( $post_id ) ) {
            //there is some error and it needs payment
            //add a uniqid to track the post easily
            $order_id = uniqid( rand( 10, 1000 ), false );
            update_post_meta( $post_id, '_wpuf_order_id', $order_id, true );
        } else {
            $count = ( $userdata->wpuf_sub_pcount ) ? $userdata->wpuf_sub_pcount : 0;

            //decrease the post count, if not umlimited
            if ( $count != 'unlimited' ) {
                $count = intval( $count );
                update_user_meta( $userdata->ID, 'wpuf_sub_pcount', $count - 1 );

                //set the post status to publish
                $this->set_post_status( $post_id );
            }
        }
    }

    /**
     * Redirect to payment page after new post
     *
     * @param string $str
     * @param type $post_id
     * @return string
     */
    function post_redirect( $response, $post_id ) {

        if ( self::has_user_error( $post_id ) ) {

            $order_id = get_post_meta( $post_id, '_wpuf_order_id', true );

            // check if there is a order ID
            if ( $order_id ) {
                $response['show_message'] = false;
                $response['redirect_to'] = add_query_arg( array(
                    'action' => 'wpuf_pay',
                    'type' => 'post',
                    'post_id' => $post_id
                ), get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) ) );

                return $response;
            }
        }

        return $response;
    }

    /**
     * Perform actions when a new payment is made
     *
     * @param array $info payment info
     */
    function payment_received( $info ) {

        if ( $info['post_id'] ) {
            $this->handle_post_publish( $info['post_id'] );
        } else if ( $info['pack_id'] ) {
            $this->new_subscription( $info['user_id'], $info['pack_id'] );
        }
    }

    /**
     * Store new subscription info on user profile
     *
     * if data = 0, means 'unlimited'
     *
     * @param int $user_id
     * @param int $pack_id subscription pack id
     */
    public function new_subscription( $user_id, $pack_id ) {
        $subscription = $this->get_subscription( $pack_id );

        if ( $user_id && $subscription ) {

            //store the duration
            if ( $subscription->duration == 0 ) {
                update_user_meta( $user_id, 'wpuf_sub_validity', 'unlimited' );
            } else {
                //store that future date in usermeta
                $duration = date( 'Y-m-d G:i:s', strtotime( date( 'Y-m-d G:i:s', time() ) . " +{$subscription->duration} day" ) );
                update_user_meta( $user_id, 'wpuf_sub_validity', $duration );
            }

            //store post count
            if ( $subscription->count == 0 ) {
                update_user_meta( $user_id, 'wpuf_sub_pcount', 'unlimited' );
            } else {
                update_user_meta( $user_id, 'wpuf_sub_pcount', $subscription->count );
            }

            //store pack id
            update_user_meta( $user_id, 'wpuf_sub_pack', $subscription->id );
        }
    }

    public static function post_by_orderid( $order_id ) {
        global $wpdb;

        //$post = get_post( $post_id );
        $sql = $wpdb->prepare( "SELECT p.ID, p.post_status
            FROM $wpdb->posts p, $wpdb->postmeta m
            WHERE p.ID = m.post_id AND p.post_status <> 'publish' AND m.meta_key = '_wpuf_order_id' AND m.meta_value = %s", $order_id );

        return $wpdb->get_row( $sql );
    }

    /**
     * Publish the post if payment is made
     *
     * @param int $post_id
     */
    function handle_post_publish( $order_id ) {
        $post = self::post_by_orderid( $order_id );

        if ( $post && $post->post_status != 'publish' ) {
            $this->set_post_status( $post->ID );
        }
    }
        
    /**
     * Maintain post status from the form settings
     * 
     * @since 2.1.9
     * @param int $post_id
     */
    function set_post_status( $post_id ) {
        $post_status = 'publish';
        $form_id = get_post_meta( $post_id, '_wpuf_form_id', true );

        if ( $form_id ) {
            $form_settings = get_post_meta( $form_id, 'wpuf_form_settings', true );
            $post_status = $form_settings['post_status'];
        }

        $update_post = array(
            'ID' => $post_id,
            'post_status' => $post_status
        );

        wp_update_post( $update_post );
    }

    /**
     * Generate users subscription info with a shortcode
     *
     * @global type $userdata
     */
    function subscription_info() {
        global $userdata;

        ob_start();

        $userdata = get_userdata( $userdata->ID ); //wp 3.3 fix

        if ( wpuf_get_option( 'charge_posting', 'wpuf_payment' ) == 'yes' && is_user_logged_in() ) {
            $duration = ( $userdata->wpuf_sub_validity ) ? $userdata->wpuf_sub_validity : 0;
            $count = ( $userdata->wpuf_sub_pcount ) ? $userdata->wpuf_sub_pcount : 0;

            $diff = strtotime( $duration ) - time();

            //var_dump( $duration, $count, $diff );
            //var_dump( $userdata );
            $d_str = '';
            $c_str = '';

            if ( $duration === 0 ) {
                $d_str = 0;
            } elseif ( $duration == 'unlimited' ) {
                $d_str = __( 'Unlimited duration', 'wpuf' );
            } elseif ( $diff <= 0 ) {
                $d_str = __( 'Expired', 'wpuf' );
            } elseif ( $diff > 0 ) {
                $d_str = 'Till ' . date_i18n( 'd M, Y H:i', strtotime( $duration ) );
            }

            if ( $count === 0 ) {
                $c_str = 0;
            } elseif ( $count == 'unlimited' ) {
                $c_str = 'unlimited post';
            } else {
                $c_str = $count;
            }
            ?>
            <div class="wpuf_sub_info">
                <h3><?php _e( 'Subscription Details', 'wpuf' ); ?></h3>
                <div class="text">
                    <strong><?php _e( 'Validity:', 'wpuf' ); ?></strong> <?php echo $d_str; ?>,
                    <strong><?php _e( 'Post Left:', 'wpuf' ); ?></strong> <?php echo $c_str; ?>
                </div>
            </div>

            <?php
        }

        return ob_get_clean();
    }

    /**
     * Show the subscription packs that are built
     * from admin Panel
     */
    function subscription_packs() {
        $packs = $this->get_subscription_packs();

        ob_start();

        if ( $packs ) {
            echo '<ul class="wpuf_packs">';
            foreach ($packs as $pack) {
                $duration = ( $pack->duration == 0 ) ? 'unlimited' : $pack->duration;
                $count = ( $pack->count == 0 ) ? 'unlimited' : $pack->count;
                $is_free = $pack->cost == '0' ? true : false;
                $price = $is_free ? __('Free', 'wpuf') : wpuf_get_option( 'currency_symbol', 'wpuf_payment' ) . $pack->cost;
                $onclick = $is_free ? 'return confirm("' . __( 'You can only buy the free pack once. Proceed?', 'wpuf' ) . '");' : '';
                $payment_page = get_permalink( wpuf_get_option( 'payment_page', 'wpuf_payment' ) );
                ?>
                <li>
                    <h3><?php echo $pack->name; ?> - <?php echo $pack->description; ?></h3>
                    <p><?php echo $count; ?> posts for <?php echo $duration; ?> days.
                        <span class="cost"><?php echo $price; ?></span>
                    </p>
                    <p>
                        <a href="<?php echo add_query_arg( array('action' => 'wpuf_pay', 'type' => 'pack', 'pack_id' => $pack->id ), $payment_page ); ?>" onclick="<?php echo esc_attr( $onclick ); ?>"><?php _e( 'Buy Now', 'wpuf' ); ?></a>
                    </p>
                </li>
                <?php
            }
            echo '</ul>';
        }

        return ob_get_clean();
    }

    /**
     * Show a info message when posting if payment is enabled
     */
    function add_post_info() {
        if ( self::has_user_error() ) {
            ?>
            <div class="wpuf-info">
                <?php printf( __( 'This will cost you <strong>%s</strong> to add a new post. You may buy some bulk package too. ', 'wpuf' ), wpuf_get_option( 'currency_symbol', 'wpuf_payment' ) . wpuf_get_option( 'cost_per_post', 'wpuf_payment' ) ); ?>
            </div>
            <?php
        }
    }

    function force_pack_notice( $text ) {
        $force_pack = wpuf_get_option( 'force_pack', 'wpuf_payment' );

        if ( $force_pack == 'yes' && WPUF_Subscription::has_user_error() ) {
            return __( 'You must purchase a pack before posting', 'wpuf' );
        }

        return $text;
    }

    function force_pack_permission( $perm ) {
        $force_pack = wpuf_get_option( 'force_pack', 'wpuf_payment' );

        if ( $force_pack == 'yes' && WPUF_Subscription::has_user_error() ) {
            return 'no';
        }

        return $perm;
    }

    /**
     * Adds the postlock form in users profile
     *
     * @param object $profileuser
     */
    function profile_subscription_details( $profileuser ) {

        if ( is_admin() && current_user_can( 'edit_users' ) ) {

            if ( wpuf_get_option( 'charge_posting', 'wpuf_payment' ) == 'yes' ) {
                $validity = (isset( $profileuser->wpuf_sub_validity )) ? $profileuser->wpuf_sub_validity : date( 'Y-m-d G:i:s', time() );
                $count = ( isset( $profileuser->wpuf_sub_pcount ) ) ? $profileuser->wpuf_sub_pcount : 0;

                if ( isset( $profileuser->wpuf_sub_pack ) ) {
                    $pack = WPUF_Subscription::get_subscription( $profileuser->wpuf_sub_pack );
                    $pack = $pack->name;
                } else {
                    $pack = 'Free';
                }
                ?>

                <h3><?php _e( 'WPUF Subscription', 'wpuf' ); ?></h3>

                <table class="form-table">
                    <tr>
                        <th><label for="wpuf_sub_pack"><?php _e( 'Pack:', 'wpuf' ); ?> </label></th>
                        <td>
                            <input type="text" disabled="disabled" name="wpuf_sub_pack" id="wpuf_sub_pack" class="regular-text" value="<?php echo $pack; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wpuf_sub_pcount"><?php _e( 'Post Count:', 'wpuf' ); ?> </label></th>
                        <td>
                            <input type="text" name="wpuf_sub_pcount" id="wpuf_sub_pcount" class="regular-text" value="<?php echo $count; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wpuf_sub_validity"><?php _e( 'Validity:', 'wpuf' ); ?> </label></th>
                        <td>
                            <input type="text" name="wpuf_sub_validity" id="wpuf_sub_validity" class="regular-text" value="<?php echo $validity; ?>" />
                        </td>
                    </tr>
                </table>

            <?php } ?>

            <?php
        }
    }

    /**
     * Update user profile lock
     *
     * @param int $user_id
     */
    function profile_subscription_update( $user_id ) {
        if ( is_admin() && current_user_can( 'edit_users' ) ) {
            if ( isset( $_POST['wpuf_sub_pcount'] ) ) {
                update_user_meta( $user_id, 'wpuf_sub_validity', $_POST['wpuf_sub_validity'] );
                update_user_meta( $user_id, 'wpuf_sub_pcount', $_POST['wpuf_sub_pcount'] );
            }
        }
    }
    
    /**
     * Determine if the user has used a free pack before
     * 
     * @since 2.1.8
     * 
     * @param int $user_id
     * @param int $pack_id
     * @return boolean
     */
    public static function has_used_free_pack( $user_id, $pack_id ) {
        $has_used = get_user_meta( $user_id, 'wpuf_fp_used', true );

        if ( $has_used == '' ) {
            return false;
        }

        if ( is_array( $has_used ) && isset( $has_used[$pack_id] ) ) {
            return true;
        }

        return false;
    }
    
    /**
     * Add a free used pack to the user account
     * 
     * @since 2.1.8
     * 
     * @param int $user_id
     * @param int $pack_id
     */
    public static function add_used_free_pack( $user_id, $pack_id ) {
        $has_used = get_user_meta( $user_id, 'wpuf_fp_used', true );
        $has_used = is_array( $has_used ) ? $has_used : array();

        $has_used[$pack_id] = $pack_id;
        update_user_meta( $user_id, 'wpuf_fp_used', $has_used );
    }

}
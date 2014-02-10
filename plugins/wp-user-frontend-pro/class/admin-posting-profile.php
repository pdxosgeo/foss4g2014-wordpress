<?php

class WPUF_Admin_Posting_Profile extends WPUF_Admin_Posting {

    function __construct() {
        add_action( 'personal_options_update', array($this, 'save_fields') );
        add_action( 'edit_user_profile_update', array($this, 'save_fields') );

        add_action( 'show_user_profile', array($this, 'render_form') );
        add_action( 'edit_user_profile', array($this, 'render_form') );
        
        add_action( 'personal_options_update', array($this, 'post_lock_update') );
        add_action( 'edit_user_profile_update', array($this, 'post_lock_update') );
 
        add_action( 'show_user_profile', array($this, 'post_lock_form') );
        add_action( 'edit_user_profile', array($this, 'post_lock_form') );

        add_action( 'wp_ajax_wpuf_delete_avatar', array($this, 'delete_avatar_ajax') );
    }

    function delete_avatar_ajax() {
        $user_id = get_current_user_id();
        $avatar = get_user_meta( $user_id, 'user_avatar', true );
        if ( $avatar ) {
            $upload_dir = wp_upload_dir();

            $full_url = str_replace( $upload_dir['baseurl'],  $upload_dir['basedir'], $avatar );

            if ( file_exists( $full_url ) ) {
                unlink( $full_url );
                delete_user_meta( $user_id, 'user_avatar' );
            }
        }

        die();
    }

    function get_role_name( $userdata ) {
        return reset( $userdata->roles );
    }

    function render_form( $userdata, $post_id = NULL, $preview = false ) {
        $option = get_option( 'wpuf_profile', array() );

        if ( !isset( $option['roles'][$this->get_role_name( $userdata )] ) || empty( $option['roles'][$this->get_role_name( $userdata )] ) ) {
            return;
        }

        $form_id = $option['roles'][$this->get_role_name( $userdata )];
        list($post_fields, $taxonomy_fields, $custom_fields) = $this->get_input_fields( $form_id );

        if ( !$custom_fields ) {
            return;
        }
        ?>

        <input type="hidden" name="wpuf_cf_update" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
        <input type="hidden" name="wpuf_cf_form_id" value="<?php echo $form_id; ?>" />

        <table class="form-table wpuf-cf-table">
            <tbody>
                <?php
                // reset -> get the first item
                if ( $avatar = reset( $this->search( $post_fields, 'name', 'avatar' ) ) ) {
                    $this->render_item_before( $avatar );
                    $this->image_upload( $avatar, $userdata->ID, 'user' );
                    $this->render_item_after( $avatar );
                }

                $this->render_items( $custom_fields, $userdata->ID, 'user', $form_id, get_post_meta( $form_id, 'wpuf_form', true ) );
                ?>
            </tbody>
        </table>
        <?php
        $this->scripts_styles();
    }

    function save_fields( $user_id ) {
        if ( !isset( $_POST['wpuf_cf_update'] ) ) {
            return $post->ID;
        }

        if ( !wp_verify_nonce( $_POST['wpuf_cf_update'], plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        list($post_fields, $taxonomy_fields, $custom_fields) = self::get_input_fields( $_POST['wpuf_cf_form_id'] );
        WPUF_Frontend_Form_Profile::update_user_meta( $custom_fields, $user_id );
    }

    /**
     * Adds the postlock form in users profile
     *
     * @param object $profileuser
     */
    function post_lock_form( $profileuser ) {

        if ( is_admin() && current_user_can( 'edit_users' ) ) {
            $select = ( $profileuser->wpuf_postlock == 'yes' ) ? 'yes' : 'no';
            ?>

            <h3><?php _e( 'WPUF Post Lock', 'wpuf' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="post-lock"><?php _e( 'Lock Post:', 'wpuf' ); ?> </label></th>
                    <td>
                        <select name="wpuf_postlock" id="post-lock">
                            <option value="no"<?php selected( $select, 'no' ); ?>>No</option>
                            <option value="yes"<?php selected( $select, 'yes' ); ?>>Yes</option>
                        </select>
                        <span class="description"><?php _e( 'Lock user from creating new post.', 'wpuf' ); ?></span></em>
                    </td>
                </tr>

                <tr>
                    <th><label for="post-lock"><?php _e( 'Lock Reason:', 'wpuf' ); ?> </label></th>
                    <td>
                        <input type="text" name="wpuf_lock_cause" id="wpuf_lock_cause" class="regular-text" value="<?php echo esc_attr( $profileuser->wpuf_lock_cause ); ?>" />
                    </td>
                </tr>
            </table>
            <?php
        }
    }

    /**
     * Update user profile lock
     *
     * @param int $user_id
     */
    function post_lock_update( $user_id ) {
        if ( is_admin() && current_user_can( 'edit_users' ) ) {
            update_user_meta( $user_id, 'wpuf_postlock', $_POST['wpuf_postlock'] );
            update_user_meta( $user_id, 'wpuf_lock_cause', $_POST['wpuf_lock_cause'] );
        }
    }

}
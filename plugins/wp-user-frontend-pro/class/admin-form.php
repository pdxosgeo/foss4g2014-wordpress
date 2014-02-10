<?php
/**
 * Admin Form UI Builder
 *
 * @package WP User Frontend
 */
class WPUF_Admin_Form {

    private $form_data_key = 'wpuf_form';
    private $form_settings_key = 'wpuf_form_settings';

    /**
     * Add neccessary actions and filters
     *
     * @return void
     */
    function __construct() {
        add_action( 'init', array($this, 'register_post_type') );
        add_filter( 'post_updated_messages', array($this, 'form_updated_message') );

        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
        add_action( 'admin_footer-edit.php', array($this, 'add_form_button_style') );
        add_action( 'admin_footer-post.php', array($this, 'add_form_button_style') );

        add_action( 'admin_head', array( $this, 'menu_icon' ) );

        // form duplication
        add_filter( 'post_row_actions', array( $this, 'row_action_duplicate' ), 10, 2 );
        add_filter( 'admin_action_wpuf_duplicate', array( $this, 'duplicate_form' ) );

        // meta boxes
        add_action( 'add_meta_boxes', array($this, 'add_meta_box_form_select') );
        add_action( 'add_meta_boxes_wpuf_forms', array($this, 'add_meta_box_post') );
        add_action( 'add_meta_boxes_wpuf_profile', array($this, 'add_meta_box_profile') );


        // custom columns
        add_filter( 'manage_edit-wpuf_forms_columns', array( $this, 'admin_column' ) );
        add_filter( 'manage_edit-wpuf_profile_columns', array( $this, 'admin_column_profile' ) );
        add_action( 'manage_wpuf_forms_posts_custom_column', array( $this, 'admin_column_value' ), 10, 2 );
        add_action( 'manage_wpuf_profile_posts_custom_column', array( $this, 'admin_column_value_profile' ), 10, 2 );

        // ajax actions for post forms
        add_action( 'wp_ajax_wpuf_form_dump', array( $this, 'form_dump' ) );
        add_action( 'wp_ajax_wpuf_form_add_el', array( $this, 'ajax_post_add_element' ) );

        add_action( 'save_post', array( $this, 'save_form_meta' ), 1, 2 ); // save the custom fields
        add_action( 'save_post', array( $this, 'form_selection_metabox_save' ), 1, 2 ); // save the custom fields
    }

    /**
     * Enqueue scripts and styles for form builder
     *
     * @global string $pagenow
     * @return void
     */
    function enqueue_scripts() {
        global $pagenow, $post;

        if ( !in_array( $pagenow, array( 'post.php', 'post-new.php') ) ) {
            return;
        }
        
        wp_enqueue_script( 'jquery-ui-autocomplete' );

        if ( !in_array( $post->post_type, array( 'wpuf_forms', 'wpuf_profile' ) ) ) {
            return;
        }

        $path = plugins_url( '', dirname( __FILE__ ) );

        // scripts
        wp_enqueue_script( 'jquery-smallipop', $path . '/js/jquery.smallipop-0.4.0.min.js', array('jquery') );
        wp_enqueue_script( 'wpuf-formbuilder', $path . '/js/formbuilder.js', array('jquery', 'jquery-ui-sortable') );

        // styles
        wp_enqueue_style( 'jquery-smallipop', $path . '/css/jquery.smallipop.css' );
        wp_enqueue_style( 'wpuf-formbuilder', $path . '/css/formbuilder.css' );
        wp_enqueue_style( 'jquery-ui-core', $path . '/css/jquery-ui-1.9.1.custom.css' );
    }

    function add_form_button_style() {
        global $pagenow, $post_type;

        if ( !in_array( $post_type, array( 'wpuf_forms', 'wpuf_profile') ) ) {
            return;
        }

        $fixed_sidebar = wpuf_get_option( 'fixed_form_element', 'wpuf_general' );
        ?>
        <style type="text/css">
            .wrap .add-new-h2, .wrap .add-new-h2:active {
                background: #21759b;
                color: #fff;
                text-shadow: 0 1px 1px #446E81;
            }

            <?php if ( $fixed_sidebar == 'on' ) { ?>
            #wpuf-metabox-fields{
                position: fixed;
                bottom: 10px;
            }
            <?php } ?>
        </style>
        <?php
    }

    /**
     * Register form post types
     *
     * @return void
     */
    function register_post_type() {
        $capability = wpuf_admin_role();

        register_post_type( 'wpuf_forms', array(
            'label' => __( 'Forms', 'wpuf' ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'wpuf-admin-opt',
            'capability_type' => 'post',
            'capabilities' => array(
                'publish_posts' => $capability,
                'edit_posts' => $capability,
                'edit_others_posts' => $capability,
                'delete_posts' => $capability,
                'delete_others_posts' => $capability,
                'read_private_posts' => $capability,
                'edit_post' => $capability,
                'delete_post' => $capability,
                'read_post' => $capability,
            ),
            'hierarchical' => false,
            'query_var' => false,
            'supports' => array('title'),
            'labels' => array(
                'name' => __( 'Forms', 'wpuf' ),
                'singular_name' => __( 'Form', 'wpuf' ),
                'menu_name' => __( 'Forms', 'wpuf' ),
                'add_new' => __( 'Add Form', 'wpuf' ),
                'add_new_item' => __( 'Add New Form', 'wpuf' ),
                'edit' => __( 'Edit', 'wpuf' ),
                'edit_item' => __( 'Edit Form', 'wpuf' ),
                'new_item' => __( 'New Form', 'wpuf' ),
                'view' => __( 'View Form', 'wpuf' ),
                'view_item' => __( 'View Form', 'wpuf' ),
                'search_items' => __( 'Search Form', 'wpuf' ),
                'not_found' => __( 'No Form Found', 'wpuf' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'wpuf' ),
                'parent' => __( 'Parent Form', 'wpuf' ),
            ),
        ) );

        register_post_type( 'wpuf_profile', array(
            'label' => __( 'Registraton Forms', 'wpuf' ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'wpuf-admin-opt',
            'capability_type' => 'post',
            'capabilities' => array(
                'publish_posts' => $capability,
                'edit_posts' => $capability,
                'edit_others_posts' => $capability,
                'delete_posts' => $capability,
                'delete_others_posts' => $capability,
                'read_private_posts' => $capability,
                'edit_post' => $capability,
                'delete_post' => $capability,
                'read_post' => $capability,
            ),
            'hierarchical' => false,
            'query_var' => false,
            'supports' => array('title'),
            'labels' => array(
                'name' => __( 'Forms', 'wpuf' ),
                'singular_name' => __( 'Form', 'wpuf' ),
                'menu_name' => __( 'Registration Forms', 'wpuf' ),
                'add_new' => __( 'Add Form', 'wpuf' ),
                'add_new_item' => __( 'Add New Form', 'wpuf' ),
                'edit' => __( 'Edit', 'wpuf' ),
                'edit_item' => __( 'Edit Form', 'wpuf' ),
                'new_item' => __( 'New Form', 'wpuf' ),
                'view' => __( 'View Form', 'wpuf' ),
                'view_item' => __( 'View Form', 'wpuf' ),
                'search_items' => __( 'Search Form', 'wpuf' ),
                'not_found' => __( 'No Form Found', 'wpuf' ),
                'not_found_in_trash' => __( 'No Form Found in Trash', 'wpuf' ),
                'parent' => __( 'Parent Form', 'wpuf' ),
            ),
        ) );
    }

    function form_updated_message( $messages ) {
        $message = array(
             0 => '',
             1 => __('Form updated.'),
             2 => __('Custom field updated.'),
             3 => __('Custom field deleted.'),
             4 => __('Form updated.'),
             5 => isset($_GET['revision']) ? sprintf( __('Form restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
             6 => __('Form published.'),
             7 => __('Form saved.'),
             8 => __('Form submitted.' ),
             9 => '',
            10 => __('Form draft updated.'),
        );

        $messages['wpuf_forms'] = $message;
        $messages['wpuf_profile'] = $message;

        return $messages;
    }

    function menu_icon() {
        ?>
        <style type="text/css">
            .icon32-posts-wpuf_forms,
            .icon32-posts-wpuf_profile {
                background: url('<?php echo admin_url( "images/icons32.png" ); ?>') no-repeat 2% 35%;
            }
        </style>
        <?php
    }

    /**
     * Columns form builder list table
     *
     * @param type $columns
     * @return string
     */
    function admin_column( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Form Name', 'wpuf' ),
            'post_type' => __( 'Post Type', 'wpuf' ),
            'post_status' => __( 'Post Status', 'wpuf' ),
            'guest_post' => __( 'Guest Post', 'wpuf' ),
            'shortcode' => __( 'Shortcode', 'wpuf' )
        );

        return $columns;
    }

    /**
     * Columns form builder list table
     *
     * @param type $columns
     * @return string
     */
    function admin_column_profile( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Form Name', 'wpuf' ),
            'role' => __( 'User Role', 'wpuf' ),
            'shortcode' => __( 'Shortcode', 'wpuf' )
        );

        return $columns;
    }

    /**
     * Custom Column value for post form builder
     *
     * @param string $column_name
     * @param int $post_id
     */
    function admin_column_value( $column_name, $post_id ) {
        switch ($column_name) {
            case 'shortcode':
                printf( '[wpuf_form id="%d"]', $post_id );
                break;

            case 'post_type':
                $settings = get_post_meta( $post_id, $this->form_settings_key, true );
                echo $settings['post_type'];
                break;

            case 'post_status':
                $settings = get_post_meta( $post_id, $this->form_settings_key, true );
                echo wpuf_admin_post_status( $settings['post_status'] );
                break;

            case 'guest_post':
                $settings = get_post_meta( $post_id, $this->form_settings_key, true );
                $url = plugins_url('images/', dirname(__FILE__));
                $image = '<img src="%s" alt="%s">';
                echo $settings['guest_post'] == 'false' ? sprintf( $image, $url . 'cross.png', __( 'No', 'wpuf' ) ) : sprintf( $image, $url . 'tick.png', __( 'Yes', 'wpuf' ) ) ;
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Custom Column value for profile form builder
     *
     * @param string $column_name
     * @param int $post_id
     */
    function admin_column_value_profile( $column_name, $post_id ) {

        switch ($column_name) {
            case 'shortcode':
                printf( 'Registration: [wpuf_profile type="registration" id="%d"]<br>', $post_id );
                printf( 'Edit Profile: [wpuf_profile type="profile" id="%d"]', $post_id );
                break;

            case 'role':
                $settings = get_post_meta( $post_id, $this->form_settings_key, true );
                echo ucfirst( $settings['role'] );
                break;
        }
    }

    /**
     * Duplicate form row action link
     *
     * @param array $actions
     * @param object $post
     * @return array
     */
    function row_action_duplicate($actions, $post) {
        if ( !current_user_can( 'activate_plugins' ) ) {
            return $actions;
        }

        if ( !in_array( $post->post_type, array( 'wpuf_forms', 'wpuf_profile') ) ) {
            return $actions;
        }

        $actions['duplicate'] = '<a href="' . esc_url( add_query_arg( array( 'action' => 'wpuf_duplicate', 'id' => $post->ID, '_wpnonce' => wp_create_nonce( 'wpuf_duplicate' ) ), admin_url( 'admin.php' ) ) ) . '" title="' . esc_attr( __( 'Duplicate form', 'wpuf' ) ) . '">' . __( 'Duplicate', 'wpuf' ) . '</a>';
        return $actions;
    }

    /**
     * Form Duplication handler
     *
     * @return type
     */
    function duplicate_form() {
        check_admin_referer( 'wpuf_duplicate' );

        if ( !current_user_can( 'activate_plugins' ) ) {
            return;
        }

        $post_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $post = get_post( $post_id );

        if ( !$post ) {
            return;
        }

        $new_form = array(
            'post_title' => $post->post_title,
            'post_type' => $post->post_type,
            'post_status' => 'draft'
        );

        $form_id = wp_insert_post( $new_form );

        if ( $form_id ) {
            $form_settings = get_post_meta( $post_id, $this->form_settings_key, true );
            $form_vars = get_post_meta( $post_id, $this->form_data_key, true );

            update_post_meta( $form_id, $this->form_settings_key, $form_settings );
            update_post_meta( $form_id, $this->form_data_key, $form_vars );

            $location = admin_url( 'edit.php?post_type=' . $post->post_type );
            wp_redirect( $location );
        }
    }

    /**
     * Meta box for all Post form selection
     *
     * Registers a meta box in public post types to select the desired WPUF
     * form select box to assign a form id.
     *
     * @return void
     */
    function add_meta_box_form_select() {

        // remove the submit div, because submit button placed on form elements
        remove_meta_box('submitdiv', 'wpuf_forms', 'side');
        remove_meta_box('submitdiv', 'wpuf_profile', 'side');

        $post_types = get_post_types( array('public' => true) );
        foreach ($post_types as $post_type) {
            add_meta_box( 'wpuf-select-form', __('WPUF Form'), array($this, 'form_selection_metabox'), $post_type, 'side', 'high' );
        }
    }

    /**
     * Add meta boxes to post form builder
     *
     * @return void
     */
    function add_meta_box_post() {
        add_meta_box( 'wpuf-metabox-editor', __( 'Form Editor', 'wpuf' ), array($this, 'metabox_post_form'), 'wpuf_forms', 'normal', 'high' );
        add_meta_box( 'wpuf-metabox-fields', __( 'Form Elements', 'wpuf' ), array($this, 'form_elements_post'), 'wpuf_forms', 'side', 'core' );
    }

    /**
     * Adds meta boxes to profile form builder
     *
     * @return void
     */
    function add_meta_box_profile() {
        add_meta_box( 'wpuf-metabox-editor', __( 'Form Editor', 'wpuf' ), array($this, 'metabox_profile_form'), 'wpuf_profile', 'normal', 'high' );
        add_meta_box( 'wpuf-metabox-fields', __( 'Form Elements', 'wpuf' ), array($this, 'form_elements_profile'), 'wpuf_profile', 'side', 'core' );
    }

    function publish_button() {
        global $post, $pagenow;

        $post_type = $post->post_type;
    	$post_type_object = get_post_type_object($post_type);
    	$can_publish = current_user_can($post_type_object->cap->publish_posts);
        ?>
        <div class="submitbox" id="submitpost">
            <div id="major-publishing-actions">
                <div id="publishing-action">
                    <?php if( $pagenow == 'post.php' ) { ?>
                        <a class="button button-primary button-large" target="_blank" href="<?php printf('%s?action=wpuf_form_preview&form_id=%s', admin_url( 'admin-ajax.php' ), $post->ID ); ?>"><?php _e( 'Preview Form', 'wpuf' ); ?></a>
                    <?php } ?>

                    <span class="spinner"></span>
                        <?php
                        if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
                            if ( $can_publish ) :
                                if ( !empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
                                    ?>
                                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Schedule' ) ?>" />
                            <?php submit_button( __( 'Schedule' ), 'primary button-large', 'publish', false, array('accesskey' => 'p') ); ?>
                        <?php else : ?>
                                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish' ) ?>" />
                            <?php submit_button( __( 'Publish' ), 'primary button-large', 'publish', false, array('accesskey' => 'p') ); ?>
                        <?php endif;
                    else :
                        ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Submit for Review' ) ?>" />
                        <?php submit_button( __( 'Submit for Review' ), 'primary button-large', 'publish', false, array('accesskey' => 'p') ); ?>
                    <?php
                    endif;
                    } else {
                        ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update' ) ?>" />
                        <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e( 'Update' ) ?>" />
                    <?php }
                ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }

    /**
     * Form selection meta box in post types
     *
     * Registered via $this->add_meta_box_form_select()
     *
     * @global object $post
     */
    function form_selection_metabox() {
        global $post;

        $forms = get_posts( array('post_type' => 'wpuf_forms', 'numberposts' => '-1') );
        $selected = get_post_meta( $post->ID, '_wpuf_form_id', true );
        ?>

        <input type="hidden" name="wpuf_form_select_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

        <select name="wpuf_form_select">
            <option value="">--</option>
            <?php foreach ($forms as $form) { ?>
            <option value="<?php echo $form->ID; ?>"<?php selected($selected, $form->ID); ?>><?php echo $form->post_title; ?></option>
            <?php } ?>
        </select>
        <?php
    }

    /**
     * Saves the form ID from form selection meta box
     *
     * @param int $post_id
     * @param object $post
     * @return int|void
     */
    function form_selection_metabox_save( $post_id, $post ) {
        if ( !isset($_POST['wpuf_form_select'])) {
            return $post->ID;
        }

        if ( !wp_verify_nonce( $_POST['wpuf_form_select_nonce'], plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return $post->ID;
        }

        update_post_meta( $post->ID, '_wpuf_form_id', $_POST['wpuf_form_select'] );
    }

    /**
     * Displays settings on post form builder
     *
     * @global object $post
     */
    function form_settings_posts() {
        global $post;
        
        $form_settings = get_post_meta( $post->ID, 'wpuf_form_settings', true );

        $restrict_message = __( "This page is restricted. Please Log in / Register to view this page.", 'wpuf' );

        $post_type_selected = isset( $form_settings['post_type'] ) ? $form_settings['post_type'] : 'post';
        $post_status_selected = isset( $form_settings['post_status'] ) ? $form_settings['post_status'] : 'publish';
        $post_format_selected = isset( $form_settings['post_format'] ) ? $form_settings['post_format'] : 0;
        $default_cat = isset( $form_settings['default_cat'] ) ? $form_settings['default_cat'] : -1;

        $guest_post = isset( $form_settings['guest_post'] ) ? $form_settings['guest_post'] : 'false';
        $guest_details = isset( $form_settings['guest_details'] ) ? $form_settings['guest_details'] : 'true';
        $name_label = isset( $form_settings['name_label'] ) ? $form_settings['name_label'] : __( 'Name' );
        $email_label = isset( $form_settings['email_label'] ) ? $form_settings['email_label'] : __( 'Email' );
        $message_restrict = isset( $form_settings['message_restrict'] ) ? $form_settings['message_restrict'] : $restrict_message;

        $redirect_to = isset( $form_settings['redirect_to'] ) ? $form_settings['redirect_to'] : 'post';
        $message = isset( $form_settings['message'] ) ? $form_settings['message'] : __( 'Post saved', 'wpuf' );
        $update_message = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'wpuf' );
        $page_id = isset( $form_settings['page_id'] ) ? $form_settings['page_id'] : 0;
        $url = isset( $form_settings['url'] ) ? $form_settings['url'] : '';
        $comment_status = isset( $form_settings['comment_status'] ) ? $form_settings['comment_status'] : 'open';

        $submit_text = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( 'Submit', 'wpuf' );
        $draft_text = isset( $form_settings['draft_text'] ) ? $form_settings['draft_text'] : __( 'Save Draft', 'wpuf' );
        $preview_text = isset( $form_settings['preview_text'] ) ? $form_settings['preview_text'] : __( 'Preview', 'wpuf' );
        $draft_post = isset( $form_settings['draft_post'] ) ? $form_settings['draft_post'] : 'false';
        ?>
        <table class="form-table">
            <tr class="wpuf-post-type">
                <th><?php _e( 'Post Type', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[post_type]">
                        <?php
                        $post_types = get_post_types();
                        unset($post_types['attachment']);
                        unset($post_types['revision']);
                        unset($post_types['nav_menu_item']);
                        unset($post_types['wpuf_forms']);
                        unset($post_types['wpuf_profile']);

                        foreach ($post_types as $post_type) {
                            printf('<option value="%s"%s>%s</option>', $post_type, selected( $post_type_selected, $post_type, false ), $post_type );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-post-status">
                <th><?php _e( 'Post Status', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[post_status]">
                        <?php
                        $statuses = get_post_statuses();

                        foreach ($statuses as $status => $label) {
                            printf('<option value="%s"%s>%s</option>', $status, selected( $post_status_selected, $status, false ), $label );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-post-fromat">
                <th><?php _e( 'Post Format', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[post_format]">
                        <option value="0"><?php _e( '- None -', 'wpuf' ); ?></option>
                        <?php
                        $post_formats = get_theme_support( 'post-formats' );

                        if ( isset($post_formats[0]) && is_array( $post_formats[0] ) ) {
                            foreach ($post_formats[0] as $format) {
                                printf('<option value="%s"%s>%s</option>', $format, selected( $post_format_selected, $format, false ), $format );
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            </tr>

            <tr class="wpuf-default-cat">
                <th><?php _e( 'Default Post Category', 'wpuf' ); ?></th>
                <td>
                    <?php
                    wp_dropdown_categories( array( 
                        'hide_empty' => false,
                        'hierarchical' => true,
                        'selected' => $default_cat,
                        'name' => 'wpuf_settings[default_cat]',
                        'show_option_none' => __( '- None -', 'wpuf' )
                    ) );
                    ?>
                    <div class="description"><?php echo __( 'If users are not allowed to choose any category, this category will be used instead (if post type supports)', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr>
                <th><?php _e( 'Guest Post', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[guest_post]" value="false">
                        <input type="checkbox" name="wpuf_settings[guest_post]" value="true"<?php checked( $guest_post, 'true' ); ?> />
                        <?php _e( 'Enable Guest Post', 'wpuf' ) ?>
                    </label>
                    <div class="description"><?php _e( 'Unregistered users will be able to submit posts', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr class="show-if-guest">
                <th><?php _e( 'User Details', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[guest_details]" value="false">
                        <input type="checkbox" name="wpuf_settings[guest_details]" value="true"<?php checked( $guest_details, 'true' ); ?> />
                        <?php _e( 'Require Name and Email address', 'wpuf' ) ?>
                    </label>
                    <div class="description"><?php _e( 'If requires, users will be automatically registered to the site using the name and email address', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr class="show-if-guest show-if-details">
                <th><?php _e( 'Name Label', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="text" name="wpuf_settings[name_label]" value="<?php echo esc_attr( $name_label ); ?>" />
                    </label>
                    <div class="description"><?php _e( 'Label text for name field', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr class="show-if-guest show-if-details">
                <th><?php _e( 'E-Mail Label', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="text" name="wpuf_settings[email_label]" value="<?php echo esc_attr( $email_label ); ?>" />
                    </label>
                    <div class="description"><?php _e( 'Label text for email field', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr class="show-if-not-guest">
                <th><?php _e( 'Unauthorized Message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="3" cols="40" name="wpuf_settings[message_restrict]"><?php echo esc_textarea( $message_restrict ); ?></textarea>
                    <div class="description"><?php _e( 'Not logged in users will see this message', 'wpuf' ); ?></div>
                </td>
            </tr>

            <tr class="wpuf-redirect-to">
                <th><?php _e( 'Redirect To', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[redirect_to]">
                        <?php
                        $redirect_options = array(
                            'post' => __( 'Newly created post', 'wpuf' ),
                            'same' => __( 'Same Page', 'wpuf' ),
                            'page' => __( 'To a page', 'wpuf' ),
                            'url' => __( 'To a custom URL', 'wpuf' )
                        );

                        foreach ($redirect_options as $to => $label) {
                            printf('<option value="%s"%s>%s</option>', $to, selected( $redirect_to, $to, false ), $label );
                        }
                        ?>
                    </select>
                    <div class="description">
                        <?php _e( 'After successfull submit, where the page will redirect to', $domain = 'default' ) ?>
                    </div>
                </td>
            </tr>

            <tr class="wpuf-same-page">
                <th><?php _e( 'Message to show', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="3" cols="40" name="wpuf_settings[message]"><?php echo esc_textarea( $message ); ?></textarea>
                </td>
            </tr>
            </tr>

            <tr class="wpuf-page-id">
                <th><?php _e( 'Page', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[page_id]">
                        <?php
                        $pages = get_posts(  array( 'numberposts' => -1, 'post_type' => 'page') );

                        foreach ($pages as $page) {
                            printf('<option value="%s"%s>%s</option>', $page->ID, selected( $page_id, $page->ID, false ), esc_attr( $page->post_title ) );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-url">
                <th><?php _e( 'Custom URL', 'wpuf' ); ?></th>
                <td>
                    <input type="url" name="wpuf_settings[url]" value="<?php echo esc_attr( $url ); ?>">
                </td>
            </tr>

            <tr class="wpuf-comment">
                <th><?php _e( 'Comment Status', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[comment_status]">
                        <option value="open" <?php selected( $comment_status, 'open'); ?>><?php _e('Open'); ?></option>
                        <option value="closed" <?php selected( $comment_status, 'closed'); ?>><?php _e('Closed'); ?></option>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-submit-text">
                <th><?php _e( 'Submit Post Button text', 'wpuf' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[submit_text]" value="<?php echo esc_attr( $submit_text ); ?>">
                </td>
            </tr>

            <tr>
                <th><?php _e( 'Post Draft', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[draft_post]" value="false">
                        <input type="checkbox" name="wpuf_settings[draft_post]" value="true"<?php checked( $draft_post, 'true' ); ?> />
                        <?php _e( 'Enable Saving as draft', 'wpuf' ) ?>
                    </label>
                    <div class="description"><?php _e( 'It will show a button to save as draft', 'wpuf' ); ?></div>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Displays settings on post form builder
     *
     * @global object $post
     */
    function form_settings_posts_edit() {
        global $post;

        $form_settings = get_post_meta( $post->ID, 'wpuf_form_settings', true );

        $post_status_selected = isset( $form_settings['edit_post_status'] ) ? $form_settings['edit_post_status'] : 'publish';

        $redirect_to = isset( $form_settings['edit_redirect_to'] ) ? $form_settings['edit_redirect_to'] : 'same';
        $update_message = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Post updated successfully', 'wpuf' );
        $page_id = isset( $form_settings['edit_page_id'] ) ? $form_settings['edit_page_id'] : 0;
        $url = isset( $form_settings['edit_url'] ) ? $form_settings['edit_url'] : '';
        $update_text = isset( $form_settings['update_text'] ) ? $form_settings['update_text'] : __( 'Update', 'wpuf' );
        ?>
        <table class="form-table">

            <tr class="wpuf-post-status">
                <th><?php _e( 'Set Post Status to', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[edit_post_status]">
                        <?php
                        $statuses = get_post_statuses();
                        
                        foreach ($statuses as $status => $label) {
                            printf('<option value="%s"%s>%s</option>', $status, selected( $post_status_selected, $status, false ), $label );
                        }
                        
                        printf( '<option value="_nochange"%s>%s</option>', selected( $post_status_selected, '_nochange', false ), __( 'No Change', 'wpuf' ) );
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-redirect-to">
                <th><?php _e( 'Redirect To', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[edit_redirect_to]">
                        <?php
                        $redirect_options = array(
                            'post' => __( 'Newly created post', 'wpuf' ),
                            'same' => __( 'Same Page', 'wpuf' ),
                            'page' => __( 'To a page', 'wpuf' ),
                            'url' => __( 'To a custom URL', 'wpuf' )
                        );

                        foreach ($redirect_options as $to => $label) {
                            printf('<option value="%s"%s>%s</option>', $to, selected( $redirect_to, $to, false ), $label );
                        }
                        ?>
                    </select>
                    <div class="description">
                        <?php _e( 'After successfull submit, where the page will redirect to', $domain = 'default' ) ?>
                    </div>
                </td>
            </tr>

            <tr class="wpuf-same-page">
                <th><?php _e( 'Post Update Message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="3" cols="40" name="wpuf_settings[update_message]"><?php echo esc_textarea( $update_message ); ?></textarea>
                </td>
            </tr>

            <tr class="wpuf-page-id">
                <th><?php _e( 'Page', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[edit_page_id]">
                        <?php
                        $pages = get_posts(  array( 'numberposts' => -1, 'post_type' => 'page') );

                        foreach ($pages as $page) {
                            printf('<option value="%s"%s>%s</option>', $page->ID, selected( $page_id, $page->ID, false ), esc_attr( $page->post_title ) );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-url">
                <th><?php _e( 'Custom URL', 'wpuf' ); ?></th>
                <td>
                    <input type="url" name="wpuf_settings[edit_url]" value="<?php echo esc_attr( $url ); ?>">
                </td>
            </tr>

            <tr class="wpuf-update-text">
                <th><?php _e( 'Update Post Button text', 'wpuf' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[update_text]" value="<?php echo esc_attr( $update_text ); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Displays settings on post form builder
     *
     * @global object $post
     */
    function form_settings_posts_notification() {
        global $post;

        $new_mail_body = "Hi Admin,\r\n";
        $new_mail_body .= "A new post has been created in your site %sitename% (%siteurl%).\r\n\r\n";

        $edit_mail_body = "Hi Admin,\r\n";
        $edit_mail_body .= "The post \"%post_title%\" has been updated.\r\n\r\n";

        $mail_body = "Here is the details:\r\n";
        $mail_body .= "Post Title: %post_title%\r\n";
        $mail_body .= "Content: %post_content%\r\n";
        $mail_body .= "Author: %author%\r\n";
        $mail_body .= "Post URL: %permalink%\r\n";
        $mail_body .= "Edit URL: %editlink%";

        $form_settings = get_post_meta( $post->ID, 'wpuf_form_settings', true );

        $new_notificaton = isset( $form_settings['notification']['new'] ) ? $form_settings['notification']['new'] : 'on';
        $new_to = isset( $form_settings['notification']['new_to'] ) ? $form_settings['notification']['new_to'] : get_option( 'admin_email' );
        $new_subject = isset( $form_settings['notification']['new_subject'] ) ? $form_settings['notification']['new_subject'] : __( 'New post created', 'wpuf' );
        $new_body = isset( $form_settings['notification']['new_body'] ) ? $form_settings['notification']['new_body'] : $new_mail_body . $mail_body;

        $edit_notificaton = isset( $form_settings['notification']['edit'] ) ? $form_settings['notification']['edit'] : 'off';
        $edit_to = isset( $form_settings['notification']['edit_to'] ) ? $form_settings['notification']['edit_to'] : get_option( 'admin_email' );
        $edit_subject = isset( $form_settings['notification']['edit_subject'] ) ? $form_settings['notification']['edit_subject'] : __( 'A post has been edited', 'wpuf' );
        $edit_body = isset( $form_settings['notification']['edit_body'] ) ? $form_settings['notification']['edit_body'] : $edit_mail_body . $mail_body;
        ?>

        <h3><?php _e( 'New Post Notificatoin', 'wpuf' ); ?></h3>

        <table class="form-table">
            <tr>
                <th><?php _e( 'Notification', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[notification][new]" value="off">
                        <input type="checkbox" name="wpuf_settings[notification][new]" value="on"<?php checked( $new_notificaton, 'on' ); ?>>
                        <?php _e( 'Enable post notification', 'wpuf' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th><?php _e( 'To', 'wpuf' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[notification][new_to]" class="regular-text" value="<?php echo esc_attr( $new_to ) ?>">
                </td>
            </tr>

            <tr>
                <th><?php _e( 'Subject', 'wpuf' ); ?></th>
                <td><input type="text" name="wpuf_settings[notification][new_subject]" class="regular-text" value="<?php echo esc_attr( $new_subject ) ?>"></td>
            </tr>

            <tr>
                <th><?php _e( 'Message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="6" cols="60" name="wpuf_settings[notification][new_body]"><?php echo esc_textarea( $new_body ) ?></textarea>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'Update Post Notificatoin', 'wpuf' ); ?></h3>

        <table class="form-table">
            <tr>
                <th><?php _e( 'Notification', 'wpuf' ); ?></th>
                <td>
                    <label>
                        <input type="hidden" name="wpuf_settings[notification][edit]" value="off">
                        <input type="checkbox" name="wpuf_settings[notification][edit]" value="on"<?php checked( $edit_notificaton, 'on' ); ?>>
                        <?php _e( 'Enable post notification', 'wpuf' ); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th><?php _e( 'To', 'wpuf' ); ?></th>
                <td><input type="text" name="wpuf_settings[notification][edit_to]" class="regular-text" value="<?php echo esc_attr( $edit_to ) ?>"></td>
            </tr>

            <tr>
                <th><?php _e( 'Subject', 'wpuf' ); ?></th>
                <td><input type="text" name="wpuf_settings[notification][edit_subject]" class="regular-text" value="<?php echo esc_attr( $edit_subject ) ?>"></td>
            </tr>

            <tr>
                <th><?php _e( 'Message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="6" cols="60" name="wpuf_settings[notification][edit_body]"><?php echo esc_textarea( $edit_body ) ?></textarea>
                </td>
            </tr>
        </table>

        <h3><?php _e( 'You may use in message:', 'wpuf' ); ?></h3>
        <p>
            <code>%post_title%</code>, <code>%post_content%</code>, <code>%post_excerpt%</code>, <code>%tags%</code>, <code>%category%</code>,
            <code>%author%</code>, <code>%sitename%</code>, <code>%siteurl%</code>, <code>%permalink%</code>, <code>%editlink%</code>
            <br><code>%custom_{NAME_OF_CUSTOM_FIELD}%</code> e.g: <code>%custom_website_url%</code> for <code>website_url</code> meta field
            </p>

        <?php
    }

    /**
     * Display settings for user profile builder
     *
     * @return void
     */
    function form_settings_profile() {
        global $post;

        $form_settings = get_post_meta( $post->ID, 'wpuf_form_settings', true );

        $role_selected = isset( $form_settings['role'] ) ? $form_settings['role'] : 'subscriber';
        $redirect_to = isset( $form_settings['redirect_to'] ) ? $form_settings['redirect_to'] : 'post';
        $message = isset( $form_settings['message'] ) ? $form_settings['message'] : __( 'Registration successful', 'wpuf' );
        $update_message = isset( $form_settings['update_message'] ) ? $form_settings['update_message'] : __( 'Profile updated successfully', 'wpuf' );
        $page_id = isset( $form_settings['page_id'] ) ? $form_settings['page_id'] : 0;
        $url = isset( $form_settings['url'] ) ? $form_settings['url'] : '';
        $submit_text = isset( $form_settings['submit_text'] ) ? $form_settings['submit_text'] : __( 'Register', 'wpuf' );
        $update_text = isset( $form_settings['update_text'] ) ? $form_settings['update_text'] : __( 'Update Profile', 'wpuf' );

        ?>
        <table class="form-table">
            <tr class="wpuf-post-type">
                <th><?php _e( 'New User Role', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[role]">
                        <?php
                        $user_roles = wpuf_get_user_roles();
                        foreach ($user_roles as $role => $label) {
                            printf('<option value="%s"%s>%s</option>', $role, selected( $role_selected, $role, false ), $label );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-redirect-to">
                <th><?php _e( 'Redirect To', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[redirect_to]">
                        <?php
                        $redirect_options = array(
                            'same' => __( 'Same Page', 'wpuf' ),
                            'page' => __( 'To a page', 'wpuf' ),
                            'url' => __( 'To a custom URL', 'wpuf' )
                        );

                        foreach ($redirect_options as $to => $label) {
                            printf('<option value="%s"%s>%s</option>', $to, selected( $redirect_to, $to, false ), $label );
                        }
                        ?>
                    </select>
                    <div class="description">
                        <?php _e( 'After successfull submit, where the page will redirect to', 'wpuf' ) ?>
                    </div>
                </td>
            </tr>

            <tr class="wpuf-same-page">
                <th><?php _e( 'Registration success message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="3" cols="40" name="wpuf_settings[message]"><?php echo esc_textarea( $message ); ?></textarea>
                </td>
            </tr>

            <tr class="wpuf-same-page">
                <th><?php _e( 'Update profile message', 'wpuf' ); ?></th>
                <td>
                    <textarea rows="3" cols="40" name="wpuf_settings[update_message]"><?php echo esc_textarea( $update_message ); ?></textarea>
                </td>
            </tr>

            <tr class="wpuf-page-id">
                <th><?php _e( 'Page', 'wpuf' ); ?></th>
                <td>
                    <select name="wpuf_settings[page_id]">
                        <?php
                        $pages = get_posts(  array( 'numberposts' => -1, 'post_type' => 'page') );

                        foreach ($pages as $page) {
                            printf('<option value="%s"%s>%s</option>', $page->ID, selected( $page_id, $page->ID, false ), esc_attr( $page->post_title ) );
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr class="wpuf-url">
                <th><?php _e( 'Custom URL', 'wpuf' ); ?></th>
                <td>
                    <input type="url" name="wpuf_settings[url]" value="<?php echo esc_attr( $url ); ?>">
                </td>
            </tr>

            <tr class="wpuf-submit-text">
                <th><?php _e( 'Submit Button text', 'wpuf' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[submit_text]" value="<?php echo esc_attr( $submit_text ); ?>">
                </td>
            </tr>

            <tr class="wpuf-update-text">
                <th><?php _e( 'Update Button text', 'wpuf' ); ?></th>
                <td>
                    <input type="text" name="wpuf_settings[update_text]" value="<?php echo esc_attr( $update_text ); ?>">
                </td>
            </tr>
        </table>
        <?php
    }

    function metabox_post_form( $post ) {
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-metabox" class="nav-tab" id="wpuf-editor-tab"><?php _e( 'Form Editor', 'wpuf' ); ?></a>
            <a href="#wpuf-metabox-settings" class="nav-tab" id="wpuf-post-settings-tab"><?php _e( 'Post Settings', 'wpuf' ); ?></a>
            <a href="#wpuf-metabox-settings-update" class="nav-tab" id="wpuf-edit-settings-tab"><?php _e( 'Edit Settings', 'wpuf' ); ?></a>
            <a href="#wpuf-metabox-notification" class="nav-tab" id="wpuf-notification-tab"><?php _e( 'Notification', 'wpuf' ); ?></a>

            <?php do_action( 'wpuf_post_form_tab' ); ?>
        </h2>

        <div class="tab-content">
            <div id="wpuf-metabox" class="group">
                <?php $this->edit_form_area(); ?>
            </div>

            <div id="wpuf-metabox-settings" class="group">
                <?php $this->form_settings_posts(); ?>
            </div>

            <div id="wpuf-metabox-settings-update" class="group">
                <?php $this->form_settings_posts_edit(); ?>
            </div>

            <div id="wpuf-metabox-notification" class="group">
                <?php $this->form_settings_posts_notification(); ?>
            </div>

            <?php do_action( 'wpuf_post_form_tab_content' ); ?>
        </div>
        <?php
    }

    function metabox_profile_form( $post ) {
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="#wpuf-metabox" class="nav-tab" id="wpuf_general-tab"><?php _e( 'Form Editor', 'wpuf' ); ?></a>
            <a href="#wpuf-metabox-settings" class="nav-tab" id="wpuf_dashboard-tab"><?php _e( 'Settings', 'wpuf' ); ?></a>

            <?php do_action( 'wpuf_profile_form_tab' ); ?>
        </h2>

        <div class="tab-content">
            <div id="wpuf-metabox" class="group">
                <?php $this->edit_form_area_profile(); ?>
            </div>

            <div id="wpuf-metabox-settings" class="group">
                <?php $this->form_settings_profile(); ?>
            </div>

            <?php do_action( 'wpuf_profile_form_tab_content' ); ?>
        </div>
        <?php
    }

    function form_elements_common() {
        $title = esc_attr( __( 'Click to add to the editor', 'wpuf' ) );
        ?>
        <h2><?php _e( 'Custom Fields', 'wpuf' ); ?></h2>
        <div class="wpuf-form-buttons">
            <button class="button" data-name="custom_text" data-type="text" title="<?php echo $title; ?>"><?php _e( 'Text', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_textarea" data-type="textarea" title="<?php echo $title; ?>"><?php _e( 'Textarea', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_select" data-type="select" title="<?php echo $title; ?>"><?php _e( 'Dropdown', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_date" data-type="date" title="<?php echo $title; ?>"><?php _e( 'Date', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_multiselect" data-type="multiselect" title="<?php echo $title; ?>"><?php _e( 'Multi Select', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_radio" data-type="radio" title="<?php echo $title; ?>"><?php _e( 'Radio', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_checkbox" data-type="checkbox" title="<?php echo $title; ?>"><?php _e( 'Checkbox', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_image" data-type="image" title="<?php echo $title; ?>"><?php _e( 'Image Upload', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_file" data-type="file" title="<?php echo $title; ?>"><?php _e( 'File Upload', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_url" data-type="url" title="<?php echo $title; ?>"><?php _e( 'URL', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_email" data-type="email" title="<?php echo $title; ?>"><?php _e( 'Email', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_repeater" data-type="repeat" title="<?php echo $title; ?>"><?php _e( 'Repeat Field', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_hidden" data-type="hidden" title="<?php echo $title; ?>"><?php _e( 'Hidden Field', 'wpuf' ); ?></button>

            <button class="button" data-name="custom_map" data-type="map" title="<?php echo $title; ?>"><?php _e( 'Google Maps', 'wpuf' ); ?></button>

            <?php do_action( 'wpuf_form_buttons_custom' ); ?>
        </div>

        <h2><?php _e( 'Others', 'wpuf' ); ?></h2>
        <div class="wpuf-form-buttons">
            <button class="button" data-name="recaptcha" data-type="captcha" title="<?php echo $title; ?>"><?php _e( 'reCaptcha', 'wpuf' ); ?></button>
            <button class="button" data-name="really_simple_captcha" data-type="rscaptcha" title="<?php echo $title; ?>"><?php _e( 'Really Simple Captcha', 'wpuf' ); ?></button>
            <button class="button" data-name="section_break" data-type="break" title="<?php echo $title; ?>"><?php _e( 'Section Break', 'wpuf' ); ?></button>
            <button class="button" data-name="custom_html" data-type="html" title="<?php echo $title; ?>"><?php _e( 'HTML', 'wpuf' ); ?></button>
            <button class="button" data-name="action_hook" data-type="action" title="<?php echo $title; ?>"><?php _e( 'Action Hook', 'wpuf' ); ?></button>
            <button class="button" data-name="toc" data-type="action" title="<?php echo $title; ?>"><?php _e( 'Term &amp; Conditions', 'wpuf' ); ?></button>

            <?php do_action( 'wpuf_form_buttons_other' ); ?>
        </div>

        <?php
    }

    /**
     * Form elements for post form builder
     *
     * @return void
     */
    function form_elements_post() {
        ?>
        <div class="wpuf-loading hide"></div>

        <h2><?php _e( 'Post Fields', 'wpuf' ); ?></h2>
        <div class="wpuf-form-buttons">
            <button class="button" data-name="post_title" data-type="text" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Post Title', 'wpuf' ); ?></button>
            <button class="button" data-name="post_content" data-type="textarea" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Post Body', 'wpuf' ); ?></button>
            <button class="button" data-name="post_excerpt" data-type="textarea" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Excerpt', 'wpuf' ); ?></button>
            <button class="button" data-name="tags" data-type="text" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Tags', 'wpuf' ); ?></button>
            <button class="button" data-name="category" data-type="category" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Category', 'wpuf' ); ?></button>
            <button class="button" data-name="featured_image" data-type="image" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php _e( 'Featured Image', 'wpuf' ); ?></button>

            <?php do_action( 'wpuf_form_buttons_post' ); ?>
        </div>


        <h2><?php _e( 'Custom Taxonomies', 'wpuf' ); ?></h2>
        <div class="wpuf-form-buttons">
            <?php
            $custom_taxonomies = get_taxonomies(array('_builtin' => false ) );
            if ( $custom_taxonomies ) {
                foreach ($custom_taxonomies as $tax) {
                    ?>
                    <button class="button" data-name="taxonomy" data-type="<?php echo $tax; ?>" title="<?php _e( 'Click to add to the editor', 'wpuf' ); ?>"><?php echo $tax; ?></button>
                    <?php
                }
            } else {
                _e('No custom taxonomies found', 'wpuf');
            }?>
        </div>


        <?php

        $this->form_elements_common();
        $this->publish_button();
    }

    /**
     * Form elements for Profile Builder
     *
     * @return void
     */
    function form_elements_profile() {
        ?>

        <div class="wpuf-loading hide"></div>

        <h2><?php _e( 'Profile Fields', 'wpuf' ); ?></h2>
        <div class="wpuf-form-buttons">
            <button class="button" data-name="user_login" data-type="text"><?php _e( 'Username', 'wpuf' ); ?></button>
            <button class="button" data-name="first_name" data-type="textarea"><?php _e( 'First Name', 'wpuf' ); ?></button>
            <button class="button" data-name="last_name" data-type="textarea"><?php _e( 'Last Name', 'wpuf' ); ?></button>
            <button class="button" data-name="nickname" data-type="text"><?php _e( 'Nickname', 'wpuf' ); ?></button>
            <button class="button" data-name="user_email" data-type="category"><?php _e( 'E-mail', 'wpuf' ); ?></button>
            <button class="button" data-name="user_url" data-type="text"><?php _e( 'Website', 'wpuf' ); ?></button>
            <button class="button" data-name="user_bio" data-type="textarea"><?php _e( 'Biographical Info', 'wpuf' ); ?></button>
            <button class="button" data-name="password" data-type="password"><?php _e( 'Password', 'wpuf' ); ?></button>
            <button class="button" data-name="user_avatar" data-type="avatar"><?php _e( 'Avatar', 'wpuf' ); ?></button>

            <?php do_action( 'wpuf_form_buttons_user' ); ?>
        </div>

        <?php
        $this->form_elements_common();
        $this->publish_button();
    }

    /**
     * Saves the form settings
     *
     * @param int $post_id
     * @param object $post
     * @return int|void
     */
    function save_form_meta( $post_id, $post ) {
        if ( !isset($_POST['wpuf_form_editor'])) {
            return $post->ID;
        }

        if ( !wp_verify_nonce( $_POST['wpuf_form_editor'], plugin_basename( __FILE__ ) ) ) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ) ) {
            return $post->ID;
        }

        // var_dump($_POST['wpuf_input']); die();
        // var_dump($_POST); die();
        update_post_meta( $post->ID, $this->form_data_key, $_POST['wpuf_input'] );
        update_post_meta( $post->ID, $this->form_settings_key, $_POST['wpuf_settings'] );
    }

    /**
     * Edit form elements area for post
     *
     * @global object $post
     * @global string $pagenow
     */
    function edit_form_area() {
        global $post, $pagenow;

        $form_inputs = get_post_meta( $post->ID, $this->form_data_key, true );
        ?>

        <input type="hidden" name="wpuf_form_editor" id="wpuf_form_editor" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

        <div style="margin-bottom: 10px">
            <button class="button wpuf-collapse"><?php _e( 'Toggle All', 'wpuf' ); ?></button>
        </div>

        <div class="wpuf-updated">
            <p><?php _e( 'Click on a form element to add to the editor', 'wpuf' ); ?></p>
        </div>

        <ul id="wpuf-form-editor" class="wpuf-form-editor unstyled">

        <?php
        if ($form_inputs) {
            $count = 0;
            foreach ($form_inputs as $order => $input_field) {
                $name = ucwords( str_replace( '_', ' ', $input_field['template'] ) );

                if ( $input_field['template'] == 'taxonomy') {
                    
                    WPUF_Admin_Template_Post::$input_field['template']( $count, $name, $input_field['name'], $input_field );
                    
                } else if ( method_exists( 'WPUF_Admin_Template_Post', $input_field['template'] ) ) {
                    
                    WPUF_Admin_Template_Post::$input_field['template']( $count, $name, $input_field );
                    
                } else {
                    do_action( 'wpuf_admin_template_post_' . $input_field['template'], $name, $count, $input_field );
                }

                $count++;
            }
        }
        ?>
        </ul>

        <?php
    }

    /**
     * Edit form elements area for profile
     *
     * @global object $post
     * @global string $pagenow
     */
    function edit_form_area_profile() {
        global $post, $pagenow;

        $form_inputs = get_post_meta( $post->ID, $this->form_data_key, true );
        ?>

        <input type="hidden" name="wpuf_form_editor" id="wpuf_form_editor" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

        <div style="margin-bottom: 10px">
            <button class="button wpuf-collapse"><?php _e( 'Toggle All', 'wpuf' ); ?></button>
        </div>

        <div class="wpuf-updated">
            <p><?php _e( 'Click on a form element to add to the editor', 'wpuf' ); ?></p>
        </div>

        <ul id="wpuf-form-editor" class="wpuf-form-editor unstyled">

        <?php
        if ($form_inputs) {
            $count = 0;
            foreach ($form_inputs as $order => $input_field) {
                $name = ucwords( str_replace( '_', ' ', $input_field['template'] ) );

                WPUF_Admin_Template_Profile::$input_field['template']( $count, $name, $input_field );

                $count++;
            }
        }
        ?>
        </ul>

        <?php
    }

    /**
     * Ajax Callback handler for insrting fields in forms
     *
     * @return void
     */
    function ajax_post_add_element() {

        $name = $_POST['name'];
        $type = $_POST['type'];
        $field_id = $_POST['order'];

        switch ($name) {
            case 'post_title':
                WPUF_Admin_Template_Post::post_title( $field_id, 'Post Title');
                break;

            case 'post_content':
                WPUF_Admin_Template_Post::post_content( $field_id, 'Post Body');
                break;

            case 'post_excerpt':
                WPUF_Admin_Template_Post::post_excerpt( $field_id, 'Excerpt');
                break;

            case 'tags':
                WPUF_Admin_Template_Post::post_tags( $field_id, 'Tags');
                break;

            case 'featured_image':
                WPUF_Admin_Template_Post::featured_image( $field_id, 'Featured Image');
                break;

            case 'custom_text':
                WPUF_Admin_Template_Post::text_field( $field_id, 'Custom field: Text');
                break;

            case 'custom_textarea':
                WPUF_Admin_Template_Post::textarea_field( $field_id, 'Custom field: Textarea');
                break;

            case 'custom_select':
                WPUF_Admin_Template_Post::dropdown_field( $field_id, 'Custom field: Select');
                break;

            case 'custom_multiselect':
                WPUF_Admin_Template_Post::multiple_select( $field_id, 'Custom field: Multiselect');
                break;

            case 'custom_radio':
                WPUF_Admin_Template_Post::radio_field( $field_id, 'Custom field: Radio');
                break;

            case 'custom_checkbox':
                WPUF_Admin_Template_Post::checkbox_field( $field_id, 'Custom field: Checkbox');
                break;

            case 'custom_image':
                WPUF_Admin_Template_Post::image_upload( $field_id, 'Custom field: Image');
                break;

            case 'custom_file':
                WPUF_Admin_Template_Post::file_upload( $field_id, 'Custom field: File Upload');
                break;

            case 'custom_url':
                WPUF_Admin_Template_Post::website_url( $field_id, 'Custom field: URL');
                break;

            case 'custom_email':
                WPUF_Admin_Template_Post::email_address( $field_id, 'Custom field: E-Mail');
                break;

            case 'custom_repeater':
                WPUF_Admin_Template_Post::repeat_field( $field_id, 'Custom field: Repeat Field');
                break;

            case 'custom_html':
                WPUF_Admin_Template_Post::custom_html( $field_id, 'HTML' );
                break;

            case 'category':
                WPUF_Admin_Template_Post::taxonomy( $field_id, 'Category', $type );
                break;

            case 'taxonomy':
                WPUF_Admin_Template_Post::taxonomy( $field_id, 'Taxonomy: ' . $type, $type );
                break;

            case 'section_break':
                WPUF_Admin_Template_Post::section_break( $field_id, 'Section Break' );
                break;

            case 'recaptcha':
                WPUF_Admin_Template_Post::recaptcha( $field_id, 'reCaptcha' );
                break;

            case 'action_hook':
                WPUF_Admin_Template_Post::action_hook( $field_id, 'Action Hook' );
                break;

            case 'really_simple_captcha':
                WPUF_Admin_Template_Post::really_simple_captcha( $field_id, 'Really Simple Captcha' );
                break;

            case 'custom_date':
                WPUF_Admin_Template_Post::date_field( $field_id, 'Custom Field: Date' );
                break;

            case 'custom_map':
                WPUF_Admin_Template_Post::google_map( $field_id, 'Custom Field: Google Map' );
                break;
                break;

            case 'custom_hidden':
                WPUF_Admin_Template_Post::custom_hidden_field( $field_id, 'Hidden Field' );
                break;

            case 'toc':
                WPUF_Admin_Template_Post::toc( $field_id, 'TOC' );
                break;

            case 'user_login':
                WPUF_Admin_Template_Profile::user_login( $field_id, __( 'Username', 'wpuf' ) );
                break;

            case 'first_name':
                WPUF_Admin_Template_Profile::first_name( $field_id, __( 'First Name', 'wpuf' ) );
                break;

            case 'last_name':
                WPUF_Admin_Template_Profile::last_name( $field_id, __( 'Last Name', 'wpuf' ) );
                break;

            case 'nickname':
                WPUF_Admin_Template_Profile::nickname( $field_id, __( 'Nickname', 'wpuf' ) );
                break;

            case 'user_email':
                WPUF_Admin_Template_Profile::user_email( $field_id, __( 'E-mail', 'wpuf' ) );
                break;

            case 'user_url':
                WPUF_Admin_Template_Profile::user_url( $field_id, __( 'Website', 'wpuf' ) );
                break;

            case 'user_bio':
                WPUF_Admin_Template_Profile::description( $field_id, __( 'Biographical Info', 'wpuf' ) );
                break;

            case 'password':
                WPUF_Admin_Template_Profile::password( $field_id, __( 'Password', 'wpuf' ) );
                break;

            case 'user_avatar':
                WPUF_Admin_Template_Profile::avatar( $field_id, __( 'Avatar', 'wpuf' ) );
                break;


            default:
                do_action( 'wpuf_admin_field_' . $name, $type, $field_id );
                break;
        }

        exit;
    }

}
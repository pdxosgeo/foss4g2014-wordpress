<?php

/**
 * Manage Subscription packs
 *
 * @package WP User Frontend
 */
class WPUF_Admin_Subscription {

    private $table;
    private $db;
    public $baseurl;

    function __construct() {
        global $wpdb;

        $this->db = $wpdb;
        $this->table = $this->db->prefix . 'wpuf_subscription';
        $this->baseurl = admin_url( 'admin.php?page=wpuf_subscription' );
    }

    function get_packs() {
        return $this->db->get_results( "SELECT * FROM {$this->table} ORDER BY created DESC" );
    }

    function get_pack( $pack_id ) {
        return $this->db->get_row( $this->db->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $pack_id ) );
    }

    function delete_pack( $pack_id ) {
        $this->db->query( $this->db->prepare( "DELETE FROM {$this->table} WHERE id= %d", $pack_id ) );
    }

    function list_packs() {

        //delete packs
        if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "del" ) {
            check_admin_referer( 'wpuf_pack_del' );
            $this->delete_pack( $_GET['id'] );
            echo '<div class="updated fade" id="message"><p><strong>' . __( 'Pack Deleted', 'wpuf' ) . '</strong></p></div>';

            echo '<script type="text/javascript">window.location.href = "' . $this->baseurl . '";</script>';
        }
        ?>

        <table class="widefat meta" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th scope="col"><?php _e( 'Name', 'wpuf' ); ?></th>
                    <th scope="col"><?php _e( 'Description', 'wpuf' ); ?></th>
                    <th scope="col"><?php _e( 'Cost', 'wpuf' ); ?></th>
                    <th scope="col"><?php _e( 'Validity', 'wpuf' ); ?></th>
                    <th scope="col"><?php _e( 'Post Count', 'wpuf' ); ?></th>
                    <th scope="col"><?php _e( 'Action', 'wpuf' ); ?></th>
                </tr>
            </thead>
            <?php
            $packs = $this->get_packs();
            if ( $packs ) {
                $count = 0;
                foreach ($packs as $row) {
                    ?>
                    <tr valign="top" <?php echo ( ($count % 2) == 0) ? 'class="alternate"' : ''; ?>>
                        <td><?php echo stripslashes( htmlspecialchars( $row->name ) ); ?></td>
                        <td><?php echo stripslashes( htmlspecialchars( $row->description ) ); ?></td>
                        <td><?php echo $row->cost; ?> <?php echo get_option( 'wpuf_sub_currency' ); ?></td>
                        <td><?php echo ( $row->duration == 0 ) ? 'Unlimited' : $row->duration . ' days'; ?></td>
                        <td><?php echo ( $row->count == 0 ) ? 'Unlimited' : $row->count; ?></td>
                        <td>
                            <a href="<?php echo wp_nonce_url( add_query_arg( array('action' => 'edit', 'pack_id' => $row->id), $this->baseurl, 'wpuf_pack_edit' ) ); ?>">
                                <?php _e( 'Edit', 'wpuf' ); ?>
                            </a>
                            <span class="sep">|</span>
                            <a href="<?php echo wp_nonce_url( add_query_arg( array('action' => 'del', 'id' => $row->id), $this->baseurl ), 'wpuf_pack_del' ); ?>" onclick="return confirm('<?php _e( 'Are you sure to delete this pack?', 'wpuf' ); ?>');">
                                <?php _e( 'Delete', 'wpuf' ); ?>
                            </a>
                        </td>

                    </tr>
                    <?php
                    $count++;
                }
                ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6"><?php _e( 'No subscription pack found', 'wpuf' ); ?></td>
                </tr>
            <?php } ?>

        </table>
        <?php
    }

    function form( $pack_id = null ) {
        //save options changes
        if ( isset( $_POST['wpuf_sub_opts_submit'] ) ) {
            check_admin_referer( 'wpuf_sub_settings', 'wpuf_sub_settings' );

            $error = false;

            if ( $_POST['name'] == '' ) {
                $error = 'Please enter pack name';
            } else if ( $_POST['cost'] == '' ) {
                $error = 'Please enter pack cost';
            } else if ( $_POST['duration'] == '' ) {
                $error = 'Please enter pack duration';
            } else if ( $_POST['count'] == '' ) {
                $error = 'Please enter post count';
            }

            if ( !$error ) { //no errors
                //whatever, insert the values
                $data = array(
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'count' => intval( $_POST['count'] ),
                    'duration' => intval( $_POST['duration'] ),
                    'cost' => floatval( $_POST['cost'] ),
                    'created' => current_time( 'mysql' )
                );

                if ( $pack_id ) {
                    $result = $this->db->update( $this->table, $data, array('id' => $pack_id) );

                    echo '<div class="updated"><p><strong>' . __( 'Pack update successful.', 'wpuf' ) . '</strong></p></div>';
                } else {
                    $result = $this->db->insert( $this->table, $data );

                    echo '<div class="updated"><p><strong>' . __( 'Pack creation successful.', 'wpuf' ) . '</strong></p></div>';
                }
            } else {
                echo '<div class="error"><p><strong>' . $error . '</strong></p></div>';
            }
        }

        if ( $pack_id ) {
            $pack = $this->get_pack( $pack_id );
        }

        $name = $pack_id ? $pack->name : '';
        $description = $pack_id ? $pack->description : '';
        $cost = $pack_id ? $pack->cost : '';
        $duration = $pack_id ? $pack->duration : '0';
        $count = $pack_id ? $pack->count : '0';
        ?>
        <form action="" method="post" style="margin-top: 20px;">

            <?php wp_nonce_field( 'wpuf_sub_settings', 'wpuf_sub_settings' ); ?>

            <table class="widefat meta" style="width: 70%">
                <thead>
                    <tr>
                        <th scope="col" colspan="2" style="font-size: 14px;"><?php _e( 'Subscription Pack Details', 'wpuf' ) ?></th>
                    </tr>
                </thead>

                <tbody>
                    <tr valign="top">
                        <td scope="row" class="label"><label for="field"><?php _e( 'Pack Name', 'wpuf' ) ?></label></td>
                        <td>
                            <input type="text" size="25" style="" id="name" value="<?php echo esc_attr( $name ); ?>" name="name" />
                            <span class="description"><?php _e( 'subscription pack name', 'wpuf' ); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row" class="label"><label for="label"><?php _e( 'Pack Description', 'wpuf' ); ?></label></td>
                        <td>
                            <input type="text" size="25" style="" id="label" value="<?php echo esc_attr( $description ); ?>" name="description" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row" class="label"><label for="help"><?php _e( 'Pack Cost', 'wpuf' ); ?></label></td>
                        <td>
                            <input type="text" size="25" style="" id="help" value="<?php echo esc_attr( $cost ); ?>" name="cost" />
                            <span class="description"><?php _e( 'price/cost of the pack', 'wpuf' ); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row" class="label"><label for="required"><?php _e( 'Pack validity', 'wpuf' ); ?></label></td>
                        <td>
                            <input type="text" size="25" style="" id="help" value="<?php echo esc_attr( $duration ); ?>" name="duration" />
                            <span class="description"><?php _e( 'How many days this pack will remain valid? Enter <strong>0</strong> for unlimited.', 'wpuf' ); ?></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td scope="row" class="label"><label for="region"><?php _e( 'Number of Posts', 'wpuf' ); ?></label></td>
                        <td>
                            <input type="text" size="25" style="" id="help" value="<?php echo esc_attr( $count ); ?>" name="count" />
                            <span class="description"><?php _e( 'How many posts the user can list with this pack? Enter <strong>0</strong> for unlimited.', 'wpuf' ); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php if ( !$pack_id ) { ?>
                <input name="wpuf_sub_opts_submit" type="submit" class="button-primary" value="<?php _e( 'Add Package', 'wpuf' ) ?>" style="margin-top: 10px;" />
            <?php } else { ?>
                <input name="wpuf_sub_opts_submit" type="submit" class="button-primary" value="<?php _e( 'Update Package', 'wpuf' ) ?>" style="margin-top: 10px;" />
            <?php } ?>

            <a class="button" href="<?php echo $this->baseurl; ?>"><?php _e( '&larr; Back', 'wpuf' ); ?></a>
        </form>
        <?php
    }

}

$subscription = new WPUF_Admin_Subscription();
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>
        <?php _e( 'Subscription Manager', 'wpuf' ) ?>
        <a href="<?php echo $subscription->baseurl; ?>&amp;action=new" class="button-primary"><?php _e( 'Add Package', 'wpuf' ); ?></a>
    </h2>

    <?php
    $action = isset( $_GET['action'] ) ? $_GET['action'] : '';
    $pack_id = isset( $_GET['pack_id'] ) ? $_GET['pack_id'] : '';

    switch ($action) {
        case 'edit':
            $subscription->form( $pack_id );
            break;

        case 'new':
            $subscription->form();
            break;

        default:
            $subscription->list_packs();
            break;
    }
    ?>
</div>


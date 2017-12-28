<?php
class UM_Private_Content_Admin {

    function __construct() {
        add_action( 'um_admin_user_row_actions',  array( &$this, 'um_admin_user_row_actions' ), 100, 2 );
        add_action( 'admin_enqueue_scripts',  array(&$this, 'admin_scripts'), 0 );

        add_action( 'load-edit.php', array( &$this, 'hide_private_content_list' ) );
        add_action( 'load-post-new.php', array( &$this, 'hide_private_content_add' ) );
        add_action( 'load-post.php', array( &$this, 'hide_private_content_add_button' ) );
        add_action( 'edit_form_top', array( &$this, 'add_username' ), 10, 1 );
    }

    /**
     * Custom row actions for users page
     *
     * @param WP_Post $post
     */
    function add_username( $post ) {
        if ( $post->post_type == 'um_private_content' ) {
            global $wpdb;

            $user_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT um.user_id
                FROM {$wpdb->usermeta} um
                WHERE meta_key='_um_private_content_post_id' AND meta_value=%d",
                $post->ID
            ) );

            $user = get_userdata( $user_id );
            if ( ! empty( $user->user_login ) )
                echo '<h2 style="margin: 0;">' . sprintf( __( 'Private Content for %s', 'um-private-content'), $user->user_login ) . '</h2>';
        }
    }


    /**
     * Custom row actions for users page
     *
     * @param array $actions
     * @param int $user_object user ID
     * @return array
     */
    function um_admin_user_row_actions( $actions, $user_object ) {
        global $um_private_content;
        $private_content_link = $um_private_content->get_private_content_post_link( $user_object );
        if ( $private_content_link ) {
            $actions['private-content'] = "<a class='' href='" . $private_content_link . "'>" . __( 'Private Content', 'um-private-content' ) . "</a>";
        }

        return $actions;
    }


    /**
     *
     */
    function admin_scripts() {
        wp_register_script( 'um_private_content', um_private_content_url . 'assets/js/um-private-content.js', array('um_admin_scripts'), '', true );
        wp_enqueue_script( 'um_private_content' );

        wp_register_style('um_private_content_settings', um_private_content_url . 'assets/css/settings.css' );
        wp_enqueue_style('um_private_content_settings');
    }


    function hide_private_content_list() {
        global $typenow;

        if ( 'um_private_content' == $typenow ) {
            wp_redirect( admin_url( 'users.php' ) ); exit;
        }
    }


    function hide_private_content_add() {
        global $typenow;

        if ( 'um_private_content' == $typenow ) {
            wp_redirect( admin_url( 'users.php' ) ); exit;
        }
    }


    function hide_private_content_add_button() {
        global $typenow;

        if ( 'um_private_content' == $typenow ) { ?>
            <style type="text/css">
                #minor-publishing {
                    display:none;
                }

                #delete-action {
                    display:none;
                }

                .page-title-action {
                    display:none;
                }
            </style>
        <?php }
    }
}
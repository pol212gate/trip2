<?php

class UM_Private_Content_API {

	function __construct() {

        $this->plugin_inactive = false;

        add_action( 'init', array(&$this, 'plugin_check'), 1);

        add_action( 'init', array(&$this, 'init'), 1);

        add_action( 'init',  array( &$this, 'create_cpt' ), 2 );
        add_action( 'user_register', array( &$this, 'add_private_content' ), 12, 1 );

        add_action( 'wp_ajax_um_generate_private_pages', array( &$this, 'ajax_generate_private_pages' ), 12, 1 );
    }

    /***
     ***	@Check plugin requirements
     ***/
    function plugin_check(){

        if ( ! class_exists('UM_API') ) {

            $this->add_notice( sprintf(__('The <strong>%s</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>','um-private-content'), um_private_content_extension) );
            $this->plugin_inactive = true;

        } else if ( ! version_compare( ultimatemember_version, um_private_content_requires, '>=' ) ) {

            $this->add_notice( sprintf(__('The <strong>%s</strong> extension requires a <a href="https://wordpress.org/plugins/ultimate-member">newer version</a> of Ultimate Member to work properly.','um-private-content'), um_private_content_extension) );
            $this->plugin_inactive = true;

        } else if ( ! get_option('__ultimatemember_private_content_setup') ) {

            $this->add_notice( sprintf(__('Existing users do not have a private content page. To generate private content pages for existing users <a href="%s">run the process</a>.','um-private-content'), add_query_arg('um-setup', 'private_content') ) );
            $this->plugin_inactive = true;

        }

    }

    /***
     ***	@Add notice
     ***/
    function add_notice( $msg ) {

        if ( !is_admin() ) return;

        echo '<div class="error"><p>' . __( $msg,'um-private-content' ) . '</p></div>';

    }


    function run_setup() {

        register_post_type( 'um_private_content', array(
            'labels'        => array(
                'name'                  => __( 'Private Contents' ),
                'singular_name'         => __( 'Private Content' ),
                'add_new'               => __( 'Add New Private Content' ),
                'add_new_item'          => __('Add New Private Content' ),
                'edit_item'             => __('Edit Private Content'),
                'not_found'             => __('You did not create any private contents yet'),
                'not_found_in_trash'    => __('Nothing found in Trash'),
                'search_items'          => __('Search Private Contents')
            ),
            'show_ui'       => true,
            'show_in_menu'  => false,
            'public'        => false,
            'supports'      => array( 'editor' )
        ) );

        $empty_users = get_users( array(
            'meta_query' => array(
                array(
                    'key' => '_um_private_content_post_id',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'number' => -1,
            'fields' => 'ids'
        ) );

        if ( ! empty( $empty_users ) ) {
            foreach ( $empty_users as $user_id ) {
                $post_id = wp_insert_post( array(
                    'post_title'    => 'private_content_' . $user_id,
                    'post_type'     => 'um_private_content',
                    'post_status'   => 'publish',
                    'post_content'  => ''
                ) );

                update_user_meta( $user_id, '_um_private_content_post_id', $post_id );
            }
        }

        update_option('__ultimatemember_private_content_setup', true );
        exit( wp_redirect('users.php') );

    }


    function init() {

        if ( isset( $_REQUEST['um-setup'] ) && $_REQUEST['um-setup'] == 'private_content' && is_admin() && current_user_can( 'manage_options' ) ) {
            $this->run_setup();
        }

        if ( $this->plugin_inactive ) return;

        // Required classes
        require_once um_private_content_path . 'core/um-private-content-admin.php';
        require_once um_private_content_path . 'core/um-private-content-shortcode.php';

        $this->admin = new UM_Private_Content_Admin();
        $this->shortcode = new UM_Private_Content_Shortcode();

        require_once um_private_content_path . 'core/filters/um-private-content-settings.php';
        require_once um_private_content_path . 'core/filters/um-private-content-tabs.php';
    }


    /***
     ***	@creates needed cpt
     ***/
    function create_cpt() {

        register_post_type( 'um_private_content', array(
            'labels'        => array(
                'name'                  => __( 'Private Contents' ),
                'singular_name'         => __( 'Private Content' ),
                'add_new'               => __( 'Add New Private Content' ),
                'add_new_item'          => __('Add New Private Content' ),
                'edit_item'             => __('Edit Private Content'),
                'not_found'             => __('You did not create any private contents yet'),
                'not_found_in_trash'    => __('Nothing found in Trash'),
                'search_items'          => __('Search Private Contents')
            ),
            'show_ui'       => true,
            'show_in_menu'  => false,
            'public'        => false,
            'supports'      => array( 'editor' )
        ) );

    }


    function add_private_content( $user_id ) {
        $post_id = wp_insert_post( array(
            'post_title'    => 'private_content_' . $user_id,
            'post_type'     => 'um_private_content',
            'post_status'   => 'publish',
            'post_content'  => ''
        ) );

        update_user_meta( $user_id, '_um_private_content_post_id', $post_id );
    }


    function ajax_generate_private_pages() {
        global $wpdb;

        $private_posts = $wpdb->get_results(
            "SELECT um.meta_value as post_id,
                    um.user_id as user_id
            FROM {$wpdb->usermeta} um
            WHERE meta_key='_um_private_content_post_id'",
        ARRAY_A );

        if ( ! empty( $private_posts ) ) {
            foreach ( $private_posts as $post ) {
                $postdata = get_post( $post['post_id'] );
                if ( empty( $postdata ) || is_wp_error( $postdata ) ) {
                    $post_id = wp_insert_post( array(
                        'post_title'    => 'private_content_' . $post['user_id'],
                        'post_type'     => 'um_private_content',
                        'post_status'   => 'publish',
                        'post_content'  => ''
                    ) );

                    update_user_meta( $post['user_id'], '_um_private_content_post_id', $post_id );
                }
            }
        }

        $empty_users = get_users( array(
            'meta_query' => array(
                array(
                    'key' => '_um_private_content_post_id',
                    'compare' => 'NOT EXISTS'
                )
            ),
            'number' => -1,
            'fields' => 'ids'
        ) );

        if ( ! empty( $empty_users ) ) {
            foreach ( $empty_users as $user_id ) {
                $post_id = wp_insert_post( array(
                    'post_title'    => 'private_content_' . $user_id,
                    'post_type'     => 'um_private_content',
                    'post_status'   => 'publish',
                    'post_content'  => ''
                ) );

                update_user_meta( $user_id, '_um_private_content_post_id', $post_id );
            }
        }

        wp_send_json_success( array( 'message' => __( 'Private Content pages was generated successfully', 'um-private-content' ) ) );

    }


    function get_private_content_post_link( $user_id ) {
        $private_post_id = get_user_meta( $user_id, '_um_private_content_post_id', true );
        $post = get_post( $private_post_id );

        if ( ! empty( $post ) )
            //return get_permalink( $post );
            return get_edit_post_link( $post->ID );
        else
            return false;
    }

}

$um_private_content = new UM_Private_Content_API();
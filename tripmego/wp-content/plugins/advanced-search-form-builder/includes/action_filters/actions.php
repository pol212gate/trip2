<?php 

$ASFB_action = new ASFB_action();

add_action('init', array($ASFB_action, 'ASFB_register_post_type'));
add_filter('manage_asfb_post_posts_columns', array($ASFB_action, 'add_head_colum'));
add_action('manage_asfb_post_posts_custom_column', array($ASFB_action, 'add_content_colum'), 10, 2);

add_action('wp_head', array($ASFB_action, 'initVariableJS'), 10, 2);

class ASFB_action
{
    public function initVariableJS() {
        echo '<script>var asfbGlobal = '. json_encode(array(
            'endpoint' => array(
                'search' =>  home_url() . '/wp-json/asfb/v1/search?',
                'suggestion' =>  home_url() . '/wp-json/asfb/v1/suggestion?',
            )
        )) .'</script>';
    }
    public function ASFB_register_post_type() {



        $labels = array(
            'name'                  => __( 'ASF builder', 'advanced_search_form_builder' ),
            'singular_name'         => __( 'ASF builder', 'advanced_search_form_builder' ),
            'menu_name'             => __( 'ASF builder', 'advanced_search_form_builder' ),
            'name_admin_bar'        => __( 'ASF builder', 'advanced_search_form_builder' ),
            'archives'              => __( 'Item Archives', 'advanced_search_form_builder' ),
            'attributes'            => __( 'Item Attributes', 'advanced_search_form_builder' ),
            'parent_item_colon'     => __( 'Parent Item:', 'advanced_search_form_builder' ),
            'all_items'             => __( 'All Items', 'advanced_search_form_builder' ),
            'add_new_item'          => __( 'Add New Item', 'advanced_search_form_builder' ),
            'add_new'               => __( 'Add New', 'advanced_search_form_builder' ),
            'new_item'              => __( 'New Item', 'advanced_search_form_builder' ),
            'edit_item'             => __( 'Edit Item', 'advanced_search_form_builder' ),
            'update_item'           => __( 'Update Item', 'advanced_search_form_builder' ),
            'view_item'             => __( 'View Item', 'advanced_search_form_builder' ),
            'view_items'            => __( 'View Items', 'advanced_search_form_builder' ),
            'search_items'          => __( 'Search Item', 'advanced_search_form_builder' ),
            'not_found'             => __( 'Not found', 'advanced_search_form_builder' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'advanced_search_form_builder' ),
            'featured_image'        => __( 'Featured Image', 'advanced_search_form_builder' ),
            'set_featured_image'    => __( 'Set featured image', 'advanced_search_form_builder' ),
            'remove_featured_image' => __( 'Remove featured image', 'advanced_search_form_builder' ),
            'use_featured_image'    => __( 'Use as featured image', 'advanced_search_form_builder' ),
            'insert_into_item'      => __( 'Insert into item', 'advanced_search_form_builder' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'advanced_search_form_builder' ),
            'items_list'            => __( 'Items list', 'advanced_search_form_builder' ),
            'items_list_navigation' => __( 'Items list navigation', 'advanced_search_form_builder' ),
            'filter_items_list'     => __( 'Filter items list', 'advanced_search_form_builder' ),
        );
        $args = array(
            'label'                 => __( 'ASF builder', 'advanced_search_form_builder' ),
            'description'           => __( 'ASF builder Description', 'advanced_search_form_builder' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,       
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        global $asfbFlat;

        if (isset($asfbFlat['version']) && $asfbFlat['version'] == 'mp') {
            $args['capabilities'] = array(
                'create_posts' => 'do_not_allow',
            );
            $args['map_meta_cap'] = true;
        }

        register_post_type( 'asfb_post', $args );
    }

    public function add_head_colum($defaults) {
        $defaults['ASFB_shorcode'] = __('Shortcode', 'advanced_search_form_builder');
        return array(
            'title' => $defaults['title'],
            'ASFB_shorcode' => $defaults['ASFB_shorcode'],
            'date' => $defaults['date'],
        );
    }

    public function add_content_colum($column_name, $post_ID) {
        if ($column_name == 'ASFB_shorcode') {
            echo '<code>[form_search id="'. $post_ID .'"]</code>';
        }
    }
}



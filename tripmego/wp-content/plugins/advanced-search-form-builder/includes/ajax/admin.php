<?php
add_action( 'wp_ajax_get_terms', 'ASFB_get_terms_ajax' );
add_action( 'wp_ajax_nopriv_get_terms', 'ASFB_get_terms_ajax' );

function ASFB_get_terms_ajax()
{
    $taxo = ASFB_request::getQuery('taxonomy', '');
    if ($taxo) {
        $terms = get_terms(array(
            'taxonomy' => $taxo
        ));
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            wp_send_json_success($terms);
        } else {
            wp_send_json_error($terms);
        }
    }

    die;
}

add_action( 'wp_ajax_get_keys', 'ASFB_get_meta_key_ajax' );
add_action( 'wp_ajax_nopriv_get_keys', 'ASFB_get_meta_key_ajax' );

function ASFB_get_meta_key_ajax()
{
    $post_type = ASFB_request::getQuery('post_type', '');
    $q = ASFB_request::getQuery('q', '');
    if (is_array($post_type)) {
        $keys = ASFB_get_meta_keys($post_type, $q,'publish', array('limit' => 20));

        if ( ! empty( $keys ) && ! is_wp_error( $keys ) ){
            wp_send_json_success($keys);
        } else {
            wp_send_json_error($keys);
        }
    }

    die;
}


add_action( 'wp_ajax_get_template', 'ASFB_get_template' );
add_action( 'wp_ajax_nopriv_get_template', 'ASFB_get_template' );

function ASFB_get_template()
{
    $id = ASFB_request::getQuery('id', '');
    if ($id > 0 && $id <= 7) {
        $str = file_get_contents(ASFB_PATH . 'admin/template_result/tpl'. $id .'.txt');
        wp_send_json_success($str);
    }

    die;
}
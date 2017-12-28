<?php

global $ASFB_config;

$text_styling_item = cs_get_option( 'text_styling_item' );

$fieldsStyling = array();

$fieldsStyling[] = array(
    'type' => 'subheading',
    'content' => __('Search result layout', 'advanced_search_form_builder'),
    'dependency' => array('styling_result_woo', '==', 0),
);

if ( class_exists( 'WooCommerce' ) ) {
    $fieldsStyling[] = array(
        'id' => 'styling_result_woo',
        'type' => 'switcher',
        'title' => __('Defaut Woocommerce', 'advanced_search_form_builder'),
        'desc' => __('Using template defaut of Woocommerce', 'advanced_search_form_builder'),
        'dependency' => array('filter_post_type_source', '==', 'product'),
    );
}

$fieldsStyling[] = array(
    'id' => 'include_form',
    'type' => 'switcher',
    'title' => __('Include form search in result page', 'advanced_search_form_builder'),
    'default' => true
);

$fieldsStyling[] = array(
    'id' => 'styling_result_column_desktop', // this is must be unique
    'type' => 'number',
    'default' => 2,
    'attributes' => array(
        'style' => 'width: 100%'
    ),
    'title' => __('Number of column DESKTOP', 'advanced_search_form_builder'),
    'desc' => 'Width window >= 960px',
);

$fieldsStyling[] = array(
    'id' => 'styling_result_column_tablet', // this is must be unique
    'type' => 'number',
    'default' => 2,
    'attributes' => array(
        'style' => 'width: 100%'
    ),
    'title' => __('Number of column TABLET', 'advanced_search_form_builder'),
    'desc' => 'Width window 481px -> 959px',
);

$fieldsStyling[] = array(
    'id' => 'styling_result_column_mobile', // this is must be unique
    'type' => 'number',
    'default' => 1,
    'attributes' => array(
        'style' => 'width: 100%'
    ),
    'title' => __('Number of column MOBILE', 'advanced_search_form_builder'),
    'desc' => 'Width window <= 480px',
);

$fieldsStyling[] = array(
    'id' => 'styling_result_column_template',
    'type' => 'textarea',
    'default' => '',
    'sanitize' => false,
    'validate' => false,
    'attributes' => array(
        'style' => 'width: 100%; height: 0px; display:none',
        'id' => 'codeItemResult',
    ),
//    'dependency' => array('styling_result_woo', '==', 0),
);



$fieldsStyling[] = array(
    'id' => 'styling_result_item_layout',
    'type' => 'image_select',
    'title' => __('Select a template', 'advanced_search_form_builder'),
    'default' => 1,
    'require' => true,
    'attributes' => array(
        'class' => 'templateItemResult',
    ),
    'options' => array(
        1 => ASFB_URL . 'admin/img/1.png',
        2 => ASFB_URL . 'admin/img/2.png',
        3 => ASFB_URL . 'admin/img/3.png',
        4 => ASFB_URL . 'admin/img/4.png',
        5 => ASFB_URL . 'admin/img/5.png',
        6 => ASFB_URL . 'admin/img/6.png',
        7 => ASFB_URL . 'admin/img/7.png',
    ),
//    'dependency' => array('styling_result_woo', '==', 0),
);
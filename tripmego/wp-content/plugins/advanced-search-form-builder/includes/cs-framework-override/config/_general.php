<?php
global $ASFB_config;

$fieldsGenaral = array(
    array(
        'id' => 'debug_mode',
        'type' => 'switcher',
        'title' => __('Debug Mode', 'advanced_search_form_builder'),
        'default' => false
    ),

    array(
        'id' => 'en_ajax',
        'type' => 'switcher',
        'title' => __('Enable Ajax search', 'advanced_search_form_builder'),
        'default' => true
    ),
    
    array(
        'id' => 'posts_per_page', // this is must be unique
        'type' => 'text',
        'title' => __('Number of result', 'advanced_search_form_builder'),
    ),
    
    array(
        'id' => 'page_result',
        'type' => 'select',
        'title' => __('Select page result', 'advanced_search_form_builder'),
        'class' => 'chosen',
        'options' => 'posts',
        'query_args' => array(
            'orderby' => 'post_title',
            'order' => 'DESC',
            'post_type' => 'page',
            'post__not_in' => array(get_option($ASFB_config['default_setting']['key_page_result'])),
        ),
        'attributes' => array(
            'style' => 'width: 100%;'
        ),
        'default_option' => get_the_title( get_option($ASFB_config['default_setting']['key_page_result']) )
    ),
    array(
        'id' => 'endpoint_other', // this is must be unique
        'type' => 'text',
        'title' => __('or Endpoint url', 'advanced_search_form_builder'),
        'desc' => __('Url form action', 'advanced_search_form_builder'),
    ),
    array(
        'type' => 'heading',
        'content' => __('Autocomplete', 'advanced_search_form_builder'),
    ),
    array(
        'id' => 'en_autocomplete',
        'type' => 'switcher',
        'title' => __('Enable Autocomplete', 'advanced_search_form_builder'),
        'default' => true,
    ),
    array(
        'id' => 'autocom_post_title',
        'type' => 'switcher',
        'title' => __('Enable post title', 'advanced_search_form_builder'),
        'default' => true,
        'dependency' => array('en_autocomplete', '==', true)
    ),
    array(
        'id' => 'autocom_tax_title',
        'type' => 'select',
        'title' => __('Enable Taxonomy title', 'advanced_search_form_builder'),
        'class' => 'chosen',
        'options' => $taxonomyOptions,
        'dependency' => array('en_autocomplete', '==', true),
        'attributes' => array(
            'multiple' => 'multiple',
            'style' => 'width: 100%;'
        ),
    ),
    array(
        'id' => 'include_live_result',
        'type' => 'switcher',
        'title' => __('Include live result', 'advanced_search_form_builder'),
        'default' => true,
        'dependency' => array('en_autocomplete', '==', true),
    ),
    array(
        'id' => 'live_result_limit',
        'type' => 'text',
        'title' => __('Limit of item', 'advanced_search_form_builder'),
        'dependency' => array('en_autocomplete', '==', true),
        'default' => 6
    ),
);
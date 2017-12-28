<?php

global $ASFB_config;

$fieldsBehavior = array(
    array(
        'type'    => 'subheading',
        'content' => 'Auto search',
    ),
    //------------------------
    array(
        'id'    => 'auto_search',
        'type'  => 'switcher',
        'title' => __('Enable Auto search', 'advanced_search_form_builder'),
    ),
    array(
        'id'    => 'auto_search_character',
        'type'  => 'number',
        'title' => __('Minimum character', 'advanced_search_form_builder'),
        'dependency' => array('auto_search', '==', true),
    ),
    array(
        'id'    => 'auto_search_time_delay',
        'type'  => 'number',
        'title' => __('Time delay typing', 'advanced_search_form_builder'),
        'dependency' => array('auto_search', '==', true),
    ),
    array(
        'id'    => 'auto_search_on_change',
        'type'  => 'switcher',
        'title' => __('Enable search on change value select, radio, checkbox', 'advanced_search_form_builder'),
    ),
    array(
        'id'    => 'hide_submit',
        'type'  => 'switcher',
        'title' => __('Hide button submit', 'advanced_search_form_builder'),
    ),
);
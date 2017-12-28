<?php 
//defined( 'CS_ACTIVE_FRAMEWORK' )  or  define( 'CS_ACTIVE_FRAMEWORK',  false );
defined( 'CS_ACTIVE_METABOX'   )  or  define( 'CS_ACTIVE_METABOX',    true );
defined( 'CS_ACTIVE_TAXONOMY'   ) or  define( 'CS_ACTIVE_TAXONOMY',   false );
defined( 'CS_ACTIVE_SHORTCODE' )  or  define( 'CS_ACTIVE_SHORTCODE',  false );
defined( 'CS_ACTIVE_CUSTOMIZE' )  or  define( 'CS_ACTIVE_CUSTOMIZE',  false );

global $ASFB_config;

$ASFB_config = array(
    'default_setting' => array(
        'key_page_result'    => 'ASFB_page_result',
        'key_post_meta_form' => '_asfb_form_data'
    ),
    'shortcode' => array(
    	'search_result' => '[search_result]'
    ),
    'cache' => array(
        'lifetime' => 24 * 60 * 60,
        'dir' => plugin_dir_path(dirname(__DIR__)) . 'cache/',
    ),
    'form_filter' => array(
        'type_input' => array(
            'input'          => __('Input', 'advanced_search_form_builder'),
            'select'         => __('Select box', 'advanced_search_form_builder'),
            'radio'          => __('Radio', 'advanced_search_form_builder'),
            'checkbox'       => __('Checkbox', 'advanced_search_form_builder'),
            'number_range'   => __('Range number', 'advanced_search_form_builder'),
            'text'           => __('Text input', 'advanced_search_form_builder'),
            'swatch_color' => __('Swatch color', 'advanced_search_form_builder'),
            'swatch_text'  => __('Swatch text', 'advanced_search_form_builder')
        ),
        'type_input_cf' => array(
            'select'       => __('Select box', 'advanced_search_form_builder'),
            'radio'        => __('Radio', 'advanced_search_form_builder'),
            'checkbox'     => __('Checkbox', 'advanced_search_form_builder'),
            'number_range' => __('Range number', 'advanced_search_form_builder'),
            'textinput'         => __('Text input', 'advanced_search_form_builder'),
//            'datetime'     => __('Datetime picker', 'advanced_search_form_builder'),
            'swatch_color' => __('Swatch color', 'advanced_search_form_builder'),
            'swatch_text'  => __('Swatch text', 'advanced_search_form_builder')
        ),
        'compare_operator' => array(
            '<'          => '<', 
            '<='         => '<=', 
            '='          => '=', 
            '>'          => '>', 
            '>='         => '>=', 
            'LIKE'       => 'LIKE',
            '%LIKE%'     => '%LIKE%', 
            '%LIKE'      => '%LIKE', 
            'LIKE%'      => 'LIKE%', 
            'IN'         => 'IN', 
            'NOT IN'     => 'NOT IN', 
            'NOT EXISTS' => 'NOT EXISTS',
        )
    ),
    'plugin' => array(
        'url_pro' => '#'
    )
);
<?php
if (!defined('ABSPATH')) {
    die;
} // Cannot access pages directly.

global $ASFB_config;

$taxonomies = get_taxonomies(array(), 'objects');

$taxonomyOptions = array();
foreach ($taxonomies as $tax) {
    $taxonomyOptions[$tax->name] = $tax->label;
}

$post_types = get_post_types(array('public' => true), 'objects');
$postTypeOptions = array();
foreach ($post_types as $post_type) {
    $postTypeOptions[$post_type->name] = $post_type->label;
}

$textOptionsGlobal = cs_get_option('input_styling_item');
$optionsText = array();
if (is_array($textOptionsGlobal)) {
    foreach ($textOptionsGlobal as $item) {
        $optionsText[$item['css_file']['url']] = $item['text_label'];
    }
}

require_once plugin_dir_path(__FILE__) . '_general.php';
require_once plugin_dir_path(__FILE__) . '_filter.php';
require_once plugin_dir_path(__FILE__) . '_behavior.php';
require_once plugin_dir_path(__FILE__) . '_styling.php';

$options = array();

$options[] = array(
    'id' => $ASFB_config['default_setting']['key_post_meta_form'],
    'title' => 'Setting form',
    'post_type' => 'asfb_post',
    'context' => 'normal',
    'priority' => 'default',
    'sections' => array(
        array(
            'name' => 'general',
            'title' => 'General',
            'icon' => 'fa fa-cog',
            'fields' => $fieldsGenaral,
        ),
        array(
            'name' => 'filter',
            'title' => 'Filter',
            'icon' => 'fa fa-tint',
            'fields' => $fieldsFilter
        ),
        array(
            'name' => 'behavior',
            'title' => 'Behavior',
            'icon' => 'fa fa-tint',
            'fields' => $fieldsBehavior,
        ),
        array(
            'name' => 'styling',
            'title' => 'Styling result',
            'icon' => 'fa fa-tint',
            'fields' => $fieldsStyling,
        ),
        array(
            'name' => 'styling_form',
            'title' => 'Styling Form',
            'icon' => 'fa fa-tint',
            'fields'    => array(
                array(
                    'type' => 'select',
                    'options' => $optionsText,
                    'id' => 'optionsTextInput',
                    'title' => __('Style Input search', 'advanced_search_form_builder'),
                    'attributes' => array(
                        'style' => 'width: 100%'
                    )
                ),

                array(
                    'type' => 'subheading',
                    'content' => __('Styling wrapper form', 'advanced_search_form_builder'),
                ),

                array(
                    'id' => 'form_styling_group',
                    'type' => 'style',
                    'options' => array(
                        array(
                            "default" => "#efefef",
                            "label" => "Background color",
                            "name" => "bg-color",
                            "type" => "color"
                        ),
                        array(
                            "default" => "1px solid #f7f7f7",
                            "label" => "Border style",
                            "name" => "border",
                            "type" => ""
                        ),
                        array(
                            "default" => "10px 10px 10px 1px",
                            "label" => "Padding",
                            "name" => "padding",
                            "type" => ""
                        ),
                        array(
                            "default" => "10px auto 10px",
                            "label" => "Margin",
                            "name" => "margin",
                            "type" => ""
                        ),
                        array(
                            "default" => "4px",
                            "label" => "Border radius",
                            "name" => "radius",
                            "type" => ""
                        ),
                    )
                ),
                array(
                    'type' => 'subheading',
                    'content' => __('Styling Filter', 'advanced_search_form_builder'),
                ),


                array(
                    'id' => 'filter_column_desktop', // this is must be unique
                    'type' => 'number',
                    'default' => 2,
                    'attributes' => array(
                        'style' => 'width: 100%'
                    ),
                    'title' => __('Number of column DESKTOP', 'advanced_search_form_builder'),
                    'desc' => 'Width window >= 960px',
                ),
                array(
                    'id' => 'filter_column_tablet', // this is must be unique
                    'type' => 'number',
                    'default' => 2,
                    'attributes' => array(
                        'style' => 'width: 100%'
                    ),
                    'title' => __('Number of column TABLET', 'advanced_search_form_builder'),
                    'desc' => 'Width window 481px -> 959px',
                ),
                array(
                    'id' => 'filter_column_mobile', // this is must be unique
                    'type' => 'number',
                    'default' => 1,
                    'attributes' => array(
                        'style' => 'width: 100%'
                    ),
                    'title' => __('Number of column MOBILE', 'advanced_search_form_builder'),
                    'desc' => 'Width window <= 480px',
                ),

                array(
                    'id' => 'filtercolums_styling_group',
                    'type' => 'style',
                    'title' => __('Style columns', 'advanced_search_form_builder'),
                    'options' => array(
                        array(
                            "default" => "#efefef",
                            "label" => "Background color",
                            "name" => "background-color",
                            "type" => "color"
                        ),
                        array(
                            "default" => "1px solid #f7f7f7",
                            "label" => "Border style",
                            "name" => "border",
                            "type" => ""
                        ),
                        array(
                            "default" => "10px 10px 10px 1px",
                            "label" => "Padding",
                            "name" => "padding",
                            "type" => ""
                        ),
                        array(
                            "default" => "10px auto 10px",
                            "label" => "Margin",
                            "name" => "margin",
                            "type" => ""
                        ),
                        array(
                            "default" => "4px",
                            "label" => "Border radius",
                            "name" => "radius",
                            "type" => ""
                        ),
                    )
                ),
            )
        ),
    ),
);

CSFramework_Metabox::instance($options);

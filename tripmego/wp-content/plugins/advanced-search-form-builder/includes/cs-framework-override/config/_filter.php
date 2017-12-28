<?php
global $ASFB_config;

$variableCss = file_get_contents(ASFB_PATH . 'public/css/variable.json');
$variableCss = json_decode($variableCss, true);

$sectionsStyle = array();
foreach ($ASFB_config['form_filter']['type_input'] as $key => $item) {
    $OptionsGlobal = cs_get_option($key . '_styling_item');
    $options = array();
    if(is_array($OptionsGlobal) ) {
        foreach ($OptionsGlobal as $_item) {
            $options[$_item['css_file']['url']] = $_item['text_label'];
        }
    }

    $sectionsStyle[] = array(
        'id'      => $key . '_styling',
        'title'     => $item,
        'type'    => 'select',
        'options' => $options,
        'dependency' => array(
            'input_type',
            '==',
            $key
        ),
    );
}

$swLabelOptionsGlobal = cs_get_option('swatch_label_styling_item');
$optionsSwLabel = array();
if(is_array($swLabelOptionsGlobal) ) {
    foreach ($swLabelOptionsGlobal as $item) {
        $optionsSwLabel[$item['css_file']['url']] = $item['text_label'];
    }
}

global $ASFB_config;

$fieldsFilter[] = array(
//-----------------------------------------------------------------
    'id' => 'filter_post_type_source',
    'type' => 'select',
    'title' => __('Post type source', 'advanced_search_form_builder'),
    'class' => 'chosen',
    'options' => $postTypeOptions,
    'attributes' => array(
        'multiple' => 'multiple',
        'style' => 'width: 100%;'
    ),
);
$fieldsFilter[] = array(
    'type' => 'subheading',
    'content' => __('Post field', 'advanced_search_form_builder'),
);

$fieldsFilter[] = array(
    'id' => 'pf_title',
    'type' => 'switcher',
    'title' => __('Enable search in post title', 'advanced_search_form_builder'),
    'default' => true
);

$fieldsFilter[] = array(
    'id' => 'pf_content',
    'type' => 'switcher',
    'title' => __('Enable search in post content', 'advanced_search_form_builder'),
    'default' => true
);

$fieldsFilter[] = array(
    'id' => 'pf_excerpt',
    'type' => 'switcher',
    'title' => __('Enable search in post excerpt', 'advanced_search_form_builder'),
    'default' => true
);

$fieldsFilter[] = array(
    'type' => 'subheading',
    'content' => __('Filter taxonomy', 'advanced_search_form_builder'),
);

$fieldsFilter[] = array(
    'id' => 'tax_relation',
    'type' => 'radio',
    'title' => __('Relation taxonomy', 'advanced_search_form_builder'),
    'options' => array(
        'AND' => 'AND',
        'OR' => 'OR',
    ),
);

$fields = array();
$fields[] = array(
    'id' => 'tax_label',
    'type' => 'text',
    'title' => __('Label', 'advanced_search_form_builder'),
);
$fields[] = array(
    'id' => 'input_type',
    'type' => 'select',
    'title' => __('Type input', 'advanced_search_form_builder'),
    'options' => $ASFB_config['form_filter']['type_input'],
    'select2' => true,
    'attributes' => array(
        'style' => 'width: 100%;'
    ),
);

$fields = array_merge($fields, $sectionsStyle);

$fields[] = array(
    'id' => 'tax_taxonomy',
    'type' => 'select',
    'title' => __('Taxonomy', 'advanced_search_form_builder'),
    'options' => $taxonomyOptions,
    'attributes' => array(
        'style' => 'width: 100%;',
        'data-changedata' => '1',
        'data-target' => 'terms',
        'data-typeaction' => 'syncterms',
        'data-wrapper' => '.cs-group-content'
    ),
    'dependency' => array(
        'input_type',
        '!=',
        'swatch_color'
    ),
);

$fields[] = array(
    'id' => 'tax_terms',
    'type' => 'select',
    'title' => __('Terms', 'advanced_search_form_builder'),
    'options' => array(),
    'class' => 'chosen',
    'attributes' => array(
        'style' => 'width: 100%;',
        'multiple' => 'multiple',
        'data-id' => 'terms',
    ),
    'dependency' => array(
        'input_type',
        '!=',
        'swatch_color'
    ),
);

$fields[] = array(
    'id' => 'tax_multiple_choice',
    'type' => 'switcher',
    'title' => __('Multiple select', 'advanced_search_form_builder'),
    'default' => false,
    'dependency' => array(
        'input_type',
        'any',
        'swatch_text'
    ),
);
$fields[] = array(
    'id' => 'tax_swatch_color',
    'type' => 'swatch_color',
    'title' => __('Data Color', 'advanced_search_form_builder'),
    'default' => true,
    'dependency' => array(
        'input_type',
        'any',
        'swatch_color'
    ),
);


$fieldsFilter[] = array(
    'id' => 'tax',
    'type' => 'group',
    'button_title' => __('Add new', 'advanced_search_form_builder'),
    'accordion_title' => 'tax_label',
    'title' => 'Taxonomy',
    'fields' => $fields
);


$fieldsFilter[] = array(
    'type' => 'subheading',
    'content' => __('Custom field taxonomy', 'advanced_search_form_builder'),
);


$fieldsFilter[] = array(
    'id' => 'cf_relation',
    'type' => 'radio',
    'title' => __('Relation custom field', 'advanced_search_form_builder'),
    'desc' => __('The conditions are greater than 3, then the relation will be turned to AND automatically', 'advanced_search_form_builder'),
    'options' => array(
        'AND' => 'AND',
        'OR' => 'OR',
    ),
);

$fields = array();

$fields[] = array(
    'id' => 'cf_label',
    'type' => 'text',
    'title' => __('Label', 'advanced_search_form_builder'),
);
$fields[] = array(
    'id' => 'input_type',
    'type' => 'select',
    'title' => __('Type input', 'advanced_search_form_builder'),
    'options' => $ASFB_config['form_filter']['type_input_cf'],
    'attributes' => array(
        'style' => 'width: 100%;'
    ),
);

$fields = array_merge($fields, $sectionsStyle);

$fields[] = array(
    'id' => 'cf_key_name',
    'type' => 'text',
    'title' => __('Custom field name', 'advanced_search_form_builder'),
    'options' => array(),
    'attributes' => array(
        'style' => 'width: 100%;',
        'data-searchajax' => '1',
        'autocomplete' => "off",
        'data-type' => 'custom_fields',
    ),
);
$fields[] = array(
    'id' => 'cf_value',
    'type' => 'textarea',
    'title' => __('Value', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100%;',
    ),
    'dependency' => array(
        'input_type',
        'any',
        'select,checkbox,radio,swatcher_color, swatcher_text'
    ),
);
$fields[] = array(
    'id' => 'cf_value_range',
    'type' => 'range_number',
    'title' => __('Range value', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100px',
    ),
    'dependency' => array('input_type', '==', 'number_range'),
);
$fields[] = array(
    'id' => 'cf_value_datepicker_min',
    'type' => 'number',
    'title' => __('Min date', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100%',
    ),
    'dependency' => array('input_type', '==', 'datetime'),
);
$fields[] = array(
    'id' => 'cf_value_datepicker_max',
    'type' => 'number',
    'title' => __('Max date', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100%',
    ),
    'dependency' => array('input_type', '==', 'datetime'),
);
$fields[] = array(
    'id' => 'cf_compare',
    'type' => 'select',
    'title' => __('Compare', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100%',
    ),
    'options' => $ASFB_config['form_filter']['compare_operator'],
    'dependency' => array(
        'input_type',
        '!=',
        'number_range'
    ),
);
$fields[] = array(
    'id' => 'unit',
    'type' => 'text',
    'title' => __('Unit', 'advanced_search_form_builder'),
    'attributes' => array(
        'style' => 'width: 100%',
    ),
    'default' => '$',
    'dependency' => array(
        'input_type',
        '==',
        'number_range'
    ),
);
$fields[] = array(
    'id' => 'cf_multiple_choice',
    'type' => 'switcher',
    'title' => __('Multiple select', 'advanced_search_form_builder'),
    'default' => false,
    'dependency' => array(
        'input_type',
        'any',
        'swatch_color, swatch_text'
    ),
);

$fieldsFilter[] = array(
    'id' => 'cf',
    'type' => 'group',
    'button_title' => __('Add new', 'advanced_search_form_builder'),
    'accordion_title' => 'cf_label',
    'title' => 'Custom field',
    'fields' => $fields
);
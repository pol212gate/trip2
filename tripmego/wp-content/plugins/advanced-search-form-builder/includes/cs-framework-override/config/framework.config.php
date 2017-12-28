<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings           = array(
  'menu_title'      => 'ASFB Settings',
  'menu_type'       => 'menu', // menu, submenu, options, theme, etc.
  'menu_slug'       => 'ASFB-settings',
  'ajax_save'       => false,
  'show_reset_all'  => false,
  'framework_title' => 'ASFB Settings <small>by CatPlugins</small>',
  'menu_position'   => 6,
);

// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options        = array();


// ----------------------------------------
// a option section for options overview  -
// ----------------------------------------
global $ASFB_config;

$variableCss = file_get_contents(ASFB_PATH . 'public/css/variable.json');
$variableCss = json_decode($variableCss, true);

$optionsForm = array(
    '' => __('Select a form', 'advanced_search_form_builder'),
);

$form = get_posts(
    array(
        'post_type'=> 'asfb_post',
        'post_status' => 'publish',
        'posts_per_page' => -1
    )
);

if ($form) {
    foreach ($form as $f) {
        $optionsForm[$f->ID] = $f->post_title;
    }
}

$sections = array();
foreach ($variableCss as $key => $item) {

    $sections[] = array(
        'name'      => $item['key'] . '_styling',
        'title'     => $item['title'],
        'fields'    => array(
            array(
                'id' => $item['key'] . '_styling_item',
                'type' => 'group',
                'button_title' => __('Add new', 'advanced_search_form_builder'),
                'title' => __('Templates', 'advanced_search_form_builder'),
                'desc' => __($item['title'], 'advanced_search_form_builder'),
                'fields' => array(
                    array(
                        'id' => 'text_label',
                        'title' => '',
                        'type' => 'text',
                    ),
                    array(
                        'id' => 'styling_group',
                        'type' => 'style',
                        'options' => $item['data'],
                        'title' => '',
                    ),
                )
            )
        )
    );
}

$options[] = array(
    'name'     => 'asfb_styling',
    'title'    => 'Styling input',
    'icon'     => 'fa fa-plus-circle',
    'sections' => $sections
);

$options[] = array(
    'name'     => 'asfb_tool',
    'title'    => 'Tool',
    'icon'     => 'fa fa-inbox',
    'fields' => array(
        array(
            'id' => 'asfb_backup',
            'type' => 'backup',
            'title' => __('Backup data', 'advanced_search_form_builder'),
        ),
    )
);

// ------------------------------
// a option section with tabs   -
// ------------------------------
$options[] = array(
  'name'   => 'seperator_1',
  'title'  => 'END',
  'icon'   => 'fa fa-bookmark'
);

CSFramework::instance( $settings, $options );

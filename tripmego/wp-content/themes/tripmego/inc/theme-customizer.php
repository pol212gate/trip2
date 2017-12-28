<?php

function mytheme_customize_register( $wp_customize ) {
   //All our sections, settings, and controls will be added here

$wp_customize->add_section( 'mytheme_new_section_name' , array(
    'title'      => __( 'Colors', 'mytheme' ),
    'priority'   => 30,
) );
//--------------------------Header color-----------------------------
$wp_customize->add_setting( 'header_color' , array(
    'default'   => '#000000',
    'transport' => 'refresh',
) );

$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'header_color', array(
	'label'      => __( 'Header Color', 'mytheme' ),
	'section'    => 'mytheme_new_section_name',
	'settings'   => 'header_color',
) ) );
//--------------------------------------------------------------------
//-------------------------- Body color-----------------------------
$wp_customize->add_setting( 'body_color' , array(
    'default'   => '#ffffff',
    'transport' => 'refresh',
) );

$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'body_color', array(
	'label'      => __( 'Body Background Color', 'mytheme' ),
	'section'    => 'mytheme_new_section_name',
	'settings'   => 'body_color',
) ) );
//-------------------------------------------------------------------

//-------------------------- Footer Callout-----------------------------
$wp_customize->add_section( 'mytheme_footer_callout' , array(
    'title'      => __( 'Footer Callout', 'mytheme' ),
    'priority'   => 100,
) );

//-------------------------- Footer Callout select-----------------------------
$wp_customize->add_setting( 'footer_callout_select' , array(
    'default'   => 'No'

) );

$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_callout_select', array(
	'label'      => __( 'Show Footer Callout?', 'mytheme' ),
	'section'    => 'mytheme_footer_callout',
	'settings'   => 'footer_callout_select',
	    'type'		=> 'select',
	    'choices' => array('No' => 'No' , 'Yes' => 'Yes')
) ) );

$wp_customize->add_setting( 'footer_callout_text' , array(
    'default'   => 'Footer Callout',
) );

$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_callout_text', array(
	'label'      => __( 'Title', 'mytheme' ),
	'section'    => 'mytheme_footer_callout',
	'settings'   => 'footer_callout_text',
) ) );

//-------------------------- Footer Callout text area-----------------------------
$wp_customize->add_setting( 'footer_callout_textarea' , array(
    'default'   => 'Footer Callout Textarea'

) );

$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_callout_textarea', array(
	'label'      => __( 'Description', 'mytheme' ),
	'section'    => 'mytheme_footer_callout',
	'settings'   => 'footer_callout_textarea',
	    'type'		=> 'textarea'
) ) );

//-------------------------- Footer Callout link-----------------------------
$wp_customize->add_setting( 'footer_callout_link' );

$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_callout_link', array(
	'label'      => __( 'Description', 'mytheme' ),
	'section'    => 'mytheme_footer_callout',
	'settings'   => 'footer_callout_link',
	    'type'		=> 'dropdown-pages'
) ) );

//-------------------------- Footer Callout image-----------------------------
$wp_customize->add_setting( 'footer_callout_image' );

$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'footer_callout_image', array(
	'label'      => __( 'Image', 'mytheme' ),
	'section'    => 'mytheme_footer_callout',
	'settings'   => 'footer_callout_image',
	'width'		 => '100',
	'height'  	 => '100'
) ) );



}
add_action( 'customize_register', 'mytheme_customize_register' );


function mytheme_customize_css()
{
    ?>
         <style type="text/css">
             h1 a:link, h1 a:visited { color: <?php echo get_theme_mod('header_color', '#000000'); ?>; }
             body { background-color:<?PHP echo get_theme_mod('body_color' ,'#ffffff'); ?> }
         </style>
    <?php
}
add_action( 'wp_head', 'mytheme_customize_css');

?>
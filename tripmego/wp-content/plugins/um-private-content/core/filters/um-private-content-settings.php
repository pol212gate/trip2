<?php

/***
 ***	@extend settings
 ***/
add_filter("redux/options/um_options/sections", 'um_private_content_config', 11 );
function um_private_content_config( $sections ){
    $sections[] = array(

        'subsection' => true,
        'title'      => __( 'Private Content', 'um-private-content'),
        'fields'     => array(
            array(
                'id'       		=> 'private_content_generate',
                'type'     		=> 'raw',
                'title'   		=> __( 'Generate pages', 'um-private-content' ),
                'desc'   		=> __( 'Generate pages for already existing users', 'um-private-content' ),
                'content'       => "<input type=\"button\" id=\"um_options_private_content_generate\" class=\"button\" value=\"" . __( 'Generate pages for existing users', 'um-private-content' ) . "\" /><div class='clear'></div><div class='um_setting_ajax_button_response'></div>",
            ),
            array(
                'id'       		=> 'show_private_content_on_profile',
                'type'     		=> 'switch',
                'title'   		=> __( 'Show Private Content tab at User\'s Profile','um-private-content' ),
                'default'		=> 1,
            ),
            array(
                'id'       => 'tab_private_content_title',
                'type'     => 'text',
                'title'    => __( 'Private Content Tab Title','um-private-content' ),
                'default'  => __( 'Private Content','um-private-content' ),
                'required' => array( 'show_private_content_on_profile', '=', 1 ),
                'desc' 	   => __( 'This is the title of the tab for show user\'s private content','um-private-content' ),
            ),
            array(
                'id'       => 'tab_private_content_icon',
                'type'     => 'text',
                'title'    => __( 'Private Content Tab Icon','um-private-content' ),
                'default'  => 'um-faicon-eye-slash',
                'required' => array( 'show_private_content_on_profile', '=', 1 ),
                'desc' 	   => __( 'This is the icon of the tab for show user\'s private content','um-private-content' ),
                'class'    => 'private_content_icon'
            ),
        )

    );

    return $sections;
}
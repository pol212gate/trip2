<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin (PRO)
* Version                 : 2.1.2
* File                    : views/settings/views-backend-settings-general.php
* File Version            : 1.0.4
* Created / Last Modified : 06 December 2015
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : Back end general settings views class.
*/

    if (!class_exists('DOPBSPViewsBackEndSettingsGeneral')){
        class DOPBSPViewsBackEndSettingsGeneral extends DOPBSPViewsBackEndSettings{
            /*
             * Constructor
             */
            function __construct(){
            }
            
            /*
             * Returns general settings template.
             * 
             * @param args (array): function arguments
             * 
             * @return general settings HTML
             */
            function template($args = array()){
                global $DOPBSP;
                
                $id = 0;
                $settings_general = $DOPBSP->classes->backend_settings->values($id,  
                                                                              'general');
?>
                <div class="dopbsp-inputs-header dopbsp-hide">
                    <h3><?php echo $DOPBSP->text('SETTINGS_GENERAL_TITLE'); ?></h3>
                    <a href="javascript:DOPBSPBackEnd.toggleInputs('general-settings')" id="DOPBSP-inputs-button-general-settings" class="dopbsp-button"></a>
                </div>
                <div id="DOPBSP-inputs-general-settings" class="dopbsp-inputs-wrapper dopbsp-last">
<?php 
                /*
                 * Google Map API Key.
                 */
                $this->displayTextInput(array('id' => 'google_map_api_key',
                                              'label' => $DOPBSP->text('SETTINGS_GENERAL_GOOGLE_MAP_API_KEY'),
                                              'value' => $settings_general->google_map_api_key,
                                              'settings_id' => $id,
                                              'settings_type' => 'general',
                                              'help' => $DOPBSP->text('SETTINGS_GENERAL_GOOGLE_MAP_API_KEY_HELP')));
?>
                </div>
<?php
            }
        }
    }
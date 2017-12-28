<?php
global $ASFB_config;

$dataForm = get_post_meta( $formBuilder->ID, $ASFB_config['default_setting']['key_post_meta_form'], true);
$targetAction = (isset($dataForm['endpoint_other']) ? $dataForm['endpoint_other'] : '');
if ($targetAction == '') {
    $targetAction = (isset($dataForm['page_result']) ? $dataForm['page_result'] : '');

    if ($targetAction == '') {
        $targetAction = get_option($ASFB_config['default_setting']['key_page_result']);
    }

    $targetAction = get_permalink($targetAction);
}

$elementForm = new ASFB_element_form();

$styleInline  ='' ;
if ( isset($dataForm['form_styling_group']['bg-color']) )
    $styleInline .= 'background-color: ' . $dataForm['form_styling_group']['bg-color'] . ';';
if ( isset($dataForm['form_styling_group']['border']) )
    $styleInline .= 'border: ' . $dataForm['form_styling_group']['border'] . ';';
if ( isset($dataForm['form_styling_group']['padding']) )
    $styleInline .= 'padding: ' . $dataForm['form_styling_group']['padding'] . ';';
if ( isset($dataForm['form_styling_group']['margin']) )
    $styleInline .= 'margin: ' . $dataForm['form_styling_group']['margin'] . ';';
if ( isset($dataForm['form_styling_group']['radius']) )
    $styleInline .= 'radius: ' . $dataForm['form_styling_group']['radius'] . ';';


$eventAutoComplete = ( $dataForm['en_autocomplete'] == 1 ? 'oninput="asfbAutoSearch(event)" onclick="asfbAutoSearch(event)"' : '');
?>
<form style="<?php echo $styleInline ?>"
      action="<?php echo $targetAction ?>"
      class="asfbFormWrapper asfb_<?php echo $formBuilder->ID ?>">



    <?php
    /*
     *
     */

    do_action('asfb_before_from_search', $dataForm);

    ?>


    <span class="jsonData" style="display: none">
        <?php
            echo json_encode(array(
                'autocom_tax_title' => $dataForm['autocom_tax_title'],
                'filter_post_type_source' => $dataForm['filter_post_type_source'],
                'include_live_result' => $dataForm['include_live_result']
            ));
        ?>
    </span>
    <div class="asfbInputSearch">

        <?php if($dataForm['en_autocomplete'] == true) :  ?>
            <input type="text" class="asfbAt
                            asfbInput
                            asfbTextInput
                            <?php echo getClassByFileName($dataForm['optionsTextInput']) ?>"
                            value=""
            >
        <?php endif ?>

        <input class=" <?php echo ( $dataForm['en_autocomplete'] == true ? 'inputAutoComplete' : '') ?>
                        asfbInput
                        asfbTextInput
                        <?php echo getClassByFileName($dataForm['optionsTextInput']) ?>
                     "
                <?php echo ( $dataForm['en_autocomplete'] == true ? 'style="background-color: transparent;"' : '') ?>

                autocomplete="off"
                <?php echo $eventAutoComplete ?>
                data-auto_search_character="<?php echo $dataForm['auto_search_character'] ?>"
                data-auto_search_time_delay="<?php echo $dataForm['auto_search_time_delay'] ?>"
                type="text" placeholder="Search for ..." name="q" value="<?php echo ASFB_request::getQuery('q') ?>">


        <div class="asfbSearchResult" style="display:none;">
            <h6>Suggestion result</h6>
            <div class="asfbSuggestion"><ul class="asfbList"></ul></div>

            <?php if ($dataForm['include_live_result'] == 1) :  ?>
                <h6>Search result</h6>
                <div class="asfbWrapResult asfbWrapResultAjax">

                </div>
            <?php endif; ?>
        </div>
        
    </div>

    <div class="asfbFilterArea
            layoutDesk_<?php echo $dataForm['filter_column_desktop'] ?>
            layoutMobile_<?php echo $dataForm['filter_column_mobile'] ?>
            layoutTablet_<?php echo $dataForm['filter_column_tablet'] ?>
        ">
        <div class="asfb_row">
            <?php
            if (isset($dataForm['tax']) && count($dataForm['tax']) > 0) {
                foreach ($dataForm['tax'] as $tax) :
                    $classElement = getClassByFileName($tax[$tax['input_type'] .'_styling']);

                    echo '<div class="asfb_itemCol">';

                    $dataElement = array(
                        "type"     => "taxonomy",
                        "label"    => ( isset($tax['tax_label']) ? $tax['tax_label'] : '' ),
                        "name"     => ( isset($tax['tax_taxonomy']) ? $tax['tax_taxonomy'] : '' ),
                        "options"  => ( isset($tax['tax_terms']) ? $tax['tax_terms'] : '' ),
                        "multiple" => ( isset($tax['tax_multiple_choice']) ? $tax['tax_multiple_choice'] : '' ),
                        "min"      => ( isset($tax['tax_value_range']['min']) ? $tax['tax_value_range']['min'] : '' ),
                        "max"      => ( isset($tax['tax_value_range']['max']) ? $tax['tax_value_range']['max'] : '' ),
                        "step"     => ( isset($tax['tax_value_range']['step']) ? $tax['tax_value_range']['step'] : '' ),
                        "search_onchange" => ( $dataForm["auto_search_on_change"] == 1 ? true : false ),
                        "class" => $classElement
                    );

                    do_action('asfb_before_render_taxonomy_element', $tax['input_type'], $dataElement);

                    switch ($tax['input_type']) {
                        case 'select':
                            echo $elementForm->select($dataElement);
                            break;
                        case 'checkbox':
                            echo $elementForm->checkbox($dataElement);
                            break;
                        case 'radio':
                            echo $elementForm->radio($dataElement);
                            break;
                        case 'number_range':
                            echo $elementForm->rangeNumber($dataElement);
                            break;
                        case 'swatch_color':
                            $dataElement['name'] = isset($tax['tax_swatch_color']['taxonomy']) ? $tax['tax_swatch_color']['taxonomy'] : '';
                            $dataElement['colors'] = $tax['tax_swatch_color']['color'];
                            echo $elementForm->swatchColor($dataElement);
                            break;
                        case 'swatch_text':
                            echo $elementForm->swatchText($dataElement);
                            break;
                        case 'textinput':
                            echo $elementForm->textinput($dataElement);
                            break;

                        default:
                            echo $tax['input_type'];
                            break;
                    }

                    do_action('asfb_after_render_taxonomy_element', $tax['input_type'], $dataElement);

                    echo '</div>';
                endforeach;
            }
            ?>

            <?php
            if (isset($dataForm['cf']) && count($dataForm['cf']) > 0) {
                foreach ($dataForm['cf'] as $cf) :
                    $classElement = getClassByFileName($cf[$cf['input_type'] .'_styling']);
                    echo '<div class="asfb_itemCol">';
                        $dataElement = array(
                            "type"           => 'custom_field',
                            "label"          => ( isset($cf['cf_label']) ? $cf['cf_label'] : '' ),
                            "name"           => ( isset($cf['cf_key_name']) ? $cf['cf_key_name'] : '' ),
                            "options"        => ( isset($cf['cf_value']) ? $cf['cf_value'] : '' ),
                            "multiple"       => ( isset($cf['cf_multiple_choice']) ? $cf['cf_multiple_choice'] : '' ),
                            "min"            => ( isset($cf['cf_value_range']['min']) ? $cf['cf_value_range']['min'] : '' ),
                            "max"            => ( isset($cf['cf_value_range']['max']) ? $cf['cf_value_range']['max'] : '' ),
                            "step"           => ( isset($cf['cf_value_range']['step']) ? $cf['cf_value_range']['step'] : '' ),
                            "datepicker_min" => ( isset($cf['cf_value_datepicker_min']) ? $cf['cf_value_datepicker_min'] : '' ),
                            "datepicker_max" => ( isset($cf['cf_value_datepicker_max']) ? $cf['cf_value_datepicker_max'] : '' ),
                            "compare"        => ( isset($cf['cf_compare']) ? $cf['cf_compare'] : '' ),
                            "unit"           => ( isset($cf['unit']) ? $cf['unit'] : '' ),
                            "search_onchange" => ( $dataForm["auto_search_on_change"] == 1 ? true : false ),
                            "class" => $classElement
                        );

                        do_action('asfb_before_render_custom_field_element', $cf['input_type'], $dataElement);

                        switch ($cf['input_type']) {
                            case 'select':
                                echo $elementForm->select($dataElement);
                                break;
                            case 'checkbox':
                                echo $elementForm->checkbox($dataElement);
                                break;
                            case 'radio':

                                echo $elementForm->radio($dataElement);
                                break;
                            case 'number_range':
                                echo $elementForm->rangeNumber($dataElement);
                                break;
                            case 'swatch_color':
                                echo $elementForm->swatchColor($dataElement);
                                break;
                            case 'swatch_text':
                                echo $elementForm->swatchText($dataElement);
                                break;
                            case 'textinput':
                                echo $elementForm->textinput($dataElement);
                                break;

                            default:
                                echo $cf['input_type'];
                                break;
                        }

                        do_action('asfb_after_render_custom_field_element', $cf['input_type'], $dataElement);

                    echo '</div>';
                endforeach;
            }
            ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php if ( $dataForm['hide_submit'] == false ) : ?>
        <div class="footerAction">
            <button class="asfbSubmitForm" type="submit">Submit</button>
        </div>
    <?php endif ?>

    <!-- REQUIRE -->
    <input type="hidden" name="form_id" value="<?php echo $options['id'] ?>" >

    <?php
    /*
     *
     */

    do_action('asfb_after_from_search', $dataForm);

    ?>
</form>

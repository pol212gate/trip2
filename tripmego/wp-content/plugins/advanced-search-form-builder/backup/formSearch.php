<?php

$dataForm = get_post_meta( $formBuilder->ID, $ASFB_config['default_setting']['key_post_meta_form'], true);
$targetAction = (isset($dataForm['page_result']) ? $dataForm['page_result'] : '');
if ($targetAction == '') {
    $targetAction = get_option($ASFB_config['default_setting']['key_page_result']);
    $targetAction = get_permalink($targetAction);
}

$formBuilderElement = new ASFB_formSearchElement();

?>

<form action="<?php echo $targetAction ?>">
    <input type="text" name="q" value="<?php echo ASFB_request::getQuery('q') ?>">
    <input type="hidden" name="form_id" value="<?php echo $options['id'] ?>" >
    <div class="filterArea">
        <?php
        if (isset($dataForm['tax']) && count($dataForm['tax']) > 0) {
            foreach ($dataForm['tax'] as $tax) { 
                $dataElement = array(
                    "type"     => "taxonomy",
                    "label"    => ( isset($tax['tax_label']) ? $tax['tax_label'] : '' ),
                    "name"     => ( isset($tax['tax_taxonomy']) ? $tax['tax_taxonomy'] : '' ),
                    "options"  => ( isset($tax['tax_terms']) ? $tax['tax_terms'] : '' ),
                    "multiple" => ( isset($tax['tax_multiple_choice']) ? $tax['tax_multiple_choice'] : '' ),
                    "min"      => ( isset($tax['tax_value_range']['min']) ? $tax['tax_value_range']['min'] : '' ),
                    "max"      => ( isset($tax['tax_value_range']['max']) ? $tax['tax_value_range']['max'] : '' ),
                    "step"     => ( isset($tax['tax_value_range']['step']) ? $tax['tax_value_range']['step'] : '' ),
                );

                if ($tax['tax_input_type'] == 'select') :
                    echo $formBuilderElement->select($dataElement);
                elseif ($tax['tax_input_type'] == 'checkbox') :
                    echo $formBuilderElement->checkbox($dataElement);
                elseif ($tax['tax_input_type'] == 'radio') :
                    echo $formBuilderElement->radio($dataElement);
                elseif ($tax['tax_input_type'] == 'textBox') :
                    echo $formBuilderElement->textBox($dataElement);
                elseif ($tax['tax_input_type'] == 'range_number') :
                    echo $formBuilderElement->rangeNumber($dataElement);
                elseif ($tax['tax_input_type'] == 'datetime') :
                    echo $formBuilderElement->datePicker($dataElement);
                elseif ($tax['tax_input_type'] == 'swatcher_color') :
                    $dataElement['options'] = '';
                    $dataElement['colors'] = $tax['tax_swatch_color']['color'];
                    echo $formBuilderElement->swatchColor($dataElement);
                elseif ($tax['tax_input_type'] == 'swatcher_text') :
                    echo $formBuilderElement->swatchText($dataElement);
                endif;
            }
        }
        ?>

        <?php
        if (isset($dataForm['cf']) && count($dataForm['cf']) > 0) {
            foreach ($dataForm['cf'] as $cf) {
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

                );

                if ($cf['cf_input_type'] == 'select') :
                    echo $formBuilderElement->select($dataElement);
                elseif ($cf['cf_input_type'] == 'checkbox') :
                    echo $formBuilderElement->checkbox($dataElement);
                elseif ($cf['cf_input_type'] == 'radio') :
                    echo $formBuilderElement->radio($dataElement);
                elseif ($cf['cf_input_type'] == 'textBox') :
                    echo $formBuilderElement->textBox($dataElement);
                elseif ($cf['cf_input_type'] == 'range_number') :
                    echo $formBuilderElement->rangeNumber($dataElement);
                elseif ($cf['cf_input_type'] == 'datetime') :
                    echo $formBuilderElement->datePicker($dataElement);
                elseif ($cf['cf_input_type'] == 'swatcher_color') :
                    echo $formBuilderElement->swatchColor($dataElement);
                elseif ($cf['cf_input_type'] == 'swatcher_text') :
                    echo $formBuilderElement->swatchText($dataElement);
                endif;
            }
        }
        ?>
    </div>

    <div class="footerAction">
        <button type="submit">Submit</button>
    </div>
</form>

<?php // print_r($dataForm) ?>

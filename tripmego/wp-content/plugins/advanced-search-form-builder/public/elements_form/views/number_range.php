<?php
    $minValue = $this->schemaElement['min'];
    if (isset($this->getValue()['min'])) {
        $minValue = $this->getValue()['min'];
    }

    $maxValue = $this->schemaElement['max'];
    if (isset($this->getValue()['max'])) {
        $maxValue = $this->getValue()['max'];
    }

?>

<div class="asfbFilterCol asfbNumberRange <?php echo $this->get('class'); ?>">
    <label for="<?php echo $this->getName() ?>">
        <div class="widget__title">
            <?php echo $this->get('label'); ?>
        </div>
    </label>
    <div class="asfb_check-slide-01__element">

        <div class="slider-range asfb_slider-range" id="number_range_<?php echo $this->get('type') . $this->getName() ?>" data-init="slider-range"
             data-min="<?php echo $this->get('min') ?>"
             data-max="<?php echo $this->get('max') ?>"
             data-unit="<?php echo $this->get('unit') ?>"
             data-min-value="<?php echo $minValue ?>"
             data-max-value="<?php echo $maxValue ?>"
             data-targetmin="number_range_<?php echo $this->get('type') . $this->getName() ?>_min"
             data-targetmax="number_range_<?php echo $this->get('type') . $this->getName() ?>_max"
             data-step="<?php echo $this->get('step') ?>">
            <span class="slide-rang-text text1"></span><span class="slide-rang-text text2"></span>
        </div>

        <input type="hidden"
               id="number_range_<?php echo $this->get('type') . $this->getName() ?>_min"
               name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>][min]" value="<?php echo $minValue ?>">

        <input type="hidden"
               id="number_range_<?php echo $this->get('type') . $this->getName() ?>_max"
               name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>][max]" value="<?php echo $maxValue ?>">
    </div>
</div>
<div class="asfbFilterCol asfbSwatchText">
    <label for="<?php echo $this->getName() ?>">
        <div class="widget__title">
            <?php echo $this->get('label'); ?>
        </div>
    </label>
    <?php if ( $this->hasOptions() ) : ?>
        <?php foreach ($this->getOptions() as $key => $value) : ?>
            <label for="swatch_text_<?php echo $this->get('type') ?>_<?php echo $this->getName() ?>_<?php echo $value['value'] ?>"
                   class="asfbSwatchLabel <?php echo $this->get('class'); ?>">
                <input
                    <?php echo $this->checkStatus($value['value'], 'checked') ?>
                    <?php echo $this->eventOnchange() ?>
                        value="<?php echo $value['value'] ?>"
                        name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>][]"
                        type="checkbox"
                        id="swatch_text_<?php echo $this->get('type') ?>_<?php echo $this->getName() ?>_<?php echo $value['value'] ?>"
                />


                <span class="textLabel">
                    <?php echo $value['label'] ?>
                    <?php if ( $value['count'] > 0 ) : ?>
                        <span class="">(<?php echo $value['count'] ?>)</span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

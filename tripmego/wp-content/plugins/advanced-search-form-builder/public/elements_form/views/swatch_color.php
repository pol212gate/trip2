<div class="asfbFilterCol asfbSelect">
    <label for="<?php echo $this->getName() ?>">
        <div class="widget__title">
            <?php echo $this->get('label'); ?>
        </div>
    </label>

    <span class="asfbWrapInput">
		<?php if ( $this->hasOptions() ) : ?>
            <?php foreach ($this->getOptions() as $key => $value) : ?>
                <label for="swatch_color_<?php echo $value['value'] ?>" class="asfbSwatchColor hiddenLabel <?php echo $this->get('class'); ?>">
                    <input
                        <?php echo $this->checkStatus($value['value'], 'checked') ?>
                        <?php echo $this->eventOnchange() ?>
                            value="<?php echo $value['value'] ?>"
                            name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>][]"
                            class=""
                            id="swatch_color_<?php echo $value['value'] ?>"
                            type="checkbox"
                    />
                    <span class="iconFake" style="background-color: <?php echo $value['color']; ?>"><span class="fa fa-check"></span></span>
                    <span class="textLabel">
                        <?php echo $value['label'] ?>

                        <?php if ( $value['count'] > 0 ) : ?>
                            <span class="count">(<?php echo $value['count'] ?>)</span>
                        <?php endif; ?>
                    </span>
                </label>
            <?php endforeach; ?>
        <?php endif; ?>
	</span>
</div>

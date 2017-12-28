<div class="asfbFilterCol asfbTextinput <?php echo $this->get('class'); ?>">
    <label for="<?php echo $this->getName() ?>">
        <div class="widget__title">
            <?php echo $this->get('label'); ?>
        </div>
    </label>

    <?php if ( $this->hasOptions() ) : ?>
        <span class="asfbWrapInput">
            <input
                    class="asfbTextbox"
                    type="text"
                    name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>]"
                    value="<?php echo $this->getValue() ?>"
            >
		</span>
    <?php endif; ?>
</div>

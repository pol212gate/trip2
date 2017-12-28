<div class="asfbFilterCol asfbSelect <?php echo $this->get('class'); ?>">
	<label for="<?php echo $this->getName() ?>">
		<div class="widget__title">
			<?php echo $this->get('label'); ?>
		</div>
	</label>
	<span class="asfbWrapInput asfbSelectSelect2 <?php echo $this->get('class'); ?>">
		<select
            <?php echo $this->eventOnchange() ?>
                data-dropdowncssclass="<?php echo $this->get('class'); ?>"
                class="js-select asfbSelectSelect2 <?php echo $this->get('class'); ?>"
                name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>]"
                id="<?php echo $this->getName() ?>"
        >
            <option value=""><?php echo __('All', 'advanced_search_form_builder') ?></option>

            <?php if ( $this->hasOptions() ) : $__options = $this->getOptions(); ?>
                <?php if ( is_array($__options) ) : ?>
                
                <?php foreach ($__options as $key => $value) : ?>
                    <option <?php echo $this->checkStatus($value['value'], 'selected') ?>  value="<?php echo $value['value'] ?>"><?php echo $value['label'] ?></option>
                <?php endforeach; ?>

                <?php endif; ?>
            <?php endif; ?>

        </select>
	</span>
</div>
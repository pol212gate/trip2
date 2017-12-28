<div class="asfbFilterCol asfbRadio <?php echo $this->get('class'); ?>">
	<label for="<?php echo $this->getName() ?>">
		<div class="widget__title">
			<?php echo $this->get('label'); ?>
		</div>
	</label>

	<?php if ( $this->hasOptions() ) : ?>
		<span class="asfbWrapInput">
			<?php foreach ($this->getOptions() as $key => $value) : ?>
                <?php $uniqId = implode('_', array('radio', $this->get('type'), $this->getName(), $value['value'])); ?>

                <label for="<?php echo $uniqId ?>"
                       class="radioCustomStyle <?php echo $this->get('class'); ?>">
                    <input
                        <?php echo $this->checkStatus($value['value'], 'checked') ?>
                        <?php echo $this->eventOnchange() ?>
                        type="radio"
                        id="<?php echo $uniqId ?>"
                        value="<?php echo $value['value'] ?>"
                        name="<?php echo $this->get('type'); ?>[<?php echo $this->getName() ?>]"
                    >
                    <span class="iconFake"><span class="fa fa-check"></span></span>
                    <span class="textLabel">
                        <?php echo $value['label'] ?>

                        <?php if ( $value['count'] > 0 ) : ?>
                            <span class="count">(<?php echo $value['count'] ?>)</span>
                        <?php endif; ?>
                    </span>
                </label>

		<?php endforeach; ?>
		</span>
	<?php endif; ?>


</div>

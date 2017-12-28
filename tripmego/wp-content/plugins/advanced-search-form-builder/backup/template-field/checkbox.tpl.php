<div class="formControl checkboxFilter">
    <label for=""><?php echo $args['label'] ?></label>
    <span class="wrapInput">
        <?php foreach ($args['options'] as $key => $value) : ?>
            <input
                <?php echo $this->statusChecked($args['type'], $args['name'], $value['value']) ?>
                type="checkbox"
                name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>][]"
                value="<?php echo $value['value'] ?>">
            <label for=""><?php echo $value['label'] ?></label>
        <?php endforeach; ?>
    </span>

</div>

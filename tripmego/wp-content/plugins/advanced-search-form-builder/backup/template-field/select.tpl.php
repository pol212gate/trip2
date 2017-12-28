<div class="form-control">
    <label for=""><?php echo $args['label'] ?></label>
    <span class="wrapInput">
        <select name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>]" id="">
            <option value="">Any</option>
            <?php foreach ($args['options'] as $key => $value) : ?>
                <option
                    <?php echo $this->statusSelected($args['type'], $args['name'], $value['value']) ?>
                        value="<?php echo $value['value'] ?>"><?php echo $value['label'] ?></option>
            <?php endforeach; ?>
        </select>
    </span>
</div>

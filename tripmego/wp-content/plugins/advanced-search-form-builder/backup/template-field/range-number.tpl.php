<div class="formControl checkboxFilter">
    <label for="">
        <?php echo $args['label'] ?>
        Từ <?php echo $args['min'] ?> đến <?php echo $args['max'] ?> Step <?php echo $args['step'] ?>
    </label>
    <span class="wrapInput">
        <input
                value="<?php
                    echo ( isset($_GET[$args['type']][$args['name']]['min']) ? sanitize_text_field($_GET[$args['type']][$args['name']]['min']) : '' )
                ?>"
                placeholder="Min"
                min="<?php echo $args['min'] ?>"
                max="<?php echo $args['max'] ?>"
                type="text"
                name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>][min]">
        <input placeholder="Max"
               min="<?php echo $args['min'] ?>"
               max="<?php echo $args['max'] ?>"
               type="text"
               value="<?php
                    echo ( isset($_GET[$args['type']][$args['name']]['max']) ? sanitize_text_field($_GET[$args['type']][$args['name']]['max']) : '' )
               ?>"
               name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>][max]">
    </span>
</div>

<div class="formControl checkboxFilter">
    <label for="">
        <?php echo $args['label'] ?>
        Từ <?php echo $args['datepicker_min'] ?> đến <?php echo $args['datepicker_max'] ?>
    </label>
    <span class="wrapInput">
        <input
            placeholder="dd/mm/yyyy"
            min="<?php echo $args['datepicker_min'] ?>"
            max="<?php echo $args['datepicker_max'] ?>"
            type="datetime"
            value="<?php
                echo ( isset($_GET[$args['type']][$args['name']]) ? sanitize_text_field($_GET[$args['type']][$args['name']]) : '' )
            ?>"
            name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>]">
    </span>
</div>

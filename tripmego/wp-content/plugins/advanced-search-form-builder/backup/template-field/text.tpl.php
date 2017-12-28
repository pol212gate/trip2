<div class="formControl checkboxFilter">
    <label for=""><?php echo $args['label'] ?></label>
    <span class="wrapInput">
        <input
            value="<?php
                echo ( isset($_GET[$args['type']][$args['name']]) ? sanitize_text_field($_GET[$args['type']][$args['name']]) : '' )
            ?>"
            type="text"
            name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>]">
    </span>
</div>

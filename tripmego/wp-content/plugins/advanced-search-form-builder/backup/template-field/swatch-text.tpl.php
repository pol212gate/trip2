<?php
$input = array(
    'type' => 'radio',
    'symbol_array' => ''
);

if ($args['multiple'] == 1) {
    $input = array(
        'type' => 'checkbox',
        'symbol_array' => '[]'
    );
}
?>

<div class="formControl checkboxFilter">
    <label for=""><?php echo $args['label'] ?></label>
    <span class="wrapInput">
        <?php foreach ($args['options'] as $key => $value) : ?>
            <label for="">
                <input
                    <?php echo $this->statusChecked($args['type'], $args['name'], $value['value']) ?>
                    type="<?php echo $input['type'] ?>"
                    name="<?php echo $args['type'] ?>[<?php echo $args['name'] ?>]<?php echo $input['symbol_array'] ?>"
                    value="<?php echo $value['value'] ?>">
                <span class="text"><?php echo $value['label'] ?></span>
            </label>
        <?php endforeach; ?>
    </span>
</div>

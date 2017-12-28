<?php

// USING: advanced-search-form-builder/includes/action_filters/actions.php
$id_miniform = cs_get_option('id_miniform');
$position_miniform = cs_get_option('position_miniform');
$display_miniform = cs_get_option('display_miniform');
$ids_post_miniform = cs_get_option('ids_post_miniform');

$btnOpen = cs_get_option('miniform_styling_image_open');
$btnClose = cs_get_option('miniform_styling_image_close');
$btnOpen = wp_get_attachment_image_src( $btnOpen, 'full' );
$btnClose = wp_get_attachment_image_src( $btnClose, 'full' );

$stylingForm = cs_get_option('miniform_styling_button');

?>
<style>
    .asfbFormGlobal .asfbBtnAction {
        background-image: url("<?php echo $btnOpen[0]; ?>");
    }
    .asfbFormGlobal.active .asfbBtnAction {
        background-image: url("<?php echo $btnClose[0]; ?>");
    }
    .asfbFormGlobal {
        background-color: <?php echo $stylingForm['bg-color'] ?>;
        border: <?php echo $stylingForm['border'] ?>;
        padding: <?php echo $stylingForm['padding'] ?>;
        border-radius: <?php echo $stylingForm['radius'] ?>;
        width: <?php echo $stylingForm['width']  ?>;
    }
</style>
<div class="asfbFormGlobal pos_<?php echo $position_miniform ?>" id="asfbFormGlobal">

    <button class="asfbBtnAction" type="button" onclick="jQuery('#asfbFormGlobal').toggleClass('active')">Open</button>

    <div class="asfbEntry">
        <?php echo do_shortcode('[form_search id="'. $id_miniform .'"]	') ?>
    </div>
</div>

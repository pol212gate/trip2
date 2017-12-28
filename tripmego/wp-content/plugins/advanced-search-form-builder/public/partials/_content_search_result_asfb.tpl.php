<?php


global $post; 

$pathTemplate = apply_filters('asfb_loop_template_path_file', $dataForm['path_template'], $form_id, $dataForm);

foreach ($searchResult['result'] as $key => $item) :
    $post = get_post($item['ID']);
    setup_postdata($post);

    ob_start();
    ?>

    <div class="asfb_itemCol">
        <?php
            if ( file_exists($pathTemplate) ) {
                include $pathTemplate;
            } else {
                echo __('Template not found', 'advanced_search_form_builder');
            }
        ?>
    </div>

<?php

    echo apply_filters('asfb_loop_content', ob_get_clean(), $form_id, $dataForm);

endforeach;  ?>

<?php wp_reset_postdata();



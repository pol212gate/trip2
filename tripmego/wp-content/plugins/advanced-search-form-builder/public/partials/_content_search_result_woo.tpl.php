<?php 
global $post;

woocommerce_product_loop_start();

foreach ($searchResult['result'] as $key => $item) :
    $post = get_post($item['ID']);
    setup_postdata($post);

    echo '<div class="asfb_itemCol">';
    	wc_get_template_part( 'content', 'product' ); 
    echo '</div>';
    
endforeach;   

woocommerce_product_loop_end();  

wp_reset_postdata(); 
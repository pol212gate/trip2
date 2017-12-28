<div class="asfbResultCounter <?php echo $searchResult['meta']['cachestatus'] ?>">
    <?php
    echo apply_filters('asfb_result_counter', '<i>
        Found '. number_format($searchResult['total_items']) .' item in '. number_format($searchResult['took'], 4, ',', '.') .' s
        for for keyword "'. ASFB_request::getQuery('q')  .'"
    </i>', $searchResult); ?>
</div>

<?php

    if ( isset($searchResult['result']) ) : ?>
        <div class="asfbWrapResult
                    layoutDesk_<?php echo $dataForm['styling_result_column_desktop'] ?>
                    layoutMobile_<?php echo $dataForm['styling_result_column_mobile'] ?>
                    layoutTablet_<?php echo $dataForm['styling_result_column_tablet'] ?>
            " id="asfb_<?php echo $form_id; ?>" >

            <div class="asfb_row"> 
                <?php   if (
                            class_exists( 'WooCommerce' )
                            && isset($dataForm['styling_result_woo'])
                            && $dataForm['styling_result_woo'] == 1
                        ) {
                            include '_content_search_result_woo.tpl.php';
                        } else {
                            include '_content_search_result_asfb.tpl.php';
                        }

                ?>
            </div>
        </div>

    <?php if ($searchResult['total_pages'] > 1) : ?>
        <div class="asfbPagination">
            <?php
            $args = array(
                'base'               => '%_%',
                'format'             => '?s_page=%#%',
                'total'              => $searchResult['total_pages'],
                'current'            => ASFB_request::getQuery('s_page'),
                'show_all'           => false,
                'end_size'           => 3,
                'mid_size'           => 3,
                'prev_next'          => true,
            );
            echo paginate_links( $args );
            ?>
    <?php endif; ?>
    
<?php endif; ?>

<?php
    if ( $dataForm['debug_mode'] == true ) {
        echo '<script> var dataResultDebug = '. json_encode($searchResult) .'</script>';
    }
?>

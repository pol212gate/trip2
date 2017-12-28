<?php
global $woocommerce, $post,$wt_main_purpose;
$wt_enddate = wt_global_expireddate();
$wt_accom_service = get_post_meta( $post->ID, 'wt_accom_service', false );
if($wt_main_purpose!='woo' && !empty($wt_accom_service)){?>
    <div class="clear"></div>
    <div class="woo-tour-accompanied col-md-12">
        <h3><?php echo esc_html__('Accompanied service','woo-tour')?></h3>
        <div class="wt-sche-detail tour-service">
            <?php 
            $i = 0;
            foreach($wt_accom_service as $item){
                $i++ ?>
                    <span><?php echo $item; ?></span>
                    <?php 
                if($i%5==0 && count($wt_accom_service)!=$i){
                    ?>
                    </div>
                    <div class="wt-sche-detail tour-service">
                    <?php
                }
            }?>
        </div>
    </div>
<?php }
$off_ssocial = get_option('wt_ssocial');
if($off_ssocial!='off'){
	?>
	<div class="wt-social-share col-md-12">
		<div class="row">
			<?php echo  wt_social_share();?>
		</div>
	</div>
<?php }?>
<div class="clear"></div>
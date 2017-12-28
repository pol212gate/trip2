<div class="div_box_left4">
<div class="div_description">
<?php
global $woocommerce, $post,$wt_main_purpose;
$wt_enddate = wt_global_expireddate();
$wt_accom_service = get_post_meta( $post->ID, 'wt_accom_service', false );
if($wt_main_purpose!='woo' && !empty($wt_accom_service)){?>
    <div class="clear"></div>
    <div class="woo-tour-accompanied col-md-12">
        <div class="div_title_description w-clearfix"><h3 class="h3_title_description"><i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp<?php echo esc_html__('Accompanied service','woo-tour')?></h3></div>
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
</div>
  <!-- Schedule -->
    

    <!--****************************** end Hook Service not include ******************************************-->
</div><!--div_box_left4-->



<?php
global $style;
global $ajax_load;
$wt_enddate = get_post_meta( get_the_ID(), 'wt_enddate', true )  ;
global $product;	
$wt_sku = $product->get_sku();
$type = $product->get_type();
$price ='';
if($type=='variable'){
	$price = wt_variable_price_html();
}else{
	if ( $price_html = $product->get_price_html() ) :
		$price = $price_html; 
	endif; 	
}
$wt_adress = wt_taxonomy_info('wt_location','off');
$wt_status = get_post_meta( get_the_ID(), 'wt_duration', true );

$wt_eventcolor = get_post_meta( get_the_ID(), 'wt_eventcolor', true );
$bgev_color = '';
if($wt_eventcolor!=""){
	$bgev_color = 'style="background-color:'.$wt_eventcolor.'"';
}
$wt_transport = get_post_meta( get_the_ID(), 'wt_transport', true ) ;

if($style!='2'){ ?>
	<tr <?php if(isset($ajax_load) && $ajax_load ==1){?>class="tb-load-item de-active" <?php }?>>
		<td class="wt-first-row"><?php the_post_thumbnail($img_size='wethumb_204x153');?></td>
		<td><h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
			<span class="event-meta wt-hidden-screen">
			  <?php if($wt_adress!=''){?>
				  <span class="tb-meta"><i class="fa fa-map-marker"></i> <?php echo $wt_adress;?></span>
			  <?php }if($price!=''){?>
				  <span class="tb-meta"><i class="fa fa-shopping-basket"></i><?php echo $price;?></span>
			  <?php }if($wt_status!=''){?>
				  <span class="tb-meta"><i class="fa fa-ticket"></i> <?php echo $wt_status;?></span>
			  <?php }?>
			</span>
		</td>
		<td class="wt-mb-hide"><?php echo $wt_adress;?></td>
		<td class="tb-price wt-mb-hide"><span><?php echo $price;?></span></td>
		<td class="wt-mb-hide"><?php echo $wt_status;?></td>
	</tr>
<?php }else{?>
	<tr <?php if(isset($ajax_load) && $ajax_load ==1){?>class="tb-load-item de-active" <?php }?>>
		<td class="wt-first-row">
			<?php the_post_thumbnail($img_size='wethumb_204x153');?>
		</td>
		<td>
			<h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
			<span class="event-meta">
			  <?php if($wt_sku!=''){?>
				  <span class="tb-meta"><i class="fa fa-info" aria-hidden="true"></i><?php echo esc_html__("Sku", "woo-tour").': '.$wt_sku;?></span>
			  <?php }if($wt_adress!=''){?>
				  <span class="tb-meta"><i class="fa fa-map-marker"></i> <?php echo $wt_adress;?></span>
			  <?php }if($wt_status!=''){?>
				  <span class="tb-meta"><i class="fa fa-clock-o"></i> <?php echo $wt_status;?></span>
			  <?php }if($wt_transport!=''){?>
				  <span class="tb-meta"><i class="fa fa-paper-plane"></i> <?php echo $wt_transport;?></span>
			  <?php }?>
			</span>
		</td>
		<td class="tb-viewdetails">
        	<span class="tb-price"><i class="fa fa-shopping-basket"></i><?php echo $price;?></span>
            <span>
            	<a class="btn btn btn-primary wt-button" <?php echo $bgev_color;?> href="<?php the_permalink();?>"><?php echo esc_html__('View Details','woo-tour');?> <i class="fa fa-angle-right" aria-hidden="true"></i></a>
            </span>
		</td>
	</tr>
<?php }

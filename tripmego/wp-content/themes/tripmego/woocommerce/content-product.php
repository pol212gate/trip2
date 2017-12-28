<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
?>



<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
	$countrytour = get_field('country_tour');
	$countrybase = get_field('country_base');
	$tourday = get_field('total_day');
	$tournight = get_field('total_night');
?>



<?PHP if(is_front_page()) { ?>
<div <?php post_class(); ?>><div class="item_frontpage"> <!--div class post-id product content product.php-->
<?PHP } else { ?>

<div <?php post_class(); ?>> <!--div class post-id product content product.php -->
	<!-- Condition Tag1 is prodduct category-->
	<?PHP if(is_product_category('Local experience')) { ?>
			<div class="item">

	<!-- Condition Tag2-->
	<?php } else if(is_product_category('Tour')) { ?>
			<div class="item">
	<!-- Condition Tag3-->
	<?php } else if(is_product_category('Tour')) { ?>
			<div class="item">
	<!-- Condition Tag3-->

	<?php } else {?>
			<div class="item">

	<?php } ?>

<?PHP } ?>

	<?php 

	
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	?>
		<div class="frameimg2">
	<?PHP
	do_action( 'woocommerce_before_shop_loop_item_title' );
	?>
	</div>

	<?PHP
	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */?>
<div>
 <div class="box_title"> <?php  do_action( 'woocommerce_shop_loop_item_title' ); ?><!-- Title -->
	

  </div>
</div>
<?php
	
	                   
                        $shortdes = get_field('short_description_trip');
                         $ctr = strtoupper($shortdes); ?>

     
<p class="p_div_detail"><?PHP //echo $shortdes; ?><?PHP echo get_the_excerpt(); ?></p> <!--descrip -->


<?PHP

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	?>

<div class="day clearfix"><div class="day_left"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp<?PHP echo $tourday."D".$tournight."N"; ?></div>
	<?PHP do_action( 'woocommerce_after_shop_loop_item_title' ); ?> <!--price-->
<?PHP 
	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
?>
</div>



<div class="btn_book">
	<div class="wish">
		<?php echo do_shortcode('[ti_wishlists_addtowishlist]') ?>
	</div> <!-- wishlist button add short code-->
	<div>
		<p class="country2country"><?PHP echo $countrybase; ?><i class="fa fa-plane iconplane" aria-hidden="true" ></i><?PHP echo $countrytour; ?></p>
		
	</div>
</div>
<?php

	//do_action( 'woocommerce_after_shop_loop_item' ); //button
	?>
<?php //echo do_shortcode('[ajax_load_more container_type="div" post_type="product" posts_per_page="1" post_format="standard" max_pages="7" transition="fade" destroy_after="1"]') ?>


<?PHP if(is_front_page()) { ?>
</div></div>
<?PHP } else { ?>
	</div><!--div item-->
</div><!--end div class post-id product -->

<?PHP } ?>


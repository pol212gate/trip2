<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="summary entry-summary">

		

<?PHP //$current_user_id = get_current_user_id(); echo $current_user_id; ?> <!--Get user ID -->




		

				<?php
					/**
					 * woocommerce_before_single_product_summary hook.
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>




<div class="item3">
				<?php
						/**
						 * woocommerce_single_product_summary hook.
						 *
						 * @hooked woocommerce_template_single_title - 5
						 * @hooked woocommerce_template_single_rating - 10
						 * @hooked woocommerce_template_single_price - 10
						 * @hooked woocommerce_template_single_excerpt - 20
						 * @hooked woocommerce_template_single_add_to_cart - 30
						 * @hooked woocommerce_template_single_meta - 40
						 * @hooked woocommerce_template_single_sharing - 50
						 * @hooked WC_Structured_Data::generate_product_data() - 60
						 */
						do_action( 'woocommerce_single_product_summary' );
				?>


	<?php
		/**
		 * woocommerce_after_single_product_summary hook.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

	
	<div class="item4">
		<div class="div_booking" >
			<div class="div_total_price w-clearfix">
				<!--<h3 class="h3_price">-->
					<?PHP   do_action('test'); ?>
					
				<!--</h3>-->
			</div>
			<div class="div_btn_and_price w-clearfix" >
				<button id="myBtn" class="btn_booking_now w-button">
					<i class="fa fa-book" aria-hidden="true"></i>&nbsp book trip 
				</button>
			</div>
		</div>
	</div>
	<div class="item2">
				<div class="div_block_question">
		<div class="link_question">
			<?PHP echo do_shortcode('[ultimatemember_message_button user_id="1"]') ?>
		</div>
	</div>
	</div>	
	<div class="item2">
		<div class="div_share">
		<div class="text_social_channel">Social Channel</div>
		<div class="div_box_icon_social">
			  <?php echo do_shortcode('[Sassy_Social_Share ]')  ?> <!-- social share // Saasy social share // Shortcode -->
		</div>
	</div>
</div>
		
				<script>
				// Get the modal
				var modal = document.getElementById('myModal');

				// Get the button that opens the modal
				var btn = document.getElementById("myBtn");

				// Get the <span> element that closes the modal
				var span = document.getElementsByClassName("close")[0];

				// When the user clicks on the button, open the modal 
				btn.onclick = function() {
				    modal.style.display = "block";
				}

				// When the user clicks on <span> (x), close the modal
				span.onclick = function() {
				    modal.style.display = "none";
				}

				// When the user clicks anywhere outside of the modal, close it
				window.onclick = function(event) {
				    if (event.target == modal) {
				        modal.style.display = "none";
				    }
				}
				</script>






	

	
</div><!-- .summary -->
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

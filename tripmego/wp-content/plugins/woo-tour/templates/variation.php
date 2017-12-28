<?php
/**
 * Single variation display
 *
 * This is a javascript-based template for single variations (see https://codex.wordpress.org/Javascript_Reference/wp.template).
 * The values will be dynamically replaced after selecting attributes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
$wt_main_purpose = wt_global_main_purpose();
$wt_slayout_purpose = get_option('wt_slayout_purpose');
$wt_layout_purpose = get_post_meta($product->get_id(),'wt_layout_purpose',true);
if(($wt_main_purpose=='custom' && $wt_layout_purpose!='tour') || ($wt_main_purpose=='meta' && $wt_layout_purpose=='woo') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='tour' && $wt_slayout_purpose=='woo') ){
	?>
    <script type="text/template" id="tmpl-variation-template">
		<div class="woocommerce-variation-description">
			{{{ data.variation.variation_description }}}
		</div>
	
		<div class="woocommerce-variation-price">
			{{{ data.variation.price_html }}}
		</div>
	
		<div class="woocommerce-variation-availability">
			{{{ data.variation.availability_html }}}
		</div>
	</script>
	<script type="text/template" id="tmpl-unavailable-variation-template">
		<p><?php _e( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ); ?></p>
	</script>

    <?php
}else{
	$wt_adult_max = get_post_meta( get_the_ID(), 'wt_adult_max', true );
	$sl_value = '';
	$l = get_option('wt_default_adl')!='' ? get_option('wt_default_adl') : 5;
	if(is_numeric ($wt_adult_max)){
		$l = $wt_adult_max;
	}
	$wt_adult_max = $wt_adult_max * 1;
	for($i=1; $i <= $l ; $i++){
		$sl_value .= '<option value="'.$i.'">'.$i.'</option>';
	}
	$wt_adult_label = get_post_meta( get_the_ID(), 'wt_adult_label', true ) ;
	$wt_adult_label = $wt_adult_label!='' ? $wt_adult_label : esc_html__('Adult: ','woo-tour');
	$html_adult = we_quantity_html('wt_number_adult',$sl_value,'1');
	?>
	<script type="text/template" id="tmpl-variation-template">
		<div class="woocommerce-variation-description">
			{{{ data.variation.variation_description }}}
		</div>
		<table class="tour-tble">
			<tbody>
				<tr>
					<td>
						<div class="woocommerce---price">
							<span class="lb-pric"><?php echo $wt_adult_label;?></span>
							<span class="price">{{{ data.variation._adult_price }}}</span>
						</div>
					</td>
					<td><?php echo $html_adult;?></td>
				</tr>
			</tbody>	
		</table>
	
		<div class="woocommerce-variation-availability">
			{{{ data.variation.availability_html }}}
		</div>
		
		{{{ data.variation._child_price }}}
						
		{{{ data.variation._infant_price }}}
	</script>
	<script type="text/template" id="tmpl-unavailable-variation-template">
		<p><?php _e( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ); ?></p>
	</script>
<?php
}
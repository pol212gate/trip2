<?php
class WooTour_Hook {
	public function __construct()
    {
		if(get_option('wt_metaposition') == 'above'){
			add_action( 'woocommerce_after_single_product_summary', array( &$this,'woocommerce_single_ev_meta') );
		}else{
			add_action( 'woocommerce_single_product_summary', array( &$this,'woocommerce_single_ev_meta') );
		}
		add_action( 'woocommerce_after_single_product_summary', array( &$this,'woocommerce_single_ev_schedu') );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( &$this,'woo_custom_cart_button_text'));  
		add_action('woocommerce_before_single_product',array( &$this,'add_info_before_single'),11);
		add_action( 'woocommerce_email_before_order_table', array( &$this,'woocommerce_email_hook'));
		add_filter ('woocommerce_add_to_cart_redirect', array( &$this,'woocommerce_redirect_to_checkout'));
    }
	//remove button if event pass
	function add_info_before_single(){
		global $woocommerce, $post;
		$time_now =  strtotime("now");
		$expireddate = wt_global_expireddate() ;
		if($expireddate !='' && $time_now > $expireddate){
			$mess = esc_html__('This tour has expired','woo-tour');
			echo '
			<div class="alert alert-warning tour-mes-info"><i class="fa fa-exclamation-triangle"></i>'.$mess.'</div>
			<style type="text/css">.woocommerce div.product form.cart, .woocommerce div.product p.cart{ display:none !important}</style>';
		}
	}// global $date
	
	function woo_custom_cart_button_text() {
		global $woocommerce, $post,$product;
		$type = $product->get_type();
		if($type=='external' && get_post_meta($post->ID,'_button_text',true)!=''){
			return get_post_meta($post->ID,'_button_text',true);
		}
		$wt_layout_purpose = get_post_meta($post->ID,'wt_layout_purpose',true);
		$wt_slayout_purpose = get_option('wt_slayout_purpose');
		if( $wt_layout_purpose=='woo' || ($wt_layout_purpose!='tour' && $wt_slayout_purpose=='def') ){
			return esc_html__( 'Add To Cart', 'woo-tour' );
		}
		return esc_html__( 'Book Now', 'woo-tour' );
	 
	}
	// Single Custom meta 
	function woocommerce_single_ev_meta() {
		global $woocommerce, $post;
		$wt_slayout_purpose = get_option('wt_slayout_purpose');
		$wt_layout_purpose = get_post_meta($post->ID,'wt_layout_purpose',true);
		if(($wt_layout_purpose == 'tour') || ($wt_layout_purpose == '' && $wt_slayout_purpose=='tour') || ($wt_layout_purpose == 'def' && $wt_slayout_purpose=='tour')){
			wootour_template_plugin('tour-meta');
		}
	}
	function woocommerce_single_ev_schedu() {
		global $woocommerce, $post;
		$wt_slayout_purpose = get_option('wt_slayout_purpose');
		$wt_layout_purpose = get_post_meta($post->ID,'wt_layout_purpose',true);
		if(($wt_layout_purpose == 'tour') || ($wt_layout_purpose == '' && $wt_slayout_purpose=='tour') || ($wt_layout_purpose == 'def' && $wt_slayout_purpose=='tour')){
			wootour_template_plugin('tour-acco');
		}
	}
	// Email hook
	function woocommerce_email_hook($order){
		$event_details = new WC_Order( $order->id );
		global $event_items;
		$event_items = $event_details->get_items();
		wootour_template_plugin('email-tour-details');

	}
	// redirect to checkout
	function woocommerce_redirect_to_checkout($wc) {
		if(get_option('wt_enable_cart')=='off'){
			global $woocommerce;
			$checkout_url = $woocommerce->cart->get_checkout_url();
			return $checkout_url;
		}
		return $wc;
	}
}
$WooTour_Hook = new WooTour_Hook();
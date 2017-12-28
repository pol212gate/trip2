<?php
class WooTour_Booking {
	public function __construct()
    {
		add_filter( 'woocommerce_is_sold_individually',  array( &$this,'wc_remove_all_quantity_fields'), 10, 2 );
		add_action( 'woocommerce_before_calculate_totals', array( &$this,'add_custom_total_price'), 199 );
		
		add_action('wp_ajax_wdm_add_user_custom_data_options', array( &$this,'add_user_data_booking'));
		add_action('wp_ajax_nopriv_wdm_add_user_custom_data_options', array( &$this,'add_user_data_booking'));
		
		add_filter('woocommerce_add_cart_item_data',array( &$this,'add_cart_user_data'),99,2);
		add_filter('woocommerce_get_cart_item_from_session', array( &$this,'get_cart_items_from_session'), 999, 3 );
		add_filter('woocommerce_get_item_data',array( &$this,'add_user_info_booking_from_session_into_cart'),1,3);
		
		add_action('woocommerce_add_order_item_meta',array( &$this,'add_info_to_order_item_meta'),1,2);
		//add form simple product
		add_action('woocommerce_before_add_to_cart_button',array( &$this,'html_custom_field'),1,1);
		//add form variable product
		add_action('woocommerce_before_variations_form',array( &$this,'html_custom_field_for_variable'),1,1);
		add_filter( 'woocommerce_order_item_meta_end', array( &$this,'display_item_order_meta'), 9, 3 );
		add_action('woocommerce_before_cart_item_quantity_zero',array( &$this,'remove_user_data_booking_from_cart'),1,1);
		add_filter( 'woocommerce_add_to_cart_validation', array( &$this,'validate_add_cart_item'), 10, 5 );
		
		//add_filter ( 'woocommerce_cart_item_subtotal' , array( &$this,'remove_subtotal'),11 ,3);
		//add_filter ( 'woocommerce_calculate_totals' , array( &$this,'check_calculate_totals'),1 ,1);
    }
	
	function check_calculate_totals( $data) {
		$cart_object = $data;
		foreach ( $cart_object->cart_contents as $key => $value ) {
			$rm = '';
			if(isset($value['_adult']) && $value['_adult']!=''){
				$rm = $value['line_total'] - $value['data']->price;
				$cart_object->cart_contents[ $key ]['line_subtotal'] = $cart_object->cart_contents[ $key ]['line_total'] = $value['data']->price;
			}
			$cart_object-> cart_contents_total = $cart_object-> cart_contents_total - $rm;
			$cart_object-> subtotal = $cart_object-> subtotal - $rm;
			$cart_object-> subtotal_ex_tax = $cart_object-> subtotal_ex_tax - $rm;
			$cart_object->removed_cart_contents = array();
		}
		return $cart_object;
	}
	function remove_subtotal( $wc, $cart_item, $cart_item_key  ) {
		//print_r($cart_item);
		if(isset($cart_item['_adult']) && $cart_item['_adult']!=''){
			$product = $cart_item['data'];
       		if ($product->wc_deposits_enable_deposit === 'yes' && !empty($cart_item['deposit']) && $cart_item['deposit']['enable'] === 'yes'){
				$tax = get_option('wc_deposits_tax_display', 'no') === 'yes' ?  $product->get_price_including_tax($cart_item['quantity']) -
        $product->get_price_excluding_tax($cart_item['quantity']) : 0;
				$deposit = $cart_item['deposit']['deposit'];
				$remaining = $cart_item['line_subtotal']*1 - $deposit*1;
				
				return woocommerce_price($deposit + $tax) . ' ' . __('Deposit', 'woocommerce-deposits') . '<br/>(' .
					   woocommerce_price($remaining) . ' ' . __('Remaining', 'woocommerce-deposits') . ')';
			}else{
				return wc_price( $cart_item['line_subtotal'] );
			}
		}else{
			return $wc;
		}
	}

	
	// Stop event booking before X day event start
	function validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
		$wt_main_purpose = get_option('wt_main_purpose');
		if($wt_main_purpose=='custom' || $wt_main_purpose=='meta'){
			$wt_layout_purpose = get_post_meta($product_id,'wt_layout_purpose',true);
			$wt_slayout_purpose = get_option('wt_slayout_purpose');
			if(($wt_layout_purpose=='woo') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='tour' && $wt_slayout_purpose=='woo') || ($wt_main_purpose=='custom' && $wt_layout_purpose=='')){
				return $passed;
			}
		}
		// do your validation, if not met switch $passed to false
		if ( !isset($_POST['wt_date']) || $_POST['wt_date']=='' ){
			$passed = false;
			$t_stopb = esc_html__('Please select Departure','woo-tour');
			wc_add_notice( $t_stopb, 'error' );
		}else{
			if ( isset($_POST['wt_number_adult']) ){
				$wt_sldate = isset($_POST['wt_sldate']) ? $_POST['wt_sldate'] : '';
				$avari = get_post_meta($product_id, $wt_sldate, true);
				
				if(isset($_POST['wt_number_adult'])){
					$ud_qty = $_POST['wt_number_adult']*1;
				}
				if(isset($_POST['wt_number_infant'])){
					$ud_qty = $ud_qty + $_POST['wt_number_infant']*1;
				}
				if(isset($_POST['wt_number_child'])){
					$ud_qty = $ud_qty + $_POST['wt_number_child']*1;
				}
				
				if($avari==''){
					$def_stock = get_post_meta($product_id, 'def_stock', true);
					if($def_stock > 0){
						$avari = $def_stock;
					}
				}
				if($avari!='' && ($avari < $ud_qty)){
					$passed = false;
					$t_stopb = esc_html__('Sorry there is not enough stock','woo-tour');
					wc_add_notice( $t_stopb, 'error' );
				}
				
				global $woocommerce;
				$items = $woocommerce->cart->get_cart();
				$check_stock = 0;
				$cart_it = 0;
				foreach($items as $item => $values) { 
					if($values['product_id'] == $product_id && $values['_date'] == $_POST['wt_date'] ){
						if(isset($values['_adult'])){
							$crr_qty = $values['_adult']*1;
						}
						if(isset($values['_infant'])){
							$crr_qty = $crr_qty + $values['_infant']*1;
						}
						if(isset($values['_child'])){
							$crr_qty = $crr_qty + $values['_child']*1;
						}
						$cart_it = $check_stock = $crr_qty;
					}
				}
				$check_stock = $check_stock + $ud_qty;
				if($avari!='' && ($avari < $check_stock) && $cart_it !=''){
					$passed = false;
					$t_stopb = sprintf(esc_html__('You cannot add that amount to the cart - we have %d in stock and you already have %d in your cart.', 'woo-tour'),$avari,$cart_it);
					wc_add_notice( $t_stopb, 'error' );
				}
			}
		}
		return $passed;
	
	}
	// remove select qty
	function wc_remove_all_quantity_fields( $return, $product ) {
		$wt_main_purpose = get_option('wt_main_purpose');
		if($wt_main_purpose=='' || $wt_main_purpose=='tour'){
			return true;
		}else{ 
			if(get_option('wt_disable_quantity') == 'yes'){
				return true;
			}else{
				return $return;
			}
		}
	}
	// Total booking price
	function add_custom_total_price( $cart_object ) {
		global $woocommerce;
		foreach ( $cart_object->cart_contents as $key => $value ) {
			$price_adu = $price_child = $price_inf = 0;
			$ud_qty ='';
			if(isset($value['_child']) && $value['_child']!=''){
				//$wt_child = get_post_meta( $value['product_id'], 'wt_child_sale', true );
				$wt_child = wt_get_price($value['product_id'], 'wt_child');
				if(isset($value['variation_id']) && $value['variation_id']!=''){
					//$wt_child = get_post_meta( $value['variation_id'], '_child_price', true );
					$wt_child = wt_get_price($value['variation_id'], '_child_price');
				}
				if($wt_child !='OFF'){
					$price_child = $wt_child * $value['_child'] ;
				}
			}
			if(isset($value['_infant']) && $value['_infant']!=''){
				//$wt_infant = get_post_meta( $value['product_id'], 'wt_infant', true );
				$wt_infant = wt_get_price($value['product_id'], 'wt_infant');
				if(isset($value['variation_id']) && $value['variation_id']!=''){
					//$wt_infant = get_post_meta( $value['variation_id'], '_infant_price', true );
					$wt_infant = wt_get_price($value['variation_id'], '_infant_price');
				}
				if($wt_infant !='OFF'){
					$price_inf = $wt_infant * $value['_infant'] ;
				}
			}
			if(isset($value['_adult']) && $value['_adult']!=''){
				if(isset($value['variation_id']) && $value['variation_id']!=''){
					$_product = new WC_Product_Variation( $value['variation_id'] );
					$pricefix =  $value['data']->get_price();
					$price_adu = $pricefix *($value['_adult'] - 1);
				}else{
					$product = new WC_Product($value['product_id']);
					$pricefix = $value['data']->get_price();
					$price_adu = $pricefix *($value['_adult'] - 1);
				}
				$wt_discount = get_post_meta($value['product_id'],'wt_discount',false);
				//echo '<pre>';print_r($wt_discount);exit;
				do_action('wt_all_cart_data',$value);
				if(!empty($wt_discount) && ( !isset($value['deposit_value']) || $value['deposit_value']=='' )){
					$cure_time =  strtotime("now");
					$gmt_offset = get_option('gmt_offset');
					if($gmt_offset!=''){
						$cure_time = $cure_time + ($gmt_offset*3600);
					}
					//echo '<pre>';print_r($value);//exit;
					usort($wt_discount, function($a, $b) { // anonymous function
						return $a['wt_disc_number'] - $b['wt_disc_number'];
					});
					$wt_discount = array_reverse($wt_discount);
					//print_r($wt_discount);//exit;
					foreach ($wt_discount as $item){
						if(($item['wt_disc_start']=='' && $item['wt_disc_end']=='') || ($item['wt_disc_start']!='' && $item['wt_disc_end']=='' && $cure_time > $item['wt_disc_start']) || ($item['wt_disc_start']=='' && $item['wt_disc_end']!='' && $cure_time < $item['wt_disc_end']) || ($item['wt_disc_start']!='' && $item['wt_disc_end']!='' && $cure_time < $item['wt_disc_end'] && $item['wt_disc_start'] < $cure_time) ){
							if($value['_adult'] >= $item['wt_disc_number']){
								if($item['wt_disc_type']=='percent' && $item['wt_disc_am'] > 0){
									$disc_price = $pricefix - ($pricefix * $item['wt_disc_am']/100);
								}elseif($item['wt_disc_am'] > 0){
									$disc_price = $pricefix - $item['wt_disc_am'];
								}else{break;}
								$pricefix = $disc_price;
								$price_adu = $pricefix *($value['_adult'] - 1);
								break;
							}
						}
					}
				}
				if(WC()->session->get('reload_checkout')==true){
					$value['data']->set_price(($value['data']->get_price()));
				}else{
					//$value['data']->price = $value['data']->price + $price_adu*1 + $price_child*1 + $price_inf*1;
					if($cart_object->cart_contents[$key]['_prhaschanged']!=''){
						$value['data']->set_price($cart_object->cart_contents[$key]['_prhaschanged']);
					}else{
						$value['data']->set_price( $pricefix + $price_adu*1 + $price_child*1 + $price_inf*1 );
						$cart_object->cart_contents[$key]['_prhaschanged'] = $pricefix + $price_adu*1 + $price_child*1 + $price_inf*1;
					}
				}
			}
			
			//echo '<pre>'; print_r($cart_object);echo '</pre>';exit;
			//$value['data']->price += $framed_price;
			
			/*if(isset($value['_adult'])){
				$ud_qty = $value['_adult']*1;
			}
			if(isset($value['_infant'])){
				$ud_qty = $ud_qty + $value['_infant']*1;
			}
			if(isset($value['_child'])){
				$ud_qty = $ud_qty + $value['_child']*1;
			}
			
			if($ud_qty!='' && is_numeric($ud_qty)){
				//$woocommerce->cart->set_quantity ( $key , $ud_qty , false );
			}*/
			//echo $ud_qty ;exit;
		}
	}	
	// step 1 add user data booking to session
	function add_user_data_booking()
	{
		//Custom data - Sent Via AJAX post method
		$product_id = $_POST['id']; //This is product ID
		$wt_date = $_POST['wt_date'];
		$wt_number_adult = $_POST['wt_number_adult']; //This is User custom value sent via AJAX
		$wt_number_child = $_POST['wt_number_child'];
		$wt_number_infant = $_POST['wt_number_infant'];
		$wt_sldate = $_POST['wt_sldate'];
		session_start();
		$_tour_info['_date'] = $wt_date;
		$_tour_info['_adult'] =  $wt_number_adult;
		$_tour_info['_child'] =  $wt_number_child;
		$_tour_info['_infant'] =  $wt_number_infant;
		$_tour_info['_metadate'] =  $wt_sldate;
	
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
	
		WC()->session->set( '_tour_info', $_tour_info );
		die();
	}
	// step 2 add user data booking to cart data
	function add_cart_user_data($cart_item_data,$product_id)
	{
		$wt_main_purpose = get_option('wt_main_purpose');
		if($wt_main_purpose=='custom' || $wt_main_purpose=='meta'){
			$wt_layout_purpose = get_post_meta($product_id,'wt_layout_purpose',true);
			$wt_slayout_purpose = get_option('wt_slayout_purpose');
			if(($wt_layout_purpose=='woo') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='tour' && $wt_slayout_purpose=='woo') || ($wt_main_purpose=='custom' && $wt_layout_purpose=='')){
				//WC()->session->set( '_tour_info', '' );
				return $cart_item_data;
			}
		}
		$_tour_info = WC()->session->get( '_tour_info' );

		$new_value = array();

		if (isset($_tour_info['_date'])) {
			$_date = $_tour_info['_date'];
			$new_value['_date'] =  $_date;
		}
		if (isset($_tour_info['_adult'])) {
			$_adult = $_tour_info['_adult'];
			$new_value['_adult'] =  $_adult;
		}
		if (isset($_tour_info['_child'])) {
			$_child = $_tour_info['_child'];
			$new_value['_child'] =  $_child;
		}
		if (isset($_tour_info['_infant'])) {
			$_infant = $_tour_info['_infant'];
			$new_value['_infant'] =  $_infant;
		}
		if (isset($_tour_info['_metadate'])) {
			$_metadate = $_tour_info['_metadate'];
			$new_value['_metadate'] =  $_metadate;
		}

		if( empty($_adult) && empty($_child) && empty($_date) && empty($_infant) )
			return $cart_item_data;
		else
		{
			if(empty($cart_item_data))
				return $new_value;
			else
				return array_merge($cart_item_data,$new_value);
		}

		unset( $_tour_info['_date'] );
		unset( $_tour_info['_adult'] );
		unset( $_tour_info['_child'] );
		unset( $_tour_info['_infant'] );
		unset( $_tour_info['_metadate'] );
		WC()->session->set( '_tour_info', '' );
		//Unset our custom session variable, as it is no longer needed.
	}
	// step 3 get cart data from session from step 2
	function get_cart_items_from_session($item,$values,$key)
	{
		//$_tour_info = WC()->session->get( '_tour_info' );
		//echo '<pre>';print_r($values);echo '</pre>';exit;
		if (array_key_exists( '_date', $values ) ){
			$item['_date'] = $values['_date'];
		}
		if (array_key_exists( '_adult', $values ) ){
			$item['_adult'] = $values['_adult'];
		}
		if (array_key_exists( '_child', $values ) ){
			$item['_child'] = $values['_child'];
		}
		if (array_key_exists( '_infant', $values ) ){
			$item['_infant'] = $values['_infant'];
		}
		if (array_key_exists( '_metadate', $values ) ){
			$item['_metadate'] = $values['_metadate'];
		}
		if ( isset( $values['_adult'] ) && ! empty ( $values['_adult'] ) ) {
			$wt_discount = get_post_meta($values['product_id'],'wt_discount',false);
			if(!empty($wt_discount) && ( !isset($values['deposit_value']) || $values['deposit_value']=='' )){
				$cure_time =  strtotime("now");
				$gmt_offset = get_option('gmt_offset');
				if($gmt_offset!=''){
					$cure_time = $cure_time + ($gmt_offset*3600);
				}
				usort($wt_discount, function($a, $b) { // anonymous function
					return $a['wt_disc_number'] - $b['wt_disc_number'];
				});
				$wt_discount = array_reverse($wt_discount);
				foreach ($wt_discount as $item_dc){
					if(($item_dc['wt_disc_start']=='' && $item_dc['wt_disc_end']=='') || ($item_dc['wt_disc_start']!='' && $item_dc['wt_disc_end']=='' && $cure_time > $item_dc['wt_disc_start']) || ($item_dc['wt_disc_start']=='' && $item_dc['wt_disc_end']!='' && $cure_time < $item_dc['wt_disc_end']) || ($item_dc['wt_disc_start']!='' && $item_dc['wt_disc_end']!='' && $cure_time < $item_dc['wt_disc_end'] && $item_dc['wt_disc_start'] < $cure_time) ){
						if($values['_adult'] >= $item_dc['wt_disc_number']){
							if($item_dc['wt_disc_type']=='percent' && $item_dc['wt_disc_am'] > 0){
								$disc_type = $item_dc['wt_disc_am'].'%';
							}elseif($item_dc['wt_disc_am'] > 0){
								$disc_type = wc_price($item_dc['wt_disc_am']);
							}else{break;}
								$item['_wtdiscount'] = $disc_type;
							break;
						}
					}
				}
			}
		}
		return $item;
	}
	// step 4 add user info booking to cart
	function add_user_info_booking_from_session_into_cart($other_data, $cart_item )
	{
						
		if ( isset( $cart_item['_date'] ) && ! empty ( $cart_item['_date'] ) ) {
			$wt_date_label = get_post_meta( $cart_item['product_id'], 'wt_date_label', true ) ;
			$wt_date_label = $wt_date_label!='' ? $wt_date_label : esc_html__('Departure','woo-tour');
			$other_data[] = array(
				'name'  => $wt_date_label,
				'value' => $cart_item['_date']
			);

		}
		
		if ( isset( $cart_item['_adult'] ) && ! empty ( $cart_item['_adult'] ) ) {
			$wt_adult_label = get_post_meta( $cart_item['product_id'], 'wt_adult_label', true ) ;
			$wt_adult_label = $wt_adult_label!='' ? $wt_adult_label : esc_html__('Adult','woo-tour');
			$_price_old = wc_get_product( $cart_item['product_id'] );
			if(isset($cart_item['variation_id']) && $cart_item['variation_id']!=''){
				$_price_old = wc_get_product( $cart_item['variation_id'] );
			}
			$other_data[] = array(
				'name'  => $wt_adult_label,
				'value' => $cart_item['_adult'].' x '.wt_addition_price_html($_price_old->get_price())
			);

		}
		
		//$wt_child = get_post_meta( $cart_item['product_id'], 'wt_child', true ) ;
		$wt_child = wt_get_price($cart_item['product_id'], 'wt_child');
		//$wt_infant = get_post_meta( $cart_item['product_id'], 'wt_infant', true ) ;
		$wt_infant = wt_get_price($cart_item['product_id'], 'wt_infant');
		if(isset($cart_item['variation_id']) && $cart_item['variation_id']!=''){
			//$wt_child = get_post_meta( $cart_item['variation_id'], '_child_price', true ) ;
			$wt_child = wt_get_price($cart_item['variation_id'], '_child_price');
			//$wt_infant = get_post_meta( $cart_item['variation_id'], '_infant_price', true ) ;
			$wt_infant = wt_get_price($cart_item['variation_id'], '_infant_price');
		}
		/*$currency_pos = get_option( 'woocommerce_currency_pos' );
		if($currency_pos=='left' || $currency_pos==''){ 
			if($wt_child!='' && $wt_child!='OFF'){
				$wt_child = get_woocommerce_currency_symbol().$wt_child; 
			}
			if($wt_infant!='' && $wt_infant!='OFF'){
				$wt_infant = get_woocommerce_currency_symbol().$wt_infant; 
			}
		}else if($currency_pos=='left_space'){ 
			if($wt_child!='' && $wt_child!='OFF'){
				$wt_child = get_woocommerce_currency_symbol().' '.$wt_child; 
			}
			if($wt_infant!='' && $wt_infant!='OFF'){
				$wt_infant = get_woocommerce_currency_symbol().' '.$wt_infant; 
			}
		}elseif($currency_pos=='right'){ 
			if($wt_child!='' && $wt_child!='OFF'){
				$wt_child = $wt_child.get_woocommerce_currency_symbol(); 
			}
			if($wt_infant!='' && $wt_infant!='OFF'){
				$wt_infant = $wt_infant.get_woocommerce_currency_symbol(); 
			}
		}else if($currency_pos=='right_space'){
			if($wt_child!='' && $wt_child!='OFF'){
				$wt_child = $wt_child.' '.get_woocommerce_currency_symbol(); 
			}
			if($wt_infant!='' && $wt_infant!='OFF'){
				$wt_infant = $wt_infant.' '.get_woocommerce_currency_symbol(); 
			}
		}*/
		if($wt_child!=''){ 
			$wt_child = wt_addition_price_html($wt_child);
			$wt_child = ' x '.$wt_child;
		}
		if($wt_infant!=''){ 
			$wt_infant = wt_addition_price_html($wt_infant);
			$wt_infant = ' x '.$wt_infant;
		}
		if ( isset( $cart_item['_child'] ) && ! empty ( $cart_item['_child'] ) ) {
			$wt_child_label = get_post_meta( $cart_item['product_id'], 'wt_child_label', true ) ;
			$wt_child_label = $wt_child_label!='' ? $wt_child_label : esc_html__('Children','woo-tour');
			$other_data[] = array(
				'name'  => $wt_child_label,
				'value' => $cart_item['_child'].$wt_child
			);

		}
		if ( isset( $cart_item['_infant'] ) && ! empty ( $cart_item['_infant'] ) ) {
			$wt_infant_label = get_post_meta( $cart_item['product_id'], 'wt_infant_label', true ) ;
			$wt_infant_label = $wt_infant_label!='' ? $wt_infant_label : esc_html__('Infant','woo-tour');
			$other_data[] = array(
				'name'  => $wt_infant_label,
				'value' => $cart_item['_infant'].$wt_infant
			);

		}
		
		if ( isset( $cart_item['_wtdiscount'] ) && ! empty ( $cart_item['_wtdiscount'] ) ) {
			$other_data[] = array(
				'name'  => esc_html__('Discount','woo-tour'),
				'value' => $cart_item['_wtdiscount'].' '.esc_html__('Per each adult','woo-tour')
			);
		}

		
		return $other_data;
	}
	// step 5 add user booking info to order admin
	function add_info_to_order_item_meta($item_id, $values)
	{
		$_date = $values['_date'];

		if(!empty($_date))
		{
			wc_add_order_item_meta($item_id,'_date',sanitize_text_field($_date));
		}
		
		$_adult = $values['_adult'];

		if(!empty($_adult))
		{
			wc_add_order_item_meta($item_id,'_adult',sanitize_text_field($_adult));
		}

		$_child = $values['_child'];

		if(!empty($_child))
		{
			wc_add_order_item_meta($item_id,'_child',sanitize_text_field($_child));
		}
		
		$_infant = $values['_infant'];

		if(!empty($_infant))
		{
			wc_add_order_item_meta($item_id,'_infant',sanitize_text_field($_infant));
		}
		
		$_metadate = $values['_metadate'];

		if(!empty($_metadate))
		{
			wc_add_order_item_meta($item_id,'_metadate',sanitize_text_field($_metadate));
		}
		
		$_wtdiscount = $values['_wtdiscount'];

		if(!empty($_wtdiscount))
		{
			wc_add_order_item_meta($item_id,'_wtdiscount',sanitize_text_field($_wtdiscount.' '.esc_html__('Per each adult','woo-tour')));
		}
	}
	// step 7 add imfo booking form for simple product
	function html_custom_field()
	{
		global $product;	
		$type = $product->get_type();
		if($type=='variable'){
			return;
		}
		$wt_main_purpose = wt_global_main_purpose();
		$wt_slayout_purpose = get_option('wt_slayout_purpose');
		$wt_show_sdate = get_option('wt_show_sdate');
		$wt_layout_purpose = get_post_meta(get_the_ID(),'wt_layout_purpose',true);
		if(($wt_main_purpose=='custom' && $wt_layout_purpose!='tour') || ($wt_main_purpose=='meta' && $wt_layout_purpose=='woo') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='tour' && $wt_slayout_purpose=='woo') ){
			return;
		}
		$wt_customdate = get_post_meta( get_the_ID(), 'wt_customdate', false ) ;
		$wt_disabledate = get_post_meta( get_the_ID(), 'wt_disabledate', false ) ;
		$arr_disdate = array();
		if(is_array($wt_disabledate) && !empty($wt_disabledate)){
			$i = 0;
			foreach($wt_disabledate as $idt){
				$i ++;
				$arr_disdate[$i] = $idt;
			}
		}
		$arr_disdate = str_replace('\/', '/', json_encode($arr_disdate));
		$wt_weekday = get_post_meta( get_the_ID(), 'wt_weekday', true ) ;
		$weekday = array(1,2,3,4,5,6,7);
		$arr_diff = array();
		if(is_array($wt_weekday) && !empty($wt_weekday)){
			$arr_diff = array_diff($weekday,$wt_weekday);
		}
		$arr_diff = str_replace('\/', '/', json_encode($arr_diff));
		$wt_expired = get_post_meta( get_the_ID(), 'wt_expired', true ) ;
		/*if($wt_expired !=''){
			$time_now =  strtotime("now");
			$wt_expired = $wt_expired - $time_now;
			$wt_expired = $wt_expired/86400;
			if($wt_expired > 0){ $wt_expired = floor($wt_expired);}else{ $wt_expired = '';}
		}*/
		if($wt_expired !=''){ $wt_expired = date_i18n('Y,m,d', $wt_expired); }
		/*--Book before--*/
		$wt_disable_book = get_post_meta( get_the_ID(), 'wt_disable_book', true ) ;
		if($wt_disable_book==''){
			$wt_disable_book = get_option('wt_disable_book');
		}
		$dis_uni = 0;
		if($wt_disable_book!='' && is_numeric($wt_disable_book)){
			$dis_uni = strtotime("+$wt_disable_book day");
			$wt_disable_book = date_i18n('Y,m,d',$dis_uni);
		}else{
			$wt_disable_book='';
		}
		$arr_ctdate = '';
		$df_day = '';
		if(is_array($wt_customdate) && !empty($wt_customdate)){
			$i=0;
			if($wt_show_sdate !='calendar'){
				$df_day ='';
				foreach($wt_customdate as $item){
					$i++;
					if($wt_disable_book!='' && $dis_uni > $item){
						$clss = 'wt-disble';
					}
					$arr_ctdate .= '<li class="'.$clss.'" data-value="'.date_i18n( get_option('date_format'), $item).'" data-date="'.date_i18n('Y_m_d', $item).'">'.date_i18n( get_option('date_format'), $item).'</li>';
				}
			}else{
				$cure_time =  strtotime("now");
				$gmt_offset = get_option('gmt_offset');
				if($gmt_offset!=''){
					$cure_time = $cure_time + ($gmt_offset*3600);
				}
				foreach($wt_customdate as $ict){
					$i ++;
					if($ict > $cure_time){
						$arr_ctdate[$i] = $ict;
					}elseif(count($wt_customdate) == $i && empty($arr_ctdate)){
						$arr_ctdate[$i] = $ict - (2*2592000);
					}
					
				}
				$arr_ctdate = str_replace('\/', '/', json_encode($arr_ctdate));
			}
		}elseif($wt_show_sdate =='calendar'){
			$arr_ctdate = array();
			$arr_ctdate = str_replace('\/', '/', json_encode($arr_ctdate));
		}
		$trsl_mtext [1]= esc_html__('January','woo-tour');
		$trsl_mtext [2]= esc_html__('February','woo-tour');
		$trsl_mtext [3]= esc_html__('March','woo-tour');
		$trsl_mtext [4]= esc_html__('April','woo-tour');
		$trsl_mtext [5]= esc_html__('May','woo-tour');
		$trsl_mtext [6]= esc_html__('June','woo-tour');
		$trsl_mtext [7]= esc_html__('July','woo-tour');
		$trsl_mtext [8]= esc_html__('August','woo-tour');
		$trsl_mtext [9]= esc_html__('September','woo-tour');
		$trsl_mtext [10]= esc_html__('October','woo-tour');
		$trsl_mtext [11]= esc_html__('November','woo-tour');
		$trsl_mtext [12]= esc_html__('December','woo-tour');
		$trsl_mtext = str_replace('\/', '/', json_encode($trsl_mtext));
		
		$trsl_dtext [1]= esc_html__('Sun','woo-tour');
		$trsl_dtext [2]= esc_html__('Mon','woo-tour');
		$trsl_dtext [3]= esc_html__('Tue','woo-tour');
		$trsl_dtext [4]= esc_html__('Wed','woo-tour');
		$trsl_dtext [5]= esc_html__('Thu','woo-tour');
		$trsl_dtext [6]= esc_html__('Fri','woo-tour');
		$trsl_dtext [7]= esc_html__('Sat','woo-tour');
		
		$trsl_dtext = str_replace('\/', '/', json_encode($trsl_dtext));
		$wt_firstday = get_option('wt_firstday','7');
		
		$wt_date_label = get_post_meta( get_the_ID(), 'wt_date_label', true ) ;
		$wt_date_label = $wt_date_label!='' ? $wt_date_label.': ' : esc_html__('Departure: ','woo-tour');
		echo '
		<div class="tour-info-select">
			<span class="wt-departure">' . $wt_date_label .'
				<span>';
					if($arr_ctdate!='' && $wt_show_sdate!='calendar'){
						echo '
						<input type="text" class="wt-custom-date" readonly name="wt_date" value="'.$df_day.'">
						<ul class="wt-list-date">'.$arr_ctdate.'</ul>';
					}else{
						$wt_calendar_lg = get_option('wt_calendar_lg');
						echo '
						<input type="hidden" name="wt_weekday_disable" value='.$arr_diff.'>
						<input type="hidden" name="wt_langu" value='.$wt_calendar_lg.'>
						<input type="hidden" name="wt_date_disable" value='.$arr_disdate.'>
						<input type="hidden" name="wt_cust_date" value='.$arr_ctdate.'>
						<input type="hidden" name="wt_expired" value="'.$wt_expired.'">
						<input type="hidden" name="wt_firstday" value="'.$wt_firstday.'">
						<input type="hidden" name="wt_daytrsl" value='.str_replace(' ', '\u0020', $trsl_dtext).'>
						<input type="hidden" name="wt_montrsl" value='.str_replace(' ', '\u0020', $trsl_mtext).'>
						<input type="text" readonly name="wt_date">';
					}
					echo '
					<i class="fa fa-calendar wt-bticon" aria-hidden="true"></i>
					<input type="hidden" name="wt_ajax_url" value='.esc_url(admin_url( 'admin-ajax.php' )).'>
					<input type="hidden" name="wt_tourid" value='.esc_attr( get_the_ID()).'>
					<input type="hidden" name="wt_sldate" value="">
					<input type="hidden" name="wt_book_before" value="'.$wt_disable_book.'">
				</span>
				<span class="wt-tickets-status"></span>
			</span>';
			echo '
			<span class="wt-user-info wtsl-'.get_option( 'wt_type_qunatity' ).'">';
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
				$wt_adult_label = '<span class="lb-pric">'.$wt_adult_label.'</span>';
				echo '<span class="">' . $wt_adult_label.' <span>'.$product->get_price_html().'</span>';
					//echo '<select name="wt_number_adult">'.$sl_value.'</select>';
					echo we_quantity_html('wt_number_adult', $sl_value,'1');
				echo '</span>';
				
				
				$wt_child = get_post_meta( get_the_ID(), 'wt_child', true ) ;
				$wt_child_max = get_post_meta( get_the_ID(), 'wt_child_max', true ) ;
				$wt_child_label = get_post_meta( get_the_ID(), 'wt_child_label', true ) ;
				$wt_child_label = $wt_child_label!='' ? $wt_child_label.': ' : esc_html__('Children: ','woo-tour');
				$wt_child_label = '<span class="lb-pric">'.$wt_child_label.'</span>';
				$wt_infant = get_post_meta( get_the_ID(), 'wt_infant', true ) ;
				$wt_infant_max = get_post_meta( get_the_ID(), 'wt_infant_max', true ) ;
				$wt_infant_label = get_post_meta( get_the_ID(), 'wt_infant_label', true ) ;
				$wt_infant_label = $wt_infant_label!='' ? $wt_infant_label.': ' : esc_html__('Infant: ','woo-tour');
				$wt_infant_label = '<span class="lb-pric">'.$wt_infant_label.'</span>';
				$wt_def_childf = get_option( 'wt_def_childf' ) ;
				if( ($wt_child!='OFF' && $wt_child!='') || ($wt_child=='' && $wt_def_childf!='off') ){
					$sl_cvalue = '<option value="">0</option>';
					$l = get_option('wt_default_child')!='' ? get_option('wt_default_child') : 5;
					if(is_numeric ($wt_child_max)){
						$l = $wt_child_max;
					}
					$wt_child_max = $wt_child_max * 1;
					for($i=1; $i <= $l ; $i++){
						$sl_cvalue .= '<option value="'.$i.'">'.$i.'</option>';
					}
					$cd_sale = get_post_meta( get_the_ID(), 'wt_child_sale', true );
					echo '<span class="_child_select">' . $wt_child_label . wt_addition_price_html($wt_child,1,$cd_sale);
						//echo '<select name="wt_number_child">'.$sl_cvalue.'</select>';
						echo we_quantity_html('wt_number_child', $sl_cvalue,'0');
					echo '</span>';
				}
				$wt_def_intff = get_option( 'wt_def_intff' ) ;
				if( ($wt_infant!='OFF' && $wt_infant!='') || ($wt_infant=='' && $wt_def_intff!='off') ){
					$sl_ivalue = '<option value="">0</option>';
					$l = get_option('wt_default_inf') !='' ? get_option('wt_default_inf') : 5 ;
					if(is_numeric ($wt_infant_max)){
						$l = $wt_infant_max;
					}
					$wt_infant_max = $wt_infant_max * 1;
					for($i=1; $i <= $l ; $i++){
						$sl_ivalue .= '<option value="'.$i.'">'.$i.'</option>';
					}
					$if_sale = get_post_meta( get_the_ID(), 'wt_infant_sale', true );
					echo '<span class="_infant_select">' . $wt_infant_label . wt_addition_price_html($wt_infant,1,$if_sale);
						//echo '<select name="wt_number_infant">'.$sl_ivalue.'</select>';
						echo we_quantity_html('wt_number_infant', $sl_ivalue,'0');
					echo '</span>';
				}
				echo '
			</span>
		</div>
		';
		
	}
	function html_custom_field_for_variable(){
		$wt_main_purpose = wt_global_main_purpose();
		$wt_slayout_purpose = get_option('wt_slayout_purpose');
		$wt_layout_purpose = get_post_meta(get_the_ID(),'wt_layout_purpose',true);
		if(($wt_main_purpose=='custom' && $wt_layout_purpose!='tour') || ($wt_main_purpose=='meta' && $wt_layout_purpose=='woo') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='tour' && $wt_slayout_purpose=='woo') ){
			return;
		}
		$wt_customdate = get_post_meta( get_the_ID(), 'wt_customdate', false ) ;
		$wt_disabledate = get_post_meta( get_the_ID(), 'wt_disabledate', false ) ;
		$arr_disdate = array();
		if(is_array($wt_disabledate) && !empty($wt_disabledate)){
			$i = 0;
			foreach($wt_disabledate as $idt){
				$i ++;
				$arr_disdate[$i] = $idt;
			}
		}
		$arr_disdate = str_replace('\/', '/', json_encode($arr_disdate));
		$wt_weekday = get_post_meta( get_the_ID(), 'wt_weekday', true ) ;
		$weekday = array(1,2,3,4,5,6,7);
		$arr_diff = array();
		if(is_array($wt_weekday) && !empty($wt_weekday)){
			$arr_diff = array_diff($weekday,$wt_weekday);
		}
		$arr_diff = str_replace('\/', '/', json_encode($arr_diff));
		$wt_expired = get_post_meta( get_the_ID(), 'wt_expired', true ) ;
		if($wt_expired !=''){ $wt_expired = date_i18n('Y,m,d', $wt_expired); }
		/*--Book before--*/
		$wt_disable_book = get_post_meta( get_the_ID(), 'wt_disable_book', true ) ;
		if($wt_disable_book==''){
			$wt_disable_book = get_option('wt_disable_book');
		}
		if($wt_disable_book!='' && is_numeric($wt_disable_book)){
			$wt_disable_book = date_i18n('Y,m,d',strtotime("+$wt_disable_book day"));
		}else{
			$wt_disable_book='';
		}
		$arr_ctdate = '';
		$df_day = '';
		$wt_show_sdate = get_option('wt_show_sdate');
		if(is_array($wt_customdate) && !empty($wt_customdate)){
			$i=0;
			if($wt_show_sdate !='calendar'){
				$df_day ='';
				foreach($wt_customdate as $item){
					$i++;
					if($wt_disable_book!='' && $dis_uni > $item){
						$clss = 'wt-disble';
					}
					$arr_ctdate .= '<li class="'.$clss.'" data-value="'.date_i18n( get_option('date_format'), $item).'" data-date="'.date_i18n('Y_m_d', $item).'">'.date_i18n( get_option('date_format'), $item).'</li>';
				}
			}else{
				$cure_time =  strtotime("now");
				$gmt_offset = get_option('gmt_offset');
				if($gmt_offset!=''){
					$cure_time = $cure_time + ($gmt_offset*3600);
				}
				foreach($wt_customdate as $ict){
					$i ++;
					if($ict > $cure_time){


						$arr_ctdate[$i] = $ict;
					}
				}
				$arr_ctdate = str_replace('\/', '/', json_encode($arr_ctdate));
			}
		}elseif($wt_show_sdate =='calendar'){
			$arr_ctdate = array();
			$arr_ctdate = str_replace('\/', '/', json_encode($arr_ctdate));
		}
		$trsl_mtext [1]= esc_html__('January','woo-tour');
		$trsl_mtext [2]= esc_html__('February','woo-tour');
		$trsl_mtext [3]= esc_html__('March','woo-tour');
		$trsl_mtext [4]= esc_html__('April','woo-tour');
		$trsl_mtext [5]= esc_html__('May','woo-tour');
		$trsl_mtext [6]= esc_html__('June','woo-tour');
		$trsl_mtext [7]= esc_html__('July','woo-tour');
		$trsl_mtext [8]= esc_html__('August','woo-tour');
		$trsl_mtext [9]= esc_html__('September','woo-tour');
		$trsl_mtext [10]= esc_html__('October','woo-tour');
		$trsl_mtext [11]= esc_html__('November','woo-tour');
		$trsl_mtext [12]= esc_html__('December','woo-tour');
		$trsl_mtext = str_replace('\/', '/', json_encode($trsl_mtext));
		
		$trsl_dtext [1]= esc_html__('Sun','woo-tour');
		$trsl_dtext [2]= esc_html__('Mon','woo-tour');
		$trsl_dtext [3]= esc_html__('Tue','woo-tour');
		$trsl_dtext [4]= esc_html__('Wed','woo-tour');
		$trsl_dtext [5]= esc_html__('Thu','woo-tour');
		$trsl_dtext [6]= esc_html__('Fri','woo-tour');
		$trsl_dtext [7]= esc_html__('Sat','woo-tour');
		
		$trsl_dtext = str_replace('\/', '/', json_encode($trsl_dtext));
		$wt_firstday = get_option('wt_firstday','7');
		
		$wt_date_label = get_post_meta( get_the_ID(), 'wt_date_label', true ) ;
		$wt_date_label = $wt_date_label!='' ? $wt_date_label.': ' : esc_html__('Departure: ','woo-tour');
		echo '
		<table class="tour-tble date-sl">
		<tbody>
		<td class="label"><label for="' . sanitize_title($wt_date_label) .'">' . $wt_date_label .'</label></td>
		<td class="value">
		<div class="tour-info-select">
			<span class="wt-departure">
				<span>';
					if($arr_ctdate!='' && $wt_show_sdate!='calendar'){
						echo '
						<input type="text" class="wt-custom-date" readonly name="wt_date" value="'.$df_day.'">
						<ul class="wt-list-date">'.$arr_ctdate.'</ul>';
					}else{
						$wt_calendar_lg = get_option('wt_calendar_lg');
						echo '
						<input type="hidden" name="wt_weekday_disable" value='.$arr_diff.'>
						<input type="hidden" name="wt_langu" value='.$wt_calendar_lg.'>
						<input type="hidden" name="wt_date_disable" value='.$arr_disdate.'>
						<input type="hidden" name="wt_cust_date" value='.$arr_ctdate.'>
						<input type="hidden" name="wt_expired" value="'.$wt_expired.'">
						<input type="hidden" name="wt_firstday" value="'.$wt_firstday.'">
						<input type="hidden" name="wt_daytrsl" value='.str_replace(' ', '\u0020', $trsl_dtext).'>
						<input type="hidden" name="wt_montrsl" value='.str_replace(' ', '\u0020', $trsl_mtext).'>
						<input type="text" readonly name="wt_date">';
					}
					echo '
					<i class="fa fa-calendar wt-bticon" aria-hidden="true"></i>
					<input type="hidden" name="wt_ajax_url" value='.esc_url(admin_url( 'admin-ajax.php' )).'>
					<input type="hidden" name="wt_tourid" value='.esc_attr( get_the_ID()).'>
					<input type="hidden" name="wt_sldate" value="">
					<input type="hidden" name="wt_book_before" value="'.$wt_disable_book.'">
				</span>
				<span class="wt-tickets-status"></span>
			</span>
		</div>
		</td>
		</tbody>
		</table>
		';
	}
	// add meta display frontend order
	function display_item_order_meta( $item_id, $item, $order ) {
		$id = $item['product_id'];
		$_date = $item->get_meta('_date');
		$output ='';
		if ( $_date !='' ) {
			$wt_date_label = get_post_meta( $id, 'wt_date_label', true ) ;
			$wt_date_label = $wt_date_label!='' ? $wt_date_label.':' : esc_html__('Departure: ','woo-tour');	
			$output .= '<dl class="variation">' . $wt_date_label .' '. $_date . '</dl>';
		}
		$_adult = $item->get_meta('_adult');
		if ( $_adult!='' ) {
			$wt_adult_label = get_post_meta( $id, 'wt_adult_label', true ) ;
			$wt_adult_label = $wt_adult_label!='' ? $wt_adult_label.':' : esc_html__('Adult: ','woo-tour');
			$output .= '<dl class="variation">' . $wt_adult_label .' '. $_adult . '</dl>';
	
		}
		$_child = $item->get_meta('_child');
		if ( $_child!='' ) {
			$wt_child_label = get_post_meta( $id, 'wt_child_label', true ) ;
			$wt_child_label = $wt_child_label!='' ? $wt_child_label.':' : esc_html__('Children: ','woo-tour');	
			$output .= '<dl class="variation">' . $wt_child_label .' '. $_child . '</dl>';
	
		}
		$_infant = $item->get_meta('_infant');
		if ( $_infant!='' ) {
			$wt_infant_label = get_post_meta( $id, 'wt_infant_label', true ) ;
			$wt_infant_label = $wt_infant_label!='' ? $wt_infant_label.':' : esc_html__('Infant: ','woo-tour');
			$output .= '<dl class="variation">' . $wt_infant_label .' '. $_infant . '</dl>';
		}
		
		$_wtdiscount = $item->get_meta('_wtdiscount');
		if ( $_wtdiscount!='' ) {
			$output .= '<dl class="variation">' . esc_html__('Discount: ','woo-tour') .' '. $_wtdiscount . '</dl>';
		}
		
		echo $output;
	
	}
	// remove user data booking
	function remove_user_data_booking_from_cart($cart_item_key)
	{
		global $woocommerce;
		// Get cart
		$cart = $woocommerce->cart->get_cart();
		// For each item in cart, if item is upsell of deleted product, delete it
		foreach( $cart as $key => $values)
		{
			if ( $values['wdm_user_custom_data_value'] == $cart_item_key )
				unset( $woocommerce->cart->cart_contents[ $key ] );
		}
	}
	

}
$WooTour_Booking = new WooTour_Booking();
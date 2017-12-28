<?php 
class WT_Checkouthook {
	public function __construct()
    {
		if(get_option('wt_disable_attendees') != 'yes'){
			add_action('woocommerce_after_order_notes', array( &$this,'add_user_data_booking'));
			add_action( 'woocommerce_checkout_update_order_meta', array( &$this,'saveto_order_meta'));
			add_action( 'woocommerce_after_order_itemmeta', array( &$this,'show_adminorder_ineach_metadata'), 10, 3 );
			add_action( 'woocommerce_order_item_meta_end', array( &$this,'show_order_ineach_metadata'), 10, 3 );
		}
		add_action( 'woocommerce_before_checkout_process', array( &$this,'verify_checkout'));
		add_action( 'woocommerce_reduce_order_stock', array( &$this,'update_quantity_ofdate'),99 );
		add_action( 'woocommerce_order_status_cancelled', array( &$this,'update_quantity_ifcancel'),99 );
    }
	function verify_checkout(){
		$check_stock = array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			if(isset($values['_date']) && isset($values['_adult']) && isset($values['_metadate'])){
				$crr_qty = $values['_adult']*1;
				if(isset($values['_infant'])){
					$crr_qty = $crr_qty + $values['_infant']*1;
				}
				if(isset($values['_child'])){
					$crr_qty = $crr_qty + $values['_child']*1;
				}
				if(isset($check_stock[$values['product_id']][1]) && $check_stock[$values['product_id']][2] == $values['_metadate'] ){
					$check_stock[$values['product_id']][1] = $check_stock[$values['product_id']][1] + $crr_qty;
				}else{
					$check_stock[$values['product_id']][1] = $crr_qty;
					$check_stock[$values['product_id']][2] = $values['_metadate'];
					$check_stock[$values['product_id']][3] = $values['product_id'];
				}
				
			}
		}
		if(!empty($check_stock)){
			foreach($check_stock as $item){
				$avari = get_post_meta($item[3], $item[2], true);
				if($avari==''){
					$def_stock = get_post_meta($item[3], 'def_stock', true);
					if($def_stock > 0){
						$avari = $def_stock;
					}
				}
				if($avari!='' && ($avari < $item[1])){echo $avari; echo $item[1];
					$title = get_the_title( $item[3] );
					$t_stopb = sprintf(esc_html__('Sorry, "%s" is not enought stock. Please edit your quantity or date of tour. We apologise for any inconvenience caused.', 'woo-tour'),$title);
					wc_add_notice( $t_stopb, 'error' );
					global $woocommerce;
					$woocommerce->cart->empty_cart();
					return;
				}
			}
		}
		//echo '<pre>';print_r($_POST);echo '</pre>';exit;
		if( isset($_POST['wt_ids']) && isset($_POST['wt_quatiny']) && (get_option('wt_disable_attendees') != 'yes') ){
			$wt_attendee_name = get_option('wt_attendee_name');
			$wt_attendee_email = get_option('wt_attendee_email');
			$wt_attendee_birth = get_option('wt_attendee_birth');
			$wt_attendee_gender = get_option('wt_attendee_gender');
			if($wt_attendee_name=='no' && $wt_attendee_email=='no' && $wt_attendee_birth=='no' && $wt_attendee_gender=='no'){
			}else{
				foreach($_POST['wt_ids'] as $item){
					//if ( ! empty( $_POST['wt_if_name'][$item] ) ) {
						for( $i = 0 ; $i < $_POST['wt_quatiny']; $i++){
							if((!isset($_POST['wt_if_name'][$item][$i]) || $_POST['wt_if_name'][$item][$i] =='') && $wt_attendee_name!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_lname'][$item][$i]) || $_POST['wt_if_lname'][$item][$i] =='') && $wt_attendee_name!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_email'][$item][$i]) || $_POST['wt_if_email'][$item][$i] =='') && $wt_attendee_email!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_dd'][$item][$i]) || $_POST['wt_if_dd'][$item][$i] =='') && $wt_attendee_birth!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_mm'][$item][$i]) || $_POST['wt_if_mm'][$item][$i] =='') && $wt_attendee_birth!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_yyyy'][$item][$i]) || $_POST['wt_if_yyyy'][$item][$i] =='') && $wt_attendee_birth!='no' ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
							if((!isset($_POST['wt_if_male'][$item][$i]) || $_POST['wt_if_male'][$item][$i] =='')  && $wt_attendee_gender!='no'  ){
								wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
							}
						}
//					}else{
//						wc_add_notice( esc_html__( 'Please fill info Passenger' ,'woo-tour'), 'error' );
//					}
				}
			}
		}
		
	}
	function add_user_data_booking( $checkout ) {
		$c_it = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$id = $cart_item['product_id'];
			$_product = wc_get_product ($id);
			$wt_main_purpose = wt_global_main_purpose();
			$wt_slayout_purpose = get_option('wt_slayout_purpose');
			$wt_layout_purpose = get_post_meta($id,'wt_layout_purpose',true);
			if( ($wt_main_purpose=='') || ($wt_main_purpose=='tour') || ($wt_main_purpose=='custom' && $wt_layout_purpose=='tour') || ($wt_main_purpose=='meta' && $wt_layout_purpose=='tour') || ($wt_main_purpose=='meta' && $wt_layout_purpose!='woo' && $wt_slayout_purpose=='tour') ){
				$c_it ++;
				if($c_it==1){
					echo '<div class="user_checkout_field"><h3>' . esc_html__('Attendees info','woo-tour') . '</h3>';
				}
				$t_fname = esc_html__('First Name: ','woo-tour');
				$t_lname = esc_html__('Last Name: ','woo-tour');
				$t_email = esc_html__('Email: ','woo-tour');
				echo '<div class="gr-product">';
					if(!isset($cart_item['_adult']) || $cart_item['_adult']==''){
						$cart_item['_adult'] = 0;
					}
					if(!isset($cart_item['_child']) || $cart_item['_child']==''){
						$cart_item['_child'] = 0;
					}
					if(!isset($cart_item['_infant']) || $cart_item['_infant']==''){
						$cart_item['_infant'] = 0;
					}
					$nb_p = $cart_item['_adult'] + $cart_item['_child'] + $cart_item['_infant'];
					
					if ( $_product && $_product->exists() && $nb_p > 0) {
						echo '<h4>('.$c_it.')'. $_product->get_title() . '</h4>';
						echo '<input type="hidden" name="wt_ids[]" value="'.$id.'">';
						echo '<input type="hidden" name="wt_quatiny" value="'.$nb_p.'">';
						echo '<div class="w-product">';
						$year = $month = $day = array();
						$day[''] = 'DD';
						$month[''] = 'MM';
						$year[''] = 'YYYY';
						for($m = 1 ; $m <13; $m++){
							$month[str_pad($m, 2, '0', STR_PAD_LEFT)] = str_pad($m, 2, '0', STR_PAD_LEFT);
						}
						for($d= 1 ; $d <32; $d++){
							$day[str_pad($d, 2, '0', STR_PAD_LEFT)] = str_pad($d, 2, '0', STR_PAD_LEFT);
						}
						$cr_y = date('Y');
						for($y = $cr_y ; $y > 1930 ; $y--){
							$year[$y] = $y;
						}
						
						$wt_attendee_name = get_option('wt_attendee_name')!='no' ? true : false;
						$wt_attendee_email = get_option('wt_attendee_email')!='no' ? true : false;
						$wt_attendee_birth = get_option('wt_attendee_birth')!='no' ? true : false;
						$wt_attendee_gender = get_option('wt_attendee_gender')!='no' ? true : false;
						for($i=0; $i < $nb_p; $i++){
							echo '<div class="wt-passenger-info">
							<p class="pa-lab">'.esc_html__('Passenger','woo-tour').'('.($i+1).')</p>';
								woocommerce_form_field( 
									'wt_if_name['.$id.']['.$i.']', 
									array(
										'type'          => 'text',
										'class'         => array('we-ct-class form-row-wide first-el'),
										'label'         => '',
										'required'  => $wt_attendee_name,
										'placeholder'   => esc_html__('First Name','woo-tour'),
									), 
									''
								);
								woocommerce_form_field( 
									'wt_if_lname['.$id.']['.$i.']', 
									array(
										'type'          => 'text',
										'class'         => array('we-ct-class form-row-wide'),
										'label'         => '',
										'required'  => $wt_attendee_name,
										'placeholder'   => esc_html__('Last Name','woo-tour'),
									), 
									''
								);
								woocommerce_form_field( 'wt_if_email['.$id.']['.$i.']', 
									array(
										'type'          => 'text',
										'class'         => array('we-ct-class form-row-wide'),
										'label'         => '',
										'required'  => $wt_attendee_email,
										'placeholder'   => esc_html__('Email','woo-tour'),
									), 
									''
								);
								woocommerce_form_field( 'wt_if_dd['.$id.']['.$i.']', 
									array(
										'type'          => 'select',
										'class'         => array('we-ct-class form-row-wide first-el'),
										'label'         => esc_html__('Date of birth','woo-tour'),
										'required'  => $wt_attendee_birth,
										'placeholder'   => '',
										'options' => $day,
									), 
									''
								);
								woocommerce_form_field( 'wt_if_mm['.$id.']['.$i.']', 
									array(
										'type'          => 'select',
										'class'         => array('we-ct-class form-row-wide'),
										'label'         => '',
										'required'  => $wt_attendee_birth,
										'placeholder'   => '',
										'options' => $month,
									), 
									''
								);
								woocommerce_form_field( 'wt_if_yyyy['.$id.']['.$i.']', 
									array(
										'type'          => 'select',
										'class'         => array('we-ct-class form-row-wide'),
										'label'         => '',
										'required'  => $wt_attendee_birth,
										'placeholder'   => '',
										'options' => $year,
									), 
									''
								);
								woocommerce_form_field( 'wt_if_male['.$id.']['.$i.']', 
									array(
										'type'          => 'select',
										'class'         => array('we-ct-class form-row-wide first-el wt-ged'),
										'label'         => esc_html__('Gender','woo-tour'),
										'required'  => $wt_attendee_gender,
										'placeholder'   => '',
										'options' => array(
											'' => esc_html__('Select','woo-tour'), 
											'male'=>esc_html__('Male','woo-tour'), 
											'female'=>esc_html__('Female','woo-tour'), 
											'other' => esc_html__('Other','woo-tour')
										),
									), 
									''
								);
								do_action( 'wt_after_custom_field', $id, $i );
							echo '</div>';
						}
						echo '</div>';
					}
				echo '</div>';
				if($c_it==1){
					echo '</div>';
				}
			}
		}
	
	}
	function saveto_order_meta( $order_id ) {
		if ( ! empty( $_POST['wt_ids'] ) ) {
			foreach($_POST['wt_ids'] as $item){
				if ( ! empty( $_POST['wt_if_name'][$item] ) ) {
					$nl_meta = '';
					$other_meta = '';
					$nbid= count($_POST['wt_if_name'][$item]);
					for( $i = 0 ; $i < $nbid; $i++){
						$name = sanitize_text_field( $_POST['wt_if_name'][$item][$i] );
						$lname = sanitize_text_field( $_POST['wt_if_lname'][$item][$i] );
						$email = sanitize_text_field( $_POST['wt_if_email'][$item][$i] );
						
						$dd = sanitize_text_field( $_POST['wt_if_dd'][$item][$i] );
						$mm = sanitize_text_field( $_POST['wt_if_mm'][$item][$i] );
						$yy = sanitize_text_field( $_POST['wt_if_yyyy'][$item][$i] );
						$bir_day = $dd.' '.$mm.' '.$yy;
						$male = sanitize_text_field( $_POST['wt_if_male'][$item][$i] );
						if($nl_meta!=''){
							$nl_meta = $nl_meta.']['.$email.'||'.$name.'||'.$lname.'||'.$bir_day.'||'.$male;
						}else{
							$nl_meta = $email.'||'.$name.'||'.$lname.'||'.$bir_day.'||'.$male;
						}
						$nl_meta = apply_filters( 'wt_custom_field_extract', $nl_meta, $_POST, $item, $i );
					}
					
					update_post_meta( $order_id, 'att_info-'.$item, $nl_meta );
				}
			}
		}
	}
	function show_adminorder_ineach_metadata($item_id, $item, $_product){
		$id = $item['product_id'];
		$metadata = get_post_meta($_GET['post'],'att_info-'.$id, true);
		if($metadata !=''){
			$metadata = explode("][",$metadata);
			if(!empty($metadata)){
				$i=0;
				foreach($metadata as $item){
					$i++;
					$item = explode("||",$item);
					$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
					$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
					$bir_day = isset($item[3]) && $item[3]!='' ? $item[3] : '';
					$male = isset($item[4]) && $item[4]!='' ? $item[4] : '';
					echo '<div class="we-user-info">'.esc_html__('Attendees info','woo-tour').' ('.$i.') <br>';
					echo  $f_name!='' && $l_name!='' ? '<span><b>'.esc_html__(' Name: ','woo-tour').'</b>'.$f_name.' '.$l_name.'</span><br>' : '';
					echo  isset($item[0]) && $item[0]!='' ? '<span><b>'.esc_html__(' Email: ','woo-tour').' </b>'.$item[0].'</span><br>' : '';
					do_action( 'wt_after_order_info', $item);
					echo '</div>';
				}
			}
		}
	}
	
	function show_order_ineach_metadata($item_id, $item, $order){
		$id = $item['product_id'];
		$metadata = get_post_meta($order->get_id(),'att_info-'.$id, true);
		if($metadata !=''){
						
			$metadata = explode("][",$metadata);
			if(!empty($metadata)){
				$i=0;
				foreach($metadata as $item){
					$i++;
					$item = explode("||",$item);
					$f_name = isset($item[1]) && $item[1]!='' ? $item[1] : '';
					$l_name = isset($item[2]) && $item[2]!='' ? $item[2] : '';
					$bir_day = isset($item[3]) && $item[3]!='' ? $item[3] : '';
					$male = isset($item[4]) && $item[4]!='' ? $item[4] : '';
					echo '<div class="we-user-info">'.esc_html__('Attendees info','woo-tour').' ('.$i.') <br>';
					echo  $f_name!='' && $l_name!='' ? '<span><b>'.esc_html__('Name: ','woo-tour').'</b>'.$f_name.' '.$l_name.'</span><br>' : '';
					echo  isset($item[0]) && $item[0]!='' ? '<span><b>'.esc_html__('Email: ','woo-tour').' </b>'.$item[0].'</span><br>' : '';
					echo  $bir_day!='' ? '<span><b>'.esc_html__('Date of birth: ','woo-tour').'</b>'.$bir_day.'</span><br>' : '';
					echo  $male!='' ? '<span><b>'.esc_html__('Gender: ','woo-tour').'</b>'.$male.'</span><br>' : '';
					do_action( 'wt_after_order_info', $item);
					echo '</div>';
				}
			}
		}
	}
	// update quantity
	function update_quantity_ofdate( $order ){
		//$order = new WC_Order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			$metadate = isset( $item['metadate']) ? $item['metadate'] : '';
			if($product_id!='' && $metadate !=''){
				$avari = get_post_meta($product_id, $metadate, true);
				if($avari==''){
					$def_stock = get_post_meta($product_id, 'def_stock', true);
					if($def_stock > 0){
						$avari = $def_stock;
					}
				}
				if($avari!='' && ($avari > 0)){
					if(isset($item['adult'])){
						$ud_qty = $item['adult']*1;
					}
					if(isset($item['infant'])){
						$ud_qty = $ud_qty + $item['infant']*1;
					}
					if(isset($item['child'])){
						$ud_qty = $ud_qty + $item['child']*1;
					}
					if($avari > $ud_qty){
						$avari = $avari - $ud_qty;
					}else{
						$avari = 0;
					}
					update_post_meta( $product_id, $metadate, $avari);
				}
			}
			
		}
	}
	function update_quantity_ifcancel( $order_id ){
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item['product_id'];
			$metadate = isset( $item['metadate']) ? $item['metadate'] : '';
			if($product_id!='' && $metadate !=''){
				$avari = get_post_meta($product_id, $metadate, true);
				if($avari==''){
					$def_stock = get_post_meta($product_id, 'def_stock', true);
					if($def_stock > 0){
						$avari = $def_stock;
					}
				}
				if($avari!='' && ($avari > 0)){
					if(isset($item['adult'])){
						$ud_qty = $item['adult']*1;
					}
					if(isset($item['infant'])){
						$ud_qty = $ud_qty + $item['infant']*1;
					}
					if(isset($item['child'])){
						$ud_qty = $ud_qty + $item['child']*1;
					}
					$avari = $avari + $ud_qty;
					update_post_meta( $product_id, $metadate, $avari);
				}
			}
			
		}
	}
	
	
}
$WT_Checkouthook = new WT_Checkouthook();
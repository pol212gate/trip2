<?php
if(!function_exists('wt_startsWith')){
	function wt_startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}
} 
if(!function_exists('wt_get_google_fonts_url')){
	function wt_get_google_fonts_url ($font_names) {
	
		$font_url = '';
	
		$font_url = add_query_arg( 'family', urlencode(implode('|', $font_names)) , "//fonts.googleapis.com/css" );
		return $font_url;
	} 
}
if(!function_exists('wt_get_google_font_name')){
	function wt_get_google_font_name($family_name){
		$name = $family_name;
		if(wt_startsWith($family_name, 'http')){
			// $family_name is a full link, so first, we need to cut off the link
			$idx = strpos($name,'=');
			if($idx > -1){
				$name = substr($name, $idx);
			}
		}
		$idx = strpos($name,':');
		if($idx > -1){
			$name = substr($name, 0, $idx);
			$name = str_replace('+',' ', $name);
		}
		return $name;
	}
}


function wt_filter_wc_get_template_single($template, $slug, $name){
	if($slug=='content' && $name =='single-product'){
		return wootour_template_plugin('single-product');
	}else{ 
		return $template;
	}
}
function filter_wc_get_template_shop($template, $slug, $name){
	if($slug=='content' && $name =='product'){
		return wootour_template_plugin('product');
	}else{ 
		return $template;
	}
}
function wt_filter_wc_get_template_related($located, $template_name, $args){
	if($template_name =='single-product/related.php'){
		if (locate_template('woo-tour/related.php') != '') {
			return get_template_part('woo-tour/related');
		} else {
			return wt_get_plugin_url().'templates/related.php';
		}
	}else{ 
		return $located;
	}
}

function wt_filter_wc_get_template_variation($located, $template_name, $args){
	if($template_name =='single-product/add-to-cart/variation.php'){
		if (locate_template('woo-tour/variation.php') != '') {
			return get_template_part('woo-tour/variation');
		} else {
			return wt_get_plugin_url().'templates/variation.php';
		}
	}else{ 
		return $located;
	}
}

$wt_main_purpose = get_option('wt_main_purpose');
add_filter( 'wc_get_template', 'wt_filter_wc_get_template_variation', 99, 3 );
if($wt_main_purpose!='meta'){
	add_filter( 'wc_get_template_part', 'wt_filter_wc_get_template_single', 10, 3 );
	add_filter( 'wc_get_template_part', 'filter_wc_get_template_shop', 99, 3 );
	if($wt_main_purpose=='custom'){
		add_filter( 'wc_get_template', 'wt_filter_wc_get_template_related', 99, 3 );
	}

}
// Change number or products per row to 3
if(!function_exists('wootour_template_plugin')){
	function wootour_template_plugin($pageName,$shortcode=false){
		if(isset($shortcode) && $shortcode== true){
			if (locate_template('woo-tour/content-shortcode/content-' . $pageName . '.php') != '') {
				get_template_part('woo-tour/content-shortcode/content', $pageName);
			} else {
				include wt_get_plugin_url().'shortcode/content/content-' . $pageName . '.php';
			}

		}else{
			if (locate_template('woo-tour/content-' . $pageName . '.php') != '') {
				get_template_part('woo-tour/content', $pageName);
			} else {
				include wt_get_plugin_url().'templates/content-' . $pageName . '.php';
			}
		}
	}
}
//
if(!function_exists('wt_taxonomy_info')){
	function wt_taxonomy_info( $tax, $link=false, $id= false){
		if(isset($id) && $id!=''){
			$product_id = $id;
		}else{
			$product_id = get_the_ID();
		}
		$post_type = 'product';
		ob_start();
		if(isset($tax) && $tax!=''){
			$args = array(
				'hide_empty'        => false, 
			);
			$terms = wp_get_post_terms($product_id, $tax, $args);
			if(!empty($terms) && !is_wp_error( $terms )){
				$c_tax = count($terms);
				$i=0;
				foreach ( $terms as $term ) {
					$i++;
					if(isset($link) && $link=='off'){
						echo $term->name;
					}else{
						echo '<a href="'.get_term_link( $term ).'" title="' . $term->name . '">'. $term->name .'</a>';
					}
					if($i != $c_tax){ echo '<span>, </span>';}
				}
			}
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
// Get has purchased
function wt_get_all_products_ordered_by_user() {
    $orders = wt_get_all_user_orders(get_current_user_id(), 'completed');
    if(empty($orders)) {
        return false;
    }
    $order_list = '(' . join(',', $orders) . ')';//let us make a list for query
    //so, we have all the orders made by this user that were completed.
    //we need to find the products in these orders and make sure they are downloadable.
    global $wpdb;
    $query_select_order_items = "SELECT order_item_id as id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id IN {$order_list}";
    $query_select_product_ids = "SELECT meta_value as product_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key=%s AND order_item_id IN ($query_select_order_items)";
    $products = $wpdb->get_col($wpdb->prepare($query_select_product_ids, '_product_id'));
    return $products;
}
function wt_get_all_user_orders($user_id, $status = 'completed') {
    if(!$user_id) {
        return false;
    }
    $args = array(
        'numberposts' => -1,
        'meta_key' => '_customer_user',
        'meta_value' => $user_id,
        'post_type' => 'shop_order',
        'post_status' => array( 'wc-completed' )
        
    );
    $posts = get_posts($args);
    //get the post ids as order ids
    return wp_list_pluck($posts, 'ID');
}
// Query function
if(!function_exists('woo_tour_query')){
	function woo_tour_query($posttype, $count, $order, $orderby, $cat, $tag, $ids,$page=false,$data_qr=false, $location=false,$meta_key = false, $meta_value=false){
		if($orderby=='has_signed_up'){
			if(get_current_user_id()){
				$ids = wt_get_all_products_ordered_by_user(); 
				if($ids ==''){$ids = '-1';}
			}else{
				$ids = '-1';
			}
		}
		if($orderby=='sale'){
			$ids = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}
		if($ids!='' || (is_array($ids) && !empty($ids))){ //specify IDs
			if(!is_array($ids)){
				$ids = explode(",", $ids);
			}
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'post__in' =>  $ids,
				'order' => $order,
				'orderby' => $orderby,
				'ignore_sticky_posts' => 1,
			);
		}elseif($ids==''){
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => 'publish',
				'order' => $order,
				'orderby' => $orderby,
				'ignore_sticky_posts' => 1,
			);
			if($tag!=''){
				$tags = explode(",",$tag);
				if(is_numeric($tags[0])){$field_tag = 'term_id'; }
				else{ $field_tag = 'slug'; }
				if(count($tags)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($tags as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => 'product_tag',
								  'field' => $field_tag,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => 'product_tag',
								  'field' => $field_tag,
								  'terms' => $tags,
							  )
					  );
				}
			}
			//cats
			if($cat!=''){
				$cats = explode(",",$cat);
				if(is_numeric($cats[0])){$field = 'term_id'; }
				else{ $field = 'slug'; }
				if(count($cats)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($cats as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => 'product_cat',
								  'field' => $field,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => 'product_cat',
								  'field' => $field,
								  'terms' => $cats,
							  )
					  );
				}
			}
			//location
			if($location!=''){
				$locations = explode(",",$location);
				if(is_numeric($locations[0])){$field = 'term_id'; }
				else{ $field = 'slug'; }
				if(count($locations)>1){
					  $texo = array(
						  'relation' => 'OR',
					  );
					  foreach($locations as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => 'wt_location',
								  'field' => $field,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo = array(
						  array(
								  'taxonomy' => 'wt_location',
								  'field' => $field,
								  'terms' => $locations,
							  )
					  );
				}
			}
			if(isset($texo)){
				$args += array('tax_query' => $texo);
			}
			if(isset($data_qr) && $data_qr!='' && is_numeric($data_qr)){
				$args['meta_query'] = array (
					 array(
						'key' => 'wt_speakers',
						'value' => $data_qr,
						'compare' => 'LIKE'
					)
				);
			}
			$cure_time =  strtotime("now");
			if($orderby=='unexpired'){
				if($order==''){$order='ASC';}
				$args += array('meta_key' => 'wt_expired', 'meta_value' => $cure_time, 'meta_compare' => '>');
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='has_expired'){
				if($order==''){$order='DESC';}
				$args += array('meta_key' => 'wt_expired', 'meta_value' => $cure_time, 'meta_compare' => '<');
				$args['orderby']= 'meta_value_num';
				$args['order']= $order;
			}elseif($orderby=='featured'){
				if(!empty($args['meta_query'])){
					$args['meta_query']['relation'] = 'AND';
				}
				$args['orderby']= '';
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				$args['tax_query'] = $tax_query;
			}
		}
		if(isset($meta_key) && $meta_key!=''){
			$args['meta_key'] = $meta_key;
		}
		if(isset($meta_value) && $meta_value!='' && $meta_key!=''){
			if(!empty($args['meta_query'])){
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = array(
				'key'  => $meta_key,
				'value' => $meta_value,
				'compare' => '='
			);
		}
		if(isset($page) && $page!=''){
			$args['paged'] = $page;
		}
//		echo '<pre>';
//		print_r($args);
//		echo '</pre>';
		return $args;
	}
}
//Status
if(!function_exists('woo_tour_status')){
	function woo_tour_status( $post_id, $wt_enddate=false){
		return '';
	}
}
//
if(!function_exists('wt_social_share')){
	function wt_social_share( $id = false){
		$id = get_the_ID();
		$tl_share_button = array('fb','tw','li','tb','gg','pin','vk','em',);
		ob_start();
		if(is_array($tl_share_button) && !empty($tl_share_button)){
			?>
			<ul class="wootour-social-share">
				<?php if(in_array('fb', $tl_share_button)){ ?>
					<li class="facebook">
						<a class="trasition-all" title="<?php esc_html_e('Share on Facebook','woo-tour');?>" href="#" target="_blank" rel="nofollow" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+'<?php echo urlencode(get_permalink($id)); ?>','facebook-share-dialog','width=626,height=436');return false;"><i class="fa fa-facebook"></i>
						</a>
					</li>
				<?php }
	
				if(in_array('tw', $tl_share_button)){ ?>
					<li class="twitter">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Share on Twitter','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('http://twitter.com/share?text=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;url=<?php echo urlencode(get_permalink($id)); ?>','twitter-share-dialog','width=626,height=436');return false;"><i class="fa fa-twitter"></i>
						</a>
					</li>
				<?php }
	
				if(in_array('li', $tl_share_button)){ ?>
						<li class="linkedin">
							<a class="trasition-all" href="#" title="<?php esc_html_e('Share on LinkedIn','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(get_permalink($id)); ?>&amp;title=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;source=<?php echo urlencode(get_bloginfo('name')); ?>','linkedin-share-dialog','width=626,height=436');return false;"><i class="fa fa-linkedin"></i>
							</a>
						</li>
				<?php }
	
				if(in_array('tb', $tl_share_button)){ ?>
					<li class="tumblr">
					   <a class="trasition-all" href="#" title="<?php esc_html_e('Share on Tumblr','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink($id)); ?>&amp;name=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>','tumblr-share-dialog','width=626,height=436');return false;"><i class="fa fa-tumblr"></i>
					   </a>
					</li>
				<?php }
	
				if(in_array('gg', $tl_share_button)){ ?>
					 <li class="google-plus">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Share on Google Plus','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('https://plus.google.com/share?url=<?php echo urlencode(get_permalink($id)); ?>','googleplus-share-dialog','width=626,height=436');return false;"><i class="fa fa-google-plus"></i>
						</a>
					 </li>
				 <?php }
	
				 if(in_array('pin', $tl_share_button)){ ?>
					 <li class="pinterest">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Pin this','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('//pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($id)) ?>&amp;media=<?php echo urlencode(wp_get_attachment_url( get_post_thumbnail_id($id))); ?>&amp;description=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>','pin-share-dialog','width=626,height=436');return false;"><i class="fa fa-pinterest"></i>
						</a>
					 </li>
				 <?php }
				 
				 if(in_array('vk', $tl_share_button)){ ?>
					 <li class="vk">
						<a class="trasition-all" href="#" title="<?php esc_html_e('Share on VK','woo-tour');?>" rel="nofollow" target="_blank" onclick="window.open('//vkontakte.ru/share.php?url=<?php echo urlencode(get_permalink(get_the_ID())); ?>','vk-share-dialog','width=626,height=436');return false;"><i class="fa fa-vk"></i>
						</a>
					 </li>
				 <?php }
	
				 if(in_array('em', $tl_share_button)){ ?>
					<li class="email">
						<a class="trasition-all" href="mailto:?subject=<?php echo urlencode(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8')); ?>&amp;body=<?php echo urlencode(get_permalink($id)) ?>" title="<?php esc_html_e('Email this','woo-tour');?>"><i class="fa fa-envelope"></i>
						</a>
					</li>
				<?php }?>
			</ul>
			<?php
		}
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}
}
//Global function
function wootour_global_layout(){
	if(is_singular('product')){
		global $layout,$post;
		if(isset($layout) && $layout!=''){
			return $layout;
		}
		$layout = get_post_meta( $post->ID, 'wt_layout', true );
		if($layout ==''){
			$layout = get_option('wt_slayout');
		}
		return $layout;
		}
}
function wt_global_expireddate(){
	global $wt_enddate, $post;
	if(isset($wt_enddate) && $wt_enddate!='' && is_main_query() && is_singular('product')){
		return $wt_enddate;
	}
	$wt_enddate = get_post_meta( $post->ID, 'wt_expired', true ) ;
	if($wt_enddate!=''){
		$wt_enddate = $wt_enddate + 86399;
	}
	return $wt_enddate;
}
function wt_global_main_purpose(){
	$wt_main_purpose = get_option('wt_main_purpose');
	return $wt_main_purpose;
}
//Ajax grid
add_action( 'wp_ajax_ex_loadmore_grid', 'ajax_ex_loadmore_grid' );
add_action( 'wp_ajax_nopriv_ex_loadmore_grid', 'ajax_ex_loadmore_grid' );

function ajax_ex_loadmore_grid(){
	global $columns,$number_excerpt;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$columns = $atts['columns']	=  isset($atts['columns']) ? $atts['columns'] : 1;
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$page = $_POST['page'];
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	//echo '<pre>';
	//print_r($param_query);//exit;
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		$i =0;
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			wootour_template_plugin('grid', true);
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
	}
	$html = ob_get_clean();
	echo  $html;
	die;
}
//table load
add_action( 'wp_ajax_ex_loadmore_table', 'ajax_ex_loadmore_table' );
add_action( 'wp_ajax_nopriv_ex_loadmore_table', 'ajax_ex_loadmore_table' );

function ajax_ex_loadmore_table(){
	global $style;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$style =  isset($atts['style']) ? $atts['style'] :'';
	$count =  isset($atts['count']) ? $atts['count'] :'6';
	$posts_per_page =  isset($atts['posts_per_page']) ? $atts['posts_per_page'] :'';
	$page = $_POST['page'];
	$style =  isset($atts['style']) ? $atts['style'] :'';
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	global $ajax_load;
	if($the_query->have_posts()){
		while($the_query->have_posts()){ $the_query->the_post();
			$ajax_load =1;
			wootour_template_plugin('table', true);
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
	}
	$html = ob_get_clean();
	echo  $html;
	die;
}
//variable_price
if(!function_exists('wt_variable_price_html')){
	function wt_variable_price_html(){
		$price_html = get_post_meta(get_the_ID(),'_min_variation_price', true );
		$fromtrsl = esc_html__('From  ','woo-tour');
		global $product; 
		return $fromtrsl.woocommerce_price($product->get_variation_price('min'));
		
		return $price;
	}
}
if(!function_exists('wt_price_currency')){
	function wt_price_currency($price){
		$currency_pos = get_option( 'woocommerce_currency_pos' );
		if($currency_pos=='left' || $currency_pos==''){ 
			$price = get_woocommerce_currency_symbol().$price; 
		}else if($currency_pos=='left_space'){ 
			$price = get_woocommerce_currency_symbol().' '.$price;
		}elseif($currency_pos=='right'){ 
			$price = $price.get_woocommerce_currency_symbol();
		}else if($currency_pos=='right_space'){ 
			$price = $price.' '.get_woocommerce_currency_symbol();
		}
		return $price;
	}
}
if(!function_exists('wt_addition_price_html')){
	function wt_addition_price_html($price,$span=true,$sale_price=false){
		if($price==''){ return;}
		if(apply_filters ('wt_price_child_infant_ac',$price) != $price ){
			return apply_filters ('wt_price_child_infant_ac',$price);
		}
		if(isset($sale_price) && $sale_price > 0){
			$sale_price ='<span class="wt-tprice">'.$sale_price.'</span>';
			$price = '<del>'.wt_price_currency($price).'</del>'.wt_price_currency($sale_price);
		}else{
			if(isset($span) && $span=='1'){ $price ='<span class="wt-tprice">'.$price.'</span>';}
			$price = '<span>'.wt_price_currency($price).'</span>';
		}
		$price = '<span>'.$price.'</span>';
		$price = apply_filters( 'wt_child_infant_price', $price );
		return $price;
	}
}
if(!function_exists('wt_meta_html')){
	function wt_meta_html(){
		$html ='';
		global $post;
		$wt_duration = get_post_meta( $post->ID, 'wt_duration', true ) ;
		if($wt_duration!=''){
		$html .='
			<span>
				<i class="fa fa-clock-o" aria-hidden="true"></i>
				'.$wt_duration.'
			</span>';
		}
		$wt_transport = get_post_meta( $post->ID, 'wt_transport', true ) ;
		if($wt_transport!=''){
		$html .='
			<span>
				<i class="fa fa-paper-plane" aria-hidden="true"></i>
				'.$wt_transport.'
			</span>';
		}
		
		return $html;
	}
}
add_filter( 'body_class', 'wt_custom_class' );
if(!function_exists('wt_custom_class')){
	function wt_custom_class( $classes ) {
		if(is_singular('product')){
			global $wp_query ;
			$post_id = '';
			if(isset($wp_query->queried_object_id)){
				$post_id = $wp_query->queried_object_id;
			}
			$wt_main_purpose = get_option('wt_main_purpose');
			if($wt_main_purpose=='meta'){
				$classes[] = 'wt-mode-meta';
				if($post_id !=''){
					$def_sg = get_option('wt_slayout_purpose');
					$s_layout = get_post_meta( $post_id, 'wt_layout_purpose', true ) ;
					if($def_sg=='tour' && $s_layout!='woo' || $def_sg=='woo' && $s_layout=='tour'){
						$classes[] = 'wt-hide-quantiny';
					}
				}
			}elseif($wt_main_purpose=='custom'){
				$classes[] = 'wt-mode-custom';
				if($post_id !=''){
					$s_layout = get_post_meta( $post_id, 'wt_layout_purpose', true ) ;
					if($s_layout=='tour'){
						$classes[] = 'wt-hide-quantiny';
					}
				}
			}else{
				$classes[] = 'wt-mode-tour wt-hide-quantiny';
			}
		}
		return $classes;
	}
}
if(!function_exists('wt_onsale_check')){
	function wt_onsale_check (){
		global $product;
		if ( $product->is_on_sale() ) {
			echo '<span class="woocommerce-wt-onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>';
		}else {
			if(function_exists('wc_get_rating_html')){
				$rating_html = wc_get_rating_html($product->get_average_rating());
			}else{
				$rating_html = $product->get_rating_html();
			}
			if ( get_option( 'woocommerce_enable_review_rating' ) != 'no' && $rating_html){
					echo '<div class="woocommerce-wt-onsale woocommerce">'.$rating_html.'</div>';
			}
		}
	}
}


//Add info to pdf invoice
add_action( 'wpo_wcpdf_after_item_meta', 'wooevents_add_event_meta', 10, 3 );
function wooevents_add_event_meta ($template_type, $item, $order) {
	$location = wt_taxonomy_info('wt_location','',$item['product_id']);
	$html ='<dl class="meta">'.esc_html__('Location: ','woo-tour').$location.'</dl>';
	// user info
	$metadata = get_post_meta($order-> id,'att_info-'.$item['product_id'], true);
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
				  
				  $html .= '<dl class="we-user-info">'.esc_html__('User ','woo-tour').'('.$i.') ';
				  $html .=  $f_name!='' && $l_name!='' ? '<p><b>'.esc_html__('Name: ','woo-tour').'</b>'.$f_name.' '.$l_name.'</p>' : '';
				  $html .=  isset($item[0]) && $item[0]!='' ?  '<p><b>'.esc_html__('Email: ','woo-tour').'</b>'.$item[0].'</p>' : '';
				  $html .=  $bir_day!='' ? '<p><b>'.esc_html__('Date of birth: ','woo-tour').'</b>'.$bir_day.'</p>' : '';
				  $html .=  $male!='' ? '<p><b>'.esc_html__('Gender: ','woo-tour').'</b>'.$male.'</p>' : '';
				  $html .= '</dl>';
			  }
		  }
	  }
	
	
	echo $html;
}
add_filter( 'body_class', 'wt_add_ct_class');
function wt_add_ct_class( $classes ) {
	$wt_dbclss = 'wt-mode';
	$purpose = get_option('wt_main_purpose');
	if(get_option(('wt_disable_quantity') != 'yes' && $purpose == 'meta') || $purpose == 'custom'){
		$wt_dbclss = 'wt-unremove-qtn';
	}
	return array_merge( $classes, array( $wt_dbclss ) );
}
// Check ticket status
add_action( 'wp_ajax_wt_check_ticket_status', 'wt_check_ticket_status' );
add_action( 'wp_ajax_nopriv_wt_check_ticket_status', 'wt_check_ticket_status' );

function wt_check_ticket_status(){
	$wt_sldate = isset($_POST['wt_sldate']) ? $_POST['wt_sldate'] : '';
	$wt_tourid = isset($_POST['wt_tourid']) ? $_POST['wt_tourid'] : '';
	$avari = get_post_meta($wt_tourid, $wt_sldate, true);
	if($avari > 0){
		$msg = $avari.' '.esc_html__('Available','woo-tour');
	}elseif($avari!='' && $avari == 0){
		$msg = esc_html__('Sorry, No ticket available at this date','woo-tour');
	}else{
		$def_stock = get_post_meta($wt_tourid, 'def_stock', true);
		if($def_stock > 0){
			$avari = $def_stock;
			$msg = $avari.' '.esc_html__('Available','woo-tour');
		}
	}
	$output =  array('status'=>$avari,'massage'=> $msg);
	echo str_replace('\/', '/', json_encode($output));
	die;
}
if(!function_exists('we_quantity_html')){
	function we_quantity_html($name, $option, $value){
		$wt_type_qunatity = get_option( 'wt_type_qunatity' ) ;
		if($wt_type_qunatity=='text'){
			$html = '
			<div class="wt-quantity">
				<input type="button" value="-" id="wtminus_ticket" class="minus" />
				<input type="text" class="wt-qf" name="'.$name.'" value="'.$value.'">
				<input type="button" value="+" id="wtadd_ticket" class="plus" />
			</div>';
		}else{
			$html = '<select name="'.$name.'">'.$option.'</select>';
		}
		return $html;
	}
}
if(!function_exists('we_table_variation_html')){
	function we_table_variation_html($price, $label, $class){
		$html = '
		<table class="tour-tble">
			<tbody>
				<tr>
					<td>
						<div class="woocommerce-variation-'.esc_attr($class).'">
							'.$price.'
						</div>
					</td>
					<td>'.$label.'</td>
				</tr>
			</tbody>	
		</table>
		';
		
		return $html;
	}
}

if(!function_exists('wt_get_price')){
	function wt_get_price($id, $meta){
		if(get_post_meta( $id, $meta.'_sale', true )!=''){
			$price = get_post_meta( $id, $meta.'_sale', true );
		}else{
			$price = get_post_meta( $id, $meta, true );
		}
		return $price;
	}
}

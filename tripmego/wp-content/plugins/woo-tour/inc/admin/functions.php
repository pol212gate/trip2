<?php
function wt_custom_admin_css() {
	$we_layout_purpose = get_option('wt_slayout_purpose');
	$wt_main_purpose = get_option('wt_main_purpose');
	if(($we_layout_purpose != 'tour' && $wt_main_purpose !='tour' ) || $wt_main_purpose =='custom'){
		if($wt_main_purpose ==''){ return;}
		echo '<style>
		.post-type-product .postbox-container #time-settings.postbox,
		.post-type-product .postbox-container #tour-info.postbox,
		.post-type-product .postbox-container #additional-information.postbox,
		.post-type-product .postbox-container #layout-settings.postbox,
		.post-type-product .postbox-container #custom-field.postbox{display: none;}
		</style>';
	}
}
add_action( 'admin_head', 'wt_custom_admin_css' );
function wt_get_product_to_duplicate( $id ) {
	global $wpdb;
	
	$id = absint( $id );
	
	if ( ! $id ) {
		return false;
	}
	
	$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
	
	if ( isset( $post->post_type ) && $post->post_type == "revision" ) {
		$id   = $post->post_parent;
		$post = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID=$id" );
	}
	
	return $post[0];
}
function wt_duplicate_post_taxonomies( $id, $new_id, $post_type ) {
	$exclude    = array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_taxonomies', array() ) );
	$taxonomies = array_diff( get_object_taxonomies( $post_type ), $exclude );

	foreach ( $taxonomies as $taxonomy ) {
		$post_terms       = wp_get_object_terms( $id, $taxonomy );
		$post_terms_count = sizeof( $post_terms );

		for ( $i = 0; $i < $post_terms_count; $i++ ) {
			wp_set_object_terms( $new_id, $post_terms[ $i ]->slug, $taxonomy, true );
		}
	}
}

/**
 * Copy the meta information of a post to another post.
 *
 * @param mixed $id
 * @param mixed $new_id
 */
function wt_duplicate_post_meta( $id, $new_id ) {
	global $wpdb;

	$sql     = $wpdb->prepare( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", absint( $id ) );
	$exclude = array_map( 'esc_sql', array_filter( apply_filters( 'woocommerce_duplicate_product_exclude_meta', array( 'total_sales' ) ) ) );

	if ( sizeof( $exclude ) ) {
		$sql .= " AND meta_key NOT IN ( '" . implode( "','", $exclude ) . "' )";
	}

	$post_meta = $wpdb->get_results( $sql );

	if ( sizeof( $post_meta ) ) {
		$sql_query_sel = array();
		$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

		foreach ( $post_meta as $post_meta_row ) {
			$sql_query_sel[] = $wpdb->prepare( "SELECT %d, %s, %s", $new_id, $post_meta_row->meta_key, $post_meta_row->meta_value );
		}

		$sql_query .= implode( " UNION ALL ", $sql_query_sel );
		$wpdb->query( $sql_query );
	}
}
function wt_duplicate_product( $post, $parent = 0, $post_status = '' ) {
	global $wpdb;

	$new_post_author    = wp_get_current_user();
	$new_post_date      = current_time( 'mysql' );
	$new_post_date_gmt  = get_gmt_from_date( $new_post_date );

	if ( $parent > 0 ) {
		$post_parent        = $parent;
		$post_status        = $post_status ? $post_status : 'publish';
	} else {
		$post_parent        = $post->post_parent;
		$post_status        = $post_status ? $post_status : 'draft';
	}

	// Insert the new template in the post table
	$wpdb->insert(
		$wpdb->posts,
		array(
			'post_author'               => $new_post_author->ID,
			'post_date'                 => $new_post_date,
			'post_date_gmt'             => $new_post_date_gmt,
			'post_content'              => $post->post_content,
			'post_content_filtered'     => $post->post_content_filtered,
			'post_title'                => $post->post_title,
			'post_excerpt'              => $post->post_excerpt,
			'post_status'               => $post_status,
			'post_type'                 => $post->post_type,
			'comment_status'            => $post->comment_status,
			'ping_status'               => $post->ping_status,
			'post_password'             => $post->post_password,
			'to_ping'                   => $post->to_ping,
			'pinged'                    => $post->pinged,
			'post_modified'             => $new_post_date,
			'post_modified_gmt'         => $new_post_date_gmt,
			'post_parent'               => $post_parent,
			'menu_order'                => $post->menu_order,
			'post_mime_type'            => $post->post_mime_type
		)
	);

	$new_post_id = $wpdb->insert_id;

	// Copy the taxonomies
	wt_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

	// Copy the meta information
	wt_duplicate_post_meta( $post->ID, $new_post_id );

	// Copy the children (variations)
	if ( ( $children_products = get_children( 'post_parent=' . $post->ID . '&post_type=product_variation' ) ) ) {
		foreach ( $children_products as $child ) {
			wt_duplicate_product( wt_get_product_to_duplicate( $child->ID ), $new_post_id, $child->post_status );
		}
	}

	return $new_post_id;
}
function woometa_update($_post_e,$wt_ID,$post_id){
	if(isset($_post_e['_downloadable'])){
		update_post_meta($wt_ID, '_downloadable', $_post_e['_downloadable']);
	}
	if(isset($_POST['_virtual'])){
		update_post_meta($wt_ID, '_virtual', $_post_e['_virtual']);
	}
	update_post_meta($wt_ID, '_visibility', $_post_e['_visibility']);
	update_post_meta($wt_ID, '_stock_status', $_post_e['_stock_status']);
	update_post_meta( $wt_ID, '_visibility', 'visible' );
    update_post_meta( $wt_ID, '_stock_status', 'instock');
	update_post_meta($wt_ID, '_regular_price', $_post_e['_regular_price']);
	update_post_meta($wt_ID, '_sale_price', $_post_e['_sale_price']);
	if($_post_e['_sale_price']==''){
		update_post_meta( $wt_ID, '_price', $_post_e['_regular_price']?$_post_e['_regular_price']:0 );
	}else{
		update_post_meta( $wt_ID, '_price', $_post_e['_sale_price']?$_post_e['_sale_price']:0 );
	}
	update_post_meta($wt_ID, '_purchase_note', $_post_e['_purchase_note']);
	update_post_meta($wt_ID, '_featured', $_post_e['current_featured']);
	update_post_meta($wt_ID, '_weight', $_post_e['_weight']);
	update_post_meta($wt_ID, '_length', $_post_e['_length']);
	update_post_meta($wt_ID, '_width', $_post_e['_width']);
	update_post_meta($wt_ID, '_height', $_post_e['_height']);
	update_post_meta($wt_ID, '_sku', $_post_e['_sku']);
	update_post_meta($wt_ID, '_product_attributes', get_post_meta($post_id,'_product_attributes', true ));
	update_post_meta($wt_ID, '_sale_price_dates_from', $_post_e['_sale_price_dates_from']);
	update_post_meta($wt_ID, '_sale_price_dates_to', $_post_e['_sale_price_dates_to']);
	update_post_meta($wt_ID, '_manage_stock', $_post_e['_manage_stock']);
	update_post_meta($wt_ID, '_backorders', $_post_e['_backorders']);
	update_post_meta($wt_ID, '_stock', $_post_e['_stock']);
	update_post_meta($wt_ID, '_product_image_gallery', $_post_e['product_image_gallery']); //the comma separated attachment id's of the product images
	//variation
	update_post_meta($wt_ID, '_min_variation_price', get_post_meta($post_id,'_min_variation_price', true ));
	update_post_meta($wt_ID, '_max_variation_price', get_post_meta($post_id,'_max_variation_price', true ));
	update_post_meta($wt_ID, '_min_price_variation_id', get_post_meta($post_id,'_min_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_price_variation_id', get_post_meta($post_id,'_max_price_variation_id', true ));
	update_post_meta($wt_ID, '_min_variation_regular_price', get_post_meta($post_id,'_min_variation_regular_price', true ));
	update_post_meta($wt_ID, '_max_variation_regular_price', get_post_meta($post_id,'_max_variation_regular_price', true ));
	update_post_meta($wt_ID, '_min_regular_price_variation_id', get_post_meta($post_id,'_min_regular_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_regular_price_variation_id', get_post_meta($post_id,'_max_regular_price_variation_id', true ));
	update_post_meta($wt_ID, '_min_variation_sale_price', get_post_meta($post_id,'_min_variation_sale_price', true ));
	update_post_meta($wt_ID, '_max_variation_sale_price', get_post_meta($post_id,'_max_variation_sale_price', true ));
	update_post_meta($wt_ID, '_min_sale_price_variation_id', get_post_meta($post_id,'_min_sale_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_sale_price_variation_id', get_post_meta($post_id,'_max_sale_price_variation_id', true ));
	
	
	// Assign sizes and colors to the main product
	if ($children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation' )) {
		foreach ( $children_products as $child ) {
			wt_duplicate_product( wt_get_product_to_duplicate( $child->ID ), $wt_ID, $child->post_status );
		}
	}
	wt_duplicate_post_taxonomies($post_id, $wt_ID, 'product' );	
	//remove the product categories
//	wp_set_object_terms($wt_ID, '', 'product_cat', true);
//	//array list of all the categories this product belongs to
//	$product_categories = $_post_e['tax_input']['product_cat'];
//	//add product categories to the product
//	foreach($product_categories as $product_category) {
//		wp_set_object_terms($wt_ID, intval($product_category), 'product_cat', true);
//	}
//	//remove the product tags
//	wp_set_object_terms($wt_ID, '', 'product_tag', true);
//	//array list of all the categories this product belongs to
//	$product_tags = $_post_e['tax_input']['product_tag'];
//	//add product categories to the product
//	foreach($product_tags as $product_tag) {
//		$term_object = term_exists($product_tag, 'product_tag');
//		if($term_object == NULL) {
//			//create the category
//			$term_object = wp_insert_term($product_category, 'product_cat', array(
//				'parent' => 0 //parent term id if it should be a sub-category
//			));
//		}
//		 
//		wp_set_object_terms($wt_ID, intval($term_object['term_id']), 'product_tag', true);
//		 
//		unset($term_object);
//	}
//	 
//	/*
//	* update the product type.
//	*
//	* the product type can be eiher simple, grouped, external or variable.
//	*/
//	$term_object = term_exists($_post_e['product-type'], 'product_type');
//	if($term_object == NULL) {
//	$term_object = wp_insert_term($_post_e['product-type'], 'product_type');
//	}
//	wp_set_object_terms($wt_ID, intval($term_object['term_id']), 'product_type', true);
//	unset($term_object);

}
//update recurren event
function wt_update_recurren($_post_e,$wt_ID,$post_id){
	
	$arr = array(
		'ID'           				=> $wt_ID,
		'post_author'               => $_post_e['post_author'],
		'post_content'              => $_post_e['post_content'],
		'post_title'                => $_post_e['post_title'],
		'post_excerpt'              => $_post_e['post_excerpt'],
		'post_status'               => $_post_e['post_status'],
		'comment_status'            => $_post_e['comment_status'],
		'ping_status'               => $_post_e['ping_status'],
		'post_password'             => $_post_e['post_password'],
		'post_parent'               => $_post_e['post_parent'],
		'menu_order'                => $_post_e['menu_order'],
		'post_mime_type'            => $_post_e['post_mime_type']
	);
	 wp_update_post( $arr );
	
	if(isset($_post_e['_downloadable'])){
		update_post_meta($wt_ID, '_downloadable', $_post_e['_downloadable']);
	}
	if(isset($_POST['_virtual'])){
		update_post_meta($wt_ID, '_virtual', $_post_e['_virtual']);
	}
	update_post_meta($wt_ID, '_visibility', $_post_e['_visibility']);
	update_post_meta($wt_ID, '_stock_status', $_post_e['_stock_status']);
	update_post_meta( $wt_ID, '_visibility', 'visible' );
    update_post_meta( $wt_ID, '_stock_status', 'instock');
	update_post_meta($wt_ID, '_regular_price', $_post_e['_regular_price']);
	update_post_meta($wt_ID, '_sale_price', $_post_e['_sale_price']);
	if($_post_e['_sale_price']==''){
		update_post_meta( $wt_ID, '_price', $_post_e['_regular_price']?$_post_e['_regular_price']:0 );
	}else{
		update_post_meta( $wt_ID, '_price', $_post_e['_sale_price']?$_post_e['_sale_price']:0 );
	}
	update_post_meta($wt_ID, '_purchase_note', $_post_e['_purchase_note']);
	update_post_meta($wt_ID, '_featured', $_post_e['current_featured']);
	update_post_meta($wt_ID, '_weight', $_post_e['_weight']);
	update_post_meta($wt_ID, '_length', $_post_e['_length']);
	update_post_meta($wt_ID, '_width', $_post_e['_width']);
	update_post_meta($wt_ID, '_height', $_post_e['_height']);
	update_post_meta($wt_ID, '_sku', $_post_e['_sku']);
	update_post_meta($wt_ID, '_product_attributes', get_post_meta($post_id,'_product_attributes', true ));
	update_post_meta($wt_ID, '_sale_price_dates_from', $_post_e['_sale_price_dates_from']);
	update_post_meta($wt_ID, '_sale_price_dates_to', $_post_e['_sale_price_dates_to']);
	update_post_meta($wt_ID, '_manage_stock', $_post_e['_manage_stock']);
	update_post_meta($wt_ID, '_backorders', $_post_e['_backorders']);
	update_post_meta($wt_ID, '_stock', $_post_e['_stock']);
	update_post_meta($wt_ID, '_product_image_gallery', $_post_e['product_image_gallery']); //the comma separated attachment id's of the product images
	//variation
	update_post_meta($wt_ID, '_min_variation_price', get_post_meta($post_id,'_min_variation_price', true ));
	update_post_meta($wt_ID, '_max_variation_price', get_post_meta($post_id,'_max_variation_price', true ));
	update_post_meta($wt_ID, '_min_price_variation_id', get_post_meta($post_id,'_min_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_price_variation_id', get_post_meta($post_id,'_max_price_variation_id', true ));
	update_post_meta($wt_ID, '_min_variation_regular_price', get_post_meta($post_id,'_min_variation_regular_price', true ));
	update_post_meta($wt_ID, '_max_variation_regular_price', get_post_meta($post_id,'_max_variation_regular_price', true ));
	update_post_meta($wt_ID, '_min_regular_price_variation_id', get_post_meta($post_id,'_min_regular_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_regular_price_variation_id', get_post_meta($post_id,'_max_regular_price_variation_id', true ));
	update_post_meta($wt_ID, '_min_variation_sale_price', get_post_meta($post_id,'_min_variation_sale_price', true ));
	update_post_meta($wt_ID, '_max_variation_sale_price', get_post_meta($post_id,'_max_variation_sale_price', true ));
	update_post_meta($wt_ID, '_min_sale_price_variation_id', get_post_meta($post_id,'_min_sale_price_variation_id', true ));
	update_post_meta($wt_ID, '_max_sale_price_variation_id', get_post_meta($post_id,'_max_sale_price_variation_id', true ));
	
	
	// Assign sizes and colors to the main product
	if ($children_products = get_children( 'post_parent=' . $post_id . '&post_type=product_variation' )) {
		foreach ( $children_products as $child ) {
			wt_duplicate_product( wt_get_product_to_duplicate( $child->ID ), $wt_ID, $child->post_status );
		}
	}
	//remove the product categories
	wp_set_object_terms($wt_ID, '', 'product_cat', true);
	//array list of all the categories this product belongs to
	$product_categories = $_post_e['tax_input']['product_cat'];
	//add product categories to the product
	foreach($product_categories as $product_category) {
		wp_set_object_terms($wt_ID, intval($product_category), 'product_cat', true);
	}
	//remove the product tags
	wp_set_object_terms($wt_ID, '', 'product_tag', true);
	//array list of all the categories this product belongs to
	$product_tags = $_post_e['tax_input']['product_tag'];
	//add product categories to the product
	foreach($product_tags as $product_tag) {
		$term_object = term_exists($product_tag, 'product_tag');
		if($term_object == NULL) {
			//create the category
			$term_object = wp_insert_term($product_category, 'product_cat', array(
				'parent' => 0 //parent term id if it should be a sub-category
			));
		}
		 
		wp_set_object_terms($wt_ID, intval($term_object['term_id']), 'product_tag', true);
		 
		unset($term_object);
	}
	 
	
//	* update the product type.
//	*
//	* the product type can be eiher simple, grouped, external or variable.
//	
	$term_object = term_exists($_post_e['product-type'], 'product_type');
	if($term_object == NULL) {
	$term_object = wp_insert_term($_post_e['product-type'], 'product_type');
	}
	wp_set_object_terms($wt_ID, intval($term_object['term_id']), 'product_type', true);
	unset($term_object);

}
//edit link recurrence
add_filter( 'get_edit_post_link', 'wt_edit_post_link', 10, 3 );
function wt_edit_post_link( $url, $post_id, $context) {
    $ex_recurr = get_post_meta($post_id,'recurren_ext', true );
	if($ex_recurr!=''){
		$ex_recurr  = explode("_",$ex_recurr);
		if(isset($ex_recurr[1]) && $ex_recurr[1]!=''){
			$url = add_query_arg( array('post'=>$ex_recurr[1]),  $url);
		}
	}
    return $url;
}
//
add_filter('post_row_actions','wt_change_edit_product_rows',10, 2 );
function wt_change_edit_product_rows($actions,$post) {
	$ex_recurr = get_post_meta($post->ID,'recurren_ext', true );
	$ex_recurr  = explode("_",$ex_recurr);
	if(isset($ex_recurr[1]) && $ex_recurr[1]!=''){
		$can_edit_post = current_user_can( 'edit_post', $post->ID );
		if ( $can_edit_post ) {
		  $actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( esc_html__( 'Edit all','woo-tour' ) ) . '">' . esc_html__( 'Edit all','woo-tour' ) . '</a>';
		  $actions['inline hide-if-no-js'] = '<a href="' . add_query_arg( array('post'=>$post->ID), get_edit_post_link( $post->ID, true )) . '" class="editsingle" title="' . esc_attr( esc_html__( 'Edit single','woo-tour' ) ) . '">' . esc_html__( 'Edit single','woo-tour' ) . '</a>';
		}
	}
	return $actions;
}
//bubble
add_action( 'admin_menu', 'wt_pending_posts_bubble', 999 );
function wt_pending_posts_bubble() 
{
    global $menu;

    // Get all post types and remove Attachments from the list
    // Add '_builtin' => false to exclude Posts and Pages
    $args = array( 'public' => true ); 
    $post_types = get_post_types( $args );

    foreach( $post_types as $pt ){
		if( $pt == 'product'){
			// Count posts
			$cpt_count = wp_count_posts( $pt );
	
			if ( $cpt_count->pending ) 
			{
				// Menu link suffix, Post is different from the rest
				$suffix = ( 'post' == $pt ) ? '' : "?post_type=$pt";
	
				// Locate the key of 
				$key = wt_recursive_array_search_php( "edit.php$suffix", $menu );
	
				// Not found, just in case 
				if( !$key )
					return;
	
				// Modify menu item
				$menu[$key][0] .= sprintf(
					'<span class="update-plugins count-%1$s" style="background-color:white;color:red; margin-left:5px;"><span class="plugin-count">%1$s</span></span>',
					$cpt_count->pending 
				);
			}
		}
    }
}
function wt_recursive_array_search_php( $needle, $haystack ) 
{
    foreach( $haystack as $key => $value ) 
    {
        $current_key = $key;
        if( 
            $needle === $value 
            OR ( 
                is_array( $value )
                && wt_recursive_array_search_php( $needle, $value ) !== false 
            )
        ) 
        {
            return $current_key;
        }
    }
    return false;
}
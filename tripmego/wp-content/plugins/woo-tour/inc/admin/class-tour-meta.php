<?php
class wootour_Meta {
	public function __construct()
    {
		add_action( 'init', array($this,'init'), 0);
    }
	function init(){
		// Variables
		add_filter( 'exc_mb_meta_boxes', array($this,'wootour_metadata') );
		add_action( 'init', array( &$this, 'register_category_taxonomies' ) );
		//create child Variation price
		add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'variation_settings_fields'), 10, 3 );
		// Save Variation Settings
		add_action( 'woocommerce_save_product_variation', array( &$this, 'save_variation_settings_fields'), 10, 2 );
		// Add Variation
		add_filter( 'woocommerce_available_variation', array( &$this, 'load_variation_settings_fields') );

	}
	/**
	 * Create new fields for variations
	 *
	*/
	function variation_settings_fields( $loop, $variation_data, $variation ) {
		// _child_price Field
		woocommerce_wp_text_input( 
			array( 
				'id'          => '_child_price[' . $variation->ID . ']', 
				'label'       => esc_html__( 'Tour price for Children','woo-tour' ), 
				'desc_tip'    => 'true',
				'wrapper_class' 	  => 'form-row form-row-first',
				'placeholder' => esc_html__('Enter number', 'woo-tour' ),
				'description' => esc_html__( 'Enter OFF to hide this field', 'woo-tour' ),
				'value'       => get_post_meta( $variation->ID, '_child_price', true ),
			)
		);
		woocommerce_wp_text_input( 
			array( 
				'id'          => '_child_price_sale[' . $variation->ID . ']', 
				'label'       => esc_html__( 'Sale price for Children','woo-tour' ), 
				'desc_tip'    => 'true',
				'wrapper_class' 	  => 'form-row form-row-last',
				'placeholder' => esc_html__('Enter number', 'woo-tour' ),
				'description' => esc_html__( 'Enter OFF to hide this field', 'woo-tour' ),
				'value'       => get_post_meta( $variation->ID, '_child_price_sale', true ),
			)
		);
		// _infant_price Field
		woocommerce_wp_text_input( 
			array( 
				'id'          => '_infant_price[' . $variation->ID . ']', 
				'label'       => esc_html__( 'Tour price for Infant','woo-tour' ), 
				'desc_tip'    => 'true',
				'wrapper_class' 	  => 'form-row form-row-first',
				'placeholder' => esc_html__('Enter number', 'woo-tour' ),
				'description' => esc_html__( 'Enter Sale price', 'woo-tour' ),
				'value'       => get_post_meta( $variation->ID, '_infant_price', true ),
			)
		);
		woocommerce_wp_text_input( 
			array( 
				'id'          => '_infant_price_sale[' . $variation->ID . ']', 
				'label'       => esc_html__( 'Sale price for Infant','woo-tour' ), 
				'desc_tip'    => 'true',
				'wrapper_class' 	  => 'form-row form-row-last',
				'placeholder' => esc_html__('Enter number', 'woo-tour' ),
				'description' => esc_html__( 'Enter Sale price', 'woo-tour' ),
				'value'       => get_post_meta( $variation->ID, '_infant_price_sale', true ),
			)
		);
	}
	/**
	 * Save new fields for variations
	 *
	*/
	function save_variation_settings_fields( $post_id ) {
		$_child_price = $_POST['_child_price'][ $post_id ];
		if( isset( $_child_price ) ) {
			update_post_meta( $post_id, '_child_price', esc_attr( $_child_price ) );
		}
		$_infant_price = $_POST['_infant_price'][ $post_id ];
		if( isset( $_infant_price ) ) {
			update_post_meta( $post_id, '_infant_price', esc_attr( $_infant_price ) );
		}
		
		$_child_price_sale = $_POST['_child_price_sale'][ $post_id ];
		if( isset( $_child_price_sale ) ) {
			update_post_meta( $post_id, '_child_price_sale', esc_attr( $_child_price_sale ) );
		}
		$_infant_price_sale = $_POST['_infant_price_sale'][ $post_id ];
		if( isset( $_infant_price_sale ) ) {
			update_post_meta( $post_id, '_infant_price_sale', esc_attr( $_infant_price_sale ) );
		}
	}
	/**
	 * Add custom fields for variations
	 *
	*/
	function load_variation_settings_fields( $variations ) {
		global $product;
		$_product_vari = new WC_Product_Variation( $variations['variation_id'] );
		$variations['_adult_price'] = $_product_vari->get_price_html();
		// duplicate the line for each field
		$wt_child = $child_price = get_post_meta( $variations[ 'variation_id' ], '_child_price', true );
		$wt_child_max = get_post_meta( $_product_vari->get_parent_id(), 'wt_child_max', true ) ;
		$wt_def_childf = get_option( 'wt_def_childf' ) ;
		if( ($wt_child!='OFF' && $wt_child!='') || ($wt_child=='' && $wt_def_childf!='off') ){
			$sl_cvalue = '<option value="">0</option>';
			$l = get_option('wt_default_child')!='' ? get_option('wt_default_child') : 5;
			if(is_numeric ($wt_child_max)){$l = $wt_child_max;}
			$wt_child_max = $wt_child_max * 1;
			for($i=1; $i <= $l ; $i++){$sl_cvalue .= '<option value="'.$i.'">'.$i.'</option>';}
			$html_child  = we_quantity_html('wt_number_child',$sl_cvalue,'0');
			$if_sale = get_post_meta( $variations[ 'variation_id' ], '_child_price_sale', true );
			$child_label = get_post_meta( $_product_vari->get_parent_id(), 'wt_child_label', true ) ;
			$child_label = $child_label!='' ? $child_label.': ' : esc_html__('Children: ','woo-tour');
			$child_label = '<span class="lb-pric">'.$child_label.'</span>';
			$html_child_price = $child_price=='' ? $child_label : $child_label.wt_addition_price_html($child_price,$span='1',$if_sale);
			$variations['_child_price'] = we_table_variation_html($html_child_price, $html_child, 'wt-child-price');
		}
		
		$wt_infant = $infant_price = get_post_meta( $variations[ 'variation_id' ], '_infant_price', true );
		$wt_infant_max = get_post_meta( $_product_vari->get_parent_id(), 'wt_infant_max', true ) ;
		$wt_def_intff = get_option( 'wt_def_intff' ) ;
		if( ($wt_infant!='OFF' && $wt_infant!='') || ($wt_infant=='' && $wt_def_intff!='off') ){
			$sl_ivalue = '<option value="">0</option>';
			$l = get_option('wt_default_inf') !='' ? get_option('wt_default_inf') : 5 ;
			if(is_numeric ($wt_infant_max)){$l = $wt_infant_max;}
			$wt_infant_max = $wt_infant_max * 1;
			for($i=1; $i <= $l ; $i++){$sl_ivalue .= '<option value="'.$i.'">'.$i.'</option>';}
			$html_infant = we_quantity_html('wt_number_infant',$sl_ivalue,'0');
			$if_sale = get_post_meta( $variations[ 'variation_id' ], '_infant_price_sale', true );
			$infant_label = get_post_meta( $_product_vari->get_parent_id(), 'wt_infant_label', true ) ;
			$infant_label = $infant_label!='' ? $infant_label.': ' : esc_html__('Infant: ','woo-tour');
			$infant_label = '<span class="lb-pric">'.$infant_label.'</span>';
			$html_infant_price = $infant_price=='' ? $child_label : $infant_label.wt_addition_price_html($infant_price,$span='1',$if_sale);
			$variations['_infant_price'] =  we_table_variation_html($html_infant_price, $html_infant, 'wt-infant-price');
		}
		return $variations;
	}
	
	function wootour_metadata(array $meta_boxes){
		// register aff store metadata		
		$time_settings = array(	
			array( 
				'id' => 'wt_weekday', 
				'name' => esc_html__('Select day:', 'woo-tour'), 
				'type' => 'select', 'options' => array( 
					'2' => esc_html__('Monday', 'woo-tour'), 
					'3' => esc_html__('Tuesday', 'woo-tour'), 
					'4' => esc_html__('Wednesday', 'woo-tour'), 
					'5' => esc_html__('Thursday', 'woo-tour'), 
					'6' => esc_html__('Friday', 'woo-tour'), 
					'7' => esc_html__('Saturday', 'woo-tour'), 
					'1' => esc_html__('Sunday', 'woo-tour') 
				),
				'cols' => 6,
				'desc' => esc_html__('Leave blank to use for daily tour', 'woo-tour') ,
				'multiple' => true 
			),
			array( 'id' => 'wt_disabledate', 'name' => esc_html__('Disable date:', 'woo-tour'), 'cols' => 6, 'type' => 'date_unix','desc' => esc_html__('Select disable date for this tour', 'woo-tour') , 'repeatable' => true, 'multiple' => true ),	
			
			array( 'id' => 'wt_customdate', 'name' => esc_html__('Special Date:', 'woo-tour'), 'cols' => 6, 'type' => 'date_unix','desc' => esc_html__('Select special date for this tour', 'woo-tour') , 'repeatable' => true, 'multiple' => true ),
			array( 'id' => 'wt_expired', 'name' => esc_html__('Expired Date:', 'woo-tour'), 'cols' => 6, 'type' => 'date_unix' ,'desc' => esc_html__('Select expired date for this tour', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_date_label', 'name' => esc_html__('Label name for Date select', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Enter text, leave blank to use default', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'def_stock', 'name' => esc_html__('Quantity', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Quantity of each date select ( enter number )', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
		);

		$info_settings = array(	
			array( 'id' => 'wt_duration', 'name' => esc_html__('Duration', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Duration of tour', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_type', 'name' => esc_html__('Tour type', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Type of tour', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_transport', 'name' => esc_html__('Transport', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Transport of tour', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_group_size', 'name' => esc_html__('Group size', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Min & Maximum number people of tour', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'wt_accom_service', 'name' => esc_html__('Accompanied service', 'woo-tour'), 'type' => 'text' ,'desc' => esc_html__('Add Accompanied service Info for this tour', 'woo-tour'), 'repeatable' => true, 'multiple' => true ),
			//array( 'id' => 'wt_eventcolor', 'name' => esc_html__('Color', 'woo-tour'), 'type' => 'colorpicker', 'repeatable' => false, 'multiple' => true ),
			
		);
		$addition_settings = array(	
			array( 'id' => 'wt_child', 'name' => esc_html__('Tour price for Children', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Enter OFF to hide this field', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_child_sale', 'name' => esc_html__('Sale price for Children', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Enter Sale price for Children', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_child_max', 'name' => esc_html__('Max value', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Max value can select', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_child_label', 'name' => esc_html__('Label name for children', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Default is Children:', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'wt_infant', 'name' => esc_html__('Tour price for Infant', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Enter OFF to hide this field', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_infant_sale', 'name' => esc_html__('Sale price for Infant', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Enter OFF to hide this field', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_infant_max', 'name' => esc_html__('Max value', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Max value can select', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_infant_label', 'name' => esc_html__('Label name for Infant', 'woo-tour'), 'cols' => 3, 'type' => 'text' ,'desc' => esc_html__('Default is Infant:', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			
			array( 'id' => 'wt_adult_max', 'name' => esc_html__('Max value adult', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Max value can select', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'wt_adult_label', 'name' => esc_html__('Label name for Adult', 'woo-tour'), 'cols' => 6, 'type' => 'text' ,'desc' => esc_html__('Default is Adult:', 'woo-tour'), 'repeatable' => false, 'multiple' => false ),
		);
		
		$event_layout = array(	
			array( 'id' => 'wt_layout', 'name' => esc_html__('Layout', 'woo-tour'), 'cols' => 6, 'type' => 'select', 'options' => array( '' => esc_html__('Default', 'woo-tour'), 'layout-2' => esc_html__('Full Width', 'woo-tour'),'layout-3' => esc_html__('Full Width Flat', 'woo-tour')),'desc' => esc_html__('Select "Default" to use settings in Event Options', 'woo-tour') , 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'wt_sidebar', 'name' => esc_html__('Sidebar', 'woo-tour'), 'cols' => 6, 'type' => 'select', 'options' => array( '' => esc_html__('Default', 'woo-tour'), 'right' => esc_html__('Right', 'woo-tour'), 'left' => esc_html__('Left', 'woo-tour'),'hide' => esc_html__('Hidden', 'woo-tour')),'desc' => esc_html__('Select "Default" to use settings in Event Options', 'woo-tour') , 'repeatable' => false, 'multiple' => false),
		);
		$event_purpose = array(	
			array( 'id' => 'wt_layout_purpose', 'name' => '', 'type' => 'select', 'options' => array( 'woo' => esc_html__('WooCommere', 'woo-tour'), 'tour' => esc_html__('Tour', 'woo-tour')),'desc' => esc_html__('Select Layout Purpose for this product', 'woo-tour') , 'repeatable' => false, 'multiple' => false)
		);
		
		$wt_main_purpose = get_option('wt_main_purpose');
		$meta_boxes[] = array(
			'title' => __('Time Settings','woo-tour'),
			'pages' => 'product',
			'fields' => $time_settings,
			'priority' => 'high'
		);
		$meta_boxes[] = array(
			'title' => __('Tour Info','woo-tour'),
			'pages' => 'product',
			'fields' => $info_settings,
			'priority' => 'high'
		);
		$meta_boxes[] = array(
			'title' => __('Additional Information','woo-tour'),
			'pages' => 'product',
			'fields' => $addition_settings,
			'priority' => 'high'
		);
		if($wt_main_purpose=='custom' || $wt_main_purpose=='meta'){
			if($wt_main_purpose=='meta'){
				$event_purpose = array(	
					array( 'id' => 'wt_layout_purpose', 'name' => '', 'type' => 'select', 'options' => array( 'def' => esc_html__('Default', 'woo-tour'), 'woo' => esc_html__('WooCommere', 'woo-tour'), 'tour' => esc_html__('Tour', 'woo-tour')),'desc' => esc_html__('Select Default to use setting in plugin setting', 'woo-tour') , 'repeatable' => false, 'multiple' => false)
				);
			}
			$meta_boxes[] = array(
				'title' => __('Layout Purpose','woo-tour'),
				'context' => 'side',
				'pages' => 'product',
				'fields' => $event_purpose,
				'priority' => 'high'
			);
		}
		$meta_boxes[] = array(
			'title' => __('Layout Settings','woo-tour'),
			'pages' => 'product',
			'fields' => $event_layout,
			'priority' => 'high'
		);
		$group_fields = array(
			array( 'id' => 'wt_custom_title',  'name' => esc_html__('Title', 'woo-tour'), 'type' => 'text', 'cols' => 6 ),
			array( 'id' => 'wt_custom_content', 'name' => esc_html__('Content', 'woo-tour'), 'type' => 'text', 'desc' => '', 'repeatable' => false, 'cols' => 6),
		);
		foreach ( $group_fields as &$field ) {
			$field['id'] = str_replace( 'field', 'gfield', $field['id'] );
		}
	
		$meta_boxes[] = array(
			'title' => esc_html__('Custom Field', 'woo-tour'),
			'pages' => 'product',
			'fields' => array(
				array(
					'id' => 'wt_custom_metadata',
					'name' => esc_html__('Custom Metadata', 'woo-tour'),
					'type' => 'group',
					'repeatable' => true,
					'sortable' => true,
					'fields' => $group_fields,
					'desc' => esc_html__('Custom metadata for this post', 'woo-tour')
				)
			),
			'priority' => 'high'
		);
		
		
		$discount_fields = array(
			array( 'id' => 'wt_disc_start', 'name' => esc_html__('Start', 'exthemes'), 'cols' => 6, 'type' => 'date_unix','desc' => ''),
			array( 'id' => 'wt_disc_end', 'name' => esc_html__('End', 'exthemes'), 'cols' => 6, 'type' => 'date_unix' ,'desc' => ''),
			array( 'id' => 'wt_disc_type', 'name' => esc_html__('Type', 'woo-tour'), 'type' => 'select', 'options' => array( 'price' => esc_html__('Fixed price', 'woo-tour'), 'percent' => esc_html__('Percentage', 'woo-tour')),'desc' => esc_html__('', 'woo-tour'),'cols' => 12),
			array( 'id' => 'wt_disc_number',  'name' => esc_html__('Number adult', 'woo-tour'), 'type' => 'text', 'cols' => 6 ),
			array( 'id' => 'wt_disc_am',  'name' => esc_html__('Amount', 'woo-tour'), 'type' => 'number', 'cols' => 6 ),
		);	
		$meta_boxes[] = array(
			'title' => esc_html__('Discount', 'woo-tour'),
			'pages' => 'product',
			'context' => 'side',
			'fields' => array(
				array(
					'id' => 'wt_discount',
					'name' => esc_html__('Discount', 'woo-tour'),
					'type' => 'group',
					'repeatable' => true,
					'sortable' => true,
					'fields' => $discount_fields,
					'desc' => esc_html__('Discount base on number adult', 'woo-tour')
				)
			),
			'priority' => ''
		);
			
		return $meta_boxes;
	}
	function register_category_taxonomies(){
		$labels = array(
			'name'              => esc_html__( 'Location', 'woo-tour' ),
			'singular_name'     => esc_html__( 'Location', 'woo-tour' ),
			'search_items'      => esc_html__( 'Search','woo-tour' ),
			'all_items'         => esc_html__( 'All Locations','woo-tour' ),
			'parent_item'       => esc_html__( 'Parent Location' ,'woo-tour'),
			'parent_item_colon' => esc_html__( 'Parent Location:','woo-tour' ),
			'edit_item'         => esc_html__( 'Edit Location' ,'woo-tour'),
			'update_item'       => esc_html__( 'Update Location','woo-tour' ),
			'add_new_item'      => esc_html__( 'Add New Location' ,'woo-tour'),
			'menu_name'         => esc_html__( 'Locations','woo-tour' ),
		);			
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'location' ),
		);
		
		register_taxonomy('wt_location', 'product', $args);
	}
}
$wootour_Meta = new wootour_Meta();

include_once(ABSPATH.'wp-admin/includes/plugin.php');
if(!is_plugin_active('categories-images/categories-images.php')){
	/* location feature image */
	add_action( 'wt_location_add_form_fields', 'wt_image_fields', 10 );
	add_action ( 'wt_location_edit_form_fields', 'wt_image_fields');
	
	function wt_image_fields( $tag ) {    //check for existing featured ID
		$t_id 					= isset($tag->term_id) ? $tag->term_id : '';
		$id_image 			= get_option( "id_image_$t_id")?get_option( "id_image_$t_id"):'';
		?>
		<tr class="form-field" style="">
			<th scope="row" valign="top">
				<label for="id-image"><?php esc_html_e('Image Attachment ID','woo-tour'); ?></label>
            </th>
			<td>
				<input type="text" name="id-image" id="id-image" style="margin-bottom:15px;" value="<?php echo esc_attr($id_image) ?>" />
            </td>
		</tr>
		<?php
	}
	//save image fields
	add_action ( 'edited_wt_location', 'wt_save_extra_image_fileds', 10, 2);
	add_action( 'created_wt_location', 'wt_save_extra_image_fileds', 10, 2 );
	function wt_save_extra_image_fileds( $term_id ) {
		if ( isset( $_POST[sanitize_key('id-image')] ) ) {
			$id_image = $_POST['id-image'];
			update_option( "id_image_$term_id", $id_image );
		}
	}
}
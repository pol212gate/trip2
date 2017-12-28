<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class wootour_Settings {
    private $dir;
	private $file;
	private $assets_dir;
	private $assets_url;
	private $settings_base;
	private $settings;
	public function __construct( $file ) {
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->settings_base = '';
		// Initialise settings
		add_action( 'admin_init', array( $this, 'init' ) );
		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
	}
	/**
	 * Initialise settings
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
	}
	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_menu_page( esc_html__( 'WooTours Settings', 'woo-tour' ) , esc_html__( 'WooTours', 'woo-tour' ) , 'manage_options' , 'wootours' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}
	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets() {
		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		// We're including the WP media scripts here because they're needed for the image upload field
		// If you're not including an image upload then you can leave this function call out
		wp_enqueue_media();
		wp_register_script( 'wpt-admin-js', $this->assets_url . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
		wp_enqueue_script( 'wpt-admin-js' );
	}
	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="options-general.php?page=wootours">' . esc_html__( 'WooTours Settings', 'woo-tour' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}
	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields() {
		$settings['general'] = array(
			'title'					=> esc_html__( 'General', 'woo-tour' ),
			'description'			=> esc_html__( '', 'woo-tour' ),
			'fields'				=> array(
				array(
					'id' 			=> 'wt_main_purpose',
					'label'			=> esc_html__( 'Main Purpose', 'woo-tour' ),
					'description'	=> esc_html__( 'Select "Custom" to use main layout as woocommerce but you can choose product as tour in each product, Select "Only use metadata" to use your theme style and you can choose product as tour in each product', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'tour' => esc_html__( 'Tour', 'woo-tour' ),
						'custom' => esc_html__( 'Custom', 'woo-tour' ),
						'meta' => esc_html__( 'Only use metadata', 'woo-tour' )
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_main_color',
					'label'			=> esc_html__( 'Main color', 'woo-tour' ),
					'description'	=> esc_html__( 'Choose main color of Wootours', 'woo-tour' ),
					'type'			=> 'color',
					'placeholder'			=> '',
					'default'		=> '#00467e'
				),
				array(
					'id' 			=> 'wt_fontfamily',
					'label'			=> esc_html__( 'Main Font Family', 'woo-tour' ),
					'description'	=> esc_html__( 'Enter Google font-family name here. For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro', 'woo-tour' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_fontsize',
					'label'			=> esc_html__( 'Main Font Size', 'woo-tour' ),
					'description'	=> esc_html__( 'Enter size of font, Ex: 13px', 'woo-tour' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_hfont',
					'label'			=> esc_html__( 'Heading Font Family', 'woo-tour' ),
					'description'	=> esc_html__( 'Enter Google font-family name here. For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro', 'woo-tour' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> '',
				),
				array(
					'id' 			=> 'wt_hfontsize',
					'label'			=> esc_html__( 'Heading Font Size', 'woo-tour' ),
					'description'	=> esc_html__( 'Enter size of font, Ex: 20px', 'woo-tour' ),
					'type'			=> 'text',
					'placeholder'			=> '',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_sidebar',
					'label'			=> esc_html__( 'Sidebar', 'woo-tour' ),
					'description'	=> esc_html__( 'Select hide to use sidebar of theme', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'right' => esc_html__( 'Right', 'woo-tour' ),
						'left' => esc_html__( 'Left', 'woo-tour' ),
						'hide' => esc_html__( 'Hide', 'woo-tour' )
					),
					'default'		=> ''
				),
				// calendar language
				array(
					'id' 			=> 'wt_calendar_lg',
					'label'			=> esc_html__( 'Calendar Language', 'woo-tour' ),
					'description'	=> esc_html__( 'Select language of Calendar', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'en', 'woo-tour' ),
						'ar' => esc_html__( 'ar', 'woo-tour' ),
						'bg_BG' => esc_html__( 'bg_BG', 'woo-tour' ),
						'bs_BA' => esc_html__( 'bs_BA', 'woo-tour' ),
						'ca_ES' => esc_html__( 'ca_ES', 'woo-tour' ),
						'cs_CZ' => esc_html__( 'cs_CZ', 'woo-tour' ),
						'da_DK' => esc_html__( 'da_DK', 'woo-tour' ),
						'de_DE' => esc_html__( 'de_DE', 'woo-tour' ),
						'el_GR' => esc_html__( 'el_GR', 'woo-tour' ),
						'es_ES' => esc_html__( 'es_ES', 'woo-tour' ),
						'et_EE' => esc_html__( 'et_EE', 'woo-tour' ),
						'eu_ES' => esc_html__( 'eu_ES', 'woo-tour' ),
						'fa_IR' => esc_html__( 'fa_IR', 'woo-tour' ),
						'fi_FI' => esc_html__( 'fi_FI', 'woo-tour' ),
						'fr_FR' => esc_html__( 'fr_FR', 'woo-tour' ),
						'ge_GEO' => esc_html__( 'ge_GEO', 'woo-tour' ),
						'gl_ES' => esc_html__( 'gl_ES', 'woo-tour' ),
						'he_IL' => esc_html__( 'he_IL', 'woo-tour' ),
						'hi_IN' => esc_html__( 'hi_IN', 'woo-tour' ),
						'hr_HR' => esc_html__( 'hr_HR', 'woo-tour' ),
						'hu_HU' => esc_html__( 'hu_HU', 'woo-tour' ),
						'id_ID' => esc_html__( 'id_ID', 'woo-tour' ),
						'is_IS' => esc_html__( 'is_IS', 'woo-tour' ),
						'it_IT' => esc_html__( 'it_IT', 'woo-tour' ),
						'ja_JP' => esc_html__( 'ja_JP', 'woo-tour' ),
						'ko_KR' => esc_html__( 'ko_KR', 'woo-tour' ),
						'lt_LT' => esc_html__( 'lt_LT', 'woo-tour' ),
						'lv_LV' => esc_html__( 'lv_LV', 'woo-tour' ),
						'nb_NO' => esc_html__( 'nb_NO', 'woo-tour' ),
						'ne_NP' => esc_html__( 'ne_NP', 'woo-tour' ),
						'nl_NL' => esc_html__( 'nl_NL', 'woo-tour' ),
						'no_NO' => esc_html__( 'no_NO', 'woo-tour' ),
						'pl_PL' => esc_html__( 'pl_PL', 'woo-tour' ),
						'pt_BR' => esc_html__( 'pt_BR', 'woo-tour' ),
						'pt_PT' => esc_html__( 'pt_PT', 'woo-tour' ),
						'ro_RO' => esc_html__( 'ro_RO', 'woo-tour' ),
						'ru_RU' => esc_html__( 'ru_RU', 'woo-tour' ),
						'sk_SK' => esc_html__( 'sk_SK', 'woo-tour' ),
						'sl_SI' => esc_html__( 'sl_SI', 'woo-tour' ),
						'sv_SE' => esc_html__( 'sv_SE', 'woo-tour' ),
						'th_TH' => esc_html__( 'th_TH', 'woo-tour' ),
						'tr_TR' => esc_html__( 'tr_TR', 'woo-tour' ),
						'uk_UA' => esc_html__( 'uk_UA', 'woo-tour' ),
						'vi_VN' => esc_html__( 'vi_VN', 'woo-tour' ),
						'zh_CN' => esc_html__( 'zh_CN', 'woo-tour' ),
						'zh_TW' => esc_html__( 'zh_TW', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_firstday',
					'label'			=> esc_html__( 'The day that each week begins', 'exthemes' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__('Sunday', 'exthemes'),
						'1' => esc_html__('Monday', 'exthemes'),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_shop_view',
					'label'			=> esc_html__( 'Listing default view', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'list' => esc_html__( 'List', 'woo-tour' ),
						'table' => esc_html__( 'Table', 'woo-tour' ),
						),
					'default'		=> ''
				),
			)
		);
		$settings['single_tour'] = array(
			'title'					=> esc_html__( 'Single Tour', 'woo-tour' ),
			'description'			=> esc_html__( '', 'woo-tour' ),
			'fields'				=> array(
				array(
					'id' 			=> 'wt_slayout',
					'label'			=> esc_html__( 'Layout', 'woo-tour' ),
					'description'	=> esc_html__( 'Select default layout of single event', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'layout-1' => esc_html__( 'Default', 'woo-tour' ),
						'layout-2' => esc_html__( 'Full Width', 'woo-tour' ),
						'layout-3' => esc_html__( 'Full Width Flat', 'woo-tour' )
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_metaposition',
					'label'			=> esc_html__( 'Meta position ( only work with layout is default)', 'woo-tour' ),
					'description'	=> esc_html__( 'Select position of tour meta', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'below' => esc_html__( 'Below Title', 'woo-tour' ),
						'above' => esc_html__( 'Above Accompanied service info', 'woo-tour' ),
					),
					'default'		=> ''
				),
				
				array(
					'id' 			=> 'wt_show_sdate',
					'label'			=> esc_html__( 'Show Special date in', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'List', 'woo-tour' ),
						'calendar' => esc_html__( 'Calendar', 'woo-tour' ),
					),
					'default'		=> ''
				),
				
				array(
					'id' 			=> 'wt_ssocial',
					'label'			=> esc_html__( 'Show Social Share', 'woo-tour' ),
					'description'	=> esc_html__( 'Show/hide Social Share section', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'woo-tour' ),
						'off' => esc_html__( 'Hide', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_srelated',
					'label'			=> esc_html__( 'Show related', 'woo-tour' ),
					'description'	=> esc_html__( 'Show/hide Related Event section', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'woo-tour' ),
						'off' => esc_html__( 'Hide', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_related_count',
					'label'			=> esc_html__( 'Number of related' , 'woo-tour' ),
					'description'	=> esc_html__( 'Enter number, default 3', 'woo-tour' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'woo-tour' )
				),
				array(
					'id' 			=> 'wt_click_remove',
					'label'			=> esc_html__( 'Remove click on button', 'woo-tour' ),
					'description'	=> esc_html__('Remove click event on qty button when your theme has already added this event', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'woo-tour' ),
						'yes' => esc_html__( 'Yes', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_enable_review',
					'label'			=> esc_html__( 'Enable Review for Tour', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'off' => esc_html__( 'Off', 'woo-tour' ),
						'on' => esc_html__( 'On', 'woo-tour' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_type_qunatity',
					'label'			=> esc_html__( 'Type of quantity field', 'woo-tour' ),
					'description'	=> esc_html__( 'if you select Text box then you can not limit max quantity', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'select' => esc_html__( 'Select box', 'woo-tour' ),
						'text' => esc_html__( 'Text box', 'woo-tour' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_default_adl',
					'label'			=> esc_html__( 'Default max quantity of adult can select' , 'woo-tour' ),
					'description'	=> esc_html__( 'Enter number, default 5', 'woo-tour' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'woo-tour' )
				),
				array(
					'id' 			=> 'wt_default_child',
					'label'			=> esc_html__( 'Default max quantity of Children can select' , 'woo-tour' ),
					'description'	=> esc_html__( 'Enter number, default 5', 'woo-tour' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'woo-tour' )
				),
				array(
					'id' 			=> 'wt_default_inf',
					'label'			=> esc_html__( 'Default max quantity of Infant can select ' , 'woo-tour' ),
					'description'	=> esc_html__( 'Enter number, default 5', 'woo-tour' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'woo-tour' )
				),
				array(
					'id' 			=> 'wt_def_childf',
					'label'			=> esc_html__( 'Default show Children field', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'woo-tour' ),
						'off' => esc_html__( 'Hide', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_def_intff',
					'label'			=> esc_html__( 'Default show Infant field', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Show', 'woo-tour' ),
						'off' => esc_html__( 'Hide', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_disable_book',
					'label'			=> esc_html__( 'User need book before' , 'woo-tour' ),
					'description'	=> esc_html__( 'This feature allow user only can booking tour before X day from now', 'woo-tour' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> esc_html__( 'Enter number', 'woo-tour' )
				),
			)
		);
		
		
		$settings['checkout_tour'] = array(
			'title'					=> esc_html__( 'Checkout', 'woo-tour' ),
			'description'			=> esc_html__( '', 'woo-tour' ),
			'fields'				=> array(
				array(
					'id' 			=> 'wt_enable_cart',
					'label'			=> esc_html__( 'Enable redirect to Checkout page', 'woo-tour' ),
					'description'	=> esc_html__( 'Redirect to the Checkout page after successful addition', 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'on' => esc_html__( 'Off', 'woo-tour' ),
						'off' => esc_html__( 'On', 'woo-tour' ),
						),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_disable_attendees',
					'label'			=> esc_html__( 'Disable multiple attendees info', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'No', 'woo-tour' ),
						'yes' => esc_html__( 'Yes', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_attendee_name',
					'label'			=> esc_html__( 'Attendee name required', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Yes', 'woo-tour' ),
						'no' => esc_html__( 'No', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_attendee_email',
					'label'			=> esc_html__( 'Attendee email required', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Yes', 'woo-tour' ),
						'no' => esc_html__( 'No', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_attendee_birth',
					'label'			=> esc_html__( 'Attendee Date of birth required', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Yes', 'woo-tour' ),
						'no' => esc_html__( 'No', 'woo-tour' ),
					),
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_attendee_gender',
					'label'			=> esc_html__( 'Attendee gender required', 'woo-tour' ),
					'description'	=> '',
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Yes', 'woo-tour' ),
						'no' => esc_html__( 'No', 'woo-tour' ),
					),
					'default'		=> ''
				),
			)
		);
		
		$settings['custom-css'] = array(
			'title'					=> esc_html__( 'Custom Code', 'woo-tour' ),
			'description'			=> esc_html__( '', 'woo-tour' ),
			'fields'				=> array(
				array(
					'id' 			=> 'wt_custom_css',
					'label'			=> esc_html__( 'Paste your CSS code' , 'woo-tour' ),
					'description'	=> esc_html__( 'Add custom CSS code to the plugin without modifying files', 'woo-tour' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> esc_html__( '', 'woo-tour' )
				),
				array(
					'id' 			=> 'wt_custom_code',
					'label'			=> esc_html__( 'Paste your js code' , 'woo-tour' ),
					'description'	=> esc_html__( 'Add custom js code to the plugin without modifying files', 'woo-tour' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> ''
				),
			)
		);
		$settings['js_css_settings'] = array(
			'title'					=> esc_html__( 'Js & Css file', 'woo-tour' ),
			'description'			=> '',
			'fields'				=> array(
				array(
					'id' 			=> 'wt_fontawesome',
					'label'			=> esc_html__( 'Turn off Font Awesome', 'woo-tour' ),
					'description'	=> esc_html__( "Turn off loading plugin's Font Awesome. Check if your theme has already loaded this library", 'woo-tour' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_boostrap_css',
					'label'			=> esc_html__( 'Turn off Bootstrap Css file', 'woo-tour' ),
					'description'	=> esc_html__( "Turn off loading plugin's Bootstrap library. Check if your theme has already loaded this library", 'woo-tour' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_googlefont_js',
					'label'			=> esc_html__( 'Turn off Google Font', 'woo-tour' ),
					'description'	=> esc_html__( "Turn off loading Google Font", 'woo-tour' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'wt_plugin_style',
					'label'			=> esc_html__( 'Plugin Style', 'woo-tour' ),
					'description'	=> esc_html__( "Select Off to disable load plugin style", 'woo-tour' ),
					'type'			=> 'select',
					'options'		=> array( 
						'' => esc_html__( 'Default', 'woo-tour' ),
						//'basic' => esc_html__( 'Basic', 'woo-tour' ),
						'off' => esc_html__( 'Off', 'woo-tour' ),
					),
					'default'		=> ''
				),
			)
		);
		$wt_main_purpose = wt_global_main_purpose();
		//echo '<pre>';print_r($settings); echo '</pre>';exit;
		if($wt_main_purpose=='meta'){
			unset ($settings['general']['fields'][6],$settings['general']['fields'][9]);
			unset ($settings['single_tour']);
			$settings['single_tour'] = array(
				'title'					=> esc_html__( 'Single Tour', 'woo-tour' ),
				'description'			=> esc_html__( '', 'woo-tour' ),
				'fields'				=> array(
					array(
						'id' 			=> 'wt_slayout_purpose',
						'label'			=> esc_html__( 'Default Layout Purpose', 'woo-tour' ),
						'description'	=> esc_html__( 'Select default layout of single event', 'woo-tour' ),
						'type'			=> 'select',
						'options'		=> array( 
							'woo' => esc_html__( 'WooCommere', 'woo-tour' ),
							'tour' => esc_html__( 'Tour', 'woo-tour' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'wt_metaposition',
						'label'			=> esc_html__( 'Meta position', 'woo-tour' ),
						'description'	=> esc_html__( 'Select position of tour meta', 'woo-tour' ),
						'type'			=> 'select',
						'options'		=> array( 
							'below' => esc_html__( 'Below Title', 'woo-tour' ),
							'above' => esc_html__( 'Above Accompanied service info', 'woo-tour' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'wt_ssocial',
						'label'			=> esc_html__( 'Show Social Share', 'woo-tour' ),
						'description'	=> esc_html__( 'Show/hide Social Share section', 'woo-tour' ),
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'Show', 'woo-tour' ),
							'off' => esc_html__( 'Hide', 'woo-tour' ),
						),
						'default'		=> ''
					),
					
					array(
						'id' 			=> 'wt_show_sdate',
						'label'			=> esc_html__( 'Show Special date in', 'woo-tour' ),
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'List', 'woo-tour' ),
							'calendar' => esc_html__( 'Calendar', 'woo-tour' ),
						),
						'default'		=> ''
					),
					
					array(
						'id' 			=> 'wt_default_adl',
						'label'			=> esc_html__( 'Default max quantity of adult can select' , 'woo-tour' ),
						'description'	=> esc_html__( 'Enter number, default 20', 'woo-tour' ),
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> esc_html__( '', 'woo-tour' )
					),
					array(
						'id' 			=> 'wt_default_child',
						'label'			=> esc_html__( 'Default max quantity of Children can select' , 'woo-tour' ),
						'description'	=> esc_html__( 'Enter number, default 20', 'woo-tour' ),
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> esc_html__( '', 'woo-tour' )
					),
					array(
						'id' 			=> 'wt_default_inf',
						'label'			=> esc_html__( 'Default max quantity of Infant can select ' , 'woo-tour' ),
						'description'	=> esc_html__( 'Enter number, default 20', 'woo-tour' ),
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> esc_html__( '', 'woo-tour' )
					),
					array(
						'id' 			=> 'wt_def_childf',
						'label'			=> esc_html__( 'Default show Children field', 'woo-tour' ),
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'Show', 'woo-tour' ),
							'off' => esc_html__( 'Hide', 'woo-tour' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'wt_def_intff',
						'label'			=> esc_html__( 'Default show Infant field', 'woo-tour' ),
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'Show', 'woo-tour' ),
							'off' => esc_html__( 'Hide', 'woo-tour' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'wt_disable_quantity',
						'label'			=> esc_html__( 'Remove all select quantity field', 'woo-tour' ),
						'description'	=> '',
						'type'			=> 'select',
						'options'		=> array( 
							'' => esc_html__( 'No', 'woo-tour' ),
							'yes' => esc_html__( 'Yes', 'woo-tour' ),
						),
						'default'		=> ''
					),
					array(
						'id' 			=> 'wt_disable_book',
						'label'			=> esc_html__( 'User need book before' , 'woo-tour' ),
						'description'	=> esc_html__( 'This feature allow user only can booking tour before X day from now', 'woo-tour' ),
						'type'			=> 'text',
						'default'		=> '',
						'placeholder'	=> esc_html__( 'Enter number', 'woo-tour' )
					),
					
				)
			);
		}
		$settings = apply_filters( 'wootours_fields', $settings );
		return $settings;
	}
	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings() {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {
				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'wootours' );
				foreach( $data['fields'] as $field ) {
					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}
					// Register field
					$option_name = $this->settings_base . $field['id'];
					register_setting( 'wootours', $option_name, $validation );
					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'wootours', $section, array( 'field' => $field ) );
				}
			}
		}
	}
	public function settings_section( $section ) {
		$html = '<p class="'.$section['id'].'"> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}
	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field( $args ) {
		$field = $args['field'];
		$html = '';
		$option_name = $this->settings_base . $field['id'];
		$option = get_option( $option_name );
		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}
		switch( $field['type'] ) {
			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;
			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;
			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;
			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;
			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;
			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;
			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;
			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;
			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . esc_html__( 'Upload an image' , 'woo-tour' ) . '" data-uploader_button_text="' . esc_html__( 'Use image' , 'woo-tour' ) . '" class="image_upload_button button" value="'. esc_html__( 'Upload new image' , 'woo-tour' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. esc_html__( 'Remove image' , 'woo-tour' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;
			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;
		}
		switch( $field['type'] ) {
			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;
			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}
		echo $html;
	}
	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}
	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page() {
		// Build page HTML
		$html = '<div class="wrap" id="wootours">' . "\n";
			$html .= '<h2>' . esc_html__( 'WooTours Settings' , 'woo-tour' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";
				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					//$html .= '<li><a class="tab all current" href="#standard">' . esc_html__( 'All' , 'woo-tour' ) . '</a></li>' . "\n";
					foreach( $this->settings as $section => $data ) {
						$html .= '<li><a class="tab" href="#' . $section . '">' . $data['title'] . '</a> <span>|</span></li>' . "\n";
					}
				$html .= '</ul>' . "\n";
				$html .= '<div class="clear"></div>' . "\n";
				// Get settings fields
				ob_start();
				settings_fields( 'wootours' );
				do_settings_sections( 'wootours' );
				$html .= ob_get_clean();
				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( esc_html__( 'Save Settings' , 'woo-tour' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";
		echo $html;
	}
}
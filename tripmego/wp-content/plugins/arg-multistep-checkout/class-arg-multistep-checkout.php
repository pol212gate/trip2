<?php
namespace argMC;

class WooCommerceCheckout
{
    private static $options         = array(); 
    private static $defaultOptions  = array();		


    /**
     * Plugin activation
     * @return void	 
     */
    public static function activate()
    {
        self::checkRequirements();
    }


    /**
     * Check plugin requirements
     * @return void	 
     */
    private static function checkRequirements()
    {
        delete_option('arg-mc-admin-error');

        //Detect WooCommerce plugin
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            //Load the plugin's translated strings
            load_plugin_textdomain('argMC', false, dirname(ARG_MC_BASENAME) . '/languages');

            $error = '<strong>' . sprintf(__('%s %s requires WooCommerce Plugin to be installed and activated.' , 'argMC'), ARG_MC_PLUGIN_NAME, ARG_MC_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s" target="_blank">install WooCommerce Plugin</a>.', 'argMC'), 'https://wordpress.org/plugins/woocommerce/');

            update_option('arg-mc-admin-error', $error);	
        }
    }	


    /**
     * Initialize WordPress hooks
     * @return void	 
     */
    public static function initHooks()
    {
        //After setup theme
        add_action('after_setup_theme', array('argMC\WooCommerceCheckout', 'setup'));

        //Init
        add_action('init', array('argMC\WooCommerceCheckout', 'init'));	
		
        //Admin init
        add_action('admin_init', array('argMC\WooCommerceCheckout', 'adminInit'));

        //Admin notices
        add_action('admin_notices', array('argMC\WooCommerceCheckout', 'adminNotices'));		

        //Admin menu
        add_action('admin_menu', array('argMC\WooCommerceCheckout', 'adminMenu'));

        //Scripts & styles
        add_action('admin_enqueue_scripts', array('argMC\WooCommerceCheckout', 'enqueueScriptAdmin'));		
        add_action('wp_enqueue_scripts', array('argMC\WooCommerceCheckout', 'enqueueScript'));   

        add_action('wp_head', array('argMC\WooCommerceCheckout', 'loadStyle'));

        //WooCommerce
        add_filter('woocommerce_locate_template', array('argMC\WooCommerceCheckout', 'locateTemplate'), 20, 3);

        add_action('woocommerce_checkout_login_form', 'woocommerce_checkout_login_form');			
        add_action('woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form');	

        add_action('woocommerce_order_review', 'woocommerce_order_review');
        add_action('woocommerce_checkout_payment', 'woocommerce_checkout_payment', 20);
		
		//Custom actions
		add_action('arg_checkout_customer_details', array('argMC\WooCommerceCheckout', 'customerDetails'));

        //Ajax login
        add_action('wp_ajax_arg_mc_login', array('argMC\WooCommerceCheckout', 'login'));
        add_action('wp_ajax_nopriv_arg_mc_login', array('argMC\WooCommerceCheckout', 'login'));
		
		//Ajax register
		add_action('wp_ajax_arg_mc_register', array('argMC\WooCommerceCheckout', 'register'));
		add_action('wp_ajax_nopriv_arg_mc_register', array('argMC\WooCommerceCheckout', 'register'));

        //Plugins page
        add_filter('plugin_row_meta', array('argMC\WooCommerceCheckout', 'pluginRowMeta'), 10, 2);
        add_filter('plugin_action_links_' . ARG_MC_BASENAME, array('argMC\WooCommerceCheckout', 'actionLinks'));

        //Admin page
        $page = filter_input(INPUT_GET, 'page');
        if (!empty($page) && $page == ARG_MC_MENU_SLUG) {
            add_filter('admin_footer_text', array('argMC\WooCommerceCheckout', 'adminFooter'));		
        }
    }


    /**
     * Plugin setup
     * @return void
     */	
    public static function setup()
    {		
		
        //Avada Theme Settings
        remove_action('woocommerce_before_checkout_form', 'avada_woocommerce_checkout_coupon_form');
        remove_action('woocommerce_checkout_before_customer_details', 'avada_woocommerce_checkout_before_customer_details');		
        remove_action('woocommerce_checkout_after_customer_details', 'avada_woocommerce_checkout_after_customer_details');
        remove_action('woocommerce_checkout_billing', 'avada_woocommerce_checkout_billing', 20);
        remove_action('woocommerce_checkout_shipping', 'avada_woocommerce_checkout_shipping', 20);
		remove_action('woocommerce_checkout_after_order_review', 'avada_woocommerce_checkout_after_order_review', 20);
		
		if (class_exists('Avada_Woocommerce')) {
			global $avada_woocommerce;

			if (!empty($avada_woocommerce) && $avada_woocommerce instanceof \Avada_Woocommerce) {
				remove_action('woocommerce_before_checkout_form', array($avada_woocommerce, 'checkout_coupon_form'));
				remove_action('woocommerce_checkout_before_customer_details', array($avada_woocommerce, 'checkout_before_customer_details'));
				remove_action('woocommerce_checkout_after_customer_details', array($avada_woocommerce, 'checkout_after_customer_details'));
				remove_action('woocommerce_checkout_billing', array($avada_woocommerce, 'checkout_billing'), 20);
				remove_action('woocommerce_checkout_shipping', array($avada_woocommerce, 'checkout_shipping'), 20);
				remove_action('woocommerce_checkout_after_order_review', array($avada_woocommerce, 'checkout_after_order_review'), 20);		
			}
		}				
    }


    /**
     * Init
     * @return void	 
     */	
    public static function init()
    {	
        //Load the plugin's translated strings
        load_plugin_textdomain('argMC', false, dirname(ARG_MC_BASENAME) . '/languages');

		//Init variables
        self::initVariables();					
    }	
	
	
    /**
     * Admin init
     * @return void	 
     */
    public static function adminInit()
    {
        //Check plugin requirements
        self::checkRequirements();
    }


    /**
     * Admin notices
     * @return void	 
     */	
    public static function adminNotices()
    {
        if (get_option('arg-mc-admin-error')) {
            $class      = 'notice notice-error';
            $message    = get_option('arg-mc-admin-error');

            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }


    /**
     * Admin menu
     * @return void	 
     */ 	
    public static function adminMenu()
    {
        add_submenu_page(
            'woocommerce',
            ARG_MC_PLUGIN_NAME,
            ARG_MC_PLUGIN_NAME,
            'manage_options',
            ARG_MC_MENU_SLUG,
            array('argMC\WooCommerceCheckout', 'adminOptions')
        );		
    }	


    /**
     * Enqueue scripts and styles for the admin
     * @return void
     */
    public static function enqueueScriptAdmin()
    {
        //Admin page
        $page = filter_input(INPUT_GET, 'page');
        if (empty($page) || $page !== ARG_MC_MENU_SLUG) {
            return;
        }

        //Color picker styles
        wp_enqueue_style('wp-color-picker');

        //Plugin admin styles
        wp_enqueue_style('arg-mc-styles-admin', ARG_MC_DIR_URL . 'css/styles-admin.css', array(), ARG_MC_VERSION);           

        //Color picker script
        wp_enqueue_script('wp-color-picker');

        //Plugin admin script
        wp_enqueue_script('arg-mc-scripts-admin', ARG_MC_DIR_URL . 'js/scripts-admin.js', array('jquery'), ARG_MC_VERSION, true);
    }


    /**
     * Enqueue scripts and styles for the front end
     * @return void
     */     
    public static function enqueueScript()
    {
        //Check if is checkout page
        if (function_exists('is_checkout') && is_checkout()) {
            //Custom fonts
            wp_enqueue_style('arg-mc-icons', ARG_MC_DIR_URL . 'icons/css/arg-mc-icons.css', array(), ARG_MC_VERSION);

            //jQuery Validation Engine styles
            wp_enqueue_style('arg-mc-jquery-validation-engine-css', ARG_MC_DIR_URL . 'css/validationEngine.jquery.css', array(), 'v2.6.2');

            //Plugin styles
            wp_enqueue_style('arg-mc-styles', ARG_MC_DIR_URL . 'css/styles.css', array(), ARG_MC_VERSION);

            //Tabs layout styles
			$tabsLayout = 'tabs-square';
			
            if (!empty(self::$options['tabs_layout']) && in_array(self::$options['tabs_layout'], array('tabs-square', 'tabs-arrow', 'tabs-arrow-alt', 'tabs-progress-bar'))) {
				$tabsLayout = self::$options['tabs_layout'];
			}
			
			switch ($tabsLayout) {
				case 'tabs-square':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-square.css', array(), ARG_MC_VERSION);
					break;
					
				case 'tabs-arrow':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-arrow.css', array(), ARG_MC_VERSION);
					break;
					
				case 'tabs-arrow-alt':
					wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-arrow-alt.css', array(), ARG_MC_VERSION);
					break;	
					
				case 'tabs-progress-bar':
				    wp_enqueue_style('arg-mc-styles-tabs', ARG_MC_DIR_URL . 'css/styles-tabs-progress-bar.css', array(), ARG_MC_VERSION);
				    break;
			}

            //Woocommerce styles
            if (!empty(self::$options['overwrite_woo_styles'])) {
                wp_enqueue_style('arg-mc-styles-woocommerce', ARG_MC_DIR_URL . 'css/styles-woocommerce.css', array(), ARG_MC_VERSION);
            }

            //jQuery Validation Engine script
            wp_register_script('arg-mc-jquery-validation-engine-en-js', ARG_MC_DIR_URL . 'js/jquery.validationEngine-en.js', array('jquery'), 'v2.6.2', true);

            wp_localize_script('arg-mc-jquery-validation-engine-en-js', 'argmcJsVars', array(
                'errorRequiredText'     => self::$options['error_required_text'],
                'errorRequiredCheckbox' => self::$options['error_required_checkbox'],
                'errorEmail'            => self::$options['error_email'],
				'errorPhone'            => self::$options['error_phone'],
				'errorZip'            	=> self::$options['error_zip']
            ));		

            wp_enqueue_script('arg-mc-jquery-validation-engine-en');
            wp_enqueue_script('arg-mc-jquery-validation-engine-js', ARG_MC_DIR_URL . 'js/jquery.validationEngine.js', array('jquery', 'arg-mc-jquery-validation-engine-en-js'), 'v2.6.2', true);	

            //Plugin script
            wp_register_script('arg-mc-scripts', ARG_MC_DIR_URL . 'js/scripts.js', array('jquery', 'wc-checkout', 'arg-mc-jquery-validation-engine-js', 'select2'), ARG_MC_VERSION, true);

            wp_localize_script('arg-mc-scripts', 'argmcJsVars', array(
                'ajaxURL'       => admin_url('admin-ajax.php'),
                'loginNonce'    => wp_create_nonce('login-nonce'),
				'registerNonce'	=> wp_create_nonce('register-nonce')
            ));
            wp_enqueue_script('arg-mc-scripts');
            
            //Custom checkout step
            if (!empty(self::$options['show_order_review']) || !empty(self::$options['show_customer_details_review'])) {
                wp_enqueue_script('arg-custom-steps', ARG_MC_DIR_URL . '/js/scripts-order-review.js', array('arg-mc-scripts'), ARG_MC_VERSION, true);							
            }            
        }
    }


    /**
     * Load custom styles
     * @return void	 
     */
    public static function loadStyle()
    {
       //Check if is checkout page
        if (function_exists('is_checkout') && is_checkout()) {
            global $argOptions;

            $argOptions = self::$options;
            include_once(ARG_MC_DIR_PATH . 'inc/style.php');
        }	
    }	


    /**
     * Load WooCommerce checkout form template file.s
     * @param mixed $template required
     * @param mixed $templateName optional
     * @param mixed $templatePath optional
     * @return mixed
     */      
    public static function locateTemplate($template, $templateName, $templatePath)
    {	
        if ($templateName == 'checkout/review-order.php' && empty(self::$options['show_product_image'])) {
            return $template;
        }

		if ($templateName == 'checkout/form-checkout.php') {
			if (!empty(self::$options['overwrite_form_checkout'])) {		
		
				// Look within passed path within the theme - this is priority.
				$templateTheme = locate_template(array(
					trailingslashit($templatePath) . $templateName,
					$templateName
				));
				
				if (!empty($templateTheme)) {
					return $templateTheme; 
				}
			}
			

			if (!empty(self::$options['remove_all_hooks'])) {
				$templateName = 'checkout/form-checkout-hooks-removed.php';
			}
		}
        
        if (file_exists(ARG_MC_DIR_PATH . 'woocommerce/' . $templateName)) {
            $template = ARG_MC_DIR_PATH . 'woocommerce/' . $templateName;
			
			return $template; 
        }
	
        return $template;               
    }
	
    /**
     * Output the customer details
     */  	
	public static function customerDetails()
	{
		?>
		<div class="argmc-customer-review">
			<div class="argmc-customer-details">
				<h3><?php _e('Customer Details', 'argMC'); ?></h3>
				<ul class="argmc-customer-list">
					<li>
						<div class="argmc-customer-detail"><?php _e('Email:', 'argMC'); ?></div>
						<div class="argmc-customer-email"></div>
					</li>
					<li>
						<div class="argmc-customer-detail"><?php _e('Phone:', 'argMC'); ?></div>
						<div class="argmc-customer-phone"></div>
					</li>
				</ul>
			</div>
			
			<div class="argmc-customer-addresses">
				<div class = "argmc-billing-details">
					<h3><?php _e('Billing Address', 'argMC'); ?></h3>
					<div class="argmc-billing-address"></div>
				</div>
				
				<div class = "argmc-shipping-details">
					<h3><?php _e('Shipping Address', 'argMC'); ?></h3>
					<div class="argmc-shipping-address"></div>
				</div>
			</div>
		</div>
		<?php
	
	}


    /**
     * Initialize global variables
     * @return void	 
     */   
    private static function initVariables()
    {			
        self::$defaultOptions = array(
            'btn_next_text'             => __('Next', 'argMC'),
            'btn_prev_text'             => __('Previous', 'argMC'),
            'btn_submit_text'           => __('Place Order', 'argMC'),
            'btn_skip_login_text'       => __('Skip Login', 'argMC'),
            'error_required_text'       => __('This field is required', 'argMC'),
            'error_required_checkbox'   => __('You must accept the terms and conditions', 'argMC'),
            'error_email'               => __('Invalid email address', 'argMC'),
			'error_phone'               => __('Invalid phone number', 'argMC'),
			'error_zip'                 => __('Please enter a valid postcode/ZIP', 'argMC'),
			'overwrite_form_checkout'	=> 0,
			'remove_all_hooks'          => 0,
			'scrollTopDesktops'         => 0,
			'scrollTopMobiles'          => 0,
            //Important - Do not change steps order
            'steps'                     => array(
                'login' => array(
                    'text'  => __('Login', 'argMC'), 
                    'class' => 'argmc-login-step argmc-skip-validation',
					'data'  => 'login-step'
                ),
                'coupon' => array(
                    'text'  => __('Coupon', 'argMC'),
                    'class' => 'argmc-coupon-step argmc-skip-validation',
					'data'  => 'coupon-step'
					
                ),
                'billing_shipping' => array(
                    'text'  => __('Billing & Shipping', 'argMC'),
                    'class' => 'argmc-billing-shipping-step',
					'data'  => 'billing-shipping-step'
                ),			
                'billing' => array(
                    'text'  => __('Billing', 'argMC'),
                    'class' => 'argmc-billing-step',
					'data'  => 'billing-step'
                ),
                'shipping' => array(
                    'text'  => __('Shipping', 'argMC'),
                    'class' => 'argmc-shipping-step',
					'data'  => 'shipping-step'
                ),
                'order_payment' => array(
                    'text'  => __('Order & Payment', 'argMC'),
                    'class' => 'argmc-order-payment-step',
					'data'  => 'order-payment-step'
                ),					
                'order' => array(
                    'text'  => __('Order', 'argMC'),
                    'class' => 'argmc-order-step',
					'data'  => 'order-step'
                ),
                'payment' => array(
                    'text'  => __('Payment', 'argMC'),
                    'class' => 'argmc-payment-step',
					'data'  => 'payment-step'
                ),
                'order_review' => array(
                    'text'  => __('Order Review', 'argMC'),
                    'class' => 'argmc-order-review-step argmc-skip-validation',
					'data'  => 'order-review-step'
					
                )
            ),
            'footer_text'                   => __('Far far away, behind the word mountains, far from the countries Vokalia and Consonantia.', 'argMC'),
            'wizard_max_width'              => '900px',
            'secondary_font'                => '',
            'secondary_font_weight'         => '600',
            'show_login'                    => 1,
			'show_register'					=> 1,
			'login_layout'					=> 'register-switched-with-login',
			'login_heading'                 => __('Login', 'argMC'),
			'register_heading'             	=> __('Register', 'argMC'),
			'force_login_message'           => '',
			'login_register_top_message'    => '',
            'show_coupon'                   => 1,
            'show_order'                    => 1,
            'show_additional_information'   => 1,
			'show_customer_details_review'  => 0,
            'show_order_review'             => 0,
			'show_order_review_table'       => 0,
            'show_product_image'            => 0,
			'coupon_position'				=> 'default',
            'merge_billing_shipping'        => 1,
            'merge_order_payment'           => 1,
            'tabs_layout'                   => 'tabs-square',			
            'tabs_template'                 => 'tabs-default',
            'tabs_width'                    => 'tabs-equal-width',
            'wizard_color'                  => '#555',
            'accent_color'                  => '#e23636',
            'border_color'                  => '#d9d9d9',
			'overwrite_wizard_buttons'      => 0, 
			'wizard_text_errors_color'      => '#e23636',
			'wizard_button_text_color'      => '#fff',
			'wizard_button_text_opacity'    => '0.7',  
			'next_button_bkg'               => '#e23636',
			'prev_button_bkg'               => '#b4b4b4',
			'place_order_button_bkg'        => '#96c457',
            
            'tab_text_color'                         => '#bbb',
            'tab_bkg_color'                          => '#eee',
            'tab_border_left_color'                  => '#dcdcdc',
            'tab_border_bottom_color'                => '#c9c9c9',
            'number_text_color'                      => '#999',
			'tab_number_bkg_color'                   => '#fff',
            'tab_number_color_hover'                 => '#e23636',
			'tab_number_bkg_color_hover'             => '#fff',
            'tab_text_color_hover'                   => '#000',
            'tab_bkg_color_hover'                    => '#f8f8f8',
            'tab_border_bottom_color_hover'          => '#e23636',
            'show_number_checkmark'                  => 0,		
            'tab_before_arrow_color'                 => '#fff',            
			'tab_adjust_number_position'             => '0px',
			'tab_adjust_checkmark_position'          => '0px',
			'tab_adjust_text_position'               => '0px',
            
            'tab_arrow_alt_text_color'                  => '#bbb',
            'tab_arrow_alt_bkg_color'                   => '#eee',
            'tab_arrow_alt_border_bottom_color'         => '#c9c9c9',
            'tab_arrow_alt_number_text_color'           => '#999',
            'tab_arrow_alt_number_bkg_color'            => '#fff',
            'tab_arrow_alt_number_color_hover'          => '#000',
            'tab_arrow_alt_number_bkg_color_hover'      => '#fff',
            'tab_arrow_alt_text_color_hover'            => '#fff',
            'tab_arrow_alt_bkg_color_hover'             => '#e23636',
            'tab_arrow_alt_border_bottom_color_hover'   => '#c9c9c9',   
            'tab_arrow_alt_completed_bkg_color'         => '#afafaf',
            'tab_arrow_alt_before_arrow_color'          => '#fff', 
            'tab_arrow_alt_show_number_checkmark'       => 0,
            'tab_arrow_alt_adjust_number_position'      => '1px',
            'tab_arrow_alt_adjust_checkmark_position'   => '1px',
            'tab_arrow_alt_adjust_text_position'        => '1px',
            
            'tab_progress_bar_text_color'                   => '#aaa',
            'tab_progress_bar_number_text_color'            => '#fff',           
            'tab_progress_bar_number_bkg_color'             => '#afafaf',            
            'tab_progress_bar_border_bottom_color'          => '#c9c9c9',
            'tab_progress_bar_bkg_color'                    => '#f9f9f9',            
            'tab_progress_bar_number_color_hover'           => '#fff',
            'tab_progress_bar_number_bkg_color_hover'       => '#000',
            'tab_progress_bar_text_color_hover'             => '#000',
            'tab_progress_bar_border_bottom_color_hover'    => '#000',
            'tab_progress_bar_show_number_checkmark'        => 0,
            'tab_progress_bar_adjust_number_position'       => '1px',
            'tab_progress_bar_adjust_checkmark_position'    => '1px',
            'tab_progress_bar_adjust_text_position'         => '0px',
                        
            'overwrite_woo_styles'              => 0,
            'woo_text_color'                    => '#555',
            'woo_label_color'                   => '#4b4b4b',
            'woo_input_border_color'            => '#ddd',
            'woo_input_bkg_color'               => '#f9f9f9',
            'woo_invalid_required_field_border' => '#e23636',
            'woo_invalid_required_field_bkg'    => '#ffefee',
            'woo_validated_field_border'        => "#ddd",
            'woo_button_bkg_color'              => '',
            'woo_button_bkg_color_login'        => '#444',
            'woo_field_border_radius'           => '0px'
		);
	

        $options        = get_option('arg-mc-options'); 
        $defaultOptions = self::$defaultOptions;

        if (!empty($options)) {
            //Merge default options array with options array
            self::setOptions($options, $defaultOptions);
            
            if ($defaultOptions !== $options) {           
                update_option('arg-mc-options', $defaultOptions);
            }
        } else { 		
            update_option('arg-mc-options', self::$defaultOptions);
        } 

        //Set options
        self::$options = $defaultOptions;        
    }

	
    /**
     * Set options
     */	
    public static function setOptions($options, &$defaultOptions) 
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                self::setOptions($options[$key], $defaultOptions[$key]);
            } else { 
                if (array_key_exists($key, $defaultOptions)) { 
                    $defaultOptions[$key] = $value;
                }
            }
        }
    }    


    /**
     * Admin options
     */ 	
    public static function adminOptions()
    {	
        $data = filter_input_array(INPUT_POST);
        
        //Form submit
        if (!empty($data)) { 
           
            $data = array_map('stripslashes_deep', $data);

            if (!empty($data['reset'])) {
                self::$options = self::$defaultOptions;
            } else {				
                foreach ($data as $fieldName => $fieldValue) {
                    if ($fieldName == 'save' || $fieldName == 'reset') {
                        continue;	
                    }

                    if (!array_key_exists($fieldName, self::$options)) {
                        continue;
                    }
                    
                    if ($fieldName == 'steps') {
                        foreach ($fieldValue as $stepName => $stepValue) {
                            self::$options[$fieldName][$stepName]['text'] = $stepValue['text'];
                        }
                    } else {
                        self::$options[$fieldName] = $fieldValue;
                    }
                }
            }

            self::$options = apply_filters('arg-mc-update-options', self::$options);

            update_option('arg-mc-options', self::$options);				
        }

        //Set options
        $options = self::$options;


        //Admin options
        $selectedTab    = 'general';
        $tab            = filter_input(INPUT_GET, 'tab');
        
        if (!empty($tab) && in_array($tab, array('general', 'steps', 'styles'))) {
            $selectedTab = $tab;
        }
        ?>

        <div class="argmc-wrapper">

            <div class="nav-tab-wrapper argmc-tab-wrapper">
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=general" class="nav-tab<?php echo $selectedTab == 'general' ? ' nav-tab-active' : ''; ?>"><?php _e('General Settings', 'argMC'); ?></a>
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=steps" class="nav-tab<?php echo $selectedTab == 'steps' ? ' nav-tab-active' : ''; ?>"><?php _e('Wizard Steps', 'argMC'); ?></a>
                <a href="?page=<?php echo ARG_MC_MENU_SLUG; ?>&tab=styles" class="nav-tab<?php echo $selectedTab == 'styles' ? ' nav-tab-active' : ''; ?>"><?php _e('Wizard Styles', 'argMC'); ?></a>
		    </div>

            <form method="post" class="argmc-form">

                <?php
                switch ($selectedTab) {
                    case 'general':
                        ?>
                        <h2 class="argmc-top-heading"><?php _e('General Settings', 'argMC'); ?></h2>
                        <p class="argmc-top-text argmc-text-general-settings"><?php _e('Under the General Settings tab you\'ll find options like: changing buttons text, custom text, wizard width, secondary font family and error messages.', 'argMC'); ?></p>
						
                        <h3><?php _e('Buttons and Custom Text', 'argMC'); ?></h3>

                        <table class="form-table argmc-table-buttons">
                            <tbody>
                                <tr>
                                    <th>
                                        <?php _e('Skip Login Button Text', 'argMC'); ?>
                                        <span class="argmc-description"><?php _e('Change the text of login button.', 'argMC'); ?></span>	
                                    </th>
                                    <td><input type="text" name="btn_skip_login_text" value="<?php echo $options['btn_skip_login_text']; ?>" /></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e('Next Button Text', 'argMC'); ?>
                                        <span class="argmc-description"><?php _e('Change the text of next button.', 'argMC'); ?></span>
                                    </th>
                                    <td><input type="text" name="btn_next_text" value="<?php echo $options['btn_next_text']; ?>" /></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e('Previous Button Text', 'argMC'); ?>
                                        <span class="argmc-description"><?php _e('Change the text of the previous button.', 'argMC'); ?></span>	
                                    </th>
                                    <td><input type="text" name="btn_prev_text" value="<?php echo $options['btn_prev_text']; ?>" /></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e('Place Order Button Text', 'argMC'); ?>
                                        <span class="argmc-description"><?php _e('Change the text of the place order button.', 'argMC'); ?></span>	
                                    </th>
                                    <td><input type="text" name="btn_submit_text" value="<?php echo $options['btn_submit_text']; ?>" /></td>
                                </tr>									
                                <tr>
                                    <th>
                                        <?php _e('Custom Text', 'argMC'); ?>
                                        <span class="argmc-description"><?php _e('Use this option to add an extra text to footer.', 'argMC'); ?></span>
                                    </th>
                                    <td><textarea name="footer_text"><?php echo $options['footer_text']; ?></textarea></td>
                                </tr>
                            </tbody>
                    </table>		

                    <h3><?php _e('Wizard width and secondary font family', 'argMC'); ?></h3>

                    <table class="form-table argmc-table-buttons">
                        <tbody>	
                            <tr>
                                <th>
                                    <?php _e('Wizard Maximum Width', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to set the maximum width of the wizard layout. Set input value to <strong>none</strong> if you want the wizard to expand to the entire layout width.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="wizard_max_width" class="input-field" value="<?php echo $options['wizard_max_width']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Wizard Secondary Font Family', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to set the font family for wizard tabs, headings, lables, buttons, payment metods labels (example: \'Poppins\',sans-serif). Leave it empty if your theme has only one font family.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="secondary_font" class="input-field" value="<?php echo $options['secondary_font']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Wizard Secondary Font Weight', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to set the font weight for wizard tabs, headings, buttons, payment metods labels.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <select name="secondary_font_weight">
                                        <option value="400" <?php selected($options['secondary_font_weight'], '400', true); ?>><?php _e('400', 'argMC'); ?></option>
                                        <option value="500" <?php selected($options['secondary_font_weight'], '500', true); ?>><?php _e('500', 'argMC'); ?></option>
                                        <option value="600" <?php selected($options['secondary_font_weight'], '600', true); ?>><?php _e('600', 'argMC'); ?></option>
                                        <option value="700" <?php selected($options['secondary_font_weight'], '700', true); ?>><?php _e('700', 'argMC'); ?></option>
                                        <option value="800" <?php selected($options['secondary_font_weight'], '800', true); ?>><?php _e('800', 'argMC'); ?></option>
                                        <option value="900" <?php selected($options['secondary_font_weight'], '900', true); ?>><?php _e('900', 'argMC'); ?></option>
                                    </select>										
                                </td>										
                            </tr>									
                        </tbody>
                    </table>

                    <h3><?php _e('Validation Error Messages', 'argMC'); ?></h3>

                    <table class="form-table argmc-table-buttons">
                        <tbody>
                            <tr>
                                <th>
                                   <?php _e('Required Field', 'argMC'); ?>
                                   <span class="argmc-description"><?php _e('Change the text of the required field error message.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="error_required_text" class="input-field" value="<?php echo $options['error_required_text']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                   <?php _e('Required Checkbox', 'argMC'); ?>
                                   <span class="argmc-description"><?php _e('Change the text of the required checkbox error message.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="error_required_checkbox" class="input-field" value="<?php echo $options['error_required_checkbox']; ?>" /></td>
                            </tr>																	
                            <tr>
                                <th>
                                    <?php _e('Invalid Email Address', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the text of the invalid email address error message.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="error_email" class="input-field" value="<?php echo $options['error_email']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Invalid Phone Number', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the text of the invalid phone number error message.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="error_phone" class="input-field" value="<?php echo $options['error_phone']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Invalid Postcode', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the text of the invalid postcode error message.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="error_zip" class="input-field" value="<?php echo $options['error_zip']; ?>" /></td>
                            </tr>								
                        </tbody>
                    </table>
					
					<h3 style="margin: 80px 0 0px;">Scroll to Top Options</h3>
					<table class="form-table argmc-table-buttons">
						<tbody>
							 <tr>
                                <th style="width: 465px;">
                                    <?php _e('Scroll to the top of the wizard - desktops adjustments', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this options if you want to scroll to the top of the wizard instead the top of the page when the user will click on the navigation buttons. Default value is 0 - that means it will scroll to the top of the page. Change this value to any value you want (usualy 60 - for a better tabs visibility) and it will scroll to the top of the wizard.', 'argMC'); ?></span>
                                </th>
                                <td><input style="min-width: 86px; width: 86px;" type="text" name="scrollTopDesktops" class="input-field" value="<?php echo $options['scrollTopDesktops']; ?>" /></td>
                            </tr>
							<tr>
                                <th style="width: 465px;">
                                    <?php _e('Scroll to the top of the wizard - mobiles adjustments', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this options if you want to scroll to the top of the wizard instead the top of the page when the user will click on the navigation buttons. Default value is 0 - that means it will scroll to the top of the page. Change this value to any value you want (usualy 30 - for a better tabs visibility) and it will scroll to the top of the wizard.', 'argMC'); ?></span>
                                </th>
                                <td><input style="min-width: 86px; width: 86px;" type="text" name="scrollTopMobiles" class="input-field" value="<?php echo $options['scrollTopMobiles']; ?>" /></td>
                            </tr>
						</tbody>
					</table>
					
					<table class="form-table argmc-table-buttons">
						<tbody>
							<tr>
								<th style="width: 465px;">
									<?php _e('Overwrite WooCommerce form-checkout.php plugin template', 'argMC'); ?>
									<span class="argmc-description"><?php _e('Turn on this option to allow the theme to overwrite the plugin form-checkout.php template (in this template we transform the checkout into a multistep and you can copy this file into your theme -> woocommerce -> checkout and make the changes to your needs).', 'argMC'); ?></span>
								</th>
								<td>
									<div class="radio-buttons-wrapper">
										<input id="overwrite-form-checkout-yes" class="input-radio-button" type="radio" name="overwrite_form_checkout" value="1" <?php checked($options['overwrite_form_checkout'], 1); ?>>
										<label class="input-label-button label-button-left" for="overwrite-form-checkout-yes">
											<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
										</label>

										<input id="overwrite-form-checkout-no" class="input-radio-button" type="radio" name="overwrite_form_checkout" value="0" <?php checked($options['overwrite_form_checkout'], 0); ?>>
										<label class="input-label-button label-button-right" for="overwrite-form-checkout-no">
											<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
										</label>																						
									</div>			
								</td>
							</tr>	
						</tbody>
					</table>
					
					<table class="form-table argmc-table-buttons">
						<tbody>
							<tr>
								<th style="width: 465px;">
									<?php _e('Remove all hooks if some steps content doesn\'t show', 'argMC'); ?>
									<span class="argmc-description"><?php _e('Turn on this option to remove all the hooks (before/after step actions) from the checkout page if some of your steps content doesn\'t show.', 'argMC'); ?></span>
								</th>
								<td>
									<div class="radio-buttons-wrapper">
										<input id="remove-all-hooks-yes" class="input-radio-button" type="radio" name="remove_all_hooks" value="1" <?php checked($options['remove_all_hooks'], 1); ?>>
										<label class="input-label-button label-button-left" for="remove-all-hooks-yes">
											<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
										</label>

										<input id="remove-all-hooks-no" class="input-radio-button" type="radio" name="remove_all_hooks" value="0" <?php checked($options['remove_all_hooks'], 0); ?>>
										<label class="input-label-button label-button-right" for="remove-all-hooks-no">
											<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
										</label>																						
									</div>			
								</td>
							</tr>	
						</tbody>
					</table>
					

                    <?php
                    break;

				case 'steps':
                    ?>
                    <h2 class="argmc-top-heading"><?php _e('Wizard Steps Options', 'argMC'); ?></h2>
                    <p class="argmc-top-text"><?php _e('These options refer to your checkout steps and all their content can be found here:', 'argMC'); ?></p>

                    <table class="form-table argmc-table-steps">
                        <thead>
                            <tr>
                                <th><?php _e('Step Name', 'argMC'); ?></th>
                                <th><?php _e('Template', 'argMC'); ?></th>
                                <th><?php _e('Show/Hide Step', 'argMC'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="steps[login][text]" value="<?php echo $options['steps']['login']['text']; ?>" /></td>
                                <td><input type="text" name="steps[login][template]" readonly value="{login_form}" /></td>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-login" class="input-radio-button" type="radio" name="show_login" value="1" <?php checked($options['show_login'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-login">
                                            <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-login" class="input-radio-button" type="radio" name="show_login" value="0" <?php checked($options['show_login'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-login">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[coupon][text]" value="<?php echo $options['steps']['coupon']['text']; ?>" /></td>
                                <td><input type="text" name="steps[coupon][template]" readonly value="{coupon_form}" /></td>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-coupon" class="input-radio-button" type="radio" name="show_coupon" value="1" <?php checked($options['show_coupon'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-coupon">
                                             <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-coupon" class="input-radio-button" type="radio" name="show_coupon" value="0" <?php checked($options['show_coupon'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-coupon">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[billing][text]" value="<?php echo $options['steps']['billing']['text']; ?>" /></td>
                                <td><input type="text" name="steps[billing][template]" readonly value="{billing_form}" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[shipping][text]" value="<?php echo $options['steps']['shipping']['text']; ?>" /></td>
                                <td><input type="text" name="steps[shipping][template]" readonly value="{shipping_form}" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[order][text]" value="<?php echo $options['steps']['order']['text']; ?>" /></td>
                                <td><input type="text" name="steps[order][template]" readonly value="{order_details}" /></td>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-order" class="input-radio-button" type="radio" name="show_order" value="1" <?php checked($options['show_order'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-order">
                                            <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-order" class="input-radio-button" type="radio" name="show_order" value="0" <?php checked($options['show_order'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-order">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[payment][text]" value="<?php echo $options['steps']['payment']['text']; ?>" /></td>
                                <td><input type="text" name="steps[payment][template]" readonly value="{payment_details}" /></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="steps[order_review][text]" value="<?php echo $options['steps']['order_review']['text']; ?>" /></td>
                                <td><input type="text" name="steps[order_review][template]" readonly value="{order_review}" /></td>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-order-review" class="input-radio-button" type="radio" name="show_order_review" value="1" <?php checked($options['show_order_review'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-order-review">
                                            <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-order-review" class="input-radio-button" type="radio" name="show_order_review" value="0" <?php checked($options['show_order_review'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-order-review">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>
                            </tr>									
																							
                        </tbody>
                    </table>

                    <table class="form-table combine-tabs-table">
                        <tbody>
                            <tr>
                                <th>
                                    <?php _e('Register Form', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to show the registration form on the login step. On the login step will be a single content area with two sections(login form and registration form), each associated with a heading.</br>', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-register" class="input-radio-button" type="radio" name="show_register" value="1" <?php checked($options['show_register'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-register">
                                            <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-register" class="input-radio-button" type="radio" name="show_register" value="0" <?php checked($options['show_register'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-register">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>	
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Login & Register Layouts', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the login & register layouts(1. Register Switched with Login - The user can click on headings to swap between content that is separated into logical sections; 2. Register on Right Side).', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <select style="width: 210px;" name="login_layout" class="argmc-login-layout">
                                        <option value="register-switched-with-login" <?php selected($options['login_layout'], 'register-switched-with-login', true); ?>><?php _e('Register Switched with Login', 'argMC'); ?></option>										
                                        <option value="register-on-right-side" <?php selected($options['login_layout'], 'register-on-right-side', true); ?>><?php _e('Register on Right Side', 'argMC'); ?></option>
                                    </select>
                                </td>	
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Login & Register Top Message', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option if you want to show a top message above the login/register sections (this option will apply only if you show the registration form on the login step).', 'argMC'); ?></span>
                                </th>
                                <td><textarea name="login_register_top_message" class="input-field"><?php echo $options['login_register_top_message']; ?></textarea></td>
                            </tr>
							
							<tr>
                                <th>
                                    <?php _e('Login Heading', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the login heading. This option will apply only if you show the registration form on the login step.', 'argMC'); ?></span>
                                </th>
                                <td><input style="width: 210px;" type="text" name="login_heading" class="input-field" value="<?php echo $options['login_heading']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Register Heading', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the register heading. This option will apply only if you show the registration form on the login step.', 'argMC'); ?></span>
                                </th>
                                <td><input style="width: 210px;" type="text" name="register_heading" class="input-field" value="<?php echo $options['register_heading']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Force Users to Login or Register', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option if you want to force users to login/register before navigate to the next step (this option will apply only if you enter an error message and show the registration form on the login step). Leave the box empty and this option will have no effect.', 'argMC'); ?></span>
                                </th>
                                <td><textarea name="force_login_message" class="input-field"><?php echo $options['force_login_message']; ?></textarea></td>
                            </tr>							
                        </tbody>
                    </table>
					
                    <table class="form-table combine-tabs-table">
                        <tbody>
                            <tr>
                                <th>
                                    <?php _e('Additional Information', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to hide additional information block from <br> the shipping section.</br>', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-additional-information" class="input-radio-button" type="radio" name="show_additional_information" value="1" <?php checked($options['show_additional_information'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-additional-information">
                                            <span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
                                        </label>

                                        <input id="hide-additional-information" class="input-radio-button" type="radio" name="show_additional_information" value="0" <?php checked($options['show_additional_information'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="hide-additional-information">
                                            <span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>	
                            </tr>								
                        </tbody>
                    </table>

                    <table class="form-table combine-tabs-table">
						<tbody>
							<tr>
								<th>
									<?php _e('Move the Coupon Form to Another Step', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to move the coupon form on a different step. This option only applies if the coupon step is hidden. Please <b>read more</b> about this option in the documentation before using it.', 'argMC'); ?></span>									
								</th>								
								<td>
									<select style="width: 210px;" name="coupon_position" class="argmc-tabs-layout">
										<option value="default" <?php selected($options['coupon_position'], 'default', true); ?>><?php _e('Default - Coupon Step', 'argMC'); ?></option>
										<option value="before-order-review-table" <?php selected($options['coupon_position'], 'before-order-review-table', true); ?>><?php _e('Before Order Review Table', 'argMC'); ?></option>
										<option value="after-order-review-table" <?php selected($options['coupon_position'], 'after-order-review-table', true); ?>><?php _e('After Order Review Table', 'argMC'); ?></option>
										<option value="before-payment" <?php selected($options['coupon_position'], 'before-payment', true); ?>><?php _e('Before Payment Methods', 'argMC'); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
                    <table class="form-table combine-tabs-table">
                        <tbody>
                            <tr class="first-row">
                                <th>
                                    <?php _e('Combine Billing and Shipping Steps?', 'argMC'); ?>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="merge-billing-shipping-yes" class="input-radio-button" type="radio" name="merge_billing_shipping" value="1" <?php checked($options['merge_billing_shipping'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="merge-billing-shipping-yes">
                                           <span class="label-button-text"><?php _e('Yes', 'argMC'); ?></span>
                                        </label>

                                        <input id="merge-billing-shipping-no" class="input-radio-button" type="radio" name="merge_billing_shipping" value="0" <?php checked($options['merge_billing_shipping'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="merge-billing-shipping-no">
                                            <span class="label-button-text"><?php _e('No', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>	
                            </tr>									
                            <tr class="second-row">
                                <td colspan="2">
                                    <div class="combine-tables-step-name"><?php _e('If so, define your new step name:', 'argMC'); ?></div>
                                    <input type="text" name="steps[billing_shipping][text]" value="<?php echo $options['steps']['billing_shipping']['text']; ?>" />
                                    <input type="text" name="steps[billing_shipping][template]" readonly value="{billing_form} {shipping_form}" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="form-table combine-tabs-table">
                        <tbody>
                            <tr class="first-row">
                                <th>
                                    <?php _e('Combine Payment and Order Details Steps?', 'argMC'); ?>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="merge-order-payment-yes" class="input-radio-button" type="radio" name="merge_order_payment" value="1" <?php checked($options['merge_order_payment'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="merge-order-payment-yes">
                                           <span class="label-button-text"><?php _e('Yes', 'argMC'); ?></span>
                                        </label>

                                        <input id="merge-order-payment-no" class="input-radio-button" type="radio" name="merge_order_payment" value="0" <?php checked($options['merge_order_payment'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="merge-order-payment-no">
                                            <span class="label-button-text"><?php _e('No', 'argMC'); ?></span>
                                        </label>
                                    </div>
                                </td>	
                            </tr>									
                            <tr class="second-row">
                                <td colspan="2">
                                    <div class="combine-tables-step-name"><?php _e('If so, define your new step name:', 'argMC'); ?></div>
                                    <input type="text" name="steps[order_payment][text]" value="<?php echo $options['steps']['order_payment']['text']; ?>" />
                                    <input type="text" name="steps[order_payment][template]" readonly value="{order_details} {payment_details}" />
                                </td>
                            </tr>				
                        </tbody>
                    </table>
					
                    <table class="form-table combine-tabs-table">
                        <tbody>
						
						<tr>
							<th>
								<?php _e('Product Thumbnail on Order Table', 'argMC'); ?>
								<span class="argmc-description"><?php _e('Use this option to show/hide product thumbnail in the checkout order table.', 'argMC'); ?></span>
							</th>
							<td>
								<div class="radio-buttons-wrapper">
									<input id="show-product-image" class="input-radio-button" type="radio" name="show_product_image" value="1" <?php checked($options['show_product_image'], 1); ?>>
									<label class="input-label-button label-button-left" for="show-product-image">
										<span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
									</label>

									<input id="hide-product-image" class="input-radio-button" type="radio" name="show_product_image" value="0" <?php checked($options['show_product_image'], 0); ?>>
									<label class="input-label-button label-button-right" for="hide-product-image">
										<span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
									</label>
								</div>
							</td>	
						</tr>	
							
						<tr>
							<th>
								<?php _e('Customer Details Review after the Payment Methods', 'argMC'); ?>
								<span class="argmc-description"><?php _e('Use this option to show customer details review (email, phone, addresses) after the payment methods.', 'argMC'); ?></span>
							</th>
							<td>
								<div class="radio-buttons-wrapper">
									<input id="show-customer-details-review" class="input-radio-button" type="radio" name="show_customer_details_review" value="1" <?php checked($options['show_customer_details_review'], 1); ?>>
									<label class="input-label-button label-button-left" for="show-customer-details-review">
										<span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
									</label>

									<input id="hide-customer-details-review" class="input-radio-button" type="radio" name="show_customer_details_review" value="0" <?php checked($options['show_customer_details_review'], 0); ?>>
									<label class="input-label-button label-button-right" for="hide-customer-details-review">
										<span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
									</label>
								</div>
							</td>	
						</tr>							

						<tr>
							<th>
								<?php _e('Order Table on Order Review Step', 'argMC'); ?>
								<span class="argmc-description"><?php _e(' Use this option to show/hide order review table on the order review step. This option works only if you decide to <strong>hide the order step</strong> from the first section using the "Show/Hide Step" options.', 'argMC'); ?></span>
							</th>
							<td>
								<div class="radio-buttons-wrapper">
									<input id="show-order-review-table" class="input-radio-button" type="radio" name="show_order_review_table" value="1" <?php checked($options['show_order_review_table'], 1); ?>>
									<label class="input-label-button label-button-left" for="show-order-review-table">
										<span class="label-button-text"><?php _e('Show', 'argMC'); ?></span>
									</label>

									<input id="hide-order-review-table" class="input-radio-button" type="radio" name="show_order_review_table" value="0" <?php checked($options['show_order_review_table'], 0); ?>>
									<label class="input-label-button label-button-right" for="hide-order-review-table">
										<span class="label-button-text"><?php _e('Hide', 'argMC'); ?></span>
									</label>
								</div>
							</td>	
						</tr>
                            									
                        </tbody>
                    </table>					
                    <?php						
                    break;

				case 'styles':
                    ?>

                    <h2 class="argmc-top-heading"><?php _e('Multistep Checkout Styles', 'argMC'); ?></h2>
                    <p class="argmc-top-text"><?php _e('Here you can find the options to change your checkout steps styles:', 'argMC'); ?></p>


                    <h3><?php _e('Wizard styles', 'argMC'); ?></h3>

                    <table class="form-table argmc-table-style">
                        <tbody>
                            <tr>
                                <th>
                                    <?php _e('Wizard Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the color of wizard footer custom text.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="wizard_color" class="color-field" value="<?php echo $options['wizard_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Wizard Accent Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the accent color of the wizard.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="accent_color" class="color-field" value="<?php echo $options['accent_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Wizard Border Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the color of the wizard footer border line.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="border_color" class="color-field" value="<?php echo $options['border_color']; ?>" /></td>
                            </tr>
							
							<tr>
                                <th>
                                    <?php _e('Wizard Validation Error Messages Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the color of validation error messages.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="wizard_text_errors_color" class="color-field" value="<?php echo $options['wizard_text_errors_color']; ?>" /></td>
                            </tr>
							
							<tr>
                                <th>
                                    <?php _e('Change Wizard Buttons Styles (skip login, next, previous, place order)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('By default your theme buttons styles will be applied. Enable this option if you want to change the text/background color of these buttons.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="overwrite-theme-buttons-yes" class="input-radio-button overwrite-wizard-buttons" data-style="overwrite-buttons" type="radio" name="overwrite_wizard_buttons" value="1" <?php checked($options['overwrite_wizard_buttons'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="overwrite-theme-buttons-yes">
                                          	<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                                        </label>

                                        <input id="overwrite-theme-buttons-no" class="input-radio-button overwrite-wizard-buttons" type="radio" data-style="overwrite-buttons-no" name="overwrite_wizard_buttons" value="0" <?php checked($options['overwrite_wizard_buttons'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="overwrite-theme-buttons-no">
                                          	<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                                        </label>																						
                                    </div>			
                                </td>
                            </tr>	

							<tr class="wizard-overwrite-buttons-option">
                                <th>
                                    <?php _e('Wizard Button Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the color of the button text. The theme button text color will be inherited if you leave the input empty.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="wizard_button_text_color" class="color-field" value="<?php echo $options['wizard_button_text_color']; ?>" /></td>
                            </tr>
							<tr class="wizard-overwrite-buttons-option">
                                <th>
                                    <?php _e('Wizard Button Text Transparency on Hover', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the opacity of the text button on hover. The opacity-level describes the transparency-level, where 1 is not transparent at all, 0.5 is 50% see-through, and 0 is completely transparent.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="wizard_button_text_opacity" class="input-field" value="<?php echo $options['wizard_button_text_opacity']; ?>" /></td>
                            </tr>
							<tr class="wizard-overwrite-buttons-option">
                                <th>
                                    <?php _e('Wizard Next/Skip Login Buttons Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the background color of the next/skip login buttons.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="next_button_bkg" class="color-field" value="<?php echo $options['next_button_bkg']; ?>" /></td>
                            </tr>
							<tr class="wizard-overwrite-buttons-option">
                                <th>
                                    <?php _e('Wizard Previous Button Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the background color of the previous button.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="prev_button_bkg" class="color-field" value="<?php echo $options['prev_button_bkg']; ?>" /></td>
                            </tr>
							<tr class="wizard-overwrite-buttons-option">
                                <th>
                                    <?php _e('Wizard Place Order Button Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the background color of the place order button.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="place_order_button_bkg" class="color-field" value="<?php echo $options['place_order_button_bkg']; ?>" /></td>
                            </tr>
                        </tbody>
                    </table>


                    <h3><?php _e('Tabs Styles', 'argMC'); ?></h3>

				   	<table class="form-table argmc-table-style" style="margin: 0;">
                    	<tbody>
                            <tr>
                                <th>
                                    <?php _e('Tabs Layouts', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the layout of your tabs.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <select name="tabs_layout" class="argmc-tabs-layout">
                                        <option value="tabs-square" <?php selected($options['tabs_layout'], 'tabs-square', true); ?>><?php _e('Square', 'argMC'); ?></option>
                                        <option value="tabs-arrow" <?php selected($options['tabs_layout'], 'tabs-arrow', true); ?>><?php _e('Arrow', 'argMC'); ?></option>
    									<option value="tabs-arrow-alt" <?php selected($options['tabs_layout'], 'tabs-arrow-alt', true); ?>><?php _e('Arrow Alt', 'argMC'); ?></option>
    									<option value="tabs-progress-bar" <?php selected($options['tabs_layout'], 'tabs-progress-bar', true); ?>><?php _e('Progress Bar', 'argMC'); ?></option>                                    
                                    </select>
                                </td>	
                            </tr>
                     	</tbody>
                     </table>

                    <table class="form-table argmc-table-style argmc-tab-style argmc-tab-default-style">
                        <tbody>					
                            <tr>
                                <th>
                                    <?php _e('Tabs Text Styles', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the text styles of your tabs.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <select name="tabs_template">
                                        <option value="tabs-default" <?php selected($options['tabs_template'], 'tabs-default', true); ?>><?php _e('Text Inline', 'argMC'); ?></option>
                                        <option value="tabs-text-under" <?php selected($options['tabs_template'], 'tabs-text-under', true); ?>><?php _e('Text Under Number', 'argMC'); ?></option>
                                        <option value="tabs-hide-numbers" <?php selected($options['tabs_template'], 'tabs-hide-numbers', true); ?>><?php _e('Hide Number on Tab', 'argMC'); ?></option>
                                    </select>
                                </td>	
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tabs Width', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change your tabs width.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <select name="tabs_width">
                                        <option value="tabs-equal-width" <?php selected($options['tabs_width'], 'tabs-equal-width', true); ?>><?php _e('Equals', 'argMC'); ?></option>
                                        <option value="tabs-width-auto" <?php selected($options['tabs_width'], 'tabs-width-auto', true); ?>><?php _e('Auto', 'argMC'); ?></option>
                                    </select>
                                </td>	
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="number_text_color" class="color-field" value="<?php echo $options['number_text_color']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_number_bkg_color" class="color-field" value="<?php echo $options['tab_number_bkg_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_text_color" class="color-field" value="<?php echo $options['tab_text_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_bkg_color" class="color-field" value="<?php echo $options['tab_bkg_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Border Left Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the border color between the tabs.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_border_left_color" class="color-field" value="<?php echo $options['tab_border_left_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Border Bottom Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the border color under the tabs.', 'argMC'); ?></span>										
                                </th>
                                <td><input type="text" name="tab_border_bottom_color" class="color-field" value="<?php echo $options['tab_border_bottom_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Current / Completed / On Hover Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number color of the completed/current/hovered tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_number_color_hover" class="color-field" value="<?php echo $options['tab_number_color_hover']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Current / Completed / On Hover Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number background color of the completed/current/hovered tab.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_number_bkg_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Current / Completed / On Hover Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the text color of the completed/current/hovered tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_text_color_hover" class="color-field" value="<?php echo $options['tab_text_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Current / Completed / On Hover Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the background color of the completed/current/hovered tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_bkg_color_hover" class="color-field" value="<?php echo $options['tab_bkg_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Current / Completed / On Hover Tab Border Bottom Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the border color under the tabs of the completed/current/hovered tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_border_bottom_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Arrow Right Color (option applied only for the "Arrow" Tab Layout)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the arrow right color. Usually it is the color of your page background, but you can choose any color.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_before_arrow_color" class="color-field" value="<?php echo $options['tab_before_arrow_color']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Show number instead of checkmark', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="show_number_checkmark" value="1" <?php checked($options['show_number_checkmark'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="show-number-yes">
                                          	<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                                        </label>

                                        <input id="show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="show_number_checkmark" value="0" <?php checked($options['show_number_checkmark'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="show-number-no">
                                          	<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                                        </label>																						
                                    </div>			
                                </td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_adjust_number_position" class="input-field" value="<?php echo $options['tab_adjust_number_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_adjust_checkmark_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_adjust_text_position" class="input-field" value="<?php echo $options['tab_adjust_text_position']; ?>" /></td>
                            </tr>
						</tbody>
					</table>		
							
							
                    <table class="form-table argmc-table-style argmc-tab-style argmc-tab-arrow-alt-style">
                        <tbody>
                            <tr>
                                <th>
                                    <?php _e('Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_arrow_alt_text_color" class="color-field" value="<?php echo $options['tab_arrow_alt_text_color']; ?>" /></td>
                            </tr>		
                            <tr>
                                <th>
                                    <?php _e('Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_arrow_alt_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_bkg_color']; ?>" /></td>
                            </tr>                            
                            <tr>
                                <th>
                                    <?php _e('Tab Border Bottom Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the border color under the tabs.', 'argMC'); ?></span>										
                                </th>
                                <td><input type="text" name="tab_arrow_alt_border_bottom_color" class="color-field" value="<?php echo $options['tab_arrow_alt_border_bottom_color']; ?>" /></td>
                            </tr>                           
                            <tr>
                                <th>
                                    <?php _e('Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_arrow_alt_number_text_color" class="color-field" value="<?php echo $options['tab_arrow_alt_number_text_color']; ?>" /></td>
                            </tr>                           
							<tr>
                                <th>
                                    <?php _e('Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_arrow_alt_number_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_number_bkg_color']; ?>" /></td>
                            </tr>                            
                            <tr>
                                <th>
                                    <?php _e('Current / On Hover Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number color of the current/hovered tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_arrow_alt_number_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_number_color_hover']; ?>" /></td>
                            </tr>                           
   							<tr>
                                <th>
                                    <?php _e('Current / On Hover Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number background color of the current/hovered tab.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_arrow_alt_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_number_bkg_color_hover']; ?>" /></td>
                            </tr>                         
                            <tr>
                                <th>
                                    <?php _e('Visited / On Hover Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the text color of the visited/hovered tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_arrow_alt_text_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_text_color_hover']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Current / On Hover Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the background color of the current/hovered tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_arrow_alt_bkg_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_bkg_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                             	<th>
                                    <?php _e('Visited Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the background color of the visited tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_arrow_alt_completed_bkg_color" class="color-field" value="<?php echo $options['tab_arrow_alt_completed_bkg_color']; ?>" /></td>
                            </tr>                            
                           	<tr>
                                <th>
                                    <?php _e('Visited / On Hover Tab Border Bottom Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the border color under the tabs of the visited/hovered tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_arrow_alt_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_arrow_alt_border_bottom_color_hover']; ?>" /></td>
                            </tr> 
                            <tr>
                                <th>
                                    <?php _e('Arrow Right Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the arrow right color. Usually it is the color of your page background, but you can choose any color.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_arrow_alt_before_arrow_color" class="color-field" value="<?php echo $options['tab_arrow_alt_before_arrow_color']; ?>" /></td>
                            </tr>							
							<tr>
                                <th>
                                    <?php _e('Show number instead of checkmark', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="tab-arrow-alt-show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="tab_arrow_alt_show_number_checkmark" value="1" <?php checked($options['tab_arrow_alt_show_number_checkmark'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="tab-arrow-alt-show-number-yes">
                                          	<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                                        </label>

                                        <input id="tab-arrow-alt-show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="tab_arrow_alt_show_number_checkmark" value="0" <?php checked($options['tab_arrow_alt_show_number_checkmark'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="tab-arrow-alt-show-number-no">
                                          	<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                                        </label>																						
                                    </div>			
                                </td>
                            </tr>  
                            <tr>
                                <th>
                                    <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_arrow_alt_adjust_number_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_number_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_arrow_alt_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_checkmark_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_arrow_alt_adjust_text_position" class="input-field" value="<?php echo $options['tab_arrow_alt_adjust_text_position']; ?>" /></td>
                            </tr>
                    	</tbody>        
                    </table>        
                            
                    <table class="form-table argmc-table-style argmc-tab-style argmc-tab-progress-bar-style">
                        <tbody>	                            
                          	<tr>
                                <th>
                                    <?php _e('Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs font color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_progress_bar_text_color" class="color-field" value="<?php echo $options['tab_progress_bar_text_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_progress_bar_number_text_color" class="color-field" value="<?php echo $options['tab_progress_bar_number_text_color']; ?>" /></td>
                            </tr> 
                         	<tr>
                                <th>
                                    <?php _e('Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change tab number background color.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name=tab_progress_bar_number_bkg_color class="color-field" value="<?php echo $options['tab_progress_bar_number_bkg_color']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Tab Border Top Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the border color above the tabs.', 'argMC'); ?></span>										
                                </th>
                                <td><input type="text" name="tab_progress_bar_border_bottom_color" class="color-field" value="<?php echo $options['tab_progress_bar_border_bottom_color']; ?>" /></td>
                            </tr> 
                            <tr>
                                <th>
                                    <?php _e('Tab Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change tabs background color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_progress_bar_bkg_color" class="color-field" value="<?php echo $options['tab_progress_bar_bkg_color']; ?>" /></td>
                            </tr>   
                      		<tr>
                                <th>
                                    <?php _e('Current Tab Number Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number color of the last visited tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_progress_bar_number_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_number_color_hover']; ?>" /></td>
                            </tr> 
   							<tr>
                                <th>
                                    <?php _e('Current Tab Number Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the number background color of the last visited tab.', 'argMC'); ?></span>																				
                                </th>
                                <td><input type="text" name="tab_progress_bar_number_bkg_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_number_bkg_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Current Tab Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the text color of the last visited/hovered tab.', 'argMC'); ?></span>																																								
                                </th>
                                <td><input type="text" name="tab_progress_bar_text_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_text_color_hover']; ?>" /></td>
                            </tr>
                            <tr>
                                <th>
                                    <?php _e('Last Visited / Completed Tab Border Top Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the border color above the tabs of the completed/current tab.', 'argMC'); ?></span>																																																		
                                </th>
                                <td><input type="text" name="tab_progress_bar_border_bottom_color_hover" class="color-field" value="<?php echo $options['tab_progress_bar_border_bottom_color_hover']; ?>" /></td>
                            </tr>   
							<tr>
                                <th>
                                    <?php _e('Show number instead of checkmark', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to show number instead of checkmark after a step is completed.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="tab-progress-bar-show-number-yes" class="input-radio-button show-number-checkmark" data-style="show-number" type="radio" name="tab_progress_bar_show_number_checkmark" value="1" <?php checked($options['tab_progress_bar_show_number_checkmark'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="tab-progress-bar-show-number-yes">
                                          	<span class="label-button-text"><?php _e('On', 'argMC'); ?></span>
                                        </label>

                                        <input id="tab-progress-bar-show-number-no" class="input-radio-button show-number-checkmark" type="radio" data-style="show-number-no" name="tab_progress_bar_show_number_checkmark" value="0" <?php checked($options['tab_progress_bar_show_number_checkmark'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="tab-progress-bar-show-number-no">
                                          	<span class="label-button-text"><?php _e('Off', 'argMC'); ?></span>
                                        </label>																						
                                    </div>			
                                </td>
                            </tr> 
							<tr>
                                <th>
                                    <?php _e('Adjust Number Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab number relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the number and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_progress_bar_adjust_number_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_number_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Checkmark Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab checkmark relatively to the text (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the checkmark and the text).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_progress_bar_adjust_checkmark_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_checkmark_position']; ?>" /></td>
                            </tr>
							<tr>
                                <th>
                                    <?php _e('Adjust Text Position (vertical alignment)', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can verticaly align the tab text relatively to the number/checkmark (usualy values like 1px, -1px, 0px, -2px, 2px will align perfect the text and the number/checkmark).', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="tab_progress_bar_adjust_text_position" class="input-field" value="<?php echo $options['tab_progress_bar_adjust_text_position']; ?>" /></td>
                            </tr>                                                                              
                        </tbody>
                    </table>


                    <h3><?php _e('Checkout Forms Styles', 'argMC'); ?></h3>

                    <table class="form-table argmc-table-style">
                        <tbody>
                            <tr>
                                <th>
                                    <?php _e('Inherit Checkout Forms Styles From Your Theme or Plugin', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('If you prefer the plugin checkout forms styles then select the "Plugin" option (the styles will be inherited from the plugin).
                                    <br>If not, your theme checkout form styles will be inherited.', 'argMC'); ?></span>
                                </th>
                                <td>
                                    <div class="radio-buttons-wrapper">
                                        <input id="overwrite-woo-styles-yes" class="input-radio-button arg-checkout-option-button" data-style="plugin" type="radio" name="overwrite_woo_styles" value="1" <?php checked($options['overwrite_woo_styles'], 1); ?>>
                                        <label class="input-label-button label-button-left" for="overwrite-woo-styles-yes">
                                          	<span class="label-button-text"><?php _e('Plugin', 'argMC'); ?></span>
                                        </label>

                                        <input id="overwrite-woo-styles-no" class="input-radio-button arg-checkout-option-button" type="radio" data-style="theme" name="overwrite_woo_styles" value="0" <?php checked($options['overwrite_woo_styles'], 0); ?>>
                                        <label class="input-label-button label-button-right" for="overwrite-woo-styles-no">
                                          	<span class="label-button-text"><?php _e('Theme', 'argMC'); ?></span>
                                        </label>																						
                                    </div>			
                                </td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Forms Text Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change forms text color.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_text_color" class="color-field" value="<?php echo $options['woo_text_color']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                   <?php _e('Forms Headings/Table Headings/Labels Color', 'argMC'); ?>
                                   <span class="argmc-description"><?php _e('Change the color of the labels(used on form fields)/form headings/table headings.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_label_color" class="color-field" value="<?php echo $options['woo_label_color']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Form Fields Border Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change form fields border colors.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_input_border_color" class="color-field" value="<?php echo $options['woo_input_border_color']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Form Fields Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change form fields background colors.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_input_bkg_color" class="color-field" value="<?php echo $options['woo_input_bkg_color']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Form Fields Border Radius', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('With this option you can give any form field "rounded corners".', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_field_border_radius" class="input-field" value="<?php echo $options['woo_field_border_radius']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Invalid Form Fields Border Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change border color for invalid form fields.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_invalid_required_field_border" class="color-field" value="<?php echo $options['woo_invalid_required_field_border']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Invalid Form Fields Background', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change background color for invalid form fields.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_invalid_required_field_bkg" class="color-field" value="<?php echo $options['woo_invalid_required_field_bkg']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                            		<?php _e('Validated Form Fields Border', 'argMC'); ?>
                                 	<span class="argmc-description"><?php _e('Change border color for validated form fields.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_validated_field_border" class="color-field" value="<?php echo $options['woo_validated_field_border']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Buttons Background Color', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Change the background color of the wizard buttons. Starting with version 1.8 this option is deprecated. Please use "Wizard styles" options instead to change buttons background colors.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_button_bkg_color" class="color-field" value="<?php echo $options['woo_button_bkg_color']; ?>" /></td>
                            </tr>
                            <tr class="checkout-form-options">
                                <th>
                                    <?php _e('Button Background Color on Login and Coupon Forms', 'argMC'); ?>
                                    <span class="argmc-description"><?php _e('Use this option to change the background color of Login and Coupon buttons.', 'argMC'); ?></span>
                                </th>
                                <td><input type="text" name="woo_button_bkg_color_login" class="color-field" value="<?php echo $options['woo_button_bkg_color_login']; ?>" /></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php
                    break;
                }			
                ?>
                <input type="submit" name="save" class="button button-primary" value="<?php _e('Save Changes', 'argMC'); ?>">
                <input type="submit" name="reset" class="button" value="<?php _e('Reset All', 'argMC'); ?>">
            </form>
        </div>
        <?php	
    }


    /**
     * Login
     * @return Json		 
     */ 		
    public static function login()
    {
		if (is_user_logged_in()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('You are already logged in', 'argMC')
			));

			exit;						
		}
		
        check_ajax_referer('login-nonce', 'security');

        $info                   = array();
        $info['user_login'] 	= filter_input(INPUT_POST, 'username');
        $info['user_password'] 	= filter_input(INPUT_POST, 'password');
		$remember 				= filter_input(INPUT_POST, 'rememberme');
		$info['remember'] 		= !empty($remember);
			
		$validationError = new \WP_Error();
		$validationError = apply_filters('woocommerce_process_login_errors', $validationError, $info['user_login'], $info['user_password']);

		if ($validationError->get_error_code() ) {
			echo json_encode(array(
				'success'   => false,
				'error'     => $validationError->get_error_message()
			));

			exit;			
		}			
				
		if (empty($info['user_login'])) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('Username is required', 'argMC')
			));

			exit;	
		}

		if (empty($info['user_password'])) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('Password is required', 'argMC')
			));

			exit;		
		}
				
		if (is_email($info['user_login']) && apply_filters('woocommerce_get_username_from_email', true)) {
			$user = get_user_by('email', $info['user_login']);

			if (!empty($user->user_login)) {
				$info['user_login'] = $user->user_login;
			} else {
				echo json_encode(array(
					'success'   => false,
					'error'     => __('A user could not be found with this email address', 'argMC')
				));
	
				exit;			
			}
		} 
				
		$secureCookie = is_ssl() ? true : false;

        $user = wp_signon($info, $secureCookie);

        if (is_wp_error($user)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => __('Incorrect username/password', 'argMC')
            ));

            exit;
        } 

        echo json_encode(array(
            'success' => true
        ));		

        exit;
    }	

	
    /**
     * Register user
     * @return Json		 
     */ 		
    public static function register()
    {
		if (is_user_logged_in()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => __('You are already logged in', 'argMC')
			));

			exit;						
		}
		
        check_ajax_referer('register-nonce', 'security');

        $username	= filter_input(INPUT_POST, 'username');
        $email		= filter_input(INPUT_POST, 'email');
		$email2		= filter_input(INPUT_POST, 'email_2');		
		$password	= filter_input(INPUT_POST, 'password');

		$username = 'no' === get_option('woocommerce_registration_generate_username') ? $username : '';
		$password = 'no' === get_option('woocommerce_registration_generate_password') ? $password : '';
		
		$validationError = new \WP_Error();
		$validationError = apply_filters('woocommerce_process_registration_errors', $validationError, $username, $password, $email);

		if ($validationError->get_error_code()) {
			echo json_encode(array(
				'success'   => false,
				'error'     => $validationError->get_error_message()
			));

			exit;			
		}		
		
		// Anti-spam trap
		if (!empty($email2)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => __('Anti-spam field was filled in.', 'argMC')
            ));

            exit;		
		}
					
		if (function_exists('wc_create_new_customer')) {
			$userId = wc_create_new_customer($email, $username, $password);
		} else {
			$data = array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => 'customer',
			);
		
			$userId = wp_insert_user($data);
		}
		
        if (is_wp_error($userId)) {
            echo json_encode(array(
                'success'   => false,
                'error'     => $userId->get_error_message()
            ));

            exit;
        } 

		// Log user in
		if (apply_filters('woocommerce_registration_auth_new_customer', true, $userId)) {
			wp_set_auth_cookie($userId);
		}
		
        echo json_encode(array(
            'success' => true
        ));		

        exit;
    }
	
	
    /**
     * Plugins page
     * @return array		  
     */ 	
    public static function pluginRowMeta($links, $file)
    {
        if ($file == ARG_MC_BASENAME) {
            unset($links[2]);

            $customLinks = array(
                'documentation'     => '<a href="' . ARG_MC_DOCUMENTATION_URL . '" target="_blank">' . __('Documentation', 'argMC') . '</a>',
                'visit-plugin-site' => '<a href="' . ARG_MC_PLUGIN_URL . '" target="_blank">' . __('Visit plugin site', 'argMC') . '</a>'
            );

            $links = array_merge($links, $customLinks);
        }

        return $links;
    }


    /**
     * Plugins page
     * @return array	 
     */ 
    public static function actionLinks($links)
    {
        $customLinks = array_merge(array('settings' => '<a href="' . admin_url('admin.php?page='. ARG_MC_MENU_SLUG) . '">' . __('Settings', 'argMC') . '</a>'), $links);

        return $customLinks;
    }


    /**
     * Admin footer
     * @return void	 
     */ 		
    public static function adminFooter()
    {
        ?>
        <p><a href="https://codecanyon.net/item/arg-multistep-checkout-for-woocommerce/reviews/18036216" class="arg-review-link" target="_blank"><?php echo sprintf(__('If you like <strong> %s </strong> please leave us a &#9733;&#9733;&#9733;&#9733;&#9733; rating.', 'argMC'), ARG_MC_PLUGIN_NAME); ?></a> <?php _e('Thank you.', 'argMC'); ?></p>
        <?php
    }
}
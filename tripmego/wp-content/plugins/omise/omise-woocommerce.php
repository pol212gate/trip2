<?php
/**
 * Plugin Name: Omise Payment Gateway
 * Plugin URI: https://www.omise.co/woocommerce
 * Description: Omise WooCommerce Gateway Plugin is a wordpress plugin designed specifically for WooCommerce. The plugin adds support for Omise Payment Gateway payment method to WooCommerce.
 * Version: 3.0
 * Author: Omise
 * Author URI: https://www.omise.co
 * Text Domain: omise
 *
 * Copyright: Copyright 2014-2015. Omise Co., Ltd.
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */
defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

class Omise {
	/**
	 * Omise plugin version number.
	 *
	 * @var string
	 */
	public $version = '3.0';

	/**
	 * The Omise Instance.
	 *
	 * @since 3.0
	 *
	 * @var   \Omise
	 */
	protected static $the_instance = null;

	/**
	 * @since  3.0
	 */
	public function __construct() {
		$this->initiate();

		do_action( 'omise_initiated' );
	}

	/**
	 * @since  3.0
	 */
	protected function initiate() {
		defined( 'OMISE_WOOCOMMERCE_PLUGIN_VERSION' ) || define( 'OMISE_WOOCOMMERCE_PLUGIN_VERSION', $this->version );
		defined( 'OMISE_WOOCOMMERCE_PLUGIN_PATH' ) || define( 'OMISE_WOOCOMMERCE_PLUGIN_PATH', __DIR__ );
		defined( 'OMISE_API_VERSION' ) || define( 'OMISE_API_VERSION', '2014-07-27' );

		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/classes/class-omise-charge.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/classes/class-omise-card-image.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-omise-payment-alipay.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-omise-payment-creditcard.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/gateway/class-omise-payment-internetbanking.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/libraries/omise-php/lib/Omise.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/libraries/omise-plugin/Omise.php';
		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-omise-wc-myaccount.php';

		require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/omise-util.php';

		add_action( 'init', 'register_omise_wc_gateway_post_type' );
		add_action( 'plugins_loaded', 'register_omise_alipay', 0 );
		add_action( 'plugins_loaded', 'register_omise_creditcard', 0 );
		add_action( 'plugins_loaded', 'register_omise_internetbanking', 0 );
		add_action( 'plugins_loaded', 'prepare_omise_myaccount_panel', 0 );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'register_user_agent' ), 10 );

		$this->init_admin();
	}

	/**
	 * @since  3.0
	 */
	protected function init_admin() {
		if ( is_admin() ) {
			require_once OMISE_WOOCOMMERCE_PLUGIN_PATH . '/includes/class-omise-admin.php';

			add_action( 'plugins_loaded', array( Omise_Admin::get_instance(), 'register_admin_page_and_actions' ) );
			add_filter( 'woocommerce_order_actions', array( $this, 'register_order_actions' ) );
		}
	}

	/**
	 * @since  3.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'omise', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * @since  3.0
	 */
	public function register_user_agent() {
		global $wp_version;

		$user_agent = sprintf( 'OmiseWooCommerce/%s WordPress/%s WooCommerce/%s', OMISE_WOOCOMMERCE_PLUGIN_VERSION, $wp_version, WC()->version );
		defined( 'OMISE_USER_AGENT_SUFFIX' ) || define( 'OMISE_USER_AGENT_SUFFIX', $user_agent );
	}

	/**
	 * @param  array $order_actions
	 *
	 * @return array
	 */
	public function register_order_actions( $order_actions ) {
		global $theorder;

		if ( 'omise' === $theorder->get_payment_method() ) {
			$order_actions[ $theorder->get_payment_method() . '_charge_capture'] = __( 'Omise: Capture this order', 'omise' );
		}

		$order_actions[ $theorder->get_payment_method() . '_sync_payment'] = __( 'Omise: Manual sync payment status', 'omise' );

		return $order_actions;
	}

	/**
	 * The Omise Instance.
	 *
	 * @see    Omise()
	 *
	 * @since  3.0
	 *
	 * @static
	 *
	 * @return \Omise - The instance.
	 */
	public static function instance() {
		if ( is_null( self::$the_instance ) ) {
			self::$the_instance = new self();
		}

		return self::$the_instance;
	}
}

function Omise() {
	return Omise::instance();
}

Omise();

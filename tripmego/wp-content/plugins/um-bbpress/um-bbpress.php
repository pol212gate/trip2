<?php
/*
Plugin Name: Ultimate Member - bbPress
Plugin URI: http://ultimatemember.com/
Description: Integrates Ultimate Member with bbPress beautifully.
Version: 1.1.8
Author: Ultimate Member
Author URI: http://ultimatemember.com/
*/

	require_once(ABSPATH.'wp-admin/includes/plugin.php');
	
	$plugin_data = get_plugin_data( __FILE__ );

	define('um_bbpress_url',plugin_dir_url(__FILE__ ));
	define('um_bbpress_path',plugin_dir_path(__FILE__ ));
	define('um_bbpress_plugin', plugin_basename( __FILE__ ) );
	define('um_bbpress_extension', $plugin_data['Name'] );
	define('um_bbpress_version', $plugin_data['Version'] );
	define('um_bbpress_textdomain', 'um-bbpress' );
	
	define('um_bbpress_requires', '1.3.35');
	
	$plugin = um_bbpress_plugin;

	/***
	***	@Init
	***/
	require_once um_bbpress_path . 'core/um-bbpress-init.php';

	function um_bbpress_plugins_loaded() {


		$locale = (get_locale() != '' ) ? get_locale() : 'en_US';
		load_textdomain( um_bbpress_textdomain, WP_LANG_DIR . '/plugins/' .um_bbpress_textdomain . '-' . $locale . '.mo');
		load_plugin_textdomain( um_bbpress_textdomain, false, dirname( plugin_basename(  __FILE__ ) ) . '/languages/' );

	}
	add_action( 'init', 'um_bbpress_plugins_loaded', 0 );
	

	/* Licensing */
	if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
	}

	if( ! function_exists('um_bbpress_get_licensey_key') ){
		function um_bbpress_get_licensey_key(){
			global $ultimatemember;
			$license_key = '';

			if( ! is_admin() ) return;

			$um_options = get_option("um_options");

			if( isset( $um_options["um_bbpress_license_key"] ) ){
				$license_key = trim( $um_options["um_bbpress_license_key"] );
			}

			return $license_key;
		}
	}

	$edd_params = array( 
				'version' 	=> '1.1.8', 		// current version number
				'license' 	=>  um_bbpress_get_licensey_key(), 	// license key 
				'item_name' => 'bbPress', 	// name of this plugin
				'author' 	=> 'Ultimate Member',  // author of this plugin
	);
		
	// setup the updater
	$um_edd_enable = apply_filters("um_enable_edd_sl_plugin_updater", true, __FILE__, $edd_params );
	if( $um_edd_enable ){
		$edd_updater = new EDD_SL_Plugin_Updater( 'https://ultimatemember.com/', __FILE__, $edd_params );
	}

	// add license key field
	add_filter('um_licensed_products_settings', 'um_bbpress_license_key');
	function um_bbpress_license_key( $array ) {
		
		if ( !function_exists( 'um_get_option' ) ) return;
		
		$array[] = array(
				'id'       		=> "um_bbpress_license_key",
				'type'     		=> 'text',
				'title'   		=> "bbPress License Key",
				'compiler' 		=> true,
				'validate_callback' => "um_bbpress_validate_license_key",
				'class'			=> 'field-warning',
		);
		
		return $array;

	}


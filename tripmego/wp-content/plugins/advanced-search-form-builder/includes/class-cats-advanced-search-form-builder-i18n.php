<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codecanyon.net/user/catsplugins/portfolio
 * @since      1.0.0
 *
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/includes
 * @author     Cat's Plugins <admin@catsplugins.com>
 */
class Cats_Advanced_Search_Form_Builder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cats-advanced-search-form-builder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

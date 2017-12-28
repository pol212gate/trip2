<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codecanyon.net/user/catsplugins/portfolio
 * @since             1.0.0
 * @package           Cats_Advanced_Search_Form_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Search Form Builder
 * Plugin URI:        https://codecanyon.net/user/catsplugins/
 * Description:       Create stunning advanced search form with filter, ajax search and high performance
 * Version:           1.2.3
 * Author:            Cat's Plugins
 * Author URI:        https://codecanyon.net/user/catsplugins/portfolio
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cats-advanced-search-form-builder
 * Domain Path:       /languages
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('ASFB_FOLDER_PLUGIN', basename(__DIR__));
define('ASFB_PATH', plugin_dir_path( __FILE__ ));
define('ASFB_URL', plugin_dir_url(__FILE__));

require plugin_dir_path( __FILE__ ) . 'includes/cats-advanced-search-form-builder-config.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cats-advanced-search-form-builder-activator.php
 */
function activate_cats_advanced_search_form_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cats-advanced-search-form-builder-activator.php';
	Cats_Advanced_Search_Form_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cats-advanced-search-form-builder-deactivator.php
 */
function deactivate_cats_advanced_search_form_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cats-advanced-search-form-builder-deactivator.php';
	Cats_Advanced_Search_Form_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cats_advanced_search_form_builder' );
register_deactivation_hook( __FILE__, 'deactivate_cats_advanced_search_form_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cats-advanced-search-form-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cats_advanced_search_form_builder() {

	$plugin = new Cats_Advanced_Search_Form_Builder();
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/bootstrap.php';

	$plugin->run();

}
run_cats_advanced_search_form_builder();

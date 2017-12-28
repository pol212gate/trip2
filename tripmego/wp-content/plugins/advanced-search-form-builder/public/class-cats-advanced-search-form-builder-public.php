<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/catsplugins/portfolio
 * @since      1.0.0
 *
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/public
 * @author     Cat's Plugins <admin@catsplugins.com>
 */
class Cats_Advanced_Search_Form_Builder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $pathCode;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = time();
        $this->pathCode = glob(__DIR__ . '/functions/*.php');
        $this->pathCode[] = plugin_dir_path(__FILE__) . '/elements_form/class-element-form-base.php';

        $this->bootstrapFile();
	}

	public function bootstrapFile()
    {
        if( ! empty( $this->pathCode ) ) {
            foreach ( $this->pathCode as $path ) {
                require_once $path;
            }
        }
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cats_Advanced_Search_Form_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cats_Advanced_Search_Form_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        $files = array(
            $this->plugin_name => plugin_dir_url( __FILE__ ) . 'css/cats-advanced-search-form-builder-public.css',
//            $this->plugin_name . '_gooleFont' => 'https://fonts.googleapis.com/css?family=Montserrat:400,600,700|Playfair+Display:400,400i,700i|Open+Sans:400,700',
            $this->plugin_name . '_css_select2' => plugin_dir_url( __FILE__ ) . 'assets/vendors/select2/select2.min.css',
            $this->plugin_name . '_app' => plugin_dir_url( __FILE__ ) . 'assets/css/app.css',
        );

        foreach ($files as $key => $file) {
            wp_enqueue_style( $key, $file, array(), $this->version, 'all' );
        }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cats-advanced-search-form-builder-public.js', array( 'jquery' ), $this->version, false );

		$files = array(
            $this->plugin_name . '_js_select2' => plugin_dir_url( __FILE__ ) . 'assets/vendors/select2/select2.min.js',
            $this->plugin_name . '_js_jquery_ui' => plugin_dir_url( __FILE__ ) . 'assets/vendors/jquery-ui/jquery-ui.min.js',
            $this->plugin_name . '_js_app' => plugin_dir_url( __FILE__ ) . 'assets/js/app.js',
        );

		foreach ($files as $key => $file) {
            wp_enqueue_script( $key, $file, array( 'jquery' ), $this->version, false );
        }
	}

}

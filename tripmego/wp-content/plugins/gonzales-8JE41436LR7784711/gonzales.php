<?php
/**
 * Gonzales WordPress Plugin
 *
 * @package   Gonzales
 * @author    Tomasz Dobrzyński
 * @link      https://tomasz-dobrzynski.com
 * @copyright Copyright © 2017 Tomasz Dobrzyński
 *
 * Plugin Name: Gonzales
 * Plugin URI: https://tomasz-dobrzynski.com/wordpress-gonzales
 * Description: Speed up your site by deactivation of useless scripts and styles.
 * Version: 2.0.4
 * Author: Tomasz Dobrzyński
 * Author URI: https://tomasz-dobrzynski.com
 * Domain Path: /languages/
 * Tested up to: 4.9
 * Revision: 2017.11.22
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Use following definition in child theme to:
 *
 *  > Disable/enable Gonzales assets control on front/back-end
 *  +-----------------------------------------------+
 *  | define('GONZALES_DISABLE_ON_FRONTEND', true); |
 *  | define('GONZALES_ENABLE_ON_BACKEND', true);   |
 *  +-----------------------------------------------+
 *
 *  > Make Gonzales quiet after setting optimal configuration
 *  +-----------------------------------------+
 *  | define('DISABLE_GONZALES_PANEL', true); |
 *  +-----------------------------------------+
 *  | Q: When I should implement this definition?
 *  | A: When you have 100% sure that everything works after saving final
 *  |    Gonzales configuration
 *  |
 *  | Q: Why I should implement it?
 *  | A: Because a lot of HTML code is added to html body and 99% of time
 *  |    you won't use it. Other thing is you'll save space in top admin bar.
 *  |
 *  | Q: Gonzales menu/panel renders only for administrator, I don't care.
 *  | A: It's OK but less memory is conumed and website renders a bit faster
 *  |    when implemented. Less it better right?
 *  +-------------------------------------------
 *
 *  > Disable cache flushing after configuration update
 *  +-----------------------------------------+
 *  | define('GONZALES_CACHE_CONTROL', true); |
 *  +-----------------------------------------+
 */

/**
 * Gonzales pre-configuration
 * ============================================================================
 */
register_activation_hook( __FILE__, 'gonzales_activated' );
register_activation_hook( __FILE__, 'gonzales_install' );

/**
 * Checks whether dependencies are meet or not
 */
function gonzales_activated() {
	add_option( 'Activated_Plugin', 'Gonzales' );

	$result = check_gonzales();
	if ( ! empty( $result ) ) {
		if ( 'gonzales-info' == $result || 'null' == $result->status ) {
			add_option( 'Gonzales_Issue_1', true );
		} elseif ( 'error' == $result->status ) {
			add_option( 'Gonzales_Issue_2', true );
		}
	}
}

global $gonzales_db_version;
$gonzales_db_version = 1.1;

/**
 * Install required tabled:
 * gonzales_disabled, gonzales_enabled
 */
function gonzales_install() {
	global $wpdb;
	global $gonzales_db_version;

	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . 'gonzales_disabled';
	$sql_gonzales = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		handler_type tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=css, 1=js',
		handler_name varchar(128) DEFAULT '' NOT NULL,
		url varchar(255) DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	$table_name = $wpdb->prefix . 'gonzales_enabled';
	$sql_gonzales_exceptions = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		handler_type tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=css, 1=js',
		handler_name varchar(128) DEFAULT '' NOT NULL,
		content_type varchar(64) DEFAULT '' NOT NULL,
		url varchar(255) DEFAULT '' NOT NULL,
		PRIMARY KEY (id)
	) $charset_collate;";

	dbDelta( $sql_gonzales );
	dbDelta( $sql_gonzales_exceptions );

	update_option( 'gonzales_db_version', $gonzales_db_version );
}

/**
 * Gonzales actual functionalty
 * ============================================================================
 */
class Gonzales {
	/**
	 * Stores current content type
	 *
	 * @var string
	 */
	private $content_type = '';

	/**
	 * Stores entire entered by user selection
	 *
	 * @var [type]
	 */
	private $gonzales_data = array();

	/**
	 * Stores list of all available assets (used in rendering panel)
	 *
	 * @var array
	 */
	private $collection = array();

	/**
	 * Initilize entire machine
	 */
	function __construct() {
		add_action( 'init', array( $this, 'load_configuration' ), 1 );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_init', array( $this, 'load_plugin' ) );
		add_action( 'template_redirect', array( $this, 'detect_content_type' ) );

		if ( ! defined( 'GONZALES_DISABLE_ON_FRONTEND' ) && ! is_admin() ) {
			add_action( 'wp_head', array( $this, 'collect_assets' ), 10000 );
			add_action( 'wp_footer', array( $this, 'collect_assets' ), 10000 );
			add_filter( 'script_loader_src', array( $this, 'unload_assets' ), 10, 2 );
			add_filter( 'style_loader_src', array( $this, 'unload_assets' ), 10, 2 );

			if ( ! defined( 'DISABLE_GONZALES_PANEL' ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'append_asset' ) );
				add_action( 'wp_footer', array( $this, 'render_panel' ), 10000 + 1 );
			}
		}

		if ( defined( 'GONZALES_ENABLE_ON_BACKEND' ) && is_admin() ) {
			add_action( 'admin_head', array( $this, 'collect_assets' ), 10000 );
			add_action( 'admin_footer', array( $this, 'collect_assets' ), 10000 );
			add_filter( 'script_loader_src', array( $this, 'unload_assets' ), 10, 2 );
			add_filter( 'style_loader_src', array( $this, 'unload_assets' ), 10, 2 );

			if ( ! defined( 'DISABLE_GONZALES_PANEL' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'append_asset' ) );
				add_action( 'admin_footer', array( $this, 'render_panel' ), 10000 + 1 );
			}
		}

		if ( ! defined( 'DISABLE_GONZALES_PANEL' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'add_node_to_admin_bar' ), 1000 );
		}

		if ( ! defined( 'GONZALES_DISABLE_ON_FRONTEND' ) ) {
			add_action( 'init', array( $this, 'update_configuration' ) );
		} elseif ( defined( 'GONZALES_ENABLE_ON_BACKEND' ) ) {
			add_action( 'admin_init', array( $this, 'update_configuration' ) );
		}
	}


	/**
	 * Check whether resource should be disabled or not.
	 *
	 * @param  string $url 		Handler URL.
	 * @param  string $handle 	Asset handle name.
	 * @return mixed
	 */
	public function unload_assets( $url, $handle ) {
		$type = ( current_filter() == 'script_loader_src' ) ? 'js' : 'css';
		$source = ( current_filter() == 'script_loader_src' ) ? wp_scripts() : wp_styles();

		return ( $this->get_visibility( $type, $handle ) ? $url : false);
	}

	/**
	 * Get information regarding used assets
	 *
	 * @return bool
	 */
	public function collect_assets() {
		$denied = array(
			'js' => array( 'gonzales', 'admin-bar' ),
			'css' => array( 'gonzales', 'admin-bar', 'dashicons' ),
		);

		/**
		 * Imitate full untouched list without dequeued assets
		 * Appends part of original table. Safe approach.
		 */
		$data_assets = array(
			'js' => wp_scripts(),
			'css' => wp_styles(),
		);

		foreach ( $data_assets as $type => $data ) {
			foreach ( $data->done as $el ) {
				if ( ! in_array( $el, $denied[ $type ] ) ) {
					if ( isset( $data->registered[ $el ]->src ) ) {
						$url = $this->prepare_correct_url( $data->registered[ $el ]->src );
						$url_short = str_replace( get_home_url(), '', $url );

						if ( false !== strpos( $url, get_theme_root_uri() ) ) {
							$resource_name = 'theme';
						} elseif ( false !== strpos( $url, plugins_url() ) ) {
							$resource_name = 'plugins';
						} else {
							$resource_name = 'misc';
						}

						$this->collection[ $resource_name ][ $type ][ $el ] = array(
							'url_full' => $url,
							'url_short' => $url_short,
							'state' => $this->get_visibility( $type, $el ),
							'size' => $this->get_asset_size( $url ),
							'deps' => ( isset( $data->registered[ $el ]->deps ) ? $data->registered[ $el ]->deps : array() ),
						);
					}
				}
			}
		}

		return false;
	}

	/**
	 * Initialize interface translation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'gonzales', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Adds notification after plugin activation how to use Gonzales
	 */
	public function load_plugin() {
		if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			if ( get_option( 'Gonzales_Issue_1' ) ) {
				delete_option( 'Gonzales_Issue_1' );
				deactivate_plugins( plugin_basename( __FILE__ ) );
				add_action( 'admin_notices', array( $this, 'gonzales_null' ) );
			} elseif ( get_option( 'Gonzales_Issue_2' ) ) {
				delete_option( 'Gonzales_Issue_2' );
				deactivate_plugins( plugin_basename( __FILE__ ) );
				add_action( 'admin_notices', array( $this, 'gonzales_error' ) );
			}
		}

		if ( is_admin() && 'Gonzales' == get_option( 'Activated_Plugin' ) ) {
			delete_option( 'Activated_Plugin' );
		}
	}

	/**
	 * Plugin activation exception #1
	 */
	public function gonzales_null() {
		$class = 'notice notice-error';
		$message = sprintf( 'Incorrect Gonzales installation. Please <a href="%s">contact developer</a>.', 'https://tomasz-dobrzynski.com/contact' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}

	/**
	 * Plugin activation exception #1
	 */
	public function gonzales_error() {
		require_once( 'gonzales-info.php' );

		$class = 'notice notice-error';
		$message = sprintf( 'Looks like you installed Gonzales on all available slots. Please <a href="%s">extend a license</a> to use on higher number of websites.', 'https://tomasz-dobrzynski.com/wordpress-gonzales/license-extend/' . $gonzales_token );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}

	/**
	 * Loads functionality that allows to enable/disable js/css without site reload
	 */
	public function append_asset() {
		if ( current_user_can( 'manage_options' ) ) {
			wp_enqueue_style( 'gonzales', plugins_url( 'style.css', __FILE__ ), array(), '2.0.1', false );
			wp_enqueue_script( 'gonzales', plugins_url( 'script.js', __FILE__ ) , array(), '2.0.1', true );
		}
	}

	/**
	 * Get asset type based on name/ID
	 *
	 * @param  int|string $input Handler type.
	 * @return int|string        Reversed handler type.
	 */
	private function get_handler_type( $input ) {
		$data = array(
			'css' => 0,
			'js' => 1,
		);

		if ( is_numeric( $input ) ) {
			$data = array_flip( $data );
		}

		return $data[ $input ];
	}

	/**
	 * Execute action once checkbox is changed
	 */
	public function update_configuration() {
		global $wpdb;

		if ( ! current_user_can( 'manage_options' ) ||
		 ! isset( $_POST['gonzalesUpdate'] ) ||
		 ! wp_verify_nonce( filter_input( INPUT_POST, 'gonzalesUpdate' ), 'gonzales' ) ||
		 ! isset( $_POST['allAssets'] ) ||
		 empty( $_POST['allAssets'] ) ||
		 empty( $_POST['currentURL'] ) ) {
			return false;
		}

		$all_assets = json_decode( html_entity_decode( filter_input( INPUT_POST, 'allAssets', FILTER_SANITIZE_SPECIAL_CHARS ) ) );

		if ( empty( $all_assets ) ) {
			return false;
		}

		/**
		 * Clearing old configuration
		 * Removing all selected plugins (list of visible passed in array).
		 * Forget about phpcs warning. It's safe & prepared SQL
		 *
		 * 1. Clear disable everywhere
		 * 2. Clear enable content types & enable here
		 */
		$sql = sprintf( 'DELETE FROM %s WHERE handler_name IN (%s) AND (url = "" OR url = "%s")', $wpdb->prefix . 'gonzales_disabled', implode( ', ', array_fill( 0, count( $all_assets ), '%s' ) ), filter_input( INPUT_POST, 'currentURL' ) );
		$prepared_sql = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $all_assets ) );
		$wpdb->query( $prepared_sql );

		$sql = sprintf( 'DELETE FROM %s WHERE handler_name IN (%s) AND (url = "" OR url = "%s")', $wpdb->prefix . 'gonzales_enabled', implode( ', ', array_fill( 0, count( $all_assets ), '%s' ) ), filter_input( INPUT_POST, 'currentURL' ) );
		$prepared_sql = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $all_assets ) );
		$wpdb->query( $prepared_sql );

		/**
		 * Inserting new configuration
		 */
		if ( isset( $_POST['disabled'] ) && ! empty( $_POST['disabled'] ) ) {
			foreach ( $_POST['disabled'] as $type => $assets ) {
				if ( ! empty( $assets ) ) {
					foreach ( $assets as $handle => $where ) {
						if ( ! empty( $where ) ) {
							foreach ( $where as $place => $nvm ) {
								$wpdb->insert(
									$wpdb->prefix . 'gonzales_disabled',
									array(
										'handler_type' => $this->get_handler_type( $type ),
										'handler_name' => $handle,
										'url' => ( 'here' == $place ? filter_input( INPUT_POST, 'currentURL' ) : '' ),
									),
									array( '%d', '%s', '%s' )
								);
							}
						}
					}
				}
			}
		}

		if ( isset( $_POST['enabled'] ) && ! empty( $_POST['enabled'] ) ) {
			foreach ( $_POST['enabled'] as $type => $assets ) {
				if ( ! empty( $assets ) ) {
					foreach ( $assets as $handle => $content_types ) {
						if ( ! empty( $content_types ) ) {
							foreach ( $content_types as $content_type => $nvm ) {
								$wpdb->insert(
									$wpdb->prefix . 'gonzales_enabled',
									array(
										'handler_type' => $this->get_handler_type( $type ),
										'handler_name' => $handle,
										'content_type' => $content_type,
										'url' => ( 'here' == $content_type ? filter_input( INPUT_POST, 'currentURL' ) : '' ),
									),
									array( '%d', '%s', '%s', '%s' )
								);
							}
						}
					}
				}
			}
		}

		/**
		 * Updating state of configuration after changes
		 */
		$this->load_configuration();

		if ( ! defined( 'GONZALES_CACHE_CONTROL' ) ) {
			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				w3tc_pgcache_flush();
			} elseif ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			} elseif ( function_exists( 'rocket_clean_files' ) ) {
				rocket_clean_files( esc_url( $_SERVER['HTTP_REFERER'] ) );
			}
		}
	}

	/**
	 * Generates Gonzales item with dynamically generated subtrees in administration menu
	 *
	 * @param mixed $wp_admin_bar 	Admin bar object.
	 */
	public function add_node_to_admin_bar( $wp_admin_bar ) {
		/**
		 * Checks whether Gonzales should appear on frontend/backend or not
		 */
		if (
			! current_user_can( 'manage_options' ) ||
			( defined( 'GONZALES_DISABLE_ON_FRONTEND' ) && ! is_admin() ) ||
			( ! defined( 'GONZALES_ENABLE_ON_BACKEND' ) && is_admin() )
		) {
			return;
		}

		$wp_admin_bar->add_menu( array(
			'id'     => 'gonzales',
			'title'  => esc_html__( 'Gonzales', 'gonzales' ),
			'meta'	 => array( 'class' => 'gonzales-object' ),
		) );
	}

	/**
	 * Checks whether item is enabled/disabled
	 *
	 * @param  string $type   Handler type (CSS/JS).
	 * @param  string $plugin Handler name.
	 * @return bool          State
	 */
	private function get_visibility( $type = '', $plugin = '' ) {
		$state = true;

		if ( isset( $this->gonzales_data['disabled'][ $type ][ $plugin ] ) ) {
			$state = false;

			if ( isset( $this->gonzales_data['enabled'][ $type ][ $plugin ][ $this->content_type ] ) ||
				isset( $this->gonzales_data['enabled'][ $type ][ $plugin ]['here'] ) ) {
				$state = true;
			}
		}

		return $state;
	}

	/**
	 * Exception for address starting from "//example.com" instead of
	 * "http://example.com". WooCommerce likes such a format
	 *
	 * @param  string $url Incorrect URL.
	 * @return string      Correct URL.
	 */
	private function prepare_correct_url( $url ) {
		if ( isset( $url[0] ) && isset( $url[1] ) && '/' == $url[0] && '/' == $url[1] ) {
			$out = (is_ssl() ? 'https:' : 'http:') . $url;
		} else {
			$out = $url;
		}

		return $out;
	}

	/**
	 * Checks how heavy is file
	 *
	 * @param  string $src    URL.
	 * @return int          Size in KB.
	 */
	private function get_asset_size( $src ) {
		$weight = 0;

		$home = get_theme_root() . '/../..';
		$src = explode( '?', $src );

		$src_relative = $home . str_replace( get_home_url(), '', $this->prepare_correct_url( $src[0] ) );

		if ( file_exists( $src_relative ) ) {
			$weight = round( filesize( $src_relative ) / 1024, 1 );
		}

		return $weight;
	}

	/**
	 * Detect current content type
	 */
	public function detect_content_type() {
		if ( is_singular() ) {
			$this->content_type = get_post_type();
		}
	}

	/**
	 * Making sure Gonzales uses latest version of DB schema.
	 */
	private function check_db_integrity() {
		global $wpdb;
		global $gonzales_db_version;

		if ( floatval( get_option( 'gonzales_db_version' ) ) < $gonzales_db_version ) {
			$table_name = $wpdb->prefix . 'gonzales_disabled';
			$wpdb->query( "ALTER TABLE $table_name ADD url varchar(255) DEFAULT '' NOT NULL;" );
		}

		update_option( 'gonzales_db_version', $gonzales_db_version );
	}

	/**
	 * Reading saved configuration
	 */
	public function load_configuration() {
		$out = array();
		global $wpdb;

		/**
		 * Load_configuration function is executing before hooks so it's first
		 * function which uses database tables... potentially old tables.
		 * Make sure that I use latest version of db schema.
		 */
		$this->check_db_integrity();

		$disabled_global = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'gonzales_disabled WHERE url = ""', ARRAY_A );
		$disabled_here = $wpdb->get_results( sprintf( 'SELECT * FROM ' . $wpdb->prefix . 'gonzales_disabled WHERE url = "%s"',
			esc_url( $this->get_current_url() )
			), ARRAY_A );

		$enabled_posts = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'gonzales_enabled WHERE content_type != "here"', ARRAY_A );
		$enabled_here = $wpdb->get_results( sprintf( 'SELECT * FROM %s WHERE content_type = \'%s\' AND url=\'%s\'',
			$wpdb->prefix . 'gonzales_enabled',
			'here',
			esc_url( $this->get_current_url() ) ), ARRAY_A );
		$enabled = array_merge( $enabled_here, $enabled_posts );

		if ( ! empty( $disabled_global ) ) {
			foreach ( $disabled_global as $row ) {
				$type = $this->get_handler_type( $row['handler_type'] );
				$out['disabled'][ $type ][ $row['handler_name'] ]['everywhere'] = true;
			}
		}

		if ( ! empty( $disabled_here ) ) {
			foreach ( $disabled_here as $row ) {
				$type = $this->get_handler_type( $row['handler_type'] );
				$out['disabled'][ $type ][ $row['handler_name'] ]['here'] = true;
			}
		}

		if ( ! empty( $enabled ) ) {
			foreach ( $enabled as $row ) {
				$type = $this->get_handler_type( $row['handler_type'] );
				$out['enabled'][ $type ][ $row['handler_name'] ][ $row['content_type'] ] = true;
			}
		}

		$this->gonzales_data = $out;
	}

	/**
	 * Print render panel
	 */
	public function render_panel() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$out = isset( $_POST['gonzalesUpdate'] ) ? '<script>document.addEventListener("DOMContentLoaded", function(event) { document.getElementById("wp-admin-bar-gonzales").click(); });</script>' : '';

		$out .= '<form id="gonzales" class="gonzales-panel" method="POST" style="display: none;">
		<h1>' . __( 'Welcome to Gonzales', 'gonzales' ) . '</h1>
		<table class="gonzales-info">
		<tr>
			<td>
			' . __( 'I\'m Tomasz Dobrzyński, Gonzales plugin author. Below you can find few words you should remember using Gonzales:', 'gonzales' ) . '
			<ul>
				<li>' . __( 'each page may present slightly different set of CSS/JS files on the list. It\'s because plugins can load assets conditionally,', 'gonzales' ) . '</li>
				<li>' . sprintf( __( 'you can define which files should be disabled/enabled on certain pages. It\'s URL based filter so checkbox called "Current URL" will have unique behavior for particular pages. "Current URL" = %s for this page,', 'gonzales' ), set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $this->get_current_url() ) ) . '</li>
				<li>' . __( 'you don\'t have to worry about caching. Each time you save configuration appropriate caches will be cleared,', 'gonzales' ) . '</li>
				<li>' . __( 'I recommend to play with Gonzales in low users activity hours. Don\'t panic if you break website appearance. You can temporary disable plugin, clear cache and wait until you\'ll be ready to continue work on Gonzales.', 'gonzales' ) . '</li>
				<li>' . __( 'unfortunately not all plugins/themes follow programming rules (codex) in context of CSS/JS implementation. Let me know if you find such a file, I\'ll notify developers and mention about problem.', 'gonzales' ) . '</li>
			</ul>
			</td>
		</tr>
		</table>';

		$all_assets = array();
		$content_types = $this->get_public_post_types();
		krsort( $this->collection );

		foreach ( $this->collection as $resource_type => $types ) {
			$out .= '<h2>' . __( $resource_type, 'gonzales' ) . '</h2>';

			$out .= '<table class="gonzales-table">
				<thead>
					<th>' . __( 'Type', 'gonzales' ) . '</th>
					<th>' . __( 'Size', 'gonzales' ) . '</th>
					<th>' . __( 'Handle / URL', 'gonzales' ) . '</th>
					<th>' . __( 'Disable', 'gonzales' ) . '</th>
					<th>' . __( 'Enable', 'gonzales' ) . '</th>
				</thead>
				<tbody>';

			foreach ( $types as $type_name => $rows ) {
				foreach ( $rows as $handle => $row ) {

					/**
					 * Find dependency
					 */
					$deps = array();
					foreach ( $rows as $dep_key => $dep_val ) {
						if ( in_array( $handle, $dep_val['deps'] ) && $dep_val['state'] ) {
							$deps[] = '<a href="#' . $type_name . '-' . $dep_key . '">' . $dep_key . '</a>';
						}
					}

					$id = '[' . $type_name . '][' . $handle . ']';

					$comment = ( ! empty( $deps ) ? '<span class="gonzales-comment">' . __( 'In use by', 'gonzales' ) . ' ' . implode( ', ', $deps ) . '</span>' : '' );

					// Disable everywhere.
					$id_ever = 'disabled' . $id . '[everywhere]';
					$is_checked_ever = ( isset( $this->gonzales_data['disabled'][ $type_name ][ $handle ]['everywhere'] ) ? 'checked="checked"' : '' );
					$option_everywhere = '<div><input type="checkbox" name="' . $id_ever . '" id="' . $id_ever . '" ' . $is_checked_ever . '><label for="' . $id_ever . '">' . __( 'Everywhere', 'gonzales' ) . '</label></div>';

					// Disable here.
					$id_curr = 'disabled' . $id . '[here]';
					$is_checked_here = ( isset( $this->gonzales_data['disabled'][ $type_name ][ $handle ]['here'] ) ? 'checked="checked"' : '' );
					$is_disabled = ( !empty( $is_checked_ever ) ? 'disabled="disabled"' : '' );
					$option_disable_here = '<div class="disable-here" data-id="' . $id_ever . '"><input type="checkbox" name="' . $id_curr . '" id="' . $id_curr . '" ' . $is_checked_here . ' ' . $is_disabled .'><label for="' . $id_curr . '">' . __( 'Current URL', 'gonzales' ) . '</label></div>';

					// Enable here.
					$id_curr = 'enabled' . $id . '[here]';
					$is_checked = (isset( $this->gonzales_data['enabled'][ $type_name ][ $handle ]['here'] ) ? 'checked="checked"' : '');
					$is_disabled = ( empty( $is_checked_ever ) ? 'disabled="disabled"' : '');
					$options_enable = '<div><input type="checkbox" name="' . $id_curr . '" id="' . $id_curr . '" ' . $is_checked . ' ' . $is_disabled . '><label for="' . $id_curr . '">' . __( 'Current URL', 'gonzales' ) . '</label></div>';

					// Enable custom type.
					foreach ( $content_types as $content_type_code => $content_type ) {
						$id_type = 'enabled' . $id . '[' . $content_type_code . ']';
						$is_checked = ( isset( $this->gonzales_data['enabled'][ $type_name ][ $handle ][ $content_type_code ] ) ? 'checked="checked"' : '' );
						$is_disabled = ( empty( $is_checked_ever ) ? 'disabled="disabled"' : '' );
						$options_enable .= '<div><input type="checkbox" name="' . $id_type . '" id="' . $id_type . '" ' . $is_checked . ' ' . $is_disabled . '><label for="' . $id_type . '">' . $content_type . '</label></div>';
					}

					$out .= '<tr>';
						$out .= '<td><div class="state-' . (int) $row['state'] . '">' . strtoupper( $type_name ) . '</div></td>';
						$out .= '<td>' . (empty( $row['size'] ) ? '?' : $row['size']) . ' KB</td>';
						$out .= '<td class="overflow"><h3><a name="' . $type_name . '-' . $handle . '">' . $handle . '</a></h3><a class="gonzales-link" href="' . $row['url_full'] . '" target="_blank">' . $row['url_short'] . '</a></td>';
						$out .= '<td class="option-everwhere">' . $option_everywhere . $option_disable_here . $comment . '</td>';
						$out .= '<td class="options" data-id="' . $id_ever . '">' . $options_enable . '</td>';
					$out .= '</tr>';

					$all_assets[] = $handle;
				}
			}
			$out .= '</tbody>
			</table>';
		}

		$out .= '<input type="submit" id="submit-gonzales" value="' . __( 'Save changes' ) . '">';
		$out .= wp_nonce_field( 'gonzales', 'gonzalesUpdate', true, false );
		$out .= '<input type="hidden" name="currentURL" value="' . esc_url( $this->get_current_url() ) . '">
			<input type="hidden" name="allAssets" value="' . filter_var( json_encode( $all_assets ), FILTER_SANITIZE_SPECIAL_CHARS ) . '">
		</form>';

		print $out;
	}

	/**
	 * Get current URL
	 *
	 * @return string
	 */
	private function get_current_url() {
		$url = explode( '?', $_SERVER['REQUEST_URI'], 2 );
		if ( strlen( $url[0] ) > 1 ) {
			$out = rtrim( $url[0], '/' );
		} else {
			$out = $url[0];
		}

		return $out;
	}

	/**
	 * Generated content types
	 *
	 * @return mixed
	 */
	private function get_public_post_types() {
		$tmp = get_post_types( array(
			'public'   => true,
		), 'objects', 'and' );

		$out = array();
		foreach ( $tmp as $key => $value ) {
			$out[ $key ] = $value->label;
		}

		return $out;
	}
}

/**
 * Verify that everything's fine with instance.
 *
 * @return mixed
 */
function check_gonzales() {
	if ( ! file_exists( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'gonzales-info.php' ) ) {
		return 'gonzales-info';
	}

	require_once( 'gonzales-info.php' );

	$response = wp_remote_post( 'https://tomasz-dobrzynski.com/?event=activate', array(
		'method' => 'POST',
		'redirection' => 5,
		'blocking' => true,
		'sslverify' => false,
		'body' => array(
			'plugin' => 'gonzales',
			'token' => $gonzales_token,
			'domain' => $_SERVER['HTTP_HOST'],
		),
	));

	unset( $gonzales_token );

	if ( ! is_wp_error( $response ) ) {
		return json_decode( $response['body'] );
	}
}

new Gonzales;

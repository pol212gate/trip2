<?php

/**
 * Fired during plugin activation
 *
 * @link       https://codecanyon.net/user/catsplugins/portfolio
 * @since      1.0.0
 *
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cats_Advanced_Search_Form_Builder
 * @subpackage Cats_Advanced_Search_Form_Builder/includes
 * @author     Cat's Plugins <admin@catsplugins.com>
 */
class Cats_Advanced_Search_Form_Builder_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        global $ASFB_config;

		self::createPageResult();

		if ( !is_dir($ASFB_config['cache']['dir']) ) {
            @mkdir($ASFB_config['cache']['dir']);
        }
		if ( !is_dir($ASFB_config['cache']['dir'] . 'css') ) {
            @mkdir($ASFB_config['cache']['dir'] . 'css');
        }
		if ( !is_dir($ASFB_config['cache']['dir'] . 'template') ) {
            @mkdir($ASFB_config['cache']['dir'] . 'template');
        } 

	}

	public static function createPageResult()
	{
		global $ASFB_config;
		$ASFB_page_result = array(
		    'post_title' => __('Search result', 'advanced_search_form_builder'),
		    'post_content' => $ASFB_config['shortcode']['search_result'],
		    'post_status' => 'publish',
		    'post_author' => get_current_user_id(),
		    'post_type' => 'page',
		);

        // CREATE PAGE RESULT
		$post = get_page_by_title($ASFB_page_result['post_title']);

		if (!isset($post->post_title)) {
		    $idPost = wp_insert_post( $ASFB_page_result, '' );
		    add_option($ASFB_config['default_setting']['key_page_result'], $idPost, '' ,TRUE);
		}

		// CREATE DB CACHE
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'asfb_cache';

        $sql = "CREATE TABLE $table_name (
                      id mediumint(9) NOT NULL AUTO_INCREMENT,
                      key_name varchar(255) DEFAULT '' NOT NULL,
                      path_file varchar(255) NOT NULL,
                      expired_time bigint DEFAULT 0 NOT NULL,
                      PRIMARY KEY  (id)
                    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
	}

}

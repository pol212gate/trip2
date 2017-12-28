<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin (PRO)
* Version                 : 2.5.8
* File                    : includes/class-database.php
* File Version            : 1.2.4
* Created / Last Modified : 22 June 2017
* Author                  : Dot on Paper
* Copyright               : © 2012 Dot on Paper
* Website                 : http://www.dotonpaper.net
* Description             : Database PHP class. IMPORTANT! Version, configuration, initialization, initial data, update,  need to be in same file because of issues with instalation/update via FTP.
*/

    if (!class_exists('DOPBSPDatabase')){
        class DOPBSPDatabase{
            /*
             * Private variables.
             */
            private $db_version = 2.41;
            private $db_version_api_keys = 1.0;
            private $db_version_calendars = 1.04;
            private $db_version_coupons = 1.0;
            private $db_version_days = 1.01;
            private $db_version_days_available = 1.0;
            private $db_version_days_unavailable = 1.0;
            private $db_version_discounts = 1.0;
            private $db_version_discounts_items = 1.0;
            private $db_version_discounts_items_rules = 1.0;
            private $db_version_emails = 1.0;
            private $db_version_emails_translation = 1.0;
            private $db_version_extras = 1.0;
            private $db_version_extras_groups = 1.001;
            private $db_version_extras_groups_items = 1.001;
            private $db_version_fees = 1.0;
            private $db_version_forms = 1.0;
            private $db_version_forms_fields = 1.001;
            private $db_version_forms_select_options = 1.0;
            private $db_version_languages = 1.0;
            private $db_version_locations = 1.001;
            private $db_version_models = 1.0;
            private $db_version_reservations = 1.004;
            private $db_version_rules = 1.0;
            private $db_version_searches = 1.0;
            private $db_version_settings = 1.0;
            private $db_version_settings_calendar = 1.0;
            private $db_version_settings_notifications = 1.0;
            private $db_version_settings_payment = 1.0;
            private $db_version_settings_search = 1.0;
            private $db_version_translation = 1.02;
            
            /*
             * Public variables.
             */
            public $db_config;
            public $db_collation = 'utf8_unicode_ci';
            
            /*
             * Constructor
             */
            function __construct(){
                global $wpdb;
                
                $this->db_collation = $wpdb->collate != '' ? $wpdb->collate:$this->db_collation;
            
                add_filter('dopbsp_filter_database_configuration', array(&$this, 'config'), 9);
                 
                /*
                 * Change database version if requested.
                 */
                if (DOPBSP_CONFIG_INIT_DATABASE
                        || DOPBSP_REPAIR_DATABASE_TEXT){
                    update_option('DOPBSP_db_version', '2.0');
                    update_option('DOPBSP_db_version_api_keys', '0.1');
                    update_option('DOPBSP_db_version_calendars', '0.1');
                    update_option('DOPBSP_db_version_coupons', '0.1');
                    update_option('DOPBSP_db_version_days', '0.1');
                    update_option('DOPBSP_db_version_days_available', '0.1');
                    update_option('DOPBSP_db_version_days_unavailable', '0.1');
                    update_option('DOPBSP_db_version_discounts', '0.1');
                    update_option('DOPBSP_db_version_discounts_items', '0.1');
                    update_option('DOPBSP_db_version_discounts_items_rules', '0.1');
                    update_option('DOPBSP_db_version_emails', '0.1');
                    update_option('DOPBSP_db_version_emails_translation', '0.1');
                    update_option('DOPBSP_db_version_extras', '0.1');
                    update_option('DOPBSP_db_version_extras_groups', '0.1');
                    update_option('DOPBSP_db_version_extras_groups_items', '0.1');
                    update_option('DOPBSP_db_version_fees', '0.1');
                    update_option('DOPBSP_db_version_forms', '0.1');
                    update_option('DOPBSP_db_version_forms_fields', '0.1');
                    update_option('DOPBSP_db_version_forms_select_options', '0.1');
                    update_option('DOPBSP_db_version_languages', '0.1');
                    update_option('DOPBSP_db_version_locations', '0.1');
                    update_option('DOPBSP_db_version_models', '0.1');
                    update_option('DOPBSP_db_version_reservations', '0.1');
                    update_option('DOPBSP_db_version_rules', '0.1');
                    update_option('DOPBSP_db_version_searches', '0.1');
                    update_option('DOPBSP_db_version_settings', '0.1');
                    update_option('DOPBSP_db_version_settings_calendar', '0.1');
                    update_option('DOPBSP_db_version_settings_notifications', '0.1');
                    update_option('DOPBSP_db_version_settings_payment', '0.1');
                    update_option('DOPBSP_db_version_settings_search', '0.1');
                    update_option('DOPBSP_db_version_translation', '0.1');
                }
            }
            
// Database
            
            /*
             * Initialize plugin tables.
             */
            function init(){
                global $DOPBSP;
                
                $this->db_config = new stdClass;
                
                /*
                 * Default values and collation filters.
                 */
                $this->db_config = apply_filters('dopbsp_filter_database_configuration', $this->db_config);
                $this->db_collation = apply_filters('dopbsp_filter_database_collation', $this->db_collation);
                
                /*
                 * Get current database version.
                 */
                $current_db_version = get_option('DOPBSP_db_version');
                 
                if ($this->db_version != (float)$current_db_version){
                    require_once(str_replace('\\', '/', ABSPATH).'wp-admin/includes/upgrade.php');
                    
                    /*
                     * Get current tables' versions.
                     */
                    $current_db_version_api_keys = get_option('DOPBSP_db_version_api_keys');
                    $current_db_version_calendars = get_option('DOPBSP_db_version_calendars');
                    $current_db_version_coupons = get_option('DOPBSP_db_version_coupons');
                    $current_db_version_days = get_option('DOPBSP_db_version_days');
                    $current_db_version_days_available = get_option('DOPBSP_db_version_days_available');
                    $current_db_version_days_unavailable = get_option('DOPBSP_db_version_days_unavailable');
                    $current_db_version_discounts = get_option('DOPBSP_db_version_discounts');
                    $current_db_version_discounts_items = get_option('DOPBSP_db_version_discounts_items');
                    $current_db_version_discounts_items_rules = get_option('DOPBSP_db_version_discounts_items_rules');
                    $current_db_version_emails = get_option('DOPBSP_db_version_emails');
                    $current_db_version_emails_translation = get_option('DOPBSP_db_version_emails_translation');
                    $current_db_version_extras = get_option('DOPBSP_db_version_extras');
                    $current_db_version_extras_groups = get_option('DOPBSP_db_version_extras_groups');
                    $current_db_version_extras_groups_items = get_option('DOPBSP_db_version_extras_groups_items');
                    $current_db_version_fees = get_option('DOPBSP_db_version_fees');
                    $current_db_version_forms = get_option('DOPBSP_db_version_forms');
                    $current_db_version_forms_fields = get_option('DOPBSP_db_version_forms_fields');
                    $current_db_version_forms_select_options = get_option('DOPBSP_db_version_forms_select_options');
                    $current_db_version_languages = get_option('DOPBSP_db_version_languages');
                    $current_db_version_locations = get_option('DOPBSP_db_version_locations');
                    $current_db_version_models = get_option('DOPBSP_db_version_models');
                    $current_db_version_reservations = get_option('DOPBSP_db_version_reservations');
                    $current_db_version_rules = get_option('DOPBSP_db_version_rules');
                    $current_db_version_searches = get_option('DOPBSP_db_version_searches');
                    $current_db_version_settings = get_option('DOPBSP_db_version_settings');
                    $current_db_version_settings_calendar = get_option('DOPBSP_db_version_settings_calendar');
                    $current_db_version_settings_notifications = get_option('DOPBSP_db_version_settings_notifications');
                    $current_db_version_settings_payment = get_option('DOPBSP_db_version_settings_payment');
                    $current_db_version_settings_search = get_option('DOPBSP_db_version_settings_search');
                    $current_db_version_translation = get_option('DOPBSP_db_version_translation');
                    
                    /*
                     * Update database.
                     */
                    $this->update();
                    
                    /*
                     * API keys table.
                     */
                    $sql_api_keys = "CREATE TABLE ".$DOPBSP->tables->api_keys." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->api_keys['user_id']." NOT NULL,
                                            api_key VARCHAR(128) DEFAULT '".$this->db_config->api_keys['api_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    /*
                     * Calendars table.
                     */
                    $sql_calendars = "CREATE TABLE ".$DOPBSP->tables->calendars." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->calendars['user_id']." NOT NULL,
                                            post_id BIGINT UNSIGNED DEFAULT ".$this->db_config->calendars['post_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->calendars['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            max_year INT UNSIGNED DEFAULT ".$this->db_config->calendars['max_year']." NOT NULL,
                                            hours_enabled VARCHAR(6) DEFAULT '".$this->db_config->calendars['hours_enabled']."' COLLATE ".$this->db_collation." NOT NULL,
                                            hours_interval_enabled VARCHAR(6) DEFAULT '".$this->db_config->calendars['hours_interval_enabled']."' COLLATE ".$this->db_collation." NOT NULL,
                                            min_available FLOAT DEFAULT ".$this->db_config->calendars['min_available']." NOT NULL,
                                            price_min FLOAT DEFAULT ".$this->db_config->calendars['price_min']." NOT NULL,
                                            price_max FLOAT DEFAULT ".$this->db_config->calendars['price_max']." NOT NULL,
                                            rating FLOAT DEFAULT ".$this->db_config->calendars['rating']." NOT NULL,
                                            address VARCHAR(512) DEFAULT '".$this->db_config->calendars['address']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_en VARCHAR(512) DEFAULT '".$this->db_config->calendars['address_en']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_alt VARCHAR(512) DEFAULT '".$this->db_config->calendars['address_alt']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_alt_en VARCHAR(512) DEFAULT '".$this->db_config->calendars['address_alt_en']."' COLLATE ".$this->db_collation." NOT NULL,
                                            coordinates TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            default_availability TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id),
                                            KEY post_id (post_id),
                                            KEY min_available (min_available),
                                            KEY price_min (price_min),
                                            KEY price_max (price_max)
                                        );";
                    
                    /*
                     * Coupons table.
                     */
                    $sql_coupons = "CREATE TABLE ".$DOPBSP->tables->coupons." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->coupons['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->coupons['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            code VARCHAR(16) DEFAULT '".$this->db_config->coupons['code']."' COLLATE ".$this->db_collation." NOT NULL,
                                            start_date VARCHAR(16) DEFAULT '".$this->db_config->coupons['start_date']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_date VARCHAR(16) DEFAULT '".$this->db_config->coupons['end_date']."' COLLATE ".$this->db_collation." NOT NULL,
                                            start_hour VARCHAR(16) DEFAULT '".$this->db_config->coupons['start_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_hour VARCHAR(16) DEFAULT '".$this->db_config->coupons['end_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            no_coupons VARCHAR(16) DEFAULT '".$this->db_config->coupons['no_coupons']."' COLLATE ".$this->db_collation." NOT NULL,
                                            operation VARCHAR(1) DEFAULT '".$this->db_config->coupons['operation']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price FLOAT DEFAULT '".$this->db_config->coupons['price']."' NOT NULL,
                                            price_type VARCHAR(8) DEFAULT '".$this->db_config->coupons['price_type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price_by VARCHAR(8) DEFAULT '".$this->db_config->coupons['price_by']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    /*
                     * Days table.
                     */
                    $sql_days = "CREATE TABLE ".$DOPBSP->tables->days." (
                                            unique_key VARCHAR(32) COLLATE ".$this->db_collation." NOT NULL,
                                            calendar_id BIGINT UNSIGNED DEFAULT ".$this->db_config->days['calendar_id']." NOT NULL,
                                            day VARCHAR(16) DEFAULT '".$this->db_config->days['day']."' COLLATE ".$this->db_collation." NOT NULL,
                                            year SMALLINT UNSIGNED DEFAULT ".$this->db_config->days['year']." NOT NULL,
                                            data TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            min_available FLOAT DEFAULT '".$this->db_config->days['min_available']."' NOT NULL,
                                            price_min FLOAT DEFAULT '".$this->db_config->days['price_min']."' NOT NULL,
                                            price_max FLOAT DEFAULT '".$this->db_config->days['price_max']."' NOT NULL,
                                            UNIQUE KEY id (unique_key),
                                            KEY calendar_id (calendar_id),
                                            KEY day (day),
                                            KEY year (year),
                                            KEY min_available (min_available),
                                            KEY price_min (price_min),
                                            KEY price_max (price_max)
                                        );";
                    
                    $sql_days_available = "CREATE TABLE ".$DOPBSP->tables->days_available." (
                                            unique_key VARCHAR(32) COLLATE ".$this->db_collation." NOT NULL,
                                            day VARCHAR(16) DEFAULT '".$this->db_config->days_available['day']."' COLLATE ".$this->db_collation." NOT NULL,
                                            hour VARCHAR(6) DEFAULT '".$this->db_config->days_available['hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            data LONGTEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (unique_key),
                                            KEY day (day),
                                            KEY hour (hour)
                                        );";
                    
                    $sql_days_unavailable = "CREATE TABLE ".$DOPBSP->tables->days_unavailable." (
                                            unique_key VARCHAR(32) COLLATE ".$this->db_collation." NOT NULL,
                                            day VARCHAR(16) DEFAULT '".$this->db_config->days_unavailable['day']."' COLLATE ".$this->db_collation." NOT NULL,
                                            hour VARCHAR(6) DEFAULT '".$this->db_config->days_unavailable['hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            data LONGTEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (unique_key),
                                            KEY day (day),
                                            KEY hour (hour)
                                        );";

                    /*
                     * Discounts tables.
                     */
                    $sql_discounts = "CREATE TABLE ".$DOPBSP->tables->discounts." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->discounts['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->discounts['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            extras VARCHAR(6) DEFAULT '".$this->db_config->discounts['extras']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    $sql_discounts_items = "CREATE TABLE ".$DOPBSP->tables->discounts_items." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            discount_id BIGINT UNSIGNED DEFAULT ".$this->db_config->discounts_items['discount_id']." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->discounts_items['position']." NOT NULL,
                                            start_time_lapse VARCHAR(8) DEFAULT '".$this->db_config->discounts_items['start_time_lapse']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_time_lapse VARCHAR(8) DEFAULT '".$this->db_config->discounts_items['end_time_lapse']."' COLLATE ".$this->db_collation." NOT NULL,
                                            operation VARCHAR(1) DEFAULT '".$this->db_config->discounts_items['operation']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price FLOAT DEFAULT '".$this->db_config->discounts_items['price']."' NOT NULL,
                                            price_type VARCHAR(8) DEFAULT '".$this->db_config->discounts_items['price_type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price_by VARCHAR(8) DEFAULT '".$this->db_config->discounts_items['price_by']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY discount_id (discount_id)
                                        );";
                    
                    $sql_discounts_items_rules = "CREATE TABLE ".$DOPBSP->tables->discounts_items_rules." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            discount_item_id BIGINT UNSIGNED DEFAULT ".$this->db_config->discounts_items_rules['discount_item_id']." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->discounts_items_rules['position']." NOT NULL,
                                            start_date VARCHAR(16) DEFAULT '".$this->db_config->discounts_items_rules['start_date']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_date VARCHAR(16) DEFAULT '".$this->db_config->discounts_items_rules['end_date']."' COLLATE ".$this->db_collation." NOT NULL,
                                            start_hour VARCHAR(16) DEFAULT '".$this->db_config->discounts_items_rules['start_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_hour VARCHAR(16) DEFAULT '".$this->db_config->discounts_items_rules['end_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            operation VARCHAR(1) DEFAULT '".$this->db_config->discounts_items_rules['operation']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price FLOAT DEFAULT '".$this->db_config->discounts_items_rules['price']."' NOT NULL,
                                            price_type VARCHAR(8) DEFAULT '".$this->db_config->discounts_items_rules['price_type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price_by VARCHAR(8) DEFAULT '".$this->db_config->discounts_items_rules['price_by']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY discount_item_id (discount_item_id)
                                        );";
                    
                    /*
                     * Emails table.
                     */
                    $sql_emails = "CREATE TABLE ".$DOPBSP->tables->emails." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->emails['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->emails['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    $sql_emails_translation = "CREATE TABLE ".$DOPBSP->tables->emails_translation." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            email_id BIGINT UNSIGNED DEFAULT ".$this->db_config->emails_translation['email_id']." NOT NULL,
                                            template VARCHAR(64) DEFAULT '".$this->db_config->emails_translation['template']."' COLLATE ".$this->db_collation." NOT NULL,
                                            subject TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            message LONGTEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY email_id (email_id),
                                            KEY template (template)
                                        );";

                    /*
                     * Extras tables.
                     */
                    $sql_extras = "CREATE TABLE ".$DOPBSP->tables->extras." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->extras['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->extras['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    $sql_extras_groups = "CREATE TABLE ".$DOPBSP->tables->extras_groups." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            extra_id BIGINT UNSIGNED DEFAULT ".$this->db_config->extras_groups['extra_id']." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->extras_groups['position']." NOT NULL,
                                            multiple_select VARCHAR(6) DEFAULT '".$this->db_config->extras_groups['multiple_select']."' COLLATE ".$this->db_collation." NOT NULL,
                                            required VARCHAR(6) DEFAULT '".$this->db_config->extras_groups['required']."' COLLATE ".$this->db_collation." NOT NULL,
                                            no_items_multiply VARCHAR(6) DEFAULT '".$this->db_config->extras_groups['no_items_multiply']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY extra_id (extra_id)
                                        );";
                    
                    $sql_extras_groups_items = "CREATE TABLE ".$DOPBSP->tables->extras_groups_items." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            group_id BIGINT UNSIGNED DEFAULT ".$this->db_config->extras_groups_items['group_id']." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->extras_groups_items['position']." NOT NULL,
                                            operation VARCHAR(1) DEFAULT '".$this->db_config->extras_groups_items['operation']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price FLOAT DEFAULT '".$this->db_config->extras_groups_items['price']."' NOT NULL,
                                            price_type VARCHAR(8) DEFAULT '".$this->db_config->extras_groups_items['price_type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price_by VARCHAR(8) DEFAULT '".$this->db_config->extras_groups_items['price_by']."' COLLATE ".$this->db_collation." NOT NULL,
                                            default_value VARCHAR(3) DEFAULT '".$this->db_config->extras_groups_items['default']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY group_id (group_id)
                                        );";
                    
                    /*
                     * Fees table.
                     */
                    $sql_fees = "CREATE TABLE ".$DOPBSP->tables->fees." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->fees['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->fees['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            operation VARCHAR(1) DEFAULT '".$this->db_config->fees['operation']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price FLOAT DEFAULT '".$this->db_config->fees['price']."' NOT NULL,
                                            price_type VARCHAR(8) DEFAULT '".$this->db_config->fees['price_type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            price_by VARCHAR(8) DEFAULT '".$this->db_config->fees['price_by']."' COLLATE ".$this->db_collation." NOT NULL,
                                            included VARCHAR(6) DEFAULT '".$this->db_config->fees['included']."' COLLATE ".$this->db_collation." NOT NULL,
                                            extras VARCHAR(6) DEFAULT '".$this->db_config->fees['extras']."' COLLATE ".$this->db_collation." NOT NULL,
                                            cart VARCHAR(6) DEFAULT '".$this->db_config->fees['cart']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";

                    /*
                     * Forms tables.
                     */
                    $sql_forms = "CREATE TABLE " . $DOPBSP->tables->forms . " (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->forms['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->forms['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    $sql_forms_fields = "CREATE TABLE " . $DOPBSP->tables->forms_fields . " (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            form_id BIGINT UNSIGNED DEFAULT ".$this->db_config->forms_fields['form_id']." NOT NULL,
                                            type VARCHAR(20) DEFAULT '".$this->db_config->forms_fields['type']."' COLLATE ".$this->db_collation." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->forms_fields['position']." NOT NULL,
                                            multiple_select VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['multiple_select']."' COLLATE ".$this->db_collation." NOT NULL,
                                            allowed_characters TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            size INT UNSIGNED DEFAULT ".$this->db_config->forms_fields['size']." NOT NULL,
                                            is_email VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['is_email']."' COLLATE ".$this->db_collation." NOT NULL,
                                            is_phone VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['is_phone']."' COLLATE ".$this->db_collation." NOT NULL,
                                            required VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['required']."' COLLATE ".$this->db_collation." NOT NULL,
                                            add_to_day_hour_info VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['add_to_day_hour_info']."' COLLATE ".$this->db_collation." NOT NULL,
                                            add_to_day_hour_body VARCHAR(6) DEFAULT '".$this->db_config->forms_fields['add_to_day_hour_body']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY form_id (form_id)
                                        );";
                    
                    $sql_forms_select_options = "CREATE TABLE " . $DOPBSP->tables->forms_fields_options . " (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            field_id BIGINT UNSIGNED DEFAULT ".$this->db_config->forms_fields_options['field_id']." NOT NULL,
                                            position INT UNSIGNED DEFAULT ".$this->db_config->forms_fields_options['position']." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY field_id (field_id)
                                        );";
                    
                    /*
                     * Languages table.
                     */
                    $sql_languages = "CREATE TABLE ".$DOPBSP->tables->languages." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->languages['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            code VARCHAR(2) DEFAULT '".$this->db_config->languages['code']."' COLLATE ".$this->db_collation." NOT NULL,
                                            enabled VARCHAR(6) DEFAULT ".$this->db_config->languages['enabled']." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY code (code),
                                            KEY enabled (enabled)
                                        );";
                    
                    /*
                     * Locations table.
                     */
                    $sql_locations = "CREATE TABLE ".$DOPBSP->tables->locations." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->locations['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->locations['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address VARCHAR(512) DEFAULT '".$this->db_config->locations['address']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_en VARCHAR(512) DEFAULT '".$this->db_config->locations['address_en']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_alt VARCHAR(512) DEFAULT '".$this->db_config->locations['address_alt']."' COLLATE ".$this->db_collation." NOT NULL,
                                            address_alt_en VARCHAR(512) DEFAULT '".$this->db_config->locations['address_alt_en']."' COLLATE ".$this->db_collation." NOT NULL,
                                            coordinates TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            calendars TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            link VARCHAR(512) DEFAULT '".$this->db_config->locations['link']."' COLLATE ".$this->db_collation." NOT NULL,
                                            image VARCHAR(512) DEFAULT '".$this->db_config->locations['image']."' COLLATE ".$this->db_collation." NOT NULL,
                                            businesses TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            businesses_other TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            languages TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            email VARCHAR(512) DEFAULT '".$this->db_config->locations['email']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    /*
                     * Models table.
                     */
                    $sql_models = "CREATE TABLE ".$DOPBSP->tables->models." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->models['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->models['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            enabled VARCHAR(6) DEFAULT '".$this->db_config->models['enabled']."' COLLATE ".$this->db_collation." NOT NULL,
				            multiple_calendars VARCHAR(6) DEFAULT '".$this->db_config->models['multiple_calendars']."' COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            translation_calendar TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";

                    /*
                     * Reservations table.
                     */   
                    $sql_reservations = "CREATE TABLE " . $DOPBSP->tables->reservations . " (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            calendar_id BIGINT UNSIGNED DEFAULT ".$this->db_config->reservations['calendar_id']." NOT NULL,
                                            language VARCHAR(8) DEFAULT '".$this->db_config->reservations['language']."' COLLATE ".$this->db_collation." NOT NULL,
                                            currency VARCHAR(32) DEFAULT '".$this->db_config->reservations['currency']."' COLLATE ".$this->db_collation." NOT NULL,
                                            currency_code VARCHAR(8) DEFAULT '".$this->db_config->reservations['currency_code']."' COLLATE ".$this->db_collation." NOT NULL,
                                            check_in VARCHAR(16) DEFAULT '".$this->db_config->reservations['check_in']."' COLLATE ".$this->db_collation." NOT NULL,
                                            check_out VARCHAR(16) DEFAULT '".$this->db_config->reservations['check_out']."' COLLATE ".$this->db_collation." NOT NULL,
                                            start_hour VARCHAR(16) DEFAULT '".$this->db_config->reservations['start_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            end_hour VARCHAR(16) DEFAULT '".$this->db_config->reservations['end_hour']."' COLLATE ".$this->db_collation." NOT NULL,
                                            no_items INT UNSIGNED DEFAULT ".$this->db_config->reservations['no_items']." NOT NULL,
                                            price FLOAT DEFAULT ".$this->db_config->reservations['price']." NOT NULL,
                                            price_total FLOAT DEFAULT ".$this->db_config->reservations['price_total']." NOT NULL,
                                            refund FLOAT DEFAULT ".$this->db_config->reservations['refund']." NOT NULL,
                                            extras TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            extras_price FLOAT DEFAULT ".$this->db_config->reservations['extras_price']." NOT NULL,
                                            discount TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            discount_price FLOAT DEFAULT ".$this->db_config->reservations['discount_price']." NOT NULL,
                                            coupon TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            coupon_price FLOAT DEFAULT ".$this->db_config->reservations['coupon_price']." NOT NULL,
                                            fees TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            fees_price FLOAT DEFAULT ".$this->db_config->reservations['fees_price']." NOT NULL,
                                            deposit TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            deposit_price FLOAT DEFAULT ".$this->db_config->reservations['deposit_price']." NOT NULL,
                                            days_hours_history TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            form TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            address_billing TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            address_shipping TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            email VARCHAR(128) DEFAULT '".$this->db_config->reservations['email']."' COLLATE ".$this->db_collation." NOT NULL,
                                            phone VARCHAR(32) DEFAULT '".$this->db_config->reservations['phone']."' COLLATE ".$this->db_collation." NOT NULL,
                                            status VARCHAR(16) DEFAULT '".$this->db_config->reservations['status']."' COLLATE ".$this->db_collation." NOT NULL,
                                            payment_method VARCHAR(32) DEFAULT '".$this->db_config->reservations['payment_method']."' NOT NULL, 
                                            payment_status VARCHAR(32) DEFAULT '".$this->db_config->reservations['payment_status']."' NOT NULL, 
                                            transaction_id VARCHAR(128) DEFAULT '".$this->db_config->reservations['transaction_id']."' COLLATE ".$this->db_collation." NOT NULL, 
                                            token VARCHAR(128) DEFAULT '".$this->db_config->reservations['token']."' NOT NULL, 
                                            ip VARCHAR(32) DEFAULT '".$this->db_config->reservations['ip']."' NOT NULL, 
                                            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY calendar_id (calendar_id),
                                            KEY check_in (check_in),
                                            KEY check_out (check_out),
                                            KEY start_hour (end_hour),
                                            KEY status (status),
                                            KEY payment_method (payment_method),
                                            KEY transaction_id (transaction_id),
                                            KEY token (token)
                                    );";
                    
                    /*
                     * Rules table.
                     */
                    $sql_rules = "CREATE TABLE ".$DOPBSP->tables->rules." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->rules['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->rules['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            time_lapse_min FLOAT DEFAULT '".$this->db_config->rules['time_lapse_min']."' NOT NULL,
                                            time_lapse_max FLOAT DEFAULT '".$this->db_config->rules['time_lapse_max']."' NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    /*
                     * Search tables.
                     */
                    $sql_searches = "CREATE TABLE ".$DOPBSP->tables->searches." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            user_id BIGINT UNSIGNED DEFAULT ".$this->db_config->searches['user_id']." NOT NULL,
                                            name VARCHAR(128) DEFAULT '".$this->db_config->searches['name']."' COLLATE ".$this->db_collation." NOT NULL,
                                            calendars_excluded TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            currency VARCHAR(128) DEFAULT '".$this->db_config->searches['currency']."' COLLATE ".$this->db_collation." NOT NULL,
                                            currency_position VARCHAR(32) DEFAULT '".$this->db_config->searches['currency_position']."' COLLATE ".$this->db_collation." NOT NULL,
                                            hours_enabled VARCHAR(6) DEFAULT '".$this->db_config->searches['hours_enabled']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY user_id (user_id)
                                        );";
                    
                    /*
                     * Settings tables.
                     */
                    $sql_settings = "CREATE TABLE ".$DOPBSP->tables->settings." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            unique_key VARCHAR(128) DEFAULT '".$this->db_config->settings['unique_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            value VARCHAR(128) DEFAULT '".$this->db_config->settings['value']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY unique_key (unique_key)
                                        );";
                    
                    $sql_settings_calendar = "CREATE TABLE ".$DOPBSP->tables->settings_calendar." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            calendar_id BIGINT UNSIGNED DEFAULT ".$this->db_config->settings_calendar['calendar_id']." NOT NULL,
                                            unique_key VARCHAR(128) DEFAULT '".$this->db_config->settings_calendar['unique_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            value TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY calendar_id (calendar_id),
                                            KEY unique_key (unique_key)
                                        );";
                    
                    $sql_settings_notifications = "CREATE TABLE ".$DOPBSP->tables->settings_notifications." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            calendar_id BIGINT UNSIGNED DEFAULT ".$this->db_config->settings_notifications['calendar_id']." NOT NULL,
                                            unique_key VARCHAR(128) DEFAULT '".$this->db_config->settings_notifications['unique_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            value TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY calendar_id (calendar_id),
                                            KEY unique_key (unique_key)
                                        );";
                    
                    $sql_settings_payment = "CREATE TABLE ".$DOPBSP->tables->settings_payment." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            calendar_id BIGINT UNSIGNED DEFAULT ".$this->db_config->settings_payment['calendar_id']." NOT NULL,
                                            unique_key VARCHAR(128) DEFAULT '".$this->db_config->settings_payment['unique_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            value TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY calendar_id (calendar_id),
                                            KEY unique_key (unique_key)
                                        );";
                    
                    $sql_settings_search = "CREATE TABLE ".$DOPBSP->tables->settings_search." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            search_id BIGINT UNSIGNED DEFAULT ".$this->db_config->settings_search['search_id']." NOT NULL,
                                            unique_key VARCHAR(128) DEFAULT '".$this->db_config->settings_search['unique_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            value TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY search_id (search_id),
                                            KEY unique_key (unique_key)
                                        );";
                    
                    /*
                     * Translation tables.
                     */
                    $languages[0] = 'en';
                    $languages = explode(',', DOPBSP_CONFIG_TRANSLATION_LANGUAGES_TO_INSTALL);
                    
                    for ($l=0; $l<count($languages); $l++){
                        $sql_translation = "CREATE TABLE ".$DOPBSP->tables->translation."_".$languages[$l]." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            key_data VARCHAR(128) DEFAULT '".$this->db_config->translation['key_data']."' COLLATE ".$this->db_collation." NOT NULL,
                                            parent_key VARCHAR(128) DEFAULT '".$this->db_config->translation['parent_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            text_data TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            location VARCHAR(32) DEFAULT '".$this->db_config->translation['location']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY key_data (key_data)
                                        );";
                        $this->db_version_translation != $current_db_version_translation ? dbDelta($sql_translation):'';
                    }
                    
                    /*
                     * Update Translations.
                     */
                    $this->updateTranslations2x();
                    
                    /*
                     * Create/update the database tables.
                     */
                    $this->db_version_api_keys != $current_db_version_api_keys ? dbDelta($sql_api_keys):'';
                    $this->db_version_calendars != $current_db_version_calendars ? dbDelta($sql_calendars):'';
                    $this->db_version_coupons != $current_db_version_coupons ? dbDelta($sql_coupons):'';
                    $this->db_version_days != $current_db_version_days ? dbDelta($sql_days):'';
                    $this->db_version_days_available != $current_db_version_days_available ? dbDelta($sql_days_available):'';
                    $this->db_version_days_unavailable != $current_db_version_days_unavailable ? dbDelta($sql_days_unavailable):'';
                    $this->db_version_discounts != $current_db_version_discounts ? dbDelta($sql_discounts):'';
                    $this->db_version_discounts_items != $current_db_version_discounts_items ? dbDelta($sql_discounts_items):'';
                    $this->db_version_discounts_items_rules != $current_db_version_discounts_items_rules ? dbDelta($sql_discounts_items_rules):'';
                    $this->db_version_emails != $current_db_version_emails ? dbDelta($sql_emails):'';
                    $this->db_version_emails_translation != $current_db_version_emails_translation ? dbDelta($sql_emails_translation):'';
                    $this->db_version_extras != $current_db_version_extras ? dbDelta($sql_extras):'';
                    $this->db_version_extras_groups != $current_db_version_extras_groups ? dbDelta($sql_extras_groups):'';
                    $this->db_version_extras_groups_items != $current_db_version_extras_groups_items ? dbDelta($sql_extras_groups_items):'';
                    $this->db_version_fees != $current_db_version_fees ? dbDelta($sql_fees):'';
                    $this->db_version_forms != $current_db_version_forms ? dbDelta($sql_forms):'';
                    $this->db_version_forms_fields != $current_db_version_forms_fields ? dbDelta($sql_forms_fields):'';
                    $this->db_version_forms_select_options != $current_db_version_forms_select_options ? dbDelta($sql_forms_select_options):'';
                    $this->db_version_languages != $current_db_version_languages ? dbDelta($sql_languages):'';
                    $this->db_version_locations != $current_db_version_locations ? dbDelta($sql_locations):'';
                    $this->db_version_models != $current_db_version_models ? dbDelta($sql_models):'';
                    $this->db_version_reservations != $current_db_version_reservations ? dbDelta($sql_reservations):'';
                    $this->db_version_rules != $current_db_version_rules ? dbDelta($sql_rules):'';
                    $this->db_version_searches != $current_db_version_searches ? dbDelta($sql_searches):'';
                    $this->db_version_settings != $current_db_version_settings ? dbDelta($sql_settings):'';
                    $this->db_version_settings_calendar != $current_db_version_settings_calendar ? dbDelta($sql_settings_calendar):'';
                    $this->db_version_settings_notifications != $current_db_version_settings_notifications ? dbDelta($sql_settings_notifications):'';
                    $this->db_version_settings_payment != $current_db_version_settings_payment ? dbDelta($sql_settings_payment):'';
                    $this->db_version_settings_search != $current_db_version_settings_search ? dbDelta($sql_settings_search):'';
                    
                    /*
                     * Update database version.
                     */
                    $current_db_version == '' ? add_option('DOPBSP_db_version', $this->db_version):update_option('DOPBSP_db_version', $this->db_version);
                        
                    /*
                     * Update tables' versions.
                     */
                    $current_db_version_api_keys == '' ? add_option('DOPBSP_db_version_api_keys', $this->db_version_api_keys):
                                                          update_option('DOPBSP_db_version_api_keys', $this->db_version_api_keys);
                    $current_db_version_calendars == '' ? add_option('DOPBSP_db_version_calendars', $this->db_version_calendars):
                                                          update_option('DOPBSP_db_version_calendars', $this->db_version_calendars);
                    $current_db_version_coupons == '' ? add_option('DOPBSP_db_version_coupons', $this->db_version_coupons):
                                                        update_option('DOPBSP_db_version_coupons', $this->db_version_coupons);
                    $current_db_version_days == '' ? add_option('DOPBSP_db_version_days', $this->db_version_days):
                                                     update_option('DOPBSP_db_version_days', $this->db_version_days);
                    $current_db_version_days_available == '' ? add_option('DOPBSP_db_version_days_available', $this->db_version_days_available):
                                                               update_option('DOPBSP_db_version_days_available', $this->db_version_days_available);
                    $current_db_version_days_unavailable == '' ? add_option('DOPBSP_db_version_days_unavailable', $this->db_version_days_unavailable):
                                                                 update_option('DOPBSP_db_version_days_unavailable', $this->db_version_days_unavailable);
                    $current_db_version_discounts == '' ? add_option('DOPBSP_db_version_discounts', $this->db_version_discounts):
                                                          update_option('DOPBSP_db_version_discounts', $this->db_version_discounts);
                    $current_db_version_discounts_items == '' ? add_option('DOPBSP_db_version_discounts_items', $this->db_version_discounts_items):
                                                                update_option('DOPBSP_db_version_discounts_items', $this->db_version_discounts_items);
                    $current_db_version_discounts_items_rules == '' ? add_option('DOPBSP_db_version_discounts_items_rules', $this->db_version_discounts_items_rules):
                                                                      update_option('DOPBSP_db_version_discounts_items_rules', $this->db_version_discounts_items_rules);
                    $current_db_version_emails == '' ? add_option('DOPBSP_db_version_emails', $this->db_version_emails):
                                                       update_option('DOPBSP_db_version_emails', $this->db_version_emails);
                    $current_db_version_emails_translation == '' ? add_option('DOPBSP_db_version_emails_translation', $this->db_version_emails_translation):
                                                                   update_option('DOPBSP_db_version_emails_translation', $this->db_version_emails_translation);
                    $current_db_version_extras == '' ? add_option('DOPBSP_db_version_extras', $this->db_version_extras):
                                                       update_option('DOPBSP_db_version_extras', $this->db_version_extras);
                    $current_db_version_extras_groups == '' ? add_option('DOPBSP_db_version_extras_groups', $this->db_version_extras_groups):
                                                              update_option('DOPBSP_db_version_extras_groups', $this->db_version_extras_groups);
                    $current_db_version_extras_groups_items == '' ? add_option('DOPBSP_db_version_extras_groups_items', $this->db_version_extras_groups_items):
                                                                    update_option('DOPBSP_db_version_extras_groups_items', $this->db_version_extras_groups_items);
                    $current_db_version_fees == '' ? add_option('DOPBSP_db_version_fees', $this->db_version_fees):
                                                     update_option('DOPBSP_db_version_fees', $this->db_version_fees);
                    $current_db_version_forms == '' ? add_option('DOPBSP_db_version_forms', $this->db_version_forms):
                                                      update_option('DOPBSP_db_version_forms', $this->db_version_forms);
                    $current_db_version_forms_fields == '' ? add_option('DOPBSP_db_version_forms_fields', $this->db_version_forms_fields):
                                                             update_option('DOPBSP_db_version_forms_fields', $this->db_version_forms_fields);
                    $current_db_version_forms_select_options == '' ? add_option('DOPBSP_db_version_forms_select_options', $this->db_version_forms_select_options):
                                                                     update_option('DOPBSP_db_version_forms_select_options', $this->db_version_forms_select_options);
                    $current_db_version_languages == '' ? add_option('DOPBSP_db_version_languages', $this->db_version_languages):
                                                          update_option('DOPBSP_db_version_languages', $this->db_version_languages);
                    $current_db_version_locations == '' ? add_option('DOPBSP_db_version_locations', $this->db_version_locations):
                                                          update_option('DOPBSP_db_version_locations', $this->db_version_locations);
                    $current_db_version_models == '' ? add_option('DOPBSP_db_version_models', $this->db_version_models):
                                                       update_option('DOPBSP_db_version_models', $this->db_version_models);
                    $current_db_version_reservations == '' ? add_option('DOPBSP_db_version_reservations', $this->db_version_reservations):
                                                             update_option('DOPBSP_db_version_reservations', $this->db_version_reservations);
                    $current_db_version_rules == '' ? add_option('DOPBSP_db_version_rules', $this->db_version_rules):
                                                      update_option('DOPBSP_db_version_rules', $this->db_version_rules);
                    $current_db_version_searches == '' ? add_option('DOPBSP_db_version_searches', $this->db_version_searches):
                                                         update_option('DOPBSP_db_version_searches', $this->db_version_searches);
                    $current_db_version_settings == '' ? add_option('DOPBSP_db_version_settings', $this->db_version_settings):
                                                         update_option('DOPBSP_db_version_settings', $this->db_version_settings);
                    $current_db_version_settings_calendar == '' ? add_option('DOPBSP_db_version_settings_calendar', $this->db_version_settings_calendar):
                                                                  update_option('DOPBSP_db_version_settings_calendar', $this->db_version_settings_calendar);
                    $current_db_version_settings_notifications == '' ? add_option('DOPBSP_db_version_settings_notifications', $this->db_version_settings_notifications):
                                                                       update_option('DOPBSP_db_version_settings_notifications', $this->db_version_settings_notifications);
                    $current_db_version_settings_payment == '' ? add_option('DOPBSP_db_version_settings_payment', $this->db_version_settings_payment):
                                                                 update_option('DOPBSP_db_version_settings_payment', $this->db_version_settings_payment);
                    $current_db_version_settings_search == '' ? add_option('DOPBSP_db_version_settings_search', $this->db_version_settings_search):
                                                                update_option('DOPBSP_db_version_settings_search', $this->db_version_settings_search);
                    $current_db_version_translation == '' ? add_option('DOPBSP_db_version_translation', $this->db_version_translation):
                                                            update_option('DOPBSP_db_version_translation', $this->db_version_translation);
                    
                    /*
                     * Initialize users permissions.
                     */
                    $DOPBSP->classes->backend_settings_users->init();
                    
                    $this->set();
                }
            }
            
// Set            
            
            /*
             * Set initial data for plugin tables.
             */
            function set(){
                global $DOPBSP;
                
                /*
                 * Translation data.
                 */
                $DOPBSP->classes->backend_translation->database();
                
                if(is_admin()) {
                    $DOPBSP->classes->translation->set();
                }
                
                /*
                 * Emails data.
                 */
                $this->setEmails();

                /*
                 * Extras data.
                 */
                $this->setExtras();
                
                /*
                 * Forms data.
                 */
                $this->setForms();
                
                /*
                 * Searches data.
                 */
                $this->setSearches();
            }
            
            /*
             * Set emails data.
             */
            function setEmails(){
                global $wpdb;
                global $DOPBSP;
                
                $control_data = $wpdb->get_row('SELECT * FROM '.$DOPBSP->tables->emails.' WHERE id=1');
                
                if ($wpdb->num_rows == 0){
                     $wpdb->insert($DOPBSP->tables->emails, array('id' => 1,
                                                                  'user_id' => 0,
                                                                  'name' => $DOPBSP->text('EMAILS_DEFAULT_NAME')));
                    /*
                     * Simple book.
                     */
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'book_admin',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_BOOK_ADMIN_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_BOOK_ADMIN')));
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'book_user',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_BOOK_USER_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_BOOK_USER')));
                    /*
                     * Book with approval.
                     */
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'book_with_approval_admin',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_BOOK_WITH_APPROVAL_ADMIN_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_BOOK_WITH_APPROVAL_ADMIN')));
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'book_with_approval_user',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_BOOK_WITH_APPROVAL_USER_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_BOOK_WITH_APPROVAL_USER')));
                    /*
                     * Approved
                     */
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'approved',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_APPROVED_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_APPROVED')));
                    /*
                     * Canceled
                     */
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'canceled',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_CANCELED_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_CANCELED')));
                    /*
                      Rejected
                     */
                    $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                             'template' => 'rejected',
                                                                             'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_REJECTED_SUBJECT'),
                                                                             'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_REJECTED')));

                    /*
                     * Payment gateways.
                     */
                    $pg_list = $DOPBSP->classes->payment_gateways->get();

                    for ($i=0; $i<count($pg_list); $i++){
                        $pg_id = $pg_list[$i];
                        
                        $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                                 'template' => $pg_id.'_admin',
                                                                                 'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_'.strtoupper($pg_id).'_ADMIN_SUBJECT'),
                                                                                 'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_'.strtoupper($pg_id).'_ADMIN')));
                        $wpdb->insert($DOPBSP->tables->emails_translation, array('email_id' => 1,
                                                                                 'template' => $pg_id.'_user',
                                                                                 'subject' => $DOPBSP->classes->translation->encodeJSON('EMAILS_DEFAULT_'.strtoupper($pg_id).'_USER_SUBJECT'),
                                                                                 'message' => $DOPBSP->classes->backend_email->defaultTemplate('EMAILS_DEFAULT_'.strtoupper($pg_id).'_USER')));
                    }
                }
            }
            
            /*
             * Set extras data.
             */
            function setExtras(){
                global $wpdb;
                global $DOPBSP;
                
                $control_data = $wpdb->get_row('SELECT * FROM '.$DOPBSP->tables->extras.' WHERE id=1');
                
                if ($wpdb->num_rows == 0){
                    $wpdb->insert($DOPBSP->tables->extras, array('id' => 1,
                                                                 'user_id' => 0,
                                                                 'name' => $DOPBSP->text('EXTRAS_DEFAULT_PEOPLE')));
                    $wpdb->insert($DOPBSP->tables->extras_groups, array('id' => 1,
                                                                        'extra_id' => 1,
                                                                        'position' => 1,
                                                                        'multiple_select' => 'false',
                                                                        'required' => 'true',
                                                                        'translation' => $DOPBSP->classes->translation->encodeJSON('EXTRAS_DEFAULT_ADULTS')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 1,
                                                                              'group_id' => 1,
                                                                              'position' => 1,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '1')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 2,
                                                                              'group_id' => 1,
                                                                              'position' => 2,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '2')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 3,
                                                                              'group_id' => 1,
                                                                              'position' => 3,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '3')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 4,
                                                                              'group_id' => 1,
                                                                              'position' => 4,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '4')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 5,
                                                                              'group_id' => 1,
                                                                              'position' => 5,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '5')));
                    $wpdb->insert($DOPBSP->tables->extras_groups, array('id' => 2,
                                                                        'extra_id' => 1,
                                                                        'position' => 2,
                                                                        'multiple_select' => 'false',
                                                                        'required' => 'true',
                                                                        'translation' => $DOPBSP->classes->translation->encodeJSON('EXTRAS_DEFAULT_CHILDREN')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 6,
                                                                              'group_id' => 2,
                                                                              'position' => 1,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '0')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 7,
                                                                              'group_id' => 2,
                                                                              'position' => 2,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '1')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 8,
                                                                              'group_id' => 2,
                                                                              'position' => 3,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '2')));
                    $wpdb->insert($DOPBSP->tables->extras_groups_items, array('id' => 9,
                                                                              'group_id' => 2,
                                                                              'position' => 4,
                                                                              'translation' => $DOPBSP->classes->translation->encodeJSON('', '3')));
                }
            }
            
            /*
             * Set forms data.
             */
            function setForms(){
                global $wpdb;
                global $DOPBSP;
                
                $control_data = $wpdb->get_row('SELECT * FROM '.$DOPBSP->tables->forms.' WHERE id=1');
                
                if ($wpdb->num_rows == 0){
                    $wpdb->insert($DOPBSP->tables->forms, array('id' => 1,
                                                                'user_id' => 0,
                                                                'name' => $DOPBSP->text('FORMS_DEFAULT_NAME')));
                    $wpdb->insert($DOPBSP->tables->forms_fields, array('id' => 1,
                                                                       'form_id' => 1,
                                                                       'type' => 'text',
                                                                       'position' => 1,
                                                                       'multiple_select' => 'false',
                                                                       'allowed_characters' => '',
                                                                       'size' => 0,
                                                                       'is_email' => 'false',
                                                                       'is_phone' => 'false',
                                                                       'required' => 'true',
                                                                       'translation' => $DOPBSP->classes->translation->encodeJSON('FORMS_DEFAULT_FIRST_NAME')));
                    $wpdb->insert($DOPBSP->tables->forms_fields, array('id' => 2,
                                                                       'form_id' => 1,
                                                                       'type' => 'text',
                                                                       'position' => 2,
                                                                       'multiple_select' => 'false',
                                                                       'allowed_characters' => '',
                                                                       'size' => 0,
                                                                       'is_email' => 'false',
                                                                       'is_phone' => 'false',
                                                                       'required' => 'true',
                                                                       'translation' => $DOPBSP->classes->translation->encodeJSON('FORMS_DEFAULT_LAST_NAME')));
                    $wpdb->insert($DOPBSP->tables->forms_fields, array('id' => 3,
                                                                       'form_id' => 1,
                                                                       'type' => 'text',
                                                                       'position' => 3,
                                                                       'multiple_select' => 'false',
                                                                       'allowed_characters' => '',
                                                                       'size' => 0,
                                                                       'is_email' => 'true',
                                                                       'is_phone' => 'false',
                                                                       'required' => 'true',
                                                                       'translation' => $DOPBSP->classes->translation->encodeJSON('FORMS_DEFAULT_EMAIL')));
                    $wpdb->insert($DOPBSP->tables->forms_fields, array('id' => 4,
                                                                       'form_id' => 1,
                                                                       'type' => 'text',
                                                                       'position' => 4,
                                                                       'multiple_select' => 'false',
                                                                       'allowed_characters' => '0123456789+-().',
                                                                       'size' => 0,
                                                                       'is_email' => 'false',
                                                                       'is_phone' => 'true',
                                                                       'required' => 'true',
                                                                       'translation' => $DOPBSP->classes->translation->encodeJSON('FORMS_DEFAULT_PHONE')));
                    $wpdb->insert($DOPBSP->tables->forms_fields, array('id' => 5,
                                                                       'form_id' => 1,
                                                                       'type' => 'textarea',
                                                                       'position' => 5,
                                                                       'multiple_select' => 'false',
                                                                       'allowed_characters' => '',
                                                                       'size' => 0,
                                                                       'is_email' => 'false',
                                                                       'is_phone' => 'false',
                                                                       'required' => 'true',
                                                                       'translation' => $DOPBSP->classes->translation->encodeJSON('FORMS_DEFAULT_MESSAGE')));
                }
            }
            
            /*
             * Set search data.
             */
            function setSearches(){
                global $wpdb;
                global $DOPBSP;
                
                $control_data = $wpdb->get_row('SELECT * FROM '.$DOPBSP->tables->searches.' WHERE id=1');
                
                if ($wpdb->num_rows == 0){
                    $wpdb->insert($DOPBSP->tables->searches, array('user_id' => 0,
                                                                   'name' => $DOPBSP->text('SEARCHES_ADD_SEARCH_NAME')));
                }
            }
            
// Update
            
            /*
             * Update database. Rename table columns and transfer data from old tables.
             */
            function update(){
                $current_db_version = get_option('DOPBSP_db_version');
                
                /*
                 * Rename calendar settings table.
                 */
                $this->updateRename();
                
                if ($current_db_version != ''
                        && $current_db_version < 2.0){
                    /*
                     * Forms tables.
                     */
                    $this->updateForms1x();

                    /*
                     * Reservation table.
                     */
                    $this->updateReservations1x();
                }
            }
            
            /*
             * Update forms tables from versions 1.x
             */
            function updateForms1x(){
                global $wpdb;
                global $DOPBSP;
                
                $fields = $wpdb->get_results('SELECT * FROM '.$DOPBSP->tables->forms_fields);

                foreach ($fields as $field){
                    if (!is_object(json_decode($field->translation))){
                        $wpdb->update($DOPBSP->tables->forms_fields, array('translation' => stripslashes($field->translation)), 
                                                                     array('id' => $field->id));
                    }
                }
            }
            
            /*
             * Update reservations tables from versions 1.x
             */
            function updateReservations1x(){
                global $wpdb;
                global $DOPBSP;
                
                $control_data = $wpdb->query('SHOW TABLES LIKE "'.$DOPBSP->tables->reservations.'"');

                if ($wpdb->num_rows != 0){
                    $new_columns = array('total_price' => 'CHANGE total_price price_total FLOAT DEFAULT '.$this->db_config->reservations['price_total'].' NOT NULL',
                                         'discount' => 'CHANGE discount discount_price FLOAT DEFAULT '.$this->db_config->reservations['discount_price'].' NOT NULL',
                                         'deposit' => 'CHANGE deposit deposit_price FLOAT DEFAULT '.$this->db_config->reservations['deposit_price'].' NOT NULL',
                                         'paypal_transaction_id' => 'CHANGE paypal_transaction_id transaction_id VARCHAR(128) DEFAULT "'.$this->db_config->reservations['transaction_id'].'" COLLATE '.$this->db_collation.' NOT NULL',
                                         'info' => 'CHANGE info form TEXT COLLATE '.$this->db_collation.' NOT NULL');
                    $valid = true;

                    $columns = $wpdb->get_results('SHOW COLUMNS FROM '.$DOPBSP->tables->reservations);

                    foreach ($columns as $column){
                        if ($column->Field == 'discount_price'
                                || $column->Field == 'deposit_price'){
                            $valid = false;
                        }
                    }

                    if ($valid){
                        foreach ($columns as $column){
                            foreach ($new_columns as $key => $query){
                                if ($column->Field == $key){
                                    $wpdb->query('ALTER TABLE '.$DOPBSP->tables->reservations.' '.$query);
                                }
                            }
                        }  

                        /*
                         * Update reservations data.
                         */
                        $reservations = $wpdb->get_results('SELECT * FROM '.$DOPBSP->tables->reservations);

                        foreach ($reservations as $reservation){
                            switch ($reservation->payment_method){
                                case '0':
                                    $payment_method = 'none';
                                    break;
                                case '1':
                                    $payment_method = 'default';
                                    break;
                                case '2':
                                    $payment_method = 'paypal';
                                    break;
                                default:
                                    $payment_method = $reservation->payment_method;
                            }

                            $form = json_decode($reservation->form);

                            for ($i=0; $i<count($form); $i++){
                                $form[$i]->translation = $form[$i]->name;
                            }

                            $wpdb->update($DOPBSP->tables->reservations, array('discount_price' => $reservation->discount,
                                                                               'deposit_price' => $reservation->deposit,
                                                                               'form' => json_encode($form),
                                                                               'payment_method' => $payment_method), 
                                                                         array('id' => $reservation->id));
                        }  
                    }
                }
            }
            
            /*
             * Update translations tables from versions 2.x
             */
            function updateTranslations2x(){
                global $wpdb;
                global $DOPBSP;
                $current_db_version = get_option('DOPBSP_db_version');
                
                if ($current_db_version != ''
                        && $current_db_version < 2.39){
                    
                
                    $languages = $wpdb->get_results('SELECT * FROM '.$DOPBSP->tables->languages.' WHERE enabled="true"');
                    $languages_codes = array();
                    
                    foreach($languages as $language) {
                        array_push($languages_codes, $language->code);
                    }
                    
                    /*
                     * Translation tables.
                     */
                    
                    for ($l=0; $l<count($languages_codes); $l++){
                        $sql_translation = "CREATE TABLE ".$DOPBSP->tables->translation."_".$languages_codes[$l]." (
                                            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                                            key_data VARCHAR(128) DEFAULT '".$this->db_config->translation['key_data']."' COLLATE ".$this->db_collation." NOT NULL,
                                            parent_key VARCHAR(128) DEFAULT '".$this->db_config->translation['parent_key']."' COLLATE ".$this->db_collation." NOT NULL,
                                            text_data TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            translation TEXT COLLATE ".$this->db_collation." NOT NULL,
                                            location VARCHAR(32) DEFAULT '".$this->db_config->translation['location']."' COLLATE ".$this->db_collation." NOT NULL,
                                            UNIQUE KEY id (id),
                                            KEY key_data (key_data)
                                        );";
                            
                        dbDelta($sql_translation);
                    }
                    
                    
                    /*
                     * Update Translation texts.
                     */
                    for ($l=0; $l<count($languages_codes); $l++){

                        $query_values = array();

                        for ($i=0; $i<count($DOPBSP->classes->translation->text); $i++){
                            /*
                             * Set add query values.
                             */
                            
                            if(isset($DOPBSP->classes->translation->text[$i]['location'])){
                                
                                if($DOPBSP->classes->translation->text[$i]['location'] != 'backend') {
                                    $wpdb->update( $DOPBSP->tables->translation.'_'.$languages_codes[$l], array( 'location' => $DOPBSP->classes->translation->text[$i]['location']),array('key_data'=> $DOPBSP->classes->translation->text[$i]['key'],
                                                        'parent_key'=> $DOPBSP->classes->translation->text[$i]['parent'],
                                                        'text_data'=> $DOPBSP->classes->translation->text[$i]['text']));
                                }
                            }
                        }
                    }
                }
            
            }
            
            /*
             * Rename tables names.
             */
            function updateRename(){
                global $wpdb;
                
                $current_db_version = get_option('DOPBSP_db_version');
                
                /*
                 * Rename calendars settings table.
                 */
                if ($current_db_version != ''
                        && $current_db_version < 2.165){
                    $control_data = $wpdb->query('SHOW TABLES LIKE "'.$wpdb->prefix.'dopbsp_settings_calendar"');
                    
                    if ($wpdb->num_rows == 0){
                        $wpdb->query('RENAME TABLE '.$wpdb->prefix.'dopbsp_settings TO '.$wpdb->prefix.'dopbsp_settings_calendar');
                    }
                }
            }
         
// Configuration
            
            /*
             * Set database configuration.
             * 
             * @param db_config (object): database configuration
             * 
             * @return database configuration
             */
            function config($db_config){
                /*
                 * API
                 */
                $db_config->api_keys = array('user_id' => 0,
                                             'api_key' => '');
                
                /*
                 * Calendars
                 */
                $db_config->calendars = array('user_id' => 0,
                                              'post_id' => 0,
                                              'name' => '',
                                              'max_year' => 0,
                                              'hours_enabled' => 'false',
                                              'hours_interval_enabled' => 'false',
                                              'min_available' => 0,
                                              'price_min' => 0,
                                              'price_max' => 0,
                                              'rating' => 0,
                                              'address' => '',
                                              'address_en' => '',
                                              'address_alt' => '',
                                              'address_alt_en' => '',
                                              'coordinates' => '',
                                              'default_availability' => '{"available":1,"bind":0,"hours":{},"hours_definitions":[{"value":"00:00"}],"info":"","notes":"","price":0,"promo":0,"status":"none"}');
                
                /*
                 * Coupons
                 */
                $db_config->coupons = array('user_id' => 0,
                                            'name' => '',
                                            'code' => '',
                                            'start_time_lapse' => '',
                                            'end_time_lapse' => '',
                                            'start_date' => '',
                                            'end_date' => '',
                                            'start_hour' => '',
                                            'end_hour' => '',
                                            'no_coupons' => '',
                                            'operation' => '+',
                                            'price' => 0,
                                            'price_type' => 'fixed',
                                            'price_by' => 'once',
                                            'translation' => '');
                
                /*
                 * Days
                 */
                $db_config->days = array('calendar_id' => 0,
                                         'day' => '',
                                         'year' => date('Y'),
                                         'data' => '',
                                         'min_available' => 0,
                                         'price_min' => 0,
                                         'price_max' => 0);
                
                /*
                 * Days available.
                 */
                $db_config->days_available = array('day' => '',
                                                   'hour' => '',
                                                   'data' => '');
                
                /*
                 * Days unavailable
                 */
                $db_config->days_unavailable = array('day' => '',
                                                     'hour' => '',
                                                     'data' => '');
                
                /*
                 * Discounts
                 */
                $db_config->discounts = array('user_id' => 0,
                                              'name' => '',
                                              'extras' => 'false');
                
                /*
                 * Discounts items.
                 */
                $db_config->discounts_items = array('discount_id' => 0,
                                                    'position' => 0,
                                                    'start_time_lapse' => '',
                                                    'end_time_lapse' => '',
                                                    'operation' => '-',
                                                    'price' => 0,
                                                    'price_type' => 'percent',
                                                    'price_by' => 'once',
                                                    'translation' => '');
                
                /*
                 * Discounts items rules.
                 */
                $db_config->discounts_items_rules = array('discount_item_id' => 0,
                                                          'position' => 0,
                                                          'start_date' => '',
                                                          'end_date' => '',
                                                          'start_hour' => '',
                                                          'end_hour' => '',
                                                          'operation' => '-',
                                                          'price' => 0,
                                                          'price_type' => 'percent',
                                                          'price_by' => 'once');
                
                /*
                 * Emails
                 */
                $db_config->emails = array('user_id' => 0,
                                           'name' => '');
                
                /*
                 * Emails translation.
                 */
                $db_config->emails_translation = array('email_id' => 0,
                                                       'template' => '',
                                                       'subject' => '',
                                                       'message' => '');
                
                /*
                 * Extras
                 */
                $db_config->extras = array('user_id' => 0,
                                           'name' => '');
                
                /*
                 * Extras groups.
                 */
                $db_config->extras_groups = array('extra_id' => 0,
                                                  'position' => 0,
                                                  'multiple_select' => 'false',
                                                  'required' => 'false',
                                                  'no_items_multiply' => 'true',
                                                  'translation' => '');
                
                /*
                 * Extras groups items.
                 */
                $db_config->extras_groups_items = array('group_id' => 0,
                                                        'position' => 0,
                                                        'operation' => '+',
                                                        'price' => 0,
                                                        'price_type' => 'fixed',
                                                        'price_by' => 'once',
                                                        'default' => 'no',
                                                        'translation' => '');
                
                /*
                 * Fees
                 */
                $db_config->fees = array('user_id' => 0,
                                         'name' => '',
                                         'operation' => '+',
                                         'price' => 0,
                                         'price_type' => 'fixed',
                                         'price_by' => 'once',
                                         'included' => 'true',
                                         'extras' => 'true',
                                         'cart' => 'true',
                                         'translation' => '');
                
                /*
                 * Forms
                 */
                $db_config->forms = array('user_id' => 0,
                                          'name' => '',
                                          'label' => '');
                
                /*
                 * Forms fields.
                 */
                $db_config->forms_fields = array('form_id' => 0,
                                                 'label' => '',
                                                 'type' => '',
                                                 'position' => 0,
                                                 'multiple_select' => 'false',
                                                 'allowed_characters' => '',
                                                 'size' => 0,
                                                 'is_email' => 'false',
                                                 'is_phone' => 'false',
                                                 'required' => 'false',
                                                 'add_to_day_hour_info' => 'false',
                                                 'add_to_day_hour_body' => 'false',
                                                 'info' => '');
                
                /*
                 * Forms select options.
                 */
                $db_config->forms_fields_options = array('field_id' => 0,
                                                         'label' => '',
                                                         'position' => 0);
                
                /*
                 * Languages
                 */
                $db_config->languages = array('name' => '',
                                              'code' => '',
                                              'enabled' => 'false');
                
                /*
                 * Locations
                 */
                $db_config->locations = array('user_id' => 0,
                                              'name' => '',
                                              'address' => '',
                                              'address_en' => '',
                                              'address_alt' => '',
                                              'address_alt_en' => '',
                                              'coordinates' => '',
                                              'calendars' => '',
                                              'link' => '',
					      'image' => '',
					      'businesses' => '',
					      'businesses_other' => '',
					      'languages' => '',
					      'email' => '');
                
                /*
                 * Models
                 */
                $db_config->models = array('user_id' => 0,
                                           'name' => '',
					   'enabled' => 'true',
					   'multiple_calendars' => 'false',
					   'translation' => '',
					   'translation_calendar' => '');
                
                /*
                 * Reservations
                 */
                $db_config->reservations = array('calendar_id' => 0,
                                                 'language' => 'en',
                                                 'currency' => '$',
                                                 'currency_code' => 'USD',

                                                 'check_in' => '',
                                                 'check_out' => '',
                                                 'start_hour' => '',
                                                 'end_hour' => '',
                                                 'no_items' => 1,
                                                 'price' => 0,
                                                 'price_total' => 0,
                                                 'refund' => 0,

                                                 'extras' => '',
                                                 'extras_price' => 0,
                                                 'discount' => '',
                                                 'discount_price' => 0,
                                                 'coupon' => '',
                                                 'coupon_price' => 0,
                                                 'fees' => '',
                                                 'fees_price' => 0,
                                                 'deposit' => '',
                                                 'deposit_price' => 0,

                                                 'days_hours_history' => '',
                                                 'form' => '',
                                                 'address_billing' => '',
                                                 'address_shipping' => '',
                                            
                                                 'email' => '',
                                                 'phone' => '',
                                                 'status' => 'pending',
                                                 'payment_method' => 'default',
                                                 'payment_status' => 'pending',
                                                 'transaction_id' => '',
                                                 'token' => '',
                                                 'ip' => '',
                                                 'date_created' => '');
                
                /*
                 * Rules
                 */
                $db_config->rules = array('user_id' => 0,
                                          'name' => '',
                                          'time_lapse_min' => 0,
                                          'time_lapse_max' => 0);
                
                /*
                 * Search
                 */
                $db_config->searches = array('user_id' => 0,
                                             'name' => '',
                                             'calendars_excluded' => '',
                                             'currency' => 'USD',
                                             'currency_position' => 'before',
                                             'hours_enabled' => 'false');
                
                /*
                 * Settings
                 */
                $db_config->settings = array('unique_key' => '',
                                             'value' => '');
                
                /*
                 * Settings calendar.
                 */
                $db_config->settings_calendar = array('calendar_id' => 0,
                                                      'unique_key' => '',
                                                      'value' => '');
                
                /*
                 * Settings notifications.
                 */
                $db_config->settings_notifications = array('calendar_id' => 0,
                                                           'unique_key' => '',
                                                           'value' => '');
                
                /*
                 * Settings payment.
                 */
                $db_config->settings_payment = array('calendar_id' => 0,
                                                     'unique_key' => '',
                                                     'value' => '');
                
                /*
                 * Settings search.
                 */
                $db_config->settings_search = array('search_id' => 0,
                                                    'unique_key' => '',
                                                    'value' => '');
                
                /*
                 * Translation
                 */
                $db_config->translation = array('key_data' => '',
                                                'location' => 'backend',
                                                'parent_key' => '',
                                                'text_data' => '',
                                                'translation' => '');
                
                return $db_config;
            }
        }
    }
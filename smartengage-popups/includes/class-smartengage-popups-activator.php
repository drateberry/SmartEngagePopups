<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Activator {

    /**
     * Plugin activation tasks.
     *
     * Creates necessary database tables, sets up default options,
     * and flushes rewrite rules after registering the custom post type.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Register the post type
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-smartengage-popups-admin.php';
        $plugin_admin = new SmartEngage_Popups_Admin('smartengage-popups', '1.0.0');
        $plugin_admin->register_popup_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create analytics tables
        self::create_analytics_tables();
        
        // Set default options
        self::set_default_options();
    }
    
    /**
     * Create database tables for analytics.
     *
     * @since    1.0.0
     */
    private static function create_analytics_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Views table
        $table_name = $wpdb->prefix . 'smartengage_popup_views';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(100) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            device_type varchar(20) DEFAULT NULL,
            referrer text DEFAULT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY popup_id (popup_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        // Clicks table
        $table_name_clicks = $wpdb->prefix . 'smartengage_popup_clicks';
        $sql .= "CREATE TABLE $table_name_clicks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(100) DEFAULT NULL,
            element_id varchar(100) DEFAULT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY popup_id (popup_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        // Include upgrade file
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Set default plugin options.
     *
     * @since    1.0.0
     */
    private static function set_default_options() {
        $options = array(
            'popup_z_index' => 999999,
            'disable_analytics' => false,
            'anonymize_ip' => true,
            'global_frequency_limit' => 'session' // 'session', 'time', 'none'
        );
        
        // Only add options if they don't exist
        foreach ($options as $key => $value) {
            if (get_option('smartengage_' . $key) === false) {
                add_option('smartengage_' . $key, $value);
            }
        }
    }
}
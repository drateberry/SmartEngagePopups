<?php
/**
 * Plugin Name: SmartEngage Popups
 * Plugin URI: https://github.com/drateberry/smartengage-popups
 * Description: Create and manage behavior-triggered slide-in and full-screen popups with powerful targeting options.
 * Version: 1.0.0
 * Author: Drate Berry
 * Author URI: https://drateberry.com
 * Text Domain: smartengage-popups
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define plugin constants
 */
define( 'SMARTENGAGE_VERSION', '1.0.0' );
define( 'SMARTENGAGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMARTENGAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMARTENGAGE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class
 */
class SmartEngage_Popups {

    /**
     * Instance of this class
     *
     * @var object
     */
    private static $instance;

    /**
     * Get singleton instance
     *
     * @return SmartEngage_Popups
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Load plugin files
        $this->includes();
        
        // Register activation/deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        
        // Initialize plugin components
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        
        // Load text domain
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
    }

    /**
     * Include required files
     */
    private function includes() {
        // Core functionality
        require_once SMARTENGAGE_PLUGIN_DIR . 'includes/class-smartengage-loader.php';
        require_once SMARTENGAGE_PLUGIN_DIR . 'includes/class-smartengage-post-types.php';
        require_once SMARTENGAGE_PLUGIN_DIR . 'includes/class-smartengage-analytics.php';
        require_once SMARTENGAGE_PLUGIN_DIR . 'includes/class-smartengage-frontend.php';
        
        // Admin only files
        if ( is_admin() ) {
            require_once SMARTENGAGE_PLUGIN_DIR . 'admin/class-smartengage-admin.php';
            require_once SMARTENGAGE_PLUGIN_DIR . 'admin/class-smartengage-metabox.php';
        }
    }

    /**
     * Initialize plugin components
     */
    public function init() {
        // Initialize post types
        $post_types = new SmartEngage_Post_Types();
        $post_types->init();
        
        // Initialize admin if in admin area
        if ( is_admin() ) {
            $admin = new SmartEngage_Admin();
            $admin->init();
        } else {
            // Initialize frontend
            $frontend = new SmartEngage_Frontend();
            $frontend->init();
        }
        
        // Initialize analytics
        $analytics = new SmartEngage_Analytics();
        $analytics->init();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create custom database tables if needed
        $this->create_tables();
        
        // Register post type on activation to flush rewrite rules
        $post_types = new SmartEngage_Post_Types();
        $post_types->register_post_types();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create custom database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . 'smartengage_analytics';
        
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            event_type varchar(20) NOT NULL,
            event_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            user_ip varchar(100) NOT NULL,
            user_agent text NOT NULL,
            referer_url varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            KEY popup_id (popup_id),
            KEY event_type (event_type),
            KEY event_date (event_date)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'smartengage-popups', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}

// Initialize the plugin
function smartengage_popups_init() {
    return SmartEngage_Popups::get_instance();
}

// Start the plugin
smartengage_popups_init();

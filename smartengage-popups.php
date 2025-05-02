<?php
/**
 * Plugin Name: SmartEngage Popups
 * Plugin URI: https://example.com/smartengage-popups
 * Description: Create intelligent, behavior-based popups for your WordPress site.
 * Version: 1.0.0
 * Author: SmartEngage
 * Author URI: https://example.com
 * Text Domain: smartengage-popups
 * Domain Path: /languages
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'SMARTENGAGE_POPUPS_VERSION', '1.0.0' );
define( 'SMARTENGAGE_POPUPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMARTENGAGE_POPUPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_smartengage_popups() {
    require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-post-type.php';
    $post_type = new SmartEngage_Popups_Post_Type();
    $post_type->register_post_type();
    flush_rewrite_rules();

    // Create analytics table
    require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-analytics.php';
    $analytics = new SmartEngage_Popups_Analytics();
    $analytics->create_tables();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smartengage_popups() {
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_smartengage_popups' );
register_deactivation_hook( __FILE__, 'deactivate_smartengage_popups' );

/**
 * The core plugin class.
 */
require SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups.php';

/**
 * Begins execution of the plugin.
 */
function run_smartengage_popups() {
    $plugin = new SmartEngage_Popups();
    $plugin->run();
}
run_smartengage_popups();

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

include_once('smartengage-popups/smartengage-popups.php');
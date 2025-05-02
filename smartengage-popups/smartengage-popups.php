<?php
/**
 * SmartEngage Popups
 *
 * @package     SmartEngage_Popups
 * @author      SmartEngage Team
 * @copyright   2025 SmartEngage
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: SmartEngage Popups
 * Plugin URI:  https://www.smartengage.com/popups
 * Description: A full-featured popup builder plugin with drag-and-drop interface, advanced targeting, and analytics.
 * Version:     1.0.0
 * Author:      SmartEngage Team
 * Author URI:  https://www.smartengage.com
 * Text Domain: smartengage-popups
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('SMARTENGAGE_POPUPS_VERSION', '1.0.0');

/**
 * Plugin directory path.
 */
define('SMARTENGAGE_POPUPS_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin directory URL.
 */
define('SMARTENGAGE_POPUPS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_smartengage_popups() {
    require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-activator.php';
    SmartEngage_Popups_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_smartengage_popups() {
    require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-deactivator.php';
    SmartEngage_Popups_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_smartengage_popups');
register_deactivation_hook(__FILE__, 'deactivate_smartengage_popups');

/**
 * The core plugin class.
 */
require SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_smartengage_popups() {
    $plugin = new SmartEngage_Popups();
    $plugin->run();
}

run_smartengage_popups();
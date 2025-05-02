<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Deactivator {

    /**
     * Plugin deactivation tasks.
     *
     * Flushes rewrite rules and performs any other cleanup needed.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Note: We don't remove analytics tables or options to preserve data
        // if the plugin is reactivated.
    }
}
<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 */
class SmartEngage_Admin {

    /**
     * Initialize the class
     */
    public function init() {
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Add settings link to plugins page
        add_filter( 'plugin_action_links_' . SMARTENGAGE_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
        
        // Enqueue admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        
        // Load metaboxes
        $metaboxes = new SmartEngage_Metabox();
        $metaboxes->init();
    }

    /**
     * Add plugin admin menu items
     */
    public function add_admin_menu() {
        // Add analytics dashboard page
        add_submenu_page(
            'edit.php?post_type=smartengage_popup',
            __( 'Analytics Dashboard', 'smartengage-popups' ),
            __( 'Analytics', 'smartengage-popups' ),
            'manage_options',
            'smartengage-analytics',
            array( $this, 'render_analytics_page' )
        );
        
        // Add settings page
        add_submenu_page(
            'edit.php?post_type=smartengage_popup',
            __( 'Settings', 'smartengage-popups' ),
            __( 'Settings', 'smartengage-popups' ),
            'manage_options',
            'smartengage-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Add settings link to plugins page
     *
     * @param array $links Plugin action links.
     * @return array Modified plugin action links.
     */
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'edit.php?post_type=smartengage_popup&page=smartengage-settings' ) . '">' . __( 'Settings', 'smartengage-popups' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on SmartEngage admin pages
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook && 'smartengage_popup_page_smartengage-analytics' !== $hook && 'smartengage_popup_page_smartengage-settings' !== $hook ) {
            return;
        }
        
        // Get current screen
        $screen = get_current_screen();
        
        // Only load on our post type
        if ( 'smartengage_popup' !== $screen->post_type && 'smartengage_popup_page_smartengage-analytics' !== $hook && 'smartengage_popup_page_smartengage-settings' !== $hook ) {
            return;
        }
        
        // Register and enqueue admin styles
        wp_register_style(
            'smartengage-admin',
            SMARTENGAGE_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SMARTENGAGE_VERSION
        );
        wp_enqueue_style( 'smartengage-admin' );
        
        // Register and enqueue admin scripts
        wp_register_script(
            'smartengage-admin',
            SMARTENGAGE_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-util' ),
            SMARTENGAGE_VERSION,
            true
        );
        
        // Only load chart.js on analytics page
        if ( 'smartengage_popup_page_smartengage-analytics' === $hook ) {
            wp_register_script(
                'smartengage-charts',
                SMARTENGAGE_PLUGIN_URL . 'assets/js/chart.min.js',
                array(),
                SMARTENGAGE_VERSION,
                true
            );
            wp_enqueue_script( 'smartengage-charts' );
        }
        
        // Localize script with data needed for JS
        wp_localize_script(
            'smartengage-admin',
            'smartEngageAdmin',
            array(
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'smartengage_admin_nonce' ),
                'strings'   => array(
                    'confirmDelete' => __( 'Are you sure you want to delete this? This action cannot be undone.', 'smartengage-popups' ),
                    'success'       => __( 'Success!', 'smartengage-popups' ),
                    'error'         => __( 'Error. Please try again.', 'smartengage-popups' ),
                ),
            )
        );
        
        wp_enqueue_script( 'smartengage-admin' );
    }

    /**
     * Render the analytics dashboard page
     */
    public function render_analytics_page() {
        include_once SMARTENGAGE_PLUGIN_DIR . 'admin/views/analytics-dashboard.php';
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        include_once SMARTENGAGE_PLUGIN_DIR . 'admin/views/settings-page.php';
    }
}

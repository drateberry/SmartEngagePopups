<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for
 * public-facing site functionality
 *
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, SMARTENGAGE_POPUPS_PLUGIN_URL . 'assets/css/smartengage-popups-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, SMARTENGAGE_POPUPS_PLUGIN_URL . 'assets/js/smartengage-popups-frontend.js', array('jquery'), $this->version, true);
        
        wp_localize_script($this->plugin_name, 'smartengagePopups', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'pageViews' => $this->increment_page_view_count(),
            'globalFrequency' => get_option('smartengage_global_frequency_limit', 'session')
        ));
    }
    
    /**
     * Increment the page view counter and return the current count.
     *
     * @since    1.0.0
     * @return   int    The current page view count.
     */
    private function increment_page_view_count() {
        $cookie_name = 'smartengage_page_views';
        
        // Get current view count from cookie
        $current_views = isset($_COOKIE[$cookie_name]) ? intval($_COOKIE[$cookie_name]) : 0;
        
        // Increment the counter
        $current_views++;
        
        // Set cookie (expires in 30 days)
        setcookie($cookie_name, $current_views, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        
        return $current_views;
    }
}
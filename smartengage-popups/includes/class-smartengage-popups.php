<?php
/**
 * The file that defines the core plugin class
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      SmartEngage_Popups_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('SMARTENGAGE_POPUPS_VERSION')) {
            $this->version = SMARTENGAGE_POPUPS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'smartengage-popups';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-i18n.php';

        /**
         * The class responsible for defining metabox functionality
         * of the plugin.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-metabox.php';

        /**
         * The class responsible for displaying popups on the frontend
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-display.php';

        /**
         * The class responsible for handling popup analytics
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-analytics.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/class-smartengage-popups-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'public/class-smartengage-popups-public.php';

        $this->loader = new SmartEngage_Popups_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new SmartEngage_Popups_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new SmartEngage_Popups_Admin($this->get_plugin_name(), $this->get_version());
        $plugin_metabox = new SmartEngage_Popups_Metabox($this->get_plugin_name(), $this->get_version());
        $plugin_analytics = new SmartEngage_Popups_Analytics($this->get_plugin_name(), $this->get_version());

        // Admin assets
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        // Register custom post type
        $this->loader->add_action('init', $plugin_admin, 'register_popup_post_type');

        // Register metaboxes
        $this->loader->add_action('add_meta_boxes', $plugin_metabox, 'register_metaboxes');
        $this->loader->add_action('save_post', $plugin_metabox, 'save_metabox', 10, 2);

        // Register admin pages
        $this->loader->add_action('admin_menu', $plugin_admin, 'register_admin_menu');
        
        // Ajax handlers
        $this->loader->add_action('wp_ajax_smartengage_save_popup_design', $plugin_admin, 'ajax_save_popup_design');
        $this->loader->add_action('wp_ajax_smartengage_get_analytics', $plugin_analytics, 'ajax_get_analytics');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new SmartEngage_Popups_Public($this->get_plugin_name(), $this->get_version());
        $plugin_display = new SmartEngage_Popups_Display($this->get_plugin_name(), $this->get_version());
        $plugin_analytics = new SmartEngage_Popups_Analytics($this->get_plugin_name(), $this->get_version());

        // Public assets
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Display popups
        $this->loader->add_action('wp_footer', $plugin_display, 'display_popups');

        // Ajax handlers for analytics
        $this->loader->add_action('wp_ajax_smartengage_record_view', $plugin_analytics, 'ajax_record_view');
        $this->loader->add_action('wp_ajax_nopriv_smartengage_record_view', $plugin_analytics, 'ajax_record_view');
        
        $this->loader->add_action('wp_ajax_smartengage_record_click', $plugin_analytics, 'ajax_record_click');
        $this->loader->add_action('wp_ajax_nopriv_smartengage_record_click', $plugin_analytics, 'ajax_record_click');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    SmartEngage_Popups_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
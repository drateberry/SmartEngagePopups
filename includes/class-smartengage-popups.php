<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The core plugin class.
 */
class SmartEngage_Popups {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions = array();

    /**
     * The filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters = array();

    /**
     * Initialize the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
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
         * The class responsible for defining post type functionality
         * of the plugin.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-post-type.php';

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
         * The class responsible for tracking analytics
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'includes/class-smartengage-popups-analytics.php';

        /**
         * The class responsible for defining all actions in the admin area.
         */
        require_once SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/class-smartengage-popups-admin.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $post_type = new SmartEngage_Popups_Post_Type();
        $metabox = new SmartEngage_Popups_Metabox();
        $admin = new SmartEngage_Popups_Admin();
        $analytics = new SmartEngage_Popups_Analytics();

        // Post type
        $this->add_action( 'init', $post_type, 'register_post_type' );

        // Metabox
        $this->add_action( 'add_meta_boxes', $metabox, 'add_meta_boxes' );
        $this->add_action( 'save_post', $metabox, 'save_metabox', 10, 2 );

        // Admin
        $this->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
        $this->add_action( 'admin_menu', $admin, 'add_analytics_page' );

        // Analytics
        $this->add_action( 'wp_ajax_smartengage_record_view', $analytics, 'record_view' );
        $this->add_action( 'wp_ajax_nopriv_smartengage_record_view', $analytics, 'record_view' );
        $this->add_action( 'wp_ajax_smartengage_record_click', $analytics, 'record_click' );
        $this->add_action( 'wp_ajax_nopriv_smartengage_record_click', $analytics, 'record_click' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $display = new SmartEngage_Popups_Display();

        $this->add_action( 'wp_enqueue_scripts', $display, 'enqueue_styles' );
        $this->add_action( 'wp_enqueue_scripts', $display, 'enqueue_scripts' );
        $this->add_action( 'wp_footer', $display, 'display_popups' );
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add_hook( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add_hook( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     */
    private function add_hook( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        );

        return $hooks;
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Register all actions
        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        // Register all filters
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }
    }
}

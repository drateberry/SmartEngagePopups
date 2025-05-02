<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks for
 * enqueuing the admin-specific stylesheet and JavaScript.
 *
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Admin {

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
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Only load on our plugin pages
        $screen = get_current_screen();
        if (!in_array($screen->id, array('smartengage_popup', 'edit-smartengage_popup', 'smartengage_page_smartengage-analytics'))) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
        wp_enqueue_style($this->plugin_name, SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/css/smartengage-popups-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-builder', SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/css/smartengage-popups-builder-modern.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Only load on our plugin pages
        $screen = get_current_screen();
        if (!in_array($screen->id, array('smartengage_popup', 'edit-smartengage_popup', 'smartengage_page_smartengage-analytics'))) {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        
        wp_enqueue_script($this->plugin_name, SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/js/smartengage-popups-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . '-builder', SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/js/smartengage-popups-builder-modern.js', array('jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable'), $this->version, false);
        
        // Analytics page
        if ($screen->id === 'smartengage_page_smartengage-analytics') {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js', array(), '3.7.1', false);
            wp_enqueue_script($this->plugin_name . '-analytics', SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/js/smartengage-popups-analytics.js', array('jquery', 'chart-js'), $this->version, false);
            
            wp_localize_script($this->plugin_name . '-analytics', 'smartengageAnalytics', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('smartengage_analytics_nonce')
            ));
        }
    }

    /**
     * Register custom post type for popups.
     *
     * @since    1.0.0
     */
    public function register_popup_post_type() {
        $labels = array(
            'name'                  => _x('Popups', 'Post Type General Name', 'smartengage-popups'),
            'singular_name'         => _x('Popup', 'Post Type Singular Name', 'smartengage-popups'),
            'menu_name'             => __('SmartEngage', 'smartengage-popups'),
            'name_admin_bar'        => __('Popup', 'smartengage-popups'),
            'archives'              => __('Popup Archives', 'smartengage-popups'),
            'attributes'            => __('Popup Attributes', 'smartengage-popups'),
            'all_items'             => __('All Popups', 'smartengage-popups'),
            'add_new_item'          => __('Add New Popup', 'smartengage-popups'),
            'add_new'               => __('Add New', 'smartengage-popups'),
            'new_item'              => __('New Popup', 'smartengage-popups'),
            'edit_item'             => __('Edit Popup', 'smartengage-popups'),
            'update_item'           => __('Update Popup', 'smartengage-popups'),
            'view_item'             => __('View Popup', 'smartengage-popups'),
            'view_items'            => __('View Popups', 'smartengage-popups'),
            'search_items'          => __('Search Popup', 'smartengage-popups'),
            'not_found'             => __('Not found', 'smartengage-popups'),
            'not_found_in_trash'    => __('Not found in Trash', 'smartengage-popups'),
            'featured_image'        => __('Featured Image', 'smartengage-popups'),
            'set_featured_image'    => __('Set featured image', 'smartengage-popups'),
            'remove_featured_image' => __('Remove featured image', 'smartengage-popups'),
            'use_featured_image'    => __('Use as featured image', 'smartengage-popups'),
            'insert_into_item'      => __('Insert into popup', 'smartengage-popups'),
            'uploaded_to_this_item' => __('Uploaded to this popup', 'smartengage-popups'),
            'items_list'            => __('Popups list', 'smartengage-popups'),
            'items_list_navigation' => __('Popups list navigation', 'smartengage-popups'),
            'filter_items_list'     => __('Filter popups list', 'smartengage-popups'),
        );
        
        $args = array(
            'label'                 => __('Popup', 'smartengage-popups'),
            'description'           => __('Smart popup for marketing and user engagement', 'smartengage-popups'),
            'labels'                => $labels,
            'supports'              => array('title'),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-feedback',
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'page',
            'show_in_rest'          => false,
        );
        
        register_post_type('smartengage_popup', $args);
    }
    
    /**
     * Register admin menu pages.
     *
     * @since    1.0.0
     */
    public function register_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=smartengage_popup',
            __('Analytics', 'smartengage-popups'),
            __('Analytics', 'smartengage-popups'),
            'manage_options',
            'smartengage-analytics',
            array($this, 'render_analytics_page')
        );
        
        add_submenu_page(
            'edit.php?post_type=smartengage_popup',
            __('Settings', 'smartengage-popups'),
            __('Settings', 'smartengage-popups'),
            'manage_options',
            'smartengage-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Render the analytics page.
     *
     * @since    1.0.0
     */
    public function render_analytics_page() {
        // Get all published popups
        $popups = get_posts(array(
            'post_type' => 'smartengage_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        ?>
        <div class="wrap smartengage-analytics-wrap">
            <h1><?php _e('SmartEngage Popups Analytics', 'smartengage-popups'); ?></h1>
            
            <div class="smartengage-analytics-header">
                <div class="smartengage-analytics-filters">
                    <select id="smartengage-popup-select">
                        <option value=""><?php _e('Select a popup to view analytics', 'smartengage-popups'); ?></option>
                        <?php foreach ($popups as $popup) : ?>
                            <option value="<?php echo esc_attr($popup->ID); ?>"><?php echo esc_html($popup->post_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select id="smartengage-date-range">
                        <option value="7days"><?php _e('Last 7 Days', 'smartengage-popups'); ?></option>
                        <option value="30days" selected><?php _e('Last 30 Days', 'smartengage-popups'); ?></option>
                        <option value="year"><?php _e('Last Year', 'smartengage-popups'); ?></option>
                        <option value="all"><?php _e('All Time', 'smartengage-popups'); ?></option>
                    </select>
                </div>
            </div>
            
            <div id="smartengage-analytics-placeholder" class="smartengage-analytics-placeholder">
                <p><?php _e('Select a popup from the dropdown above to view analytics data.', 'smartengage-popups'); ?></p>
            </div>
            
            <div id="smartengage-analytics-dashboard" class="smartengage-analytics-dashboard" style="display: none;">
                <div class="smartengage-stats-overview">
                    <div class="smartengage-stat-card views-card">
                        <div class="smartengage-stat-icon dashicons dashicons-visibility"></div>
                        <div class="smartengage-stat-content">
                            <h3><?php _e('Total Views', 'smartengage-popups'); ?></h3>
                            <div class="smartengage-stat-value" id="stat-total-views">0</div>
                        </div>
                    </div>
                    
                    <div class="smartengage-stat-card clicks-card">
                        <div class="smartengage-stat-icon dashicons dashicons-yes-alt"></div>
                        <div class="smartengage-stat-content">
                            <h3><?php _e('Total Clicks', 'smartengage-popups'); ?></h3>
                            <div class="smartengage-stat-value" id="stat-total-clicks">0</div>
                        </div>
                    </div>
                    
                    <div class="smartengage-stat-card rate-card">
                        <div class="smartengage-stat-icon dashicons dashicons-chart-bar"></div>
                        <div class="smartengage-stat-content">
                            <h3><?php _e('Conversion Rate', 'smartengage-popups'); ?></h3>
                            <div class="smartengage-stat-value" id="stat-conversion-rate">0.00%</div>
                        </div>
                    </div>
                </div>
                
                <div class="smartengage-charts-section">
                    <div class="smartengage-chart-wrap">
                        <h2><?php _e('Performance Over Time', 'smartengage-popups'); ?></h2>
                        <div class="smartengage-chart-container">
                            <canvas id="smartengage-performance-chart"></canvas>
                        </div>
                    </div>
                    
                    <div class="smartengage-chart-wrap">
                        <h2><?php _e('Device Breakdown', 'smartengage-popups'); ?></h2>
                        <div class="smartengage-chart-container">
                            <canvas id="smartengage-devices-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
        .smartengage-analytics-wrap {
            margin: 20px 20px 0 0;
        }
        
        .smartengage-analytics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .smartengage-analytics-filters {
            display: flex;
            gap: 15px;
        }
        
        .smartengage-analytics-filters select {
            min-width: 200px;
        }
        
        .smartengage-analytics-placeholder {
            background: #fff;
            padding: 50px;
            text-align: center;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .smartengage-analytics-placeholder p {
            font-size: 16px;
            color: #666;
        }
        
        .smartengage-stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .smartengage-stat-card {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        
        .smartengage-stat-icon {
            font-size: 32px;
            margin-right: 15px;
            padding: 10px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .views-card .smartengage-stat-icon {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        
        .clicks-card .smartengage-stat-icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .rate-card .smartengage-stat-icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .smartengage-stat-content h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #666;
        }
        
        .smartengage-stat-value {
            font-size: 24px;
            font-weight: 600;
        }
        
        .smartengage-charts-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }
        
        @media (min-width: 992px) {
            .smartengage-charts-section {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .smartengage-chart-wrap {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .smartengage-chart-wrap h2 {
            margin-top: 0;
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .smartengage-chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Loading indicator */
        .smartengage-analytics-loading {
            text-align: center;
            padding: 50px;
        }
        
        .smartengage-analytics-loading .spinner {
            float: none;
            visibility: visible;
            margin-bottom: 10px;
        }
        </style>
        <?php
    }
    
    /**
     * Render the settings page.
     *
     * @since    1.0.0
     */
    public function render_settings_page() {
        if (isset($_POST['smartengage_save_settings']) && current_user_can('manage_options')) {
            check_admin_referer('smartengage_settings_nonce', 'smartengage_settings_nonce');
            
            $popup_z_index = isset($_POST['popup_z_index']) ? intval($_POST['popup_z_index']) : 999999;
            $disable_analytics = isset($_POST['disable_analytics']) ? true : false;
            $anonymize_ip = isset($_POST['anonymize_ip']) ? true : false;
            $global_frequency_limit = isset($_POST['global_frequency_limit']) ? sanitize_text_field($_POST['global_frequency_limit']) : 'session';
            
            update_option('smartengage_popup_z_index', $popup_z_index);
            update_option('smartengage_disable_analytics', $disable_analytics);
            update_option('smartengage_anonymize_ip', $anonymize_ip);
            update_option('smartengage_global_frequency_limit', $global_frequency_limit);
            
            add_settings_error('smartengage_settings', 'settings_updated', __('Settings saved successfully.', 'smartengage-popups'), 'updated');
        }
        
        // Get current settings
        $popup_z_index = get_option('smartengage_popup_z_index', 999999);
        $disable_analytics = get_option('smartengage_disable_analytics', false);
        $anonymize_ip = get_option('smartengage_anonymize_ip', true);
        $global_frequency_limit = get_option('smartengage_global_frequency_limit', 'session');
        
        ?>
        <div class="wrap smartengage-settings-wrap">
            <h1><?php _e('SmartEngage Popups Settings', 'smartengage-popups'); ?></h1>
            
            <?php settings_errors('smartengage_settings'); ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('smartengage_settings_nonce', 'smartengage_settings_nonce'); ?>
                
                <div class="smartengage-settings-section">
                    <h2><?php _e('General Settings', 'smartengage-popups'); ?></h2>
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Popup Z-Index', 'smartengage-popups'); ?></th>
                            <td>
                                <input type="number" name="popup_z_index" value="<?php echo esc_attr($popup_z_index); ?>" min="1" max="2147483647" />
                                <p class="description"><?php _e('Set the z-index value for popups. Default is 999999. Increase this if popups appear behind other elements on your site.', 'smartengage-popups'); ?></p>
                                <p class="description" style="color:#d63638;"><?php _e('If your popups are not displaying, try increasing this value to 9999999 or higher.', 'smartengage-popups'); ?></p>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><?php _e('Global Frequency Limit', 'smartengage-popups'); ?></th>
                            <td>
                                <select name="global_frequency_limit">
                                    <option value="none" <?php selected($global_frequency_limit, 'none'); ?>><?php _e('No global limit', 'smartengage-popups'); ?></option>
                                    <option value="session" <?php selected($global_frequency_limit, 'session'); ?>><?php _e('One popup per session', 'smartengage-popups'); ?></option>
                                    <option value="time" <?php selected($global_frequency_limit, 'time'); ?>><?php _e('One popup every 24 hours', 'smartengage-popups'); ?></option>
                                </select>
                                <p class="description"><?php _e('Limit how many popups can be shown to a visitor. This is a global setting that applies to all popups.', 'smartengage-popups'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="smartengage-settings-section">
                    <h2><?php _e('Analytics Settings', 'smartengage-popups'); ?></h2>
                    
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Disable Analytics', 'smartengage-popups'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="disable_analytics" <?php checked($disable_analytics, true); ?> />
                                    <?php _e('Disable popup analytics and data collection', 'smartengage-popups'); ?>
                                </label>
                                <p class="description"><?php _e('Check this if you do not want to collect analytics data for popups.', 'smartengage-popups'); ?></p>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><?php _e('Anonymize IP Addresses', 'smartengage-popups'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="anonymize_ip" <?php checked($anonymize_ip, true); ?> />
                                    <?php _e('Anonymize visitor IP addresses', 'smartengage-popups'); ?>
                                </label>
                                <p class="description"><?php _e('If enabled, visitor IP addresses will be anonymized for enhanced privacy.', 'smartengage-popups'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p class="submit">
                    <input type="submit" name="smartengage_save_settings" class="button-primary" value="<?php _e('Save Settings', 'smartengage-popups'); ?>" />
                </p>
            </form>
        </div>
        <style>
        .smartengage-settings-wrap {
            margin: 20px 20px 0 0;
        }
        
        .smartengage-settings-section {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .smartengage-settings-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        </style>
        <?php
    }
    
    /**
     * Ajax handler for saving popup design.
     *
     * @since    1.0.0
     */
    public function ajax_save_popup_design() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'smartengage_builder_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
            return;
        }
        
        // Check required data
        if (!isset($_POST['popup_id']) || !isset($_POST['design_data'])) {
            wp_send_json_error(array('message' => 'Missing required data'));
            return;
        }
        
        $popup_id = intval($_POST['popup_id']);
        $design_data = $_POST['design_data'];
        
        // Check permissions
        if (!current_user_can('edit_post', $popup_id)) {
            wp_send_json_error(array('message' => 'Permission denied'));
            return;
        }
        
        // Save the data
        update_post_meta($popup_id, '_popup_design_json', wp_slash($design_data));
        
        wp_send_json_success(array('message' => __('Popup design saved successfully', 'smartengage-popups')));
    }
}
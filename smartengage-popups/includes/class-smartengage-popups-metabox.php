<?php
/**
 * The metabox functionality of the plugin.
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The metabox functionality of the plugin.
 *
 * Defines the plugin name, version, and metabox functionality
 *
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Metabox {

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
     * Register metaboxes for the popup post type.
     *
     * @since    1.0.0
     */
    public function register_metaboxes() {
        add_meta_box(
            'smartengage_popup_builder',
            __('Popup Builder', 'smartengage-popups'),
            array($this, 'render_builder_metabox'),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_popup_targeting',
            __('Popup Targeting & Triggers', 'smartengage-popups'),
            array($this, 'render_targeting_metabox'),
            'smartengage_popup',
            'normal',
            'default'
        );
        
        add_meta_box(
            'smartengage_popup_analytics',
            __('Popup Analytics', 'smartengage-popups'),
            array($this, 'render_analytics_metabox'),
            'smartengage_popup',
            'side',
            'default'
        );
    }

    /**
     * Render the builder metabox.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_builder_metabox($post) {
        // Include the builder template
        include SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/partials/popup-builder-modern-template.php';
    }

    /**
     * Render the targeting metabox.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_targeting_metabox($post) {
        // Get saved values
        $trigger_type = get_post_meta($post->ID, '_popup_trigger_type', true) ?: 'time_delay';
        $trigger_value = get_post_meta($post->ID, '_popup_trigger_value', true) ?: '5';
        $frequency_rule = get_post_meta($post->ID, '_popup_frequency', true) ?: 'session';
        $frequency_value = get_post_meta($post->ID, '_popup_frequency_value', true) ?: '1';
        $display_on = get_post_meta($post->ID, '_popup_display_on', true) ?: 'all';
        $specific_pages = get_post_meta($post->ID, '_popup_specific_pages', true) ?: '';
        $device_target = get_post_meta($post->ID, '_popup_device_target', true) ?: 'all';
        $user_roles = get_post_meta($post->ID, '_popup_user_roles', true) ?: array();
        
        // Nonce for verification
        wp_nonce_field('smartengage_popup_targeting_nonce', 'smartengage_targeting_nonce');
        
        // Output the form fields
        ?>
        <div class="smartengage-metabox-section">
            <h4><?php _e('Trigger Settings', 'smartengage-popups'); ?></h4>
            <p><?php _e('Choose when the popup should appear to visitors', 'smartengage-popups'); ?></p>
            
            <div class="smartengage-field-row">
                <label for="popup_trigger_type"><?php _e('Trigger Type:', 'smartengage-popups'); ?></label>
                <select id="popup_trigger_type" name="popup_trigger_type">
                    <option value="time_delay" <?php selected($trigger_type, 'time_delay'); ?>><?php _e('Time Delay (seconds)', 'smartengage-popups'); ?></option>
                    <option value="scroll_depth" <?php selected($trigger_type, 'scroll_depth'); ?>><?php _e('Scroll Depth (%)', 'smartengage-popups'); ?></option>
                    <option value="exit_intent" <?php selected($trigger_type, 'exit_intent'); ?>><?php _e('Exit Intent', 'smartengage-popups'); ?></option>
                    <option value="click" <?php selected($trigger_type, 'click'); ?>><?php _e('Click on Element', 'smartengage-popups'); ?></option>
                    <option value="page_views" <?php selected($trigger_type, 'page_views'); ?>><?php _e('After X Page Views', 'smartengage-popups'); ?></option>
                </select>
            </div>
            
            <div class="smartengage-field-row" id="trigger_value_wrapper">
                <label for="popup_trigger_value"><?php _e('Trigger Value:', 'smartengage-popups'); ?></label>
                <input type="text" id="popup_trigger_value" name="popup_trigger_value" value="<?php echo esc_attr($trigger_value); ?>" />
                <p class="description"><?php _e('For time delay: seconds. For scroll depth: percentage. For page views: number of views. For click: CSS selector of the element.', 'smartengage-popups'); ?></p>
            </div>
        </div>
        
        <div class="smartengage-metabox-section">
            <h4><?php _e('Frequency Settings', 'smartengage-popups'); ?></h4>
            <p><?php _e('Control how often a visitor sees this popup', 'smartengage-popups'); ?></p>
            
            <div class="smartengage-field-row">
                <label for="popup_frequency"><?php _e('Frequency Rule:', 'smartengage-popups'); ?></label>
                <select id="popup_frequency" name="popup_frequency">
                    <option value="always" <?php selected($frequency_rule, 'always'); ?>><?php _e('Show every time', 'smartengage-popups'); ?></option>
                    <option value="session" <?php selected($frequency_rule, 'session'); ?>><?php _e('Once per session', 'smartengage-popups'); ?></option>
                    <option value="days" <?php selected($frequency_rule, 'days'); ?>><?php _e('Once every X days', 'smartengage-popups'); ?></option>
                    <option value="views" <?php selected($frequency_rule, 'views'); ?>><?php _e('Once every X page views', 'smartengage-popups'); ?></option>
                </select>
            </div>
            
            <div class="smartengage-field-row" id="frequency_value_wrapper" <?php echo ($frequency_rule == 'always' || $frequency_rule == 'session') ? 'style="display:none;"' : ''; ?>>
                <label for="popup_frequency_value"><?php _e('Frequency Value:', 'smartengage-popups'); ?></label>
                <input type="number" id="popup_frequency_value" name="popup_frequency_value" value="<?php echo esc_attr($frequency_value); ?>" min="1" />
                <p class="description"><?php _e('Number of days or page views between popup appearances.', 'smartengage-popups'); ?></p>
            </div>
        </div>
        
        <div class="smartengage-metabox-section">
            <h4><?php _e('Page Targeting', 'smartengage-popups'); ?></h4>
            <p><?php _e('Choose which pages this popup should appear on', 'smartengage-popups'); ?></p>
            
            <div class="smartengage-field-row">
                <label for="popup_display_on"><?php _e('Display On:', 'smartengage-popups'); ?></label>
                <select id="popup_display_on" name="popup_display_on">
                    <option value="all" <?php selected($display_on, 'all'); ?>><?php _e('All Pages', 'smartengage-popups'); ?></option>
                    <option value="homepage" <?php selected($display_on, 'homepage'); ?>><?php _e('Homepage Only', 'smartengage-popups'); ?></option>
                    <option value="posts" <?php selected($display_on, 'posts'); ?>><?php _e('All Posts', 'smartengage-popups'); ?></option>
                    <option value="pages" <?php selected($display_on, 'pages'); ?>><?php _e('All Pages (not posts)', 'smartengage-popups'); ?></option>
                    <option value="specific" <?php selected($display_on, 'specific'); ?>><?php _e('Specific Pages/Posts', 'smartengage-popups'); ?></option>
                </select>
            </div>
            
            <div class="smartengage-field-row" id="specific_pages_wrapper" <?php echo ($display_on != 'specific') ? 'style="display:none;"' : ''; ?>>
                <label for="popup_specific_pages"><?php _e('Specific Pages/Posts:', 'smartengage-popups'); ?></label>
                <textarea id="popup_specific_pages" name="popup_specific_pages" rows="3"><?php echo esc_textarea($specific_pages); ?></textarea>
                <p class="description"><?php _e('Enter URLs, one per line. Use * as a wildcard. Example: /product/* for all product pages.', 'smartengage-popups'); ?></p>
            </div>
        </div>
        
        <div class="smartengage-metabox-section">
            <h4><?php _e('Visitor Targeting', 'smartengage-popups'); ?></h4>
            <p><?php _e('Target specific types of visitors', 'smartengage-popups'); ?></p>
            
            <div class="smartengage-field-row">
                <label for="popup_device_target"><?php _e('Device Type:', 'smartengage-popups'); ?></label>
                <select id="popup_device_target" name="popup_device_target">
                    <option value="all" <?php selected($device_target, 'all'); ?>><?php _e('All Devices', 'smartengage-popups'); ?></option>
                    <option value="desktop" <?php selected($device_target, 'desktop'); ?>><?php _e('Desktop Only', 'smartengage-popups'); ?></option>
                    <option value="mobile" <?php selected($device_target, 'mobile'); ?>><?php _e('Mobile Only', 'smartengage-popups'); ?></option>
                    <option value="tablet" <?php selected($device_target, 'tablet'); ?>><?php _e('Tablet Only', 'smartengage-popups'); ?></option>
                </select>
            </div>
            
            <div class="smartengage-field-row">
                <label><?php _e('User Roles:', 'smartengage-popups'); ?></label>
                <div class="smartengage-checkbox-list">
                    <?php
                    $all_roles = wp_roles()->get_names();
                    $all_roles['logged_out'] = __('Logged Out Users', 'smartengage-popups');
                    
                    foreach ($all_roles as $role_id => $role_name) {
                        $checked = in_array($role_id, (array)$user_roles) || empty($user_roles);
                        ?>
                        <label class="smartengage-checkbox-item">
                            <input type="checkbox" name="popup_user_roles[]" value="<?php echo esc_attr($role_id); ?>" <?php checked($checked, true); ?> />
                            <?php echo esc_html($role_name); ?>
                        </label>
                        <?php
                    }
                    ?>
                </div>
                <p class="description"><?php _e('Leave all unchecked to target all users.', 'smartengage-popups'); ?></p>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Toggle trigger value field visibility based on trigger type
            $('#popup_trigger_type').on('change', function() {
                const triggerType = $(this).val();
                if (triggerType === 'exit_intent') {
                    $('#trigger_value_wrapper').hide();
                } else {
                    $('#trigger_value_wrapper').show();
                }
            }).trigger('change');
            
            // Toggle frequency value field visibility based on frequency rule
            $('#popup_frequency').on('change', function() {
                const frequencyRule = $(this).val();
                if (frequencyRule === 'always' || frequencyRule === 'session') {
                    $('#frequency_value_wrapper').hide();
                } else {
                    $('#frequency_value_wrapper').show();
                }
            }).trigger('change');
            
            // Toggle specific pages field visibility based on display option
            $('#popup_display_on').on('change', function() {
                const displayOn = $(this).val();
                if (displayOn === 'specific') {
                    $('#specific_pages_wrapper').show();
                } else {
                    $('#specific_pages_wrapper').hide();
                }
            }).trigger('change');
        });
        </script>
        <style>
        .smartengage-metabox-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .smartengage-metabox-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .smartengage-metabox-section h4 {
            margin: 0 0 10px;
            font-size: 16px;
        }
        
        .smartengage-field-row {
            margin-bottom: 15px;
        }
        
        .smartengage-field-row label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .smartengage-field-row select,
        .smartengage-field-row input[type="text"],
        .smartengage-field-row input[type="number"],
        .smartengage-field-row textarea {
            width: 100%;
            max-width: 400px;
        }
        
        .smartengage-checkbox-list {
            margin-top: 5px;
        }
        
        .smartengage-checkbox-item {
            display: block;
            margin-bottom: 5px;
        }
        
        .smartengage-checkbox-item input {
            margin-right: 5px;
        }
        
        p.description {
            font-style: italic;
            color: #666;
            margin-top: 3px;
        }
        </style>
        <?php
    }

    /**
     * Render the analytics metabox.
     *
     * @since    1.0.0
     * @param    WP_Post    $post    The post object.
     */
    public function render_analytics_metabox($post) {
        // Get analytics data
        $views = $this->get_popup_views_count($post->ID);
        $clicks = $this->get_popup_clicks_count($post->ID);
        $conversion_rate = ($views > 0) ? round(($clicks / $views) * 100, 2) : 0;
        
        ?>
        <div class="smartengage-analytics-summary">
            <div class="smartengage-stat-item">
                <span class="smartengage-stat-label"><?php _e('Total Views:', 'smartengage-popups'); ?></span>
                <span class="smartengage-stat-value"><?php echo number_format($views); ?></span>
            </div>
            
            <div class="smartengage-stat-item">
                <span class="smartengage-stat-label"><?php _e('Total Clicks:', 'smartengage-popups'); ?></span>
                <span class="smartengage-stat-value"><?php echo number_format($clicks); ?></span>
            </div>
            
            <div class="smartengage-stat-item">
                <span class="smartengage-stat-label"><?php _e('Conversion Rate:', 'smartengage-popups'); ?></span>
                <span class="smartengage-stat-value"><?php echo $conversion_rate; ?>%</span>
            </div>
        </div>
        
        <p class="description">
            <?php _e('This is a summary of popup performance. For detailed analytics, view the Analytics tab in the main SmartEngage menu.', 'smartengage-popups'); ?>
        </p>
        
        <style>
        .smartengage-analytics-summary {
            margin-bottom: 15px;
        }
        
        .smartengage-stat-item {
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .smartengage-stat-label {
            font-weight: 600;
            display: block;
        }
        
        .smartengage-stat-value {
            font-size: 18px;
            color: #0073aa;
        }
        </style>
        <?php
    }

    /**
     * Save the metabox data.
     *
     * @since    1.0.0
     * @param    int       $post_id    The post ID.
     * @param    WP_Post   $post       The post object.
     */
    public function save_metabox($post_id, $post) {
        // Check if post type is our popup type
        if ($post->post_type !== 'smartengage_popup') {
            return;
        }
        
        // Save popup builder data
        if (isset($_POST['popup_design_json'])) {
            update_post_meta($post_id, '_popup_design_json', wp_slash($_POST['popup_design_json']));
        }
        
        if (isset($_POST['popup_type'])) {
            update_post_meta($post_id, '_popup_type', sanitize_text_field($_POST['popup_type']));
        }
        
        if (isset($_POST['popup_position'])) {
            update_post_meta($post_id, '_popup_position', sanitize_text_field($_POST['popup_position']));
        }
        
        if (isset($_POST['popup_theme'])) {
            update_post_meta($post_id, '_popup_theme', sanitize_text_field($_POST['popup_theme']));
        }
        
        // Check nonce for targeting metabox
        if (!isset($_POST['smartengage_targeting_nonce']) || !wp_verify_nonce($_POST['smartengage_targeting_nonce'], 'smartengage_popup_targeting_nonce')) {
            return;
        }
        
        // Save targeting settings
        if (isset($_POST['popup_trigger_type'])) {
            update_post_meta($post_id, '_popup_trigger_type', sanitize_text_field($_POST['popup_trigger_type']));
        }
        
        if (isset($_POST['popup_trigger_value'])) {
            update_post_meta($post_id, '_popup_trigger_value', sanitize_text_field($_POST['popup_trigger_value']));
        }
        
        if (isset($_POST['popup_frequency'])) {
            update_post_meta($post_id, '_popup_frequency', sanitize_text_field($_POST['popup_frequency']));
        }
        
        if (isset($_POST['popup_frequency_value'])) {
            update_post_meta($post_id, '_popup_frequency_value', absint($_POST['popup_frequency_value']));
        }
        
        if (isset($_POST['popup_display_on'])) {
            update_post_meta($post_id, '_popup_display_on', sanitize_text_field($_POST['popup_display_on']));
        }
        
        if (isset($_POST['popup_specific_pages'])) {
            update_post_meta($post_id, '_popup_specific_pages', sanitize_textarea_field($_POST['popup_specific_pages']));
        }
        
        if (isset($_POST['popup_device_target'])) {
            update_post_meta($post_id, '_popup_device_target', sanitize_text_field($_POST['popup_device_target']));
        }
        
        if (isset($_POST['popup_user_roles'])) {
            update_post_meta($post_id, '_popup_user_roles', array_map('sanitize_text_field', $_POST['popup_user_roles']));
        } else {
            update_post_meta($post_id, '_popup_user_roles', array());
        }
    }
    
    /**
     * Get popup views count from database.
     *
     * @since    1.0.0
     * @param    int    $popup_id    The popup ID.
     * @return   int                  The views count.
     */
    private function get_popup_views_count($popup_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartengage_popup_views';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if (!$table_exists) {
            return 0;
        }
        
        return intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE popup_id = %d", $popup_id)));
    }
    
    /**
     * Get popup clicks count from database.
     *
     * @since    1.0.0
     * @param    int    $popup_id    The popup ID.
     * @return   int                  The clicks count.
     */
    private function get_popup_clicks_count($popup_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartengage_popup_clicks';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if (!$table_exists) {
            return 0;
        }
        
        return intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE popup_id = %d", $popup_id)));
    }
}
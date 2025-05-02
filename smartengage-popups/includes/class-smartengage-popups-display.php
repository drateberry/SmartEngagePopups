<?php
/**
 * The display functionality of the plugin.
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The display functionality of the plugin.
 *
 * Handles rendering popups on the frontend based on targeting rules.
 *
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Display {

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
     * Display popups in the footer.
     *
     * @since    1.0.0
     */
    public function display_popups() {
        // Get popups that should be displayed
        $popups = $this->get_popups_to_display();
        
        // If no popups to display, return early
        if (empty($popups)) {
            return;
        }
        
        // Get global z-index setting
        $z_index = get_option('smartengage_popup_z_index', 999999);
        
        // Output popups
        foreach ($popups as $popup) {
            $popup_design = get_post_meta($popup->ID, '_popup_design_json', true);
            $popup_type = get_post_meta($popup->ID, '_popup_type', true) ?: 'slide-in';
            $popup_position = get_post_meta($popup->ID, '_popup_position', true) ?: 'bottom-right';
            $popup_theme = get_post_meta($popup->ID, '_popup_theme', true) ?: 'default';
            $trigger_type = get_post_meta($popup->ID, '_popup_trigger_type', true) ?: 'time_delay';
            $trigger_value = get_post_meta($popup->ID, '_popup_trigger_value', true) ?: '5';
            
            // Output popup container
            ?>
            <div id="smartengage-popup-<?php echo esc_attr($popup->ID); ?>" class="smartengage-popup-container theme-<?php echo esc_attr($popup_theme); ?>" data-popup-id="<?php echo esc_attr($popup->ID); ?>" data-trigger-type="<?php echo esc_attr($trigger_type); ?>" data-trigger-value="<?php echo esc_attr($trigger_value); ?>" style="z-index: <?php echo esc_attr($z_index); ?>;">
                <div class="smartengage-popup-overlay"></div>
                <div class="smartengage-popup type-<?php echo esc_attr($popup_type); ?> position-<?php echo esc_attr($popup_position); ?>">
                    <button class="smartengage-popup-close" aria-label="<?php esc_attr_e('Close', 'smartengage-popups'); ?>">&times;</button>
                    <div class="smartengage-popup-content">
                        <?php echo $this->render_popup_content($popup_design); ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    
    /**
     * Render popup content from JSON design data.
     *
     * @since    1.0.0
     * @param    string    $design_json    The JSON string with popup design.
     * @return   string                    The rendered HTML content.
     */
    private function render_popup_content($design_json) {
        $output = '';
        
        if (empty($design_json)) {
            return $output;
        }
        
        $elements = json_decode($design_json, true);
        
        if (!is_array($elements)) {
            return $output;
        }
        
        foreach ($elements as $element) {
            $element_html = '';
            
            // Position and size
            $style = sprintf(
                'position: absolute; left: %dpx; top: %dpx; width: %dpx; height: %dpx;',
                isset($element['position']['left']) ? intval($element['position']['left']) : 0,
                isset($element['position']['top']) ? intval($element['position']['top']) : 0,
                isset($element['size']['width']) ? intval($element['size']['width']) : 100,
                isset($element['size']['height']) ? intval($element['size']['height']) : 50
            );
            
            // Additional styles based on element type
            switch ($element['type']) {
                case 'heading':
                    $color = isset($element['color']) ? esc_attr($element['color']) : '#333333';
                    $font_size = isset($element['fontSize']) ? intval($element['fontSize']) : 24;
                    $style .= sprintf(' color: %s; font-size: %dpx;', $color, $font_size);
                    
                    $element_html = sprintf(
                        '<h2 class="element-heading" style="%s">%s</h2>',
                        $style,
                        wp_kses_post($element['content'])
                    );
                    break;
                    
                case 'text':
                    $color = isset($element['color']) ? esc_attr($element['color']) : '#666666';
                    $font_size = isset($element['fontSize']) ? intval($element['fontSize']) : 16;
                    $style .= sprintf(' color: %s; font-size: %dpx;', $color, $font_size);
                    
                    $element_html = sprintf(
                        '<p class="element-text" style="%s">%s</p>',
                        $style,
                        wp_kses_post($element['content'])
                    );
                    break;
                    
                case 'button':
                    $color = isset($element['color']) ? esc_attr($element['color']) : '#ffffff';
                    $bg_color = isset($element['backgroundColor']) ? esc_attr($element['backgroundColor']) : '#4361ee';
                    $font_size = isset($element['fontSize']) ? intval($element['fontSize']) : 16;
                    $url = isset($element['url']) ? esc_url($element['url']) : '#';
                    
                    $style .= sprintf(' color: %s; background-color: %s; font-size: %dpx;', $color, $bg_color, $font_size);
                    
                    $element_html = sprintf(
                        '<a href="%s" class="element-button smartengage-popup-button" style="%s" data-element-id="%s">%s</a>',
                        $url,
                        $style,
                        isset($element['id']) ? esc_attr($element['id']) : '',
                        wp_kses_post($element['content'])
                    );
                    break;
                    
                case 'image':
                    $src = isset($element['src']) ? esc_url($element['src']) : '';
                    $alt = isset($element['alt']) ? esc_attr($element['alt']) : '';
                    
                    if (!empty($src)) {
                        $element_html = sprintf(
                            '<div class="element-image" style="%s"><img src="%s" alt="%s" /></div>',
                            $style,
                            $src,
                            $alt
                        );
                    } else {
                        $element_html = sprintf(
                            '<div class="element-image" style="%s"></div>',
                            $style
                        );
                    }
                    break;
                    
                case 'divider':
                    $element_html = sprintf(
                        '<hr class="element-divider" style="%s" />',
                        $style
                    );
                    break;
                    
                case 'spacer':
                    $element_html = sprintf(
                        '<div class="element-spacer" style="%s"></div>',
                        $style
                    );
                    break;
            }
            
            $output .= $element_html;
        }
        
        return $output;
    }
    
    /**
     * Get popups that should be displayed to the current user.
     *
     * @since    1.0.0
     * @return   array    Array of popup post objects.
     */
    private function get_popups_to_display() {
        $args = array(
            'post_type' => 'smartengage_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_popup_status',
                    'value' => 'active',
                    'compare' => '='
                ),
                array(
                    'key' => '_popup_status',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $popups = get_posts($args);
        $eligible_popups = array();
        
        // Filter popups based on targeting rules
        foreach ($popups as $popup) {
            if ($this->should_display_popup($popup)) {
                $eligible_popups[] = $popup;
            }
        }
        
        return $eligible_popups;
    }
    
    /**
     * Check if a popup should be displayed based on targeting rules.
     *
     * @since    1.0.0
     * @param    WP_Post    $popup    The popup post object.
     * @return   bool                 Whether the popup should be displayed.
     */
    private function should_display_popup($popup) {
        // Check page targeting
        if (!$this->check_page_targeting($popup->ID)) {
            return false;
        }
        
        // Check device targeting
        if (!$this->check_device_targeting($popup->ID)) {
            return false;
        }
        
        // Check user role targeting
        if (!$this->check_user_role_targeting($popup->ID)) {
            return false;
        }
        
        // Check frequency rules
        if (!$this->check_frequency_rules($popup->ID)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if the current page matches the popup's page targeting rules.
     *
     * @since    1.0.0
     * @param    int     $popup_id    The popup ID.
     * @return   bool                 Whether the current page matches the targeting.
     */
    private function check_page_targeting($popup_id) {
        $display_on = get_post_meta($popup_id, '_popup_display_on', true) ?: 'all';
        
        // All pages
        if ($display_on === 'all') {
            return true;
        }
        
        // Homepage only
        if ($display_on === 'homepage' && is_front_page()) {
            return true;
        }
        
        // All posts
        if ($display_on === 'posts' && is_single()) {
            return true;
        }
        
        // All pages (not posts)
        if ($display_on === 'pages' && is_page()) {
            return true;
        }
        
        // Specific pages/posts
        if ($display_on === 'specific') {
            $specific_pages = get_post_meta($popup_id, '_popup_specific_pages', true) ?: '';
            $current_url = $this->get_current_url();
            
            if (empty($specific_pages)) {
                return false;
            }
            
            $page_list = explode("\n", $specific_pages);
            
            foreach ($page_list as $page_pattern) {
                $page_pattern = trim($page_pattern);
                
                // Skip empty lines
                if (empty($page_pattern)) {
                    continue;
                }
                
                // Check for exact match
                if ($page_pattern === $current_url) {
                    return true;
                }
                
                // Check for wildcard match
                if (strpos($page_pattern, '*') !== false) {
                    $regex = '/^' . str_replace('*', '.*', preg_quote($page_pattern, '/')) . '$/';
                    if (preg_match($regex, $current_url)) {
                        return true;
                    }
                }
            }
            
            return false;
        }
        
        return false;
    }
    
    /**
     * Check if the current device matches the popup's device targeting rules.
     *
     * @since    1.0.0
     * @param    int     $popup_id    The popup ID.
     * @return   bool                 Whether the current device matches the targeting.
     */
    private function check_device_targeting($popup_id) {
        $device_target = get_post_meta($popup_id, '_popup_device_target', true) ?: 'all';
        
        // All devices
        if ($device_target === 'all') {
            return true;
        }
        
        // Detect current device
        $current_device = $this->get_current_device();
        
        return $device_target === $current_device;
    }
    
    /**
     * Check if the current user role matches the popup's user role targeting rules.
     *
     * @since    1.0.0
     * @param    int     $popup_id    The popup ID.
     * @return   bool                 Whether the current user role matches the targeting.
     */
    private function check_user_role_targeting($popup_id) {
        $user_roles = get_post_meta($popup_id, '_popup_user_roles', true) ?: array();
        
        // If no roles specified, show to everyone
        if (empty($user_roles)) {
            return true;
        }
        
        // Check for logged out users
        if (in_array('logged_out', $user_roles) && !is_user_logged_in()) {
            return true;
        }
        
        // If user is logged in, check their role
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            
            foreach ($user->roles as $role) {
                if (in_array($role, $user_roles)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check if the popup should be displayed based on frequency rules.
     *
     * @since    1.0.0
     * @param    int     $popup_id    The popup ID.
     * @return   bool                 Whether the popup passes frequency rules.
     */
    private function check_frequency_rules($popup_id) {
        $frequency_rule = get_post_meta($popup_id, '_popup_frequency', true) ?: 'session';
        
        // Always show
        if ($frequency_rule === 'always') {
            return true;
        }
        
        // Get cookie names
        $viewed_cookie = 'smartengage_popup_viewed_' . $popup_id;
        
        // Once per session
        if ($frequency_rule === 'session') {
            // If cookie exists, don't show again in this session
            if (isset($_COOKIE[$viewed_cookie])) {
                return false;
            }
            return true;
        }
        
        // Once every X days
        if ($frequency_rule === 'days') {
            $days = intval(get_post_meta($popup_id, '_popup_frequency_value', true) ?: 7);
            
            // Check if cookie exists and is within the time limit
            if (isset($_COOKIE[$viewed_cookie])) {
                $last_view = intval($_COOKIE[$viewed_cookie]);
                $days_passed = (time() - $last_view) / DAY_IN_SECONDS;
                
                if ($days_passed < $days) {
                    return false;
                }
            }
            
            return true;
        }
        
        // Once every X page views
        if ($frequency_rule === 'views') {
            $page_views = intval(get_post_meta($popup_id, '_popup_frequency_value', true) ?: 3);
            
            // Get current page view count from cookie
            $view_count_cookie = 'smartengage_page_views';
            $current_views = isset($_COOKIE[$view_count_cookie]) ? intval($_COOKIE[$view_count_cookie]) : 1;
            
            // Check if we already showed this popup
            if (isset($_COOKIE[$viewed_cookie])) {
                $last_view_count = intval($_COOKIE[$viewed_cookie]);
                
                // If views since last popup show is less than required, don't show
                if (($current_views - $last_view_count) < $page_views) {
                    return false;
                }
            }
            
            return true;
        }
        
        return true;
    }
    
    /**
     * Get the current URL.
     *
     * @since    1.0.0
     * @return   string    The current URL.
     */
    private function get_current_url() {
        global $wp;
        return add_query_arg($wp->query_vars, home_url($wp->request));
    }
    
    /**
     * Get the current device type.
     *
     * @since    1.0.0
     * @return   string    The device type: 'desktop', 'tablet', or 'mobile'.
     */
    private function get_current_device() {
        // Check if Mobile_Detect class is available
        if (class_exists('Mobile_Detect')) {
            $detect = new Mobile_Detect();
            
            if ($detect->isTablet()) {
                return 'tablet';
            } elseif ($detect->isMobile()) {
                return 'mobile';
            } else {
                return 'desktop';
            }
        }
        
        // Fallback to simple user agent check
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*(mobi|opera mini)))/i', $user_agent)) {
            return 'tablet';
        }
        
        if (preg_match('/(mobile|android|touch|iphone|ipod|blackberry|phone)/i', $user_agent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }
}
<?php
/**
 * The analytics functionality of the plugin.
 *
 * @link       https://www.smartengage.com
 * @since      1.0.0
 *
 * @package    SmartEngage_Popups
 */

/**
 * The analytics functionality of the plugin.
 *
 * Handles tracking popup views and interactions
 *
 * @package    SmartEngage_Popups
 * @author     SmartEngage Team
 */
class SmartEngage_Popups_Analytics {

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
     * Ajax handler for recording popup views.
     *
     * @since    1.0.0
     */
    public function ajax_record_view() {
        // Check if analytics is disabled globally
        if (get_option('smartengage_disable_analytics', false)) {
            wp_send_json_success();
            return;
        }
        
        // Check required parameters
        if (!isset($_POST['popup_id']) || empty($_POST['popup_id'])) {
            wp_send_json_error(array('message' => 'Missing popup ID'));
            return;
        }
        
        $popup_id = intval($_POST['popup_id']);
        
        // Record the view
        $this->record_view($popup_id);
        
        // Set cookie for frequency rules
        $frequency_rule = get_post_meta($popup_id, '_popup_frequency', true) ?: 'session';
        $cookie_name = 'smartengage_popup_viewed_' . $popup_id;
        
        if ($frequency_rule === 'session') {
            // Session cookie - expires when browser closes
            setcookie($cookie_name, '1', 0, COOKIEPATH, COOKIE_DOMAIN);
        } elseif ($frequency_rule === 'days') {
            // Days cookie - expires after specified days
            $days = intval(get_post_meta($popup_id, '_popup_frequency_value', true) ?: 7);
            setcookie($cookie_name, time(), time() + ($days * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        } elseif ($frequency_rule === 'views') {
            // Page views cookie - store current view count
            $view_count_cookie = 'smartengage_page_views';
            $current_views = isset($_COOKIE[$view_count_cookie]) ? intval($_COOKIE[$view_count_cookie]) : 1;
            setcookie($cookie_name, $current_views, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        }
        
        wp_send_json_success();
    }
    
    /**
     * Ajax handler for recording popup clicks.
     *
     * @since    1.0.0
     */
    public function ajax_record_click() {
        // Check if analytics is disabled globally
        if (get_option('smartengage_disable_analytics', false)) {
            wp_send_json_success();
            return;
        }
        
        // Check required parameters
        if (!isset($_POST['popup_id']) || empty($_POST['popup_id'])) {
            wp_send_json_error(array('message' => 'Missing popup ID'));
            return;
        }
        
        $popup_id = intval($_POST['popup_id']);
        $element_id = isset($_POST['element_id']) ? sanitize_text_field($_POST['element_id']) : '';
        
        // Record the click
        $this->record_click($popup_id, $element_id);
        
        wp_send_json_success();
    }
    
    /**
     * Ajax handler for getting analytics data.
     *
     * @since    1.0.0
     */
    public function ajax_get_analytics() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
            return;
        }
        
        // Check required parameters
        if (!isset($_POST['popup_id']) || empty($_POST['popup_id'])) {
            wp_send_json_error(array('message' => 'Missing popup ID'));
            return;
        }
        
        $popup_id = intval($_POST['popup_id']);
        $date_range = isset($_POST['date_range']) ? sanitize_text_field($_POST['date_range']) : '30days';
        
        // Get analytics data
        $analytics = $this->get_popup_analytics($popup_id, $date_range);
        
        wp_send_json_success($analytics);
    }
    
    /**
     * Record a popup view in the database.
     *
     * @since    1.0.0
     * @param    int    $popup_id    The popup ID.
     */
    private function record_view($popup_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartengage_popup_views';
        
        // Prepare data
        $data = array(
            'popup_id' => $popup_id,
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'device_type' => $this->get_device_type(),
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'timestamp' => current_time('mysql')
        );
        
        // Anonymize IP if enabled
        if (get_option('smartengage_anonymize_ip', true)) {
            $data['ip_address'] = $this->anonymize_ip($data['ip_address']);
        }
        
        // Insert into database
        $wpdb->insert($table_name, $data);
    }
    
    /**
     * Record a popup click in the database.
     *
     * @since    1.0.0
     * @param    int       $popup_id     The popup ID.
     * @param    string    $element_id    The clicked element ID.
     */
    private function record_click($popup_id, $element_id = '') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'smartengage_popup_clicks';
        
        // Prepare data
        $data = array(
            'popup_id' => $popup_id,
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->get_client_ip(),
            'element_id' => $element_id,
            'timestamp' => current_time('mysql')
        );
        
        // Anonymize IP if enabled
        if (get_option('smartengage_anonymize_ip', true)) {
            $data['ip_address'] = $this->anonymize_ip($data['ip_address']);
        }
        
        // Insert into database
        $wpdb->insert($table_name, $data);
    }
    
    /**
     * Get analytics data for a specific popup.
     *
     * @since    1.0.0
     * @param    int       $popup_id      The popup ID.
     * @param    string    $date_range    The date range ('7days', '30days', 'year', or 'all').
     * @return   array                    The analytics data.
     */
    private function get_popup_analytics($popup_id, $date_range = '30days') {
        global $wpdb;
        
        // Calculate date range
        $date_condition = '';
        if ($date_range === '7days') {
            $date_condition = "AND timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($date_range === '30days') {
            $date_condition = "AND timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        } elseif ($date_range === 'year') {
            $date_condition = "AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }
        
        // Get views
        $views_table = $wpdb->prefix . 'smartengage_popup_views';
        $views_query = $wpdb->prepare(
            "SELECT DATE(timestamp) as date, COUNT(*) as count 
            FROM $views_table 
            WHERE popup_id = %d $date_condition
            GROUP BY DATE(timestamp) 
            ORDER BY date ASC",
            $popup_id
        );
        
        $views_data = $wpdb->get_results($views_query);
        
        // Get clicks
        $clicks_table = $wpdb->prefix . 'smartengage_popup_clicks';
        $clicks_query = $wpdb->prepare(
            "SELECT DATE(timestamp) as date, COUNT(*) as count 
            FROM $clicks_table 
            WHERE popup_id = %d $date_condition
            GROUP BY DATE(timestamp) 
            ORDER BY date ASC",
            $popup_id
        );
        
        $clicks_data = $wpdb->get_results($clicks_query);
        
        // Get device breakdown
        $devices_query = $wpdb->prepare(
            "SELECT device_type, COUNT(*) as count 
            FROM $views_table 
            WHERE popup_id = %d $date_condition
            GROUP BY device_type",
            $popup_id
        );
        
        $devices_data = $wpdb->get_results($devices_query);
        
        // Get total counts
        $total_views = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $views_table WHERE popup_id = %d $date_condition",
            $popup_id
        ));
        
        $total_clicks = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $clicks_table WHERE popup_id = %d $date_condition",
            $popup_id
        ));
        
        // Calculate conversion rate
        $conversion_rate = ($total_views > 0) ? ($total_clicks / $total_views) * 100 : 0;
        
        return array(
            'views' => array(
                'total' => $total_views,
                'data' => $views_data
            ),
            'clicks' => array(
                'total' => $total_clicks,
                'data' => $clicks_data
            ),
            'conversion_rate' => round($conversion_rate, 2),
            'devices' => $devices_data
        );
    }
    
    /**
     * Get the client IP address.
     *
     * @since    1.0.0
     * @return   string    The client IP address.
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }
        
        return '127.0.0.1'; // Default localhost IP
    }
    
    /**
     * Anonymize an IP address.
     *
     * @since    1.0.0
     * @param    string    $ip    The IP address to anonymize.
     * @return   string           The anonymized IP address.
     */
    private function anonymize_ip($ip) {
        if (empty($ip)) {
            return '';
        }
        
        // IPv4
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                $parts[2] = '0';
                $parts[3] = '0';
                return implode('.', $parts);
            }
        }
        
        // IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            if (count($parts) >= 4) {
                $parts = array_slice($parts, 0, 4);
                return implode(':', $parts) . ':0:0:0:0';
            }
        }
        
        return $ip;
    }
    
    /**
     * Get the device type from user agent.
     *
     * @since    1.0.0
     * @return   string    The device type: 'desktop', 'tablet', or 'mobile'.
     */
    private function get_device_type() {
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
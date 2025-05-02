<?php
/**
 * Class responsible for tracking and displaying analytics.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class for tracking and displaying analytics.
 */
class SmartEngage_Popups_Analytics {

    /**
     * The table name for views.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $views_table    The table name for views.
     */
    private $views_table;

    /**
     * The table name for clicks.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $clicks_table    The table name for clicks.
     */
    private $clicks_table;

    /**
     * Initialize the class.
     *
     * @since    1.0.0
     */
    public function __construct() {
        global $wpdb;
        
        $this->views_table = $wpdb->prefix . 'smartengage_popup_views';
        $this->clicks_table = $wpdb->prefix . 'smartengage_popup_clicks';
    }

    /**
     * Create the database tables for analytics.
     *
     * @since    1.0.0
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $views_table = "CREATE TABLE $this->views_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            user_ip varchar(100) NOT NULL,
            user_agent varchar(255) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY popup_id (popup_id),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        $clicks_table = "CREATE TABLE $this->clicks_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            popup_id bigint(20) NOT NULL,
            user_ip varchar(100) NOT NULL,
            user_agent varchar(255) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY popup_id (popup_id),
            KEY timestamp (timestamp)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $views_table );
        dbDelta( $clicks_table );
    }

    /**
     * Record a popup view.
     *
     * @since    1.0.0
     */
    public function record_view() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'smartengage-popups-nonce')) {
            wp_die('Security check failed');
        }
        
        if (!isset($_POST['popup_id'])) {
            wp_die('Popup ID is required');
        }
        
        $popup_id = intval($_POST['popup_id']);
        
        // Check if popup exists
        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== 'smartengage_popup') {
            wp_die('Invalid popup ID');
        }
        
        global $wpdb;
        
        $data = array(
            'popup_id' => $popup_id,
            'user_ip' => $this->get_anonymized_ip(),
            'user_agent' => $this->get_anonymized_user_agent(),
        );
        
        $wpdb->insert($this->views_table, $data);
        
        wp_die('Success');
    }

    /**
     * Record a popup click.
     *
     * @since    1.0.0
     */
    public function record_click() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'smartengage-popups-nonce')) {
            wp_die('Security check failed');
        }
        
        if (!isset($_POST['popup_id'])) {
            wp_die('Popup ID is required');
        }
        
        $popup_id = intval($_POST['popup_id']);
        
        // Check if popup exists
        $popup = get_post($popup_id);
        if (!$popup || $popup->post_type !== 'smartengage_popup') {
            wp_die('Invalid popup ID');
        }
        
        global $wpdb;
        
        $data = array(
            'popup_id' => $popup_id,
            'user_ip' => $this->get_anonymized_ip(),
            'user_agent' => $this->get_anonymized_user_agent(),
        );
        
        $wpdb->insert($this->clicks_table, $data);
        
        wp_die('Success');
    }

    /**
     * Get anonymized IP address.
     *
     * @since    1.0.0
     * @return   string    The anonymized IP address.
     */
    private function get_anonymized_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Anonymize the IP address (remove last octet for IPv4, last 80 bits for IPv6)
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ip = preg_replace('/\.\d+$/', '.0', $ip);
        } else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = substr($ip, 0, strrpos($ip, ':')) . ':0000';
        }
        
        return $ip;
    }

    /**
     * Get anonymized user agent.
     *
     * @since    1.0.0
     * @return   string    The anonymized user agent.
     */
    private function get_anonymized_user_agent() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        // Extract only browser and operating system info
        $pattern = '/^(.*?)(Chrome|Firefox|Safari|Edge|MSIE|Trident|Opera)\/[\d\.]+/';
        if (preg_match($pattern, $user_agent, $matches)) {
            $user_agent = $matches[0];
        }
        
        return $user_agent;
    }

    /**
     * Get the number of views for a popup.
     *
     * @since    1.0.0
     * @param    int       $popup_id    The popup ID.
     * @param    string    $period      The period for which to get views (daily, weekly, monthly, all).
     * @return   int                    The number of views.
     */
    public function get_popup_views( $popup_id, $period = 'all' ) {
        global $wpdb;
        
        $where = $wpdb->prepare('WHERE popup_id = %d', $popup_id);
        
        if ($period !== 'all') {
            $date_clause = $this->get_date_clause($period);
            $where .= " AND " . $date_clause;
        }
        
        $query = "SELECT COUNT(*) FROM $this->views_table $where";
        
        return (int) $wpdb->get_var($query);
    }

    /**
     * Get the number of clicks for a popup.
     *
     * @since    1.0.0
     * @param    int       $popup_id    The popup ID.
     * @param    string    $period      The period for which to get clicks (daily, weekly, monthly, all).
     * @return   int                    The number of clicks.
     */
    public function get_popup_clicks( $popup_id, $period = 'all' ) {
        global $wpdb;
        
        $where = $wpdb->prepare('WHERE popup_id = %d', $popup_id);
        
        if ($period !== 'all') {
            $date_clause = $this->get_date_clause($period);
            $where .= " AND " . $date_clause;
        }
        
        $query = "SELECT COUNT(*) FROM $this->clicks_table $where";
        
        return (int) $wpdb->get_var($query);
    }

    /**
     * Get daily data for charts.
     *
     * @since    1.0.0
     * @param    int       $popup_id    The popup ID.
     * @param    int       $days        The number of days to get data for.
     * @return   array                  The daily data.
     */
    public function get_daily_chart_data( $popup_id, $days = 30 ) {
        global $wpdb;
        
        $result = array(
            'labels' => array(),
            'views' => array(),
            'clicks' => array(),
            'conversion_rates' => array()
        );
        
        // Calculate dates
        $end_date = current_time('Y-m-d');
        $start_date = date('Y-m-d', strtotime("-$days days", strtotime($end_date)));
        
        // Generate all dates in the range
        $date_range = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            new DateTime($end_date . ' +1 day')
        );
        
        // Initialize data arrays with zeros
        foreach ($date_range as $date) {
            $date_str = $date->format('Y-m-d');
            $result['labels'][] = $date->format('M j');
            $result['views'][$date_str] = 0;
            $result['clicks'][$date_str] = 0;
        }
        
        // Get view data
        $view_query = $wpdb->prepare("
            SELECT DATE(timestamp) as date, COUNT(*) as count
            FROM $this->views_table
            WHERE popup_id = %d AND timestamp BETWEEN %s AND %s
            GROUP BY DATE(timestamp)
        ", $popup_id, $start_date, $end_date . ' 23:59:59');
        
        $views = $wpdb->get_results($view_query);
        
        foreach ($views as $view) {
            $result['views'][$view->date] = (int) $view->count;
        }
        
        // Get click data
        $click_query = $wpdb->prepare("
            SELECT DATE(timestamp) as date, COUNT(*) as count
            FROM $this->clicks_table
            WHERE popup_id = %d AND timestamp BETWEEN %s AND %s
            GROUP BY DATE(timestamp)
        ", $popup_id, $start_date, $end_date . ' 23:59:59');
        
        $clicks = $wpdb->get_results($click_query);
        
        foreach ($clicks as $click) {
            $result['clicks'][$click->date] = (int) $click->count;
        }
        
        // Calculate conversion rates
        foreach ($result['views'] as $date => $views) {
            $clicks = isset($result['clicks'][$date]) ? $result['clicks'][$date] : 0;
            $result['conversion_rates'][$date] = $views > 0 ? round(($clicks / $views) * 100, 1) : 0;
        }
        
        // Convert arrays to sequential for JS
        $result['views'] = array_values($result['views']);
        $result['clicks'] = array_values($result['clicks']);
        $result['conversion_rates'] = array_values($result['conversion_rates']);
        
        return $result;
    }

    /**
     * Get SQL clause for date filtering.
     *
     * @since    1.0.0
     * @param    string    $period    The period (daily, weekly, monthly).
     * @return   string               The SQL date clause.
     */
    private function get_date_clause( $period ) {
        $now = current_time('mysql');
        
        switch ($period) {
            case 'daily':
                return "DATE(timestamp) = DATE('$now')";
            case 'weekly':
                $week_start = date('Y-m-d', strtotime('-7 days', strtotime($now)));
                return "timestamp >= '$week_start'";
            case 'monthly':
                $month_start = date('Y-m-d', strtotime('-30 days', strtotime($now)));
                return "timestamp >= '$month_start'";
            default:
                return "1=1";
        }
    }
}

<?php
/**
 * Analytics functionality for tracking popup performance
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Analytics class for popup performance tracking
 */
class SmartEngage_Analytics {

    /**
     * Initialize the class
     */
    public function init() {
        // Register AJAX handler for analytics data
        add_action( 'wp_ajax_smartengage_get_analytics', array( $this, 'get_analytics_data' ) );
    }

    /**
     * Get analytics data for a popup or all popups
     */
    public function get_analytics_data() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'smartengage_admin_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions' );
        }
        
        // Get params
        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0;
        $date_range = isset( $_POST['date_range'] ) ? sanitize_text_field( $_POST['date_range'] ) : '7days';
        
        // Get date range values
        $dates = $this->get_date_range( $date_range );
        $start_date = $dates['start_date'];
        $end_date = $dates['end_date'];
        
        // Get analytics data
        $data = $this->get_analytics( $popup_id, $start_date, $end_date );
        
        wp_send_json_success( $data );
    }

    /**
     * Get analytics data from database
     *
     * @param int    $popup_id   Popup post ID (0 for all popups).
     * @param string $start_date Start date in MySQL format.
     * @param string $end_date   End date in MySQL format.
     * @return array Analytics data.
     */
    public function get_analytics( $popup_id = 0, $start_date = '', $end_date = '' ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartengage_analytics';
        
        // Set default date range if not specified
        if ( empty( $start_date ) ) {
            $start_date = date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
        }
        
        if ( empty( $end_date ) ) {
            $end_date = current_time( 'mysql' );
        }
        
        // Base query
        $sql = "SELECT 
                    popup_id, 
                    event_type, 
                    DATE(event_date) as date, 
                    COUNT(*) as count 
                FROM 
                    $table_name 
                WHERE 
                    event_date BETWEEN %s AND %s";
        
        $params = array( $start_date, $end_date );
        
        // Add popup filter if specified
        if ( $popup_id > 0 ) {
            $sql .= " AND popup_id = %d";
            $params[] = $popup_id;
        }
        
        // Group and order results
        $sql .= " GROUP BY popup_id, event_type, DATE(event_date) 
                  ORDER BY date ASC, popup_id ASC, event_type ASC";
        
        // Prepare and execute query
        $sql = $wpdb->prepare( $sql, $params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $results = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        
        // Process results
        $data = array(
            'impressions' => array(),
            'conversions' => array(),
            'dates' => array(),
            'popups' => array(),
            'totals' => array(
                'impressions' => 0,
                'conversions' => 0,
                'rate' => 0,
            ),
        );
        
        // Get unique dates
        $dates = array();
        foreach ( $results as $row ) {
            if ( ! in_array( $row['date'], $dates, true ) ) {
                $dates[] = $row['date'];
            }
        }
        sort( $dates );
        $data['dates'] = $dates;
        
        // Get popup data
        if ( $popup_id > 0 ) {
            $data['popups'][ $popup_id ] = array(
                'id' => $popup_id,
                'title' => get_the_title( $popup_id ),
                'daily' => array(),
                'totals' => array(
                    'impressions' => 0,
                    'conversions' => 0,
                    'rate' => 0,
                ),
            );
        } else {
            $popup_query = new WP_Query( array(
                'post_type' => 'smartengage_popup',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            ) );
            
            if ( $popup_query->have_posts() ) {
                while ( $popup_query->have_posts() ) {
                    $popup_query->the_post();
                    $id = get_the_ID();
                    
                    $data['popups'][ $id ] = array(
                        'id' => $id,
                        'title' => get_the_title(),
                        'daily' => array(),
                        'totals' => array(
                            'impressions' => 0,
                            'conversions' => 0,
                            'rate' => 0,
                        ),
                    );
                }
                
                wp_reset_postdata();
            }
        }
        
        // Initialize daily data for all popups
        foreach ( $data['popups'] as $id => $popup ) {
            foreach ( $dates as $date ) {
                $data['popups'][ $id ]['daily'][ $date ] = array(
                    'impressions' => 0,
                    'conversions' => 0,
                    'rate' => 0,
                );
            }
        }
        
        // Populate data
        foreach ( $results as $row ) {
            $pid = $row['popup_id'];
            $date = $row['date'];
            $type = $row['event_type'];
            $count = (int) $row['count'];
            
            // Skip if popup doesn't exist in data
            if ( ! isset( $data['popups'][ $pid ] ) ) {
                continue;
            }
            
            // Add to daily data
            if ( 'impression' === $type ) {
                $data['popups'][ $pid ]['daily'][ $date ]['impressions'] = $count;
                $data['popups'][ $pid ]['totals']['impressions'] += $count;
                $data['totals']['impressions'] += $count;
            } elseif ( 'conversion' === $type ) {
                $data['popups'][ $pid ]['daily'][ $date ]['conversions'] = $count;
                $data['popups'][ $pid ]['totals']['conversions'] += $count;
                $data['totals']['conversions'] += $count;
            }
        }
        
        // Calculate conversion rates
        foreach ( $data['popups'] as $id => $popup ) {
            // Calculate daily rates
            foreach ( $popup['daily'] as $date => $metrics ) {
                if ( $metrics['impressions'] > 0 ) {
                    $data['popups'][ $id ]['daily'][ $date ]['rate'] = ( $metrics['conversions'] / $metrics['impressions'] ) * 100;
                }
            }
            
            // Calculate total rate
            if ( $popup['totals']['impressions'] > 0 ) {
                $data['popups'][ $id ]['totals']['rate'] = ( $popup['totals']['conversions'] / $popup['totals']['impressions'] ) * 100;
            }
        }
        
        // Calculate overall conversion rate
        if ( $data['totals']['impressions'] > 0 ) {
            $data['totals']['rate'] = ( $data['totals']['conversions'] / $data['totals']['impressions'] ) * 100;
        }
        
        // Format chart data
        $chart_data = array();
        
        foreach ( $dates as $date ) {
            $date_data = array(
                'date' => $date,
                'impressions' => 0,
                'conversions' => 0,
            );
            
            foreach ( $data['popups'] as $popup ) {
                $date_data['impressions'] += $popup['daily'][ $date ]['impressions'];
                $date_data['conversions'] += $popup['daily'][ $date ]['conversions'];
            }
            
            $chart_data[] = $date_data;
        }
        
        $data['chart_data'] = $chart_data;
        
        return $data;
    }

    /**
     * Get date range based on period
     *
     * @param string $range Date range (7days, 30days, 90days, etc.).
     * @return array Start date and end date.
     */
    private function get_date_range( $range ) {
        $end_date = current_time( 'mysql' );
        
        switch ( $range ) {
            case '7days':
                $start_date = date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
                break;
                
            case '30days':
                $start_date = date( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
                break;
                
            case '90days':
                $start_date = date( 'Y-m-d H:i:s', strtotime( '-90 days' ) );
                break;
                
            case 'year':
                $start_date = date( 'Y-m-d H:i:s', strtotime( '-1 year' ) );
                break;
                
            default:
                $start_date = date( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
                break;
        }
        
        return array(
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
    }
}

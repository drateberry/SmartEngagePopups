<?php
/**
 * Frontend functionality for popups
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Frontend class for popup rendering and functionality
 */
class SmartEngage_Frontend {

    /**
     * Initialize the class
     */
    public function init() {
        // Enqueue frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        
        // Add popup HTML to footer
        add_action( 'wp_footer', array( $this, 'render_popups' ) );
        
        // Register AJAX handler for conversion tracking
        add_action( 'wp_ajax_smartengage_track_conversion', array( $this, 'track_conversion' ) );
        add_action( 'wp_ajax_nopriv_smartengage_track_conversion', array( $this, 'track_conversion' ) );
        
        // Register AJAX handler for impression tracking
        add_action( 'wp_ajax_smartengage_track_impression', array( $this, 'track_impression' ) );
        add_action( 'wp_ajax_nopriv_smartengage_track_impression', array( $this, 'track_impression' ) );
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_assets() {
        // Only enqueue if popups are available
        if ( ! $this->has_active_popups() ) {
            return;
        }
        
        wp_enqueue_style(
            'smartengage-frontend',
            SMARTENGAGE_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SMARTENGAGE_VERSION
        );
        
        wp_enqueue_script(
            'smartengage-frontend',
            SMARTENGAGE_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            SMARTENGAGE_VERSION,
            true
        );
        
        wp_localize_script(
            'smartengage-frontend',
            'smartEngageFrontend',
            array(
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'smartengage_frontend_nonce' ),
                'popups'    => $this->get_popup_settings(),
            )
        );
    }

    /**
     * Check if there are active popups for the current page
     *
     * @return bool True if active popups exist, false otherwise.
     */
    private function has_active_popups() {
        $args = array(
            'post_type'      => 'smartengage_popup',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => '_smartengage_popup_status',
                    'value'   => 'enabled',
                    'compare' => '=',
                ),
            ),
        );
        
        $query = new WP_Query( $args );
        
        return $query->have_posts();
    }

    /**
     * Get popup settings for frontend
     *
     * @return array Array of popup settings.
     */
    private function get_popup_settings() {
        $popups = array();
        
        $args = array(
            'post_type'      => 'smartengage_popup',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => '_smartengage_popup_status',
                    'value'   => 'enabled',
                    'compare' => '=',
                ),
            ),
        );
        
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                
                // Skip if popup doesn't match current targeting
                if ( ! $this->should_display_popup( $post_id ) ) {
                    continue;
                }
                
                $popup = array(
                    'id'             => $post_id,
                    'title'          => get_the_title(),
                    'content'        => get_the_content(),
                    'type'           => get_post_meta( $post_id, '_smartengage_popup_type', true ),
                    'position'       => get_post_meta( $post_id, '_smartengage_popup_position', true ),
                    'trigger_type'   => get_post_meta( $post_id, '_smartengage_trigger_type', true ),
                    'page_views'     => (int) get_post_meta( $post_id, '_smartengage_page_views', true ),
                    'time_on_page'   => (int) get_post_meta( $post_id, '_smartengage_time_on_page', true ),
                    'scroll_depth'   => (int) get_post_meta( $post_id, '_smartengage_scroll_depth', true ),
                    'exit_intent'    => get_post_meta( $post_id, '_smartengage_exit_intent', true ),
                    'frequency_rule' => get_post_meta( $post_id, '_smartengage_frequency_rule', true ),
                    'days_between'   => (int) get_post_meta( $post_id, '_smartengage_days_between', true ),
                    'max_impressions' => (int) get_post_meta( $post_id, '_smartengage_max_impressions', true ),
                    'cta_text'       => get_post_meta( $post_id, '_smartengage_cta_text', true ),
                    'cta_url'        => get_post_meta( $post_id, '_smartengage_cta_url', true ),
                    'cta2_text'      => get_post_meta( $post_id, '_smartengage_cta2_text', true ),
                    'cta2_url'       => get_post_meta( $post_id, '_smartengage_cta2_url', true ),
                    'image'          => get_the_post_thumbnail_url( $post_id, 'medium' ),
                );
                
                $popups[] = $popup;
            }
            
            wp_reset_postdata();
        }
        
        return $popups;
    }

    /**
     * Check if popup should be displayed based on targeting rules
     *
     * @param int $popup_id Popup post ID.
     * @return bool True if popup should be displayed, false otherwise.
     */
    private function should_display_popup( $popup_id ) {
        // Device type targeting
        $device_type = get_post_meta( $popup_id, '_smartengage_device_type', true );
        
        if ( 'desktop' === $device_type && wp_is_mobile() ) {
            return false;
        }
        
        if ( 'mobile' === $device_type && ! wp_is_mobile() ) {
            return false;
        }
        
        // User login status targeting
        $user_logged_in = get_post_meta( $popup_id, '_smartengage_user_logged_in', true );
        
        if ( 'logged_in' === $user_logged_in && ! is_user_logged_in() ) {
            return false;
        }
        
        if ( 'logged_out' === $user_logged_in && is_user_logged_in() ) {
            return false;
        }
        
        // User role targeting
        if ( is_user_logged_in() && 'logged_in' === $user_logged_in ) {
            $user_roles = get_post_meta( $popup_id, '_smartengage_user_roles', true );
            
            if ( ! empty( $user_roles ) && is_array( $user_roles ) ) {
                $current_user = wp_get_current_user();
                $user_role_match = false;
                
                foreach ( $user_roles as $role ) {
                    if ( in_array( $role, (array) $current_user->roles, true ) ) {
                        $user_role_match = true;
                        break;
                    }
                }
                
                if ( ! $user_role_match ) {
                    return false;
                }
            }
        }
        
        // URL targeting
        $target_urls = get_post
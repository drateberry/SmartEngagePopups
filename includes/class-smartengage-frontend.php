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
        $target_urls = get_post_meta( $popup_id, '_smartengage_target_urls', true );
        
        if ( ! empty( $target_urls ) ) {
            $current_url = trailingslashit( get_permalink() );
            $target_urls_array = array_map( 'trim', explode( "\n", $target_urls ) );
            $url_match = false;
            
            foreach ( $target_urls_array as $url ) {
                // Convert to regex pattern
                $pattern = '#' . str_replace( '\*', '.*', preg_quote( $url, '#' ) ) . '#i';
                
                if ( preg_match( $pattern, $current_url ) ) {
                    $url_match = true;
                    break;
                }
            }
            
            if ( ! $url_match ) {
                return false;
            }
        }
        
        // Post type targeting
        $target_post_types = get_post_meta( $popup_id, '_smartengage_target_post_types', true );
        
        if ( ! empty( $target_post_types ) && is_array( $target_post_types ) ) {
            $current_post_type = get_post_type();
            
            if ( ! in_array( $current_post_type, $target_post_types, true ) ) {
                return false;
            }
        }
        
        // Referrer URL targeting
        $referrer_url = get_post_meta( $popup_id, '_smartengage_referrer_url', true );
        
        if ( ! empty( $referrer_url ) ) {
            $http_referrer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
            
            if ( empty( $http_referrer ) ) {
                return false;
            }
            
            // Convert to regex pattern
            $pattern = '#' . str_replace( '\*', '.*', preg_quote( $referrer_url, '#' ) ) . '#i';
            
            if ( ! preg_match( $pattern, $http_referrer ) ) {
                return false;
            }
        }
        
        // Cookie targeting
        $cookie_targeting = get_post_meta( $popup_id, '_smartengage_cookie_targeting', true );
        
        if ( 'enabled' === $cookie_targeting ) {
            $cookie_name = get_post_meta( $popup_id, '_smartengage_cookie_name', true );
            
            if ( ! empty( $cookie_name ) && ! isset( $_COOKIE[ $cookie_name ] ) ) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Render popups in the footer
     */
    public function render_popups() {
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
            echo '<div id="smartengage-popups" class="smartengage-popups" aria-hidden="true">';
            
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();
                
                // Skip if popup doesn't match current targeting
                if ( ! $this->should_display_popup( $post_id ) ) {
                    continue;
                }
                
                $popup_type = get_post_meta( $post_id, '_smartengage_popup_type', true );
                $popup_position = get_post_meta( $post_id, '_smartengage_popup_position', true );
                $cta_text = get_post_meta( $post_id, '_smartengage_cta_text', true );
                $cta_url = get_post_meta( $post_id, '_smartengage_cta_url', true );
                $cta2_text = get_post_meta( $post_id, '_smartengage_cta2_text', true );
                $cta2_url = get_post_meta( $post_id, '_smartengage_cta2_url', true );
                
                // Set default values if empty
                if ( empty( $popup_type ) ) {
                    $popup_type = 'slide-in';
                }
                
                if ( empty( $popup_position ) ) {
                    $popup_position = 'bottom-right';
                }
                
                // Generate popup HTML
                $classes = array(
                    'smartengage-popup',
                    'smartengage-popup-' . $post_id,
                    'smartengage-popup-type-' . $popup_type,
                    'smartengage-popup-position-' . $popup_position,
                );
                
                ?>
                <div id="smartengage-popup-<?php echo esc_attr( $post_id ); ?>" 
                     class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" 
                     data-popup-id="<?php echo esc_attr( $post_id ); ?>"
                     aria-labelledby="smartengage-popup-title-<?php echo esc_attr( $post_id ); ?>"
                     aria-hidden="true"
                     role="dialog">
                    
                    <div class="smartengage-popup-content">
                        <button class="smartengage-popup-close" aria-label="<?php esc_attr_e( 'Close popup', 'smartengage-popups' ); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="smartengage-popup-image">
                                <?php the_post_thumbnail( 'medium' ); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="smartengage-popup-text">
                            <h2 id="smartengage-popup-title-<?php echo esc_attr( $post_id ); ?>" class="smartengage-popup-title">
                                <?php the_title(); ?>
                            </h2>
                            
                            <div class="smartengage-popup-body">
                                <?php the_content(); ?>
                            </div>
                            
                            <?php if ( ! empty( $cta_text ) ) : ?>
                                <div class="smartengage-popup-cta">
                                    <a href="<?php echo esc_url( $cta_url ); ?>" class="smartengage-popup-button smartengage-popup-primary-button" data-popup-id="<?php echo esc_attr( $post_id ); ?>">
                                        <?php echo esc_html( $cta_text ); ?>
                                    </a>
                                    
                                    <?php if ( ! empty( $cta2_text ) ) : ?>
                                        <a href="<?php echo esc_url( $cta2_url ); ?>" class="smartengage-popup-button smartengage-popup-secondary-button" data-popup-id="<?php echo esc_attr( $post_id ); ?>">
                                            <?php echo esc_html( $cta2_text ); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            
            echo '</div>';
            
            wp_reset_postdata();
        }
    }

    /**
     * Track popup conversion via AJAX
     */
    public function track_conversion() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'smartengage_frontend_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        // Get popup ID
        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0;
        
        if ( empty( $popup_id ) ) {
            wp_send_json_error( 'Invalid popup ID' );
        }
        
        // Update conversion count
        $conversions = (int) get_post_meta( $popup_id, '_smartengage_conversions', true );
        $conversions++;
        update_post_meta( $popup_id, '_smartengage_conversions', $conversions );
        
        // Record conversion in analytics table
        $this->record_analytics_event( $popup_id, 'conversion' );
        
        wp_send_json_success( array(
            'conversions' => $conversions,
        ) );
    }

    /**
     * Track popup impression via AJAX
     */
    public function track_impression() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'smartengage_frontend_nonce' ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }
        
        // Get popup ID
        $popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0;
        
        if ( empty( $popup_id ) ) {
            wp_send_json_error( 'Invalid popup ID' );
        }
        
        // Update impression count
        $impressions = (int) get_post_meta( $popup_id, '_smartengage_impressions', true );
        $impressions++;
        update_post_meta( $popup_id, '_smartengage_impressions', $impressions );
        
        // Record impression in analytics table
        $this->record_analytics_event( $popup_id, 'impression' );
        
        wp_send_json_success( array(
            'impressions' => $impressions,
        ) );
    }

    /**
     * Record analytics event in database
     *
     * @param int    $popup_id   Popup post ID.
     * @param string $event_type Event type (impression, conversion).
     */
    private function record_analytics_event( $popup_id, $event_type ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'smartengage_analytics';
        
        $wpdb->insert(
            $table_name,
            array(
                'popup_id'    => $popup_id,
                'event_type'  => $event_type,
                'event_date'  => current_time( 'mysql' ),
                'user_ip'     => $this->get_user_ip(),
                'user_agent'  => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
                'referer_url' => isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '',
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );
    }

    /**
     * Get user IP address with proxy support
     *
     * @return string User IP address.
     */
    private function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            // Get the first IP in a list of comma-separated IPs
            $ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
            $ip = trim( $ips[0] );
        } else {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }
        
        return $ip;
    }
}

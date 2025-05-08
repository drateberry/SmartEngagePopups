<?php
/**
 * Metaboxes for popup configuration
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Metabox class for popup configuration
 */
class SmartEngage_Metabox {

    /**
     * Initialize the class
     */
    public function init() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_smartengage_popup', array( $this, 'save_meta_box_data' ) );
        
        // Register Gutenberg sidebar if using block editor
        add_action( 'init', array( $this, 'register_gutenberg_sidebar' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
    }

    /**
     * Register meta boxes for popup post type
     */
    public function add_meta_boxes() {
        add_meta_box(
            'smartengage_popup_options',
            __( 'Popup Options', 'smartengage-popups' ),
            array( $this, 'render_popup_options_meta_box' ),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_display_rules',
            __( 'Display Rules', 'smartengage-popups' ),
            array( $this, 'render_display_rules_meta_box' ),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_targeting',
            __( 'Targeting Options', 'smartengage-popups' ),
            array( $this, 'render_targeting_meta_box' ),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_frequency',
            __( 'Frequency Rules', 'smartengage-popups' ),
            array( $this, 'render_frequency_meta_box' ),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_preview',
            __( 'Popup Preview', 'smartengage-popups' ),
            array( $this, 'render_preview_meta_box' ),
            'smartengage_popup',
            'side',
            'high'
        );
        
        add_meta_box(
            'smartengage_stats',
            __( 'Popup Statistics', 'smartengage-popups' ),
            array( $this, 'render_stats_meta_box' ),
            'smartengage_popup',
            'side',
            'default'
        );
    }
    
    /**
     * Render popup options meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_popup_options_meta_box( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_save_meta_box_data', 'smartengage_meta_box_nonce' );
        
        // Get saved values
        $popup_type = get_post_meta( $post->ID, '_smartengage_popup_type', true );
        $popup_position = get_post_meta( $post->ID, '_smartengage_popup_position', true );
        $popup_status = get_post_meta( $post->ID, '_smartengage_popup_status', true );
        $cta_text = get_post_meta( $post->ID, '_smartengage_cta_text', true );
        $cta_url = get_post_meta( $post->ID, '_smartengage_cta_url', true );
        $cta2_text = get_post_meta( $post->ID, '_smartengage_cta2_text', true );
        $cta2_url = get_post_meta( $post->ID, '_smartengage_cta2_url', true );
        
        // Set defaults if empty
        if ( empty( $popup_type ) ) {
            $popup_type = 'slide-in';
        }
        
        if ( empty( $popup_position ) ) {
            $popup_position = 'bottom-right';
        }
        
        if ( empty( $popup_status ) ) {
            $popup_status = 'enabled';
        }
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-popup-options.php';
    }
    
    /**
     * Render display rules meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_display_rules_meta_box( $post ) {
        // Get saved values
        $trigger_type = get_post_meta( $post->ID, '_smartengage_trigger_type', true );
        $page_views = get_post_meta( $post->ID, '_smartengage_page_views', true );
        $time_on_page = get_post_meta( $post->ID, '_smartengage_time_on_page', true );
        $scroll_depth = get_post_meta( $post->ID, '_smartengage_scroll_depth', true );
        $exit_intent = get_post_meta( $post->ID, '_smartengage_exit_intent', true );
        $target_urls = get_post_meta( $post->ID, '_smartengage_target_urls', true );
        $target_post_types = get_post_meta( $post->ID, '_smartengage_target_post_types', true );
        $logic_operator = get_post_meta( $post->ID, '_smartengage_logic_operator', true );
        
        // Set defaults if empty
        if ( empty( $trigger_type ) ) {
            $trigger_type = 'time';
        }
        
        if ( empty( $page_views ) ) {
            $page_views = 1;
        }
        
        if ( empty( $time_on_page ) ) {
            $time_on_page = 10;
        }
        
        if ( empty( $scroll_depth ) ) {
            $scroll_depth = 50;
        }
        
        if ( empty( $exit_intent ) ) {
            $exit_intent = 'disabled';
        }
        
        if ( empty( $logic_operator ) ) {
            $logic_operator = 'OR';
        }
        
        // Get all post types
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-display-rules.php';
    }
    
    /**
     * Render targeting meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_targeting_meta_box( $post ) {
        // Get saved values
        $device_type = get_post_meta( $post->ID, '_smartengage_device_type', true );
        $user_logged_in = get_post_meta( $post->ID, '_smartengage_user_logged_in', true );
        $user_roles = get_post_meta( $post->ID, '_smartengage_user_roles', true );
        $referrer_url = get_post_meta( $post->ID, '_smartengage_referrer_url', true );
        $cookie_targeting = get_post_meta( $post->ID, '_smartengage_cookie_targeting', true );
        $cookie_name = get_post_meta( $post->ID, '_smartengage_cookie_name', true );
        
        // Set defaults if empty
        if ( empty( $device_type ) ) {
            $device_type = 'all';
        }
        
        if ( empty( $user_logged_in ) ) {
            $user_logged_in = 'all';
        }
        
        // Get all user roles
        $roles = wp_roles()->get_names();
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-targeting.php';
    }
    
    /**
     * Render frequency meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_frequency_meta_box( $post ) {
        // Get saved values
        $frequency_rule = get_post_meta( $post->ID, '_smartengage_frequency_rule', true );
        $days_between = get_post_meta( $post->ID, '_smartengage_days_between', true );
        $max_impressions = get_post_meta( $post->ID, '_smartengage_max_impressions', true );
        
        // Set defaults if empty
        if ( empty( $frequency_rule ) ) {
            $frequency_rule = 'once_session';
        }
        
        if ( empty( $days_between ) ) {
            $days_between = 7;
        }
        
        if ( empty( $max_impressions ) ) {
            $max_impressions = 3;
        }
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-frequency.php';
    }
    
    /**
     * Render preview meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_preview_meta_box( $post ) {
        // Get popup type and position
        $popup_type = get_post_meta( $post->ID, '_smartengage_popup_type', true );
        $popup_position = get_post_meta( $post->ID, '_smartengage_popup_position', true );
        
        // Set defaults if empty
        if ( empty( $popup_type ) ) {
            $popup_type = 'slide-in';
        }
        
        if ( empty( $popup_position ) ) {
            $popup_position = 'bottom-right';
        }
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-preview.php';
    }
    
    /**
     * Render stats meta box
     *
     * @param WP_Post $post Current post object.
     */
    public function render_stats_meta_box( $post ) {
        // Get statistics
        $impressions = get_post_meta( $post->ID, '_smartengage_impressions', true );
        $conversions = get_post_meta( $post->ID, '_smartengage_conversions', true );
        
        // Calculate conversion rate
        $rate = 0;
        if ( !empty( $impressions ) && $impressions > 0 && !empty( $conversions ) ) {
            $rate = ( $conversions / $impressions ) * 100;
        }
        
        // Include the view
        include SMARTENGAGE_PLUGIN_DIR . 'admin/views/metabox-stats.php';
    }
    
    /**
     * Save meta box data
     *
     * @param int $post_id The post ID.
     */
    public function save_meta_box_data( $post_id ) {
        // Check if our nonce is set
        if ( ! isset( $_POST['smartengage_meta_box_nonce'] ) ) {
            return;
        }
        
        // Verify the nonce
        if ( ! wp_verify_nonce( $_POST['smartengage_meta_box_nonce'], 'smartengage_save_meta_box_data' ) ) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        
        // Check the user's permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
        
        // Sanitize and save popup options
        if ( isset( $_POST['_smartengage_popup_type'] ) ) {
            update_post_meta( $post_id, '_smartengage_popup_type', sanitize_text_field( $_POST['_smartengage_popup_type'] ) );
        }
        
        if ( isset( $_POST['_smartengage_popup_position'] ) ) {
            update_post_meta( $post_id, '_smartengage_popup_position', sanitize_text_field( $_POST['_smartengage_popup_position'] ) );
        }
        
        if ( isset( $_POST['_smartengage_popup_status'] ) ) {
            update_post_meta( $post_id, '_smartengage_popup_status', sanitize_text_field( $_POST['_smartengage_popup_status'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cta_text'] ) ) {
            update_post_meta( $post_id, '_smartengage_cta_text', sanitize_text_field( $_POST['_smartengage_cta_text'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cta_url'] ) ) {
            update_post_meta( $post_id, '_smartengage_cta_url', esc_url_raw( $_POST['_smartengage_cta_url'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cta2_text'] ) ) {
            update_post_meta( $post_id, '_smartengage_cta2_text', sanitize_text_field( $_POST['_smartengage_cta2_text'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cta2_url'] ) ) {
            update_post_meta( $post_id, '_smartengage_cta2_url', esc_url_raw( $_POST['_smartengage_cta2_url'] ) );
        }
        
        // Sanitize and save display rules
        if ( isset( $_POST['_smartengage_trigger_type'] ) ) {
            update_post_meta( $post_id, '_smartengage_trigger_type', sanitize_text_field( $_POST['_smartengage_trigger_type'] ) );
        }
        
        if ( isset( $_POST['_smartengage_page_views'] ) ) {
            update_post_meta( $post_id, '_smartengage_page_views', absint( $_POST['_smartengage_page_views'] ) );
        }
        
        if ( isset( $_POST['_smartengage_time_on_page'] ) ) {
            update_post_meta( $post_id, '_smartengage_time_on_page', absint( $_POST['_smartengage_time_on_page'] ) );
        }
        
        if ( isset( $_POST['_smartengage_scroll_depth'] ) ) {
            update_post_meta( $post_id, '_smartengage_scroll_depth', absint( $_POST['_smartengage_scroll_depth'] ) );
        }
        
        if ( isset( $_POST['_smartengage_exit_intent'] ) ) {
            update_post_meta( $post_id, '_smartengage_exit_intent', sanitize_text_field( $_POST['_smartengage_exit_intent'] ) );
        }
        
        if ( isset( $_POST['_smartengage_target_urls'] ) ) {
            update_post_meta( $post_id, '_smartengage_target_urls', sanitize_textarea_field( $_POST['_smartengage_target_urls'] ) );
        }
        
        if ( isset( $_POST['_smartengage_target_post_types'] ) && is_array( $_POST['_smartengage_target_post_types'] ) ) {
            update_post_meta( $post_id, '_smartengage_target_post_types', array_map( 'sanitize_text_field', $_POST['_smartengage_target_post_types'] ) );
        } else {
            delete_post_meta( $post_id, '_smartengage_target_post_types' );
        }
        
        if ( isset( $_POST['_smartengage_logic_operator'] ) ) {
            update_post_meta( $post_id, '_smartengage_logic_operator', sanitize_text_field( $_POST['_smartengage_logic_operator'] ) );
        }
        
        // Sanitize and save targeting options
        if ( isset( $_POST['_smartengage_device_type'] ) ) {
            update_post_meta( $post_id, '_smartengage_device_type', sanitize_text_field( $_POST['_smartengage_device_type'] ) );
        }
        
        if ( isset( $_POST['_smartengage_user_logged_in'] ) ) {
            update_post_meta( $post_id, '_smartengage_user_logged_in', sanitize_text_field( $_POST['_smartengage_user_logged_in'] ) );
        }
        
        if ( isset( $_POST['_smartengage_user_roles'] ) && is_array( $_POST['_smartengage_user_roles'] ) ) {
            update_post_meta( $post_id, '_smartengage_user_roles', array_map( 'sanitize_text_field', $_POST['_smartengage_user_roles'] ) );
        } else {
            delete_post_meta( $post_id, '_smartengage_user_roles' );
        }
        
        if ( isset( $_POST['_smartengage_referrer_url'] ) ) {
            update_post_meta( $post_id, '_smartengage_referrer_url', sanitize_text_field( $_POST['_smartengage_referrer_url'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cookie_targeting'] ) ) {
            update_post_meta( $post_id, '_smartengage_cookie_targeting', sanitize_text_field( $_POST['_smartengage_cookie_targeting'] ) );
        }
        
        if ( isset( $_POST['_smartengage_cookie_name'] ) ) {
            update_post_meta( $post_id, '_smartengage_cookie_name', sanitize_text_field( $_POST['_smartengage_cookie_name'] ) );
        }
        
        // Sanitize and save frequency rules
        if ( isset( $_POST['_smartengage_frequency_rule'] ) ) {
            update_post_meta( $post_id, '_smartengage_frequency_rule', sanitize_text_field( $_POST['_smartengage_frequency_rule'] ) );
        }
        
        if ( isset( $_POST['_smartengage_days_between'] ) ) {
            update_post_meta( $post_id, '_smartengage_days_between', absint( $_POST['_smartengage_days_between'] ) );
        }
        
        if ( isset( $_POST['_smartengage_max_impressions'] ) ) {
            update_post_meta( $post_id, '_smartengage_max_impressions', absint( $_POST['_smartengage_max_impressions'] ) );
        }
    }
    
    /**
     * Register meta for use in Gutenberg
     */
    public function register_gutenberg_sidebar() {
        // Register meta fields for Gutenberg
        register_meta( 'post', '_smartengage_popup_type', array(
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
        
        register_meta( 'post', '_smartengage_popup_position', array(
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
        
        register_meta( 'post', '_smartengage_popup_status', array(
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
        
        register_meta( 'post', '_smartengage_cta_text', array(
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
        
        register_meta( 'post', '_smartengage_cta_url', array(
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
        
        // Register other meta fields as needed
    }
    
    /**
     * Enqueue block editor assets for Gutenberg
     */
    public function enqueue_block_editor_assets() {
        $screen = get_current_screen();
        
        // Only load on our post type
        if ( 'smartengage_popup' !== $screen->post_type ) {
            return;
        }
        
        wp_enqueue_script(
            'smartengage-sidebar',
            SMARTENGAGE_PLUGIN_URL . 'assets/js/sidebar.js',
            array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-compose' ),
            SMARTENGAGE_VERSION,
            true
        );
    }
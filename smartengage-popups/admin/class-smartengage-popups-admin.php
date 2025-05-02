<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The admin-specific functionality of the plugin.
 */
class SmartEngage_Popups_Admin {

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles( $hook ) {
        // Only load on our post type edit screen or our plugin's pages
        $screen = get_current_screen();
        if ( $screen->post_type !== 'smartengage_popup' && strpos( $hook, 'smartengage' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'smartengage-popups-admin',
            SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/css/smartengage-popups-admin.css',
            array(),
            SMARTENGAGE_POPUPS_VERSION,
            'all'
        );
        
        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        
        // Only load builder styles on edit/add new popup screens
        if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
            // jQuery UI styles for draggable & resizable
            wp_enqueue_style(
                'jquery-ui',
                'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
                array(),
                '1.12.1',
                'all'
            );
            
            // Builder stylesheet
            wp_enqueue_style(
                'smartengage-popups-builder',
                SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/css/smartengage-popups-builder.css',
                array('jquery-ui'),
                SMARTENGAGE_POPUPS_VERSION,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts( $hook ) {
        // Only load on our post type edit screen or our plugin's pages
        $screen = get_current_screen();
        if ( $screen->post_type !== 'smartengage_popup' && strpos( $hook, 'smartengage' ) === false ) {
            return;
        }

        wp_enqueue_script(
            'smartengage-popups-admin',
            SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/js/smartengage-popups-admin.js',
            array( 'jquery', 'wp-color-picker' ),
            SMARTENGAGE_POPUPS_VERSION,
            true
        );

        // Only load builder scripts on edit/add new popup screens
        if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
            // Make sure jQuery UI components are loaded
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-sortable');
            
            // WordPress media uploader
            wp_enqueue_media();
            
            // Builder script
            wp_enqueue_script(
                'smartengage-popups-builder',
                SMARTENGAGE_POPUPS_PLUGIN_URL . 'admin/js/smartengage-popups-builder.js',
                array( 'jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'wp-color-picker' ),
                SMARTENGAGE_POPUPS_VERSION,
                true
            );
        }

        // For popup analytics page, add Chart.js
        if ( strpos( $hook, 'smartengage-analytics' ) !== false ) {
            wp_enqueue_script(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js',
                array(),
                '3.7.1',
                true
            );
            
            // Localize script with popup data if viewing a specific popup
            if ( isset( $_GET['popup'] ) ) {
                $popup_id = intval( $_GET['popup'] );
                $analytics = new SmartEngage_Popups_Analytics();
                $chart_data = $analytics->get_daily_chart_data( $popup_id, 30 );
                
                wp_localize_script(
                    'smartengage-popups-admin',
                    'SmartEngageAnalytics',
                    array(
                        'chartData' => $chart_data
                    )
                );
            }
        }
    }

    /**
     * Add the analytics dashboard page.
     *
     * @since    1.0.0
     */
    public function add_analytics_page() {
        add_submenu_page(
            'edit.php?post_type=smartengage_popup',
            __( 'Popup Analytics', 'smartengage-popups' ),
            __( 'Analytics', 'smartengage-popups' ),
            'manage_options',
            'smartengage-analytics',
            array( $this, 'display_analytics_page' )
        );
    }

    /**
     * Display the analytics dashboard page.
     *
     * @since    1.0.0
     */
    public function display_analytics_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Check if viewing a specific popup
        $popup_id = isset( $_GET['popup'] ) ? intval( $_GET['popup'] ) : 0;
        $popup = get_post( $popup_id );

        if ( $popup && $popup->post_type === 'smartengage_popup' ) {
            // Display analytics for a specific popup
            include SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/partials/analytics-dashboard.php';
        } else {
            // Display overview of all popups
            $args = array(
                'post_type'      => 'smartengage_popup',
                'posts_per_page' => -1,
            );

            $popups = get_posts( $args );
            $analytics = new SmartEngage_Popups_Analytics();

            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                
                <?php if ( empty( $popups ) ) : ?>
                    <div class="notice notice-info">
                        <p><?php _e( 'You haven\'t created any popups yet.', 'smartengage-popups' ); ?></p>
                    </div>
                    <p>
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=smartengage_popup' ) ); ?>" class="button button-primary">
                            <?php _e( 'Create Your First Popup', 'smartengage-popups' ); ?>
                        </a>
                    </p>
                <?php else : ?>
                    <div class="smartengage-analytics-overview">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Popup', 'smartengage-popups' ); ?></th>
                                    <th><?php _e( 'Status', 'smartengage-popups' ); ?></th>
                                    <th><?php _e( 'Views', 'smartengage-popups' ); ?></th>
                                    <th><?php _e( 'Clicks', 'smartengage-popups' ); ?></th>
                                    <th><?php _e( 'Conversion Rate', 'smartengage-popups' ); ?></th>
                                    <th><?php _e( 'Actions', 'smartengage-popups' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $popups as $popup ) : 
                                    $views = $analytics->get_popup_views( $popup->ID );
                                    $clicks = $analytics->get_popup_clicks( $popup->ID );
                                    $conversion_rate = $views > 0 ? round( ( $clicks / $views ) * 100, 2 ) : 0;
                                    $status = get_post_meta( $popup->ID, '_popup_status', true );
                                    ?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $popup->ID . '&action=edit' ) ); ?>">
                                                    <?php echo esc_html( $popup->post_title ); ?>
                                                </a>
                                            </strong>
                                        </td>
                                        <td>
                                            <?php if ( $status === 'active' ) : ?>
                                                <span class="status-active"><?php _e( 'Active', 'smartengage-popups' ); ?></span>
                                            <?php else : ?>
                                                <span class="status-inactive"><?php _e( 'Inactive', 'smartengage-popups' ); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo esc_html( $views ); ?></td>
                                        <td><?php echo esc_html( $clicks ); ?></td>
                                        <td><?php echo esc_html( $conversion_rate ); ?>%</td>
                                        <td>
                                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=smartengage-analytics&popup=' . $popup->ID ) ); ?>" class="button button-small">
                                                <?php _e( 'View Details', 'smartengage-popups' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    }
}

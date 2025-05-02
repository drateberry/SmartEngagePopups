<?php
/**
 * Class responsible for handling metaboxes.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class for handling metaboxes.
 */
class SmartEngage_Popups_Metabox {

    /**
     * Add meta boxes for the custom post type.
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {
        add_meta_box(
            'smartengage_popup_settings',
            __( 'Popup Settings', 'smartengage-popups' ),
            array( $this, 'render_settings_metabox' ),
            'smartengage_popup',
            'normal',
            'high'
        );
        
        add_meta_box(
            'smartengage_popup_builder',
            __( 'Popup Design Builder', 'smartengage-popups' ),
            array( $this, 'render_builder_metabox' ),
            'smartengage_popup',
            'normal',
            'high'
        );

        add_meta_box(
            'smartengage_popup_triggers',
            __( 'Trigger Conditions', 'smartengage-popups' ),
            array( $this, 'render_triggers_metabox' ),
            'smartengage_popup',
            'normal',
            'high'
        );

        add_meta_box(
            'smartengage_popup_frequency',
            __( 'Frequency Rules', 'smartengage-popups' ),
            array( $this, 'render_frequency_metabox' ),
            'smartengage_popup',
            'normal',
            'high'
        );

        add_meta_box(
            'smartengage_popup_targeting',
            __( 'Targeting Options', 'smartengage-popups' ),
            array( $this, 'render_targeting_metabox' ),
            'smartengage_popup',
            'normal',
            'high'
        );

        add_meta_box(
            'smartengage_popup_analytics',
            __( 'Popup Analytics', 'smartengage-popups' ),
            array( $this, 'render_analytics_metabox' ),
            'smartengage_popup',
            'side',
            'default'
        );
    }

    /**
     * Render the settings metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_settings_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_popup_settings_metabox', 'smartengage_popup_settings_nonce' );

        // Get saved values
        $popup_type = get_post_meta( $post->ID, '_popup_type', true ) ?: 'slide-in';
        $popup_position = get_post_meta( $post->ID, '_popup_position', true ) ?: 'bottom-right';
        $button_text = get_post_meta( $post->ID, '_button_text', true ) ?: '';
        $button_url = get_post_meta( $post->ID, '_button_url', true ) ?: '';
        $button_color = get_post_meta( $post->ID, '_button_color', true ) ?: '#4CAF50';
        $popup_status = get_post_meta( $post->ID, '_popup_status', true ) ?: 'inactive';

        include SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/partials/popup-metabox-template.php';
    }

    /**
     * Render the triggers metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_triggers_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_popup_triggers_metabox', 'smartengage_popup_triggers_nonce' );

        // Get saved values
        $time_delay = get_post_meta( $post->ID, '_time_delay', true ) ?: '';
        $scroll_depth = get_post_meta( $post->ID, '_scroll_depth', true ) ?: '';
        $exit_intent = get_post_meta( $post->ID, '_exit_intent', true ) ?: 0;
        $page_views = get_post_meta( $post->ID, '_page_views', true ) ?: '';
        $specific_url = get_post_meta( $post->ID, '_specific_url', true ) ?: '';
        $user_status = get_post_meta( $post->ID, '_user_status', true ) ?: 'all';
        $conditions_match = get_post_meta( $post->ID, '_conditions_match', true ) ?: 'any';

        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="time_delay"><?php esc_html_e( 'Time on Page (seconds)', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="number" id="time_delay" name="time_delay" value="<?php echo esc_attr( $time_delay ); ?>" min="0" class="small-text">
                    <p class="description"><?php esc_html_e( 'Show popup after this many seconds on the page. Leave empty to disable.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="scroll_depth"><?php esc_html_e( 'Scroll Depth (%)', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="number" id="scroll_depth" name="scroll_depth" value="<?php echo esc_attr( $scroll_depth ); ?>" min="0" max="100" class="small-text">
                    <p class="description"><?php esc_html_e( 'Show popup after user scrolls this percentage of the page. Leave empty to disable.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'Exit Intent', 'smartengage-popups' ); ?>
                </th>
                <td>
                    <label for="exit_intent">
                        <input type="checkbox" id="exit_intent" name="exit_intent" value="1" <?php checked( $exit_intent, 1 ); ?>>
                        <?php esc_html_e( 'Show popup when user is about to leave the page', 'smartengage-popups' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="page_views"><?php esc_html_e( 'Page Views', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="number" id="page_views" name="page_views" value="<?php echo esc_attr( $page_views ); ?>" min="1" class="small-text">
                    <p class="description"><?php esc_html_e( 'Show popup after this many page views. Leave empty to disable.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="specific_url"><?php esc_html_e( 'Specific URL Path', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="text" id="specific_url" name="specific_url" value="<?php echo esc_attr( $specific_url ); ?>" class="regular-text">
                    <p class="description"><?php esc_html_e( 'Show popup only on this URL path (e.g. /contact/). Use * for wildcards. Leave empty to show on all pages.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="user_status"><?php esc_html_e( 'User Status', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <select id="user_status" name="user_status">
                        <option value="all" <?php selected( $user_status, 'all' ); ?>><?php esc_html_e( 'All Users', 'smartengage-popups' ); ?></option>
                        <option value="logged_in" <?php selected( $user_status, 'logged_in' ); ?>><?php esc_html_e( 'Logged-in Users Only', 'smartengage-popups' ); ?></option>
                        <option value="logged_out" <?php selected( $user_status, 'logged_out' ); ?>><?php esc_html_e( 'Logged-out Users Only', 'smartengage-popups' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="conditions_match"><?php esc_html_e( 'Conditions Match', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <select id="conditions_match" name="conditions_match">
                        <option value="any" <?php selected( $conditions_match, 'any' ); ?>><?php esc_html_e( 'Any (OR Logic)', 'smartengage-popups' ); ?></option>
                        <option value="all" <?php selected( $conditions_match, 'all' ); ?>><?php esc_html_e( 'All (AND Logic)', 'smartengage-popups' ); ?></option>
                    </select>
                    <p class="description"><?php esc_html_e( 'How to evaluate multiple conditions', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render the frequency metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_frequency_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_popup_frequency_metabox', 'smartengage_popup_frequency_nonce' );

        // Get saved values
        $frequency = get_post_meta( $post->ID, '_frequency', true ) ?: 'once_per_session';
        $days_between = get_post_meta( $post->ID, '_days_between', true ) ?: '7';
        $max_impressions = get_post_meta( $post->ID, '_max_impressions', true ) ?: '';

        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="frequency"><?php esc_html_e( 'Frequency', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <select id="frequency" name="frequency">
                        <option value="once_per_session" <?php selected( $frequency, 'once_per_session' ); ?>><?php esc_html_e( 'Once Per Session', 'smartengage-popups' ); ?></option>
                        <option value="every_x_days" <?php selected( $frequency, 'every_x_days' ); ?>><?php esc_html_e( 'Every X Days', 'smartengage-popups' ); ?></option>
                        <option value="every_page" <?php selected( $frequency, 'every_page' ); ?>><?php esc_html_e( 'Every Page Load', 'smartengage-popups' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr class="days-between-row" <?php echo $frequency !== 'every_x_days' ? 'style="display: none;"' : ''; ?>>
                <th scope="row">
                    <label for="days_between"><?php esc_html_e( 'Days Between Shows', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="number" id="days_between" name="days_between" value="<?php echo esc_attr( $days_between ); ?>" min="1" class="small-text">
                    <p class="description"><?php esc_html_e( 'Number of days to wait before showing the popup again to the same user', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="max_impressions"><?php esc_html_e( 'Max Impressions Per User', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="number" id="max_impressions" name="max_impressions" value="<?php echo esc_attr( $max_impressions ); ?>" min="1" class="small-text">
                    <p class="description"><?php esc_html_e( 'Maximum number of times to show this popup to a single user. Leave empty for unlimited.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render the targeting metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_targeting_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_popup_targeting_metabox', 'smartengage_popup_targeting_nonce' );

        // Get saved values
        $device_type = get_post_meta( $post->ID, '_device_type', true ) ?: 'all';
        $referrer_url = get_post_meta( $post->ID, '_referrer_url', true ) ?: '';
        $cookie_name = get_post_meta( $post->ID, '_cookie_name', true ) ?: '';
        $cookie_value = get_post_meta( $post->ID, '_cookie_value', true ) ?: '';
        $user_roles = get_post_meta( $post->ID, '_user_roles', true ) ?: array();

        if (!is_array($user_roles)) {
            $user_roles = array();
        }

        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="device_type"><?php esc_html_e( 'Device Type', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <select id="device_type" name="device_type">
                        <option value="all" <?php selected( $device_type, 'all' ); ?>><?php esc_html_e( 'All Devices', 'smartengage-popups' ); ?></option>
                        <option value="desktop" <?php selected( $device_type, 'desktop' ); ?>><?php esc_html_e( 'Desktop Only', 'smartengage-popups' ); ?></option>
                        <option value="mobile" <?php selected( $device_type, 'mobile' ); ?>><?php esc_html_e( 'Mobile Only', 'smartengage-popups' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="referrer_url"><?php esc_html_e( 'Referrer URL', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <input type="text" id="referrer_url" name="referrer_url" value="<?php echo esc_attr( $referrer_url ); ?>" class="regular-text">
                    <p class="description"><?php esc_html_e( 'Show popup only if user came from this URL. Use * for wildcards. Leave empty to disable.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cookie_name"><?php esc_html_e( 'Cookie-based Targeting', 'smartengage-popups' ); ?></label>
                </th>
                <td>
                    <label for="cookie_name"><?php esc_html_e( 'Cookie Name:', 'smartengage-popups' ); ?></label>
                    <input type="text" id="cookie_name" name="cookie_name" value="<?php echo esc_attr( $cookie_name ); ?>" class="medium-text">
                    <br>
                    <label for="cookie_value"><?php esc_html_e( 'Cookie Value:', 'smartengage-popups' ); ?></label>
                    <input type="text" id="cookie_value" name="cookie_value" value="<?php echo esc_attr( $cookie_value ); ?>" class="medium-text">
                    <p class="description"><?php esc_html_e( 'Show popup only if this cookie with this value exists. Leave empty to disable.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php esc_html_e( 'User Roles', 'smartengage-popups' ); ?>
                </th>
                <td>
                    <?php
                    // Get all roles
                    $roles = get_editable_roles();
                    foreach ($roles as $role_id => $role_info) :
                    ?>
                        <label>
                            <input type="checkbox" name="user_roles[]" value="<?php echo esc_attr($role_id); ?>" <?php checked(in_array($role_id, $user_roles), true); ?>>
                            <?php echo esc_html($role_info['name']); ?>
                        </label><br>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e( 'Show popup only to users with selected roles. Leave all unchecked to show to all roles.', 'smartengage-popups' ); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render the popup builder metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_builder_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'smartengage_popup_builder_metabox', 'smartengage_popup_builder_nonce' );
        
        // Include the builder template
        include SMARTENGAGE_POPUPS_PLUGIN_DIR . 'admin/partials/popup-builder-template.php';
    }

    /**
     * Render the analytics metabox.
     *
     * @since    1.0.0
     * @param    WP_Post $post The post object.
     */
    public function render_analytics_metabox( $post ) {
        $analytics = new SmartEngage_Popups_Analytics();
        $views = $analytics->get_popup_views( $post->ID );
        $clicks = $analytics->get_popup_clicks( $post->ID );
        $conversion_rate = 0;
        
        if ($views > 0) {
            $conversion_rate = round(($clicks / $views) * 100, 2);
        }
        
        ?>
        <div class="smartengage-analytics">
            <div class="analytics-item">
                <span class="analytics-number"><?php echo esc_html( $views ); ?></span>
                <span class="analytics-label"><?php esc_html_e( 'Views', 'smartengage-popups' ); ?></span>
            </div>
            <div class="analytics-item">
                <span class="analytics-number"><?php echo esc_html( $clicks ); ?></span>
                <span class="analytics-label"><?php esc_html_e( 'Clicks', 'smartengage-popups' ); ?></span>
            </div>
            <div class="analytics-item">
                <span class="analytics-number"><?php echo esc_html( $conversion_rate ); ?>%</span>
                <span class="analytics-label"><?php esc_html_e( 'Conversion Rate', 'smartengage-popups' ); ?></span>
            </div>
            <p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=smartengage-analytics&popup=' . $post->ID ) ); ?>"><?php esc_html_e( 'View Detailed Analytics', 'smartengage-popups' ); ?></a>
            </p>
        </div>
        <?php
    }

    /**
     * Save the metabox data.
     *
     * @since    1.0.0
     * @param    int     $post_id    The ID of the post being saved.
     * @param    WP_Post $post       The post object.
     */
    public function save_metabox( $post_id, $post ) {
        // Check if our nonces are set
        $nonces = array(
            'smartengage_popup_settings_nonce',
            'smartengage_popup_triggers_nonce',
            'smartengage_popup_frequency_nonce',
            'smartengage_popup_targeting_nonce',
            'smartengage_popup_builder_nonce'
        );

        // Only check nonces that are set
        foreach ($nonces as $nonce) {
            if (isset($_POST[$nonce])) {
                // Verify the nonce
                $nonce_action = str_replace('_nonce', '_metabox', $nonce);
                if (!wp_verify_nonce($_POST[$nonce], $nonce_action)) {
                    return;
                }
            }
        }

        // Check if an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Make sure we're saving the correct post type
        if ('smartengage_popup' !== $post->post_type) {
            return;
        }

        // Settings
        if (isset($_POST['popup_type'])) {
            update_post_meta($post_id, '_popup_type', sanitize_text_field($_POST['popup_type']));
        }

        if (isset($_POST['popup_position'])) {
            update_post_meta($post_id, '_popup_position', sanitize_text_field($_POST['popup_position']));
        }

        if (isset($_POST['button_text'])) {
            update_post_meta($post_id, '_button_text', sanitize_text_field($_POST['button_text']));
        }

        if (isset($_POST['button_url'])) {
            update_post_meta($post_id, '_button_url', esc_url_raw($_POST['button_url']));
        }

        if (isset($_POST['button_color'])) {
            update_post_meta($post_id, '_button_color', sanitize_hex_color($_POST['button_color']));
        }

        if (isset($_POST['popup_status'])) {
            update_post_meta($post_id, '_popup_status', sanitize_text_field($_POST['popup_status']));
        }

        // Triggers
        $time_delay = isset($_POST['time_delay']) ? absint($_POST['time_delay']) : '';
        update_post_meta($post_id, '_time_delay', $time_delay);

        $scroll_depth = isset($_POST['scroll_depth']) ? absint($_POST['scroll_depth']) : '';
        update_post_meta($post_id, '_scroll_depth', $scroll_depth);

        $exit_intent = isset($_POST['exit_intent']) ? 1 : 0;
        update_post_meta($post_id, '_exit_intent', $exit_intent);

        $page_views = isset($_POST['page_views']) ? absint($_POST['page_views']) : '';
        update_post_meta($post_id, '_page_views', $page_views);

        if (isset($_POST['specific_url'])) {
            update_post_meta($post_id, '_specific_url', sanitize_text_field($_POST['specific_url']));
        }

        if (isset($_POST['user_status'])) {
            update_post_meta($post_id, '_user_status', sanitize_text_field($_POST['user_status']));
        }

        if (isset($_POST['conditions_match'])) {
            update_post_meta($post_id, '_conditions_match', sanitize_text_field($_POST['conditions_match']));
        }

        // Frequency
        if (isset($_POST['frequency'])) {
            update_post_meta($post_id, '_frequency', sanitize_text_field($_POST['frequency']));
        }

        $days_between = isset($_POST['days_between']) ? absint($_POST['days_between']) : 7;
        update_post_meta($post_id, '_days_between', $days_between);

        $max_impressions = isset($_POST['max_impressions']) ? absint($_POST['max_impressions']) : '';
        update_post_meta($post_id, '_max_impressions', $max_impressions);

        // Targeting
        if (isset($_POST['device_type'])) {
            update_post_meta($post_id, '_device_type', sanitize_text_field($_POST['device_type']));
        }

        if (isset($_POST['referrer_url'])) {
            update_post_meta($post_id, '_referrer_url', sanitize_text_field($_POST['referrer_url']));
        }

        if (isset($_POST['cookie_name'])) {
            update_post_meta($post_id, '_cookie_name', sanitize_text_field($_POST['cookie_name']));
        }

        if (isset($_POST['cookie_value'])) {
            update_post_meta($post_id, '_cookie_value', sanitize_text_field($_POST['cookie_value']));
        }

        $user_roles = isset($_POST['user_roles']) ? (array) $_POST['user_roles'] : array();
        $sanitized_roles = array();
        
        foreach ($user_roles as $role) {
            $sanitized_roles[] = sanitize_text_field($role);
        }
        
        update_post_meta($post_id, '_user_roles', $sanitized_roles);
        
        // Save popup builder design data
        if (isset($_POST['popup_design_json'])) {
            // Sanitize the JSON data
            $design_json = stripslashes($_POST['popup_design_json']);
            
            // Validate that it's valid JSON
            json_decode($design_json);
            if (json_last_error() === JSON_ERROR_NONE) {
                update_post_meta($post_id, '_popup_design_json', $design_json);
            }
        }
    }
}

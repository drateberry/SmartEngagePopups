<?php
/**
 * Settings page view
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get settings
$data_retention = get_option( 'smartengage_data_retention', 90 );
$disable_on_mobile = get_option( 'smartengage_disable_on_mobile', 'no' );
$disable_for_admin = get_option( 'smartengage_disable_for_admin', 'no' );
$animation_speed = get_option( 'smartengage_animation_speed', 'normal' );
$css_load_method = get_option( 'smartengage_css_load_method', 'footer' );

// Process form submission
if ( isset( $_POST['smartengage_settings_nonce'] ) && wp_verify_nonce( $_POST['smartengage_settings_nonce'], 'smartengage_save_settings' ) ) {
    // Validate and save settings
    if ( isset( $_POST['smartengage_data_retention'] ) ) {
        $data_retention = absint( $_POST['smartengage_data_retention'] );
        update_option( 'smartengage_data_retention', $data_retention );
    }
    
    if ( isset( $_POST['smartengage_disable_on_mobile'] ) ) {
        $disable_on_mobile = sanitize_text_field( $_POST['smartengage_disable_on_mobile'] );
        update_option( 'smartengage_disable_on_mobile', $disable_on_mobile );
    } else {
        update_option( 'smartengage_disable_on_mobile', 'no' );
    }
    
    if ( isset( $_POST['smartengage_disable_for_admin'] ) ) {
        $disable_for_admin = sanitize_text_field( $_POST['smartengage_disable_for_admin'] );
        update_option( 'smartengage_disable_for_admin', $disable_for_admin );
    } else {
        update_option( 'smartengage_disable_for_admin', 'no' );
    }
    
    if ( isset( $_POST['smartengage_animation_speed'] ) ) {
        $animation_speed = sanitize_text_field( $_POST['smartengage_animation_speed'] );
        update_option( 'smartengage_animation_speed', $animation_speed );
    }
    
    if ( isset( $_POST['smartengage_css_load_method'] ) ) {
        $css_load_method = sanitize_text_field( $_POST['smartengage_css_load_method'] );
        update_option( 'smartengage_css_load_method', $css_load_method );
    }
    
    // Show success message
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'smartengage-popups' ) . '</p></div>';
}
?>

<div class="wrap smartengage-settings-page">
    <h1><?php esc_html_e( 'SmartEngage Settings', 'smartengage-popups' ); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'smartengage_save_settings', 'smartengage_settings_nonce' ); ?>
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="smartengage-data-retention"><?php esc_html_e( 'Data Retention Period', 'smartengage-popups' ); ?></label>
                    </th>
                    <td>
                        <input type="number" id="smartengage-data-retention" name="smartengage_data_retention" value="<?php echo esc_attr( $data_retention ); ?>" min="1" step="1" class="small-text" />
                        <span class="description"><?php esc_html_e( 'days', 'smartengage-popups' ); ?></span>
                        <p class="description"><?php esc_html_e( 'Analytics data older than this will be automatically deleted.', 'smartengage-popups' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php esc_html_e( 'Global Popup Preferences', 'smartengage-popups' ); ?></th>
                    <td>
                        <fieldset>
                            <label for="smartengage-disable-on-mobile">
                                <input type="checkbox" id="smartengage-disable-on-mobile" name="smartengage_disable_on_mobile" value="yes" <?php checked( $disable_on_mobile, 'yes' ); ?> />
                                <?php esc_html_e( 'Disable all popups on mobile devices', 'smartengage-popups' ); ?>
                            </label>
                            <br>
                            
                            <label for="smartengage-disable-for-admin">
                                <input type="checkbox" id="smartengage-disable-for-admin" name="smartengage_disable_for_admin" value="yes" <?php checked( $disable_for_admin, 'yes' ); ?> />
                                <?php esc_html_e( 'Disable all popups for administrators', 'smartengage-popups' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smartengage-animation-speed"><?php esc_html_e( 'Animation Speed', 'smartengage-popups' ); ?></label>
                    </th>
                    <td>
                        <select id="smartengage-animation-speed" name="smartengage_animation_speed">
                            <option value="fast" <?php selected( $animation_speed, 'fast' ); ?>><?php esc_html_e( 'Fast', 'smartengage-popups' ); ?></option>
                            <option value="normal" <?php selected( $animation_speed, 'normal' ); ?>><?php esc_html_e( 'Normal', 'smartengage-popups' ); ?></option>
                            <option value="slow" <?php selected( $animation_speed, 'slow' ); ?>><?php esc_html_e( 'Slow', 'smartengage-popups' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'The speed of popup animations.', 'smartengage-popups' ); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="smartengage-css-load-method"><?php esc_html_e( 'CSS Loading Method', 'smartengage-popups' ); ?></label>
                    </th>
                    <td>
                        <select id="smartengage-css-load-method" name="smartengage_css_load_method">
                            <option value="header" <?php selected( $css_load_method, 'header' ); ?>><?php esc_html_e( 'Header (Faster but blocking)', 'smartengage-popups' ); ?></option>
                            <option value="footer" <?php selected( $css_load_method, 'footer' ); ?>><?php esc_html_e( 'Footer (Non-blocking)', 'smartengage-popups' ); ?></option>
                            <option value="inline" <?php selected( $css_load_method, 'inline' ); ?>><?php esc_html_e( 'Inline (Fastest, no separate request)', 'smartengage-popups' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'How to load the popup CSS for optimal performance.', 'smartengage-popups' ); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <h2><?php esc_html_e( 'Data Management', 'smartengage-popups' ); ?></h2>
        
        <p>
            <button type="button" id="smartengage-clear-analytics" class="button">
                <?php esc_html_e( 'Clear All Analytics Data', 'smartengage-popups' ); ?>
            </button>
            
            <span class="spinner"></span>
        </p>
        <p class="description">
            <?php esc_html_e( 'Warning: This will permanently delete all popup analytics data. This action cannot be undone.', 'smartengage-popups' ); ?>
        </p>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'smartengage-popups' ); ?>" />
        </p>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Clear analytics data
        $('#smartengage-clear-analytics').on('click', function() {
            if (confirm('<?php echo esc_js( __( 'Are you sure you want to delete all analytics data? This action cannot be undone.', 'smartengage-popups' ) ); ?>')) {
                var $button = $(this);
                var $spinner = $button.next('.spinner');
                
                $button.prop('disabled', true);
                $spinner.css('visibility', 'visible');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'smartengage_clear_analytics',
                        nonce: '<?php echo esc_js( wp_create_nonce( 'smartengage_admin_nonce' ) ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('<?php echo esc_js( __( 'Analytics data has been cleared successfully.', 'smartengage-popups' ) ); ?>');
                        } else {
                            alert('<?php echo esc_js( __( 'Error: Could not clear analytics data.', 'smartengage-popups' ) ); ?>');
                        }
                        
                        $button.prop('disabled', false);
                        $spinner.css('visibility', 'hidden');
                    },
                    error: function() {
                        alert('<?php echo esc_js( __( 'Error: Could not clear analytics data.', 'smartengage-popups' ) ); ?>');
                        $button.prop('disabled', false);
                        $spinner.css('visibility', 'hidden');
                    }
                });
            }
        });
    });
</script>

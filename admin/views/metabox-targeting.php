<?php
/**
 * Targeting metabox view
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="smartengage-metabox-container">
    <div class="smartengage-field-group">
        <label for="smartengage-device-type" class="smartengage-field-label">
            <?php esc_html_e( 'Device Type', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-device-type" name="_smartengage_device_type" class="smartengage-select">
                <option value="all" <?php selected( $device_type, 'all' ); ?>>
                    <?php esc_html_e( 'All Devices', 'smartengage-popups' ); ?>
                </option>
                <option value="desktop" <?php selected( $device_type, 'desktop' ); ?>>
                    <?php esc_html_e( 'Desktop Only', 'smartengage-popups' ); ?>
                </option>
                <option value="mobile" <?php selected( $device_type, 'mobile' ); ?>>
                    <?php esc_html_e( 'Mobile Only', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Choose which device types this popup should appear on.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-user-logged-in" class="smartengage-field-label">
            <?php esc_html_e( 'User Login Status', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-user-logged-in" name="_smartengage_user_logged_in" class="smartengage-select">
                <option value="all" <?php selected( $user_logged_in, 'all' ); ?>>
                    <?php esc_html_e( 'All Users', 'smartengage-popups' ); ?>
                </option>
                <option value="logged_in" <?php selected( $user_logged_in, 'logged_in' ); ?>>
                    <?php esc_html_e( 'Logged-in Users Only', 'smartengage-popups' ); ?>
                </option>
                <option value="logged_out" <?php selected( $user_logged_in, 'logged_out' ); ?>>
                    <?php esc_html_e( 'Logged-out Users Only', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Choose which user login states this popup should appear for.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group" id="smartengage-user-roles-group">
        <label class="smartengage-field-label">
            <?php esc_html_e( 'User Roles', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <?php foreach ( $roles as $role_id => $role_name ) : ?>
                <label class="smartengage-checkbox-label">
                    <input type="checkbox" 
                           name="_smartengage_user_roles[]" 
                           value="<?php echo esc_attr( $role_id ); ?>" 
                           <?php checked( is_array( $user_roles ) && in_array( $role_id, $user_roles, true ) ); ?> />
                    <?php echo esc_html( $role_name ); ?>
                </label>
            <?php endforeach; ?>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Select which user roles this popup should appear for. Only applies if "Logged-in Users Only" is selected above.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-referrer-url" class="smartengage-field-label">
            <?php esc_html_e( 'Referrer URL', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <input type="text" id="smartengage-referrer-url" name="_smartengage_referrer_url" value="<?php echo esc_attr( $referrer_url ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., google.com, facebook.com', 'smartengage-popups' ); ?>" />
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Only show the popup if the visitor comes from a specific referring site. Use * as wildcard. Leave empty to show for all referrers.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-cookie-targeting" class="smartengage-checkbox-label">
            <input type="checkbox" id="smartengage-cookie-targeting" name="_smartengage_cookie_targeting" value="enabled" <?php checked( $cookie_targeting, 'enabled' ); ?> />
            <?php esc_html_e( 'Cookie Targeting', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input" id="smartengage-cookie-settings">
            <input type="text" id="smartengage-cookie-name" name="_smartengage_cookie_name" value="<?php echo esc_attr( $cookie_name ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., user_visited_pricing', 'smartengage-popups' ); ?>" />
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Only show the popup if a specific cookie exists in the visitor\'s browser.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Show/hide user roles based on login status selection
        function toggleUserRoles() {
            var loginStatus = $('#smartengage-user-logged-in').val();
            
            if (loginStatus === 'logged_in') {
                $('#smartengage-user-roles-group').show();
            } else {
                $('#smartengage-user-roles-group').hide();
            }
        }
        
        // Show/hide cookie name field based on cookie targeting checkbox
        function toggleCookieSettings() {
            if ($('#smartengage-cookie-targeting').is(':checked')) {
                $('#smartengage-cookie-settings').show();
            } else {
                $('#smartengage-cookie-settings').hide();
            }
        }
        
        // Run on page load
        toggleUserRoles();
        toggleCookieSettings();
        
        // Run when selections change
        $('#smartengage-user-logged-in').on('change', toggleUserRoles);
        $('#smartengage-cookie-targeting').on('change', toggleCookieSettings);
    });
</script>

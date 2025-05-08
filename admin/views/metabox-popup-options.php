<?php
/**
 * Popup Options metabox view
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
        <label for="smartengage-popup-status" class="smartengage-field-label">
            <?php esc_html_e( 'Popup Status', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-popup-status" name="_smartengage_popup_status" class="smartengage-select">
                <option value="enabled" <?php selected( $popup_status, 'enabled' ); ?>>
                    <?php esc_html_e( 'Enabled', 'smartengage-popups' ); ?>
                </option>
                <option value="disabled" <?php selected( $popup_status, 'disabled' ); ?>>
                    <?php esc_html_e( 'Disabled', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Enable or disable this popup.', 'smartengage-popups' ); ?>
            </p>
        </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Show/hide position field based on popup type
        function togglePositionField() {
            var popupType = $('#smartengage-popup-type').val();
            
            if (popupType === 'full-screen') {
                $('#smartengage-position-field').hide();
            } else {
                $('#smartengage-position-field').show();
            }
        }
        
        // Run on page load
        togglePositionField();
        
        // Run when popup type changes
        $('#smartengage-popup-type').on('change', togglePositionField);
    });
</script>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-popup-type" class="smartengage-field-label">
            <?php esc_html_e( 'Popup Type', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-popup-type" name="_smartengage_popup_type" class="smartengage-select">
                <option value="slide-in" <?php selected( $popup_type, 'slide-in' ); ?>>
                    <?php esc_html_e( 'Slide-in', 'smartengage-popups' ); ?>
                </option>
                <option value="full-screen" <?php selected( $popup_type, 'full-screen' ); ?>>
                    <?php esc_html_e( 'Full-screen', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Select the type of popup display.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group" id="smartengage-position-field">
        <label for="smartengage-popup-position" class="smartengage-field-label">
            <?php esc_html_e( 'Popup Position', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-popup-position" name="_smartengage_popup_position" class="smartengage-select">
                <option value="bottom-right" <?php selected( $popup_position, 'bottom-right' ); ?>>
                    <?php esc_html_e( 'Bottom Right', 'smartengage-popups' ); ?>
                </option>
                <option value="bottom-left" <?php selected( $popup_position, 'bottom-left' ); ?>>
                    <?php esc_html_e( 'Bottom Left', 'smartengage-popups' ); ?>
                </option>
                <option value="center" <?php selected( $popup_position, 'center' ); ?>>
                    <?php esc_html_e( 'Center', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Choose where the popup should appear on the screen.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-cta-text" class="smartengage-field-label">
            <?php esc_html_e( 'Primary CTA Button', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <input type="text" id="smartengage-cta-text" name="_smartengage_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Button Text', 'smartengage-popups' ); ?>" />
            <input type="url" id="smartengage-cta-url" name="_smartengage_cta_url" value="<?php echo esc_url( $cta_url ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Button URL', 'smartengage-popups' ); ?>" />
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Enter the text and URL for the primary call-to-action button.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group">
        <label for="smartengage-cta2-text" class="smartengage-field-label">
            <?php esc_html_e( 'Secondary CTA Button (Optional)', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <input type="text" id="smartengage-cta2-text" name="_smartengage_cta2_text" value="<?php echo esc_attr( $cta2_text ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Button Text', 'smartengage-popups' ); ?>" />
            <input type="url" id="smartengage-cta2-url" name="_smartengage_cta2_url" value="<?php echo esc_url( $cta2_url ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Button URL', 'smartengage-popups' ); ?>" />
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Enter the text and URL for an optional secondary button.', 'smartengage-popups' ); ?>
            </p>
        </div>
    
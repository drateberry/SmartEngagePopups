<?php
/**
 * Popup settings metabox template.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="smartengage-meta-box">
    <div class="smartengage-form-row">
        <label for="popup_type"><?php esc_html_e( 'Popup Type', 'smartengage-popups' ); ?></label>
        <select id="popup_type" name="popup_type">
            <option value="slide-in" <?php selected( $popup_type, 'slide-in' ); ?>><?php esc_html_e( 'Slide-in Popup', 'smartengage-popups' ); ?></option>
            <option value="full-screen" <?php selected( $popup_type, 'full-screen' ); ?>><?php esc_html_e( 'Full-screen Overlay', 'smartengage-popups' ); ?></option>
        </select>
    </div>

    <div class="smartengage-form-row popup-position-field" <?php if ( $popup_type !== 'slide-in' ) echo 'style="display: none;"'; ?>>
        <label for="popup_position"><?php esc_html_e( 'Popup Position', 'smartengage-popups' ); ?></label>
        <select id="popup_position" name="popup_position">
            <option value="bottom-right" <?php selected( $popup_position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'smartengage-popups' ); ?></option>
            <option value="bottom-left" <?php selected( $popup_position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'smartengage-popups' ); ?></option>
            <option value="center" <?php selected( $popup_position, 'center' ); ?>><?php esc_html_e( 'Center', 'smartengage-popups' ); ?></option>
        </select>
    </div>

    <div class="smartengage-form-row">
        <label for="button_text"><?php esc_html_e( 'Button Text', 'smartengage-popups' ); ?></label>
        <input type="text" id="button_text" name="button_text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php esc_attr_e( 'e.g., "Sign Up Now"', 'smartengage-popups' ); ?>">
        <p class="description"><?php esc_html_e( 'Leave empty to hide the button', 'smartengage-popups' ); ?></p>
    </div>

    <div class="smartengage-form-row">
        <label for="button_url"><?php esc_html_e( 'Button URL', 'smartengage-popups' ); ?></label>
        <input type="text" id="button_url" name="button_url" value="<?php echo esc_url( $button_url ); ?>" placeholder="https://">
    </div>

    <div class="smartengage-form-row">
        <label for="button_color"><?php esc_html_e( 'Button Color', 'smartengage-popups' ); ?></label>
        <input type="text" id="button_color" name="button_color" value="<?php echo esc_attr( $button_color ); ?>" class="color-picker">
    </div>

    <div class="smartengage-form-row">
        <label for="popup_status"><?php esc_html_e( 'Popup Status', 'smartengage-popups' ); ?></label>
        <select id="popup_status" name="popup_status">
            <option value="active" <?php selected( $popup_status, 'active' ); ?>><?php esc_html_e( 'Active', 'smartengage-popups' ); ?></option>
            <option value="inactive" <?php selected( $popup_status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'smartengage-popups' ); ?></option>
        </select>
    </div>

    <div class="smartengage-form-row">
        <p class="description"><?php esc_html_e( 'Add a featured image to display an image in your popup (optional).', 'smartengage-popups' ); ?></p>
    </div>

    <div class="smartengage-form-row">
        <button class="button popup-preview-button"><?php esc_html_e( 'Preview Popup', 'smartengage-popups' ); ?></button>
    </div>
</div>

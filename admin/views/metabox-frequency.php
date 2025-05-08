<?php
/**
 * Frequency Rules metabox view
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
        <label for="smartengage-frequency-rule" class="smartengage-field-label">
            <?php esc_html_e( 'Frequency', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-frequency-rule" name="_smartengage_frequency_rule" class="smartengage-select">
                <option value="once_session" <?php selected( $frequency_rule, 'once_session' ); ?>>
                    <?php esc_html_e( 'Once per session', 'smartengage-popups' ); ?>
                </option>
                <option value="every_page" <?php selected( $frequency_rule, 'every_page' ); ?>>
                    <?php esc_html_e( 'On every page load', 'smartengage-popups' ); ?>
                </option>
                <option value="days_between" <?php selected( $frequency_rule, 'days_between' ); ?>>
                    <?php esc_html_e( 'Once every X days', 'smartengage-popups' ); ?>
                </option>
                <option value="max_impressions" <?php selected( $frequency_rule, 'max_impressions' ); ?>>
                    <?php esc_html_e( 'Limited total impressions', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Control how often the same visitor sees this popup.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group smartengage-frequency-option" id="smartengage-days-between-group">
        <label for="smartengage-days-between" class="smartengage-field-label">
            <?php esc_html_e( 'Days Between Appearances', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <input type="number" id="smartengage-days-between" name="_smartengage_days_between" value="<?php echo esc_attr( $days_between ); ?>" min="1" step="1" class="small-text" />
            <span class="smartengage-field-suffix"><?php esc_html_e( 'days', 'smartengage-popups' ); ?></span>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'The number of days to wait before showing this popup to the same visitor again.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>

    <div class="smartengage-field-group smartengage-frequency-option" id="smartengage-max-impressions-group">
        <label for="smartengage-max-impressions" class="smartengage-field-label">
            <?php esc_html_e( 'Maximum Impressions', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <input type="number" id="smartengage-max-impressions" name="_smartengage_max_impressions" value="<?php echo esc_attr( $max_impressions ); ?>" min="1" step="1" class="small-text" />
            <span class="smartengage-field-suffix"><?php esc_html_e( 'impressions', 'smartengage-popups' ); ?></span>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'The maximum number of times to show this popup to the same visitor, ever.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Show/hide frequency options based on selected frequency rule
        function toggleFrequencyOptions() {
            var frequencyRule = $('#smartengage-frequency-rule').val();
            
            // Hide all option groups first
            $('.smartengage-frequency-option').hide();
            
            // Show the appropriate option group
            if (frequencyRule === 'days_between') {
                $('#smartengage-days-between-group').show();
            } else if (frequencyRule === 'max_impressions') {
                $('#smartengage-max-impressions-group').show();
            }
        }
        
        // Run on page load
        toggleFrequencyOptions();
        
        // Run when frequency rule changes
        $('#smartengage-frequency-rule').on('change', toggleFrequencyOptions);
    });
</script>

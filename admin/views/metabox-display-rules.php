<?php
/**
 * Display Rules metabox view
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
        <label for="smartengage-logic-operator" class="smartengage-field-label">
            <?php esc_html_e( 'Logic Operator', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <select id="smartengage-logic-operator" name="_smartengage_logic_operator" class="smartengage-select">
                <option value="OR" <?php selected( $logic_operator, 'OR' ); ?>>
                    <?php esc_html_e( 'OR - Trigger if ANY condition is met', 'smartengage-popups' ); ?>
                </option>
                <option value="AND" <?php selected( $logic_operator, 'AND' ); ?>>
                    <?php esc_html_e( 'AND - Trigger if ALL conditions are met', 'smartengage-popups' ); ?>
                </option>
            </select>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Choose how to evaluate multiple trigger conditions.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <h3 class="smartengage-section-title"><?php esc_html_e( 'Trigger Conditions', 'smartengage-popups' ); ?></h3>
        <p class="smartengage-section-description">
            <?php esc_html_e( 'Select the conditions that will trigger this popup to appear.', 'smartengage-popups' ); ?>
        </p>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-trigger-time" class="smartengage-checkbox-label">
            <input type="checkbox" id="smartengage-trigger-time" name="_smartengage_trigger_type[]" value="time" <?php checked( strpos( $trigger_type, 'time' ) !== false ); ?> />
            <?php esc_html_e( 'Time on Page', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input smartengage-trigger-option" id="smartengage-time-settings">
            <input type="number" id="smartengage-time-on-page" name="_smartengage_time_on_page" value="<?php echo esc_attr( $time_on_page ); ?>" min="0" step="1" class="small-text" />
            <span class="smartengage-field-suffix"><?php esc_html_e( 'seconds', 'smartengage-popups' ); ?></span>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Show the popup after the visitor has been on the page for this many seconds.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-trigger-scroll" class="smartengage-checkbox-label">
            <input type="checkbox" id="smartengage-trigger-scroll" name="_smartengage_trigger_type[]" value="scroll" <?php checked( strpos( $trigger_type, 'scroll' ) !== false ); ?> />
            <?php esc_html_e( 'Scroll Depth', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input smartengage-trigger-option" id="smartengage-scroll-settings">
            <input type="number" id="smartengage-scroll-depth" name="_smartengage_scroll_depth" value="<?php echo esc_attr( $scroll_depth ); ?>" min="0" max="100" step="1" class="small-text" />
            <span class="smartengage-field-suffix">%</span>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Show the popup after the visitor has scrolled this percentage of the page.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-trigger-exit" class="smartengage-checkbox-label">
            <input type="checkbox" id="smartengage-trigger-exit" name="_smartengage_exit_intent" value="enabled" <?php checked( $exit_intent, 'enabled' ); ?> />
            <?php esc_html_e( 'Exit Intent', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Show the popup when the visitor is about to leave the page (moves cursor to top of viewport or quick scroll up on mobile).', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-trigger-page-views" class="smartengage-checkbox-label">
            <input type="checkbox" id="smartengage-trigger-page-views" name="_smartengage_trigger_type[]" value="page_views" <?php checked( strpos( $trigger_type, 'page_views' ) !== false ); ?> />
            <?php esc_html_e( 'Page Views', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input smartengage-trigger-option" id="smartengage-page-views-settings">
            <input type="number" id="smartengage-page-views" name="_smartengage_page_views" value="<?php echo esc_attr( $page_views ); ?>" min="1" step="1" class="small-text" />
            <span class="smartengage-field-suffix"><?php esc_html_e( 'page views', 'smartengage-popups' ); ?></span>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Show the popup after the visitor has viewed this many pages on your site (uses cookies).', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <h3 class="smartengage-section-title"><?php esc_html_e( 'Page Targeting', 'smartengage-popups' ); ?></h3>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-target-urls" class="smartengage-field-label">
            <?php esc_html_e( 'Target URLs', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <textarea id="smartengage-target-urls" name="_smartengage_target_urls" rows="5" class="large-text"><?php echo esc_textarea( $target_urls ); ?></textarea>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Enter URLs where this popup should appear (one per line). Use * as wildcard. Leave empty to show on all pages.', 'smartengage-popups' ); ?>
                <br>
                <?php esc_html_e( 'Examples: /product/*, /checkout/, https://example.com/specific-page/', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
    
    <div class="smartengage-field-group">
        <label for="smartengage-target-post-types" class="smartengage-field-label">
            <?php esc_html_e( 'Target Post Types', 'smartengage-popups' ); ?>
        </label>
        <div class="smartengage-field-input">
            <?php foreach ( $post_types as $post_type ) : ?>
                <?php
                // Skip attachments and our own post type
                if ( in_array( $post_type->name, array( 'attachment', 'smartengage_popup' ), true ) ) {
                    continue;
                }
                ?>
                <label class="smartengage-checkbox-label">
                    <input type="checkbox" 
                           name="_smartengage_target_post_types[]" 
                           value="<?php echo esc_attr( $post_type->name ); ?>" 
                           <?php checked( is_array( $target_post_types ) && in_array( $post_type->name, $target_post_types, true ) ); ?> />
                    <?php echo esc_html( $post_type->labels->singular_name ); ?>
                </label>
            <?php endforeach; ?>
            <p class="smartengage-field-description">
                <?php esc_html_e( 'Select which post types this popup should appear on. Leave all unchecked to show on all post types.', 'smartengage-popups' ); ?>
            </p>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Show/hide trigger options based on checkboxes
        function toggleTriggerOptions() {
            $('.smartengage-trigger-option').each(function() {
                var $this = $(this);
                var $checkbox = $this.parent().find('input[type="checkbox"]');
                
                if ($checkbox.is(':checked')) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
        }
        
        // Run on page load
        toggleTriggerOptions();
        
        // Run when checkboxes change
        $('input[name="_smartengage_trigger_type[]"]').on('change', toggleTriggerOptions);
    });
</script>

<?php
/**
 * Preview metabox view
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="smartengage-metabox-container">
    <p><?php esc_html_e( 'Click the button below to preview your popup.', 'smartengage-popups' ); ?></p>
    
    <button type="button" id="smartengage-preview-button" class="button button-primary">
        <?php esc_html_e( 'Preview Popup', 'smartengage-popups' ); ?>
    </button>
    
    <div id="smartengage-preview-container" style="display: none;">
        <div id="smartengage-preview-popup" class="smartengage-popup-preview smartengage-popup-type-<?php echo esc_attr( $popup_type ); ?> smartengage-popup-position-<?php echo esc_attr( $popup_position ); ?>">
            <div class="smartengage-popup-content">
                <button class="smartengage-popup-close" aria-label="<?php esc_attr_e( 'Close popup', 'smartengage-popups' ); ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                
                <div class="smartengage-preview-info">
                    <div class="smartengage-preview-image">
                        <?php esc_html_e( 'Featured Image', 'smartengage-popups' ); ?>
                    </div>
                    
                    <div class="smartengage-preview-title">
                        <?php esc_html_e( 'Popup Title', 'smartengage-popups' ); ?>
                    </div>
                    
                    <div class="smartengage-preview-content">
                        <?php esc_html_e( 'Popup content will appear here.', 'smartengage-popups' ); ?>
                    </div>
                    
                    <div class="smartengage-preview-buttons">
                        <span class="smartengage-preview-button primary"><?php esc_html_e( 'Primary Button', 'smartengage-popups' ); ?></span>
                        <span class="smartengage-preview-button secondary"><?php esc_html_e( 'Secondary Button', 'smartengage-popups' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Preview button click
        $('#smartengage-preview-button').on('click', function() {
            var $container = $('#smartengage-preview-container');
            var $popup = $('#smartengage-preview-popup');
            
            // Get current popup type and position
            var popupType = $('#smartengage-popup-type').val();
            var popupPosition = $('#smartengage-popup-position').val();
            
            // Update preview classes
            $popup.removeClass('smartengage-popup-type-slide-in smartengage-popup-type-full-screen');
            $popup.removeClass('smartengage-popup-position-bottom-right smartengage-popup-position-bottom-left smartengage-popup-position-center');
            
            $popup.addClass('smartengage-popup-type-' + popupType);
            $popup.addClass('smartengage-popup-position-' + popupPosition);
            
            // Show preview
            $container.show();
            $popup.addClass('smartengage-popup-visible');
            
            // Update button text
            var ctaText = $('#smartengage-cta-text').val();
            var cta2Text = $('#smartengage-cta2-text').val();
            
            if (ctaText) {
                $('.smartengage-preview-button.primary').text(ctaText).show();
            } else {
                $('.smartengage-preview-button.primary').hide();
            }
            
            if (cta2Text) {
                $('.smartengage-preview-button.secondary').text(cta2Text).show();
            } else {
                $('.smartengage-preview-button.secondary').hide();
            }
        });
        
        // Close preview on click
        $(document).on('click', '.smartengage-popup-close, .smartengage-popup-preview', function(e) {
            if (e.target === this) {
                $('.smartengage-popup-preview
<?php
/**
 * Popup builder template.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get the current popup design if it exists
$popup_design = get_post_meta( $post->ID, '_popup_design_json', true ) ?: '';
?>

<div class="smartengage-builder-wrap">
    <!-- Elements Palette -->
    <div class="smartengage-elements-palette">
        <h3><?php esc_html_e( 'Elements', 'smartengage-popups' ); ?></h3>
        <div class="smartengage-element-items">
            <div class="smartengage-element-item" data-element-type="heading">
                <span class="dashicons dashicons-heading smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Heading', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-element-item" data-element-type="text">
                <span class="dashicons dashicons-text smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Text', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-element-item" data-element-type="image">
                <span class="dashicons dashicons-format-image smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Image', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-element-item" data-element-type="button">
                <span class="dashicons dashicons-button smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Button', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-element-item" data-element-type="divider">
                <span class="dashicons dashicons-minus smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Divider', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-element-item" data-element-type="spacer">
                <span class="dashicons dashicons-editor-expand smartengage-element-icon"></span>
                <span class="smartengage-element-name"><?php esc_html_e( 'Spacer', 'smartengage-popups' ); ?></span>
            </div>
        </div>
        
        <div class="smartengage-help-text">
            <p><?php esc_html_e( 'Drag elements to the canvas to build your popup. Click on an element to edit its properties.', 'smartengage-popups' ); ?></p>
        </div>
    </div>
    
    <!-- Builder Canvas -->
    <div class="smartengage-builder-canvas-container">
        <div class="smartengage-builder-toolbar">
            <div class="smartengage-canvas-dimensions">
                <span><i class="dashicons dashicons-desktop"></i> <?php esc_html_e( 'Desktop Preview', 'smartengage-popups' ); ?></span>
            </div>
            <div class="smartengage-builder-actions">
                <button type="button" id="smartengage-preview-design" class="button"><?php esc_html_e( 'Preview', 'smartengage-popups' ); ?></button>
                <button type="button" id="smartengage-reset-design" class="button"><?php esc_html_e( 'Reset', 'smartengage-popups' ); ?></button>
                <button type="button" id="smartengage-save-design" class="button button-primary"><?php esc_html_e( 'Save Design', 'smartengage-popups' ); ?></button>
            </div>
        </div>
        <div id="smartengage-builder-canvas" class="smartengage-builder-canvas"></div>
    </div>
    
    <!-- Properties Panel -->
    <div class="smartengage-properties-panel">
        <h3><?php esc_html_e( 'Element Properties', 'smartengage-popups' ); ?></h3>
        
        <div id="smartengage-element-properties">
            <!-- No element selected message -->
            <p class="smartengage-no-element-selected"><?php esc_html_e( 'Select an element to edit its properties.', 'smartengage-popups' ); ?></p>
            
            <!-- Common properties for all elements -->
            <div class="property-group property-group-common" style="display: none;">
                <h4><?php esc_html_e( 'Position & Size', 'smartengage-popups' ); ?></h4>
                <div class="smartengage-property-row">
                    <label for="element-left"><?php esc_html_e( 'Left', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-left" class="smartengage-property-control" data-property="left" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-top"><?php esc_html_e( 'Top', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-top" class="smartengage-property-control" data-property="top" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-width"><?php esc_html_e( 'Width', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-width" class="smartengage-property-control" data-property="width" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-height"><?php esc_html_e( 'Height', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-height" class="smartengage-property-control" data-property="height" />
                </div>
                <div class="smartengage-property-row">
                    <button type="button" id="smartengage-delete-element" class="button" style="display: none;"><?php esc_html_e( 'Delete Element', 'smartengage-popups' ); ?></button>
                </div>
            </div>
            
            <!-- Text-specific properties -->
            <div class="property-group property-group-text property-group-heading" style="display: none;">
                <h4><?php esc_html_e( 'Text Properties', 'smartengage-popups' ); ?></h4>
                <div class="smartengage-property-row">
                    <label for="element-text-content"><?php esc_html_e( 'Text Content', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-text-content" class="smartengage-property-control" data-property="text-content" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-font-size"><?php esc_html_e( 'Font Size (px)', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-font-size" class="smartengage-property-control" data-property="font-size" min="8" max="72" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-color"><?php esc_html_e( 'Text Color', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-color" class="color-picker smartengage-property-control" data-property="color" />
                </div>
            </div>
            
            <!-- Button-specific properties -->
            <div class="property-group property-group-button" style="display: none;">
                <h4><?php esc_html_e( 'Button Properties', 'smartengage-popups' ); ?></h4>
                <div class="smartengage-property-row">
                    <label for="element-text-content"><?php esc_html_e( 'Button Text', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-text-content" class="smartengage-property-control" data-property="text-content" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-button-url"><?php esc_html_e( 'Button URL', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-button-url" class="smartengage-property-control" data-property="button-url" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-font-size"><?php esc_html_e( 'Font Size (px)', 'smartengage-popups' ); ?></label>
                    <input type="number" id="element-font-size" class="smartengage-property-control" data-property="font-size" min="8" max="72" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-color"><?php esc_html_e( 'Text Color', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-color" class="color-picker smartengage-property-control" data-property="color" />
                </div>
                <div class="smartengage-property-row">
                    <label for="element-bg-color"><?php esc_html_e( 'Background Color', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-bg-color" class="color-picker smartengage-property-control" data-property="bg-color" />
                </div>
            </div>
            
            <!-- Image-specific properties -->
            <div class="property-group property-group-image" style="display: none;">
                <h4><?php esc_html_e( 'Image Properties', 'smartengage-popups' ); ?></h4>
                <div class="smartengage-property-row">
                    <label for="element-image-url"><?php esc_html_e( 'Image URL', 'smartengage-popups' ); ?></label>
                    <div class="smartengage-image-input-group">
                        <input type="text" id="element-image-url" class="smartengage-property-control" data-property="image-url" />
                        <button type="button" id="element-image-upload" class="button"><?php esc_html_e( 'Select', 'smartengage-popups' ); ?></button>
                    </div>
                </div>
                <div class="smartengage-property-row">
                    <label for="element-image-alt"><?php esc_html_e( 'Alt Text', 'smartengage-popups' ); ?></label>
                    <input type="text" id="element-image-alt" class="smartengage-property-control" data-property="image-alt" />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden input to store the popup design JSON -->
<input type="hidden" id="popup_design_json" name="popup_design_json" value="<?php echo esc_attr( $popup_design ); ?>" />

<script>
jQuery(document).ready(function($) {
    // Initialize the media uploader
    $('#element-image-upload').on('click', function(e) {
        e.preventDefault();
        
        // Create a media frame
        var mediaFrame = wp.media({
            title: '<?php esc_html_e( 'Select or Upload Image', 'smartengage-popups' ); ?>',
            button: {
                text: '<?php esc_html_e( 'Use this image', 'smartengage-popups' ); ?>'
            },
            multiple: false
        });
        
        // When an image is selected, run a callback
        mediaFrame.on('select', function() {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            $('#element-image-url').val(attachment.url).trigger('change');
            $('#element-image-alt').val(attachment.alt).trigger('change');
        });
        
        // Open the media frame
        mediaFrame.open();
    });
});
</script>
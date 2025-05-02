<?php
/**
 * Popup builder modern template inspired by Squarespace's elegant design.
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
$popup_type = get_post_meta( $post->ID, '_popup_type', true ) ?: 'slide-in';
$popup_position = get_post_meta( $post->ID, '_popup_position', true ) ?: 'bottom-right';
$popup_theme = get_post_meta( $post->ID, '_popup_theme', true ) ?: 'default';
?>

<div class="smartengage-builder-container">
    <div class="smartengage-builder-wrapper">
        <!-- Main Header -->
        <div class="smartengage-builder-header">
            <div class="smartengage-builder-title">
                <div class="smartengage-builder-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <h2><?php esc_html_e( 'Popup Builder', 'smartengage-popups' ); ?></h2>
            </div>
            <div class="smartengage-builder-actions">
                <button id="smartengage-reset-design" type="button" class="smartengage-builder-button danger">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                    <?php esc_html_e( 'Reset', 'smartengage-popups' ); ?>
                </button>
                <button id="smartengage-preview-design" type="button" class="smartengage-builder-button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <?php esc_html_e( 'Preview', 'smartengage-popups' ); ?>
                </button>
                <button id="smartengage-save-design" type="button" class="smartengage-builder-button primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <?php esc_html_e( 'Save', 'smartengage-popups' ); ?>
                </button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="smartengage-builder-content">
            <!-- Sidebar -->
            <div class="smartengage-builder-sidebar">
                <div class="smartengage-sidebar-header">
                    <h3><?php esc_html_e( 'Editor', 'smartengage-popups' ); ?></h3>
                    <div class="smartengage-sidebar-toggle" title="<?php esc_attr_e( 'Toggle Sidebar', 'smartengage-popups' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </div>
                </div>
                
                <div class="smartengage-sidebar-tabs">
                    <div class="smartengage-sidebar-tab active" data-tab="elements">
                        <?php esc_html_e( 'Elements', 'smartengage-popups' ); ?>
                    </div>
                    <div class="smartengage-sidebar-tab" data-tab="properties">
                        <?php esc_html_e( 'Properties', 'smartengage-popups' ); ?>
                    </div>
                    <div class="smartengage-sidebar-tab" data-tab="settings">
                        <?php esc_html_e( 'Settings', 'smartengage-popups' ); ?>
                    </div>
                </div>
                
                <div class="smartengage-sidebar-content">
                    <!-- Elements Tab -->
                    <div class="smartengage-elements-panel active" data-panel="elements">
                        <div class="smartengage-elements-grid">
                            <div class="smartengage-element" data-element-type="heading">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 12h12"></path>
                                        <path d="M6 4h12"></path>
                                        <path d="M6 20h12"></path>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Heading', 'smartengage-popups' ); ?></div>
                            </div>
                            
                            <div class="smartengage-element" data-element-type="text">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="4 7 4 4 20 4 20 7"></polyline>
                                        <line x1="9" y1="20" x2="15" y2="20"></line>
                                        <line x1="12" y1="4" x2="12" y2="20"></line>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Text', 'smartengage-popups' ); ?></div>
                            </div>
                            
                            <div class="smartengage-element" data-element-type="button">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="7" width="18" height="10" rx="1"></rect>
                                        <path d="M8 7v10"></path>
                                        <path d="M16 7v10"></path>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Button', 'smartengage-popups' ); ?></div>
                            </div>
                            
                            <div class="smartengage-element" data-element-type="image">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Image', 'smartengage-popups' ); ?></div>
                            </div>
                            
                            <div class="smartengage-element" data-element-type="divider">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="3" y1="12" x2="21" y2="12"></line>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Divider', 'smartengage-popups' ); ?></div>
                            </div>
                            
                            <div class="smartengage-element" data-element-type="spacer">
                                <div class="smartengage-element-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="7 8 3 12 7 16"></polyline>
                                        <polyline points="17 8 21 12 17 16"></polyline>
                                        <line x1="3" y1="12" x2="21" y2="12"></line>
                                    </svg>
                                </div>
                                <div class="smartengage-element-label"><?php esc_html_e( 'Spacer', 'smartengage-popups' ); ?></div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button id="smartengage-show-presets" type="button" class="smartengage-builder-button" style="width:100%;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                                <?php esc_html_e( 'Templates & Presets', 'smartengage-popups' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Properties Tab -->
                    <div class="smartengage-properties-panel" data-panel="properties">
                        <!-- No element selected message -->
                        <p class="smartengage-no-element-selected">
                            <?php esc_html_e( 'Select an element to edit its properties', 'smartengage-popups' ); ?>
                        </p>
                        
                        <!-- Position & Size Section (common for all elements) -->
                        <div class="smartengage-property-section smartengage-position-size-section" style="display: none;">
                            <h4 class="smartengage-property-section-title">
                                <?php esc_html_e( 'Position & Size', 'smartengage-popups' ); ?>
                            </h4>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-left">
                                    <?php esc_html_e( 'Left (px)', 'smartengage-popups' ); ?>
                                </label>
                                <input type="number" id="element-left" class="smartengage-property-input" data-property="left" min="0" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-top">
                                    <?php esc_html_e( 'Top (px)', 'smartengage-popups' ); ?>
                                </label>
                                <input type="number" id="element-top" class="smartengage-property-input" data-property="top" min="0" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-width">
                                    <?php esc_html_e( 'Width (px)', 'smartengage-popups' ); ?>
                                </label>
                                <input type="number" id="element-width" class="smartengage-property-input" data-property="width" min="10" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-height">
                                    <?php esc_html_e( 'Height (px)', 'smartengage-popups' ); ?>
                                </label>
                                <input type="number" id="element-height" class="smartengage-property-input" data-property="height" min="10" />
                            </div>
                        </div>
                        
                        <!-- Text Properties Section -->
                        <div class="smartengage-property-section smartengage-text-properties-section" style="display: none;">
                            <h4 class="smartengage-property-section-title">
                                <?php esc_html_e( 'Text Properties', 'smartengage-popups' ); ?>
                            </h4>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-text-content">
                                    <?php esc_html_e( 'Text Content', 'smartengage-popups' ); ?>
                                </label>
                                <textarea id="element-text-content" class="smartengage-property-textarea" data-property="text-content"></textarea>
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-font-size">
                                    <?php esc_html_e( 'Font Size (px)', 'smartengage-popups' ); ?>
                                </label>
                                <input type="number" id="element-font-size" class="smartengage-property-input" data-property="font-size" min="8" max="72" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-color">
                                    <?php esc_html_e( 'Text Color', 'smartengage-popups' ); ?>
                                </label>
                                <div class="smartengage-color-picker-wrapper">
                                    <div class="smartengage-color-preview" id="element-color-preview"></div>
                                    <input type="text" id="element-color" class="smartengage-property-input smartengage-color-input" data-property="color" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Button Properties Section -->
                        <div class="smartengage-property-section smartengage-button-properties-section" style="display: none;">
                            <h4 class="smartengage-property-section-title">
                                <?php esc_html_e( 'Button Properties', 'smartengage-popups' ); ?>
                            </h4>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-button-text">
                                    <?php esc_html_e( 'Button Text', 'smartengage-popups' ); ?>
                                </label>
                                <input type="text" id="element-button-text" class="smartengage-property-input" data-property="button-text" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-button-url">
                                    <?php esc_html_e( 'Button URL', 'smartengage-popups' ); ?>
                                </label>
                                <input type="text" id="element-button-url" class="smartengage-property-input" data-property="button-url" />
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-button-bg-color">
                                    <?php esc_html_e( 'Background Color', 'smartengage-popups' ); ?>
                                </label>
                                <div class="smartengage-color-picker-wrapper">
                                    <div class="smartengage-color-preview" id="element-button-bg-color-preview"></div>
                                    <input type="text" id="element-button-bg-color" class="smartengage-property-input smartengage-color-input" data-property="button-bg-color" />
                                </div>
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-button-text-color">
                                    <?php esc_html_e( 'Text Color', 'smartengage-popups' ); ?>
                                </label>
                                <div class="smartengage-color-picker-wrapper">
                                    <div class="smartengage-color-preview" id="element-button-text-color-preview"></div>
                                    <input type="text" id="element-button-text-color" class="smartengage-property-input smartengage-color-input" data-property="button-text-color" />
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Properties Section -->
                        <div class="smartengage-property-section smartengage-image-properties-section" style="display: none;">
                            <h4 class="smartengage-property-section-title">
                                <?php esc_html_e( 'Image Properties', 'smartengage-popups' ); ?>
                            </h4>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-image-url">
                                    <?php esc_html_e( 'Image URL', 'smartengage-popups' ); ?>
                                </label>
                                <div class="smartengage-image-input-group">
                                    <input type="text" id="element-image-url" class="smartengage-property-input" data-property="image-url" />
                                    <button type="button" id="element-image-upload" class="smartengage-builder-button">
                                        <?php esc_html_e( 'Select', 'smartengage-popups' ); ?>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="element-image-alt">
                                    <?php esc_html_e( 'Alt Text', 'smartengage-popups' ); ?>
                                </label>
                                <input type="text" id="element-image-alt" class="smartengage-property-input" data-property="image-alt" />
                            </div>
                        </div>
                        
                        <!-- Delete Element Button -->
                        <div class="smartengage-property-section smartengage-delete-section" style="display: none;">
                            <button type="button" id="smartengage-delete-element" class="smartengage-builder-button danger">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                <?php esc_html_e( 'Delete Element', 'smartengage-popups' ); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Settings Tab -->
                    <div class="smartengage-settings-panel" data-panel="settings">
                        <div class="smartengage-property-section">
                            <h4 class="smartengage-property-section-title">
                                <?php esc_html_e( 'Popup Settings', 'smartengage-popups' ); ?>
                            </h4>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="popup-type">
                                    <?php esc_html_e( 'Popup Type', 'smartengage-popups' ); ?>
                                </label>
                                <select id="popup-type" class="smartengage-property-input" name="popup_type">
                                    <option value="slide-in" <?php selected( $popup_type, 'slide-in' ); ?>>
                                        <?php esc_html_e( 'Slide-in', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="full-screen" <?php selected( $popup_type, 'full-screen' ); ?>>
                                        <?php esc_html_e( 'Full-screen Overlay', 'smartengage-popups' ); ?>
                                    </option>
                                </select>
                            </div>
                            
                            <div class="smartengage-property-row" id="position-setting">
                                <label class="smartengage-property-label" for="popup-position">
                                    <?php esc_html_e( 'Position', 'smartengage-popups' ); ?>
                                </label>
                                <select id="popup-position" class="smartengage-property-input" name="popup_position">
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
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="popup-theme">
                                    <?php esc_html_e( 'Theme', 'smartengage-popups' ); ?>
                                </label>
                                <select id="popup-theme" class="smartengage-property-input" name="popup_theme">
                                    <option value="default" <?php selected( $popup_theme, 'default' ); ?>>
                                        <?php esc_html_e( 'Default', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="clean" <?php selected( $popup_theme, 'clean' ); ?>>
                                        <?php esc_html_e( 'Clean', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="rounded" <?php selected( $popup_theme, 'rounded' ); ?>>
                                        <?php esc_html_e( 'Rounded', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="dark" <?php selected( $popup_theme, 'dark' ); ?>>
                                        <?php esc_html_e( 'Dark', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="gradient" <?php selected( $popup_theme, 'gradient' ); ?>>
                                        <?php esc_html_e( 'Gradient', 'smartengage-popups' ); ?>
                                    </option>
                                </select>
                            </div>
                            
                            <div class="smartengage-property-row">
                                <label class="smartengage-property-label" for="popup-status">
                                    <?php esc_html_e( 'Status', 'smartengage-popups' ); ?>
                                </label>
                                <select id="popup-status" class="smartengage-property-input" name="popup_status">
                                    <option value="active">
                                        <?php esc_html_e( 'Active', 'smartengage-popups' ); ?>
                                    </option>
                                    <option value="inactive">
                                        <?php esc_html_e( 'Inactive', 'smartengage-popups' ); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Canvas -->
            <div class="smartengage-builder-canvas-container">
                <div class="smartengage-canvas-toolbar">
                    <div class="smartengage-canvas-title">
                        <?php esc_html_e( 'Canvas', 'smartengage-popups' ); ?>
                    </div>
                    <div class="smartengage-device-switcher">
                        <div class="smartengage-device-option active" data-device="desktop" title="<?php esc_attr_e( 'Desktop View', 'smartengage-popups' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <div class="smartengage-device-option" data-device="tablet" title="<?php esc_attr_e( 'Tablet View', 'smartengage-popups' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                            </svg>
                        </div>
                        <div class="smartengage-device-option" data-device="mobile" title="<?php esc_attr_e( 'Mobile View', 'smartengage-popups' ); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="smartengage-canvas-wrapper">
                    <div id="smartengage-builder-canvas" class="smartengage-canvas"></div>
                </div>
            </div>
            
            <!-- Templates & Presets Panel -->
            <div class="smartengage-presets-panel">
                <div class="smartengage-presets-header">
                    <h3><?php esc_html_e( 'Templates & Presets', 'smartengage-popups' ); ?></h3>
                    <div class="smartengage-presets-close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>
                </div>
                
                <div class="smartengage-preset-section">
                    <h4 class="smartengage-preset-section-title">
                        <?php esc_html_e( 'Layout Templates', 'smartengage-popups' ); ?>
                    </h4>
                    <div class="smartengage-presets-grid">
                        <div class="smartengage-preset-item" data-preset="template-1">
                            <div class="smartengage-preset-preview">
                                <?php esc_html_e( 'Email Sign-up', 'smartengage-popups' ); ?>
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Simple Form', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-preset="template-2">
                            <div class="smartengage-preset-preview">
                                <?php esc_html_e( 'Special Offer', 'smartengage-popups' ); ?>
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Promotion', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-preset="template-3">
                            <div class="smartengage-preset-preview">
                                <?php esc_html_e( 'Image + Text', 'smartengage-popups' ); ?>
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Announcement', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-preset="template-4">
                            <div class="smartengage-preset-preview">
                                <?php esc_html_e( 'Simple Button', 'smartengage-popups' ); ?>
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Call-to-Action', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="smartengage-preset-section">
                    <h4 class="smartengage-preset-section-title">
                        <?php esc_html_e( 'Color Schemes', 'smartengage-popups' ); ?>
                    </h4>
                    <div class="smartengage-presets-grid">
                        <div class="smartengage-preset-item" data-color-scheme="blue">
                            <div class="smartengage-preset-preview" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Blue', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-color-scheme="green">
                            <div class="smartengage-preset-preview" style="background: linear-gradient(135deg, #2cb67d, #16a34a);">
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Green', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-color-scheme="red">
                            <div class="smartengage-preset-preview" style="background: linear-gradient(135deg, #ef4444, #b91c1c);">
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Red', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                        <div class="smartengage-preset-item" data-color-scheme="purple">
                            <div class="smartengage-preset-preview" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                            </div>
                            <div class="smartengage-preset-name">
                                <?php esc_html_e( 'Purple', 'smartengage-popups' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden input to store the popup design JSON -->
    <input type="hidden" id="popup_design_json" name="popup_design_json" value="<?php echo esc_attr( $popup_design ); ?>" />
</div>

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
            
            if (attachment.alt) {
                $('#element-image-alt').val(attachment.alt).trigger('change');
            }
        });
        
        // Open the media frame
        mediaFrame.open();
    });
    
    // Tab switching
    $('.smartengage-sidebar-tab').on('click', function() {
        var tab = $(this).data('tab');
        
        // Update active tab
        $('.smartengage-sidebar-tab').removeClass('active');
        $(this).addClass('active');
        
        // Show corresponding panel
        $('.smartengage-sidebar-content > div').removeClass('active');
        $('[data-panel="' + tab + '"]').addClass('active');
    });
    
    // Device switching
    $('.smartengage-device-option').on('click', function() {
        var device = $(this).data('device');
        
        // Update active device
        $('.smartengage-device-option').removeClass('active');
        $(this).addClass('active');
        
        // Update canvas size
        $('.smartengage-canvas').removeClass('desktop tablet mobile').addClass(device);
    });
    
    // Toggle sidebar
    $('.smartengage-sidebar-toggle').on('click', function() {
        $('.smartengage-builder-sidebar').toggleClass('collapsed');
    });
    
    // Show/hide presets panel
    $('#smartengage-show-presets').on('click', function() {
        $('.smartengage-presets-panel').addClass('visible');
    });
    
    $('.smartengage-presets-close').on('click', function() {
        $('.smartengage-presets-panel').removeClass('visible');
    });
    
    // Show/hide position setting based on popup type
    $('#popup-type').on('change', function() {
        if ($(this).val() === 'full-screen') {
            $('#position-setting').hide();
        } else {
            $('#position-setting').show();
        }
    }).trigger('change');
    
    // Initialize color pickers
    if ($.fn.wpColorPicker) {
        $('.smartengage-color-input').wpColorPicker({
            change: function(event, ui) {
                var color = ui.color.toString();
                var $input = $(event.target);
                var previewId = $input.attr('id') + '-preview';
                
                // Update color preview
                $('#' + previewId).css('background-color', color);
                
                // Trigger change event for the property update
                $input.trigger('se-color-change', [color]);
            }
        });
    }
    
    // Update color preview when colors are selected
    $('.smartengage-color-preview').on('click', function() {
        var inputId = $(this).attr('id').replace('-preview', '');
        $('#' + inputId).wpColorPicker('open');
    });
});
</script>
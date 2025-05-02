<?php
/**
 * Class responsible for displaying popups on the frontend.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Class for displaying popups on the frontend.
 */
class SmartEngage_Popups_Display {

    /**
     * Initialize the class.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->analytics = new SmartEngage_Popups_Analytics();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        if ($this->should_load_assets()) {
            wp_enqueue_style(
                'smartengage-popups',
                SMARTENGAGE_POPUPS_PLUGIN_URL . 'assets/css/smartengage-popups-frontend.css',
                array(),
                SMARTENGAGE_POPUPS_VERSION,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        if ($this->should_load_assets()) {
            wp_enqueue_script(
                'smartengage-popups',
                SMARTENGAGE_POPUPS_PLUGIN_URL . 'assets/js/smartengage-popups-frontend.js',
                array( 'jquery' ),
                SMARTENGAGE_POPUPS_VERSION,
                true
            );

            // Localize the script with data
            $popups = $this->get_active_popups();
            $popup_data = array();

            foreach ($popups as $popup) {
                $popup_data[] = $this->get_popup_data($popup);
            }

            wp_localize_script(
                'smartengage-popups',
                'SmartEngagePopups',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'smartengage-popups-nonce' ),
                    'popups' => $popup_data
                )
            );
        }
    }

    /**
     * Check if we should load assets on this page
     *
     * @since    1.0.0
     * @return   boolean  Whether to load assets
     */
    private function should_load_assets() {
        $active_popups = $this->get_active_popups();
        return !empty($active_popups);
    }

    /**
     * Get all active popups
     *
     * @since    1.0.0
     * @return   array    Array of popup post objects
     */
    private function get_active_popups() {
        $args = array(
            'post_type'      => 'smartengage_popup',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'   => '_popup_status',
                    'value' => 'active',
                ),
            ),
        );

        $popups = get_posts( $args );
        return $popups;
    }

    /**
     * Get popup data for JavaScript
     *
     * @since    1.0.0
     * @param    WP_Post $popup  The popup post object
     * @return   array           The popup data
     */
    private function get_popup_data( $popup ) {
        // Basic popup settings
        $popup_id = $popup->ID;
        $popup_type = get_post_meta( $popup_id, '_popup_type', true ) ?: 'slide-in';
        $popup_position = get_post_meta( $popup_id, '_popup_position', true ) ?: 'bottom-right';
        $button_text = get_post_meta( $popup_id, '_button_text', true ) ?: '';
        $button_url = get_post_meta( $popup_id, '_button_url', true ) ?: '';
        $button_color = get_post_meta( $popup_id, '_button_color', true ) ?: '#4CAF50';

        // Trigger conditions
        $time_delay = get_post_meta( $popup_id, '_time_delay', true ) ?: '';
        $scroll_depth = get_post_meta( $popup_id, '_scroll_depth', true ) ?: '';
        $exit_intent = get_post_meta( $popup_id, '_exit_intent', true ) ?: 0;
        $page_views = get_post_meta( $popup_id, '_page_views', true ) ?: '';
        $specific_url = get_post_meta( $popup_id, '_specific_url', true ) ?: '';
        $user_status = get_post_meta( $popup_id, '_user_status', true ) ?: 'all';
        $conditions_match = get_post_meta( $popup_id, '_conditions_match', true ) ?: 'any';

        // Frequency rules
        $frequency = get_post_meta( $popup_id, '_frequency', true ) ?: 'once_per_session';
        $days_between = get_post_meta( $popup_id, '_days_between', true ) ?: '7';
        $max_impressions = get_post_meta( $popup_id, '_max_impressions', true ) ?: '';

        // Targeting options
        $device_type = get_post_meta( $popup_id, '_device_type', true ) ?: 'all';
        $referrer_url = get_post_meta( $popup_id, '_referrer_url', true ) ?: '';
        $cookie_name = get_post_meta( $popup_id, '_cookie_name', true ) ?: '';
        $cookie_value = get_post_meta( $popup_id, '_cookie_value', true ) ?: '';
        $user_roles = get_post_meta( $popup_id, '_user_roles', true ) ?: array();

        // Get image if set
        $image_url = '';
        if (has_post_thumbnail($popup_id)) {
            $image_url = get_the_post_thumbnail_url($popup_id, 'medium');
        }

        return array(
            'id' => $popup_id,
            'title' => get_the_title($popup_id),
            'content' => apply_filters('the_content', $popup->post_content),
            'type' => $popup_type,
            'position' => $popup_position,
            'buttonText' => $button_text,
            'buttonUrl' => $button_url,
            'buttonColor' => $button_color,
            'imageUrl' => $image_url,
            'triggers' => array(
                'timeDelay' => $time_delay,
                'scrollDepth' => $scroll_depth,
                'exitIntent' => (bool) $exit_intent,
                'pageViews' => $page_views,
                'specificUrl' => $specific_url,
                'userStatus' => $user_status,
                'conditionsMatch' => $conditions_match
            ),
            'frequency' => array(
                'type' => $frequency,
                'daysBetween' => $days_between,
                'maxImpressions' => $max_impressions
            ),
            'targeting' => array(
                'deviceType' => $device_type,
                'referrerUrl' => $referrer_url,
                'cookieName' => $cookie_name,
                'cookieValue' => $cookie_value,
                'userRoles' => $user_roles
            )
        );
    }

    /**
     * Display popups in the footer
     *
     * @since    1.0.0
     */
    public function display_popups() {
        $popups = $this->get_active_popups();
        
        if (empty($popups)) {
            return;
        }

        // Add the popup containers to the page
        ?>
        <div id="smartengage-popups-container" aria-hidden="true">
            <?php foreach ($popups as $popup) : ?>
                <div id="smartengage-popup-<?php echo esc_attr($popup->ID); ?>" class="smartengage-popup" role="dialog" aria-modal="true" aria-labelledby="smartengage-popup-title-<?php echo esc_attr($popup->ID); ?>" style="display: none;">
                    <div class="smartengage-popup-content">
                        <button class="smartengage-popup-close" aria-label="<?php esc_attr_e('Close popup', 'smartengage-popups'); ?>">&times;</button>
                        
                        <?php
                        // Check if this popup has a custom design from the builder
                        $popup_design = get_post_meta($popup->ID, '_popup_design_json', true);
                        
                        if (!empty($popup_design)) {
                            // Custom design from builder
                            $design_elements = json_decode($popup_design, true);
                            
                            if (is_array($design_elements) && !empty($design_elements)) {
                                // Render each element in the design
                                foreach ($design_elements as $element) {
                                    // Skip invalid elements
                                    if (!isset($element['type']) || !isset($element['id'])) {
                                        continue;
                                    }
                                    
                                    // Get common styles
                                    $position_style = sprintf(
                                        'position: absolute; left: %dpx; top: %dpx; width: %dpx; height: %dpx;',
                                        $element['position']['left'],
                                        $element['position']['top'],
                                        $element['size']['width'],
                                        $element['size']['height']
                                    );
                                    
                                    // Render based on element type
                                    switch ($element['type']) {
                                        case 'heading':
                                            $color_style = isset($element['color']) ? sprintf('color: %s;', esc_attr($element['color'])) : '';
                                            $font_size = isset($element['fontSize']) ? sprintf('font-size: %dpx;', $element['fontSize']) : '';
                                            
                                            printf(
                                                '<h2 class="builder-element element-heading" id="%s" style="%s %s %s">%s</h2>',
                                                esc_attr($element['id']),
                                                $position_style,
                                                $color_style,
                                                $font_size,
                                                esc_html($element['content'])
                                            );
                                            break;
                                            
                                        case 'text':
                                            $color_style = isset($element['color']) ? sprintf('color: %s;', esc_attr($element['color'])) : '';
                                            $font_size = isset($element['fontSize']) ? sprintf('font-size: %dpx;', $element['fontSize']) : '';
                                            
                                            printf(
                                                '<p class="builder-element element-text" id="%s" style="%s %s %s">%s</p>',
                                                esc_attr($element['id']),
                                                $position_style,
                                                $color_style,
                                                $font_size,
                                                esc_html($element['content'])
                                            );
                                            break;
                                            
                                        case 'button':
                                            $color_style = isset($element['color']) ? sprintf('color: %s;', esc_attr($element['color'])) : '';
                                            $bg_color = isset($element['backgroundColor']) ? sprintf('background-color: %s;', esc_attr($element['backgroundColor'])) : '';
                                            $font_size = isset($element['fontSize']) ? sprintf('font-size: %dpx;', $element['fontSize']) : '';
                                            $url = isset($element['url']) ? esc_url($element['url']) : '#';
                                            
                                            printf(
                                                '<a href="%s" class="builder-element element-button smartengage-popup-button" id="%s" data-popup-id="%d" style="%s %s %s %s">%s</a>',
                                                $url,
                                                esc_attr($element['id']),
                                                $popup->ID,
                                                $position_style,
                                                $color_style,
                                                $bg_color,
                                                $font_size,
                                                esc_html($element['content'])
                                            );
                                            break;
                                            
                                        case 'image':
                                            $src = isset($element['src']) ? esc_url($element['src']) : '';
                                            $alt = isset($element['alt']) ? esc_attr($element['alt']) : '';
                                            
                                            printf(
                                                '<div class="builder-element element-image" id="%s" style="%s"><img src="%s" alt="%s"></div>',
                                                esc_attr($element['id']),
                                                $position_style,
                                                $src,
                                                $alt
                                            );
                                            break;
                                            
                                        case 'divider':
                                            printf(
                                                '<hr class="builder-element element-divider" id="%s" style="%s">',
                                                esc_attr($element['id']),
                                                $position_style
                                            );
                                            break;
                                            
                                        case 'spacer':
                                            printf(
                                                '<div class="builder-element element-spacer" id="%s" style="%s"></div>',
                                                esc_attr($element['id']),
                                                $position_style
                                            );
                                            break;
                                    }
                                }
                            }
                        } else {
                            // Legacy popup design (no builder)
                            if (has_post_thumbnail($popup->ID)) : ?>
                                <div class="smartengage-popup-image">
                                    <?php echo get_the_post_thumbnail($popup->ID, 'medium'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <h2 id="smartengage-popup-title-<?php echo esc_attr($popup->ID); ?>" class="smartengage-popup-title"><?php echo esc_html(get_the_title($popup->ID)); ?></h2>
                            
                            <div class="smartengage-popup-body">
                                <?php echo apply_filters('the_content', $popup->post_content); ?>
                            </div>
                            
                            <?php 
                            $button_text = get_post_meta($popup->ID, '_button_text', true);
                            $button_url = get_post_meta($popup->ID, '_button_url', true);
                            $button_color = get_post_meta($popup->ID, '_button_color', true) ?: '#4CAF50';
                            
                            if (!empty($button_text) && !empty($button_url)) : 
                            ?>
                                <div class="smartengage-popup-buttons">
                                    <a href="<?php echo esc_url($button_url); ?>" class="smartengage-popup-button" 
                                       data-popup-id="<?php echo esc_attr($popup->ID); ?>"
                                       style="background-color: <?php echo esc_attr($button_color); ?>;">
                                        <?php echo esc_html($button_text); ?>
                                    </a>
                                </div>
                            <?php endif;
                        } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

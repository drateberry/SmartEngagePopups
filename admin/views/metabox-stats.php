<?php
/**
 * Stats metabox view
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Set defaults if empty
if ( empty( $impressions ) ) {
    $impressions = 0;
}

if ( empty( $conversions ) ) {
    $conversions = 0;
}
?>

<div class="smartengage-metabox-container">
    <div class="smartengage-stats-overview">
        <div class="smartengage-stat-box">
            <div class="smartengage-stat-label"><?php esc_html_e( 'Impressions', 'smartengage-popups' ); ?></div>
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $impressions ) ); ?></div>
        </div>
        
        <div class="smartengage-stat-box">
            <div class="smartengage-stat-label"><?php esc_html_e( 'Conversions', 'smartengage-popups' ); ?></div>
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $conversions ) ); ?></div>
        </div>
        
        <div class="smartengage-stat-box">
            <div class="smartengage-stat-label"><?php esc_html_e( 'Conversion Rate', 'smartengage-popups' ); ?></div>
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $rate, 1 ) ); ?>%</div>
        </div>
    </div>
    
    <p>
        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=smartengage_popup&page=smartengage-analytics&popup=' . $post->ID ) ); ?>" class="button">
            <?php esc_html_e( 'View Detailed Analytics', 'smartengage-popups' ); ?>
        </a>
    </p>
</div>

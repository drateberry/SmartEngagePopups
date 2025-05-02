<?php
/**
 * Analytics dashboard template.
 *
 * @since      1.0.0
 * @package    SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$analytics = new SmartEngage_Popups_Analytics();

// Get analytics data
$views_all = $analytics->get_popup_views( $popup_id, 'all' );
$views_monthly = $analytics->get_popup_views( $popup_id, 'monthly' );
$views_weekly = $analytics->get_popup_views( $popup_id, 'weekly' );
$views_daily = $analytics->get_popup_views( $popup_id, 'daily' );

$clicks_all = $analytics->get_popup_clicks( $popup_id, 'all' );
$clicks_monthly = $analytics->get_popup_clicks( $popup_id, 'monthly' );
$clicks_weekly = $analytics->get_popup_clicks( $popup_id, 'weekly' );
$clicks_daily = $analytics->get_popup_clicks( $popup_id, 'daily' );

// Calculate conversion rates
$conversion_all = $views_all > 0 ? round( ( $clicks_all / $views_all ) * 100, 2 ) : 0;
$conversion_monthly = $views_monthly > 0 ? round( ( $clicks_monthly / $views_monthly ) * 100, 2 ) : 0;
$conversion_weekly = $views_weekly > 0 ? round( ( $clicks_weekly / $views_weekly ) * 100, 2 ) : 0;
$conversion_daily = $views_daily > 0 ? round( ( $clicks_daily / $views_daily ) * 100, 2 ) : 0;

// Get popup settings for display
$popup_type = get_post_meta( $popup_id, '_popup_type', true ) ?: 'slide-in';
$popup_position = get_post_meta( $popup_id, '_popup_position', true ) ?: 'bottom-right';
$popup_status = get_post_meta( $popup_id, '_popup_status', true ) ?: 'inactive';

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <h2>
        <?php echo esc_html( $popup->post_title ); ?>
        <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $popup_id . '&action=edit' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Edit Popup', 'smartengage-popups' ); ?></a>
    </h2>
    
    <div class="popup-info">
        <p>
            <strong><?php esc_html_e( 'Type:', 'smartengage-popups' ); ?></strong> <?php echo esc_html( $popup_type === 'slide-in' ? __( 'Slide-in Popup', 'smartengage-popups' ) : __( 'Full-screen Overlay', 'smartengage-popups' ) ); ?>
            <?php if ( $popup_type === 'slide-in' ) : ?>
                (<?php echo esc_html( ucwords( str_replace( '-', ' ', $popup_position ) ) ); ?>)
            <?php endif; ?>
        </p>
        <p>
            <strong><?php esc_html_e( 'Status:', 'smartengage-popups' ); ?></strong> 
            <?php if ( $popup_status === 'active' ) : ?>
                <span class="status-active"><?php esc_html_e( 'Active', 'smartengage-popups' ); ?></span>
            <?php else : ?>
                <span class="status-inactive"><?php esc_html_e( 'Inactive', 'smartengage-popups' ); ?></span>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="analytics-period-selector">
        <h3><?php esc_html_e( 'Performance Overview', 'smartengage-popups' ); ?></h3>
    </div>
    
    <div class="smartengage-analytics">
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $views_daily ); ?></span>
            <span class="analytics-label"><?php esc_html_e( 'Views Today', 'smartengage-popups' ); ?></span>
        </div>
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $clicks_daily ); ?></span>
            <span class="analytics-label"><?php esc_html_e( 'Clicks Today', 'smartengage-popups' ); ?></span>
        </div>
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $conversion_daily ); ?>%</span>
            <span class="analytics-label"><?php esc_html_e( 'Conversion Rate Today', 'smartengage-popups' ); ?></span>
        </div>
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $views_all ); ?></span>
            <span class="analytics-label"><?php esc_html_e( 'Total Views', 'smartengage-popups' ); ?></span>
        </div>
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $clicks_all ); ?></span>
            <span class="analytics-label"><?php esc_html_e( 'Total Clicks', 'smartengage-popups' ); ?></span>
        </div>
        <div class="analytics-item">
            <span class="analytics-number"><?php echo esc_html( $conversion_all ); ?>%</span>
            <span class="analytics-label"><?php esc_html_e( 'Overall Conversion Rate', 'smartengage-popups' ); ?></span>
        </div>
    </div>
    
    <div class="analytics-chart-container">
        <h2><?php esc_html_e( 'Performance Over Time (Last 30 Days)', 'smartengage-popups' ); ?></h2>
        <canvas id="analytics-chart"></canvas>
    </div>
    
    <div class="analytics-summary">
        <h3><?php esc_html_e( 'Summary', 'smartengage-popups' ); ?></h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Period', 'smartengage-popups' ); ?></th>
                    <th><?php esc_html_e( 'Views', 'smartengage-popups' ); ?></th>
                    <th><?php esc_html_e( 'Clicks', 'smartengage-popups' ); ?></th>
                    <th><?php esc_html_e( 'Conversion Rate', 'smartengage-popups' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php esc_html_e( 'Today', 'smartengage-popups' ); ?></td>
                    <td><?php echo esc_html( $views_daily ); ?></td>
                    <td><?php echo esc_html( $clicks_daily ); ?></td>
                    <td><?php echo esc_html( $conversion_daily ); ?>%</td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Last 7 Days', 'smartengage-popups' ); ?></td>
                    <td><?php echo esc_html( $views_weekly ); ?></td>
                    <td><?php echo esc_html( $clicks_weekly ); ?></td>
                    <td><?php echo esc_html( $conversion_weekly ); ?>%</td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Last 30 Days', 'smartengage-popups' ); ?></td>
                    <td><?php echo esc_html( $views_monthly ); ?></td>
                    <td><?php echo esc_html( $clicks_monthly ); ?></td>
                    <td><?php echo esc_html( $conversion_monthly ); ?>%</td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'All Time', 'smartengage-popups' ); ?></td>
                    <td><?php echo esc_html( $views_all ); ?></td>
                    <td><?php echo esc_html( $clicks_all ); ?></td>
                    <td><?php echo esc_html( $conversion_all ); ?>%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <p class="back-link">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=smartengage-analytics' ) ); ?>">&larr; <?php esc_html_e( 'Back to Analytics Overview', 'smartengage-popups' ); ?></a>
    </p>
</div>

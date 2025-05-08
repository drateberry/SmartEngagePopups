<?php
/**
 * Analytics Dashboard view
 *
 * @package SmartEngage_Popups
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get selected popup
$popup_id = isset( $_GET['popup'] ) ? absint( $_GET['popup'] ) : 0;

// Get all popups for dropdown
$popups = get_posts( array(
    'post_type'      => 'smartengage_popup',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
) );

// Get analytics data
$analytics = new SmartEngage_Analytics();
$date_range = isset( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7days';
$data = $analytics->get_analytics( $popup_id, '', '' );
?>

<div class="wrap smartengage-analytics-dashboard">
    <h1><?php esc_html_e( 'Analytics Dashboard', 'smartengage-popups' ); ?></h1>
    
    <div class="smartengage-analytics-filters">
        <form method="get" action="">
            <input type="hidden" name="post_type" value="smartengage_popup" />
            <input type="hidden" name="page" value="smartengage-analytics" />
            
            <select name="popup" id="smartengage-popup-filter">
                <option value="0" <?php selected( $popup_id, 0 ); ?>><?php esc_html_e( 'All Popups', 'smartengage-popups' ); ?></option>
                <?php foreach ( $popups as $popup ) : ?>
                    <option value="<?php echo esc_attr( $popup->ID ); ?>" <?php selected( $popup_id, $popup->ID ); ?>>
                        <?php echo esc_html( $popup->post_title ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="range" id="smartengage-date-range">
                <option value="7days" <?php selected( $date_range, '7days' ); ?>><?php esc_html_e( 'Last 7 Days', 'smartengage-popups' ); ?></option>
                <option value="30days" <?php selected( $date_range, '30days' ); ?>><?php esc_html_e( 'Last 30 Days', 'smartengage-popups' ); ?></option>
                <option value="90days" <?php selected( $date_range, '90days' ); ?>><?php esc_html_e( 'Last 90 Days', 'smartengage-popups' ); ?></option>
                <option value="year" <?php selected( $date_range, 'year' ); ?>><?php esc_html_e( 'Last Year', 'smartengage-popups' ); ?></option>
            </select>
            
            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'smartengage-popups' ); ?></button>
        </form>
    </div>
    
    <div class="smartengage-analytics-overview">
        <div class="smartengage-stat-card">
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $data['totals']['impressions'] ) ); ?></div>
            <div class="smartengage-stat-label"><?php esc_html_e( 'Total Impressions', 'smartengage-popups' ); ?></div>
        </div>
        
        <div class="smartengage-stat-card">
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $data['totals']['conversions'] ) ); ?></div>
            <div class="smartengage-stat-label"><?php esc_html_e( 'Total Conversions', 'smartengage-popups' ); ?></div>
        </div>
        
        <div class="smartengage-stat-card">
            <div class="smartengage-stat-value"><?php echo esc_html( number_format( $data['totals']['rate'], 1 ) ); ?>%</div>
            <div class="smartengage-stat-label"><?php esc_html_e( 'Conversion Rate', 'smartengage-popups' ); ?></div>
        </div>
    </div>
    
    <div class="smartengage-analytics-chart-container">
        <canvas id="smartengage-analytics-chart"></canvas>
    </div>
    
    <?php if ( count( $data['popups'] ) > 0 ) : ?>
    <div class="smartengage-analytics-table-container">
        <h2><?php esc_html_e( 'Popup Performance', 'smartengage-popups' ); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Popup', 'smartengage-popups' ); ?></th>
                    <th class="smartengage-numeric-column"><?php esc_html_e( 'Impressions', 'smartengage-popups' ); ?></th>
                    <th class="smartengage-numeric-column"><?php esc_html_e( 'Conversions', 'smartengage-popups' ); ?></th>
                    <th class="smartengage-numeric-column"><?php esc_html_e( 'Conversion Rate', 'smartengage-popups' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $data['popups'] as $popup ) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url( admin_url( 'post.php?post=' . $popup['id'] . '&action=edit' ) ); ?>">
                                <?php echo esc_html( $popup['title'] ); ?>
                            </a>
                        </td>
                        <td class="smartengage-numeric-column"><?php echo esc_html( number_format( $popup['totals']['impressions'] ) ); ?></td>
                        <td class="smartengage-numeric-column"><?php echo esc_html( number_format( $popup['totals']['conversions'] ) ); ?></td>
                        <td class="smartengage-numeric-column"><?php echo esc_html( number_format( $popup['totals']['rate'], 1 ) ); ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Only initialize chart if we have a canvas element
        if (document.getElementById('smartengage-analytics-chart')) {
            var ctx = document.getElementById('smartengage-analytics-chart').getContext('2d');
            
            // Chart data
            var chartData = <?php echo wp_json_encode( $data['chart_data'] ); ?>;
            var labels = chartData.map(function(item) { return item.date; });
            var impressionsData = chartData.map(function(item) { return item.impressions; });
            var conversionsData = chartData.map(function(item) { return item.conversions; });
            
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '<?php esc_html_e( 'Impressions', 'smartengage-popups' ); ?>',
                            data: impressionsData,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3
                        },
                        {
                            label: '<?php esc_html_e( 'Conversions', 'smartengage-popups' ); ?>',
                            data: conversionsData,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>

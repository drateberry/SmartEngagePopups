/**
 * Analytics JavaScript for SmartEngage Popups
 */
(function($) {
    'use strict';

    // Analytics class
    class PopupAnalytics {
        constructor() {
            this.currentPopupId = null;
            this.currentDateRange = '30days';
            this.charts = {
                performanceChart: null,
                devicesChart: null
            };
            
            this.init();
        }
        
        /**
         * Initialize analytics
         */
        init() {
            this.setupEventListeners();
        }
        
        /**
         * Set up event listeners
         */
        setupEventListeners() {
            const self = this;
            
            // Popup selection
            $('#smartengage-popup-select').on('change', function() {
                const popupId = $(this).val();
                if (popupId) {
                    self.currentPopupId = popupId;
                    self.loadAnalyticsData();
                } else {
                    self.hideAnalyticsDashboard();
                }
            });
            
            // Date range selection
            $('#smartengage-date-range').on('change', function() {
                self.currentDateRange = $(this).val();
                if (self.currentPopupId) {
                    self.loadAnalyticsData();
                }
            });
        }
        
        /**
         * Load analytics data for a popup
         */
        loadAnalyticsData() {
            const self = this;
            
            // Show loading state
            this.showLoadingState();
            
            // Send AJAX request
            $.ajax({
                url: smartengageAnalytics.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smartengage_get_analytics',
                    popup_id: self.currentPopupId,
                    date_range: self.currentDateRange,
                    nonce: smartengageAnalytics.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.displayAnalyticsData(response.data);
                    } else {
                        self.showError(response.data.message || 'Failed to load analytics data.');
                    }
                },
                error: function() {
                    self.showError('An error occurred while loading analytics data.');
                }
            });
        }
        
        /**
         * Display analytics data
         * 
         * @param {Object} data The analytics data
         */
        displayAnalyticsData(data) {
            // Show the dashboard
            this.showAnalyticsDashboard();
            
            // Update stats
            $('#stat-total-views').text(this.formatNumber(data.views.total));
            $('#stat-total-clicks').text(this.formatNumber(data.clicks.total));
            $('#stat-conversion-rate').text(data.conversion_rate + '%');
            
            // Update charts
            this.updatePerformanceChart(data);
            this.updateDevicesChart(data);
        }
        
        /**
         * Update the performance chart
         * 
         * @param {Object} data The analytics data
         */
        updatePerformanceChart(data) {
            const ctx = document.getElementById('smartengage-performance-chart').getContext('2d');
            
            // Prepare labels and datasets
            const labels = data.views.data.map(item => item.date);
            const viewsData = data.views.data.map(item => item.count);
            const clicksData = [];
            
            // Match clicks data to views dates
            for (const date of labels) {
                const clickItem = data.clicks.data.find(item => item.date === date);
                clicksData.push(clickItem ? clickItem.count : 0);
            }
            
            // Destroy previous chart if exists
            if (this.charts.performanceChart) {
                this.charts.performanceChart.destroy();
            }
            
            // Create new chart
            this.charts.performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Views',
                            data: viewsData,
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Clicks',
                            data: clicksData,
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        /**
         * Update the devices chart
         * 
         * @param {Object} data The analytics data
         */
        updateDevicesChart(data) {
            const ctx = document.getElementById('smartengage-devices-chart').getContext('2d');
            
            // Prepare data
            const labels = [];
            const counts = [];
            const colors = [
                'rgba(0, 123, 255, 0.7)',
                'rgba(40, 167, 69, 0.7)',
                'rgba(255, 193, 7, 0.7)'
            ];
            
            // Extract device data
            data.devices.forEach((device, index) => {
                labels.push(this.capitalizeFirstLetter(device.device_type || 'Unknown'));
                counts.push(device.count);
            });
            
            // Destroy previous chart if exists
            if (this.charts.devicesChart) {
                this.charts.devicesChart.destroy();
            }
            
            // Create new chart
            this.charts.devicesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: counts,
                            backgroundColor: colors,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        /**
         * Show the analytics dashboard
         */
        showAnalyticsDashboard() {
            $('#smartengage-analytics-placeholder').hide();
            $('#smartengage-analytics-dashboard').show();
        }
        
        /**
         * Hide the analytics dashboard
         */
        hideAnalyticsDashboard() {
            $('#smartengage-analytics-dashboard').hide();
            $('#smartengage-analytics-placeholder').show();
        }
        
        /**
         * Show loading state
         */
        showLoadingState() {
            // Show loading indicator
            if ($('.smartengage-analytics-loading').length === 0) {
                $('<div class="smartengage-analytics-loading"><span class="spinner is-active"></span><p>Loading analytics data...</p></div>')
                    .insertAfter('#smartengage-analytics-placeholder')
                    .show();
            }
            
            // Hide dashboard and placeholder
            $('#smartengage-analytics-dashboard, #smartengage-analytics-placeholder').hide();
        }
        
        /**
         * Show error message
         * 
         * @param {string} message The error message
         */
        showError(message) {
            // Remove loading indicator
            $('.smartengage-analytics-loading').remove();
            
            // Show error message
            $('#smartengage-analytics-placeholder')
                .html('<p class="smartengage-error"><strong>Error:</strong> ' + message + '</p>')
                .show();
            
            // Hide dashboard
            $('#smartengage-analytics-dashboard').hide();
        }
        
        /**
         * Format a number with commas
         * 
         * @param {number} num The number to format
         * @return {string} The formatted number
         */
        formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
        
        /**
         * Capitalize the first letter of a string
         * 
         * @param {string} str The string to capitalize
         * @return {string} The capitalized string
         */
        capitalizeFirstLetter(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    }

    // Initialize analytics when the document is ready
    $(function() {
        window.smartengagePopupAnalytics = new PopupAnalytics();
    });

})(jQuery);
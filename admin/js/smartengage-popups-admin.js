/**
 * Admin JavaScript for SmartEngage Popups
 */
(function( $ ) {
    'use strict';

    $(function() {
        // Initialize color picker
        $('.color-picker').wpColorPicker();

        // Toggle display of popup position field based on popup type
        $('#popup_type').on('change', function() {
            if ($(this).val() === 'slide-in') {
                $('.popup-position-field').show();
            } else {
                $('.popup-position-field').hide();
            }
        });

        // Toggle display of days between field based on frequency
        $('#frequency').on('change', function() {
            if ($(this).val() === 'every_x_days') {
                $('.days-between-row').show();
            } else {
                $('.days-between-row').hide();
            }
        });

        // Preview button functionality
        $('.popup-preview-button').on('click', function(e) {
            e.preventDefault();
            
            // Get popup configuration
            const type = $('#popup_type').val();
            const position = $('#popup_position').val();
            const title = $('#title').val() || 'Popup Title';
            const content = tinyMCE.activeEditor ? tinyMCE.activeEditor.getContent() : $('#content').val();
            const buttonText = $('#button_text').val();
            const buttonColor = $('#button_color').val();
            
            // Create preview popup
            let popupClass = 'smartengage-popup-preview';
            if (type === 'slide-in') {
                popupClass += ' type-slide-in position-' + position;
            } else {
                popupClass += ' type-full-screen';
            }
            
            const popupHtml = `
                <div class="${popupClass}">
                    <div class="smartengage-popup-content">
                        <button class="smartengage-popup-close">&times;</button>
                        <h2 class="smartengage-popup-title">${title}</h2>
                        <div class="smartengage-popup-body">${content}</div>
                        ${buttonText ? `<div class="smartengage-popup-buttons">
                            <a href="#" class="smartengage-popup-button" style="background-color: ${buttonColor};">${buttonText}</a>
                        </div>` : ''}
                    </div>
                </div>
                <div class="smartengage-popup-overlay"></div>
            `;
            
            // Remove any existing preview
            $('.smartengage-popup-preview, .smartengage-popup-overlay').remove();
            
            // Add the preview to the page
            $('body').append(popupHtml);
            
            // Handle close button
            $('.smartengage-popup-close, .smartengage-popup-overlay').on('click', function() {
                $('.smartengage-popup-preview, .smartengage-popup-overlay').remove();
            });
        });

        // Analytics chart initialization if on the analytics page
        if (typeof SmartEngageAnalytics !== 'undefined' && $('#analytics-chart').length) {
            const data = SmartEngageAnalytics.chartData;
            const ctx = document.getElementById('analytics-chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Views',
                            data: data.views,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Clicks',
                            data: data.clicks,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0.3
                        },
                        {
                            label: 'Conversion Rate (%)',
                            data: data.conversion_rates,
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    stacked: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Conversion Rate (%)'
                            },
                            // Grid line settings
                            grid: {
                                drawOnChartArea: false, // only want the grid lines for one axis to show up
                            },
                            min: 0,
                            max: 100
                        }
                    }
                }
            });
        }
    });

})( jQuery );

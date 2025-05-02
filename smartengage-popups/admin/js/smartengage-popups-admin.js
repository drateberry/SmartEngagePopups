/**
 * Admin JavaScript for SmartEngage Popups
 */
(function($) {
    'use strict';

    // Initialize admin functionality when the document is ready
    $(function() {
        // Initialize tooltips
        if (typeof $.fn.tooltip === 'function') {
            $('.smartengage-tooltip').tooltip();
        }
        
        // Handle ajax save button functionality (independent of the builder)
        $('#smartengage-ajax-save').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const popupId = $button.data('popup-id');
            const originalText = $button.text();
            
            // Disable button and show loading state
            $button.prop('disabled', true).text('Saving...');
            
            // Get form data
            const formData = $('#post').serializeArray();
            
            // Send AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartengage_save_popup',
                    popup_id: popupId,
                    form_data: formData,
                    nonce: $('#smartengage_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>')
                            .insertAfter($button.closest('.smartengage-action-bar'))
                            .delay(3000)
                            .fadeOut(function() {
                                $(this).remove();
                            });
                    } else {
                        // Show error message
                        $('<div class="notice notice-error is-dismissible"><p>' + response.data.message + '</p></div>')
                            .insertAfter($button.closest('.smartengage-action-bar'));
                    }
                },
                error: function() {
                    // Show error message
                    $('<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>')
                        .insertAfter($button.closest('.smartengage-action-bar'));
                },
                complete: function() {
                    // Reset button state
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Handle popup status toggle
        $('.smartengage-status-toggle').on('change', function() {
            const $toggle = $(this);
            const popupId = $toggle.data('popup-id');
            const isActive = $toggle.prop('checked');
            
            // Send AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartengage_toggle_status',
                    popup_id: popupId,
                    status: isActive ? 'active' : 'inactive',
                    nonce: $('#smartengage_nonce').val()
                }
            });
        });
        
        // Handle duplicate popup
        $('.smartengage-duplicate-popup').on('click', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const popupId = $button.data('popup-id');
            
            // Confirm duplication
            if (!confirm('Are you sure you want to duplicate this popup?')) {
                return;
            }
            
            // Send AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'smartengage_duplicate_popup',
                    popup_id: popupId,
                    nonce: $('#smartengage_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        // Redirect to the duplicate popup
                        window.location.href = response.data.edit_url;
                    } else {
                        // Show error message
                        alert(response.data.message || 'An error occurred.');
                    }
                },
                error: function() {
                    // Show error message
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });

})(jQuery);
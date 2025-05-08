/**
 * SmartEngage Popups Admin JavaScript
 *
 * @package SmartEngage_Popups
 */

(function($) {
    'use strict';

    /**
     * SmartEngage Popups Admin Class
     */
    var SmartEngageAdmin = {
        
        /**
         * Initialize the plugin
         */
        init: function() {
            this.initColorPicker();
            this.setupCopyShortcode();
            this.setupCloneButton();
            this.setupResetStats();
        },
        
        /**
         * Initialize color picker
         */
        initColorPicker: function() {
            if ($.fn.wpColorPicker) {
                $('.smartengage-color-picker').wpColorPicker();
            }
        },
        
        /**
         * Setup copy shortcode button
         */
        setupCopyShortcode: function() {
            $('.smartengage-copy-shortcode').on('click', function(e) {
                e.preventDefault();
                
                var $this = $(this);
                var $shortcode = $this.prev('.smartengage-shortcode');
                var $tempInput = $('<input>');
                
                // Create temporary input to copy from
                $('body').append($tempInput);
                $tempInput.val($shortcode.val()).select();
                
                // Copy the text
                document.execCommand('copy');
                
                // Remove temporary input
                $tempInput.remove();
                
                // Update button text temporarily
                var originalText = $this.text();
                $this.text(smartEngageAdmin.strings.success);
                
                setTimeout(function() {
                    $this.text(originalText);
                }, 1000);
            });
        },
        
        /**
         * Setup clone button
         */
        setupCloneButton: function() {
            $('.smartengage-clone-popup').on('click', function(e) {
                e.preventDefault();
                
                if (confirm(smartEngageAdmin.strings.confirmClone)) {
                    var popupId = $(this).data('popup-id');
                    
                    var data = {
                        action: 'smartengage_clone_popup',
                        popup_id: popupId,
                        nonce: smartEngageAdmin.nonce
                    };
                    
                    $.post(smartEngageAdmin.ajaxUrl, data, function(response) {
                        if (response.success) {
                            window.location.href = response.data.redirect;
                        } else {
                            alert(smartEngageAdmin.strings.error);
                        }
                    });
                }
            });
        },
        
        /**
         * Setup reset stats button
         */
        setupResetStats: function() {
            $('.smartengage-reset-stats').on('click', function(e) {
                e.preventDefault();
                
                if (confirm(smartEngageAdmin.strings.confirmReset)) {
                    var popupId = $(this).data('popup-id');
                    
                    var data = {
                        action: 'smartengage_reset_stats',
                        popup_id: popupId,
                        nonce: smartEngageAdmin.nonce
                    };
                    
                    $.post(smartEngageAdmin.ajaxUrl, data, function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(smartEngageAdmin.strings.error);
                        }
                    });
                }
            });
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        SmartEngageAdmin.init();
    });
    
})(jQuery);

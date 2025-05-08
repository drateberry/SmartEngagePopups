/**
 * SmartEngage Popups Frontend JavaScript
 *
 * @package SmartEngage_Popups
 */

(function($) {
    'use strict';

    /**
     * SmartEngage Popups Frontend Class
     */
    var SmartEngagePopups = {
        
        /**
         * Store popup data
         */
        popups: [],
        
        /**
         * Initialize the plugin
         */
        init: function() {
            // Get popups data
            if (typeof smartEngageFrontend !== 'undefined' && smartEngageFrontend.popups) {
                this.popups = smartEngageFrontend.popups;
            }
            
            // Exit if no popups
            if (!this.popups.length) {
                return;
            }
            
            // Initialize each popup
            this.initPopups();
            
            // Setup event handlers
            this.setupEventHandlers();
        },
        
        /**
         * Initialize popups
         */
        initPopups: function() {
            var self = this;
            
            // Process each popup
            $.each(this.popups, function(index, popup) {
                // Check if popup should be shown
                if (!self.shouldShowPopup(popup)) {
                    return;
                }
                
                // Initialize popup triggers
                self.initTriggers(popup);
            });
        },
        
        /**
         * Check if popup should be shown based on frequency rules
         *
         * @param {Object} popup Popup data.
         * @return {boolean} True if popup should be shown, false otherwise.
         */
        shouldShowPopup: function(popup) {
            var cookieName = 'smartengage_popup_' + popup.id;
            var frequency = popup.frequency_rule;
            
            // Check frequency rule
            switch (frequency) {
                case 'once_session':
                    // Don't show if seen in this session
                    if (sessionStorage.getItem(cookieName)) {
                        return false;
                    }
                    break;
                    
                case 'days_between':
                    // Don't show if seen within X days
                    var cookie = this.getCookie(cookieName);
                    if (cookie) {
                        return false;
                    }
                    break;
                    
                case 'max_impressions':
                    // Don't show if max impressions reached
                    var impressions = localStorage.getItem(cookieName);
                    if (impressions && parseInt(impressions, 10) >= popup.max_impressions) {
                        return false;
                    }
                    break;
                    
                case 'every_page':
                    // Always show (no additional check needed)
                    break;
            }
            
            return true;
        },
        
        /**
         * Initialize popup triggers
         *
         * @param {Object} popup Popup data.
         */
        initTriggers: function(popup) {
            var self = this;
            var triggerType = popup.trigger_type;
            
            // Time on page trigger
            if (triggerType.indexOf('time') !== -1) {
                setTimeout(function() {
                    self.showPopup(popup.id);
                }, popup.time_on_page * 1000);
            }
            
            // Scroll depth trigger
            if (triggerType.indexOf('scroll') !== -1) {
                $(window).on('scroll', function() {
                    var scrollPercentage = self.getScrollPercentage();
                    
                    if (scrollPercentage >= popup.scroll_depth) {
                        self.showPopup(popup.id);
                        // Remove scroll event after trigger
                        $(window).off('scroll');
                    }
                });
            }
            
            // Exit intent trigger
            if (popup.exit_intent === 'enabled') {
                // For desktop
                if (!self.isMobile()) {
                    $(document).on('mouseleave', function(e) {
                        if (e.clientY < 0) {
                            self.showPopup(popup.id);
                        }
                    });
                } else {
                    // For mobile (detect quick scroll up)
                    var startY = 0;
                    var scrollThreshold = 30;
                    
                    $(window).on('touchstart', function(e) {
                        startY = e.originalEvent.touches[0].pageY;
                    });
                    
                    $(window).on('touchmove', function(e) {
                        var currentY = e.originalEvent.touches[0].pageY;
                        var scrollPosition = $(window).scrollTop();
                        
                        // If near the bottom of the page and quick scroll up
                        if (scrollPosition > 100 && startY < currentY && (currentY - startY) > scrollThreshold) {
                            self.showPopup(popup.id);
                        }
                        
                        startY = currentY;
                    });
                }
            }
            
            // Page views trigger
            if (triggerType.indexOf('page_views') !== -1) {
                var pageViews = this.getPageViews();
                
                if (pageViews >= popup.page_views) {
                    setTimeout(function() {
                        self.showPopup(popup.id);
                    }, 1000);
                }
            }
        },
        
        /**
         * Setup event handlers
         */
        setupEventHandlers: function() {
            var self = this;
            
            // Close popup when close button or outside area clicked
            $(document).on('click', '.smartengage-popup-close, .smartengage-popup', function(e) {
                if (e.target === this || $(e.target).hasClass('smartengage-popup-close') || $(e.target).parent().hasClass('smartengage-popup-close')) {
                    var popupId = $(this).closest('.smartengage-popup').data('popup-id');
                    self.closePopup(popupId);
                }
            });
            
            // Track conversion when CTA button clicked
            $(document).on('click', '.smartengage-popup-button', function() {
                var popupId = $(this).data('popup-id');
                self.trackConversion(popupId);
                self.closePopup(popupId);
            });
            
            // Close popup on ESC key
            $(document).keyup(function(e) {
                if (e.key === 'Escape') {
                    $('.smartengage-popup:visible').each(function() {
                        var pop
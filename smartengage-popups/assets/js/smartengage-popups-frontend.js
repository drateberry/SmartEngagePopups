/**
 * Frontend JavaScript for SmartEngage Popups
 * Handles popup display, triggers, and interactions with improved z-index handling
 */
(function($) {
    'use strict';

    // Popup Manager class
    class PopupManager {
        constructor() {
            // Initialize properties
            this.popups = {};
            this.displayedPopups = [];
            this.globalSettings = {
                pageViews: parseInt(smartengagePopups.pageViews) || 1,
                globalFrequency: smartengagePopups.globalFrequency || 'session'
            };
            
            // Initialize the manager
            this.init();
        }

        /**
         * Initialize the popup manager
         */
        init() {
            // Find all popups on the page
            this.setupPopups();
            
            // Track page view count for frequency rules
            this.incrementPageViewCount();
        }

        /**
         * Set up all popups on the page
         */
        setupPopups() {
            const self = this;
            
            // Find all popup containers
            $('.smartengage-popup-container').each(function() {
                const $popup = $(this);
                const popupId = $popup.data('popup-id');
                const triggerType = $popup.data('trigger-type');
                const triggerValue = $popup.data('trigger-value');
                
                // Skip if already processed
                if (self.popups[popupId]) return;
                
                // Store popup data
                self.popups[popupId] = {
                    $element: $popup,
                    id: popupId,
                    triggerType: triggerType,
                    triggerValue: triggerValue,
                    displayed: false
                };
                
                // Set up triggers
                self.setupTriggers(self.popups[popupId]);
                
                // Set up close button
                $popup.find('.smartengage-popup-close, .smartengage-popup-overlay').on('click', function(e) {
                    e.preventDefault();
                    self.closePopup(popupId);
                });
                
                // Set up click tracking for buttons
                $popup.find('.smartengage-popup-button').on('click', function() {
                    const elementId = $(this).data('element-id') || '';
                    self.recordPopupClick(popupId, elementId);
                });
            });
        }

        /**
         * Set up triggers for a popup
         * 
         * @param {Object} popup The popup object
         */
        setupTriggers(popup) {
            const self = this;
            
            // Skip if conditions not met
            if (!this.checkAllConditionsMet(popup)) {
                return;
            }
            
            // Set up trigger based on type
            switch (popup.triggerType) {
                case 'time_delay':
                    // Time delay trigger
                    const delay = parseInt(popup.triggerValue) * 1000 || 5000;
                    setTimeout(function() {
                        self.showPopup(popup);
                    }, delay);
                    break;
                    
                case 'scroll_depth':
                    // Scroll depth trigger
                    const targetDepth = parseInt(popup.triggerValue) || 50;
                    $(window).on('scroll', self.debounce(function() {
                        if (self.getScrollPercentage() >= targetDepth && !popup.displayed) {
                            self.showPopup(popup);
                        }
                    }, 200));
                    break;
                    
                case 'exit_intent':
                    // Exit intent trigger
                    $(document).on('mouseleave', function(e) {
                        if (e.clientY < 0 && !popup.displayed) {
                            self.showPopup(popup);
                        }
                    });
                    break;
                    
                case 'click':
                    // Click trigger
                    const selector = popup.triggerValue.trim();
                    if (selector) {
                        $(document).on('click', selector, function(e) {
                            e.preventDefault();
                            self.showPopup(popup);
                        });
                    }
                    break;
                    
                case 'page_views':
                    // Page views trigger
                    const viewsRequired = parseInt(popup.triggerValue) || 3;
                    if (self.globalSettings.pageViews >= viewsRequired && !popup.displayed) {
                        self.showPopup(popup);
                    }
                    break;
            }
        }

        /**
         * Show a popup
         * 
         * @param {Object} popup The popup object
         */
        showPopup(popup) {
            // Check global frequency rules
            if (!this.checkGlobalFrequencyRules()) {
                return;
            }
            
            // Skip if already displayed
            if (popup.displayed) {
                return;
            }
            
            // Double-check z-index to ensure it's on top
            this.ensureZIndexOnTop(popup.$element);
            
            // Mark as displayed
            popup.displayed = true;
            this.displayedPopups.push(popup.id);
            
            // Show the popup with animation
            popup.$element.addClass('active');
            
            // Record the view
            this.recordPopupView(popup.id);
        }
        
        /**
         * Ensure the popup has a higher z-index than any other element on the page
         * This helps troubleshoot issues with popups being hidden behind other elements
         * 
         * @param {jQuery} $element The popup element
         */
        ensureZIndexOnTop($element) {
            // Get current z-index
            let zIndex = parseInt($element.css('z-index'));
            
            // If it's not a number or less than our minimum, use configured z-index or default
            if (isNaN(zIndex) || zIndex < 100) {
                zIndex = window.smartengagePopups && window.smartengagePopups.zIndex ? 
                    parseInt(window.smartengagePopups.zIndex) : 999999;
            }
            
            // Ensure popup overlay has proper z-index
            const $overlay = $element.find('.smartengage-popup-overlay');
            if ($overlay.length) {
                $overlay.css('z-index', 1);
            }
            
            // Ensure popup content has proper z-index
            const $popup = $element.find('.smartengage-popup');
            if ($popup.length) {
                $popup.css('z-index', 2);
            }
            
            // Apply the z-index to the container
            $element.css('z-index', zIndex);
            
            // Debug z-index issues - log to console if enabled
            if (window.smartengagePopups && window.smartengagePopups.debug) {
                console.log('SmartEngage Popups: Setting z-index to ' + zIndex + ' for popup #' + $element.attr('id'));
            }
        }

        /**
         * Close a popup
         * 
         * @param {string} popupId The popup ID
         */
        closePopup(popupId) {
            const popup = this.popups[popupId];
            if (!popup) return;
            
            // Hide the popup with animation
            popup.$element.removeClass('active');
        }

        /**
         * Check if all conditions are met for showing a popup
         * 
         * @param {Object} popup The popup object
         * @return {boolean} Whether all conditions are met
         */
        checkAllConditionsMet(popup) {
            // Check device targeting
            if (!this.checkDeviceTargeting(popup)) {
                return false;
            }
            
            // Check frequency rules
            if (!this.checkFrequencyRules(popup)) {
                return false;
            }
            
            return true;
        }

        /**
         * Check device targeting conditions
         * 
         * @param {Object} popup The popup object
         * @return {boolean} Whether device targeting conditions are met
         */
        checkDeviceTargeting(popup) {
            // Get device targeting settings from data attributes
            const deviceTarget = popup.$element.data('device-target') || 'all';
            
            // All devices
            if (deviceTarget === 'all') {
                return true;
            }
            
            // Check current device
            const isMobile = this.isMobile();
            
            if (deviceTarget === 'mobile' && isMobile) {
                return true;
            }
            
            if (deviceTarget === 'desktop' && !isMobile) {
                return true;
            }
            
            return false;
        }

        /**
         * Check frequency rules for a popup
         * 
         * @param {Object} popup The popup object
         * @return {boolean} Whether frequency rules are met
         */
        checkFrequencyRules(popup) {
            // Get frequency rule from data attributes
            const frequencyRule = popup.$element.data('frequency') || 'session';
            
            // Always show
            if (frequencyRule === 'always') {
                return true;
            }
            
            // Get cookie names
            const viewedCookie = 'smartengage_popup_viewed_' + popup.id;
            
            // Once per session
            if (frequencyRule === 'session') {
                // If cookie exists, don't show again in this session
                if (this.getCookie(viewedCookie)) {
                    return false;
                }
                return true;
            }
            
            // Once every X days
            if (frequencyRule === 'days') {
                const days = parseInt(popup.$element.data('frequency-value')) || 7;
                
                // Check if cookie exists and is within the time limit
                const lastView = this.getLastPopupDisplayTime(popup.id);
                if (lastView) {
                    const now = new Date().getTime();
                    const daysPassed = (now - lastView) / (1000 * 60 * 60 * 24);
                    
                    if (daysPassed < days) {
                        return false;
                    }
                }
                
                return true;
            }
            
            // Once every X page views
            if (frequencyRule === 'views') {
                const pageViews = parseInt(popup.$element.data('frequency-value')) || 3;
                
                // Get last view count
                const lastViewCount = this.getLastViewCount(popup.id);
                
                // If page views since last show is less than required, don't show
                if (lastViewCount && (this.globalSettings.pageViews - lastViewCount) < pageViews) {
                    return false;
                }
                
                return true;
            }
            
            return true;
        }

        /**
         * Check global frequency rules
         * 
         * @return {boolean} Whether global frequency rules allow showing another popup
         */
        checkGlobalFrequencyRules() {
            const globalFrequency = this.globalSettings.globalFrequency;
            
            // No global limit
            if (globalFrequency === 'none') {
                return true;
            }
            
            // One popup per session
            if (globalFrequency === 'session') {
                // If any popup already displayed in this session, don't show another
                if (this.getCookie('smartengage_global_popup_displayed')) {
                    return false;
                }
                
                // Set cookie for this session
                document.cookie = 'smartengage_global_popup_displayed=1; path=/;';
                return true;
            }
            
            // One popup every 24 hours
            if (globalFrequency === 'time') {
                // Check if cookie exists and is within the time limit
                const lastGlobalPopup = this.getCookie('smartengage_global_popup_time');
                if (lastGlobalPopup) {
                    const now = new Date().getTime();
                    const hoursPassed = (now - parseInt(lastGlobalPopup)) / (1000 * 60 * 60);
                    
                    if (hoursPassed < 24) {
                        return false;
                    }
                }
                
                // Set cookie with current timestamp
                document.cookie = 'smartengage_global_popup_time=' + new Date().getTime() + '; path=/; max-age=86400;';
                return true;
            }
            
            return true;
        }

        /**
         * Increment the page view counter
         */
        incrementPageViewCount() {
            // This is handled by the PHP backend
        }

        /**
         * Record a popup view
         * 
         * @param {string} popupId The popup ID
         */
        recordPopupView(popupId) {
            // Send AJAX request
            $.ajax({
                url: smartengagePopups.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smartengage_record_view',
                    popup_id: popupId
                }
            });
        }

        /**
         * Record a popup click
         * 
         * @param {string} popupId The popup ID
         * @param {string} elementId The clicked element ID
         */
        recordPopupClick(popupId, elementId) {
            // Send AJAX request
            $.ajax({
                url: smartengagePopups.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'smartengage_record_click',
                    popup_id: popupId,
                    element_id: elementId
                }
            });
        }

        /**
         * Get the scroll percentage
         * 
         * @return {number} The scroll percentage (0-100)
         */
        getScrollPercentage() {
            const documentHeight = $(document).height() - $(window).height();
            const scrollTop = $(window).scrollTop();
            
            return (scrollTop / documentHeight) * 100;
        }

        /**
         * Check if the visitor is on a mobile device
         * 
         * @return {boolean} Whether the visitor is on a mobile device
         */
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        /**
         * Get a cookie value
         * 
         * @param {string} name The cookie name
         * @return {string|null} The cookie value or null if not found
         */
        getCookie(name) {
            const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
            return match ? match[3] : null;
        }

        /**
         * Get the timestamp of the last time a popup was displayed
         * 
         * @param {string} popupId The popup ID
         * @return {number|null} The timestamp or null if not found
         */
        getLastPopupDisplayTime(popupId) {
            const cookie = this.getCookie('smartengage_popup_viewed_' + popupId);
            return cookie ? parseInt(cookie) : null;
        }

        /**
         * Get the page view count when a popup was last displayed
         * 
         * @param {string} popupId The popup ID
         * @return {number|null} The page view count or null if not found
         */
        getLastViewCount(popupId) {
            const cookie = this.getCookie('smartengage_popup_view_count_' + popupId);
            return cookie ? parseInt(cookie) : null;
        }

        /**
         * Debounce function to limit how often a function can run
         * 
         * @param {Function} func The function to debounce
         * @param {number} wait The debounce wait time in milliseconds
         * @return {Function} The debounced function
         */
        debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }
    }

    // Initialize the popup manager when the document is ready
    $(function() {
        window.smartengagePopupManager = new PopupManager();
    });

})(jQuery);
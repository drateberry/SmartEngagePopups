/**
 * Frontend JavaScript for SmartEngage Popups
 */
(function($) {
    'use strict';

    // Popup Manager
    class PopupManager {
        constructor() {
            this.popups = SmartEngagePopups.popups || [];
            this.initialized = false;
            this.init();
        }

        init() {
            if (this.initialized || this.popups.length === 0) {
                return;
            }

            this.initialized = true;
            this.setupPopups();
        }

        setupPopups() {
            this.popups.forEach(popup => {
                // Check device targeting
                if (!this.checkDeviceTargeting(popup)) {
                    return;
                }

                // Check user role targeting
                if (!this.checkUserRoleTargeting(popup)) {
                    return;
                }

                // Check user status targeting
                if (!this.checkUserStatusTargeting(popup)) {
                    return;
                }

                // Check URL targeting
                if (!this.checkUrlTargeting(popup)) {
                    return;
                }

                // Check referrer targeting
                if (!this.checkReferrerTargeting(popup)) {
                    return;
                }

                // Check cookie targeting
                if (!this.checkCookieTargeting(popup)) {
                    return;
                }

                // Check frequency rules
                if (!this.checkFrequencyRules(popup)) {
                    return;
                }

                // Setup trigger conditions
                this.setupTriggers(popup);
            });
        }

        setupTriggers(popup) {
            const triggers = popup.triggers;
            const popupId = popup.id;
            let triggered = false;

            // Function to show the popup
            const showPopup = () => {
                if (triggered) {
                    return;
                }
                
                triggered = true;
                this.showPopup(popup);
            };

            // Set up each trigger
            const timeDelay = parseInt(triggers.timeDelay);
            const scrollDepth = parseInt(triggers.scrollDepth);
            const exitIntent = triggers.exitIntent;
            const pageViews = parseInt(triggers.pageViews);
            
            // Time delay trigger
            if (!isNaN(timeDelay) && timeDelay > 0) {
                setTimeout(() => {
                    if (triggers.conditionsMatch === 'any') {
                        showPopup();
                    } else {
                        this.checkAllConditionsMet(popup) && showPopup();
                    }
                }, timeDelay * 1000);
            }

            // Scroll depth trigger
            if (!isNaN(scrollDepth) && scrollDepth > 0) {
                $(window).on('scroll', this.debounce(() => {
                    const scrollPercentage = this.getScrollPercentage();
                    if (scrollPercentage >= scrollDepth) {
                        if (triggers.conditionsMatch === 'any') {
                            showPopup();
                        } else {
                            this.checkAllConditionsMet(popup) && showPopup();
                        }
                    }
                }, 200));
            }

            // Exit intent trigger
            if (exitIntent) {
                // Desktop exit intent
                if (!this.isMobile()) {
                    $(document).on('mouseleave', (e) => {
                        if (e.clientY <= 0) {
                            if (triggers.conditionsMatch === 'any') {
                                showPopup();
                            } else {
                                this.checkAllConditionsMet(popup) && showPopup();
                            }
                        }
                    });
                } else {
                    // Mobile "exit intent" (quick scroll up at page bottom)
                    let lastScrollTop = 0;
                    $(window).on('scroll', this.debounce(() => {
                        const scrollTop = $(window).scrollTop();
                        const scrollPercentage = this.getScrollPercentage();
                        
                        if (scrollPercentage > 70 && scrollTop < lastScrollTop) {
                            if (triggers.conditionsMatch === 'any') {
                                showPopup();
                            } else {
                                this.checkAllConditionsMet(popup) && showPopup();
                            }
                        }
                        
                        lastScrollTop = scrollTop;
                    }, 200));
                }
            }

            // Page views trigger
            if (!isNaN(pageViews) && pageViews > 0) {
                const currentPageViews = this.incrementPageViewCount();
                
                if (currentPageViews >= pageViews) {
                    if (triggers.conditionsMatch === 'any') {
                        showPopup();
                    } else {
                        this.checkAllConditionsMet(popup) && showPopup();
                    }
                }
            }
        }

        showPopup(popup) {
            const $popup = $('#smartengage-popup-' + popup.id);
            
            if ($popup.length === 0 || $popup.is(':visible')) {
                return;
            }
            
            // Set up popup based on type and position
            if (popup.type === 'slide-in') {
                $popup.addClass('type-slide-in position-' + popup.position);
            } else {
                $popup.addClass('type-full-screen');
                $('body').addClass('smartengage-popup-open');
            }
            
            // Show the popup with animation
            $popup.fadeIn(300);
            
            // Record view
            this.recordPopupView(popup.id);
            
            // Keep track of display in user's browser
            this.recordPopupDisplay(popup.id);
            
            // Set up close button
            $popup.find('.smartengage-popup-close').on('click', (e) => {
                e.preventDefault();
                this.closePopup(popup.id);
            });
            
            // Set up CTA button click tracking
            $popup.find('.smartengage-popup-button').on('click', () => {
                this.recordPopupClick(popup.id);
            });
            
            // Set up keyboard navigation for accessibility
            $(document).on('keydown.smartengagepopup', (e) => {
                if (e.key === 'Escape') {
                    this.closePopup(popup.id);
                }
            });
        }

        closePopup(popupId) {
            const $popup = $('#smartengage-popup-' + popupId);
            
            $popup.fadeOut(300, function() {
                if ($popup.hasClass('type-full-screen')) {
                    $('body').removeClass('smartengage-popup-open');
                }
            });
            
            // Remove keyboard event handler
            $(document).off('keydown.smartengagepopup');
        }

        checkDeviceTargeting(popup) {
            const deviceType = popup.targeting.deviceType;
            
            if (deviceType === 'all') {
                return true;
            }
            
            const isMobile = this.isMobile();
            
            return (deviceType === 'mobile' && isMobile) || (deviceType === 'desktop' && !isMobile);
        }

        checkUserRoleTargeting(popup) {
            const userRoles = popup.targeting.userRoles;
            
            // If no roles specified, show to all
            if (!userRoles || userRoles.length === 0) {
                return true;
            }
            
            // This check will happen server-side with the popup rendering, 
            // since JavaScript doesn't have access to user role information
            return true;
        }

        checkUserStatusTargeting(popup) {
            const userStatus = popup.triggers.userStatus;
            
            // If set to all, show to everyone
            if (userStatus === 'all') {
                return true;
            }
            
            // This is already handled server-side during popup rendering
            return true;
        }

        checkUrlTargeting(popup) {
            const specificUrl = popup.triggers.specificUrl;
            
            if (!specificUrl) {
                return true;
            }
            
            const currentPath = window.location.pathname;
            
            // Convert wildcard pattern to regex
            const pattern = specificUrl.replace(/\*/g, '.*');
            const regex = new RegExp('^' + pattern + '$');
            
            return regex.test(currentPath);
        }

        checkReferrerTargeting(popup) {
            const referrerUrl = popup.targeting.referrerUrl;
            
            if (!referrerUrl) {
                return true;
            }
            
            const referrer = document.referrer;
            
            if (!referrer) {
                return false;
            }
            
            // Convert wildcard pattern to regex
            const pattern = referrerUrl.replace(/\*/g, '.*');
            const regex = new RegExp(pattern);
            
            return regex.test(referrer);
        }

        checkCookieTargeting(popup) {
            const cookieName = popup.targeting.cookieName;
            const cookieValue = popup.targeting.cookieValue;
            
            if (!cookieName) {
                return true;
            }
            
            const cookie = this.getCookie(cookieName);
            
            if (!cookie) {
                return false;
            }
            
            // If no specific value required, just check if cookie exists
            if (!cookieValue) {
                return true;
            }
            
            return cookie === cookieValue;
        }

        checkFrequencyRules(popup) {
            const frequencyType = popup.frequency.type;
            const daysBetween = parseInt(popup.frequency.daysBetween);
            const maxImpressions = parseInt(popup.frequency.maxImpressions);
            const popupId = popup.id;
            
            // Check max impressions
            if (!isNaN(maxImpressions) && maxImpressions > 0) {
                const impressions = this.getPopupImpressions(popupId);
                
                if (impressions >= maxImpressions) {
                    return false;
                }
            }
            
            // Check frequency type
            switch (frequencyType) {
                case 'once_per_session':
                    return !this.wasPopupDisplayedInSession(popupId);
                
                case 'every_x_days':
                    const lastDisplayed = this.getLastPopupDisplayTime(popupId);
                    
                    if (!lastDisplayed) {
                        return true;
                    }
                    
                    const daysSinceDisplay = Math.floor((Date.now() - lastDisplayed) / (1000 * 60 * 60 * 24));
                    return daysSinceDisplay >= daysBetween;
                
                case 'every_page':
                    return true;
                
                default:
                    return true;
            }
        }

        checkAllConditionsMet(popup) {
            const triggers = popup.triggers;
            let conditionsMet = 0;
            let totalConditions = 0;
            
            // Check time delay
            if (!isNaN(parseInt(triggers.timeDelay)) && parseInt(triggers.timeDelay) > 0) {
                totalConditions++;
                // We can't check this one in this function since it's time-based
                // We'll assume it's met if we're checking
                conditionsMet++;
            }
            
            // Check scroll depth
            if (!isNaN(parseInt(triggers.scrollDepth)) && parseInt(triggers.scrollDepth) > 0) {
                totalConditions++;
                const scrollPercentage = this.getScrollPercentage();
                if (scrollPercentage >= parseInt(triggers.scrollDepth)) {
                    conditionsMet++;
                }
            }
            
            // Check exit intent - can't really check this one proactively
            if (triggers.exitIntent) {
                totalConditions++;
                // We'll assume it's met if we're checking
                conditionsMet++;
            }
            
            // Check page views
            if (!isNaN(parseInt(triggers.pageViews)) && parseInt(triggers.pageViews) > 0) {
                totalConditions++;
                const currentPageViews = this.getPageViewCount();
                if (currentPageViews >= parseInt(triggers.pageViews)) {
                    conditionsMet++;
                }
            }
            
            // If no conditions are set, return true
            if (totalConditions === 0) {
                return true;
            }
            
            return conditionsMet === totalConditions;
        }

        incrementPageViewCount() {
            const pageViewsKey = 'smartengage_page_views';
            let pageViews = parseInt(localStorage.getItem(pageViewsKey) || '0');
            
            pageViews++;
            localStorage.setItem(pageViewsKey, pageViews.toString());
            
            return pageViews;
        }

        getPageViewCount() {
            const pageViewsKey = 'smartengage_page_views';
            return parseInt(localStorage.getItem(pageViewsKey) || '0');
        }

        wasPopupDisplayedInSession(popupId) {
            return sessionStorage.getItem('smartengage_popup_' + popupId) === 'displayed';
        }

        getLastPopupDisplayTime(popupId) {
            const timestamp = localStorage.getItem('smartengage_popup_time_' + popupId);
            return timestamp ? parseInt(timestamp) : null;
        }

        getPopupImpressions(popupId) {
            return parseInt(localStorage.getItem('smartengage_popup_impressions_' + popupId) || '0');
        }

        recordPopupDisplay(popupId) {
            // Record in session storage (for once_per_session)
            sessionStorage.setItem('smartengage_popup_' + popupId, 'displayed');
            
            // Record timestamp in local storage (for every_x_days)
            localStorage.setItem('smartengage_popup_time_' + popupId, Date.now().toString());
            
            // Increment impressions counter (for max_impressions)
            const impressions = this.getPopupImpressions(popupId);
            localStorage.setItem('smartengage_popup_impressions_' + popupId, (impressions + 1).toString());
        }

        recordPopupView(popupId) {
            $.post(SmartEngagePopups.ajaxurl, {
                action: 'smartengage_record_view',
                popup_id: popupId,
                nonce: SmartEngagePopups.nonce
            });
        }

        recordPopupClick(popupId) {
            $.post(SmartEngagePopups.ajaxurl, {
                action: 'smartengage_record_click',
                popup_id: popupId,
                nonce: SmartEngagePopups.nonce
            });
        }

        getScrollPercentage() {
            const scrollTop = $(window).scrollTop();
            const documentHeight = $(document).height();
            const windowHeight = $(window).height();
            
            return (scrollTop / (documentHeight - windowHeight)) * 100;
        }

        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        getCookie(name) {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? match[2] : null;
        }

        debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        new PopupManager();
    });

})(jQuery);

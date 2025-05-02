/**
 * SmartEngage Popups - Modern Builder JavaScript
 * A Squarespace-inspired popup builder with intuitive drag-and-drop functionality
 */
(function($) {
    'use strict';

    // Modern Builder class
    class PopupBuilderModern {
        constructor() {
            // Element references
            this.$canvas = $('#smartengage-builder-canvas');
            this.$propertiesPanel = $('.smartengage-properties-panel');
            this.selectedElement = null;
            this.elements = [];
            this.undoStack = [];
            this.redoStack = [];
            this.templates = {
                'template-1': [
                    {
                        type: 'heading',
                        content: 'Join Our Newsletter',
                        position: { left: 50, top: 50 },
                        size: { width: 500, height: 60 },
                        color: '#333333',
                        fontSize: 28
                    },
                    {
                        type: 'text',
                        content: 'Sign up to receive updates, special offers, and more delivered straight to your inbox.',
                        position: { left: 50, top: 120 },
                        size: { width: 500, height: 80 },
                        color: '#666666',
                        fontSize: 16
                    },
                    {
                        type: 'button',
                        content: 'Subscribe Now',
                        position: { left: 50, top: 220 },
                        size: { width: 200, height: 50 },
                        url: '#',
                        color: '#ffffff',
                        backgroundColor: '#4361ee',
                        fontSize: 16
                    }
                ],
                'template-2': [
                    {
                        type: 'heading',
                        content: 'Limited Time Offer',
                        position: { left: 50, top: 50 },
                        size: { width: 500, height: 60 },
                        color: '#333333',
                        fontSize: 28
                    },
                    {
                        type: 'text',
                        content: 'Get 20% off your first purchase when you sign up today!',
                        position: { left: 50, top: 120 },
                        size: { width: 500, height: 80 },
                        color: '#666666',
                        fontSize: 16
                    },
                    {
                        type: 'button',
                        content: 'Claim Discount',
                        position: { left: 50, top: 220 },
                        size: { width: 200, height: 50 },
                        url: '#',
                        color: '#ffffff',
                        backgroundColor: '#ef4444',
                        fontSize: 16
                    }
                ],
                'template-3': [
                    {
                        type: 'image',
                        position: { left: 50, top: 50 },
                        size: { width: 200, height: 200 },
                        src: '',
                        alt: 'Announcement Image'
                    },
                    {
                        type: 'heading',
                        content: 'Important Announcement',
                        position: { left: 270, top: 50 },
                        size: { width: 280, height: 60 },
                        color: '#333333',
                        fontSize: 24
                    },
                    {
                        type: 'text',
                        content: 'We have some exciting news to share with you! Stay tuned for updates.',
                        position: { left: 270, top: 120 },
                        size: { width: 280, height: 130 },
                        color: '#666666',
                        fontSize: 16
                    }
                ],
                'template-4': [
                    {
                        type: 'heading',
                        content: 'Ready to get started?',
                        position: { left: 50, top: 100 },
                        size: { width: 500, height: 60 },
                        color: '#333333',
                        fontSize: 28
                    },
                    {
                        type: 'button',
                        content: 'Get Started Now',
                        position: { left: 175, top: 180 },
                        size: { width: 250, height: 60 },
                        url: '#',
                        color: '#ffffff',
                        backgroundColor: '#16a34a',
                        fontSize: 18
                    }
                ]
            };
            
            // Color schemes
            this.colorSchemes = {
                'blue': {
                    heading: '#1e40af',
                    text: '#1e3a8a',
                    button: {
                        bg: '#3b82f6',
                        text: '#ffffff'
                    }
                },
                'green': {
                    heading: '#15803d',
                    text: '#166534',
                    button: {
                        bg: '#22c55e',
                        text: '#ffffff'
                    }
                },
                'red': {
                    heading: '#b91c1c',
                    text: '#991b1b',
                    button: {
                        bg: '#ef4444',
                        text: '#ffffff'
                    }
                },
                'purple': {
                    heading: '#6d28d9',
                    text: '#5b21b6',
                    button: {
                        bg: '#8b5cf6',
                        text: '#ffffff'
                    }
                }
            };

            // Initialize the builder
            this.initBuilder();
        }

        /**
         * Initialize the popup builder
         */
        initBuilder() {
            this.setupEventListeners();
            this.loadPopupDesign();
            
            // Initialize tooltips
            this.initTooltips();
            
            // Make canvas droppable
            this.setupCanvasDroppable();
            
            // Initialize element palette
            this.setupElementPalette();
        }
        
        /**
         * Setup all event listeners
         */
        setupEventListeners() {
            // Save button
            $('#smartengage-save-design').on('click', () => this.savePopupDesign());
            
            // Reset button
            $('#smartengage-reset-design').on('click', () => this.resetDesign());
            
            // Preview button
            $('#smartengage-preview-design').on('click', () => this.previewDesign());
            
            // Delete element button
            $('#smartengage-delete-element').on('click', () => this.deleteSelectedElement());
            
            // Property changes
            $('.smartengage-property-input').on('change keyup', (e) => {
                if (this.selectedElement) {
                    const $input = $(e.target);
                    const property = $input.data('property');
                    const value = $input.val();
                    
                    this.updateElementProperty(property, value);
                }
            });
            
            // Color picker changes
            $(document).on('se-color-change', '.smartengage-color-input', (e, color) => {
                if (this.selectedElement) {
                    const $input = $(e.target);
                    const property = $input.data('property');
                    
                    this.updateElementProperty(property, color);
                }
            });
            
            // Canvas click (deselect)
            this.$canvas.on('click', (e) => {
                if ($(e.target).is(this.$canvas)) {
                    this.deselectElement();
                }
            });
            
            // Template selection
            $('.smartengage-preset-item[data-preset]').on('click', (e) => {
                const preset = $(e.currentTarget).data('preset');
                this.applyTemplate(preset);
            });
            
            // Color scheme selection
            $('.smartengage-preset-item[data-color-scheme]').on('click', (e) => {
                const scheme = $(e.currentTarget).data('color-scheme');
                this.applyColorScheme(scheme);
            });
            
            // Document listener for element selection
            $(document).on('click', '.builder-element', (e) => {
                e.stopPropagation();
                this.selectElement(e.currentTarget);
            });
        }
        
        /**
         * Make canvas droppable
         */
        setupCanvasDroppable() {
            // Enhanced dropzone with visual indicators and improved UX
            this.$canvas.droppable({
                accept: '.smartengage-element',
                drop: (event, ui) => this.handleElementDrop(event, ui),
                over: (event, ui) => {
                    // Add a visual cue when dragging over canvas
                    this.$canvas.addClass('drag-over');
                    
                    // Show a helpful drop indicator
                    if (!$('.smartengage-drop-indicator').length) {
                        const $indicator = $('<div>', {
                            class: 'smartengage-drop-indicator',
                            css: {
                                width: '100px',
                                height: '50px',
                                left: (ui.position.left - this.$canvas.offset().left) + 'px',
                                top: (ui.position.top - this.$canvas.offset().top) + 'px'
                            }
                        });
                        this.$canvas.append($indicator);
                    } else {
                        $('.smartengage-drop-indicator').css({
                            left: (ui.position.left - this.$canvas.offset().left) + 'px',
                            top: (ui.position.top - this.$canvas.offset().top) + 'px'
                        });
                    }
                },
                out: (event, ui) => {
                    // Remove visual cues when dragging out
                    this.$canvas.removeClass('drag-over');
                    $('.smartengage-drop-indicator').remove();
                }
            });
            
            // Initialize zoom functionality
            this.setupCanvasZoom();
        }
        
        /**
         * Setup element palette items with enhanced drag-and-drop
         */
        setupElementPalette() {
            $('.smartengage-element').draggable({
                helper: 'clone',
                appendTo: 'body',
                revert: 'invalid',
                zIndex: 1000,
                opacity: 0.85,
                containment: 'window',
                start: (event, ui) => {
                    // Add visual effects when dragging starts
                    $(ui.helper).addClass('element-being-dragged');
                    this.$canvas.addClass('awaiting-drop');
                    
                    // Add pulsing animation to the canvas
                    $('.smartengage-canvas-wrapper').addClass('pulse-highlight');
                },
                stop: (event, ui) => {
                    // Clean up visual effects
                    this.$canvas.removeClass('awaiting-drop');
                    $('.smartengage-canvas-wrapper').removeClass('pulse-highlight');
                    $('.smartengage-drop-indicator').remove();
                }
            });
        }
        
        /**
         * Setup canvas zoom functionality
         */
        setupCanvasZoom() {
            let zoomLevel = 100; // percentage
            const $zoomLevel = $('.zoom-level');
            const $canvas = this.$canvas;
            const $wrapper = $('.smartengage-canvas-wrapper');
            
            // Zoom in button
            $('.zoom-in-btn').on('click', () => {
                if (zoomLevel < 200) {
                    zoomLevel += 10;
                    updateZoom();
                }
            });
            
            // Zoom out button
            $('.zoom-out-btn').on('click', () => {
                if (zoomLevel > 50) {
                    zoomLevel -= 10;
                    updateZoom();
                }
            });
            
            // Reset zoom button
            $('.zoom-reset-btn').on('click', () => {
                zoomLevel = 100;
                updateZoom();
            });
            
            // Mouse wheel zoom
            $wrapper.on('wheel', (e) => {
                if (e.originalEvent.ctrlKey) {
                    e.preventDefault();
                    
                    if (e.originalEvent.deltaY < 0 && zoomLevel < 200) {
                        // Scroll up - zoom in
                        zoomLevel += 5;
                    } else if (e.originalEvent.deltaY > 0 && zoomLevel > 50) {
                        // Scroll down - zoom out
                        zoomLevel -= 5;
                    }
                    
                    updateZoom();
                }
            });
            
            // Update zoom function
            const updateZoom = () => {
                const scale = zoomLevel / 100;
                $canvas.css({
                    transform: `scale(${scale})`,
                    transformOrigin: 'center center'
                });
                $zoomLevel.text(zoomLevel + '%');
            };
        }
        
        /**
         * Initialize tooltips
         */
        initTooltips() {
            // Already handled via CSS for simplicity
        }
        
        /**
         * Handle dropping a new element onto the canvas
         */
        handleElementDrop(event, ui) {
            const elementType = ui.draggable.data('element-type');
            const canvasOffset = this.$canvas.offset();
            const dropX = ui.offset.left - canvasOffset.left;
            const dropY = ui.offset.top - canvasOffset.top;
            
            // Create a unique ID for the element
            const elementId = 'element-' + Date.now();
            
            // Create element based on type
            let $element;
            let elementData = {
                id: elementId,
                type: elementType,
                position: {
                    left: dropX,
                    top: dropY
                }
            };
            
            switch (elementType) {
                case 'heading':
                    $element = $('<h2>', {
                        class: 'builder-element element-heading',
                        id: elementId,
                        text: 'Heading Text',
                        'data-element-type': elementType
                    });
                    
                    elementData.content = 'Heading Text';
                    elementData.color = '#333333';
                    elementData.fontSize = 24;
                    elementData.size = {
                        width: 300,
                        height: 40
                    };
                    break;
                    
                case 'text':
                    $element = $('<p>', {
                        class: 'builder-element element-text',
                        id: elementId,
                        text: 'Your text here. Click to edit.',
                        'data-element-type': elementType
                    });
                    
                    elementData.content = 'Your text here. Click to edit.';
                    elementData.color = '#666666';
                    elementData.fontSize = 16;
                    elementData.size = {
                        width: 300,
                        height: 80
                    };
                    break;
                    
                case 'button':
                    $element = $('<a>', {
                        class: 'builder-element element-button',
                        id: elementId,
                        href: '#',
                        text: 'Button Text',
                        'data-element-type': elementType
                    });
                    
                    elementData.content = 'Button Text';
                    elementData.url = '#';
                    elementData.color = '#ffffff';
                    elementData.backgroundColor = '#4361ee';
                    elementData.fontSize = 16;
                    elementData.size = {
                        width: 150,
                        height: 40
                    };
                    break;
                    
                case 'image':
                    $element = $('<div>', {
                        class: 'builder-element element-image',
                        id: elementId,
                        'data-element-type': elementType,
                        html: '<div class="image-placeholder">Click to select image</div>'
                    });
                    
                    elementData.src = '';
                    elementData.alt = '';
                    elementData.size = {
                        width: 200,
                        height: 150
                    };
                    break;
                    
                case 'divider':
                    $element = $('<hr>', {
                        class: 'builder-element element-divider',
                        id: elementId,
                        'data-element-type': elementType
                    });
                    
                    elementData.size = {
                        width: 300,
                        height: 2
                    };
                    break;
                    
                case 'spacer':
                    $element = $('<div>', {
                        class: 'builder-element element-spacer',
                        id: elementId,
                        'data-element-type': elementType,
                        html: '<div class="spacer-inner"></div>'
                    });
                    
                    elementData.size = {
                        width: 200,
                        height: 50
                    };
                    break;
            }
            
            if ($element) {
                // Add element controls
                const $controls = $('<div>', {
                    class: 'builder-element-controls',
                    html: `
                        <div class="builder-element-control move" title="Move">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M5 9l-3 3 3 3"></path>
                                <path d="M9 5l3-3 3 3"></path>
                                <path d="M15 19l3-3 3 3"></path>
                                <path d="M19 9l3 3-3 3"></path>
                                <path d="M2 12h20"></path>
                                <path d="M12 2v20"></path>
                            </svg>
                        </div>
                        <div class="builder-element-control delete" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    `
                });
                
                $element.append($controls);
                
                // Position the element
                $element.css({
                    position: 'absolute',
                    left: dropX + 'px',
                    top: dropY + 'px',
                    width: elementData.size.width + 'px',
                    height: elementData.size.height + 'px'
                });
                
                // Add to canvas with animation effect - more Squarespace-like
                this.$canvas.append($element);
                $element.hide().fadeIn(300).addClass('just-added');
                setTimeout(() => {
                    $element.removeClass('just-added');
                }, 1000);
                
                // Make element selectable and editable
                $element.on('click', (e) => {
                    e.stopPropagation();
                    this.selectElement($element[0]);
                });
                
                // Add direct inline text editing for applicable elements
                if (['heading', 'text', 'button'].includes(elementType)) {
                    $element.on('dblclick', (e) => {
                        e.stopPropagation();
                        
                        // Highlight the editing state
                        $element.addClass('editing-content');
                        
                        // Make element editable
                        $element.attr('contenteditable', true).focus();
                        document.execCommand('selectAll', false, null);
                        
                        const originalText = $element.text();
                        
                        // Handle completing the edit
                        const finishEdit = () => {
                            $element.attr('contenteditable', false);
                            $element.removeClass('editing-content');
                            const newText = $element.text();
                            
                            if (newText !== originalText) {
                                // Update element data
                                const elementId = $element.attr('id');
                                const element = this.findElementById(elementId);
                                
                                if (element) {
                                    element.content = newText;
                                    this.updatePopupJson();
                                    
                                    // Also update the property panel if this element is selected
                                    if (this.selectedElement && $(this.selectedElement).attr('id') === elementId) {
                                        $('#element-text-content').val(newText);
                                    }
                                }
                            }
                        };
                        
                        // Save on blur or Enter
                        $element.on('blur.contentEdit', finishEdit);
                        $element.on('keydown.contentEdit', (e) => {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                finishEdit();
                                $element.off('.contentEdit');
                            } else if (e.key === 'Escape') {
                                e.preventDefault();
                                $element.text(originalText);
                                finishEdit();
                                $element.off('.contentEdit');
                            }
                        });
                    });
                }
                
                // Make element draggable with enhanced visual feedback
                $element.draggable({
                    containment: 'parent',
                    handle: '.move',
                    cursor: 'move',
                    opacity: 0.9,
                    zIndex: 100,
                    start: (event, ui) => {
                        // Visual feedback that element is being moved
                        $element.addClass('being-dragged');
                        this.selectElement($element[0]);
                        
                        // Show alignment guides/grid
                        this.$canvas.addClass('show-grid');
                        
                        // Create position tooltip if it doesn't exist
                        if (!$('#position-tooltip').length) {
                            $('body').append('<div id="position-tooltip" class="smartengage-position-tooltip"></div>');
                        }
                    },
                    drag: (event, ui) => {
                        // Real-time position display
                        $('#position-tooltip').html(`X: ${Math.round(ui.position.left)}px, Y: ${Math.round(ui.position.top)}px`).css({
                            left: event.pageX + 10,
                            top: event.pageY + 10
                        });
                        
                        // Update position inputs in real-time
                        $('#element-left').val(Math.round(ui.position.left));
                        $('#element-top').val(Math.round(ui.position.top));
                    },
                    stop: (event, ui) => {
                        // Clean up visual effects
                        $element.removeClass('being-dragged');
                        this.$canvas.removeClass('show-grid');
                        $('#position-tooltip').remove();
                        
                        // Snap to grid for precise positioning (optional)
                        const gridSize = 5; // 5px grid
                        const snappedLeft = Math.round(ui.position.left / gridSize) * gridSize;
                        const snappedTop = Math.round(ui.position.top / gridSize) * gridSize;
                        
                        // Update position in data
                        const elementId = $(ui.helper).attr('id');
                        const element = this.findElementById(elementId);
                        
                        if (element) {
                            // Apply snapped position
                            $element.css({
                                left: snappedLeft + 'px',
                                top: snappedTop + 'px'
                            });
                            
                            element.position.left = snappedLeft;
                            element.position.top = snappedTop;
                            
                            // Update position inputs
                            $('#element-left').val(snappedLeft);
                            $('#element-top').val(snappedTop);
                            
                            // Update JSON
                            this.updatePopupJson();
                        }
                    }
                });
                
                // Make element resizable with enhanced visual feedback
                $element.resizable({
                    containment: 'parent',
                    handles: 'all',
                    minWidth: 20,
                    minHeight: 20,
                    classes: {
                        "ui-resizable-handle": "smartengage-resize-handle"
                    },
                    start: (event, ui) => {
                        // Visual feedback
                        $element.addClass('being-resized');
                        this.selectElement($element[0]);
                        
                        // Create size tooltip if it doesn't exist
                        if (!$('#size-tooltip').length) {
                            $('body').append('<div id="size-tooltip" class="smartengage-size-tooltip"></div>');
                        }
                    },
                    resize: (event, ui) => {
                        // Real-time size display
                        $('#size-tooltip').html(`W: ${Math.round(ui.size.width)}px, H: ${Math.round(ui.size.height)}px`).css({
                            left: event.pageX + 10,
                            top: event.pageY + 10
                        });
                        
                        // Update size inputs in real-time
                        $('#element-width').val(Math.round(ui.size.width));
                        $('#element-height').val(Math.round(ui.size.height));
                    },
                    stop: (event, ui) => {
                        // Clean up visual effects
                        $element.removeClass('being-resized');
                        $('#size-tooltip').remove();
                        
                        // Update size in data
                        const elementId = $(ui.helper).attr('id');
                        const element = this.findElementById(elementId);
                        
                        if (element) {
                            element.size.width = ui.size.width;
                            element.size.height = ui.size.height;
                            
                            // Update JSON
                            this.updatePopupJson();
                        }
                    }
                });
                
                // Add delete handler
                $element.find('.delete').on('click', (e) => {
                    e.stopPropagation();
                    this.deleteElement(elementId);
                });
                
                // Store element data
                this.elements.push(elementData);
                
                // Select the newly created element
                this.selectElement($element[0]);
                
                // Update JSON
                this.updatePopupJson();
            }
        }
        
        /**
         * Select an element
         */
        selectElement(element) {
            // Deselect any previously selected element
            this.deselectElement();
            
            const $element = $(element);
            this.selectedElement = element;
            
            // Add selected class
            $element.addClass('selected');
            
            // Show element properties
            this.showElementProperties($element);
            
            // Show the delete section
            $('.smartengage-delete-section').show();
            
            // Switch to properties tab
            $('.smartengage-sidebar-tab[data-tab="properties"]').click();
        }
        
        /**
         * Deselect the currently selected element
         */
        deselectElement() {
            $('.builder-element').removeClass('selected');
            this.selectedElement = null;
            
            // Hide all property sections
            $('.smartengage-position-size-section').hide();
            $('.smartengage-text-properties-section').hide();
            $('.smartengage-button-properties-section').hide();
            $('.smartengage-image-properties-section').hide();
            $('.smartengage-delete-section').hide();
            
            // Show the no element selected message
            $('.smartengage-no-element-selected').show();
        }
        
        /**
         * Show properties for the selected element
         */
        showElementProperties($element) {
            // Hide the no element selected message
            $('.smartengage-no-element-selected').hide();
            
            // Always show position & size
            $('.smartengage-position-size-section').show();
            
            // Get element data
            const elementId = $element.attr('id');
            const elementType = $element.data('element-type');
            const element = this.findElementById(elementId);
            
            if (!element) return;
            
            // Set position and size values
            $('#element-left').val(element.position.left);
            $('#element-top').val(element.position.top);
            $('#element-width').val(element.size.width);
            $('#element-height').val(element.size.height);
            
            // Show type-specific properties
            switch (elementType) {
                case 'heading':
                case 'text':
                    $('.smartengage-text-properties-section').show();
                    $('#element-text-content').val(element.content);
                    $('#element-font-size').val(element.fontSize);
                    $('#element-color').val(element.color);
                    $('#element-color-preview').css('background-color', element.color);
                    break;
                    
                case 'button':
                    $('.smartengage-button-properties-section').show();
                    $('#element-button-text').val(element.content);
                    $('#element-button-url').val(element.url);
                    $('#element-button-bg-color').val(element.backgroundColor);
                    $('#element-button-bg-color-preview').css('background-color', element.backgroundColor);
                    $('#element-button-text-color').val(element.color);
                    $('#element-button-text-color-preview').css('background-color', element.color);
                    break;
                    
                case 'image':
                    $('.smartengage-image-properties-section').show();
                    $('#element-image-url').val(element.src || '');
                    $('#element-image-alt').val(element.alt || '');
                    break;
            }
        }
        
        /**
         * Update a property of the selected element
         */
        updateElementProperty(property, value) {
            if (!this.selectedElement) return;
            
            const $element = $(this.selectedElement);
            const elementId = $element.attr('id');
            const elementType = $element.data('element-type');
            const element = this.findElementById(elementId);
            
            if (!element) return;
            
            // Update based on property type
            switch (property) {
                case 'left':
                    element.position.left = parseInt(value) || 0;
                    $element.css('left', element.position.left + 'px');
                    break;
                    
                case 'top':
                    element.position.top = parseInt(value) || 0;
                    $element.css('top', element.position.top + 'px');
                    break;
                    
                case 'width':
                    element.size.width = parseInt(value) || 20;
                    $element.css('width', element.size.width + 'px');
                    break;
                    
                case 'height':
                    element.size.height = parseInt(value) || 20;
                    $element.css('height', element.size.height + 'px');
                    break;
                    
                case 'text-content':
                    element.content = value;
                    $element.text(value);
                    
                    // Re-add controls if they were overwritten
                    if (!$element.find('.builder-element-controls').length) {
                        const $controls = $('<div>', {
                            class: 'builder-element-controls',
                            html: `
                                <div class="builder-element-control move" title="Move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M5 9l-3 3 3 3"></path>
                                        <path d="M9 5l3-3 3 3"></path>
                                        <path d="M15 19l3-3 3 3"></path>
                                        <path d="M19 9l3 3-3 3"></path>
                                        <path d="M2 12h20"></path>
                                        <path d="M12 2v20"></path>
                                    </svg>
                                </div>
                                <div class="builder-element-control delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </div>
                            `
                        });
                        
                        $element.append($controls);
                        
                        // Re-add event handler
                        $element.find('.delete').on('click', (e) => {
                            e.stopPropagation();
                            this.deleteElement(elementId);
                        });
                    }
                    break;
                    
                case 'font-size':
                    element.fontSize = parseInt(value) || 16;
                    $element.css('font-size', element.fontSize + 'px');
                    break;
                    
                case 'color':
                    element.color = value;
                    $element.css('color', value);
                    $('#element-color-preview').css('background-color', value);
                    break;
                    
                case 'button-text':
                    element.content = value;
                    $element.text(value);
                    
                    // Re-add controls if they were overwritten
                    if (!$element.find('.builder-element-controls').length) {
                        const $controls = $('<div>', {
                            class: 'builder-element-controls',
                            html: `
                                <div class="builder-element-control move" title="Move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M5 9l-3 3 3 3"></path>
                                        <path d="M9 5l3-3 3 3"></path>
                                        <path d="M15 19l3-3 3 3"></path>
                                        <path d="M19 9l3 3-3 3"></path>
                                        <path d="M2 12h20"></path>
                                        <path d="M12 2v20"></path>
                                    </svg>
                                </div>
                                <div class="builder-element-control delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </div>
                            `
                        });
                        
                        $element.append($controls);
                        
                        // Re-add event handler
                        $element.find('.delete').on('click', (e) => {
                            e.stopPropagation();
                            this.deleteElement(elementId);
                        });
                    }
                    break;
                    
                case 'button-url':
                    element.url = value;
                    $element.attr('href', value);
                    break;
                    
                case 'button-bg-color':
                    element.backgroundColor = value;
                    $element.css('background-color', value);
                    $('#element-button-bg-color-preview').css('background-color', value);
                    break;
                    
                case 'button-text-color':
                    element.color = value;
                    $element.css('color', value);
                    $('#element-button-text-color-preview').css('background-color', value);
                    break;
                    
                case 'image-url':
                    element.src = value;
                    if (value) {
                        if ($element.find('img').length) {
                            $element.find('img').attr('src', value);
                        } else {
                            $element.empty().append($('<img>', {
                                src: value,
                                alt: element.alt || ''
                            }));
                            
                            // Re-add controls
                            const $controls = $('<div>', {
                                class: 'builder-element-controls',
                                html: `
                                    <div class="builder-element-control move" title="Move">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                            <path d="M5 9l-3 3 3 3"></path>
                                            <path d="M9 5l3-3 3 3"></path>
                                            <path d="M15 19l3-3 3 3"></path>
                                            <path d="M19 9l3 3-3 3"></path>
                                            <path d="M2 12h20"></path>
                                            <path d="M12 2v20"></path>
                                        </svg>
                                    </div>
                                    <div class="builder-element-control delete" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </div>
                                `
                            });
                            
                            $element.append($controls);
                            
                            // Re-add event handler
                            $element.find('.delete').on('click', (e) => {
                                e.stopPropagation();
                                this.deleteElement(elementId);
                            });
                        }
                    } else {
                        $element.empty().append($('<div>', {
                            class: 'image-placeholder',
                            text: 'Click to select image'
                        }));
                        
                        // Re-add controls
                        const $controls = $('<div>', {
                            class: 'builder-element-controls',
                            html: `
                                <div class="builder-element-control move" title="Move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M5 9l-3 3 3 3"></path>
                                        <path d="M9 5l3-3 3 3"></path>
                                        <path d="M15 19l3-3 3 3"></path>
                                        <path d="M19 9l3 3-3 3"></path>
                                        <path d="M2 12h20"></path>
                                        <path d="M12 2v20"></path>
                                    </svg>
                                </div>
                                <div class="builder-element-control delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </div>
                            `
                        });
                        
                        $element.append($controls);
                        
                        // Re-add event handler
                        $element.find('.delete').on('click', (e) => {
                            e.stopPropagation();
                            this.deleteElement(elementId);
                        });
                    }
                    break;
                    
                case 'image-alt':
                    element.alt = value;
                    $element.find('img').attr('alt', value);
                    break;
            }
            
            // Update the JSON data
            this.updatePopupJson();
        }
        
        /**
         * Delete the selected element
         */
        deleteSelectedElement() {
            if (!this.selectedElement) return;
            
            const elementId = $(this.selectedElement).attr('id');
            this.deleteElement(elementId);
        }
        
        /**
         * Delete an element by ID
         */
        deleteElement(elementId) {
            // Remove from DOM
            $('#' + elementId).remove();
            
            // Remove from data array
            this.elements = this.elements.filter(element => element.id !== elementId);
            
            // Deselect if this was the selected element
            if (this.selectedElement && $(this.selectedElement).attr('id') === elementId) {
                this.deselectElement();
            }
            
            // Update JSON
            this.updatePopupJson();
        }
        
        /**
         * Find element data by ID
         */
        findElementById(id) {
            return this.elements.find(element => element.id === id);
        }
        
        /**
         * Update the popup JSON data
         */
        updatePopupJson() {
            $('#popup_design_json').val(JSON.stringify(this.elements));
        }
        
        /**
         * Show position feedback when moving elements
         * 
         * @param {number} x The X position
         * @param {number} y The Y position
         */
        showPositionFeedback(x, y) {
            // Update tooltip if it exists
            $('#position-tooltip').html(`X: ${Math.round(x)}px, Y: ${Math.round(y)}px`);
        }
        
        /**
         * Hide position feedback
         */
        hidePositionFeedback() {
            $('#position-tooltip').remove();
        }
        
        /**
         * Show size feedback when resizing elements
         * 
         * @param {number} width The width
         * @param {number} height The height
         */
        showSizeFeedback(width, height) {
            // Update tooltip if it exists
            $('#size-tooltip').html(`W: ${Math.round(width)}px, H: ${Math.round(height)}px`);
        }
        
        /**
         * Hide size feedback
         */
        hideSizeFeedback() {
            $('#size-tooltip').remove();
        }
        
        /**
         * Load the popup design from JSON
         */
        loadPopupDesign() {
            const designJson = $('#popup_design_json').val();
            
            if (!designJson) return;
            
            try {
                // Parse the JSON design data
                const elements = JSON.parse(designJson);
                
                // Clear the canvas
                this.$canvas.empty();
                this.elements = [];
                
                // Create each element
                elements.forEach(element => {
                    let $element;
                    
                    switch (element.type) {
                        case 'heading':
                            $element = $('<h2>', {
                                class: 'builder-element element-heading',
                                id: element.id,
                                text: element.content,
                                'data-element-type': element.type
                            });
                            break;
                            
                        case 'text':
                            $element = $('<p>', {
                                class: 'builder-element element-text',
                                id: element.id,
                                text: element.content,
                                'data-element-type': element.type
                            });
                            break;
                            
                        case 'button':
                            $element = $('<a>', {
                                class: 'builder-element element-button',
                                id: element.id,
                                href: element.url,
                                text: element.content,
                                'data-element-type': element.type
                            });
                            break;
                            
                        case 'image':
                            $element = $('<div>', {
                                class: 'builder-element element-image',
                                id: element.id,
                                'data-element-type': element.type
                            });
                            
                            if (element.src) {
                                $element.append($('<img>', {
                                    src: element.src,
                                    alt: element.alt || ''
                                }));
                            } else {
                                $element.append($('<div>', {
                                    class: 'image-placeholder',
                                    text: 'Click to select image'
                                }));
                            }
                            break;
                            
                        case 'divider':
                            $element = $('<hr>', {
                                class: 'builder-element element-divider',
                                id: element.id,
                                'data-element-type': element.type
                            });
                            break;
                            
                        case 'spacer':
                            $element = $('<div>', {
                                class: 'builder-element element-spacer',
                                id: element.id,
                                'data-element-type': element.type,
                                html: '<div class="spacer-inner"></div>'
                            });
                            break;
                    }
                    
                    if ($element) {
                        // Add element controls
                        const $controls = $('<div>', {
                            class: 'builder-element-controls',
                            html: `
                                <div class="builder-element-control move" title="Move">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M5 9l-3 3 3 3"></path>
                                        <path d="M9 5l3-3 3 3"></path>
                                        <path d="M15 19l3-3 3 3"></path>
                                        <path d="M19 9l3 3-3 3"></path>
                                        <path d="M2 12h20"></path>
                                        <path d="M12 2v20"></path>
                                    </svg>
                                </div>
                                <div class="builder-element-control delete" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </div>
                            `
                        });
                        
                        $element.append($controls);
                        
                        // Position and style the element
                        $element.css({
                            position: 'absolute',
                            left: element.position.left + 'px',
                            top: element.position.top + 'px',
                            width: element.size.width + 'px',
                            height: element.size.height + 'px'
                        });
                        
                        // Apply type-specific styles
                        switch (element.type) {
                            case 'heading':
                            case 'text':
                                if (element.color) $element.css('color', element.color);
                                if (element.fontSize) $element.css('font-size', element.fontSize + 'px');
                                break;
                                
                            case 'button':
                                if (element.color) $element.css('color', element.color);
                                if (element.backgroundColor) $element.css('background-color', element.backgroundColor);
                                if (element.fontSize) $element.css('font-size', element.fontSize + 'px');
                                break;
                        }
                        
                        // Add to canvas
                        this.$canvas.append($element);
                        
                        // Make element draggable
                        $element.draggable({
                            containment: 'parent',
                            handle: '.move',
                            start: () => {
                                this.selectElement($element[0]);
                            },
                            stop: (event, ui) => {
                                // Update position in data
                                const elementId = $(ui.helper).attr('id');
                                const element = this.findElementById(elementId);
                                
                                if (element) {
                                    element.position.left = ui.position.left;
                                    element.position.top = ui.position.top;
                                    
                                    // Update position inputs
                                    $('#element-left').val(ui.position.left);
                                    $('#element-top').val(ui.position.top);
                                    
                                    // Update JSON
                                    this.updatePopupJson();
                                }
                            }
                        });
                        
                        // Make element resizable
                        $element.resizable({
                            containment: 'parent',
                            handles: 'all',
                            minWidth: 20,
                            minHeight: 20,
                            resize: (event, ui) => {
                                // Update size inputs in real-time
                                $('#element-width').val(ui.size.width);
                                $('#element-height').val(ui.size.height);
                            },
                            stop: (event, ui) => {
                                // Update size in data
                                const elementId = $(ui.helper).attr('id');
                                const element = this.findElementById(elementId);
                                
                                if (element) {
                                    element.size.width = ui.size.width;
                                    element.size.height = ui.size.height;
                                    
                                    // Update JSON
                                    this.updatePopupJson();
                                }
                            }
                        });
                        
                        // Add delete handler
                        $element.find('.delete').on('click', (e) => {
                            e.stopPropagation();
                            this.deleteElement(element.id);
                        });
                        
                        // Store element data in the array
                        this.elements.push(element);
                    }
                });
            } catch (error) {
                console.error('Error loading popup design:', error);
            }
        }
        
        /**
         * Save the popup design
         */
        savePopupDesign() {
            // Update JSON one final time
            this.updatePopupJson();
            
            // Submit the form
            $('#post').submit();
        }
        
        /**
         * Reset the design
         */
        resetDesign() {
            if (confirm(smartengageL10n.resetConfirmation)) {
                // Clear the canvas
                this.$canvas.empty();
                
                // Reset the elements array
                this.elements = [];
                
                // Clear the JSON input
                $('#popup_design_json').val('');
                
                // Deselect any element
                this.deselectElement();
            }
        }
        
        /**
         * Preview the design
         */
        previewDesign() {
            // Get popup settings
            const popupType = $('#popup-type').val();
            const popupPosition = $('#popup-position').val();
            const popupTheme = $('#popup-theme').val();
            
            // Create the preview wrapper
            const $previewWrapper = $('<div>', {
                class: 'smartengage-preview-wrapper'
            });
            
            // Create the preview container
            const $previewContainer = $('<div>', {
                class: 'smartengage-preview-container'
            });
            
            // Create the preview header
            const $previewHeader = $('<div>', {
                class: 'smartengage-preview-header',
                html: `
                    <h3>${smartengageL10n.previewTitle}</h3>
                    <div class="smartengage-preview-close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </div>
                `
            });
            
            // Create the preview content
            const $previewContent = $('<div>', {
                class: 'smartengage-preview-content'
            });
            
            // Create the preview iframe container
            const $previewIframeContainer = $('<div>', {
                class: 'smartengage-preview-iframe-container'
            });
            
            // Create a div to show the popup preview
            const $popupPreview = $('<div>', {
                class: `smartengage-popup-container theme-${popupTheme}`,
                html: `
                    <div class="smartengage-popup-overlay"></div>
                    <div class="smartengage-popup type-${popupType} position-${popupPosition}">
                        <div class="smartengage-popup-content"></div>
                    </div>
                `
            });
            
            // Clone all elements from the builder and add them to the preview
            this.elements.forEach(element => {
                let $previewElement;
                
                switch (element.type) {
                    case 'heading':
                        $previewElement = $('<h2>', {
                            class: 'element-heading',
                            text: element.content,
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                                font-size: ${element.fontSize}px;
                                color: ${element.color};
                            `
                        });
                        break;
                        
                    case 'text':
                        $previewElement = $('<p>', {
                            class: 'element-text',
                            text: element.content,
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                                font-size: ${element.fontSize}px;
                                color: ${element.color};
                            `
                        });
                        break;
                        
                    case 'button':
                        $previewElement = $('<a>', {
                            class: 'element-button',
                            href: element.url,
                            text: element.content,
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                                font-size: ${element.fontSize}px;
                                color: ${element.color};
                                background-color: ${element.backgroundColor};
                            `
                        });
                        break;
                        
                    case 'image':
                        $previewElement = $('<div>', {
                            class: 'element-image',
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                            `
                        });
                        
                        if (element.src) {
                            $previewElement.append($('<img>', {
                                src: element.src,
                                alt: element.alt || ''
                            }));
                        }
                        break;
                        
                    case 'divider':
                        $previewElement = $('<hr>', {
                            class: 'element-divider',
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                            `
                        });
                        break;
                        
                    case 'spacer':
                        $previewElement = $('<div>', {
                            class: 'element-spacer',
                            style: `
                                position: absolute;
                                left: ${element.position.left}px;
                                top: ${element.position.top}px;
                                width: ${element.size.width}px;
                                height: ${element.size.height}px;
                            `
                        });
                        break;
                }
                
                if ($previewElement) {
                    $popupPreview.find('.smartengage-popup-content').append($previewElement);
                }
            });
            
            // Add close button to popup
            $popupPreview.find('.smartengage-popup-content').append($('<button>', {
                class: 'smartengage-popup-close',
                html: '&times;'
            }));
            
            // Add the popup preview to the iframe container
            $previewIframeContainer.append($popupPreview);
            
            // Assemble the preview
            $previewContent.append($previewIframeContainer);
            $previewContainer.append($previewHeader).append($previewContent);
            $previewWrapper.append($previewContainer);
            
            // Add to body and show
            $('body').append($previewWrapper);
            
            // Slight delay to allow for DOM update
            setTimeout(() => {
                $previewWrapper.addClass('active');
                $popupPreview.addClass('active');
            }, 10);
            
            // Handle close button click
            $previewWrapper.on('click', '.smartengage-preview-close, .smartengage-popup-close, .smartengage-popup-overlay', function() {
                $previewWrapper.removeClass('active');
                
                // Remove the preview after animation completes
                setTimeout(() => {
                    $previewWrapper.remove();
                }, 300);
            });
        }
        
        /**
         * Apply a template preset
         */
        applyTemplate(templateId) {
            if (!this.templates[templateId]) return;
            
            // Confirm if there are existing elements
            if (this.elements.length > 0) {
                if (!confirm(smartengageL10n.templateConfirmation)) {
                    return;
                }
            }
            
            // Clear existing elements
            this.$canvas.empty();
            this.elements = [];
            
            // Clone template to avoid issues with reference
            const templateElements = JSON.parse(JSON.stringify(this.templates[templateId]));
            
            // Add unique IDs to template elements
            templateElements.forEach(element => {
                element.id = 'element-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            });
            
            // Store new elements temporarily
            const newElements = [];
            
            // Create each element from the template
            templateElements.forEach(element => {
                let $element;
                
                switch (element.type) {
                    case 'heading':
                        $element = $('<h2>', {
                            class: 'builder-element element-heading',
                            id: element.id,
                            text: element.content,
                            'data-element-type': element.type
                        });
                        break;
                        
                    case 'text':
                        $element = $('<p>', {
                            class: 'builder-element element-text',
                            id: element.id,
                            text: element.content,
                            'data-element-type': element.type
                        });
                        break;
                        
                    case 'button':
                        $element = $('<a>', {
                            class: 'builder-element element-button',
                            id: element.id,
                            href: element.url,
                            text: element.content,
                            'data-element-type': element.type
                        });
                        break;
                        
                    case 'image':
                        $element = $('<div>', {
                            class: 'builder-element element-image',
                            id: element.id,
                            'data-element-type': element.type
                        });
                        
                        if (element.src) {
                            $element.append($('<img>', {
                                src: element.src,
                                alt: element.alt || ''
                            }));
                        } else {
                            $element.append($('<div>', {
                                class: 'image-placeholder',
                                text: 'Click to select image'
                            }));
                        }
                        break;
                        
                    case 'divider':
                        $element = $('<hr>', {
                            class: 'builder-element element-divider',
                            id: element.id,
                            'data-element-type': element.type
                        });
                        break;
                        
                    case 'spacer':
                        $element = $('<div>', {
                            class: 'builder-element element-spacer',
                            id: element.id,
                            'data-element-type': element.type,
                            html: '<div class="spacer-inner"></div>'
                        });
                        break;
                }
                
                if ($element) {
                    // Add element controls
                    const $controls = $('<div>', {
                        class: 'builder-element-controls',
                        html: `
                            <div class="builder-element-control move" title="Move">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M5 9l-3 3 3 3"></path>
                                    <path d="M9 5l3-3 3 3"></path>
                                    <path d="M15 19l3-3 3 3"></path>
                                    <path d="M19 9l3 3-3 3"></path>
                                    <path d="M2 12h20"></path>
                                    <path d="M12 2v20"></path>
                                </svg>
                            </div>
                            <div class="builder-element-control delete" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                        `
                    });
                    
                    $element.append($controls);
                    
                    // Position and style the element
                    $element.css({
                        position: 'absolute',
                        left: element.position.left + 'px',
                        top: element.position.top + 'px',
                        width: element.size.width + 'px',
                        height: element.size.height + 'px'
                    });
                    
                    // Apply type-specific styles
                    switch (element.type) {
                        case 'heading':
                        case 'text':
                            if (element.color) $element.css('color', element.color);
                            if (element.fontSize) $element.css('font-size', element.fontSize + 'px');
                            break;
                            
                        case 'button':
                            if (element.color) $element.css('color', element.color);
                            if (element.backgroundColor) $element.css('background-color', element.backgroundColor);
                            if (element.fontSize) $element.css('font-size', element.fontSize + 'px');
                            break;
                    }
                    
                    // Add to canvas
                    this.$canvas.append($element);
                    
                    // Make element draggable
                    $element.draggable({
                        containment: 'parent',
                        handle: '.move',
                        start: () => {
                            this.selectElement($element[0]);
                        },
                        stop: (event, ui) => {
                            // Update position in data
                            const elementId = $(ui.helper).attr('id');
                            const element = this.findElementById(elementId);
                            
                            if (element) {
                                element.position.left = ui.position.left;
                                element.position.top = ui.position.top;
                                
                                // Update position inputs
                                $('#element-left').val(ui.position.left);
                                $('#element-top').val(ui.position.top);
                                
                                // Update JSON
                                this.updatePopupJson();
                            }
                        }
                    });
                    
                    // Make element resizable
                    $element.resizable({
                        containment: 'parent',
                        handles: 'all',
                        minWidth: 20,
                        minHeight: 20,
                        resize: (event, ui) => {
                            // Update size inputs in real-time
                            $('#element-width').val(ui.size.width);
                            $('#element-height').val(ui.size.height);
                        },
                        stop: (event, ui) => {
                            // Update size in data
                            const elementId = $(ui.helper).attr('id');
                            const element = this.findElementById(elementId);
                            
                            if (element) {
                                element.size.width = ui.size.width;
                                element.size.height = ui.size.height;
                                
                                // Update JSON
                                this.updatePopupJson();
                            }
                        }
                    });
                    
                    // Add delete handler
                    $element.find('.delete').on('click', (e) => {
                        e.stopPropagation();
                        this.deleteElement(element.id);
                    });
                    
                    // Add to elements array
                    newElements.push(element);
                }
            });
            
            // Update elements array with new elements
            this.elements = newElements;
            
            // Update JSON
            this.updatePopupJson();
            
            // Close presets panel
            $('.smartengage-presets-panel').removeClass('visible');
        }
        
        /**
         * Apply a color scheme to existing elements
         */
        applyColorScheme(schemeId) {
            if (!this.colorSchemes[schemeId]) return;
            
            const scheme = this.colorSchemes[schemeId];
            
            // Apply to existing elements
            this.elements.forEach(element => {
                const $element = $('#' + element.id);
                
                switch (element.type) {
                    case 'heading':
                        element.color = scheme.heading;
                        $element.css('color', scheme.heading);
                        break;
                        
                    case 'text':
                        element.color = scheme.text;
                        $element.css('color', scheme.text);
                        break;
                        
                    case 'button':
                        element.color = scheme.button.text;
                        element.backgroundColor = scheme.button.bg;
                        $element.css({
                            'color': scheme.button.text,
                            'background-color': scheme.button.bg
                        });
                        break;
                }
            });
            
            // Update selected element properties if any
            if (this.selectedElement) {
                const type = $(this.selectedElement).data('element-type');
                
                switch (type) {
                    case 'heading':
                    case 'text':
                        $('#element-color').val(scheme.heading);
                        $('#element-color-preview').css('background-color', scheme.heading);
                        break;
                        
                    case 'button':
                        $('#element-button-text-color').val(scheme.button.text);
                        $('#element-button-text-color-preview').css('background-color', scheme.button.text);
                        $('#element-button-bg-color').val(scheme.button.bg);
                        $('#element-button-bg-color-preview').css('background-color', scheme.button.bg);
                        break;
                }
            }
            
            // Update JSON
            this.updatePopupJson();
            
            // Close presets panel
            $('.smartengage-presets-panel').removeClass('visible');
        }
    }
    
    // Initialize the builder when the document is ready
    $(function() {
        // Check if we're on a popup edit page with the builder
        if ($('#smartengage-builder-canvas').length) {
            // Create global script object for translations
            window.smartengageL10n = {
                resetConfirmation: 'Are you sure you want to reset the design? This will remove all elements and cannot be undone.',
                templateConfirmation: 'Applying a template will replace your current design. Continue?',
                previewTitle: 'Preview',
                noElements: 'No elements found. Drag elements from the sidebar to create your popup.'
            };
            
            // Initialize the builder
            window.popupBuilder = new PopupBuilderModern();
        }
    });
    
})(jQuery);
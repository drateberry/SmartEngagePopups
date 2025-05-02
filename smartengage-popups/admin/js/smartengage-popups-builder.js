/**
 * Drag-and-drop builder for SmartEngage Popups
 */
(function($) {
    'use strict';

    // Builder class
    class PopupBuilder {
        constructor() {
            this.elements = [];
            this.selectedElement = null;
            this.initBuilder();
        }

        initBuilder() {
            this.setupCanvas();
            this.setupPalette();
            this.setupPropertyPanel();
            this.bindEvents();
            this.loadPopupDesign();
        }

        setupCanvas() {
            this.$canvas = $('#smartengage-builder-canvas');
            
            // Make canvas a dropzone
            this.$canvas.droppable({
                accept: '.smartengage-element-item',
                drop: (event, ui) => this.handleElementDrop(event, ui)
            });
        }

        setupPalette() {
            // Make palette items draggable
            $('.smartengage-element-item').draggable({
                helper: 'clone',
                revert: 'invalid',
                connectToSortable: '.smartengage-builder-canvas'
            });
        }

        setupPropertyPanel() {
            this.$propertyPanel = $('#smartengage-element-properties');
            
            // Initialize color pickers
            $('.color-picker').wpColorPicker({
                change: (event, ui) => {
                    if (this.selectedElement) {
                        const propName = $(event.target).data('property');
                        const color = ui.color.toString();
                        this.updateElementProperty(propName, color);
                    }
                }
            });
            
            // Handle property changes
            $('.smartengage-property-control').on('change keyup', (e) => {
                if (this.selectedElement) {
                    const $input = $(e.target);
                    const propName = $input.data('property');
                    let value = $input.val();
                    
                    // Convert to appropriate type if needed
                    if ($input.attr('type') === 'number') {
                        value = parseInt(value) || 0;
                    }
                    
                    this.updateElementProperty(propName, value);
                }
            });
        }

        bindEvents() {
            // Save design button
            $('#smartengage-save-design').on('click', () => {
                this.savePopupDesign();
            });
            
            // Reset design button
            $('#smartengage-reset-design').on('click', () => {
                if (confirm('Are you sure you want to reset the design? This will remove all elements and cannot be undone.')) {
                    this.resetBuilder();
                }
            });
            
            // Preview button
            $('#smartengage-preview-design').on('click', () => {
                this.previewPopupDesign();
            });
            
            // Delete element button 
            $('#smartengage-delete-element').on('click', () => {
                if (this.selectedElement) {
                    $(this.selectedElement).remove();
                    this.deselectElement();
                    this.updatePopupJson();
                }
            });
            
            // Document listener for element selection
            $(document).on('click', '.builder-element', (e) => {
                const isSelected = $(e.currentTarget).hasClass('selected');
                this.deselectElement();
                
                if (!isSelected) {
                    this.selectElement(e.currentTarget);
                }
                
                e.stopPropagation();
            });
            
            // Click on canvas to deselect
            this.$canvas.on('click', (e) => {
                if ($(e.target).is(this.$canvas)) {
                    this.deselectElement();
                }
            });
        }

        handleElementDrop(event, ui) {
            const elementType = ui.draggable.data('element-type');
            
            // Generate a new unique element ID
            const elementId = 'element-' + Date.now();
            
            // Create the element based on its type
            let $element;
            
            switch (elementType) {
                case 'heading':
                    $element = $('<h2>', {
                        class: 'builder-element element-heading',
                        id: elementId,
                        text: 'Heading Text',
                        'data-element-type': elementType
                    });
                    break;
                    
                case 'text':
                    $element = $('<p>', {
                        class: 'builder-element element-text',
                        id: elementId,
                        text: 'Your text here. Click to edit.',
                        'data-element-type': elementType
                    });
                    break;
                    
                case 'image':
                    $element = $('<div>', {
                        class: 'builder-element element-image',
                        id: elementId,
                        'data-element-type': elementType,
                        html: '<img src="" alt=""><p class="image-placeholder">Click to select image</p>'
                    });
                    break;
                    
                case 'button':
                    $element = $('<a>', {
                        class: 'builder-element element-button',
                        id: elementId,
                        href: '#',
                        text: 'Button Text',
                        'data-element-type': elementType
                    });
                    break;
                    
                case 'divider':
                    $element = $('<hr>', {
                        class: 'builder-element element-divider',
                        id: elementId,
                        'data-element-type': elementType
                    });
                    break;
                    
                case 'spacer':
                    $element = $('<div>', {
                        class: 'builder-element element-spacer',
                        id: elementId,
                        'data-element-type': elementType,
                        html: '<div class="spacer-inner"></div>'
                    });
                    break;
            }
            
            // Make the element draggable and resizable
            if ($element) {
                // Add the element to the canvas
                const offsetX = ui.offset.left - this.$canvas.offset().left;
                const offsetY = ui.offset.top - this.$canvas.offset().top;
                
                $element.css({
                    position: 'absolute',
                    left: offsetX + 'px',
                    top: offsetY + 'px'
                });
                
                this.$canvas.append($element);
                
                // Make the element draggable
                $element.draggable({
                    containment: 'parent',
                    stop: () => this.updatePopupJson()
                });
                
                // Make the element resizable if applicable
                if (elementType !== 'divider') {
                    $element.resizable({
                        containment: 'parent',
                        handles: 'all',
                        stop: () => this.updatePopupJson()
                    });
                }
                
                // Select the newly created element
                this.selectElement($element[0]);
                
                // Update the popup JSON data
                this.updatePopupJson();
            }
        }

        selectElement(element) {
            this.selectedElement = element;
            $(element).addClass('selected');
            
            // Show properties for the selected element
            this.showElementProperties(element);
            
            // Show the delete button
            $('#smartengage-delete-element').show();
        }

        deselectElement() {
            $('.builder-element').removeClass('selected');
            this.selectedElement = null;
            
            // Hide the properties panel
            this.$propertyPanel.find('.property-group').hide();
            
            // Hide the delete button
            $('#smartengage-delete-element').hide();
        }

        showElementProperties(element) {
            const $element = $(element);
            const elementType = $element.data('element-type');
            
            // Hide all property groups first
            this.$propertyPanel.find('.property-group').hide();
            
            // Show the common properties
            this.$propertyPanel.find('.property-group-common').show();
            
            // Update common property values
            $('#element-left').val(parseInt($element.css('left')));
            $('#element-top').val(parseInt($element.css('top')));
            $('#element-width').val($element.width());
            $('#element-height').val($element.height());
            
            // Show type-specific properties
            this.$propertyPanel.find('.property-group-' + elementType).show();
            
            // Update type-specific property values
            switch (elementType) {
                case 'heading':
                case 'text':
                    $('#element-text-content').val($element.text());
                    $('#element-font-size').val(parseInt($element.css('font-size')));
                    $('#element-color').val(this.rgbToHex($element.css('color')));
                    $('#element-color').wpColorPicker('color', this.rgbToHex($element.css('color')));
                    break;
                    
                case 'button':
                    $('#element-text-content').val($element.text());
                    $('#element-button-url').val($element.attr('href'));
                    $('#element-font-size').val(parseInt($element.css('font-size')));
                    $('#element-color').val(this.rgbToHex($element.css('color')));
                    $('#element-color').wpColorPicker('color', this.rgbToHex($element.css('color')));
                    $('#element-bg-color').val(this.rgbToHex($element.css('background-color')));
                    $('#element-bg-color').wpColorPicker('color', this.rgbToHex($element.css('background-color')));
                    break;
                    
                case 'image':
                    const $img = $element.find('img');
                    $('#element-image-url').val($img.attr('src'));
                    $('#element-image-alt').val($img.attr('alt'));
                    break;
            }
        }

        updateElementProperty(propName, value) {
            if (!this.selectedElement) return;
            
            const $element = $(this.selectedElement);
            const elementType = $element.data('element-type');
            
            // Handle common properties
            switch (propName) {
                case 'left':
                    $element.css('left', value + 'px');
                    break;
                    
                case 'top':
                    $element.css('top', value + 'px');
                    break;
                    
                case 'width':
                    $element.width(value);
                    break;
                    
                case 'height':
                    $element.height(value);
                    break;
                    
                case 'text-content':
                    $element.text(value);
                    break;
                    
                case 'font-size':
                    $element.css('font-size', value + 'px');
                    break;
                    
                case 'color':
                    $element.css('color', value);
                    break;
                    
                case 'bg-color':
                    $element.css('background-color', value);
                    break;
                    
                case 'button-url':
                    $element.attr('href', value);
                    break;
                    
                case 'image-url':
                    if (value) {
                        $element.find('img').attr('src', value);
                        $element.find('.image-placeholder').hide();
                    } else {
                        $element.find('.image-placeholder').show();
                    }
                    break;
                    
                case 'image-alt':
                    $element.find('img').attr('alt', value);
                    break;
            }
            
            // Update the popup JSON data
            this.updatePopupJson();
        }

        updatePopupJson() {
            // Collect all elements and their properties
            const elements = [];
            
            this.$canvas.find('.builder-element').each(function() {
                const $element = $(this);
                const elementType = $element.data('element-type');
                
                const elementData = {
                    id: $element.attr('id'),
                    type: elementType,
                    position: {
                        left: parseInt($element.css('left')),
                        top: parseInt($element.css('top'))
                    },
                    size: {
                        width: $element.width(),
                        height: $element.height()
                    }
                };
                
                // Add type-specific properties
                switch (elementType) {
                    case 'heading':
                    case 'text':
                        elementData.content = $element.text();
                        elementData.fontSize = parseInt($element.css('font-size'));
                        elementData.color = $element.css('color');
                        break;
                        
                    case 'button':
                        elementData.content = $element.text();
                        elementData.url = $element.attr('href');
                        elementData.fontSize = parseInt($element.css('font-size'));
                        elementData.color = $element.css('color');
                        elementData.backgroundColor = $element.css('background-color');
                        break;
                        
                    case 'image':
                        const $img = $element.find('img');
                        elementData.src = $img.attr('src');
                        elementData.alt = $img.attr('alt');
                        break;
                }
                
                elements.push(elementData);
            });
            
            // Store the JSON data in the hidden input
            $('#popup_design_json').val(JSON.stringify(elements));
        }

        loadPopupDesign() {
            const designJson = $('#popup_design_json').val();
            
            if (!designJson) return;
            
            try {
                const elements = JSON.parse(designJson);
                
                // Clear the canvas first
                this.$canvas.empty();
                
                // Add each element to the canvas
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
                            $element.css({
                                'font-size': element.fontSize + 'px',
                                'color': element.color
                            });
                            break;
                            
                        case 'text':
                            $element = $('<p>', {
                                class: 'builder-element element-text',
                                id: element.id,
                                text: element.content,
                                'data-element-type': element.type
                            });
                            $element.css({
                                'font-size': element.fontSize + 'px',
                                'color': element.color
                            });
                            break;
                            
                        case 'image':
                            $element = $('<div>', {
                                class: 'builder-element element-image',
                                id: element.id,
                                'data-element-type': element.type,
                                html: `<img src="${element.src}" alt="${element.alt}"><p class="image-placeholder">Click to select image</p>`
                            });
                            
                            if (element.src) {
                                $element.find('.image-placeholder').hide();
                            }
                            break;
                            
                        case 'button':
                            $element = $('<a>', {
                                class: 'builder-element element-button',
                                id: element.id,
                                href: element.url,
                                text: element.content,
                                'data-element-type': element.type
                            });
                            $element.css({
                                'font-size': element.fontSize + 'px',
                                'color': element.color,
                                'background-color': element.backgroundColor
                            });
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
                        // Set position and size
                        $element.css({
                            position: 'absolute',
                            left: element.position.left + 'px',
                            top: element.position.top + 'px',
                            width: element.size.width + 'px',
                            height: element.size.height + 'px'
                        });
                        
                        this.$canvas.append($element);
                        
                        // Make the element draggable
                        $element.draggable({
                            containment: 'parent',
                            stop: () => this.updatePopupJson()
                        });
                        
                        // Make the element resizable if applicable
                        if (element.type !== 'divider') {
                            $element.resizable({
                                containment: 'parent',
                                handles: 'all',
                                stop: () => this.updatePopupJson()
                            });
                        }
                    }
                });
                
            } catch (e) {
                console.error('Error loading popup design:', e);
            }
        }

        savePopupDesign() {
            // Update the popup JSON one final time
            this.updatePopupJson();
            
            // Trigger the form submission
            $('#post').submit();
        }

        resetBuilder() {
            // Clear the canvas
            this.$canvas.empty();
            
            // Reset the JSON data
            $('#popup_design_json').val('');
            
            // Deselect any element
            this.deselectElement();
        }

        previewPopupDesign() {
            // Create a temporary container for the preview
            const $previewContainer = $('<div>', {
                id: 'smartengage-preview-container'
            });
            
            // Create the popup overlay
            const $overlay = $('<div>', {
                class: 'smartengage-popup-overlay'
            });
            
            // Create the popup
            const $popup = $('<div>', {
                class: 'smartengage-popup-preview'
            });
            
            // Get the popup type and position
            const popupType = $('#popup_type').val();
            const popupPosition = $('#popup_position').val();
            
            // Add appropriate classes
            if (popupType === 'slide-in') {
                $popup.addClass('type-slide-in position-' + popupPosition);
            } else {
                $popup.addClass('type-full-screen');
            }
            
            // Create the popup content container
            const $popupContent = $('<div>', {
                class: 'smartengage-popup-content'
            });
            
            // Add close button
            const $closeButton = $('<button>', {
                class: 'smartengage-popup-close',
                text: '×'
            });
            
            $popupContent.append($closeButton);
            
            // Clone all elements from the builder and add them to the preview
            this.$canvas.find('.builder-element').each(function() {
                const $element = $(this);
                const $clone = $element.clone();
                
                // Remove builder-specific classes and attributes
                $clone.removeClass('builder-element selected ui-draggable ui-draggable-handle ui-resizable');
                $clone.find('.ui-resizable-handle').remove();
                
                // Apply the same styling
                $clone.css({
                    position: 'absolute',
                    left: $element.css('left'),
                    top: $element.css('top'),
                    width: $element.css('width'),
                    height: $element.css('height')
                });
                
                $popupContent.append($clone);
            });
            
            // Add the content to the popup
            $popup.append($popupContent);
            
            // Add the popup and overlay to the container
            $previewContainer.append($overlay).append($popup);
            
            // Add the container to the body
            $('body').append($previewContainer);
            
            // Show the popup with animation
            $overlay.fadeIn(300);
            $popup.fadeIn(300);
            
            // Handle close button click
            $closeButton.on('click', function() {
                $overlay.fadeOut(300);
                $popup.fadeOut(300, function() {
                    $previewContainer.remove();
                });
            });
            
            // Handle overlay click to close
            $overlay.on('click', function() {
                $closeButton.click();
            });
        }

        // Helper function to convert RGB to hex
        rgbToHex(rgb) {
            if (!rgb || rgb === 'transparent' || rgb === 'rgba(0, 0, 0, 0)') {
                return '#ffffff';
            }
            
            // If already in hex format, return as is
            if (rgb.charAt(0) === '#') {
                return rgb;
            }
            
            // Extract RGB values
            const match = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            if (!match) return '#ffffff';
            
            function hex(x) {
                return ("0" + parseInt(x).toString(16)).slice(-2);
            }
            
            return "#" + hex(match[1]) + hex(match[2]) + hex(match[3]);
        }
    }

    // Initialize the builder when the document is ready
    $(function() {
        if ($('#smartengage-builder-canvas').length) {
            window.popupBuilder = new PopupBuilder();
        }
    });

})(jQuery);
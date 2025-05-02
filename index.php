<?php
/**
 * SmartEngage Popups Builder Demo
 * 
 * This file demonstrates the functionality of the SmartEngage Popups plugin
 * in a standalone environment without WordPress.
 */

// Set page title
$pageTitle = 'SmartEngage Popups - Builder Demo';

// Mock WordPress functions to prevent errors
function get_post_meta($id, $key, $single = false) {
    return '';
}

function wp_create_nonce($action) {
    return 'mock-nonce-' . $action;
}

function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_html_e($text, $domain = 'default') {
    echo esc_html($text);
}

function esc_attr_e($text, $domain = 'default') {
    echo esc_attr($text);
}

function selected($selected, $current = true, $echo = true) {
    if ($selected == $current) {
        $result = ' selected="selected"';
    } else {
        $result = '';
    }

    if ($echo) {
        echo $result;
    }
    
    return $result;
}

// Mock WordPress AJAX URL
$ajaxurl = 'ajax-handler.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Load jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
    
    <!-- Load our plugin styles -->
    <link rel="stylesheet" href="smartengage-popups/admin/css/smartengage-popups-builder-modern.css">
    
    <style>
        /* Basic styles for demo page */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #f0f0f1;
            color: #1d2327;
            margin: 0;
            padding: 20px;
        }
        
        .wrap {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        h1 {
            font-size: 24px;
            margin: 0 0 20px;
            color: #1d2327;
            font-weight: 600;
        }
        
        /* Mock WordPress admin styles */
        #wpbody-content {
            padding-bottom: 65px;
        }
        
        #wpfooter {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #f0f0f1;
            color: #50575e;
            padding: 15px 20px;
            border-top: 1px solid #dcdcde;
            z-index: 100;
            font-size: 13px;
        }
        
        .notice {
            background: #fff;
            border-left: 4px solid #72aee6;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
            margin: 20px 0;
            padding: 12px;
            font-size: 14px;
        }
        
        .notice-success {
            border-left-color: #00a32a;
        }
        
        .notice-info {
            border-left-color: #72aee6;
        }
        
        /* Hidden form elements for demo */
        .hidden-inputs {
            display: none;
        }
    </style>
</head>
<body class="wp-admin post-type-smartengage_popup">
    <div id="wpbody">
        <div id="wpbody-content">
            <div class="wrap">
                <h1>SmartEngage Popups - Builder Demo</h1>
                
                <div class="notice notice-info">
                    <p>
                        This is a standalone demo of the SmartEngage Popups builder. In a real WordPress environment, 
                        all actions would be saved to the database. This demo shows the improved builder interface with 
                        auto-saving functionality.
                    </p>
                </div>
                
                <!-- Hidden inputs to simulate WordPress environment -->
                <div class="hidden-inputs">
                    <input type="hidden" id="post_ID" value="1001">
                    <input type="hidden" id="smartengage_builder_nonce" value="<?php echo wp_create_nonce('smartengage_builder_nonce'); ?>">
                    <input type="hidden" id="popup_design_json" value="[]">
                </div>
                
                <!-- Include the builder template -->
                <?php include('smartengage-popups/admin/partials/popup-builder-modern-template.php'); ?>
            </div>
        </div>
    </div>
    
    <div id="wpfooter">
        Thank you for creating with <a href="#">WordPress</a> | <span>Version 6.3.2</span>
    </div>

    <!-- Load our plugin scripts -->
    <script>
        // Mock WordPress ajaxurl
        var ajaxurl = '<?php echo $ajaxurl; ?>';
        
        // Log to console when document is ready
        $(document).ready(function() {
            console.log('SmartEngage Popups Builder Demo loaded successfully!');
        });
    </script>
    <script src="smartengage-popups/admin/js/smartengage-popups-builder-modern.js"></script>
</body>
</html>
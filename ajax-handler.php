<?php
/**
 * Mock AJAX handler for SmartEngage Popups Builder Demo
 * 
 * This file simulates WordPress AJAX responses for the builder.
 */

// Set header to JSON
header('Content-Type: application/json');

// Check for action parameter
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Mock response data
$response = [];

// Handle different actions
switch ($action) {
    case 'smartengage_save_popup_design':
        // Verify all required parameters are present
        if (!isset($_POST['popup_id']) || !isset($_POST['design_data'])) {
            $response = [
                'success' => false,
                'message' => 'Missing required data'
            ];
            
            break;
        }
        
        // In a real environment, this would save to the database
        // For demo purposes, we'll just return success
        
        // Log the data for debugging
        $log_data = [
            'time' => date('Y-m-d H:i:s'),
            'popup_id' => $_POST['popup_id'],
            'is_autosave' => isset($_POST['is_autosave']) ? $_POST['is_autosave'] : false
        ];
        
        // Write to log file (real plugin would use database)
        file_put_contents('popup-saves.log', json_encode($log_data) . "\n", FILE_APPEND);
        
        $response = [
            'success' => true,
            'message' => 'Popup design saved successfully',
            'demo_note' => 'This is a demo - data is not actually being saved to a database'
        ];
        
        break;
        
    default:
        $response = [
            'success' => false,
            'message' => 'Unknown action'
        ];
}

// Output the response
echo json_encode($response);
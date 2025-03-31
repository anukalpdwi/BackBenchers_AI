<?php

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allowed request methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allowed headers

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Configuration file for the API
 */

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// API Keys and configuration
$config = [
    'unsplash' => [
        'access_key' => 'ichOqfSyjSsXzY3SdDU8FazEYktkXcTZZblLshF7HXk',  // Directly insert key
        'api_url' => 'https://api.unsplash.com/search/photos',
        'per_page' => 6
    ],
    'starryai' => [
        'api_key' => '',  // Add your StarryAI key here if needed
        'api_url' => 'https://api.starryai.com/v1/generation'
    ]
];


// Helper function to return JSON response
function sendJsonResponse($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Helper function to handle errors
function sendErrorResponse($message, $statusCode = 400) {
    $response = [
        'success' => false,
        'error' => $message
    ];
    sendJsonResponse($response, $statusCode);
}
?>
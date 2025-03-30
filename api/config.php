<?php
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
        'access_key' => getenv('UNSPLASH_ACCESS_KEY'),
        'api_url' => 'https://api.unsplash.com/search/photos',
        'per_page' => 6
    ],
    'starryai' => [
        'api_key' => getenv('STARRYAI_API_KEY'),
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
<?php
/**
 * Configuration file for AI Image Generator
 */

// Error handling in development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration settings
$config = [
    // StarryAI API Key (get from environment variable)
    'starryai_api_key' => getenv('STARRYAI_API_KEY') ?: 'default_key',
    
    // Maximum request timeout in seconds
    'request_timeout' => 30,
    
    // Maximum image size
    'max_image_size' => 1024 * 1024 * 5, // 5MB
    
    // Default image format
    'default_format' => 'jpg'
];

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

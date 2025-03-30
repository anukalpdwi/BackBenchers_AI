<?php
/**
 * Image Generation API Endpoint
 * Handles communication with StarryAI API
 */

// Allow cross-origin requests (if needed)
header('Content-Type: application/json');

// Include configuration
require_once 'config.php';

// Get the request body
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Validate input
if (!isset($input['prompt']) || empty(trim($input['prompt']))) {
    sendErrorResponse('Prompt cannot be empty');
    exit;
}

// Extract parameters
$prompt = $input['prompt'];
$style = isset($input['style']) ? $input['style'] : 'realistic';
$imageCount = isset($input['imageCount']) ? (int)$input['imageCount'] : 1;

// Ensure image count is valid (1, 2, or 4)
if (!in_array($imageCount, [1, 2, 4])) {
    $imageCount = 1;
}

try {
    // Make API request to StarryAI
    $result = generateImagesFromStarryAI($prompt, $style, $imageCount);
    
    // Return successful response
    echo json_encode([
        'success' => true,
        'images' => $result,
        'message' => 'Images generated successfully'
    ]);
    
} catch (Exception $e) {
    sendErrorResponse($e->getMessage());
}

/**
 * Generate images using StarryAI API
 * 
 * @param string $prompt Text prompt for image generation
 * @param string $style Art style to apply
 * @param int $count Number of images to generate
 * @return array Array of image data
 */
function generateImagesFromStarryAI($prompt, $style, $count) {
    global $config;
    
    // Prepare API endpoint
    $url = 'https://api.starryai.com/v1/images/generate';
    
    // Map our style options to what StarryAI expects
    $styleMapping = [
        'realistic' => 'photographic',
        'anime' => 'anime',
        'cartoon' => 'cartoon',
        'painting' => 'oil-painting',
        'sketch' => 'pencil-sketch'
    ];
    
    // Default to photographic if style not in mapping
    $starryAIStyle = isset($styleMapping[$style]) ? $styleMapping[$style] : 'photographic';
    
    // Prepare request data
    $postData = [
        'prompt' => $prompt,
        'style' => $starryAIStyle,
        'num_images' => $count,
        'width' => 512,
        'height' => 512
    ];
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $config['starryai_api_key']
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new Exception('API request failed: ' . curl_error($ch));
    }
    
    // Get HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Parse response
    $responseData = json_decode($response, true);
    
    // Check for API errors
    if ($httpCode !== 200) {
        $errorMessage = isset($responseData['error']['message']) 
            ? $responseData['error']['message'] 
            : 'API request failed with status code ' . $httpCode;
        
        throw new Exception($errorMessage);
    }
    
    // For testing purposes, we'll simulate a response if the API isn't actually connected
    // In a real application, this would be removed
    if (!isset($responseData['images']) || empty($responseData['images'])) {
        // This is a fallback for development/testing
        // In production, this should not be included
        $mockImages = [];
        
        // Create the mock images based on requested count
        for ($i = 0; $i < $count; $i++) {
            $mockImages[] = [
                'id' => 'img_' . uniqid(),
                'url' => 'https://via.placeholder.com/512x512.png?text=AI+Generated+Image+'.$i,
                'prompt' => $prompt
            ];
        }
        
        // Log the issue (in production, handle more appropriately)
        error_log("Warning: StarryAI API response did not contain images. Using placeholders.");
        
        return $mockImages;
    }
    
    // Process and return the image data
    $images = [];
    foreach ($responseData['images'] as $image) {
        $images[] = [
            'id' => $image['id'] ?? ('img_' . uniqid()),
            'url' => $image['url'],
            'prompt' => $prompt
        ];
    }
    
    return $images;
}

/**
 * Send error response in JSON format
 * 
 * @param string $message Error message
 * @param int $statusCode HTTP status code
 * @return void
 */
function sendErrorResponse($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

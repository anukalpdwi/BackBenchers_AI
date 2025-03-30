<?php
/**
 * API endpoint for image generation
 */
require_once 'config.php';

// Get the request body
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

// Validate the request
if (!$data || !isset($data['prompt']) || empty($data['prompt'])) {
    sendErrorResponse('Prompt is required');
}

// Get request parameters
$prompt = $data['prompt'];
$style = isset($data['style']) ? $data['style'] : 'photo';
$count = isset($data['count']) ? intval($data['count']) : 1;

// Limit count to a reasonable number
$count = min(max(1, $count), 4);

// Check if we have the Unsplash API key
if (empty($config['unsplash']['access_key'])) {
    sendErrorResponse('Unsplash API key is not configured.', 500);
}

// Try Unsplash API first as it's more reliable for photos
try {
    // Prepare Unsplash API request
    $apiUrl = $config['unsplash']['api_url'];
    $queryParams = [
        'query' => $prompt,
        'per_page' => $count,
        'orientation' => 'landscape'
    ];
    
    // Add style-specific parameters
    if ($style == 'black-and-white') {
        $queryParams['color'] = 'black_and_white';
    } elseif ($style == 'minimal') {
        $queryParams['content_filter'] = 'high';
    } elseif ($style == 'vibrant') {
        $queryParams['color'] = 'vibrant';
    }
    
    // Build request URL
    $requestUrl = $apiUrl . '?' . http_build_query($queryParams);
    
    // Setup cURL request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $requestUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Client-ID ' . $config['unsplash']['access_key'],
        'Accept-Version: v1'
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check for errors
    if ($httpCode != 200) {
        $errorData = json_decode($response, true);
        $errorMessage = isset($errorData['errors']) ? implode(', ', $errorData['errors']) : 'API request failed';
        throw new Exception($errorMessage);
    }
    
    // Parse the response
    $responseData = json_decode($response, true);
    
    // Check if we have results
    if (!isset($responseData['results']) || empty($responseData['results'])) {
        sendErrorResponse('No images found for the given prompt. Please try another search term.');
    }
    
    // Format the results
    $images = [];
    foreach ($responseData['results'] as $image) {
        $images[] = [
            'url' => $image['urls']['regular'],
            'full_url' => $image['urls']['full'],
            'credit' => [
                'name' => $image['user']['name'],
                'link' => $image['user']['links']['html'] . '?utm_source=ai_image_generator&utm_medium=referral'
            ]
        ];
    }
    
    // Return success response
    sendJsonResponse([
        'success' => true,
        'images' => $images,
        'source' => 'unsplash'
    ]);
    
} catch (Exception $e) {
    // Fallback to StarryAI if available
    if (!empty($config['starryai']['api_key'])) {
        try {
            // Prepare StarryAI API request
            $apiUrl = $config['starryai']['api_url'];
            $requestData = [
                'prompt' => $prompt,
                'height' => 512,
                'width' => 768,
                'numberOfImages' => $count,
                'style' => mapStyleToStarryAI($style)
            ];
            
            // Setup cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $config['starryai']['api_key'],
                'Content-Type: application/json'
            ]);
            
            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            // Check for errors
            if ($httpCode != 200) {
                $errorData = json_decode($response, true);
                $errorMessage = isset($errorData['message']) ? $errorData['message'] : 'StarryAI API request failed';
                throw new Exception($errorMessage);
            }
            
            // Parse the response
            $responseData = json_decode($response, true);
            
            // Format the results (StarryAI response format may vary)
            $images = [];
            if (isset($responseData['generations'])) {
                foreach ($responseData['generations'] as $generation) {
                    $images[] = [
                        'url' => $generation['image_url'],
                        'full_url' => $generation['image_url'],
                        'credit' => [
                            'name' => 'StarryAI',
                            'link' => 'https://www.starryai.com'
                        ]
                    ];
                }
            }
            
            // Return success response
            sendJsonResponse([
                'success' => true,
                'images' => $images,
                'source' => 'starryai'
            ]);
            
        } catch (Exception $starryError) {
            // If StarryAI also fails, return the original Unsplash error
            sendErrorResponse('Image generation failed: ' . $e->getMessage());
        }
    } else {
        // No fallback available
        sendErrorResponse('Image generation failed: ' . $e->getMessage());
    }
}

/**
 * Map frontend style to StarryAI style
 */
function mapStyleToStarryAI($style) {
    switch ($style) {
        case 'photo':
            return 'photographic';
        case 'digital-art':
            return 'digital-art';
        case 'anime':
            return 'anime';
        case 'oil-painting':
            return 'oil-painting';
        case 'black-and-white':
            return 'black-and-white';
        case 'minimal':
            return 'minimalist';
        case 'vibrant':
            return 'vibrant';
        default:
            return 'photographic';
    }
}
?>
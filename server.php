<?php
/**
 * Simple PHP development server for the Image Generator application
 */

// Run the server at 0.0.0.0:8000
$host = '0.0.0.0';
$port = 8000;

// Output the start message
echo "Starting PHP server at http://{$host}:{$port}\n";
echo "Press Ctrl+C to stop the server\n";

// Command to start the PHP server
$command = "php -S {$host}:{$port}";

// Execute the command
passthru($command);
?>
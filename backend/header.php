<?php
// CORS headers
header("Access-Control-Allow-Origin: *"); // For development, allow all origins
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
// Handle preflight requests (important for React fetch)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once './config/dbconn.php';
?>

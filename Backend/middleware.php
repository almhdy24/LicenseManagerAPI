<?php
function authenticateRequest() {
    // Correct path to config.php
    $config = require __DIR__ . '/config.php';
    $secretKey = $config['secret_key'];

    // Check for secret key in request headers
    if (!isset($_SERVER['HTTP_X_SECRET_KEY']) || $_SERVER['HTTP_X_SECRET_KEY'] !== $secretKey) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['message' => 'Forbidden: Invalid secret key.']);
        exit;
    }
}
?>
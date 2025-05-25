<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $scoreboard = getScoreboard();
    echo json_encode([
        'success' => true,
        'data' => $scoreboard,
        'timestamp' => time()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch scores']);
}
?>

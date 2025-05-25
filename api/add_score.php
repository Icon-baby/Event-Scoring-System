<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$judge_id = sanitizeInput($input['judge_id'] ?? '');
$user_id = intval($input['user_id'] ?? 0);
$score = intval($input['score'] ?? 0);

if (empty($judge_id) || $user_id <= 0 || $score < 1 || $score > 100) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input parameters']);
    exit;
}

if (!judgeExists($judge_id)) {
    http_response_code(404);
    echo json_encode(['error' => 'Judge not found']);
    exit;
}

if (addScore($judge_id, $user_id, $score)) {
    echo json_encode(['success' => true, 'message' => 'Score submitted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit score']);
}
?>

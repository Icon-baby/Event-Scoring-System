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
$display_name = sanitizeInput($input['display_name'] ?? '');

if (empty($judge_id) || empty($display_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Both judge_id and display_name are required']);
    exit;
}

if (judgeExists($judge_id)) {
    http_response_code(409);
    echo json_encode(['error' => 'Judge with this ID already exists']);
    exit;
}

if (addJudge($judge_id, $display_name)) {
    echo json_encode(['success' => true, 'message' => 'Judge added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to add judge']);
}
?>

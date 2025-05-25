<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Add a new judge to the system
 */
function addJudge($judge_id, $display_name) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO judges (judge_id, display_name) VALUES (?, ?)");
        return $stmt->execute([$judge_id, $display_name]);
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Get all judges
 */
function getAllJudges() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM judges ORDER BY display_name");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Get all users (participants)
 */
function getAllUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY name");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Add a score for a user by a judge
 */
function addScore($judge_id, $user_id, $score) {
    global $pdo;
    
    try {
        // Check if score already exists for this judge-user combination
        $stmt = $pdo->prepare("SELECT id FROM scores WHERE judge_id = ? AND user_id = ?");
        $stmt->execute([$judge_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing score
            $stmt = $pdo->prepare("UPDATE scores SET score = ?, created_at = NOW() WHERE judge_id = ? AND user_id = ?");
            return $stmt->execute([$score, $judge_id, $user_id]);
        } else {
            // Insert new score
            $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, score) VALUES (?, ?, ?)");
            return $stmt->execute([$judge_id, $user_id, $score]);
        }
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Get scoreboard data with total points
 */
function getScoreboard() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT 
                u.id,
                u.name,
                COALESCE(SUM(s.score), 0) as total_score,
                COUNT(s.score) as score_count
            FROM users u
            LEFT JOIN scores s ON u.id = s.user_id
            GROUP BY u.id, u.name
            ORDER BY total_score DESC, u.name ASC
        ");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Get scores for a specific judge
 */
function getJudgeScores($judge_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                u.id,
                u.name,
                s.score
            FROM users u
            LEFT JOIN scores s ON u.id = s.user_id AND s.judge_id = ?
            ORDER BY u.name
        ");
        $stmt->execute([$judge_id]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Check if judge exists
 */
function judgeExists($judge_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM judges WHERE judge_id = ?");
        $stmt->execute([$judge_id]);
        return $stmt->fetchColumn() > 0;
    } catch(PDOException $e) {
        return false;
    }
}
?>

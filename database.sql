-- Event Scoring System Database Schema
-- LAMP Stack Implementation

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS event_scoring;
-- USE event_scoring;

-- Table for storing judges
CREATE TABLE IF NOT EXISTS judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_judge_id (judge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing users/participants
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing scores
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judge_id VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    score INT NOT NULL CHECK (score >= 1 AND score <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(judge_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_judge_user (judge_id, user_id),
    INDEX idx_judge_id (judge_id),
    INDEX idx_user_id (user_id),
    INDEX idx_score (score),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample participants/users for demonstration
INSERT IGNORE INTO users (name, email) VALUES
('Alice Johnson', 'alice.johnson@example.com'),
('Bob Smith', 'bob.smith@example.com'),
('Carol Davis', 'carol.davis@example.com'),
('David Wilson', 'david.wilson@example.com'),
('Eva Brown', 'eva.brown@example.com'),
('Frank Miller', 'frank.miller@example.com'),
('Grace Lee', 'grace.lee@example.com'),
('Henry Taylor', 'henry.taylor@example.com'),
('Ivy Anderson', 'ivy.anderson@example.com'),
('Jack Thomas', 'jack.thomas@example.com');

-- Create a view for easy scoreboard queries
CREATE OR REPLACE VIEW scoreboard_view AS
SELECT 
    u.id,
    u.name,
    u.email,
    COALESCE(SUM(s.score), 0) as total_score,
    COUNT(s.score) as judge_count,
    CASE 
        WHEN COUNT(s.score) > 0 THEN ROUND(AVG(s.score), 2)
        ELSE 0 
    END as average_score,
    MAX(s.created_at) as last_scored
FROM users u
LEFT JOIN scores s ON u.id = s.user_id
GROUP BY u.id, u.name, u.email
ORDER BY total_score DESC, average_score DESC, u.name ASC;

-- Create a view for judge performance
CREATE OR REPLACE VIEW judge_stats_view AS
SELECT 
    j.judge_id,
    j.display_name,
    COUNT(s.score) as scores_given,
    COALESCE(AVG(s.score), 0) as average_score_given,
    MIN(s.score) as min_score,
    MAX(s.score) as max_score,
    MAX(s.created_at) as last_activity
FROM judges j
LEFT JOIN scores s ON j.judge_id = s.judge_id
GROUP BY j.judge_id, j.display_name
ORDER BY scores_given DESC, j.display_name ASC;

-- Stored procedure to get user rankings
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS GetUserRankings()
BEGIN
    SELECT 
        u.id,
        u.name,
        u.email,
        COALESCE(SUM(s.score), 0) as total_score,
        COUNT(s.score) as judge_count,
        ROUND(COALESCE(AVG(s.score), 0), 2) as average_score,
        RANK() OVER (ORDER BY COALESCE(SUM(s.score), 0) DESC) as ranking
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id
    GROUP BY u.id, u.name, u.email
    ORDER BY total_score DESC, average_score DESC, u.name ASC;
END //
DELIMITER ;

-- Function to get user's current rank
DELIMITER //
CREATE FUNCTION IF NOT EXISTS GetUserRank(user_id INT) 
RETURNS INT
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE user_rank INT;
    
    SELECT ranking INTO user_rank
    FROM (
        SELECT 
            u.id,
            RANK() OVER (ORDER BY COALESCE(SUM(s.score), 0) DESC) as ranking
        FROM users u
        LEFT JOIN scores s ON u.id = s.user_id
        GROUP BY u.id
    ) ranked_users
    WHERE id = user_id;
    
    RETURN COALESCE(user_rank, 0);
END //
DELIMITER ;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_scores_composite ON scores(user_id, score, created_at);
CREATE INDEX IF NOT EXISTS idx_users_name ON users(name);
CREATE INDEX IF NOT EXISTS idx_judges_display_name ON judges(display_name);

-- Add some constraints for data integrity
ALTER TABLE scores ADD CONSTRAINT chk_score_range CHECK (score BETWEEN 1 AND 100);

-- Add trigger to update timestamp on score modification
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_score_timestamp 
    BEFORE UPDATE ON scores
    FOR EACH ROW
BEGIN
    SET NEW.created_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Optional: Add audit table for tracking changes
CREATE TABLE IF NOT EXISTS score_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_type ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    judge_id VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    old_score INT,
    new_score INT,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_timestamp (action_timestamp),
    INDEX idx_audit_judge (judge_id),
    INDEX idx_audit_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Triggers for audit trail
DELIMITER //
CREATE TRIGGER IF NOT EXISTS audit_score_insert
    AFTER INSERT ON scores
    FOR EACH ROW
BEGIN
    INSERT INTO score_audit (action_type, judge_id, user_id, new_score)
    VALUES ('INSERT', NEW.judge_id, NEW.user_id, NEW.score);
END //

CREATE TRIGGER IF NOT EXISTS audit_score_update
    AFTER UPDATE ON scores
    FOR EACH ROW
BEGIN
    INSERT INTO score_audit (action_type, judge_id, user_id, old_score, new_score)
    VALUES ('UPDATE', NEW.judge_id, NEW.user_id, OLD.score, NEW.score);
END //

CREATE TRIGGER IF NOT EXISTS audit_score_delete
    AFTER DELETE ON scores
    FOR EACH ROW
BEGIN
    INSERT INTO score_audit (action_type, judge_id, user_id, old_score)
    VALUES ('DELETE', OLD.judge_id, OLD.user_id, OLD.score);
END //
DELIMITER ;

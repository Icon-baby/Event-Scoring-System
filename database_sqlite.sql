-- Event Scoring System Database Schema
-- SQLite Implementation

-- Table for storing judges
CREATE TABLE IF NOT EXISTS judges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    judge_id VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_judge_id ON judges(judge_id);

-- Table for storing users/participants
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_email ON users(email);

-- Table for storing scores
CREATE TABLE IF NOT EXISTS scores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    judge_id VARCHAR(50) NOT NULL,
    user_id INTEGER NOT NULL,
    score INTEGER NOT NULL CHECK (score >= 1 AND score <= 100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (judge_id) REFERENCES judges(judge_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (judge_id, user_id)
);

CREATE INDEX IF NOT EXISTS idx_scores_judge_id ON scores(judge_id);
CREATE INDEX IF NOT EXISTS idx_scores_user_id ON scores(user_id);
CREATE INDEX IF NOT EXISTS idx_scores_score ON scores(score);

-- Insert sample participants/users for demonstration
INSERT OR IGNORE INTO users (name, email) VALUES
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
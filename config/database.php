<?php
// Database configuration - Using SQLite for demonstration
$dbPath = __DIR__ . '/../event_scoring.db';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize database if tables don't exist
try {
    // Check if tables exist, if not create them
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='judges'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        // Read and execute SQL schema
        $sql = file_get_contents('database_sqlite.sql');
        $pdo->exec($sql);
    }
} catch(PDOException $e) {
    // Tables might already exist, which is fine
}
?>

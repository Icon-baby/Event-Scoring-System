<?php
require_once 'includes/functions.php';

echo "<h2>Debug: Judges in Database</h2>";

try {
    $judges = getAllJudges();
    echo "<p>Found " . count($judges) . " judges:</p>";
    
    if (empty($judges)) {
        echo "<p style='color: red;'>No judges found in database!</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Judge ID</th><th>Display Name</th><th>Created At</th></tr>";
        foreach ($judges as $judge) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($judge['id']) . "</td>";
            echo "<td>" . htmlspecialchars($judge['judge_id']) . "</td>";
            echo "<td>" . htmlspecialchars($judge['display_name']) . "</td>";
            echo "<td>" . htmlspecialchars($judge['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test specific judge
    echo "<h3>Testing judge001:</h3>";
    if (judgeExists('judge001')) {
        echo "<p style='color: green;'>judge001 EXISTS in database</p>";
    } else {
        echo "<p style='color: red;'>judge001 NOT FOUND in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
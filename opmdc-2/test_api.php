<?php
// Test API endpoint
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...\n";

try {
    $mysqli = require __DIR__ . '/db.php';
    echo "Database connected successfully!\n";
    
    echo "Testing query...\n";
    $sql = "SELECT id, username, email, name AS representative, barangayName, role, status, created_at FROM users WHERE role = 'Barangay Official' ORDER BY barangayName ASC LIMIT 5";
    $res = $mysqli->query($sql);
    
    if ($res) {
        echo "Query executed successfully!\n";
        echo "Number of rows: " . $res->num_rows . "\n";
        
        while ($row = $res->fetch_assoc()) {
            echo "Found user: " . $row['username'] . " (" . $row['barangayName'] . ")\n";
        }
    } else {
        echo "Query failed: " . $mysqli->error . "\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

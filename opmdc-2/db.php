<?php
// db.php - simple mysqli connection helper
// Edit these settings if your MySQL credentials differ
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'capstone';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo 'Failed to connect to database.';
    error_log('DB connection error: ' . $mysqli->connect_error);
    exit;
}

// Set charset and collation
if (! $mysqli->set_charset('utf8mb4')) {
    // fallback or log
    error_log('Error setting charset: ' . $mysqli->error);
}

// Ensure connection collation is utf8mb4_general_ci
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

return $mysqli;
?>

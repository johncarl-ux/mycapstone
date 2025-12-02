<?php
// Test if proposal_history table exists and has data
session_start();
$conn = require_once '../db.php';

header('Content-Type: application/json');

if (!$conn || $conn->connect_errno) {
    echo json_encode(['error' => 'Connection failed: ' . ($conn ? $conn->connect_error : 'No connection')]);
    exit;
}

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'proposal_history'");
if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Table proposal_history does not exist']);
    exit;
}

// Count records
$result = $conn->query("SELECT COUNT(*) as count FROM proposal_history");
$row = $result->fetch_assoc();

echo json_encode([
    'table_exists' => true,
    'total_records' => $row['count'],
    'session_user' => $_SESSION['user_id'] ?? 'not set',
    'session_role' => $_SESSION['role'] ?? 'not set'
]);
?>

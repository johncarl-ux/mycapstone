<?php
// list_barangay_accounts.php
// Returns JSON list of users with role 'Barangay Official' (open access per requirements)
header('Content-Type: application/json');

$mysqli = require dirname(__DIR__) . '/db.php';

$sql = "SELECT id, username, email, name AS representative, barangayName, role, status, created_at FROM users WHERE role = 'Barangay Official' ORDER BY barangayName ASC";
// execute query and handle errors
$res = $mysqli->query($sql);
$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
    $mysqli->close();
    echo json_encode(['success' => true, 'accounts' => $out]);
    exit;
} else {
    // query failed
    $err = $mysqli->error;
    $mysqli->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query failed', 'error' => $err]);
    exit;
}

?>
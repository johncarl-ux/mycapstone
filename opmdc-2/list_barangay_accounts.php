<?php
// list_barangay_accounts.php
// Returns JSON list of users with role 'Barangay Official'
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'] ?? '', ['OPMDC Staff','OPMDC Head','Admin'], true)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$sql = "SELECT id, username, email, name AS representative, barangayName, role, status, created_at FROM users WHERE role = 'Barangay Official' ORDER BY barangayName ASC";
$res = $mysqli->query($sql);
$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
}

$mysqli->close();
echo json_encode(['success' => true, 'accounts' => $out]);
exit;

?>

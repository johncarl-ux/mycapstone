<?php
// list_staff_accounts.php
// Returns JSON list of users with roles 'OPMDC Staff' or 'OPMDC Head' or 'Admin'
header('Content-Type: application/json; charset=utf-8');

try {
    $mysqli = require dirname(__DIR__) . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$out = [];
$sql = "SELECT id, name, username, email, role FROM users WHERE role IN ('OPMDC Staff','OPMDC Head','Admin') ORDER BY name ASC";
$res = $mysqli->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
}
$mysqli->close();
echo json_encode(['success' => true, 'staff' => $out]);
exit;
?>
<?php
// list_barangay_accounts.php
// Returns JSON list of users with role 'Barangay Official' (open access per requirements)
header('Content-Type: application/json');

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

<?php
// list_requests.php - returns JSON list of requests.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$mysqli = require __DIR__ . '/db.php';

$role = $_GET['role'] ?? null; // optional: OPMDC Staff or OPMDC Head or Barangay Official
$barangay = $_GET['barangay'] ?? null; // optional filter

$sql = "SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests";
$conds = [];
$params = [];

if ($role === 'Barangay Official' && $barangay) {
    $conds[] = 'barangay = ?';
    $params[] = $barangay;
}

if (count($conds) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $conds);
}

$sql .= ' ORDER BY created_at DESC LIMIT 500';

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed', 'details' => $mysqli->error]);
    exit;
}

if (count($params) > 0) {
    // bind_param with dynamic args; for one param just bind directly to avoid unpacking issues
    if (count($params) === 1) {
        $stmt->bind_param('s', $params[0]);
    } else {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
}

if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed', 'details' => $stmt->error]);
    exit;
}

$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    // try to decode history JSON to a PHP array
    $r['history'] = json_decode($r['history'], true) ?: [];
    $rows[] = $r;
}

echo json_encode(['requests' => $rows]);
exit;
?>

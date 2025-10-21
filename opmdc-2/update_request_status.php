<?php
// update_request_status.php - update status of a request and append to history
// Use session-based auth: only OPMDC Staff and OPMDC Head may update request status.
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// Basic auth: require logged-in user with allowed role
$allowedRoles = ['OPMDC Staff', 'OPMDC Head'];
$sessionUser = $_SESSION['user'] ?? null;
if (! $sessionUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated']);
    exit;
}
if (! in_array($sessionUser['role'] ?? '', $allowedRoles, true)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$id = intval($_POST['id'] ?? 0);
$newStatus = $_POST['status'] ?? '';
$note = $mysqli->real_escape_string($_POST['note'] ?? '');
// Prefer session user as actor; do not trust client-supplied actor
$actorName = $sessionUser['name'] ?? $sessionUser['username'] ?? ($sessionUser['role'] ?? '');
$actor = $mysqli->real_escape_string($actorName);

if ($id <= 0 || !in_array($newStatus, ['Pending','Approved','Declined'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// fetch current history
$stmt = $mysqli->prepare('SELECT history FROM requests WHERE id = ?');
$stmt->bind_param('i', $id);
if (! $stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'fetch failed']); exit; }
$res = $stmt->get_result();
if ($res->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'not found']); exit; }
$row = $res->fetch_assoc();
$stmt->close();

$history = json_decode($row['history'], true) ?: [];
$history[] = ['status' => $newStatus, 'timestamp' => date('c'), 'notes' => $note, 'actor' => $actor];
$historyJson = json_encode($history);

$stmt = $mysqli->prepare('UPDATE requests SET status = ?, history = ? WHERE id = ?');
$stmt->bind_param('ssi', $newStatus, $historyJson, $id);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'update failed', 'details' => $stmt->error]);
    exit;
}

echo json_encode(['id' => $id, 'status' => $newStatus, 'history' => $history]);
exit;
?>

<?php
// delete_request.php - deletes a request
// Open access (no server-side session). Only allow delete when status is Approved or Declined.
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $mysqli = require __DIR__ . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid id']);
    exit;
}

// find the request and verify allowed status
$stmt = $mysqli->prepare('SELECT id, barangay, status FROM requests WHERE id = ? LIMIT 1');
if (! $stmt) { http_response_code(500); echo json_encode(['error' => 'Prepare failed']); exit; }
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();
if (! $row) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$status = strtolower((string)($row['status'] ?? ''));
if (!in_array($status, ['approved', 'declined'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Only Approved or Declined requests can be deleted']);
    exit;
}

$del = $mysqli->prepare('DELETE FROM requests WHERE id = ?');
if (! $del) { http_response_code(500); echo json_encode(['error' => 'Prepare failed']); exit; }
$del->bind_param('i', $id);
if (! $del->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Delete failed']);
    exit;
}
$del->close();

echo json_encode(['success' => true]);
exit;
?>

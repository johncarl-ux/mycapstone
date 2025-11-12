<?php

header('Content-Type: application/json; charset=utf-8');

// Simple session/role check to prevent unauthorized access
session_start();
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? null;
// allow OPMDC Head and Admin (you can extend this list if needed)
if (! $role || ! in_array($role, ['OPMDC Head', 'Admin', 'OPMDC Staff'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing id']);
    exit;
}

$sql = "SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests WHERE id = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed', 'details' => $mysqli->error]);
    exit;
}

$stmt->bind_param('i', $id);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed', 'details' => $stmt->error]);
    exit;
}

$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (! $row) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

// try decode history JSON
if (isset($row['history'])) {
    $decoded = json_decode($row['history'], true);
    $row['history'] = $decoded ?: [];
} else {
    $row['history'] = [];
}

echo json_encode(['request' => $row]);
exit;
?>

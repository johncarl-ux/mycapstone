<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'Authentication required']); exit; }
try {
    $mysqli = require __DIR__ . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }

// authorize: allow if user is staff/head or if user is creator
$userId = $_SESSION['user']['id'] ?? 0;
$userRole = $_SESSION['user']['role'] ?? '';
$check = $mysqli->prepare('SELECT created_by FROM notifications WHERE id = ?');
$check->bind_param('i', $id);
$check->execute();
$res = $check->get_result()->fetch_assoc();
$createdBy = $res['created_by'] ?? 0;
if (!in_array($userRole, ['OPMDC Staff','OPMDC Head']) && intval($createdBy) !== intval($userId)) {
    http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit;
}

$stmt = $mysqli->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
$stmt->bind_param('i', $id);
if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'update failed']); exit; }

echo json_encode(['success' => true]);
?>

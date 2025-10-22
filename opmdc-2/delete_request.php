<?php
// delete_request.php - deletes a request if the user is authorized
header('Content-Type: application/json; charset=utf-8');
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (! isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
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

// find the request
$stmt = $mysqli->prepare('SELECT id, barangay, created_at FROM requests WHERE id = ? LIMIT 1');
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

$user = $_SESSION['user'];
$role = $user['role'] ?? '';
$userBarangay = $user['barangayName'] ?? ($user['barangay'] ?? null);

// authorize: allow if staff/head or same barangay
if (! in_array($role, ['OPMDC Staff', 'OPMDC Head']) ) {
    if (! $userBarangay || $userBarangay !== $row['barangay']) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
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

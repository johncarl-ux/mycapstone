<?php
header('Content-Type: application/json; charset=utf-8');
// POST to delete a recommendation by id
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
$input = $_POST;
if (!isset($input['id']) || !is_numeric($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}
$id = intval($input['id']);

$dbPath = __DIR__ . '/db.php';
if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database config missing']);
    exit;
}
$mysqli = include $dbPath;
if (!($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB init failed']);
    exit;
}

// Optional: check permissions here (not implemented)

$sql = "DELETE FROM plan_recommendations WHERE id = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'deleted' => $id]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Not found']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Execute failed', 'details' => $stmt->error]);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Prepare failed']);
}

@$mysqli->close();

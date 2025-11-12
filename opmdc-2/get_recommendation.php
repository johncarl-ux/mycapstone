<?php
header('Content-Type: application/json; charset=utf-8');
// GET one recommendation by id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}
$id = intval($_GET['id']);

$dbPath = __DIR__ . '/db.php';
if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database config missing']);
    exit;
}
/** @noinspection PhpIncludeInspection */
$mysqli = include $dbPath;
if (!($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB init failed']);
    exit;
}

$sql = "SELECT id, category, title, summary, details, relevance, source, created_at, updated_at FROM plan_recommendations WHERE id = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row) {
        echo json_encode(['success' => true, 'recommendation' => $row]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Not found']);
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Prepare failed']);
}

@$mysqli->close();

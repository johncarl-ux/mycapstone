<?php
// notifications.php
// Simple notifications endpoint.
// GET: ?role=OPMDC%20Staff or ?user_id=123  -> returns JSON list
// POST: { title, body, target_role } -> creates notification

header('Content-Type: application/json; charset=utf-8');
session_start();
// require logged-in user for all notification operations
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}
try {
    $mysqli = require dirname(__DIR__) . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Ensure notifications table exists (simple migration)
$createSql = "CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    request_id BIGINT UNSIGNED DEFAULT NULL,
    target_role VARCHAR(64) DEFAULT NULL,
    target_user_id BIGINT UNSIGNED DEFAULT NULL,
    created_by BIGINT UNSIGNED DEFAULT NULL,
    created_by_role VARCHAR(64) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($createSql);
// If notifications table existed before this change, ensure request_id column exists (MySQL 8+ supports IF NOT EXISTS)
$mysqli->query("ALTER TABLE notifications ADD COLUMN IF NOT EXISTS request_id BIGINT UNSIGNED DEFAULT NULL;");

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $role = $_GET['role'] ?? null;
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

    // default: if no role/user specified, use current user's role
    if (!$role && !$userId) {
        $role = $_SESSION['user']['role'] ?? null;
    }

    $params = [];
    $sql = "SELECT id, title, body, request_id, target_role, target_user_id, created_by, created_by_role, is_read, created_at FROM notifications";
    $conds = [];
    if ($role) { $conds[] = "target_role = ?"; $params[] = $role; }
    if ($userId) { $conds[] = "target_user_id = ?"; $params[] = $userId; }
    if (count($conds)) { $sql .= ' WHERE ' . implode(' OR ', $conds); }
    $sql .= ' ORDER BY created_at DESC LIMIT 200';

    $stmt = $mysqli->prepare($sql);
    if ($params) {
        // build types string carefully: 's' for role, 'i' for user id
        $types = '';
        foreach ($params as $p) { $types .= is_int($p) ? 'i' : 's'; }
        // bind params dynamically
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['notifications' => $rows]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    $title = trim($input['title'] ?? '');
    $body = trim($input['body'] ?? '');
    $targetRole = $input['target_role'] ?? null;
    $targetUserId = isset($input['target_user_id']) ? intval($input['target_user_id']) : null;

    if ($title === '' || $body === '') {
        http_response_code(400);
        echo json_encode(['error' => 'title and body required']);
        exit;
    }

    $createdBy = $_SESSION['user']['id'] ?? null;
    $createdByRole = $_SESSION['user']['role'] ?? null;
    $requestId = isset($input['request_id']) ? intval($input['request_id']) : null;
    $stmt = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_role, target_user_id, created_by, created_by_role) VALUES (?,?,?,?,?,?,?)');
    // types: title(s), body(s), request_id(i), target_role(s), target_user_id(i), created_by(i), created_by_role(s)
    $stmt->bind_param('ssisiis', $title, $body, $requestId, $targetRole, $targetUserId, $createdBy, $createdByRole);
    if (! $stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Insert failed']);
        exit;
    }

    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

?>

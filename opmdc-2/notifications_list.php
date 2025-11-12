<?php
// notifications_list.php
// Returns recent notifications for the current authenticated user/role and an unread count.
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['user'])) {
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

$role = $_SESSION['user']['role'] ?? null;
$userId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$sinceId = isset($_GET['since_id']) ? intval($_GET['since_id']) : 0;

$params = [];
$sql = "SELECT id, title, body, request_id, target_role, target_user_id, created_by, created_by_role, is_read, created_at FROM notifications";
$conds = [];
if ($role) { $conds[] = "target_role = ?"; $params[] = $role; }
if ($userId) { $conds[] = "target_user_id = ?"; $params[] = $userId; }
if (count($conds)) { $sql .= ' WHERE (' . implode(' OR ', $conds) . ')'; }
if ($sinceId > 0) { $sql .= (count($conds) ? ' AND ' : ' WHERE ') . 'id > ?'; $params[] = $sinceId; }
$sql .= ' ORDER BY created_at DESC LIMIT ?';
$params[] = $limit;

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'prepare failed']);
    exit;
}

$types = '';
foreach ($params as $p) { $types .= is_int($p) ? 'i' : 's'; }
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;

// compute unread count for this user/role
$countSql = "SELECT COUNT(*) as unread FROM notifications";
$countParams = [];
$countConds = [];
if ($role) { $countConds[] = "target_role = ?"; $countParams[] = $role; }
if ($userId) { $countConds[] = "target_user_id = ?"; $countParams[] = $userId; }
if (count($countConds)) { $countSql .= ' WHERE (' . implode(' OR ', $countConds) . ') AND is_read = 0'; }
else { $countSql .= ' WHERE is_read = 0'; }

$countStmt = $mysqli->prepare($countSql);
if ($countStmt) {
    if (count($countParams)) {
        $ctypes = '';
        foreach ($countParams as $p) $ctypes .= is_int($p) ? 'i' : 's';
        $countStmt->bind_param($ctypes, ...$countParams);
    }
    $countStmt->execute();
    $cnt = $countStmt->get_result()->fetch_assoc();
    $unread = intval($cnt['unread'] ?? 0);
} else {
    $unread = 0;
}

echo json_encode(['notifications' => $rows, 'unread_count' => $unread]);
exit;
?>

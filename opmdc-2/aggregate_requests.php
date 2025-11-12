<?php
// aggregate_requests.php - returns aggregated counts (total, approved, pending, declined) and type breakdowns
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$mysqli = require __DIR__ . '/db.php';

// Optional filters (role/barangay) to match list_requests usage
$role = $_GET['role'] ?? null;
$barangay = $_GET['barangay'] ?? null;

$conds = [];
$params = [];
if ($role === 'Barangay Official' && $barangay) {
    $conds[] = 'barangay = ?';
    $params[] = $barangay;
}
$where = '';
if (count($conds) > 0) {
    $where = ' WHERE ' . implode(' AND ', $conds);
}

// Build aggregate query. Use case-insensitive comparisons and include numeric fallbacks.
$aggSql = "SELECT 
    COUNT(*) AS total, 
    SUM(CASE WHEN LOWER(status) LIKE '%approved%' OR status = '1' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN LOWER(status) LIKE '%pending%' OR status = '0' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN LOWER(status) LIKE '%declined%' OR LOWER(status) LIKE '%denied%' THEN 1 ELSE 0 END) AS declined
    FROM requests" . $where;

$stmt = $mysqli->prepare($aggSql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed', 'details' => $mysqli->error]);
    exit;
}
if (count($params) === 1) $stmt->bind_param('s', $params[0]);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed', 'details' => $stmt->error]);
    exit;
}
$res = $stmt->get_result();
$agg = $res->fetch_assoc() ?: ['total'=>0,'approved'=>0,'pending'=>0,'declined'=>0];

// By type breakdown
$byTypeSql = "SELECT COALESCE(NULLIF(request_type,''),'Unknown') AS request_type, COUNT(*) AS cnt FROM requests" . $where . " GROUP BY request_type ORDER BY cnt DESC LIMIT 50";
$stmt2 = $mysqli->prepare($byTypeSql);
if ($stmt2 === false) {
    // still return agg
    echo json_encode(['aggregate' => $agg, 'by_type' => []]);
    exit;
}
if (count($params) === 1) $stmt2->bind_param('s', $params[0]);
if (! $stmt2->execute()) {
    echo json_encode(['aggregate' => $agg, 'by_type' => []]);
    exit;
}
$res2 = $stmt2->get_result();
$types = [];
while ($row = $res2->fetch_assoc()) {
    $types[] = ['type' => $row['request_type'], 'count' => (int)$row['cnt']];
}

// include server timestamp for accuracy
echo json_encode(['aggregate' => array_map('intval', $agg), 'by_type' => $types, 'as_of' => date('c')]);
exit;
?>
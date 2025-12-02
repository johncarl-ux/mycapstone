<?php
// list_staff_proposals.php
// Returns project proposals with history and lastUpdated for staff UI
error_reporting(0);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

// Note: This endpoint is used by the staff dashboard which may be served as a plain HTML file
// without a PHP session context. To ensure the dashboard can read proposals, we allow open access
// here. If you need to restrict access, add proper auth (JWT/session) and include credentials
// from the client accordingly.

try {
    $mysqli = require dirname(__DIR__) . '/db.php';
    
    // Check if database connection is valid
    if ($mysqli->connect_errno) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed', 'details' => $mysqli->connect_error]);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$idFilter = isset($_GET['id']) ? intval($_GET['id']) : 0;
$barangayFilter = isset($_GET['barangay']) ? trim($_GET['barangay']) : '';

$sql = 'SELECT id, title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail, status, created_at FROM project_proposals';
$params = [];
$types = '';
$conditions = [];

if ($idFilter) {
    $conditions[] = 'id = ?';
    $params[] = $idFilter;
    $types .= 'i';
}
if ($barangayFilter) {
    $conditions[] = 'barangay = ?';
    $params[] = $barangayFilter;
    $types .= 's';
}

if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

if ($idFilter) {
    $sql .= ' LIMIT 1';
} else {
    $sql .= ' ORDER BY created_at DESC LIMIT 500';
}

$stmt = $mysqli->prepare($sql);
if ($params) { 
    $stmt->bind_param($types, ...$params); 
}
$stmt->execute();
$res = $stmt->get_result();
$proposals = [];
while ($row = $res->fetch_assoc()) {
    $proposalId = intval($row['id']);
    // fetch history
    $hstmt = $mysqli->prepare('SELECT status, remarks, user_id, user_role, created_at FROM proposal_history WHERE proposal_id = ? ORDER BY created_at ASC');
    $hstmt->bind_param('i', $proposalId);
    $hstmt->execute();
    $hres = $hstmt->get_result();
    $history = [];
    $lastUpdated = $row['created_at'];
    while ($h = $hres->fetch_assoc()) {
        $history[] = [
            'status' => $h['status'],
            'remarks' => $h['remarks'],
            'user_id' => $h['user_id'],
            'user_role' => $h['user_role'],
            'date' => $h['created_at']
        ];
        if (strtotime($h['created_at']) > strtotime($lastUpdated)) $lastUpdated = $h['created_at'];
    }
    $hstmt->close();

    $proposals[] = [
        'id' => $proposalId,
        'title' => $row['title'],
        'project_type' => $row['project_type'],
        'barangay' => $row['barangay'],
        'request_type' => $row['request_type'],
        'urgency' => $row['urgency'],
        'location' => $row['location'],
        'budget' => $row['budget'],
        'description' => $row['description'],
        'attachment' => $row['attachment'],
        'thumbnail' => $row['thumbnail'],
        'status' => $row['status'],
        'date' => $row['created_at'],
        'lastUpdated' => $lastUpdated,
        'history' => $history
    ];
}
$stmt->close();

echo json_encode(['proposals' => $proposals]);
exit;
?>
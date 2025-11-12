<?php
// list_staff_proposals.php
// Returns project proposals with history and lastUpdated for staff UI
header('Content-Type: application/json; charset=utf-8');
session_start();

// Basic authorization: require a logged-in user (staff/head/admin recommended)
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

$idFilter = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = 'SELECT id, title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail, status, created_at FROM project_proposals';
$params = [];
if ($idFilter) {
    $sql .= ' WHERE id = ? LIMIT 1';
    $params[] = $idFilter;
} else {
    $sql .= ' ORDER BY created_at DESC LIMIT 500';
}

$stmt = $mysqli->prepare($sql);
if ($params) { $stmt->bind_param('i', ...$params); }
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

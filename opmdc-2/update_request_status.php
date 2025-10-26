<?php
// update_request_status.php - update status of a request and append to history
// Open access per requirements (no server-side session auth)

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$mysqli = require __DIR__ . '/db.php';

$id = intval($_POST['id'] ?? 0);
$newStatus = $_POST['status'] ?? '';
$note = $mysqli->real_escape_string($_POST['note'] ?? '');
// Use provided actor if any; else default to OPMDC Staff (client-controlled UI guards which role can call this)
$actorParam = trim($_POST['actor'] ?? '');
$actorName = $actorParam !== '' ? $actorParam : 'OPMDC Staff';
$actor = $mysqli->real_escape_string($actorName);

if ($id <= 0 || !in_array($newStatus, ['Pending','Approved','Declined'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// fetch current history
$stmt = $mysqli->prepare('SELECT history FROM requests WHERE id = ?');
$stmt->bind_param('i', $id);
if (! $stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'fetch failed']); exit; }
$res = $stmt->get_result();
if ($res->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'not found']); exit; }
$row = $res->fetch_assoc();
$stmt->close();

$history = json_decode($row['history'], true) ?: [];
$history[] = ['status' => $newStatus, 'timestamp' => date('c'), 'notes' => $note, 'actor' => $actor];
$historyJson = json_encode($history);

$stmt = $mysqli->prepare('UPDATE requests SET status = ?, history = ? WHERE id = ?');
$stmt->bind_param('ssi', $newStatus, $historyJson, $id);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'update failed', 'details' => $stmt->error]);
    exit;
}

// Best-effort: notify the barangay user (if any) that their request changed status
try {
    // get barangay name for the request
    $q = $mysqli->prepare('SELECT barangay FROM requests WHERE id = ? LIMIT 1');
    if ($q) {
        $q->bind_param('i', $id);
        $q->execute();
        $rr = $q->get_result()->fetch_assoc();
        $q->close();
        $barangayName = $rr['barangay'] ?? null;
    } else {
        $barangayName = null;
    }

    $targetUserId = null;
    if ($barangayName) {
        $q2 = $mysqli->prepare("SELECT id FROM users WHERE role = 'Barangay Official' AND barangayName = ? LIMIT 1");
        if ($q2) {
            $q2->bind_param('s', $barangayName);
            $q2->execute();
            $r2 = $q2->get_result()->fetch_assoc();
            $q2->close();
            $targetUserId = $r2['id'] ?? null;
        }
    }

    $title = 'Request Update';
    $body = "Request #{$id} status changed to {$newStatus} by {$actor}";
    if ($ins = $mysqli->prepare('INSERT INTO notifications (title, body, target_role, target_user_id, created_by, created_by_role) VALUES (?,?,?,?,?,?)')) {
        $createdBy = null;
        $createdByRole = $actorName;
        $targetRole = null; // prefer targeting specific user id
        $targetUserParam = $targetUserId ? intval($targetUserId) : null;
        $ins->bind_param('sssiis', $title, $body, $targetRole, $targetUserParam, $createdBy, $createdByRole);
        @$ins->execute();
        $ins->close();
    }
} catch (Exception $e) {
    // ignore notification errors
}

echo json_encode(['id' => $id, 'status' => $newStatus, 'history' => $history]);
exit;
?>

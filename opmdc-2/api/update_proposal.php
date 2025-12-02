<?php
// update_proposal.php
// Update a proposal's status and create notification(s) for the submitting barangay user(s).
header('Content-Type: application/json; charset=utf-8');
session_start();

// simple auth: only staff/head/admin may update proposal status
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}
$user = $_SESSION['user'];
$role = $user['role'] ?? '';
if (!in_array($role, ['OPMDC Staff', 'OPMDC Head', 'Admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

try {
    $mysqli = require dirname(__DIR__) . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Accept form-encoded or JSON
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $json = @json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

$id = isset($input['id']) ? intval($input['id']) : 0;
$status = isset($input['status']) ? trim($input['status']) : '';
$remarks = isset($input['remarks']) ? trim($input['remarks']) : '';

if (!$id || $status === '') {
    http_response_code(400);
    echo json_encode(['error' => 'id and status required']);
    exit;
}

// Find existing proposal
$stmt = $mysqli->prepare('SELECT id, title, project_type, barangay, status FROM project_proposals WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$proposal = $res->fetch_assoc();
$stmt->close();

if (! $proposal) {
    http_response_code(404);
    echo json_encode(['error' => 'Proposal not found']);
    exit;
}

// If staff selects Approved, forward as 'For Head Approval' for Head review
if ($role === 'OPMDC Staff' && strcasecmp($status, 'Approved') === 0) {
    $status = 'For Head Approval';
}

// Update status in master table
$u = $mysqli->prepare('UPDATE project_proposals SET status = ? WHERE id = ?');
$u->bind_param('si', $status, $id);
if (! $u->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update proposal status', 'db_error' => $u->error]);
    exit;
}
$u->close();

// Optional: record history in a separate table (create if not exists)
$mysqli->query("CREATE TABLE IF NOT EXISTS proposal_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proposal_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(128) NOT NULL,
    remarks TEXT DEFAULT NULL,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    user_role VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_proposal_id (proposal_id),
    KEY idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$hist = $mysqli->prepare('INSERT INTO proposal_history (proposal_id, status, remarks, user_id, user_role) VALUES (?,?,?,?,?)');
$uid = $user['id'] ?? null;
$urole = $user['role'] ?? null;
$hist->bind_param('issis', $id, $status, $remarks, $uid, $urole);
@$hist->execute();
$hist->close();

// Initialize notification IDs array
$insertedNotifIds = [];

// If a staff member forwards the proposal for approval, notify the Head (only once)
try {
    if (isset($user['role']) && $user['role'] === 'OPMDC Staff' && (strcasecmp($status, 'For Head Approval') === 0 || strcasecmp($status, 'For Approval') === 0)) {
        // Check if notification already exists for this proposal and status
        $checkStmt = $mysqli->prepare('SELECT id FROM notifications WHERE request_id = ? AND target_role = ? AND title LIKE ? ORDER BY created_at DESC LIMIT 1');
        $searchTitle = '%forwarded for head approval%';
        $checkRole = 'OPMDC Head';
        $checkStmt->bind_param('iss', $id, $checkRole, $searchTitle);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        $existingNotif = $checkRes->fetch_assoc();
        $checkStmt->close();
        
        // Only create notification if one doesn't already exist or if the last one is old (more than 5 minutes)
        $shouldCreate = !$existingNotif;
        if ($existingNotif) {
            $lastCreated = strtotime($existingNotif['created_at'] ?? 'now');
            $shouldCreate = (time() - $lastCreated) > 300; // 5 minutes
        }
        
        if ($shouldCreate) {
            $hTitle = "Proposal forwarded for head approval";
            $hBody = sprintf("Proposal '%s' (ID #%d) has been forwarded to the Head for approval by %s", $proposal['title'] ?? ('#' . $id), $id, $user['role'] ?? 'OPMDC Staff');
            if ($hn = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_role, created_by, created_by_role) VALUES (?,?,?,?,?,?)')) {
                $targetRoleHead = 'OPMDC Head';
                $createdBy = $user['id'] ?? null;
                $createdByRole = $user['role'] ?? null;
                // types: s, s, i, s, i, s
                $hn->bind_param('ssisis', $hTitle, $hBody, $id, $targetRoleHead, $createdBy, $createdByRole);
                @ $hn->execute();
                if ($hn->insert_id) $insertedNotifIds[] = $hn->insert_id;
                $hn->close();
            }
        }
    }
} catch (Exception $e) { /* ignore notification errors */ }

// Build notification content for barangay
$title = 'Proposal Status Update';
$body = sprintf("Your proposal '%s' has been updated to: %s", $proposal['title'] ?? ('#' . $id), $status);

// Try to find barangay user(s) to target
$findUsers = $mysqli->prepare('SELECT id FROM users WHERE barangayName = ?');
$findUsers->bind_param('s', $proposal['barangay']);
$findUsers->execute();
$r = $findUsers->get_result();
$users = $r->fetch_all(MYSQLI_ASSOC);
$findUsers->close();

if (count($users) > 0) {
    $nstmt = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_user_id, created_by, created_by_role) VALUES (?,?,?,?,?,?)');
    foreach ($users as $urow) {
        $targetUid = intval($urow['id']);
        $createdBy = $user['id'] ?? null;
        $createdByRole = $user['role'] ?? null;
        $nstmt->bind_param('ssiiis', $title, $body, $id, $targetUid, $createdBy, $createdByRole);
        if (@$nstmt->execute()) $insertedNotifIds[] = $nstmt->insert_id;
    }
    $nstmt->close();
} else {
    // fallback: notify role 'Barangay Official' so some client-side will receive it
    $nstmt = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_role, created_by, created_by_role) VALUES (?,?,?,?,?,?)');
    $targetRole = 'Barangay Official';
    $createdBy = $user['id'] ?? null;
    $createdByRole = $user['role'] ?? null;
    $nstmt->bind_param('ssisis', $title, $body, $id, $targetRole, $createdBy, $createdByRole);
    // NOTE: older PHP/MySQL bindings might complain about types; use execute and ignore failures
    if (@$nstmt->execute()) $insertedNotifIds[] = $nstmt->insert_id;
    $nstmt->close();
}

// Attempt to trigger server-sent event by touching a lightweight endpoint (SSE consumer will pick it up from DB polling)

echo json_encode(['success' => true, 'notification_ids' => $insertedNotifIds]);
exit;
?>

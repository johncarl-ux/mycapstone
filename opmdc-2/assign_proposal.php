<?php
// assign_proposal.php
// Assign a proposal to a staff member (updates project_proposals.assigned_to and inserts history)
header('Content-Type: application/json; charset=utf-8');
session_start();

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
    $mysqli = require __DIR__ . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Accept POST or JSON
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $json = @json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

$proposalId = isset($input['id']) ? intval($input['id']) : 0;
$assigneeId = isset($input['assignee_id']) ? intval($input['assignee_id']) : 0;
$remarks = isset($input['remarks']) ? trim($input['remarks']) : '';

if (!$proposalId || !$assigneeId) {
    http_response_code(400);
    echo json_encode(['error' => 'id and assignee_id required']);
    exit;
}

// verify assignee exists and is staff/head/admin
$u = $mysqli->prepare('SELECT id, name, role FROM users WHERE id = ? LIMIT 1');
$u->bind_param('i', $assigneeId);
$u->execute();
$res = $u->get_result();
$assignee = $res->fetch_assoc();
$u->close();
if (! $assignee || !in_array($assignee['role'], ['OPMDC Staff','OPMDC Head','Admin'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Assignee must be a staff/head/admin user']);
    exit;
}

// Ensure assigned_to column exists
@$mysqli->query("ALTER TABLE project_proposals ADD COLUMN assigned_to BIGINT UNSIGNED NULL AFTER status");

// Update assignment
$up = $mysqli->prepare('UPDATE project_proposals SET assigned_to = ? WHERE id = ?');
$up->bind_param('ii', $assigneeId, $proposalId);
if (! $up->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to assign proposal', 'db_error' => $up->error]);
    exit;
}
$up->close();

// record history entry
$mysqli->query("CREATE TABLE IF NOT EXISTS proposal_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    proposal_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(128) NOT NULL,
    remarks TEXT DEFAULT NULL,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    user_role VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_proposal_id (proposal_id),
    KEY idx_proposal_history_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
$hist = $mysqli->prepare('INSERT INTO proposal_history (proposal_id, status, remarks, user_id, user_role) VALUES (?,?,?,?,?)');
$statusNote = 'Assigned';
$uid = $user['id'] ?? null;
$urole = $user['role'] ?? null;
$hist->bind_param('issis', $proposalId, $statusNote, $remarks, $uid, $urole);
@$hist->execute();
$hist->close();

// Optionally notify the assignee
try {
    $title = 'Proposal Assigned';
    $body = sprintf("You have been assigned proposal ID #%d", $proposalId);
    $nstmt = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_user_id, created_by, created_by_role) VALUES (?,?,?,?,?,?)');
    $createdBy = $user['id'] ?? null;
    $createdByRole = $user['role'] ?? null;
    $nstmt->bind_param('ssiiis', $title, $body, $proposalId, $assigneeId, $createdBy, $createdByRole);
    @$nstmt->execute();
    $nstmt->close();
} catch (Exception $e) { /* ignore */ }

echo json_encode(['success' => true]);
exit;
?>
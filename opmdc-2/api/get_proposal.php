<?php
// get_proposal.php
// Fetch a single proposal with its history for tracking UI.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

try { $mysqli = require dirname(__DIR__) . '/db.php'; } catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'DB connection failed']);
  exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'error' => 'id required']); exit; }

// Ensure tables exist (lightweight safety for dev environments)
$mysqli->query("CREATE TABLE IF NOT EXISTS project_proposals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    project_type VARCHAR(32) NOT NULL,
    barangay VARCHAR(255) NOT NULL,
    request_type VARCHAR(128) DEFAULT NULL,
    urgency VARCHAR(32) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    budget DECIMAL(15,2) DEFAULT NULL,
    description TEXT NOT NULL,
    attachment VARCHAR(512) DEFAULT NULL,
    thumbnail VARCHAR(512) DEFAULT NULL,
    status VARCHAR(64) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

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

// Fetch proposal
$p = $mysqli->prepare('SELECT id, title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail, status, created_at, updated_at FROM project_proposals WHERE id = ? LIMIT 1');
$p->bind_param('i', $id);
$p->execute();
$res = $p->get_result();
$proposal = $res->fetch_assoc();
$p->close();

if (!$proposal) { http_response_code(404); echo json_encode(['success' => false, 'error' => 'Proposal not found']); exit; }

// History
$h = $mysqli->prepare('SELECT id, status, remarks, user_id, user_role, created_at FROM proposal_history WHERE proposal_id = ? ORDER BY created_at ASC, id ASC');
$h->bind_param('i', $id);
$h->execute();
$hres = $h->get_result();
$history = [];
while ($row = $hres->fetch_assoc()) { $history[] = $row; }
$h->close();

// Build ordered unique timeline (in case of duplicate consecutive statuses)
$timeline = [];
$lastStatus = null;
foreach ($history as $evt) {
  $s = $evt['status'];
  if ($s === $lastStatus) continue; // collapse duplicates
  $timeline[] = $evt;
  $lastStatus = $s;
}

// Response
echo json_encode([
  'success' => true,
  'proposal' => $proposal,
  'history' => $history,
  'timeline' => $timeline
]);
exit;
?>
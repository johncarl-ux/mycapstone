<?php
// submit_project_proposal.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

// Start session so we can determine the submitting user for notifications
if (session_status() === PHP_SESSION_NONE) session_start();

$title = trim($_POST['title'] ?? '');
// Accept either 'project_type' (used by most forms) or 'project_category' (legacy/front-end id)
$projectType = trim($_POST['project_type'] ?? $_POST['project_category'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$budget = trim($_POST['budget'] ?? '');
$description = trim($_POST['description'] ?? '');
// whether to use uploaded image as thumbnail
$useAsThumb = trim($_POST['use_as_thumbnail'] ?? '0');
// Fields from the old new_request.php that must be preserved
$requestType = trim($_POST['requestType'] ?? '');
$urgency = trim($_POST['urgency'] ?? 'Medium');
$location = trim($_POST['location'] ?? '');

if ($title === '' || $projectType === '' || $barangay === '' || $description === '') {
    http_response_code(400);
    $missing = [];
    if ($title === '') $missing[] = 'title';
    if ($projectType === '') $missing[] = 'project_type (or project_category)';
    if ($barangay === '') $missing[] = 'barangay';
    if ($description === '') $missing[] = 'description';
    echo json_encode(['success' => false, 'error' => 'Missing required fields: ' . implode(', ', $missing)]);
    exit;
}

// Handle optional attachment
$attachmentPath = null;
if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/proposals';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $original = basename($_FILES['attachment']['name']);
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $safe = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
    $target = $uploadDir . '/' . $safe;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
        $attachmentPath = 'uploads/proposals/' . $safe;
    }
}

// decide thumbnail path: if user opted and uploaded image, use it; otherwise use default site image
$thumbnailPath = null;
$fullAttachmentPath = $attachmentPath ? (__DIR__ . '/' . $attachmentPath) : null;
if ($useAsThumb === '1' && $fullAttachmentPath && file_exists($fullAttachmentPath)) {
    // quick image check
    $isImage = @getimagesize($fullAttachmentPath) !== false;
    if ($isImage) {
        $thumbnailPath = $attachmentPath;
    } else {
        // not an image, fallback to default
        $thumbnailPath = 'assets/image1.png';
    }
} else {
    // no upload or user declined: use default image
    $thumbnailPath = 'assets/image1.png';
}

// Create master table if not exists
$createMaster = "CREATE TABLE IF NOT EXISTS project_proposals (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($createMaster);

// Create per-type routing tables
$mysqli->query("CREATE TABLE IF NOT EXISTS proposals_clup LIKE project_proposals;");
$mysqli->query("CREATE TABLE IF NOT EXISTS proposals_cdp LIKE project_proposals;");
$mysqli->query("CREATE TABLE IF NOT EXISTS proposals_aip LIKE project_proposals;");

// Insert into master (include preserved request fields)
// (keep the computed $thumbnailPath above — do not overwrite it)
$stmt = $mysqli->prepare('INSERT INTO project_proposals (title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail) VALUES (?,?,?,?,?,?,?,?,?,?)');
if (! $stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $mysqli->error]);
    exit;
}
$budgetVal = ($budget === '' ? 0.0 : floatval($budget));
$stmt->bind_param('ssssssdsss', $title, $projectType, $barangay, $requestType, $urgency, $location, $budgetVal, $description, $attachmentPath, $thumbnailPath);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
    exit;
}
$newId = $stmt->insert_id;
$stmt->close();

// Route to specific table
$lower = strtoupper($projectType);
try {
    if ($lower === 'CLUP') {
        $ins = $mysqli->prepare('INSERT INTO proposals_clup (title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail) VALUES (?,?,?,?,?,?,?,?,?,?)');
    } elseif ($lower === 'CDP') {
        $ins = $mysqli->prepare('INSERT INTO proposals_cdp (title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail) VALUES (?,?,?,?,?,?,?,?,?,?)');
    } elseif ($lower === 'AIP') {
        $ins = $mysqli->prepare('INSERT INTO proposals_aip (title, project_type, barangay, request_type, urgency, location, budget, description, attachment, thumbnail) VALUES (?,?,?,?,?,?,?,?,?,?)');
    } else {
        $ins = null;
    }
    if ($ins) {
        $ins->bind_param('ssssssdsss', $title, $projectType, $barangay, $requestType, $urgency, $location, $budgetVal, $description, $attachmentPath, $thumbnailPath);
        @$ins->execute();
        $ins->close();
    }
} catch (Exception $e) {
    // ignore per-type insert failures
}

// Create notification entry to notify OPMDC Staff
// Also set the proposal status to 'For Review' so it appears in staff workflows
try {
    // determine a simple 'meets_condition' flag for notification routing
    $meetsCondition = false;
    if (is_numeric($budgetVal) && floatval($budgetVal) > 0 && floatval($budgetVal) <= 500000) {
        $meetsCondition = true;
    } elseif (strtoupper(trim($projectType)) === 'AIP') {
        $meetsCondition = true;
    }

    $notifTitle = 'New Project Proposal';
    $notifBody = sprintf('%s proposal submitted by %s (Type: %s)', $title, $barangay, $projectType);
    // ensure proposal_history table exists and insert initial history row
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

    // mark proposal status so staff UI picks it up under review
    try {
        $u = $mysqli->prepare('UPDATE project_proposals SET status = ? WHERE id = ?');
        $reviewStatus = 'For Review';
        if ($u) { $u->bind_param('si', $reviewStatus, $newId); @ $u->execute(); $u->close(); }
    } catch (Exception $e) { /* ignore non-fatal */ }

    try {
        $initHist = $mysqli->prepare('INSERT INTO proposal_history (proposal_id, status, remarks, user_id, user_role) VALUES (?,?,?,?,?)');
        $sessUserId = session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : null;
        $sessUserRole = session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'Barangay Official';
        $initialStatus = 'For Review';
        $initHist->bind_param('issis', $newId, $initialStatus, $description, $sessUserId, $sessUserRole);
        @$initHist->execute();
        $initHist->close();
    } catch (Exception $e) { /* non-fatal */ }

    if ($n = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_role, created_by_role, created_by) VALUES (?, ?, ?, ?, ?, ?)')) {
        $requestId = $newId;
        // Always notify staff
        $targetRole = 'OPMDC Staff';
        $createdByRole = session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'Barangay Official';
        $createdBy = session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
        // types: s (title), s (body), i (request_id), s (target_role), s (created_by_role), i (created_by)
        $types = 'ssissi';
        $n->bind_param($types, $notifTitle, $notifBody, $requestId, $targetRole, $createdByRole, $createdBy);
        @$n->execute();
        $n->close();
        // If the proposal meets the configured condition, also notify the Head
        if ($meetsCondition) {
            if ($nh = $mysqli->prepare('INSERT INTO notifications (title, body, request_id, target_role, created_by_role, created_by) VALUES (?, ?, ?, ?, ?, ?)')) {
                $targetRoleHead = 'OPMDC Head';
                $nh->bind_param($types, $notifTitle, $notifBody, $requestId, $targetRoleHead, $createdByRole, $createdBy);
                @$nh->execute();
                $nh->close();
            }
        }
    }
} catch (Exception $e) {
    // ignore
}

// Assign to default container 'For Review' so it appears in staff dashboard containers
try {
    $mysqli->query("CREATE TABLE IF NOT EXISTS proposal_containers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(128) NOT NULL UNIQUE,
        description TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $mysqli->query("CREATE TABLE IF NOT EXISTS proposal_container_map (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        proposal_id BIGINT UNSIGNED NOT NULL,
        container_id INT NOT NULL,
        assigned_by BIGINT UNSIGNED DEFAULT NULL,
        assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // ensure default container exists
    $cstmt = $mysqli->prepare('SELECT id FROM proposal_containers WHERE name = ? LIMIT 1');
    $cname = 'For Review';
    if ($cstmt) {
        $cstmt->bind_param('s', $cname);
        $cstmt->execute();
        $cres = $cstmt->get_result();
        $containerId = null;
        if ($row = $cres->fetch_assoc()) {
            $containerId = intval($row['id']);
        }
        $cstmt->close();
        if (!$containerId) {
            $insc = $mysqli->prepare('INSERT INTO proposal_containers (name, description) VALUES (?, ?)');
            $desc = 'Default container for newly submitted proposals, pending staff review.';
            if ($insc) { $insc->bind_param('ss', $cname, $desc); @$insc->execute(); $containerId = $insc->insert_id; $insc->close(); }
        }

        if ($containerId) {
            $map = $mysqli->prepare('INSERT INTO proposal_container_map (proposal_id, container_id, assigned_by) VALUES (?,?,?)');
            $assignedBy = session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : null;
            if ($map) { $map->bind_param('iii', $newId, $containerId, $assignedBy); @ $map->execute(); $map->close(); }
        }
    }
} catch (Exception $e) { /* non-fatal */ }

// Insert an initial analytics row for the new proposal (basic placeholder)
try {
    $mysqli->query("CREATE TABLE IF NOT EXISTS proposal_analytics (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        proposal_id BIGINT UNSIGNED NOT NULL,
        metric_key VARCHAR(128) NOT NULL,
        metric_value JSON DEFAULT NULL,
        note TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pstmt = $mysqli->prepare('INSERT INTO proposal_analytics (proposal_id, metric_key, metric_value, note) VALUES (?,?,?,?)');
    if ($pstmt) {
        $metricKey = 'submitted';
        $metricVal = json_encode(['by' => $barangay, 'project_type' => $projectType]);
        $note = 'Initial submission event';
        $pstmt->bind_param('isss', $newId, $metricKey, $metricVal, $note);
        @$pstmt->execute();
        $pstmt->close();
    }
} catch (Exception $e) { /* ignore */ }

// determine a simple 'meets_condition' flag for client-side alerts
// NOTE: assumption: a proposal "meets condition" when budget is set and less than or equal to 500,000 PHP
// or when project type is AIP. Adjust this rule to your real business logic as needed.
$meetsCondition = false;
if (is_numeric($budgetVal) && floatval($budgetVal) > 0 && floatval($budgetVal) <= 500000) {
    $meetsCondition = true;
} elseif (strtoupper(trim($projectType)) === 'AIP') {
    $meetsCondition = true;
}

echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Proposal submitted', 'meets_condition' => $meetsCondition]);
exit;
?>

<?php
// submit_request.php - accepts form submissions from barangay dashboard
header('Content-Type: application/json; charset=utf-8');
// allow CORS for local testing (adjust in production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// include DB connection
$mysqli = require __DIR__ . '/db.php';

// Prepare inputs
$barangay = $mysqli->real_escape_string($_POST['barangay'] ?? '');
$requestType = $mysqli->real_escape_string($_POST['requestType'] ?? '');
$urgency = $mysqli->real_escape_string($_POST['urgency'] ?? 'Medium');
$location = $mysqli->real_escape_string($_POST['location'] ?? '');
$description = $mysqli->real_escape_string($_POST['description'] ?? '');
$email = $mysqli->real_escape_string($_POST['email'] ?? '');
$notes = $mysqli->real_escape_string($_POST['notes'] ?? '');

// Ensure attachment filename is a string (empty if none) to keep bind_param safe
$attachmentFilename = '';
if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $original = basename($_FILES['attachment']['name']);
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $safe = bin2hex(random_bytes(8)) . ($ext ? '.' . $ext : '');
    $target = $uploadDir . '/' . $safe;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
        $attachmentFilename = $mysqli->real_escape_string('uploads/' . $safe);
    }
}

$history = json_encode([['status' => 'Pending', 'timestamp' => date('c'), 'notes' => 'Submitted by barangay official']]);

$stmt = $mysqli->prepare("INSERT INTO requests (barangay, request_type, urgency, location, description, email, notes, attachment, history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (! $stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed', 'details' => $mysqli->error]);
    exit;
}
$stmt->bind_param('sssssssss', $barangay, $requestType, $urgency, $location, $description, $email, $notes, $attachmentFilename, $history);
if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed', 'details' => $stmt->error]);
    exit;
}

$newId = $stmt->insert_id;
$stmt->close();

// --- Best-effort: create notifications for staff and head so connected clients get informed ---
try {
    $notifTitle = 'New Request Submitted';
    $notifBody = "{$requestType} submitted by {$barangay} ({$email})";

    // prepare insert (if notifications table exists)
    if ($ins = $mysqli->prepare("INSERT INTO notifications (title, body, target_role, created_by_role, created_by) VALUES (?, ?, ?, ?, ?)") ) {
        $createdByRole = 'Barangay Official';
        $createdById = 0; // unknown/anonymous in this context

        // Insert for OPMDC Staff
        $targetRole = 'OPMDC Staff';
        $ins->bind_param('ssssi', $notifTitle, $notifBody, $targetRole, $createdByRole, $createdById);
        @$ins->execute();

        // Insert for OPMDC Head
        $targetRole = 'OPMDC Head';
        $ins->bind_param('ssssi', $notifTitle, $notifBody, $targetRole, $createdByRole, $createdById);
        @$ins->execute();

        $ins->close();
    }
} catch (Exception $e) {
    // ignore notification failures - response should still succeed
}

echo json_encode(['id' => $newId, 'status' => 'Pending']);
exit;
?>

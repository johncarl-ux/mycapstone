<?php
// update_account_status.php (open access per requirements)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// no session authorization required

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$newStatus = $_POST['status'] ?? '';

if ($userId <= 0 || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$allowed = ['pending','approved','active','disabled','declined'];
if (!in_array($newStatus, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';
$checkStmt = $mysqli->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$checkStmt->bind_param('i', $userId);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    $checkStmt->close();
    $mysqli->close();
    exit;
}
$checkStmt->bind_result($existingRole);
$checkStmt->fetch();
$checkStmt->close();

if ($existingRole !== 'Barangay Official') {
    echo json_encode(['success' => false, 'message' => 'Target user is not a Barangay Official']);
    $mysqli->close();
    exit;
}

if ($stmt = $mysqli->prepare("UPDATE users SET status = ? WHERE id = ? LIMIT 1")) {
    $stmt->bind_param('si', $newStatus, $userId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
$mysqli->close();
exit;

?>

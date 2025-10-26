<?php
// delete_account.php (open access per requirements)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// no session authorization required

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user id']);
    exit;
}

 $mysqli = require __DIR__ . '/db.php';
 // ensure target is Barangay Official
 $check = $mysqli->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
 $check->bind_param('i', $userId);
 $check->execute();
 $check->store_result();
 if ($check->num_rows === 0) {
     echo json_encode(['success' => false, 'message' => 'User not found']);
     $check->close();
     $mysqli->close();
     exit;
 }
 $check->bind_result($role);
 $check->fetch();
 $check->close();
 if ($role !== 'Barangay Official') {
     echo json_encode(['success' => false, 'message' => 'Target user is not a Barangay Official']);
     $mysqli->close();
     exit;
 }
 if ($stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? LIMIT 1")) {
     $stmt->bind_param('i', $userId);
     if ($stmt->execute()) {
         echo json_encode(['success' => true]);
     } else {
         echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
     }
     $stmt->close();
 } else {
     echo json_encode(['success' => false, 'message' => 'Server error']);
 }
 $mysqli->close();
exit;

?>

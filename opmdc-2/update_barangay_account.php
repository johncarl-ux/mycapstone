<?php
// update_barangay_account.php - Edit Barangay Official accounts (open access)
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$barangayName = trim($_POST['barangayName'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($userId <= 0 || $name === '' || $email === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

// Ensure the user exists and is a Barangay Official
$chk = $mysqli->prepare('SELECT id, role FROM users WHERE id = ? LIMIT 1');
$chk->bind_param('i', $userId);
$chk->execute();
$res = $chk->get_result();
$row = $res->fetch_assoc();
$chk->close();
if (!$row) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    $mysqli->close();
    exit;
}
if ($row['role'] !== 'Barangay Official') {
    echo json_encode(['success' => false, 'message' => 'Target user is not a Barangay Official']);
    $mysqli->close();
    exit;
}

// Ensure email unique (excluding current user)
$uniq = $mysqli->prepare('SELECT id FROM users WHERE (email = ? OR username = ?) AND id <> ? LIMIT 1');
$uniq->bind_param('ssi', $email, $email, $userId);
$uniq->execute();
$uniq->store_result();
if ($uniq->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email/username already in use']);
    $uniq->close();
    $mysqli->close();
    exit;
}
$uniq->close();

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('UPDATE users SET name = ?, email = ?, username = ?, barangayName = ?, password = ? WHERE id = ? LIMIT 1');
    $stmt->bind_param('sssssi', $name, $email, $email, $barangayName, $hash, $userId);
} else {
    $stmt = $mysqli->prepare('UPDATE users SET name = ?, email = ?, username = ?, barangayName = ? WHERE id = ? LIMIT 1');
    $stmt->bind_param('ssssi', $name, $email, $email, $barangayName, $userId);
}

if ($stmt && $stmt->execute()) {
    $stmt->close();
    // Return updated record
    $g = $mysqli->prepare("SELECT id, username, email, name AS representative, barangayName, role, status, created_at FROM users WHERE id = ? LIMIT 1");
    $g->bind_param('i', $userId);
    $g->execute();
    $gr = $g->get_result();
    $user = $gr->fetch_assoc();
    $g->close();
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update account']);
}

$mysqli->close();
exit;
?>
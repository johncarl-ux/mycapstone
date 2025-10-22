<?php
// staff_create_account.php
// Only OPMDC Staff or Head can create accounts for others
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['OPMDC Staff','OPMDC Head','Admin'])) {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$name = trim($_POST['name'] ?? '');
$role = $_POST['role'] ?? 'Barangay Official';
$barangayName = trim($_POST['barangayName'] ?? '') ?: null;

if (!$username || !$email || !$password || !$name) {
    echo json_encode(['success'=>false,'message'=>'All fields required']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success'=>false,'message'=>'Invalid email']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success'=>false,'message'=>'Password too short']);
    exit;
}

// Check for existing username/email
$stmt = $mysqli->prepare('SELECT id FROM users WHERE username=? OR email=? LIMIT 1');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'Username or email already exists']);
    exit;
}
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$status = 'approved'; // Staff-created accounts are auto-approved
$stmt = $mysqli->prepare('INSERT INTO users (username,email,password,name,role,barangayName,status) VALUES (?,?,?,?,?,?,?)');
$stmt->bind_param('sssssss', $username, $email, $hash, $name, $role, $barangayName, $status);
if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'Failed to create account']);
}
$stmt->close();
$mysqli->close();

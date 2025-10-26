<?php
// Open access: allow edit without session auth (per requirement)
// session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'OPMDC Staff';
$barangayName = trim($_POST['barangayName'] ?? '') ?: null;
$status = $_POST['status'] ?? 'pending';

if ($userId <= 0 || $name === '' || $email === '') {
    if (session_status() === PHP_SESSION_ACTIVE) $_SESSION['admin_message'] = 'Invalid input.';
    header('Location: admin.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if (session_status() === PHP_SESSION_ACTIVE) $_SESSION['admin_message'] = 'Invalid email.';
    header('Location: admin.php');
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$sql = "UPDATE users SET name = ?, email = ?, role = ?, barangayName = ?, status = ? WHERE id = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('sssssi', $name, $email, $role, $barangayName, $status, $userId);
    if ($stmt->execute()) {
        if (session_status() === PHP_SESSION_ACTIVE) $_SESSION['admin_message'] = 'User updated.';
    } else {
        if (session_status() === PHP_SESSION_ACTIVE) $_SESSION['admin_message'] = 'Failed to update user.';
    }
    $stmt->close();
} else {
    if (session_status() === PHP_SESSION_ACTIVE) $_SESSION['admin_message'] = 'Server error.';
}

$mysqli->close();
header('Location: admin.php');
exit;

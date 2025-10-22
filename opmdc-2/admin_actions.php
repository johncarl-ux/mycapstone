<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'] ?? '', ['OPMDC Head','Admin'], true)) {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

$action = $_POST['action'] ?? '';
$userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($userId <= 0) {
    $_SESSION['admin_message'] = 'Invalid user id.';
    header('Location: admin.php');
    exit;
}

$mysqli = require __DIR__ . '/db.php';

switch ($action) {
    case 'approve':
        $sql = "UPDATE users SET status = 'approved' WHERE id = ? LIMIT 1";
        break;
    case 'activate':
        $sql = "UPDATE users SET status = 'active' WHERE id = ? LIMIT 1";
        break;
    case 'disable':
        $sql = "UPDATE users SET status = 'disabled' WHERE id = ? LIMIT 1";
        break;
    case 'delete':
        $sql = "DELETE FROM users WHERE id = ? LIMIT 1";
        break;
    default:
        $_SESSION['admin_message'] = 'Unknown action.';
        header('Location: admin.php');
        exit;
}

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $userId);
    if ($stmt->execute()) {
        $_SESSION['admin_message'] = 'Action completed.';
    } else {
        $_SESSION['admin_message'] = 'Failed to perform action.';
    }
    $stmt->close();
} else {
    $_SESSION['admin_message'] = 'Server error.';
}

$mysqli->close();
header('Location: admin.php');
exit;

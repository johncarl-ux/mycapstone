<?php
// Minimal endpoint: verify account exists in DB and return role/name
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$credential = isset($_POST['credential']) ? trim($_POST['credential']) : '';
if ($credential === '') {
    echo json_encode(['success' => false, 'message' => 'Missing credential']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$sql = 'SELECT id, username, email, name, role, barangayName, status FROM users WHERE username = ? OR email = ? LIMIT 1';
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('ss', $credential, $credential);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Account not found']);
    exit;
}

// Only allow if approved/active
$status = strtolower((string)($user['status'] ?? ''));
if (!in_array($status, ['approved', 'active'], true)) {
    echo json_encode(['success' => false, 'message' => 'Account not approved']);
    exit;
}

$out = [
    'id' => (int)$user['id'],
    'username' => $user['username'],
    'email' => $user['email'],
    'name' => $user['name'],
    'role' => $user['role'],
    'barangayName' => $user['barangayName']
];

echo json_encode(['success' => true, 'user' => $out]);
exit;

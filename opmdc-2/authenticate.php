<?php
// authenticate.php - Verify user credentials against the database and return role/name
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$credential = trim($_POST['credential'] ?? '');
$password = $_POST['password'] ?? '';
if ($credential === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Missing credential or password']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

// Try to find user by username or email
$stmt = $mysqli->prepare('SELECT id, username, email, password, name, role, barangayName, status FROM users WHERE username = ? OR email = ? LIMIT 1');
if (! $stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
$stmt->bind_param('ss', $credential, $credential);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// If user not found and the credential matches the special Admin bootstrap, create it securely in DB
if (!$user && strcasecmp($credential, 'adminopmdc@gmail.com') === 0 && $password === '12345678') {
    $uname = 'admin';
    $email = 'adminopmdc@gmail.com';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Admin';
    $role = 'Admin';
    $status = 'active';
    $barangayName = null;
    if ($ins = $mysqli->prepare('INSERT INTO users (username,email,password,name,role,barangayName,status) VALUES (?,?,?,?,?,?,?)')) {
        $ins->bind_param('sssssss', $uname, $email, $hash, $name, $role, $barangayName, $status);
        if ($ins->execute()) {
            $newId = $ins->insert_id;
            $ins->close();
            // refetch as canonical user row
            $g = $mysqli->prepare('SELECT id, username, email, password, name, role, barangayName, status FROM users WHERE id = ? LIMIT 1');
            $g->bind_param('i', $newId);
            $g->execute();
            $user = $g->get_result()->fetch_assoc();
            $g->close();
        } else {
            $ins->close();
        }
    }
}

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

// Only allow approved/active accounts
$status = strtolower((string)($user['status'] ?? ''));
if (!in_array($status, ['approved', 'active'], true)) {
    echo json_encode(['success' => false, 'message' => 'Account not approved']);
    exit;
}

// Verify password
if (!password_verify($password, (string)$user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    exit;
}

// Success
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
?>
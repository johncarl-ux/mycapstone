<?php
// authenticate.php - Verify user credentials against the database and return role/name
// Also establishes a PHP session so server-side endpoints (e.g., notifications) work.
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

$mysqli = require dirname(__DIR__) . '/db.php';

// Try to find user by username or email
// Prefer index-friendly lookups (try username first, then email)
$user = null;
$stmt = $mysqli->prepare('SELECT id, username, email, password, name, role, barangayName, status FROM users WHERE username = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('s', $credential);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
    }
    $stmt->close();
}
if (!$user) {
    $stmt2 = $mysqli->prepare('SELECT id, username, email, password, name, role, barangayName, status FROM users WHERE email = ? LIMIT 1');
    if ($stmt2) {
        $stmt2->bind_param('s', $credential);
        if ($stmt2->execute()) {
            $res2 = $stmt2->get_result();
            $user = $res2->fetch_assoc();
        }
        $stmt2->close();
    }
}

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
// Start session only after successful verification to avoid session file lock during auth
session_start();
// Strengthen session: refresh ID and store minimal user profile
session_regenerate_id(true);
// Store both a compact user array and legacy/session keys used by other endpoints
$_SESSION['user'] = $out;
// Backwards-compatibility: set individual session keys expected by existing APIs
$_SESSION['user_id'] = $out['id'];
$_SESSION['role'] = $out['role'];
$_SESSION['name'] = $out['name'] ?? '';

echo json_encode(['success' => true, 'user' => $out]);
exit;
?>

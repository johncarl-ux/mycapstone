<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$credential = isset($_POST['credential']) ? trim($_POST['credential']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($credential === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

// Special-case: allow fixed admin credential to sign in as Admin without DB lookup
if ($credential === 'adminopmdc@gmail.com' && $password === '12345678') {
    $_SESSION['user'] = [
        'id' => 0,
        'username' => 'adminopmdc',
        'email' => 'adminopmdc@gmail.com',
        'name' => 'Admin',
        'role' => 'Admin',
        'barangayName' => null
    ];
    echo json_encode(['success' => true, 'role' => 'Admin']);
    exit;
}

$mysqli = require __DIR__ . '/db.php';

$sql = "SELECT id, username, email, password, name, role, barangayName, status FROM users WHERE username = ? OR email = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('ss', $credential, $credential);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    error_log('Prepare failed: ' . $mysqli->error);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

if (!in_array($user['status'], ['approved','active'])) {
    echo json_encode(['success' => false, 'message' => 'Account not approved']);
    exit;
}

if (password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'name' => $user['name'],
        'role' => $user['role'],
        'barangayName' => $user['barangayName']
    ];

    echo json_encode(['success' => true, 'role' => $user['role']]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

?>

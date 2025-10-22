<?php
// me.php - return current session user info as JSON
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$u = $_SESSION['user'];

// If session is missing barangayName (or other fields), try to load fresh data from DB
$userOut = [
    'id' => $u['id'] ?? null,
    'username' => $u['username'] ?? ($u['email'] ?? null),
    'email' => $u['email'] ?? null,
    'name' => $u['name'] ?? null,
    'role' => $u['role'] ?? null,
    'barangayName' => $u['barangayName'] ?? ($u['barangay'] ?? null),
];

if (empty($userOut['barangayName']) && !empty($userOut['id']) && is_numeric($userOut['id']) && $userOut['id'] > 0) {
    // attempt DB lookup for authoritative record
    $mysqli = require __DIR__ . '/db.php';
    if ($stmt = $mysqli->prepare('SELECT username,email,name,role,barangayName FROM users WHERE id = ? LIMIT 1')) {
        $stmt->bind_param('i', $userOut['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $userOut['username'] = $row['username'] ?? $userOut['username'];
            $userOut['email'] = $row['email'] ?? $userOut['email'];
            $userOut['name'] = $row['name'] ?? $userOut['name'];
            $userOut['role'] = $row['role'] ?? $userOut['role'];
            $userOut['barangayName'] = $row['barangayName'] ?? $userOut['barangayName'];
        }
        $stmt->close();
    }
    $mysqli->close();
}

$payload = ['success' => true, 'user' => $userOut];
echo json_encode($payload);
exit;

?>

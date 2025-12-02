<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

echo json_encode([
    'session_exists' => isset($_SESSION['user']),
    'session_data' => $_SESSION ?? [],
    'session_id' => session_id()
]);
?>

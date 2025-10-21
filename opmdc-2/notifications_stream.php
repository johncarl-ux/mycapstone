<?php
// notifications_stream.php
// SSE endpoint: stream new notifications for the authenticated user/role.
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo "Authentication required\n";
    exit;
}

// Ensure script can run for a long time
set_time_limit(0);
// Headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

try {
    $mysqli = require __DIR__ . '/db.php';
} catch (Exception $e) {
    // Send an error event and exit
    echo "event: error\n";
    echo "data: {\"error\": \"DB connection failed\"}\n\n";
    flush();
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'] ?? null;
$userId = isset($user['id']) ? intval($user['id']) : null;

// Keep track of last seen notification id to avoid resending
$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

// Send a comment to establish connection
echo ": connected\n\n";
flush();

// Main loop: poll DB every 2 seconds for new notifications
while (true) {
    // Build query: notifications targeted to role OR user id, and id > lastId
    $sql = "SELECT id, title, body, target_role, target_user_id, created_by, created_by_role, is_read, created_at FROM notifications WHERE id > ? AND (";
    $params = [$lastId];
    $conds = [];
    if ($role) { $conds[] = "target_role = ?"; $params[] = $role; }
    if ($userId) { $conds[] = "target_user_id = ?"; $params[] = $userId; }
    if (count($conds) === 0) {
        // nothing to listen for for this user
        // sleep and continue
        sleep(2);
        continue;
    }
    $sql .= implode(' OR ', $conds) . ') ORDER BY id ASC LIMIT 50';

    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) {
        echo "event: error\n";
        echo "data: {\"error\": \"prepare failed\"}\n\n";
        flush();
        break;
    }

    // bind types
    $types = '';
    for ($i = 0; $i < count($params); $i++) {
        $types .= is_int($params[$i]) ? 'i' : 's';
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $sentAny = false;
    while ($row = $res->fetch_assoc()) {
        $lastId = max($lastId, intval($row['id']));
        $payload = json_encode($row);
        // send SSE event 'notification'
        echo "event: notification\n";
        // data must be sent as lines prefixed with 'data:' and end with a blank line
        foreach (explode("\n", $payload) as $line) {
            echo "data: $line\n";
        }
        echo "\n";
        flush();
        $sentAny = true;
    }

    // If nothing new, send a ping comment to keep the connection alive every 15s
    if (!$sentAny) {
        echo ": ping\n\n";
        flush();
    }

    // Sleep for a short while before polling again
    // Use smaller sleep if we just sent something to reduce latency
    usleep(500000); // 0.5s

    // Abort if client disconnected
    if (connection_aborted()) break;
}

exit;

?>

<?php
/**
 * save_recommendation.php
 * Create or update a plan recommendation (clean single-file implementation).
 */
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Allow CLI tests (script sets $_POST) or enforce POST for web
if (php_sapi_name() !== 'cli' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Role check
$role = $_SESSION['role'] ?? null;
if (!in_array($role, ['admin', 'staff'], true)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden: insufficient permissions']);
    exit;
}

// Inputs
$id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
$details = isset($_POST['details']) ? trim($_POST['details']) : '';
$relevance = isset($_POST['relevance']) && $_POST['relevance'] !== '' ? floatval($_POST['relevance']) : 0.0;
$source = isset($_POST['source']) ? trim($_POST['source']) : '';

if ($category === '' || $title === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields: category and title are required']);
    exit;
}

// DB
$dbPath = dirname(__DIR__) . '/db.php';
if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database config missing']);
    exit;
}
/** @noinspection PhpIncludeInspection */
$mysqli = include $dbPath;
if (!($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB init failed']);
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE plan_recommendations SET category = ?, title = ?, summary = ?, details = ?, relevance = ?, source = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? LIMIT 1";
        $stmt = $mysqli->prepare($sql);
        if (! $stmt) throw new RuntimeException('Prepare failed: ' . $mysqli->error);

        $stmt->bind_param('ssssdsi', $category, $title, $summary, $details, $relevance, $source, $id);
        if (! $stmt->execute()) throw new RuntimeException('Execute failed: ' . $stmt->error);

        if ($stmt->affected_rows === 0) {
            $stmt->close();
            $check = $mysqli->prepare('SELECT id FROM plan_recommendations WHERE id = ? LIMIT 1');
            $check->bind_param('i', $id);
            $check->execute();
            $res = $check->get_result();
            if ($res && $res->fetch_assoc()) {
                echo json_encode(['success' => true, 'id' => $id, 'note' => 'No changes']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Not found']);
            }
            $check->close();
            exit;
        }

        $stmt->close();
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }

    // Insert
    $sql = 'INSERT INTO plan_recommendations (category, title, summary, details, relevance, source) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $mysqli->prepare($sql);
    if (! $stmt) throw new RuntimeException('Prepare failed: ' . $mysqli->error);

    $stmt->bind_param('ssssds', $category, $title, $summary, $details, $relevance, $source);
    if (! $stmt->execute()) throw new RuntimeException('Insert execute failed: ' . $stmt->error);

    $newId = $mysqli->insert_id;
    $stmt->close();
    echo json_encode(['success' => true, 'id' => $newId]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error', 'details' => $e->getMessage()]);
    exit;
} finally {
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        @$mysqli->close();
    }
}

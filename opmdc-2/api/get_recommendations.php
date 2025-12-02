<?php
header('Content-Type: application/json; charset=utf-8');
// Simple API to return plan recommendations for a given category
// GET param: category (required)

// Allow optional category or search query. If neither provided, return top recommendations.
$category = isset($_GET['category']) && trim($_GET['category']) !== '' ? trim($_GET['category']) : null;
$q = isset($_GET['q']) && trim($_GET['q']) !== '' ? trim($_GET['q']) : null;

// include DB connection helper (returns $mysqli)
$dbPath = dirname(__DIR__) . '/db.php';
if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database configuration not found (db.php missing)']);
    exit;
}
/** @noinspection PhpIncludeInspection */
$mysqli = include $dbPath;
if (!($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to initialize database connection']);
    exit;
}

try {
    // Build SQL dynamically based on presence of category or query
    $rows = [];
    if ($category) {
        $sql = "SELECT id, category, title, summary, details, relevance FROM plan_recommendations WHERE category = ? ORDER BY relevance DESC, id ASC LIMIT 200";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('s', $category);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $rows[] = [
                    'id' => (int)$r['id'],
                    'category' => $r['category'],
                    'title' => $r['title'],
                    'summary' => $r['summary'],
                    'details' => $r['details'],
                    'relevance' => isset($r['relevance']) ? (float)$r['relevance'] : null,
                ];
            }
            $stmt->close();
            echo json_encode(['success' => true, 'category' => $category, 'recommendations' => $rows]);
            return;
        }

    } elseif ($q) {
        // free-text search across title/summary/details
        $like = '%' . $q . '%';
        $sql = "SELECT id, category, title, summary, details, relevance FROM plan_recommendations WHERE title LIKE ? OR summary LIKE ? OR details LIKE ? ORDER BY relevance DESC, id ASC LIMIT 200";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('sss', $like, $like, $like);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) {
                $rows[] = [
                    'id' => (int)$r['id'],
                    'category' => $r['category'],
                    'title' => $r['title'],
                    'summary' => $r['summary'],
                    'details' => $r['details'],
                    'relevance' => isset($r['relevance']) ? (float)$r['relevance'] : null,
                ];
            }
            $stmt->close();
            echo json_encode(['success' => true, 'query' => $q, 'recommendations' => $rows]);
            return;
        }

    } else {
        // Return top recommendations when no filters provided
        $sql = "SELECT id, category, title, summary, details, relevance FROM plan_recommendations ORDER BY relevance DESC, id ASC LIMIT 200";
        if ($res = $mysqli->query($sql)) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = [
                    'id' => (int)$r['id'],
                    'category' => $r['category'],
                    'title' => $r['title'],
                    'summary' => $r['summary'],
                    'details' => $r['details'],
                    'relevance' => isset($r['relevance']) ? (float)$r['relevance'] : null,
                ];
            }
            echo json_encode(['success' => true, 'recommendations' => $rows]);
            return;
        }
    }

    // Fallback error
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to prepare or execute statement']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error', 'details' => $e->getMessage()]);
}

// close connection
@$mysqli->close();

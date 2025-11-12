<?php
// request_status_api.php - returns JSON for a single request or all requests for a barangay
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
/** @var mysqli $mysqli */
$mysqli = require __DIR__ . '/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$barangay = isset($_GET['barangay']) ? trim((string)$_GET['barangay']) : '';

$steps = ['Submitted', 'For Review', 'Approved', 'Completed'];

function normalize_request($row, $steps) {
    $row['history'] = $row['history'] ? json_decode($row['history'], true) : null;
    if (!is_array($row['history'])) {
        $row['history'] = [['status' => $row['status'] ?: 'Pending', 'timestamp' => $row['created_at'] ?: date('c'), 'notes' => 'Submitted']];
    }
    $happened = [];
    foreach ($row['history'] as $h) {
        $s = strtolower(trim((string)($h['status'] ?? $h['notes'] ?? '')));
        if ($s !== '') $happened[] = $s;
    }
    $happened = array_unique($happened);
    $stepStatus = [];
    foreach ($steps as $s) {
        $k = strtolower($s);
        if ($k === 'submitted') $stepStatus[] = true;
        elseif ($k === 'for review') $stepStatus[] = (bool) preg_grep('/pending|for review|processing|submitted/', $happened);
        elseif ($k === 'approved') $stepStatus[] = (bool) preg_grep('/approved|accept|approved by/', $happened) || stripos((string)$row['status'], 'approved') !== false;
        elseif ($k === 'completed') $stepStatus[] = (bool) preg_grep('/completed|allocated|closed|delivered|finished/', $happened);
        else $stepStatus[] = false;
    }
    $row['_stepStatus'] = $stepStatus;
    $lastEvent = end($row['history']);
    $row['_lastTs'] = $lastEvent['timestamp'] ?? $row['created_at'] ?? date('c');
    return $row;
}

try {
    if ($id > 0) {
        $stmt = $mysqli->prepare("SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if (! $row) {
            echo json_encode(['success' => false, 'message' => 'Not found']);
            exit;
        }
        $row = normalize_request($row, $steps);
        echo json_encode(['success' => true, 'request' => $row]);
        exit;
    }

    if ($barangay !== '') {
        $stmt = $mysqli->prepare("SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests WHERE barangay = ? ORDER BY created_at DESC LIMIT 200");
        $stmt->bind_param('s', $barangay);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[] = normalize_request($r, $steps);
        }
        echo json_encode(['success' => true, 'requests' => $out]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Missing id or barangay']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

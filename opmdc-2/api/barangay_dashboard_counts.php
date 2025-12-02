<?php
// barangay_dashboard_counts.php
// Returns per-barangay aggregate counts for dashboard cards.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$mysqli = require dirname(__DIR__) . '/db.php';

$barangay = trim((string)($_GET['barangay'] ?? ''));
if ($barangay === '') {
    echo json_encode(['success' => false, 'error' => 'Missing barangay parameter']);
    exit;
}

function run_count($mysqli, $sql, $types = '', $params = []) {
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) return 0;
    if ($types !== '' && count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }
    if (! $stmt->execute()) return 0;
    $res = $stmt->get_result();
    $row = $res->fetch_row();
    return (int)($row[0] ?? 0);
}

// Requests counts
$total_requests    = run_count($mysqli, 'SELECT COUNT(*) FROM requests WHERE barangay = ?', 's', [$barangay]);
$approved_requests = run_count($mysqli, "SELECT COUNT(*) FROM requests WHERE barangay = ? AND status = 'Approved'", 's', [$barangay]);
$declined_requests = run_count($mysqli, "SELECT COUNT(*) FROM requests WHERE barangay = ? AND status = 'Declined'", 's', [$barangay]);
$pending_requests  = run_count($mysqli, "SELECT COUNT(*) FROM requests WHERE barangay = ? AND status NOT IN ('Approved','Declined')", 's', [$barangay]);

// Proposal counts (add to totals)
$total_proposals    = run_count($mysqli, 'SELECT COUNT(*) FROM project_proposals WHERE barangay = ?', 's', [$barangay]);
$approved_proposals = run_count($mysqli, "SELECT COUNT(*) FROM project_proposals WHERE barangay = ? AND status = 'Approved'", 's', [$barangay]);
$declined_proposals = run_count($mysqli, "SELECT COUNT(*) FROM project_proposals WHERE barangay = ? AND status = 'Declined'", 's', [$barangay]);
$pending_proposals  = run_count($mysqli, "SELECT COUNT(*) FROM project_proposals WHERE barangay = ? AND status NOT IN ('Approved','Declined')", 's', [$barangay]);

$output = [
    'success' => true,
    'barangay' => $barangay,
    'total' => $total_requests + $total_proposals,
    'approved' => $approved_requests + $approved_proposals,
    'pending' => $pending_requests + $pending_proposals,
    'declined' => $declined_requests + $declined_proposals,
    'breakdown' => [
        'requests' => [
            'total' => $total_requests,
            'approved' => $approved_requests,
            'pending' => $pending_requests,
            'declined' => $declined_requests
        ],
        'proposals' => [
            'total' => $total_proposals,
            'approved' => $approved_proposals,
            'pending' => $pending_proposals,
            'declined' => $declined_proposals
        ]
    ]
];

echo json_encode($output);
exit;
?>
<?php
// dashboard_counts.php
// Returns JSON with aggregate counts used by the dashboard.
header('Content-Type: application/json; charset=utf-8');

$mysqli = require __DIR__ . '/db.php';

$phase = trim((string)($_GET['phase'] ?? 'all'));

// Helper: run a count query with optional param
function run_count($mysqli, $sql, $types = '', $params = []) {
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) return null;
    if ($types !== '' && count($params) > 0) {
        $stmt->bind_param($types, ...$params);
    }
    if (! $stmt->execute()) return null;
    $res = $stmt->get_result();
    $row = $res->fetch_row();
    return (int)($row[0] ?? 0);
}

$out = [
    'total_requests' => 0,
    'total_requests_for_phase' => 0,
    'pending_requests' => 0,
    'pending_requests_for_phase' => 0,
    'total_accounts' => 0,
    'pending_accounts' => 0,
];

// Total requests overall
$out['total_requests'] = run_count($mysqli, 'SELECT COUNT(*) FROM requests');

// Total requests for the selected phase
if ($phase === '' || strtolower($phase) === 'all') {
    $out['total_requests_for_phase'] = $out['total_requests'];
} else {
    $out['total_requests_for_phase'] = run_count($mysqli, 'SELECT COUNT(*) FROM requests WHERE status = ?', 's', [$phase]);
}

// Pending overall: any status that is not final (Approved or Declined)
$out['pending_requests'] = run_count($mysqli, "SELECT COUNT(*) FROM requests WHERE status NOT IN ('Approved','Declined')");

// Pending for phase: if phase is a non-final status, count of that status; otherwise 0
if ($phase === '' || strtolower($phase) === 'all') {
    $out['pending_requests_for_phase'] = $out['pending_requests'];
} else {
    // If phase is one of final statuses, pending for phase is 0, otherwise count matching status
    $finals = ['Approved','Declined'];
    if (in_array($phase, $finals, true)) {
        $out['pending_requests_for_phase'] = 0;
    } else {
        $out['pending_requests_for_phase'] = run_count($mysqli, 'SELECT COUNT(*) FROM requests WHERE status = ? AND status NOT IN ("Approved","Declined")', 's', [$phase]);
    }
}

// Accounts: count users with role 'Barangay Official' (registered barangay accounts)
$out['total_accounts'] = run_count($mysqli, "SELECT COUNT(*) FROM users WHERE role = 'Barangay Official'");
$out['pending_accounts'] = run_count($mysqli, "SELECT COUNT(*) FROM users WHERE role = 'Barangay Official' AND status = 'pending'");

// For backwards compatibility with client code expecting total and pending counts for dashboard cards,
// return top-level keys total_requests and pending_requests mapped to the phase-filtered values when a phase filter is applied.
if ($phase === '' || strtolower($phase) === 'all') {
    echo json_encode([
        'total_requests' => $out['total_requests'],
        'pending_requests' => $out['pending_requests'],
        'total_accounts' => $out['total_accounts'],
        'pending_accounts' => $out['pending_accounts'],
    ]);
    exit;
} else {
    echo json_encode([
        'total_requests' => $out['total_requests_for_phase'],
        'pending_requests' => $out['pending_requests_for_phase'],
        'total_accounts' => $out['total_accounts'],
        'pending_accounts' => $out['pending_accounts'],
    ]);
    exit;
}

?>

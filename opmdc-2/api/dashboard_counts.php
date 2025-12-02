<?php
// dashboard_counts.php
// Returns JSON with aggregate counts used by the dashboard.
header('Content-Type: application/json; charset=utf-8');

$mysqli = require dirname(__DIR__) . '/db.php';

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

// Aggregate counters (requests + proposals) so dashboard reflects all submissions
$out = [
    'total_requests' => 0,                 // total submissions (requests + proposals)
    'total_requests_for_phase' => 0,       // total for a specific status when filtered
    'pending_requests' => 0,               // submissions not final (across both tables)
    'pending_requests_for_phase' => 0,
    'total_accounts' => 0,
    'pending_accounts' => 0,
];

// Helper closures for requests + proposals
$count_requests_all   = run_count($mysqli, 'SELECT COUNT(*) FROM requests');
$count_proposals_all  = run_count($mysqli, 'SELECT COUNT(*) FROM project_proposals');

$out['total_requests'] = (int)$count_requests_all + (int)$count_proposals_all;

if ($phase === '' || strtolower($phase) === 'all') {
    $out['total_requests_for_phase'] = $out['total_requests'];
} else {
    $req_phase = run_count($mysqli, 'SELECT COUNT(*) FROM requests WHERE status = ?', 's', [$phase]);
    $prop_phase = run_count($mysqli, 'SELECT COUNT(*) FROM project_proposals WHERE status = ?', 's', [$phase]);
    $out['total_requests_for_phase'] = (int)$req_phase + (int)$prop_phase;
}

// Pending (not Approved/Declined) across both tables
$pending_req = run_count($mysqli, "SELECT COUNT(*) FROM requests WHERE status NOT IN ('Approved','Declined')");
$pending_prop = run_count($mysqli, "SELECT COUNT(*) FROM project_proposals WHERE status NOT IN ('Approved','Declined')");
$out['pending_requests'] = (int)$pending_req + (int)$pending_prop;

if ($phase === '' || strtolower($phase) === 'all') {
    $out['pending_requests_for_phase'] = $out['pending_requests'];
} else {
    $finals = ['Approved','Declined'];
    if (in_array($phase, $finals, true)) {
        $out['pending_requests_for_phase'] = 0;
    } else {
        $req_p_phase = run_count($mysqli, 'SELECT COUNT(*) FROM requests WHERE status = ? AND status NOT IN ("Approved","Declined")', 's', [$phase]);
        $prop_p_phase = run_count($mysqli, 'SELECT COUNT(*) FROM project_proposals WHERE status = ? AND status NOT IN ("Approved","Declined")', 's', [$phase]);
        $out['pending_requests_for_phase'] = (int)$req_p_phase + (int)$prop_p_phase;
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

<?php
// list_requests.php - returns JSON list of requests.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$mysqli = require __DIR__ . '/db.php';

$role = $_GET['role'] ?? null; // optional: OPMDC Staff or OPMDC Head or Barangay Official
$barangay = $_GET['barangay'] ?? null; // optional filter

$sql = "SELECT id, request_code, barangay, request_type, urgency, location, description, email, notes, attachment, status, history, created_at FROM requests";
$conds = [];
$params = [];

if ($role === 'Barangay Official' && $barangay) {
    $conds[] = 'barangay = ?';
    $params[] = $barangay;
}

if (count($conds) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $conds);
}

$sql .= ' ORDER BY created_at DESC LIMIT 500';

$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare failed', 'details' => $mysqli->error]);
    exit;
}

if (count($params) > 0) {
    // bind_param with dynamic args; for one param just bind directly to avoid unpacking issues
    if (count($params) === 1) {
        $stmt->bind_param('s', $params[0]);
    } else {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
}

if (! $stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute failed', 'details' => $stmt->error]);
    exit;
}

$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    // try to decode history JSON to a PHP array
    $r['history'] = json_decode($r['history'], true) ?: [];
    $r['kind'] = 'request';
    $rows[] = $r;
}

// Also include project proposals so dashboards (head/staff/barangay) can display submitted proposals
// If a barangay filter is present, only include proposals for that barangay.
try {
    $pSql = 'SELECT id, title, project_type, barangay, request_type, urgency, location, description, attachment, status, created_at FROM project_proposals';
    $pConds = [];
    $pParams = [];
    if ($barangay) {
        $pConds[] = 'barangay = ?';
        $pParams[] = $barangay;
    }
    if (count($pConds) > 0) $pSql .= ' WHERE ' . implode(' AND ', $pConds);
    $pSql .= ' ORDER BY created_at DESC LIMIT 500';

    $pstmt = $mysqli->prepare($pSql);
    if ($pstmt) {
        if (count($pParams) === 1) {
            $pstmt->bind_param('s', $pParams[0]);
        } elseif (count($pParams) > 1) {
            $types = str_repeat('s', count($pParams));
            $pstmt->bind_param($types, ...$pParams);
        }
        if ($pstmt->execute()) {
            $pres = $pstmt->get_result();
            while ($p = $pres->fetch_assoc()) {
                // load proposal history
                $h = [];
                try {
                    $hstmt = $mysqli->prepare('SELECT status, remarks, user_id, user_role, created_at FROM proposal_history WHERE proposal_id = ? ORDER BY created_at ASC');
                    if ($hstmt) {
                        $hstmt->bind_param('i', $p['id']);
                        $hstmt->execute();
                        $hres = $hstmt->get_result();
                        while ($hr = $hres->fetch_assoc()) {
                            $h[] = $hr;
                        }
                        $hstmt->close();
                    }
                } catch (Exception $e) { /* ignore */ }

                $rows[] = [
                    'id' => $p['id'],
                    'request_code' => null,
                    'barangay' => $p['barangay'],
                    'request_type' => $p['title'],
                    'urgency' => $p['urgency'],
                    'location' => $p['location'],
                    'description' => $p['description'],
                    'email' => null,
                    'notes' => null,
                    'attachment' => $p['attachment'],
                    'status' => $p['status'],
                    'history' => $h,
                    'created_at' => $p['created_at'],
                    'kind' => 'proposal',
                ];
            }
        }
        $pstmt->close();
    }
} catch (Exception $e) { /* ignore */ }

echo json_encode(['requests' => $rows]);
exit;
?>

<?php
// analytics_api.php - returns aggregated analytics for dashboards (JSON)
header('Content-Type: application/json; charset=utf-8');
// allow local fetches from dashboard
header('Access-Control-Allow-Origin: *');

try {
    $mysqli = require __DIR__ . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$out = [
    // associative arrays to avoid stdClass/array confusion
    'counts' => [],
    'proposals_timeseries' => ['labels'=>[], 'data'=>[]],
    'requests_by_barangay' => [],
    'requests_by_status' => [],
    'proposals_by_status' => [],
    'approval_funnel' => [],
    'avg_turnaround_series' => ['labels'=>[], 'data'=>[]],
    'barangay_heatmap' => ['barangays'=>[], 'months'=>[], 'matrix'=>[]],
    'insights' => []
];

// Optional date range filters (YYYY-MM-DD)
$start = isset($_GET['start']) ? trim($_GET['start']) : null;
$end = isset($_GET['end']) ? trim($_GET['end']) : null;

function is_valid_ymd($s) {
    if (!$s) return false;
    $d = DateTime::createFromFormat('Y-m-d', $s);
    return $d && $d->format('Y-m-d') === $s;
}

$startValid = is_valid_ymd($start);
$endValid = is_valid_ymd($end);

// Build SQL range conditions (safe because we validate format and escape below)
$rangeConditions = '';
$startSql = '';
$endSql = '';
if ($startValid) { $startSql = $mysqli->real_escape_string($start) . ' 00:00:00'; $rangeConditions .= " AND created_at >= '$startSql'"; }
if ($endValid) { $endSql = $mysqli->real_escape_string($end) . ' 23:59:59'; $rangeConditions .= " AND created_at <= '$endSql'"; }


// 1) Basic counts (apply optional date range)
try {
    // build where base
    $whereBase = ' WHERE 1=1' . $rangeConditions;

    $r = $mysqli->query("SELECT COUNT(*) AS c FROM requests" . $whereBase);
    $row = $r->fetch_assoc(); $out['counts']['total_requests'] = intval($row['c'] ?? 0);
    $r->free();

    $r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals" . $whereBase);
    $row = $r->fetch_assoc(); $out['counts']['total_proposals'] = intval($row['c'] ?? 0);
    $r->free();

    $r = $mysqli->query("SELECT COUNT(*) AS c FROM requests WHERE LOWER(status) LIKE '%pending%'" . ($rangeConditions ? $rangeConditions : ''));
    $row = $r->fetch_assoc(); $out['counts']['pending_requests'] = intval($row['c'] ?? 0);
    $r->free();

    $r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals WHERE LOWER(status) LIKE '%for review%' OR LOWER(status) LIKE '%processing%' OR LOWER(status) LIKE '%pending%'" . ($rangeConditions ? $rangeConditions : ''));
    $row = $r->fetch_assoc(); $out['counts']['pending_proposals'] = intval($row['c'] ?? 0);
    $r->free();
} catch (Exception $e) { /* ignore count errors */ }

// 2) Proposals timeseries (by month within range or last 12 months)
try {
    // determine months range
    if ($startValid || $endValid) {
        $startForMonths = $startValid ? $start : date('Y-m-d', strtotime($end . ' -11 months'));
        $endForMonths = $endValid ? $end : date('Y-m-d');
    } else {
        $startForMonths = date('Y-m-d', strtotime('-11 months'));
        $endForMonths = date('Y-m-d');
    }

    // build SQL using rangeConditions if user provided a range
    if ($startValid || $endValid) {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM project_proposals WHERE 1=1 " . $rangeConditions . " GROUP BY ym ORDER BY ym ASC";
        $res = $mysqli->query($sql);
    } else {
        $res = $mysqli->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM project_proposals WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY ym ORDER BY ym ASC");
    }

    $labels = [];
    $data = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $labels[] = $r['ym'];
            $data[] = intval($r['c']);
        }
        $res->free();
    }

    // build months between startForMonths and endForMonths
    $months = [];
    $cur = new DateTime($startForMonths);
    $cur->modify('first day of this month');
    $endDt = new DateTime($endForMonths);
    $endDt->modify('first day of this month');
    while ($cur <= $endDt) {
        $months[] = $cur->format('Y-m');
        $cur->modify('+1 month');
    }

    $map = array_combine($labels, $data) ?: [];
    $filled = [];
    foreach ($months as $m) { $filled[] = intval($map[$m] ?? 0); }
    $out['proposals_timeseries'] = ['labels' => $months, 'data' => $filled];
} catch (Exception $e) { $out['proposals_timeseries'] = ['labels'=>[], 'data'=>[]]; }

// 3) Requests by barangay (top 10) — apply optional range
try {
    $sql = "SELECT barangay, COUNT(*) AS c FROM requests WHERE 1=1 " . $rangeConditions . " GROUP BY barangay ORDER BY c DESC LIMIT 10";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $out['requests_by_barangay'][] = ['barangay' => $r['barangay'], 'count' => intval($r['c'])];
        }
        $res->free();
    }
} catch (Exception $e) { }

// 4) Status breakdowns
try {
    $sql = "SELECT status, COUNT(*) AS c FROM requests WHERE 1=1 " . $rangeConditions . " GROUP BY status";
    $res = $mysqli->query($sql);
    if ($res) {
        while ($r = $res->fetch_assoc()) { $out['requests_by_status'][$r['status'] ?? 'unknown'] = intval($r['c']); }
        $res->free();
    }

    $sql2 = "SELECT status, COUNT(*) AS c FROM project_proposals WHERE 1=1 " . $rangeConditions . " GROUP BY status";
    $res = $mysqli->query($sql2);
    if ($res) {
        while ($r = $res->fetch_assoc()) { $out['proposals_by_status'][$r['status'] ?? 'unknown'] = intval($r['c']); }
        $res->free();
    }
} catch (Exception $e) { }

// 4b) Approval funnel (project_proposals by status) - map to common buckets
try {
    // reuse proposals_by_status computed above if available
    $funnel = [];
    $possible = ['Approved','Declined','For Review','Processing','Requires Revision','Pending'];
    foreach ($possible as $s) { $funnel[$s] = intval($out['proposals_by_status'][$s] ?? 0); }
    // also include any other statuses present
    foreach ($out['proposals_by_status'] as $s=>$c) { if (!isset($funnel[$s])) $funnel[$s] = intval($c); }
    $out['approval_funnel'] = $funnel;
} catch (Exception $e) { }

// 4c) Average turnaround series (monthly) - average days per month for proposals
try {
    $sqlAvgSeries = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, AVG(TIMESTAMPDIFF(DAY, p.created_at, IFNULL((SELECT MAX(ph.created_at) FROM proposal_history ph WHERE ph.proposal_id = p.id), p.created_at))) AS avg_days FROM project_proposals p WHERE 1=1 " . $rangeConditions . " GROUP BY ym ORDER BY ym ASC";
    $res = $mysqli->query($sqlAvgSeries);
    $avgMap = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) { $avgMap[$r['ym']] = round(floatval($r['avg_days'] ?? 0),1); }
        $res->free();
    }
    // align with proposals_timeseries months computed earlier
    $months = $out['proposals_timeseries']['labels'] ?? [];
    $avgData = [];
    foreach ($months as $m) { $avgData[] = floatval($avgMap[$m] ?? 0); }
    $out['avg_turnaround_series'] = ['labels'=>$months, 'data'=>$avgData];
} catch (Exception $e) { $out['avg_turnaround_series'] = ['labels'=>[], 'data'=>[]]; }

// 4d) Barangay heatmap: top N barangays by count in range, monthly counts matrix
try {
    // determine top barangays within range
    $res = $mysqli->query("SELECT barangay, COUNT(*) AS c FROM requests WHERE 1=1 " . $rangeConditions . " GROUP BY barangay ORDER BY c DESC LIMIT 10");
    $bars = [];
    if ($res) { while ($r = $res->fetch_assoc()) { $bars[] = $r['barangay']; } $res->free(); }
    if (count($bars) > 0) {
        // fetch counts grouped by month and barangay
        $inList = "'" . implode("','", array_map(function($s){return addslashes($s);}, $bars)) . "'";
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, barangay, COUNT(*) AS c FROM requests WHERE 1=1 " . $rangeConditions . " AND barangay IN (".$inList.") GROUP BY ym, barangay ORDER BY ym ASC";
        $res = $mysqli->query($sql);
        $cells = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) { $cells[$r['ym']][$r['barangay']] = intval($r['c']); }
            $res->free();
        }
        $months = $out['proposals_timeseries']['labels'] ?? [];
        // ensure months cover request months as well: merge unique months from cells
        foreach (array_keys($cells) as $ym) { if (!in_array($ym, $months)) $months[] = $ym; }
        sort($months);
        $matrix = [];
        foreach ($bars as $b) {
            $row = [];
            foreach ($months as $m) { $row[] = intval($cells[$m][$b] ?? 0); }
            $matrix[] = $row;
        }
        $out['barangay_heatmap'] = ['barangays'=>$bars, 'months'=>$months, 'matrix'=>$matrix];
    }
} catch (Exception $e) { }

// 5) Generate actionable insights server-side with severity levels
try {
    $insights = [];
    $totalRequests = intval($out['counts']['total_requests'] ?? 0);
    $pendingRequests = intval($out['counts']['pending_requests'] ?? 0);
    $pendingProposals = intval($out['counts']['pending_proposals'] ?? 0);

    // Top barangay
    if (count($out['requests_by_barangay']) > 0) {
        $top = $out['requests_by_barangay'][0];
    $pct = $totalRequests > 0 ? round(($top['count'] / $totalRequests) * 100, 1) : 0;
    // Adjusted sensitivity: flag a barangay as high if it accounts for >=30% of requests
    $sev = 'low';
    if ($pct >= 30) $sev = 'high'; elseif ($pct >= 18) $sev = 'medium';
        $insights[] = ['message' => "Highest request volume: {$top['barangay']} ({$top['count']} requests, {$pct}% of total) — consider targeted outreach or resource allocation.", 'severity' => $sev, 'code' => 'TOP_BARANGAY'];
    }

    // Pending requests backlog severity
    // Revised backlog thresholds: more sensitive for earlier warnings
    $sev = ($pendingRequests > 40) ? 'high' : (($pendingRequests > 15) ? 'medium' : (($pendingRequests > 5) ? 'low' : 'low'));
    if ($pendingRequests > 0) {
        $insights[] = ['message' => "Pending requests: {$pendingRequests} — backlog level: {$sev}.", 'severity' => $sev, 'code' => 'PENDING_REQUESTS'];
    }

    // Pending proposals pressure
    // Pending proposals thresholds adjusted
    $psev = ($pendingProposals > 20) ? 'high' : (($pendingProposals > 8) ? 'medium' : 'low');
    if ($pendingProposals > 0) {
        $insights[] = ['message' => "Pending proposals: {$pendingProposals} — review capacity may be impacted.", 'severity' => $psev, 'code' => 'PENDING_PROPOSALS'];
    }

    // Proposals growth/decline analysis (last 3 vs previous 3 months)
    $ts = $out['proposals_timeseries']['data'] ?? [];
    if (count($ts) >= 6) {
        $last3 = array_sum(array_slice($ts, -3));
        $prev3 = array_sum(array_slice($ts, -6, 3));
        if ($prev3 === 0 && $last3 > 0) {
            $insights[] = ['message' => "Recent surge: proposals increased from 0 to {$last3} in the last 3 months — review intake and processing capacity.", 'severity' => 'high', 'code' => 'PROPOSAL_SURGE'];
        } elseif ($prev3 > 0) {
            $pct = round((($last3 - $prev3) / $prev3) * 100, 1);
            $sev = 'low';
            if (abs($pct) >= 50) $sev = 'high'; elseif (abs($pct) >= 20) $sev = 'medium';
            if ($pct > 0) {
                $insights[] = ['message' => "Proposals increased by {$pct}% compared to the previous 3-month period.", 'severity' => $sev, 'code' => 'PROPOSAL_INCREASE'];
            } else {
                $insights[] = ['message' => "Proposals changed by {$pct}% compared to the previous 3-month period.", 'severity' => $sev, 'code' => 'PROPOSAL_CHANGE'];
            }
        }
    }

    // Old pending requests (older than 30 days) — relative to end date if provided
    try {
        $endExpr = $endValid ? "'{$endSql}'" : 'NOW()';
        $oldWhere = "";
        if ($startValid) { $oldWhere .= " AND created_at >= '{$startSql}'"; }
        $sqlOld = "SELECT COUNT(*) AS c FROM requests WHERE LOWER(status) LIKE '%pending%' AND created_at <= DATE_SUB(" . $endExpr . ", INTERVAL 30 DAY)" . $oldWhere;
        $r = $mysqli->query($sqlOld);
        $row = $r->fetch_assoc(); $oldPending = intval($row['c'] ?? 0);
        $r->free();
        if ($oldPending > 0) {
            // Old pending threshold tuned to surface smaller but important backlogs
            $sev = ($oldPending > 15) ? 'high' : (($oldPending > 5) ? 'medium' : 'low');
            $insights[] = ['message' => "{$oldPending} pending requests are older than 30 days — consider escalation.", 'severity' => $sev, 'code' => 'OLD_PENDING'];
        }
    } catch (Exception $e) { }

    // Approval rate for proposals (use provided range if present, otherwise last 6 months)
    try {
        if ($startValid || $endValid) {
            $sqlApp = "SELECT SUM(status='Approved') AS approved_count, COUNT(*) AS total_count FROM project_proposals WHERE 1=1 " . $rangeConditions;
            $res = $mysqli->query($sqlApp);
        } else {
            $res = $mysqli->query("SELECT SUM(status='Approved') AS approved_count, COUNT(*) AS total_count FROM project_proposals WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)");
        }
        if ($res) {
            $row = $res->fetch_assoc();
            $approved = intval($row['approved_count'] ?? 0);
            $total = intval($row['total_count'] ?? 0);
            $res->free();
            if ($total > 0) {
                $rate = round(($approved / $total) * 100, 1);
                /* Approval rate: lower rates are more severe; adjust thresholds */
                $sev = ($rate < 40) ? 'high' : (($rate < 60) ? 'medium' : 'low');
                $insights[] = ['message' => "Approval rate: {$rate}% ({$approved}/{$total}).", 'severity' => $sev, 'code' => 'APPROVAL_RATE'];
            }
        }
    } catch (Exception $e) { }

    // Average turnaround (days) — average time from created_at to last history entry (if proposal_history exists)
    try {
        if ($startValid || $endValid) {
            $sqlAvg = "SELECT AVG(TIMESTAMPDIFF(DAY, p.created_at, IFNULL((SELECT MAX(ph.created_at) FROM proposal_history ph WHERE ph.proposal_id = p.id), p.created_at))) AS avg_days FROM project_proposals p WHERE 1=1 " . $rangeConditions;
            $res = $mysqli->query($sqlAvg);
        } else {
            $res = $mysqli->query("SELECT AVG(TIMESTAMPDIFF(DAY, p.created_at, IFNULL((SELECT MAX(ph.created_at) FROM proposal_history ph WHERE ph.proposal_id = p.id), p.created_at))) AS avg_days FROM project_proposals p WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)");
        }
        if ($res) {
            $row = $res->fetch_assoc();
            $avgDays = round(floatval($row['avg_days'] ?? 0), 1);
            $res->free();
                if ($avgDays > 0) {
                // Average turnaround thresholds tightened (days)
                $sev = ($avgDays > 21) ? 'high' : (($avgDays > 10) ? 'medium' : 'low');
                $insights[] = ['message' => "Average proposal turnaround: {$avgDays} days.", 'severity' => $sev, 'code' => 'AVG_TURNAROUND'];
            }
        }
    } catch (Exception $e) { }

    // Fallback: if no insights detected, show stability note
    if (count($insights) === 0) {
        $insights[] = ['message' => 'No significant trends detected in the selected period.', 'severity' => 'low', 'code' => 'STABLE'];
    }

    $out['insights'] = $insights;

} catch (Exception $e) {
    // on error, return a simple text insight to avoid breaking client
    $out['insights'] = [['message' => 'Could not compute insights', 'severity' => 'low', 'code' => 'ERROR']];
}

echo json_encode($out);
exit;
?>

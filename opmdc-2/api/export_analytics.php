<?php
// export_analytics.php - Export analytics data as CSV or PDF
session_start();
require_once dirname(__DIR__) . '/check_session.php';

// Only allow staff/head users
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'head')) {
    http_response_code(403);
    die('Access denied');
}

try {
    $mysqli = require dirname(__DIR__) . '/db.php';
} catch (Exception $e) {
    http_response_code(500);
    die('Database connection failed');
}

$format = $_GET['format'] ?? 'csv';
$start = isset($_GET['start']) ? trim($_GET['start']) : null;
$end = isset($_GET['end']) ? trim($_GET['end']) : null;

function is_valid_ymd($s) {
    if (!$s) return false;
    $d = DateTime::createFromFormat('Y-m-d', $s);
    return $d && $d->format('Y-m-d') === $s;
}

$startValid = is_valid_ymd($start);
$endValid = is_valid_ymd($end);

// Build SQL range conditions
$rangeConditions = '';
if ($startValid) {
    $startSql = $mysqli->real_escape_string($start) . ' 00:00:00';
    $rangeConditions .= " AND created_at >= '$startSql'";
}
if ($endValid) {
    $endSql = $mysqli->real_escape_string($end) . ' 23:59:59';
    $rangeConditions .= " AND created_at <= '$endSql'";
}

// Fetch analytics data
$data = [
    'counts' => [],
    'proposals_by_barangay' => [],
    'proposals_by_status' => [],
    'insights' => [],
    'date_range' => [
        'start' => $startValid ? $start : 'N/A',
        'end' => $endValid ? $end : 'N/A'
    ],
    'generated_at' => date('Y-m-d H:i:s')
];

// Get counts
$whereBase = ' WHERE 1=1' . $rangeConditions;
$r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals" . $whereBase);
$row = $r->fetch_assoc();
$data['counts']['total_proposals'] = intval($row['c'] ?? 0);
$r->free();

$r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals WHERE (LOWER(status) LIKE '%for review%' OR LOWER(status) LIKE '%processing%' OR LOWER(status) LIKE '%pending%')" . ($rangeConditions ? $rangeConditions : ''));
$row = $r->fetch_assoc();
$data['counts']['pending_proposals'] = intval($row['c'] ?? 0);
$r->free();

// Get approved count
$r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals WHERE status='Approved'" . $whereBase);
$row = $r->fetch_assoc();
$data['counts']['approved_proposals'] = intval($row['c'] ?? 0);
$r->free();

// Get declined count
$r = $mysqli->query("SELECT COUNT(*) AS c FROM project_proposals WHERE status='Declined'" . $whereBase);
$row = $r->fetch_assoc();
$data['counts']['declined_proposals'] = intval($row['c'] ?? 0);
$r->free();

// Get proposals by barangay
$sql = "SELECT barangay, COUNT(*) AS c FROM project_proposals WHERE 1=1 " . $rangeConditions . " GROUP BY barangay ORDER BY c DESC LIMIT 10";
$res = $mysqli->query($sql);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $data['proposals_by_barangay'][] = [
            'barangay' => $r['barangay'],
            'count' => intval($r['c'])
        ];
    }
    $res->free();
}

// Get proposals by status
$sql2 = "SELECT status, COUNT(*) AS c FROM project_proposals WHERE 1=1 " . $rangeConditions . " GROUP BY status ORDER BY c DESC";
$res = $mysqli->query($sql2);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $data['proposals_by_status'][] = [
            'status' => $r['status'] ?? 'Unknown',
            'count' => intval($r['c'])
        ];
    }
    $res->free();
}

// Calculate approval rate
$totalProposals = $data['counts']['total_proposals'];
$approvedProposals = $data['counts']['approved_proposals'];
$approvalRate = $totalProposals > 0 ? round(($approvedProposals / $totalProposals) * 100, 2) : 0;
$data['counts']['approval_rate'] = $approvalRate;

// Calculate average turnaround time
$sqlAvg = "SELECT AVG(TIMESTAMPDIFF(DAY, p.created_at, IFNULL((SELECT MAX(ph.created_at) FROM proposal_history ph WHERE ph.proposal_id = p.id), p.created_at))) AS avg_days FROM project_proposals p WHERE 1=1 " . $rangeConditions;
$res = $mysqli->query($sqlAvg);
if ($res) {
    $row = $res->fetch_assoc();
    $data['counts']['avg_turnaround_days'] = round(floatval($row['avg_days'] ?? 0), 2);
    $res->free();
}

// Generate insights
$pendingProposals = $data['counts']['pending_proposals'];

if (count($data['proposals_by_barangay']) > 0) {
    $top = $data['proposals_by_barangay'][0];
    $pct = $totalProposals > 0 ? round(($top['count'] / $totalProposals) * 100, 1) : 0;
    $data['insights'][] = "Highest proposal volume: {$top['barangay']} ({$top['count']} proposals, {$pct}% of total)";
}

if ($pendingProposals > 0) {
    $data['insights'][] = "Currently {$pendingProposals} proposals pending review";
}

if ($approvalRate > 0) {
    $data['insights'][] = "Approval rate: {$approvalRate}% ({$approvedProposals}/{$totalProposals} proposals)";
}

$avgDays = $data['counts']['avg_turnaround_days'] ?? 0;
if ($avgDays > 0) {
    $data['insights'][] = "Average turnaround time: {$avgDays} days";
}

if ($totalProposals == 0) {
    $data['insights'][] = "No proposals found in the selected period";
}

// Generate export based on format
if ($format === 'csv') {
    generateCSV($data);
} elseif ($format === 'pdf') {
    generatePDF($data);
} else {
    http_response_code(400);
    die('Invalid format');
}

function generateCSV($data) {
    $filename = 'OPMDC_Analytics_Report_' . date('Y-m-d_His') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Title and metadata
    fputcsv($output, ['OPMDC Analytics Report']);
    fputcsv($output, ['Generated:', $data['generated_at']]);
    fputcsv($output, ['Date Range:', $data['date_range']['start'] . ' to ' . $data['date_range']['end']]);
    fputcsv($output, []);
    
    // Summary counts
    fputcsv($output, ['SUMMARY STATISTICS']);
    fputcsv($output, ['Metric', 'Value']);
    fputcsv($output, ['Total Proposals', $data['counts']['total_proposals']]);
    fputcsv($output, ['Pending Proposals', $data['counts']['pending_proposals']]);
    fputcsv($output, ['Approved Proposals', $data['counts']['approved_proposals']]);
    fputcsv($output, ['Declined Proposals', $data['counts']['declined_proposals']]);
    fputcsv($output, ['Approval Rate (%)', $data['counts']['approval_rate']]);
    fputcsv($output, ['Average Turnaround (days)', $data['counts']['avg_turnaround_days']]);
    fputcsv($output, []);
    
    // Proposals by barangay
    fputcsv($output, ['PROPOSALS BY BARANGAY (Top 10)']);
    fputcsv($output, ['Barangay', 'Count', 'Percentage (%)']);
    foreach ($data['proposals_by_barangay'] as $item) {
        $pct = $data['counts']['total_proposals'] > 0 
            ? round(($item['count'] / $data['counts']['total_proposals']) * 100, 2) 
            : 0;
        fputcsv($output, [$item['barangay'], $item['count'], $pct]);
    }
    fputcsv($output, []);
    
    // Proposals by status
    fputcsv($output, ['PROPOSALS BY STATUS']);
    fputcsv($output, ['Status', 'Count', 'Percentage (%)']);
    foreach ($data['proposals_by_status'] as $item) {
        $pct = $data['counts']['total_proposals'] > 0 
            ? round(($item['count'] / $data['counts']['total_proposals']) * 100, 2) 
            : 0;
        fputcsv($output, [$item['status'], $item['count'], $pct]);
    }
    fputcsv($output, []);
    
    // Key insights
    fputcsv($output, ['KEY INSIGHTS']);
    foreach ($data['insights'] as $insight) {
        fputcsv($output, [$insight]);
    }
    
    fclose($output);
    exit;
}

function generatePDF($data) {
    // Simple HTML to PDF conversion using inline styling
    $filename = 'OPMDC_Analytics_Report_' . date('Y-m-d_His') . '.pdf';
    
    // For basic PDF generation without external libraries, we'll use HTML with print CSS
    // This can be opened in a browser and printed to PDF
    // For production, consider using libraries like TCPDF or mPDF
    
    header('Content-Type: text/html; charset=utf-8');
    
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OPMDC Analytics Report</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11pt; 
            line-height: 1.4;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 { 
            color: #1e3a8a; 
            font-size: 24pt; 
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 { 
            color: #1e40af; 
            font-size: 16pt; 
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .meta { 
            background: #f3f4f6; 
            padding: 15px; 
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .meta p { margin: 5px 0; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0;
            font-size: 10pt;
        }
        th { 
            background: #1e3a8a; 
            color: white; 
            padding: 10px; 
            text-align: left;
            font-weight: 600;
        }
        td { 
            padding: 8px; 
            border-bottom: 1px solid #ddd; 
        }
        tr:nth-child(even) { background: #f9fafb; }
        .insight { 
            background: #eff6ff; 
            border-left: 4px solid #3b82f6;
            padding: 10px 15px;
            margin: 8px 0;
            border-radius: 3px;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 5px;
            padding: 15px;
        }
        .stat-label {
            color: #0369a1;
            font-size: 9pt;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            color: #1e3a8a;
        }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
        .print-btn {
            background: #1e3a8a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11pt;
            margin: 20px 0;
        }
        .print-btn:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print / Save as PDF</button>
    
    <h1>OPMDC Analytics Report</h1>
    
    <div class="meta">
        <p><strong>Report Generated:</strong> ' . htmlspecialchars($data['generated_at']) . '</p>
        <p><strong>Date Range:</strong> ' . htmlspecialchars($data['date_range']['start']) . ' to ' . htmlspecialchars($data['date_range']['end']) . '</p>
        <p><strong>Generated by:</strong> ' . htmlspecialchars($_SESSION['full_name'] ?? 'OPMDC Staff') . '</p>
    </div>
    
    <h2>Summary Statistics</h2>
    <div class="stat-grid">
        <div class="stat-box">
            <div class="stat-label">Total Proposals</div>
            <div class="stat-value">' . $data['counts']['total_proposals'] . '</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Pending Proposals</div>
            <div class="stat-value">' . $data['counts']['pending_proposals'] . '</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Approved Proposals</div>
            <div class="stat-value">' . $data['counts']['approved_proposals'] . '</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Approval Rate</div>
            <div class="stat-value">' . $data['counts']['approval_rate'] . '%</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Declined Proposals</div>
            <div class="stat-value">' . $data['counts']['declined_proposals'] . '</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Avg Turnaround (days)</div>
            <div class="stat-value">' . $data['counts']['avg_turnaround_days'] . '</div>
        </div>
    </div>
    
    <h2>Proposals by Barangay (Top 10)</h2>
    <table>
        <thead>
            <tr>
                <th>Barangay</th>
                <th style="text-align: center;">Count</th>
                <th style="text-align: center;">Percentage</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data['proposals_by_barangay'] as $item) {
        $pct = $data['counts']['total_proposals'] > 0 
            ? round(($item['count'] / $data['counts']['total_proposals']) * 100, 2) 
            : 0;
        $html .= '<tr>
            <td>' . htmlspecialchars($item['barangay']) . '</td>
            <td style="text-align: center;">' . $item['count'] . '</td>
            <td style="text-align: center;">' . $pct . '%</td>
        </tr>';
    }
    
    $html .= '</tbody>
    </table>
    
    <h2>Proposals by Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th style="text-align: center;">Count</th>
                <th style="text-align: center;">Percentage</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($data['proposals_by_status'] as $item) {
        $pct = $data['counts']['total_proposals'] > 0 
            ? round(($item['count'] / $data['counts']['total_proposals']) * 100, 2) 
            : 0;
        $html .= '<tr>
            <td>' . htmlspecialchars($item['status']) . '</td>
            <td style="text-align: center;">' . $item['count'] . '</td>
            <td style="text-align: center;">' . $pct . '%</td>
        </tr>';
    }
    
    $html .= '</tbody>
    </table>
    
    <h2>Key Insights & Recommendations</h2>';
    
    foreach ($data['insights'] as $insight) {
        $html .= '<div class="insight">' . htmlspecialchars($insight) . '</div>';
    }
    
    $html .= '
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd; font-size: 9pt; color: #666;">
        <p><strong>Office of the Provincial Mayor Development Coordinator (OPMDC)</strong></p>
        <p>Mabini, Batangas | Analytics System v1.0</p>
    </div>
    
</body>
</html>';
    
    echo $html;
    exit;
}
?>

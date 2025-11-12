<?php
// announcements handler: include DB and handle create announcement requests
$mysqli = include __DIR__ . '/db.php';

// Ensure announcements table exists
$createTableSql = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_by VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$mysqli->query($createTableSql);

$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement'])) {
    $title = trim($_POST['title'] ?? '');
    $body = trim($_POST['body'] ?? '');
    // In a real app, set created_by from session user info
    $created_by = 'OPMDC Staff';

    if ($title === '' || $body === '') {
        $error_message = 'Please provide both a title and announcement body.';
    } else {
        $stmt = $mysqli->prepare('INSERT INTO announcements (title, body, created_by) VALUES (?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('sss', $title, $body, $created_by);
            if ($stmt->execute()) {
                $success_message = 'Announcement posted successfully.';
            } else {
                $error_message = 'Database error while posting announcement.';
            }
            $stmt->close();
        } else {
            $error_message = 'Database prepare error.';
        }
    }
}

// Fetch latest announcements for display in the dashboard
$announcements = [];
$res = $mysqli->query("SELECT id, title, body, created_by, created_at FROM announcements ORDER BY created_at DESC LIMIT 50");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $announcements[] = $row;
    }
    $res->free();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OPMDC Staff Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
        /* Formal pop effect for dashboard cards */
        .card-pop {
            transition: transform 0.18s cubic-bezier(.4,2,.6,1), box-shadow 0.18s, border-color 0.18s;
            box-shadow: 0 2px 12px 0 rgba(59,130,246,0.10), 0 1px 4px 0 rgba(0,0,0,0.06);
            border: 2px solid #e0e7ef;
            border-radius: 1rem;
            background: #fff;
        }
            .card-pop:hover {
                transform: translateY(-4px) scale(1.035);
                box-shadow: 0 8px 32px 0 rgba(59,130,246,0.13), 0 3px 12px 0 rgba(0,0,0,0.10);
                border-color: #fbfbfc;
                background: #fff;
            }
            /* Logo formal and clean */
            .logo-formal {
                width: 6.5rem;
                height: 6.5rem;
                border-radius: 9999px;
                background: #fff;
                display: block;
                object-fit: cover;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 0.9rem;
                border: 2px solid #e6eef8; /* subtle blue tint */
                box-shadow: 0 6px 18px rgba(14, 45, 80, 0.06); /* softer shadow */
                padding: 4px;
                transition: transform 0.18s ease, box-shadow 0.18s ease;
                animation: logo-pop 420ms ease-out both;
            }
            .logo-formal:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 28px rgba(14,45,80,0.08); }

            @keyframes logo-pop {
                0% { transform: translateY(8px) scale(0.96); opacity: 0; }
                60% { transform: translateY(-2px) scale(1.02); opacity: 1; }
                100% { transform: translateY(0) scale(1); opacity: 1; }
            }
    body {
      font-family: 'Inter', sans-serif;
    }
    .modal-backdrop {
      background-color: rgba(0,0,0,0.5);
    }
    /* Custom styles for action buttons */
    .action-btn {
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      font-weight: 500;
      transition: background-color 0.2s;
      margin-right: 0.5rem;
      cursor: pointer;
    }
    .btn-approve {
      background-color: #10B981; /* Green-500 */
      color: white;
    }
    .btn-approve:hover {
      background-color: #059669; /* Green-600 */
    }
    .btn-decline {
      background-color: #EF4444; /* Red-500 */
      color: white;
    }
    .btn-decline:hover {
      background-color: #DC2626; /* Red-600 */
    }
     .btn-update, .btn-details {
      background-color: #3B82F6; /* Blue-500 */
      color: white;
    }
    .btn-update:hover, .btn-details:hover {
      background-color: #2563EB; /* Blue-600 */
    }
    .notification-dot {
        height: 10px;
        width: 10px;
        background-color: #EF4444;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    /* Modal transition styles */
    .modal-enter {
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 0.3s ease-out, transform 0.3s ease-out;
    }
    
    .modal-enter-active {
        opacity: 1;
        transform: scale(1);
    }
    
    .modal-backdrop {
        transition: opacity 0.3s ease-out;
    }
    /* Notification toast styles (shared) */
    .notif-toast-container { position: fixed; right: 1rem; bottom: 1rem; z-index: 60; display:flex; flex-direction:column; gap:0.5rem; }
    .notif-toast { background: white; border-radius: 0.5rem; box-shadow: 0 6px 18px rgba(0,0,0,0.12); padding: 0.75rem 1rem; width: 320px; cursor: pointer; transition: transform 0.15s ease, opacity 0.2s ease; }
    .notif-toast:hover { transform: translateY(-4px); }
    .notif-toast__title { font-weight: 600; color: #1F2937; }
    .notif-toast__body { font-size: 0.85rem; color: #4B5563; margin-top: 0.25rem; }
    .notif-toast--new { border-left: 4px solid #3B82F6; }
    .highlight-request { box-shadow: 0 8px 30px rgba(59,130,246,0.12); border-radius: 8px; animation: highlightFade 3s ease forwards; }
    @keyframes highlightFade { 0% { background: #fef3c7; } 100% { background: transparent; } }
        /* Sidebar slide/fade animation */
        /* reveal CSS moved to assets/ui-animations.css */
    </style>
    <link rel="stylesheet" href="assets/ui-animations.css">
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-slate-50">

    <div class="flex h-screen">
    <aside class="sidebar w-64 bg-gray-800 text-white flex flex-col" aria-hidden="false" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
    <img src="assets/image1.png" alt="Mabini Seal" class="logo-formal">
    <h2 id="sidebar-role-title" class="text-xl font-semibold">OPMDC Staff</h2>
        <p class="text-xs text-gray-400">Mabini, Batangas</p>
      </div>
      <nav class="flex-grow px-4 py-6">
        <a href="#" id="dashboard-link" class="sidebar-link flex items-center px-4 py-2 text-gray-100 rounded-md">
          <i class="fas fa-tachometer-alt mr-3"></i>
          <span>Dashboard</span>
        </a>
                <a href="#announcements" id="announcements-link" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                    <i class="fas fa-bullhorn mr-3"></i>
                    <span>Announcements</span>
                </a>
                <a href="#proposals" id="proposals-link" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                    <i class="fas fa-project-diagram mr-3"></i>
                    <span>Proposals</span>
                    <span id="proposalsBadge" class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-white bg-red-600 rounded-full hidden">0</span>
                </a>
                <a href="#analytics" id="analytics-link" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
                    <i class="fas fa-chart-line mr-3"></i>
                    <span>Analytics</span>
                </a>
         <a href="#accounts" id="accounts-link" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
          <i class="fas fa-users mr-3"></i>
          <span>Accounts</span>
        </a>
        <a href="#" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
          <i class="fas fa-history mr-3"></i>
          <span>History</span>
        </a>
      </nav>
      <div class="p-4 border-t border-gray-700">
        <a href="login.html" class="flex items-center w-full px-4 py-2 text-gray-300 hover:bg-red-600 hover:text-white rounded-md">
          <i class="fas fa-sign-out-alt mr-3"></i>
          Logout
        </a>
      </div>
    </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-md p-4" data-reveal="header">
        <div class="flex items-center justify-between">
          <div>
            <h1 id="main-content-title" class="text-2xl font-bold text-gray-800">Dashboard</h1>
            <p id="current-date" class="text-sm text-gray-500"></p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="relative">
              <span class="absolute left-3 top-2 text-gray-400">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
                        <!-- Notification bell -->
                        <div class="relative">
                            <button id="staffNotifBell" title="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none">
                                <i class="fas fa-bell text-gray-600 text-lg"></i>
                                <span id="staffNotifBadge" class="notification-dot hidden"></span>
                            </button>
                            <div id="staffNotifDropdown" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg z-50 w-80">
                                <div class="p-3 border-b flex items-center justify-between"><strong>Notifications</strong><button id="staffMarkAllRead" class="text-xs text-blue-600">Mark all read</button></div>
                                <div id="staffNotifList" class="max-h-64 overflow-auto p-2"><div class="text-center text-gray-500">Loading…</div></div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full" src="https://placehold.co/100x100/E2E8F0/4A5568?text=Staff" alt="User Avatar">
                            <div class="ml-3">
                                <p id="header-user-name" class="text-sm font-semibold text-gray-800">OPMDC Staff</p>
                                <p id="header-user-role" class="text-xs text-gray-500">Staff</p>
                            </div>
                        </div>
          </div>
        </div>
      </header>
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div id="dashboard-view" class="view">
          <div class="container mx-auto">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Activity Reports & Accounts Overview</h3>
                            <!-- phase filter removed as requested -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-reveal="group">
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-file-alt text-blue-600 text-3xl mr-3"></i>
                                        <span class="text-blue-600 text-lg font-semibold">Requests</span>
                                    </div>
                                    <span id="total-requests-stat" class="text-4xl font-extrabold text-blue-600">0</span>
                                    <span class="text-gray-500 text-xs mt-1">All submitted</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-hourglass-half text-yellow-600 text-3xl mr-3"></i>
                                        <span class="text-yellow-600 text-lg font-semibold">Pending</span>
                                    </div>
                                    <span id="pending-requests-stat" class="text-4xl font-extrabold text-yellow-600">0</span>
                                    <span class="text-gray-500 text-xs mt-1">Awaiting action</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-users text-green-600 text-3xl mr-3"></i>
                                        <span class="text-green-600 text-lg font-semibold">Accounts</span>
                                    </div>
                                    <span id="total-accounts-stat" class="text-4xl font-extrabold text-green-600">0</span>
                                    <span class="text-gray-500 text-xs mt-1">Registered</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-user-clock text-red-600 text-3xl mr-3"></i>
                                        <span class="text-red-600 text-lg font-semibold">For Approval</span>
                                    </div>
                                    <span id="pending-accounts-stat" class="text-4xl font-extrabold text-red-600">0</span>
                                    <span class="text-gray-500 text-xs mt-1">New signups</span>
                                </div>
                            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Submissions</h3>
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                  <thead class="text-xs text-slate-500 uppercase bg-slate-50 font-medium">
                    <tr>
                      <th scope="col" class="px-6 py-4">Request ID</th>
                      <th scope="col" class="px-6 py-4">Barangay</th>
                      <th scope="col" class="px-6 py-4">Type</th>
                      <th scope="col" class="px-6 py-4">Date Submitted</th>
                      <th scope="col" class="px-6 py-4">Status</th>
                      <th scope="col" class="px-6 py-4">Action</th>
                    </tr>
                  </thead>
                  <tbody id="requests-table-body"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="proposals-view" class="view hidden">
            <div class="container mx-auto">
                <div class="mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Project Proposal Management</h2>
                        <p class="text-gray-600">Monitor, track, and manage all project proposals.</p>
                    </div>
                </div>
                
                <!-- Filters removed: Submission / Sent by / Status controls intentionally removed to simplify the Proposals view -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 font-medium">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Project Title</th>
                                    <th scope="col" class="px-6 py-4">Barangay</th>
                                    <th scope="col" class="px-6 py-4">Date Submitted</th>
                                    <th scope="col" class="px-6 py-4">Last Updated</th>
                                    <th scope="col" class="px-6 py-4">Status</th>
                                    <th scope="col" class="px-6 py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody id="proposals-management-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="analytics-view" class="view hidden">
            <div class="container mx-auto">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Analytics & Insights</h2>
                        <p class="text-gray-600">Visualize proposals and requests to drive actionable decisions.</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input id="analyticsStart" type="date" class="border rounded px-2 py-1 text-sm" />
                        <input id="analyticsEnd" type="date" class="border rounded px-2 py-1 text-sm" />
                        <button id="analyticsRefresh" class="bg-blue-600 text-white text-sm px-3 py-2 rounded">Refresh</button>
                        <button id="exportCsvBtn" class="bg-slate-200 hover:bg-slate-300 text-sm px-3 py-2 rounded">Export CSV</button>
                        <button id="exportPdfBtn" class="bg-slate-200 hover:bg-slate-300 text-sm px-3 py-2 rounded">Export PDF</button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                                    <h4 class="text-lg font-semibold mb-2">Proposals (selected period)</h4>
                            <canvas id="proposalsChart" height="160"></canvas>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Requests by Barangay (top 10)</h4>
                            <canvas id="barangayChart" height="160"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Approval Funnel</h4>
                            <canvas id="approvalFunnel" height="180"></canvas>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Avg Turnaround (trend)</h4>
                            <canvas id="avgTurnaroundChart" height="180"></canvas>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold mb-2">Barangay Heatmap (top)</h4>
                            <div id="heatmapContainer" class="overflow-auto max-h-48 p-2 bg-gray-50 rounded"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-lg font-semibold mb-4">Actionable Insights</h4>
                    <div id="severityLegend" class="mb-3 text-sm text-gray-600">
                        <strong>Severity legend</strong>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <div class="flex items-start gap-2"><span style="width:12px;height:12px;border-radius:9999px;background:#009E73;display:inline-block;margin-top:3px"></span><div><strong>Low</strong> — Normal / No immediate action (e.g. low backlog)</div></div>
                            <div class="flex items-start gap-2"><span style="width:12px;height:12px;border-radius:9999px;background:#F0E442;display:inline-block;margin-top:3px"></span><div><strong>Medium</strong> — Needs attention soon (e.g. moderate backlog)</div></div>
                            <div class="flex items-start gap-2"><span style="width:12px;height:12px;border-radius:9999px;background:#D55E00;display:inline-block;margin-top:3px"></span><div><strong>High</strong> — Immediate action recommended (e.g. large backlog)</div></div>
                            <div class="col-span-full text-xs text-slate-500">Reference ranges (for guidance): Pending requests — Low 0–5, Medium 6–40, High &gt;40; Approval rate — Low severity &gt;=60%, Medium 40–59%, High &lt;40%; Avg turnaround — Low 0–10d, Medium 11–21d, High &gt;21d; Top barangay share — Medium 18–29%, High &gt;=30%.</div>
                        </div>
                    </div>
                    <div id="analyticsInsights" class="space-y-2 text-sm text-gray-700">Loading insights…</div>
                </div>
            </div>
        </div>

        <div id="announcements-view" class="view hidden">
            <div class="container mx-auto">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Announcements</h2>
                    <p class="text-gray-600">Create announcements that will appear on the public homepage.</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <?php if (
                        $success_message
                    ): ?>
                        <div class="p-3 mb-4 rounded bg-green-50 border border-green-200 text-green-700"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>
                    <?php if (
                        $error_message
                    ): ?>
                        <div class="p-3 mb-4 rounded bg-red-50 border border-red-200 text-red-700"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <form method="post" action="staff-dashboard.php">
                        <input type="hidden" name="create_announcement" value="1">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input name="title" type="text" class="w-full border rounded-md p-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Announcement</label>
                            <textarea name="body" rows="4" class="w-full border rounded-md p-2" required></textarea>
                        </div>
                        
                        <div class="flex items-center">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Post Announcement</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h4 class="text-lg font-semibold mb-4">Recent Announcements</h4>
                    <?php if (count($announcements) === 0): ?>
                        <p class="text-gray-500">No announcements yet.</p>
                    <?php else: ?>
                        <ul class="space-y-4">
                        <?php foreach ($announcements as $a): ?>
                            <li class="border rounded p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-semibold text-gray-800"><?php echo htmlspecialchars($a['title']); ?></h5>
                                        <p class="text-sm text-gray-600 mt-1 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($a['body'])); ?></p>
                                    </div>
                                    <div class="text-right text-xs text-gray-500 ml-4">
                                        <div><?php echo htmlspecialchars($a['created_by']); ?></div>
                                        <div><?php echo htmlspecialchars($a['created_at']); ?></div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div id="accounts-view" class="view hidden">
            <div class="container mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Barangay Account Management</h2>
                        <p class="text-gray-600">Create and manage accounts for each barangay.</p>
                    </div>
                    <button id="createAccountBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create Account
                    </button>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 font-medium">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Barangay</th>
                                    <th scope="col" class="px-6 py-4">Representative</th>
                                    <th scope="col" class="px-6 py-4">Email</th>
                                    <th scope="col" class="px-6 py-4">Status</th>
                                    <th scope="col" class="px-6 py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody id="accounts-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </main>
    </div>
  </div>

  <div id="proposalDetailModal" class="fixed inset-0 z-50 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-8 w-full max-w-2xl z-10 max-h-[90vh] flex flex-col modal-enter">
            <div class="flex justify-between items-center mb-4 border-b pb-4">
                <h2 class="text-2xl font-bold text-gray-800">Proposal Details</h2>
                <button id="closeDetailModalBtn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

                            <!-- staged reveal JS moved to assets/ui-animations.js -->
                            <script src="assets/ui-animations.js"></script>
            <div class="flex-grow overflow-y-auto pr-4">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div><p class="font-semibold text-gray-700">Project Title:</p><p id="detail-title"></p></div>
                    <div><p class="font-semibold text-gray-700">Barangay:</p><p id="detail-barangay"></p></div>
                    <div><p class="font-semibold text-gray-700">Date Submitted:</p><p id="detail-date"></p></div>
                    <div><p class="font-semibold text-gray-700">Current Status:</p><p id="detail-status"></p></div>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Document History</h3>
                    <div id="proposal-history-list" class="space-y-2 max-h-40 overflow-y-auto bg-gray-50 p-3 rounded-md"></div>
                </div>
                <form id="proposalUpdateForm">
                    <input type="hidden" id="proposalId">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-t pt-4">Update Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="proposalStatus" class="block text-gray-700 text-sm font-bold mb-2">New Status:</label>
                            <select id="proposalStatus" name="proposalStatus" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option>For Review</option>
                                <option>Processing</option>
                                <option>Requires Revision</option>
                                <option>For Head Approval</option>
                                <option>Approved</option>
                                <option>Declined</option>
                            </select>
                        </div>
                        <div>
                            <label for="remarks" class="block text-gray-700 text-sm font-bold mb-2">Remarks (Optional):</label>
                            <input type="text" id="remarks" name="remarks" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Add a comment...">
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-6">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <div id="createAccountModal" class="fixed inset-0 z-50 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-8 w-full max-w-md z-10 modal-enter">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Create New Barangay Account</h2>
                <button id="closeAccountModalBtn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <form id="createBarangayAccountForm">
                <div class="mb-4">
                    <label for="barangayName" class="block text-gray-700 text-sm font-bold mb-2">Barangay:</label>
                    <select id="barangayName" name="barangayName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="" disabled selected>Select a Barangay</option>
                        <option value="Anilao East">Anilao East</option>
                        <option value="Anilao Proper">Anilao Proper</option>
                        <option value="Bagalangit">Bagalangit</option>
                        <option value="Bulacan">Bulacan</option>
                        <option value="Calamias">Calamias</option>
                        <option value="Estrella">Estrella</option>
                        <option value="Gasang">Gasang</option>
                        <option value="Laurel">Laurel</option>
                        <option value="Ligaya">Ligaya</option>
                        <option value="Mainaga">Mainaga</option>
                        <option value="Mainit">Mainit</option>
                        <option value="Majuben">Majuben</option>
                        <option value="Malimatoc I">Malimatoc I</option>
                        <option value="Malimatoc II">Malimatoc II</option>
                        <option value="Nag-Iba">Nag-Iba</option>
                        <option value="Pilahan">Pilahan</option>
                        <option value="Poblacion">Poblacion</option>
                        <option value="Pulang Lupa">Pulang Lupa</option>
                        <option value="Pulong Anahao">Pulong Anahao</option>
                        <option value="Pulong Balibaguhan">Pulong Balibaguhan</option>
                        <option value="Pulong Niogan">Pulong Niogan</option>
                        <option value="Saguing">Saguing</option>
                        <option value="Sampaguita">Sampaguita</option>
                        <option value="San Francisco">San Francisco</option>
                        <option value="San Jose">San Jose</option>
                        <option value="San Juan">San Juan</option>
                        <option value="San Teodoro">San Teodoro</option>
                        <option value="Santa Ana">Santa Ana</option>
                        <option value="Santa Mesa">Santa Mesa</option>
                        <option value="Santo Niño">Santo Niño</option>
                        <option value="Santo Tomas">Santo Tomas</option>
                        <option value="Solo">Solo</option>
                        <option value="Talaga East">Talaga East</option>
                        <option value="Talaga Proper">Talaga Proper</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="representative" class="block text-gray-700 text-sm font-bold mb-2">Representative Name:</label>
                    <input type="text" id="representative" name="representative" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                 <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="alertModal" class="fixed inset-0 z-60 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-8 w-full max-w-sm z-10 modal-enter">
            <h2 id="alertModalTitle" class="text-xl font-bold text-gray-800 mb-4">Alert</h2>
            <p id="alertModalMessage" class="text-gray-600 mb-6">Message goes here.</p>
            <div id="alertModalActions" class="flex items-center justify-end"></div>
        </div>
    </div>

    <!-- Insight Detail Modal -->
    <div id="insightDetailModal" class="fixed inset-0 z-60 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-6 w-full max-w-lg z-10 modal-enter">
            <div class="flex justify-between items-center mb-4">
                <h2 id="insightDetailTitle" class="text-xl font-bold text-gray-800">Insight Details</h2>
                <button id="closeInsightModalBtn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <div id="insightDetailBody" class="text-sm text-gray-700 whitespace-pre-wrap max-h-[60vh] overflow-auto"></div>
        </div>
    </div>

    <!-- Edit Barangay Account Modal -->
    <div id="editAccountModal" class="fixed inset-0 z-50 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-8 w-full max-w-md z-10 modal-enter">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Barangay Account</h2>
                <button id="closeEditAccountModalBtn" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <form id="editBarangayAccountForm">
                <input type="hidden" id="editUserId">
                <div class="mb-4">
                    <label for="editBarangayName" class="block text-gray-700 text-sm font-bold mb-2">Barangay:</label>
                    <select id="editBarangayName" name="barangayName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="Anilao East">Anilao East</option>
                        <option value="Anilao Proper">Anilao Proper</option>
                        <option value="Bagalangit">Bagalangit</option>
                        <option value="Bulacan">Bulacan</option>
                        <option value="Calamias">Calamias</option>
                        <option value="Estrella">Estrella</option>
                        <option value="Gasang">Gasang</option>
                        <option value="Laurel">Laurel</option>
                        <option value="Ligaya">Ligaya</option>
                        <option value="Mainaga">Mainaga</option>
                        <option value="Mainit">Mainit</option>
                        <option value="Majuben">Majuben</option>
                        <option value="Malimatoc I">Malimatoc I</option>
                        <option value="Malimatoc II">Malimatoc II</option>
                        <option value="Nag-Iba">Nag-Iba</option>
                        <option value="Pilahan">Pilahan</option>
                        <option value="Poblacion">Poblacion</option>
                        <option value="Pulang Lupa">Pulang Lupa</option>
                        <option value="Pulong Anahao">Pulong Anahao</option>
                        <option value="Pulong Balibaguhan">Pulong Balibaguhan</option>
                        <option value="Pulong Niogan">Pulong Niogan</option>
                        <option value="Saguing">Saguing</option>
                        <option value="Sampaguita">Sampaguita</option>
                        <option value="San Francisco">San Francisco</option>
                        <option value="San Jose">San Jose</option>
                        <option value="San Juan">San Juan</option>
                        <option value="San Teodoro">San Teodoro</option>
                        <option value="Santa Ana">Santa Ana</option>
                        <option value="Santa Mesa">Santa Mesa</option>
                        <option value="Santo Niño">Santo Niño</option>
                        <option value="Santo Tomas">Santo Tomas</option>
                        <option value="Solo">Solo</option>
                        <option value="Talaga East">Talaga East</option>
                        <option value="Talaga Proper">Talaga Proper</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="editRepresentative" class="block text-gray-700 text-sm font-bold mb-2">Representative Name:</label>
                    <input type="text" id="editRepresentative" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="editEmail" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="editEmail" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-6">
                    <label for="editPassword" class="block text-gray-700 text-sm font-bold mb-2">Password (leave blank to keep):</label>
                    <input type="password" id="editPassword" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Route guard: ensure only OPMDC Staff role lands here
    try {
        const u = JSON.parse(localStorage.getItem('loggedInUser'));
        if (!u || !u.role) throw new Error('no user');
        const r = String(u.role);
        if (/admin/i.test(r)) return (window.location.href = 'admin.php');
        if (/head/i.test(r)) return (window.location.href = 'head-dashboard.php');
        if (!/staff/i.test(r)) return (window.location.href = 'barangay-dashboard.php');
    } catch (e) { /* if no user, default to login */ }
    // --- DOM Elements ---
    const allViews = document.querySelectorAll('.view');
    const mainContentTitle = document.getElementById('main-content-title');
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    const createAccountBtn = document.getElementById('createAccountBtn');
    const createAccountModal = document.getElementById('createAccountModal');
    const closeAccountModalBtn = document.getElementById('closeAccountModalBtn');
    const createAccountForm = document.getElementById('createBarangayAccountForm');
    
    // --- Initial setup ---
    // Load real data from server
    fetchServerData();
    // Load badge state from localStorage (deferred so helpers are defined later in this script)
    setTimeout(() => { try { if (typeof loadBadgeCount === 'function') loadBadgeCount(); } catch (e) {} }, 0);

    // Populate user info from localStorage when available
    try {
        const u = JSON.parse(localStorage.getItem('loggedInUser'));
        if (u && u.role) {
            const roleTitle = document.getElementById('sidebar-role-title');
            if (roleTitle) roleTitle.textContent = u.role;
        }
        if (u && (u.name || u.role)) {
            const nameEl = document.getElementById('header-user-name');
            const roleEl = document.getElementById('header-user-role');
            if (nameEl) nameEl.textContent = u.name || 'Staff';
            if (roleEl) roleEl.textContent = u.role || 'OPMDC Staff';
        }
    } catch (e) { /* ignore */ }
    
    // --- Event Listeners ---
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1) + '-view';
            // A special case for the dashboard link which has href="#"
            const viewIdToShow = targetId === "-view" ? "dashboard-view" : targetId;
            const targetView = document.getElementById(viewIdToShow);
            const title = link.querySelector('span').textContent;
            // If opening proposals, ensure we request the latest server proposals first
            if (viewIdToShow === 'proposals-view') {
                try { if (typeof fetchServerData === 'function') fetchServerData(); } catch (e) { console.warn('fetchServerData missing', e); }
            }
            showView(targetView, title, link);
        });
    });
    
    createAccountBtn.addEventListener('click', () => {
        createAccountForm.reset();
        openModal('createAccountModal');
    });
    closeAccountModalBtn.addEventListener('click', () => closeModal('createAccountModal'));

createAccountForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const formData = new FormData(createAccountForm);
    const payload = new URLSearchParams();
    payload.append('barangayName', formData.get('barangayName'));
    payload.append('name', formData.get('representative'));
    // use email as username for barangay accounts
    const emailVal = formData.get('email');
    payload.append('username', emailVal);
    payload.append('email', emailVal);
    payload.append('password', formData.get('password'));
    payload.append('role', 'Barangay Official');

    fetch('staff_create_account.php', {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: payload
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            createAccountForm.reset();
            closeModal('createAccountModal');
            showAlert('Success', 'Account created successfully.');
            // If server returned the created user, re-render accounts table to include it
            if (data.user) {
                // trigger accounts re-render which fetches fresh list from server
                if (typeof renderAccountsTable === 'function') renderAccountsTable();
            } else {
                if (typeof renderAccountsTable === 'function') renderAccountsTable();
            }
        } else {
            showAlert('Error', data.message || 'Failed to create account.');
        }
    })
    .catch(() => {
        showAlert('Error', 'Server error.');
    });
});
    
    window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal-backdrop')) {
            closeModal('proposalDetailModal');
            closeModal('createAccountModal');
            closeModal('alertModal');
            closeModal('insightDetailModal');
        }
    });

    // Filters removed from the DOM; no change listeners needed here.

        // Cross-tab storage sync removed: app is server-driven. Use server refresh / SSE instead.

    // --- Initial Page Load ---
    updateTime();
    setInterval(updateTime, 60000); // Update time every minute
    showView(document.getElementById('dashboard-view'), 'Dashboard', document.getElementById('dashboard-link'));
});

// --- Core Functions ---
function showView(viewToShow, title, linkToActivate) {
    document.querySelectorAll('.view').forEach(view => view.classList.add('hidden'));
    if (viewToShow) {
        viewToShow.classList.remove('hidden');
    }
    document.getElementById('main-content-title').textContent = title;
    
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('bg-slate-700', 'text-white');
        link.classList.add('text-gray-300');
    });
    if (linkToActivate) {
        linkToActivate.classList.add('bg-slate-700', 'text-white');
        linkToActivate.classList.remove('text-gray-300');
    }
    
    renderAllViews();
    // If analytics view is shown, fetch analytics data to render charts
    try {
        if (viewToShow && viewToShow.id === 'analytics-view' && typeof fetchAnalytics === 'function') {
            fetchAnalytics();
        }
    } catch (e) { console.warn('fetchAnalytics error', e); }
}

// Modal helper functions for smooth transitions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.modal-enter');
    
    modal.style.display = 'flex';
    modal.classList.remove('opacity-0');
    modal.classList.add('opacity-100');
    
    // Trigger transition
    setTimeout(() => {
        if (modalContent) {
            modalContent.classList.add('modal-enter-active');
        }
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.modal-enter');
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    if (modalContent) {
        modalContent.classList.remove('modal-enter-active');
    }
    
    // Hide modal after transition
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function renderAllViews() {
    renderDashboardUI();
    renderAccountsTable();
    renderRequestsTable();
    renderProposalsPage();
}

function renderDashboardUI() {
    // Prefer server-provided data (populated by fetchServerData()); do NOT fall back to localStorage.
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];
    const serverRequests = (window._serverRequests && Array.isArray(window._serverRequests)) ? window._serverRequests : null;
    const localRequests = [];
    const accounts = (window._serverAccounts && Array.isArray(window._serverAccounts)) ? window._serverAccounts : [];

    // If serverRequests exist, include them; otherwise merge proposals + local requests as before
    let totalRequestsCount = 0;
    let pendingCount = 0;
    if (serverRequests) {
        totalRequestsCount = serverRequests.length;
        pendingCount = serverRequests.filter(r => /pending|for review|processing/i.test(r.status || '')).length;
    } else {
        totalRequestsCount = proposals.length + localRequests.length;
        const proposalsPending = proposals.filter(r => /For Review|Processing/i.test(r.status)).length;
        const localPending = localRequests.filter(r => /Pending|For Review|Processing/i.test(r.status || '')).length;
        pendingCount = proposalsPending + localPending;
    }

    document.getElementById('total-requests-stat').textContent = totalRequestsCount;
    document.getElementById('pending-requests-stat').textContent = pendingCount;
    document.getElementById('total-accounts-stat').textContent = accounts.length;
    document.getElementById('pending-accounts-stat').textContent = accounts.filter(a => a.status === 'pending').length;
}

// Small HTML escaper available globally (used by multiple renderers)
function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function(c){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
    });
}

async function renderAccountsTable() {
    const tableBody = document.getElementById('accounts-table-body');
    if (!tableBody) return;
    tableBody.innerHTML = '';

    try {
        const res = await fetch('list_barangay_accounts.php', { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        const accounts = (data && data.success && Array.isArray(data.accounts)) ? data.accounts : [];

        if (accounts.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No accounts found.</td></tr>`;
            return;
        }

        accounts.forEach((acc, index) => {
            const emailKey = acc.email || acc.username || '';
            const be = encodeURIComponent(acc.barangayName || '');
            const re = encodeURIComponent(acc.representative || '');
            const ee = encodeURIComponent(emailKey);
            const row = `
                <tr class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                    <td class="py-4 px-4">${acc.barangayName}</td>
                    <td class="py-4 px-4">${acc.representative}</td>
                    <td class="py-4 px-4">${emailKey}</td>
                    <td class="py-4 px-4">${getStatusBadge(acc.status)}</td>
                    <td class="py-4 px-4">
                        <button class="action-btn btn-update" onclick="openEditAccountFromEncoded(${acc.id}, '${be}', '${re}', '${ee}')">Edit</button>
                        <button class="action-btn btn-decline" onclick="deleteAccount(${acc.id})">Delete</button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (err) {
        console.warn('Could not load barangay accounts from server, falling back to empty.', err);
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No accounts found.</td></tr>`;
    }
}


function renderRequestsTable() {
    // This table shows a summary of recent proposals and barangay requests.
    // Use server-provided requests (window._serverRequests) and server proposals only; no localStorage fallbacks.
    const serverRequests = (window._serverRequests && Array.isArray(window._serverRequests)) ? window._serverRequests : null;
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];
    const localRequests = [];

    let requests = [];
    if (serverRequests) {
        // normalize serverRequests to a common shape (also pass through request_code)
        requests = serverRequests.map(r => ({ id: r.id, code: r.request_code || null, barangay: r.barangay, title: r.request_type || r.title || 'Barangay Request', date: r.created_at || r.date || new Date().toISOString(), status: r.status || 'Pending' }));
    } else {
        // Normalize local requests to the proposals shape and merge with local proposals
        const mappedLocal = localRequests.map(r => ({ id: r.id, barangay: r.barangay, title: r.requestType || 'Barangay Request', date: r.date || r.date_submitted || new Date().toISOString(), status: r.status || 'Pending', isLocal: true }));
        requests = proposals.concat(mappedLocal);
    }
    const tableBody = document.getElementById('requests-table-body');
    if (!tableBody) return;
    tableBody.innerHTML = '';

    if (requests.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No requests found.</td></tr>`;
        return;
    }
    
    // Show top 5 most recent (by date)
    requests.sort((a,b)=> new Date(a.date) - new Date(b.date));
    requests.slice(-5).reverse().forEach((req, index) => {
        const displayId = req.code || req.id;
        const row = `
            <tr id="request-row-${req.id}" class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                <td class="py-4 px-4">${escapeHtml(String(displayId))}</td>
                <td class="py-4 px-4">${req.barangay}</td>
                <td class="py-4 px-4">${escapeHtml(req.title || (req.requestType || 'Proposal'))}</td>
                <td class="py-4 px-4">${new Date(req.date || req.created_at).toLocaleDateString()}</td>
                <td class="py-4 px-4">${getStatusBadge(req.status)}</td>
                <td class="py-4 px-4">
                            ${ (String(req.status).toLowerCase() !== 'approved' && String(req.status).toLowerCase() !== 'declined') ? `
                                <button class="action-btn btn-approve" onclick="confirmUpdateRequestStatus(${req.id}, 'Approved')">Approve</button>
                                <button class="action-btn btn-decline" onclick="confirmUpdateRequestStatus(${req.id}, 'Declined')">Decline</button>
                            ` : `
                                <button class="action-btn btn-decline" onclick="confirmDeleteRequest(${req.id})">Delete</button>
                            ` }
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// Highlight a newly submitted proposal in the Recent Submissions table without refreshing the whole page
function highlightNewProposal(proposal) {
    try {
        const tableBody = document.getElementById('requests-table-body');
        if (!tableBody || !proposal) return;
        const req = {
            id: proposal.id,
            code: proposal.request_code || null,
            barangay: proposal.barangay || proposal.created_by || 'Unknown',
            title: proposal.title || proposal.request_type || 'Project Proposal',
            date: proposal.created_at || proposal.date || new Date().toISOString(),
            status: proposal.status || 'For Review'
        };

        const existing = document.getElementById('request-row-' + req.id);
        const displayId = req.code || req.id;
        const rowHtml = `
            <tr id="request-row-${req.id}" class="border-b highlight-request">
                <td class="py-4 px-4">${escapeHtml(String(displayId))}</td>
                <td class="py-4 px-4">${escapeHtml(req.barangay)}</td>
                <td class="py-4 px-4">${escapeHtml(req.title)}</td>
                <td class="py-4 px-4">${new Date(req.date).toLocaleDateString()}</td>
                <td class="py-4 px-4">${getStatusBadge(req.status)}</td>
                <td class="py-4 px-4">
                    ${ (String(req.status).toLowerCase() !== 'approved' && String(req.status).toLowerCase() !== 'declined') ? `
                        <button class="action-btn btn-approve" onclick="confirmUpdateRequestStatus(${req.id}, 'Approved')">Approve</button>
                        <button class="action-btn btn-decline" onclick="confirmUpdateRequestStatus(${req.id}, 'Declined')">Decline</button>
                    ` : `
                        <button class="action-btn btn-decline" onclick="confirmDeleteRequest(${req.id})">Delete</button>
                    ` }
                </td>
            </tr>
        `;

        if (existing) {
            // Replace existing row and flash highlight
            existing.outerHTML = rowHtml;
            const newRow = document.getElementById('request-row-' + req.id);
            if (newRow) {
                newRow.classList.add('highlight-request');
                newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => newRow.classList.remove('highlight-request'), 4000);
            }
            return;
        }

        // Prepend new row to the table
        tableBody.insertAdjacentHTML('afterbegin', rowHtml);
        const added = document.getElementById('request-row-' + req.id);
        if (added) {
            added.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => { try { added.classList.remove('highlight-request'); } catch(e){} }, 4000);
        }
        // Keep only top 5 rows to match renderRequestsTable behavior
        while (tableBody.children.length > 5) tableBody.removeChild(tableBody.lastElementChild);
    } catch (err) {
        console.error('highlightNewProposal error', err);
    }
}

// --- New submissions badge (staff) ---
const _badgeKeyStaff = 'opmdcNewSubmissions_Staff';
function loadBadgeCount() {
    try {
        const n = parseInt(localStorage.getItem(_badgeKeyStaff) || '0', 10) || 0;
        setBadgeCount(n);
    } catch (e) { console.warn('loadBadgeCount error', e); }
}
function setBadgeCount(n) {
    const el = document.getElementById('proposalsBadge');
    if (!el) return;
    try {
        n = Math.max(0, parseInt(n || 0, 10));
        if (n > 0) { el.textContent = String(n); el.classList.remove('hidden'); }
        else { el.textContent = '0'; el.classList.add('hidden'); }
        localStorage.setItem(_badgeKeyStaff, String(n));
    } catch (e) { console.warn('setBadgeCount error', e); }
}
function incrementBadge() {
    try { const cur = parseInt(localStorage.getItem(_badgeKeyStaff) || '0',10) || 0; setBadgeCount(cur + 1); } catch(e){console.warn(e);} }
function clearBadge() { setBadgeCount(0); }

// helper: small visual toast for incoming notifications (staff)
function showStaffNotificationToast(title, body, opts = {}) {
    try {
        const containerId = 'notifToastContainerStaff';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'notif-toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = 'notif-toast notif-toast--new';
        toast.innerHTML = `<div class="notif-toast__title">${escapeHtml(title || 'Notification')}</div><div class="notif-toast__body">${escapeHtml(body || '')}</div>`;
        toast.onclick = () => {
            const rid = opts.requestId || opts.request_id || toast.getAttribute('data-request-id');
            if (rid) {
                try { jumpToRequest(parseInt(rid,10)); } catch(e) { console.warn('jump error', e); }
            }
            const dd = document.getElementById('staffNotifDropdown');
            if (dd) { dd.classList.remove('hidden'); }
            if (typeof window.fetchStaffNotifications === 'function') window.fetchStaffNotifications();
            const bell = document.getElementById('staffNotifBell'); if (bell) bell.focus();
            toast.remove();
        };
        if (opts.requestId) toast.setAttribute('data-request-id', String(opts.requestId));
        container.appendChild(toast);
        setTimeout(() => { try { toast.style.opacity = '0'; setTimeout(()=> toast.remove(), 250); } catch(e){} }, opts.duration || 6000);
    } catch (e) { console.warn('Toast error', e); }
}

// clear badge when user opens the proposals view
const proposalsLinkEl = document.getElementById('proposals-link');
if (proposalsLinkEl) proposalsLinkEl.addEventListener('click', () => { clearBadge(); });


    // No cross-tab localStorage sync — updates come from server (via periodic fetch or SSE).

function renderProposalsPage() {
    const proposalsManagementTableBody = document.getElementById('proposals-management-table-body');
    if (!proposalsManagementTableBody) return;
    // prefer server-provided proposals when available
    // If the client hasn't fetched server proposals yet, ask the server and show a loading row.
    if (!window._serverProposals) {
        proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-gray-500">Loading proposals…</td></tr>`;
        try { if (typeof fetchServerData === 'function') fetchServerData(); } catch (e) { console.warn('fetchServerData missing', e); }
        return;
    }
    let proposals = (Array.isArray(window._serverProposals)) ? window._serverProposals.slice() : [];
    
    // Filters have been removed from the UI; show all server proposals without additional client-side filtering.


    proposalsManagementTableBody.innerHTML = '';
    if (proposals.length === 0) {
      proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">No project proposals match the filter.</td></tr>`;
      return;
    }
    proposals.forEach((prop, index) => {
        const statusBadge = getStatusBadge(prop.status);
    const actionButton = `<button class="action-btn btn-details" onclick="openProposalDetails('${prop.id}')">View Details</button>`;
    // download link if attachment present
    const downloadLink = prop.attachment ? `<a class="text-sm text-blue-600 mr-3" href="${prop.attachment}" target="_blank" download>Download</a>` : '';
    const row = `<tr class="bg-white border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${prop.title}</th>
        <td class="px-6 py-4">${prop.barangay}</td><td class="px-6 py-4">${prop.date}</td>
        <td class="px-6 py-4">${prop.lastUpdated}</td><td class="px-6 py-4">${statusBadge}</td>
        <td class="px-6 py-4">${downloadLink}${actionButton}</td></tr>`;
        proposalsManagementTableBody.innerHTML += row;
    });
}


function getStatusBadge(status) {
    const colors = {
        'Approved': 'bg-green-100 text-green-800',
        'approved': 'bg-green-100 text-green-800',
        'Declined': 'bg-red-100 text-red-800',
        'declined': 'bg-red-100 text-red-800',
        'For Review': 'bg-blue-100 text-blue-800',
        'Processing': 'bg-indigo-100 text-indigo-800',
        'For Head Approval': 'bg-yellow-100 text-yellow-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'Requires Revision': 'bg-pink-100 text-pink-800',
    };
    const colorClass = colors[status] || 'bg-gray-100 text-gray-800';
    return `<span class="${colorClass} text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">${status}</span>`;
}

function updateTime() {
    const dateElement = document.getElementById('current-date');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    dateElement.textContent = new Date().toLocaleDateString('en-US', options);
}

// Fetch server-side data and cache it for rendering; fall back to localStorage if server unavailable.
async function fetchServerData() {
    // fetch requests (server-side)
    try {
        const res = await fetch('list_requests.php', { credentials: 'same-origin' });
        if (res.ok) {
            const data = await res.json();
            window._serverRequests = data.requests || [];
        }
    } catch (e) {
        console.warn('list_requests.php not available', e);
        window._serverRequests = null;
    }

    // fetch staff proposals (if available)
    try {
        const res2 = await fetch('list_staff_proposals.php', { credentials: 'same-origin' });
        if (res2.ok) {
            const d2 = await res2.json();
            // expect { proposals: [...] } or { success: true, proposals: [...] }
            window._serverProposals = d2.proposals || d2 || [];
        }
    } catch (e) {
        console.warn('list_staff_proposals.php not available', e);
        window._serverProposals = null;
    }

    // re-render views now that server data may be available
    if (typeof renderAllViews === 'function') renderAllViews();

    // Also fetch dashboard counts (server-side authoritative)
    if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();

    // refresh periodically
    setTimeout(fetchServerData, 60000);
}

// Fetch aggregate counts from server endpoint and update the dashboard cards.
async function fetchDashboardCounts(phase = 'all') {
    try {
        const url = 'dashboard_counts.php?phase=' + encodeURIComponent(phase);
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const d = await res.json();
        if (d) {
            if (typeof d.total_requests !== 'undefined') document.getElementById('total-requests-stat').textContent = d.total_requests;
            if (typeof d.pending_requests !== 'undefined') document.getElementById('pending-requests-stat').textContent = d.pending_requests;
            if (typeof d.total_accounts !== 'undefined') document.getElementById('total-accounts-stat').textContent = d.total_accounts;
            if (typeof d.pending_accounts !== 'undefined') document.getElementById('pending-accounts-stat').textContent = d.pending_accounts;
            return;
        }
    } catch (err) {
        // Silent fallback: if endpoint not available, the client-side rendering will still run
        console.warn('Could not fetch dashboard counts', err);
    }
}

function showAlert(title, message, callback) {
    const alertModal = document.getElementById('alertModal');
    document.getElementById('alertModalTitle').textContent = title;
    document.getElementById('alertModalMessage').textContent = message;
    const actions = document.getElementById('alertModalActions');
    actions.innerHTML = `<button id="alertOkBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">OK</button>`;
    openModal('alertModal');
    document.getElementById('alertOkBtn').onclick = () => {
        closeModal('alertModal');
        if (callback) callback();
    };
}

function showConfirm(title, message, callback, confirmLabel = 'Confirm', variant = 'primary') {
    const alertModal = document.getElementById('alertModal');
    document.getElementById('alertModalTitle').textContent = title;
    document.getElementById('alertModalMessage').textContent = message;
    const actions = document.getElementById('alertModalActions');
    const confirmBtnClasses = variant === 'danger'
        ? 'bg-red-600 hover:bg-red-700'
        : 'bg-blue-600 hover:bg-blue-700';
    actions.innerHTML = `
        <button id="confirmCancelBtn" class="mr-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
        <button id="confirmOkBtn" class="${confirmBtnClasses} text-white font-bold py-2 px-4 rounded">${confirmLabel}</button>`;
    openModal('alertModal');
    document.getElementById('confirmOkBtn').onclick = () => { closeModal('alertModal'); if (callback) callback(true); };
    document.getElementById('confirmCancelBtn').onclick = () => { closeModal('alertModal'); if (callback) callback(false); };
}

// --- Global Functions for button clicks (must be global for inline onclick) ---
async function updateAccountStatus(userId, newStatus) {
    try {
        const res = await fetch('update_account_status.php', { method: 'POST', body: new URLSearchParams({ user_id: userId, status: newStatus }), credentials: 'same-origin' });
        const d = await res.json();
        if (d && d.success) {
            renderAllViews();
            showAlert('Success', 'Account status updated.');
            return;
        }
        showAlert('Error', d.message || 'Failed to update account');
    } catch (err) {
        showAlert('Error', 'Server error');
    }
}

function deleteAccount(userId) {
    showConfirm('Confirm Deletion', `Are you sure you want to delete the account?`, async (confirmed) => {
        if (!confirmed) return;
        try {
            const res = await fetch('delete_account.php', { method: 'POST', body: new URLSearchParams({ user_id: userId }), credentials: 'same-origin' });
            const d = await res.json();
            if (d && d.success) {
                renderAllViews();
                showAlert('Deleted', 'Account deleted.');
                return;
            }
            showAlert('Error', d.message || 'Failed to delete account');
        } catch (err) {
            showAlert('Error', 'Server error');
        }
    }, 'Delete', 'danger');
}

// Edit modal helpers
function openEditAccountFromEncoded(id, bEnc, rEnc, eEnc) {
    const barangay = decodeURIComponent(bEnc || '');
    const rep = decodeURIComponent(rEnc || '');
    const email = decodeURIComponent(eEnc || '');
    const idInput = document.getElementById('editUserId');
    const bSel = document.getElementById('editBarangayName');
    const repInput = document.getElementById('editRepresentative');
    const emailInput = document.getElementById('editEmail');
    const passInput = document.getElementById('editPassword');
    idInput.value = id;
    repInput.value = rep;
    emailInput.value = email;
    passInput.value = '';
    if (bSel) {
        bSel.value = barangay || '';
    }
    openModal('editAccountModal');
}

document.getElementById('closeEditAccountModalBtn') && document.getElementById('closeEditAccountModalBtn').addEventListener('click', ()=> closeModal('editAccountModal'));
document.getElementById('editBarangayAccountForm') && document.getElementById('editBarangayAccountForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const id = document.getElementById('editUserId').value;
    const barangayName = document.getElementById('editBarangayName').value;
    const name = document.getElementById('editRepresentative').value;
    const email = document.getElementById('editEmail').value;
    const password = document.getElementById('editPassword').value;
    const params = new URLSearchParams({ user_id: id, barangayName, name, email });
    if (password && password.trim() !== '') params.append('password', password);
    try {
        const res = await fetch('update_barangay_account.php', { method: 'POST', body: params, credentials: 'same-origin' });
        const d = await res.json();
        if (d && d.success) {
            closeModal('editAccountModal');
            renderAllViews();
            showAlert('Saved', 'Account updated successfully.');
            return;
        }
        showAlert('Error', (d && d.message) || 'Failed to update account.');
    } catch (err) {
        showAlert('Error', 'Server error.');
    }
});

function openProposalDetails(proposalId) {
    const proposalDetailModal = document.getElementById('proposalDetailModal');
    // prefer server-provided proposals when available
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];
    const proposal = proposals.find(p => String(p.id) == String(proposalId));
    if (!proposal) return;

    document.getElementById('proposalId').value = proposal.id;
    document.getElementById('detail-title').textContent = proposal.title;
    document.getElementById('detail-barangay').textContent = proposal.barangay;
    document.getElementById('detail-date').textContent = proposal.date;
    document.getElementById('detail-status').innerHTML = getStatusBadge(proposal.status);
    document.getElementById('proposalStatus').value = proposal.status;

    const historyList = document.getElementById('proposal-history-list');
    historyList.innerHTML = '';
    proposal.history.slice().reverse().forEach(entry => {
        const remarksHtml = entry.remarks ? `<p class="text-xs text-gray-500 mt-1 pl-4 border-l-2 border-gray-300">"${entry.remarks}"</p>` : '';
        historyList.innerHTML += `<div class="text-sm"><p><span class="font-semibold">${entry.user}</span> updated status to <span class="font-semibold">${entry.status}</span> on ${new Date(entry.date).toLocaleDateString()}</p>${remarksHtml}</div>`;
    });
    
    openModal('proposalDetailModal');
    
    document.getElementById('closeDetailModalBtn').onclick = () => closeModal('proposalDetailModal');
    
    document.getElementById('proposalUpdateForm').onsubmit = e => {
        e.preventDefault();
        const newStatus = document.getElementById('proposalStatus').value;
        const remarks = document.getElementById('remarks').value;
        updateProposalStatus(proposalId, newStatus, remarks);
        closeModal('proposalDetailModal');
    };
}

function updateProposalStatus(proposalId, newStatus, remarks) {
    // Try server-side update first, if available
    fetch('update_proposal.php', { method: 'POST', body: new URLSearchParams({ id: proposalId, status: newStatus, remarks: remarks || '' }), credentials: 'same-origin' })
    .then(r => r.json())
    .then(d => {
        if (d && d.success) {
            // server updated, re-fetch server data later
            if (typeof fetchServerData === 'function') fetchServerData();
            renderAllViews();
            return;
        }
        // Do NOT fall back to localStorage. Inform user to retry.
        showAlert('Error', d.message || 'Failed to update proposal. Please try again.');
    }).catch(err => {
        // Server unavailable — do not persist locally. Notify user.
        console.error(err);
        showAlert('Error', 'Server error — could not update proposal. Try again later.');
    });
}

// Update request status (used for barangay requests). Available to staff/head.
async function updateRequestStatus(requestId, newStatus) {
    try {
        const form = new URLSearchParams({ id: requestId, status: newStatus });
        const res = await fetch('update_request_status.php', { method: 'POST', body: form, credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const d = await res.json();
        if (d && d.status) {
            // re-fetch server data
            if (typeof fetchServerData === 'function') fetchServerData();
            renderAllViews();
            showAlert('Success', `Request ${requestId} marked ${d.status}`);
            return;
        }
        showAlert('Error', 'Failed to update request');
    } catch (err) {
        console.error(err);
        showAlert('Error', 'Server error');
    }
}

// Confirmation wrapper used by action buttons
function confirmUpdateRequestStatus(requestId, newStatus) {
    showConfirm('Confirm Action', `Are you sure you want to mark request ${requestId} as ${newStatus}?`, async (confirmed) => {
        if (!confirmed) return;
        await updateRequestStatus(requestId, newStatus);
    }, 'Confirm', 'primary');
}

// Delete a request (only shown when status is Approved or Declined)
function confirmDeleteRequest(requestId) {
    showConfirm('Confirm Deletion', `Do you really want to delete request ${requestId}? This cannot be undone.`, async (confirmed) => {
        if (!confirmed) return;
        try {
            const res = await fetch('delete_request.php', { method: 'POST', body: new URLSearchParams({ id: requestId }), credentials: 'same-origin' });
            const d = await res.json();
            if (d && d.success) {
                if (typeof fetchServerData === 'function') fetchServerData();
                renderAllViews();
                showAlert('Deleted', `Request ${requestId} has been deleted.`);
                return;
            }
            showAlert('Error', (d && d.error) || 'Failed to delete request.');
        } catch (err) {
            showAlert('Error', 'Server error.');
        }
    }, 'Delete', 'danger');
}

// SSE: subscribe to notifications stream so staff dashboard refreshes when relevant notifications arrive
try {
    const staffSse = new EventSource('notifications_stream.php');
    staffSse.addEventListener('notification', (e) => {
        try {
            const payload = JSON.parse(e.data || '{}');
            // increment the "New submissions" badge when notification targets staff
            try { if (payload && (payload.target_role === 'OPMDC Staff' || payload.target_user_id)) { incrementBadge(); try { showStaffNotificationToast(payload.title || 'New notification', payload.body || '', { requestId: payload.request_id }); } catch(e){} } } catch (ee) { }
            // If notification targets staff and contains a request_id (proposal id), fetch that single proposal
            if (payload && payload.request_id) {
                const pid = parseInt(payload.request_id, 10);
                if (!isNaN(pid)) {
                    // Fetch only the new proposal and highlight it in the UI
                    fetch(`list_staff_proposals.php?id=${encodeURIComponent(pid)}`, { credentials: 'same-origin' })
                        .then(r => r.json())
                        .then(data => {
                            let proposal = null;
                            if (data && data.proposals && Array.isArray(data.proposals) && data.proposals.length) proposal = data.proposals[0];
                            else if (Array.isArray(data) && data.length) proposal = data[0];
                            if (proposal) {
                                // add to client cache (so filters and other renderers see it)
                                window._serverProposals = window._serverProposals || [];
                                const exists = window._serverProposals.find(p => Number(p.id) === Number(proposal.id));
                                if (!exists) window._serverProposals.unshift(proposal);
                                // highlight in the mini recent submissions table and refresh proposals view
                                try { highlightNewProposal(proposal); } catch (err) { console.error('Highlight failed', err); }
                                try { if (typeof renderProposalsPage === 'function') renderProposalsPage(); } catch (e) {}
                                return;
                            }
                            // fallback to full refresh if we couldn't get the single proposal
                            if (typeof fetchServerData === 'function') fetchServerData();
                        }).catch(err => {
                            console.error('Failed to fetch single proposal', err);
                            if (typeof fetchServerData === 'function') fetchServerData();
                        });
                    return;
                }
            }

            // otherwise fallback to the previous behavior: refresh server cache
            if (!payload.target_role || payload.target_role === 'OPMDC Staff' || payload.target_user_id) {
                if (typeof fetchServerData === 'function') fetchServerData();
            }
        } catch (err) { console.error('Invalid SSE payload', err); }
    });
    staffSse.addEventListener('error', (err) => { console.warn('SSE error', err); staffSse.close(); });
} catch (e) { console.warn('SSE not available', e); }

/* --- Manage My Submissions (staff) --- */
function loadMyProposals() {
    // Try server endpoint first
    fetch('list_staff_proposals.php')
    .then(r => r.json())
    .then(data => {
        if (data && Array.isArray(data.proposals)) {
            renderMyProposalsTable(data.proposals);
        } else if (Array.isArray(data)) {
            renderMyProposalsTable(data);
        } else {
            // No local fallback; show empty list
            renderMyProposalsTable([]);
        }
    }).catch(err => {
        console.error(err);
        renderMyProposalsTable([]);
    });
}

function renderMyProposalsTable(proposals) {
    const tbody = document.getElementById('my-proposals-table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (proposals.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No submissions found.</td></tr>`;
        return;
    }
    proposals.forEach((p, index) => {
        const downloadAnchor = p.attachment ? `<a href="${p.attachment}" target="_blank" download class="text-sm text-blue-600 mr-2">Download</a>` : '';
        const editBtn = (p.status !== 'Approved' && p.status !== 'Declined') ? `<button class="action-btn btn-update" onclick="openEditSubmission('${p.id}')">Edit / Resubmit</button>` : '';
        const viewBtn = `<button class="action-btn btn-details" onclick="openProposalDetails('${p.id}')">View</button>`;
        const row = `
            <tr class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                <td class="py-4 px-4">${p.id}</td>
                <td class="py-4 px-4">${p.title}</td>
                <td class="py-4 px-4">${new Date(p.date).toLocaleDateString()}</td>
                <td class="py-4 px-4">${getStatusBadge(p.status)}</td>
                <td class="py-4 px-4">${downloadAnchor}${editBtn}${viewBtn}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function openEditSubmission(proposalId) {
    // Try to get from server; fallback to localStorage
    fetch(`list_staff_proposals.php?id=${encodeURIComponent(proposalId)}`)
    .then(r => r.json())
    .then(data => {
        let proposal = null;
        if (data && data.proposals && Array.isArray(data.proposals) && data.proposals.length) proposal = data.proposals[0];
        if (!proposal && Array.isArray(data) && data.length) proposal = data[0];
        if (!proposal) {
            // Do not use localStorage fallback — show an error
            return showAlert('Error', 'Proposal not found on server.');
        }
        if (!proposal) return showAlert('Error', 'Proposal not found');
        document.getElementById('editProposalId').value = proposal.id;
        document.getElementById('editTitle').value = proposal.title || '';
        document.getElementById('editDescription').value = proposal.description || '';
        document.getElementById('editBudget').value = proposal.budget || '';
        document.getElementById('editAttachment').value = proposal.attachment || '';
        openModal('editSubmissionModal');
        document.getElementById('closeEditSubmissionBtn').onclick = () => closeModal('editSubmissionModal');
    }).catch(err => { console.error(err); showAlert('Error', 'Server error'); });
}

document.getElementById('editSubmissionForm') && document.getElementById('editSubmissionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const payload = {
        id: fd.get('proposalId'),
        title: fd.get('title'),
        description: fd.get('description'),
        budget: fd.get('budget'),
        attachment: fd.get('attachment')
    };

    // POST to update_proposal.php
    fetch('update_proposal.php', { method: 'POST', body: new URLSearchParams(payload) })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            showAlert('Success', 'Proposal updated and resubmitted.');
            closeModal('editSubmissionModal');
            if (typeof fetchServerData === 'function') fetchServerData();
            renderAllViews();
            return;
        }
    }).catch(err => {
        console.error(err);
        showAlert('Error', 'Server error — could not update proposal. Please try again later.');
        closeModal('editSubmissionModal');
        renderAllViews();
    });
});

// --- Server notifications (runs after DOM is ready) ---
document.addEventListener('DOMContentLoaded', () => {
    const staffBell = document.getElementById('staffNotifBell');
    const staffBadge = document.getElementById('staffNotifBadge');
    const staffDropdown = document.getElementById('staffNotifDropdown');
    const staffList = document.getElementById('staffNotifList');
    const staffMarkAllRead = document.getElementById('staffMarkAllRead');

    async function fetchStaffNotifications() {
        try {
            const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Staff'));
            if (!res.ok) throw new Error('Network');
            const data = await res.json();
            renderStaffNotifs(data.notifications || []);
        } catch (err) {
            staffList.innerHTML = '<div class="text-center text-gray-500">Could not load notifications</div>';
        }
    }

    function renderStaffNotifs(notes) {
        if (!notes.length) {
            staffList.innerHTML = '<div class="text-center text-gray-500">No notifications.</div>';
            staffBadge.classList.add('hidden');
            return;
        }
        const unread = notes.filter(n => n.is_read == 0).length;
        if (unread > 0) staffBadge.classList.remove('hidden'); else staffBadge.classList.add('hidden');
        staffList.innerHTML = '';
        notes.forEach(n => {
            const div = document.createElement('div');
            div.className = 'p-2 border-b';
            div.innerHTML = `<div class="flex justify-between"><div><strong>${escapeHtml(n.title)}</strong><div class="text-xs text-gray-600">${escapeHtml(n.body)}</div><div class="text-xs text-gray-400 mt-1">${n.created_at}</div></div><div class="flex flex-col items-end space-y-1"><button class="mark-read text-xs text-blue-600" data-id="${n.id}">${n.is_read==0? 'Mark read':'Unread'}</button><button class="delete text-xs text-red-600" data-id="${n.id}">Delete</button></div></div>`;
            staffList.appendChild(div);
        });
        staffList.querySelectorAll('.mark-read').forEach(btn => btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            await fetch('notifications_mark_read.php?id=' + encodeURIComponent(id), { method: 'POST' });
            fetchStaffNotifications();
        }));
        staffList.querySelectorAll('.delete').forEach(btn => btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            await fetch('notifications_delete.php?id=' + encodeURIComponent(id), { method: 'POST' });
            fetchStaffNotifications();
        }));
    }

    staffBell && staffBell.addEventListener('click', (e) => { e.stopPropagation(); staffDropdown.classList.toggle('hidden'); if (!staffDropdown.classList.contains('hidden')) fetchStaffNotifications(); });
    document.addEventListener('click', () => staffDropdown.classList.add('hidden'));

    staffMarkAllRead && staffMarkAllRead.addEventListener('click', async () => {
        try {
            const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Staff'));
            const data = await res.json();
            for (const n of data.notifications || []) {
                await fetch('notifications_mark_read.php?id=' + encodeURIComponent(n.id), { method: 'POST' });
            }
            fetchStaffNotifications();
        } catch (err) { console.error(err); }
    });

    function escapeHtml(s) { return String(s).replace(/[&<>\"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }

    // initial
    fetchStaffNotifications();
});
</script>
<script>
// Analytics charts (Chart.js) — loaded on-demand when Analytics view is shown
let proposalsChart = null;
let barangayChart = null;
let approvalChart = null;
let avgTurnaroundChart = null;

async function fetchAnalytics() {
    try {
        // read date inputs when present
        const start = document.getElementById('analyticsStart') ? document.getElementById('analyticsStart').value : '';
        const end = document.getElementById('analyticsEnd') ? document.getElementById('analyticsEnd').value : '';
        let url = 'analytics_api.php';
        const params = [];
        if (start) params.push('start=' + encodeURIComponent(start));
        if (end) params.push('end=' + encodeURIComponent(end));
        if (params.length) url += '?' + params.join('&');
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        renderAnalytics(data);
    } catch (err) {
        console.warn('Could not fetch analytics', err);
        const el = document.getElementById('analyticsInsights');
        if (el) el.textContent = 'Could not load analytics.';
    }
}

function renderAnalytics(d) {
    try {
        // Proposals timeseries
        const pctx = document.getElementById('proposalsChart');
        const labels = (d.proposals_timeseries && d.proposals_timeseries.labels) ? d.proposals_timeseries.labels.map(l => {
            // format YYYY-MM to MMM YYYY
            const parts = l.split('-');
            const dt = new Date(parts[0], parseInt(parts[1],10)-1, 1);
            return dt.toLocaleString('en-US', { month: 'short', year: 'numeric' });
        }) : [];
        const pdata = (d.proposals_timeseries && d.proposals_timeseries.data) ? d.proposals_timeseries.data : [];

        if (pctx) {
            if (proposalsChart) {
                proposalsChart.data.labels = labels;
                proposalsChart.data.datasets[0].data = pdata;
                proposalsChart.update();
            } else {
                proposalsChart = new Chart(pctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{ label: 'Proposals (selected range)', data: pdata, borderColor: '#0072B2', backgroundColor: 'rgba(0,114,178,0.12)', tension: 0.3, pointRadius: 4 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            }
        }

        // Barangay chart
        const bctx = document.getElementById('barangayChart');
        const bLabels = (d.requests_by_barangay || []).map(x => x.barangay || 'Unknown');
        const bData = (d.requests_by_barangay || []).map(x => x.count || 0);
            if (bctx) {
                if (barangayChart) {
                    barangayChart.data.labels = bLabels;
                    barangayChart.data.datasets[0].data = bData;
                    barangayChart.update();
                    } else {
                    // Colorblind-friendly palette (ColorBrewer / CB-friendly mix)
                    const palette = ['#0072B2','#D55E00','#009E73','#F0E442','#CC79A7','#56B4E9','#E69F00','#999999','#8DA0CB','#A6CEE3'];
                    const colors = bLabels.map((_, i) => palette[i % palette.length]);
                    barangayChart = new Chart(bctx, {
                        type: 'bar',
                        data: { labels: bLabels, datasets: [{ label: 'Requests', data: bData, backgroundColor: colors }] },
                        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
                    });
                }
            }

        // store last analytics for exports
        window._lastAnalytics = d;

        // Approval funnel (d.approval_funnel expected as object)
        try {
            const afCtx = document.getElementById('approvalFunnel');
            const funnel = d.approval_funnel || {};
            const fLabels = Object.keys(funnel);
            const fData = fLabels.map(l => funnel[l] || 0);
            if (afCtx) {
                if (approvalChart) {
                    approvalChart.data.labels = fLabels;
                    approvalChart.data.datasets[0].data = fData;
                    approvalChart.update();
                } else {
                    // Map common statuses to accessible colors
                    const statusColorMap = { 'Approved':'#009E73', 'Declined':'#D55E00', 'For Review':'#0072B2', 'Processing':'#56B4E9', 'Requires Revision':'#CC79A7', 'Pending':'#999999' };
                    const fColors = fLabels.map(l => statusColorMap[l] || '#8DA0CB');
                    approvalChart = new Chart(afCtx, {
                        type: 'doughnut',
                        data: { labels: fLabels, datasets: [{ data: fData, backgroundColor: fColors }] },
                        options: { responsive: true, plugins: { legend: { position: 'right' } } }
                    });
                }
            }
        } catch (e) { console.warn('approval funnel render error', e); }

        // Avg turnaround series
        try {
            const atCtx = document.getElementById('avgTurnaroundChart');
            const atLabels = (d.avg_turnaround_series && d.avg_turnaround_series.labels) ? d.avg_turnaround_series.labels.map(l => { const parts = l.split('-'); const dt = new Date(parts[0], parseInt(parts[1],10)-1, 1); return dt.toLocaleString('en-US', { month: 'short', year: 'numeric' }); }) : [];
            const atData = (d.avg_turnaround_series && d.avg_turnaround_series.data) ? d.avg_turnaround_series.data : [];
            if (atCtx) {
                if (avgTurnaroundChart) {
                    avgTurnaroundChart.data.labels = atLabels;
                    avgTurnaroundChart.data.datasets[0].data = atData;
                    avgTurnaroundChart.update();
                } else {
                    avgTurnaroundChart = new Chart(atCtx, {
                        type: 'line',
                        data: { labels: atLabels, datasets: [{ label: 'Avg Days', data: atData, borderColor: '#56B4E9', backgroundColor: 'rgba(86,180,233,0.12)', tension: 0.3, pointRadius: 3 }] },
                        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { title: { display: true, text: 'Days' } } } }
                    });
                }
            }
        } catch (e) { console.warn('avg turnaround render error', e); }

        // Heatmap: render a simple table colored by intensity
        try {
            const heatEl = document.getElementById('heatmapContainer');
            const heat = d.barangay_heatmap || { barangays: [], months: [], matrix: [] };
            if (heatEl) {
                heatEl.innerHTML = '';
                const bars = heat.barangays || [];
                const months = heat.months || [];
                const matrix = heat.matrix || [];
                if (!bars.length || !months.length) {
                    heatEl.innerHTML = '<div class="text-sm text-gray-500">No heatmap data.</div>';
                } else {
                    // compute max for normalization
                    let max = 0;
                    matrix.forEach(row => row.forEach(v => { if (v > max) max = v; }));
                    const table = document.createElement('table');
                    table.className = 'w-full text-sm border-collapse';
                    const thead = document.createElement('thead');
                    const thr = document.createElement('tr');
                    thr.innerHTML = '<th class="p-1"></th>' + months.map(m=>`<th class="p-1 text-xs">${m}</th>`).join('');
                    thead.appendChild(thr); table.appendChild(thead);
                    const tbody = document.createElement('tbody');
                    bars.forEach((b, i) => {
                        const tr = document.createElement('tr');
                        const nameTd = document.createElement('td'); nameTd.className='p-1 font-medium'; nameTd.textContent = b;
                        tr.appendChild(nameTd);
                        (matrix[i] || []).forEach(val => {
                            const td = document.createElement('td'); td.className='p-1';
                            const intensity = max > 0 ? Math.round((val / max) * 240) : 0; // 0-240
                            td.style.background = `hsl(220, 60%, ${100 - intensity/3}%)`;
                            td.textContent = val > 0 ? val : '';
                            tr.appendChild(td);
                        });
                        tbody.appendChild(tr);
                    });
                    table.appendChild(tbody);
                    heatEl.appendChild(table);
                }
            }
        } catch (e) { console.warn('heatmap render error', e); }

        // Insights (structured objects with severity) — render as table for readability
        const insightsEl = document.getElementById('analyticsInsights');
        if (insightsEl) {
            insightsEl.innerHTML = '';
            if (Array.isArray(d.insights) && d.insights.length) {
                const table = document.createElement('table');
                table.className = 'w-full text-sm border-collapse';
                table.setAttribute('role', 'table');

                const thead = document.createElement('thead');
                const htr = document.createElement('tr');
                htr.innerHTML = `
                    <th class="text-left p-2 text-xs text-slate-600">Severity</th>
                    <th class="text-left p-2 text-sm text-slate-700">Message</th>
                    <th class="text-left p-2 text-xs text-slate-500">Code</th>
                `;
                thead.appendChild(htr);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                // helper to build a tooltip/detail string for each insight
                const getInsightDetail = (insight, analytics) => {
                    try {
                        const code = (typeof insight === 'string') ? '' : (insight.code || '');
                        if (code === 'TOP_BARANGAY') {
                            const top = (analytics.requests_by_barangay && analytics.requests_by_barangay[0]) || null;
                            const total = (analytics.counts && analytics.counts.total_requests) || 0;
                            if (top) return `${top.barangay}: ${top.count} requests (${total ? Math.round((top.count/total)*100*10)/10 : 0}% of total)`;
                        }
                        if (code === 'PENDING_REQUESTS') {
                            const pr = (analytics.counts && analytics.counts.pending_requests) || 0;
                            return `Pending requests: ${pr} (Low:0-5, Medium:6-40, High:>40)`;
                        }
                        if (code === 'PENDING_PROPOSALS') {
                            const pp = (analytics.counts && analytics.counts.pending_proposals) || 0;
                            return `Pending proposals: ${pp} (Low:<=8, Medium:9-20, High:>20)`;
                        }
                        if (code === 'PROPOSAL_SURGE' || code === 'PROPOSAL_INCREASE' || code === 'PROPOSAL_CHANGE') {
                            const series = (analytics.proposals_timeseries && analytics.proposals_timeseries.data) || [];
                            if (series.length >= 6) {
                                const last3 = series.slice(-3).reduce((a,b)=>a+b,0);
                                const prev3 = series.slice(-6,-3).reduce((a,b)=>a+b,0);
                                const pct = prev3 ? Math.round(((last3-prev3)/prev3)*100*10)/10 : (last3>0?100:0);
                                return `Last3: ${last3}, Prev3: ${prev3}, Change: ${pct}%`;
                            }
                        }
                        if (code === 'APPROVAL_RATE') {
                            const pbs = analytics.proposals_by_status || {};
                            const approved = parseInt(pbs['Approved'] || pbs['approved'] || 0,10);
                            const total = Object.values(pbs).reduce((a,b)=>a+parseInt(b||0,10),0);
                            const rate = total ? Math.round((approved/total)*100*10)/10 : 0;
                            return `Approved: ${approved}, Total: ${total}, Approval rate: ${rate}%`;
                        }
                        if (code === 'AVG_TURNAROUND') {
                            const series = (analytics.avg_turnaround_series && analytics.avg_turnaround_series.data) || [];
                            if (series.length) {
                                const avg = Math.round((series.reduce((a,b)=>a+ (parseFloat(b)||0),0)/series.length)*10)/10;
                                return `Avg turnaround (recent months avg): ${avg} days`;
                            }
                        }
                        if (code === 'OLD_PENDING') {
                            return `Old pending requests: see server details (older than 30 days)`;
                        }
                        // fallback: show raw message
                        return (typeof insight === 'string') ? insight : (insight.message || 'No additional details');
                    } catch (e) { return insight.message || String(insight); }
                };

                d.insights.forEach(i => {
                    const msg = (typeof i === 'string') ? i : (i.message || '');
                    const sev = (typeof i === 'string') ? 'low' : (i.severity || 'low');
                    const code = (typeof i === 'string') ? '' : (i.code || '');

                    const tr = document.createElement('tr');
                    tr.className = 'border-t';

                    const sevTd = document.createElement('td');
                    sevTd.className = 'p-2 align-top';
                    const badgeWrap = document.createElement('div');
                    badgeWrap.style.display = 'flex'; badgeWrap.style.alignItems = 'center'; badgeWrap.style.gap = '8px';
                    const badge = document.createElement('div');
                    badge.style.width = '12px'; badge.style.height = '12px'; badge.style.borderRadius = '9999px';
                    // Accessible severity colors
                    if (sev === 'high') badge.style.background = '#D55E00';
                    else if (sev === 'medium') badge.style.background = '#F0E442';
                    else badge.style.background = '#009E73';
                    const sevLabel = document.createElement('span');
                    sevLabel.className = 'text-xs font-semibold';
                    sevLabel.textContent = sev.charAt(0).toUpperCase() + sev.slice(1);
                    badgeWrap.appendChild(badge); badgeWrap.appendChild(sevLabel);
                    sevTd.appendChild(badgeWrap);

                    const msgTd = document.createElement('td');
                    msgTd.className = 'p-2 text-base text-slate-800 whitespace-normal break-words';
                    msgTd.textContent = msg;

                    const codeTd = document.createElement('td');
                    codeTd.className = 'p-2 text-xs text-slate-500 flex items-center gap-2';
                    codeTd.textContent = code;
                    // info icon with tooltip/detail
                    const infoBtn = document.createElement('button');
                    infoBtn.type = 'button';
                    infoBtn.className = 'text-xs text-slate-400 hover:text-slate-600';
                    infoBtn.style.border = 'none'; infoBtn.style.background = 'transparent'; infoBtn.style.cursor = 'pointer';
                    const detail = getInsightDetail(i, d);
                    infoBtn.title = 'View insight details';
                    infoBtn.setAttribute('aria-label', 'Insight details');
                    infoBtn.textContent = 'ℹ';
                    infoBtn.onclick = function(e) { e.stopPropagation(); if (typeof showInsightModal === 'function') showInsightModal(code || 'Insight Details', detail); else alert(detail); };
                    codeTd.appendChild(infoBtn);

                    tr.appendChild(sevTd); tr.appendChild(msgTd); tr.appendChild(codeTd);
                    tbody.appendChild(tr);
                });

                table.appendChild(tbody);
                insightsEl.appendChild(table);
            } else {
                const p = document.createElement('div');
                p.className = 'text-sm text-slate-500';
                p.textContent = 'No insights available.';
                insightsEl.appendChild(p);
            }
        }

        // wire export buttons (use last analytics)
        try {
            const csvBtn = document.getElementById('exportCsvBtn');
            const pdfBtn = document.getElementById('exportPdfBtn');
            if (csvBtn) csvBtn.onclick = () => exportAnalyticsCSV(window._lastAnalytics);
            if (pdfBtn) pdfBtn.onclick = () => exportAnalyticsPDF(window._lastAnalytics);
        } catch (e) { console.warn('export button bind error', e); }

    } catch (err) { console.error('renderAnalytics error', err); }
}

// wire the Refresh button after DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const refreshBtn = document.getElementById('analyticsRefresh');
    if (refreshBtn) refreshBtn.addEventListener('click', (e) => { e.preventDefault(); fetchAnalytics(); });
});

// Export analytics as CSV (client-side)
function exportAnalyticsCSV(data) {
    try {
        if (!data) return alert('No analytics data available to export.');
    const lines = [];
    lines.push('Proposals (Selected Range)');
        if (data.proposals_timeseries && Array.isArray(data.proposals_timeseries.labels)) {
            lines.push('Month,Count');
            for (let i = 0; i < data.proposals_timeseries.labels.length; i++) {
                lines.push(`${data.proposals_timeseries.labels[i]},${data.proposals_timeseries.data[i] || 0}`);
            }
            lines.push('');
        }

        lines.push('Requests by Barangay (Top 10)');
        lines.push('Barangay,Count');
        (data.requests_by_barangay || []).forEach(r => {
            lines.push(`${r.barangay || ''},${r.count || 0}`);
        });
        lines.push('');

        lines.push('Insights');
        lines.push('Severity,Message');
        (data.insights || []).forEach(i => {
            if (typeof i === 'string') lines.push(`low,${i}`);
            else lines.push(`${i.severity || 'low'},"${(i.message||'').replace(/"/g,'""')}"`);
        });

        // Approval funnel
        lines.push('');
        lines.push('Approval Funnel');
        lines.push('Status,Count');
        if (data.approval_funnel) {
            Object.keys(data.approval_funnel).forEach(k => { lines.push(`${k},${data.approval_funnel[k] || 0}`); });
        }

        // Avg turnaround series
        lines.push('');
        lines.push('Avg Turnaround');
        lines.push('Month,AvgDays');
        if (data.avg_turnaround_series && Array.isArray(data.avg_turnaround_series.labels)) {
            for (let i = 0; i < data.avg_turnaround_series.labels.length; i++) {
                lines.push(`${data.avg_turnaround_series.labels[i]},${data.avg_turnaround_series.data[i] || 0}`);
            }
        }

        // Heatmap
        lines.push('');
        lines.push('Barangay Heatmap');
        if (data.barangay_heatmap && Array.isArray(data.barangay_heatmap.barangays)) {
            const months = data.barangay_heatmap.months || [];
            lines.push(['Barangay'].concat(months).join(','));
            (data.barangay_heatmap.barangays || []).forEach((b, idx) => {
                const row = [b].concat((data.barangay_heatmap.matrix[idx] || []).map(v => v || 0));
                lines.push(row.join(','));
            });
        }

        const csv = lines.join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const now = new Date();
        const fname = `opmdc-analytics-${now.getFullYear()}${(now.getMonth()+1).toString().padStart(2,'0')}${now.getDate().toString().padStart(2,'0')}.csv`;
        a.download = fname;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    } catch (err) { console.error('exportAnalyticsCSV error', err); alert('Export failed'); }
}

// Export charts and insights as a PDF using jsPDF
async function exportAnalyticsPDF(data) {
    try {
        if (!data) return alert('No analytics data available to export.');
        const { jsPDF } = window.jspdf || {};
        if (!jsPDF) return alert('PDF library not available.');
        const doc = new jsPDF({ orientation: 'landscape' });
        const title = 'OPMDC Analytics Report';
        doc.setFontSize(16);
        doc.text(title, 14, 20);

        // Add proposals chart image
        if (proposalsChart && proposalsChart.canvas) {
            const img1 = proposalsChart.canvas.toDataURL('image/png', 1.0);
            doc.addImage(img1, 'PNG', 14, 28, 120, 60);
        }
        // Add barangay chart image
        if (barangayChart && barangayChart.canvas) {
            const img2 = barangayChart.canvas.toDataURL('image/png', 1.0);
            doc.addImage(img2, 'PNG', 140, 28, 120, 60);
        }

        // Add insights as text
        doc.setFontSize(12);
        let y = 100;
        doc.text('Insights:', 14, y);
        y += 6;
        (data.insights || []).forEach(i => {
            const msg = typeof i === 'string' ? i : (i.message || '');
            const lines = doc.splitTextToSize('- ' + msg, 260);
            doc.text(lines, 14, y);
            y += lines.length * 6 + 2;
            if (y > 180) { doc.addPage(); y = 20; }
        });

        const now = new Date();
        const fname = `opmdc-analytics-${now.getFullYear()}${(now.getMonth()+1).toString().padStart(2,'0')}${now.getDate().toString().padStart(2,'0')}.pdf`;
        doc.save(fname);
    } catch (err) { console.error('exportAnalyticsPDF error', err); alert('PDF export failed'); }
}
// Show insight detail modal with formatted text
function showInsightModal(title, body) {
    try {
        const t = document.getElementById('insightDetailTitle');
        const b = document.getElementById('insightDetailBody');
        if (t) t.textContent = title || 'Insight Details';
        if (b) b.textContent = body || '';
        openModal('insightDetailModal');
    } catch (e) { alert(body || title || 'Details'); }
}

// wire close button for insight modal
document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.getElementById('closeInsightModalBtn');
    if (closeBtn) closeBtn.addEventListener('click', () => closeModal('insightDetailModal'));
});
</script>
<!-- Toast container for staff (created dynamically if needed) -->
<div id="notifToastContainerStaff" class="notif-toast-container" aria-live="polite"></div>
</body>
</html>
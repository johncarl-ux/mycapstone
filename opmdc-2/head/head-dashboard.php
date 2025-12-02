<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPMDC Head Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { 
            --brand-bg:#F8FAFC; 
            --brand-accent:#2563EB; 
            --brand-accent-2:#3B82F6; 
            --card-border:#E2E8F0; 
            --ink-1:#0F172A; 
            --ink-2:#334155; 
            --ink-3:#64748B;
            --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #94a3b8 0%, #64748b 100%); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #64748b 0%, #475569 100%); }
        
        /* Logo formal and clean */
        .logo-formal {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: #fff;
            display: block;
            object-fit: cover;
            margin: 0 auto 0.75rem;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 3px;
            transition: all 0.3s ease;
        }
        
        .logo-formal:hover { 
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        @keyframes logo-pop {
            0% { transform: translateY(8px) scale(0.96); opacity: 0; }
            60% { transform: translateY(-2px) scale(1.02); opacity: 1; }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        .page-content {
            display: none;
        }
        .active-nav-link {
            background-color: #334155 !important;
            color: #FFFFFF;
        }
        
        /* Enhanced Card Styles */
        .stat-card,
        .bg-white {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.75rem;
        }
        
        .bg-white:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }
        
        /* Table Enhancements */
        tbody tr {
            transition: all 0.2s ease;
        }
        
        tbody tr:hover {
            background: linear-gradient(90deg, #F8FAFC 0%, #F1F5F9 100%) !important;
        }
        
        /* Button Enhancements */
        button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        button:hover {
            transform: translateY(-1px);
        }
        
        button:active {
            transform: translateY(0);
        }
        /* simple table styling for inserted descriptive section */
        .desc-table td, .desc-table th { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; }
        .desc-summary { color:#374151; margin-bottom:8px }
        /* reveal CSS moved to assets/ui-animations.css */
        /* Notification toast styles */
        .notif-toast-container { position: fixed; right: 1rem; bottom: 1rem; z-index: 60; display:flex; flex-direction:column; gap:0.5rem; }
        .notif-toast { background: white; border-radius: 0.5rem; box-shadow: 0 6px 18px rgba(0,0,0,0.12); padding: 0.75rem 1rem; width: 320px; cursor: pointer; transition: transform 0.15s ease, opacity 0.2s ease; }
        .notif-toast:hover { transform: translateY(-4px); }
        .notif-toast__title { font-weight: 600; color: #1F2937; }
        .notif-toast__body { font-size: 0.85rem; color: #4B5563; margin-top: 0.25rem; }
        .notif-toast--new { border-left: 4px solid #3B82F6; }
        .highlight-request { box-shadow: 0 8px 30px rgba(59,130,246,0.12); border-radius: 8px; animation: highlightFade 3s ease forwards; }
        @keyframes highlightFade { 0% { background: #fef3c7; } 100% { background: transparent; } }
        
        /* Notification Dot Animation */
        .notification-dot {
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Actionable Insights Table Enhancements */
        #analyticsInsights table {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        #analyticsInsights table thead th {
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        #analyticsInsights table tbody tr {
            transition: all 0.15s ease-in-out;
        }
        
        #analyticsInsights table tbody tr:last-child td {
            border-bottom: none;
        }
        
        #analyticsInsights .inline-flex:hover {
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }
        
        /* Loading spinner animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Analytics Chart Card Enhancements */
        #analytics .bg-white {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        #analytics .bg-white:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        /* Chart canvas styling */
        canvas {
            max-width: 100%;
            height: auto !important;
        }
        
        /* Date input styling for analytics */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: brightness(0) invert(1);
            cursor: pointer;
        }
        
        /* Grid gap consistency */
        .grid {
            gap: 1.5rem;
        }

        /* Table & action refinements: keep markup unchanged, improve visual affordances */
        .container { max-width: 1200px; }
        table.w-full { border-collapse: separate; border-spacing: 0; background: transparent; }
        table.w-full thead th { background: none; color: var(--ink-2); font-weight: 600; padding: 12px 16px; text-align: left; }
        table.w-full tbody td { padding: 12px 16px; color: var(--ink-1); vertical-align: middle; }
        table.w-full tbody tr { border-bottom: 1px solid var(--card-border); }
        table.w-full tbody tr:last-child { border-bottom: none; }

        /* Make inline action links & buttons look like compact pills (no JS changes) */
        table.w-full a, table.w-full button { display: inline-block; padding: 6px 10px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.85rem; border: 1px solid transparent; transition: all 160ms ease; }
        table.w-full a { background: transparent; }
        table.w-full a.text-blue-600 { color: var(--brand-accent-2); background: rgba(59,130,246,0.06); border-color: rgba(59,130,246,0.12); }
        table.w-full button.text-green-600 { color: #065f46; background: #ecfdf5; border-color: #bbf7d0; }
        table.w-full button.text-yellow-600 { color: #92400e; background: #fff7ed; border-color: #ffd8a8; }
        table.w-full button.text-red-600 { color: #7f1d1d; background: #fff1f2; border-color: #fbcaca; }
        table.w-full a:hover, table.w-full button:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(2,6,23,0.06); }

        /* Submission preview polish */
        .submission-preview { width: 360px; max-height: 70vh; padding: 0; }
        .submission-preview__header { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; background: linear-gradient(90deg, #ffffff 0%, #f8fafc 100%); }
        .submission-preview__body { padding: 14px 16px; overflow:auto; max-height: 340px; color: var(--ink-2); font-size: 0.95rem; }
        .submission-preview__actions { padding: 12px 14px; border-top: 1px solid #f3f4f6; display:flex; justify-content:flex-end; gap:8px; background: #fff; }
        .submission-preview__actions button { padding: 8px 12px; border-radius: 999px; font-weight: 700; font-size: 0.85rem; }
        .submission-preview__actions button#pv_view_btn { color: var(--brand-accent-2); background: transparent; border: 1px solid rgba(59,130,246,0.08); }
        .submission-preview__actions button#pv_approve_btn { color: #065f46; background: #ecfdf5; border: 1px solid #bbf7d0; }
        .submission-preview__actions button#pv_decline_btn { color: #7f1d1d; background: #fff1f2; border: 1px solid #fbcaca; }

        /* Modal improvements: smoother entrance and clearer hierarchy (UI-only) */
        .modal-enter { transform: translateY(8px); opacity: 0; transition: transform 200ms ease, opacity 200ms ease; }
        .modal-enter-active { transform: translateY(0); opacity: 1; }
        #headAlertModal .modal-enter, #proposalCrudModal .modal-enter, #headViewModal .modal-enter { max-width: 900px; border-radius: 12px; padding: 18px; }
        #headAlertModal .modal-enter h2, #headViewModal .modal-enter h2, #proposalCrudModal .modal-enter h2 { font-size: 1.12rem; color: var(--ink-1); }
        #headAlertModal .modal-enter p, #headViewModal .modal-enter .text-sm, #proposalCrudModal .modal-enter .text-sm { color: var(--ink-2); }
        #headViewModal .modal-enter { max-width: 920px; }
        /* Make confirm/cancel buttons consistent and readable */
        .modal-enter button { padding: 8px 12px; border-radius: 8px; font-weight: 700; }
        .modal-enter .bg-blue-600 { background: linear-gradient(180deg,var(--brand-accent),var(--brand-accent-2)); border: none; }
        .modal-enter .bg-red-600 { background: linear-gradient(180deg,#ef4444,#dc2626); border: none; }

        /* Smaller screens: keep layout readable */
        @media (max-width: 1024px) {
            #analytics .grid { grid-template-columns: 1fr; }
            .submission-preview { right: 0.75rem; left: auto; width: calc(100% - 2rem); max-width: 420px; }
            .container { padding-left: 1rem; padding-right: 1rem; }
        }
    /* Submissions widget animations */
    .stat-card { border-radius: 0.5rem; transition: transform 220ms ease, box-shadow 220ms ease; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(2,6,23,0.08); }
    .stat-card.active { box-shadow: 0 16px 40px rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.16); }
    .stat-count { font-weight: 700; font-size: 1.5rem; }
    .pulse-anim { animation: pulseCount 700ms ease; }
    @keyframes pulseCount { 0% { transform: scale(1); opacity: 0.9 } 40% { transform: scale(1.08); opacity: 1 } 100% { transform: scale(1); opacity: 1 } }
    .last-updated-badge { font-size: 0.8rem; color: #6b7280; margin-top: 6px; }
    .last-updated-time { font-weight: 600; color: #374151; }
    /* By-type widget styles */
    #byTypeWidget { margin-top: 0.5rem; }
    .type-row { display:flex; align-items:center; gap:0.75rem; }
    .type-name { flex: 0 0 38%; font-size:0.95rem; color:#374151; }
    .type-stats { flex: 1 1 auto; }
    .type-count { flex: 0 0 50px; text-align:right; font-weight:600; color:#374151; }
    .bar { background:#f3f4f6; height:10px; border-radius:9999px; overflow:hidden; }
    .bar-fill { height:100%; width:0%; background:linear-gradient(90deg,#60A5FA,#3B82F6); border-radius:9999px; transition: width 900ms cubic-bezier(.2,.8,.2,1); }
    .type-row .pct { font-size:0.85rem; color:#6b7280; margin-left:0.5rem; }
    /* Preview widget for Submissions list */
    .submission-preview {
        position: fixed;
        right: 1.5rem;
        top: 18%;
        width: 320px;
        max-height: 420px;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 12px 30px rgba(2,6,23,0.12);
        overflow: hidden;
        transform: translateY(8px) scale(0.98);
        opacity: 0;
        transition: transform 260ms cubic-bezier(.2,.9,.2,1), opacity 220ms ease;
        z-index: 80;
        border: 1px solid rgba(15,23,42,0.04);
    }
    .submission-preview.open { transform: translateY(0) scale(1); opacity: 1; }
    .submission-preview__header { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
    .submission-preview__body { padding: 12px 14px; overflow:auto; max-height: 320px; }
    .submission-preview__title { font-weight:700; color:#111827; }
    .submission-preview__meta { font-size:0.85rem; color:#6b7280; }
    .submission-preview__actions { padding: 10px 14px; border-top: 1px solid #f3f4f6; display:flex; justify-content:flex-end; gap:8px; }
    .preview-pulse { animation: previewPulse 560ms ease; }
    @keyframes previewPulse { 0% { transform: scale(0.996); opacity: 0.9 } 50% { transform: scale(1.02); opacity: 1 } 100% { transform: scale(1); opacity: 1 } }
    </style>
    <link rel="stylesheet" href="../assets/ui-animations.css">
</head>
<body class="bg-gray-100">
 
    <div class="flex h-screen overflow-hidden">
    <aside class="sidebar w-64 text-white flex flex-col relative" data-reveal="sidebar" style="background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);">
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar" style="background: white; border: 2px solid #e5e7eb; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); position: absolute; right: -12px; top: 24px;">
              <i class="fas fa-chevron-left text-gray-600 text-xs"></i>
            </button>
            
            <!-- Header Section -->
            <div class="p-6 text-center border-b border-gray-700/50">
                <img src="../assets/image1.png" alt="Mabini Seal" class="logo-formal">
                <h2 id="sidebar-role-title" class="text-lg font-bold sidebar-text tracking-wide">OPMDC Head</h2>
                <p class="text-[11px] text-gray-400 sidebar-text mt-1 font-medium">Oversight Panel</p>
            </div>
            
            <!-- Navigation Section -->
            <nav class="flex-1 px-3 py-6 space-y-1">
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3 sidebar-text">Main Menu</p>
                
                <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-700 active-nav-link" onclick="showPage('dashboard'); return false;" title="Dashboard" style="position: relative; transition: all 0.2s ease; font-weight: 500;">
                    <i class="fas fa-th-large mr-3" style="font-size: 1.125rem; width: 20px; text-align: center;"></i> <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="#" id="submissions-link" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-700" onclick="showPage('submissions'); try{clearHeadBadge();}catch(e){} return false;" title="Submissions" style="position: relative; transition: all 0.2s ease; font-weight: 500;">
                    <i class="fas fa-file-invoice mr-3" style="font-size: 1.125rem; width: 20px; text-align: center;"></i> <span class="sidebar-text">Submissions</span>
                    <span id="submissionsBadge" class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold text-white bg-rose-600 rounded-full hidden sidebar-text">0</span>
                </a>
                <a href="#" id="analytics-link" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-700" onclick="showPage('analytics'); return false;" title="Analytics" style="position: relative; transition: all 0.2s ease; font-weight: 500;">
                    <i class="fas fa-chart-bar mr-3" style="font-size: 1.125rem; width: 20px; text-align: center;"></i> <span class="sidebar-text">Analytics</span>
                </a>
            </nav>
            
            <!-- Footer Section -->
            <div class="p-4 border-t border-gray-700/50">
                <a href="../login.html" class="flex items-center w-full px-4 py-3 rounded-lg hover:bg-rose-600 hover:text-white transition-all" title="Logout" style="color: #FCA5A5;">
                    <i class="fas fa-right-from-bracket mr-3" style="font-size: 1.125rem; width: 20px; text-align: center;"></i> <span class="sidebar-text">Logout</span>
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto" style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);">
            <div class="container mx-auto px-6 py-6">
                
                <div id="dashboard" class="page-content" style="display: block;">
                    <div class="flex items-center justify-between mb-6" data-reveal="header">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 tracking-tight mb-1">Dashboard Overview</h3>
                            <p class="text-xs text-gray-600 font-semibold flex items-center gap-2">
                                <i class="fas fa-calendar-day text-blue-600"></i>
                                <span id="current-date-head"></span>
                            </p>
                        </div>
                        <div class="relative">
                            <button id="headNotifBell" title="Notifications" class="relative p-2.5 rounded-xl hover:bg-gray-100 focus:outline-none transition-all">
                                <i class="fas fa-bell text-gray-600 text-lg"></i>
                                <span id="headNotifBadge" class="notification-dot hidden" style="position: absolute; top: 6px; right: 6px; height: 10px; width: 10px; background-color: #EF4444; border-radius: 50%; border: 2px solid white;"></span>
                            </button>
                            <div id="headNotifDropdown" class="hidden absolute right-0 mt-2 bg-white shadow-xl rounded-xl z-50 w-80 border border-gray-200">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <strong class="text-sm font-bold text-gray-900">Notifications</strong>
                                    <button id="headMarkAllRead" class="text-[11px] text-blue-600 font-semibold hover:text-blue-700">Mark all read</button>
                                </div>
                                <div id="headNotifList" class="max-h-64 overflow-auto p-2"><div class="text-center text-gray-500 py-8 text-sm">Loading…</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5" data-reveal="group">
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="text-sm font-medium text-gray-500 mb-1">Total Submissions</div>
                            <div class="text-3xl font-bold text-gray-800" id="card-total">142</div>
                        </div>
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="text-sm font-medium text-gray-500 mb-1">Pending Approvals</div>
                            <div class="text-3xl font-bold text-yellow-600" id="card-pending">12</div>
                        </div>
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="text-sm font-medium text-gray-500 mb-1">Active Projects</div>
                            <div class="text-3xl font-bold text-blue-600" id="card-active">35</div>
                        </div>
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="text-sm font-medium text-gray-500 mb-1">Registered Users</div>
                            <div class="text-3xl font-bold text-green-600" id="card-users">89</div>
                        </div>
                    </div>

                    <!-- Proposals Needing Attention (Dashboard quick view) -->
                    <div class="mt-6 bg-white p-5 rounded-lg shadow-sm border border-gray-100" data-reveal="panel">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-800">Recent Proposals</h4>
                            <div class="flex items-center gap-2">
                                    <button onclick="showPage('submissions')" class="text-sm text-blue-600 hover:text-blue-700 transition-colors">View All</button>
                                </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs font-semibold uppercase bg-gray-50 text-gray-600 border-b border-gray-200">
                                    <tr>
                                        <th class="py-3 px-4">Title</th>
                                        <th class="py-3 px-4">Barangay</th>
                                        <th class="py-3 px-4">Date</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-proposals-body">
                                    <tr><td colspan="5" class="py-6 text-center text-gray-400 text-sm">Loading…</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Descriptive Analytics section removed per request -->
                    <!-- Placeholder: keep empty container (display:none) to avoid JS errors if any legacy code still references elements -->
                    <div id="headDescAnalyticsPlaceholder" style="display:none"></div>

                    <!-- Account Status Chart -->
                    <div class="mt-6">
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Account Status Distribution</h4>
                            <canvas id="accountStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div id="submissions" class="page-content">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-5">Manage Submissions</h3>
                    <!-- Animated quick widget for Manage Submissions -->
                    <div class="mb-5 grid grid-cols-1 md:grid-cols-3 gap-4" id="submissions-widget" data-reveal="group">
                        <div class="bg-white p-4 stat-card shadow-sm border border-gray-100 flex items-center space-x-4 cursor-pointer rounded-lg" data-status="approved">
                            <div class="p-3 rounded-lg bg-green-50 text-green-600"><i class="fas fa-check-circle fa-lg"></i></div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Approved</div>
                                <div id="sub-card-approved" class="stat-count text-green-700">0</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 stat-card shadow-sm border border-gray-100 flex items-center space-x-4 cursor-pointer rounded-lg" data-status="pending">
                            <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600"><i class="fas fa-hourglass-half fa-lg"></i></div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending</div>
                                <div id="sub-card-pending" class="stat-count text-yellow-600">0</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 stat-card shadow-sm border border-gray-100 flex items-center space-x-4 cursor-pointer rounded-lg" data-status="declined">
                            <div class="p-3 rounded-lg bg-red-50 text-red-600"><i class="fas fa-times-circle fa-lg"></i></div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Declined</div>
                                <div id="sub-card-declined" class="stat-count text-red-600">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-2 mb-4">
                        <div class="last-updated-badge">Last updated: <span id="sub-widget-updated" class="last-updated-time">—</span></div>
                        <div class="text-xs text-gray-500">Click a card to filter</div>
                    </div>
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs font-semibold uppercase bg-gray-50 text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4">Project Name</th>
                                    <th class="py-3 px-4">Submitted By</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="submissions-table-body"></tbody>
                        </table>
                    </div>
                    <!-- Submission preview removed per request -->
                </div>

                <div id="analytics" class="page-content">
                    <!-- Header Section -->
                    <div class="mb-6 bg-gradient-to-r from-slate-700 to-slate-600 rounded-xl p-6 shadow-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-white">Analytics & Insights Dashboard</h3>
                                    <p class="text-sm text-slate-200 mt-1">Comprehensive data analysis and decision support</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <input id="analyticsStart" type="date" class="bg-transparent border-none text-white text-sm focus:outline-none focus:ring-0 placeholder-slate-300" placeholder="Start Date" />
                                </div>
                                <span class="text-white">—</span>
                                <div class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <input id="analyticsEnd" type="date" class="bg-transparent border-none text-white text-sm focus:outline-none focus:ring-0 placeholder-slate-300" placeholder="End Date" />
                                </div>
                                <button id="analyticsRefresh" class="bg-white text-slate-700 font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-slate-50 transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh
                                </button>
                                <div class="flex gap-2 ml-2">
                                    <button id="exportCsvBtn" class="bg-white bg-opacity-10 backdrop-blur-sm hover:bg-opacity-20 text-white text-sm px-4 py-2.5 rounded-lg transition-all duration-200 font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        CSV
                                    </button>
                                    <button id="exportPdfBtn" class="bg-white bg-opacity-10 backdrop-blur-sm hover:bg-opacity-20 text-white text-sm px-4 py-2.5 rounded-lg transition-all duration-200 font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actionable Insights Section (Priority) -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-gray-800">Actionable Insights</h4>
                            <span class="text-xs text-gray-500 bg-gray-50 px-3 py-1 rounded-full">Real-time Monitoring</span>
                        </div>
                        
                        <!-- Severity Legend -->
                        <div id="severityLegend" class="mb-5 p-4 bg-gradient-to-r from-slate-50 to-gray-50 rounded-lg border border-gray-200">
                            <div class="font-semibold text-sm text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Severity Classification
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                                <div class="flex items-center gap-2 bg-white p-2 rounded border border-gray-100">
                                    <span style="width:12px;height:12px;border-radius:50%;background:#009E73;display:inline-block;box-shadow:0 2px 4px rgba(0,158,115,0.3)"></span>
                                    <span class="text-sm"><strong class="text-green-700">Low</strong> <span class="text-gray-600">— Normal operations</span></span>
                                </div>
                                <div class="flex items-center gap-2 bg-white p-2 rounded border border-gray-100">
                                    <span style="width:12px;height:12px;border-radius:50%;background:#F0E442;display:inline-block;box-shadow:0 2px 4px rgba(240,228,66,0.3)"></span>
                                    <span class="text-sm"><strong class="text-yellow-700">Medium</strong> <span class="text-gray-600">— Attention needed</span></span>
                                </div>
                                <div class="flex items-center gap-2 bg-white p-2 rounded border border-gray-100">
                                    <span style="width:12px;height:12px;border-radius:50%;background:#D55E00;display:inline-block;box-shadow:0 2px 4px rgba(213,94,0,0.3)"></span>
                                    <span class="text-sm"><strong class="text-orange-700">High</strong> <span class="text-gray-600">— Immediate action required</span></span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-600 bg-white p-2 rounded border border-gray-100">
                                <strong class="text-gray-700">Threshold Ranges:</strong> 
                                Pending <span class="font-mono">(Low: 0-5, Med: 6-40, High: &gt;40)</span> | 
                                Approval <span class="font-mono">(Low: ≥60%, Med: 40-59%, High: &lt;40%)</span> | 
                                Turnaround <span class="font-mono">(Low: 0-10d, Med: 11-21d, High: &gt;21d)</span>
                            </div>
                        </div>

                        <!-- Insights Table Container -->
                        <div id="analyticsInsights" class="overflow-x-auto">
                            <div class="flex items-center justify-center py-8 text-gray-500">
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading insights…
                            </div>
                        </div>
                    </div>

                    <!-- Primary Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Proposals Trend Chart -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden lg:col-span-2">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-500 bg-opacity-10 p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-gray-800">Proposals Trend</h4>
                                            <p class="text-xs text-gray-600">Submissions over selected period</p>
                                        </div>
                                    </div>
                                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">Time Series</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <canvas id="proposalsChart" height="360" style="width:100%;max-height:520px;"></canvas>
                            </div>
                        </div>

                        <!-- Barangay Distribution Chart -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-green-500 bg-opacity-10 p-2 rounded-lg">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-base font-bold text-gray-800">Barangay Distribution</h4>
                                            <p class="text-xs text-gray-600">Top 10 barangays by volume</p>
                                        </div>
                                    </div>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">Geographic</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <canvas id="barangayChart" height="220"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Approval Funnel Chart -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="bg-purple-500 bg-opacity-10 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800">Approval Status</h4>
                                        <p class="text-xs text-gray-600">Distribution by status</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <canvas id="approvalFunnel" height="240"></canvas>
                            </div>
                        </div>

                        <!-- Average Turnaround Chart -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-b border-gray-200 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="bg-orange-500 bg-opacity-10 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800">Processing Time</h4>
                                        <p class="text-xs text-gray-600">Average turnaround</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <canvas id="avgTurnaroundChart" height="240"></canvas>
                            </div>
                        </div>

                        <!-- Barangay Heatmap -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-cyan-50 to-teal-50 border-b border-gray-200 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="bg-cyan-500 bg-opacity-10 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800">Activity Heatmap</h4>
                                        <p class="text-xs text-gray-600">Monthly patterns</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <div id="heatmapContainer" class="overflow-auto max-h-56 bg-gray-50 rounded-lg border border-gray-200 p-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                

                <!-- Strategic Planning section removed for Head dashboard -->
                
                <!-- Policy Development section removed for Head dashboard -->

            </div>
        </main>
    </div>

            <script src="../assets/ui-animations.js"></script>

    <!-- Simple Alert/Confirm Modal (shared pattern with Staff) -->
    <div id="headAlertModal" class="fixed inset-0 z-60 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-8 w-full max-w-sm z-10 modal-enter">
            <h2 id="headAlertModalTitle" class="text-xl font-bold text-gray-800 mb-4">Confirm</h2>
            <p id="headAlertModalMessage" class="text-gray-600 mb-6">Message goes here.</p>
            <div id="headAlertModalActions" class="flex items-center justify-end"></div>
        </div>
    </div>

    <!-- Head View Modal: shows detailed request info and allows quick actions -->
    <div id="headViewModal" class="fixed inset-0 z-70 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 modal-backdrop"></div>
        <div class="bg-white rounded-lg shadow-xl m-auto p-6 w-full max-w-2xl z-10 modal-enter">
            <div class="flex items-start justify-between mb-4">
                <h2 id="headViewModalTitle" class="text-xl font-bold text-gray-800">Request Details</h2>
                <button id="headViewCloseBtn" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div id="headViewModalBody" class="text-sm text-gray-700 max-h-80 overflow-auto">
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-4 bg-gray-50 p-3 rounded-lg">
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Project / Type</p>
                        <p id="hv_request_type" class="text-lg font-semibold text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Barangay</p>
                        <p id="hv_barangay" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date</p>
                        <p id="hv_date" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Current Status</p>
                        <p id="hv_status" class="text-sm font-medium"></p>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description</p>
                    <div id="hv_description" class="text-sm text-gray-800 leading-relaxed bg-white p-3 rounded border border-gray-200 max-h-56 overflow-y-auto"></div>
                </div>

                <div class="mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Attachment</p>
                    <div id="hv_attachment" class="flex items-center gap-3 text-sm text-gray-700"></div>
                </div>

                <div id="hv_preview" class="mb-3 bg-gray-50 p-3 rounded border border-gray-200 max-h-80 overflow-auto hidden"></div>

                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</p>
                    <div id="hv_notes" class="mt-1 p-2 bg-gray-50 rounded"></div>
                </div>
            </div>
            <div id="headViewModalActions" class="flex items-center justify-end mt-4 space-x-2">
                <!-- Buttons will be generated by JS based on status -->
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sidebar toggle functionality
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        
        if (sidebarState === 'true') {
          sidebar.classList.add('collapsed');
        }
        
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
          const isCollapsed = sidebar.classList.contains('collapsed');
          localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Update date and time display
        function updateTime() {
            const dateElement = document.getElementById('current-date-head');
            if (dateElement) {
                const now = new Date();
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
                const formattedDate = now.toLocaleDateString('en-US', options);
                const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
                dateElement.textContent = `${formattedDate} · ${formattedTime}`;
            }
        }
        
        updateTime();
        setInterval(updateTime, 60000);
        
        // Route guard: ensure only OPMDC Head stays on this page
        try {
            const u = JSON.parse(localStorage.getItem('loggedInUser'));
            if (!u || !u.role) throw new Error('no user');
            const r = String(u.role);
            if (/admin/i.test(r)) return (window.location.href = 'admin.php');
            if (/staff/i.test(r)) return (window.location.href = 'staff-dashboard.php');
            if (!/head/i.test(r)) return (window.location.href = 'barangay-dashboard.php');
        } catch (e) { /* ignore */ }
        // Show logged-in user info from localStorage if present
        try {
            const u = JSON.parse(localStorage.getItem('loggedInUser'));
            if (u && u.role) {
                const r = document.getElementById('sidebar-role-title');
                if (r) r.textContent = u.role;
            }
        } catch (e) { /* ignore */ }
        // Populate head dashboard (PROPOSALS ONLY – requests removed per new requirement)
        async function loadRequestsAndPopulate() {
            let requests = []; // now holds proposals only
            // helper for animating integers (used for stat cards and by-type table)
            function animateNumber(el, start, end, duration = 700) {
                if (!el) return;
                start = Number(start) || 0;
                end = Number(end) || 0;
                if (start === end) { el.innerText = String(end); return; }
                const startTime = performance.now();
                const step = (now) => {
                    const t = Math.min(1, (now - startTime) / duration);
                    // easeOutQuad
                    const eased = 1 - (1 - t) * (1 - t);
                    const current = Math.round(start + (end - start) * eased);
                    el.innerText = String(current);
                    if (t < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            }
            try {
                // Fetch proposals only
                const propRes = await apiFetch('list_staff_proposals.php', { credentials: 'same-origin' });
                let props = [];
                if (propRes.ok) {
                    const contentType = propRes.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const propData = await propRes.json();
                        props = Array.isArray(propData.proposals) ? propData.proposals : [];
                        // Optionally show all proposals needing head attention: For Head Approval OR already Approved/Declined for audit
                        props = props.filter(p => {
                            const s = String(p.status || '').toLowerCase();
                            return s === 'for head approval' || /approved|declined|denied/.test(s);
                        }).map(p => ({
                            ...p,
                            kind: 'proposal',
                            request_type: p.project_type || p.title || 'Project Proposal',
                            request_code: p.id,
                            created_at: p.date || p.created_at
                        }));
                    }
                }
                requests = props; // proposals only
            } catch (err) {
                console.warn('Could not load proposals:', err);
                requests = [];
            }

            // expose latest proposals for UI helpers
            try { window._headRequests = requests; } catch (e) { /* ignore */ }

            // Compute counts locally
            const total = requests.length;
            const approved = requests.filter(r => /approved/i.test(String(r.status))).length;
            const declined = requests.filter(r => /declined|denied/i.test(String(r.status))).length;
            const pending = total - (approved + declined);

            const totalEl = document.getElementById('card-total');
            const pendingEl = document.getElementById('card-pending');
            if (totalEl) totalEl.innerText = total;
            if (pendingEl) pendingEl.innerText = pending;
            // Update quick submissions widget if present and animate numbers (tween) + pulse
            try {
                // animateNumber is defined earlier in this function (hoisted)

                const aEl = document.getElementById('sub-card-approved');
                const pEl = document.getElementById('sub-card-pending');
                const dEl = document.getElementById('sub-card-declined');
                // previous values (persist across refreshes)
                const prev = window._headAggPrev || { approved: 0, pending: 0, declined: 0, total: 0 };
                if (aEl) { animateNumber(aEl, prev.approved, approved); aEl.classList.add('pulse-anim'); setTimeout(()=> aEl.classList.remove('pulse-anim'), 800); }
                if (pEl) { animateNumber(pEl, prev.pending, pending); pEl.classList.add('pulse-anim'); setTimeout(()=> pEl.classList.remove('pulse-anim'), 800); }
                if (dEl) { animateNumber(dEl, prev.declined, declined); dEl.classList.add('pulse-anim'); setTimeout(()=> dEl.classList.remove('pulse-anim'), 800); }
                // update stored prev
                window._headAggPrev = { approved: approved, pending: pending, declined: declined, total: total };

                // Update last-updated badge (use server as_of when available)
                try {
                    const updEl = document.getElementById('sub-widget-updated');
                    if (updEl) {
                        let dt = (agg && agg.as_of) ? new Date(agg.as_of) : new Date();
                        // show localized time with date when older than a day
                        const now = new Date();
                        const diffMs = Math.abs(now - dt);
                        let txt = dt.toLocaleTimeString();
                        if (diffMs > 24 * 60 * 60 * 1000) txt = dt.toLocaleString();
                        updEl.innerText = txt;
                    }
                } catch (e) { /* ignore */ }
            } catch (e) { console.warn('widget update error', e); }

            // Active Projects: show number of approved proposals (use approved count computed above)
            try {
                const activeEl = document.getElementById('card-active');
                const prevActive = window._headActivePrev || { approved: 0 };
                if (activeEl) { animateNumber(activeEl, prevActive.approved, approved); activeEl.classList.add('pulse-anim'); setTimeout(()=> activeEl.classList.remove('pulse-anim'), 800); }
                window._headActivePrev = { approved };
            } catch (e) { /* ignore */ }

            // Registered Users: fetch dashboard counts and show barangay accounts count
            try {
                (async () => {
                    try {
                        const res = await apiFetch('dashboard_counts.php', { credentials: 'same-origin' });
                        if (res && res.ok) {
                            const d = await res.json();
                            const totalAccounts = parseInt(d.total_accounts || d.total_accounts || 0, 10) || 0;
                            const usersEl = document.getElementById('card-users');
                            const prevUsers = window._headUsersPrev || { count: 0 };
                            if (usersEl) { animateNumber(usersEl, prevUsers.count, totalAccounts); usersEl.classList.add('pulse-anim'); setTimeout(()=> usersEl.classList.remove('pulse-anim'), 800); }
                            window._headUsersPrev = { count: totalAccounts };
                        }
                    } catch (e) { /* ignore */ }
                })();
            } catch (e) { /* ignore */ }

            // Populate submissions table (respect filter if set)
            const subBody = document.getElementById('submissions-table-body');
            const dashBody = document.getElementById('dashboard-proposals-body');
            if (subBody) {
                subBody.innerHTML = '';

                // helper: determine if request matches named filter
                function matchesFilter(req, filter) {
                    if (!filter) return true;
                    try {
                        const s = String(req.status || '').toLowerCase().trim();
                        if (!s) {
                            // fallback to numeric-like values if stored as 0/1
                            const n = String(req.status || req.status_code || '').trim();
                            if (n === '1' && filter === 'approved') return true;
                            if (n === '0' && filter === 'pending') return true;
                            // treat anything else as declined when numeric and not 0/1
                            if ((n && n !== '0' && n !== '1') && filter === 'declined') return true;
                            return false;
                        }
                        if (s.includes('approved') && filter === 'approved') return true;
                        if ((s.includes('pending') || s.includes('for approval') || s.includes('for head approval') || s.includes('for review') || s.includes('processing')) && filter === 'pending') return true;
                        if ((s.includes('declined') || s.includes('denied')) && filter === 'declined') return true;
                        // numeric strings
                        if (s === '1' && filter === 'approved') return true;
                        if (s === '0' && filter === 'pending') return true;
                        return false;
                    } catch (e) { return false; }
                }

                const currentFilter = window._headSubmissionFilter || null;
                const effective = currentFilter ? requests.filter(r => matchesFilter(r, currentFilter)) : requests;

                if (effective.length === 0) {
                    subBody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500 py-4">No submissions found.</td></tr>';
                } else {
                    effective.slice(0, 50).forEach(r => {
                        const tr = document.createElement('tr');
                        tr.id = 'request-row-' + (r.id || r.request_code || Math.random().toString(36).slice(2,8));
                        tr.tabIndex = 0; // make row keyboard-focusable
                        tr.className = 'border-b hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-200';
                        const actions = [];
                        if (r.attachment) {
                            actions.push(`<a href="${r.attachment}" target="_blank" class="text-sm text-blue-600 mr-2">Download</a>`);
                        }
                        actions.push(`<button class="text-sm text-blue-600" onclick="openHeadProposal(${r.id})">View</button>`);
                        const isFinalized = /approved|declined/i.test(String(r.status || ''));
                        // approvals always available when not final
                        if (!isFinalized) {
                            actions.push(`<button class=\"ml-2 text-sm text-green-600\" onclick=\"confirmUpdateSubmissionStatus(${r.id}, 'Approved')\">Approve</button>`);
                            actions.push(`<button class=\"ml-2 text-sm text-red-600\" onclick=\"confirmUpdateSubmissionStatus(${r.id}, 'Declined')\">Decline</button>`);
                        } else {
                            actions.push(`<button class=\"ml-2 text-sm text-red-600\" onclick=\"confirmDeleteProposal(${r.id})\">Delete</button>`);
                        }
                        
                        // Add visual indicator for proposals vs requests
                        const icon = '<i class=\"fas fa-file-alt mr-1 text-blue-600\"></i>';
                        const typeLabel = `${icon}<span class=\"font-medium text-blue-700\">Proposal:</span> ${escapeHtml(r.request_type || r.title || 'Project')}`;
                        
                        tr.innerHTML = `<td class="py-3 px-4 font-medium">${typeLabel}</td><td class="py-3 px-4">${escapeHtml(r.barangay || r.submitter || '')}</td><td class="py-3 px-4">${new Date(r.created_at).toLocaleDateString()}</td><td class="py-3 px-4">${getStatusLabel(r.status || '')}</td><td class="py-3 px-4">${actions.join('')}</td>`;
                        // clicking (or pressing Enter/Space) toggles persistent preview for this row
                        tr.addEventListener('click', () => { if (window.togglePreviewPin) window.togglePreviewPin(r.id); });
                        tr.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); if (window.togglePreviewPin) window.togglePreviewPin(r.id); } });
                        subBody.appendChild(tr);
                    });
                }

                // reflect active card
                try {
                    const widget = document.getElementById('submissions-widget');
                    if (widget) {
                        widget.querySelectorAll('.stat-card').forEach(c => c.classList.remove('active'));
                        if (currentFilter) {
                            const sel = widget.querySelector(`.stat-card[data-status="${currentFilter}"]`);
                            if (sel) sel.classList.add('active');
                        }
                    }
                } catch (e) { /* ignore */ }
            }

            // Dashboard quick list (top 8 recent proposals)
            if (dashBody) {
                dashBody.innerHTML = '';
                const recent = requests
                    .slice() // copy
                    .sort((a,b)=> new Date(b.created_at||b.date||0) - new Date(a.created_at||a.date||0))
                    .slice(0, 8);
                if (!recent.length) {
                    dashBody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-gray-400">No proposals found.</td></tr>';
                } else {
                    recent.forEach(p => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b hover:bg-gray-50';
                        const dt = p.created_at || p.date || '';
                        tr.innerHTML = `
                            <td class="py-2 px-3 font-medium text-gray-700">${escapeHtml(p.title || p.request_type || 'Proposal')}</td>
                            <td class="py-2 px-3">${escapeHtml(p.barangay || '—')}</td>
                            <td class="py-2 px-3">${dt ? new Date(dt).toLocaleDateString() : '—'}</td>
                            <td class="py-2 px-3">${getStatusLabel(p.status || '')}</td>
                            <td class="py-2 px-3"><button class="text-blue-600 text-xs" onclick="openHeadProposal(${p.id})">View</button></td>
                        `;
                        dashBody.appendChild(tr);
                    });
                }
            }

            // Descriptive analytics: prefer server-provided by-type breakdown when available
            try {
                // helper to render by-type table (animated counts + small bar)
                function renderByTypeWidget(items, totalCount) {
                    const table = document.getElementById('tblByType');
                    const loading = document.getElementById('byTypeLoading');
                    if (!table) return;
                    const tbody = table.querySelector('tbody');
                    tbody.innerHTML = '';
                    if (loading) loading.style.display = (items && items.length) ? 'none' : 'block';
                    if (!items || !items.length) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-sm text-gray-500 py-3">No data available.</td></tr>';
                        return;
                    }
                    items.forEach((it, idx) => {
                        const cnt = Number(it.count ?? it.cnt ?? 0);
                        const pct = totalCount ? ((cnt/totalCount)*100) : 0;
                        const tr = document.createElement('tr');
                        // create unique ids to allow tweening
                        const countId = `bytype-count-${idx}`;
                        const pctId = `bytype-pct-${idx}`;
                        tr.innerHTML = `
                            <td class="py-2 px-2">${escapeHtml(it.type || it.request_type || 'Unknown')}</td>
                            <td class="py-2 px-2 text-right"><span id="${countId}">${cnt}</span></td>
                            <td class="py-2 px-2 text-right"><span id="${pctId}">${pct.toFixed(1)}%</span></td>
                            <td class="py-2 px-2"><div class="bar"><div class="bar-fill" data-pct="${pct.toFixed(1)}" style="width:0%"></div></div></td>
                        `;
                        tbody.appendChild(tr);
                        // animate count from previous if present
                        const prev = (window._headByTypePrev && window._headByTypePrev[it.type]) ? window._headByTypePrev[it.type] : 0;
                        const elCount = document.getElementById(countId);
                        if (elCount) animateNumber(elCount, prev, cnt, 700);
                        // animate pct text (simple fade-in) and bar fill
                        setTimeout(() => {
                            const fill = tr.querySelector('.bar-fill');
                            if (fill) fill.style.width = Math.min(100, Math.max(0, pct)) + '%';
                        }, 80 * idx + 60);
                    });
                    // store prev counts for next tween
                    window._headByTypePrev = {};
                    items.forEach(it => { window._headByTypePrev[it.type] = Number(it.count ?? it.cnt ?? 0); });
                }

                // build by-type from proposals and render (requests removed)
                const byType = {};
                requests.forEach(r => { const t = r.request_type || r.project_type || r.title || 'Unknown'; byType[t] = (byType[t] || 0) + 1; });
                const arr = Object.keys(byType).map(k => ({ type: k, count: byType[k] })).sort((a,b)=> b.count - a.count);
                renderByTypeWidget(arr, requests.length);
            } catch (e) { try { computeDescriptive(requests); } catch (_) {} }

            // Charts: status distribution
            renderStatusChart({ approved, pending, declined });
    }

    // expose for external callers (e.g., SSE handlers)
    window.loadRequestsAndPopulate = loadRequestsAndPopulate;

    // Small helper: HTML-escape (global scope for toast notifications)
    function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }
    window.escapeHtml = escapeHtml;

    // Setup click handlers for the submissions-widget stat cards to toggle filtering
    try {
        (function setupSubmissionFilterHandlers(){
            const widget = document.getElementById('submissions-widget');
            if (!widget) return;
            widget.querySelectorAll('.stat-card[data-status]').forEach(card => {
                card.addEventListener('click', () => {
                    const status = card.getAttribute('data-status');
                    const cur = window._headSubmissionFilter || null;
                    if (cur === status) {
                        window._headSubmissionFilter = null; // toggle off
                    } else {
                        window._headSubmissionFilter = status; // set filter
                    }
                    // visual toggle
                    widget.querySelectorAll('.stat-card').forEach(c => c.classList.toggle('active', (window._headSubmissionFilter && c.getAttribute('data-status') === window._headSubmissionFilter)));
                    if (typeof loadRequestsAndPopulate === 'function') loadRequestsAndPopulate();
                });
            });
        })();
    } catch (e) { /* ignore */ }

    // Submission preview handlers removed per request (preview modal removed)

    // Jump to a request row in the submissions table and highlight it
    function jumpToRequest(requestId) {
        try {
            if (!requestId) return;
            showPage('submissions');
            // reload to ensure row exists
            if (typeof loadRequestsAndPopulate === 'function') loadRequestsAndPopulate();
            // give some time for DOM to update
            setTimeout(() => {
                const el = document.getElementById('request-row-' + requestId);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('highlight-request');
                    // remove highlight after animation
                    setTimeout(() => el.classList.remove('highlight-request'), 3000);
                }
            }, 500);
        } catch (e) { console.warn('jumpToRequest error', e); }
    }

        function getStatusLabel(s) {
            const st = String(s || '');
            if (/approved/i.test(st)) return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">Approved</span>';
            if (/declined|denied/i.test(st)) return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">Declined</span>';
            return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">'+escapeHtml(st || 'Pending')+'</span>';
        }

        function computeDescriptive(requests) {
            // Section removed: if elements not present, exit silently
            const summaryEl = document.getElementById('descSummary');
            if (!summaryEl) return;
            const byType = {};
            const byBarangay = {};
            const reviewTimes = [];

            requests.forEach(r => {
                const type = r.request_type || r.title || 'Unknown';
                byType[type] = (byType[type] || 0) + 1;
                const b = r.barangay || 'Unknown';
                byBarangay[b] = (byBarangay[b] || 0) + 1;

                // compute review time if history contains approved/initial timestamps
                try {
                    const hist = r.history || [];
                    const submitted = new Date(r.created_at || (hist[hist.length-1] && hist[hist.length-1].timestamp) || null);
                    const approvedEntry = (hist || []).find(h => /approved/i.test(h.status || ''));
                    if (submitted && approvedEntry) {
                        const approvedAt = new Date(approvedEntry.timestamp || approvedEntry.date || approvedEntry.created_at || null);
                        if (!isNaN(submitted.getTime()) && !isNaN(approvedAt.getTime())) {
                            const mins = Math.round((approvedAt - submitted) / 60000);
                            if (!isNaN(mins)) reviewTimes.push(mins);
                        }
                    }
                } catch (e) { /* ignore */ }
            });

            // summary
            const total = requests.length;
            summaryEl.innerText = `Showing ${total} submissions`;

            // by type
            // `tblByType` may be a TABLE used as fallback or a TBODY; handle both
            (function(){
                const tbElem = document.getElementById('tblByType');
                if (!tbElem) return;
                let tbody = tbElem;
                if (tbElem.tagName === 'TABLE') tbody = tbElem.querySelector('tbody') || tbElem;
                tbody.innerHTML = '';
                Object.keys(byType).sort((a,b)=> byType[b]-byType[a]).forEach(k => {
                    const cnt = byType[k];
                    const pct = total ? ((cnt/total)*100).toFixed(1) : 0;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${escapeHtml(k)}</td><td style="text-align:right">${cnt}</td><td style="text-align:right">${pct}%</td>`;
                    tbody.appendChild(tr);
                });
            })();

            // top barangays
            const tbB = document.getElementById('tblTopBarangay');
            if (tbB) tbB.innerHTML = '';
            if (tbB) Object.keys(byBarangay).sort((a,b)=> byBarangay[b]-byBarangay[a]).slice(0,10).forEach(k => {
                const cnt = byBarangay[k];
                const pct = total ? ((cnt/total)*100).toFixed(1) : 0;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${escapeHtml(k)}</td><td style="text-align:right">${cnt}</td><td style="text-align:right">${pct}%</td>`;
                tbB.appendChild(tr);
            });

            // review time stats
            const reviewDiv = document.getElementById('reviewStats');
            if (reviewDiv) {
                if (reviewTimes.length === 0) {
                    reviewDiv.innerText = 'No reviewed proposals in range.';
                } else {
                    const avg = Math.round(reviewTimes.reduce((a,b)=>a+b,0)/reviewTimes.length);
                    const sorted = reviewTimes.slice().sort((a,b)=>a-b);
                    const median = sorted[Math.floor(sorted.length/2)];
                    const std = Math.round(Math.sqrt(reviewTimes.map(x=>Math.pow(x-avg,2)).reduce((a,b)=>a+b,0)/reviewTimes.length));
                    reviewDiv.innerHTML = `Count: ${reviewTimes.length}<br>Avg: ${avg} min<br>Median: ${median} min<br>Stddev: ${std} min`;
                }
            }
        }

        // charts
        function renderStatusChart({approved, pending, declined}) {
            const requestCtx = document.getElementById('requestStatusChart')?.getContext('2d');
            if (!requestCtx || typeof Chart === 'undefined') return;
            // destroy previous chart instance if any (simple approach)
            if (window._headRequestChart) { window._headRequestChart.destroy(); }
            window._headRequestChart = new Chart(requestCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Declined'],
                    datasets: [{ data: [approved, pending, declined], backgroundColor: ['#4CAF50', '#FFC107', '#F44336'] }]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' }, title: { display: true, text: 'Request Status Distribution' } } }
            });
        }

        // initial load
        loadRequestsAndPopulate();
        // refresh periodically
        setInterval(loadRequestsAndPopulate, 60_000);
    });

        // Dynamic API base detection + resilient fetch wrapper
        (function initApiHelpers(){
            const candidates = ['../api/', './api/', 'api/', '/opmdc-2/api/'];
            let _apiBase = null;
            async function detect(){
                for (const base of candidates){
                    try {
                        const res = await fetch(base + 'me.php', { credentials: 'same-origin' });
                        if (res.ok) { _apiBase = base; break; }
                    } catch(e){ /* ignore */ }
                }
                if (!_apiBase) _apiBase = '../api/';
                try { console.log('[head-dashboard] API base:', _apiBase); } catch(_){}
                return _apiBase;
            }
            window.getApiBase = async function(){ return _apiBase || await detect(); };
            window.apiFetch = async function(path, init = {}) {
                const base = await window.getApiBase();
                const firstUrl = base + path;
                try {
                    let r = await fetch(firstUrl, init);
                    if (r.ok) return r;
                    if (r.status === 404) {
                        for (const cand of candidates){
                            if (cand === base) continue;
                            try {
                                const rr = await fetch(cand + path, init);
                                if (rr.ok){
                                    _apiBase = cand;
                                    try { console.log('[head-dashboard] API base switched to:', _apiBase); } catch(_){}
                                    return rr;
                                }
                            } catch(e){ /* ignore */ }
                        }
                    }
                    return r;
                } catch(err){
                    for (const cand of candidates){
                        if (cand === base) continue;
                        try {
                            const rr = await fetch(cand + path, init);
                            if (rr.ok){
                                _apiBase = cand;
                                try { console.log('[head-dashboard] API base recovered to:', _apiBase); } catch(_){}
                                return rr;
                            }
                        } catch(e){ /* ignore */ }
                    }
                    throw err;
                }
            };
        })();

        // Allow Head to update submission status (request or proposal)
        async function updateSubmissionStatus(id, newStatus) {
            try {
                const res = await apiFetch('update_proposal.php', { method: 'POST', body: new URLSearchParams({ id: id, status: newStatus }), credentials: 'same-origin' });
                if (!res.ok) throw new Error('Network');
                const d = await res.json();
                if (d && d.success) {
                    if (typeof window.loadRequestsAndPopulate === 'function') window.loadRequestsAndPopulate();
                    return true;
                }
                alert('Failed to update proposal');
            } catch (err) {
                console.error(err);
                alert('Server error');
            }
            return false;
        }

        function confirmUpdateSubmissionStatus(id, newStatus) {
            showHeadConfirm('Confirm Action', `Are you sure you want to mark this proposal as ${newStatus}?`, async (ok) => {
                if (!ok) return;
                await updateSubmissionStatus(id, newStatus);
            }, 'Confirm', 'primary');
        }

        // Request update/delete functions removed (requests deprecated)

    function showPage(pageId) {
        document.querySelectorAll('.page-content').forEach(page => page.style.display = 'none');
        document.getElementById(pageId).style.display = 'block';

        document.querySelectorAll('nav a').forEach(link => {
            link.classList.remove('active-nav-link');
            if(link.getAttribute('onclick').includes(pageId)) {
                link.classList.add('active-nav-link');
            }
        });
        // Auto-load analytics charts when navigating to Analytics section
        if (pageId === 'analytics' && typeof fetchAnalytics === 'function') {
            try { fetchAnalytics(); } catch (e) { console.warn('fetchAnalytics failed', e); }
        }
    }
    // Modal helpers for head confirm/alert
    function headOpenModal() {
        const modal = document.getElementById('headAlertModal');
        modal.style.display = 'flex';
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        const content = modal.querySelector('.modal-enter');
        setTimeout(()=> content && content.classList.add('modal-enter-active'), 10);
    }
    function headCloseModal() {
        const modal = document.getElementById('headAlertModal');
        const content = modal.querySelector('.modal-enter');
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        content && content.classList.remove('modal-enter-active');
        setTimeout(()=> modal.style.display = 'none', 250);
    }
    function showHeadAlert(title, message, cb) {
        document.getElementById('headAlertModalTitle').textContent = title;
        document.getElementById('headAlertModalMessage').textContent = message;
        const actions = document.getElementById('headAlertModalActions');
        actions.innerHTML = `<button id="headAlertOk" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">OK</button>`;
        headOpenModal();
        document.getElementById('headAlertOk').onclick = ()=> { headCloseModal(); if (cb) cb(); };
    }
    function showHeadConfirm(title, message, onResult, confirmLabel = 'Confirm', variant = 'primary') {
        document.getElementById('headAlertModalTitle').textContent = title;
        document.getElementById('headAlertModalMessage').textContent = message;
        const actions = document.getElementById('headAlertModalActions');
        const confirmClasses = variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700';
        actions.innerHTML = `
            <button id="headConfirmCancel" class="mr-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
            <button id="headConfirmOk" class="${confirmClasses} text-white font-bold py-2 px-4 rounded">${confirmLabel}</button>
        `;
        headOpenModal();
        document.getElementById('headConfirmCancel').onclick = ()=> { headCloseModal(); onResult && onResult(false); };
        document.getElementById('headConfirmOk').onclick = ()=> { headCloseModal(); onResult && onResult(true); };
    }

    // Confirmation wrapper for head approve/decline
    // Request-specific confirm/delete removed (requests deprecated)

    // Open the Head View modal and load request details via AJAX
    // Unified view function now delegates to proposal loader
    async function openHeadView(proposalId) { return openHeadProposal(proposalId); }

    // Open a Proposal in the Head View (and also open the full Proposal Detail modal)
    async function openHeadProposal(proposalId) {
        try {
            if (!proposalId) return;
            const res = await apiFetch('list_staff_proposals.php?id=' + encodeURIComponent(proposalId), { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Network');
            const data = await res.json();
            const p = (data && data.proposals && data.proposals[0]) ? data.proposals[0] : null;
            if (!p) { showHeadAlert('Not found', 'Proposal not found.'); return; }
            // normalize fields with fallbacks to accommodate API variations
            const desc = p.description || p.details || p.project_description || p.body || p.long_description || p.summary || '';
            const attach = p.attachment || p.file || p.filename || '';
            const hist = Array.isArray(p.history) ? p.history.map(h => ({ status: h.status || h.state, remarks: h.remarks || h.note || h.comment || '', date: h.date || h.timestamp || h.created_at || '', user: h.user_role || h.user || '' })) : [];
            const r = {
                id: p.id,
                request_type: p.title || p.request_type || 'Project Proposal',
                title: p.title || p.request_type || '',
                barangay: p.barangay || p.barangay_name || '',
                submitter: p.submitter || p.submit_by || p.name || '',
                email: p.email || '',
                created_at: p.date || p.created_at || '',
                status: p.status || '',
                location: p.location || '',
                description: desc,
                notes: p.remarks || p.note || p.notes || '',
                attachment: attach,
                history: hist
            };
            // keep existing compact head view in sync
            try { renderHeadView(r); } catch(e){}
            // render and open the full proposal detail modal (copied from Staff view for consistency)
            try { renderDetailView(r); headOpenDetailModal(); } catch (e) { console.warn('detail modal render error', e); }
        } catch (err) {
            console.error(err);
            showHeadAlert('Error', 'Could not load proposal details.');
        }
    }

    // Ensure a globally available status label helper (used by multiple renderers)
    if (typeof window.getStatusLabel !== 'function') {
        window.getStatusLabel = function(s) {
            const st = String(s || '');
            const esc = (v) => String(v || '').replace(/[&<>"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; });
            if (/approved/i.test(st)) return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">Approved</span>';
            if (/declined|denied/i.test(st)) return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-200 text-red-800">Declined</span>';
            return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800">' + esc(st || 'Pending') + '</span>';
        };
    }

    function renderHeadView(r) {
        try {
            // Safe setters: guard against missing elements so this view can be reused
            function setText(id, value) {
                const el = document.getElementById(id);
                if (!el) return;
                el.textContent = value;
            }
            function setHTML(id, value) {
                const el = document.getElementById(id);
                if (!el) return;
                el.innerHTML = value;
            }

            setText('hv_request_type', r.request_type || r.title || '');
            setText('hv_barangay', r.barangay || '');
            setText('hv_submitter', r.submitter || r.email || '');
            setText('hv_date', r.created_at ? new Date(r.created_at).toLocaleString() : '');
            // status uses HTML label markup
            const statusEl = document.getElementById('hv_status'); if (statusEl) statusEl.innerHTML = getStatusLabel(r.status || '');
            setText('hv_location', r.location || '');
            setText('hv_description', r.description || '');
            setText('hv_notes', r.notes || '');
            const attachEl = document.getElementById('hv_attachment'); if (attachEl) attachEl.innerHTML = '';
            const previewEl = document.getElementById('hv_preview'); if (previewEl) { previewEl.innerHTML = ''; previewEl.classList.add('hidden'); }
            if (r.attachment) {
                const rawName = (r.attachment || '').split('/').pop();
                const fileName = rawName || r.attachment;
                const safeName = escapeHtml(fileName);

                // Build app-root relative URL (works when head dashboard is under /head/)
                const pathname = window.location.pathname.replace(/\\/g, '/');
                const appRoot = pathname.replace(/\/head\/.*$/i, '');
                const fileUrl = appRoot + '/uploads/proposals/' + encodeURIComponent(fileName);

                attachEl.innerHTML = `
                    <a href="${fileUrl}" target="_blank" class="text-blue-600 hover:underline">Open</a>
                    <a href="${fileUrl}" download="${safeName}" class="px-3 py-1 text-xs bg-gray-100 border rounded hover:bg-gray-50">Download</a>
                    <div class="text-xs text-gray-600">${safeName}</div>
                `;

                (async () => {
                    try {
                        const head = await fetch(fileUrl, { method: 'HEAD', credentials: 'same-origin' });
                        if (!head.ok) {
                            if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<div class="text-sm text-red-600">Attachment not found on server.</div>`; }
                            return;
                        }
                    } catch (err) {
                        if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<div class="text-sm text-red-600">Could not verify attachment availability.</div>`; }
                        return;
                    }

                    const ext = (fileName.split('.').pop() || '').toLowerCase();
                    try {
                        if (['png','jpg','jpeg','gif','webp','bmp'].includes(ext)) {
                            if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<img src="${fileUrl}" alt="${safeName}" class="mx-auto rounded border max-h-72" />`; }
                        } else if (ext === 'pdf') {
                            if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<iframe src="${fileUrl}" class="w-full h-80 border rounded" title="${safeName}"></iframe>`; }
                        } else {
                            // non-previewable file types: leave preview hidden
                        }
                    } catch (e) { /* ignore preview render errors */ }
                })();

            } else { attachEl.textContent = '—'; }

            // Do not display document history in this Head view (intentionally omitted)
            try { const histEl = document.getElementById('hv_history'); if (histEl) histEl.textContent = '—'; } catch(e) {}

            // actions area: only allow closing from this view (no update-status in this view)
            const actions = document.getElementById('headViewModalActions');
            actions.innerHTML = '';
            const btnClose = document.createElement('button'); btnClose.className = 'bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded'; btnClose.textContent = 'Close'; btnClose.onclick = headViewClose;
            actions.appendChild(btnClose);
        } catch (e) { console.error('renderHeadView error', e); }
    }

    function headViewOpen() {
        const modal = document.getElementById('headViewModal'); if (!modal) return;
        modal.style.display = 'flex'; modal.classList.remove('opacity-0'); modal.classList.add('opacity-100');
        const content = modal.querySelector('.modal-enter'); setTimeout(()=> content && content.classList.add('modal-enter-active'), 10);
        // wire close button
        const closeBtn = document.getElementById('headViewCloseBtn'); if (closeBtn) closeBtn.onclick = headViewClose;
    }

    function headViewClose() {
        const modal = document.getElementById('headViewModal'); if (!modal) return;
        const content = modal.querySelector('.modal-enter'); modal.classList.remove('opacity-100'); modal.classList.add('opacity-0'); content && content.classList.remove('modal-enter-active');
        setTimeout(()=> modal.style.display = 'none', 250);
    }

    // ==== CRUD Modals & Handlers (Create / Edit / Delete Proposal) ====
    function openCreateProposalModal(){
        const m = document.getElementById('proposalCrudModal');
        if(!m) return; resetProposalForm(); m.dataset.mode='create'; m.querySelector('#proposalCrudTitle').textContent='New Proposal'; m.style.display='flex'; requestAnimationFrame(()=>{ m.classList.remove('opacity-0'); m.classList.add('opacity-100'); });
    }
    function openEditProposalModal(id){
        const m = document.getElementById('proposalCrudModal'); if(!m) return;
        const form = document.getElementById('proposalCrudForm'); if(!form) return;
        const all = window._headRequests || [];
        const p = all.find(x=> Number(x.id)===Number(id));
        if(!p) return alert('Proposal not found');
        m.dataset.mode='edit'; m.dataset.proposalId=String(id);
        m.querySelector('#proposalCrudTitle').textContent='Edit Proposal';
        form.title.value = p.title || p.request_type || '';
        form.project_type.value = p.project_type || '';
        form.barangay.value = p.barangay || '';
        form.location.value = p.location || '';
        form.budget.value = p.budget || '';
        form.description.value = p.description || '';
        m.style.display='flex'; requestAnimationFrame(()=>{ m.classList.remove('opacity-0'); m.classList.add('opacity-100'); });
    }
    function closeProposalCrud(){
        const m = document.getElementById('proposalCrudModal'); if(!m) return;
        m.classList.remove('opacity-100'); m.classList.add('opacity-0'); setTimeout(()=> m.style.display='none',250);
    }
    function resetProposalForm(){
        const f = document.getElementById('proposalCrudForm'); if(!f) return;
        ['title','project_type','barangay','location','budget','description'].forEach(k=>{ if(f[k]) f[k].value=''; });
    }
    async function saveProposalCrud(){
        const m = document.getElementById('proposalCrudModal'); const mode = m?.dataset.mode;
        const f = document.getElementById('proposalCrudForm'); if(!f) return;
        const fd = new FormData();
        fd.append('title', f.title.value.trim());
        fd.append('project_type', f.project_type.value.trim());
        fd.append('barangay', f.barangay.value.trim());
        fd.append('location', f.location.value.trim());
        fd.append('budget', f.budget.value.trim());
        fd.append('description', f.description.value.trim());
        try {
            if(mode==='create'){
                const res = await fetch(await getApiBase() + 'submit_project_proposal.php',{method:'POST', body:fd, credentials:'same-origin'});
                const data = await res.json();
                if(!data.success) throw new Error(data.error||'Create failed');
            } else if(mode==='edit') {
                fd.append('id', m.dataset.proposalId || '');
                const res = await fetch(await getApiBase() + 'update_proposal_fields.php',{method:'POST', body:fd, credentials:'same-origin'});
                const data = await res.json();
                if(!data.success) throw new Error(data.error||'Update failed');
            }
            closeProposalCrud();
            if(typeof window.loadRequestsAndPopulate==='function') window.loadRequestsAndPopulate();
        } catch(err){ alert(err.message || 'Operation failed'); }
    }
    function confirmDeleteProposal(id){
        showHeadConfirm('Delete Proposal', 'Delete proposal '+id+'? This cannot be undone.', async (ok)=>{ if(!ok) return; try { const fd = new FormData(); fd.append('id', id); const res = await fetch(await getApiBase() + 'delete_proposal.php',{method:'POST',body:fd,credentials:'same-origin'}); const d = await res.json(); if(!d.success) return alert(d.error||'Delete failed'); if(typeof window.loadRequestsAndPopulate==='function') window.loadRequestsAndPopulate(); } catch(e){ alert('Server error'); } }, 'Delete', 'danger');
    }

    // helper: small visual toast for incoming notifications
    function showNotificationToast(title, body, opts = {}) {
        try {
            const containerId = 'notifToastContainer';
            let container = document.getElementById(containerId);
            if (!container) {
                container = document.createElement('div');
                container.id = containerId;
                container.className = 'notif-toast-container';
                document.body.appendChild(container);
            }
            
            // Limit to maximum 3 toasts at once
            const existingToasts = container.querySelectorAll('.notif-toast');
            if (existingToasts.length >= 3) {
                // Remove oldest toast
                existingToasts[0].remove();
            }
            
            const toast = document.createElement('div');
            toast.className = 'notif-toast notif-toast--new';
            toast.innerHTML = `<div class="notif-toast__title">${escapeHtml(title || 'Notification')}</div><div class="notif-toast__body">${escapeHtml(body || '')}</div>`;
            toast.onclick = () => {
                // if a requestId was provided, jump to that request in the UI
                const rid = opts.requestId || opts.request_id || toast.getAttribute('data-request-id');
                if (rid) {
                    try { jumpToRequest(parseInt(rid,10)); } catch(e) { console.warn('jump error', e); }
                }
                // open notifications dropdown and refresh
                const dd = document.getElementById('headNotifDropdown');
                if (dd) { dd.classList.remove('hidden'); }
                if (typeof window.fetchHeadNotifs === 'function') window.fetchHeadNotifs();
                // also focus bell
                const bell = document.getElementById('headNotifBell'); if (bell) bell.focus();
                // remove toast immediately
                toast.remove();
            };
            if (opts.requestId) toast.setAttribute('data-request-id', String(opts.requestId));
            container.appendChild(toast);
            // auto-dismiss
            setTimeout(() => { try { toast.style.opacity = '0'; setTimeout(()=> toast.remove(), 250); } catch(e){} }, opts.duration || 4000);
        } catch (e) { console.warn('Toast error', e); }
    }

    // SSE listener to refresh dashboard when notifications relevant to Head arrive
    try {
        (async () => {
            const b = await getApiBase();
            const headSse = new EventSource(b + 'notifications_stream.php');
            window._headSse = headSse;
            headSse.addEventListener('notification', (e) => {
                try {
                    const payload = JSON.parse(e.data || '{}');
                    try { if (payload && (payload.target_role === 'OPMDC Head' || payload.target_user_id)) incrementHeadBadge(); } catch (ee) {}
                    if (!payload.target_role || payload.target_role === 'OPMDC Head' || payload.target_user_id) {
                        if (typeof loadRequestsAndPopulate === 'function') loadRequestsAndPopulate();
                        if (typeof window.fetchHeadNotifs === 'function') window.fetchHeadNotifs();
                        const bEl = document.getElementById('headNotifBadge');
                        if (bEl) bEl.classList.remove('hidden');
                    }
                } catch (err) { console.error('SSE payload parse error', err); }
            });
            headSse.addEventListener('error', (err) => { console.warn('Head SSE error', err); headSse.close(); });
        })();
    } catch (e) { console.warn('Head SSE not available', e); }
    // --- Submissions badge (Head) ---
    const _badgeKeyHead = 'opmdcNewSubmissions_Head';
    function loadHeadBadgeCount() {
        try {
            const n = parseInt(localStorage.getItem(_badgeKeyHead) || '0', 10) || 0;
            setHeadBadgeCount(n);
        } catch (e) { console.warn('loadHeadBadgeCount error', e); }
    }
    function setHeadBadgeCount(n) {
        const el = document.getElementById('submissionsBadge');
        if (!el) return;
        try {
            n = Math.max(0, parseInt(n || 0, 10));
            if (n > 0) { el.textContent = String(n); el.classList.remove('hidden'); }
            else { el.textContent = '0'; el.classList.add('hidden'); }
            localStorage.setItem(_badgeKeyHead, String(n));
        } catch (e) { console.warn('setHeadBadgeCount error', e); }
    }
    function incrementHeadBadge() { try { const cur = parseInt(localStorage.getItem(_badgeKeyHead) || '0',10) || 0; setHeadBadgeCount(cur + 1); } catch(e){console.warn(e);} }
    function clearHeadBadge() { setHeadBadgeCount(0); }

    // Clear when the Submissions link is clicked
    const submissionsLink = document.getElementById('submissions-link');
    if (submissionsLink) submissionsLink.addEventListener('click', () => { clearHeadBadge(); });

// Head notifications
document.addEventListener('DOMContentLoaded', () => {
    const headBell = document.getElementById('headNotifBell');
    const headBadge = document.getElementById('headNotifBadge');
    const headDropdown = document.getElementById('headNotifDropdown');
    const headList = document.getElementById('headNotifList');
    const headMarkAllRead = document.getElementById('headMarkAllRead');

    async function fetchHeadNotifs() {
        try {
            const res = await apiFetch('notifications.php?role=' + encodeURIComponent('OPMDC Head'));
            const data = await res.json();
            renderHeadNotifs(data.notifications || []);
        } catch (err) {
            headList.innerHTML = '<div class="text-center text-gray-500">Could not load notifications</div>';
        }
    }

    function renderHeadNotifs(notes) {
        if (!notes.length) { headList.innerHTML = '<div class="text-center text-gray-500">No notifications.</div>'; headBadge.classList.add('hidden'); return; }
        const unread = notes.filter(n => n.is_read == 0).length;
        if (unread > 0) headBadge.classList.remove('hidden'); else headBadge.classList.add('hidden');
        headList.innerHTML = '';
        notes.forEach(n => {
            const div = document.createElement('div');
            div.className = 'p-2 border-b';
            div.innerHTML = `<div class="flex justify-between"><div><strong>${escapeHtml(n.title)}</strong><div class="text-xs text-gray-600">${escapeHtml(n.body)}</div><div class="text-xs text-gray-400 mt-1">${n.created_at}</div></div><div class="flex flex-col items-end space-y-1"><button class="mark-read text-xs text-blue-600" data-id="${n.id}">${n.is_read==0? 'Mark read':'Unread'}</button><button class="delete text-xs text-red-600" data-id="${n.id}">Delete</button></div></div>`;
            headList.appendChild(div);
        });
        headList.querySelectorAll('.mark-read').forEach(btn => btn.addEventListener('click', async (e) => { const id = e.target.dataset.id; await apiFetch('notifications_mark_read.php?id=' + encodeURIComponent(id), { method: 'POST' }); fetchHeadNotifs(); }));
        headList.querySelectorAll('.delete').forEach(btn => btn.addEventListener('click', async (e) => { const id = e.target.dataset.id; await apiFetch('notifications_delete.php?id=' + encodeURIComponent(id), { method: 'POST' }); fetchHeadNotifs(); }));
    }

    headBell && headBell.addEventListener('click', (e) => { e.stopPropagation(); headDropdown.classList.toggle('hidden'); if (!headDropdown.classList.contains('hidden')) fetchHeadNotifs(); });
    document.addEventListener('click', () => headDropdown.classList.add('hidden'));
    headMarkAllRead && headMarkAllRead.addEventListener('click', async () => { const res = await apiFetch('notifications.php?role=' + encodeURIComponent('OPMDC Head')); const data = await res.json(); for (const n of data.notifications || []) { await apiFetch('notifications_mark_read.php?id=' + encodeURIComponent(n.id), { method: 'POST' }); } fetchHeadNotifs(); });

    function escapeHtml(s) { return String(s).replace(/[&<>\"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#39;'})[c]; }); }

    // expose for external callers (SSE handler) so incoming events can refresh the widget
    window.fetchHeadNotifs = fetchHeadNotifs;
    // initialize head submissions badge count
    try { if (typeof loadHeadBadgeCount === 'function') loadHeadBadgeCount(); } catch (e) {}
    fetchHeadNotifs();
});

// ====== Analytics Charts (Chart.js) ======
let proposalsChart = null;
let barangayChart = null;
let approvalChart = null;
let avgTurnaroundChart = null;

async function fetchAnalytics() {
    try {
        // read date inputs when present
        const start = document.getElementById('analyticsStart') ? document.getElementById('analyticsStart').value : '';
        const end = document.getElementById('analyticsEnd') ? document.getElementById('analyticsEnd').value : '';
        const base = await getApiBase();
        let url = base + 'analytics_api.php';
        const params = [];
        if (start) params.push('start=' + encodeURIComponent(start));
        if (end) params.push('end=' + encodeURIComponent(end));
        if (params.length) url += '?' + params.join('&');
        const res = await apiFetch(url.replace(base, '') ? url.replace(base, '') : 'analytics_api.php', { credentials: 'same-origin' });
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

        // Barangay chart (proposals only)
        const bctx = document.getElementById('barangayChart');
        const bLabels = (d.proposals_by_barangay || []).map(x => x.barangay || 'Unknown');
        const bData = (d.proposals_by_barangay || []).map(x => x.count || 0);
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
                        data: { labels: bLabels, datasets: [{ label: 'Proposals', data: bData, backgroundColor: colors }] },
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

        // Insights (structured objects with severity) — render as professional table
        const insightsEl = document.getElementById('analyticsInsights');
        if (insightsEl) {
            insightsEl.innerHTML = '';
            if (Array.isArray(d.insights) && d.insights.length) {
                const tableWrapper = document.createElement('div');
                tableWrapper.className = 'border border-gray-200 rounded-lg overflow-hidden';
                
                const table = document.createElement('table');
                table.className = 'w-full text-sm';
                table.setAttribute('role', 'table');
                table.style.borderCollapse = 'separate';
                table.style.borderSpacing = '0';

                const thead = document.createElement('thead');
                thead.className = 'bg-gradient-to-r from-slate-700 to-slate-600 text-white';
                const htr = document.createElement('tr');
                htr.innerHTML = `
                    <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="width: 130px;">Severity</th>
                    <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider">Insight Message</th>
                    <th class="text-center px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="width: 120px;">Reference</th>
                    <th class="text-center px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="width: 80px;">Details</th>
                `;
                thead.appendChild(htr);
                table.appendChild(thead);

                const tbody = document.createElement('tbody');
                tbody.className = 'bg-white divide-y divide-gray-200';
                // helper to build a tooltip/detail string for each insight
                const getInsightDetail = (insight, analytics) => {
                    try {
                        const code = (typeof insight === 'string') ? '' : (insight.code || '');
                        if (code === 'TOP_BARANGAY') {
                            const top = (analytics.proposals_by_barangay && analytics.proposals_by_barangay[0]) || null;
                            const total = (analytics.counts && analytics.counts.total_proposals) || 0;
                            if (top) return `${top.barangay}: ${top.count} proposals (${total ? Math.round((top.count/total)*100*10)/10 : 0}% of total)`;
                        }
                        // PENDING_REQUESTS removed (requests deprecated)
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
                        // OLD_PENDING removed
                        // fallback: show raw message
                        return (typeof insight === 'string') ? insight : (insight.message || 'No additional details');
                    } catch (e) { return insight.message || String(insight); }
                };

                d.insights.forEach((i, index) => {
                    const msg = (typeof i === 'string') ? i : (i.message || '');
                    const sev = (typeof i === 'string') ? 'low' : (i.severity || 'low');
                    const code = (typeof i === 'string') ? '' : (i.code || '');

                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors duration-150';
                    if (index % 2 === 0) tr.classList.add('bg-gray-50');

                    // Severity Column
                    const sevTd = document.createElement('td');
                    sevTd.className = 'px-4 py-3 align-middle';
                    const badgeWrap = document.createElement('div');
                    badgeWrap.className = 'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold';
                    
                    const badge = document.createElement('span');
                    badge.style.width = '8px'; 
                    badge.style.height = '8px'; 
                    badge.style.borderRadius = '50%';
                    badge.style.flexShrink = '0';
                    
                    let badgeColor, bgClass, textClass;
                    if (sev === 'high') {
                        badgeColor = '#D55E00';
                        bgClass = 'bg-orange-100';
                        textClass = 'text-orange-800';
                    } else if (sev === 'medium') {
                        badgeColor = '#F0E442';
                        bgClass = 'bg-yellow-100';
                        textClass = 'text-yellow-800';
                    } else {
                        badgeColor = '#009E73';
                        bgClass = 'bg-green-100';
                        textClass = 'text-green-800';
                    }
                    
                    badge.style.background = badgeColor;
                    badge.style.boxShadow = `0 0 0 3px ${badgeColor}20`;
                    badgeWrap.className += ` ${bgClass} ${textClass}`;
                    
                    const sevLabel = document.createElement('span');
                    sevLabel.textContent = sev.charAt(0).toUpperCase() + sev.slice(1);
                    
                    badgeWrap.appendChild(badge);
                    badgeWrap.appendChild(sevLabel);
                    sevTd.appendChild(badgeWrap);

                    // Message Column
                    const msgTd = document.createElement('td');
                    msgTd.className = 'px-4 py-3 text-sm text-slate-700 whitespace-normal leading-relaxed';
                    msgTd.textContent = msg;

                    // Code/Reference Column
                    const codeTd = document.createElement('td');
                    codeTd.className = 'px-4 py-3 text-center align-middle';
                    if (code) {
                        const codeSpan = document.createElement('span');
                        codeSpan.className = 'inline-block px-2 py-1 text-xs font-mono bg-slate-100 text-slate-700 rounded border border-slate-200';
                        codeSpan.textContent = code;
                        codeTd.appendChild(codeSpan);
                    } else {
                        codeTd.innerHTML = '<span class="text-gray-400 text-xs">—</span>';
                    }

                    // Details/Info Column
                    const actionTd = document.createElement('td');
                    actionTd.className = 'px-4 py-3 text-center align-middle';
                    const infoBtn = document.createElement('button');
                    infoBtn.type = 'button';
                    infoBtn.className = 'inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all duration-200 border border-blue-200';
                    infoBtn.style.cursor = 'pointer';
                    const detail = getInsightDetail(i, d);
                    infoBtn.title = 'View detailed information';
                    infoBtn.setAttribute('aria-label', 'View insight details');
                    infoBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    infoBtn.onclick = function(e) { 
                        e.stopPropagation(); 
                        if (typeof showInsightModal === 'function') {
                            showInsightModal(code || 'Insight Details', detail);
                        } else {
                            alert(detail);
                        }
                    };
                    actionTd.appendChild(infoBtn);

                    tr.appendChild(sevTd);
                    tr.appendChild(msgTd);
                    tr.appendChild(codeTd);
                    tr.appendChild(actionTd);
                    tbody.appendChild(tr);
                });

                table.appendChild(tbody);
                tableWrapper.appendChild(table);
                insightsEl.appendChild(tableWrapper);
            } else {
                const emptyState = document.createElement('div');
                emptyState.className = 'text-center py-12 px-4';
                emptyState.innerHTML = `
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No Insights Available</h3>
                    <p class="text-sm text-gray-500">There are currently no actionable insights to display for the selected period.</p>
                `;
                insightsEl.appendChild(emptyState);
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
<!-- Insight Detail Modal -->
<div id="insightDetailModal" class="fixed inset-0 z-60 items-center justify-center hidden transition-all duration-300 opacity-0">
    <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white rounded-xl shadow-2xl m-auto p-0 w-full max-w-2xl z-10 modal-enter overflow-hidden">
        <div class="bg-gradient-to-r from-slate-700 to-slate-600 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 id="insightDetailTitle" class="text-xl font-bold text-white">Insight Details</h2>
            </div>
            <button id="closeInsightModalBtn" class="text-white hover:text-gray-200 text-2xl transition-colors duration-200 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white hover:bg-opacity-20">&times;</button>
        </div>
        <div class="p-6">
            <div id="insightDetailBody" class="text-sm text-gray-700 whitespace-pre-wrap max-h-[60vh] overflow-auto bg-gray-50 p-4 rounded-lg border border-gray-200 leading-relaxed"></div>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="document.getElementById('closeInsightModalBtn').click()" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors duration-200 font-medium">
                Close
            </button>
        </div>
    </div>
</div>

    <!-- Full Proposal Detail Modal (copied from Staff for consistency) -->
    <div id="proposalDetailModal" class="fixed inset-0 z-80 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-xl shadow-2xl m-auto p-8 w-full max-w-5xl z-10 max-h-[92vh] flex flex-col modal-enter">
            <div class="flex justify-between items-center mb-5 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-extrabold text-gray-900">Proposal Details</h2>
                <button id="closeDetailModalBtn" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">&times;</button>
            </div>
            <div class="flex-grow overflow-y-auto pr-2">
                <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-6 bg-gray-50 p-4 rounded-lg">
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Project Title</p>
                        <p id="detail-title" class="text-lg font-semibold text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Barangay</p>
                        <p id="detail-barangay" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date Submitted</p>
                        <p id="detail-date" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Current Status</p>
                        <p id="detail-status" class="text-sm font-medium"></p>
                    </div>
                </div>
                <div class="mb-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Project Description</p>
                    <div id="detail-description" class="text-lg text-gray-800 leading-relaxed bg-white p-4 rounded border border-gray-200 max-h-60 overflow-y-auto"></div>
                </div>
                <div class="mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Attachment</p>
                    <div id="detail-attachment" class="flex items-center gap-3 text-sm text-gray-700"></div>
                </div>
                <div id="detail-preview" class="mb-5 bg-gray-50 p-3 rounded border border-gray-200 max-h-80 overflow-auto hidden"></div>
                <div class="mb-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wide">Document History</h3>
                    <div id="proposal-history-list" class="space-y-2 max-h-48 overflow-y-auto bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
                </div>
                <form id="proposalUpdateForm">
                    <input type="hidden" id="proposalId">
                    <h3 class="text-sm font-bold text-gray-800 mb-4 border-t border-gray-200 pt-5 uppercase tracking-wide">Update Status</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="proposalStatus" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">New Status</label>
                            <select id="proposalStatus" name="proposalStatus" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option>For Review</option>
                                <option>Processing</option>
                                <option>Requires Revision</option>
                                <option>For Head Approval</option>
                                <option>Approved</option>
                                <option>Declined</option>
                            </select>
                        </div>
                        <div>
                            <label for="remarks" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">Remarks <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea id="remarks" name="remarks" rows="2" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Add a comment..."></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-5 gap-3">
                        <button type="button" id="cancelUpdateBtn" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Detail modal helpers and renderer for Head (keeps parity with Staff modal)
    function headOpenDetailModal() {
        const modal = document.getElementById('proposalDetailModal'); if (!modal) return;
        modal.style.display = 'flex'; modal.classList.remove('opacity-0'); modal.classList.add('opacity-100');
        const content = modal.querySelector('.modal-enter'); setTimeout(()=> content && content.classList.add('modal-enter-active'), 10);
        const closeBtn = document.getElementById('closeDetailModalBtn'); if (closeBtn) closeBtn.onclick = headCloseDetailModal;
    }
    function headCloseDetailModal() {
        const modal = document.getElementById('proposalDetailModal'); if (!modal) return;
        const content = modal.querySelector('.modal-enter'); modal.classList.remove('opacity-100'); modal.classList.add('opacity-0'); content && content.classList.remove('modal-enter-active');
        setTimeout(()=> modal.style.display = 'none', 250);
    }

    function renderDetailView(r) {
        try {
            const setText = (id, value) => { const el = document.getElementById(id); if (!el) return; el.textContent = value; };
            const setHTML = (id, value) => { const el = document.getElementById(id); if (!el) return; el.innerHTML = value; };

            setText('detail-title', r.title || r.request_type || 'Project Proposal');
            setText('detail-barangay', r.barangay || '');
            setText('detail-date', r.created_at ? new Date(r.created_at).toLocaleString() : '');
            const statusEl = document.getElementById('detail-status'); if (statusEl) statusEl.innerHTML = getStatusLabel(r.status || '');
            // show description safely; preserve newlines
            const descEl = document.getElementById('detail-description');
            if (descEl) {
                const txt = String(r.description || '').trim();
                if (!txt) {
                    descEl.textContent = '';
                } else {
                    // escape then convert newlines to <br>
                    descEl.innerHTML = escapeHtml(txt).replace(/\n/g, '<br>');
                }
            }
            // set hidden input value properly
            const pid = document.getElementById('proposalId'); if (pid) { try { pid.value = r.id || ''; } catch(e){ pid.textContent = r.id || ''; } }

            const attachEl = document.getElementById('detail-attachment'); if (attachEl) attachEl.innerHTML = '';
            const previewEl = document.getElementById('detail-preview'); if (previewEl) { previewEl.innerHTML = ''; previewEl.classList.add('hidden'); }

            if (r.attachment) {
                const rawName = (r.attachment || '').split('/').pop();
                const fileName = rawName || r.attachment;
                const safeName = escapeHtml(fileName);
                const pathname = window.location.pathname.replace(/\\/g, '/');
                const appRoot = pathname.replace(/\/head\/.*$/i, '');
                const fileUrl = appRoot + '/uploads/proposals/' + encodeURIComponent(fileName);
                if (attachEl) {
                    attachEl.innerHTML = `\n                        <a href="${fileUrl}" target="_blank" class="text-blue-600 hover:underline">Open</a>
                        <a href="${fileUrl}" download="${safeName}" class="px-3 py-1 text-xs bg-gray-100 border rounded hover:bg-gray-50">Download</a>
                        <div class="text-xs text-gray-600">${safeName}</div>\n                    `;
                }
                (async () => {
                    try {
                        const head = await fetch(fileUrl, { method: 'HEAD', credentials: 'same-origin' });
                        if (!head.ok) { if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<div class="text-sm text-red-600">Attachment not found on server.</div>`; } return; }
                    } catch (err) { if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<div class="text-sm text-red-600">Could not verify attachment availability.</div>`; } return; }

                    const ext = (fileName.split('.').pop() || '').toLowerCase();
                    try {
                        if (['png','jpg','jpeg','gif','webp','bmp'].includes(ext)) {
                            if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<img src="${fileUrl}" alt="${safeName}" class="mx-auto rounded border max-h-72" />`; }
                        } else if (ext === 'pdf') {
                            if (previewEl) { previewEl.classList.remove('hidden'); previewEl.innerHTML = `<iframe src="${fileUrl}" class="w-full h-80 border rounded" title="${safeName}"></iframe>`; }
                        } else { /* leave preview hidden for other types */ }
                    } catch (e) { /* ignore */ }
                })();
            } else { if (attachEl) attachEl.textContent = '—'; }

            // history
            const histEl = document.getElementById('proposal-history-list'); if (histEl) {
                histEl.innerHTML = '';
                (r.history || []).slice().reverse().forEach(h => {
                    const div = document.createElement('div'); div.className = 'bg-white p-3 rounded border';
                    const by = h.user || ''; const when = h.date ? new Date(h.date).toLocaleString() : '';
                    div.innerHTML = `<div class="text-xs text-gray-500">${escapeHtml(when)} ${by ? ' — ' + escapeHtml(by) : ''}</div><div class="text-sm text-gray-800 mt-1">${escapeHtml(h.remarks || h.note || '')}</div>`;
                    histEl.appendChild(div);
                });
                if (!(r.history && r.history.length)) histEl.innerHTML = '<div class="text-sm text-gray-500">No document history.</div>';
            }
        } catch (e) { console.error('renderDetailView error', e); }
    }

    // Wire up detail modal form handlers
    document.addEventListener('click', (ev) => {
        if (ev.target && ev.target.classList && ev.target.classList.contains('modal-backdrop')) {
            headCloseDetailModal();
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
        const closeBtn = document.getElementById('closeDetailModalBtn'); if (closeBtn) closeBtn.onclick = headCloseDetailModal;
        const cancelBtn = document.getElementById('cancelUpdateBtn'); if (cancelBtn) cancelBtn.onclick = headCloseDetailModal;
        const form = document.getElementById('proposalUpdateForm'); if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('proposalId')?.value;
                const newStatus = document.getElementById('proposalStatus')?.value;
                if (!id || !newStatus) return showHeadAlert('Missing', 'Proposal id or status missing.');
                const ok = await updateSubmissionStatus(id, newStatus);
                if (ok) { headCloseDetailModal(); if (typeof loadRequestsAndPopulate === 'function') loadRequestsAndPopulate(); }
            });
        }
    });
    </script>
<!-- Toast container (created dynamically if needed) -->
<div id="notifToastContainer" class="notif-toast-container" aria-live="polite"></div>

<!-- Proposal CRUD Modal -->
<div id="proposalCrudModal" class="fixed inset-0 z-60 items-center justify-center hidden transition-all duration-300 opacity-0">
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>
    <div class="bg-white rounded-lg shadow-xl m-auto p-6 w-full max-w-xl z-10 modal-enter">
        <div class="flex justify-between items-center mb-4">
            <h2 id="proposalCrudTitle" class="text-xl font-bold text-gray-800">New Proposal</h2>
            <button type="button" onclick="closeProposalCrud()" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
        </div>
        <form id="proposalCrudForm" class="space-y-4" onsubmit="event.preventDefault(); saveProposalCrud();">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Title</label>
                <input name="title" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" required />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Project Type</label>
                    <select name="project_type" class="mt-1 w-full border rounded px-3 py-2 text-sm" required>
                        <option value="">Select type</option>
                        <option value="CLUP">CLUP</option>
                        <option value="CDP">CDP</option>
                        <option value="AIP">AIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Barangay</label>
                    <input name="barangay" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" required />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Location</label>
                    <input name="location" type="text" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Budget (PHP)</label>
                    <input name="budget" type="number" step="0.01" min="0" class="mt-1 w-full border rounded px-3 py-2 text-sm" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Description</label>
                <textarea name="description" rows="4" class="mt-1 w-full border rounded px-3 py-2 text-sm" required></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeProposalCrud()" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>
</body> 
</html>

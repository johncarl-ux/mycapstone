<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPMDC Head Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Logo formal and clean - shared style with other dashboards */
        .logo-formal {
            width: 7rem;
            height: 7rem;
            border-radius: 9999px;
            background: #fff;
            display: block;
            object-fit: cover;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 0.75rem;
            border: 2px solid #e5e7eb;
            animation: logo-pop 420ms ease-out both;
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
            background-color: #4A5568;
            color: #FFFFFF;
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
    <link rel="stylesheet" href="assets/ui-animations.css">
</head>
<body class="bg-gray-100">
 
    <div class="flex h-screen overflow-hidden">
    <aside class="sidebar w-64 bg-slate-800 text-white flex flex-col" data-reveal="sidebar">
            <div class="p-6 text-center border-b border-gray-700">
                <img src="assets/image1.png" alt="Mabini Seal" class="logo-formal">
                <h2 id="sidebar-role-title" class="text-xl font-semibold">OPMDC Head</h2>
                <p class="text-sm text-gray-400">Oversight Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 active-nav-link" onclick="showPage('dashboard')">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
                </a>
                <a href="#" id="submissions-link" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('submissions')">
                    <i class="fas fa-file-alt w-5 mr-3"></i> Submissions
                    <span id="submissionsBadge" class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-white bg-red-600 rounded-full hidden">0</span>
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('resources')">
                    <i class="fas fa-cubes w-5 mr-3"></i> Resource Allocation
                </a>
                <!-- Strategic Planning and Policy Development removed from sidebar for Head role -->
            </nav>
            <div class="p-4 border-t border-gray-700">
                <a href="login.html" class="flex items-center w-full px-4 py-2 rounded-md text-red-400 hover:bg-red-500 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5 mr-3"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
            <div class="container mx-auto px-6 py-8">
                
                <div id="dashboard" class="page-content" style="display: block;">
                    <div class="flex items-start justify-between" data-reveal="header">
                        <h3 class="text-3xl font-medium text-gray-700">Dashboard Analytics</h3>
                        <div class="relative">
                            <button id="headNotifBell" title="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none">
                                <i class="fas fa-bell text-gray-600 text-lg"></i>
                                <span id="headNotifBadge" class="notification-dot hidden"></span>
                            </button>
                            <div id="headNotifDropdown" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg z-50 w-80">
                                <div class="p-3 border-b flex items-center justify-between"><strong>Notifications</strong><button id="headMarkAllRead" class="text-xs text-blue-600">Mark all read</button></div>
                                <div id="headNotifList" class="max-h-64 overflow-auto p-2"><div class="text-center text-gray-500">Loading…</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-reveal="group">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold text-gray-600">Total Submissions</h4>
                            <p class="text-3xl font-bold text-gray-800" id="card-total">142</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-lg fosnt-semibold text-gray-600">Pending Approvals</h4>
                            <p class="text-3xl font-bold text-yellow-500" id="card-pending">12</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold text-gray-600">Active Projects</h4>
                            <p class="text-3xl font-bold text-blue-500" id="card-active">35</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h4 class="text-lg font-semibold text-gray-600">Registered Users</h4>
                            <p class="text-3xl font-bold text-green-500" id="card-users">89</p>
                        </div>
                    </div>

                    <!-- INSERTED: Descriptive Analytics Section -->
                    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                        <h4 class="text-xl font-semibold text-gray-700">Descriptive Analytics</h4>
                        <div class="desc-summary" id="descSummary">Loading summary…</div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h5 class="text-sm text-gray-600 mb-2">By Type</h5>
                                <div id="byTypeLoading" class="text-sm text-gray-500 mb-2">Loading…</div>
                                <table id="tblByType" class="w-full desc-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left text-xs text-gray-500">Type</th>
                                            <th class="text-right text-xs text-gray-500">Count</th>
                                            <th class="text-right text-xs text-gray-500">%</th>
                                            <th class="text-left text-xs text-gray-500">Visual</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <div>
                                <h5 class="text-sm text-gray-600 mb-2">Top Barangays</h5>
                                <table class="w-full desc-table">
                                    <thead>
                                        <tr><th class="text-left text-xs text-gray-500">Barangay</th><th class="text-right text-xs text-gray-500">Count</th><th class="text-right text-xs text-gray-500">%</th></tr>
                                    </thead>
                                    <tbody id="tblTopBarangay"></tbody>
                                </table>
                            </div>

                            <div>
                                <h5 class="text-sm text-gray-600 mb-2">Review Time (mins)</h5>
                                <div id="reviewStats" class="text-sm text-gray-700"></div>
                            </div>
                        </div>
                    </div>
                    <!-- END INSERTED -->

                    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <canvas id="requestStatusChart"></canvas>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <canvas id="accountStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <div id="submissions" class="page-content">
                    <h3 class="text-3xl font-medium text-gray-700">Manage Submissions</h3>
                    <!-- Animated quick widget for Manage Submissions -->
                    <div class="mt-4 mb-2 grid grid-cols-1 md:grid-cols-3 gap-4" id="submissions-widget" data-reveal="group">
                        <div class="bg-white p-4 stat-card shadow-sm flex items-center space-x-4 cursor-pointer" data-status="approved">
                            <div class="p-3 rounded-md bg-green-50 text-green-600"><i class="fas fa-check-circle fa-lg"></i></div>
                            <div>
                                <div class="text-sm text-gray-600">Approved</div>
                                <div id="sub-card-approved" class="stat-count text-green-700">0</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 stat-card shadow-sm flex items-center space-x-4 cursor-pointer" data-status="pending">
                            <div class="p-3 rounded-md bg-yellow-50 text-yellow-600"><i class="fas fa-hourglass-half fa-lg"></i></div>
                            <div>
                                <div class="text-sm text-gray-600">Pending</div>
                                <div id="sub-card-pending" class="stat-count text-yellow-600">0</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 stat-card shadow-sm flex items-center space-x-4 cursor-pointer" data-status="declined">
                            <div class="p-3 rounded-md bg-red-50 text-red-600"><i class="fas fa-times-circle fa-lg"></i></div>
                            <div>
                                <div class="text-sm text-gray-600">Declined</div>
                                <div id="sub-card-declined" class="stat-count text-red-600">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-2 mb-4">
                        <div class="last-updated-badge">Last updated: <span id="sub-widget-updated" class="last-updated-time">—</span></div>
                        <div class="text-xs text-gray-500">Click a card to filter</div>
                    </div>
                    <div class="mt-6 bg-white p-6 rounded-lg shadow-md overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4">Project Name</th>
                                    <th class="py-3 px-4">Submitted By</th>
                                    <th class="py-3 px-4">Date</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="submissions-table-body"></tbody>
                        </table>
                    </div>
                    <!-- Submission Preview Widget (animated) -->
                    <div id="submissionPreview" class="submission-preview" role="region" aria-hidden="true" aria-labelledby="pv_title" aria-describedby="pv_description" tabindex="-1">
                        <div class="submission-preview__header">
                            <div>
                                <div id="pv_title" class="submission-preview__title">—</div>
                                <div id="pv_meta" class="submission-preview__meta">—</div>
                            </div>
                            <button id="pv_close" type="button" aria-label="Close preview" class="text-gray-400 hover:text-gray-700">&times;</button>
                        </div>
                        <div class="submission-preview__body">
                            <div><strong>Location:</strong> <span id="pv_location">—</span></div>
                            <div class="mt-2"><strong>Description:</strong><div id="pv_description" class="mt-1 text-sm text-gray-700">—</div></div>
                            <div class="mt-2"><strong>Attachment:</strong> <div id="pv_attachment">—</div></div>
                            <div class="mt-2"><strong>History:</strong> <div id="pv_history" class="mt-1 text-xs text-gray-600">—</div></div>
                        </div>
                        <div class="submission-preview__actions">
                            <button id="pv_view_btn" type="button" aria-label="Open full view" class="text-sm text-blue-600">View</button>
                            <button id="pv_approve_btn" type="button" aria-label="Approve request" class="text-sm text-green-600">Approve</button>
                            <button id="pv_decline_btn" type="button" aria-label="Decline request" class="text-sm text-red-600">Decline</button>
                        </div>
                    </div>
                </div>
                
                <div id="resources" class="page-content">
                  <h3 class="text-3xl font-medium text-gray-700">Resource Allocation</h3>
                  <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                      <table class="w-full text-sm text-left text-gray-500">
                          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                              <tr>
                                  <th class="py-3 px-4">Project/Department</th>
                                  <th class="py-3 px-4">Budget Allocated</th>
                                  <th class="py-3 px-4">Personnel</th>
                                  <th class="py-3 px-4">Status</th>
                              </tr>
                          </thead>
                          <tbody id="resource-table-body"></tbody>
                      </table>
                  </div>
                </div>

                <!-- Strategic Planning section removed for Head dashboard -->
                
                <!-- Policy Development section removed for Head dashboard -->

            </div>
        </main>
    </div>

            <script src="assets/ui-animations.js"></script>

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
                <div class="mb-2"><strong>Project / Type:</strong> <span id="hv_request_type"></span></div>
                <div class="mb-2"><strong>Barangay:</strong> <span id="hv_barangay"></span></div>
                <div class="mb-2"><strong>Submitted By:</strong> <span id="hv_submitter"></span></div>
                <div class="mb-2"><strong>Date:</strong> <span id="hv_date"></span></div>
                <div class="mb-2"><strong>Status:</strong> <span id="hv_status"></span></div>
                <div class="mb-2"><strong>Location:</strong> <span id="hv_location"></span></div>
                <div class="mb-2"><strong>Description:</strong><div id="hv_description" class="mt-1 p-2 bg-gray-50 rounded"></div></div>
                <div class="mb-2"><strong>Notes:</strong><div id="hv_notes" class="mt-1 p-2 bg-gray-50 rounded"></div></div>
                <div class="mb-2"><strong>Attachment:</strong> <div id="hv_attachment"></div></div>
                <div class="mb-2"><strong>History:</strong><div id="hv_history" class="mt-1 p-2 bg-gray-50 rounded text-xs"></div></div>
            </div>
            <div id="headViewModalActions" class="flex items-center justify-end mt-4 space-x-2">
                <!-- Buttons will be generated by JS based on status -->
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
        // Populate head dashboard from server API `list_requests.php` and fallback to localStorage when needed.
        async function loadRequestsAndPopulate() {
            let requests = [];
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
                const res = await fetch('list_requests.php', { credentials: 'same-origin' });
                if (!res.ok) throw new Error('Server response not ok');
                const data = await res.json();
                requests = data.requests || [];
            } catch (err) {
                // Server unavailable — do not fall back to localStorage. Show empty list and log.
                console.warn('Could not load server requests:', err);
                requests = [];
            }

            // expose latest requests for UI helpers (preview widget, jumpToRequest, etc.)
            try { window._headRequests = requests; } catch (e) { /* ignore */ }

            // Update cards — prefer server-side aggregates for accuracy
            let agg = null;
            try {
                const aggRes = await fetch('aggregate_requests.php', { credentials: 'same-origin' });
                if (aggRes.ok) agg = await aggRes.json();
            } catch (e) { console.warn('aggregate fetch failed', e); }

            const total = (agg && agg.aggregate && typeof agg.aggregate.total !== 'undefined') ? Number(agg.aggregate.total) : requests.length;
            const approved = (agg && agg.aggregate && typeof agg.aggregate.approved !== 'undefined') ? Number(agg.aggregate.approved) : requests.filter(r => String(r.status).toLowerCase() === 'approved').length;
            const declined = (agg && agg.aggregate && typeof agg.aggregate.declined !== 'undefined') ? Number(agg.aggregate.declined) : requests.filter(r => String(r.status).toLowerCase() === 'declined').length;
            const pending = (agg && agg.aggregate && typeof agg.aggregate.pending !== 'undefined') ? Number(agg.aggregate.pending) : (total - (approved + declined));

            document.getElementById('card-total').innerText = total;
            document.getElementById('card-pending').innerText = pending;
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

            // Active Projects & Registered Users are not part of requests; keep previous placeholders or calculate if you have endpoints
            // For now, clear or keep existing values — leave as-is

            // Populate submissions table (respect filter if set)
            const subBody = document.getElementById('submissions-table-body');
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
                        if (s.includes('pending') && filter === 'pending') return true;
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
                        // download link if attachment present
                        if (r.attachment) {
                            actions.push(`<a href="${r.attachment}" target="_blank" class="text-sm text-blue-600 mr-2">Download</a>`);
                        }
                        // always allow viewing details
                        actions.push(`<button class="text-sm text-blue-600" onclick="openHeadView(${r.id})">View</button>`);
                        const isFinalized = /approved|declined/i.test(String(r.status || ''));
                        if (!isFinalized) {
                            actions.push(`<button class="ml-2 text-sm text-green-600" onclick="confirmUpdateRequestStatus(${r.id}, 'Approved')">Approve</button>`);
                            actions.push(`<button class="ml-2 text-sm text-red-600" onclick="confirmUpdateRequestStatus(${r.id}, 'Declined')">Decline</button>`);
                        } else {
                            actions.push(`<button class="ml-2 text-sm text-red-600" onclick="confirmDeleteRequest(${r.id})">Delete</button>`);
                        }
                        tr.innerHTML = `<td class="py-3 px-4 font-medium">${escapeHtml(r.request_type || r.title || 'Request')}</td><td class="py-3 px-4">${escapeHtml(r.barangay || r.submitter || '')}</td><td class="py-3 px-4">${new Date(r.created_at).toLocaleDateString()}</td><td class="py-3 px-4">${getStatusLabel(r.status || '')}</td><td class="py-3 px-4">${actions.join('')}</td>`;
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

                if (agg && Array.isArray(agg.by_type) && agg.by_type.length) {
                    // use server-provided breakdown
                    renderByTypeWidget(agg.by_type.map(x => ({ type: x.type ?? x.request_type, count: Number(x.count) })), total);
                } else {
                    // build by-type from client-side requests and render
                    const byType = {};
                    requests.forEach(r => { const t = r.request_type || r.title || 'Unknown'; byType[t] = (byType[t] || 0) + 1; });
                    const arr = Object.keys(byType).map(k => ({ type: k, count: byType[k] })).sort((a,b)=> b.count - a.count);
                    renderByTypeWidget(arr, requests.length);
                }
            } catch (e) { try { computeDescriptive(requests); } catch (_) {} }

            // Charts: status distribution
            renderStatusChart({ approved, pending, declined });
    }

    // expose for external callers (e.g., SSE handlers)
    window.loadRequestsAndPopulate = loadRequestsAndPopulate;

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

    // Setup preview widget handlers (delegated) — show animated preview on row hover and allow quick actions
    try {
        (function setupSubmissionPreviewHandlers(){
            const tbody = document.getElementById('submissions-table-body');
            const preview = document.getElementById('submissionPreview');
            const pvClose = document.getElementById('pv_close');
            const pvView = document.getElementById('pv_view_btn');
            const pvApprove = document.getElementById('pv_approve_btn');
            const pvDecline = document.getElementById('pv_decline_btn');
            // accessibility: keep track of pinned preview
            window.previewPinnedId = window.previewPinnedId || null;

            function populatePreviewForId(id) {
                if (!window._headRequests) return;
                const r = window._headRequests.find(x => Number(x.id) === Number(id));
                if (!r) return;
                document.getElementById('pv_title').textContent = r.request_type || r.title || 'Request';
                document.getElementById('pv_meta').textContent = `${r.barangay || '—'} · ${new Date(r.created_at || '').toLocaleDateString()}`;
                document.getElementById('pv_location').textContent = r.location || '—';
                document.getElementById('pv_description').textContent = r.description || '—';
                const attach = document.getElementById('pv_attachment'); attach.innerHTML = '';
                if (r.attachment) { const a = document.createElement('a'); a.href = r.attachment; a.target = '_blank'; a.textContent = 'Open'; a.className='text-blue-600'; attach.appendChild(a); } else attach.textContent = '—';
                const hist = r.history || [];
                const histEl = document.getElementById('pv_history'); histEl.innerHTML = '';
                if (!hist.length) histEl.textContent = 'No history.'; else histEl.textContent = (hist.slice(-3).map(h=> (h.timestamp||h.date||h.created_at||'') + ' • ' + (h.status||h.note||h.message||'')).join('\n'));

                // wire actions
                pvView.onclick = () => openHeadView(r.id);
                pvApprove.onclick = () => confirmUpdateRequestStatus(r.id, 'Approved');
                pvDecline.onclick = () => confirmUpdateRequestStatus(r.id, 'Declined');
            }

            let hoverTimer = null;
            if (tbody && preview) {
                tbody.addEventListener('mouseover', (ev) => {
                    // if pinned, do not show transient hover previews
                    if (window.previewPinnedId) return;
                    const tr = ev.target.closest && ev.target.closest('tr');
                    if (!tr || !tr.id) return;
                    const match = tr.id.match(/request-row-(\d+)/);
                    if (!match) return;
                    const id = match[1];
                    // small delay to avoid flicker on quick movement
                    clearTimeout(hoverTimer);
                    hoverTimer = setTimeout(() => {
                        populatePreviewForId(id);
                        preview.classList.add('open');
                        preview.setAttribute('aria-hidden','false');
                        preview.classList.add('preview-pulse');
                        setTimeout(()=> preview.classList.remove('preview-pulse'), 700);
                    }, 120);
                });

                tbody.addEventListener('mouseout', (ev) => {
                    // if preview is pinned, do not auto-close on mouseout
                    if (window.previewPinnedId) return;
                    const related = ev.relatedTarget;
                    // if leaving to preview itself, keep it open
                    if (related && preview.contains(related)) return;
                    clearTimeout(hoverTimer);
                    preview.classList.remove('open');
                    preview.setAttribute('aria-hidden','true');
                });

                // close button and hover on preview should keep preview visible
                pvClose && pvClose.addEventListener('click', () => { window.previewPinnedId = null; preview.classList.remove('open'); preview.setAttribute('aria-hidden','true'); });
                preview.addEventListener('mouseenter', () => clearTimeout(hoverTimer));
                preview.addEventListener('mouseleave', () => { if (!window.previewPinnedId) { preview.classList.remove('open'); preview.setAttribute('aria-hidden','true'); } });

                // toggle persist (pin) when clicking table rows via keyboard or mouse
                window.togglePreviewPin = function(id) {
                    try {
                        if (!id) return;
                        if (window.previewPinnedId && String(window.previewPinnedId) === String(id)) {
                            // unpin
                            window.previewPinnedId = null;
                            preview.classList.remove('open');
                            preview.setAttribute('aria-hidden','true');
                        } else {
                            populatePreviewForId(id);
                            preview.classList.add('open');
                            preview.setAttribute('aria-hidden','false');
                            window.previewPinnedId = String(id);
                            // focus first action for keyboard users
                            setTimeout(()=> { try { pvView && pvView.focus(); } catch(e){} }, 80);
                        }
                    } catch (e) { console.warn('togglePreviewPin error', e); }
                };

                // keyboard: Escape closes/unpins preview
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        if (window.previewPinnedId) { window.previewPinnedId = null; }
                        if (preview) { preview.classList.remove('open'); preview.setAttribute('aria-hidden','true'); }
                    }
                });
            }
        })();
    } catch (e) { console.warn('Preview widget handlers failed', e); }

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
            const summaryEl = document.getElementById('descSummary');
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
            const tbB = document.getElementById('tblTopBarangay'); tbB.innerHTML = '';
            Object.keys(byBarangay).sort((a,b)=> byBarangay[b]-byBarangay[a]).slice(0,10).forEach(k => {
                const cnt = byBarangay[k];
                const pct = total ? ((cnt/total)*100).toFixed(1) : 0;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${escapeHtml(k)}</td><td style="text-align:right">${cnt}</td><td style="text-align:right">${pct}%</td>`;
                tbB.appendChild(tr);
            });

            // review time stats
            const reviewDiv = document.getElementById('reviewStats');
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

        // small helper: HTML-escape
        function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]; }); }

        // initial load
        loadRequestsAndPopulate();
        // refresh periodically
        setInterval(loadRequestsAndPopulate, 60_000);
    });

        // Allow Head to update request status (calls server endpoint)
        async function updateRequestStatus(requestId, newStatus) {
            try {
                const res = await fetch('update_request_status.php', { method: 'POST', body: new URLSearchParams({ id: requestId, status: newStatus }), credentials: 'same-origin' });
                if (!res.ok) throw new Error('Network');
                const d = await res.json();
                if (d && d.status) {
                    // refresh table
                    if (typeof window.loadRequestsAndPopulate === 'function') window.loadRequestsAndPopulate();
                    return;
                }
                alert('Failed to update request');
            } catch (err) {
                console.error(err);
                alert('Server error');
            }
        }

        // Delete a finalized request (Approved/Declined)
        async function deleteRequest(requestId) {
            try {
                const res = await fetch('delete_request.php', { method: 'POST', body: new URLSearchParams({ id: requestId }), credentials: 'same-origin' });
                if (!res.ok) throw new Error('Network');
                const d = await res.json();
                if ((d && d.success) || (d && d.status === 'success') || (d && d.status === true)) {
                    if (typeof window.loadRequestsAndPopulate === 'function') window.loadRequestsAndPopulate();
                    return;
                }
                alert((d && (d.message || d.error)) ? (d.message || d.error) : 'Failed to delete request');
            } catch (err) {
                console.error(err);
                alert('Server error');
            }
        }

    function showPage(pageId) {
        document.querySelectorAll('.page-content').forEach(page => page.style.display = 'none');
        document.getElementById(pageId).style.display = 'block';

        document.querySelectorAll('nav a').forEach(link => {
            link.classList.remove('active-nav-link');
            if(link.getAttribute('onclick').includes(pageId)) {
                link.classList.add('active-nav-link');
            }
        });
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
    function confirmUpdateRequestStatus(requestId, newStatus) {
        showHeadConfirm('Confirm Action', `Are you sure you want to mark request ${requestId} as ${newStatus}?`, async (ok) => {
            if (!ok) return;
            await updateRequestStatus(requestId, newStatus);
        }, 'Confirm', 'primary');
    }

    // Confirmation wrapper for delete
    function confirmDeleteRequest(requestId) {
        showHeadConfirm('Confirm Deletion', `Delete request ${requestId}? This cannot be undone.`, async (ok) => {
            if (!ok) return;
            await deleteRequest(requestId);
        }, 'Delete', 'danger');
    }

    // Open the Head View modal and load request details via AJAX
    async function openHeadView(requestId) {
        try {
            if (!requestId) return;
            // fetch JSON details from server endpoint
            const res = await fetch('head_view_request.php?id=' + encodeURIComponent(requestId), { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Network');
            const data = await res.json();
            if (!data || !data.request) {
                showHeadAlert('Not found', 'Request not found.');
                return;
            }
            renderHeadView(data.request);
            headViewOpen();
        } catch (err) {
            console.error(err);
            showHeadAlert('Error', 'Could not load request details.');
        }
    }

    function renderHeadView(r) {
        try {
            document.getElementById('hv_request_type').textContent = r.request_type || r.title || '';
            document.getElementById('hv_barangay').textContent = r.barangay || '';
            document.getElementById('hv_submitter').textContent = r.submitter || r.email || '';
            document.getElementById('hv_date').textContent = r.created_at ? new Date(r.created_at).toLocaleString() : '';
            document.getElementById('hv_status').innerHTML = getStatusLabel(r.status || '');
            document.getElementById('hv_location').textContent = r.location || '';
            document.getElementById('hv_description').textContent = r.description || '';
            document.getElementById('hv_notes').textContent = r.notes || '';
            const attachEl = document.getElementById('hv_attachment'); attachEl.innerHTML = '';
            if (r.attachment) {
                const a = document.createElement('a'); a.href = r.attachment; a.target = '_blank'; a.textContent = 'Open attachment'; a.className = 'text-blue-600'; attachEl.appendChild(a);
            } else { attachEl.textContent = '—'; }
            // history: render simple list
            const hist = r.history || [];
            const histEl = document.getElementById('hv_history'); histEl.innerHTML = '';
            if (hist.length === 0) histEl.textContent = 'No history.';
            else {
                const ul = document.createElement('div'); ul.className = 'space-y-1';
                hist.slice().reverse().forEach(h => {
                    const d = document.createElement('div'); d.className = 'p-2 bg-white border rounded';
                    const ts = h.timestamp || h.date || h.created_at || '';
                    d.innerHTML = `<div class="text-xs text-gray-500">${escapeHtml(String(ts))}</div><div class="text-sm">${escapeHtml(String(h.status || h.note || h.message || ''))}</div>`;
                    ul.appendChild(d);
                });
                histEl.appendChild(ul);
            }

            // actions area
            const actions = document.getElementById('headViewModalActions');
            actions.innerHTML = '';
            const isFinal = /approved|declined/i.test(String(r.status || ''));
            // View modal action buttons
            const btnClose = document.createElement('button'); btnClose.className = 'bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded'; btnClose.textContent = 'Close'; btnClose.onclick = headViewClose;
            actions.appendChild(btnClose);
            if (!isFinal) {
                const btnApprove = document.createElement('button'); btnApprove.className = 'bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded'; btnApprove.textContent = 'Approve';
                btnApprove.onclick = () => showHeadConfirm('Confirm', 'Approve this request?', async (ok) => { if (!ok) return; await updateRequestStatus(r.id, 'Approved'); headViewClose(); }, 'Approve', 'primary');
                actions.appendChild(btnApprove);
                const btnDecline = document.createElement('button'); btnDecline.className = 'ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'; btnDecline.textContent = 'Decline';
                btnDecline.onclick = () => showHeadConfirm('Confirm', 'Decline this request?', async (ok) => { if (!ok) return; await updateRequestStatus(r.id, 'Declined'); headViewClose(); }, 'Decline', 'danger');
                actions.appendChild(btnDecline);
            } else {
                const btnDelete = document.createElement('button'); btnDelete.className = 'ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'; btnDelete.textContent = 'Delete';
                btnDelete.onclick = () => showHeadConfirm('Confirm Deletion', 'Delete this request? This cannot be undone.', async (ok) => { if (!ok) return; await deleteRequest(r.id); headViewClose(); }, 'Delete', 'danger');
                actions.appendChild(btnDelete);
            }
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
            setTimeout(() => { try { toast.style.opacity = '0'; setTimeout(()=> toast.remove(), 250); } catch(e){} }, opts.duration || 6000);
        } catch (e) { console.warn('Toast error', e); }
    }

    // SSE listener to refresh dashboard when notifications relevant to Head arrive
    try {
        const headSse = new EventSource('notifications_stream.php');
        headSse.addEventListener('notification', (e) => {
            try {
                const payload = JSON.parse(e.data || '{}');
                // increment the Submissions badge if targeted to Head
                try { if (payload && (payload.target_role === 'OPMDC Head' || payload.target_user_id)) incrementHeadBadge(); } catch (ee) {}
                if (!payload.target_role || payload.target_role === 'OPMDC Head' || payload.target_user_id) {
                    // reload requests
                    if (typeof loadRequestsAndPopulate === 'function') loadRequestsAndPopulate();
                    // refresh the notification widget if the fetch function has been exposed
                    if (typeof window.fetchHeadNotifs === 'function') window.fetchHeadNotifs();
                    // ensure the small badge is visible as a quick indicator
                    const b = document.getElementById('headNotifBadge');
                    if (b) b.classList.remove('hidden');
                    // show a small toast to make incoming notification obvious; include request link if present
                    if (payload.title || payload.body) showNotificationToast(payload.title || 'New notification', payload.body || '', { requestId: payload.request_id });
                }
            } catch (err) { console.error('SSE payload parse error', err); }
        });
        headSse.addEventListener('error', (err) => { console.warn('Head SSE error', err); headSse.close(); });
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
            const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Head'));
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
        headList.querySelectorAll('.mark-read').forEach(btn => btn.addEventListener('click', async (e) => { const id = e.target.dataset.id; await fetch('notifications_mark_read.php?id=' + encodeURIComponent(id), { method: 'POST' }); fetchHeadNotifs(); }));
        headList.querySelectorAll('.delete').forEach(btn => btn.addEventListener('click', async (e) => { const id = e.target.dataset.id; await fetch('notifications_delete.php?id=' + encodeURIComponent(id), { method: 'POST' }); fetchHeadNotifs(); }));
    }

    headBell && headBell.addEventListener('click', (e) => { e.stopPropagation(); headDropdown.classList.toggle('hidden'); if (!headDropdown.classList.contains('hidden')) fetchHeadNotifs(); });
    document.addEventListener('click', () => headDropdown.classList.add('hidden'));
    headMarkAllRead && headMarkAllRead.addEventListener('click', async () => { const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Head')); const data = await res.json(); for (const n of data.notifications || []) { await fetch('notifications_mark_read.php?id=' + encodeURIComponent(n.id), { method: 'POST' }); } fetchHeadNotifs(); });

    function escapeHtml(s) { return String(s).replace(/[&<>\"']/g, function(c){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]; }); }

    // expose for external callers (SSE handler) so incoming events can refresh the widget
    window.fetchHeadNotifs = fetchHeadNotifs;
    // initialize head submissions badge count
    try { if (typeof loadHeadBadgeCount === 'function') loadHeadBadgeCount(); } catch (e) {}
    fetchHeadNotifs();
});
</script>
<!-- Toast container (created dynamically if needed) -->
<div id="notifToastContainer" class="notif-toast-container" aria-live="polite"></div>
</body> 
</html>

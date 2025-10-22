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
    </style>
    <link rel="stylesheet" href="assets/ui-animations.css">
</head>
<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">
    <aside class="sidebar w-64 bg-slate-800 text-white flex flex-col" data-reveal="sidebar">
            <div class="p-6 text-center border-b border-gray-700">
                <img src="assets/image1.png" alt="Mabini Seal" class="logo-formal">
                <h2 class="text-xl font-semibold">OPMDC Head</h2>
                <p class="text-sm text-gray-400">Oversight Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700 active-nav-link" onclick="showPage('dashboard')">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('submissions')">
                    <i class="fas fa-file-alt w-5 mr-3"></i> Submissions
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('resources')">
                    <i class="fas fa-cubes w-5 mr-3"></i> Resource Allocation
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('planning')">
                    <i class="fas fa-calendar-alt w-5 mr-3"></i> Strategic Planning
                </a>
                <a href="#" class="flex items-center px-4 py-2 rounded-md hover:bg-gray-700" onclick="showPage('policies')">
                    <i class="fas fa-gavel w-5 mr-3"></i> Policy Development
                </a>
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
                            <h4 class="text-lg font-semibold text-gray-600">Pending Approvals</h4>
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
                                <table class="w-full desc-table">
                                    <thead>
                                        <tr><th class="text-left text-xs text-gray-500">Type</th><th class="text-right text-xs text-gray-500">Count</th><th class="text-right text-xs text-gray-500">%</th></tr>
                                    </thead>
                                    <tbody id="tblByType"></tbody>
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

                <div id="planning" class="page-content">
                  <h3 class="text-3xl font-medium text-gray-700">Strategic Planning</h3>
                   <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                      <table class="w-full text-sm text-left text-gray-500">
                          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                              <tr>
                                  <th class="py-3 px-4">Goal</th>
                                  <th class="py-3 px-4">Timeframe</th>
                                  <th class="py-3 px-4">KPIs</th>
                                  <th class="py-3 px-4">Status</th>
                              </tr>
                          </thead>
                          <tbody id="planning-table-body"></tbody>
                      </table>
                  </div>
                </div>
                
                <div id="policies" class="page-content">
                    <h3 class="text-3xl font-medium text-gray-700">Policy Development</h3>
                    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                      <table class="w-full text-sm text-left text-gray-500">
                          <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                              <tr>
                                  <th class="py-3 px-4">Policy Name</th>
                                  <th class="py-3 px-4">Status</th>
                                  <th class="py-3 px-4">Last Updated</th>
                                  <th class="py-3 px-4">Actions</th>
                              </tr>
                          </thead>
                          <tbody id="policy-table-body"></tbody>
                      </table>
                  </div>
                </div>

            </div>
        </main>
    </div>

            <script src="assets/ui-animations.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Populate head dashboard from server API `list_requests.php` and fallback to localStorage when needed.
        async function loadRequestsAndPopulate() {
            let requests = [];
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

            // Update cards
            const total = requests.length;
            const approved = requests.filter(r => String(r.status).toLowerCase() === 'approved').length;
            const declined = requests.filter(r => String(r.status).toLowerCase() === 'declined').length;
            const pending = total - (approved + declined);
            document.getElementById('card-total').innerText = total;
            document.getElementById('card-pending').innerText = pending;

            // Active Projects & Registered Users are not part of requests; keep previous placeholders or calculate if you have endpoints
            // For now, clear or keep existing values — leave as-is

            // Populate submissions table
            const subBody = document.getElementById('submissions-table-body');
            if (subBody) {
                subBody.innerHTML = '';
                if (requests.length === 0) {
                    subBody.innerHTML = '<tr><td colspan="5" class="text-center text-gray-500 py-4">No submissions found.</td></tr>';
                } else {
                    requests.slice(0, 50).forEach(r => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b hover:bg-gray-50';
                        const actions = [];
                        actions.push(`<a class="text-blue-500 hover:underline" href="list_requests.php?id=${encodeURIComponent(r.id)}">View</a>`);
                        if (!/approved|declined/i.test(String(r.status || ''))) {
                            actions.push(`<button class="ml-2 text-sm text-green-600" onclick="confirmUpdateRequestStatus(${r.id}, 'Approved')">Approve</button>`);
                            actions.push(`<button class="ml-2 text-sm text-red-600" onclick="confirmUpdateRequestStatus(${r.id}, 'Declined')">Decline</button>`);
                        }
                        tr.innerHTML = `<td class="py-3 px-4 font-medium">${escapeHtml(r.request_type || r.title || 'Request')}</td><td class="py-3 px-4">${escapeHtml(r.barangay || r.submitter || '')}</td><td class="py-3 px-4">${new Date(r.created_at).toLocaleDateString()}</td><td class="py-3 px-4">${getStatusLabel(r.status || '')}</td><td class="py-3 px-4">${actions.join('')}</td>`;
                        subBody.appendChild(tr);
                    });
                }
            }

            // Descriptive analytics: by type + top barangays + review time
            computeDescriptive(requests);

            // Charts: status distribution
            renderStatusChart({ approved, pending, declined });
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
            const tbType = document.getElementById('tblByType'); tbType.innerHTML = '';
            Object.keys(byType).sort((a,b)=> byType[b]-byType[a]).forEach(k => {
                const cnt = byType[k];
                const pct = total ? ((cnt/total)*100).toFixed(1) : 0;
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${escapeHtml(k)}</td><td style="text-align:right">${cnt}</td><td style="text-align:right">${pct}%</td>`;
                tbType.appendChild(tr);
            });

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
                    // refresh view
                    loadRequestsAndPopulate();
                    return;
                }
                alert('Failed to update request');
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
    // Confirmation wrapper for head approve/decline
    function confirmUpdateRequestStatus(requestId, newStatus) {
        if (!confirm(`Are you sure you want to mark request ${requestId} as ${newStatus}?`)) return;
        updateRequestStatus(requestId, newStatus);
    }

    // SSE listener to refresh dashboard when notifications relevant to Head arrive
    try {
        const headSse = new EventSource('notifications_stream.php');
        headSse.addEventListener('notification', (e) => {
            try {
                const payload = JSON.parse(e.data || '{}');
                if (!payload.target_role || payload.target_role === 'OPMDC Head' || payload.target_user_id) {
                    // reload requests and notifs
                    loadRequestsAndPopulate();
                }
            } catch (err) { console.error('SSE payload parse error', err); }
        });
        headSse.addEventListener('error', (err) => { console.warn('Head SSE error', err); headSse.close(); });
    } catch (e) { console.warn('Head SSE not available', e); }
</script>
<script>
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

    fetchHeadNotifs();
});
</script>
</body> 
</html>

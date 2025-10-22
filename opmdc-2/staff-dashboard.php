<?php
// staff-dashboard.php - simple UI for OPMDC Staff to review requests
// Require session-based login and allow only staff/head roles
session_start();
$allowed = ['OPMDC Staff', 'OPMDC Head', 'Admin'];
$user = $_SESSION['user'] ?? null;
if (! $user || ! in_array($user['role'] ?? '', $allowed, true)) {
  // redirect to login page (same-origin)
  header('Location: login.html');
  exit;
}
// This page is intentionally minimal and uses client-side JS to call existing endpoints.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>OPMDC Staff Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="max-w-6xl mx-auto p-6">
    <header class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold">OPMDC Staff Dashboard</h1>
      <div class="flex items-center space-x-4">
        <div class="relative">
          <button id="staffNotifBell" title="Notifications" class="relative p-2 rounded hover:bg-gray-100 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-700">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V8.25A6.75 6.75 0 004.5 8.25v1.5a8.967 8.967 0 01-2.31 6.022c1.733.64 3.56 1.085 5.454 1.31m7.213 0a24.255 24.255 0 01-7.213 0m7.213 0a3 3 0 11-7.213 0" />
            </svg>
            <span id="staffNotifBadge" class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-red-500 hidden"></span>
          </button>
          <div id="staffNotifDropdown" class="hidden absolute right-0 mt-2 bg-white border rounded shadow w-80 z-40">
            <div class="p-3 border-b flex items-center justify-between">
              <strong>Notifications</strong>
              <button id="staffMarkAllRead" class="text-xs text-blue-600 hover:underline">Mark all read</button>
            </div>
            <div id="staffNotifList" class="max-h-64 overflow-auto p-2">
              <div class="text-center text-gray-500">No notifications.</div>
            </div>
          </div>
        </div>
        <a href="login.html" class="text-sm text-gray-600">Logout</a>
      </div>
    </header>

  <section class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold">Incoming Requests</h2>
        <div>
          <select id="filterStatus" class="border rounded p-1 text-sm">
            <option value="">All statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Declined">Declined</option>
          </select>
          <button id="refreshBtn" class="ml-2 bg-blue-600 text-white px-3 py-1 rounded text-sm">Refresh</button>
        </div>
      </div>
  <div id="requestsContainer" class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2">ID</th>
              <th class="px-3 py-2">Barangay</th>
              <th class="px-3 py-2">Type</th>
              <th class="px-3 py-2">Submitted</th>
              <th class="px-3 py-2">Status</th>
              <th class="px-3 py-2">Actions</th>
            </tr>
          </thead>
          <tbody id="requestsBody"></tbody>
        </table>
      </div>
    </section>
    
      <!-- Details modal -->
      <div id="detailsModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black opacity-40"></div>
        <div class="bg-white rounded-lg shadow-lg z-10 w-full max-w-3xl p-6">
          <div class="flex items-start justify-between">
            <h3 id="modalTitle" class="text-lg font-semibold">Request Details</h3>
            <button id="closeModal" class="text-gray-500">&times;</button>
          </div>
          <div id="modalBody" class="mt-4 max-h-72 overflow-auto text-sm text-gray-800"></div>
          <div class="mt-4 flex justify-end space-x-2">
            <button id="modalDecline" class="bg-red-600 text-white px-3 py-1 rounded">Decline</button>
            <button id="modalApprove" class="bg-green-600 text-white px-3 py-1 rounded">Approve</button>
            <button id="modalClose" class="bg-gray-200 px-3 py-1 rounded">Close</button>
          </div>
        </div>
      </div>
  </div>

  <script>
    async function fetchRequests() {
      const statusFilter = document.getElementById('filterStatus').value;
      const q = statusFilter ? `?` : '';
      // list_requests returns 'requests' array
  const res = await fetch('list_requests.php', { credentials: 'same-origin' });
      if (!res.ok) { document.getElementById('requestsBody').innerHTML = '<tr><td colspan="6" class="p-4 text-red-600">Failed to load requests.</td></tr>'; return; }
      const data = await res.json();
      const rows = (data.requests || []).filter(r => !statusFilter || r.status === statusFilter);
      const body = document.getElementById('requestsBody');
      body.innerHTML = '';
      if (rows.length === 0) {
        body.innerHTML = '<tr><td colspan="6" class="p-4 text-gray-600">No requests found.</td></tr>';
        return;
      }
      rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.className = 'bg-white border-b hover:bg-gray-50';
        tr.innerHTML = `
          <td class="px-3 py-2 font-medium">${r.id}</td>
          <td class="px-3 py-2">${escapeHtml(r.barangay || '')}</td>
          <td class="px-3 py-2">${escapeHtml(r.request_type || '')}</td>
          <td class="px-3 py-2">${new Date(r.created_at).toLocaleString()}</td>
          <td class="px-3 py-2">${escapeHtml(r.status || '')}</td>
          <td class="px-3 py-2">
            ${r.status === 'Pending' ? `<button class="approveBtn bg-green-600 text-white px-2 py-1 rounded mr-2" data-id="${r.id}">Approve</button>
            <button class="declineBtn bg-red-600 text-white px-2 py-1 rounded" data-id="${r.id}">Decline</button>` : '<span class="text-sm text-gray-600">No actions</span>'}
            <button class="viewBtn ml-2 text-sm text-blue-600" data-id="${r.id}">View</button>
          </td>
        `;
        body.appendChild(tr);
      });

      document.querySelectorAll('.approveBtn').forEach(btn => btn.addEventListener('click', () => changeStatus(btn.dataset.id, 'Approved')));
      document.querySelectorAll('.declineBtn').forEach(btn => btn.addEventListener('click', () => changeStatus(btn.dataset.id, 'Declined')));
      document.querySelectorAll('.viewBtn').forEach(btn => btn.addEventListener('click', () => openModalWithRequest(btn.dataset.id, data.requests)));
    }

    function escapeHtml(s) { return String(s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"})[c]); }

    async function changeStatus(id, newStatus, note='') {
      const form = new FormData();
      form.append('id', id);
      form.append('status', newStatus);
      form.append('note', note || '');
      const res = await fetch('update_request_status.php', { method: 'POST', body: form, credentials: 'same-origin' });
      if (!res.ok) {
        const text = await res.text().catch(()=>'');
        alert('Failed to update status: ' + text);
        return null;
      }
      const d = await res.json();
      return d;
    }

    // Modal handling
    const detailsModal = document.getElementById('detailsModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const closeModal = document.getElementById('closeModal');
    const modalClose = document.getElementById('modalClose');
    const modalApprove = document.getElementById('modalApprove');
    const modalDecline = document.getElementById('modalDecline');
    let currentModalRequestId = null;

    function openModalWithRequest(id, allRequests) {
      const req = (allRequests || []).find(r => String(r.id) == String(id));
      if (!req) { alert('Request not found.'); return; }
      currentModalRequestId = req.id;
      modalTitle.textContent = `Request #${req.id} — ${req.request_type}`;
      let html = `
        <p class="text-sm"><strong>Barangay:</strong> ${escapeHtml(req.barangay || '')}</p>
        <p class="text-sm"><strong>Submitted:</strong> ${new Date(req.created_at).toLocaleString()}</p>
        <p class="mt-3"><strong>Description</strong><br>${escapeHtml(req.description || '')}</p>
        <p class="mt-3"><strong>Location:</strong> ${escapeHtml(req.location || '')}</p>
        <p class="mt-3"><strong>Email:</strong> ${escapeHtml(req.email || '')}</p>
        <p class="mt-3"><strong>Attachment:</strong> ${req.attachment ? `<a class=\"text-blue-600\" href=\"${escapeHtml(req.attachment)}\" target=\"_blank\">Download</a>` : 'None'}</p>
        <hr class="my-3">
        <div><strong>History</strong></div>
      `;
      const history = req.history || [];
      if (history.length === 0) html += '<p class="text-sm text-gray-600">No history available.</p>';
      history.forEach(h => {
        html += `<div class="mt-2 text-sm p-2 bg-gray-50 rounded"><div class=\"text-xs text-gray-500\">${new Date(h.timestamp).toLocaleString()} — ${escapeHtml(h.actor||'')}</div><div class=\"font-medium\">${escapeHtml(h.status)}</div><div class=\"text-xs text-gray-700\">${escapeHtml(h.notes||'')}</div></div>`;
      });
      modalBody.innerHTML = html;
      // show modal
      detailsModal.classList.remove('hidden');
      detailsModal.classList.add('flex');
      // enable/disable approve/decline depending on status
      const canAct = req.status === 'Pending';
      modalApprove.disabled = !canAct;
      modalDecline.disabled = !canAct;
    }

    async function performModalAction(newStatus) {
      if (!currentModalRequestId) return;
      const note = prompt(`Add a note for this ${newStatus.toLowerCase()} (optional):`, '');
      const result = await changeStatus(currentModalRequestId, newStatus, note || '');
      if (result) {
        alert(`Request #${result.id} updated to ${result.status}`);
        closeDetailsModal();
        fetchRequests();
      }
    }

    function closeDetailsModal() {
      detailsModal.classList.add('hidden');
      detailsModal.classList.remove('flex');
      currentModalRequestId = null;
    }

    closeModal.addEventListener('click', closeDetailsModal);
    modalClose.addEventListener('click', closeDetailsModal);
    modalApprove.addEventListener('click', () => performModalAction('Approved'));
    modalDecline.addEventListener('click', () => performModalAction('Declined'));

    document.getElementById('refreshBtn').addEventListener('click', fetchRequests);
    document.getElementById('filterStatus').addEventListener('change', fetchRequests);

    // initial load
    fetchRequests();

    // --- Staff notifications (role-based) ---
    const staffBell = document.getElementById('staffNotifBell');
    const staffBadge = document.getElementById('staffNotifBadge');
    const staffDropdown = document.getElementById('staffNotifDropdown');
    const staffList = document.getElementById('staffNotifList');
    const staffMarkAllRead = document.getElementById('staffMarkAllRead');

    async function fetchStaffNotifs() {
      try {
        const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Staff'), { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed');
        const data = await res.json();
        renderStaffNotifs(data.notifications || []);
      } catch (e) {
        staffList.innerHTML = '<div class="text-center text-gray-500">Could not load notifications</div>';
      }
    }

    function renderStaffNotifs(notes) {
      if (!notes.length) {
        staffList.innerHTML = '<div class="text-center text-gray-500">No notifications.</div>';
        staffBadge.classList.add('hidden');
        return;
      }
      let unread = 0;
      const html = notes.slice(0, 50).map(n => {
        if (!parseInt(n.is_read)) unread++;
        return `
          <div class="flex items-start justify-between p-2 rounded hover:bg-gray-50 ${parseInt(n.is_read)?'':'bg-blue-50'}">
            <div class="pr-2">
              <div class="text-sm font-medium text-gray-800">${escapeHtml(n.title)}</div>
              <div class="text-xs text-gray-600 mt-1">${escapeHtml(n.body)}</div>
              <div class="text-[11px] text-gray-400 mt-1">${new Date(n.created_at).toLocaleString()}</div>
            </div>
            <div class="flex flex-col items-end ml-2 space-y-1">
              <button data-id="${n.id}" class="mark-read text-[11px] text-blue-600">${parseInt(n.is_read)?'Unread':'Mark read'}</button>
              <button data-id="${n.id}" class="delete text-[11px] text-red-600">Delete</button>
            </div>
          </div>`;
      }).join('');
      staffList.innerHTML = html;
      if (unread > 0) staffBadge.classList.remove('hidden'); else staffBadge.classList.add('hidden');
      // bind buttons
      staffList.querySelectorAll('.mark-read').forEach(btn => btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        await fetch('notifications_mark_read.php?id=' + encodeURIComponent(id), { method: 'POST', credentials: 'same-origin' });
        fetchStaffNotifs();
      }));
      staffList.querySelectorAll('.delete').forEach(btn => btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        await fetch('notifications_delete.php?id=' + encodeURIComponent(id), { method: 'POST', credentials: 'same-origin' });
        fetchStaffNotifs();
      }));
    }

    staffBell && staffBell.addEventListener('click', (e) => {
      e.stopPropagation();
      staffDropdown.classList.toggle('hidden');
      if (!staffDropdown.classList.contains('hidden')) fetchStaffNotifs();
    });
    document.addEventListener('click', () => { if (!staffDropdown.classList.contains('hidden')) staffDropdown.classList.add('hidden'); });
    staffMarkAllRead && staffMarkAllRead.addEventListener('click', async () => {
      const res = await fetch('notifications.php?role=' + encodeURIComponent('OPMDC Staff'), { credentials: 'same-origin' });
      const data = await res.json();
      for (const n of data.notifications || []) {
        await fetch('notifications_mark_read.php?id=' + encodeURIComponent(n.id), { method: 'POST', credentials: 'same-origin' });
      }
      fetchStaffNotifs();
    });

    // Realtime updates via SSE so staff sees new items instantly
    (function startStaffSSE(){
      try {
        const last = parseInt(localStorage.getItem('opmdcStaffLastNotifId')||'0',10) || 0;
        const es = new EventSource('notifications_stream.php?last_id=' + last);
        es.addEventListener('notification', (e) => {
          try {
            const data = JSON.parse(e.data);
            const id = parseInt(data.id||0,10);
            if (id) localStorage.setItem('opmdcStaffLastNotifId', String(id));
            // show badge and refresh list if open
            staffBadge && staffBadge.classList.remove('hidden');
            if (staffDropdown && !staffDropdown.classList.contains('hidden')) {
              fetchStaffNotifs();
            }
          } catch(err) { /* ignore */ }
        });
        es.addEventListener('error', () => {
          // Let browser handle reconnection automatically; no-op
        });
      } catch(err) { /* SSE not supported */ }
    })();
  </script>
</body>
</html>

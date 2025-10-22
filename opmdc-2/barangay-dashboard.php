<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Officials Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .modal-backdrop {
      background-color: rgba(0,0,0,0.5);
    }
    /* Logo formal and clean - copied from staff-dashboard for consistent sizing */
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
    }
  .logo-formal:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 28px rgba(14,45,80,0.08); }
  @keyframes logo-pop { 0% { transform: translateY(8px) scale(0.96); opacity: 0; } 60% { transform: translateY(-2px) scale(1.02); opacity: 1; } 100% { transform: translateY(0) scale(1); opacity: 1; } }
    /* Styles for the tracking timeline */
    .timeline-item {
      position: relative;
      padding-bottom: 2rem;
      padding-left: 2.5rem;
      border-left: 2px solid #e2e8f0;
    }
    .timeline-item:last-child {
      border-left: 2px solid transparent;
      padding-bottom: 0;
    }
    .timeline-dot {
      position: absolute;
      left: -0.6rem;
      top: 0.1rem;
      width: 1.2rem;
      height: 1.2rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .timeline-dot-pending { background-color: #f6e05e; } /* yellow-400 */
    .timeline-dot-approved { background-color: #68d391; } /* green-400 */
    .timeline-dot-declined { background-color: #fc8181; } /* red-400 */
    .timeline-dot-default { background-color: #cbd5e0; } /* gray-400 */

    /* Active nav link style */
    .nav-active {
        background-color: #1f2937; /* bg-gray-900 */
        color: #f9fafb; /* text-gray-100 */
    }
    /* Notification styles */
    .notif-badge {
      position: absolute;
      top: -6px;
      right: -6px;
      background: #ef4444; /* red-500 */
      color: white;
      font-size: 11px;
      padding: 2px 6px;
      border-radius: 9999px;
      border: 2px solid white;
    }
    .notif-dropdown { min-width: 300px; max-width: 360px; }
    .notif-item.unread { background: rgba(59,130,246,0.06); }
    .notif-item { display: flex; gap: 0.75rem; padding: 0.5rem; align-items: start; }
    .notif-item .time { font-size: 11px; color: #6b7280; }
    .notif-empty { padding: 1rem; color: #6b7280; }
    /* reveal CSS moved to assets/ui-animations.css */
  </style>
  <link rel="stylesheet" href="assets/ui-animations.css">
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
  <aside class="sidebar w-64 bg-gray-800 text-white flex flex-col" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
  <img src="assets/image1.png" alt="Logo" class="logo-formal">
        <h2 id="barangay-name-header" class="text-xl font-semibold">Barangay Name</h2>
        <p class="text-xs text-gray-400">Mabini, Batangas</p>
      </div>
      <nav class="flex-grow px-4 py-6">
        <a href="#" id="nav-dashboard" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md nav-active">
          <i class="fas fa-tachometer-alt mr-3"></i>
          Dashboard
        </a>
        <a href="#" id="nav-status" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-spinner mr-3"></i>
          Status
        </a>
        <a href="#" id="nav-history" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-history mr-3"></i>
          History
        </a>
        <a href="#" class="flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-md">
          <i class="fas fa-user-circle mr-3"></i>
          Profile
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
            <h1 id="header-title" class="text-2xl font-bold text-gray-800">Dashboard</h1>
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
              <button id="notifBellBtn" title="Notifications" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none">
                <i class="fas fa-bell text-gray-600 text-lg"></i>
                <span id="notifBadge" class="notif-badge hidden">0</span>
              </button>
              <div id="notificationDropdown" class="hidden absolute right-0 mt-2 bg-white shadow-lg rounded-lg notif-dropdown z-50">
                <div class="p-3 border-b flex items-center justify-between">
                  <strong>Notifications</strong>
                  <div class="flex items-center space-x-2">
                    <button id="markAllReadBtn" class="text-xs text-blue-600 hover:underline">Mark all read</button>
                  </div>
                </div>
                <div id="notifList" class="max-h-64 overflow-auto p-2">
                  <div class="notif-empty text-center">No notifications.</div>
                </div>
                <div class="p-2 border-t text-right">
                  <!-- sample button removed -->
                </div>
              </div>
            </div>

            <div class="flex items-center">
              <img class="w-10 h-10 rounded-full" src="https://placehold.co/100x100/E2E8F0/4A5568?text=User" alt="User Avatar">
              <div class="ml-3">
                <p id="user-name" class="text-sm font-semibold text-gray-800">Barangay Tanod</p>
                <p id="user-role" class="text-xs text-gray-500">Official</p>
              </div>
            </div>
          </div>
        </div>
      </header>
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div class="container mx-auto">

            <div id="dashboard-view">
                <div class="flex justify-between items-center mb-6" data-reveal="group">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">What's up, <span id="welcome-name">Barangay Name</span>!</h2>
                        <p class="text-gray-600">Here's a look at the latest reports and activities.</p>
                    </div>
                    <button id="newRequestBtn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i> New Request
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="bg-blue-100 text-blue-500 p-4 rounded-full">
                        <i class="fas fa-folder text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Reports</p>
                        <p id="total-reports" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="bg-green-100 text-green-500 p-4 rounded-full">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Approved</p>
                        <p id="approved-reports" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="bg-yellow-100 text-yellow-500 p-4 rounded-full">
                        <i class="fas fa-hourglass-half text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Pending</p>
                        <p id="pending-reports" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                    <div class="bg-red-100 text-red-500 p-4 rounded-full">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Declined</p>
                        <p id="declined-reports" class="text-2xl font-bold text-gray-800">0</p>
                    </div>
                    </div>
                </div>

                <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Request ID</th>
                            <th scope="col" class="px-6 py-3">Request Type</th>
                            <th scope="col" class="px-6 py-3">Date Submitted</th>
              <th scope="col" class="px-6 py-3">Status</th>
              <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody id="activity-table-body">
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div id="status-view" class="hidden">
                 <div class="bg-white p-6 md:p-8 rounded-lg shadow-md max-w-4xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Request Status Tracker</h2>
                    <p class="text-gray-600 mb-6">Enter a Request ID to see the current status and history of your request.</p>
                    
                    <div class="flex flex-col sm:flex-row sm:space-x-4">
                        <input type="text" id="tracking-id-input" placeholder="Enter your Request ID" class="flex-grow px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-3 sm:mb-0">
                        <button id="track-btn" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
                            Track
                        </button>
                    </div>
                    
                    <div id="tracking-results-container" class="mt-8 border-t pt-6">
                       <p class="text-center text-gray-500">Tracking details will appear here.</p>
                    </div>
                </div>
            </div>

      <div id="history-view" class="hidden">
        <div class="bg-white p-6 md:p-8 rounded-lg shadow-md max-w-5xl mx-auto">
          <h2 class="text-2xl font-bold text-gray-800 mb-4">All Transactions & Histories</h2>
          <p class="text-gray-600 mb-4">Below are all recorded requests for your barangay. Expand each entry to view its full status history.</p>
          <div id="history-list" class="space-y-4">
            <!-- Filled by renderHistory() -->
          </div>
        </div>
      </div>

        </div>
      </main>
    </div>
  </div>

  <script src="assets/ui-animations.js"></script>

  <div id="requestModal" class="fixed inset-0 z-50 items-center justify-center hidden overflow-y-auto">
      <div class="modal-backdrop fixed inset-0"></div>
      <div class="bg-white rounded-lg shadow-xl m-4 p-6 w-full max-w-2xl z-10">
          <div class="flex justify-between items-center mb-6 border-b pb-4">
              <h2 class="text-2xl font-bold text-gray-800">Request Center</h2>
              <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800 text-3xl font-light">&times;</button>
          </div>
          <form id="requestForm">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                  <div>
                      <label for="requestType" class="block text-gray-700 text-sm font-bold mb-2">Request Type:</label>
                      <select id="requestType" name="requestType" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                          <option value="">Select a type</option>
                          <option value="Barangay Clearance">Barangay Clearance</option>
                          <option value="Certificate of Residency">Certificate of Residency</option>
                          <option value="Incident Report">Incident Report</option>
                          <option value="Community Assistance">Community Assistance</option>
                      </select>
                  </div>
                  <div>
                      <label for="urgencyLevel" class="block text-gray-700 text-sm font-bold mb-2">Urgency Level:</label>
                      <select id="urgencyLevel" name="urgencyLevel" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                          <option value="Low">Low</option>
                          <option value="Medium" selected>Medium</option>
                          <option value="High">High</option>
                          <option value="Urgent">Urgent</option>
                      </select>
                  </div>
              </div>
              <div class="mb-4">
                  <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location / Address:</label>
                  <input type="text" id="location" name="location" placeholder="e.g., Purok 1, Barangay Hall" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
              </div>
              <div class="mb-4">
                  <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description of Request:</label>
                  <textarea id="description" name="description" rows="3" placeholder="Provide a detailed description of your request..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
              </div>
        <div class="mb-4">
          <label for="attachment" class="block text-gray-700 text-sm font-bold mb-2">Attachment:</label>
          <input type="file" id="attachment" name="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
        </div>
              <div class="flex items-center justify-end space-x-4 border-t pt-4">
                  <button type="button" id="cancelModalBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                      Cancel
                  </button>
                  <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                      Submit Request
                  </button>
              </div>
          </form>
      </div>
  </div>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Globals & Element References ---
        const modal = document.getElementById('requestModal');
        const newRequestBtn = document.getElementById('newRequestBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelModalBtn = document.getElementById('cancelModalBtn');
        const requestForm = document.getElementById('requestForm');
        const activityTableBody = document.getElementById('activity-table-body');
        
  // View related elements
  const dashboardView = document.getElementById('dashboard-view');
  const statusView = document.getElementById('status-view');
  const historyView = document.getElementById('history-view');
  const navDashboard = document.getElementById('nav-dashboard');
  const navStatus = document.getElementById('nav-status');
  const navHistory = document.getElementById('nav-history');
  const headerTitle = document.getElementById('header-title');
  const historyList = document.getElementById('history-list');

        // Status view specific elements
        const trackBtn = document.getElementById('track-btn');
        const trackingInput = document.getElementById('tracking-id-input');
        const trackingResultsContainer = document.getElementById('tracking-results-container');
        
  let loggedInUser;
  let currentUserBarangay;

        // --- Authentication & Initialization ---
    async function initializeDashboard() {
      // Try to get authoritative user data from server session first
      try {
        const res = await fetch('me.php', { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Not authenticated');
        const data = await res.json();
        if (data && data.success && data.user) {
          loggedInUser = data.user;
        }
      } catch (err) {
        // Fallback to client-side stored user if server session not available
        try { loggedInUser = JSON.parse(localStorage.getItem('loggedInUser')); } catch(e) { loggedInUser = null; }
      }

      if (!loggedInUser || loggedInUser.role !== 'Barangay Official') {
        alert('Access denied. Please login as a Barangay Official.');
        window.location.href = 'login.html';
        return;
      }

  currentUserBarangay = loggedInUser.barangayName || loggedInUser.barangay || 'Barangay Name';

      // Update UI with user info
  document.getElementById('barangay-name-header').textContent = currentUserBarangay;
  // Welcome should show the representative's name; fall back to barangay if name missing
  document.getElementById('welcome-name').textContent = loggedInUser.name || currentUserBarangay;
      document.getElementById('user-name').textContent = loggedInUser.name || 'Official';
      document.getElementById('user-role').textContent = loggedInUser.role;

      // fetch server requests for this barangay and then render UI
      await fetchAndCacheRequests();
      renderUI();
      updateTime();
      setInterval(updateTime, 60000);
    }

        // --- Event Listeners ---
        newRequestBtn.addEventListener('click', () => modal.style.display = 'flex');
        closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
        cancelModalBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (event) => {
            if (event.target.classList.contains('modal-backdrop')) {
                modal.style.display = 'none';
            }
        });

        requestForm.addEventListener('submit', handleFormSubmission);
        trackBtn.addEventListener('click', handleTracking);
        
  // Navigation listeners
  navDashboard.addEventListener('click', (e) => { e.preventDefault(); showView('dashboard'); });
  navStatus.addEventListener('click', (e) => { e.preventDefault(); showView('status'); });
  navHistory && navHistory.addEventListener('click', (e) => { e.preventDefault(); showView('history'); });

        window.addEventListener('storage', (e) => {
          if (e.key === 'opmdcRequests') {
            renderUI();
            // also refresh history if visible
            if (!historyView.classList.contains('hidden')) renderHistory();
          }
        });
        
        // --- View Management ---
        function showView(viewName) {
            // Hide all views
            dashboardView.classList.add('hidden');
            statusView.classList.add('hidden');
      historyView.classList.add('hidden');

            // Deactivate all nav links
            document.querySelectorAll('aside nav a').forEach(link => link.classList.remove('nav-active'));

            if (viewName === 'dashboard') {
                dashboardView.classList.remove('hidden');
                navDashboard.classList.add('nav-active');
                headerTitle.textContent = 'Dashboard';
            } else if (viewName === 'status') {
                statusView.classList.remove('hidden');
                navStatus.classList.add('nav-active');
                headerTitle.textContent = 'Status Tracking';
      } else if (viewName === 'history') {
        historyView.classList.remove('hidden');
        navHistory && navHistory.classList.add('nav-active');
        headerTitle.textContent = 'All Transactions & Histories';
        renderHistory();
            }
        }

        // --- Core Functions ---
    function handleFormSubmission(e) {
      e.preventDefault();
      const attachmentInput = document.getElementById('attachment');
      // prepare form data and submit to server
      const newRequest = {
        barangay: currentUserBarangay,
        requestType: document.getElementById('requestType').value,
        urgency: document.getElementById('urgencyLevel').value,
        location: document.getElementById('location').value,
        description: document.getElementById('description').value,
      };
      requestForm.reset();
      modal.style.display = 'none';

      // send to server using FormData (handles file upload)
      const formData = new FormData();
      formData.append('barangay', newRequest.barangay);
      formData.append('requestType', newRequest.requestType);
      formData.append('urgency', newRequest.urgency);
      formData.append('location', newRequest.location);
  formData.append('description', newRequest.description);
      if (attachmentInput.files.length > 0) formData.append('attachment', attachmentInput.files[0]);

      (async () => {
        try {
          const res = await fetch('submit_request.php', { method: 'POST', body: formData });
          if (!res.ok) throw new Error('Network response was not ok');
          const data = await res.json();
          if (data && data.request) {
            // refresh server-backed requests so UI shows canonical data (request_code etc)
            await fetchAndCacheRequests();
            renderUI();
          } else if (data && data.id) {
            await fetchAndCacheRequests();
            renderUI();
          }

        } catch (err) {
          console.error('submit_request failed', err);
          // leave the temp entry in localStorage so user can retry later; optionally mark as failed
          alert('Failed to submit request to server. Your request is saved locally and will remain visible.');
        }
      })();
    }

  async function handleTracking() {
            const requestId = trackingInput.value.trim();
            if (!requestId) {
                trackingResultsContainer.innerHTML = `<p class="text-red-500 text-center">Please enter a Request ID.</p>`;
                return;
            }
        // prefer server-backed requests
        try {
          const res = await fetch(`list_requests.php?role=${encodeURIComponent('Barangay Official')}&barangay=${encodeURIComponent(currentUserBarangay)}`, { credentials: 'same-origin' });
          if (res.ok) {
            const d = await res.json();
            const found = (d.requests || []).find(r => String(r.id) === String(requestId));
            if (found) return displayTrackingTimeline(found);
          }
        } catch (e) { /* ignore */ }

        const allRequests = JSON.parse(localStorage.getItem('opmdcRequests')) || [];
        const request = allRequests.find(r => r.id == requestId && r.barangay === currentUserBarangay);
        if (request) {
          displayTrackingTimeline(request);
        } else {
          trackingResultsContainer.innerHTML = `<p class="text-center text-gray-600">No request found with ID #${requestId} for this barangay.</p>`;
        }
        }
        
        function displayTrackingTimeline(request) {
            let timelineHTML = `
                <h4 class="font-semibold mb-2">Tracking Details for Request #${request.id}</h4>
                <p class="text-sm text-gray-600 mb-4">Type: ${request.requestType}</p>
            `;
            const history = request.history || [{ status: request.status, timestamp: request.date, notes: 'Initial status.' }];
            history.forEach((event, index) => {
                const isLast = index === history.length - 1;
                let dotClass = 'timeline-dot-default';
                let icon = 'fa-clock';
                if (event.status === 'Pending') { dotClass = 'timeline-dot-pending'; icon = 'fa-hourglass-half'; }
                if (event.status === 'Approved') { dotClass = 'timeline-dot-approved'; icon = 'fa-check'; }
                if (event.status === 'Declined') { dotClass = 'timeline-dot-declined'; icon = 'fa-times'; }
                timelineHTML += `
                    <div class="timeline-item ${isLast ? 'pb-0' : ''}">
                        <div class="timeline-dot ${dotClass}">
                            <i class="fas ${icon} text-white text-xs"></i>
                        </div>
                        <div class="font-semibold">${event.status}</div>
                        <div class="text-xs text-gray-500">${new Date(event.timestamp).toLocaleString()}</div>
                        <p class="text-sm mt-1">${event.notes || ''}</p>
                    </div>
                `;
            });
            trackingResultsContainer.innerHTML = timelineHTML;
        }

        function renderUI() {
      // prefer server cached requests for this barangay
      let serverCached = JSON.parse(localStorage.getItem('opmdcRequestsServer')) || null;
      let barangayRequests = [];
      if (serverCached && Array.isArray(serverCached)) {
        barangayRequests = serverCached.filter(r => r.barangay === currentUserBarangay);
      } else {
        // fallback: empty list (avoid localStorage usage for main data)
        barangayRequests = [];
      }

            document.getElementById('total-reports').textContent = barangayRequests.length;
            document.getElementById('approved-reports').textContent = barangayRequests.filter(r => r.status === 'Approved').length;
            document.getElementById('pending-reports').textContent = barangayRequests.filter(r => r.status === 'Pending').length;
            document.getElementById('declined-reports').textContent = barangayRequests.filter(r => r.status === 'Declined').length;
            
            activityTableBody.innerHTML = '';
            if (barangayRequests.length === 0) {
              activityTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">No recent activity.</td></tr>`;
            } else {
        barangayRequests.slice(0, 5).forEach(req => {
          const statusBadge = getStatusBadge(req.status);
          const displayId = req.request_code || req.id;
          const created = req.created_at || req.date || new Date().toISOString();
          const row = `
            <tr class="bg-white border-b hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${escapeHtml(displayId)}</td>
              <td class="px-6 py-4">${escapeHtml(req.request_type || req.requestType || '')}</td>
              <td class="px-6 py-4">${new Date(created).toLocaleDateString()}</td>
              <td class="px-6 py-4">${statusBadge}</td>
              <td class="px-6 py-4 flex items-center space-x-3">
                <a class="text-blue-600 hover:underline" href="list_requests.php?id=${encodeURIComponent(req.id)}">View</a>
              </td>
            </tr>
          `;
          activityTableBody.innerHTML += row;
        });
            }
        }

    // Delete handler: remove request from localStorage (or server) and rerender
    function deleteActivityById(id) {
      if (!id) return;
      if (!confirm('Delete this request from Recent Activity? This cannot be undone.')) return;
      // try server delete endpoint first (if exists)
      (async () => {
        try {
          const res = await fetch('delete_request.php', { method: 'POST', body: new URLSearchParams({ id }), credentials: 'same-origin' });
          if (res.ok) {
            await fetchAndCacheRequests();
            renderUI();
            return;
          }
        } catch (e) { /* ignore */ }

        // fallback to local removal
        let all = JSON.parse(localStorage.getItem('opmdcRequests')) || [];
        const beforeLen = all.length;
        all = all.filter(r => !(r.id == id && r.barangay === currentUserBarangay));
        if (all.length === beforeLen) return; // nothing removed
        localStorage.setItem('opmdcRequests', JSON.stringify(all));
        renderUI();
      })();
    }

    // Render full history list for the current barangay
    function renderHistory() {
      // use server cached requests if available
      const serverCached = JSON.parse(localStorage.getItem('opmdcRequestsServer')) || null;
      let barangayRequests = [];
      if (serverCached && Array.isArray(serverCached)) {
        barangayRequests = serverCached.filter(r => r.barangay === currentUserBarangay).sort((a,b)=> new Date(b.created_at || b.date) - new Date(a.created_at || a.date));
      } else {
        const allRequests = JSON.parse(localStorage.getItem('opmdcRequests')) || [];
        barangayRequests = allRequests.filter(r => r.barangay === currentUserBarangay).sort((a,b)=> new Date(b.date) - new Date(a.date));
      }
      historyList.innerHTML = '';
      if (barangayRequests.length === 0) {
        historyList.innerHTML = '<div class="text-gray-500">No transactions found.</div>';
        return;
      }

      barangayRequests.forEach(req => {
  const hist = req.history || [{ status: req.status, timestamp: req.date || req.created_at, notes: 'Initial' }];
        const item = document.createElement('div');
        item.className = 'p-4 border rounded-lg bg-white';
        item.innerHTML = `
          <div class="flex justify-between items-start">
            <div>
              <div class="text-sm text-gray-500">Request ID</div>
              <div class="font-medium text-gray-800">${escapeHtml(String(req.id))}</div>
              <div class="text-xs text-gray-500">${escapeHtml(req.requestType || '')} • ${new Date(req.date).toLocaleString()}</div>
            </div>
            <div class="text-right">
              <div class="text-sm">Status</div>
              <div class="font-semibold">${escapeHtml(req.status || '')}</div>
            </div>
          </div>
          <details class="mt-3 border-t pt-3">
            <summary class="cursor-pointer text-sm text-blue-600">View full history (${hist.length} events)</summary>
            <div class="mt-2 space-y-2">
              ${hist.map(ev => `
                <div class="p-2 bg-gray-50 rounded">
                  <div class="text-xs text-gray-600">${new Date(ev.timestamp).toLocaleString()}</div>
                  <div class="font-medium">${escapeHtml(ev.status || '')}</div>
                  <div class="text-sm text-gray-700">${escapeHtml(ev.notes || '')}</div>
                </div>
              `).join('')}
            </div>
          </details>
        `;
        historyList.appendChild(item);
      });
    }

    // Fetch and cache server-side requests for this barangay (and globally) into localStorage
    async function fetchAndCacheRequests() {
      try {
        const res = await fetch(`list_requests.php?role=${encodeURIComponent('Barangay Official')}&barangay=${encodeURIComponent(currentUserBarangay)}`, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const d = await res.json();
        if (d && Array.isArray(d.requests)) {
          // store server copy for quick UI use
          // also keep a global server cache under opmdcRequestsServer
          localStorage.setItem('opmdcRequestsServer', JSON.stringify(d.requests));
          return true;
        }
      } catch (err) {
        console.warn('Could not fetch server requests for barangay', err);
      }
      return false;
    }

        function getStatusBadge(status) {
            const statuses = {
                'Approved': 'bg-green-100 text-green-800', 'Pending': 'bg-yellow-100 text-yellow-800', 'Declined': 'bg-red-100 text-red-800',
            };
            const classes = statuses[status] || 'bg-gray-100 text-gray-800';
            return `<span class="${classes} text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">${status}</span>`;
        }

        function updateTime() {
          const dateElement = document.getElementById('current-date');
          const now = new Date();
          const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
          dateElement.textContent = now.toLocaleDateString('en-US', options);
        }
        
        // Initial call
        initializeDashboard();

    // delegate delete clicks for activity table
    activityTableBody.addEventListener('click', function (e) {
      const btn = e.target.closest('.delete-activity-btn');
      if (!btn) return;
      const id = btn.getAttribute('data-id');
      deleteActivityById(id);
    });

    // --- Notification System (Realtime via SSE) ---
    const notifBellBtn = document.getElementById('notifBellBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');

    // EventSource for realtime notifications
    let evtSource = null;
    let sseReconnectDelay = 1000; // start with 1s
    function startSSE() {
      if (typeof EventSource === 'undefined') return; // not supported
      try {
        // pass last_id from local storage so stream sends only newer ones
        const last = (JSON.parse(localStorage.getItem('opmdcNotifications')) || []).reduce((m, n) => Math.max(m, n.id || 0), 0);
        evtSource = new EventSource(`notifications_stream.php?last_id=${last}`);

        evtSource.addEventListener('open', () => {
          console.log('SSE connected');
          sseReconnectDelay = 1000;
        });

        evtSource.addEventListener('notification', (e) => {
          try {
            const data = JSON.parse(e.data);
            // normalize and add to local list
            const title = data.title || 'Notification';
            const body = data.body || '';
            const id = parseInt(data.id, 10) || Date.now();
            const time = data.created_at || new Date().toISOString();
            const note = { id, title, body, time, read: false };
            // Add and render
            const notes = loadNotifications();
            // avoid duplicates
            if (!notes.find(n => n.id === note.id)) {
              notes.push(note);
              saveNotifications(notes);
            }
            renderNotifications();
          } catch (err) {
            console.error('Invalid SSE payload', err);
          }
        });

        evtSource.addEventListener('error', (ev) => {
          if (evtSource.readyState === EventSource.CLOSED) {
            console.warn('SSE closed by server');
            evtSource.close();
            evtSource = null;
            scheduleReconnect();
          }
        });

      } catch (err) {
        console.error('SSE start failed', err);
        scheduleReconnect();
      }
    }

    function scheduleReconnect() {
      setTimeout(() => {
        sseReconnectDelay = Math.min(60000, sseReconnectDelay * 2);
        startSSE();
      }, sseReconnectDelay);
    }

    function loadNotifications() {
      return JSON.parse(localStorage.getItem('opmdcNotifications')) || [];
    }

    function saveNotifications(arr) {
      localStorage.setItem('opmdcNotifications', JSON.stringify(arr));
      window.dispatchEvent(new Event('storage'));
    }

    function renderNotifications() {
      const notes = loadNotifications();
      const unreadCount = notes.filter(n => !n.read).length;
      if (unreadCount > 0) {
        notifBadge.textContent = unreadCount;
        notifBadge.classList.remove('hidden');
      } else {
        notifBadge.classList.add('hidden');
      }

      if (notes.length === 0) {
        notifList.innerHTML = '<div class="notif-empty text-center">No notifications.</div>';
        return;
      }

      notifList.innerHTML = '';
      notes.slice().reverse().forEach(note => {
        const item = document.createElement('div');
        item.className = 'notif-item rounded hover:bg-gray-50 p-2 ' + (note.read ? '' : 'unread');
        item.innerHTML = `
          <div class="flex-1">
            <div class="text-sm font-medium">${escapeHtml(note.title)}</div>
            <div class="text-xs text-gray-600 mt-1">${escapeHtml(note.body)}</div>
            <div class="time mt-1">${new Date(note.time).toLocaleString()}</div>
          </div>
          <div class="flex flex-col items-end ml-2 space-y-2">
            <button class="mark-read-btn text-xs text-blue-600">${note.read ? 'Unread' : 'Mark read'}</button>
            <button class="delete-btn text-xs text-red-600">Delete</button>
          </div>
        `;

        // Handlers
        item.querySelector('.mark-read-btn').addEventListener('click', () => {
          toggleRead(note.id);
        });
        item.querySelector('.delete-btn').addEventListener('click', () => {
          deleteNotification(note.id);
        });

        notifList.appendChild(item);
      });
    }

    function toggleRead(id) {
      const notes = loadNotifications();
      const idx = notes.findIndex(n => n.id === id);
      if (idx === -1) return;
      const newState = !notes[idx].read;
      notes[idx].read = newState;
      saveNotifications(notes);
      renderNotifications();
      // attempt to sync to server (mark as read)
      if (newState) {
        // call notifications_mark_read.php?id=... (GET)
        fetch(`notifications_mark_read.php?id=${encodeURIComponent(id)}`, { method: 'GET' }).catch(err => console.warn('mark read failed', err));
      }
    }

    function markAllRead() {
      const notes = loadNotifications();
      notes.forEach(n => n.read = true);
      saveNotifications(notes);
      renderNotifications();
      // sync to server: iterate through notes and call mark read endpoint
      notes.forEach(n => {
        fetch(`notifications_mark_read.php?id=${encodeURIComponent(n.id)}`, { method: 'GET' }).catch(err => {});
      });
    }

    function deleteNotification(id) {
      let notes = loadNotifications();
      notes = notes.filter(n => n.id !== id);
      saveNotifications(notes);
      renderNotifications();
    }

    // Add a notification to localStorage. If serverId/time are provided, use them so local IDs match DB.
    function addNotification(title, body, opts = {}) {
      const notes = loadNotifications();
      const id = opts.id ? Number(opts.id) : (Date.now() + Math.floor(Math.random() * 1000));
      const time = opts.time || new Date().toISOString();
      notes.push({ id, title, body, time, read: !!opts.read });
      saveNotifications(notes);
      renderNotifications();
    }

    // Simple XSS-safe text escaper
    function escapeHtml(str) {
      return String(str).replace(/[&<>\"']/g, function (s) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s];
      });
    }

    // Toggle dropdown
    notifBellBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notificationDropdown.classList.toggle('hidden');
      if (!notificationDropdown.classList.contains('hidden')) {
        renderNotifications();
      }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!notificationDropdown.classList.contains('hidden')) {
        notificationDropdown.classList.add('hidden');
      }
    });

    // sample button removed; use addNotification(...) and POST to notifications.php where needed

    markAllReadBtn.addEventListener('click', () => markAllRead());

    // Initial render of notifications
    renderNotifications();
  // Start realtime stream
  startSSE();
    });
  </script>                            

</body>
</html> 
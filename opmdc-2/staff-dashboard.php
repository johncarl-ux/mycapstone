<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        /* Sidebar slide/fade animation */
        /* reveal CSS moved to assets/ui-animations.css */
    </style>
    <link rel="stylesheet" href="assets/ui-animations.css">
    </style>
</head>
<body class="bg-slate-50">

    <div class="flex h-screen">
    <aside class="sidebar w-64 bg-gray-800 text-white flex flex-col" aria-hidden="false" data-reveal="sidebar">
      <div class="p-6 text-center border-b border-gray-700">
    <img src="assets/image1.png" alt="Mabini Seal" class="logo-formal">
        <h2 class="text-xl font-semibold">OPMDC Staff</h2>
        <p class="text-xs text-gray-400">Mabini, Batangas</p>
      </div>
      <nav class="flex-grow px-4 py-6">
        <a href="#" id="dashboard-link" class="sidebar-link flex items-center px-4 py-2 text-gray-100 rounded-md">
          <i class="fas fa-tachometer-alt mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="#proposals" id="proposals-link" class="sidebar-link flex items-center px-4 py-2 mt-2 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md">
          <i class="fas fa-project-diagram mr-3"></i>
          <span>Proposals</span>
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
                                <p class="text-sm font-semibold text-gray-800">OPMDC Staff</p>
                                <p class="text-xs text-gray-500">Staff</p>
                            </div>
                        </div>
          </div>
        </div>
      </header>
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
        <div id="dashboard-view" class="view">
          <div class="container mx-auto">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Activity Reports & Accounts Overview</h3>
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
                
                <div class="border rounded-lg p-4 bg-white mb-6 shadow-sm">
                    <h4 class="text-lg font-medium mb-3 text-gray-800">Filters</h4>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center space-x-2">
                            <label for="typeFilter" class="text-sm font-medium text-gray-700">Submission:</label>
                            <select id="typeFilter" class="w-48 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="all">All Types</option>
                                <option value="Project Proposal">Project Proposal</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="barangayFilter" class="text-sm font-medium text-gray-700">Sent by:</label>
                            <select id="barangayFilter" class="w-48 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                               <option value="all">All Barangays</option>
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
                        <div class="flex items-center space-x-2">
                            <label for="statusFilter" class="text-sm font-medium text-gray-700">Status:</label>
                            <select id="statusFilter" class="w-48 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="all">All</option>
                                <option value="For Review">For Review</option>
                                <option value="Processing">Processing</option>
                                <option value="For Head Approval">For Head Approval</option>
                                <option value="Requires Revision">Requires Revision</option>
                                <option value="Approved">Approved</option>
                                <option value="Declined">Declined</option>
                            </select>
                        </div>
                    </div>
                </div>
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

<script>
document.addEventListener("DOMContentLoaded", () => {
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
    
    // --- Event Listeners ---
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1) + '-view';
            // A special case for the dashboard link which has href="#"
            const viewIdToShow = targetId === "-view" ? "dashboard-view" : targetId;
            const targetView = document.getElementById(viewIdToShow);
            const title = link.querySelector('span').textContent;
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
        }
    });

    document.getElementById('statusFilter').addEventListener('change', renderProposalsPage);
    document.getElementById('typeFilter').addEventListener('change', renderProposalsPage);
    document.getElementById('barangayFilter').addEventListener('change', renderProposalsPage);

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
            const row = `
                <tr class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                    <td class="py-4 px-4">${acc.barangayName}</td>
                    <td class="py-4 px-4">${acc.representative}</td>
                    <td class="py-4 px-4">${emailKey}</td>
                    <td class="py-4 px-4">${getStatusBadge(acc.status)}</td>
                    <td class="py-4 px-4">
                        ${acc.status === 'pending' ? `
                            <button class="action-btn btn-approve" onclick="updateAccountStatus(${acc.id}, 'approved')">Approve</button>
                            <button class="action-btn btn-decline" onclick="updateAccountStatus(${acc.id}, 'declined')">Decline</button>
                        ` : `
                            <button class="action-btn btn-decline" onclick="deleteAccount(${acc.id})">Delete</button>
                        `}
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
        // normalize serverRequests to a common shape
        requests = serverRequests.map(r => ({ id: r.id, barangay: r.barangay, title: r.request_type || r.title || 'Barangay Request', date: r.created_at || r.date || new Date().toISOString(), status: r.status || 'Pending' }));
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
        const row = `
            <tr class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                <td class="py-4 px-4">${req.id}</td>
                <td class="py-4 px-4">${req.barangay}</td>
                <td class="py-4 px-4">${escapeHtml(req.title || (req.requestType || 'Proposal'))}</td>
                <td class="py-4 px-4">${new Date(req.created_at || req.date).toLocaleDateString()}</td>
                <td class="py-4 px-4">${getStatusBadge(req.status)}</td>
                <td class="py-4 px-4">
                     <a class="action-btn btn-details" href="list_requests.php?id=${encodeURIComponent(req.id)}">View</a>
                            ${ (String(req.status).toLowerCase() !== 'approved' && String(req.status).toLowerCase() !== 'declined') ? `
                                <button class="action-btn btn-approve" onclick="confirmUpdateRequestStatus(${req.id}, 'Approved')">Approve</button>
                                <button class="action-btn btn-decline" onclick="confirmUpdateRequestStatus(${req.id}, 'Declined')">Decline</button>
                            ` : '' }
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

    // No cross-tab localStorage sync — updates come from server (via periodic fetch or SSE).

function renderProposalsPage() {
    const proposalsManagementTableBody = document.getElementById('proposals-management-table-body');
    if (!proposalsManagementTableBody) return;
    // prefer server-provided proposals when available
    let proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals.slice() : [];
    
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const barangayFilter = document.getElementById('barangayFilter').value;
    
    if (statusFilter !== 'all') {
        proposals = proposals.filter(p => p.status === statusFilter);
    }
    if (barangayFilter !== 'all') {
        proposals = proposals.filter(p => p.barangay === barangayFilter);
    }
    // Since all current data is 'Project Proposal', this filter will hide all items if another type is selected.
    if (typeFilter !== 'all' && typeFilter !== 'Project Proposal') {
        proposals = [];
    }


    proposalsManagementTableBody.innerHTML = '';
    if (proposals.length === 0) {
      proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">No project proposals match the filter.</td></tr>`;
      return;
    }
    proposals.forEach((prop, index) => {
        const statusBadge = getStatusBadge(prop.status);
        const actionButton = `<button class="action-btn btn-details" onclick="openProposalDetails('${prop.id}')">View Details</button>`;
        const row = `<tr class="bg-white border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">${prop.title}</th>
                <td class="px-6 py-4">${prop.barangay}</td><td class="px-6 py-4">${prop.date}</td>
                <td class="px-6 py-4">${prop.lastUpdated}</td><td class="px-6 py-4">${statusBadge}</td>
                <td class="px-6 py-4">${actionButton}</td></tr>`;
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

    // refresh periodically
    setTimeout(fetchServerData, 60000);
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

function showConfirm(title, message, callback) {
    const alertModal = document.getElementById('alertModal');
    document.getElementById('alertModalTitle').textContent = title;
    document.getElementById('alertModalMessage').textContent = message;
    const actions = document.getElementById('alertModalActions');
    actions.innerHTML = `
        <button id="confirmCancelBtn" class="mr-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
        <button id="confirmOkBtn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>`;
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
    });
}

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
    });
}

// SSE: subscribe to notifications stream so staff dashboard refreshes when relevant notifications arrive
try {
    const staffSse = new EventSource('notifications_stream.php');
    staffSse.addEventListener('notification', (e) => {
        try {
            const payload = JSON.parse(e.data || '{}');
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
        const row = `
            <tr class="border-b ${index % 2 === 1 ? 'even:bg-slate-50' : ''}">
                <td class="py-4 px-4">${p.id}</td>
                <td class="py-4 px-4">${p.title}</td>
                <td class="py-4 px-4">${new Date(p.date).toLocaleDateString()}</td>
                <td class="py-4 px-4">${getStatusBadge(p.status)}</td>
                <td class="py-4 px-4">
                    ${(p.status !== 'Approved' && p.status !== 'Declined') ? `<button class="action-btn btn-update" onclick="openEditSubmission('${p.id}')">Edit / Resubmit</button>` : ''}
                    <button class="action-btn btn-details" onclick="openProposalDetails('${p.id}')">View</button>
                </td>
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

<<<<<<< HEAD
=======
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
>>>>>>> 996c8d0d135fff7224812be0b39c025218bf85f0
</body>
</html>
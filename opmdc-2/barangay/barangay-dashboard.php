<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Officials Dashboard</title>
  <base href="./">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    
    .modal-backdrop {
      background-color: rgba(0,0,0,0.5);
    }
    
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

  /* Horizontal stepper (tracker) styles */
  .stepper { display:flex; align-items:center; gap:1rem; width:100%; }
  .stepper-line { height:4px; background:#e5e7eb; flex:1; position:relative; }
  .step { display:flex; flex-direction:column; align-items:center; width:120px; text-align:center; }
  .step .circle { width:18px; height:18px; border-radius:9999px; background:#cbd5e0; display:flex; align-items:center; justify-content:center; color:#fff; }
  .step.active .circle { background:#0ea5e9; }
  .step-label { font-size:13px; color:#6b7280; margin-top:8px; }
  .step.active .step-label { color:#111827; font-weight:600; }

    /* Active nav link style */
    .nav-active {
        background-color: #1f2937; /* bg-gray-800 */
        color: #ffffff;
        font-weight: 600;
    }
    /* Enhanced Collapsible Sidebar */
    .sidebar {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      width: 16rem;
      background: var(--sidebar-bg);
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar.collapsed {
      width: 4.5rem;
    }
    
    .sidebar.collapsed .sidebar-text {
      opacity: 0;
      display: none;
    }
    
    .sidebar.collapsed #barangay-name-header,
    .sidebar.collapsed .text-xs.text-gray-400 {
      display: none;
    }
    
    /* Sidebar Navigation Links */
    .sidebar a {
      position: relative;
      transition: all 0.2s ease;
      font-weight: 500;
    }
    
    .sidebar a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 0;
      background: var(--brand-accent-2);
      border-radius: 0 3px 3px 0;
      transition: height 0.3s ease;
    }
    
    .sidebar a:hover::before,
    .sidebar a.nav-active::before {
      height: 70%;
    }
    
    .sidebar a i {
      font-size: 1.125rem;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-toggle {
      position: absolute;
      right: -12px;
      top: 24px;
      background: white;
      border: 2px solid #e5e7eb;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-toggle:hover {
      background: var(--brand-accent);
      border-color: var(--brand-accent);
      transform: scale(1.1);
    }
    
    .sidebar-toggle:hover i {
      color: white !important;
    }
    
    .sidebar.collapsed .sidebar-toggle {
      transform: rotate(180deg);
    }
    
    .sidebar.collapsed .sidebar-toggle:hover {
      transform: rotate(180deg) scale(1.1);
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
  /* Toast notifications (small popups) */
  .notif-toast-container { position: fixed; right: 1rem; bottom: 1rem; z-index: 60; display:flex; flex-direction:column; gap:0.5rem; }
  .notif-toast { background: white; border-radius: 0.5rem; box-shadow: 0 6px 18px rgba(0,0,0,0.12); padding: 0.75rem 1rem; width: 320px; cursor: pointer; transition: transform 0.15s ease, opacity 0.2s ease; }
  .notif-toast:hover { transform: translateY(-4px); }
  .notif-toast__title { font-weight: 600; color: #1F2937; }
  .notif-toast__body { font-size: 0.85rem; color: #4B5563; margin-top: 0.25rem; }
  .notif-toast--new { border-left: 4px solid #3B82F6; }
    /* reveal CSS moved to assets/ui-animations.css */

  /* Flip card for simple translations */
  .flip-card { perspective: 1000px; }
  .flip-card .flip-inner { transition: transform 0.6s; transform-style: preserve-3d; position: relative; }
  .flip-card .flip-front,
  .flip-card .flip-back { -webkit-backface-visibility: hidden; backface-visibility: hidden; border-radius: 0.75rem; }
  .flip-card .flip-back { transform: rotateY(180deg); position: absolute; inset: 0; }
  .flip-card.is-flipped .flip-inner { transform: rotateY(180deg); }
  /* allow hover flip on non-touch devices */
  @media (hover: hover) {
    .flip-card:hover .flip-inner { transform: rotateY(180deg); }
  }
  .flip-card[tabindex] { outline: none; cursor: pointer; }
  .flip-back .tag-label { font-size: 12px; color: #374151; margin-top: 6px; }
  </style>
  <link rel="stylesheet" href="../assets/ui-animations.css">
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
  <aside class="sidebar w-64 text-white flex flex-col relative" data-reveal="sidebar">
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
      <i class="fas fa-chevron-left text-gray-600 text-xs"></i>
    </button>
    
    <!-- Header Section -->
    <div class="p-6 text-center border-b border-gray-700/50">
      <img src="../assets/image1.png" alt="Logo" class="logo-formal">
      <h2 id="barangay-name-header" class="text-lg font-bold sidebar-text tracking-wide">Barangay Name</h2>
      <p class="text-[11px] text-gray-400 sidebar-text mt-1 font-medium">Mabini, Batangas</p>
    </div>
    
    <!-- Navigation Section -->
    <nav class="flex-grow px-3 py-6 space-y-1">
      <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3 sidebar-text">Main Menu</p>
      
      <a href="#" id="nav-dashboard" class="flex items-center px-4 py-3 text-gray-100 bg-slate-700 rounded-lg nav-active" title="Dashboard">
        <i class="fas fa-th-large mr-3"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
      
      <a href="#" id="nav-status" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="Status">
        <i class="fas fa-tasks mr-3"></i>
        <span class="sidebar-text">Proposal Tracking</span>
      </a>
      
      <a href="#" id="nav-history" class="flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="History">
        <i class="fas fa-clock-rotate-left mr-3"></i>
        <span class="sidebar-text">History</span>
      </a>
    </nav>
    
    <!-- Footer Section -->
    <div class="p-4 border-t border-gray-700/50">
      <a href="../login.html" class="flex items-center w-full px-4 py-3 text-gray-300 hover:bg-rose-600 hover:text-white rounded-lg transition-all" title="Logout">
        <i class="fas fa-right-from-bracket mr-3"></i>
        <span class="sidebar-text">Logout</span>
      </a>
    </div>
  </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4" data-reveal="header">
        <div class="flex items-center justify-between">
          <div>
            <h1 id="header-title" class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p id="current-date" class="text-xs text-gray-500 mt-0.5"></p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="relative">
              <span class="absolute left-3 top-2.5 text-gray-400">
                <i class="fas fa-search text-sm"></i>
              </span>
              <input type="text" placeholder="Search..." class="w-64 pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                <div class="flex justify-between items-center mb-8" data-reveal="group">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Welcome, <span id="welcome-name">Barangay Name</span></h2>
                        <p class="text-gray-500 text-sm mt-1">Overview of reports and activities</p>
                    </div>
                    <div>
                      <button id="newProjectProposalBtn" class="bg-green-600 text-white font-semibold py-3 px-6 rounded-lg shadow-sm hover:bg-green-700 transition-all duration-200 flex items-center">
                          <i class="fas fa-plus mr-2"></i> Submit Proposal
                      </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow duration-200">
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-lg">
                        <i class="fas fa-folder text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-medium">Total</p>
                        <p id="total-reports" class="text-3xl font-bold text-gray-900 mt-1">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow duration-200">
                    <div class="bg-green-50 text-green-600 p-4 rounded-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-medium">Approved</p>
                        <p id="approved-reports" class="text-3xl font-bold text-gray-900 mt-1">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow duration-200">
                    <div class="bg-yellow-50 text-yellow-600 p-4 rounded-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-medium">Pending</p>
                        <p id="pending-reports" class="text-3xl font-bold text-gray-900 mt-1">0</p>
                    </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow duration-200">
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-xs uppercase tracking-wider font-medium">Declined</p>
                        <p id="declined-reports" class="text-3xl font-bold text-gray-900 mt-1">0</p>
                    </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="flip-card">
                    <div class="flip-inner bg-white rounded-xl shadow-sm border border-gray-100">
                      <div class="flip-front p-5">
                        <div class="flex items-start space-x-3">
                          <div class="bg-blue-50 text-blue-600 p-3 rounded-lg">
                            <i class="fas fa-map-marked-alt text-xl"></i>
                          </div>
                          <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">CLUP</div>
                            <div class="text-sm font-semibold text-gray-900">Comprehensive Land Use Plan</div>
                            <div class="text-xs text-gray-500 mt-2">Long-term guide for physical development, zoning, and spatial strategies. Coverage: 8–12 years.</div>
                          </div>
                        </div>
                      </div>
                      <div class="flip-back p-5 bg-white">
                        <div>
                          <div class="text-sm font-semibold text-gray-900">Plano ng Lupa (CLUP)</div>
                          <div class="text-xs text-gray-500 mt-2">Pangmatagalang gabay sa paggamit ng lupa at zoning. Saklaw: 8–12 taon.</div>
                          <div class="tag-label">I-click o pindutin para bumalik.</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="flip-card">
                    <div class="flip-inner bg-white rounded-xl shadow-sm border border-gray-100">
                      <div class="flip-front p-5">
                        <div class="flex items-start space-x-3">
                          <div class="bg-green-50 text-green-600 p-3 rounded-lg">
                            <i class="fas fa-project-diagram text-xl"></i>
                          </div>
                          <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">CDP</div>
                            <div class="text-sm font-semibold text-gray-900">Comprehensive Development Plan</div>
                            <div class="text-xs text-gray-500 mt-2">Medium-term programs and development strategies supporting the CLUP. Coverage: typically 6 years.</div>
                          </div>
                        </div>
                      </div>
                      <div class="flip-back p-5 bg-white">
                        <div>
                          <div class="text-sm font-semibold text-gray-900">Plano ng Pag-unlad (CDP)</div>
                          <div class="text-xs text-gray-500 mt-2">Gitnang-panahong mga programa at estratehiya na sumusuporta sa CLUP. Saklaw: karaniwang 6 na taon.</div>
                          <div class="tag-label">I-click o pindutin para bumalik.</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="flip-card">
                    <div class="flip-inner bg-white rounded-xl shadow-sm border border-gray-100">
                      <div class="flip-front p-5">
                        <div class="flex items-start space-x-3">
                          <div class="bg-yellow-50 text-yellow-600 p-3 rounded-lg">
                            <i class="fas fa-calendar-check text-xl"></i>
                          </div>
                          <div>
                            <div class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-1">AIP</div>
                            <div class="text-sm font-semibold text-gray-900">Annual Investment Program</div>
                            <div class="text-xs text-gray-500 mt-2">Yearly list of priority projects and budget allocations aligned with CDP and CLUP. Coverage: 1 year (updated annually).</div>
                          </div>
                        </div>
                      </div>
                      <div class="flip-back p-5 bg-white">
                        <div>
                          <div class="text-sm font-semibold text-gray-900">Taunang Programa ng Puhunan (AIP)</div>
                          <div class="text-xs text-gray-500 mt-2">Taunang listahan ng prayoridad na proyekto at alokasyon ng budget na naka-ayon sa CDP at CLUP. Saklaw: 1 taon.</div>
                          <div class="tag-label">I-click o pindutin para bumalik.</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-8 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-5">Recent Activity</h3>
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">ID</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Type</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Date</th>
              <th scope="col" class="px-6 py-4 font-semibold">Status</th>
              <th scope="col" class="px-6 py-4 font-semibold">Action</th>
                        </tr>
                        </thead>
                        <tbody id="activity-table-body" class="divide-y divide-gray-100">
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div id="status-view" class="hidden">
              <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-6xl mx-auto">
                <div class="px-8 py-6 border-b border-gray-200">
                  <div class="flex items-center justify-between mb-4">
                    <div>
                      <h2 class="text-2xl font-bold text-gray-900" id="tracker-title">Proposal Tracking</h2>
                      <p class="text-sm text-gray-500 mt-1" id="tracker-subtitle">Enter Proposal ID to view status.</p>
                    </div>
                    <div class="flex space-x-3" id="tracker-action-buttons">
                      <button id="proposal-track-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-sm transition-all duration-200">Track</button>
                      <button id="proposal-clear-btn" class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium px-5 py-2.5 rounded-lg transition-all duration-200">Clear</button>
                    </div>
                  </div>
                  <div class="flex items-center space-x-3">
                    <input id="proposal-id-input" type="text" placeholder="Proposal ID" class="w-64 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    <p class="text-xs text-gray-400">Statuses: Pending, For Review, For Head Approval, Approved, Implementing, Completed, Declined</p>
                  </div>
                </div>
                <div id="proposal-progress-wrapper" class="px-8 py-6 hidden">
                  <div class="mb-8">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-500 uppercase tracking-wide" id="progress-labels"></div>
                    <div class="relative mt-3 h-2.5 bg-gray-100 rounded-full" id="progress-bar-container">
                      <div id="progress-fill" class="absolute top-0 left-0 h-2.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-700 shadow-sm" style="width:0%"></div>
                      <div id="progress-markers" class="absolute inset-0 flex justify-between"></div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="proposal-details-grid">
                    <!-- Filled dynamically -->
                  </div>
                  <div class="mt-8 pt-8 border-t border-gray-200" id="proposal-attachments-wrapper">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Attachments</h3>
                    <div id="proposal-attachments" class="text-sm text-gray-600">None</div>
                  </div>
                  <div class="mt-8 pt-8 border-t border-gray-200" id="proposal-timeline-wrapper">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Timeline</h3>
                    <div id="proposal-timeline" class="space-y-3"></div>
                  </div>
                </div>
                <div id="proposal-empty-state" class="px-8 py-16 text-center">
                  <i class="fas fa-chart-line text-gray-300 text-5xl mb-4"></i>
                  <p class="text-gray-500 text-sm">Enter Proposal ID above to track</p>
                </div>
              </div>
            </div>

      <div id="history-view" class="hidden">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 max-w-5xl mx-auto">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Transaction History</h2>
          <p class="text-gray-500 text-sm mb-6">All recorded requests. Expand to view status history.</p>
          <div id="history-list" class="space-y-3">
            <!-- Filled by renderHistory() -->
          </div>
        </div>
      </div>

        </div>
      </main>
    </div>
  </div>

  <script src="../assets/ui-animations.js"></script>

  <!-- Request modal removed - proposals only -->


  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
    
    // Route guard: ensure only Barangay Official stays on this page
    try {
      const u = JSON.parse(localStorage.getItem('loggedInUser'));
      if (u && u.role) {
        const r = String(u.role);
  if (/admin/i.test(r)) return (window.location.href = '../admin/admin.php');
  if (/head/i.test(r)) return (window.location.href = '../head/head-dashboard.php');
  if (/staff/i.test(r)) return (window.location.href = '../staff/staff-dashboard.php');
      }
    } catch (e) { /* ignore */ }
  // --- Globals & Element References ---
  const newProjectProposalBtn = document.getElementById('newProjectProposalBtn');
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
          // Proposal tracking elements (new UI)
          const proposalTrackBtn = document.getElementById('proposal-track-btn');
          const proposalClearBtn = document.getElementById('proposal-clear-btn');
          const proposalIdInput = document.getElementById('proposal-id-input');
          const proposalProgressWrapper = document.getElementById('proposal-progress-wrapper');
          const proposalEmptyState = document.getElementById('proposal-empty-state');
          const progressFill = document.getElementById('progress-fill');
          const progressMarkers = document.getElementById('progress-markers');
          const progressLabels = document.getElementById('progress-labels');
          const proposalDetailsGrid = document.getElementById('proposal-details-grid');
          const proposalTimeline = document.getElementById('proposal-timeline');
          const proposalAttachments = document.getElementById('proposal-attachments');
          // Status mapping sequence for visual progress
          const PROPOSAL_STAGES = [
            'Pending','For Review','For Head Approval','Approved','Implementing','Completed','Declined'
          ];
        
  let loggedInUser;
  let currentUserBarangay;
  // Server-derived aggregate counts for this barangay (requests + proposals)
  let barangayCounts = { total: 0, approved: 0, pending: 0, declined: 0 };

        // --- Authentication & Initialization ---
    async function initializeDashboard() {
      // Skip server authentication; allow open access with defaults
      try { loggedInUser = JSON.parse(localStorage.getItem('loggedInUser')); } catch(e) { loggedInUser = null; }
      if (!loggedInUser) {
        loggedInUser = { name: 'Official', role: 'Barangay Official', barangayName: 'Barangay Name' };
      }

      currentUserBarangay = loggedInUser.barangayName || loggedInUser.barangay || 'Barangay Name';

      // Update UI with user info
  document.getElementById('barangay-name-header').textContent = currentUserBarangay;
  // Welcome should show the representative's name; fall back to barangay if name missing
  document.getElementById('welcome-name').textContent = loggedInUser.name || currentUserBarangay;
      document.getElementById('user-name').textContent = loggedInUser.name || 'Official';
      document.getElementById('user-role').textContent = loggedInUser.role;

      // fetch server requests and aggregate counts for this barangay then render UI
      await Promise.all([fetchBarangayCounts(), fetchAndCacheRequests()]);
      renderUI();
      updateTime();
      setInterval(updateTime, 60000);
      // refresh when tab gains focus or becomes visible (helps after navigating back)
      window.addEventListener('focus', () => { refreshRequestsAndUI(); });
      document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible') refreshRequestsAndUI(); });
    }

    // --- Event Listeners ---
  // Open project proposal page (merged form) -> new barangay submit page with Smart Recommendations
    if (newProjectProposalBtn) newProjectProposalBtn.addEventListener('click', () => window.location.href = 'barangay-submit-proposal.php');
    // Proposal tracking listeners
    if (proposalTrackBtn) proposalTrackBtn.addEventListener('click', () => {
      const id = (proposalIdInput.value || '').trim();
      if (!id) { alert('Enter a Proposal ID'); return; }
      startProposalTracking(id);
    });
    if (proposalClearBtn) proposalClearBtn.addEventListener('click', () => clearProposalTracking());
    // Allow Enter key
    if (proposalIdInput) proposalIdInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); proposalTrackBtn.click(); }});
        
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
              headerTitle.textContent = 'Proposal Tracking';
      } else if (viewName === 'history') {
        historyView.classList.remove('hidden');
        navHistory && navHistory.classList.add('nav-active');
        headerTitle.textContent = 'All Transactions & Histories';
        renderHistory();
            }
        }

        // --- Core Functions ---
    // Request submission removed - proposals only

  // --- Proposal Tracking Functions ---
  let proposalPollInterval = null;
  function clearProposalTracking() {
    if (proposalPollInterval) { clearInterval(proposalPollInterval); proposalPollInterval = null; }
    proposalProgressWrapper.classList.add('hidden');
    proposalEmptyState.classList.remove('hidden');
    progressFill.style.width = '0%';
    proposalDetailsGrid.innerHTML = '';
    proposalTimeline.innerHTML = '';
    proposalAttachments.textContent = 'None.';
  }

  async function startProposalTracking(id) {
    await fetchAndRenderProposal(id);
    // Poll every 12s for realtime feel
    if (proposalPollInterval) clearInterval(proposalPollInterval);
    proposalPollInterval = setInterval(() => fetchAndRenderProposal(id), 12000);
  }

  async function fetchAndRenderProposal(id) {
    try {
      const res = await fetch(`../api/get_proposal.php?id=${encodeURIComponent(id)}`, { credentials: 'same-origin' });
      if (!res.ok) throw new Error('Network');
      const data = await res.json();
      if (!data.success) throw new Error(data.error || 'Failed');
      renderProposalTracking(data.proposal, data.timeline || data.history || []);
    } catch (e) {
      console.warn('Fetch proposal failed', e);
      proposalTimeline.innerHTML = `<div class="text-red-600 text-sm">Unable to load proposal #${id}</div>`;
    }
  }

  function renderProposalTracking(proposal, timeline) {
    if (!proposal) return clearProposalTracking();
    proposalEmptyState.classList.add('hidden');
    proposalProgressWrapper.classList.remove('hidden');
    // Progress bar
    const currentIndex = PROPOSAL_STAGES.findIndex(s => s.toLowerCase() === String(proposal.status||'').toLowerCase());
    const effectiveIndex = currentIndex >= 0 ? currentIndex : 0;
    const pct = ((effectiveIndex) / (PROPOSAL_STAGES.length - 1)) * 100;
    progressFill.style.width = pct + '%';
    // labels
    progressLabels.innerHTML = PROPOSAL_STAGES.map(s => `<div class="flex-1 text-center ${s===proposal.status? 'text-gray-900 font-semibold':'text-gray-500'}">${s}</div>`).join('');
    // markers
    progressMarkers.innerHTML = PROPOSAL_STAGES.map((s,i) => {
      const done = i <= effectiveIndex && proposal.status !== 'Declined';
      const declined = proposal.status === 'Declined' && i === effectiveIndex;
      const baseClasses = 'w-3.5 h-3.5 rounded-full border-2 transition-all duration-500 shadow-sm';
      const color = declined ? 'bg-red-500 border-red-500' : (done ? 'bg-blue-600 border-blue-600' : 'bg-white border-gray-300');
      return `<div class="relative" style="flex:1;display:flex;justify-content:${i===0?'flex-start': i===PROPOSAL_STAGES.length-1?'flex-end':'center'}"><span class="${baseClasses} ${color}"></span></div>`;
    }).join('');
    // details grid
    proposalDetailsGrid.innerHTML = `
      ${detailCard('Proposal ID', proposal.id)}
      ${detailCard('Title', escapeHtml(proposal.title||''))}
      ${detailCard('Type', escapeHtml(proposal.project_type||''))}
      ${detailCard('Barangay', escapeHtml(proposal.barangay||''))}
      ${detailCard('Status', escapeHtml(proposal.status||''))}
      ${detailCard('Location', escapeHtml(proposal.location||'—'))}
      ${detailCard('Urgency', escapeHtml(proposal.urgency||'—'))}
      ${detailCard('Budget', proposal.budget != null ? formatCurrency(proposal.budget) : '—')}
      ${detailCard('Submitted', new Date(proposal.created_at).toLocaleString())}
    `;
    // attachments
    if (proposal.attachment) {
      const isImg = /\.(png|jpe?g|gif|webp)$/i.test(proposal.attachment);
      proposalAttachments.innerHTML = isImg ? `<a href="../${proposal.attachment}" target="_blank" class="text-blue-600 underline">View Image</a>` : `<a href="../${proposal.attachment}" target="_blank" class="text-blue-600 underline">Download Attachment</a>`;
    } else {
      proposalAttachments.textContent = 'None.';
    }
    // timeline
    proposalTimeline.innerHTML = timeline.length === 0 ? '<div class="text-gray-400 text-sm">No history</div>' : timeline.map(evt => {
      const ts = new Date(evt.created_at).toLocaleString();
      return `<div class="p-4 border border-gray-200 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
        <div class="flex items-center justify-between mb-2">
          <div class="font-semibold text-gray-900 text-sm">${escapeHtml(evt.status||'')}</div>
          <div class="text-xs text-gray-500">${ts}</div>
        </div>
        <div class="text-sm text-gray-700">${escapeHtml(evt.remarks||'')}</div>
        ${evt.user_role || evt.user_id ? `<div class="text-xs text-gray-500 mt-2">${evt.user_role ? escapeHtml(evt.user_role) : ''}${evt.user_role && evt.user_id ? ' • ' : ''}${evt.user_id ? 'User #' + evt.user_id : ''}</div>` : ''}
      </div>`;
    }).join('');
  }

  function detailCard(label, value) {
    return `<div class="p-5 border border-gray-200 rounded-lg bg-white hover:shadow-md transition-shadow duration-200"><div class="text-xs uppercase tracking-wider text-gray-500 font-medium mb-2">${label}</div><div class="text-sm font-semibold text-gray-900 break-words leading-relaxed">${value}</div></div>`;
  }
  function formatCurrency(n) { try { return '₱' + (parseFloat(n).toLocaleString('en-PH',{minimumFractionDigits:2, maximumFractionDigits:2})); } catch(e){ return n; } }

        function normalizeBarangay(str) {
          const s = String(str || '').trim().toLowerCase();
          return s.replace(/^brgy\.?\s+/i, '').replace(/^barangay\s+/i, '').trim();
        }

        function renderUI() {
      // prefer server cached requests for this barangay
      let serverCached = JSON.parse(localStorage.getItem('opmdcRequestsServer')) || null;
      let barangayRequests = [];
      if (serverCached && Array.isArray(serverCached)) {
        const key = normalizeBarangay(currentUserBarangay);
        barangayRequests = serverCached
          .filter(r => normalizeBarangay(r.barangay) === key)
          .sort((a,b)=> new Date(b.created_at || b.date || 0) - new Date(a.created_at || a.date || 0));
      } else {
        // fallback: empty list (avoid localStorage usage for main data)
        barangayRequests = [];
      }

            // If still empty, attempt a one-time direct refresh (race condition guard)
            if (barangayRequests.length === 0 && !renderUI._retried) {
              renderUI._retried = true;
              fetchAndCacheRequests().then(ok => {
                if (ok) {
                  try {
                    const sc = JSON.parse(localStorage.getItem('opmdcRequestsServer')) || [];
                    const key = normalizeBarangay(currentUserBarangay);
                    barangayRequests = sc.filter(r => normalizeBarangay(r.barangay) === key)
                      .sort((a,b)=> new Date(b.created_at || b.date || 0) - new Date(a.created_at || a.date || 0));
                    // re-run fill after retry
                    fillRecent(barangayRequests);
                  } catch(e){ /* ignore */ }
                } else {
                  fillRecent(barangayRequests);
                }
              });
            } else {
              fillRecent(barangayRequests);
            }

            function fillRecent(barangayRequests) {
              // Use API counts when available; fallback to client-side computation
              const fallbackCounts = {
                total: barangayRequests.length,
                approved: barangayRequests.filter(r => /approved/i.test(String(r.status||''))).length,
                pending: barangayRequests.filter(r => /(pending|for review|for approval|processing)/i.test(String(r.status||''))).length,
                declined: barangayRequests.filter(r => /(declined|rejected)/i.test(String(r.status||''))).length
              };
              const countsToShow = (barangayCounts.total + barangayCounts.approved + barangayCounts.pending + barangayCounts.declined) > 0 ? barangayCounts : fallbackCounts;
              document.getElementById('total-reports').textContent = countsToShow.total;
              document.getElementById('approved-reports').textContent = countsToShow.approved;
              document.getElementById('pending-reports').textContent = countsToShow.pending;
              document.getElementById('declined-reports').textContent = countsToShow.declined;

              activityTableBody.innerHTML = '';
              if (barangayRequests.length === 0) {
                activityTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-gray-500">No recent activity yet. Submit a project proposal.</td></tr>`;
              } else {
                barangayRequests.slice(0, 10).forEach(req => {
                  const statusBadge = getStatusBadge(req.status);
                  const displayId = req.request_code || req.id;
                  const created = req.created_at || req.date || new Date().toISOString();
                  const isFinalized = /approved|declined|completed/i.test(String(req.status || ''));
                  
                  // Better labeling for proposals vs requests
                  let typeLabel = '';
                  if (req.kind === 'proposal') {
                    const icon = '<i class=\"fas fa-file-alt mr-1 text-blue-600\"></i>';
                    typeLabel = `${icon}<span class=\"font-medium text-blue-700\">Proposal:</span> ${escapeHtml(req.title || req.request_type || req.project_type || '')}`;
                  } else {
                    const icon = '<i class=\"fas fa-clipboard-list mr-1 text-green-600\"></i>';
                    typeLabel = `${icon}<span class=\"font-medium text-green-700\">Request:</span> ${escapeHtml(req.request_type || req.requestType || '')}`;
                  }
                  
                  const actionsHtml = `
                    <div class=\"flex items-center space-x-2\">
                      <button class=\"track-btn bg-blue-600 text-white text-xs px-3 py-1 rounded hover:bg-blue-700\" data-id=\"${String(req.id)}\" data-kind=\"${req.kind || 'request'}\">Track</button>
                      ${isFinalized ? `<button class=\"delete-activity-btn text-red-600 text-xs px-3 py-1 rounded border hover:bg-red-50\" data-id=\"${String(req.id)}\">Delete</button>` : `<button class=\"delete-activity-btn text-red-600 text-xs px-3 py-1 rounded border hidden\" data-id=\"${String(req.id)}\">Delete</button>`}
                    </div>
                  `;
                  const row = `
                    <tr class=\"bg-white border-b hover:bg-gray-50\">
                      <td class=\"px-6 py-4 font-medium text-gray-900 whitespace-nowrap\">${escapeHtml(displayId)}</td>
                      <td class=\"px-6 py-4\">${typeLabel}</td>
                      <td class=\"px-6 py-4\">${new Date(created).toLocaleDateString()}</td>
                      <td class=\"px-6 py-4\">${statusBadge}</td>
                      <td class=\"px-6 py-4\">${actionsHtml}</td>
                    </tr>
                  `;
                  activityTableBody.innerHTML += row;
                });
              }
            }
        }

    // Delete handler: remove request from localStorage (or server) and rerender
    function deleteActivityById(id) {
      if (!id) return;
      if (!confirm('Delete this request from Recent Activity? This cannot be undone.')) return;
      // try server delete endpoint first (if exists)
      (async () => {
        try {
          const res = await fetch('../api/delete_request.php', { method: 'POST', body: new URLSearchParams({ id }), credentials: 'same-origin' });
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
        const key = normalizeBarangay(currentUserBarangay);
        barangayRequests = serverCached.filter(r => normalizeBarangay(r.barangay) === key).sort((a,b)=> new Date(b.created_at || b.date) - new Date(a.created_at || a.date));
      } else {
        const allRequests = JSON.parse(localStorage.getItem('opmdcRequests')) || [];
        const key = normalizeBarangay(currentUserBarangay);
        barangayRequests = allRequests.filter(r => normalizeBarangay(r.barangay) === key).sort((a,b)=> new Date(b.date) - new Date(a.date));
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
    async function fetchBarangayCounts() {
      try {
        const res = await fetch(`../api/barangay_dashboard_counts.php?barangay=${encodeURIComponent(currentUserBarangay)}`, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        if (data && data.success) {
          barangayCounts = {
            total: data.total || 0,
            approved: data.approved || 0,
            pending: data.pending || 0,
            declined: data.declined || 0
          };
          return true;
        }
      } catch (e) { console.warn('Could not fetch barangay counts', e); }
      return false;
    }

    // Fetch and cache server-side requests (and proposals) for this barangay
    async function fetchAndCacheRequests() {
      try {
        // Fetch both requests and proposals
        const [requestsRes, proposalsRes] = await Promise.all([
          fetch(`../api/list_requests.php?role=${encodeURIComponent('Barangay Official')}&barangay=${encodeURIComponent(currentUserBarangay)}`, { credentials: 'same-origin' }),
          fetch(`../api/list_staff_proposals.php?barangay=${encodeURIComponent(currentUserBarangay)}`, { credentials: 'same-origin' })
        ]);
        
        // Parse requests with proper error handling
        let requests = [];
        if (requestsRes.ok) {
          try {
            const contentType = requestsRes.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
              const reqData = await requestsRes.json();
              requests = reqData.requests || [];
            }
          } catch (e) {
            console.warn('Failed to parse requests JSON:', e);
          }
        }
        
        // Parse proposals with proper error handling
        let proposals = [];
        if (proposalsRes.ok) {
          try {
            const contentType = proposalsRes.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
              const propData = await proposalsRes.json();
              proposals = propData.success ? (propData.proposals || []) : (propData.proposals || propData || []);
            }
          } catch (e) {
            console.warn('Failed to parse proposals JSON:', e);
          }
        }
        
        // Mark proposals with a 'kind' field for identification
        const markedProposals = proposals.map(p => ({
          ...p,
          kind: 'proposal',
          request_type: p.project_type || p.title || 'Project Proposal',
          status: p.status || 'Pending',
          date: p.created_at,
          barangay: p.barangay
        }));
        
        // Mark requests with 'kind' field
        const markedRequests = requests.map(r => ({ ...r, kind: 'request' }));
        
        // Combine both arrays
        const combined = [...markedRequests, ...markedProposals];
        
        // Store combined data
        localStorage.setItem('opmdcRequestsServer', JSON.stringify(combined));
        return true;
      } catch (err) {
        console.warn('Could not fetch server requests/proposals for barangay', err);
      }
      return false;
    }

    // Refresh helper: re-fetch latest and update dashboard + history if open
    async function refreshRequestsAndUI() {
      try {
        const ok = await fetchAndCacheRequests();
        if (ok) {
          renderUI();
          if (!historyView.classList.contains('hidden')) {
            renderHistory();
          }
        }
      } catch (e) { /* ignore */ }
    }

        function getStatusBadge(status) {
            const statuses = {
            'Approved': 'bg-green-100 text-green-800', 'Pending': 'bg-yellow-100 text-yellow-800', 'Declined': 'bg-red-100 text-red-800', 'For Review': 'bg-yellow-100 text-yellow-800', 'For Approval': 'bg-yellow-100 text-yellow-800', 'Processing': 'bg-yellow-100 text-yellow-800'
            };
          let classes = statuses[status];
          if (!classes) {
            if (/approved/i.test(String(status||''))) classes = 'bg-green-100 text-green-800';
            else if (/(pending|for review|for approval|processing)/i.test(String(status||''))) classes = 'bg-yellow-100 text-yellow-800';
            else if (/(declined|rejected)/i.test(String(status||''))) classes = 'bg-red-100 text-red-800';
            else classes = 'bg-gray-100 text-gray-800';
          }
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

    // delegate clicks for activity table (delete & track)
    activityTableBody.addEventListener('click', function (e) {
      const delBtn = e.target.closest('.delete-activity-btn');
      if (delBtn) {
        const id = delBtn.getAttribute('data-id');
        deleteActivityById(id);
        return;
      }
      const trkBtn = e.target.closest('.track-btn');
      if (trkBtn) {
        const id = trkBtn.getAttribute('data-id');
        // Re-use tracker UI for proposal if IDs overlap (legacy button kept) 
        proposalIdInput.value = id;
        showView('status');
        startProposalTracking(id);
        return;
      }
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
  evtSource = new EventSource(`../api/notifications_stream.php?last_id=${last}`);

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
            // show a small toast for incoming notification
            try { showBarangayNotificationToast(title, body, { requestId: data.request_id }); } catch (err) { console.warn('toast failed', err); }
            // refresh dashboard data so statuses update in near-realtime
            refreshRequestsAndUI();
            // If notification is about proposal approval, auto-refresh the tracking view if it's open
            if (data.request_id && /approved|status update/i.test(title)) {
              if (!statusView.classList.contains('hidden') && proposalIdInput.value == data.request_id) {
                // Refresh the currently tracked proposal to show updated status
                setTimeout(() => fetchAndRenderProposal(data.request_id), 500);
              }
            }
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
  fetch(`../api/notifications_mark_read.php?id=${encodeURIComponent(id)}`, { method: 'GET' }).catch(err => console.warn('mark read failed', err));
      }
    }

    function markAllRead() {
      const notes = loadNotifications();
      notes.forEach(n => n.read = true);
      saveNotifications(notes);
      renderNotifications();
      // sync to server: iterate through notes and call mark read endpoint
      notes.forEach(n => {
  fetch(`../api/notifications_mark_read.php?id=${encodeURIComponent(n.id)}`, { method: 'GET' }).catch(err => {});
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
      const request_id = opts.request_id || opts.requestId || null;
      notes.push({ id, title, body, time, read: !!opts.read, request_id });
      saveNotifications(notes);
      renderNotifications();
    }

    // helper: small visual toast for incoming notifications (barangay)
    function showBarangayNotificationToast(title, body, opts = {}) {
      try {
        const containerId = 'notifToastContainerBarangay';
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
            try { window.location.href = `request_tracker.php?id=${encodeURIComponent(rid)}`; } catch(e) { console.warn('jump error', e); }
          }
          const dd = document.getElementById('notificationDropdown');
          if (dd) { dd.classList.remove('hidden'); }
          if (typeof renderNotifications === 'function') renderNotifications();
          const bell = document.getElementById('notifBellBtn'); if (bell) bell.focus();
          toast.remove();
        };
        if (opts.requestId) toast.setAttribute('data-request-id', String(opts.requestId));
        container.appendChild(toast);
        setTimeout(() => { try { toast.style.opacity = '0'; setTimeout(()=> toast.remove(), 250); } catch(e){} }, opts.duration || 6000);
      } catch (e) { console.warn('Toast error', e); }
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
    
    // Poll server every 10s for notifications (AJAX fallback / supplement to SSE).
    async function pollServerNotifications() {
      try {
  const res = await fetch('../api/notifications.php', { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const data = await res.json();
        if (!data || !Array.isArray(data.notifications)) return;
        // Merge server notifications into local storage list while preserving read state when possible
        const local = loadNotifications();
        const localById = Object.fromEntries(local.map(n=>[n.id, n]));
        let changed = false;
        let hasNewApproval = false;
        for (const s of data.notifications) {
          const sid = Number(s.id);
          if (!sid) continue;
          const existing = localById[sid];
          const noteObj = {
            id: sid,
            title: s.title || '',
            body: s.body || '',
            time: s.created_at || new Date().toISOString(),
            read: (s.is_read == 1),
            request_id: s.request_id
          };
          if (existing) {
            // preserve local read flag if it was already read locally
            noteObj.read = existing.read || noteObj.read;
            // update if content changed
            if (existing.title !== noteObj.title || existing.body !== noteObj.body || existing.time !== noteObj.time || existing.read !== noteObj.read) {
              const idx = local.findIndex(n=>n.id===sid);
              if (idx!==-1) { local[idx] = noteObj; changed = true; }
            }
          } else {
            // new notification - check if it's about proposal approval
            if (/approved|status update/i.test(noteObj.title) && noteObj.request_id) {
              hasNewApproval = true;
            }
            local.push(noteObj); changed = true;
          }
        }
        if (changed) { 
          saveNotifications(local); 
          renderNotifications(); 
          refreshRequestsAndUI();
          // If there's a new approval notification and tracking view is open, refresh it
          if (hasNewApproval && !statusView.classList.contains('hidden') && proposalIdInput.value) {
            setTimeout(() => fetchAndRenderProposal(proposalIdInput.value), 500);
          }
        }
      } catch (err) {
        // network errors are acceptable; SSE provides realtime when available
        // console.warn('pollServerNotifications failed', err);
      }
    }

    
    // initial poll and interval
    pollServerNotifications();
    setInterval(pollServerNotifications, 10000);
    // Also periodically refresh dashboard data to keep statuses fresh
    setInterval(refreshRequestsAndUI, 15000);
    });
  </script>                            

  <script>
    // Flip-card interaction: allow click and keyboard toggling for touch/accessibility
    (function(){
      function initFlipCards(){
        const cards = document.querySelectorAll('.flip-card');
        cards.forEach(c => {
          // ensure flip-inner fills the card
          const inner = c.querySelector('.flip-inner');
          if (inner) {
            inner.style.width = '100%';
            inner.style.height = '100%';
          }
          c.setAttribute('tabindex','0');
          c.setAttribute('role','button');
          c.setAttribute('aria-pressed','false');
          c.addEventListener('click', function(e){
            const flipped = c.classList.toggle('is-flipped');
            c.setAttribute('aria-pressed', flipped ? 'true' : 'false');
          });
          c.addEventListener('keydown', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              const flipped = c.classList.toggle('is-flipped');
              c.setAttribute('aria-pressed', flipped ? 'true' : 'false');
            }
          });
        });
      }
      // initialize after DOM ready
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initFlipCards);
      else initFlipCards();
    })();
  </script>

<!-- Toast container for barangay (created dynamically if missing) -->
<div id="notifToastContainerBarangay" class="notif-toast-container" aria-live="polite"></div>

</body>
</html> 

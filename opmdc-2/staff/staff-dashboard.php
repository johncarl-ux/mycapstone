<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OPMDC Staff Dashboard</title>
  <base href="../">
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
        
        /* Enhanced Dashboard Cards with Perfect Grid Alignment */
        .card-pop {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--card-border);
            border-radius: 0.75rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            overflow: hidden;
        }
        
        .card-pop::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-accent) 0%, var(--brand-accent-2) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .card-pop:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--brand-accent-2);
        }
        
        .card-pop:hover::before {
            opacity: 1;
        }
        
        /* Icon Container Styling */
        .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .card-pop:hover .icon-container {
            transform: scale(1.1) rotate(5deg);
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
    body {
      font-family: 'Inter', sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    
    ::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: linear-gradient(180deg, #94a3b8 0%, #64748b 100%);
      border-radius: 10px;
      transition: background 0.3s ease;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #64748b 0%, #475569 100%);
    }
    
    .modal-backdrop {
      background-color: rgba(0,0,0,0.5);
    }
    
    /* Table Row Enhancements */
    tbody tr {
      transition: all 0.2s ease;
    }
    
    tbody tr:hover {
      background: linear-gradient(90deg, #F8FAFC 0%, #F1F5F9 100%) !important;
      transform: scale(1.001);
    }
    
    tbody td {
      padding: 1rem 1.5rem !important;
      font-size: 0.875rem;
      color: #1F2937;
      font-weight: 500;
      line-height: 1.5;
    }
    /* Custom styles for action buttons */
    .action-btn {
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-weight: 600;
      font-size: 0.8125rem;
      transition: all 0.2s ease;
      cursor: pointer;
      border: none;
      outline: none;
      text-transform: capitalize;
    }
    .btn-approve {
      background-color: #10B981;
      color: white;
    }
    .btn-approve:hover {
      background-color: #059669;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .btn-decline {
      background-color: #EF4444;
      color: white;
    }
    .btn-decline:hover {
      background-color: #DC2626;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
     .btn-update, .btn-details {
      background-color: #3B82F6;
      color: white;
    }
    .btn-update:hover, .btn-details:hover {
      background-color: #2563EB;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }
    
    /* Table row hover effect */
    #proposals-management-table-body tr {
      transition: background-color 0.15s ease;
    }
    #proposals-management-table-body tr:hover {
      background-color: #F9FAFB;
    }
    
    /* Table cell styling */
    #proposals-management-table-body td {
      padding: 1.25rem 1.5rem;
      font-size: 0.8125rem;
      color: #374151;
      font-weight: 500;
      vertical-align: middle;
      line-height: 1.5;
    }
    
    /* Perfect grid alignment for cards */
    .grid {
      display: grid;
      align-items: stretch;
    }
    
    .card-pop {
      display: flex;
      flex-direction: column;
      min-height: 100%;
    }
    
    /* Header improvements */
    #main-content-title {
      letter-spacing: -0.025em;
    }
    
    /* Truncate text with ellipsis */
    .truncate {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .notification-dot {
        height: 10px;
        width: 10px;
        background-color: #EF4444;
        border-radius: 50%;
        display: inline-block;
        position: absolute;
        top: 6px;
        right: 6px;
        border: 2px solid white;
        animation: pulse 1.5s infinite;
    }
    
    /* Stable badge: fixed height, centered content, doesn't shift layout when number changes */
    .stable-badge {
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        background-color: #ef4444;
        border-radius: 9999px;
        border: 2px solid white;
        box-sizing: border-box;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @keyframes pulse {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
    
    /* Status Badge Styling */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.025em;
        text-transform: uppercase;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
        color: #92400E;
        border: 1px solid #FCD34D;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        color: #065F46;
        border: 1px solid #6EE7B7;
    }
    
    .status-declined {
        background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
        color: #991B1B;
        border: 1px solid #FCA5A5;
    }
    
    .status-processing {
        background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
        color: #1E40AF;
        border: 1px solid #93C5FD;
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
    
    .sidebar.collapsed #sidebar-role-title,
    .sidebar.collapsed .text-xs.text-gray-400 {
      display: none;
    }
    
    /* Sidebar Navigation Links */
    .sidebar-link {
      position: relative;
      transition: all 0.2s ease;
      font-weight: 500;
      letter-spacing: 0.01em;
    }
    
    .sidebar-link::before {
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
    
    .sidebar-link:hover::before,
    .sidebar-link.bg-slate-700::before {
      height: 70%;
    }
    
    .sidebar-link i {
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

    /* Clean, formal cards specifically for analytics view */
    .card-clean { 
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%); 
        border: 1px solid var(--card-border); 
        border-radius: 12px; 
        box-shadow: 0 2px 4px rgba(16,24,40,.05), 0 1px 2px rgba(16,24,40,.08); 
        padding: 1.75rem; 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .card-clean::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent 0%, var(--brand-accent-2) 50%, transparent 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .card-clean:hover {
        box-shadow: 0 8px 24px rgba(16,24,40,.12), 0 4px 8px rgba(16,24,40,.1);
        transform: translateY(-4px);
        border-color: var(--brand-accent-2);
    }
    
    .card-clean:hover::after {
        opacity: 1;
    }
    #advanced-dashboard h4 { color: var(--ink-2); letter-spacing: 0.02em; }
    #advanced-dashboard .stat-lg { font-size: 2.75rem; font-weight: 800; color: var(--brand-accent); line-height: 1; }
    #advanced-dashboard .subtle { color: var(--brand-accent-2); font-size: 0.75rem; font-weight: 500; }
    #adv-status-barangay-table th, #adv-status-barangay-table td { 
        border-bottom: 1px solid #EEF2F7; 
        padding: 0.5rem 0.5rem;
    }
    #adv-status-barangay-table thead th { 
        font-weight: 600; 
        color: #6B7280; 
        background: #F9FAFB;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.05em;
    }
    .reveal-up { 
        opacity: 0; 
        transform: translateY(12px); 
        transition: opacity 0.5s cubic-bezier(.25,.6,.3,1), transform 0.5s cubic-bezier(.25,.6,.3,1); 
    }
    .reveal-up.show { opacity: 1; transform: translateY(0); }
    .count-finish { animation: pulseScale .4s ease-out; }
    @keyframes pulseScale { 0%{transform:scale(1);} 50%{transform:scale(1.06);} 100%{transform:scale(1);} }
    
    /* Enhanced Button Styles */
    button, .btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    button:hover, .btn:hover {
        transform: translateY(-1px);
    }
    
    button:active, .btn:active {
        transform: translateY(0);
    }
    
    /* Card Grid Animation Delay */
    .card-pop:nth-child(1) { animation-delay: 0s; }
    .card-pop:nth-child(2) { animation-delay: 0.1s; }
    .card-pop:nth-child(3) { animation-delay: 0.2s; }
    .card-pop:nth-child(4) { animation-delay: 0.3s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    [data-reveal="group"] .card-pop {
        animation: fadeInUp 0.6s ease-out backwards;
    }
    
    /* Professional Form Elements */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select,
    textarea {
        transition: all 0.2s ease;
    }
    
    input:focus,
    select:focus,
    textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }
    
    /* Loading State */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(37, 99, 235, 0.2);
        border-radius: 50%;
        border-top-color: #2563EB;
        animation: spinner 0.8s linear infinite;
    }
    
    @keyframes spinner {
        to { transform: rotate(360deg); }
    }
    
    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: #6B7280;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #E5E7EB;
        margin-bottom: 1rem;
    }
    
    /* Improved Modal Styling */
    #proposalDetailModal .modal-enter,
    #createAccountModal .modal-enter {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 1px solid #E5E7EB;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-pop {
            padding: 1.25rem !important;
        }
        
        .sidebar {
            width: 4.5rem;
        }
        
        .sidebar-text {
            display: none;
        }
    }
    </style>
    <link rel="stylesheet" href="assets/ui-animations.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-slate-50">

    <div class="flex h-screen">
    <aside class="sidebar w-64 text-white flex flex-col relative" aria-hidden="false" data-reveal="sidebar">
      <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fas fa-chevron-left text-gray-600 text-xs"></i>
      </button>
      
      <!-- Header Section -->
      <div class="p-6 text-center border-b border-gray-700/50">
        <img src="assets/image1.png" alt="Mabini Seal" class="logo-formal">
        <h2 id="sidebar-role-title" class="text-lg font-bold sidebar-text tracking-wide">OPMDC Staff</h2>
        <p class="text-[11px] text-gray-400 sidebar-text mt-1 font-medium">Mabini, Batangas</p>
      </div>
      
      <!-- Navigation Section -->
      <nav class="flex-grow px-3 py-6 space-y-1">
        <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3 sidebar-text">Main Menu</p>
        
        <a href="#" id="dashboard-link" class="sidebar-link flex items-center px-4 py-3 text-gray-100 bg-slate-700 rounded-lg" title="Dashboard">
          <i class="fas fa-th-large mr-3"></i>
          <span class="sidebar-text">Dashboard</span>
        </a>
        
        <a href="#proposals" id="proposals-link" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="Proposals">
          <i class="fas fa-file-invoice mr-3"></i>
          <span class="sidebar-text">Proposals</span>
          <span id="proposalsBadge" class="ml-auto stable-badge hidden sidebar-text">0</span>
        </a>
        
        <a href="#analytics" id="analytics-link" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="Analytics">
          <i class="fas fa-chart-bar mr-3"></i>
          <span class="sidebar-text">Analytics</span>
        </a>
        
        <div class="my-3 border-t border-gray-700/50 sidebar-text"></div>
        <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3 sidebar-text">Management</p>
        
        <a href="#accounts" id="accounts-link" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="Accounts">
          <i class="fas fa-user-shield mr-3"></i>
          <span class="sidebar-text">Accounts</span>
        </a>
        
        <a href="#history" id="history-link" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:bg-slate-700 hover:text-white rounded-lg" title="History">
          <i class="fas fa-clock-rotate-left mr-3"></i>
          <span class="sidebar-text">History</span>
        </a>
      </nav>
      
      <!-- Footer Section -->
      <div class="p-4 border-t border-gray-700/50">
        <a href="login.html" class="flex items-center w-full px-4 py-3 text-gray-300 hover:bg-rose-600 hover:text-white rounded-lg transition-all" title="Logout">
          <i class="fas fa-right-from-bracket mr-3"></i>
          <span class="sidebar-text">Logout</span>
        </a>
      </div>
    </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-5" data-reveal="header" style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
        <div class="flex items-center justify-between">
          <div>
            <h1 id="main-content-title" class="text-2xl font-bold text-gray-900 tracking-tight mb-1">Dashboard</h1>
            <div class="flex items-center gap-2">
              <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
              <p id="current-date" class="text-[11px] text-gray-600 font-semibold"></p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
                        <!-- Notification bell -->
                        <div class="relative">
                            <button id="staffNotifBell" title="Notifications" class="relative p-2.5 rounded-xl hover:bg-gray-100 focus:outline-none transition-all">
                                <i class="fas fa-bell text-gray-600 text-lg"></i>
                                <span id="staffNotifBadge" class="notification-dot hidden"></span>
                            </button>
                            <div id="staffNotifDropdown" class="hidden absolute right-0 mt-2 bg-white shadow-xl rounded-xl z-50 w-80 border border-gray-200">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                  <strong class="text-sm font-bold text-gray-900">Notifications</strong>
                                  <button id="staffMarkAllRead" class="text-[11px] text-blue-600 font-semibold hover:text-blue-700">Mark all read</button>
                                </div>
                                <div id="staffNotifList" class="max-h-64 overflow-auto p-2"><div class="text-center text-gray-500 py-8 text-sm">Loading…</div></div>
                            </div>
                        </div>

                        <!-- User Profile -->
                        <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                            <div class="relative">
                              <img class="w-11 h-11 rounded-xl object-cover border-2 border-blue-100" src="https://placehold.co/100x100/DBEAFE/2563EB?text=Staff" alt="User Avatar">
                              <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div>
                                <p id="header-user-name" class="text-sm font-bold text-gray-900">OPMDC Staff</p>
                                <p id="header-user-role" class="text-[11px] text-gray-500 font-medium">Staff Member</p>
                            </div>
                        </div>
          </div>
        </div>
      </header>
      
      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6" style="background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);">
        <div id="dashboard-view" class="view">
          <div class="container mx-auto max-w-7xl">
            <div class="mb-6">
              <h3 class="text-base font-bold text-gray-700 mb-1 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1 h-4 bg-blue-600 rounded-full"></span>
                Overview
              </h3>
              <p class="text-sm text-gray-600 ml-5 font-medium">Quick insights and metrics at a glance</p>
            </div>
            <!-- Advanced analytics moved to Analytics view -->
                            <!-- phase filter removed as requested -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" data-reveal="group">
                                <!-- Proposals Card -->
                                <div class="card-pop flex flex-col p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total</p>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Proposals</h3>
                                        </div>
                                        <div class="icon-container" style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);">
                                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <span id="total-requests-stat" class="text-4xl font-bold text-gray-900 tracking-tight">0</span>
                                        <p class="text-xs text-gray-600 font-semibold mt-2">All submitted proposals</p>
                                    </div>
                                </div>

                                <!-- Pending Card -->
                                <div class="card-pop flex flex-col p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Pending</h3>
                                        </div>
                                        <div class="icon-container" style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);">
                                            <i class="fas fa-clock text-amber-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <span id="pending-requests-stat" class="text-4xl font-bold text-gray-900 tracking-tight">0</span>
                                        <p class="text-xs text-gray-600 font-semibold mt-2">Awaiting review action</p>
                                    </div>
                                </div>

                                <!-- Accounts Card -->
                                <div class="card-pop flex flex-col p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total</p>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Accounts</h3>
                                        </div>
                                        <div class="icon-container" style="background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);">
                                            <i class="fas fa-users text-emerald-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <span id="total-accounts-stat" class="text-4xl font-bold text-gray-900 tracking-tight">0</span>
                                        <p class="text-xs text-gray-600 font-semibold mt-2">Registered barangays</p>
                                    </div>
                                </div>

                                <!-- For Approval Card -->
                                <div class="card-pop flex flex-col p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pending</p>
                                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Approval</h3>
                                        </div>
                                        <div class="icon-container" style="background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);">
                                            <i class="fas fa-user-check text-rose-600 text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <span id="pending-accounts-stat" class="text-4xl font-bold text-gray-900 tracking-tight">0</span>
                                        <p class="text-xs text-gray-600 font-semibold mt-2">New account signups</p>
                                    </div>
                                </div>
                            </div>

            <div class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
              <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-base font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                      <i class="fas fa-list-ul text-blue-600"></i>
                      Recent Submissions
                    </h3>
                    <p class="text-xs text-gray-600 mt-1 font-medium">Latest project proposals from barangays</p>
                  </div>
                  <div class="text-xs text-gray-600 font-semibold">
                    <i class="fas fa-sync-alt mr-1"></i>
                    <span>Auto-refresh</span>
                  </div>
                </div>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 font-bold tracking-wider">
                    <tr>
                      <th scope="col" class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-hashtag text-gray-500 text-[10px]"></i>
                          Proposal ID
                        </div>
                      </th>
                      <th scope="col" class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-map-marker-alt text-gray-500 text-[10px]"></i>
                          Barangay
                        </div>
                      </th>
                      <th scope="col" class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-tag text-gray-500 text-[10px]"></i>
                          Type
                        </div>
                      </th>
                      <th scope="col" class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-calendar text-gray-500 text-[10px]"></i>
                          Date Submitted
                        </div>
                      </th>
                      <th scope="col" class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-info-circle text-gray-500 text-[10px]"></i>
                          Status
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="requests-table-body" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div id="proposals-view" class="view hidden">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Proposals Management</h2>
                    <div class="flex items-center gap-2">
                      <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                      <p id="current-date-proposals" class="text-[11px] text-gray-600 font-semibold">Monitor and manage project proposals</p>
                    </div>
                </div>
                
                <!-- Filters removed: Submission / Sent by / Status controls intentionally removed to simplify the Proposals view -->
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-folder-open text-blue-600"></i>
                          <h3 class="text-base font-bold text-gray-800 uppercase tracking-wide">All Proposals</h3>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 ml-6 font-medium">Complete list of submitted project proposals</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b-2 border-gray-200">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[30%]">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-file-alt text-gray-400 text-[9px]"></i>
                                            Project Title
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[13%]">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-gray-400 text-[9px]"></i>
                                            Barangay
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[13%]">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar-plus text-gray-400 text-[9px]"></i>
                                            Submitted
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[13%]">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-gray-400 text-[9px]"></i>
                                            Updated
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[16%]">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-info-circle text-gray-400 text-[9px]"></i>
                                            Status
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-[10px] font-bold text-gray-600 uppercase tracking-wider w-[15%]">
                                        <div class="flex items-center justify-center gap-2">
                                            <i class="fas fa-cog text-gray-400 text-[9px]"></i>
                                            Action
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="proposals-management-table-body" class="divide-y divide-gray-100 bg-white"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="analytics-view" class="view hidden">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Analytics & Insights</h2>
                    <p class="text-sm text-gray-600 mb-3">Comprehensive analytics dashboard with real-time data</p>
                    <div class="flex items-center gap-4 text-xs">
                        <span class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-lg">
                            <i class="fas fa-clock text-blue-600"></i>
                            <span class="text-gray-700">Last Updated: <span id="analytics-last-updated" class="font-bold text-blue-600">--</span></span>
                        </span>
                        <span class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg">
                            <i class="fas fa-database text-emerald-600"></i>
                            <span class="text-gray-700">Range: <span id="analytics-data-range" class="font-bold text-emerald-600">All Time</span></span>
                        </span>
                    </div>
                </div>
                <section id="advanced-dashboard" aria-label="Advanced Analytics" class="mb-8" style="font-family: 'Inter', sans-serif;">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Left Info Panel -->
                        <div class="lg:col-span-1">
                            <div class="card-clean h-full flex flex-col">
                                <h2 class="text-xl leading-snug font-bold text-slate-800">Actionable<br>Solution</h2>
                                <p class="mt-5 text-xs text-slate-500">Show Dashboard for the month<br><span id="adv-month-label" class="font-semibold text-slate-700"></span></p>
                                <p class="mt-3 text-xs text-slate-500">Department Referral<br><span class="font-semibold text-slate-700">All</span></p>
                                <div class="mt-auto pt-6 text-[10px] text-slate-400 space-y-0.5">
                                    <p class="uppercase tracking-wide font-semibold">DATA SOURCE:</p>
                                    <p>#OPMDC Proposals</p>
                                    <p>Processed by: System Analytics Engine</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Grid Panel -->
                        <div class="lg:col-span-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Proposals Current Month -->
                                <div class="card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Proposals current month</h4>
                                    <div class="stat-lg" id="adv-requests-this-month">0</div>
                                    <div class="mt-2 subtle" id="adv-requests-change">No change</div>
                                </div>
                                
                                <!-- Approval Rate -->
                                <div class="card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Approval Rate (proposals)</h4>
                                    <div class="text-4xl font-extrabold text-indigo-600" id="adv-approval-rate">0%</div>
                                    <div class="mt-2 text-xs text-indigo-600" id="adv-approval-rate-note"></div>
                                    <div class="mt-3 text-[10px] text-slate-400">Approved / Total proposals (last 6 months)</div>
                                </div>
                                
                                <!-- Age Distribution -->
                                <div class="card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Proposal age distribution</h4>
                                    <canvas id="adv-age-chart" height="130"></canvas>
                                </div>
                                
                                <!-- Status vs Barangays -->
                                <div class="md:col-span-2 card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Status vs Top Barangays</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-[11px]" id="adv-status-barangay-table">
                                            <thead class="text-slate-500">
                                                <tr id="adv-status-barangay-head"><th class="px-2 py-2 text-left">Status</th></tr>
                                            </thead>
                                            <tbody id="adv-status-barangay-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Status Distribution -->
                                <div class="card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Proposal status distribution</h4>
                                    <canvas id="adv-proposal-status-chart" height="150"></canvas>
                                </div>
                                
                                <!-- Weekday Intake -->
                                <div class="md:col-span-2 lg:col-span-1 card-clean reveal-up">
                                    <h4 class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-4">Proposal intake (weekday)</h4>
                                    <canvas id="adv-weekday-chart" height="150"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Announcements removed -->
        
        <div id="accounts-view" class="view hidden">
            <div class="container mx-auto max-w-7xl">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Accounts Management</h2>
                        <div class="flex items-center gap-2">
                          <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                          <p class="text-[11px] text-gray-600 font-semibold">Wednesday, November 26, 2025 at 11:35 PM</p>
                        </div>
                    </div>
                    <button id="createAccountBtn" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-bold py-3 px-6 rounded-xl flex items-center shadow-lg transition-all">
                        <i class="fas fa-user-plus mr-2"></i> Create Account
                    </button>
                </div>
                <div id="accounts-table-container">
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-white">
                            <div class="flex items-center gap-2">
                              <i class="fas fa-users-cog text-emerald-600"></i>
                              <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Barangay Account Management</h3>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 ml-6">Create and manage accounts for each barangay</p>
                        </div>
                        <div class="overflow-x-auto" id="accounts-table-wrapper">
                            <table id="accounts-table" class="w-full text-sm">
                                <thead class="text-[10px] text-gray-600 uppercase bg-gray-50 border-b-2 border-gray-200 font-bold tracking-wider">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-map-marker-alt text-gray-400 text-[9px]"></i>
                                                Barangay
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-user text-gray-400 text-[9px]"></i>
                                                Representative
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-envelope text-gray-400 text-[9px]"></i>
                                                Email
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-toggle-on text-gray-400 text-[9px]"></i>
                                                Status
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <i class="fas fa-cog text-gray-400 text-[9px]"></i>
                                                Action
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="accounts-table-body" class="divide-y divide-gray-100 bg-white"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="history-view" class="view hidden">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Activity History</h2>
                    <div class="flex items-center gap-2">
                      <i class="fas fa-calendar-day text-blue-600 text-xs"></i>
                      <p class="text-[11px] text-gray-600 font-semibold">Wednesday, November 26, 2025 at 11:41 PM</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-white">
                        <div class="flex items-center gap-2">
                          <i class="fas fa-history text-purple-600"></i>
                          <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Activity Reports</h3>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1 ml-6">Historical records of all proposal activities</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[10px] text-gray-600 uppercase bg-gray-50 border-b-2 border-gray-200 font-bold tracking-wider">
                                <tr>
                                    <th scope="col" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-hashtag text-gray-400 text-[9px]"></i>
                                            Proposal ID
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-gray-400 text-[9px]"></i>
                                            Barangay
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-tag text-gray-400 text-[9px]"></i>
                                            Type
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar text-gray-400 text-[9px]"></i>
                                            Date Submitted
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-info-circle text-gray-400 text-[9px]"></i>
                                            Status
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body" class="divide-y divide-gray-100 bg-white">
                                <!-- History records will be loaded here -->
                            </tbody>
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
        <div class="bg-white rounded-xl shadow-2xl m-auto p-8 w-full max-w-5xl z-10 max-h-[92vh] flex flex-col modal-enter">
            <div class="flex justify-between items-center mb-5 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-extrabold text-gray-900">Proposal Details</h2>
                <button id="closeDetailModalBtn" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">&times;</button>
            </div>

                            <!-- staged reveal JS moved to assets/ui-animations.js -->
                            <script src="assets/ui-animations.js"></script>
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
                        <button type="button" id="cancelUpdateBtn" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <div id="createAccountModal" class="fixed inset-0 z-50 items-center justify-center hidden transition-all duration-300 opacity-0">
        <div class="modal-backdrop fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-white rounded-xl shadow-2xl m-auto p-6 w-full max-w-md z-10 modal-enter">
            <div class="flex justify-between items-center mb-5 border-b border-gray-200 pb-4">
                <h2 class="text-xl font-bold text-gray-900">Create Barangay Account</h2>
                <button id="closeAccountModalBtn" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">&times;</button>
            </div>
            <form id="createBarangayAccountForm">
                <div class="mb-4">
                    <label for="barangayName" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">Barangay</label>
                    <select id="barangayName" name="barangayName" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
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
                    <label for="representative" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">Representative Name</label>
                    <input type="text" id="representative" name="representative" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">Email</label>
                    <input type="email" id="email" name="email" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                 <div class="mb-5">
                    <label for="password" class="block text-gray-600 text-xs font-semibold mb-2 uppercase tracking-wide">Password</label>
                    <input type="password" id="password" name="password" class="w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                </div>
                <div class="flex items-center justify-end mt-5 gap-3">
                    <button type="button" id="cancelCreateAccountBtn" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
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
    // Load last known dashboard counts immediately to avoid flicker on refresh
    try {
        const cached = localStorage.getItem('opmdcDashboardCounts');
        if (cached) {
            const c = JSON.parse(cached);
            if (c && typeof setDashboardCounts === 'function') setDashboardCounts(c);
        }
    } catch (e) { /* ignore */ }
    // Load real data from server
    fetchServerData();
    // Kick an early counts refresh so cards populate asap
    try { if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts(); } catch (e) {}
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
    document.getElementById('cancelCreateAccountBtn').addEventListener('click', () => closeModal('createAccountModal'));

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

    fetch('api/staff_create_account.php', {
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
                if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
            } else {
                if (typeof renderAccountsTable === 'function') renderAccountsTable();
                if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
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
    // Build advanced analytics only when analytics view is active
    try {
        if (viewToShow && viewToShow.id === 'analytics-view') {
            // Fetch analytics data first, which will then call buildAdvancedDashboard
            fetchAnalytics();
        }
    } catch (e) { console.warn('advanced analytics build error', e); }
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
    renderHistoryTable();
}

function renderDashboardUI() {
    // If we already have authoritative counts, use them to avoid zero flicker on refresh
    if (window._dashboardCounts && typeof setDashboardCounts === 'function') {
        setDashboardCounts(window._dashboardCounts);
    }
    // Prefer server-provided data (populated by fetchServerData()); do NOT fall back to localStorage.
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];
    const serverRequests = (window._serverRequests && Array.isArray(window._serverRequests)) ? window._serverRequests : null;
    const localRequests = [];
    const accounts = (window._serverAccounts && Array.isArray(window._serverAccounts)) ? window._serverAccounts : [];

    // Proposals only (request submission removed)
    let totalRequestsCount = proposals.length;
    let pendingCount = proposals.filter(r => /For Review|Processing/i.test(r.status || '')).length;

    // Update computed counts only if we don't have authoritative ones yet
    if (!window._dashboardCounts) {
        document.getElementById('total-requests-stat').textContent = totalRequestsCount;
        document.getElementById('pending-requests-stat').textContent = pendingCount;
        document.getElementById('total-accounts-stat').textContent = accounts.length;
        document.getElementById('pending-accounts-stat').textContent = accounts.filter(a => a.status === 'pending').length;
    }
    // Advanced analytics removed from dashboard view.
}

// Small HTML escaper available globally (used by multiple renderers)
function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function(c){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
    });
}

async function renderAccountsTable() {
    const wrapper = document.getElementById('accounts-table-wrapper');
    if (!wrapper) return;
    try {
        console.log('[DEBUG] Fetching from:', new URL('api/list_barangay_accounts.php', window.location.href).href);
        const res = await fetch('api/list_barangay_accounts.php', { credentials: 'same-origin' });
        console.log('[DEBUG] Response status:', res.status, 'OK:', res.ok);
        console.log('[DEBUG] Response headers:', res.headers.get('content-type'));
        if (!res.ok) {
            const errorText = await res.text();
            console.error('[DEBUG] Error response:', errorText);
            throw new Error('Network response was not ok: ' + res.status);
        }
        const rawText = await res.text();
        console.log('[DEBUG] Raw response:', rawText.substring(0, 200));
        const data = JSON.parse(rawText);
        console.log('[DEBUG] Parsed data:', data);
        const accounts = (data && data.success && Array.isArray(data.accounts)) ? data.accounts : [];

        // Cache accounts for dashboard stats and update immediately
        try {
            window._serverAccounts = accounts;
            const totalEl = document.getElementById('total-accounts-stat');
            const pendingEl = document.getElementById('pending-accounts-stat');
            if (totalEl) totalEl.textContent = accounts.length;
            if (pendingEl) pendingEl.textContent = accounts.filter(a => String(a.status || '').toLowerCase() === 'pending').length;
        } catch (e) { /* noop */ }

        if (!accounts || accounts.length === 0) {
            // Replace table with a friendly placeholder to avoid header-only visual glitch
            wrapper.innerHTML = `<div class="text-center py-12 text-gray-500">No barangay accounts found. Click <strong>Create Account</strong> to add one.</div>`;
            return;
        }

        // Rebuild table (ensures clean DOM and avoids leftover event handlers)
        wrapper.innerHTML = `
            <table id="accounts-table" class="w-full text-sm">
                <thead class="text-xs text-gray-600 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">BARANGAY</th>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">REPRESENTATIVE</th>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">EMAIL</th>
                        <th scope="col" class="px-6 py-3.5 text-left font-semibold">STATUS</th>
                        <th scope="col" class="px-6 py-3.5 text-center font-semibold">ACTION</th>
                    </tr>
                </thead>
                <tbody id="accounts-table-body" class="divide-y divide-gray-100"></tbody>
            </table>
        `;

        const tableBody = document.getElementById('accounts-table-body');
        if (!tableBody) return;
        tableBody.innerHTML = '';

        accounts.forEach((acc, index) => {
            const emailKey = acc.email || acc.username || '';
            const be = encodeURIComponent(acc.barangayName || '');
            const re = encodeURIComponent(acc.representative || '');
            const ee = encodeURIComponent(emailKey);
            const row = `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-900 font-medium">${escapeHtml(acc.barangayName)}</td>
                    <td class="px-6 py-4 text-gray-700">${escapeHtml(acc.representative)}</td>
                    <td class="px-6 py-4 text-gray-600">${escapeHtml(emailKey)}</td>
                    <td class="px-6 py-4">${getStatusBadge(acc.status)}</td>
                    <td class="px-6 py-4 text-center">
                        <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors mr-2 edit-account-btn" data-id="${acc.id}" data-barangay="${be}" data-rep="${re}" data-email="${ee}">Edit</button>
                        <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors delete-account-btn" data-id="${acc.id}">Delete</button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });

        // Attach handlers safely
        setTimeout(() => {
            try {
                tableBody.querySelectorAll('.edit-account-btn').forEach(btn => btn.addEventListener('click', _editAccountClickHandler));
                tableBody.querySelectorAll('.delete-account-btn').forEach(btn => btn.addEventListener('click', _deleteAccountClickHandler));
            } catch (e) { console.warn('binding account buttons failed', e); }
        }, 0);
    } catch (err) {
        console.warn('Could not load barangay accounts from server.', err);
        wrapper.innerHTML = `<div class="text-center py-12 text-red-500">Failed to load accounts. Please try again later.</div>`;
    }
}


function renderRequestsTable() {
    // This table shows a summary of recent proposals only (request submission removed).
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];

    let requests = proposals;
    const tableBody = document.getElementById('requests-table-body');
    if (!tableBody) return;
    tableBody.innerHTML = '';

    if (requests.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No requests found.</td></tr>`;
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

async function renderHistoryTable() {
    const tableBody = document.getElementById('history-table-body');
    if (!tableBody) return;
    
    try {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">Loading history...</td></tr>';
        
        const response = await fetch('api/staff_history.php', { 
            credentials: 'same-origin' 
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error:', response.status, errorText);
            throw new Error(`Server returned ${response.status}: ${errorText.substring(0, 100)}`);
        }
        
        const data = await response.json();
        console.log('History data received:', data);
        
        if (!data.success) {
            const errorMsg = data.message || 'Unknown error';
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">${escapeHtml(errorMsg)}</td></tr>`;
            return;
        }
        
        if (!data.history || data.history.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No history records found.</td></tr>';
            return;
        }
        
        tableBody.innerHTML = '';
        
        data.history.forEach((record, index) => {
            const dateStr = new Date(record.created_at).toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const statusBadge = getStatusBadge(record.status);
            
            const remarksText = record.remarks ? `<div class="text-xs text-gray-500 mt-1">${escapeHtml(record.remarks)}</div>` : '';
            
            const row = `
                <tr class="border-b ${index % 2 === 1 ? 'bg-slate-50' : 'bg-white'}">
                    <td class="px-6 py-4">#${record.proposal_id}</td>
                    <td class="px-6 py-4">
                        ${escapeHtml(record.barangay)}
                        <div class="text-xs text-gray-500 mt-1">by ${escapeHtml(record.staff_name)}</div>
                    </td>
                    <td class="px-6 py-4">
                        ${escapeHtml(record.project_title)}
                        <div class="text-xs text-gray-400 mt-1">${escapeHtml(record.project_type)}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">${dateStr}</td>
                    <td class="px-6 py-4">
                        ${statusBadge}
                        ${remarksText}
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
        
    } catch (error) {
        console.error('Error loading history:', error);
        tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Failed to load history. Please try again.<br><small class="text-xs">${escapeHtml(error.message)}</small></td></tr>`;
    }
}

function renderProposalsPage() {
    const proposalsManagementTableBody = document.getElementById('proposals-management-table-body');
    if (!proposalsManagementTableBody) return;
    // prefer server-provided proposals when available
    // If the client hasn't fetched server proposals yet, fetch directly and render to avoid a stuck loader.
    if (!window._serverProposals) {
        proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-400 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</td></tr>`;
        fetch('api/list_staff_proposals.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(d => {
                window._serverProposals = d.proposals || (Array.isArray(d) ? d : []);
                renderProposalsPage();
            })
            .catch(err => {
                console.warn('Could not load proposals', err);
                proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-500">Failed to load proposals.</td></tr>`;
            });
        return;
    }
    let proposals = (Array.isArray(window._serverProposals)) ? window._serverProposals.slice() : [];
    
    // Filters have been removed from the UI; show all server proposals without additional client-side filtering.


    proposalsManagementTableBody.innerHTML = '';
    if (proposals.length === 0) {
      proposalsManagementTableBody.innerHTML = `<tr><td colspan="6" class="text-center py-12 text-gray-400 text-sm font-medium">No proposals found</td></tr>`;
      return;
    }
    proposals.forEach((prop, index) => {
        const statusBadge = getStatusBadge(prop.status);
    const actionButton = `<button class="action-btn btn-details" onclick="openProposalDetails('${prop.id}')">View Details</button>`;
    const row = `<tr>
        <td class="px-6 py-4 font-semibold text-gray-900 truncate" style="max-width: 280px;" title="${prop.title}">${prop.title}</td>
        <td class="px-6 py-4 text-gray-700">${prop.barangay}</td>
        <td class="px-6 py-4 text-gray-600 text-sm">${prop.date}</td>
        <td class="px-6 py-4 text-gray-600 text-sm">${prop.lastUpdated}</td>
        <td class="px-6 py-4">${statusBadge}</td>
        <td class="px-6 py-4 text-center">${actionButton}</td>
    </tr>`;
        proposalsManagementTableBody.innerHTML += row;
    });
}


function getStatusBadge(status) {
    const colors = {
        'Approved': 'bg-green-50 text-green-700 border-green-200',
        'approved': 'bg-green-50 text-green-700 border-green-200',
        'Declined': 'bg-red-50 text-red-700 border-red-200',
        'declined': 'bg-red-50 text-red-700 border-red-200',
        'For Review': 'bg-blue-50 text-blue-700 border-blue-200',
        'Processing': 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'For Head Approval': 'bg-amber-50 text-amber-700 border-amber-200',
        'For Approval': 'bg-amber-50 text-amber-700 border-amber-200',
        'pending': 'bg-amber-50 text-amber-700 border-amber-200',
        'Requires Revision': 'bg-pink-50 text-pink-700 border-pink-200',
    };
    const colorClass = colors[status] || 'bg-gray-50 text-gray-700 border-gray-200';
    return `<span class="${colorClass} text-xs font-semibold px-3 py-1.5 rounded-lg border inline-block">${status}</span>`;
}

function updateTime() {
    const dateElement = document.getElementById('current-date');
    const dateElementProposals = document.getElementById('current-date-proposals');
    const now = new Date();
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: true };
    const formattedDate = now.toLocaleDateString('en-US', options);
    const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
    const displayText = `${formattedDate} · ${formattedTime}`;
    if (dateElement) dateElement.textContent = displayText;
    if (dateElementProposals) dateElementProposals.textContent = displayText;
}

// Fetch server-side data and cache it for rendering; fall back to localStorage if server unavailable.
async function fetchServerData() {
    // Request submission removed - proposals only
    window._serverRequests = null;

    // fetch staff proposals (if available)
    try {
        const res2 = await fetch('api/list_staff_proposals.php', { credentials: 'same-origin' });
        if (res2.ok) {
            const d2 = await res2.json();
            // expect { proposals: [...] } or { success: true, proposals: [...] }
            window._serverProposals = d2.proposals || d2 || [];
        }
    } catch (e) {
        console.warn('list_staff_proposals.php not available', e);
        window._serverProposals = null;
    }

    // fetch barangay accounts for dashboard counters
    try {
        const res3 = await fetch('api/list_barangay_accounts.php', { credentials: 'same-origin' });
        if (res3.ok) {
            const d3 = await res3.json();
            window._serverAccounts = (d3 && Array.isArray(d3.accounts)) ? d3.accounts : [];
        }
    } catch (e) {
        console.warn('list_barangay_accounts.php not available', e);
        window._serverAccounts = window._serverAccounts || [];
    }

    // re-render views now that server data may be available
    if (typeof renderAllViews === 'function') renderAllViews();

    // Also fetch dashboard counts (server-side authoritative)
    if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();

    // refresh periodically
    setTimeout(fetchServerData, 30000);
}

// Fetch aggregate counts from server endpoint and update the dashboard cards.
async function fetchDashboardCounts(phase = 'all') {
    try {
        const url = 'api/dashboard_counts.php?phase=' + encodeURIComponent(phase);
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Network');
        const d = await res.json();
        if (d) {
            // Cache in-memory + localStorage and update UI
            window._dashboardCounts = {
                total_requests: d.total_requests,
                pending_requests: d.pending_requests,
                total_accounts: d.total_accounts,
                pending_accounts: d.pending_accounts,
            };
            try { localStorage.setItem('opmdcDashboardCounts', JSON.stringify(window._dashboardCounts)); } catch(e){}
            if (typeof setDashboardCounts === 'function') setDashboardCounts(window._dashboardCounts);
            return;
        }
    } catch (err) {
        // Silent fallback: if endpoint not available, the client-side rendering will still run
        console.warn('Could not fetch dashboard counts', err);
    }
}

/* Advanced Dashboard (reference-style UI) */
let advAgeChart = null;
let advProposalStatusChart = null;
let advWeekdayChart = null;
let _cachedAnalyticsData = null;

function buildAdvancedDashboard(analyticsData = null){
    // Use cached analytics data or fetch from API
    const data = analyticsData || _cachedAnalyticsData;
    if (!data) {
        console.warn('No analytics data available for advanced dashboard');
        return;
    }
    
    // Month label
    const monthEl = document.getElementById('adv-month-label');
    if (monthEl) {
        monthEl.textContent = new Date().toLocaleDateString('en-US', { month:'long', year:'numeric' });
    }
    
    const proposals = (window._serverProposals && Array.isArray(window._serverProposals)) ? window._serverProposals : [];
    // Proposals current month + change - use analytics data
    const now = new Date();
    const ymNow = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0');
    const prevYmDate = new Date(now.getFullYear(), now.getMonth()-1, 1);
    const ymPrev = prevYmDate.getFullYear() + '-' + String(prevYmDate.getMonth()+1).padStart(2,'0');
    
    // Get current and previous month counts from analytics timeseries data
    let curMonthCount = 0, prevMonthCount = 0;
    if (data.proposals_timeseries && data.proposals_timeseries.labels && data.proposals_timeseries.data) {
        const labels = data.proposals_timeseries.labels;
        const values = data.proposals_timeseries.data;
        const curIdx = labels.indexOf(ymNow);
        const prevIdx = labels.indexOf(ymPrev);
        if (curIdx >= 0) curMonthCount = values[curIdx] || 0;
        if (prevIdx >= 0) prevMonthCount = values[prevIdx] || 0;
    }
    
    const curEl = document.getElementById('adv-requests-this-month');
    const changeEl = document.getElementById('adv-requests-change');
    if (curEl) animateMetric('#adv-requests-this-month', curMonthCount);
    if (changeEl){
        if (prevMonthCount === 0){ changeEl.textContent = curMonthCount>0? '↑ vs 0 last month' : 'No change'; }
        else { const diff = curMonthCount - prevMonthCount; const pct = Math.round((diff/prevMonthCount)*100); changeEl.textContent = (diff>=0? '↑':'↓') + ' ' + Math.abs(diff) + ' ('+pct+'%) vs '+prevMonthCount; }
    }
    // Crosstab: statuses vs top barangays (top 6 by proposal volume) - use analytics data
    const topBarangays = (data.proposals_by_barangay || []).slice(0,6).map(b => b.barangay);
    const statusesObj = data.proposals_by_status || {};
    const statuses = Object.keys(statusesObj).slice(0,6);
    const headRow = document.getElementById('adv-status-barangay-head');
    const bodyTable = document.getElementById('adv-status-barangay-body');
    if (headRow && bodyTable && topBarangays.length > 0){
        headRow.innerHTML = '<th class="px-2 py-1 text-left">Status</th>' + topBarangays.map(b=>'<th class="px-2 py-1 text-center text-[10px] font-medium">'+b+'</th>').join('');
        bodyTable.innerHTML='';
        statuses.forEach(st=>{
            const rowVals = topBarangays.map(b=>{
                // Count proposals from server data filtered by barangay and status
                const c = proposals.filter(p=>p.barangay===b && p.status===st).length;
                return '<td class="px-2 py-1 text-center bg-indigo-50/40">'+(c||'0')+'</td>';
            });
            bodyTable.innerHTML += '<tr><td class="px-2 py-1 font-medium text-slate-600">'+st+'</td>'+rowVals.join('')+'</tr>';
        });
    }
    // Age distribution buckets (0-9,10-19,...70+ days) - use proposals data
    const ageBuckets = new Array(8).fill(0); // indices 0..7
    const today = Date.now();
    proposals.forEach(p=>{ const d = new Date(p.created_at).getTime(); const days = Math.floor((today - d)/(1000*60*60*24)); let idx = Math.min(Math.floor(days/10),7); ageBuckets[idx]++; });
    const ageCtx = document.getElementById('adv-age-chart');
    if (ageCtx){
        const labels = ['0','10','20','30','40','50','60','70+'];
        if (advAgeChart){ advAgeChart.data.labels = labels; advAgeChart.data.datasets[0].data = ageBuckets; advAgeChart.update(); }
        else { advAgeChart = new Chart(ageCtx, { type:'bar', data:{ labels, datasets:[{ data:ageBuckets, backgroundColor:'#6366F1' }] }, options:{ plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true } } } }); }
    }
    // Approval rate - use analytics data
    const statusCounts = data.proposals_by_status || {};
    const approved = statusCounts['Approved']||0;
    const totalProposals = Object.values(statusCounts).reduce((sum, val) => sum + val, 0);
    const rate = totalProposals? Math.round((approved/totalProposals)*1000)/10 : 0;
    const arEl = document.getElementById('adv-approval-rate'); if (arEl) { arEl.textContent='0%'; animateMetric('#adv-approval-rate', rate, true); }
    const noteEl = document.getElementById('adv-approval-rate-note'); if (noteEl){ noteEl.textContent = totalProposals? approved+' of '+totalProposals+' proposals' : 'No proposals'; }
    // Proposal status distribution chart
    const psCtx = document.getElementById('adv-proposal-status-chart');
    if (psCtx){
        const labels = Object.keys(statusCounts);
        const data = labels.map(l=>statusCounts[l]);
        const palette = ['#4F46E5','#6366F1','#818CF8','#A5B4FC','#4338CA','#3730A3','#312E81'];
        const colors = labels.map((_,i)=>palette[i%palette.length]);
        if (advProposalStatusChart){ advProposalStatusChart.data.labels = labels; advProposalStatusChart.data.datasets[0].data = data; advProposalStatusChart.update(); }
        else { advProposalStatusChart = new Chart(psCtx,{ type:'bar', data:{ labels, datasets:[{ data, backgroundColor:colors }] }, options:{ plugins:{legend:{display:false}}, scales:{ y:{ beginAtZero:true } } } }); }
    }
    // Weekday intake - use proposals data
    const weekdayCounts = new Array(7).fill(0);
    proposals.forEach(p=>{ const d = new Date(p.created_at); weekdayCounts[d.getDay()]++; });
    const wdCtx = document.getElementById('adv-weekday-chart');
    if (wdCtx){
        const labels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        if (advWeekdayChart){ advWeekdayChart.data.labels = labels; advWeekdayChart.data.datasets[0].data = weekdayCounts; advWeekdayChart.update(); }
        else { advWeekdayChart = new Chart(wdCtx,{ type:'bar', data:{ labels, datasets:[{ data:weekdayCounts, backgroundColor:'#93C5FD' }] }, options:{ indexAxis:'y', plugins:{legend:{display:false}}, scales:{ x:{ beginAtZero:true } } } }); }
    }
}

// Helper: set counts to cards in one place
function setDashboardCounts(counts) {
    try {
        animateMetric('#total-requests-stat', counts.total_requests);
        animateMetric('#pending-requests-stat', counts.pending_requests);
        animateMetric('#total-accounts-stat', counts.total_accounts);
        animateMetric('#pending-accounts-stat', counts.pending_accounts);
    } catch (e) { /* ignore */ }
}

// Count-up animation utility
function animateMetric(selector, value, isPercent=false){
    const el = document.querySelector(selector); if(!el) return; const target = parseFloat(value)||0; const duration = 800; const start = performance.now(); const initial = 0;
    const format = v => isPercent? Math.round(v)+'%' : Math.round(v);
    function step(ts){ const p = Math.min(1,(ts-start)/duration); const eased = p<.5? 2*p*p : -1+(4-2*p)*p; const current = initial + (target-initial)*eased; el.textContent = format(current); if(p<1){ requestAnimationFrame(step); } else { el.textContent = format(target); el.classList.add('count-finish'); setTimeout(()=> el.classList.remove('count-finish'),450); } }
    requestAnimationFrame(step);
}

// Reveal animation observer
const _revealObserver = new IntersectionObserver(entries=>{ entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('show'); _revealObserver.unobserve(e.target); } }); }, { threshold:0.12 });
document.addEventListener('DOMContentLoaded', ()=>{ document.querySelectorAll('.reveal-up').forEach(el=> _revealObserver.observe(el)); });

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
        const res = await fetch('api/update_account_status.php', { method: 'POST', body: new URLSearchParams({ user_id: userId, status: newStatus }), credentials: 'same-origin' });
        const d = await res.json();
        if (d && d.success) {
            renderAllViews();
            showAlert('Success', 'Account status updated.');
            if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
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
            const res = await fetch('api/delete_account.php', { method: 'POST', body: new URLSearchParams({ user_id: userId }), credentials: 'same-origin' });
            const d = await res.json();
            if (d && d.success) {
                renderAllViews();
                showAlert('Deleted', 'Account deleted.');
                if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
                return;
            }
            showAlert('Error', d.message || 'Failed to delete account');
        } catch (err) {
            showAlert('Error', 'Server error');
        }
    }, 'Delete', 'danger');
}

// Click handlers attached dynamically to account table buttons (defined in JS scope)
function _editAccountClickHandler(e) {
    const btn = e.currentTarget || e.target;
    const id = btn.getAttribute('data-id');
    const be = btn.getAttribute('data-barangay');
    const re = btn.getAttribute('data-rep');
    const ee = btn.getAttribute('data-email');
    openEditAccountFromEncoded(id, be, re, ee);
}

function _deleteAccountClickHandler(e) {
    const btn = e.currentTarget || e.target;
    const id = btn.getAttribute('data-id');
    if (!id) return;
    deleteAccount(id);
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
        const res = await fetch('api/update_barangay_account.php', { method: 'POST', body: params, credentials: 'same-origin' });
        const d = await res.json();
        if (d && d.success) {
            closeModal('editAccountModal');
            renderAllViews();
            showAlert('Saved', 'Account updated successfully.');
            if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
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

    // Populate description (larger, scrollable)
    const detailDesc = document.getElementById('detail-description');
    detailDesc.innerHTML = proposal.description ? escapeHtml(proposal.description).replace(/\n/g, '<br>') : '<span class="text-gray-400">No description provided.</span>';

    // Populate attachment (view + download) and prepare inline preview
    const attachEl = document.getElementById('detail-attachment');
    const previewEl = document.getElementById('detail-preview');
    attachEl.innerHTML = '';
    previewEl.innerHTML = '';
    previewEl.classList.add('hidden');
    if (proposal.attachment) {
        const rawName = (proposal.attachment || '').split('/').pop();
        const fileName = rawName || proposal.attachment;
        const safeName = escapeHtml(fileName);

        // Build absolute URL relative to the app root so links work from /staff/
        const pathname = window.location.pathname.replace(/\\/g, '/');
        const appRoot = pathname.replace(/\/staff\/.*$/i, '');
        const fileUrl = appRoot + '/uploads/proposals/' + encodeURIComponent(fileName);

        // links (always shown)
        attachEl.innerHTML = `
            <a href="${fileUrl}" target="_blank" class="text-blue-600 hover:underline">Open</a>
            <a href="${fileUrl}" download="${safeName}" class="px-3 py-1 text-xs bg-gray-100 border rounded hover:bg-gray-50">Download</a>
            <div class="text-xs text-gray-600">${safeName}</div>
        `;

        // Check file existence before showing preview
        (async () => {
            try {
                const head = await fetch(fileUrl, { method: 'HEAD', credentials: 'same-origin' });
                if (!head.ok) {
                    previewEl.classList.remove('hidden');
                    previewEl.innerHTML = `<div class="text-sm text-red-600">Attachment not found on server.</div>`;
                    return;
                }
            } catch (err) {
                // network error or CORS — show message but keep links
                previewEl.classList.remove('hidden');
                previewEl.innerHTML = `<div class="text-sm text-red-600">Could not verify attachment availability.</div>`;
                return;
            }

            const ext = (fileName.split('.').pop() || '').toLowerCase();
            if (['png','jpg','jpeg','gif','webp','bmp'].includes(ext)) {
                previewEl.classList.remove('hidden');
                previewEl.innerHTML = `<img src="${fileUrl}" alt="${safeName}" class="mx-auto rounded border max-h-72" />`;
            } else if (ext === 'pdf') {
                previewEl.classList.remove('hidden');
                previewEl.innerHTML = `<iframe src="${fileUrl}" class="w-full h-80 border rounded" title="${safeName}"></iframe>`;
            } else {
                // non-previewable file types: no inline preview
            }
        })();
    } else {
        attachEl.innerHTML = '<span class="text-gray-400 text-sm">No attachment</span>';
    }

    const historyList = document.getElementById('proposal-history-list');
    historyList.innerHTML = '';
    proposal.history.slice().reverse().forEach(entry => {
        const remarksHtml = entry.remarks ? `<p class="text-xs text-gray-500 mt-1 pl-4 border-l-2 border-gray-300">"${entry.remarks}"</p>` : '';
        historyList.innerHTML += `<div class="text-sm"><p><span class="font-semibold">${entry.user}</span> updated status to <span class="font-semibold">${entry.status}</span> on ${new Date(entry.date).toLocaleDateString()}</p>${remarksHtml}</div>`;
    });
    
    openModal('proposalDetailModal');
    
    document.getElementById('closeDetailModalBtn').onclick = () => closeModal('proposalDetailModal');
    
    document.getElementById('cancelUpdateBtn').onclick = () => closeModal('proposalDetailModal');
    
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
    fetch('api/update_proposal.php', { method: 'POST', body: new URLSearchParams({ id: proposalId, status: newStatus, remarks: remarks || '' }), credentials: 'same-origin' })
    .then(r => r.json())
    .then(d => {
        if (d && d.success) {
            // server updated, re-fetch server data later
            if (typeof fetchServerData === 'function') fetchServerData();
            renderAllViews();
            try { if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts(); } catch (e) {}
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

// Request status management functions removed - proposals only

// SSE: subscribe to notifications stream so staff dashboard refreshes when relevant notifications arrive
try {
    const staffSse = new EventSource('api/notifications_stream.php');
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
                    fetch(`api/list_staff_proposals.php?id=${encodeURIComponent(pid)}`, { credentials: 'same-origin' })
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
                                try { if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts(); } catch (e) {}
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
                if (typeof fetchDashboardCounts === 'function') fetchDashboardCounts();
            }
        } catch (err) { console.error('Invalid SSE payload', err); }
    });
    staffSse.addEventListener('error', (err) => { console.warn('SSE error', err); staffSse.close(); });
} catch (e) { console.warn('SSE not available', e); }

/* --- Manage My Submissions (staff) --- */
function loadMyProposals() {
    // Try server endpoint first
    fetch('api/list_staff_proposals.php')
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
    fetch(`api/list_staff_proposals.php?id=${encodeURIComponent(proposalId)}`)
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
    fetch('api/update_proposal.php', { method: 'POST', body: new URLSearchParams(payload) })
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
    
    const staffBell = document.getElementById('staffNotifBell');
    const staffBadge = document.getElementById('staffNotifBadge');
    const staffDropdown = document.getElementById('staffNotifDropdown');
    const staffList = document.getElementById('staffNotifList');
    const staffMarkAllRead = document.getElementById('staffMarkAllRead');

    async function fetchStaffNotifications() {
        try {
            const res = await fetch('api/notifications.php?role=' + encodeURIComponent('OPMDC Staff'));
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
            await fetch('api/notifications_mark_read.php?id=' + encodeURIComponent(id), { method: 'POST' });
            fetchStaffNotifications();
        }));
        staffList.querySelectorAll('.delete').forEach(btn => btn.addEventListener('click', async (e) => {
            const id = e.target.dataset.id;
            await fetch('api/notifications_delete.php?id=' + encodeURIComponent(id), { method: 'POST' });
            fetchStaffNotifications();
        }));
    }

    // Improved dropdown handling: move dropdown to body and use fixed positioning
    if (staffDropdown && staffBell) {
        // ensure dropdown is a direct child of body so it's not clipped by overflow-hidden ancestors
        if (staffDropdown.parentElement !== document.body) {
            document.body.appendChild(staffDropdown);
        }

        function showStaffDropdown() {
            // position dropdown below the bell
            const rect = staffBell.getBoundingClientRect();
            // ensure dropdown is visible to measure width/height
            staffDropdown.style.position = 'fixed';
            staffDropdown.style.zIndex = '9999';
            staffDropdown.classList.remove('hidden');
            staffDropdown.style.visibility = 'hidden';
            // force layout so offsetWidth/Height are available
            const ddW = staffDropdown.offsetWidth || 320;
            const left = Math.max(8, rect.right - ddW);
            staffDropdown.style.left = left + 'px';
            staffDropdown.style.top = (rect.bottom + 8) + 'px';
            staffDropdown.style.visibility = 'visible';
            // fetch notifications when showing
            fetchStaffNotifications();
        }

        function hideStaffDropdown() {
            staffDropdown.classList.add('hidden');
        }

        staffBell.addEventListener('click', (e) => {
            e.stopPropagation();
            if (staffDropdown.classList.contains('hidden')) showStaffDropdown(); else hideStaffDropdown();
        });

        // hide when clicking outside
        document.addEventListener('click', (ev) => {
            if (!staffDropdown.classList.contains('hidden')) {
                // if click is inside dropdown, ignore
                if (staffDropdown.contains(ev.target) || staffBell.contains(ev.target)) return;
                hideStaffDropdown();
            }
        });
        // hide on escape
        document.addEventListener('keydown', (ev) => { if (ev.key === 'Escape') hideStaffDropdown(); });
    }

    staffMarkAllRead && staffMarkAllRead.addEventListener('click', async () => {
        try {
            const res = await fetch('api/notifications.php?role=' + encodeURIComponent('OPMDC Staff'));
            const data = await res.json();
            for (const n of data.notifications || []) {
                await fetch('api/notifications_mark_read.php?id=' + encodeURIComponent(n.id), { method: 'POST' });
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
        let url = 'api/analytics_api.php';
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
        // Cache analytics data for advanced dashboard
        _cachedAnalyticsData = d;
        
        // Update timestamp and data range info
        const now = new Date();
        const timeStr = now.toLocaleString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit'
        });
        const lastUpdatedEl = document.getElementById('analytics-last-updated');
        if (lastUpdatedEl) lastUpdatedEl.textContent = timeStr;
        
        // Update data range display
        const start = document.getElementById('analyticsStart') ? document.getElementById('analyticsStart').value : '';
        const end = document.getElementById('analyticsEnd') ? document.getElementById('analyticsEnd').value : '';
        const dataRangeEl = document.getElementById('analytics-data-range');
        if (dataRangeEl) {
            if (start && end) {
                dataRangeEl.textContent = `${start} to ${end}`;
            } else if (start) {
                dataRangeEl.textContent = `From ${start}`;
            } else if (end) {
                dataRangeEl.textContent = `Until ${end}`;
            } else {
                dataRangeEl.textContent = 'All Time';
            }
        }
        
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
                        data: { labels: bLabels, datasets: [{ label: 'Requests', data: bData, backgroundColor: colors }] },
                        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
                    });
                }
            }

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

        // Update advanced dashboard with fetched analytics data
        buildAdvancedDashboard(d);

    } catch (err) { console.error('renderAnalytics error', err); }
}

// wire the Refresh button after DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const refreshBtn = document.getElementById('analyticsRefresh');
    if (refreshBtn) refreshBtn.addEventListener('click', (e) => { e.preventDefault(); fetchAnalytics(); });
});

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

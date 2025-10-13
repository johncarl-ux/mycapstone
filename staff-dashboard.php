<?php
// --- DATABASE CONNECTION AND DATA FETCHING ---

// IMPORTANT: Replace these with your actual database credentials.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables to 0 as a default
$totalRequests = 0;
$pendingRequests = 0;
$totalAccounts = 0;
$pendingAccounts = 0;

// --- 1. Query for Total Requests (from a 'proposals' table) ---
$sqlTotalRequests = "SELECT COUNT(*) as count FROM proposals";
$result = mysqli_query($conn, $sqlTotalRequests);
if ($result) {
    $totalRequests = mysqli_fetch_assoc($result)['count'];
}

// --- 2. Query for Pending Requests ---
$sqlPendingRequests = "SELECT COUNT(*) as count FROM proposals WHERE status IN ('For Review', 'Processing')";
$result = mysqli_query($conn, $sqlPendingRequests);
if ($result) {
    $pendingRequests = mysqli_fetch_assoc($result)['count'];
}

// --- 3. Query for Total Registered Accounts (from an 'accounts' table) ---
// Note: Adjust 'Barangay Official' if your role name is different
$sqlTotalAccounts = "SELECT COUNT(*) as count FROM accounts WHERE role = 'Barangay Official'";
$result = mysqli_query($conn, $sqlTotalAccounts);
if ($result) {
    $totalAccounts = mysqli_fetch_assoc($result)['count'];
}

// --- 4. Query for Pending Accounts (For Approval) ---
$sqlPendingAccounts = "SELECT COUNT(*) as count FROM accounts WHERE status = 'pending' AND role = 'Barangay Official'";
$result = mysqli_query($conn, $sqlPendingAccounts);
if ($result) {
    $pendingAccounts = mysqli_fetch_assoc($result)['count'];
}

// Close the database connection
mysqli_close($conn);
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
  </style>
</head>
<body class="bg-slate-50">

  <div class="flex h-screen">
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
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
      <header class="bg-white shadow-md p-4">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-file-alt text-blue-600 text-3xl mr-3"></i>
                                        <span class="text-blue-600 text-lg font-semibold">Requests</span>
                                    </div>
                                    <span id="total-requests-stat" class="text-4xl font-extrabold text-blue-600"><?php echo $totalRequests; ?></span>
                                    <span class="text-gray-500 text-xs mt-1">All submitted</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-hourglass-half text-yellow-600 text-3xl mr-3"></i>
                                        <span class="text-yellow-600 text-lg font-semibold">Pending</span>
                                    </div>
                                    <span id="pending-requests-stat" class="text-4xl font-extrabold text-yellow-600"><?php echo $pendingRequests; ?></span>
                                    <span class="text-gray-500 text-xs mt-1">Awaiting action</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-users text-green-600 text-3xl mr-3"></i>
                                        <span class="text-green-600 text-lg font-semibold">Accounts</span>
                                    </div>
                                    <span id="total-accounts-stat" class="text-4xl font-extrabold text-green-600"><?php echo $totalAccounts; ?></span>
                                    <span class="text-gray-500 text-xs mt-1">Registered</span>
                                </div>
                                <div class="card-pop flex flex-col items-start p-6">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-user-clock text-red-600 text-3xl mr-3"></i>
                                        <span class="text-red-600 text-lg font-semibold">For Approval</span>
                                    </div>
                                    <span id="pending-accounts-stat" class="text-4xl font-extrabold text-red-600"><?php echo $pendingAccounts; ?></span>
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

        </main>
    </div>
  </div>

  <script>
document.addEventListener("DOMContentLoaded", () => {
    // --- DOM Elements ---
    const allViews = document.querySelectorAll('.view');
    const mainContentTitle = document.getElementById('main-content-title');
    // ... (rest of your DOM elements)
    
    // --- Initial setup ---
    seedInitialData(); 
    
    // ... (rest of your event listeners)
});

// ... (rest of your functions like showView, openModal, closeModal)

function renderAllViews() {
    renderDashboardUI();
    renderAccountsTable();
    renderRequestsTable();
    renderProposalsPage();
}

function renderDashboardUI() {
    // -- MODIFIED --
    // These lines are now commented out because the values are loaded from the database via PHP.
    // This prevents JavaScript from overriding the server-side values with localStorage data.
    
    // const proposals = JSON.parse(localStorage.getItem('opmdcProposals')) || [];
    // const accounts = JSON.parse(localStorage.getItem('barangayAccounts')) || [];
    
    // document.getElementById('total-requests-stat').textContent = proposals.length;
    // document.getElementById('pending-requests-stat').textContent = proposals.filter(r => r.status === 'For Review' || r.status === 'Processing').length;
    // document.getElementById('total-accounts-stat').textContent = accounts.length;
    // document.getElementById('pending-accounts-stat').textContent = accounts.filter(a => a.status === 'pending').length;
}

// ... (The rest of your JavaScript code remains unchanged) ...
</script>

</body>
</html>  
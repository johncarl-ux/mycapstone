<?php
// Relocated Admin user management UI
// Adjusted path for db.php after moving into admin/ directory
$mysqli = require dirname(__DIR__) . '/db.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$errors = [];
$users = [];
$sql = "SELECT id, username, email, name, role, barangayName, status, created_at FROM users ORDER BY created_at DESC";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_assoc()) { $users[] = $row; }
    $result->free();
} else { $errors[] = 'Failed to fetch users.'; }
$mysqli->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin - User Management</title>
  <base href="../">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>body{font-family:'Poppins',sans-serif}</style>
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <script>
    (function(){
      try{ const u = JSON.parse(localStorage.getItem('loggedInUser')); if (!u||!u.role){ location.href='login.html'; return; }
        const r = String(u.role); if(/head/i.test(r)){location.href='head-dashboard.php';return;} if(/staff/i.test(r)){location.href='staff-dashboard.php';return;} if(!/admin/i.test(r)){location.href='barangay-dashboard.php';return;}
      }catch(e){ location.href='login.html'; }
    })();
  </script>
  <div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold">User Accounts</h1>
        <p class="text-sm text-gray-500">Manage user accounts, approve, edit or remove.</p>
      </div>
      <div class="flex gap-2 items-center">
        <input id="searchBox" type="text" placeholder="Search users..." class="px-3 py-2 border rounded w-64" />
        <a href="register.php" class="px-3 py-2 bg-blue-600 text-white rounded">Create new</a>
        <a href="login.html" class="px-3 py-2 bg-gray-200 rounded">Logout</a>
      </div>
    </div>
    <?php if (!empty($errors)): ?>
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        <ul class="list-disc pl-5">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table id="usersTable" class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-4 py-3">Name</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Barangay</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Joined</th><th class="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="text-sm divide-y">
        <?php foreach ($users as $u): ?>
          <tr>
            <td class="px-4 py-3"><?= htmlspecialchars($u['name']) ?></td>
            <td class="px-4 py-3">@<?= htmlspecialchars($u['username']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($u['email']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($u['role']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($u['barangayName']) ?></td>
            <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs <?= $u['status']==='approved'||$u['status']==='active' ? 'bg-green-100 text-green-800' : ($u['status']==='pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>"><?= htmlspecialchars($u['status']) ?></span></td>
            <td class="px-4 py-3"><?= htmlspecialchars($u['created_at']) ?></td>
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <?php if ($u['status']==='pending'): ?>
                  <form method="post" action="admin_actions.php" class="inline"><input type="hidden" name="action" value="approve"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="px-2 py-1 bg-green-600 text-white rounded text-xs">Approve</button></form>
                <?php endif; ?>
                <?php if ($u['status']!=='active' && $u['status']!=='disabled'): ?>
                  <form method="post" action="admin_actions.php" class="inline"><input type="hidden" name="action" value="activate"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="px-2 py-1 bg-indigo-600 text-white rounded text-xs">Activate</button></form>
                <?php endif; ?>
                <?php if ($u['status']!=='disabled'): ?>
                  <form method="post" action="admin_actions.php" class="inline"><input type="hidden" name="action" value="disable"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="px-2 py-1 bg-red-600 text-white rounded text-xs">Disable</button></form>
                <?php endif; ?>
                <form method="get" action="admin_edit.php" class="inline"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="px-2 py-1 bg-gray-700 text-white rounded text-xs">Edit</button></form>
                <form method="post" action="admin_actions.php" class="inline" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>"><button class="px-2 py-1 bg-black text-white rounded text-xs">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <script>
      const searchBox = document.getElementById('searchBox');
      const table = document.getElementById('usersTable');
      searchBox.addEventListener('input', () => {
        const q = searchBox.value.toLowerCase().trim();
        for (const row of table.tBodies[0].rows) {
          const text = row.innerText.toLowerCase();
          row.style.display = q===''||text.includes(q) ? '' : 'none';
        }
      });
    </script>
  </div>
</body>
</html>

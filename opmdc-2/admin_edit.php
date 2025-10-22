<?php
session_start();

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'] ?? '', ['OPMDC Head','Admin'], true)) {
  http_response_code(403);
  echo 'Access denied.';
  exit;
}

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($userId <= 0) {
    echo 'Invalid user id.';
    exit;
}

$mysqli = require __DIR__ . '/db.php';
$sql = "SELECT id, username, email, name, role, barangayName, status FROM users WHERE id = ? LIMIT 1";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    echo 'Server error.';
    exit;
}

if (!$user) {
    echo 'User not found.';
    exit;
}

$mysqli->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit User</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Edit User: <?= htmlspecialchars($user['username']) ?></h1>
    <form method="post" action="admin_edit_action.php" class="space-y-4">
      <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
      <div>
        <label class="block text-sm">Full name</label>
        <input name="name" value="<?= htmlspecialchars($user['name']) ?>" class="mt-1 w-full border px-3 py-2 rounded" />
      </div>
      <div>
        <label class="block text-sm">Email</label>
        <input name="email" value="<?= htmlspecialchars($user['email']) ?>" class="mt-1 w-full border px-3 py-2 rounded" />
      </div>
      <div>
        <label class="block text-sm">Role</label>
        <select name="role" class="mt-1 w-full border px-3 py-2 rounded">
          <option <?= $user['role'] === 'OPMDC Staff' ? 'selected' : '' ?>>OPMDC Staff</option>
          <option <?= $user['role'] === 'OPMDC Head' ? 'selected' : '' ?>>OPMDC Head</option>
          <option <?= $user['role'] === 'Barangay Official' ? 'selected' : '' ?>>Barangay Official</option>
          <option <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>
      <div>
        <label class="block text-sm">Barangay</label>
        <input name="barangayName" value="<?= htmlspecialchars($user['barangayName']) ?>" class="mt-1 w-full border px-3 py-2 rounded" />
      </div>
      <div>
        <label class="block text-sm">Status</label>
        <select name="status" class="mt-1 w-full border px-3 py-2 rounded">
          <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
          <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?>>approved</option>
          <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>active</option>
          <option value="disabled" <?= $user['status'] === 'disabled' ? 'selected' : '' ?>>disabled</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
        <a href="admin.php" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>

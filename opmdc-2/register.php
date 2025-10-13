<?php
// register.php - Pinagsamang registration page at admin approval UI
session_start();

// Mga variable para sa error at success messages
$errors = [];
$success = false;
$message = null;
$mysqli = require __DIR__ . '/db.php'; // Isang beses na lang i-require ang database connection

// --- LOGIC PARA SA ADMIN ACTIONS (APPROVE/DECLINE) ---
// Tumatakbo lang ito kapag ang POST request ay may 'action' na 'approve' or 'decline'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['approve', 'decline'], true)) {
    // Siguraduhin na OPMDC Head lang ang pwedeng gumawa nito
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'OPMDC Head') {
        $errors[] = 'Unauthorized action. Admin access required.';
    } else {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($userId > 0) {
            $newStatus = $_POST['action'] === 'approve' ? 'approved' : 'disabled';
            $updateSql = "UPDATE users SET status = ? WHERE id = ? AND status = 'pending' LIMIT 1";
            
            if ($stmt = $mysqli->prepare($updateSql)) {
                $stmt->bind_param('si', $newStatus, $userId);
                if ($stmt->execute()) {
                    $message = 'User status has been successfully updated.';
                } else {
                    $errors[] = 'Failed to update user status.';
                }
                $stmt->close();
            } else {
                $errors[] = 'Server error: Could not prepare the update statement.';
            }
        } else {
            $errors[] = 'Invalid user ID provided.';
        }
    }
}
// --- LOGIC PARA SA USER REGISTRATION ---
// Tumatakbo lang ito kapag ang POST request ay para sa registration (walang 'action' na field)
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $role = $_POST['role'] ?? 'OPMDC Staff'; // Default role
    $barangayName = trim($_POST['barangayName'] ?? '') ?: null;

    // Basic Validations
    if (empty($username) || empty($email) || empty($password) || empty($name)) {
        $errors[] = 'Please fill all required fields.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address format.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    // Kung walang validation errors, i-check kung existing na ang user
    if (empty($errors)) {
        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
        if ($stmt = $mysqli->prepare($checkSql)) {
            $stmt->bind_param('ss', $username, $email);
            $stmt->execute();
            if ($stmt->get_result()->fetch_assoc()) {
                $errors[] = 'Username or email is already taken.';
            }
            $stmt->close();
        }
    }
    
    // Kung walang errors, ituloy ang pag-insert sa database
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $status = 'pending'; // Lahat ng bagong registration ay 'pending' muna
        $insertSql = "INSERT INTO users (username, email, password, name, role, barangayName, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $mysqli->prepare($insertSql)) {
            $stmt->bind_param('sssssss', $username, $email, $hash, $name, $role, $barangayName, $status);
            if ($stmt->execute()) {
                $success = true;
                $message = 'Registration submitted successfully! Your account is now pending for admin approval.';
            } else {
                $errors[] = 'Failed to create account. Please try again.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Server error: Could not prepare the registration statement.';
        }
    }
}

// --- DATA FETCHING PARA SA ADMIN VIEW ---
// Kuhanin ang listahan ng pending accounts kung ang user ay OPMDC Head
$pending_accounts = [];
if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'OPMDC Head') {
    $query = "SELECT id, username, email, name, role, barangayName, created_at FROM users WHERE status = 'pending' ORDER BY created_at ASC";
    if ($result = $mysqli->query($query)) {
        while ($row = $result->fetch_assoc()) {
            $pending_accounts[] = $row;
        }
    }
}
$mysqli->close(); // Isara ang connection pagkatapos ng lahat ng queries
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - OPMDC Mabini</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-8 space-y-6">
        
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Create an Account</h1>

            <?php if ($message): ?>
                <div class="mb-4 p-3 rounded-md <?= $success ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-blue-50 border border-blue-200 text-blue-800' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <ul class="list-disc pl-5 text-sm text-red-700">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="post" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="OPMDC Staff" <?= (($_POST['role'] ?? '') === 'OPMDC Staff') ? 'selected' : '' ?>>OPMDC Staff</option>
                        <option value="OPMDC Head" <?= (($_POST['role'] ?? '') === 'OPMDC Head') ? 'selected' : '' ?>>OPMDC Head</option>
                        <option value="Barangay Official" <?= (($_POST['role'] ?? '') === 'Barangay Official') ? 'selected' : '' ?>>Barangay Official</option>
                    </select>
                </div>
                <div>
                    <label for="barangayName" class="block text-sm font-medium text-gray-700">Barangay Name (if applicable)</label>
                    <input type="text" id="barangayName" name="barangayName" value="<?= htmlspecialchars($_POST['barangayName'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create Account</button>
                </div>
            </form>
            <p class="mt-4 text-sm text-center text-gray-600">
                Already have an account? <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">Sign in</a>
            </p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($pending_accounts)): ?>
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-800 mb-3">Pending Account Approvals</h2>
                <div class="space-y-3">
                    <?php foreach ($pending_accounts as $account): ?>
                        <div class="p-3 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($account['name']) ?> <span class="text-sm font-normal text-gray-500">(@<?= htmlspecialchars($account['username']) ?>)</span></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($account['email']) ?> &middot; <span class="font-medium"><?= htmlspecialchars($account['role']) ?></span></p>
                                <?php if ($account['barangayName']): ?>
                                    <p class="text-xs text-gray-500">Barangay: <?= htmlspecialchars($account['barangayName']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex-shrink-0 flex gap-2">
                                <form method="post" class="inline">
                                    <input type="hidden" name="user_id" value="<?= (int)$account['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700">Approve</button>
                                </form>
                                <form method="post" class="inline">
                                    <input type="hidden" name="user_id" value="<?= (int)$account['id'] ?>">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700">Decline</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'OPMDC Head'): ?>
             <div class="border-t border-gray-200 pt-6 text-center">
                <h2 class="text-xl font-bold text-gray-800 mb-3">Pending Account Approvals</h2>
                <p class="text-sm text-gray-500">No pending accounts for approval at this time.</p>
            </div>
        <?php endif; ?>
        
    </div>
</body>
</html>
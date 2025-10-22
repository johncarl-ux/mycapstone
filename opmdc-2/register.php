<?php
// register.php - Pinagsamang registration page at admin approval UI
session_start();

// Mga variable para sa error at success messages
$errors = [];
$success = false;
$message = null;
$mysqli = require __DIR__ . '/db.php'; // Isang beses na lang i-require ang database connection

// --- LOGIC PARA SA USER REGISTRATION ---
// Tumatakbo lang ito kapag ang POST request ay para sa registration
// Capture return_to for redirect after registration (prefer explicit GET param, else HTTP_REFERER)
$return_to = '';
if (isset($_GET['return_to'])) {
    $return_to = $_GET['return_to'];
} elseif (!empty($_SERVER['HTTP_REFERER'])) {
    $return_to = $_SERVER['HTTP_REFERER'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // accept return_to from POST as hidden field
    $return_to = $_POST['return_to'] ?? $return_to;
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
                    // safe redirect: allow relative paths OR same-host absolute URLs only
                    if (!empty($return_to)) {
                        $allow = false;
                        $parsed = parse_url($return_to);
                        $serverHost = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');

                        if (!isset($parsed['scheme'])) {
                            // relative path, allow
                            $allow = true;
                        } else {
                            // absolute URL - allow only if host matches server host and scheme is http/https
                            $scheme = strtolower($parsed['scheme'] ?? '');
                            $host = $parsed['host'] ?? '';
                            if (in_array($scheme, ['http', 'https'], true) && $host === $serverHost) {
                                $allow = true;
                            }
                        }

                        if ($allow) {
                            // avoid redirecting back to the register page itself
                            $path = parse_url($return_to, PHP_URL_PATH) ?: '';
                            if (strpos($path, basename(__FILE__)) === false) {
                                header('Location: ' . $return_to);
                                exit;
                            }
                        }
                    }
                } else {
                $errors[] = 'Failed to create account. Please try again.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Server error: Could not prepare the registration statement.';
        }
    }
}

// Close DB connection
$mysqli->close();
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
                        <!-- Admin option removed: Admin account must be created by an existing admin -->
                    </select>
                </div>
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($return_to ?? '') ?>">
                <div>
                    <label for="barangayName" class="block text-sm font-medium text-gray-700">Barangay Name (if applicable)</label>
                    <input type="text" id="barangayName" name="barangayName" value="<?= htmlspecialchars($_POST['barangayName'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Create Account</button>
                </div>
            </form>
            <!-- Sign-in link removed per request -->
            <?php endif; ?>
        </div>
        
        
    </div>
</body>
</html>
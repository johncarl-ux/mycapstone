<?php
// db.php - simple mysqli connection helper
// Prefer environment variables or a runtime configuration so you can point to a non-localhost DB server.
// Supported environment variables: DB_HOST, DB_USER, DB_PASS, DB_NAME
// Fallback to local defaults for XAMPP.
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'opmdc';

// If you prefer a per-project PHP config file, create `config.db.php` next to this file and define
// $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME variables — they will override environment values.
if (file_exists(__DIR__ . '/config.db.php')) {
    /** @noinspection PhpIncludeInspection */
    include __DIR__ . '/config.db.php';
    if (isset($DB_HOST)) $dbHost = $DB_HOST;
    if (isset($DB_USER)) $dbUser = $DB_USER;
    if (isset($DB_PASS)) $dbPass = $DB_PASS;
    if (isset($DB_NAME)) $dbName = $DB_NAME;
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo 'Failed to connect to database.';
    error_log('DB connection error: ' . $mysqli->connect_error);
    exit;
}

// Set charset and collation
if (! $mysqli->set_charset('utf8mb4')) {
    // fallback or log
    error_log('Error setting charset: ' . $mysqli->error);
}

// Ensure connection collation is utf8mb4_general_ci
$mysqli->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");

return $mysqli;
?>

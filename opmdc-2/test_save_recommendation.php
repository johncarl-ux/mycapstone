<?php
// CLI test for save_recommendation.php
// This script simulates POST create and update flows by setting $_POST

chdir(__DIR__);
if (session_status() === PHP_SESSION_NONE) session_start();
// Grant staff role for test
$_SESSION['role'] = 'staff';

// Helper to run include and capture output
function run_save($post) {
    // Reset globals
    $_POST = $post;
    $_SERVER['REQUEST_METHOD'] = 'POST';

    ob_start();
    include __DIR__ . '/save_recommendation.php';
    $out = ob_get_clean();
    return $out;
}

// 1) Create
$createPost = [
    'category' => 'CLUP',
    'title' => 'CLI Test Recommendation ' . time(),
    'summary' => 'Created by CLI test',
    'details' => 'Detailed description from CLI test',
    'relevance' => '0.75',
    'source' => 'UnitTest'
];

echo "Running CREATE test...\n";
$createResp = run_save($createPost);
echo "CREATE RESPONSE:\n" . $createResp . "\n\n";

$decoded = json_decode($createResp, true);
if (!($decoded && !empty($decoded['success']) && !empty($decoded['id']))) {
    echo "Create failed or did not return id. Stopping test.\n";
    exit(1);
}

$id = (int)$decoded['id'];

// 2) Update
$updatePost = [
    'id' => $id,
    'category' => 'CLUP',
    'title' => 'CLI Test Recommendation (updated) ' . time(),
    'summary' => 'Updated summary',
    'details' => 'Updated details',
    'relevance' => '0.9',
    'source' => 'UnitTest'
];

echo "Running UPDATE test for id {$id}...\n";
$updateResp = run_save($updatePost);
echo "UPDATE RESPONSE:\n" . $updateResp . "\n\n";

$decoded2 = json_decode($updateResp, true);
if (!($decoded2 && !empty($decoded2['success']))) {
    echo "Update failed.\n";
    exit(2);
}

echo "CLI tests completed successfully.\n";

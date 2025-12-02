<?php
// Direct test of API file
chdir('c:/xampp/htdocs/mycapstone/opmdc-2');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';

ob_start();
require 'c:/xampp/htdocs/mycapstone/opmdc-2/api/list_barangay_accounts.php';
$output = ob_get_clean();

echo "Output:\n";
echo $output . "\n";
?>

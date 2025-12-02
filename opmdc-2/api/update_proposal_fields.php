<?php
// update_proposal_fields.php - update core proposal fields (not status)
header('Content-Type: application/json; charset=utf-8');

session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Authentication required']);
    exit;
}
$role = $_SESSION['user']['role'] ?? '';
if (!preg_match('/head|admin|staff/i', $role)) { // allow staff to adjust before forwarding
    http_response_code(403);
    echo json_encode(['success'=>false,'error'=>'Forbidden']);
    exit;
}

$mysqli = require dirname(__DIR__) . '/db.php';

$id = (int)($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$project_type = trim($_POST['project_type'] ?? '');
$barangay = trim($_POST['barangay'] ?? '');
$location = trim($_POST['location'] ?? '');
$budget = trim($_POST['budget'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'id required']);
    exit;
}

// Build dynamic update
$fields = [];
$params = [];
$types = '';
function addField(&$fields,&$params,&$types,$col,$val,$type){
    if ($val === '' && $col !== 'budget') return; // skip empties except numeric budget
    $fields[] = "$col = ?";
    $params[] = $val;
    $types .= $type;
}
if ($title !== '') addField($fields,$params,$types,'title',$title,'s');
if ($project_type !== '') addField($fields,$params,$types,'project_type',$project_type,'s');
if ($barangay !== '') addField($fields,$params,$types,'barangay',$barangay,'s');
if ($location !== '') addField($fields,$params,$types,'location',$location,'s');
if ($budget !== '') { $bVal = is_numeric($budget)? (float)$budget : 0.0; addField($fields,$params,$types,'budget',$bVal,'d'); }
if ($description !== '') addField($fields,$params,$types,'description',$description,'s');

if (!count($fields)) {
    echo json_encode(['success'=>false,'error'=>'No fields provided']);
    exit;
}

$sql = 'UPDATE project_proposals SET ' . implode(', ', $fields) . ' WHERE id = ? LIMIT 1';
$params[] = $id; $types .= 'i';
$stmt = $mysqli->prepare($sql);
if (!$stmt){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Prepare failed']);
    exit;
}
$stmt->bind_param($types, ...$params);
if (!$stmt->execute()){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Execute failed']);
    exit;
}
$stmt->close();

// Log history entry (optional)
try {
    $mysqli->query("CREATE TABLE IF NOT EXISTS proposal_history ( id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, proposal_id BIGINT UNSIGNED NOT NULL, status VARCHAR(128) NOT NULL, remarks TEXT DEFAULT NULL, user_id BIGINT UNSIGNED DEFAULT NULL, user_role VARCHAR(64) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY idx_proposal_id (proposal_id) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $uid = $_SESSION['user']['id'] ?? null; $urole = $_SESSION['user']['role'] ?? null;
    $hist = $mysqli->prepare('INSERT INTO proposal_history (proposal_id, status, remarks, user_id, user_role) VALUES (?,?,?,?,?)');
    if ($hist) { $st = 'Fields Updated'; $rem = 'Proposal fields modified'; $hist->bind_param('issis',$id,$st,$rem,$uid,$urole); @$hist->execute(); $hist->close(); }
} catch (Exception $e) {}

echo json_encode(['success'=>true,'id'=>$id]);

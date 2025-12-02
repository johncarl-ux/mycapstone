<?php
// api/staff_history.php
// Returns proposal history showing all staff activities

session_start();
$conn = require_once '../db.php';

header('Content-Type: application/json');

// Check if user is logged in and is staff
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SESSION['role'] !== 'OPMDC Staff') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Staff only']);
    exit;
}

// Check database connection
if (!$conn || $conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Query to get all proposal history with proposal details
    $sql = "
        SELECT 
            ph.id,
            ph.proposal_id,
            ph.status,
            ph.remarks,
            ph.user_role,
            ph.created_at,
            pp.title as project_title,
            pp.barangay,
            pp.project_type,
            u.name as staff_name
        FROM proposal_history ph
        LEFT JOIN project_proposals pp ON ph.proposal_id = pp.id
        LEFT JOIN users u ON ph.user_id = u.id
        WHERE ph.user_role = 'OPMDC Staff'
        ORDER BY ph.created_at DESC
        LIMIT 100
    ";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'id' => $row['id'],
            'proposal_id' => $row['proposal_id'],
            'project_title' => $row['project_title'] ?? 'Unknown',
            'barangay' => $row['barangay'] ?? 'Unknown',
            'project_type' => $row['project_type'] ?? 'Unknown',
            'status' => $row['status'],
            'remarks' => $row['remarks'],
            'staff_name' => $row['staff_name'] ?? 'Unknown Staff',
            'user_role' => $row['user_role'],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'history' => $history,
        'count' => count($history)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

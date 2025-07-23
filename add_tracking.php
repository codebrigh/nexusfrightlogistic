<?php
// add_tracking.php
header('Content-Type: application/json');
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'nexus_fright';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$tracking_number = $conn->real_escape_string($data['tracking_number']);

if (!$tracking_number) {
    echo json_encode(['error' => 'No tracking number provided']);
    exit;
}

// Optionally, check if it already exists
$res = $conn->query("SELECT id FROM tracking WHERE tracking_number='$tracking_number'");
if ($res->num_rows > 0) {
    echo json_encode(['message' => 'Tracking number already exists']);
    exit;
}

// Insert with default/fake values (you can customize)
$status = $conn->real_escape_string($data['status'] ?? 'Processing');
$last_update = $conn->real_escape_string($data['last_update'] ?? 'Tracking created by user');
$estimated_delivery = $conn->real_escape_string($data['estimated_delivery'] ?? date('Y-m-d', strtotime('+5 days')));

$conn->query("INSERT INTO tracking (tracking_number, status, last_update, estimated_delivery) VALUES ('$tracking_number', '$status', '$last_update', '$estimated_delivery')");

echo json_encode(['success' => true]); 
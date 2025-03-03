<?php
header('Content-Type: application/json');

if(file_exists('../../../../config.php')) {
    include('../../../../config.php');
}

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$sql = "SELECT * FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo json_encode(['success' => true, 'leave' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Leave request not found']);
}

$stmt->close();
$conn->close();
?>

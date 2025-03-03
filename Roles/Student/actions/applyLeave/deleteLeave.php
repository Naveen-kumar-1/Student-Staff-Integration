<?php
header('Content-Type: application/json');

if(file_exists('../../../../config.php')) {
    include('../../../../config.php');
}

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get the leave ID from POST request
$id = $_POST['id'] ?? '';

// Validate ID
if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Check if the leave request exists and is pending
$checkQuery = "SELECT status FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Leave request not found']);
    exit;
}

if ($row['status'] !== 'Pending') {
    echo json_encode(['success' => false, 'message' => 'Only pending requests can be deleted']);
    exit;
}

// Delete the leave request
$deleteQuery = "DELETE FROM leave_requests WHERE id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete leave request']);
}

$stmt->close();
$conn->close();
?>
